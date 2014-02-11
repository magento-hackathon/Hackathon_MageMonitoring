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
class Hackathon_MageMonitoring_Model_Widget_Log_Abstract extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    // define config keys
    const CONFIG_LOG_LINES = 'linecount';
    // define global defaults
    protected $_DEF_LOG_LINES = 30;

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();
        // add config for tail -n param
        $this->addConfig(self::CONFIG_LOG_LINES, 'Number of lines to tail:',
                $this->_DEF_LOG_LINES, 'text', false);
        return $this->_config;
    }

    /**
     * Adds a tail -n row to widget output.
     *
     * @param string $errorLevel
     * @param string $fileName
     */
    protected function addLogRow($errorLevel, $fileName)
    {
        $log = Mage::helper('magemonitoring')->tailFile('var/log/'.$fileName,
                $this->getConfig(self::CONFIG_LOG_LINES));
        if (empty($log)) {
            $log = 'Log is empty. ^^';
            $errorLevel = 'success';
        }
        $this->addRow($errorLevel, null, nl2br($log));
    }

}
