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
 * Class Hackathon_MageMonitoring_Model_Widget_Extensions_Community
 * renders the community extension checks.
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class Hackathon_MageMonitoring_Model_Widget_Extensions_Community
    extends Hackathon_MageMonitoring_Model_Widget_Extensions_Abstract
{
    const NODE_NAME = 'default_extensions_community';

    /** @var array A list of recommended community extensions */
    protected $_extensions = array(
        'Aoe_ApiLog',
        'Aoe_CacheCleaner',
        'Aoe_QuoteCleaner',
        'Aoe_Scheduler',
        'Aoe_TemplateHints',
        'AvS_AdminNotificationAdvanced',
        'AvS_MissingTranslations',
        'AvS_ScopeHint',
        'FireGento_MageSetup',
        'German_LocalePackDe',
        'Hackathon_IndexerStats',
        'Hackathon_MageMonitoring',
        'Netzarbeiter_NicerImageNames',
        'Treynolds_Qconfig',
    );

    /**
     * Returns widget name.
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magemonitoring')->__('Community Extensions');
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
     * Render the extension-check results.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        return $this->_checkExtensions($this->_extensions);
    }

}
