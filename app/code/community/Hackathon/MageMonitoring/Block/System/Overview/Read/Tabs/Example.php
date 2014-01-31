<?php
class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Example extends Mage_Adminhtml_Block_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('monitoring/example.phtml');
        return parent::_construct();
    }
}