<?php
class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('magemonitoring_tabs');
        $this->setDestElementId('read_form');
        $this->setTitle($this->__('System Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
                'label'   => $this->__('Main information'),
                'title'   => $this->__('Main information'),
                'content' => $this->getLayout()->createBlock('magemonitoring/system_overview_read_tabs_main')->toHtml(),
        ));

        $this->addTab('php_section', array(
                'label'   => $this->__('PHP information'),
                'title'   => $this->__('PHP information'),
                'content' => $this->getLayout()->createBlock('magemonitoring/system_overview_read_tabs_php')->toHtml(),
        ));
        
        $this->addTab('apc_section', array(
                'label'   => $this->__('APC information'),
                'title'   => $this->__('APC information'),
                'content' => $this->getLayout()->createBlock('magemonitoring/system_overview_read_tabs_apc')->toHtml(),
        ));

        $this->addTab('example_section', array(
            'label'   => $this->__('Example'),
            'title'   => $this->__('Example'),
            'content' => $this->getLayout()->createBlock('magemonitoring/system_overview_read_tabs_example')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}