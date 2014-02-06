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

class Hackathon_MageMonitoring_Block_Widget extends Mage_Core_Block_Template
{
    private $_widgetModel;
    private $_output;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('monitoring/widget.phtml');
    }

    /**
     * @param Hackathon_MageMonitoring_Model_Widget $model
     */
    private function _getWidget() {
        if ($this->_widgetModel instanceof Hackathon_MageMonitoring_Model_Widget) {
            return $this->_widgetModel;
        } else {
            throw new Exception ('Use setWidget() before using any getter.');
        }
    }

    /**
     * Set source model.
     *
     * @param Hackathon_MageMonitoring_Model_Widget $model
     */
    public function setWidget($model) {
        if ($model instanceof Hackathon_MageMonitoring_Model_Widget) {
            $model->loadConfig();
            $this->_widgetModel = $model;
        } else {
            throw new Exception ('Passed model does not implement Hackathon_MageMonitoring_Model_Widget interface.');
        }
        return $this;
    }

    /**
     * Returns id string.
     *
     * @return string
     */
    public function getWidgetId() {
        return $this->_getWidget()->getId();
    }

    /**
     * Returns name of widget.
     *
     * @return string
     */
    public function getName() {
        return $this->_getWidget()->getName();
    }

    /**
     * Returns true if widget should render in collapsed state.
     *
     * @return bool
     */
    public function displayCollapsed() {
        return $this->_getWidget()->displayCollapsed();
    }

    /**
     * Returns config array.
     *
     * @return array
     */
    public function getConfig() {
        return $this->_getWidget()->getConfig();
    }

    /**
     * Returns output array for rendering.
     *
     * @return array
     */
    public function getOutput() {
        if (!$this->_output) {
            $this->_output = $this->_getWidget()->getOutput();
        }
        return $this->_output;
    }

    /**
     * Returns button array for rendering.
     *
     * @return array
     */
    public function getButtons() {
        return $this->_getWidget()->getButtons();
    }

}
