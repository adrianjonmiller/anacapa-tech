DLN.Behaviors.equalHeights = function(container){
	var children = container.children(),
	currentTallest = 0;

//	children.each(function(){
//		$(this).height(tallest());
//	});

	console.log(tallest());

	$(window).load(function(){
		if($(window).width() > 480) {
			children.each(function(){
				$(this).height(tallest());
			});
		}
	});

	$(window).resize(function(){
		if($(window).width() > 720) {


			children.each(function(){
				$(this).outerHeight('auto');
				$(this).height(tallest());
			});

		} else {
			children.each(function() {
				$(this).outerHeight('auto');
			});
		}
	});

//	$(window).resize(function(){
//		children.each(function(){
//			$(this).height(tallest());
//		});
//	});

	function tallest() {
		children.each(function(){
			if ($(this).height() > currentTallest) {
				currentTallest = $(this).height();
			}
		});
		return currentTallest;
	}
}
