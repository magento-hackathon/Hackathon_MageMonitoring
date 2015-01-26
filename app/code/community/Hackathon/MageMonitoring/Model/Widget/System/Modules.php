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

class Hackathon_MageMonitoring_Model_Widget_System_Modules extends Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
                                                           implements Hackathon_MageMonitoring_Model_Widget
{
    const CONFIG_CODE_POOL = 'code_pool';
    // set/override defaults
    protected $_DEF_WIDGET_TITLE = 'Magento Modules';
    protected $_DEF_CODE_POOL = 'all';
    protected $_DEF_DISPLAY_PRIO = 20;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for code pool filter
        $this->addConfig(self::CONFIG_CODE_POOL,
                'Filter by code pool:',
                $this->_DEF_CODE_POOL,
                'widget',
                'text',
                true,
                'all | community | core | local');
        return $this->_config;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $block = $this->newMonitoringBlock();
        $poolFilter = $this->getConfig(self::CONFIG_CODE_POOL);
        $modules = (array)Mage::getConfig()->getModuleConfig();
        $notEmpty = false;
        foreach ($modules as $key => $module) {
            if ($poolFilter === 'all' || $module->codePool == $poolFilter) {
                $notEmpty = true;
                $block->addRow($module->is('active') ? 'success' : 'error', $key, $module->version);
            }
        }
        if (!$notEmpty) {
            $block->addRow('info', Mage::helper('magemonitoring')->__('No modules found.'));
        }

        $this->_output[] = $block;
        return $this->_output;
    }

}
