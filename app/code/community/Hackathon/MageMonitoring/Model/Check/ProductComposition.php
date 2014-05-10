<?php
/**
 * Created by PhpStorm.
 * User: blaber
 * Date: 01/03/14
 * Time: 14:28
 */


class Hackathon_MageMonitoring_Model_Check_ProductComposition extends Hackathon_MageMonitoring_Model_Check_Abstract
{
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

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('type_id', array('eq' => 'bundle'));

        if (count($collection) != 0) {
            foreach ($collection as $bundle) {
                $simples[$bundle->getSku()] = 0;
                foreach ($bundle->getTypeInstance()->getChildrenIds($bundle->getId()) as $simpleGroup) {
                    $simples[$bundle->getSku()] += count($simpleGroup);
                }
            }
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

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'));

        if (count($collection) != 0) {
            foreach ($collection as $configurable) {
                $simples[$configurable->getSku()] = count($configurable->getTypeInstance()->getUsedProductIds());
            }
            return $this->getDataRow($simples);

        } else {
            return false;
        }
    }

    public function _run()
    {
        $configurables = $this->getConfigurableRow();
        $bundles = $this->getBundleRow();

        $helper = Mage::helper('magemonitoring');
        $renderer = $this->getContentRenderer();

        $header = array(
            $helper->__('Avg. connected product count'),
            $helper->__('Most complex product (SKU)'),
            $helper->__('Most simples attached'),
        );

        $renderer->setHeaderRow($header);

        if ($configurables) {
            $renderer->addRow($configurables);
        } else if ($bundles) {
            $renderer->addRow(array('Bundles', 'Bundles', 'Bundles'));
            $renderer->addRow($bundles);
        } else {
            $this->throwPlaintextContent('No data available');
        }

    }

}