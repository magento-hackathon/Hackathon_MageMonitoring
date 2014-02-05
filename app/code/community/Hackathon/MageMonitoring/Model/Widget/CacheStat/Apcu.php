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
class Hackathon_MageMonitoring_Model_Widget_CacheStat_Apcu extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
                                                           implements Hackathon_MageMonitoring_Model_Widget_CacheStat
{
    private $_opCacheStats;

    public function __construct()
    {
        if (extension_loaded('apc') && extension_loaded('apcu')) {
            $this->_opCacheStats = apc_cache_info();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getId()
     */
    public function getId() {
        return $this->getClassId(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'APC User Cache';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return phpversion('apc');
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        if (extension_loaded('apc') && extension_loaded('apcu') && ini_get('apc.enabled')) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return Mage::helper('magemonitoring')->getValueInByte(ini_get('apc.shm_size'));
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
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
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheHits()
     */
    public function getCacheHits()
    {
        if (isset($this->_opCacheStats['nhits'])) {
            return $this->_opCacheStats['nhits'];
        }
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheMisses()
     */
    public function getCacheMisses()
    {
        if (isset($this->_opCacheStats['nmisses'])) {
            return $this->_opCacheStats['nmisses'];
        }
        return 0;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::flushCache()
     */
    public function flushCache()
    {
        return apc_clear_cache();
    }

}
