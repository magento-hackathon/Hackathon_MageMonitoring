<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageMonitoring
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * Monitoring Controller
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Adminhtml_MageMonitoring_MonitoringController     extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/monitoring');
        $this->_addBreadcrumb(
            Mage::helper('magemonitoring')->__('Monitoring'),
            Mage::helper('magemonitoring')->__('Monitoring')
        );

        $this->_title('Mage Monitoring');

        $this->_addContent(
            $this->getLayout()->createBlock('magemonitoring/tab', 'magemonitoring_main')
        );

        $this->renderLayout();
    }

    /**
     * Config tabs action
     */
    public function config_tabsAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/monitoring');
        $this->_addBreadcrumb(
                Mage::helper('magemonitoring')->__('Monitoring'),
                Mage::helper('magemonitoring')->__('Monitoring')
        );

        $this->_title('Mage Monitoring - Tab Config');

        $this->_addContent(
                $this->getLayout()->createBlock('magemonitoring/tab_config', 'magemonitoring_tab_config')
        );

        $this->renderLayout();
    }

    /**
     * Reset config action
     *
     * @return mixed
     */
    public function resetConfigAction()
    {
        $transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $config = Mage::getStoreConfig('magemonitoring');
            $transaction->beginTransaction();
            $this->deleteConfigData($config);
            $transaction->commit();
            Mage::getConfig()->reinit();
            $this->_getSession()->addSuccess($this->__('Wiped all module configuration from database.'));
        } catch (Exception $e) {
            $transaction->rollback();
            $this->_getSession()->addError($e->__toString());
        }

        return $this->_redirect('*/*/index');
    }

    /**
     * Deletes entries in $config from core_config_data. Recursive. Locked to entries below path 'magemonitoring/'
     *
     * @param array  $config Configuration array
     * @param string $prefix Prefix
     */
    protected function deleteConfigData($config, $prefix='')
    {
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

    /**
     * Flush Cache action
     *
     * @return mixed
     */
    public function flushAllCacheAction()
    {
        try {
            $caches = Mage::helper('magemonitoring')->getActiveWidgets(
                '*',
                null,
                false,
                'Hackathon_MageMonitoring_Model_Widget_CacheStat'
            );

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

    /**
     * Permission check
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/magemonitoring');
    }
}
