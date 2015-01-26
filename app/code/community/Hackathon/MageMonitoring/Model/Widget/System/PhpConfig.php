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

class Hackathon_MageMonitoring_Model_Widget_System_PhpConfig extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
                                                             implements Hackathon_MageMonitoring_Model_Widget
{
    protected $_config_check = array(
            'safe_mode' => '0',
            'memory_limit' => 512, // in MB
            'post_max_size' => 100, // in MB
            'upload_max_filesize' => 100, // in MB
            'file_uploads' => '1',
            'sendmail_from' => '',
            'sendmail_path' => '',
            'smtp_port' => '',
            'SMTP' => '',
            'soap.wsdl_cache' => '',
            //'soap.wsdl_cache_dir' => '',
            'soap.wsdl_cache_enabled' => '',
            'soap.wsdl_cache_ttl' => '',
    );

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'PHP Config Check';
    }

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
     * @see Hackathon_MageMonitoring_Model_Widget::getOutput()
     */
    public function getOutput()
    {
        $helper = Mage::helper('magemonitoring');
        $block = $this->newMonitoringBlock();

        $recTxt = '';
        $class = 'success';
        if (version_compare(phpversion(), '5.2.13', 'lt') || version_compare(phpversion(), '5.5', 'ge')) {
            $recTxt = ' - ' . $helper->__('Should be 5.2.13 - 5.3.24, 5.4.x needs official Magento Patch.');
            $class = 'warning';
        }
        $block->addRow($class, 'Version', phpversion() . $recTxt);

        foreach ($this->_config_check as $key => $config) {
            if (in_array($key, array('memory_limit', 'post_max_size', 'upload_max_filesize'))) {
                $class = ((int) $helper->getValueInByte(ini_get($key), true) < (int) $config) ? 'warning' : 'success';
                $value = $helper->getValueInByte(ini_get($key), true) . 'MB';
                $recommended = ($config && $class == 'warning') ? $config . 'MB' : false;
            } else {
                $value = ini_get($key);
                $class = (empty($config) || (!empty($config) && (bool) $config == (bool) $value) ) ? 'success' : 'warning';
                $recommended = ($config && $class == 'warning') ? $config : false;
            }
            if ($recommended) {
                $value .= ' - ' . $helper->__('Recommended') . ': ' . $recommended;
            }
            $block->addRow($class, $key, $value);
        }

        $this->_output[] = $block;
        return $this->_output;
    }

}
