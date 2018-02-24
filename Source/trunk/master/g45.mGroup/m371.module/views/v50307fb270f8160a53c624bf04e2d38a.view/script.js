jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listen to info editor
	jq(document).on("team.info.edit", function(ev) {
		// Switch from viewer to editor
		jq(".teamInfo .infoViewer").addClass("noDisplay");
		jq(".teamInfo .infoEditor").removeClass("noDisplay");
	});
	
	// Listen to info editor
	jq(document).on("team.info.reload", function(ev) {
		// Switch from viewer to editor
		jq(".teamInfo .infoViewer").removeClass("noDisplay");
		jq(".teamInfo .infoEditor").addClass("noDisplay");
		
		// Reload info
		jq(".teamInfoViewer").trigger("reload");
	});
});