<?php
/**
 * Created by PhpStorm.
 * User: blaber
 * Date: 01/03/14
 * Time: 14:28
 */

class Hackathon_MageMonitoring_Model_Widget_HealthCheck_ProductComposition
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Product Composition Check';
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

    /**
     * @param $simples array like "sku => connected product count"
     * @return array avg, biggest, etc data array
     */
    private function getDataRow($simples)
    {
        $avgSimplePerConfig = number_format(array_sum($simples) / count(array_keys($simples)), 2);
        $biggestConfigurableVal = max($simples);
        $biggestConfigurableSku = array_keys($simples, max($simples));

        return array($avgSimplePerConfig, $biggestConfigurableSku, $biggestConfigurableVal);
    }

    /**
     * @return array @see getDataRow
     *
     * somewhat redundant BundleRow and ConfigurableRow - still beta though
     */
    private function getBundleRow()
    {
        $simples = array();

        Varien_Profiler::start('HEALTHCHECK BUNDLES');

        $resourceModel = Mage::getResourceModel('catalog/product');
        $connection = $resourceModel->getReadConnection();
        $sql = $connection
            ->select()
            ->from(array('cp' => $resourceModel->getTable('catalog/product')))
            ->join(array('cpb' => $resourceModel->getTable('bundle/selection')),
                'cp.entity_id = cpb.parent_product_id',
                array('children_count' => 'count(cpb.parent_product_id)'))
            ->where("type_id = 'bundle'")
            ->group('cp.entity_id');

        $items = $connection->fetchAll($sql);

        if (count($items) != 0) {
            foreach ($items as $bundle) {
                $simples[$bundle['sku']] = 0;
                if (!empty($bundle['children_count'])) {
                    $simples[$bundle['sku']] = $bundle['children_count'];
                }
            }

            Varien_Profiler::stop('HEALTHCHECK BUNDLES');
            return $this->getDataRow($simples);
        } else {
            return false;
        }
    }

    /**
     * @return array @see getDataRow
     *
     * somewhat redundant BundleRow and ConfigurableRow - still beta though - event this comment ;)
     */
    private function getConfigurableRow()
    {
        $simples = array();

        Varien_Profiler::start('HEALTHCHECK CONFIGURABLE');

        $resourceModel = Mage::getResourceModel('catalog/product');
        $connection = $resourceModel->getReadConnection();
        $sql = $connection
            ->select()
            ->from(array('cp' => $resourceModel->getTable('catalog/product')))
            ->join(array('cpc' => $resourceModel->getTable('catalog/product_super_link')),
                'cp.entity_id = cpc.parent_id',
                array('children_count' => 'count(cpc.parent_id)'))
            ->where("type_id = 'configurable'")
            ->group('cp.entity_id');

        $items = $connection->fetchAll($sql);

        if (count($items) != 0) {
            foreach ($items as $configurable) {
                $simples[$configurable['sku']] = 0;
                if (!empty($configurable['children_count'])) {
                    $simples[$configurable['sku']] = $configurable['children_count'];
                }
            }

            Varien_Profiler::stop('HEALTHCHECK CONFIGURABLE');
            return $this->getDataRow($simples);
        } else {
            return false;
        }
    }

    public function getOutput()
    {
        $configurables = $this->getConfigurableRow();
        $bundles = $this->getBundleRow();

        $helper = Mage::helper('magemonitoring');
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        $header = array(
            $helper->__('Avg. connected product count'),
            $helper->__('Most complex product (SKU)'),
            $helper->__('Most simples attached'),
        );

        $renderer->setHeaderRow($header);

        if ($configurables) {
            $renderer->addRow(array('Configurables', 'Configurables', 'Configurables'));
            $renderer->addRow($configurables);
        }

        if ($bundles) {
            $renderer->addRow(array('Bundles', 'Bundles', 'Bundles'));
            $renderer->addRow($bundles);
        }

        if (!$configurables && !$bundles) {
            $noDataText = $helper->__('No data available');
            $renderer->addRow(array($noDataText, $noDataText, $noDataText));
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
