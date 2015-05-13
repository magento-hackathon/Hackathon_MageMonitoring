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
 * interface Firegento_MageMonitoring_Model_WatchDog
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
interface Firegento_MageMonitoring_Model_WatchDog
{
    /**
     * Returns id string, use classname to avoid possible conflicts.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return string
     */
    public function getDogId();

    /**
     * Returns watch dog name.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return string
     */
    public function getDogName();

    /**
     * Returns true if this watch dog is active.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return bool
     */
    public function onDuty();

    /**
     * Returns string in standard cron format or false.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract provides default impl.
     *
     * @return false|string
     */
    public function getSchedule();

    /**
     * Method that executes if getSchedule() says it's time.
     * Returns false if there is nothing to report or array with results.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract provides addReportRow() for convenience.
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
