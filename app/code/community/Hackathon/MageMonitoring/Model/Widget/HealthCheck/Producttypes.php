<?php

class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Producttypes
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_DEF_START_COLLAPSED = 1;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Product Type Check';
    }

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
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    public function getOutput()
    {
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Donutchart $renderer */
        $renderer = $block->newContentRenderer('donutchart');

        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->getSelect()->group('type_id')->columns('type_id, COUNT(*) AS count');

        foreach ($productCollection as $_product) {
            $renderer->addValue($_product->getTypeId(), $_product->getCount());
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
