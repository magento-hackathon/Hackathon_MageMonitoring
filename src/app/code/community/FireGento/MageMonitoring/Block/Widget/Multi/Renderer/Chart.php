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
 * Block for rendering multi renderer chart
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Widget_Multi_Renderer_Chart
    extends FireGento_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
    implements FireGento_MageMonitoring_Block_Widget_Multi_Renderer
{
    const CONTENT_TYPE_CHART = 'chart';

    /**
     * Retrieve the data for the block output.
     *
     * @return mixed
     */
    public function _getContent()
    {
        return $this->getValues();
    }

    /**
     * Add new slice to pie chart.
     *
     * @param  string $title Title
     * @param  string $value Value
     * @return $this
     */
    public function addValue($title, $value)
    {
        if (is_null($this->getValues())) {
            $this->setValues(array());
        }

        $values = $this->getValues();
        $values[$title] = $value;
        $this->setValues($values);

        return $this;
    }
}
