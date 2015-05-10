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
 * class Hackathon_MageMonitoring_Model_Widget_System_Magento
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_System_Magento
    extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_defDisplayPrio = 20;

    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Magento Information';
    }

    /**
     * Returns version
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Fetches and returns output
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $block->addRow('info', 'Magento Version', $this->getMagentoInfo('version'));
        $block->addRow('info', 'Magento Root Path', $this->_getValue('DOCUMENT_ROOT'));
        $block->addRow('info', 'Total Products Count', $this->getMagentoInfo('products_count'));
        $block->addRow('info', 'Total Customers Count', $this->getMagentoInfo('customers_count'));
        $block->addRow('info', 'Total Orders Count', $this->getMagentoInfo('orders_count'));
        $block->addRow('info', 'Current Online Visitors', $this->getMagentoInfo('online_visitors'));

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

