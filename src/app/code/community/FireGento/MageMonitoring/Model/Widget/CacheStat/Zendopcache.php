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
 * class FireGento_MageMonitoring_Model_Widget_CacheStat_Zendopcache
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_CacheStat_Zendopcache
    extends FireGento_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements FireGento_MageMonitoring_Model_Widget_CacheStat
{
    private $_opCacheConfig;
    private $_opCacheStats;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (extension_loaded('Zend OPcache')) {
            $this->_opCacheStats = opcache_get_status();
            $this->_opCacheConfig = opcache_get_configuration();
        }
    }

    /**
     * Returns name
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getName()
     */
    public function getName()
    {
        return 'ZendOpCache';
    }

    /**
     * Returns version
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getVersion()
     */
    public function getVersion()
    {
        if (isset($this->_opCacheConfig['version'])) {
            return $this->_opCacheConfig['version']['version'];
        }

        return 'ERR';
    }

    /**
     * Returns isActive flag
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::isActive()
     */
    public function isActive()
    {
        if (extension_loaded('Zend OPcache')) {
            if (isset($this->_opCacheConfig['directives'])) {
                return $this->_opCacheConfig['directives']['opcache.enable'];
            }
        }

        return false;
    }

    /**
     * Returns memory max
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getMemoryMax()
     */
    public function getMemoryMax()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['free_memory'];
        }

        return 0;
    }

    /**
     * Returns used memory
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['used_memory'];
        }

        return 0;
    }

    /**
     * Returns number of cache hits
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['hits'];
        }

        return 0;
    }

    /**
     * Returns number of cache misses
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['misses'];
        }

        return 0;
    }

    /**
     * Flushes cache
     *
     * @see FireGento_MageMonitoring_Model_CacheStats::flushCache()
     */
    public function flushCache()
    {
        return opcache_reset();
    }

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
