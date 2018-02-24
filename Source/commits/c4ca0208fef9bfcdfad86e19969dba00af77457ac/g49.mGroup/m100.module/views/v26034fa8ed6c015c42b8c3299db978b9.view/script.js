jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".dev-navbarContainer .toggle_sidebar.menu", function() {
		jq(this).closest(".dev-domain").toggleClass("ws");
	});
});