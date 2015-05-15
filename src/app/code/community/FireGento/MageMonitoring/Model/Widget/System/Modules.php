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
 * class FireGento_MageMonitoring_Model_Widget_System_Modules
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_System_Modules
    extends FireGento_MageMonitoring_Model_Widget_AbstractGeneric
    implements FireGento_MageMonitoring_Model_Widget
{
    const CONFIG_CODE_POOL = 'code_pool';
    // set/override defaults
    protected $_defWidgetTitle = 'Magento Modules';
    protected $_defCodePool = 'all';
    protected $_defDisplayPrio = 20;

    /**
     * Returns version
     *
     * @see FireGento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Initializes configuration
     *
     * @see FireGento_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for code pool filter
        $this->addConfig(self::CONFIG_CODE_POOL,
                'Filter by code pool:',
                $this->_defCodePool,
                'widget',
                'text',
                true,
                'all | community | core | local');

        return $this->_config;
    }

    /**
     * Fetches and returns output
     *
     * @see FireGento_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $poolFilter = $this->getConfig(self::CONFIG_CODE_POOL);
        $modules = (array)Mage::getConfig()->getModuleConfig();
        $notEmpty = false;
        foreach ($modules as $key => $module) {
            if ($poolFilter === 'all' || $module->codePool == $poolFilter) {
                $notEmpty = true;
                $block->addRow($module->is('active') ? 'success' : 'error', $key, $module->version);
            }
        }
        if (!$notEmpty) {
            $block->addRow('info', Mage::helper('magemonitoring')->__('No modules found.'));
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
