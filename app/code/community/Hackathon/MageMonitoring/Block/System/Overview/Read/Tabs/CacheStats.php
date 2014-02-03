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

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_CacheStats extends Mage_Adminhtml_Block_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('monitoring/cache_stats.phtml');
        return parent::_construct();
    }

    /**
     * Returns memory chart data as array, feeds Hackathon_MageMonitoring_Block_Chart.
     *
     * @param Hackathon_MageMonitoring_Model_CacheStats $cache
     * @return array
     */
    public function getMemoryChartData($cache) {
        $free = (int)$cache->getMemoryMax()-(int)$cache->getMemoryUsed();
        $used = $cache->getMemoryUsed();
        return array( 'chart_id' => $cache->getId().'_chart_memory',
                      'chart_type' => 'Pie',
                      'canvas_width' => 76,
                      'canvas_height' => 76,
                      'chart_data' => array(array('value' => $free, 'color' => '#00f000'),
                                            array('value' => $used, 'color' => '#f00000')
                                            )
                    );
    }

    /**
     * Returns css class id for memory stats.
     *
     * @param Hackathon_MageMonitoring_Model_CacheStats $cache
     * @return string
     */
    public function getMemoryCssId($cache) {
        $freeMemRatio = 100 - round($cache->getMemoryUsed()*100/$cache->getMemoryMax());
        $id = 'success';
        switch ($freeMemRatio) {
            case 0:
            case $freeMemRatio <= 10:
                $id = 'error';
                break;
            case $freeMemRatio <= 25:
                $id = 'warning';
                break;
        }
        return $id;
    }

    /**
     * Returns hit/miss chart data as array, feeds Hackathon_MageMonitoring_Block_Chart.
     *
     * @param Hackathon_MageMonitoring_Model_CacheStats $cache
     * @return array
     */
    public function getHitMissChartData($cache) {
        $hits = $cache->getCacheHits();
        $misses = $cache->getCacheMisses();
        return array( 'chart_id' => $cache->getId().'_chart_hitmiss',
                      'chart_type' => 'Pie',
                      'canvas_width' => 76,
                      'canvas_height' => 76,
                      'chart_data' => array(array('value' => $hits, 'color' => '#00f000'),
                                            array('value' => $misses, 'color' => '#f00000')
                                            )
                    );
    }

    /**
     * Returns css class id for hit/miss stats.
     *
     * @param Hackathon_MageMonitoring_Model_CacheStats $cache
     * @return string
     */
    public function getHitMissCssId($cache) {
        $hitMissRatio = round($this->getHitRatio($cache->getCacheHits(), $cache->getCacheMisses()));
        $id = 'success';
        switch ($hitMissRatio) {
            case 0:
            case $hitMissRatio <= 50:
                $id = 'error';
                break;
            case $hitMissRatio <= 75:
                $id = 'warning';
                break;
        }
        return $id;
    }

    /**
     * Returns hit/miss percentage.
     *
     * @param int $hits
     * @param int $misses
     * @return number
     */
    public function getHitRatio($hits, $misses) {
        $total = $misses+$hits;
        if ($total != 0) {
            return round($hits*100/$total,2);
        } else {
            return 0;
        }
    }

}