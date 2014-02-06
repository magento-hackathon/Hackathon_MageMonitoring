<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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

    // ajax refresh
    public function refreshWidgetAction() {
        $response = "ERR";
        if ($id = $this->getRequest()->getParam('widgetId', null)) {
            $widget = new $id();
            $widget->loadConfig();
            $response = $this->getLayout()->createBlock('core/template')
                ->setTemplate('monitoring/widget/body.phtml')
                ->setData('output', $widget->getOutput())
                ->setData('buttons', $widget->getButtons())
                ->toHtml();
        }
        $this->getResponse()
             ->clearHeaders()
             ->setHeader('Content-Type', 'application/json')
             ->setBody($response);
    }

    // get widget config html
    public function getWidgetConfAction() {
        $response = "ERR";
        if ($id = $this->getRequest()->getParam('widgetId', null)) {
            $widget = new $id();
            $widget->loadConfig();
            $response = $this->getLayout()->createBlock('core/template')
                                                    ->setTemplate('monitoring/widget/config.phtml')
                                                    ->setData('widget', $widget)
                                                    ->toHtml();
        }
        $this->getResponse()->setBody($response);
    }

    // save widget config
    public function saveWidgetConfAction() {
        $response = "ERR";
        if ($id = $this->getRequest()->getParam('widgetId')) {
            $widget = new $id();
            $post = $this->getRequest()->getPost();
            unset($post['form_key']);
            $widget->saveConfig($post);
            $response = 'Settings saved for '.$widget->getName().' Reload the page or widget.';
        }
        $this->getResponse()->setBody($response);
    }

    // delete widget config
    public function resetWidgetConfAction() {
        $response = "ERR";
        if ($id = $this->getRequest()->getParam('widgetId')) {
            $widget = new $id();
            $widget->deleteConfig();
            $response = 'Deleted config for ' . $widget->getName();
        }
        $this->getResponse()->setBody($response);
    }

    public function flushAllCacheAction()
    {
        try {
            $caches = Mage::helper('magemonitoring')->getActiveWidgets('CacheStat');

            foreach ($caches as $cache) {
                if ($cache instanceof Hackathon_MageMonitoring_Model_Widget_CacheStat) {
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
        $cacheId = (string) $this->getRequest()->getParam('cache');

        if ($cacheId) {
            try {

                $cache = Mage::helper('magemonitoring')->getActiveWidgets('CacheStat', $cacheId);
                if (!empty($cache) && $cache instanceof Hackathon_MageMonitoring_Model_Widget_CacheStat) {
                    $cache->flushCache();
                }

                $this->_getSession()->addSuccess($this->__('Cache %s flushed with success', $cacheId));
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->__toString());
            }
        }

        return $this->_redirect('*/*/index');
    }

}