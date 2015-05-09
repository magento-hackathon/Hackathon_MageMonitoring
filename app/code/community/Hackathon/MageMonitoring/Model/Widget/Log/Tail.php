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
class Hackathon_MageMonitoring_Model_Widget_Log_Tail
    extends Hackathon_MageMonitoring_Model_Widget_Log_Abstract
    implements Hackathon_MageMonitoring_Model_Widget,
    Hackathon_MageMonitoring_Model_WatchDog
{
    // define config keys
    const CONFIG_LOG_FILE = 'file_path';
    const CONFIG_LOG_COLOR = 'color';

    // set/override defaults
    protected $_defWidgetTitle = 'Log Watcher';
    protected $_defLogFile = '';
    protected $_defLogColor = 'info';
    protected $_defLogLines = 60;
    protected $_defDisplayPrio = 20;

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
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for log file path
        $this->addConfig(self::CONFIG_LOG_FILE,
            'Log file path:',
            $this->_defLogFile,
            'widget',
            'text',
            true,
            'Complete system path or relative from magento root or var/log.');
        // add background color
        $this->addConfig(self::CONFIG_LOG_COLOR,
            'Background color:',
            $this->_defLogColor,
            'widget',
            'text',
            false,
            'success | info | warning | error');
        return $this->_config;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $logName = $this->getConfig(self::CONFIG_LOG_FILE);
        if ($logName) {
            $this->_output[] = $this->newLogBlock($this->getConfig(self::CONFIG_LOG_COLOR), $logName);
        } else {
            $this->dump(Mage::helper('magemonitoring')->__('Log file path is not configured!'));
        }
        return $this->_output;
    }

    /**
     * Reports on new log entries.
     *
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_WatchDog::watch()
     */
    public function watch()
    {
        $logName = $this->getConfig(self::CONFIG_LOG_FILE);
        if ($logName) {
            $log = $this->getLogTail($logName, $this->getConfig(self::CONFIG_LOG_LINES));
            return $this->watchLog($log, $logName);
        } else {
            $this->addReportRow('error', Mage::helper('magemonitoring')->__('Log file path is not configured!'), '');
            return $this->_report;
        }
    }

}
