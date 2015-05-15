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
 * Block for rendering widget button
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * Set onClick data for button display.
     *
     * @param  string $widget           Id of the widget
     * @param  string $controllerAction Controller action
     * @param  array  $urlParams        URL params
     * @param  string $confirmMessage   Confirmation message
     *
     * @return string
     */
    public function setOnClick($widget, $controllerAction, $urlParams = null, $confirmMessage = null)
    {
        $onClick = '';
        // check if this is an ajax call with callback
        $cbMarker = FireGento_MageMonitoring_Model_Widget_Abstract::CALLBACK;
        if (!strncmp($controllerAction, $cbMarker, strlen($cbMarker))) {
            $callback = substr($controllerAction, strlen($cbMarker));
            $widgetId = $widget->getConfigId();
            $tabId = $widget->getTabId();
            $widgetName = $widget->getName();
            $callbackUrl = Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/execCallback', $widget);
            $refreshUrl = 'null';
            // check if refresh flag is set
            if (isset($urlParams['refreshAfter']) && $urlParams['refreshAfter']) {
                $refreshUrl = '\'' . Mage::helper('magemonitoring')->getWidgetUrl(
                        '*/widgetAjax/refreshWidget',
                        $widget
                ) . '\'';
            }
            // add callback js
            $onClick .= "execWidgetCallback('$tabId-$widgetId', '$widgetName', '$callback',".
                "'$callbackUrl', $refreshUrl);";
            // add confirm dialog?
            if ($confirmMessage) {
                $onClick = "var r=confirm('$confirmMessage'); if (r==true) {" . $onClick . "}";
            }
            return parent::setOnClick($onClick);
        }
        $url = Mage::getSingleton('adminhtml/url')->getUrl($controllerAction, $urlParams);
        if ($confirmMessage) {
            $onClick = "confirmSetLocation('$confirmMessage','$url')";
        } else {
            $onClick = "setLocation('$url')";
        }

        return parent::setOnClick($onClick);
    }
}
