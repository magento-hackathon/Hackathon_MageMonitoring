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

    // watch dog config keys, only added if widget implements watchdog interface
    const CONFIG_WATCHDOG_ACTIVE = 'cron/enabled';
    const CONFIG_WATCHDOG_BARKON = 'cron/barkon';
    const CONFIG_WATCHDOG_CRON = 'cron/schedule';
    const CONFIG_WATCHDOG_MAILTO = 'cron/mail_to';

    // global watch dog config keys
    const CONFIG_DOGS_DISABLED = 'dogs/disabled';
    const CONFIG_DOGS_MAILTO = 'dogs/mail_to';

    // global default values
    protected $_DEF_START_COLLAPSED = false;
    protected $_DEF_DISPLAY_PRIO = 10;

    // watch dog defaults
    protected $_DEF_WATCHDOG_ACTIVE = true;
    protected $_DEF_WATCHDOG_BARKON = 'warning';
    protected $_DEF_WATCHDOG_CRON = '*/5 * * * *';
    protected $_DEF_WATCHDOG_MAILTO = null;

    // global watch dog defaults
    protected $_DEF_DOGS_DISABLED = 1;
    protected $_DEF_DOGS_MAILTO = 'general';

    // base node for all config keys
    const CONFIG_PRE_KEY = 'widgets/';

    // callback marker
    const CALLBACK = 'cb:';

    protected $_output = array();
    protected $_buttons = array();
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
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::isActive()
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Add empty or header row to output.
     *
     * @param string $label
     * @param string $background_id
     */
    public function addHeaderRow($header = null, $background_id = 'info') {
        $this->_output[] = array(
                'css_id' => $background_id,
                'label' => null,
                'value' => $header
        );
        return $this;
    }

    /**
     * Adds a row to output array.
     *
     * @param string $css_id
     * @param string $label
     * @param string $value
     * @param string $chart
     * @return $this
     */
    public function addRow($css_id, $label, $value = null, $chart = null)
    {
        $this->_output[] = array(
            'css_id' => $css_id,
            'label' => $label,
            'value' => $value,
            'chart' => $chart
        );

        return $this;
    }

    /**
     * Adds a button to button array.
     *
     * @param string $button_id
     * @param string $label
     * @param string $controller_action or self::CALLBACK.$callbackMethod
     * @param array $url_params
     * @param string $confirm_message
     * @param string $css_class
     * @return $this
     */
    public function addButton(
        $button_id,
        $label,
        $controller_action,
        $url_params = null,
        $confirm_message = null,
        $css_class = 'f-right'
    ) {
        $b = Mage::app()->getLayout()->createBlock('adminhtml/widget_button');
        $b->setId($button_id);
        $b->setLabel($label);
        $b->setOnClick($this->getOnClick($controller_action, $url_params, $confirm_message));
        $b->setClass($css_class);
        $b->setType('button');

        $this->_buttons[] = $b;

        return $this;
    }

    /**
     * Get onClick data for button display.
     *
     * @param string $controller_action
     * @param string $url_params
     * @param string $confirm_message
     *
     * @return string
     */
    protected function getOnClick($controller_action, $url_params = null, $confirm_message = null)
    {
        $onClick = '';
        // check if this is an ajax call with callback
        if (!strncmp($controller_action, self::CALLBACK, strlen(self::CALLBACK))) {
            $callback = substr($controller_action, strlen(self::CALLBACK));
            $widgetId = $this->getId();
            $widgetName = $this->getName();
            $callbackUrl = Mage::helper('magemonitoring')->getWidgetUrl('*/widgetAjax/execCallback', $this->getId());
            $refreshUrl = 'null';
            // check if refresh flag is set
            if (isset($url_params['refreshAfter']) && $url_params['refreshAfter']) {
                $refreshUrl = '\'' . Mage::helper('magemonitoring')->getWidgetUrl(
                        '*/widgetAjax/refreshWidget',
                        $this->getId()
                    ) . '\'';
            }
            // add callback js
            $onClick .= "execWidgetCallback('$widgetId', '$widgetName', '$callback', '$callbackUrl', $refreshUrl);";
            // add confirm dialog?
            if ($confirm_message) {
                $onClick = "var r=confirm('$confirm_message'); if (r==true) {" . $onClick . "}";
            }

            return $onClick;
        }
        $url = Mage::getSingleton('adminhtml/url')->getUrl($controller_action, $url_params);
        if ($confirm_message) {
            $onClick = "confirmSetLocation('$confirm_message','$url')";
        } else {
            $onClick = "setLocation('$url')";
        }

        return $onClick;
    }

    /**
     * Returns output array.
     *
     * @return array|false
     */
    public function getButtons()
    {
        if (empty($this->_buttons)) {
            return false;
        }

        return $this->_buttons;
    }

    /**
     * Returns an array that can feed Hackathon_MageMonitoring_Block_Chart.
     *
     * @param string $canvasId
     * @param array $chartData
     * @param string $chartType
     * @param int $width
     * @param int $height
     *
     * @return array
     */
    public function createChartArray($canvasId, $chartData, $chartType = 'Pie', $width = 76, $height = 76)
    {
        return array(
            'chart_id' => $canvasId,
            'chart_type' => $chartType,
            'canvas_width' => $width,
            'canvas_height' => $height,
            'chart_data' => $chartData
        );
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function displayCollapsed()
    {
        return $this->getConfig(self::CONFIG_START_COLLAPSED);
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::displayCollapsed()
     */
    public function getDisplayPrio()
    {
        return $this->getConfig(self::CONFIG_DISPLAY_PRIO);
    }


    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        $this->addConfigHeader('Widget Configuration');

        $this->addConfig(
            self::CONFIG_START_COLLAPSED,
            'Do not render widget on pageload?',
            $this->_DEF_START_COLLAPSED,
            'checkbox',
            false
        );

        $this->addConfig(
            self::CONFIG_DISPLAY_PRIO,
            'Display priority (0=top):',
            $this->_DEF_DISPLAY_PRIO,
            'text',
            false
        );

        if ($this instanceof Hackathon_MageMonitoring_Model_WatchDog) {
            $id = 'Hackathon_MageMonitoring_Model_Widget_System_Watchdog';
            $confKey = Mage::helper('magemonitoring')->getConfigKey(self::CONFIG_DOGS_MAILTO, self::CONFIG_PRE_KEY, $id);
            $defMail = Mage::getStoreConfig($confKey);
            if (!$defMail) {
                $defMail = $this->_DEF_DOGS_MAILTO;
            }
            $this->_DEF_WATCHDOG_MAILTO = $defMail;

            $this->addConfigHeader('Watch Dog Settings');
            $this->addConfig(
                    self::CONFIG_WATCHDOG_ACTIVE,
                    'Dog is on duty:',
                    $this->_DEF_WATCHDOG_ACTIVE,
                    'checkbox',
                    false
            );
            $this->addConfig(
                    self::CONFIG_WATCHDOG_CRON,
                    'Schedule:',
                    $this->_DEF_WATCHDOG_CRON,
                    'text',
                    false
            );
            $this->addConfig(
                    self::CONFIG_WATCHDOG_BARKON,
                    'Minimum bark level (warning|error):',
                    $this->_DEF_WATCHDOG_BARKON,
                    'text',
                    false
            );
            $this->addConfig(
                    self::CONFIG_WATCHDOG_MAILTO,
                    'Barks at:',
                    $this->_DEF_WATCHDOG_MAILTO,
                    'text',
                    false
            );
        }

        return $this->_config;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getConfig()
     */
    public function getConfig($config_key = null, $valueOnly = true)
    {
        if (empty($this->_config)) {
            $this->_config = $this->initConfig();
        }
        if ($config_key && array_key_exists($config_key, $this->_config)) {
            if ($valueOnly) {
                return $this->_config[$config_key]['value'];
            } else {
                return $this->_config[$config_key];
            }
        } else {
            if ($config_key) {
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
    public function addConfigHeader($header=null) {
        $this->_config[] = array('label' => $header);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::addConfig()
     */
    public function addConfig(
        $config_key,
        $label,
        $value,
        $inputType = "text",
        $required = false,
        $tooltip = null
    ) {
        $this->_config[$config_key] = array(
            'label' => $label,
            'value' => $value,
            'type' => $inputType,
            'required' => $required,
            'tooltip' => $tooltip
        );

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::loadConfig()
     */
    public function loadConfig($configKey = null)
    {
        $config = array();
        if ($configKey) {
            $config[$configKey] = array('value' => null);
        } else {
            $config = $this->getConfig();
        }

        foreach ($config as $key => $conf) {
            $ck = Mage::helper('magemonitoring')->getConfigKey($key, self::CONFIG_PRE_KEY, $this->getId());
            $value = Mage::getStoreConfig($ck);
            if ($value != null)
            {
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
     * @see Hackathon_MageMonitoring_Model_Widget::saveConfig()
     */
    public function saveConfig($post, $postOnly = false)
    {
        $config = null;
        if ($postOnly) {
            $config = $post;
        } else {
            $config = $this->getConfig();
        }
        foreach ($config as $key => $conf) {
            if (is_numeric($key)) continue; // skip header entries
            // handle checkbox states
            if (array_key_exists('type', $conf) && $conf['type'] == 'checkbox') {
                if (!array_key_exists($key, $post)) {
                    $post[$key] = 0;
                } else {
                    $post[$key] = 1;
                }
            }
            $c = Mage::getModel('core/config');
            $value = null;
            if (!$postOnly && array_key_exists($key, $post)) {
                $value = $post[$key];
            } else {
                $value = $post[$key]['value'];
            }
            # @todo: batch save
            $c->saveConfig(
                Mage::helper('magemonitoring')->getConfigKey($key, self::CONFIG_PRE_KEY, $this->getId()),
                $value,
                'default',
                0
            );
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::deleteConfig()
     */
    public function deleteConfig()
    {
        foreach ($this->getConfig() as $key => $conf) {
            $c = Mage::getModel('core/config');
            $c->deleteConfig(
                Mage::helper('magemonitoring')->getConfigKey($key, self::CONFIG_PRE_KEY, $this->getId()),
                'default',
                0
            );
        }

        return $this;
    }

    /**
     * @see Hackathon_MageMonitoring_Model_WatchDog::getSchedule()
     * @return string
     */
    public function getSchedule()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_CRON, true);
    }

    /**
     * @see Hackathon_MageMonitoring_Model_WatchDog::onDuty()
     * @return string
     */
    public function onDuty()
    {
        return $this->getConfig(self::CONFIG_WATCHDOG_ACTIVE, true);
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
     * @return void
     */
    public function addReportRow($css_id, $label, $value, $attachments=null) {
        $this->_report[] = array(
                'css_id' => $css_id,
                'label' => $label,
                'value' => $value,
                'attachments' => $attachments
        );
        return $this;
    }

}
