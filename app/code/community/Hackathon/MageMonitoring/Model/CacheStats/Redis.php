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
class Hackathon_MageMonitoring_Model_CacheStats_Redis implements Hackathon_MageMonitoring_Model_CacheStats
{
    private $_redisClient;
    private $_redisInfo;

    public function __construct()
    {
        try {
            $localXml = simplexml_load_file('app/etc/local.xml', null, LIBXML_NOCDATA);
            $cacheBackend = $localXml->xpath('//cache/backend');

            if ((string)$cacheBackend[0] == 'Cm_Cache_Backend_Redis') {
                if ($backendOptions = $localXml->xpath('//cache/backend_options')) {
                    $server = (string)$backendOptions[0]->server;
                    $port   = (int)$backendOptions[0]->port;

                    $this->_redisClient = new Credis_Client($server, $port);
                    $this->_redisInfo = $this->_redisClient->__call('info', array());
                }
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function getId()
    {
        return $this->_redisInfo['process_id'];
    }

    public function getName()
    {
        return 'Redis';
    }

    public function getVersion()
    {
        if(isset($this->_redisInfo['redis_version'])) {
            return $this->_redisInfo['redis_version'];
        }
        return 0;
    }

    public function isActive()
    {
        if(isset($this->_redisClient) && isset($this->_redisInfo)) {
            return true;
        }
        return false;
    }

    public function getMemoryMax()
    {
        return 0;
    }

    public function getMemoryUsed()
    {
        if(isset($this->_redisInfo['used_memory'])) {
            return $this->_redisInfo['used_memory'];
        }
        return 0;
    }

    public function getCacheHits()
    {
        if(isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_hits'];
        }
        return 0;
    }

    public function getCacheMisses()
    {
        if(isset($this->_redisInfo['keyspace_hits'])) {
            return $this->_redisInfo['keyspace_misses'];
        }
        return 0;
    }

    public function flushCache()
    {
        $this->_redisClient->flushDb();
        return true;
    }

}