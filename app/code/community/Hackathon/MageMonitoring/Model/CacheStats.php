<?php
interface Hackathon_MageMonitoring_Model_CacheStats {

    /**
     * Returns cache name.
     *
     * @return string
     */
    public function getName();
    /**
     * Returns version information of cache.
     *
     * @return string
     */
    public function getVersion();
    /**
     * Returns maximum cache size in bytes, return false if not implemented
     *
     * @return int
     */
    public function getMemoryMax();
    /**
     * Returns used cache size in bytes, return false if not implemented
     *
     * @return int
     */
    public function getMemoryUsed();
    /**
     * Returns cache hit count, return false if not implemented
     *
     * @return int
     */
    public function getCacheHits();
    /**
     * Returns cache miss count, return false if not implemented
     *
     * @return int
     */
    public function getCacheMisses();
    /**
     * Whooosh!
     *
     * @return bool
     */
    public function flushCache();

}