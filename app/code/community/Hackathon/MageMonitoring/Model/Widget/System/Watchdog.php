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

/**
 * Class Hackathon_MageMonitoring_Model_Widget_System_Watchdog
 */
class Hackathon_MageMonitoring_Model_Widget_System_Watchdog
    extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    // override defaults
    protected $_defDisplayPrio = 100;
    protected $_defStartCollapsed = 1;
    protected $_defaultConfig = false;

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Watch Dog Control';
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * @return array
     */
    public function initConfig()
    {
        parent::initConfig();

        $this->addConfigHeader($this->getHelper()->__('Global Watch Dog Configuration'));
        $this->addConfig(
            Hackathon_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED,
            $this->getHelper()->__('Disable all dogs?'),
            Hackathon_MageMonitoring_Model_Widget_Watchdog::DEFAULT_DISABLED,
            'global',
            'checkbox'
        );

        $this->addConfig(Hackathon_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_MAILTO,
            $this->getHelper()->__('Default report destination (global|support|sales|custom1|custom2):'),
            Hackathon_MageMonitoring_Model_Widget_Watchdog::DEFAULT_MAILTO,
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
        if ($this->getConfig(Hackathon_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED)) {
            return $this->getHelper()
                ->__('Error: Watch dogs are globally disabled. Click the gear icon of the watch dog widget to edit.');
        }

        if (Mage::getModel('magemonitoring/watchDog_uberDog')->triggerActiveDogs(false)) {
            $email = $this->getConfig(Hackathon_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_MAILTO);
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
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $disabled = $this->getConfig(Hackathon_MageMonitoring_Model_Widget_Watchdog::CONFIG_DOGS_DISABLED);

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
