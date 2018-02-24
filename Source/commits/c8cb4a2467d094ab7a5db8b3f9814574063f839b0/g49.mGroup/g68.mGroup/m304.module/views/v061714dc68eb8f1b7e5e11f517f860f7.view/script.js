jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload boss settings
	jq(document).on("boss_settings.reload", function(ev) {
		jq(".bossSettingsContainer").trigger("reload");
	});
});