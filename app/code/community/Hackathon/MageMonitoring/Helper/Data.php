<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageMonitoring
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * Data helper
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Helper_Data extends Mage_Core_Helper_Data
{
    const VERSIONS_REGEXP = '#[\d\.\*]+#ims';
    const CHECK_NODE = 'global/healthcheck/%s';
    const WARN_CSSCLASS = '_cssClasses';
    const WARN_TYPE_OK = 'health-ok';
    const WARN_TYPE_WARNING = 'health-warning';
    const WARN_TYPE_ERROR = 'health-error';

    /**
     * Returns array with implementations of $baseInterface that return isActive() == true.
     *
     * @param  string|array $widgetId      or array of widgetIds, if widgetId equals '*' all widgets are returned
     * @param  string       $tabId         config scope, null for globals only
     * @param  boolean      $returnSorted  Return sorted or not
     * @param  string       $baseInterface The basic interface
     * @return array
     */
    public function getActiveWidgets(
        $widgetId = '*',
        $tabId = null,
        $returnSorted = true,
        $baseInterface = 'Hackathon_MageMonitoring_Model_Widget'
    ) {
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
                        create_function(
                            '$className',
                            "return in_array(\"$baseInterface\", class_implements(\"\$className\"));"
                        )
                );
            }
        } else {
            $widgets = $widgetId;
        }
        // collect active widgets
        $activeWidgets = array();
        foreach ($widgets as $widgetDbId => $widget) {
            try {
                $w = new $widget();
            } catch (Exception $e) {
                Mage::logException($e);
                continue;
            }
            if ($w->isActive()) {
                $w->loadConfig(null, $tabId, $widgetDbId);
                if ($widgetId == $w->getId()) {
                    return $w;
                } else {
                    $activeWidgets[$w->getConfigId()] = $w;
                }
            }
        }

        if ($returnSorted) {
            uasort($activeWidgets, array($this, 'compareWidgetDisplayPrio'));
        }

        return $activeWidgets;
    }

    /**
     * Returns widget(s) configuration. Filters invisible/inactive widgets and sorts by display_prio.
     *
     * @param  string  $tabId         Tab Id
     * @param  string  $widgetDbId    Widget Db Id
     * @param  boolean $returnSorted  Return sorted or not
     * @param  string  $baseInterface Basic interface
     * @return multitype:Ambigous <multitype:, unknown, multitype:unknown >
     */
    public function getConfiguredWidgets(
        $tabId = '*',
        $widgetDbId = null,
        $returnSorted = true,
        $baseInterface = 'Hackathon_MageMonitoring_Model_Widget'
    ) {
        if ($tabId !== '*') {
            $tabs = array($tabId => Mage::getStoreConfig('magemonitoring/tabs/'.$tabId));
        } else {
            $tabs = Mage::getStoreConfig('magemonitoring/tabs');
        }
        $widgets = array();
        foreach ($tabs as $key => $tab) {
            // custom block for tab?
            if (array_key_exists('block', $tab) && $tab['block']) {
                continue;
            }

            if (array_key_exists('widgets', $tab) && is_array($tab['widgets'])) {
                $implList = array();
                if ($widgetDbId) {
                    $implList[$widgetDbId] = $tab['widgets'][$widgetDbId]['impl'];
                } else {
                    foreach ($tab['widgets'] as $wDbId => $config) {
                        $visible = true;
                        if (array_key_exists('visible', $config) && !$config['visible']) {
                            $visible = false;
                        }
                        if (array_key_exists('impl', $config) && $visible) {
                            if (in_array($baseInterface, class_implements($config['impl']))) {
                                $implList[$wDbId] = $config['impl'];
                            }
                        }
                    }
                }
                $widgets[$key] = $this->getActiveWidgets($implList, $key, $returnSorted, $baseInterface);
            }
        }

        return $widgets;
    }

    /**
     * Returns active watch dogs as flattened and indexed array.
     * 
     * @return array
     */
    public function getConfiguredWatchDogs()
    {
        $tabs = $this->getConfiguredWidgets('*', null, false, 'Hackathon_MageMonitoring_Model_WatchDog');
        $watchDogs = array();
        foreach ($tabs as $tab) {
            $d = array_values($tab);
            $watchDogs += $d;
        }

        return $watchDogs;
    }

    /**
     * Returns tab config array. Filters invisible and sorts by display_prio.
     *
     * @param  string $tabId Tab Id
     * @return array
     */
    public function getConfiguredTabs($tabId = '*')
    {
        if ($tabId !== '*') {
            $tabs = array($tabId => Mage::getStoreConfig('magemonitoring/tabs/'.$tabId));
        } else {
            $tabs = Mage::getStoreConfig('magemonitoring/tabs');
        }
        $tabs = array_filter($tabs, array($this, 'filterVisibleTabs'));
        uasort($tabs, array($this, 'compareTabDisplayPrio'));

        return $tabs;
    }

    /**
     * Compare function for uasort(). Sorts by widget display_prio.
     *
     * @param  array $a Array A to compare display prio from
     * @param  array $b Array B to compare display prio from
     * @return number
     */
    protected function compareWidgetDisplayPrio($a, $b)
    {
        if ($a->getDisplayPrio() == $b->getDisplayPrio()) {
            return 0;
        }

        return ($a->getDisplayPrio() > $b->getDisplayPrio()) ? 1 : -1;
    }

    /**
     * Compare function for uasort(). Sorts by tab display_prio. Entries without display_prio go to the bottom.
     *
     * @param  array $a Array A to compare display prio from
     * @param  array $b Array B to compare display prio from
     * @return number
     */
    protected function compareTabDisplayPrio($a, $b)
    {
        if (!array_key_exists('display_prio', $a) && array_key_exists('display_prio', $b)) {
            return -1;
        } else if (array_key_exists('display_prio', $a) && !array_key_exists('display_prio', $b)) {
            return 1;
        } else if (!array_key_exists('display_prio', $a) && !array_key_exists('display_prio', $b)) {
            return 0;
        }
        if ($a['display_prio'] == $b['display_prio']) {
            return 0;
        }

        return ($a['display_prio'] > $b['display_prio']) ? 1 : -1;
    }

    /**
     * Filter function for array_filter(). Returns array where tabs have visible != 0.
     *
     * @param  array $entry The entry array
     * @return boolean
     */
    protected function filterVisibleTabs($entry)
    {
        if (array_key_exists('visible', $entry) && $entry['visible'] == 0) {
            return false;
        }

        return true;
    }

    /**
     * Calls require_once on all files found in $path.
     *
     * @param string $path     File path
     * @param int    $maxDepth Max depth
     */
    public function requireAll($path, $maxDepth=7)
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
     * Returns a value in byte
     *
     * @param  string $value       The initial value
     * @param  bool   $inMegabytes Return result in Megabytes
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
     * Format size from Byte to KB, MB or GB
     *
     * @param  int $size The initial size
     * @return string
     */
    public function formatByteSize($size)
    {
        if ($size < 1024) {
            return $size . " bytes";
        } else {
            if ($size < (1024 * 1024)) {
                $size = round($size / 1024, 1);

                return $size . " KB";
            } else {
                if ($size < (1024 * 1024 * 1024)) {
                    $size = round($size / (1024 * 1024), 1);

                    return $size . " MB";
                } else {
                    $size = round($size / (1024 * 1024 * 1024), 1);

                    return $size . " GB";
                }
            }
        }
    }

    /**
     * Returns total size in bytes of $dir, also supports files.
     * Returns false if os native way of getting dir size is not available.
     *
     * Source: http://stackoverflow.com/a/18568222
     *
     * @param  string $dir Directory path
     * @return number|false
     */
    public function getTotalSize($dir)
    {
        $dir = rtrim(str_replace('\\', '/', $dir), '/');

        if (is_dir($dir) === true) {
            $totalSize = 0;
            $os        = strtoupper(substr(PHP_OS, 0, 3));
            // If on a Unix Host (Linux, Mac OS)
            if ($os !== 'WIN') {
                $io = popen('/usr/bin/du -sk ' . $dir, 'r');
                if ($io !== false) {
                    $totalSize = intval(fgets($io, 80));
                    pclose($io);
                    return $totalSize*1024;
                }
            }
            // If on a Windows Host (WIN32, WINNT, Windows)
            if ($os === 'WIN' && extension_loaded('com_dotnet')) {
                $obj = new \COM('scripting.filesystemobject');
                if (is_object($obj)) {
                    $ref       = $obj->getfolder($dir);
                    $totalSize = $ref->size;
                    $obj       = null;
                    return $totalSize;
                }
            }

            return false;
        } else if (is_file($dir) === true) {
            return filesize($dir);
        }
    }

    /**
     * Returns an array with PHP information
     *
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
                    '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a><h1>PHP Version (.*?)</h1>'.
                    '(?:\n+?)</td></tr>#',
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
                    $pi[$n][$m[1]] = (!isset($m[3]) || $m[2]==$m[3]) ? $m[2] : array_slice($m, 2);
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
     * @param  string  $filepath File path
     * @param  int     $lines    Lines
     * @param  boolean $adaptive use adaptive buffersize for seeking, if false use static buffersize of 4096
     *
     * @return string
     */
    public function tailFile($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }

        // Sets buffer size
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

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
     * Get widget url
     *
     * @param  string                                $controllerAction Controller action
     * @param  Hackathon_MageMonitoring_Model_Widget $widget           Widget model
     * @return string $url
     */
    public function getWidgetUrl($controllerAction, $widget)
    {
        $params = array('widgetId' => $widget->getConfigId());
        if ($tabId = $widget->getTabId()) {
            $params['tabId'] = $tabId;
        }

        return Mage::getSingleton('adminhtml/url')->getUrl($controllerAction, $params);
    }

    /**
     * If $email is valid returns it with default rec. name,
     * else tries to treat $email as magento trans email code.
     *
     * @param  string $email E-Mail
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
     * Adds $dateString to $fileName, takes care of file extension handling.
     *
     * @param  string $fileName   File name
     * @param  string $dateString Date as string
     * @return string|false
     */
    public function stampFileName($fileName, $dateString)
    {
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
     * @param  string                                $configKey Configuration key
     * @param  Hackathon_MageMonitoring_Model_Widget $widget    Widget Model
     * @param  string                                $scope     Scope
     * @throws Exception
     * @return string
     */
    public function getConfigKey($configKey, $widget, $scope = 'global')
    {
        $conf = $widget->getConfig($configKey, false);
        if (is_array($conf) && array_key_exists ('scope', $conf)) {
            if ($conf['scope'] === 'widget' && method_exists($widget, 'getTabId') && $widget->getTabId() !== null) {
                $scope = 'tabs/' . $widget->getTabId();
            }
        }
        $id = null;
        if (class_implements($widget, 'Hackathon_MageMonitoring_Model_Widget')) {
            if ($scope === 'global') {
                $id = $widget->getId(); // class name for global params as db key
            } else {
                $id = $widget->getConfigId();
            }
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
     * @param  string $configKey  Configuration key
     * @param  string $widgetDbId Widget DB Id
     * @param  string $scope      Scope
     * @return string
     */
    public function getConfigKeyById($configKey, $widgetDbId, $scope = 'global')
    {
        $key = 'magemonitoring/';
        $prefix = Hackathon_MageMonitoring_Model_Widget_Abstract::CONFIG_PRE_KEY;

        return $key .= $scope . '/'. $prefix . '/' . $widgetDbId . '/' . $configKey;
    }

    /**
     * Extract versions from csv versions string with wildcards
     *
     * @param  string $versions Versions as string
     * @return array matches
     */
    public function extractVersions($versions)
    {
        preg_match_all(self::VERSIONS_REGEXP, $versions, $matches);

        return $matches[0];
    }

    /**
     * Get value of constant
     *
     * @param  string $typestring Constant name
     * @return mixed
     */
    public function getConst($typestring)
    {
        return constant('self::'.$typestring);
    }
}
