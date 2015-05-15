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
 * Block for rendering widget configuration
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Tab_Config_Form_WidgetConf extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $widget = $this->getWidget();

        $form = new Varien_Data_Form();
        $formLabel = Mage::helper('magemonitoring')->__('Widget Config');
        if ($widget) {
            $formLabel = $widget->getName().' - '.$formLabel;
        }
        $fieldset = $form->addFieldset('widget_config', array('legend'=> $formLabel));

        if ($widget) {
            $fieldParams = array(
                    'label' => Mage::helper('magemonitoring')->__('Tab Id'),
                    'name' => 'widget_id',
                    'required' => true,
                    'disabled' => true,
                    'class' => 'required-entry validate-no-html-tags',
                    'value' => $this->getTabId()
            );
            $fieldset->addField('tab_id', 'text', $fieldParams);

            $fieldParams['name'] = 'widget_id';
            $fieldParams['disabled'] = false;
            $fieldParams['label'] = Mage::helper('magemonitoring')->__('Widget Id');
            $fieldParams['value'] = $widget->getConfigId();
            $fieldset->addField('widget_id', 'text', $fieldParams);

            $fieldParams['name'] = 'class_name_display';
            $fieldParams['disabled'] = true;
            $fieldParams['label'] = Mage::helper('magemonitoring')->__('Class');
            $fieldParams['value'] = $widget->getId();
            $fieldset->addField('class_name_display', 'text', $fieldParams);
            $fieldset->addField('class_name', 'hidden',
                                array(
                                    'name' => 'class_name',
                                    'value' => $widget->getId(),
                                )
            );
            $fieldset->addField('widget_id_org', 'hidden',
                    array(
                            'name' => 'widget_id_org',
                            'value' => $this->getWidgetIdOrg(),
                    )
            );
            foreach ($widget->getConfig() as $k => $c) {
                if (is_numeric($k)) { // add a custom header @todo looks fugly
                    $fieldset->addField($k, 'text', array(
                            'label' => '',
                            'name' => $k,
                            'disabled' => true,
                            'value' => Mage::helper('magemonitoring')->__($c['label'])
                    ));
                    continue;
                }
                if ($k === 'display_prio') {
                     continue;
                }
                $cssClasses = 'validate-no-html-tags';
                if ($c['required'] === true) {
                    $cssClasses .= ' required-entry';
                }
                $fieldParams = array(
                    'label' => Mage::helper('magemonitoring')->__($c['label']),
                    'note' => Mage::helper('magemonitoring')->__($c['tooltip']),
                    'name' => $k,
                    'required' => $c['required'],
                    'class' => $cssClasses,
                    'value' => $c['value']
                );
                switch ($c['type']) {
                    case 'text':
                        break;
                    case 'checkbox':
                        $fieldParams['checked'] = $c['value'] ? true : false;
                        break;
                }
                $fieldset->addField($k, $c['type'], $fieldParams);
            }
            $postUrl = Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/saveWidgetConf', $widget);
            $this->setChild('form_after',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('magemonitoring')->__('Save'),
                'onclick'   => 'saveWidgetConfig(\''.$postUrl.'\')',
                'class'     => 'save'
            )));
        } else {
            $fieldset->addField('edit_widget_form_help', 'text', array(
                    'label' => '',
                    'name' => 'edit_widget_form_help',
                    'disabled' => true,
                    'value' => Mage::helper('magemonitoring')->__("Select a widget in the tab tree.")
            ));
        }

        $form->setId('widget_conf_form');
        $form->setMethod('post');
        $form->setAction('#');
        $form->setUseContainer(true);
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
