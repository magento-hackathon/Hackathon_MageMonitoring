<?php

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_CacheStats extends Mage_Adminhtml_Block_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('monitoring/cache_stats.phtml');
        return parent::_construct();
    }

    public function getHitRatio($hits, $misses) {
        $total = $misses+$hits;
        if ($total != 0) {
            return round($hits*100/$total,2);
        } else {
            return 0;
        }
    }
}