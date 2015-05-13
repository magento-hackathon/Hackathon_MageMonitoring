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
 * Block for rendering widget configuration
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Block_Widget_Multi_Renderer_Plaintext
    extends Firegento_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
    implements Firegento_MageMonitoring_Block_Widget_Multi_Renderer
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
