<?php

class Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Plaintext
    extends Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
    implements Hackathon_MageMonitoring_Block_Widget_Multi_Renderer
{
    const CONTENT_TYPE_PLAINTEXT = 'plaintext';

    /**
     * Retrieve the data for the block output.
     *
     * @return string
     */
    public function _getContent()
    {
        if ($this->getPlaintextContent()) {
            return $this->getPlaintextContent();
        }
    }
}
