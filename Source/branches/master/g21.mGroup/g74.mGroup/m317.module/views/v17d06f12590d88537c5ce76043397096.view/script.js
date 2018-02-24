var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Close dialog
	jq(document).on("click", ".apiKeyDialog .close_button", function() {
		// Click on menu
		jq(this).trigger("dispose");
	});
});