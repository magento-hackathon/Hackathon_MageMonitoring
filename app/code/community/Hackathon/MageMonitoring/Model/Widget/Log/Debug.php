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

class Hackathon_MageMonitoring_Model_Widget_Log_Debug extends Hackathon_MageMonitoring_Model_Widget_Log_Abstract
                                                      implements Hackathon_MageMonitoring_Model_Widget_Log,
                                                                 Hackathon_MageMonitoring_Model_WatchDog
{
    protected $_DEF_START_COLLAPSED = 1;
    // watch dog defaults
    protected $_DEF_WATCHDOG_ACTIVE = false;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Magento Debug Log';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $this->_output[] = $this->newLogBlock('warning', Mage::getStoreConfig('dev/log/file'));
        return $this->_output;
    }

    /**
     * Reports on new log entries.
     *
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_WatchDog::watch()
     */
    public function watch()
    {
        $attachmentName = Mage::getStoreConfig('dev/log/file');
        $widgetOut = $this->getOutput();

        return $this->watchLog($widgetOut[0]['value'], $attachmentName);
    }
}
