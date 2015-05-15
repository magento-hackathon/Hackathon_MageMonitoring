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
 * class FireGento_MageMonitoring_Helper_Config
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Helper_Config
    extends Mage_Core_Helper_Abstract
{

    const CONFIG_MODULE_ACTIVE = 'magemonitoring/general/module_active';

    /**
     * Retrieve all stores in which this module is activated.
     *
     * @return array
     */
    public function getActiveStoreIds()
    {
        return array_keys(Mage::getConfig()->getStoresConfigByPath(self::CONFIG_MODULE_ACTIVE, array('1')));
    }

    /**
     * Check if module is active in a specific store.
     *
     * @param  null|int|Mage_Core_Model_Store $store Store model
     * @return bool
     */
    public function isModuleActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::CONFIG_MODULE_ACTIVE, $store);
    }

    /**
     * Converts bcc from comma separated string to array.
     *
     * Only valid e-mail address will be returned.
     *
     * @param  string $bcc E-Mail BCC
     * @return array
     */
    protected function _getEmailBcc($bcc)
    {
        // is field filled?
        if (empty($bcc)) {
            return array();
        }

        // split email addresses
        $addresses = $bcc;
        if (is_string($bcc)) {
            $addresses = preg_split('#[,; ]+#', $bcc);
        }

        // validate email addresses
        $validAddresses = array();
        foreach ($addresses as $_address) {
            if (Zend_Validate::is($_address, 'EmailAddress')) {
                $validAddresses[] = $_address;
            }
        }

        return $validAddresses;
    }
}

