<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Hackathon_MageMonitoring_Model_Widget_CacheStat_Zendopcache extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements Hackathon_MageMonitoring_Model_Widget_CacheStat
{
    private $_opCacheConfig;
    private $_opCacheStats;

    public function __construct()
    {
        if (extension_loaded('Zend OPcache')) {
            $this->_opCacheStats = opcache_get_status();
            $this->_opCacheConfig = opcache_get_configuration();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getName()
     */
    public function getName()
    {
        return 'ZendOpCache';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getVersion()
     */
    public function getVersion()
    {
        if (isset($this->_opCacheConfig['version'])) {
            return $this->_opCacheConfig['version']['version'];
        }

        return 'ERR';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::isActive()
     */
    public function isActive()
    {
        if (extension_loaded('Zend OPcache')) {
            if (isset($this->_opCacheConfig['directives'])) {
                return $this->_opCacheConfig['directives']['opcache.enable'];
            }
        }

        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryMax()
     */
    public function getMemoryMax()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['free_memory'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['used_memory'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['hits'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['misses'];
        }

        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::flushCache()
     */
    public function flushCache()
    {
        return opcache_reset();
    }

}
