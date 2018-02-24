jq = jQuery.noConflict();
jq(document).one("ready", function() {
	banner_slideshow.init();
	
	function resizeMain()
	{
		// Get inner window height
		var innerHeight = window.innerHeight;
		
		// Check if there is a toolbar and remove height
		innerHeight -= jq(".uiToolbarNav").height();
		// Calculate min height
		var minHeight = jq(".frontendPage > .fsection.main .middleContainer").outerHeight() + 120;
		var newHeight = (innerHeight < minHeight ? minHeight : innerHeight);
		jq(".frontendPage > .fsection.main").height(newHeight);
	}
	resizeMain();
	window.onresize = function() {
		resizeMain();
	};
});

banner_slideshow = {
	slideTimer: 0,
	init: function() {
		// Set nav items navigation listeners
		jq(document).on("click", ".frontendPage .snav .navitem", function() {
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
		var bullCount = jq(".frontendPage .snav .navitem").length;
		var index = jq(".frontendPage .snav .navitem.active").index();
		
		// Set next index
		var nextIndex = (index + offset) % bullCount;
		banner_slideshow.gotoSlide(nextIndex);
	},
	gotoSlide: function(index) {
		// Set active bullet
		jq(".frontendPage .snav .navitem").removeClass("active");
		jq(".frontendPage .snav .navitem").eq(index).addClass("active");
		
		// Set active slide
		jq(".frontendPage .content .desc").removeClass("active");
		jq(".frontendPage .content .desc").eq(index).addClass("active");
		
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