<?php

class Hackathon_MageMonitoring_Helper_Config
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
     * @param null|int|Mage_Core_Model_Store $store
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
     * @param string $bcc
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

