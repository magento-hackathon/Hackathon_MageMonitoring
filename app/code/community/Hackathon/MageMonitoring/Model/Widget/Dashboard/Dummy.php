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

    public function __construct()
    {
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getId()
     */
    public function getId() {
        return $this->getClassId(__CLASS__);
    }

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
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return false;
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
        $chartDataPie = array(array('value' => 34, 'color' => '#ff0000'),
                              array('value' => 12, 'color' => '#00ff00'),
                              array('value' => 42, 'color' => '#0000ff')
                             );
        $chart = $this->createChartArray($this->getId().'_another_imp', $chartDataPie);

        // add row with pie chart
        $this->addRow('warning', 'Another Important Stat with a 3 pieces pie chart', 'I like pie.', $chart);

        $chart = $this->createChartArray($this->getId().'_orders', $chartData, 'Radar', 300, 300);

        // add row with radar chart
        $this->addRow('error', 'Orders of the last 6 months in comparsion with previous year.', ' ', $chart);

        // add 2 buttons
        $this->addButton($this->getId().'_bone', 'click me!' , '*/*/', null, 'Thanks for clicking!', 'f-right');
        $this->addButton($this->getId().'_btwo', 'dont click me!' , '*/*/qwerty', null, 'Last chance!', 'f-right');

        return $this->_output;
    }

}
