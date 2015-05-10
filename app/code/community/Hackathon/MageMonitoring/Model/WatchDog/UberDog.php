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
 * Class Hackathon_MageMonitoring_Model_WatchDog_UberDog
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_WatchDog_UberDog
{
    private $_exceptionList = array();
    private $_watchDogResults = array();

    /**
     * Collects all registered watch dogs, handles their schedule and fires them if it's time.
     * Sends aggregated reports via email.
     *
     * @param  boolean $skipTestDog Do not trigger watchdogs
     * @throws Exception
     * @return void|boolean
     */
    public function triggerActiveDogs($skipTestDog = true)
    {
        $helper = Mage::helper('magemonitoring');
        $id = 'Hackathon_MageMonitoring_Model_Widget_System_Watchdog';
        // exit if globally disabled
        if (Mage::getStoreConfigFlag($helper->getConfigKeyById('dogs/disabled', $id))) {
            return false;
        }

        $watchDogs = $helper->getConfiguredWatchDogs();

        // add test watch dogs that always fire a report and a runtime error?
        if (!$skipTestDog) {
            foreach (array('test', 'error') as $m) {
                $t = Mage::getModel('magemonitoring/watchDog_' . $m);
                $t->loadConfig();
                $watchDogs[] = $t;
            }
        }

        foreach ($watchDogs as $d) {
            if (!$d->onDuty()) { // skip inactive dogs
                continue;
            }

            $mailTo = $d->getConfig('cron/mail_to');
            try {
                // check watch dog schedules and run watch() if it's time
                $schedule = Mage::getModel('cron/schedule')->setCronExpr($d->getSchedule());
                if ($schedule->trySchedule(time()) && $results = $d->watch()) {
                    $this->_watchDogResults[$mailTo][] = array('watchdog' => $d, 'output' => $results);
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_exceptionList[$mailTo][] = array('exception' => $e, 'watchdog' => $d);
            }
        }

        if (empty($this->_watchDogResults) && empty($this->_exceptionList)) {
            return false;
        }

        // send collected reports to each mail
        foreach ($this->_watchDogResults as $email => $results) {
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('magemonitoring_watchdog_report');
            // add all attachments
            foreach ($results as $report) {
                if (array_key_exists('output', $report) && is_array($report['output'])) {
                    foreach ($report['output'] as $row) {
                        if (array_key_exists('attachments', $row) && is_array($row['attachments'])) {
                            foreach ($row['attachments'] as $attachment) {
                                $a = $emailTemplate->getMail()->createAttachment($attachment['content']);
                                $a->filename = $attachment['filename'];
                            }
                        }
                    }
                }
            }

            $mailFrom = $helper->validateEmail('general');
            $mailTo = $helper->validateEmail($email);

            if (!$mailFrom || !$mailTo) {
                throw new Exception (
                    $helper->__('Error sending watch dog report. Could not find valid sender or recipient address.')
                );
            }

            $emailTemplate->setSenderName($mailFrom['name']);
            $emailTemplate->setSenderEmail($mailFrom['email']);

            $vars = array('reports' => $results, 'errors' => null);
            if (array_key_exists($email, $this->_exceptionList)) {
                $vars['errors'] = $this->_exceptionList[$email];
            }

            $emailTemplate->send($mailTo['email'], $mailTo['name'], $vars);

            return true;
        }
    }
}
