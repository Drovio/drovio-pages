jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".noTeamPopup .noTeam .btn.dismiss", function() {
		// Dismiss popup
		jq(this).trigger("dispose");
	});
});