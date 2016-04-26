// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {

    $.grids = function(element, options) {

        var defaults = {
        	pad: 16
        }

        var plugin = this;

        plugin.settings = {}

        var $element = $(element),
             element = element;

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            
            
            
            $(window).on('resize', function() {
            	assign_widths();
            });
            
            var assign_widths = function() {
            	var $children = $element.children();
            	var total_column_widths = parent_width() - plugin.settings.pad*($children.length-1);
            	var grid_unit = total_column_widths*(1/total_columns());            
            	var i = 0;
            	$children.each(function(){
	            	$this = $(this);
	            	var columnWidth = grid_unit*columns()[i];
	            	       	
	            	if(columnWidth > 0) {
	            		$this.css({'width': columnWidth, 'position': 'relative', 'left': plugin.settings.pad*i});
	            	} 
	            	i++;
       			});
            }
            
            assign_widths();
            
            
        }

        plugin.foo_public_method = function() {
        	
        }

        var get_width = function() {
        	
        	return 100/$element.children().length;
        }
        
        var columns = function() {
        	
        	var split = $element.attr("data-columns").split(', ');
        	return split;
        	
        }
        
        var parent_width = function() {
        	return $element.width();
        }
        var total_columns = function() {
	        var total = 0;
	        for (var i = 0; i < columns().length; i++) {
	            total += columns()[i] << 0;
	        }
	        return total;
        }

        plugin.init();

    }

    $.fn.grids = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('grids')) {
                var plugin = new $.grids(this, options);
                $(this).data('grids', plugin);
            }
        });

    }

})(jQuery);
//Usage
//
//$(document).ready(function() {
//
    // attach the plugin to an element
//    $('#element').grids({'foo': 'bar'});
//
    // call a public method
//    $('#element').data('grids').foo_public_method();
//
    // get the value of a property
//    $('#element').data('grids').settings.foo;
//
//});
