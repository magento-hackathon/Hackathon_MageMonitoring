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
 * Class Firegento_MageMonitoring_Model_Widget_Privacy
 * checks common privacy/security settings such as https urls.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_Privacy
    extends Firegento_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'privacy';
    const CONFIG_SECURE_URL = 'web/secure/base_url';

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Privacy');
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
     * Render the privacy report results.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        parent::_renderMoreChecks();

        /** @var Firegento_MageMonitoring_Helper_Data $helper */
        $helper = Mage::helper('magemonitoring');

        $secureUrl = Mage::getStoreConfig(self::CONFIG_SECURE_URL);
        $isHttps = (substr($secureUrl, 0, 8) === 'https://');

        $this->getRenderer()->addRow(
            array(
                $helper->__('Secure URL is https'),
                self::CONFIG_SECURE_URL,
                $secureUrl,
                $helper->__('starting with https://')
            ),
            $this->_getRowConfig($isHttps)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

}
