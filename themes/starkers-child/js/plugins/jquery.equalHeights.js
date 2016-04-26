// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

    $.equalHeights = function(element, options) {

        var defaults = {
            foo: 'bar',
            onFoo: function() {}
        }

        var plugin = this;

        plugin.settings = {}

        var $element = $(element),
             element = element,
             currentTallest = 0;

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            
            var children = $element.children();
            
            children.each(function() {
            	$(this).height(plugin.checkTallest());
            });
            
            $(window).resize(function(){
            	children.each(function() {
            		$(this).height(plugin.checkTallest());
            	});
            });
                        
        }

        plugin.checkTallest = function() {
        	$element.children().each(function() {
						if ($(this).height() > currentTallest) {
							currentTallest = $(this).height();
						}
        	});
        	
        	return currentTallest;
//        	if (!px && Number.prototype.pxToEm) currentTallest = currentTallest.pxToEm(); //use ems unless px is specified
        }

        var foo_private_method = function() {
            // code goes here
        }

        plugin.init();

    }

    $.fn.equalHeights = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('equalHeights')) {
                var plugin = new $.equalHeights(this, options);
                $(this).data('equalHeights', plugin);
            }
        });

    }

})(jQuery);
//Usage

//$(document).ready(function() {
//
    // attach the plugin to an element
//    $('#element').equalHeights({'foo': 'bar'});
//
    // call a public method
//    $('#element').data('equalHeights').foo_public_method();
//
    // get the value of a property
//    $('#element').data('equalHeights').settings.foo;
//
//});
