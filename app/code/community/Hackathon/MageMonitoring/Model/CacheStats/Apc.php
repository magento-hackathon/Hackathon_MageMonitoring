<?php
class Hackathon_MageMonitoring_Model_CacheStats_Apc implements Hackathon_MageMonitoring_Model_CacheStats {

    public function getName() {
        return 'APC';
    }

    public function getVersion() {
        return phpversion('apc');
    }

    public function getMemoryMax() {
        return ini_get('apc.shm_size');
    }

    public function getMemoryUsed() {
        $stats = apc_cache_info();
        if (isset($stats['mem_size'])) {
            return $stats['mem_size'];
        }
        return 'ERR';
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
        apc_clear_cache();
        apc_clear_cache('user');
        return true;
    }

}