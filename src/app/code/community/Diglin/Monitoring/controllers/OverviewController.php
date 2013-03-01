<?php
class Diglin_Monitoring_OverviewController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/tools/monitoring');
        $this->_addBreadcrumb(Mage::helper('monitoring')->__('Monitoring'), Mage::helper('monitoring')->__('Monitoring'));
            
        $this->_title('Overview');
        
        $this->_addContent($this->getLayout()->createBlock('monitoring/system_overview', 'monitoring_overview'));
        $this->renderLayout();
    }
}