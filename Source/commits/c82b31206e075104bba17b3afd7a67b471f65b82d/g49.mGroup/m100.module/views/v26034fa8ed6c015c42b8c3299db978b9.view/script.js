jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar
	jq(document).on("click", ".navbarContainer .toggle_sidebar.menu", function() {
		jq(this).closest(".developersPage").toggleClass("ws");
	});
});