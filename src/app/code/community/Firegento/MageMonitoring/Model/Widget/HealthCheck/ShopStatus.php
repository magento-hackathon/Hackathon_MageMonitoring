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
 * class Firegento_MageMonitoring_Model_Widget_HealthCheck_ShopStatus
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_HealthCheck_ShopStatus
    extends Firegento_MageMonitoring_Model_Widget_Abstract
    implements Firegento_MageMonitoring_Model_Widget
{
    /**
     * Returns name
     *
     * @see Firegento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Shop Status';
    }

    /**
     * Returns version
     *
     * @see Firegento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Returns isActive flag
     *
     * @see Firegento_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Fetches and returns output
     *
     * @return array
     */
    public function getOutput()
    {
        $helper = Mage::helper('magemonitoring');

        $statusError    = $helper->getConst('WARN_TYPE_ERROR');
        $statusWarning  = $helper->getConst('WARN_TYPE_WARNING');
        $statusOk       = $helper->getConst('WARN_TYPE_OK');
        $statusCssClass = $helper->getConst('WARN_CSSCLASS');

        $block = $this->newMultiBlock();
        /** @var Firegento_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        $header = array(
            $helper->__('Service'),
            $helper->__('Status'),
        );

        $row = array();

        /**
         * Webserver interface, PHP.ini information
         */

        $maxExecutionTime = ini_get('max_execution_time');
        $memoryLimit      = ini_get('memory_limit');

        $row[$helper->__("Webserver")] = $_SERVER["SERVER_SOFTWARE"];
        $row[$helper->__("Maximum execution time (PHP)")] = array('value' => $maxExecutionTime,
            'status' => array(
                $statusCssClass =>
                    $maxExecutionTime <= 30 ?
                        $statusError : ($maxExecutionTime >= 180 ? $statusOk : $statusWarning)
            )
        );

        /**
         * Extract Memory Limit as Integer
         */
        preg_match("/([0-9]+[\.,]?)+/", $memoryLimit, $matches);
        $memoryLimitValue = $matches[0];

        $row[$helper->__("Memory Limit")] = array('value' => $memoryLimit,
            'status' => array(
                $statusCssClass =>
                    $memoryLimitValue <= 64 ?
                        $statusError : ($memoryLimitValue >= 256 ? $statusOk : $statusWarning)
            )
        );

        /**
         * HTACCESS-Check
         */
        $row[$helper->__('.htaccess')] = file_exists(Mage::getBaseDir() . "/.htaccess") ?
            (array('value' => $helper->__('.htaccess exists'),
                'status' => array($statusCssClass => $statusOk,
                ))) :
            (array('value' => $helper->__('.htaccess does not exist'),
                'status' => array($statusCssClass => $statusError,
                )));


        /**
         * Magento-URL Information
         */
        $row[$helper->__('Admin-URL')] = Mage::helper('adminhtml')->getUrl('adminhtml');

        foreach (Mage::app()->getStores() as $store) {
            $row[$store->getName()] = Mage::app()->getStore($store->getId())->getUrl();
        }

        /**
         * Rendering
         */
        $renderer->setHeaderRow($header);

        foreach ($row as $key => $line) {

            if (is_array($line)) {
                $renderer->addRow(array($key, $line['value']), $line['status']);
            } else {
                $renderer->addRow(array($key, $line));
            }
        }

        $this->_output[] = $block;

        return $this->_output;
    }

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
