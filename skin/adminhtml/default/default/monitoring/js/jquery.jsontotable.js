(function ($) {
    $.jsontotable = function(data, options) {
        var settings = $.extend({
            id: null, // target element id
            header: true,
            className: null
        }, options);

        options = $.extend(settings, options);

        var obj = data;
        if(typeof obj === "string") {
            obj = $.parseJSON(obj);
        }

        if(options.id && obj.length) {

            var i, row;
            var table = $("<table></table>");

            for(var key in data[0]) {
                if(key[0] != '_') {
                    table.append('<th>' + key + '</th>');
                }
            }

            if(options.className) {
                table.addClass(options.className);
            }

            $.fn.appendTr = function(rowData, isHeader) {
                var frameTag = (isHeader) ? "thead" : "tbody";
                var rowTag = (isHeader) ? "th" : "td";
                var cssClass = rowData['_cssClasses'];

                row = $('<tr class="' + cssClass + '"></tr>');
                for(var key in rowData) {
                    var data = rowData[key].toString();
                    if(data.substr(0,7) != 'health-') {
                        row.append("<" + rowTag + ">" + rowData[key] + "</" + rowTag + ">");
                    }
                }

                $(this).append($("<" + frameTag + "></" + frameTag + ">").append(row));
                return this;
            };

            if(options.header) {
                table.appendTr(obj[0], true);
            }

            for (i = 0; i < obj.length; i++) {
                table.appendTr(obj[i]);
            }

            $(options.id).html(table);
        }

        return this;
    };
}(jQuery));
