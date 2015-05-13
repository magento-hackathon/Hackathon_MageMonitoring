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
 * Block for rendering widget multi
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Widget_Multi extends Mage_Core_Block_Template
{
    protected $_renderer = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('monitoring/widget/multi.phtml');
    }

    /**
     * Initializes and returns a content renderer block of specified type.
     *
     * @param  string $type Renderer type
     * @return Firegento_MageMonitoring_Block_Widget_Multi_Renderer
     */
    public function newContentRenderer($type = 'table')
    {
        $blockString = 'magemonitoring/widget_multi_renderer_' . $type;
        $renderer = $this->getLayout()->createBlock($blockString);
        if ($renderer instanceof Firegento_MageMonitoring_Block_Widget_Multi_Renderer) {
            $renderer->setWidgetId($this->getWidgetId())
                ->setTabId($this->getTabId())
                ->setType($type);
            $this->_renderer = $renderer;
            return $renderer;
        } else {
            Mage::throwException('Renderer not found: ' . $type);
        }
    }

    /**
     * Returns current content renderer.
     *
     * @return Firegento_MageMonitoring_Block_Widget_Multi_Renderer
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            Mage::throwException('Error: Undefined renderer.');
        }

        return $this->_renderer;
    }
}
