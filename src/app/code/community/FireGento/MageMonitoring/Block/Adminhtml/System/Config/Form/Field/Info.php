<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_MageMonitoring
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * class FireGento_MageMonitoring_Block_Adminhtml_System_Config_Form_Field_Info
 *
 * @category FireGento
 * @package  FireGento_MageMonitoring
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_MageMonitoring_Block_Adminhtml_System_Config_Form_Field_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    const URL = 'http://www.firegento.com/';

    /**
     * Renders element
     *
     * @param  Varien_Data_Form_Element_Abstract $element Element to render
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $logoSrc = $this->getSkinUrl('monitoring/images/firegento.jpg');

        $html = '
<tr id="row_%s">
    <td colspan="2">
        <div class="box">
            <p>
                <a href="' . self::URL . '" target="_blank" title="' . $this->__('Go to FireGento Website') . '">
                    <img src="' . $logoSrc . '" alt="' . $this->__('FireGento') . '" />
                </a>
            </p>
            <p>%s</p>
            <ul>%s</ul>
        </div>
    </td>
</tr>
';

        $linksHtml = '';

        /** @var $links Mage_Core_Model_Config_Element */
        $links = $element->getFieldConfig()->links;
        if ($links) {
            foreach ($links->children() as $_link) {
                $_linkLabel = $this->__((string)$_link->label);
                $linksHtml .= sprintf('<li><a href="%s" target="_blank">%s</a>', $_link->url, $_linkLabel) . '</li>';
            }
        }

        return sprintf($html, $element->getHtmlId(), $element->getComment(), $linksHtml);
    }
}
