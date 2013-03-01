<?php
class Diglin_Monitoring_Block_System_Overview extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'monitoring';
        $this->_controller = 'system_overview';
        $this->_headerText = $this->__('System Overview');
        $this->_mode = 'read';
        
        $this->removeButton('reset');
        $this->removeButton('save');
    }
    
    protected function _prepareLayout()
    {        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('monitoring/system_overview_read_tabs', 'monitoring_tabs'));
        return parent::_prepareLayout();
    }
}