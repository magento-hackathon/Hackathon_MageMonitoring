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

class Hackathon_MageMonitoring_Adminhtml_MonitoringController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/monitoring');
        $this->_addBreadcrumb(
            Mage::helper('magemonitoring')->__('Monitoring'),
            Mage::helper('magemonitoring')->__('Monitoring')
        );

        $this->_title('Overview');

        $this->_addContent(
            $this->getLayout()->createBlock('magemonitoring/tab', 'magemonitoring_main')
        );

        $this->renderLayout();
    }

    public function config_tabsAction() {
        $this->loadLayout();
        $this->_setActiveMenu('system/monitoring');
        $this->_addBreadcrumb(
                Mage::helper('magemonitoring')->__('Monitoring'),
                Mage::helper('magemonitoring')->__('Monitoring')
        );

        $this->_title('Tab Config');

        $this->_addContent(
                $this->getLayout()->createBlock('magemonitoring/tab_config', 'magemonitoring_tab_config')
        );
        $this->renderLayout();
    }

    public function resetConfigAction() {
        $transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $config = Mage::getStoreConfig('magemonitoring');
            $transaction->beginTransaction();
            $this->deleteConfigData($config);
            $transaction->commit();
            $this->_getSession()->addSuccess($this->__('Wiped all module configuration from database.'));
        } catch (Exception $e) {
            $transaction->rollback();
            $this->_getSession()->addError($e->__toString());
        }

        return $this->_redirect('*/*/index');
    }

    /**
     * Deletes entries in $config from config_data. Recursive. Locked to entries below path 'magemonitoring/'
     *
     * @param array $config
     * @param string $prefix
     */
    protected function deleteConfigData($config, $prefix='') {
        foreach ($config as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $this->deleteConfigData($value, $prefix.$key.'/');
            }
            $c = Mage::getModel('core/config');
            $c->deleteConfig(
                    'magemonitoring/'.$prefix.$key,
                    'default',
                    0
            );
        }
    }

    public function flushAllCacheAction()
    {
        try {

            $caches = Mage::helper('magemonitoring')->getActiveWidgets('*', null, false, 'Hackathon_MageMonitoring_Model_Widget_CacheStat');

            foreach ($caches as $cache) {
                if ($cache instanceof Hackathon_MageMonitoring_Model_Widget_CacheStat) {
                    $cache->flushCache();
                }
            }

            $this->_getSession()->addSuccess($this->__('All caches flushed with success'));

        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->__toString());
        }

        return $this->_redirect('*/*/index');
    }

}
