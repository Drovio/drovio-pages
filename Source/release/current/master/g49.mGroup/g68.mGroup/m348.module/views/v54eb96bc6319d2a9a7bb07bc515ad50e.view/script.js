var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Refresh key list
	jq(document).on("application.keys.list.reload", function() {
		jq(".devApplicationKeys").trigger("reload");
	});
});