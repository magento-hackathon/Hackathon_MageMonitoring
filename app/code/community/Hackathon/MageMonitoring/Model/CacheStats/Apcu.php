<?php
class Hackathon_MageMonitoring_Model_CacheStats_Apcu implements Hackathon_MageMonitoring_Model_CacheStats {

    public function getName() {
        return 'APCU';
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
        return ini_get('apc.shm_size');
    }

    public function getMemoryUsed() {
        $stats = apc_cache_info();
        if (isset($stats['mem_size'])) {
            return $stats['mem_size'];
        }
        return 0;
    }

    public function getCacheHits() {
        $stats = apc_cache_info();
        if (isset($stats['num_hits'])) {
            return $stats['num_hits'];
        }
        return 'ERR';
    }

    public function getCacheMisses() {
        $stats = apc_cache_info();
        if (isset($stats['num_misses'])) {
            return $stats['num_misses'];
        }
        return 'ERR';
    }

    public function flushCache() {
        return apc_clear_cache();
    }

}