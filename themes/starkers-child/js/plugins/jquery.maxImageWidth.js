// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

    $.maxImageWidth = function(element, options) {

        var defaults = {
            foo: 'bar',
            onFoo: function() {}
        }

        var plugin = this;

        plugin.settings = {}

        var $element = $(element),
             element = element;

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            $element.hide();
            var url = $element.attr('src');

            var img = new Image();
            var maxWidth;

            img.onload = function() {
              maxWidth = img.width;
              if(maxWidth) {
                $element.css("max-width", maxWidth);
                $element.css("width", "100%");
                $element.show();
              }
            }
            img.src = url;
            // code goes here
        }

        plugin.foo_public_method = function() {
            // code goes here
        }

        var foo_private_method = function() {
            // code goes here
        }

        plugin.init();

    }

    $.fn.maxImageWidth = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('maxImageWidth')) {
                var plugin = new $.maxImageWidth(this, options);
                $(this).data('maxImageWidth', plugin);
            }
        });

    }

})(jQuery);