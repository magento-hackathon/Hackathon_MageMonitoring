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
 * Widget cache stat APC
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_CacheStat_Apc
    extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements Hackathon_MageMonitoring_Model_Widget_CacheStat
{
    private $_opCacheStats;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (extension_loaded('apc') && !extension_loaded('apcu')) {
            $this->_opCacheStats = apc_cache_info();
        }
    }

    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'APC';
    }

    /**
     * Returns version
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return phpversion('apc');
    }

    /**
     * Returns isActive state
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        if (extension_loaded('apc') && !extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }

        return false;
    }

    /**
     * Returns memory max
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return Mage::helper('magemonitoring')->getValueInByte(ini_get('apc.shm_size'));
    }

    /**
     * Returns used memory
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_opCacheStats['mem_size'])) {
            return $this->_opCacheStats['mem_size'];
        }

        return 0;
    }

    /**
     * Returns number of cache hits
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['num_hits'])) {
            return $this->_opCacheStats['num_hits'];
        }

        return 0;
    }

    /**
     * Returns number of cache misses
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['num_misses'])) {
            return $this->_opCacheStats['num_misses'];
        }

        return 0;
    }

    /**
     * Flushes cache
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::flushCache()
     */
    public function flushCache()
    {
        apc_clear_cache();
        apc_clear_cache('user');

        return true;
    }

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
