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
 * Block for rendering widget monitoring
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Widget_Monitoring extends Mage_Core_Block_Template
{
    /**
     * Format of array:
     * array (array ( 'css_id' => 'info|success|warning|error',
     *                 'label' => $label,
     *                 'value' => $value
     *                 'chart' => false|array (see below),
     *        ...
     *        )
     *
     * Setting 'label' = null will skip icon and label output, allowing free form html output via 'value'.
     * 'css_id' will still be used for background color. Set to info for neutral background.
     *
     * Format of chart array:
     * array('chart_id' => 'unique_id',
     *         'chart_type' => 'Bar|Doughnut|Line|Pie|PolarArea|Radar',
     *         'canvas_width' => width in pixel as int,
     *         'canvas_height' => height in pixel as int,
     *         'chart_data' => array that matches chart type data structure spec at http://www.chartjs.org/docs/
     *         'chart_options' => array that matches chart type chart options spec at http://www.chartjs.org/docs/
     *         )
     * @var array
     **/
    protected $_rows = array();

    /**
     * Array with magemonitoring/widget_button block instances.
     *
     * @var array
     */
    protected $_buttons = array();

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('monitoring/widget/monitoring.phtml');
    }

    /**
     * Adds a row to table.
     *
     * @param  string $cssId CSS id
     * @param  string $label Label
     * @param  string $value Value
     * @param  string $chart Chart
     * @return $this
     */
    public function addRow($cssId, $label, $value = null, $chart = null)
    {
        $this->_rows[] = array(
            'css_id' => $cssId,
            'label' => $label,
            'value' => $value,
            'chart' => $chart
        );

        return $this;
    }

    /**
     * Add empty or header row to table.
     *
     * @param  string $header       Header
     * @param  string $backgroundId Background ID
     * @param  string $wrapperTag   Wrapper tag
     * @return $this
     */
    public function addHeaderRow($header = null, $backgroundId = 'info', $wrapperTag = 'h4')
    {
        if ($wrapperTag && $wrapperTag !== '') {
            $header = '<'.$wrapperTag.'>'.$header.'</'.$wrapperTag.'>';
        }
        $this->_rows[] = array(
                'css_id' => $backgroundId,
                'label' => null,
                'value' => $header
        );

        return $this;
    }

    /**
     * Returns rows
     *
     * @return array
     */
    public function getRows()
    {
        if (empty($this->_rows)) {
            return false;
        }

        return $this->_rows;
    }

    /**
     * Returns an array that can feed FireGento_MageMonitoring_Block_Chart.
     *
     * @param  string $canvasId  Canvas Id
     * @param  array  $chartData Chart data
     * @param  string $chartType Chart type
     * @param  int    $width     Width
     * @param  int    $height    Height
     *
     * @return array
     */
    public function newChartArray($canvasId, $chartData, $chartType = 'Pie', $width = 76, $height = 76)
    {
        return array(
            'chart_id'      => $this->getTabId() . '_' . $this->getWidgetId() . '_' . $canvasId,
            'chart_type'    => $chartType,
            'canvas_width'  => $width,
            'canvas_height' => $height,
            'chart_data'    => $chartData
        );
    }

    /**
     * Adds a button to button array.
     *
     * @param  string $widget           Widget Id
     * @param  string $buttonId         Button Id
     * @param  string $label            Label
     * @param  string $controllerAction Controller action
     * @param  array  $urlParams        Url Params
     * @param  string $confirmMessage   Confirm Message
     * @param  string $cssClass         CSS class
     * @return $this
     */
    public function addButton(
        $widget,
        $buttonId,
        $label,
        $controllerAction,
        $urlParams = null,
        $confirmMessage = null,
        $cssClass = 'f-right'
    ) {
        $b = Mage::app()->getLayout()->createBlock('magemonitoring/widget_button');
        $b->setId($widget->getId().'_'.$buttonId);
        $b->setLabel($label);
        $b->setOnClick($widget, $controllerAction, $urlParams, $confirmMessage);
        $b->setClass($cssClass);
        $b->setType('button');

        $this->_buttons[] = $b;

        return $this;
    }

    /**
     * Returns buttons
     *
     * @return array|false
     */
    public function getButtons()
    {
        if (empty($this->_buttons)) {
            return false;
        }

        return $this->_buttons;
    }

    /** Sets a list of buttons
     *
     * @param  array $buttons List of buttons
     * @return $this
     */
    public function setButtons($buttons = array())
    {
        if (!is_array($buttons)) {
            $buttons = array($buttons);
        }
        $this->_buttons = $buttons;

        return $this;
    }
}
