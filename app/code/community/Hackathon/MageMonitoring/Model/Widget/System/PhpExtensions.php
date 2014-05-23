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

class Hackathon_MageMonitoring_Model_Widget_System_PhpExtensions extends Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
                                                                 implements Hackathon_MageMonitoring_Model_Widget
{
    const CONFIG_ONLY_REQUIRED = 'only_required';
    // set/override defaults
    protected $_DEF_WIDGET_TITLE = 'PHP Extensions';
    protected $_DEF_ONLY_REQUIRED = false;
    protected $_DEF_DISPLAY_PRIO = 20;

    protected $_req_extensions = array(
            'curl',
            'dom',
            'gd',
            'hash',
            'iconv',
            'mcrypt',
            'pcre',
            'pdo',
            'pdo_mysql',
            'simplexml',
            'soap'
    );

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getVersion()
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for required extensions filter
        $this->addConfig(self::CONFIG_ONLY_REQUIRED,
                'Only show required extensions:',
                $this->_DEF_ONLY_REQUIRED,
                'widget',
                'checkbox',
                false,
                'Only show extensions required by Magento.');
        return $this->_config;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $helper = Mage::helper('magemonitoring');
        $block = $this->newMonitoringBlock();

        $loadedExtensions = get_loaded_extensions();
        foreach ($loadedExtensions as $extension) {
            if ($this->getConfig(self::CONFIG_ONLY_REQUIRED) && !in_array($extension, $this->_req_extensions)) {
                continue;
            }
            $class = 'info';
            if (in_array($extension, $this->_req_extensions)) {
                $class = (extension_loaded($extension)) ? 'success' : 'error';
            }
            $block->addRow($class, $extension, (phpversion($extension)) ? phpversion($extension) : $helper->__('enabled'));
        }

        $this->_output[] = $block;
        return $this->_output;
    }

}
