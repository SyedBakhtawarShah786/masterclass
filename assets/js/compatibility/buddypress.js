/**
 * 
 * Tophive Responsive Menu Jquery plugin
 * @author Tophive
 * @version 1.0.0
 * 
 */
(function($) {
    
    $.fn.TophiveResponsiveMenu = function() {
        
        return this.each(function() {

            var $this = $(this),
                limit,
                position,
                area,
                list,
                active;

            setup($this);

            function setup(obj) {
                obj.wrap("<div class='tophive-responsive-container'></div>").css("visibility", "hidden");
                if (!obj.find("li.tophive-responsive-dropdown").length) {
                    obj.append('<li style="display:none" class="tophive-responsive-dropdown"></li>')
                        .find("li.tophive-responsive-dropdown")
                        .append('<div class="dropdown"></div>')
                        .find("div.dropdown")
                        .append('<a href="javascript:void(0)" data-toggle="dropdown">...<span class="caret"></span></a>')
                        .append('<ul class="dropdown-menu dropdown-menu-center tophive-responsive-menu"></ul>');
                }
                obj.find("li").each(function(index) {
                    $(this).attr("data-index", index);
                });
                limit = obj.find("li").length;
                index = limit - 1;
                active = obj.find("li.current").index();
                create(obj);
            }

            function reset(obj) {
                position = limit;
                $.when(obj.find(".tophive-responsive-menu li").appendTo(obj)).done(function() {
                    $.when(sort(obj)).done(function() {
                        create(obj);
                    });
                });
            }

            function sort(obj) {
                obj.each(function() {
                    $(this).html(
                        $(this).children("li").sort(function(a, b) {
                            return $(b).data("index") < $(a).data("index") ? 1 : -1;
                        })
                    );
                });
            }

            function create(obj) {
                area = obj.outerWidth(true);
                list = obj.offset().left + obj.find("li.tophive-responsive-dropdown").outerWidth(true) + obj.find("li.current").outerWidth(true);
                obj.find('li:not(".current, .tophive-responsive-dropdown")').each(function(index) {
                    list += $(this).outerWidth(true);
                    if (list >= area) {
                        if (index > active) {
                            position = index + 1;
                        }
                        else {
                            position = index;
                        }
                        move(obj, position, index);
                        obj.find(".tophive-responsive-dropdown").show();
                        return false;
                    } 
                    else {
                        obj.find(".tophive-responsive-dropdown").hide();
                    }
                });
                obj.css("visibility", "visible");
            }

            function move(obj, position, index) {
                for (x = position; x <= limit; x++) {
                    obj.find('li:not(".current, .tophive-responsive-dropdown")[data-index="' + x + '"]').appendTo(obj.find(".tophive-responsive-menu"));
                }
            }

            $(window).resize(function() {
                reset($this);
            });
        });
    };

})(jQuery);

jQuery(function() {
    jQuery(".main-navs ul").TophiveResponsiveMenu();
    jQuery(".tophive-responsive-dropdown a").on('click', function(){
        jQuery(this).parent().toggleClass('open');
    });
});
