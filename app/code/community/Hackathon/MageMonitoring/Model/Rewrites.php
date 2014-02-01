<?php

/**
 *
 * Class Hackathon_MageMonitoring_Model_Rewrites
 */
class Hackathon_MageMonitoring_Model_Rewrites extends Mage_Core_Model_Config
{
    protected $_rewriteTypes = array(
        'blocks',
        'helpers',
        'models',
    );

    /**
     * Return all rewrites
     *
     * @return array
     */
    public function loadRewrites()
    {
        $rewrites = array(
            'blocks',
            'models',
            'helpers',
        );

        // Load config of each module because modules can overwrite config each other. Global config is already merged
        $modules = $this->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleData) {
            // Check only active modules
            if (!$moduleData->is('active')) {
                continue;
            }

            // Load config of module
            $configXmlFile = $this->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . 'config.xml';
            if (! file_exists($configXmlFile)) {
                continue;
            }

            $xml = simplexml_load_file($configXmlFile);
            if ($xml) {
                $rewriteElements = $xml->xpath('//rewrite');
                foreach ($rewriteElements as $element) {
                    foreach ($element->children() as $child) {
                        $type = simplexml_import_dom(dom_import_simplexml($element)->parentNode->parentNode)->getName();
                        if (!in_array($type, $this->_rewriteTypes)) {
                            continue;
                        }
                        $groupClassName = simplexml_import_dom(dom_import_simplexml($element)->parentNode)->getName();
                        if (!isset($rewrites[$type][$groupClassName . '/' . $child->getName()])) {
                            $rewrites[$type][$groupClassName . '/' . $child->getName()] = array();
                        }
                        $rewrites[$type][$groupClassName . '/' . $child->getName()][] = (string) $child;
                    }
                }
            }
        }

        return $rewrites;
    }

    /**
     * Searches for all rewrites over autoloader in "app/code/local" of
     * Mage, Enterprise Zend, Varien namespaces.
     *
     * @return array
     */
    protected function loadLocalAutoloaderRewrites()
    {
        $return = array();
        $localCodeFolder = \Mage::getBaseDir('code') . '/local';

        $folders = array(
            'Mage'       => $localCodeFolder . '/Mage',
            'Enterprise' => $localCodeFolder . '/Enterprise',
            'Varien'     => $localCodeFolder . '/Varien',
            'Zend'       => $localCodeFolder . '/Zend',
        );

        foreach ($folders as $vendorPrefix => $folder) {
            if (is_dir($folder)) {
                $finder = new Finder();
                $finder
                    ->files()
                    ->ignoreUnreadableDirs(true)
                    ->followLinks()
                    ->in($folder);
                foreach ($finder as $file) {
                    $classFile = trim(str_replace($folder, '', $file->getPathname()), '/');
                    $className = $vendorPrefix
                        . '_'
                        . str_replace(DIRECTORY_SEPARATOR, '_', $classFile);
                    $className = substr($className, 0, -4); // replace .php extension
                    $return['autoload: ' . $vendorPrefix][$className][] = $className;
                }
            }
        }

        return $return;
    }

}