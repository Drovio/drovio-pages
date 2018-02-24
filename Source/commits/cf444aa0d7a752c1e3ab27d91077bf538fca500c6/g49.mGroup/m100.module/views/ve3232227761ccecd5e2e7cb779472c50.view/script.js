jq = jQuery.noConflict();
jq(document).one("ready", function() {
	slideshow.init();
});

slideshow = {
	slideTimer: 0,
	init: function() {
		// Set listeners for arrows
		jq(document).on("click", ".developerPage .slideshow .arrow", function() {
			// Move slideshow
			var step = 1;
			if (jq(this).hasClass("lft"))
				step = -1;
			slideshow.gotoSlideRelative(step);
			
			// Re-start presentation
			slideshow.presentation();
		});
		
		// Set nav balls navigation listeners
		jq(document).on("click", ".developerPage .slideshow .bullets .bull", function() {
			// Go to specific slide
			slideshow.gotoSlide(jq(this).index());
			
			// Re-start presentation
			slideshow.presentation();
		});
		
		// Start presentation
		this.presentation();
	},
	gotoSlideRelative: function(offset) {
		// Get bullets
		var bullCount = jq(".developerPage .slideshow .bullets .bull").length;
		var index = jq(".developerPage .slideshow .bullets .bull.active").index();
		
		// Set next index
		var nextIndex = (index + offset) % bullCount;
		slideshow.gotoSlide(nextIndex);
	},
	gotoSlide: function(index) {
		// Set active bullet
		jq(".developerPage .slideshow .bullets .bull").removeClass("active");
		jq(".developerPage .slideshow .bullets .bull").eq(index).addClass("active");
		
		// Set active slide
		jq(".developerPage .slideshow .slide").removeClass("active");
		jq(".developerPage .slideshow .slide").eq(index).addClass("active");
		
		return true;
	},
	presentation: function() {
		// Set timer
		clearInterval(this.slideTimer);
		this.slideTimer = setInterval(function() {
			slideshow.gotoSlideRelative(1);
		}, 5000);
	}
}