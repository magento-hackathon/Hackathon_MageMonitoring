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

class Hackathon_MageMonitoring_Block_System_Overview_Read_Tabs_Php extends Mage_Adminhtml_Block_Abstract
{
    protected $_template = 'monitoring/php.phtml';

    protected $_extensions = array(
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

    protected $_config = array(
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
     *
     * @return Hackathon_MageMonitoring_Helper_Data|Mage_Core_Block_Abstract
     */
    public function getMonitoringHelper()
    {
        return Mage::helper('magemonitoring');
    }

    /**
     * @return array
     */
    public function getPhpVersion ()
    {
        // 5.2.13 - 5.3.24, 5.4.x wtih Patch http://www.magentocommerce.com/download
        if (version_compare(phpversion(), '5.2.13', 'lt') || version_compare(phpversion(), '5.5', 'ge')) {
            $class = 'warning';
            $recommended = true;
        } else {
            $class = 'success';
            $recommended = false;
        }

        $version = array(
            'label' => $this->__('Version'),
            'installed' => phpversion(),
            'recommended' => ($recommended) ? $this->__('5.2.13 - 5.3.24, 5.4.x wtih <a href="%s">Patch</a>', 'http://www.magentocommerce.com/download') : false,
            'class' => $class
        );

        return $version;
    }

    /**
     * @return array
     */
    public function getPhpConfigCheck()
    {
        $check = array();
        foreach ($this->_config as $key => $config) {

            if (in_array($key, array('memory_limit', 'post_max_size', 'upload_max_filesize'))) {
                $class = ((int) $this->getMonitoringHelper()->getValueInByte(ini_get($key), true) < (int) $config) ? 'warning' : 'success';
                $value = $this->getMonitoringHelper()->getValueInByte(ini_get($key), true) . 'MB';
                $recommended = ($config && $class == 'warning') ? $config . 'MB' : false;
            } else {
                $value = ini_get($key);
                $class = (empty($config) || (!empty($config) && (bool) $config == (bool) $value) ) ? 'success' : 'warning';
                $recommended = ($config && $class == 'warning') ? $config : false;
            }

            $check[] = array(
                'label' => $key,
                'installed' => $value,
                'recommended' => $recommended,
                'class' => $class,
            );
        }

        return $check;
    }

    /**
     * @return array
     */
    public function getExtensionsCheck()
    {
        $check = array();
        foreach ($this->_extensions as $extension) {
            $check[] = array(
                'label' => $extension,
                'installed' => (extension_loaded($extension)) ? $this->__('enabled') : $this->__('disabled'),
                'recommended' => false,
                'class' => (extension_loaded($extension)) ? 'success' : 'error'
            );
        }

        return $check;
    }

    /**
     * @return array
     */
    public function getAllExtensions()
    {
        $loadedExtensions = get_loaded_extensions();
        $extensions = array();
        foreach ($loadedExtensions as $extension) {
            if (in_array($extension, $this->_extensions)) {
                continue;
            }
            $extensions[] = array(
                'label' => $extension,
                'installed' => (phpversion($extension)) ? phpversion($extension) : $this->__('enabled'),
                'recommended' => false,
                'class' => 'info'
            );
        }

        return $extensions;
    }

    /**
     * @return mixed
     */
    public function getPhpInfo()
    {
        return $this->getMonitoringHelper()->getPhpInfoArray();
    }

}