jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listener to remove ignored invitations
	jq(document).on("account_invitations.remove", function(ev, refID) {
		// Remove current invitation
		jq(".myInvitationsContainer .ivrow#"+refID).remove();
	});
});