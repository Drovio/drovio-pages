jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload team information
	jq(document).on("team.info.reload", function(ev) {
		jq(".teamInfoViewer").trigger("reload");
	})
});