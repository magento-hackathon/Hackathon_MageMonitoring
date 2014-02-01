<?php

class Hackathon_MageMonitoring_Helper_Data extends Mage_Core_Helper_Data
{
    public function getActiveCaches($cacheId = null)
    {
        // @todo: add caching mechanism (core_config_data with rescan button in backend?)

        // load all classes in Model/CacheStats
        $implFolder = Mage::getModuleDir(null, 'Hackathon_MageMonitoring') . DS . 'Model' . DS . 'CacheStats';
        foreach (array_filter(glob($implFolder."/*"), 'is_file') as $f) {
            require_once $f;
        }

        // get classes implementing cachestats interface
        $cacheClasses = array();
        $iName = 'Hackathon_MageMonitoring_Model_CacheStats';
        if (interface_exists($iName)) {
            $cacheClasses = array_filter(
                get_declared_classes(),
                create_function('$className', "return in_array(\"$iName\", class_implements(\"\$className\"));")
            );
        }

        // collect active caches
        $activeCaches = array();
        foreach ($cacheClasses as $cache) {
            $c = new $cache();
            if ($c->isActive() && !is_null($cacheId) && $cacheId == $c->getId()) {
                return $c;
            } else if ($c->isActive()) {
                $activeCaches[] = $c;
            }
        }

        return $activeCaches;
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
            $memoryLimit = ($memoryLimit / 1024) / 1024;
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

}