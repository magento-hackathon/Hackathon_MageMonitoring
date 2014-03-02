<?php

class Hackathon_MageMonitoring_Model_Factory extends Mage_Core_Model_Abstract
{
    /**
     * Retrieve a check model.
     *
     * @param $checkIdentifier
     * @return mixed
     */
    public function getCheck($checkIdentifier)
    {
        $nodeString = sprintf(Hackathon_MageMonitoring_Helper_Data::CHECK_NODE, $checkIdentifier);
        $checkConfig = Mage::getConfig()->getNode($nodeString);

        if ($checkConfig) {
            $model = Mage::getModel($checkConfig->model);
            $model
                ->setData($checkConfig->asArray())
                ->setCheckId($checkIdentifier)
                ->setConfigNodePath($nodeString)
            ;
            return $model;
        } else {
            Mage::throwException('Check Identifier not found or not present!');
        }
    }

    /**
     * Return a content block of specified type.
     *
     * @param $check
     * @return mixed
     */
    public function getContentRenderer($check)
    {
        $renderer = Mage::getModel('magemonitoring/content_renderer_' . $check->getContentType());
        if ($renderer) {
            $renderer->setCheck($check);
            return $renderer;
        } else {
            Mage::throwException('Renderer not found: ' . $check->getContentType());
        }
    }
}