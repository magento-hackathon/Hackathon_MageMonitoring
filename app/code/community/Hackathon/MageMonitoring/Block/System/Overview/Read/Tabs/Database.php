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
 * Block for rendering database information
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Database extends Mage_Adminhtml_Block_Abstract
{
    /**
     * Returns database helper
     *
     * @return Hackathon_MageMonitoring_Helper_Data|Mage_Core_Block_Abstract
     */
    public function getDatabaseHelper()
    {
        return Mage::helper('magemonitoring/database');
    }

    /**
     * Returns template name
     *
     * @return string
     */
    public function getTemplate()
    {
        $connection = $this->getDatabaseHelper()->getConnection();
        $_config = $connection->getConfig();

        switch ($_config['model']) {
            case 'mysql4':
                $_template = 'monitoring/database/mysql.phtml';
                break;
            default:
                $_template = 'monitoring/database/default.phtml';
                break;
        }

        //ToDo: get Database Type and return the right tab
        return $_template;
    }
}
