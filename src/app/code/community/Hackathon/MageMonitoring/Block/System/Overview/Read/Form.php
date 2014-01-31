<?php
class Hackathon_MageMonitoring_Block_System_Overview_Read_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init Form Block
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'read_form',
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}