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
 * Block for rendering charts
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Chart extends Mage_Core_Block_Template
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('monitoring/chart.phtml');
    }

    /**
     * Returns json encoded chart data
     *
     * @return json encoded array
     */
    public function getChartData()
    {
        $d = $this->getData('chart_data');
        if (!is_array($d)) {
            $d = array();
        }

        return json_encode($d);
    }

    /**
     * Return json encoded chart options
     *
     * @return json encoded array
     */
    public function getChartOptions()
    {
        $o = $this->getData('chart_options');
        if (!is_array($o)) {
            $o = array();
        }

        return json_encode($o);
    }
}
