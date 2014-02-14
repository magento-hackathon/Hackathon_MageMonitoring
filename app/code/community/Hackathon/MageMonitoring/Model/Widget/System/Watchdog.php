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

class Hackathon_MageMonitoring_Model_Widget_System_Watchdog extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
                                                            implements Hackathon_MageMonitoring_Model_Widget_System
{
    // override defaults
    protected $_DEF_DISPLAY_PRIO = 100;
    protected $_DEF_START_COLLAPSED = 1;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Watch Dogs';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    public function initConfig() {
        parent::initConfig();
        $this->addConfigHeader('Global Watch Dog Configuration');
        $this->addConfig(self::CONFIG_DOGS_DISABLED, 'Disable all dogs?', $this->_DEF_DOGS_DISABLED, 'checkbox');
        $this->addConfig(self::CONFIG_DOGS_MAILTO, 'Bark reports at: (sales|etc or valid email)', $this->_DEF_DOGS_MAILTO);
        return $this->_config;
    }

    /**
     * Simulates cron run with 2 added test dogs.
     *
     * @return string
     */
    public function testCallback()
    {
        if ($this->getConfig(self::CONFIG_DOGS_DISABLED)) {
            return 'Error: Watch dogs are globally disabled. Click the gear icon of the watch dog widget to edit.';
        }
        if (Mage::getModel('magemonitoring/watchDog_uberDog')->triggerActiveDogs(false)) {
            $email = $this->getConfig(self::CONFIG_DOGS_MAILTO, true);
            if ($mailTo = Mage::helper('magemonitoring')->validateEmail($email)) {
                return 'Dogs barked, report mail has been sent to: '.$mailTo['email'];
            }
            return 'Error, i have no valid email address to send to: '.$email;
        }
        return 'Error, looks like the test watch dog did not bark.';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $disabled = $this->getConfig(self::CONFIG_DOGS_DISABLED);
        if (!$disabled) {
            $this->addRow('success', 'Watch Dogs are enabled');
        } else {
            $this->addRow('error', 'All Watch Dogs are disabled', 'Click on the gear icon of this widget to edit.' );
        }

        $this->addRow('info', 'Installed Watch Dogs:', 'Schedule:');

        $dogs = Mage::helper('magemonitoring')->getActiveWidgets('*', null, 'Hackathon_MageMonitoring_Model_WatchDog');
        foreach ($dogs as $d) {
            $this->addRow( (!$disabled && $d->onDuty()) ? 'success':'error', $d->getName(), $d->getSchedule());
        }

        // add callback button to launch self test
        $this->addButton($this->getId().'_test', 'Test Report Mail' , self::CALLBACK.'testCallback', array('refreshAfter' => true));

        return $this->_output;
    }

}
