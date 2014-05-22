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
        $tabs = Mage::helper('magemonitoring')->getConfiguredTabs();
        foreach ($tabs as $tabId => $tab) {
            // custom block for tab?
            if (array_key_exists('block', $tab)) {
                $block = $tab['block'];
            } else {
                $block = 'magemonitoring/system_overview_read_tabs_widgetList';
            }
            $block = $this->getLayout()->createBlock($block);
            // pass widgets
            if (array_key_exists('widgets', $tab) && is_array($tab['widgets'])) {
                $block->setWidgets(Mage::helper('magemonitoring')->getConfiguredWidgets($tabId));
                $block->setTabId($tabId);
            }
            // add tab if permissions are ok
            $this->addTab(
                    $tabId, array(
                            'label' => $this->__($tab['label']),
                            'title' => $this->__($tab['title']),
                            'content' => $block->toHtml()
                    )
            );
        }

        return parent::_beforeToHtml();
    }

}
