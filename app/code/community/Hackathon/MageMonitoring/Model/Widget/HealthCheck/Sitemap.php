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
 * class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Sitemap
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_HealthCheck_Sitemap
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
    implements Hackathon_MageMonitoring_Model_Widget
{
    /**
     * Returns name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'Sitemap Check';
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
     * Returns file info
     *
     * @param  string                               $pathToFile Path to the file
     * @param  Hackathon_MageMonitoring_Helper_Data $helper     Helper class
     * @return array
     */
    private function _getFileInfo($pathToFile, $helper)
    {
        if (file_exists($pathToFile)) {

            $mtime = filemtime($pathToFile);
            $date = Mage::getModel('core/date')->timestamp(time());
            $date = date('Y-m-d H:i:s', $date);
            $time = strtotime($date) - 86400;


            if ($mtime - $time < 0) {
                $status = $helper->__('OK, but not change within last 24h');
                $warn = array('_cssClasses' => $helper->getConst('WARN_TYPE_WARNING'));
            } else {
                $status = $helper->__('OK');
                $warn = array('_cssClasses' =>  $helper->getConst('WARN_TYPE_OK'));
            }
            $lastModified = date('d.m.Y H:i:s ', $mtime);
        } else {
            $status = $helper->__('File not found');
            $warn = array('_cssClasses' =>  $helper->getConst('WARN_TYPE_ERROR'));
            $lastModified = $helper->__('Not available');
        }

        return array($status, $warn, $lastModified);

    }

    /**
     * Fetches and returns output
     *
     * @return array
     */
    public function getOutput()
    {
        $sitemaps = Mage::getModel('sitemap/sitemap')->getCollection();
        $helper = Mage::helper('magemonitoring');
        $block = $this->newMultiBlock();
        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');

        $header = array(
            $helper->__('Filename'),
            $helper->__('Path'),
            $helper->__('Status'),
            $helper->__('Last modified'),
        );
        $renderer->setHeaderRow($header);

        if (count($sitemaps)) {
            foreach ($sitemaps as $sitemap) {
                $filename = $sitemap->getSitemapFilename();
                $path = $sitemap->getSitemapPath();
                $totalPath = Mage::getBaseDir() . $path . $filename;
                $fileInfo = $this->_getFileInfo($totalPath, $helper);
                $row = array ($filename, $totalPath, $fileInfo[0], $fileInfo[2]);
            }
        } else {
            $fileInfo = $this->_getFileInfo('none', $helper);
            $row = array ('Sitemap', '', $fileInfo[0], $fileInfo[2]);
        }

        $renderer->addRow($row, $fileInfo[1]);

        /**
         * Check for robots.txt as well. We do this here, because the
         * robots.txt info is the same as the sitemap-info - no filesize or bar-chart
         * needed. And its too little to do an own check for it.
         */

        $file = 'robots.txt';
        $path = Mage::getBaseDir() . '/' . $file;


        $fileInfo = $this->_getFileInfo($path, $helper);

        $renderer->addRow(array($file, $path, $fileInfo[0], $fileInfo[2]), $fileInfo[1]);

        $this->_output[] = $block;

        return $this->_output;
    }
}
