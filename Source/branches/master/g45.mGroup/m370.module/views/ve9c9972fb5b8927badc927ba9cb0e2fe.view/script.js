jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload invitations
	jq(document).on("team_invitations.reload", function(ev, refID) {
		jq(".invitationList").trigger("reload");
	});
	// Remove an invitation
	jq(document).on("team_invitations.remove", function(ev, refID) {
		// Remove current invitation
		jq(".invitationList .ivrow#"+refID).remove();
		
		// If invitations are empty, remove the entire container
		if (jq(".invitationList .ivrow").length == 0)
			jq(".invitationList").trigger("reload");
	});
});