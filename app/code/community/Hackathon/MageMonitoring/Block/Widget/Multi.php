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

class Hackathon_MageMonitoring_Block_Widget_Multi extends Mage_Core_Block_Template
{
    protected $_renderer = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('monitoring/widget/multi.phtml');
    }

    /**
     * Initializes and returns a content renderer block of specified type.
     *
     * @param $type
     * @return Hackathon_MageMonitoring_Block_Widget_Multi_Renderer
     */
    public function newContentRenderer($type='table')
    {
        $blockString = 'magemonitoring/widget_multi_renderer_' . $type;
        $renderer = $this->getLayout()->createBlock($blockString);
        if ($renderer instanceof Hackathon_MageMonitoring_Block_Widget_Multi_Renderer) {
            $renderer->setWidgetId($this->getWidgetId())
                ->setTabId($this->getTabId())
                ->setType($type);
            $this->_renderer = $renderer;
            return $renderer;
        } else {
            Mage::throwException('Renderer not found: ' . $type);
        }
    }

    /**
     * Returns current content renderer.
     *
     * @return Hackathon_MageMonitoring_Block_Widget_Multi_Renderer
     */
    public function getRenderer()
    {
        if ($this->_renderer) {
            Mage::throwException('Error: Undefined renderer.');
        }
        return $this->_renderer;
    }

}
