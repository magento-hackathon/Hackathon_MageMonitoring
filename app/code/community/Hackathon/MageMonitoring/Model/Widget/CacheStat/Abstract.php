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
class Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    /**
     * Default output for cachestat widgets.
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput() {
        $this->addRow('info', 'Version', $this->getVersion());

        $this->addRow($this->getMemoryCssId($this),
                'Memory',
                $this->getFormatedMemoryValue($this),
                $this->getMemoryChartData($this));

        $this->addRow($this->getHitMissCssId($this),
                'Hit/Miss Ratio',
                $this->getFormatedHitMissValue($this),
                $this->getHitMissChartData($this));

        $this->addFlushButton($this);
        return $this->_output;
    }

    /**
     * Returns memory chart data as array, feeds Hackathon_MageMonitoring_Block_Chart.
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return array
     */
    public function getMemoryChartData($cache) {
        $free = (int)$cache->getMemoryMax()-(int)$cache->getMemoryUsed();
        $used = $cache->getMemoryUsed();
        $chartData = array(array('value' => $free, 'color' => '#00f000'),
                           array('value' => $used, 'color' => '#f00000')
                          );
        return $this->createChartArray($cache->getId().'_chart_memory', $chartData);
    }

    /**
     * Returns hit/miss chart data as array, feeds Hackathon_MageMonitoring_Block_Chart.
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return array
     */
    public function getHitMissChartData($cache) {
        $hits = $cache->getCacheHits();
        $misses = $cache->getCacheMisses();
        $chartData = array(array('value' => $hits, 'color' => '#00f000'),
                array('value' => $misses, 'color' => '#f00000')
        );
        return $this->createChartArray($cache->getId().'_chart_hitmiss', $chartData);
    }

    /**
     * Takes $value and compares it with given thresholds.
     * Returns 'error' if $value <= $errorTresh, 'warning' if $value <= $warningThres
     *
     * @param int $value
     * @param int $errorTresh
     * @param int $warningThresh
     * @return string
     */
    public function getCssIdByThreshold($value, $errorTresh=0, $warningThresh=0) {
        $id = 'success';
        switch ($value) {
            case 0:
            case $value <= $errorTresh:
                $id = 'error';
                break;
            case $value <= $warningThresh:
                $id = 'warning';
                break;
        }
        return $id;
    }

    /**
     * Returns css class id for memory stats.
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return string
     */
    public function getMemoryCssId($cache) {
        $freeMemRatio = 0;
        if ($cache->getMemoryMax() > 0) {
            $freeMemRatio = 100 - round($cache->getMemoryUsed()*100/$cache->getMemoryMax());
        }
        return $this->getCssIdByThreshold($freeMemRatio, 10, 25);
    }

    /**
     * Returns css class id for hit/miss stats.
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return string
     */
    public function getHitMissCssId($cache) {
        $hitMissRatio = round($this->getHitRatio($cache->getCacheHits(), $cache->getCacheMisses()));
        return $this->getCssIdByThreshold($hitMissRatio, 50, 75);
    }

    /**
     * Returns hit/miss percentage.
     *
     * @param int $hits
     * @param int $misses
     * @return number
     */
    public function getHitRatio($hits, $misses) {
        $total = $hits + $misses;
        if ($total != 0) {
            return round($hits*100/$total,2);
        } else {
            return 0;
        }
    }

    /**
     * Format memory values for frontend
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return string
     */
    public function getFormatedMemoryValue($cache) {
        $used = Mage::helper('magemonitoring')->getValueInByte($cache->getMemoryUsed(), true);
        $max = Mage::helper('magemonitoring')->getValueInByte($cache->getMemoryMax(), true);
        return $used . 'M / ' . $max . 'M';
    }

    /**
     * Format memory values for frontend
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return string
     */
    public function getFormatedHitMissValue($cache) {
        $hits = $cache->getCacheHits();
        $misses = $cache->getCacheMisses();
        return $hits . ' / ' . $misses . ' - ' . $this->getHitRatio($hits, $misses) . '%';
    }

    /**
     * Returns default flush cache button.
     *
     * @param Hackathon_MageMonitoring_Model_Widget_CacheStat $cache
     * @return $this
     */
    public function addFlushButton($cache) {
        $this->addButton('flush'.$this->getId(),
                         'Flush '.$this->getName(),
                         '*/*/flushcache',
                         array('cache' => $this->getId()),
                         'Do you really want to flush ' . $this->getName(),
                         'delete f-right');
        return $this;
    }

}
