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
	
	// Toggle sidebar
	jq(document).on("click", ".landingPage .navbarContainer .navMenu li a", function() {
		// Toggle nav menu
		jq(".landingPage .navbarContainer .navMenu").animate({
			height: "hide"
		}, 200, function() {
			if (jq(this).css("display") == "none")
				jq(this).css("display", "");
		});
	});
});