<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Modules
    extends Mage_Adminhtml_Block_Abstract
{
    protected $_template = 'monitoring/modules.phtml';

    public function getModulesList()
    {
        return (array) Mage::getConfig()->getModuleConfig();
    }
}