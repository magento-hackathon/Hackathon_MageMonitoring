<?php
class Hackathon_MageMonitoring_Model_CacheStats_Memcache implements Hackathon_MageMonitoring_Model_CacheStats {

    private $_memCachePool;

    public function __construct()
    {
        try {
            if ($this->_memCachePool == null && class_exists('Memcache', false)) {
                $this->_memCachePool = new Memcache;
                $this->_memCachePool->addServer('127.0.0.1', 11211);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function getId() {
        $o = array();
        preg_match("/.+_(.+)\z/", __CLASS__, $o);
        return strtolower($o[1]);
    }

    public function getName() {
        return 'Memcache';
    }

    public function getVersion() {
        return $this->_memCachePool->getVersion();
    }

    public function isActive() {
        if ($this->_memCachePool && $this->_memCachePool->getVersion()) {
            return true;
        }
        return false;
    }

    public function getMemoryMax() {
        $stats = $this->_memCachePool->getStats();
        if (isset($stats['limit_maxbytes'])) {
            return $stats['limit_maxbytes'];
        }
        return 'ERR';
    }

    public function getMemoryUsed() {
            $stats = $this->_memCachePool->getStats();
        if (isset($stats['bytes'])) {
            return $stats['bytes'];
        }
        return 0;
    }

    public function getCacheHits() {
            $stats = $this->_memCachePool->getStats();
        if (isset($stats['get_hits'])) {
            return $stats['get_hits'];
        }
        return 0;
    }

    public function getCacheMisses() {
        $stats = $this->_memCachePool->getStats();
        if (isset($stats['get_misses'])) {
            return $stats['get_misses'];
        }
        return 0;
    }

    public function flushCache() {
        $this->_memCachePool->flush();
        return true;
    }

}