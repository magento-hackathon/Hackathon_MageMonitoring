<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Hackathon_MageMonitoring_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     * Returns array with implementations of $baseInterface+$type that return isActive() == true.
     *
     * @param string $widgetId
     * @return array
     */
    public function getActiveWidgets($type='Dashboard', $widgetId = null, $baseInterface='Hackathon_MageMonitoring_Model_Widget')
    {
        // @todo: add caching mechanism (core_config_data with rescan button in backend?)

        $classFolders = array();

        // collect subscribed widget folders
        $eventConf = Mage::getConfig()->getEventConfig('global', 'magemonitoring_collect_widgets_'.strtolower($type));
        foreach ($eventConf->observers->children() as $module => $conf) {
            $o = array();
            if (preg_match("/([a-zA-Z]+_[a-zA-Z]+)/", get_class(Mage::helper($module)), $o)) {
                $classFolders[] = Mage::getModuleDir(null, $o[1]) . DS . $conf->class;
            }
        }

        // load all classes in subscribed folders
        foreach ($classFolders as $path) {
            foreach (array_filter(glob($path."/*"), 'is_file') as $f) {
                require_once $f;
            }
        }

        // get classes implementing widget interface
        $widgetClasses = array();
        $iName = $baseInterface.'_'.$type;
        if (interface_exists($iName)) {
            $widgetClasses = array_filter(
                    get_declared_classes(),
                    create_function('$className', "return in_array(\"$iName\", class_implements(\"\$className\"));")
            );
        }

        // collect active widgets
        $activeWidgets = array();
        foreach ($widgetClasses as $widget) {
            $w = new $widget();
            if ($w->isActive() && !is_null($widgetId) && $widgetId == $w->getId()) {
                return $w;
            } else if ($w->isActive()) {
                $w->loadConfig();
                $prio = 100;
                if ($w->getDisplayPrio()) {
                    $prio = $w->getDisplayPrio();
                }
                $activeWidgets[$prio.'_'.$w->getId()] = $w;
            }
        }

        ksort($activeWidgets, SORT_NUMERIC);
        return $activeWidgets;
    }

    /**
     * @param string $value
     * @param bool   $inMegabytes
     *
     * @return int|string
     */
    public function getValueInByte($value, $inMegabytes = false)
    {
        $memoryLimit = trim($value);

        $lastMemoryLimitLetter = strtolower(substr($memoryLimit, -1));
        switch($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        if ($inMegabytes) {
            $memoryLimit = round(($memoryLimit / 1024) / 1024);
        }

        return $memoryLimit;
    }

    public function getPhpInfoArray()
    {
        try {

            ob_start();
            phpinfo(INFO_ALL);

            $pi = preg_replace(
                array(
                    '#^.*<body>(.*)</body>.*$#m', '#<h2>PHP License</h2>.*$#ms',
                    '#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
                    "#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
                    '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a><h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
                    '#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
                    '#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
                    "# +#", '#<tr>#', '#</tr>#'),
                array(
                    '$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
                    '<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
                    "\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
                    '<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
                    '<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
                    '<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'
                ), ob_get_clean()
            );

            $sections = explode('<h2>', strip_tags($pi, '<h2><th><td>'));
            unset($sections[0]);

            $pi = array();
            foreach ($sections as $section) {
                $n = substr($section, 0, strpos($section, '</h2>'));
                preg_match_all(
                    '#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
                    $section,
                    $askapache,
                    PREG_SET_ORDER
                );
                foreach ($askapache as $m) {
                    if (!isset($m[0]) || !isset($m[1]) || !isset($m[2])) {
                        continue;
                    }
                    $pi[$n][$m[1]]=(!isset($m[3])||$m[2]==$m[3])?$m[2]:array_slice($m,2);
                }
            }

        } catch (Exception $exception) {
            return array();
        }

        return $pi;
    }

    /**
     * tail -n in php, kindly lifted from https://gist.github.com/lorenzos/1711e81a9162320fde20
     *
     * @param string $filepath
     * @param int $lines
     * @param bool $adaptive use adaptive buffersize for seeking, if false use static buffersize of 4096
     *
     * @return string
     */
    function tailFile($filepath, $lines = 1, $adaptive = true) {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);
        return trim($output);
    }

    /**
     * @param string $controller_action
     * @param string $widgetId
     * @return string $url
     */
    public function getWidgetUrl($controller_action, $widgetId) {
        return Mage::getSingleton('adminhtml/url')->getUrl($controller_action, array('widgetId' => $widgetId));
    }

}