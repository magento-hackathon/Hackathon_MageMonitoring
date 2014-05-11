<?php

class Hackathon_MageMonitoring_Helper_Database extends Mage_Core_Helper_Abstract
{
    private $_connection = null;

    //ToDo: make this arrays configurable in backend
    private $_serverSettings = array();
    private $_innodbSettings = array();

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

        foreach ($results as $_result) {
            if ($_result['Variable_name'] == 'version') {
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
            if (!count($this->_serverSettings) || in_array($_result['Variable_name'], $this->_serverSettings)) {
                $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
            }
        }

        return $result;
    }

    public function getMysqlInnodbBufferSizeInformation()
    {

        $query = 'SELECT engine, FORMAT( ( (sum( index_length ) ) + (sum( data_length ) ) ), 0) " total_size" FROM information_schema.TABLES WHERE engine IS NOT NULL GROUP BY engine;';
        $readConnection = $this->getConnection();
        $results = $readConnection->fetchAll($query);

        $query = 'SHOW VARIABLES like "%innodb_buffer_pool_size%";';
        $_poolSizeValue = $readConnection->fetchAll($query);
        $results[] = $_poolSizeValue[0];
        $_comparableResult = array('label' => 'InnoDB Buffer Pool Size Check');

        foreach ($results as $_result) {
            if (array_key_exists('Variable_name', $_result) && $_result['Variable_name'] == "innodb_buffer_pool_size") {
                $_bufferPoolSizeValue = (int)str_replace(',', '', $_result['Value']);
                $_comparableResult[] = array(
                    'label' => $_result['Variable_name'], 'value' => $_bufferPoolSizeValue
                );

            }
            if (array_key_exists('engine', $_result) && $_result['engine'] == "InnoDB") {
                $_bufferPoolSizeRecommendationValue = (int)str_replace(',', '', $_result['total_size']);
                $_comparableResult[] = array(
                    'label' => $_result['engine'], 'value' => $_bufferPoolSizeRecommendationValue
                );

            }

        }

        $_check = ($_bufferPoolSizeRecommendationValue > $_bufferPoolSizeValue) ? "success" : "warning";
        $_comparableResult[] = array(
            'label' => "check", 'value' => $_check
        );

        return $_comparableResult;
    }

}