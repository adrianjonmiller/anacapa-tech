// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

    $.superslider = function(element, options) {

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
            $children = $element.children();
            $children.each(function() {
            	alert('success');
            });
        }

        plugin.foo_public_method = function() {
            // code goes here
        }

        var foo_private_method = function() {
            // code goes here
        }

        plugin.init();

    }

    $.fn.superslider = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('superslider')) {
                var plugin = new $.superslider(this, options);
                $(this).data('superslider', plugin);
            }
        });

    }

})(jQuery);
//Usage
//
//$(document).ready(function() {
//
    // attach the plugin to an element
//    $('#element').superslider({'foo': 'bar'});
//
    // call a public method
//    $('#element').data('superslider').foo_public_method();
//
    // get the value of a property
//    $('#element').data('superslider').settings.foo;
//
//});
