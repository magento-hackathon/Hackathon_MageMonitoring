<?php
class Hackathon_MageMonitoring_Model_CacheStats_Apcu implements Hackathon_MageMonitoring_Model_CacheStats {

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
        return 'APC User Cache';
    }

    public function getVersion() {
        return phpversion('apc');
    }

    public function isActive() {
        if (extension_loaded('apc') && extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }
        return false;
    }

    public function getMemoryMax() {
        return Mage::helper('magemonitoring')->getValueInByte(ini_get('apc.shm_size'));
    }

    public function getMemoryUsed() {
        if (isset($this->_opCacheStats['mem_size'])) {
            return $this->_opCacheStats['mem_size'];
        }
        return 0;
    }

    public function getCacheHits() {
        if (isset($this->_opCacheStats['nhits'])) {
            return $this->_opCacheStats['nhits'];
        }
        return 0;
    }

    public function getCacheMisses() {
        if (isset($this->_opCacheStats['nmisses'])) {
            return $this->_opCacheStats['nmisses'];
        }
        return 0;
    }

    public function flushCache() {
        return apc_clear_cache();
    }

}