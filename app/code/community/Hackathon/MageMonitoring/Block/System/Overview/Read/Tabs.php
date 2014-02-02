<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('magemonitoring_tabs');
        $this->setDestElementId('read_form');
        $this->setTitle($this->__('System Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
                'cachestats_section', array(
                        'label'   => $this->__('Cache Statistics'),
                        'title'   => $this->__('Cache Statistics'),
                        'content' => $this->getLayout()->createBlock(
                                'magemonitoring/system_overview_read_tabs_cacheStats'
                        )->toHtml(),
                )
        );

        $this->addTab(
                'main_section', array(
                        'label'   => $this->__('System Overview'),
                        'title'   => $this->__('System Overview'),
                        'content' => $this->getLayout()->createBlock(
                                'magemonitoring/system_overview_read_tabs_overview'
                        )->toHtml(),
                )
        );

        $this->addTab(
            'logs_section', array(
                'label'   => $this->__('System Logs'),
                'title'   => $this->__('System Logs'),
                'content' => $this->getLayout()->createBlock(
                    'magemonitoring/system_overview_read_tabs_logs'
                )->toHtml(),
            )
        );

        $this->addTab(
            'php_section', array(
                'label'   => $this->__('PHP Information'),
                'title'   => $this->__('PHP Information'),
                'content' => $this->getLayout()->createBlock(
                    'magemonitoring/system_overview_read_tabs_php'
                )->toHtml(),
            )
        );

        $this->addTab(
            'rewrites_section', array(
                'label'   => $this->__('Class Rewrites'),
                'title'   => $this->__('Class Rewrites'),
                'content' => $this->getLayout()->createBlock(
                    'magemonitoring/system_overview_read_tabs_rewrites'
                )->toHtml(),
            )
        );

//        $this->addTab(
//            'example_section', array(
//                'label'   => $this->__('Example'),
//                'title'   => $this->__('Example'),
//                'content' => $this->getLayout()->createBlock(
//                    'magemonitoring/system_overview_read_tabs_example'
//                )->toHtml(),
//            )
//        );

        return parent::_beforeToHtml();
    }

}