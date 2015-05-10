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
 * class Hackathon_MageMonitoring_Model_Widget_HealthCheck_ShopConfiguration
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_HealthCheck_ShopConfiguration
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Shop Configuration';
    }

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
     * Returns isActive flag
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Returns values
     *
     * @return mixed
     */
    protected function _getValues()
    {
        return Mage::getConfig()->getNode('global/healthcheck/shop_configuration/values')->children();
    }

    /**
     * Fetches and returns output
     *
     * @return array
     */
    public function getOutput()
    {
        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = Mage::helper('magemonitoring');

        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        $renderer->setHeaderRow(
            array(
                $helper->__('Configuration Parameter'),
                $helper->__('Configuration Path'),
                $helper->__('Configuration Value'),
                $helper->__('Configuration Recommendation')
            )
        );
        foreach ($this->_getValues() as $_valueName => $_config) {
            $rowConfig = array();
            $path = (string)$_config->path;
            $configValue =  Mage::getStoreConfig($path);

            if (is_null($configValue)) {
                $configValue = '---';
            }
            // get a more readable parameter name
            $paramName = (string) $_config->path;
            $beautyfulParamName = str_replace(
                array('/', '_'),
                array(' > ', ' '),
                $paramName
            );

            $recommendation = (string) $_config->recommendation;
            if ($recommendation) {
                if ($configValue == $recommendation) {
                    $rowConfig = array('_cssClasses'   => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_OK);
                } else {
                    $rowConfig = array('_cssClasses'   => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_WARNING);
                }
            } else {
                $recommendation = '---';
            }
            $renderer->addRow(
                array($beautyfulParamName, $paramName, $configValue, $recommendation),
                $rowConfig
            );
        }

        $this->_output[] = $block;

        return $this->_output;
    }

    /**
     * Returns node name
     */
    protected function _getNodeName()
    {
        // TODO: Implement _getNodeName() method.
    }
}
