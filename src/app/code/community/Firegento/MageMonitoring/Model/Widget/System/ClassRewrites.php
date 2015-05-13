<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageMonitoring
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * class Firegento_MageMonitoring_Model_Widget_System_ClassRewrites
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_System_ClassRewrites
    extends Firegento_MageMonitoring_Model_Widget_System_Abstract
    implements Firegento_MageMonitoring_Model_Widget
{
    protected $_defDisplayPrio = 20;

    /**
     * Returns name
     *
     * @see Firegento_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Class Rewrites / Local Overlay';
    }

    /**
     * Returns version
     *
     * @see Firegento_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Fetches and returns output
     *
     * @see Firegento_MageMonitoring_Model_Widget::getOutput()
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
                if (!empty($rewritesList[$t])) {
                    $block->addHeaderRow($helper->__('%s Rewrites', ucfirst($t)));
                    foreach ($rewritesList[$t] as $node => $rewriteInfo) {
                        $block->addRow(
                            $this->_getIconType($rewriteInfo),
                            $node,
                            implode(', ', array_values($rewriteInfo['classes']))
                        );
                    }
                }
            }

            if (!empty($rewritesList['autoload'])) {
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
     * @param  array $rewriteInfo Rewrite info array
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
