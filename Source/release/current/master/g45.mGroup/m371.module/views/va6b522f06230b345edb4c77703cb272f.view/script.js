var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload key list
	jq(document).on("team.keys.list.reload", function(ev, application_id) {
		jq("#team_keys_app" + application_id).trigger("reload");
	});
});