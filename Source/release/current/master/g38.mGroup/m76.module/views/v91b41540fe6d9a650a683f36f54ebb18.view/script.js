jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".help-navbarContainer .toggle_sidebar.menu", function() {
		jq(this).closest(".HelpCenterPage").toggleClass("ws");
	});
});