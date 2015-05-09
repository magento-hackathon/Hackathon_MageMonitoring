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
 * Interface Hackathon_MageMonitoring_Model_WatchDog
 */
interface Hackathon_MageMonitoring_Model_WatchDog
{
    /**
     * Returns id string, use classname to avoid possible conflicts.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return string
     */
    public function getDogId();

    /**
     * Returns watch dog name.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return string
     */
    public function getDogName();

    /**
     * Returns true if this watch dog is active.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return bool
     */
    public function onDuty();

    /**
     * Returns string in standard cron format or false.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return false|string
     */
    public function getSchedule();

    /**
     * Method that executes if getSchedule() says it's time.
     * Returns false if there is nothing to report or array with results.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract provides addReportRow() for convenience.
     *
     * Return format of array:
     * array(array('css_id' => 'info|success|warning|error',
     *             'label' => 'My Check',
     *             'value' => 'my report msg', // any html
     *             'attachments => array(array('filename' => $name, 'content' => $content), ...),
     *             ... ))
     *
     * @return false|array
     */
    public function watch();
}
