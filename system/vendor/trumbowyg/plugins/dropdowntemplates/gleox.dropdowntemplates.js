(function ($) {
    'use strict';

    // Adds the extra button definition
    $.extend(true, $.trumbowyg, {
        plugins: {
            dropdowntemplates: {
                init: function (trumbowyg) {
                    var temps = templateInit(trumbowyg);
                    trumbowyg.o.btns = trumbowyg.o.btns.concat(temps);
                }
            }
        }
    });

    function templateInit(trumbowyg) {
        var temps = [];

        var dropdowntemplates = $("#"+trumbowyg.$ta.attr("id")+"-dropdowntemplates");

        var areas = dropdowntemplates.find("> dropdowntemplate");
        $.each(areas, function (index, area) {
            var area = $(area);

            if (area.attr("template_id") === undefined) {
                area.attr("template_id", Math.floor(Math.random() * 999999));
            }
            var temp_id = area.attr("template_id");

            trumbowyg.addBtnDef('dropdowntemplate_'+temp_id, {
                dropdown: templateSelector(trumbowyg, area, area.find("> content > item")),
                hasIcon: false,
                text: area.find("> title").html()
            });
            temps.push(['dropdowntemplate_' + temp_id]);
        });
        dropdowntemplates.remove();
        return temps;
    } 

    // Creates the template-selector dropdown.
    function templateSelector(trumbowyg, area, items) {
        var temp_id = area.attr("template_id");
        var templates = [];

        $.each(items, function (index, template) {
            template = $(template);
            trumbowyg.addBtnDef('dropdowntemplate_' + temp_id + '_' + index, {
                fn: function () {
                    var insert_type = area.attr("insert_type");
                    if (area.attr("insert_type") === undefined) {
                        insert_type = "content";
                    }
                    if (insert_type == "inner") {
                        trumbowyg.execCmd('insertHTML', template.find("> data").html());
                    } else {
                        trumbowyg.html(template.find("> data").html());
                    }
                    
                },
                hasIcon: false,
                title: template.find("> title").html()
            });
            templates.push('dropdowntemplate_' + temp_id + '_' + index);
        });

        return templates;
    }
})(jQuery);
