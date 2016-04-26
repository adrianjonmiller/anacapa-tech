
	jQuery(document).ready(function($) {

		jQuery( ".select-menu" ).change(function() { 
		        window.location = jQuery(this).find("option:selected").val();
		    });
		    
		$('.obj_badge').each(function(){
			$this = $(this);
			var height = $this.height(),
			round = height/2;
			
			$this.attr('style', 'border-radius: '+round+'px; -moz-border-radius:'+round+'px; -webkit-border-radius:'+round+'px');
		});
		
		
//		$(window).load(function(){
//			if($(window).width() > 480) {
//				fixedHeight();
//			}
//		});
		
//		$(window).resize(function(){
//			if($(window).width() > 720) {
//				$('.obj_sameHeights').children().outerHeight('auto');
//				fixedHeight();
//			} else {
//				$('.obj_sameHeights').children().outerHeight('auto');
//			}
//		});
//		
//		function fixedHeight() {
//			var px = false;
//			
//			$('.obj_sameHeights').each(function(){
//				var currentTallest = 0;
//				$(this).children().each(function(i){
//					if ($(this).height() > currentTallest) { currentTallest = $(this).height(); }
//				});
//			if (!px && Number.prototype.pxToEm) currentTallest = currentTallest.pxToEm(); //use ems unless px is specified
				// for ie6, set height since min-height isn't supported
//				if ($.browser.msie && $.browser.version == 6.0) { $(this).children().css({'height': currentTallest}); }
//				$(this).children().height(currentTallest); 
//			});
//		}
		
// 		$("img").each(function() {
// 			$(this).load(function() {
// 			           //Get the width of the image
//         	var width = $(this).width();
//         	var height = $(this).height();
		
// 		           //Max-width substitution (works for all browsers)
//              $(this).css("max-width", width);
//              $(this).css("width", "100%");
             
//              $(this).css("max-height", height);
//              });
		
// 		});
	});

