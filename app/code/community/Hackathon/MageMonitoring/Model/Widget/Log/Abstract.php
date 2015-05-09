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
 * class Hackathon_MageMonitoring_Model_Widget_Log_Abstract
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_Log_Abstract
    extends Hackathon_MageMonitoring_Model_Widget_Watchdog
{
    // define config keys
    const CONFIG_LAST_LOG_ENTRY = 'last_log_entry';
    const CONFIG_LOG_LINES = 'linecount';
    
    // define global defaults
    protected $_defLogLines = 30;

    protected $_regexLogstamp = "\d{4}(-\d{2}){2}T(\d{2}:){2}\d{2}(\+|-)\d{2}:\d{2}";

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        $this->addConfigHeader('Log Settings');
        // add config for tail -n param
        $this->addConfig(self::CONFIG_LOG_LINES, 'Max. number of lines to tail:', $this->_defLogLines, 'widget');
        return $this->_config;
    }

    /**
     * Adds a tail -n row to widget output.
     *
     * @param  string $errorLevel Error Level
     * @param  string $fileName   Filename
     * @return Hackathon_MageMonitoring_Block_Widget_Monitoring Block
     */
    protected function newLogBlock($errorLevel, $fileName)
    {
        $block = $this->newMonitoringBlock();
        $log = $this->getLogTail($fileName, $this->getConfig(self::CONFIG_LOG_LINES));
        if (empty($log)) {
            $errorLevel = 'success';
        }
        return $block->addRow($errorLevel, null, nl2br(htmlspecialchars($log)));
    }

    /**
     * Compares last entry in $logIn with last saved timestamp and adds all log entries between
     * the last logged or whole tail if the last saved timestamp is not found.
     * Returns false if $logIn is empty or array with added report data.
     *
     * @param  string $logIn          Log
     * @param  string $attachmentName Attachment
     * @return array|false Report
     */
    protected function watchLog($logIn, $attachmentName)
    {
        if (!$logIn) {
            return false;
        }

        $this->loadConfig(self::CONFIG_LAST_LOG_ENTRY);

        $log = $this->extractNewLogEntries($logIn, $this->getConfig(self::CONFIG_LAST_LOG_ENTRY));
        if (!$log) {
            $log = $logIn;
        }

        $lastLog = new DateTime($this->getConfig(self::CONFIG_LAST_LOG_ENTRY));
        $fileName = Mage::helper('magemonitoring')->stampFileName($attachmentName, $lastLog->format('Y-m-d_H-i'));
        $value = 'New entries in ' . $attachmentName . '. See attachment: ' . $fileName . ' for details.';
        $this->addReportRow('error',
                $this->getConfig(self::CONFIG_LAST_LOG_ENTRY),
                $value,
                array(array('filename' => $fileName, 'content' => $log)));

        return $this->_report;
    }

    /**
     * Returns log entries with timestamp after $from or false if $from is not found.
     *
     * @param  string $log  Log
     * @param  string $from From
     * @return string|false
     */
    protected function extractNewLogEntries($log, $from)
    {
        $regOut = array();
        // find last time stamp
        $pattern = '/('.$this->_regexLogstamp.')(?!.*'.$this->_regexLogstamp.')(.+)\z/ms';
        if (preg_match_all($pattern, $log, $regOut)) {
            // last time stamp in log
            $curExcept = $regOut[1][0];
            $curExceptDate = new DateTime($curExcept);
            $lastSavedDate = null;
            if ($from) {
                $lastSavedDate = new DateTime($from);
            }

            if ($from == null || $lastSavedDate != $curExceptDate) {
                $this->saveConfig(array(self::CONFIG_LAST_LOG_ENTRY => array('value' => $curExcept)), true);

                // try to match everything after last logged exception
                $r = array();
                $p = '/(!?' . str_replace('+', '\+', $from) . ').*((' . $this->_regexLogstamp . ')(.*?))\z/ms';
                $logOutput = '';
                if ($from && preg_match_all($p, $log, $r)) {
                    return $r[7][0];
                }
            }
        }
        return false;
    }

    /**
     * Returns last lines of given $filePath.
     *
     * @param  string $filePath Filepath
     * @param  int    $lines    Lines
     * @return string
     */
    protected function getLogTail($filePath, $lines)
    {
        if (file_exists($filePath)) {
            $logFile = $filePath;
        } elseif (file_exists(Mage::getBaseDir('log').DS.$filePath)) {
            $logFile = Mage::getBaseDir('log').DS.$filePath;
        } else {
            return false;
        }
        return Mage::helper('magemonitoring')->tailFile($logFile, $lines);
    }
}
