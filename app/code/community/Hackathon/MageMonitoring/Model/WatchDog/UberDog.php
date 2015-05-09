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
 * Class Hackathon_MageMonitoring_Model_WatchDog_UberDog
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
