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
 * class Hackathon_MageMonitoring_Model_Widget_Abstract
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
abstract class Hackathon_MageMonitoring_Model_Widget_Abstract implements Hackathon_MageMonitoring_Model_Widget
{
    // define config keys
    const CONFIG_START_COLLAPSED = 'collapsed';
    const CONFIG_DISPLAY_PRIO = 'display_prio';

    // base node for all config keys
    const CONFIG_PRE_KEY = 'widgets';

    // callback marker
    const CALLBACK = 'cb:';

    // global default values
    protected $_defStartCollapsed = 0;
    protected $_defDisplayPrio = 10;

    protected $_dbConfigKey = null;
    protected $_tabId = null;
    protected $_output = array();
    protected $_config = array();
    protected $_report = array();

    /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract $_renderer */
    protected $_renderer;

    /**
     * Used to render the widget, returns array of classes that have a ->toHtml() method.
     * Extending from Hackathon_MageMonitoring_Model_Widget_Abstract will give you .
     *
     * @return array
     */
    public function getOutput()
    {
        $block = $this->newMultiBlock();

        /** @var Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Table $renderer */
        $renderer = $block->newContentRenderer('table');
        $this->setRenderer($renderer);

        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = Mage::helper('magemonitoring');

        $renderer->setHeaderRow(
            array(
                $helper->__('Test aspect'),
                $helper->__('Test Details'),
                $helper->__('Test result'),
                $helper->__('Recommendation')
            )
        );

        try {

            $this->_renderConfigChecks();
            $this->_renderMoreChecks();

        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_output;
    }

    /**
     * New Multi Block
     *
     * @return Hackathon_MageMonitoring_Block_Widget_Multi Block
     */
    public function newMultiBlock()
    {
        $block = Mage::app()->getLayout()->createBlock('magemonitoring/widget_multi');
        $block->setTabId($this->getTabId());
        $block->setWidgetId($this->getConfigId());
        return $block;
    }

    /**
     * Get Tab Id
     *
     * @return string
     */
    public function getTabId()
    {
        return $this->_tabId;
    }

    /**
     * Returns db config key, returns last 2 parts of classname with appended random string as default.
     *
     * @return string
     */
    public function getConfigId()
    {
        if (!$this->_dbConfigKey) {
            $regOut = array();
            if (preg_match("/.*_(.*_.*)/", $this->getId(), $regOut)) {
                $this->_dbConfigKey = strtolower($regOut[1] . '_' . substr(md5(rand()), 0, 6));
            }
        }
        return $this->_dbConfigKey;
    }

    /**
     * Returns unique widget id. You really don't want to override is. ;)
     *
     * @return string
     */
    public function getId()
    {
        return get_called_class();
    }

    /**
     * Render configuration checks defined in config.xml
     *
     * @return $this
     */
    protected function _renderConfigChecks()
    {

        foreach ($this->_getConfigValues() as $_config) {
            $title = (string)$_config->title;
            $path = (string)$_config->path;
            $recommendation = (string)$_config->recommendation;

            $this->_checkConfigValue($title, $path, $recommendation);
        }

        return $this;
    }

    /**
     * Return configuration params from config.xml
     *
     * @return array|SimpleXMLElement
     */
    protected function _getConfigValues()
    {
        $nodes = Mage::getConfig()->getNode('default/magemonitoring/tabs/monitoring_check/widgets/' . $this->_getNodeName() . '/shop_configuration/values');
        if ($nodes) {
            return $nodes->children();
        }
        return array();
    }

    /**
     * Returns the name of the widgets xml node
     *
     * @return string
     */
    abstract protected function _getNodeName();

    /**
     * Check a given configuration value concerning an optional recommendation
     *
     * @param  string $title The title of the configuration value.
     * @param  string $configPath The configuration value path.
     * @param  string $recommendation The recommended value for the given config value.
     *
     * @return $this
     */
    protected function _checkConfigValue($title, $configPath, $recommendation = null)
    {
        $renderer = $this->getRenderer();
        if (is_null($renderer)) {
            Mage::throwException('No Renderer set for Monitoring');
        }

        /** @var Hackathon_MageMonitoring_Helper_Data $helper */
        $helper = $this->_getHelper();

        $configValue = Mage::getStoreConfig($configPath);
        if (is_null($configValue)) {
            $configValue = '---';
        }

        if (is_null($recommendation)) {
            $recommendation = '---';
        }

        $this->getRenderer()->addRow(
            array($helper->__($title), $configPath, $configValue, $recommendation),
            $this->_getRowConfig($configValue == $recommendation)
        );

        $this->_output[] = $this->getRenderer();

        return $this;
    }

    /**
     * Get the renderer.
     *
     * @return Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    /**
     * Set the renderer.
     *
     * @param Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract $renderer A renderer
     */
    public function setRenderer(Hackathon_MageMonitoring_Block_Widget_Multi_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * Get an instance of the module's data helper.
     *
     * @return Hackathon_MageMonitoring_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('magemonitoring');
    }

    /**
     * Get a row configuration.
     *
     * @param  bool $conditionMet A flag indicating whether the condition was met or not.
     *
     * @return array
     */
    protected function _getRowConfig($conditionMet)
    {
        if ($conditionMet) {
            $rowConfig = array('_cssClasses' => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_OK);
        } else {
            $rowConfig = array('_cssClasses' => Hackathon_MageMonitoring_Helper_Data::WARN_TYPE_WARNING);
        }
        return $rowConfig;
    }

    /**
     * Render additional checks.
     *
     * @return $this
     */
    protected function _renderMoreChecks()
    {
        return $this;
    }

    /**
     * Returns true if this widget is active.
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Returns true if widget should start collapsed, speeds up loading times as
     * the widget won't render it's content on page load.
     *
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function displayCollapsed()
    {
        return $this->getConfig(self::CONFIG_START_COLLAPSED);
    }

    /**
     * Get Config
     *
     * @param  null $configKey Config Key
     * @param  bool $valueOnly Value Only
     * @return array|bool
     * @see Hackathon_MageMonitoring_Model_Widget::getConfig()
     */
    public function getConfig($configKey = null, $valueOnly = true)
    {
        if (empty($this->_config)) {
            $this->_config = $this->initConfig();
        }
        if ($configKey && array_key_exists($configKey, $this->_config)) {
            if ($valueOnly) {
                return $this->_config[$configKey]['value'];
            } else {
                return $this->_config[$configKey];
            }
        } else {
            if ($configKey) {
                return false;
            }
        }

        return $this->_config;
    }

    /**
     * Returns array with default config data for this widget or false if not implemented.
     *
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        $this->addConfigHeader('Widget Configuration');

        $this->addConfig(
            self::CONFIG_START_COLLAPSED,
            'Do not render widget on pageload?',
            $this->_defStartCollapsed,
            'widget',
            'checkbox',
            false
        );

        $this->addConfig(
            self::CONFIG_DISPLAY_PRIO,
            'Display priority (0=top):',
            $this->_defDisplayPrio,
            'widget',
            'text',
            false
        );

        return $this->_config;
    }

    /**
     * Add empty or header row to config modal output.
     *
     * @param  string $header Header
     * @return Hackathon_MageMonitoring_Model_Widget
     */
    public function addConfigHeader($header = null)
    {
        $this->_config[] = array('label' => $header);
        return $this;
    }

    /**
     * Add Configuration
     *
     * @param  string      $configKey Config Key
     * @param  string      $label     Label
     * @param  string|int  $value     Value
     * @param  string      $scope     Scope
     * @param  string      $inputType Input Type
     * @param  bool        $required  Is Required
     * @param  null|string $tooltip   Tooltip
     * @return $this
     * @see Hackathon_MageMonitoring_Model_Widget::addConfig()
     */
    public function addConfig(
        $configKey,
        $label,
        $value,
        $scope = 'global',
        $inputType = 'text',
        $required = false,
        $tooltip = null
    )
    {
        $this->_config[$configKey] = array(
            'scope' => $scope,
            'label' => $label,
            'value' => $value,
            'type' => $inputType,
            'required' => $required,
            'tooltip' => $tooltip
        );

        return $this;
    }

    /**
     * Returns true if widget should start collapsed, speeds up loading times as
     * the widget won't render it's content on page load.
     *
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function getDisplayPrio()
    {
        return $this->getConfig(self::CONFIG_DISPLAY_PRIO);
    }

    /**
     * New Monitoring Block
     *
     * @return Hackathon_MageMonitoring_Block_Widget_Monitoring Block
     */
    public function newMonitoringBlock()
    {
        $block = Mage::app()->getLayout()->createBlock('magemonitoring/widget_monitoring');
        $block->setTabId($this->getTabId());
        $block->setWidgetId($this->getConfigId());
        return $block;
    }

    /**
     * Adds $string to output.
     *
     * @param  string $string String
     * @return Hackathon_MageMonitoring_Model_Widget_Abstract
     */
    public function dump($string)
    {
        $this->_output[] = Mage::app()->getLayout()->createBlock('magemonitoring/widget_dump')->setOutput($string);
        return $this;
    }

    /**
     * Load Config
     *
     * @param  null|string|int $configKey Config Key
     * @param  null|string|int $tabId Tab Id
     * @param  null|string|int $widgetDbId Widget DB Id
     * @return array
     * @see Hackathon_MageMonitoring_Model_Widget::loadConfig()
     */
    public function loadConfig($configKey = null, $tabId = null, $widgetDbId = null)
    {
        $config = array();
        $this->_tabId = $tabId;
        if ($widgetDbId !== null) {
            $this->_dbConfigKey = $widgetDbId;
        }
        if ($configKey) {
            $config[$configKey] = array('value' => null);
        } else {
            $config = $this->getConfig();
        }

        foreach ($config as $key => $conf) {
            $ck = $this->getHelper()->getConfigKey($key, $this);
            $value = Mage::getStoreConfig($ck);
            if ($value != null) {
                $this->_config[$key]['value'] = $value;
            }
        }
        return $this->_config;
    }

    /**
     * Get Helper
     *
     * @return Hackathon_MageMonitoring_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('magemonitoring');
    }

    /**
     * Save config in $post to core_config_data, can handle raw $_POST
     * or widget config arrays if $postOnly is true.
     *
     * @param  array $post Post
     * @param  bool $postOnly Post Only
     * @return $this
     * @see Hackathon_MageMonitoring_Model_Widget::saveConfig()
     */
    public function saveConfig($post, $postOnly = false)
    {
        $config = null;
        if (array_key_exists('widget_id', $post)) {
            $this->_dbConfigKey = $post['widget_id'];
        }
        if ($postOnly) {
            $config = $post;
        } else {
            $c = Mage::getModel('core/config');
            if (array_key_exists('class_name', $post)) {
                $c->saveConfig(
                    $this->getHelper()->getConfigKeyById('impl', $this->_dbConfigKey, 'tabs/' . $this->getTabId()),
                    $post['class_name'],
                    'default',
                    0
                );
            }
            $config = $this->getConfig();
        }
        foreach ($config as $key => $conf) {
            if (is_numeric($key)) { // skip header entries
                continue;
            }
            // handle checkbox states
            if (array_key_exists('type', $conf) && $conf['type'] == 'checkbox') {
                if (!array_key_exists($key, $post)) {
                    $post[$key] = 0;
                } else {
                    $post[$key] = 1;
                }
            }
            $value = null;
            if (array_key_exists($key, $post)) {
                if (!$postOnly) {
                    $value = $post[$key];
                } else {
                    $value = $post[$key]['value'];
                }
            }
            //@todo: batch save
            $c = Mage::getModel('core/config');
            $c->saveConfig(
                $this->getHelper()->getConfigKey($key, $this),
                $value,
                'default',
                0
            );
        }

        return $this;
    }

    /**
     * Delete Config
     *
     * @param  string|null $tabId Tab Id
     * @return $this
     * @see Hackathon_MageMonitoring_Model_Widget::deleteConfig()
     */
    public function deleteConfig($tabId = null)
    {
        $this->_tabId = $tabId;
        foreach ($this->getConfig() as $key => $conf) {
            $c = Mage::getModel('core/config');
            $c->deleteConfig(
                $this->getHelper()->getConfigKey($key, $this),
                'default',
                0
            );
        }

        return $this;
    }

    /**
     * Adds another row to watch dog report output.
     *
     * Format of $attachments array:
     * array(array('filename' => $name, 'content' => $content), ...)
     *
     * @param  string $cssId Css Id
     * @param  string $label Label
     * @param  string $value Value
     * @param  array|null $attachments Attachments
     * @return $this
     */
    public function addReportRow($cssId, $label, $value, $attachments = null)
    {
        $this->_report[] = array(
            'css_id' => $cssId,
            'label' => $label,
            'value' => $value,
            'attachments' => $attachments
        );
        return $this;
    }

    /**
     * Get Version
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.0.1';
    }

    /**
     * Check Versions
     *
     * @return bool
     */
    protected function _checkVersions()
    {
        if ($this->getSupportedMagentoVersions() === '*') {
            return true;
        }
        // @todo: do proper merge, things will go probably south for code below.
        $mageVersion = Mage::getVersion();

        // retrieve supported versions from config.xml
        $versions = $this->getHelper()->extractVersions($this->getSupportedMagentoVersions());

        // iterate on versions to find a fitting one
        foreach ($versions as $_version) {
            $quotedVersion = preg_quote($_version);
            // build regular expression with wildcard to check magento version
            $pregExpr = '#\A' . str_replace('\*', '.*', $quotedVersion) . '\z#ims';

            if (preg_match($pregExpr, $mageVersion)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Supported Magento Versions
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getSupportedMagentoVersions()
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return '*';
    }
}
