<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @author      Christian Munch
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Hackathon_MageMonitoring_Model_Rewrites extends Mage_Core_Model_Config
{
    /**
     * Return all rewrites
     *
     * @return array
     */
    public function getRewrites()
    {
        $rewrites = array(
            'blocks',
            'models',
            'helpers',
        );

        /* @var $_magentoConfig Mage_Core_Model_Config */
        $_magentoConfig = Mage::getConfig();

        // Load config of each module because modules can overwrite config each other. Global config is already merged
        $modules = $_magentoConfig->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleData) {
            // Check only active modules
            if (!$moduleData->is('active')) {
                continue;
            }

            // Load config of module
            $configXmlFile = $_magentoConfig->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . 'config.xml';
            if (!file_exists($configXmlFile)) {
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

        if (empty($rewrites['blocks']) && empty($rewrites['models']) && empty($rewrites['helpers'])) {
            return false;
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