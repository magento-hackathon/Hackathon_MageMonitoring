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
 * Class Firegento_MageMonitoring_Model_Widget_Hosting
 * checks the availability of typical files of a standard
 * Magento installation.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_MageMonitoring_Model_Widget_Hosting
    extends Firegento_MageMonitoring_Model_Widget_Abstract
{
    const NODE_NAME = 'hosting';

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Hosting');
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
     * Check the accessibility of the specified file.
     *
     * @param  string $file               The path of the file to check
     * @param  bool   $shouldBeAccessible A flag indicating whether the file should be accessible or not
     * @return $this
     */
    protected function _checkFileAccessible($file, $shouldBeAccessible = false)
    {
        /** @var Firegento_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        /** @var Firegento_MageMonitoring_Helper_Http $httpHelper */
        $httpHelper = Mage::helper('magemonitoring/http');

        $response = $httpHelper->checkFile($file);
        $isAccessible = 200 == $response->getStatus();
        $result = $shouldBeAccessible ? $isAccessible : !$isAccessible;

        $this->getRenderer()->addRow(
            array(
                $helper->__('File accessible'),
                $file,
                $response->getStatus(),
                $shouldBeAccessible ? $helper->__('accessible') : $helper->__('not accessible'),
            ),
            $this->_getRowConfig($result)
        );
        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Render the accessibility status.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        parent::_renderMoreChecks();

        $this->_checkFileAccessible('.git/config');
        $this->_checkFileAccessible('.svn/entries');

        $this->_checkFileAccessible('.htaccess');
        $this->_checkFileAccessible('.htpasswd');

        $this->_checkFileAccessible('index.php.sample');
        $this->_checkFileAccessible('install.php');
        $this->_checkFileAccessible('php.ini.sample');

        $this->_checkFileAccessible('LICENSE.html');
        $this->_checkFileAccessible('LICENSE.txt');
        $this->_checkFileAccessible('LICENSE_AFL.txt');
        $this->_checkFileAccessible('RELEASE_NOTES.txt');

        $this->_checkFileAccessible('favicon.ico', true);

        $this->_checkTouchIconSize()
             ->_checkTouchIconSize(57)
             ->_checkTouchIconSize(72)
             ->_checkTouchIconSize(76)
             ->_checkTouchIconSize(114)
             ->_checkTouchIconSize(120)
             ->_checkTouchIconSize(144)
             ->_checkTouchIconSize(152);

        return $this;
    }

    /**
     * Check of the accessibility of apple touch icon sizes
     *
     * @param  integer $size The size of the apple touch icon (e.g. 57, 72, 76)
     *
     * @return $this
     */
    protected function _checkTouchIconSize($size = null)
    {
        if (is_null($size)) {
            $this->_checkFileAccessible('apple-touch-icon.png', true);
            $this->_checkFileAccessible('apple-touch-icon-precomposed.png', true);
        } else {
            $this->_checkFileAccessible('apple-touch-icon-' . $size . 'x' . $size . '.png', true);
            $this->_checkFileAccessible('apple-touch-icon-' . $size . 'x' . $size . '-precomposed.png', true);
        }

        return $this;
    }

}
