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

class Hackathon_MageMonitoring_Block_Widget_Monitoring extends Mage_Core_Block_Template
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

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('monitoring/widget/monitoring.phtml');
    }

    /**
     * Adds a row to table.
     *
     * @param string $css_id
     * @param string $label
     * @param string $value
     * @param string $chart
     * @return $this
     */
    public function addRow($css_id, $label, $value = null, $chart = null)
    {
        $this->_rows[] = array(
            'css_id' => $css_id,
            'label' => $label,
            'value' => $value,
            'chart' => $chart
        );

        return $this;
    }

    /**
     * Add empty or header row to table.
     *
     * @param string $label
     * @param string $background_id
     * @param string $wrapper_tag
     * @return $this
     */
    public function addHeaderRow($header = null, $background_id = 'info', $wrapper_tag = 'h4')
    {
        if ($wrapper_tag && $wrapper_tag !== '') {
            $header = '<'.$wrapper_tag.'>'.$header.'</'.$wrapper_tag.'>';
        }
        $this->_rows[] = array(
                'css_id' => $background_id,
                'label' => null,
                'value' => $header
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getRows() {
        if (empty($this->_rows)) {
            return false;
        }

        return $this->_rows;
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
    public function newChartArray($canvasId, $chartData, $chartType = 'Pie', $width = 76, $height = 76)
    {
        return array(
                'chart_id' => $this->getTabId().'_'.$this->getWidgetId().'_'.$canvasId,
                'chart_type' => $chartType,
                'canvas_width' => $width,
                'canvas_height' => $height,
                'chart_data' => $chartData
        );
    }

    /**
     * Adds a button to button array.
     *
     * @param string $widget_id
     * @param string $button_id
     * @param string $label
     * @param string $controller_action or Hackathon_MageMonitoring_Model_Widget_Abstract::CALLBACK.$callbackMethod
     * @param array $url_params
     * @param string $confirm_message
     * @param string $css_class
     * @return $this
     */
    public function addButton($widget,
            $button_id,
            $label,
            $controller_action,
            $url_params = null,
            $confirm_message = null,
            $css_class = 'f-right'
    ) {
        $b = Mage::app()->getLayout()->createBlock('magemonitoring/widget_button');
        $b->setId($widget->getId().'_'.$button_id);
        $b->setLabel($label);
        $b->setOnClick($widget, $controller_action, $url_params, $confirm_message);
        $b->setClass($css_class);
        $b->setType('button');

        $this->_buttons[] = $b;

        return $this;
    }

    /**
     * @return array|false
     */
    public function getButtons()
    {
        if (empty($this->_buttons)) {
            return false;
        }

        return $this->_buttons;
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function setButtons($buttons=array()) {
        if (!is_array($buttons)) {
            $buttons = array($buttons);
        }
        $this->_buttons = $buttons;
        return $this;
    }

}
