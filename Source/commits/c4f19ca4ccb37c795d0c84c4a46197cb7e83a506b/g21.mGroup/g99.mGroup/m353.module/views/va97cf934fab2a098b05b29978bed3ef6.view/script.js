jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listen to info editor
	jq(document).on("click", ".teamInfoEditor .close_btn", function(ev) {
		// Switch from viewer to editor
		jq(".teamInfo .infoViewer").removeClass("noDisplay");
		jq(".teamInfo .infoEditor").addClass("noDisplay");
	});
});