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
 * class Hackathon_MageMonitoring_Model_Widget_CacheStat_Redis
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_CacheStat_Redis
    extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements Hackathon_MageMonitoring_Model_Widget_CacheStat
{
    private $_redisClient;
    private $_redisInfo;

    /**
     * Constructor
     */
    public function __construct()
    {
        try {
            $cacheConfig = Mage::getConfig()->getNode('global/cache')->asArray();
            if (array_key_exists('backend', $cacheConfig) && $cacheConfig['backend'] == 'Cm_Cache_Backend_Redis') {
                $server = $cacheConfig['backend_options']['server'];
                $port = $cacheConfig['backend_options']['port'];

                $this->_redisClient = new Credis_Client($server, $port);
                $this->_redisInfo = $this->_redisClient->__call('info', array());
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Redis';
    }

    /**
     * Returns version
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        if (isset($this->_redisInfo['redis_version'])) {
            return $this->_redisInfo['redis_version'];
        }

        return 0;
    }

    /**
     * Returns isActive flag
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        if (isset($this->_redisClient) && isset($this->_redisInfo)) {
            return true;
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return 0;
    }

    /**
     * Returns used memory
     *
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_redisInfo['used_memory'])) {
            return $this->_redisInfo['used_memory'];
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
        if (isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_hits'];
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
        if (isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_misses'];
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
        $this->_redisClient->flushDb();

        return true;
    }

    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
