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
 * class FireGento_MageMonitoring_Model_Widget_System_Server
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Model_Widget_System_Server
    extends FireGento_MageMonitoring_Model_Widget_System_Abstract
    implements FireGento_MageMonitoring_Model_Widget
{
    /**
     * Returns name
     *
     * @see FireGento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'System Information';
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
        $block->addRow('info', 'Host Name', $this->_getValue('HTTP_HOST'));
        $block->addRow('info', 'Server Software', $this->_getValue('SERVER_SOFTWARE'));
        $block->addRow('info', 'Server IP Address', $this->_getValue('SERVER_ADDR'));
        $block->addRow('info', 'Server Port', $this->_getValue('SERVER_PORT'));
        $block->addRow('info', 'Server Gateway Interface', $this->_getValue('GATEWAY_INTERFACE'));

        if (!is_null($memInfo = $this->getMemoryInfo())) {
            $block->addRow('info', 'Server Memory', $memInfo);
        }

        if (!is_null($cpuInfo = $this->getCpuInfo())) {
            $block->addRow('info', 'Server CPU', $cpuInfo);
        }

        $block->addRow('info', 'Server Admin', $this->_getValue('SERVER_ADMIN'));
        $block->addRow('info', 'Accept Encoding', $this->_getValue('HTTP_ACCEPT_ENCODING'));
        $block->addRow('info', 'OS Information', $this->getOsInfo());

        $this->_output[] = $block;

        return $this->_output;
    }
}
