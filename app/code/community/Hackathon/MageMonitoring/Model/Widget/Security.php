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
 * Class Hackathon_MageMonitoring_Model_Widget_Security
 * checks if the magento backend is available via the default "admin" route.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_Security
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'security';
    const CONFIG_ADMIN_URL_CUSTOM_PATH = 'admin/url/custom_path';
    const CONFIG_ADMIN_URL_XML = 'admin/routers/adminhtml/args/frontName';

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Security');
    }

    /**
     * Returns the name of the widgets xml node
     *
     * @return string
     */
    protected function _getNodeName()
    {
        return self::NODE_NAME;
    }

    /**
     * Display whether Mangento backend is reachable under the default "admin" route.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        parent::_renderMoreChecks();

        $helper = $this->_getHelper();

        $node = (string)Mage::getConfig()->getNode(self::CONFIG_ADMIN_URL_XML);

        $this->getRenderer()->addRow(
            array(
                $helper->__('Admin-URL in XML'),
                'XML-Node: ' . self::CONFIG_ADMIN_URL_XML, $node, $helper->__('not admin')
            ),
            $this->_getRowConfig(!is_null($node) && $node !== 'admin')
        );

        $configValue = Mage::getStoreConfig(self::CONFIG_ADMIN_URL_CUSTOM_PATH);
        $this->getRenderer()->addRow(
            array(
                $helper->__('Custom admin URL'),
                self::CONFIG_ADMIN_URL_CUSTOM_PATH,
                (is_null($configValue)) ? 'not set' : $configValue, $helper->__('not admin')
            ),
            $this->_getRowConfig(!is_null($configValue) && $configValue !== 'admin')
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

}
