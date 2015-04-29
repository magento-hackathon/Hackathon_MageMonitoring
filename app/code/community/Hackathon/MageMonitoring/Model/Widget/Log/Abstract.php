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
class Hackathon_MageMonitoring_Model_Widget_Log_Abstract
    extends Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
{
    // define config keys
    const CONFIG_LAST_LOG_ENTRY = 'last_log_entry';
    const CONFIG_LOG_LINES = 'linecount';
    // define global defaults
    protected $_DEF_LOG_LINES = 30;

    protected $_REGEX_LOGSTAMP = "\d{4}(-\d{2}){2}T(\d{2}:){2}\d{2}(\+|-)\d{2}:\d{2}";

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        $this->addConfigHeader('Log Settings');
        // add config for tail -n param
        $this->addConfig(self::CONFIG_LOG_LINES, 'Max. number of lines to tail:', $this->_DEF_LOG_LINES);
        return $this->_config;
    }

    /**
     * Adds a tail -n row to widget output.
     *
     * @param string $errorLevel
     * @param string $fileName
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
     * @param string $log
     * @return array|false
     */
    protected function watchLog($logIn, $attachmentName)
    {
        if (!$logIn) return false;

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
     * @param string $log
     * @param string $from
     * @return string|false
     */
    protected function extractNewLogEntries($log, $from)
    {
        $regOut = array();
        // find last time stamp
        $pattern = '/('.$this->_REGEX_LOGSTAMP.')(?!.*'.$this->_REGEX_LOGSTAMP.')(.+)\z/ms';
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
                $p = '/(!?'.str_replace('+', '\+',$from).').*(('.$this->_REGEX_LOGSTAMP.')(.*?))\z/ms';
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
     * @param string $filePath
     * @param int $lines
     * @return string
     */
    protected function getLogTail($filePath, $lines)
    {
        $logFile = '';
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
