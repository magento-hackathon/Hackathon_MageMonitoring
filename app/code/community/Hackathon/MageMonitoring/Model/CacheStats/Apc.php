<?php

class Hackathon_MageMonitoring_Model_CacheStats_Apc implements Hackathon_MageMonitoring_Model_CacheStats
{
    public function getId() {
        $o = array();
        preg_match("/.+_(.+)\z/", __CLASS__, $o);
        return strtolower($o[1]);
    }

    public function getName()
    {
        return 'APC';
    }

    public function getVersion()
    {
        return phpversion('apc');
    }

    public function isActive()
    {
        if (extension_loaded('apc') && !extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }
        return false;
    }

    public function getMemoryMax()
    {
        return ini_get('apc.shm_size');
    }

    public function getMemoryUsed()
    {
        $stats = apc_cache_info();
        if (isset($stats['mem_size'])) {
            return $stats['mem_size'];
        }
        return 0;
    }

    public function getCacheHits()
    {
        $stats = apc_cache_info();
        if (isset($stats['num_hits'])) {
            return $stats['num_hits'];
        }
        return 'ERR';
    }

    public function getCacheMisses()
    {
        $stats = apc_cache_info();
        if (isset($stats['num_misses'])) {
            return $stats['num_misses'];
        }
        return 'ERR';
    }

    public function flushCache()
    {
        apc_clear_cache();
        apc_clear_cache('user');
        return true;
    }

}