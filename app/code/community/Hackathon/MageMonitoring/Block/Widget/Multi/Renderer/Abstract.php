<?php

class Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('monitoring/widget/multi/renderer/default.phtml');
    }

    /**
     * Encode the content to json.
     *
     * @param $content mixed
     * @return string
     */
    protected function _encode($content)
    {
        return Mage::helper('core')->jsonEncode($content);
    }

    /**
     * Return the encoded content.
     *
     * @return mixed
     */
    public function getContent()
    {
        $content = $this->_getContent();
        if (empty($content)) {
            return $this->_encode(Mage::helper('magemonitoring')->__('No information available'));
        }
        $result = array(
                'type'      => $this->getType(),
                'content'   => $content
        );

        return $this->_encode($result);
    }

    /**
     * Returns div id used for rendering content.
     *
     * @return string
     */
    public function getDivId() {
        return 'multi_' . $this->getTabId() . '_' . $this->getWidgetId();
    }

}
