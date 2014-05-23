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
class Hackathon_MageMonitoring_Model_Widget_AbstractGeneric
    extends Hackathon_MageMonitoring_Model_Widget_Abstract
{
    // define config keys
    const CONFIG_WIDGET_TITLE = 'title';

    // global default values
    protected $_DEF_WIDGET_TITLE = 'MageMonitoring Widget';

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::initConfig()
     */
    public function initConfig()
    {
        parent::initConfig();

        $configOrg = $this->getConfig();
        $configNew = array();
        $configNew[] = array_shift($configOrg);

        // "reset" config
        $this->_config = $configNew;

        // add title
        $this->addConfig(
                self::CONFIG_WIDGET_TITLE,
                'Widget Title:',
                $this->_DEF_WIDGET_TITLE,
                'widget',
                'text',
                true
        );

        // append old config
        $this->_config += $configOrg;

        return $this->_config;
    }

    /**
     * (non-PHPdoc)
     * @see Hackathon_MageMonitoring_Model_Widget::getName()
     * @return string
     */
    public function getName()
    {
        return $this->getConfig(self::CONFIG_WIDGET_TITLE);
    }

}
