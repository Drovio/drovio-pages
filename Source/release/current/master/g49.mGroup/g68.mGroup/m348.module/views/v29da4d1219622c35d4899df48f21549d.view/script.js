var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Quick link
	jq(document).on("click", ".apiKeyDialog .close_button", function() {
		// Click on menu
		jq(this).trigger("dispose");
	});
	
	// Refresh key info
	jq(document).on("application.keys.info.reload", function() {
		jq("#keyInfoContainer").trigger("reload");
	});
});