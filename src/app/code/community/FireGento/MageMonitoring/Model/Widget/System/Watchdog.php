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
 * class FireGento_MageMonitoring_Model_Widget_System_Watchdog
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_System_Watchdog
    extends FireGento_MageMonitoring_Model_Widget_System_Abstract
    implements FireGento_MageMonitoring_Model_Widget
{
    // override defaults
    protected $_defDisplayPrio = 100;
    protected $_defStartCollapsed = 1;
    protected $_defaultConfig = false;

    /**
     * (non-PHPdoc)
     *
     * @see FireGento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Watch Dog Control';
    }

    /**
     * (non-PHPdoc)
     *
     * @see FireGento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * InitConfig
     *
     * @return array Config
     */
    public function initConfig()
    {
        parent::initConfig();

        $this->addConfigHeader($this->getHelper()->__('Global Watch Dog Configuration'));
        $this->addConfig(
            FireGento_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED,
            $this->getHelper()->__('Disable all dogs?'),
            FireGento_MageMonitoring_Model_Widget_Watchdog::DEFAULT_DISABLED,
            'global',
            'checkbox'
        );

        $this->addConfig(FireGento_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_MAILTO,
            $this->getHelper()->__('Default report destination (global|support|sales|custom1|custom2):'),
            FireGento_MageMonitoring_Model_Widget_Watchdog::DEFAULT_MAILTO,
            'global',
            'text',
            false,
            $this->getHelper()->__('Magento mail id (general, sales, etc) or valid email address.'));

        return $this->_config;
    }

    /**
     * Simulates cron run with 2 added test dogs.
     *
     * @return string
     */
    public function testCallback()
    {
        if ($this->getConfig(FireGento_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED)) {
            return $this->getHelper()
                ->__('Error: Watch dogs are globally disabled. Click the gear icon of the watch dog widget to edit.');
        }

        if (Mage::getModel('magemonitoring/watchDog_uberDog')->triggerActiveDogs(false)) {
            $email = $this->getConfig(FireGento_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_MAILTO);
            if ($mailTo = Mage::helper('magemonitoring')->validateEmail($email)) {
                return $this->getHelper()->__('Dogs barked, report mail has been sent to: %s', $mailTo['email']);
            }

            return $this->getHelper()->__('Error, i have no valid email address to send to: %s', $email);
        }

        return $this->getHelper()->__('Error, looks like the test watch dogs did not bark.');
    }

    /**
     * (non-PHPdoc)
     *
     * @see FireGento_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $disabled = $this->getConfig(FireGento_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED);

        if (!$disabled) {
            $block->addRow('success', $this->getHelper()->__('Watch Dogs are enabled'));
        } else {
            $block->addRow('error',
                $this->getHelper()->__('All Watch Dogs are disabled'),
                $this->getHelper()->__('Click on the gear icon of this widget to edit.'));
        }

        $block->addRow('info', $this->getHelper()->__('Installed Watch Dogs:'), $this->getHelper()->__('Schedule:'));

        $dogs = $this->getHelper()->getConfiguredWatchDogs();

        foreach ($dogs as $d) {
            $block->addRow((!$disabled && $d->onDuty()) ? 'success' : 'error', $d->getDogName(), $d->getSchedule());
        }

        // add callback button to launch self test
        $block->addButton(
            $this,
            'test',
            $this->getHelper()->__('Test Report Mail'),
            self::CALLBACK . 'testCallback',
            array('refreshAfter' => true)
        );

        $this->_output[] = $block;

        return $this->_output;
    }
}
