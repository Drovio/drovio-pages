jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Set listener to reload members
	jq(document).on("team_members.reload", function() {
		jq(".memberListContainer").trigger("reload");
	});
});