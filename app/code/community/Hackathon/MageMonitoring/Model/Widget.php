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

interface Hackathon_MageMonitoring_Model_Widget
{
    /**
     * Returns id string, use classname to avoid possible conflicts.
     *
     * @return string
     */
    public function getId();
    /**
     * Returns true if this widget is active.
     *
     * return bool
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
     * Returns init state for collapseable the plugin is displayed in.
     *
     * return bool
     */
    public function displayCollapsed();
    /**
     * Returns display prio of this widget.
     *
     * return int
     */
    public function getDisplayPrio();
    /**
     * Used to render the widget, returns array of classes that have a ->toHtml() method.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you .
     *
     * @return array
     */
    public function getOutput();
    /**
     * Returns array with default config data for this widget or false if not implemented.
     *
     * Implementing this method enables you to add custom entries for user configuration.
     *
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
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
     * @param string $key
     * @param bool $valueOnly
     * @return array|false
     */
    public function getConfig($key=null, $valueOnly=null);
    /**
     * Loads and returns the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * Returned array has same structure as initConfig()
     *
     * @return array|false
     */
    public function loadConfig();
    /**
     * Saves the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * Format of input array:
     * array('config_key' => $newValue, ...)
     *
     * @param array $array
     * @return bool
     */
    public function saveConfig($array);
    /**
     * Deletes the widget config via desired persistance layer. Never called if getConfig() returns false.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you persistence via core_config_data.
     *
     * @return bool
     */
    public function deleteConfig();

}
