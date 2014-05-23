/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function toggleWithLoad(widgetId, refreshUrl) {
    if (!$(widgetId).firstDescendant().firstDescendant()) {
        refreshWidget(widgetId, refreshUrl);
    }
    $('refresh-'+widgetId).toggleClassName('control-invis');
    Fieldset.toggleCollapse(widgetId);
};

function refreshWidget(widgetId, url) {
       new Ajax.Updater($(widgetId).firstDescendant(), url,
               { evalScripts: true,
                 parameters: { widgetId: widgetId }
       });
       return false;
};

function openWidgetConf(widgetName, url) {
    Modalbox.show(url, {title: widgetName, width: 600}); return false;
};

function execWidgetCallback(widgetId, widgetName, callback, url, refreshUrl) {
    var modalParams = {params: {cb: callback},
                        title: widgetName,
                        width: 600};
    if (refreshUrl != null) {
        modalParams.afterHide = function() { refreshWidget(widgetId, refreshUrl); };
    }
    Modalbox.show(url, modalParams); return false;
};
