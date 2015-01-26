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

        Varien_Profiler::start('HEALTHCHECK PRODUCT_TYPE_CHECK');

        $resourceModel = Mage::getResourceModel('catalog/product');
        $connection = $resourceModel->getReadConnection();
        $sql = $connection
            ->select()
            ->from(array('cp' => $resourceModel->getTable('catalog/product')), array('type_id', 'count' => 'count(*)'))
            ->group('cp.type_id');

        $items = $connection->fetchAll($sql);

        foreach($items as $item) {
            $renderer->addValue($item['type_id'], $item['count']);
        }

        Varien_Profiler::stop('HEALTHCHECK PRODUCT_TYPE_CHECK');

        $this->_output[] = $block;

        return $this->_output;

    }
}
