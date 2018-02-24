jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Set listener to reload members
	jq(document).on("members.reload", function() {
		jq("#teamMembers").trigger("reload");
	});
	
	jq(document).on("project_invitations.remove", function(ev, refID) {
		// Remove current invitation
		jq(".projectInvitations .ivrow#"+refID).remove();
		
		// If invitations are empty, remove the entire container
		if (jq(".projectInvitations .ivrow").length == 0)
			jq(".projectInvitations").remove();
	});
});