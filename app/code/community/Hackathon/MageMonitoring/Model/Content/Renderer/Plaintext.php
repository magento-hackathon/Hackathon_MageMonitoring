<?php

class Hackathon_MageMonitoring_Model_Content_Renderer_Plaintext extends Hackathon_MageMonitoring_Model_Content_Renderer_Abstract
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