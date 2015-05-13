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
 * Abstract class for multi widget renderer
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Widget_Multi_Renderer_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('monitoring/widget/multi/renderer/default.phtml');
    }

    /**
     * Encode the content to json.
     *
     * @param  mixed $content The content to encode
     * @return string
     */
    protected function _encode($content)
    {
        return Mage::helper('core')->jsonEncode($content);
    }

    /**
     * Return the encoded content.
     *
     * @return mixed
     */
    public function getContent()
    {
        $content = $this->_getContent();
        if (empty($content)) {
            return $this->_encode(Mage::helper('magemonitoring')->__('No information available'));
        }
        $result = array(
                'type'      => $this->getType(),
                'content'   => $content
        );

        return $this->_encode($result);
    }

    /**
     * Returns div id used for rendering content.
     *
     * @return string
     */
    public function getDivId()
    {
        return 'multi_' . $this->getTabId() . '_' . $this->getWidgetId();
    }
}
