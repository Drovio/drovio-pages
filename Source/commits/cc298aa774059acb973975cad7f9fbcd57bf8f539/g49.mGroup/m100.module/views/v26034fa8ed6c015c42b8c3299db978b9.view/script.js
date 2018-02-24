jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".dev-navbarContainer .toggle_sidebar.menu", function() {
		jq(this).closest(".dev-domain").toggleClass("ws");
	});
	
	// Track events
	jq(document).on("click", ".dev-navbarContainer .navMenu li a", function() {
		// Track event
		mixpanel.track("developers_page_navigation " + jq(this).attr("href"));
	});
	
	// Login dialog listener
	jq(document).on("click", ".dev-navbarContainer .navMenu li.login", function() {
		// Track login dialog open
		mixpanel.track("login_dialog");
	});
});