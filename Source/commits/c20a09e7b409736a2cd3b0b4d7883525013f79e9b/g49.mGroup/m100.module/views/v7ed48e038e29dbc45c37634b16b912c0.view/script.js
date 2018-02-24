jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Select document and close sidebar
	jq(document).on("click", ".docMenu .menu-item", function() {
		jq(this).closest(".dev-domain").removeClass("ws");
	});
});