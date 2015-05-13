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
function toggleWithLoad(widgetId, refreshUrl) {
    if (!$(widgetId).firstDescendant().firstDescendant()) {
        refreshWidget(widgetId, refreshUrl);
    }
    $('refresh-' + widgetId).toggleClassName('control-invis');
    Fieldset.toggleCollapse(widgetId);
};

function refreshWidget(widgetId, url) {
    new Ajax.Updater($(widgetId).firstDescendant(), url,
        {
            evalScripts: true,
            parameters: {widgetId: widgetId}
        });
    return false;
};

function openWidgetConf(widgetName, url) {
    Modalbox.show(url, {title: widgetName, width: 600});
    return false;
};

function execWidgetCallback(widgetId, widgetName, callback, url, refreshUrl) {
    var modalParams = {
        params: {cb: callback},
        title: widgetName,
        width: 600
    };
    if (refreshUrl != null) {
        modalParams.afterHide = function () {
            refreshWidget(widgetId, refreshUrl);
        };
    }
    Modalbox.show(url, modalParams);
    return false;
};
