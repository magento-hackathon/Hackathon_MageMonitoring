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
 * class Firegento_MageMonitoring_Model_Widget_Watchdog
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_Watchdog extends Firegento_MageMonitoring_Model_Widget_AbstractGeneric
{
    // watch dog config keys, only added if widget implements watchdog interface
    const CONFIG_WATCHDOG_ACTIVE = 'cron/enabled';
    const CONFIG_WATCHDOG_BARKON = 'cron/barkon';
    const CONFIG_WATCHDOG_CRON = 'cron/schedule';
    const CONFIG_WATCHDOG_MAILTO = 'cron/mail_to';

    // global watch dog config keys
    const CONFIG_DOGS_DISABLED = 'dogs/disabled';
    const CONFIG_DOGS_MAILTO = 'dogs/mail_to';

    // watch dog defaults
    protected $_defWatchdogActive = 1;
    protected $_defWatchdogBarkon = 'warning';
    protected $_defWatchdogCron = '*/5 * * * *';
    protected $_defWatchdogMailto = null;

    const DEFAULT_MAILTO = 'general';
    const DEFAULT_DISABLED = 1;

    protected $_defaultConfig = true;

    /**
     * Init Config
     *
     * @return array Configuration of the Widget
     */
    public function initConfig()
    {
        parent::initConfig();

        if ($this->_defaultConfig) {
            $helper = Mage::helper('magemonitoring');

            // override watch dog default mail_to if global config is found
            $id = 'Firegento_MageMonitoring_Model_Widget_System_Watchdog';
            $confKey = $helper->getConfigKeyById(self::CONFIG_DOGS_MAILTO, $id);

            $defMail = Mage::getStoreConfig($confKey);
            if (!$defMail) {
                $defMail = self::DEFAULT_MAILTO;
            }
            $this->_defWatchdogMailto = $defMail;

            $this->addConfigHeader('Watch Dog Settings');
            $this->addConfig(
                self::CONFIG_WATCHDOG_ACTIVE,
                'Dog is on duty:',
                $this->_defWatchdogActive,
                'global',
                'checkbox',
                false
            );
            $this->addConfig(
                self::CONFIG_WATCHDOG_CRON,
                'Schedule:',
                $this->_defWatchdogCron,
                'global',
                'text',
                false
            );
            $this->addConfig(
                self::CONFIG_WATCHDOG_BARKON,
                'Minimum bark level (warning|error):',
                $this->_defWatchdogBarkon,
                'global',
                'text',
                false
            );
            $this->addConfig(
                self::CONFIG_WATCHDOG_MAILTO,
                'Barks at:',
                $this->_defWatchdogMailto,
                'global',
                'text',
                false,
                $helper->__('Magento mail id (general, sales, etc) or valid email address.')
            );
        }

        return $this->_config;
    }

    /**
     * Get Dog Id
     *
     * @see Firegento_MageMonitoring_Model_WatchDog::getDogId()
     */
    public function getDogId()
    {
        return $this->getId();
    }

    /**
     * Get Dog Name
     *
     * @see Firegento_MageMonitoring_Model_WatchDog::getDogName()
     */
    public function getDogName()
    {
        return $this->getName();
    }

    /**
     * Get Schedule
     *
     * @see Firegento_MageMonitoring_Model_WatchDog::getSchedule()
     * @return string
     */
    public function getSchedule()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_CRON, true);
    }

    /**
     * Is On Duty
     *
     * @see Firegento_MageMonitoring_Model_WatchDog::onDuty()
     * @return string
     */
    public function onDuty()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_ACTIVE, true);
    }
}
