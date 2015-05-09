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
 * Class Hackathon_MageMonitoring_Model_Widget_Watchdog
 */
class Hackathon_MageMonitoring_Model_Widget_Watchdog extends Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
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
            $id = 'Hackathon_MageMonitoring_Model_Widget_System_Watchdog';
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
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_WatchDog::getDogId()
     */
    public function getDogId()
    {
        return $this->getId();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_WatchDog::getDogName()
     */
    public function getDogName()
    {
        return $this->getName();
    }

    /**
     * Get Schedule
     *
     * @see Hackathon_MageMonitoring_Model_WatchDog::getSchedule()
     * @return string
     */
    public function getSchedule()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_CRON, true);
    }

    /**
     * Is On Duty
     *
     * @see Hackathon_MageMonitoring_Model_WatchDog::onDuty()
     * @return string
     */
    public function onDuty()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_ACTIVE, true);
    }
}