<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Hackathon_MageMonitoringExt_Model_Widget_Dummy_CacheDummy
    extends Hackathon_MageMonitoring_Model_Widget_CacheStat_Abstract
    implements Hackathon_MageMonitoring_Model_Widget_CacheStat, Hackathon_MageMonitoring_Model_WatchDog
{
    // override defaults
    protected $_DEF_START_COLLAPSED = 1;
    protected $_DEF_WATCHDOG_ACTIVE = 0;

    public function __construct() {}

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model::getName()
     */
    public function getName()
    {
        return 'Dummy CacheStat Widget';
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
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryMax()
     */
    public function getMemoryMax()
    {
        return 1024*1024*1024;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getMemoryUsed()
     */
    public function getMemoryUsed()
    {
        return 1008*1024*1024;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheHits()
     */
    public function getCacheHits()
    {
        return 64556;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::getCacheMisses()
     */
    public function getCacheMisses()
    {
        return 727;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        // generate default cache widget output..
        parent::getOutput();

        // grab generated block so we can play with it
        $defaultBlock = reset($this->_output);

        // no buttons please.
        $defaultBlock->setButtons();

        // add some more data
        $defaultBlock->addRow('info', 'Simple Info Stat with no chart', '42');

        // prepare a chart
        $chartData = array(array('value' => 34, 'color' => '#ff0000'),
                           array('value' => 12, 'color' => '#00ff00'),
                           array('value' => 42, 'color' => '#0000ff')
                           );
        $chart = $defaultBlock->newChartArray('another_imp', $chartData);

        // add row with chart
        $defaultBlock->addRow('warning', 'Another Important Stat with a 3 pieces pie chart', 'I like pie.', $chart);

        // add another block to output that just dumps passed string
        $this->dump('<br/>hello world!<br/>');

        //$block = Mage::app()->getLayout()->createBlock('yourmod/yourblock');
        //$this->_output[] = $block;

        return $this->_output;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget_CacheStat::flushCache()
     */
    public function flushCache()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_WatchDog::watch()
     */
    public function watch() {
        $this->addReportRow('warning', 'some label', 'some warning');
        return $this->_report;
    }

}
