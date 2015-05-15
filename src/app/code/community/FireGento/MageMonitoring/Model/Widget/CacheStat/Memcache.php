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
 * class FireGento_MageMonitoring_Model_Widget_CacheStat_Memcache
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_CacheStat_Memcache
    extends FireGento_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements FireGento_MageMonitoring_Model_Widget_CacheStat
{
    private $_memCachePool;
    private $_memCacheStats;

    /**
     * Constructor
     */
    public function __construct()
    {
        try {
            if ($this->_memCachePool == null && class_exists('Memcache', false)) {

                $cacheConfig = Mage::getConfig()->getNode('global/cache')->asArray();

                if ((array_key_exists('backend', $cacheConfig) && strtolower($cacheConfig['backend']) == 'memcached') ||
                    (array_key_exists('slow_backend', $cacheConfig) && strtolower(
                            $cacheConfig['slow_backend']
                        ) == 'memcached')
                ) {
                    $this->_memCachePool = new Memcache;

                    foreach ($cacheConfig['memcached']['servers'] as $server) {
                        $host = (string)$server['host'];
                        $port = (string)$server['port'];
                        $this->_memCachePool->addServer($host, $port);
                    }

                    $this->_memCacheStats = $this->_memCachePool->getStats();
                }
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Returns name
     *
     * @see FireGento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Memcache';
    }

    /**
     * Returns version
     *
     * @see FireGento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return $this->_memCachePool->getVersion();
    }

    /**
     * Returns isActive flag
     *
     * @see FireGento_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        if ($this->_memCachePool && $this->_memCachePool->getVersion()) {
            return true;
        }

        return false;
    }

    /**
     * Returns memory max
     *
     * @see FireGento_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        if (isset($this->_memCacheStats['limit_maxbytes'])) {
            return $this->_memCacheStats['limit_maxbytes'];
        }

        return 0;
    }

    /**
     * Returns used memory
     *
     * @see FireGento_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_memCacheStats['bytes'])) {
            return $this->_memCacheStats['bytes'];
        }

        return 0;
    }

    /**
     * Returns number of cache hits
     *
     * @see FireGento_MageMonitoring_Model_Widget_CacheStat::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_memCacheStats['get_hits'])) {
            return $this->_memCacheStats['get_hits'];
        }

        return 0;
    }

    /**
     * Returns number of cache misses
     *
     * @see FireGento_MageMonitoring_Model_Widget_CacheStat::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_memCacheStats['get_misses'])) {
            return $this->_memCacheStats['get_misses'];
        }

        return 0;
    }

    /**
     * Flushes cache
     *
     * @see FireGento_MageMonitoring_Model_Widget_CacheStat::flushCache()
     */
    public function flushCache()
    {
        $this->_memCachePool->flush();

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
