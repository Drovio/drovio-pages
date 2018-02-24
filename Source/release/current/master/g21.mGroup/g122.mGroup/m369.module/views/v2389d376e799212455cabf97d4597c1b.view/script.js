jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload invitations
	jq(document).on("account_invitations.reload", function(ev, refID) {
		jq(".myInvitations").trigger("reload");
	});
	// Remove an invitation
	jq(document).on("account_invitations.remove", function(ev, refID) {
		// Remove current invitation
		jq(".myInvitations .ivrow#"+refID).remove();
		
		// If invitations are empty, remove the entire container
		if (jq(".myInvitations .ivrow").length == 0)
			jq(".myInvitations").trigger("reload");
	});
});