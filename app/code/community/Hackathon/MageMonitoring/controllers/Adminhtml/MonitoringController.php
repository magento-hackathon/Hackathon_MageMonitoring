<?php

class Hackathon_MageMonitoring_Adminhtml_MonitoringController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/tools/monitoring');
        $this->_addBreadcrumb(
            Mage::helper('magemonitoring')->__('Monitoring'),
            Mage::helper('magemonitoring')->__('Monitoring')
        );
            
        $this->_title('Overview');
        
        $this->_addContent(
            $this->getLayout()->createBlock('magemonitoring/system_overview', 'magemonitoring_overview')
        );
        $this->renderLayout();
    }

}