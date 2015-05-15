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
 * Abstract cache stat widget model
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
abstract class FireGento_MageMonitoring_Model_Widget_CacheStat_Abstract
    extends FireGento_MageMonitoring_Model_Widget_Abstract
{

    /**
     * Default output for cachestat widgets.
     *
     * @see FireGento_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $block->addRow('info', 'Version', $this->getVersion());

        $block->addRow(
            $this->getMemoryCssId($this),
            'Memory',
            $this->getFormatedMemoryValue($this),
            $this->getMemoryChartData($this, $block)
        );

        $block->addRow(
            $this->getHitMissCssId($this),
            'Hit/Miss Ratio',
            $this->getFormatedHitMissValue($this),
            $this->getHitMissChartData($this, $block)
        );

        $block->addButton(
            $this,
            'flush',
            'Flush ' . $this->getName(),
            self::CALLBACK . 'flushCache',
            array('widgetId' => $this->getId(), 'refreshAfter' => true),
            'Do you really want to flush ' . $this->getName() . '?',
            'delete f-right'
        );

        $this->_output[] = $block;

        return $this->_output;
    }

    /**
     * Returns memory chart data as array, feeds FireGento_MageMonitoring_Block_Chart.
     *
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @param  FireGento_MageMonitoring_Block_Chart            $block Block
     * @return array
     */
    public function getMemoryChartData($cache, $block)
    {
        $free = (int)$cache->getMemoryMax() - (int)$cache->getMemoryUsed();
        $used = $cache->getMemoryUsed();
        $chartData = array(
            array('value' => $free, 'color' => '#00f000'),
            array('value' => $used, 'color' => '#f00000')
        );

        return $block->newChartArray('chart_memory', $chartData);
    }

    /**
     * Returns hit/miss chart data as array, feeds FireGento_MageMonitoring_Block_Chart.
     *
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @param  FireGento_MageMonitoring_Block_Chart            $block Block
     * @return array
     */
    public function getHitMissChartData($cache, $block)
    {
        $hits = $cache->getCacheHits();
        $misses = $cache->getCacheMisses();
        $chartData = array(
            array('value' => $hits, 'color' => '#00f000'),
            array('value' => $misses, 'color' => '#f00000')
        );

        return $block->newChartArray('chart_hitmiss', $chartData);
    }

    /**
     * Takes $value and compares it with given thresholds.
     * Returns 'error' if $value <= $errorTresh, 'warning' if $value <= $warningThres
     *
     * @param  int $value         Value
     * @param  int $errorTresh    Error threshold
     * @param  int $warningThresh Warning threshold
     * @return string
     */
    public function getCssIdByThreshold($value, $errorTresh = 0, $warningThresh = 0)
    {
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
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @return string
     */
    public function getMemoryCssId($cache)
    {
        if ($cache->getMemoryMax() != 0) {
            $freeMemRatio = 100 - round(
                $cache->getMemoryUsed() * 100 / (0 === $cache->getMemoryMax() ? 1 : $cache->getMemoryMax())
            );
        } else {
            $freeMemRatio = 0;
        }

        return $this->getCssIdByThreshold($freeMemRatio, 10, 25);
    }

    /**
     * Returns css class id for hit/miss stats.
     *
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @return string
     */
    public function getHitMissCssId($cache)
    {
        $hitMissRatio = round($this->getHitRatio($cache->getCacheHits(), $cache->getCacheMisses()));

        return $this->getCssIdByThreshold($hitMissRatio, 50, 75);
    }

    /**
     * Returns hit/miss percentage.
     *
     * @param  int $hits   Number of hits
     * @param  int $misses Number of misses
     * @return number
     */
    public function getHitRatio($hits, $misses)
    {
        $total = $hits + $misses;
        if ($total != 0) {
            return round($hits * 100 / $total, 2);
        } else {
            return 0;
        }
    }

    /**
     * Format memory values for frontend
     *
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @return string
     */
    public function getFormatedMemoryValue($cache)
    {
        $used = Mage::helper('magemonitoring')->getValueInByte($cache->getMemoryUsed(), true);
        $max  = Mage::helper('magemonitoring')->getValueInByte($cache->getMemoryMax(), true);

        return $used . 'M / ' . $max . 'M';
    }

    /**
     * Format memory values for frontend
     *
     * @param  FireGento_MageMonitoring_Model_Widget_CacheStat $cache Cache model
     * @return string
     */
    public function getFormatedHitMissValue($cache)
    {
        $hits   = $cache->getCacheHits();
        $misses = $cache->getCacheMisses();

        return $hits . ' / ' . $misses . ' - ' . $this->getHitRatio($hits, $misses) . '%';
    }
}
