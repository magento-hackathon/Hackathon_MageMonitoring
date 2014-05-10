<?php

class Hackathon_MageMonitoring_Helper_Database extends Mage_Core_Helper_Abstract
{
    private $_connection = null;

    private $_settings = array("have_innodb");

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
     * @return string
     */
    public function getMysqlServerInfo()
    {
        /**
         * Retrieve the read connection
         */
        $readConnection = $this->getConnection();

        $query = 'SHOW VARIABLES LIKE "%version%"';

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);

        foreach ($results as $_result){
            if($_result['Variable_name'] == 'version'){
                $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
            }
        }

        return $result;
    }

    public function getMysqlServerStatus()
    {
        /**
         * Retrieve the read connection
         */
        $readConnection = $this->getConnection();

        $query = 'SHOW STATUS;';

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);

        foreach ($results as $_result) {
            $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
        }

        return $result;
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

    public function getMysqlServerSettingsInformation()
    {

        /**
         * Retrieve the read connection
         */
        $readConnection = $this->getConnection();

        $query = 'SHOW VARIABLES;';

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);

        $result = array();
        foreach ($results as $_result) {
            if (!count($this->_settings) || in_array($_result['Variable_name'], $this->_settings)) {
                $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
            }
        }

        return $result;
    }

}