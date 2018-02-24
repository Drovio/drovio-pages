jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".dev-navbarContainer .toggle_menu.sidebar", function() {
		jq(this).closest(".dev-domain").toggleClass("ws");
	});
	
	// Toggle nav menu
	jq(document).on("click", ".dev-navbarContainer .toggle_menu.nav", function() {
		// Toggle nav menu
		jq(".dev-navbarContainer .navMenu").animate({
			height: "toggle"
		}, 200, function() {
			if (jq(this).css("display") == "none")
				jq(this).css("display", "");
		});
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

jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".landingPage .navbarContainer .toggle_sidebar.menu", function() {
		// Toggle nav menu
		jq(".landingPage .navbarContainer .navMenu").animate({
			height: "toggle"
		}, 200, function() {
			if (jq(this).css("display") == "none")
				jq(this).css("display", "");
		});
	});
});