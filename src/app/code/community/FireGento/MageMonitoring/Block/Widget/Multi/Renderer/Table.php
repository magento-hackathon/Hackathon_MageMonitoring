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
 * Block for rendering multi renderer table
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Widget_Multi_Renderer_Table
    extends FireGento_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
    implements FireGento_MageMonitoring_Block_Widget_Multi_Renderer
{
    const CONTENT_TYPE_TABLE = 'table';

    /**
     * Constructor
     */
    public function _construct()
    {
        $this->init();
    }

    /**
     * Reset the table rows.
     */
    public function init()
    {
        $this->setRows(array());
        $this->setHeaderRow(array());

        return $this;
    }

    /**
     * Retrieve the data for the block output.
     *
     * @return mixed
     */
    public function _getContent()
    {
        $result = array();
        foreach ($this->getRows() as $row) {
            $rowData = array_combine($this->getHeaderRow(), $row['values']);
            if (isset($row['config'])) {
                $rowData = array_merge($rowData, $row['config']);
            }
            $result[] = $rowData;
        }
        return $result;
    }

    /**
     * Add new row to table.
     *
     * @param  array $row       Data row
     * @param  array $rowConfig Row configuration
     * @return $this
     */
    public function addRow($row, $rowConfig = array())
    {
        $rows = $this->getRows();

        // collect data for new row and combine with config
        $rowData = array('values' => $row);
        if (count($rowConfig)) {
            $rowData['config'] = $rowConfig;
        }

        $rows[] = $rowData;
        $this->setRows($rows);

        return $this;
    }
}
