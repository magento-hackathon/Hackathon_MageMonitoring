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
class Hackathon_MageMonitoring_Model_Widget_Abstract
{
    protected $_output = array();
    protected $_buttons = array();

    /**
     * Returns last part of $className in lowercase.
     *
     * @param string $className
     *
     * @return string
     */
    protected function getClassId($className)
    {
        return $className;
    }

    /**
     * Adds a row to output array.
     *
     * @return $this
     */
    public function addRow($css_id, $label, $value=null, $chart=null) {
        $this->_output[] = array('css_id' => $css_id,
                            'label' => $label,
                            'value' => $value,
                            'chart' => $chart
                          );
        return $this;
    }

    /**
     * Adds a button to button array.
     *
     * @return $this
     */
    public function addButton($button_id, $label, $controller_action, $url_params = null, $confirm_message=null, $css_class='info') {
        $b = Mage::app()->getLayout()->createBlock('adminhtml/widget_button');
        $b->setId($button_id);
        $b->setLabel($label);
        $b->setOnClick($this->getOnClick($controller_action, $url_params, $confirm_message));
        $b->setClass($css_class);
        $b->setType('button');

        $this->_buttons[] = $b;
        return $this;
    }

    /**
     * Get onClick data for button display.
     *
     * @param string $controller_action
     * @param string $url_params
     * @param string $confirm_message
     *
     * @return string
     */
    protected function getOnClick($controller_action, $url_params = null, $confirm_message=null) {
        $url = Mage::getSingleton('adminhtml/url')->getUrl($controller_action, $url_params);
        if ($confirm_message) {
            $onClick = "confirmSetLocation('$confirm_message','$url')";
        } else {
            $onClick = "setLocation('$url')";
        }
        return $onClick;
    }

    /**
     * Returns output array.
     *
     * @return array|false
     */
    public function getButtons() {
        if (empty($this->_buttons)) {
            return false;
        }
        return $this->_buttons;
    }

    /**
     * Returns an array that can feed Hackathon_MageMonitoring_Block_Chart.
     *
     * @param string $canvasId
     * @param array $chartData
     * @param string $chartType
     * @param int $width
     * @param int $height
     *
     * @return array
     */
    public function createChartArray($canvasId, $chartData, $chartType='Pie', $width=76, $height=76) {
        return array( 'chart_id' => $canvasId,
                      'chart_type' => $chartType,
                      'canvas_width' => $width,
                      'canvas_height' => $height,
                      'chart_data' => $chartData
                    );
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function displayCollapsed() {
        return false;
    }

}
