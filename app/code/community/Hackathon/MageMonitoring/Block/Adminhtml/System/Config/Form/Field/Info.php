<?php
 
class Hackathon_MageMonitoring_Block_Adminhtml_System_Config_Form_Field_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    const URL = 'http://www.magemonitoring.com/';

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $imagesPath = str_replace('_', '/', mb_strtolower($this->getModuleName()));
        $logoSrc = $this->getSkinUrl('images/' . $imagesPath . '/magemonitoring_logo.png');

        $html = '
<tr id="row_%s">
    <td colspan="2">
        <div class="box">
            <p>
                <a href="' . self::URL . '" target="_blank" title="' . $this->__('Go to Shopwerft Website') . '">
                    <img src="' . $logoSrc . '" alt="' . $this->__('Shopwerft') . '" />
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

