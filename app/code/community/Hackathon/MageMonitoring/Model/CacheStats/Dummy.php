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

class Hackathon_MageMonitoring_Model_CacheStats_Dummy extends Hackathon_MageMonitoring_Model_CacheStats_Abstract
                                                      implements Hackathon_MageMonitoring_Model_CacheStats
{

    public function __construct()
    {
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getId()
     */
    public function getId() {
        return $this->getClassId(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getName()
     */
    public function getName()
    {
        return 'Dummy Monitoring Widget';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getVersion()
     */
    public function getVersion()
    {
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryMax()
     */
    public function getMemoryMax()
    {
        // skip memory row in frontend
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        // skip memory row in frontend
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheHits()
     */
    public function getCacheHits()
    {
        // skip hit/miss row in frontend
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheMisses()
     */
    public function getCacheMisses()
    {
        // skip hit/miss row in frontend
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCustomStats()
     */
    public function getCustomStats() {
        return array(// 1st row
                     array('css_class' => 'info',
                         'label' => 'Simple Info Stat with no chart',
                         'value' => '42'
                         ),
                     // 2nd row
                     array('css_class' => 'warning',
                           'label' => 'Very Important Stat with a line chart and 2 data sets',
                           'value' => '303 / 2048M',
                           'chart' => array('chart_id' => $this->getId().'_very_imp',
                                            'chart_type' => 'Line',
                                            'canvas_width' => 500,
                                            'canvas_height' => 200,
                                            'chart_data' => array('labels' => array("January","February","March","April","May","June","July"),
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
                                                                )
                                         )
                           ),
                    // 3rd row
                    array('css_class' => 'success',
                          'label' => 'Another Important Stat with a 3 pieces pie chart',
                          'value' => 'I like pie.',
                          'chart' => array( 'chart_id' => $this->getId().'_another_imp',
                                            'chart_type' => 'Pie',
                                            'canvas_width' => 76,
                                            'canvas_height' => 76,
                                            'chart_data' => array(array('value' => 34, 'color' => '#ff0000'),
                                                                  array('value' => 12, 'color' => '#00ff00'),
                                                                  array('value' => 42, 'color' => '#0000ff')
                                                                  )
                                          )
                        ),
                   // 4th row
                   array('css_class' => 'error',
                         'label' => 'Orders Today/Yesterday with a radar chart.',
                         'value' => ' ',
                         'chart' => array('chart_id' => $this->getId().'_radar',
                                          'chart_type' => 'Radar',
                                          'canvas_width' => 350,
                                          'canvas_height' => 350,
                                          'chart_data' => array('labels' => array("Total","Processed","Unprocessed"),
                                                                'datasets' => array(array('fillColor' => "rgba(220,220,220,0.5)",
                                                                                          'strokeColor' => "rgba(220,220,220,1)",
                                                                                          'pointColor' =>"rgba(220,220,220,1)",
                                                                                          'pointStrokeColor' => "#fff",
                                                                                          'data' => array(100,90,10)),
                                                                                    array('fillColor' => "rgba(150,150,150,0.5)",
                                                                                          'strokeColor' => "rgba(150,150,150,1)",
                                                                                          'pointColor' =>"rgba(150,150,150,1)",
                                                                                          'pointStrokeColor' => "#ffa",
                                                                                          'data' => array(100,20,80))
                                                                                    )
                                                                )
                                        )
                          )
               );
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::displayCollapsed()
     */
    public function displayCollapsed() {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::hasFlushCache()
     */
    public function hasFlushCache() {
        // we don't want a flush button in frontend
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::flushCache()
     */
    public function flushCache()
    {
        return true;
    }

}
