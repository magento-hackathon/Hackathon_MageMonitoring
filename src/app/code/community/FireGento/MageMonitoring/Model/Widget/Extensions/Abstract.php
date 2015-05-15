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
 * Class FireGento_MageMonitoring_Model_Widget_Extensions_Abstract
 * provides the base for all extension check implementations.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
abstract class FireGento_MageMonitoring_Model_Widget_Extensions_Abstract
    extends FireGento_MageMonitoring_Model_Widget_Abstract
{

    /** @var array */
    protected $_installedExtensions = null;

    /**
     * Get a list of installed extensions.
     *
     * @return array
     */
    protected function _getInstalledExtensions()
    {
        if (!is_array($this->_installedExtensions)) {
            $this->_installedExtensions = (array) Mage::getConfig()->getNode('modules')->children();
        }
        return $this->_installedExtensions;
    }

    /**
     * Check if the extension with the specified name is installed.
     *
     * @param  string $extensionName The name of an extension (e.g. "Aoe_CacheCleaner")
     *
     * @return $this
     */
    protected function _checkExtensionInstalled($extensionName)
    {
        /** @var FireGento_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        $extensions = $this->_getInstalledExtensions();
        $installed = isset($extensions[$extensionName]);
        $installedAndInactive = $installed && (string) $extensions[$extensionName]->active != "true";

        if ($installedAndInactive) {
            $result = 'deactivated';
        } elseif (!$installed) {
            $result = 'not installed';
        } else {
            $result = 'installed';
        }

        $this->getRenderer()->addRow(
            array(
                $extensionName,
                $helper->__('Check if extension <strong>%s</strong> is installed and activated.', $extensionName),
                $helper->__($result),
                $helper->__('installed')
            ),
            $this->_getRowConfig($installed && !$installedAndInactive)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Check if the supplied list of extensions is installed.
     *
     * @param  array $extensions A list of extension names
     *
     * @return $this
     */
    protected function _checkExtensions(Array $extensions)
    {
        foreach ($extensions as $_extension) {
            $this->_checkExtensionInstalled($_extension);
        }
        return $this;

    }

}
