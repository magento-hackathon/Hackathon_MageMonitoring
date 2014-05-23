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

class Hackathon_MageMonitoring_Model_Widget_System_ClassRewrites extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
                                                                 implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_DEF_DISPLAY_PRIO = 20;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Class Rewrites / Local Overlay';
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
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $rewritesList = Mage::helper('magemonitoring/rewrites')->getRewrites();

        $block = $this->newMonitoringBlock();
        $helper = Mage::helper('magemonitoring');

        if ($rewritesList === false) {
            $block->addRow('success',
                            $helper->__('No rewrites or local overlay found, hurray for the clean implementation!'),
                            '');
        } else {
            $types = array('blocks', 'models', 'helpers');
            foreach ($types as $t) {
                if (!empty($rewritesList[$t]))
                {
                    $block->addHeaderRow($helper->__('%s Rewrites', ucfirst($t)));
                    foreach ($rewritesList[$t] as $node => $rewriteInfo) {
                        $block->addRow($this->_getIconType($rewriteInfo), $node, implode(', ', array_values($rewriteInfo['classes'])));
                    }
                }
            }

            if (!empty($rewritesList['autoload']))
            {
                $block->addHeaderRow($helper->__('Local Pool Overrides'));
                foreach ($rewritesList['autoload'] as $node => $rewriteInfo) {
                    $block->addRow('warning', '', implode(', ', array_values($rewriteInfo['classes'])));
                }
            }

        }

        $this->_output[] = $block;
        return $this->_output;
    }

    /**
     * Return rewrite warning level
     *
     * @param array $rewriteInfo Rewrite info array
     *
     * @return string
     */
    protected function _getIconType($rewriteInfo)
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
