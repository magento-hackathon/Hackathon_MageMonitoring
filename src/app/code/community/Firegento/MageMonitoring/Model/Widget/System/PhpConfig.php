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
 * class Firegento_MageMonitoring_Model_Widget_System_PhpConfig
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_System_PhpConfig
    extends Firegento_MageMonitoring_Model_Widget_System_Abstract
    implements Firegento_MageMonitoring_Model_Widget
{
    protected $_configCheck = array(
        'safe_mode' => '0',
        'memory_limit' => 512, // in MB
        'post_max_size' => 100, // in MB
        'upload_max_filesize' => 100, // in MB
        'file_uploads' => '1',
        'sendmail_from' => '',
        'sendmail_path' => '',
        'smtp_port' => '',
        'SMTP' => '',
        'soap.wsdl_cache' => '',
        //'soap.wsdl_cache_dir' => '',
        'soap.wsdl_cache_enabled' => '',
        'soap.wsdl_cache_ttl' => '',
    );

    /**
     * Returns name
     *
     * @see Firegento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'PHP Config Check';
    }

    /**
     * Returns version
     *
     * @see Firegento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Fetches and returns output
     *
     * @see Firegento_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $helper = Mage::helper('magemonitoring');
        $block = $this->newMonitoringBlock();

        $recTxt = '';
        $class = 'success';
        if (version_compare(phpversion(), '5.2.13', 'lt') || version_compare(phpversion(), '5.5', 'ge')) {
            $recTxt = ' - ' . $helper->__('Should be 5.2.13 - 5.3.24, 5.4.x needs official Magento Patch.');
            $class = 'warning';
        }
        $block->addRow($class, 'Version', phpversion() . $recTxt);

        foreach ($this->_configCheck as $key => $config) {
            if (in_array($key, array('memory_limit', 'post_max_size', 'upload_max_filesize'))) {
                $class = ((int) $helper->getValueInByte(ini_get($key), true) < (int) $config) ? 'warning' : 'success';
                $value = $helper->getValueInByte(ini_get($key), true) . 'MB';
                $recommended = ($config && $class == 'warning') ? $config . 'MB' : false;
            } else {
                $value = ini_get($key);
                $class = (empty($config) || (!empty($config) && (bool) $config == (bool) $value) )
                    ? 'success'
                    : 'warning';
                $recommended = ($config && $class == 'warning') ? $config : false;
            }
            if ($recommended) {
                $value .= ' - ' . $helper->__('Recommended') . ': ' . $recommended;
            }
            $block->addRow($class, $key, $value);
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
