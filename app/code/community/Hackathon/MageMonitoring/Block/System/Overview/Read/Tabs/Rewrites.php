<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Rewrites
    extends Mage_Adminhtml_Block_Abstract
{
    protected $_template = 'monitoring/rewrites.phtml';

    /**
     * Returns list of all rewrites in the system
     *
     * @return mixed
     */
    public function getRewritesInfo()
    {
        $rewrites = Mage::getModel('magemonitoring/rewrites')->getRewrites();

        return $rewrites;
    }

    /**
     * Return rewrite warning level
     *
     * @param array $rewriteInfo Rewrite info array
     *
     * @return string
     */
    public function getIconType($rewriteInfo)
    {
        if (isset($rewriteInfo['conflicts']) && count($rewriteInfo['conflicts']) > 1) {
            return 'error';
        }

        if (isset($rewriteInfo['classes']) && count($rewriteInfo['classes']) > 1) {
            return 'warning';
        }

        return 'info';
    }

}