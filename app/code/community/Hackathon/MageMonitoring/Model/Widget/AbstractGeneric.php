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
 * class Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    // define config keys
    const CONFIG_WIDGET_TITLE = 'title';

    // global default values
    protected $_defWidgetTitle = 'MageMonitoring Widget';

    /**
     * Init Config
     *
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();

        $configOrg = $this->getConfig();
        $configNew = array();
        $configNew[] = reset($configOrg);

        // "reset" config
        $this->_config = $configNew;

        // add title config
        $this->addConfig(
                self::CONFIG_WIDGET_TITLE,
                'Widget Title:',
                $this->_defWidgetTitle,
                'widget',
                'text',
                true
        );

        // append old config
        $this->_config += $configOrg;
        return $this->_config;
    }

    /**
     * Get Name
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     * @return string
     */
    public function getName()
    {
        return $this->getConfig(self::CONFIG_WIDGET_TITLE);
    }
}
