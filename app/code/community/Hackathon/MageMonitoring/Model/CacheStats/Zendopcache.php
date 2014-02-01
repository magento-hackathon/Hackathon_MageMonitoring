<?php
class Hackathon_MageMonitoring_Model_CacheStats_Zendopcache implements Hackathon_MageMonitoring_Model_CacheStats {

    public function getName() {
        return 'ZendOpCache';
    }

    public function getVersion() {
        $conf = opcache_get_configuration();
        if (isset($conf['version'])) {
            return $conf['version']['version'];
        }
        return 'ERR';
    }

    public function isActive() {
        if (extension_loaded('Zend OPcache')) {
            $conf = opcache_get_configuration();
            if (isset($conf['directives'])) {
                return $conf['directives']['opcache.enable'];
            }
        }
        return false;
    }

    public function getMemoryMax() {
        $stats = opcache_get_status();
        if (isset($stats['memory_usage'])) {
            return $stats['memory_usage']['free_memory'];
        }
        return 'ERR';
    }

    public function getMemoryUsed() {
        $stats = opcache_get_status();
        if (isset($stats['memory_usage'])) {
            return $stats['memory_usage']['used_memory'];
        }
        return 0;
    }

    public function getCacheHits() {
        $stats = opcache_get_status();
        if (isset($stats['opcache_statistics'])) {
            return $stats['opcache_statistics']['hits'];
        }
        return 0;
    }

    public function getCacheMisses() {
        $stats = opcache_get_status();
        if (isset($stats['opcache_statistics'])) {
            return $stats['opcache_statistics']['misses'];
        }
        return 0;
    }

    public function flushCache() {
        return opcache_reset();
    }

}