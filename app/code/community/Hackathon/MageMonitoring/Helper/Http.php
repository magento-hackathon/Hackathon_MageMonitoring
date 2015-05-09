<?php

class Hackathon_MageMonitoring_Helper_Http
    extends Mage_Core_Helper_Abstract
{


    public function checkFile($filePath)
    {
        $baseUrl = Mage::getStoreConfig('web/unsecure/base_url');
        $http = new Zend_Http_Client($baseUrl . $filePath);
        return $http->request();
    }


}