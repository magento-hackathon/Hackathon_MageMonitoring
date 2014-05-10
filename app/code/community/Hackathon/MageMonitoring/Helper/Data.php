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
	const VERSIONS_REGEXP = '#[\d\.\*]+#ims';

    const CHECK_NODE = 'global/healthcheck/%s';

    const TYPE_STATIC = 'static';
    const TYPE_ONDEMAND = 'ondemand';

    const WARN_CSSCLASS = '_cssClasses';
    const WARN_TYPE_OK = 'health-ok';
    const WARN_TYPE_WARNING = 'health-warning';
    const WARN_TYPE_ERROR = 'health-error';
    
    /**
     * Returns array with implementations of $baseInterface that return isActive() == true.
     *
     * @param string|array $widgetId or array of widgetIds, if widgetId equals '*' all widgets are returned
     * @param string $tabId config scope, null for global
     * @param string $baseInterface
     * @return array
     */
    public function getActiveWidgets($widgetId='*', $tabId=null, $baseInterface='Hackathon_MageMonitoring_Model_Widget')
    {
        $classFolders = array();
        $widgets = array();

        if (!is_array($widgetId) && $widgetId !== '*') {
            $widgets[] = $widgetId;
        } else if ($widgetId === '*') {
            $widgetConf = Mage::getConfig()->getNode('global/widgets');
            foreach ($widgetConf->children() as $module => $conf) {
                $o = array();
                if (preg_match("/([a-zA-Z]+_[a-zA-Z]+)/", get_class(Mage::helper($module)), $o)) {
                    $classFolders[] = Mage::getModuleDir(null, $o[1]) . DS . $conf->folder;
                }
            }
            // include all classes in subscribed folders
            foreach ($classFolders as $path) {
                $this->requireAll($path);
            }
            // get classes implementing widget interface
            if (interface_exists($baseInterface)) {
                $widgets = array_filter(
                        get_declared_classes(),
                        create_function('$className', "return in_array(\"$baseInterface\", class_implements(\"\$className\"));")
                );
            }
        } else {
            $widgets = $widgetId;
        }
        // collect active widgets
        $activeWidgets = array();
        foreach ($widgets as $widget) {
            try {
                $w = new $widget();
            } catch (Exception $e) {
                Mage::logException($e);
                continue;
            }
            if ($w->isActive() && $widgetId == $w->getId()) {
                return $w;
            } else if ($w->isActive()) {
                $w->loadConfig(null, $tabId);
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
     * Calls require_once on all files found in $path.
     *
     * @param string $path
     * @param int $maxDepth
     */
    public function requireAll($path, $maxDepth=3)
    {
        foreach (array_filter(glob($path."/*"), 'is_dir') as $d) {
            if ($maxDepth > 0) {
                $this->requireAll($d, --$maxDepth);
            }
        }
        foreach (array_filter(glob($path."/*"), 'is_file') as $f) {
            require_once $f;
        }
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

    /**
     * @return array|mixed
     */
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
     * @param Hackathon_MageMonitoring_Model_Widget $widget
     * @return string $url
     */
    public function getWidgetUrl($controller_action, $widget) {
        $params = array('widgetId' => $widget->getId());
        if ($tabId = $widget->getTabId()) {
            $params['tabId'] = $tabId;
        }
        return Mage::getSingleton('adminhtml/url')->getUrl($controller_action, $params);
    }

    /**
     * If $email is valid returns it with default rec. name,
     * else tries to treat $email as magento trans email code.
     *
     * @param string $email
     * @return array|false
     */
    public function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('email' => $email, 'name' => 'MageMonitoring');
        }
        $name = Mage::getStoreConfig('trans_email/ident_'.$email.'/name');
        $email = Mage::getStoreConfig('trans_email/ident_'.$email.'/email');
        if ($name) {
            return array('email' => $email, 'name' => $name);
        }
        return false;
    }

    /**
     * Adds $dateString to $fileName, takes care or file extension handling.
     *
     * @param string $fileName
     * @param string $dateString
     * @return string|false
     */
    public function stampFileName($fileName, $dateString) {
        $p = pathinfo($fileName);
        $r = false;
        if (isset($p['filename']) && $p['filename']) {
            $r = $p['filename'].'-'.$dateString;
        }
        if (isset($p['extension']) && $p['extension']) {
            $r .= '.' . $p['extension'];
        }
        return $r;
    }

    /**
     * Returns unique config key for widget configs.
     *
     * @param string $configKey
     * @param Hackathon_MageMonitoring_Model_Widget $widget
     * @return string
     */
    public function getConfigKey($configKey, $widget) {
        $conf = $widget->getConfig($configKey, false);
        $scope = 'global';
        if (is_array($conf) && array_key_exists ('scope', $conf)) {
            if ($conf['scope'] === 'widget' && method_exists($widget, 'getTabId') && $widget->getTabId() !== null) {
                $scope = 'tabs/' . $widget->getTabId();
            }
        }
        $id = null;
        if (class_implements($widget, 'Hackathon_MageMonitoring_Model_Widget')) {
            $id = $widget->getId();
        } elseif (class_implements($widget, 'Hackathon_MageMonitoring_Model_WatchDog')) {
            $id = $widget->getDogId();
        } else {
            throw new Exception("Passed class does not implement Widget or WatchDog interface.");
        }
        return $this->getConfigKeyById($configKey, $id, $scope);
    }

    /**
     * Returns unique config key for widget configs.
     *
     * @param string $configKey
     * @param string $widgetId
     * @param string $scope
     * @return string
     */
    public function getConfigKeyById($configKey, $widgetId, $scope='global') {
        $key = 'magemonitoring/';
        $prefix = Hackathon_MageMonitoring_Model_Widget_Abstract::CONFIG_PRE_KEY;
        return $key .= $scope . '/'. $prefix . '/' . $widgetId . '/' . $configKey;
    }

    /**
     * Extract versions from csv versions string with wildcards
     *
     * @param $versions
     * @return array matches
     */
    public function extractVersions($versions)
    {
        preg_match_all(self::VERSIONS_REGEXP, $versions, $matches);
        return $matches[0];
    }

    public function getConst($typestring)
    {
        return constant('self::'.$typestring);
    }

}