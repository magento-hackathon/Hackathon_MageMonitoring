<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'monitoring/healthcheck/dashboard.phtml';

    public function getAllChecks()
    {
        /** @var Hackathon_MageMonitoring_Model_Check_Collection $collection */
        $collection = Mage::getModel('magemonitoring/check_collection');
        $collection->setOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC);

        return $collection;
    }

    public function getCheckBlock(Hackathon_MageMonitoring_Model_Check_Abstract $check)
    {
        if ($check->isAvailable()) {
            $blockString = 'magemonitoring/type_' . $check->getContentType();
            $block = $this->getLayout()->createBlock($blockString);
            $block
                ->setCheck($check)
                ->setTemplate('monitoring/healthcheck/type/default.phtml');

            return $block;
        }
    }
}