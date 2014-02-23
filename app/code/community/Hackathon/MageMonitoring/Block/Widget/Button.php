<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Hackathon_MageMonitoring_Block_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{

    /**
     * Set onClick data for button display.
     *
     * @param string $widgetId
     * @param string $controller_action
     * @param array $url_params
     * @param string $confirm_message
     *
     * @return string
     */
    public function setOnClick($widget, $controller_action, $url_params = null, $confirm_message = null)
    {
        $onClick = '';
        // check if this is an ajax call with callback
        $cbMarker = Hackathon_MageMonitoring_Model_Widget_Abstract::CALLBACK;
        if (!strncmp($controller_action, $cbMarker, strlen($cbMarker))) {
            $callback = substr($controller_action, strlen($cbMarker));
            $widgetId = $widget->getId();
            $widgetName = $widget->getName();
            $callbackUrl = Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/execCallback', $widgetId);
            $refreshUrl = 'null';
            // check if refresh flag is set
            if (isset($url_params['refreshAfter']) && $url_params['refreshAfter']) {
                $refreshUrl = '\'' . Mage::helper('magemonitoring')->getWidgetUrl(
                        '*/widgetAjax/refreshWidget',
                        $widgetId
                ) . '\'';
            }
            // add callback js
            $onClick .= "execWidgetCallback('$widgetId', '$widgetName', '$callback', '$callbackUrl', $refreshUrl);";
            // add confirm dialog?
            if ($confirm_message) {
                $onClick = "var r=confirm('$confirm_message'); if (r==true) {" . $onClick . "}";
            }
            return parent::setOnClick($onClick);
        }
        $url = Mage::getSingleton('adminhtml/url')->getUrl($controller_action, $url_params);
        if ($confirm_message) {
            $onClick = "confirmSetLocation('$confirm_message','$url')";
        } else {
            $onClick = "setLocation('$url')";
        }
        return parent::setOnClick($onClick);
    }

}
