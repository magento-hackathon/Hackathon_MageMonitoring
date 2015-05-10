<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageMonitoring
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * class Hackathon_MageMonitoring_Model_Widget_System_Abstract
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
abstract class Hackathon_MageMonitoring_Model_Widget_System_Abstract
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    protected $_serverInfo = null;
    protected $_mageInfo = null;

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }

    /**
     * Returns requested parameter's value from the $_SERVER variable
     *
     * @param  string $value Value
     * @return string
     */
    public function getServerInfo($value)
    {
        if (is_null($this->_serverInfo)) {
            $this->_serverInfo = $_SERVER;
        }

        if (isset($this->_serverInfo[$value])) {
            return $this->_serverInfo[$value];
        } else {
            return '';
        }
    }

    /**
     * Returns memory size. Supposed to be working only on LINUX servers
     *
     * @return string|null
     */
    public function getMemoryInfo()
    {
        $fh = @fopen('/proc/meminfo', 'r');
        $mem = 0;
        if ($fh) {
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('^MemTotal:\s+(\d+)\skB$^', $line, $pieces)) {
                    $mem = $pieces[1];
                    break;
                }
            }
            fclose($fh);
        }
        if ($mem > 0) {
            $mem = $mem / 1024;
            return $mem . 'M';
        } else {
            return $this->_getTopMemoryInfo();
        }
    }

    /**
     * Returns memory size. Alternative way
     *
     * @return string|null
     */
    public function _getTopMemoryInfo()
    {
        $memInfo = exec('top -l 1 | head -n 10 | grep PhysMem');
        $memInfo = str_ireplace('PhysMem: ', '', $memInfo);

        if (!empty($memInfo)) {
            return $memInfo;
        } else {
            return null;
        }
    }

    /**
     * Returns server's CPU information. Supposed to be working only on LINUX servers
     *
     * @return null|string
     */
    public function getCpuInfo()
    {
        $cpuInfo = '';
        $fh = @fopen('/proc/cpuinfo', 'r');
        if ($fh) {
            while ($line = fgets($fh)) {
                if (stristr($line, 'model name')) {
                    $cpuInfo = $line;
                    break;
                }
            }
        }

        if (!empty($cpuInfo)) {
            return $cpuInfo;
        } else {
            return $this->_getBsdCpuInfo();
        }
    }

    /**
     * Returns requested Magento information
     *
     * @param  string $value Value
     * @return mixed
     */
    public function getMagentoInfo($value)
    {
        if (is_null($this->_mageInfo)) {
            $this->_mageInfo['version'] = Mage::getVersion() . ' ' . Mage::getEdition() . ' Edition';
            $statInfo = $this->getMagentoStatInfo();
            if (!is_null($statInfo)) {
                $this->_mageInfo = array_merge($this->_mageInfo, $statInfo);
            }
        }

        return $this->_mageInfo[$value];
    }

    /**
     * Returns server's cpu information on BSD servers
     *
     * @return null|string
     */
    protected function _getBsdCpuInfo()
    {
        $cpuInfo = exec("sysctl -a | egrep -i 'hw.model'");

        if (!empty($cpuInfo)) {

            /* If OSX is being used on server - we need a bit another way */
            if (stristr($cpuInfo, 'Mac')) {
                $cpuInfo = exec('sysctl -n machdep.cpu.brand_string');
            }

            return str_ireplace('hw.model = ', '', $cpuInfo);
        } else {
            return null;
        }
    }

    /**
     * Returns OS information
     *
     * @return string
     */
    public function getOsInfo()
    {
        $osInfo = php_uname();

        return $osInfo;
    }

    /**
     * Outputs requested $_SERVER parameter
     *
     * @param  string $value Value
     * @return array
     */
    protected function _getValue($value)
    {
        return $this->getServerInfo($value);
    }

    /**
     * Collects some useful (and not) statistic information from Magento
     *
     * @return array|null
     */
    public function getMagentoStatInfo()
    {
        try {
            $statInfo['products_count'] = Mage::getModel('catalog/product')->getCollection()->getSize();
            $statInfo['orders_count'] = Mage::getModel('sales/order')->getCollection()->getSize();
            $statInfo['customers_count'] = Mage::getModel('customer/customer')->getCollection()->getSize();
            $statInfo['online_visitors'] = Mage::getModel('log/visitor_online')->getCollection()->getSize();
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }

        return $statInfo;
    }
}
