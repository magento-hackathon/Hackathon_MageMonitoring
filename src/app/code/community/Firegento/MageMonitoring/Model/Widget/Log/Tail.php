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
 * class Firegento_MageMonitoring_Model_Widget_Log_Tail
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_Log_Tail
    extends Firegento_MageMonitoring_Model_Widget_Log_Abstract
    implements Firegento_MageMonitoring_Model_Widget, Firegento_MageMonitoring_Model_WatchDog
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
     * @see Firegento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     *
     * @see Firegento_MageMonitoring_Model_Widget::initConfig()
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
     * @see Firegento_MageMonitoring_Model_Widget::getOutput()
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
     * @see Firegento_MageMonitoring_Model_WatchDog::watch()
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

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
