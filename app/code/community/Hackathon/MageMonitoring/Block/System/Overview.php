<?php

class Hackathon_MageMonitoring_Block_System_Overview extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'magemonitoring';
        $this->_controller = 'system_overview';
        $this->_headerText = $this->__('System Overview');
        $this->_mode = 'read';
        
        $this->removeButton('reset');
        $this->removeButton('save');
    }
    
    protected function _prepareLayout()
    {        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('magemonitoring/system_overview_read_tabs', 'magemonitoring_tabs'));
        return parent::_prepareLayout();
    }

}