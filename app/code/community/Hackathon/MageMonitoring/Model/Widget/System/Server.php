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

class Hackathon_MageMonitoring_Model_Widget_System_Server extends Hackathon_MageMonitoring_Model_Widget_System_Abstract
                                                          implements Hackathon_MageMonitoring_Model_Widget
{
    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     */
    public function getName()
    {
        return 'System Information';
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
        $block = $this->newMonitoringBlock();
        $block->addRow('info', 'Host Name', $this->_getValue('HTTP_HOST'));
        $block->addRow('info', 'Server Software', $this->_getValue('SERVER_SOFTWARE'));
        $block->addRow('info', 'Server IP Address', $this->_getValue('SERVER_ADDR'));
        $block->addRow('info', 'Server Port', $this->_getValue('SERVER_PORT'));
        $block->addRow('info', 'Server Gateway Interface', $this->_getValue('GATEWAY_INTERFACE'));

        if (!is_null($memInfo = $this->getMemoryInfo())) {
            $block->addRow('info', 'Server Memory', $memInfo);
        }

        if (!is_null($cpuInfo = $this->getCpuInfo())) {
            $block->addRow('info', 'Server CPU', $cpuInfo);
        }

        $block->addRow('info', 'Server Admin', $this->_getValue('SERVER_ADMIN'));
        $block->addRow('info', 'Accept Encoding', $this->_getValue('HTTP_ACCEPT_ENCODING'));
        $block->addRow('info', 'OS Information', $this->getOsInfo());

        $this->_output[] = $block;
        return $this->_output;
    }

}
