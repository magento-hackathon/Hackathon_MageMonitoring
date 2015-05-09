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
 * Class Hackathon_MageMonitoring_Model_Widget_RootFiles
 * checks if the magento root contains problematic file types such as the "install.php".
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_RootFiles
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'rootfiles';

    /** @var array A list of file-exentions which are considered harmful in a Magento root */
    protected $_criticalFileEndings = array(
        '.sample',
        '.sql',
        '.pdf',
        '.exe',
        '.com',
        '.zip',
        '.gz',
        '.gzip',
        '.tgz',
        '.tar',
        '.ini',
        'install.php',
    );

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Root Files');
    }

    /**
     * Returns the name of the widgets xml node
     *
     * @return string
     */
    protected function _getNodeName()
    {
        return self::NODE_NAME;
    }

    /**
     * Displays the results of the root-file checks.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();
        $offendingFileNames = $this->getOffendingFilesFromMagentoRoot();

        $noOffendingFilesDetected = count($offendingFileNames) == 0;
        if ($noOffendingFilesDetected) {
            $this->getRenderer()->addRow(
                array(
                    $helper->__('Files in root directory'),
                    $helper->__('Checked files in root directory for critical file endings.'),
                    $helper->__('No problems found'),
                    $helper->__('---'),
                ),
                $this->_getRowConfig(true)
            );
            return $this;
        }

        foreach ($offendingFileNames as $_problem) {
            $this->getRenderer()->addRow(
                array(
                    $_problem,
                    $helper->__('Possibly critical file %s found in root directory.', $_problem),
                    $_problem,
                    $helper->__('Check if file should be there.'),
                ),
                $this->_getRowConfig(false)
            );
        }

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Get a list of all offending file names that can be found in the magento root.
     *
     * @return array
     */
    protected function getOffendingFilesFromMagentoRoot()
    {
        $endings = $this->_criticalFileEndings;
        array_walk($endings, 'preg_quote');
        $offendingFilenamePattern = '#(' . implode('|', $endings) . ')\z#i';
        $magentoRootIterator = new FilesystemIterator(Mage::getBaseDir(), FilesystemIterator::SKIP_DOTS);
        $problematicFileNames = array();
        foreach ($magentoRootIterator as $file) {
            $file = (string)$file;

            // skip directories
            if (!is_file($file)) {
                continue;
            }

            // check if the filename matches the pattern
            $isProblematicFileName = preg_match($offendingFilenamePattern, $file);
            if ($isProblematicFileName) {
                $problematicFileNames[] = basename($file);
            }
        }
        return $problematicFileNames;
    }

}
