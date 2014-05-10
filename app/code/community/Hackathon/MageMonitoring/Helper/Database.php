<?php

class Hackathon_MageMonitoring_Helper_Database extends Mage_Core_Helper_Abstract
{
    private $_connection = null;

    public function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        }

        return $this->_connection;
    }

    /**
     * @return array
     */
    public function getMysqlVersion()
    {
        return $this->getConnection()->getAttribute(PDO::ATTR_CLIENT_VERSION);
    }

    /**
     * @return mixed
     */
    public function getMysqlServerInfo()
    {
        return "ToDo";
    }

    public function getMysqlTunerOutput()
    {
        return "ToDo";
    }

    public function getMysqlTrafficInformation()
    {
        return "ToDo";
    }

    public function getMysqlConnectionInformation()
    {
        return "ToDo";
    }

    public function getMysqlStatementsInformation()
    {
        return "ToDo";
    }

    public function getMysqlServerSettingInformation()
    {
        $_settings = array(
            "char_set"
        );

        return "ToDo";
    }

}