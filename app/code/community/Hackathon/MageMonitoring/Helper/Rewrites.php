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
 * @author      Christian Münch <c.muench@netz98.de>
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Hackathon_MageMonitoring_Helper_Rewrites extends Mage_Core_Helper_Abstract
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
                        $rewrites[$type][$groupClassName . '/' . $child->getName()]['classes'][] = (string) $child;
                    }
                }
            }
        }

        foreach ($rewrites as $type => $data) {
            if (count($data) > 0 && is_array($data)) {
                foreach ($data as $node => $rewriteInfo) {
                    if (count($rewriteInfo['classes']) > 1) {
                        if ($this->_isInheritanceConflict($rewriteInfo['classes'])) {
                            $rewrites[$type][$node]['conflicts'][] = array(
                                'node' => $node,
                                'loaded_class' => $this->_getLoadedClass($type, $node)
                            );
                        }
                    }
                }
            }
        }

        $rewrites = array_merge($rewrites, $this->_loadLocalAutoloaderRewrites());

        if (empty($rewrites['blocks']) && empty($rewrites['models']) && empty($rewrites['helpers'])) {
            return false;
        }

        return $rewrites;
    }

    /**
     * Check if rewritten class has inherited the parent class.
     * If yes we have no conflict. The top class can extend every core class.
     * So we cannot check this.
     *
     * @var array $classes
     * @return bool
     */
    protected function _isInheritanceConflict($classes)
    {
        $classes = array_reverse($classes);
        for ($i = 0; $i < count($classes) - 1; $i++) {
            try {
                if (class_exists($classes[$i]) && class_exists($classes[$i + 1])) {
                    if (!is_a($classes[$i], $classes[$i + 1], true)) {
                        return true;
                    }
                }
            } catch (Exception $e) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns loaded class by type like models or blocks
     *
     * @param string $type       Class Type
     * @param string $classGroup Class Group Name
     *
     * @return string
     */
    protected function _getLoadedClass($type, $classGroup)
    {
        switch ($type) {
            case 'blocks':
                return Mage::getConfig()->getBlockClassName($classGroup);

            case 'helpers':
                return Mage::getConfig()->getHelperClassName($classGroup);

            default:
            case 'models':
                return Mage::getConfig()->getModelClassName($classGroup);
                break;
        }
    }


    /**
     * Searches for all rewrites over autoloader in "app/code/local" of
     * Mage, Enterprise Zend, Varien namespaces.
     *
     * @return array
     */
    protected function _loadLocalAutoloaderRewrites()
    {
        $return = array();
        $localCodeFolder = Mage::getBaseDir('code') . '/local';

        $folders = array(
            'Mage'       => $localCodeFolder . '/Mage',
            'Enterprise' => $localCodeFolder . '/Enterprise',
            'Varien'     => $localCodeFolder . '/Varien',
            'Zend'       => $localCodeFolder . '/Zend',
        );

        foreach ($folders as $vendorPrefix => $folder) {
            if (is_dir($folder)) {
                $directory = new RecursiveDirectoryIterator($folder);
                $iterator = new RecursiveIteratorIterator($directory);
                $files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

                foreach ($files as $file) {
                    $classFile = trim(str_replace($folder, '', realpath($file[0])), '/');
                    $className = $vendorPrefix
                        . '_'
                        . str_replace(DIRECTORY_SEPARATOR, '_', $classFile);
                    $className = substr($className, 0, -4); // replace .php extension
                    $return['autoload'][$className]['classes'][] = $className;
                }
            }
        }
        return $return;
    }

}