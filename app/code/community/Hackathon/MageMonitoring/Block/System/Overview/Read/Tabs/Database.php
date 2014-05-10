<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Database extends Mage_Adminhtml_Block_Abstract
{

    /**
     *
     * @return Hackathon_MageMonitoring_Helper_Data|Mage_Core_Block_Abstract
     */
    public function getMonitoringHelper()
    {
        return Mage::helper('magemonitoring');
    }

    public function getTemplate()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        switch($connection->getConfig()->getModel()){
            case "mysql4":
                $_template = 'monitoring/mysql.phtml';
                break;
            default:
                $_template = 'monitoring/database.phtml';
                break;
        }
        //ToDo: get Database Type and return the right tab
        return $_template;
    }

}