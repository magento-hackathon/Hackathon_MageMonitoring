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

class Hackathon_MageMonitoring_Model_Widget_Dashboard_Dummy extends Hackathon_MageMonitoring_Model_Widget_Abstract
                                                            implements Hackathon_MageMonitoring_Model_Widget_Dashboard
{

    const CONFIG_PIE_COLOR_ONE = 'pie_color_one';
    const CONFIG_PIE_COLOR_TWO = 'pie_color_two';
    const CONFIG_PIE_COLOR_THREE = 'pie_color_three';

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Dummy Monitoring Widget';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig() {
        // we want the default config for persistent collapseable state ..
        parent::initConfig();

        // ...and add 3 text fields with tooltip to that
        $this->addConfig(self::CONFIG_PIE_COLOR_ONE, 'Pie Color 1:', '#f00000', 'text', false, 'A wild tooltip appears.');
        $this->addConfig(self::CONFIG_PIE_COLOR_TWO, 'Pie Color 2:', '#0000f0', 'text', false, 'Another wild tooltip appears.');
        $this->addConfig(self::CONFIG_PIE_COLOR_THREE, 'Pie Color 3:', '#00f000', 'text', false, 'Yep, here too.');

        return $this->_config;
    }

    /**
     * Example for a custom callback method. Do some work and return string or any html.
     *
     * @return string
     */
    public function helloCallback () {
        // hard work
        sleep(3);
        return 'Pleased to inform you that the operation was indeed a great success! <br/> Now let me refresh that widget for you..';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput() {
        // add some data
        $this->addRow('info', 'Simple Info Stat with no chart', '42');

        // prepare a chart suitable for lines/bars/radar
        $chartData = array('labels' => array("January","February","March","April","May","June","July"),
                           'datasets' => array(array('fillColor' => "rgba(220,220,220,0.5)",
                                                     'strokeColor' => "rgba(220,220,220,1)",
                                                     'pointColor' =>"rgba(220,220,220,1)",
                                                     'pointStrokeColor' => "#fff",
                                                     'data' => array(65,59,90,81,56,55,40)),
                                               array('fillColor' => "rgba(120,220,120,0.5)",
                                                     'strokeColor' => "rgba(120,220,120,1)",
                                                     'pointColor' =>"rgba(120,220,120,1)",
                                                     'pointStrokeColor' => "#ffa",
                                                     'data' => array(25,29,50,81,52,25,70))
                                              )
                           );
        $chart = $this->createChartArray($this->getId().'_very_imp', $chartData, 'Line', 500, 200);

        // add row with line chart
        $this->addRow('warning', 'Very Important Stat with a line chart and 2 data sets', '303 / 2048M', $chart);

        // prepare a pie chart
        $chartDataPie = array(array('value' => 34, 'color' => $this->getConfig(self::CONFIG_PIE_COLOR_ONE)),
                              array('value' => 12, 'color' => $this->getConfig(self::CONFIG_PIE_COLOR_TWO)),
                              array('value' => 42, 'color' => $this->getConfig(self::CONFIG_PIE_COLOR_THREE))
                             );
        $chart = $this->createChartArray($this->getId().'_another_imp', $chartDataPie);

        // add row with pie chart
        $this->addRow('warning', 'Another Important Stat with a 3 pieces pie chart, colors can be changed by user', 'I like pie.', $chart);

        $chart = $this->createChartArray($this->getId().'_orders', $chartData, 'Radar', 300, 300);

        // add row with radar chart
        $this->addRow('error', 'Orders of the last 6 months in comparsion with previous year.', ' ', $chart);

        // add a button that will execute helloCallback() when clicked. callback method needs to be in this class.
        // we want the widget to refresh itself after the call is done
        // and a confirm dialog before clicking
        $this->addButton($this->getId().'_bone', 'click me!' , self::CALLBACK.'helloCallback', array('refreshAfter' => true), 'Long running operation! You sure?');

        // just a normal button that links to a standard magento route
        $this->addButton($this->getId().'_btwo', 'dont click me!' , '*/*/qwerty', null, 'Last chance!');

        return $this->_output;
    }

}
