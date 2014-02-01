<?php

class Hackathon_MageMonitoring_Model_CacheStats_Apc implements Hackathon_MageMonitoring_Model_CacheStats
{
    private $_opCacheStats;

    public function __construct()
    {
        $this->_opCacheStats = apc_cache_info();
    }

    public function getId() {
        $o = array();
        preg_match("/.+_(.+)\z/", __CLASS__, $o);
        return strtolower($o[1]);
    }

    public function getName() {
        return 'APC';
    }

    public function getVersion() {
        return phpversion('apc');
    }

    public function isActive() {
        if (extension_loaded('apc') && !extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }
        return false;
    }

    public function getMemoryMax() {
        return ini_get('apc.shm_size');
    }

    public function getMemoryUsed() {
        if (isset($this->_opCacheStats['mem_size'])) {
            return $this->_opCacheStats['mem_size'];
        }
        return 0;
    }

    public function getCacheHits() {
        if (isset($this->_opCacheStats['num_hits'])) {
            return $this->_opCacheStats['num_hits'];
        }
        return 0;
    }

    public function getCacheMisses() {
        if (isset($this->_opCacheStats['num_misses'])) {
            return $this->_opCacheStats['num_misses'];
        }
        return 0;
    }

    public function flushCache() {
        apc_clear_cache();
        apc_clear_cache('user');
        return true;
    }

}