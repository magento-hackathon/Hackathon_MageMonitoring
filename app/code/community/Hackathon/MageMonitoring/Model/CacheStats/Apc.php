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
class Hackathon_MageMonitoring_Model_CacheStats_Apc extends Hackathon_MageMonitoring_Model_CacheStats_Abstract
                                                    implements Hackathon_MageMonitoring_Model_CacheStats
{
    private $_opCacheStats;

    public function __construct()
    {
        if (extension_loaded('apc') && !extension_loaded('apcu')) {
            $this->_opCacheStats = apc_cache_info();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getId()
     */
    public function getId() {
        return parent::getId(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getName()
     */
    public function getName()
    {
        return 'APC';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getVersion()
     */
    public function getVersion()
    {
        return phpversion('apc');
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::isActive()
     */
    public function isActive()
    {
        if (extension_loaded('apc') && !extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return Mage::helper('magemonitoring')->getValueInByte(ini_get('apc.shm_size'));
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        if (isset($this->_opCacheStats['mem_size'])) {
            return $this->_opCacheStats['mem_size'];
        }
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['num_hits'])) {
            return $this->_opCacheStats['num_hits'];
        }
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['num_misses'])) {
            return $this->_opCacheStats['num_misses'];
        }
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_CacheStats::flushCache()
     */
    public function flushCache()
    {
        apc_clear_cache();
        apc_clear_cache('user');
        return true;
    }
}
