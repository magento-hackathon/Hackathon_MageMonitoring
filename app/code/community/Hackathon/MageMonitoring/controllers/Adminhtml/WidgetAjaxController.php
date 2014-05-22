<?php
/**
 * Hackathon
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

class Hackathon_MageMonitoring_Adminhtml_WidgetAjaxController extends Mage_Adminhtml_Controller_Action
{

    // ajax refresh
    public function refreshWidgetAction()
    {
        $response = '';
        if ($widget = $this->_getWidgetFromRequest()) {
            foreach ($widget->getOutput() as $blocks) {
                $response .= $blocks->toHtml();
            }
        }
        if ($response == '') {
            $response = 'ERR';
        }
        $this->getResponse()
             ->clearHeaders()
             ->setHeader('Content-Type', 'application/json')
             ->setBody($response);
    }

    // get widget config html
    public function getWidgetConfAction()
    {
        $response = "ERR";
        if ($widget = $this->_getWidgetFromRequest()) {
            $response = $this->getLayout()->createBlock('core/template')
                ->setTemplate('monitoring/widget/config.phtml')
                ->setData('widget', $widget)
                ->setData('tab_id', $this->getRequest()->getParam('tabId'))
                ->toHtml();
        }
        $this->getResponse()->setBody($response);
    }

    // save widget config
    public function saveWidgetConfAction()
    {
        $response = "ERR";
        $post = $this->getRequest()->getPost();
        $className = null;
        if (array_key_exists('class_name', $post)) {
            $className = $post['class_name'];
        }
        $widgetDbId = null;
        if (array_key_exists('widget_id', $post)) {
            $widgetDbId = $post['widget_id'];
        }
        if ($widget = $this->_getWidgetFromRequest($className, $widgetDbId)) {
            // ignore display prio if we save from config tab page
            $ref = $this->getRequest()->getServer('HTTP_REFERER');
            if (strpos($ref, '/config_tabs/') !== false) {
                $post['display_prio'] = $widget->getConfig('display_prio', true);
            }
            unset($post['form_key']);
            $widget->saveConfig($post);
            $response = 'Settings saved for '.$widget->getName().'. Changing display prio and collapseable state requires a page reload.';
        }
        $this->getResponse()->setBody($response);
    }

    // delete widget config
    public function resetWidgetConfAction()
    {
        $response = "ERR";
        if ($widget = $this->_getWidgetFromRequest()) {
            $widget->deleteConfig();
            $response = 'Deleted config for ' . $widget->getName();
        }
        $this->getResponse()->setBody($response);
    }

    // tab config - get widget config form html
    public function getWidgetConfigFormAction()
    {
        $response = "ERR";
        if ($widget = $this->_getWidgetFromRequest()) {
            $response = $this->getLayout()->createBlock('magemonitoring/tab_config_form_widgetConf')
                ->setData('widget', $widget)
                ->setData('tab_id', $this->getRequest()->getParam('tabId'))
                ->setData('widget_id_org', $this->getRequest()->getParam('widgetId'))
                ->toHtml();
        }
        $this->getResponse()->setBody($response);
    }

    // save tab config
    public function saveTabConfigAction()
    {
        $response = array();
        $hasError = false;
        $data = Mage::helper('core')->jsonDecode($this->getRequest()->getPost('data'));

        try {
            $this->_saveTabConfig($data);
            $this->_getSession()->addSuccess($this->__('Tab config has been saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addException($e,
                    $this->__('An error occurred while saving the tab config.'));
            $hasError = true;
        }

        if ($hasError) {
            $this->_initLayoutMessages('adminhtml/session');
            $response['error']   = 1;
            $response['message'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        } else {
            $response['error']   = 0;
            $response['url']     = $this->getUrl('*/monitoring');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    // execute callback on widget
    public function execCallbackAction() {
        $response = "ERR";
        if ($widget = $this->_getWidgetFromRequest()) {
            if ($cbMethod = $this->getRequest()->getParam('cb')) {
                if (method_exists($widget, $cbMethod)) {
                    try {
                        $response = $widget->$cbMethod();
                        // make true/false responses look a bit nicer
                        if ($response === true) {
                            $response = $cbMethod . ' was successful!';
                        } else if ($response === false) {
                            $response = $cbMethod . ' caused an error. :(';
                        }
                    } catch (Exception $e) {
                        $response = $e->getMessage();
                        Mage::logException($e);
                    }
                }
            }
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Returns widget instance if widgetId or widgetImpl is found in current request params.
     *
     * @param string $className
     *
     * @return Hackathon_MageMonitoring_Model_Widget|false
     */
    private function _getWidgetFromRequest($className=null, $widgetDbId=null) {
        if ($id = $this->getRequest()->getParam('widgetId')) {
            $tabId = null;
            if ($this->getRequest()->getParam('tabId')) {
                $tabId = $this->getRequest()->getParam('tabId');
            }
            if ($className !== null || (is_numeric($id) && $this->getRequest()->getParam('widgetImpl'))) {
                if ($className === null) {
                    $className = $this->getRequest()->getParam('widgetImpl');
                }
                if ($widgetDbId === null) {
                    $w = new $className();
                    $widgetDbId = $w->getConfigId();
                }
                $id = array($widgetDbId => $className);
                $widget = Mage::helper('magemonitoring')->getActiveWidgets($id, $tabId, false);
            } else {
                $widget = Mage::helper('magemonitoring')->getConfiguredWidgets($tabId, $id, false);
                $widget = reset($widget);
            }
            $widget = reset($widget);
            if ($widget instanceof Hackathon_MageMonitoring_Model_Widget) {
                return $widget;
            }
        }
        return false;
    }

    /**
     *  Saves tab/widget display structure from ext.tree data array.
     *
     * @param array $data
     */
    private function _saveTabConfig($data)
    {
        $tabData = $data['tabs'];
        foreach ($tabData as $t) {
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$t[0].'/visible', 1);
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$t[0].'/title', $t[1]);
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$t[0].'/label', $t[1]);
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$t[0].'/display_prio', $t[2]*10 );
        }

        $tabData = $data['removedTabs'];
        foreach ($tabData as $t) {
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$t.'/visible', 0);
        }

        $widgetData = $data['widgets'];
        foreach ($widgetData as $w) {
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$w[1].'/widgets/'.$w[0].'/impl', $w[3]);
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$w[1].'/widgets/'.$w[0].'/display_prio', $w[2]*10);
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$w[1].'/widgets/'.$w[0].'/visible', 1);
        }

        $widgetData = $data['removedWidgets'];
        foreach ($widgetData as $w) {
            Mage::getConfig()->saveConfig('magemonitoring/tabs/'.$w[0].'/widgets/'.$w[1].'/visible', 0);
        }

        Mage::getConfig()->reinit();
    }

}
