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
 * Class Hackathon_MageMonitoring_Model_Widget_Log
 * renders log statistics.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_Log
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'log';

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Log & Reports');
    }

    /**
     * Returns the name of the widgets xml node
     *
     * @return string
     */
    protected function _getNodeName()
    {
        return self::NODE_NAME;
    }

    /**
     * Render log file statistics.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        parent::_renderMoreChecks();

        $firstDayOfThisMonth = strtotime(date('Y-m-01'));
        $firstDayOfLastMonth = strtotime(date('Y-m-01', $firstDayOfThisMonth - 1));
        $firstDayOfMonthTwoBefore = strtotime(date('Y-m-01', $firstDayOfLastMonth - 1));
        $firstDayOfMonthThreeBefore = strtotime(date('Y-m-01', $firstDayOfMonthTwoBefore - 1));

        // all error messages
        $this->_countLoggedErrors('Logged errors%s', 'The number of all logged errors (ERR, CRIT, ALERT, EMERG)');

        // error messages in log files last months
        $this->_countLoggedErrors('Logged errors%s', '', $firstDayOfMonthThreeBefore, $firstDayOfMonthTwoBefore);
        $this->_countLoggedErrors('Logged errors%s', '', $firstDayOfMonthTwoBefore, $firstDayOfLastMonth);
        $this->_countLoggedErrors('Logged errors%s', '', $firstDayOfLastMonth, $firstDayOfThisMonth);
        $this->_countLoggedErrors('Logged errors%s', '', $firstDayOfThisMonth, time());

        // all reports
        $this->_countReports('Reports%s', 'The number of reports currently saved in /var/reports');

        // reports last month
        $this->_countReports('Reports%s', '', $firstDayOfMonthThreeBefore, $firstDayOfMonthTwoBefore);
        $this->_countReports('Reports%s', '', $firstDayOfMonthTwoBefore, $firstDayOfLastMonth);
        $this->_countReports('Reports%s', '', $firstDayOfLastMonth, $firstDayOfThisMonth);
        $this->_countReports('Reports%s', '', $firstDayOfThisMonth, time());

        return $this;
    }

    /**
     * Render the log-statistics for the given report text.
     *
     * @param  string $textShort Short text
     * @param  string $textLong  Long text
     * @param  int    $dateFrom  Date from
     * @param  int    $dateTo    Date to
     *
     * @return $this
     */
    protected function _countLoggedErrors($textShort, $textLong, $dateFrom = null, $dateTo = null)
    {
        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        $fi = new FilesystemIterator(Mage::getBaseDir('log'), FilesystemIterator::SKIP_DOTS);
        $errorsCount = 0;
        foreach ($fi as $file) {
            $content = file_get_contents($file);
            preg_match_all(
                '#(\d{4}-\d{2}-\d{2})T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2} (ERR|CRIT|ALERT|EMERG)#ims',
                $content,
                $matches
            );

            if (!is_null($dateFrom) && !is_null($dateTo)) {
                foreach ($matches[1] as $_match) {
                    $logDate = strtotime($_match);
                    if ($logDate >= $dateFrom && $logDate < $dateTo) {
                        $errorsCount++;
                    }
                }
            } else {
                $errorsCount += count($matches[1]);
            }
        }

        $this->getRenderer()->addRow(
            array(
                $helper->__($textShort, $this->_getMonth($dateFrom)),
                $helper->__($textLong),
                $errorsCount,
                $helper->__('solve problems')
            ),
            $this->_getRowConfig(0 === $errorsCount)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Render the report-statistics for the given report text.
     *
     * @param  string $textShort Text short
     * @param  string $textLong  Text long
     * @param  int    $dateFrom  Date from
     * @param  int    $dateTo    Date to
     *
     * @return $this
     */
    protected function _countReports($textShort, $textLong, $dateFrom = null, $dateTo = null)
    {
        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        $fi = new FilesystemIterator(Mage::getBaseDir('var') . DS . 'report' . DS, FilesystemIterator::SKIP_DOTS);
        $reportCount = 0;
        if (!is_null($dateFrom) && !is_null($dateTo)) {
            foreach ($fi as $file) {
                if ($dateFrom <= $file->getMTime() && $dateTo >= $file->getMTime()) {
                    $reportCount++;
                }
            }
        } else {
            $reportCount = iterator_count($fi);
        }

        $this->getRenderer()->addRow(
            array(
                $helper->__($textShort, $this->_getMonth($dateFrom)),
                $helper->__($textLong),
                $reportCount,
                $helper->__('solve problems')
            ),
            $this->_getRowConfig(0 === $reportCount)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Get the month for the given $date.
     *
     * @param  int $date A date
     *
     * @return string
     */
    protected function _getMonth($date = null)
    {
        if (!is_null($date)) {
            /** @var Hackathon_MageMonitoring_Helper_Data $helper */
            $helper = $this->_getHelper();

            return ' - ' . $helper->__(date('F', $date));
        }

        return '';
    }
}
