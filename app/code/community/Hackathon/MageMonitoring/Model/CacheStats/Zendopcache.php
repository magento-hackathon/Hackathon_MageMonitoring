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

class Hackathon_MageMonitoring_Model_CacheStats_Zendopcache implements Hackathon_MageMonitoring_Model_CacheStats
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

    public function getId()
    {
        $o = array();
        preg_match("/.+_(.+)\z/", __CLASS__, $o);
        return strtolower($o[1]);
    }

    public function getName()
    {
        return 'ZendOpCache';
    }

    public function getVersion()
    {
        if (isset($this->_opCacheConfig['version'])) {
            return $this->_opCacheConfig['version']['version'];
        }
        return 'ERR';
    }

    public function isActive()
    {
        if (extension_loaded('Zend OPcache')) {
            if (isset($this->_opCacheConfig['directives'])) {
                return $this->_opCacheConfig['directives']['opcache.enable'];
            }
        }
        return false;
    }

    public function getMemoryMax()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['free_memory'];
        }
        return 0;
    }

    public function getMemoryUsed()
    {
        if (isset($this->_opCacheStats['memory_usage'])) {
            return $this->_opCacheStats['memory_usage']['used_memory'];
        }
        return 0;
    }

    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['hits'];
        }
        return 0;
    }

    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['opcache_statistics'])) {
            return $this->_opCacheStats['opcache_statistics']['misses'];
        }
        return 0;
    }

    public function flushCache()
    {
        return opcache_reset();
    }
}