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
 * class FireGento_MageMonitoring_Model_Widget_System_PhpInfo
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_System_PhpInfo
    extends FireGento_MageMonitoring_Model_Widget_System_Abstract
    implements FireGento_MageMonitoring_Model_Widget
{
    // global default values
    protected $_defStartCollapsed = true;

    /**
     * Returns name
     *
     * @see FireGento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'PHP Information';
    }

    /**
     * Returns version
     *
     * @see FireGento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Fetches and returns output
     *
     * @see FireGento_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();

        $info = Mage::helper('magemonitoring')->getPhpInfoArray();
        foreach ($info as $title => $configs) {
            $block->addHeaderRow($title);
            if (is_array($configs)) {
                foreach ($configs as $key => $config) {
                    $block->addRow('info', $key, implode(' (new) - ', (array) $config));
                }
            }
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
