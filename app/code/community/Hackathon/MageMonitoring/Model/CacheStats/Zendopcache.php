<?php
class Hackathon_MageMonitoring_Model_CacheStats_Zendopcache implements Hackathon_MageMonitoring_Model_CacheStats {

    private $_opCacheConfig;
    private $_opCacheStats;

    public function __construct()
    {
        $this->_opCacheStats = opcache_get_status();
        $this->_opCacheConfig = opcache_get_configuration();
    }

    public function getId() {
        $o = array();
        preg_match("/.+_(.+)\z/", __CLASS__, $o);
        return strtolower($o[1]);
    }

    public function getName() {
        return 'ZendOpCache';
    }

    public function getVersion() {
        if (isset($this->_opCacheConfig['version'])) {
            return $this->_opCacheConfig['version']['version'];
        }
        return 'ERR';
    }

    public function isActive() {
        if (extension_loaded('Zend OPcache')) {
            if (isset($this->_opCacheConfig['directives'])) {
                return $this->_opCacheConfig['directives']['opcache.enable'];
            }
        }
        return false;
    }

    public function getMemoryMax() {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['free_memory'];
        }
        return 0;
    }

    public function getMemoryUsed() {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['used_memory'];
        }
        return 0;
    }

    public function getCacheHits() {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['hits'];
        }
        return 0;
    }

    public function getCacheMisses() {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['misses'];
        }
        return 0;
    }

    public function flushCache() {
        return opcache_reset();
    }

}