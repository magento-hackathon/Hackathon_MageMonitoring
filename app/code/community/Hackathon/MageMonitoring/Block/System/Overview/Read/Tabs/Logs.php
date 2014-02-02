<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Logs extends Mage_Adminhtml_Block_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('monitoring/logs.phtml');
        return parent::_construct();
    }

    public function getDebugLog($lines=30) {
        $debugLogName = Mage::getStoreConfig('dev/log/file');
        return Mage::helper('magemonitoring')->tailFile('var/log/'.$debugLogName, $lines);
    }

    public function getExceptionLog($lines=30) {
        $exLogName = Mage::getStoreConfig('dev/log/exception_file');
        return Mage::helper('magemonitoring')->tailFile('var/log/'.$exLogName, $lines);
    }
}