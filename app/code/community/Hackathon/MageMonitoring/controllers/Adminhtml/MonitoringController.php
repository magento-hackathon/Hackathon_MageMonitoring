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

    public function flushAllCacheAction()
    {
        try {
            $caches = Mage::helper('magemonitoring')->getActiveCaches();

            foreach ($caches as $cache) {
                if (method_exists($cache, 'flushCache')) {
                    $cache->flushCache();
                }
            }

            $this->_getSession()->addSuccess($this->__('All caches flushed with success'));

        } catch (Exception $e) {
            MAge::logException($e);
            $this->_getSession()->addError($e->__toString());
        }

        return $this->_redirect('*/*/index');
    }

    public function flushCacheAction()
    {
        $cacheName = (string) $this->getRequest()->getParam('cache');

        if ($cacheName) {
            try {

                $cache = Mage::helper('magemonitoring')->getActiveCaches($cacheName);
                if (!empty($cache) && $cache instanceof Hackathon_MageMonitoring_Model_CacheStats && method_exists($cache, 'flushCache')) {
                    $cache->flushCache();
                }

                $this->_getSession()->addSuccess($this->__('Caches %s flushed with success', $cacheName));
            } catch (Exception $e) {
                MAge::logException($e);
                $this->_getSession()->addError($e->__toString());
            }
        }

        return $this->_redirect('*/*/index');
    }

}