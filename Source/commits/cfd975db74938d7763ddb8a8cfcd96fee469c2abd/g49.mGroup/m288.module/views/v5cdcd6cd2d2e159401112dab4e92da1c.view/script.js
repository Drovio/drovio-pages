jq = jQuery.noConflict();
jq(document).one("ready", function() {
	ft_slideshow.init();
});

ft_slideshow = {
	slideTimer: 0,
	init: function() {
		// Set nav items navigation listeners
		jq(document).on("click", ".developerFtrPage .snav .navitem", function() {
			// Go to specific slide
			ft_slideshow.gotoSlide(jq(this).index());
			
			// Re-start presentation
			ft_slideshow.presentation();
		});
		
		// Start presentation
		this.presentation();
	},
	gotoSlideRelative: function(offset) {
		// Get bullets
		var bullCount = jq(".developerFtrPage .snav .navitem").length;
		var index = jq(".developerFtrPage .snav .navitem.active").index();
		
		// Set next index
		var nextIndex = (index + offset) % bullCount;
		ft_slideshow.gotoSlide(nextIndex);
	},
	gotoSlide: function(index) {
		// Set active bullet
		jq(".developerFtrPage .snav .navitem").removeClass("active");
		jq(".developerFtrPage .snav .navitem").eq(index).addClass("active");
		
		// Set active slide
		jq(".developerFtrPage .slides .slide").removeClass("active");
		jq(".developerFtrPage .slides .slide").eq(index).addClass("active");
		
		return true;
	},
	presentation: function() {
		// Set timer
		clearInterval(this.slideTimer);
		this.slideTimer = setInterval(function() {
			ft_slideshow.gotoSlideRelative(1);
		}, 8000);
	}
}