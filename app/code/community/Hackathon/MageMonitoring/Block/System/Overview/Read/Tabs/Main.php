<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Main extends Mage_Adminhtml_Block_Abstract
{
    protected $_serverInfo = null;

    protected function _construct()
    {
        $this->setTemplate('monitoring/main.phtml');
        return parent::_construct();
    }

    /**
     * Returns requested parameter's value from the $_SERVER variable
     *
     * @param string $value
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
        $fh = fopen('/proc/meminfo', 'r');
        $mem = 0;
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('^MemTotal:\s+(\d+)\skB$', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose($fh);
        if ($mem > 0) {
            $mem = $mem / 1024;
            return $mem . 'M';
        } else {
            return $this->_getTopMemoryInfo();
        }
    }

    /**
     * Returns memory size. Alternative way
     * @return string|null
     */
    public function _getTopMemoryInfo()
    {
        $meminfo = exec('top -l 1 | head -n 10 | grep PhysMem');
        $meminfo = str_ireplace('PhysMem: ', '', $meminfo);

        if (!empty($meminfo)) {
            return $meminfo;
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
        $fh = fopen('/proc/cpuinfo', 'r');
        while ($line = fgets($fh)) {
            if (stristr($line, 'model name')) {
                $cpuInfo = $line;
                break;
            }
        }

        if (!empty($cpuInfo)) {
            return $cpuInfo;
        } else {
            return $this->_getBsdCpuInfo();
        }
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
}