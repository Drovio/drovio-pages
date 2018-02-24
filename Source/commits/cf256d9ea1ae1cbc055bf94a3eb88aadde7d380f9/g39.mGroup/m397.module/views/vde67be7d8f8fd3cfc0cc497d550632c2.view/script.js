jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".drov-navbarContainer .toggle_sidebar.menu", function() {
		// Toggle nav menu
		jq(".drov-navbarContainer .navMenu").animate({
			height: "toggle"
		}, 200, function() {
			if (jq(this).css("display") == "none")
				jq(this).css("display", "");
		});
	});
	
	// Toggle sidebar
	jq(document).on("click", ".drov-navbarContainer .navMenu li a", function() {
		// Toggle nav menu
		jq(".drov-navbarContainer .navMenu").animate({
			height: "hide"
		}, 200, function() {
			if (jq(this).css("display") == "none")
				jq(this).css("display", "");
		});
		
		// Track event
		mixpanel.track("landing_page_navigation " + jq(this).attr("href"));
	});
	
	// Login dialog listener
	jq(document).on("click", ".drov-navbarContainer .navMenu li.login", function() {
		// Track login dialog open
		mixpanel.track("login_dialog");
	});
});