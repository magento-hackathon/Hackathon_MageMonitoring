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
 * class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Logfilesize
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Logfilesize
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_defStartCollapsed = 1;

    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Logfile Size Check';
    }

    /**
     * Returns version
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * Returns isActive flag
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Fetches output
     *
     * @return array
     */
    public function getOutput()
    {
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Barchart $renderer */
        $renderer = $block->newContentRenderer('barchart');
        $path = Mage::getBaseDir() . '/var/log/';

        if (is_dir($path) && file_exists($path)) {
            if ($handle = opendir($path)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != ".." && strpos($file, '.log')) {
                        $filesize = filesize($path . $file);
                        // Byte to MB conversion, round to two
                        /**
                         * @TODO dynamically choose KB, MB, BG and use it correct in frontend
                         */
                        $renderer->addValue($file, number_format($filesize / 1024 / 1024, 2));
                    }
                }
                closedir($handle);
            }
        } else {
            $this->dump('No log directory');
        }

        $this->_output[] = $block;

        return $this->_output;
    }
}
