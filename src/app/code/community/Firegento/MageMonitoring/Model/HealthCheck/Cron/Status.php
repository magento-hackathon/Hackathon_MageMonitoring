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
 * Class Firegento_MageMonitoring_Model_HealthCheck_Cron_Status
 * renders the cron job status.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_HealthCheck_Cron_Status
    extends Firegento_MageMonitoring_Model_Widget_Abstract
    implements Firegento_MageMonitoring_Model_Widget
{
    const NODE_NAME = 'default_healthcheck_cron_status';

    /** @var array */
    protected $_cronStatus = array(
        Mage_Cron_Model_Schedule::STATUS_ERROR,
        Mage_Cron_Model_Schedule::STATUS_MISSED,
        Mage_Cron_Model_Schedule::STATUS_PENDING,
        Mage_Cron_Model_Schedule::STATUS_RUNNING,
        Mage_Cron_Model_Schedule::STATUS_SUCCESS,
    );

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Cron Status');
    }

    /**
     * Used to render the widget, returns array of classes that have a ->toHtml() method.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you .
     *
     * @return array
     */
    public function getOutput()
    {
        $block = $this->newMultiBlock();

        /** @var Firegento_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        /** @var Firegento_MageMonitoring_Helper_Data $helper */
        $helper = Mage::helper('magemonitoring');

        $renderer->setHeaderRow(array($helper->__('Status'), $helper->__('Job Count')));

        /** @var Mage_Cron_Model_Mysql4_Schedule_Collection $collection */
        $collection = Mage::getResourceModel('cron/schedule_collection');
        $collection->getSelect()
            ->group('status')
            ->columns('COUNT(schedule_id) as status_count')
            ->order('status ASC');

        foreach ($this->_cronStatus as $_status) {
            $_statusCount = 0;
            if ($_schedule = $collection->getItemByColumnValue('status', $_status)) {
                $_statusCount = $_schedule->getStatusCount();
            }

            $_rowConfig = array();

            switch ($_status) {
                case Mage_Cron_Model_Schedule::STATUS_ERROR:
                    if ($_statusCount > 0) {
                        $_rowConfig['_cssClasses'] = Firegento_MageMonitoring_Helper_Data::WARN_TYPE_ERROR;
                    }
                    break;
                case Mage_Cron_Model_Schedule::STATUS_MISSED:
                case Mage_Cron_Model_Schedule::STATUS_RUNNING:
                    if ($_statusCount > 0) {
                        $_rowConfig['_cssClasses'] = Firegento_MageMonitoring_Helper_Data::WARN_TYPE_WARNING;
                    }
                    break;
                case Mage_Cron_Model_Schedule::STATUS_PENDING:
                    if (0 == $_statusCount) {
                        $_rowConfig['_cssClasses'] = Firegento_MageMonitoring_Helper_Data::WARN_TYPE_WARNING;
                    }
                    break;
            }

            $renderer->addRow(array($_status, $_statusCount), $_rowConfig);
        }

        $this->_output[] = $renderer;

        return $this->_output;
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
}
