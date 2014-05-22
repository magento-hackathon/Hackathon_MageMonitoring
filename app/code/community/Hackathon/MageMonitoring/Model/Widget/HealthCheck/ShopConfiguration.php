<?php

class Hackathon_MageMonitoring_Model_Widget_HealthCheck_ShopConfiguration
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Shop Configuration';
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

    protected function _getValues()
    {
        return Mage::getConfig()->getNode('global/healthcheck/shop_configuration/values')->children();
    }

    public function getOutput()
    {
        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = Mage::helper('magemonitoring');

        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        $renderer->setHeaderRow(
            array(
                $helper->__('Configuration Parameter'),
                $helper->__('Configuration Path'),
                $helper->__('Configuration Value'),
                $helper->__('Configuration Recommendation')
            )
        );
        foreach ($this->_getValues() as $_valueName => $_config) {
            $rowConfig = array();
            $path = (string)$_config->path;
            $configValue =  Mage::getStoreConfig($path);

            if (is_null($configValue)) {
                $configValue = '---';
            }
            // get a more readable parameter name
            $paramName = (string) $_config->path;
            $beautyfulParamName = str_replace(
                array('/', '_'),
                array(' > ', ' '),
                $paramName
            );

            $recommendation = (string) $_config->recommendation;
            if ($recommendation) {
                if ($configValue == $recommendation) {
                    $rowConfig = array('_cssClasses'   => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_OK);
                } else {
                    $rowConfig = array('_cssClasses'   => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_WARNING);
                }
            } else {
                $recommendation = '---';
            }
            $renderer->addRow(
                array($beautyfulParamName, $paramName, $configValue, $recommendation),
                $rowConfig
            );
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
