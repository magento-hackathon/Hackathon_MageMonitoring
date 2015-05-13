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
 * Block for rendering tabs
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Tab extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // don't let the core try to guess the form block
        $this->_blockGroup = null;
        $this->_headerText = '';

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');

        $this->addButton('tab_config', array(
                'label' => $this->__('Tab Config'),
                'onclick' => 'setLocation(\''.$this->getUrl('*/*/config_tabs').'\')',
                'class' => 'config'
        ));

        $this->addButton('flush_all_cache', array(
            'label' => $this->__('Flush All Caches'),
            'onclick' => 'confirmSetLocation(\'' .
                $this->__('Do you really want to flush all caches?') .'\', \'' .
                $this->getUrl('*/*/flushallcache') .
            '\')',
            'class' => 'delete'
        ));
    }

    /**
     * Prepare layout
     *
     * @return mixed
     */
    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock('magemonitoring/tab_form'));
        $tabList = $this->getLayout()->createBlock('magemonitoring/tab_list', 'magemonitoring_tabs');
        $this->getLayout()->getBlock('left')->append($tabList);

        return parent::_prepareLayout();
    }
}
