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
 * class Hackathon_MageMonitoring_Model_Widget_System_PhpExtensions
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_System_PhpExtensions
    extends Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
    implements Hackathon_MageMonitoring_Model_Widget
{
    const CONFIG_ONLY_REQUIRED = 'only_required';

    // set/override defaults
    protected $_defWidgetTitle  = 'PHP Extensions';
    protected $_defOnlyRequired = false;
    protected $_defDisplayPrio  = 20;
    protected $_reqExtensions   = array(
        'curl',
        'dom',
        'gd',
        'hash',
        'iconv',
        'mcrypt',
        'pcre',
        'pdo',
        'pdo_mysql',
        'simplexml',
        'soap'
    );

    /**
     * Returns version
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Initialize configuration
     *
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for required extensions filter
        $this->addConfig(self::CONFIG_ONLY_REQUIRED,
                'Only show required extensions:',
                $this->_defOnlyRequired,
                'widget',
                'checkbox',
                false,
                'Only show extensions required by Magento.');

        return $this->_config;
    }

    /**
     * Fetches and returns output
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $helper = Mage::helper('magemonitoring');
        $block = $this->newMonitoringBlock();

        $loadedExtensions = get_loaded_extensions();
        foreach ($loadedExtensions as $extension) {
            if ($this->getConfig(self::CONFIG_ONLY_REQUIRED) && !in_array($extension, $this->_reqExtensions)) {
                continue;
            }
            $class = 'info';
            if (in_array($extension, $this->_reqExtensions)) {
                $class = (extension_loaded($extension)) ? 'success' : 'error';
            }
            $block->addRow(
                $class, $extension, (phpversion($extension)) ? phpversion($extension) : $helper->__('enabled')
            );
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
