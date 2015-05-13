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
 * Database helper
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Helper_Database extends Mage_Core_Helper_Abstract
{
    private $_connection = null;

    //ToDo: make this arrays configurable in backend
    private $_serverSettings = array();
    private $_innodbSettings = array();

    /**
     * Return database connection
     *
     * @return DB connection
     */
    public function getConnection()
    {
        if (!$this->_connection) {
            $this->_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        }

        return $this->_connection;
    }

    /**
     * Returns MySQL version
     *
     * @return int
     */
    public function getMysqlVersion()
    {
        return $this->getConnection()->getAttribute(PDO::ATTR_CLIENT_VERSION);
    }

    /**
     * Returns MySQL server information
     *
     * @return array
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
        $result  = array();

        foreach ($results as $_result) {
            if ($_result['Variable_name'] == 'version') {
                $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
            }
        }

        return $result;
    }

    /**
     * Returns MySQL server status
     *
     * @return array
     */
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
        $result  = array();

        foreach ($results as $_result) {
            $result[] = array('label' => $_result['Variable_name'], 'value' => $_result['Value']);
        }

        return $result;
    }

    /**
     * Returns MySQL tuner output
     *
     * @return string
     * @todo Implement
     */
    public function getMysqlTunerOutput()
    {
        return "ToDo";
    }

    /**
     * Returns MySQL traffic information
     *
     * @return string
     * @todo Implement
     */
    public function getMysqlTrafficInformation()
    {
        return "ToDo";
    }

    /**
     * Returns MySQL connection information
     *
     * @return string
     * @todo Implement
     */
    public function getMysqlConnectionInformation()
    {
        return "ToDo";
    }

    /**
     * Returns MySQL statements information
     *
     * @return string
     * @todo Implement
     */
    public function getMysqlStatementsInformation()
    {
        return "ToDo";
    }

    /**
     * Returns MySQL server settings information
     *
     * @return array
     */
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

    /**
     * Returns MySQL innoDB buffer size information
     *
     * @return array
     */
    public function getMysqlInnodbBufferSizeInformation()
    {
        $query = <<<EOS
SELECT
    engine,
    FORMAT(((sum(index_length)) + (sum(data_length))), 0) " total_size"
FROM
    information_schema.TABLES
WHERE
    engine IS NOT NULL
GROUP BY
    engine;
EOS;

        $readConnection = $this->getConnection();
        $results = $readConnection->fetchAll($query);

        $query = 'SHOW VARIABLES like "%innodb_buffer_pool_size%";';
        $_poolSizeValue = $readConnection->fetchAll($query);
        $results[] = $_poolSizeValue[0];
        $_comparableResult = array('label' => 'InnoDB Buffer Pool Size Check');
        $_bufferPoolSizeRecommendationValue = 0;
        $_bufferPoolSizeValue = 0;

        foreach ($results as $_result) {
            if (array_key_exists('Variable_name', $_result) && $_result['Variable_name'] == 'innodb_buffer_pool_size') {
                $_bufferPoolSizeValue = (int)str_replace(',', '', $_result['Value']);
                $_comparableResult['settings'][$_result['Variable_name']] = $_bufferPoolSizeValue;

            }
            if (array_key_exists('engine', $_result) && $_result['engine'] == 'InnoDB') {
                $_bufferPoolSizeRecommendationValue = (int)str_replace(',', '', $_result['total_size']);
                $_comparableResult['settings'][$_result['engine']] = $_bufferPoolSizeRecommendationValue;
            }
        }

        $_comparableResult['check'] = ($_bufferPoolSizeRecommendationValue > $_bufferPoolSizeValue) ? 'ok' : 'warning';

        return $_comparableResult;
    }
}
