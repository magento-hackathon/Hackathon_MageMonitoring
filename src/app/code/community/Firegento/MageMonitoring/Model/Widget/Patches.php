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
 * Class Firegento_MageMonitoring_Model_Widget_Patches
 * displays whether the latest patches have been applied.
 * Todo: I am not sure how useful a static list of patches is ...
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_Patches
    extends Firegento_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'patches';

    /** @var array */
    protected $_patches = array(
        'APPSEC-212-2014' => array(
            'title' => 'APPSEC-212 (17.01.2014)',
            'callback' => '_isAPPSEC2014Applied',
        ),
        'APPSEC-212-2013' => array(
            'title' => 'APPSEC-212 (11.12.2013)',
            'callback' => '_isAPPSEC2013Applied',
        ),
        'PHP54' => array(
            'title' => 'PHP 5.4 support (17.01.2014)',
            'callback' => '_isPHP54Applied',
        ),
        'Zend-2012' => array(
            'title' => 'Zend Platform Vulnerability (05.07.2012)',
            'callback' => '_isZend2012Applied',
        ),
    );

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Patches');
    }

    /**
     * Returns the name of the widgets xml node
     *
     * @return string
     */
    protected function _getNodeName()
    {
        return self::NODE_NAME;
    }

    /**
     * Checks whether the "APPSEC-212-2014" patch has been applied.
     *
     * @return bool
     */
    protected function _isAPPSEC2014Applied()
    {
        $wysiwygHelperContent = file_get_contents(
            Mage::getBaseDir('code') . DS . 'core/Mage/Cms/Helper/Wysiwyg/Images.php'
        );

        if (!strpos($wysiwygHelperContent, '$_storageRoot')) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the "APPSEC-212-2013" patch has been applied.
     *
     * @return bool
     */
    protected function _isAPPSEC2013Applied()
    {
        if ($this->_isAPPSEC2014Applied()) {
            return true;
        }

        $wysiwygHelperContent = file_get_contents(
            Mage::getBaseDir('code') . DS . 'core/Mage/Cms/Helper/Wysiwyg/Images.php'
        );

        if (!strpos($wysiwygHelperContent, 'realpath($this->getStorageRoot())')) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the "PHP54" patch has been applied.
     *
     * @return bool
     */
    protected function _isPHP54Applied()
    {
        $catalogModelProductContent = file_get_contents(
            Mage::getBaseDir('code') . DS . 'core/Mage/Catalog/Model/Product.php'
        );

        if (strpos($catalogModelProductContent, '$options->setOptions(array_diff')) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the "Zend-2012" patch has been applied.
     *
     * @return bool
     */
    protected function _isZend2012Applied()
    {
        $xmlRpcResponseContent = file_get_contents(Mage::getBaseDir('lib') . DS . 'Zend/XmlRpc/Response.php');
        $rewriteFileExists = file_exists(Mage::getBaseDir('code') . DS . 'core/Zend/XmlRpc/Response.php');

        if (!strpos($xmlRpcResponseContent, '$loadEntities =') && !$rewriteFileExists) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the patch with the supplied name has been applied.
     *
     * @param  string $patch The patch name (e.g. "Zend-2012")
     *
     * @return $this
     */
    protected function _checkPatchApplied($patch)
    {
        if (!isset($this->_patches[$patch]) || !isset($this->_patches[$patch]['callback'])) {
            return $this;
        }

        /** @var Firegento_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        $callbackFunction = $this->_patches[$patch]['callback'];
        $patchApplied = $this->$callbackFunction();

        $this->getRenderer()->addRow(
            array(
                $helper->__('Patch applied'),
                $this->_patches[$patch]['title'],
                $patchApplied ? $helper->__('applied') : $helper->__('not applied'),
                $helper->__('apply')
            ),
            $this->_getRowConfig($patchApplied)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Render the patch status for all known patches in the list.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        parent::_renderMoreChecks();

        foreach (array_keys($this->_patches) as $_patch) {
            $this->_checkPatchApplied($_patch);
        }

        return $this;
    }
}
