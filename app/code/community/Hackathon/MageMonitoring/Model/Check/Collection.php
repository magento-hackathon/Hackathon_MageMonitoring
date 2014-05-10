<?php

class Hackathon_MageMonitoring_Model_Check_Collection extends Varien_Data_Collection
{

    protected function _sort($a, $b)
    {
        foreach ($this->_orders as $_key => $_order) {
            $aSortOrder = (is_null($a->{$_key})) ? PHP_INT_MAX : $a->{$_key};
            $bSortOrder = (is_null($b->{$_key})) ? PHP_INT_MAX : $b->{$_key};
            if (Varien_Data_Collection::SORT_ORDER_ASC == $_order) {
                return $aSortOrder - $bSortOrder;
            } else {
                return $bSortOrder - $aSortOrder;
            }
        }
        return 0;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        $nodeString = trim(sprintf(Hackathon_MageMonitoring_Helper_Data::CHECK_NODE, ''), '/');
        $checkConfig = Mage::getConfig()->getNode($nodeString);

        /** @var Hackathon_MageMonitoring_Model_Factory $factory */
        $factory = Mage::getModel('magemonitoring/factory');

        $checks = array();
        foreach ($checkConfig->asArray() as $key => $_checkConfig) {
            $checks[] = $factory->getCheck($key);
        }
        usort($checks, array($this, '_sort'));

        foreach ($checks as $_check) {
            $this->_addItem($_check);
        }

        return $this;
    }
}