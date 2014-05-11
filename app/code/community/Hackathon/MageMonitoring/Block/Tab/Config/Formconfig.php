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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Hackathon_MageMonitoring_Block_Tab_Config_Formconfig extends Mage_Adminhtml_Block_Widget_Form
{
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
                $fieldParams = array(
                    'label' => Mage::helper('magemonitoring')->__($c['label']),
                    'note' => Mage::helper('magemonitoring')->__($c['tooltip']),
                    'name' => $k,
                    'required' => $c['required'],
                    'class' => 'required-entry validate-no-html-tags',
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
            $onClick = "Modalbox.show('".$postUrl."',
                                   {title: 'Saving Config..',
                                   params: Form.serialize('set_prop_form'),
                                   method: 'post',
                                  });
                     return false;";
            $this->setChild('form_after',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('magemonitoring')->__('Save'),
                'onclick'   => $onClick,
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

        $form->setId('set_prop_form');
        $form->setMethod('post');
        $form->setAction('#');
        $form->setUseContainer(true);
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
