jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".teamEditor .manage", function() {
		var teamEditor = jq(this).closest(".teamEditor");
		teamEditor.toggleClass("open");
		if (teamEditor.hasClass("open"))
			jq("#teamMembers").trigger("load");
	});
});