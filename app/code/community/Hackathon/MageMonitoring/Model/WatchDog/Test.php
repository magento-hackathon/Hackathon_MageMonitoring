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
 * Class Hackathon_MageMonitoring_Model_WatchDog_Test
 */
class Hackathon_MageMonitoring_Model_WatchDog_Test
    extends Hackathon_MageMonitoring_Model_WatchDog_Abstract
{
    protected $_defWatchdogCron = '* * * * *';

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getDogName()
    {
        return 'Watch Dog Test';
    }

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
     * @see Hackathon_MageMonitoring_Model_WatchDog::watch()
     */
    public function watch()
    {
        $value = $this->getHelper()->__('Something terrible happened. See attachment test.log for details.');

        $this->addReportRow('error', 'test label', $value,
            array(array('filename' => 'test.log', 'content' => 'test test')));

        $this->addReportRow(
            'warning',
            $this->getHelper()->__('Another test label'),
            $this->getHelper()->__('Just a warning')
        );
        return $this->_report;
    }

}
