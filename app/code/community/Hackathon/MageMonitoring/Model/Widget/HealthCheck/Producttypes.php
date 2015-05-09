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
 * class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Producttypes
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Producttypes
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_defStartCollapsed = 1;

    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Product Type Check';
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
     * Returns isActive flag
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
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
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Donutchart $renderer */
        $renderer = $block->newContentRenderer('donutchart');

        Varien_Profiler::start('HEALTHCHECK PRODUCT_TYPE_CHECK');

        $resourceModel = Mage::getResourceModel('catalog/product');
        $connection = $resourceModel->getReadConnection();
        $sql = $connection
            ->select()
            ->from(array('cp' => $resourceModel->getTable('catalog/product')), array('type_id', 'count' => 'count(*)'))
            ->group('cp.type_id');

        $items = $connection->fetchAll($sql);

        foreach ($items as $item) {
            $renderer->addValue($item['type_id'], $item['count']);
        }

        Varien_Profiler::stop('HEALTHCHECK PRODUCT_TYPE_CHECK');

        $this->_output[] = $block;

        return $this->_output;
    }

    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
