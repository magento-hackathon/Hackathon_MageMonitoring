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
 * Block for rendering tab list
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Block_Tab_List
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('magemonitoring_tab_list');
        $this->setDestElementId('tab_form');
        $this->setTitle($this->__('Mage Monitoring'));
    }

    /**
     * Add tabs
     *
     * @return mixed
     */
    protected function _beforeToHtml()
    {
        $tabs = Mage::helper('magemonitoring')->getConfiguredTabs();
        foreach ($tabs as $tabId => $tab) {
            // custom block for tab?
            if (array_key_exists('block', $tab)) {
                $block = $tab['block'];
            } else {
                $block = 'magemonitoring/tab_content_widgetList';
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
