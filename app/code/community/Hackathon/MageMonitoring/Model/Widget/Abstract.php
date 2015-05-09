<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Hackathon
 * @package     Hackathon_MageMonitoring
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Hackathon_MageMonitoring_Model_Widget_Abstract
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
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function displayCollapsed()
    {
        return $this->getConfig(self::CONFIG_START_COLLAPSED);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function getDisplayPrio()
    {
        return $this->getConfig(self::CONFIG_DISPLAY_PRIO);
    }

    /**
     * @return Hackathon_MageMonitoring_Block_Widget_Monitoring
     */
    public function newMonitoringBlock()
    {
        $b = Mage::app()->getLayout()->createBlock('magemonitoring/widget_monitoring');
        $b->setTabId($this->getTabId());
        $b->setWidgetId($this->getConfigId());
        return $b;
    }

    /**
     * @return Hackathon_MageMonitoring_Block_Widget_Multi
     */
    public function newMultiBlock()
    {
        $b = Mage::app()->getLayout()->createBlock('magemonitoring/widget_multi');
        $b->setTabId($this->getTabId());
        $b->setWidgetId($this->getConfigId());
        return $b;
    }

    /**
     * Adds $string to output.
     *
     * @param string $string
     * @return Hackathon_MageMonitoring_Model_Widget_Abstract
     */
    public function dump($string)
    {
        $this->_output[] = Mage::app()->getLayout()->createBlock('magemonitoring/widget_dump')->setOutput($string);
        return $this;
    }

    /**
     * (non-PHPdoc)
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
     *
     * @see Hackathon_MageMonitoring_Model_Widget::getConfig()
     *
     * @param null $configKey
     * @param bool $valueOnly
     * @return array|bool
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
     * Add empty or header row to config modal output.
     *
     * @param string $header
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
     * @see Hackathon_MageMonitoring_Model_Widget::addConfig()
     *
     * @param $config_key
     * @param $label
     * @param $value
     * @param string $scope
     * @param string $inputType
     * @param bool $required
     * @param null $tooltip
     * @return $this
     */
    public function addConfig(
        $config_key,
        $label,
        $value,
        $scope = 'global',
        $inputType = 'text',
        $required = false,
        $tooltip = null
    )
    {
        $this->_config[$config_key] = array(
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
     * @see Hackathon_MageMonitoring_Model_Widget::loadConfig()
     *
     * @param null $configKey
     * @param null $tabId
     * @param null $widgetDbId
     * @return array
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
     * Save config in $post to core_config_data, can handle raw $_POST
     * or widget config arrays if $postOnly is true.
     *
     * (non-PHPdoc)
     *
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
     * (non-PHPdoc)
     *
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
     * @param string $css_id
     * @param string $label
     * @param string $value
     * @param array $attachments
     * @return $this
     */
    public function addReportRow($css_id, $label, $value, $attachments = null)
    {
        $this->_report[] = array(
            'css_id' => $css_id,
            'label' => $label,
            'value' => $value,
            'attachments' => $attachments
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getTabId()
    {
        return $this->_tabId;
    }

    public function getVersion()
    {
        return '0.0.1';
    }

    /**
     * @see Hackathon_MageMonitoring_Model_Widget::getSupportedMagentoVersions()
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return '*';
    }

    /**
     * @return bool
     */
    protected function _checkVersions()
    {
        if ($this->getSupportedMagentoVersions() === '*') {
            return true;
        }
        #TODO: do proper merge, things will go probably south for code below.
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

    public function getHelper()
    {
        return Mage::helper('magemonitoring');
    }

}
