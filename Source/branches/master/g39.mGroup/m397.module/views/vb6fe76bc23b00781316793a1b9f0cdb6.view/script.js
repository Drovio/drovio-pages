var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Scroll to section
	jq(document).on("click", ".banner__icons .barrow", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Animate
		jq('html,body').animate({
			scrollTop: jq("#products").offset().top,
		}, 1000, "easeInOutExpo");
	});
	
	
	
	// MIXPANEL TRACKING
	// Create new team
	jq(document).on("submit", ".form_container.newTeamFormContainer form", function() {
		// Track team creation
		mixpanel.track("create_team");
	});
});