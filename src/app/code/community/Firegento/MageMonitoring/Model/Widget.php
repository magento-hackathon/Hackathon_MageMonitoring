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
 * interface Firegento_MageMonitoring_Model_Widget
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
interface Firegento_MageMonitoring_Model_Widget
{
    /**
     * Returns class name that must be compatible with magento autoloader.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns widget config key in database.
     *
     * @return string
     */
    public function getConfigId();

    /**
     * Returns true if this widget is active.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName();


    /**
     * Returns version string.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Returns magento versions supported by this widget.
     *
     * @return string
     */
    public function getSupportedMagentoVersions();

    /**
     * Returns true if widget should start collapsed, speeds up loading times as
     * the widget won't render it's content on page load.
     *
     * @return bool
     */
    public function displayCollapsed();

    /**
     * Returns display prio of this widget.
     *
     * @return int
     */
    public function getDisplayPrio();

    /**
     * Used to render the widget, returns array of classes that have a ->toHtml() method.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you .
     *
     * @return array
     */
    public function getOutput();

    /**
     * Returns array with default config data for this widget or false if not implemented.
     *
     * Implementing this method enables you to add custom entries for user configuration.
     *
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     * Data is saved to core_config_data with path = 'widgets/' + $widgetClassName '/' + $config_key
     *
     * Format of return array:
     * array ('config_key' => array('type' => $inputType, // 'text' or 'checkbox' for now
     *                              'scope' => 'global|widget',
     *                              'required' => true|false,
     *                              'label' => $label,
     *                              'value' => $value,
     *                              'tooltip' => $tooltipMsg),
     *        ...)
     *
     * @return array|false
     */
    public function initConfig();

    /**
     * Returns current config data of this widget.
     *
     * Returned array has same structure as initConfig()
     *
     * @param  string $key       Key
     * @param  bool   $valueOnly Value Only
     * @return array|false
     */
    public function getConfig($key=null, $valueOnly=null);

    /**
     * Loads and returns the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * Returned array has same structure as initConfig()
     *
     * @return array|false
     */
    public function loadConfig();

    /**
     * Saves the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * Format of input array:
     * array('config_key' => $newValue, ...)
     *
     * @param  array $array Array
     * @return bool
     */
    public function saveConfig($array);

    /**
     * Deletes the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Firegento_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * @return bool
     */
    public function deleteConfig();
}
