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

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Overview
    extends Mage_Adminhtml_Block_Abstract
{
    protected $_serverInfo = null;
    protected $_mageInfo = null;

    protected function _construct()
    {
        $this->setTemplate('monitoring/overview.phtml');
        return parent::_construct();
    }

    /**
     * Returns requested parameter's value from the $_SERVER variable
     *
     * @param string $value
     *
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
     * @param $value
     * @return mixed
     */
    public function getMagentoInfo($value)
    {
        if (is_null($this->_mageInfo)) {
            $this->_mageInfo['version'] = ' ' . Mage::getEdition() . ' Edition';
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
     * @param string $value
     */
    protected function _getValue($value)
    {
       echo $this->getServerInfo($value);
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