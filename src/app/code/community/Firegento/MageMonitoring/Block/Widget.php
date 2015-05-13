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
 * Block for rendering widget
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Widget extends Mage_Core_Block_Template
{
    private $_widgetModel;
    private $_output;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('monitoring/widget.phtml');
    }

    /**
     * Returning HTML
     *
     * @return mixed
     */
    protected function _toHtml()
    {
        if (!$this->displayCollapsed()) {
            foreach ($this->getOutput() as $block) {
                $this->append($block);
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get the widget model
     *
     * @return Firegento_MageMonitoring_Model_Widget
     * @throws Exception
     */
    protected function _getWidget()
    {
        if ($this->_widgetModel instanceof Firegento_MageMonitoring_Model_Widget) {
            return $this->_widgetModel;
        } else {
            throw new Exception ('Use setWidget() before using any getter.');
        }
    }

    /**
     * Set source model
     *
     * @param  Firegento_MageMonitoring_Model_Widget $model Model
     * @return Firegento_MageMonitoring_Block_Widget $this  Widget block
     * @throws Exception
     */
    public function setWidget($model)
    {
        if ($model instanceof Firegento_MageMonitoring_Model_Widget) {
            $this->_widgetModel = $model;
        } else {
            throw new Exception ('Passed model does not implement Firegento_MageMonitoring_Model_Widget interface.');
        }

        return $this;
    }

    /**
     * Returns id string.
     *
     * @return string
     */
    public function getWidgetId()
    {
        return $this->getTabId() . '-' . $this->_getWidget()->getConfigId();
    }

    /**
     * Returns name of widget.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getWidget()->getName();
    }

    /**
     * Returns true if widget should render in collapsed state.
     *
     * @return bool
     */
    public function displayCollapsed()
    {
        return $this->_getWidget()->displayCollapsed();
    }

    /**
     * Returns config array.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_getWidget()->getConfig();
    }

    /**
     * Returns output array for rendering.
     *
     * @return array
     */
    public function getOutput()
    {
        if (!$this->_output) {
            $this->_output = $this->_getWidget()->getOutput();
        }

        return $this->_output;
    }

    /**
     * Returns configuration URL
     *
     * @return string
     */
    public function getConfigUrl()
    {
        return Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/getWidgetConf', $this->_getWidget());
    }

    /**
     * Returns callback URL
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/execCallback', $this->_getWidget());
    }

    /**
     * Returns refresh URL
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/refreshWidget', $this->_getWidget());
    }
}
