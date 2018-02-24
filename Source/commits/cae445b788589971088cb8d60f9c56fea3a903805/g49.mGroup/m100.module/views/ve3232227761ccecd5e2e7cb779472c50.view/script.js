jq = jQuery.noConflict();
jq(document).one("ready", function() {
	banner_slideshow.init();
	screen_slideshow.init();
});

banner_slideshow = {
	slideTimer: 0,
	init: function() {
		// Set nav items navigation listeners
		jq(document).on("click", ".developerPage .snav .navitem", function() {
			// Go to specific slide
			banner_slideshow.gotoSlide(jq(this).index());
			
			// Re-start presentation
			banner_slideshow.presentation();
		});
		
		// Start presentation
		this.presentation();
	},
	gotoSlideRelative: function(offset) {
		// Get bullets
		var bullCount = jq(".developerPage .snav .navitem").length;
		var index = jq(".developerPage .snav .navitem.active").index();
		
		// Set next index
		var nextIndex = (index + offset) % bullCount;
		banner_slideshow.gotoSlide(nextIndex);
	},
	gotoSlide: function(index) {
		// Set active bullet
		jq(".developerPage .snav .navitem").removeClass("active");
		jq(".developerPage .snav .navitem").eq(index).addClass("active");
		
		// Set active slide
		jq(".developerPage .content .desc").removeClass("active");
		jq(".developerPage .content .desc").eq(index).addClass("active");
		
		return true;
	},
	presentation: function() {
		// Set timer
		clearInterval(this.slideTimer);
		this.slideTimer = setInterval(function() {
			banner_slideshow.gotoSlideRelative(1);
		}, 60000);
	}
}

screen_slideshow = {
	slideTimer: 0,
	init: function() {
		// Set listeners for arrows
		jq(document).on("click", ".developerPage .slideshow .arrow", function() {
			// Move slideshow
			var step = 1;
			if (jq(this).hasClass("lft"))
				step = -1;
			screen_slideshow.gotoSlideRelative(step);
			
			// Re-start presentation
			screen_slideshow.presentation();
		});
		
		// Set nav balls navigation listeners
		jq(document).on("click", ".developerPage .slideshow .bullets .bull", function() {
			// Go to specific slide
			screen_slideshow.gotoSlide(jq(this).index());
			
			// Re-start presentation
			screen_slideshow.presentation();
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
		screen_slideshow.gotoSlide(nextIndex);
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
			screen_slideshow.gotoSlideRelative(1);
		}, 15000);
	}
}