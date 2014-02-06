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

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('magemonitoring_tabs');
        $this->setDestElementId('read_form');
        $this->setTitle($this->__('Mage Monitoring'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'dashboard_section', array(
                    'label' => $this->__('Dashboard'),
                    'title' => $this->__('Dashboard'),
                    'content' => $this->getLayout()->createBlock(
                            'magemonitoring/system_overview_read_tabs_dashboard'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'main_section', array(
                'label' => $this->__('System Overview'),
                'title' => $this->__('System Overview'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_overview'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'php_section', array(
                'label' => $this->__('PHP Information'),
                'title' => $this->__('PHP Information'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_php'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'cachestats_section', array(
                'label' => $this->__('Cache Statistics'),
                'title' => $this->__('Cache Statistics'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_cacheStats'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'rewrites_section', array(
                'label' => $this->__('Class Rewrites'),
                'title' => $this->__('Class Rewrites'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_rewrites'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'modules_section', array(
                'label' => $this->__('Modules Installed'),
                'title' => $this->__('Modules Installed'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_modules'
                    )->toHtml(),
            )
        );

        $this->addTab(
            'logs_section', array(
                'label' => $this->__('System Logs'),
                'title' => $this->__('System Logs'),
                'content' => $this->getLayout()->createBlock(
                        'magemonitoring/system_overview_read_tabs_logs'
                    )->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}