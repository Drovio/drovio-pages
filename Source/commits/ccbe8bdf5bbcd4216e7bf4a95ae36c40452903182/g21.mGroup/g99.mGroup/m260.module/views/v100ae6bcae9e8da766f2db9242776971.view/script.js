jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Set listener to reload members
	jq(document).on("members.reload", function() {
		jq("#teamMembers").trigger("reload");
	});
});