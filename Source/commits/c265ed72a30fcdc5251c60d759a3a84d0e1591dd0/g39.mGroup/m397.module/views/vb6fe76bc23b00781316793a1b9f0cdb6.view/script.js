var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Switch forms
	jq(document).on("click", ".form_sub.existing_team", function() {
		// Display other text
		jq(this).closest(".engage_form_container").addClass("existing_team").find(".existingTeamContainer input[name='tname']").focus();
	});
	jq(document).on("click", ".form_sub.new_team", function() {
		// Display other text
		jq(this).closest(".engage_form_container").removeClass("existing_team").find(".newTeamFormContainer input[name='tname']").focus();
	});
	
	// Go to team dashboard
	jq(document).on("click", ".existingTeamContainer .tmbtn.go", function(ev) {
		// Go to team
		var teamName = jq(".existingTeamContainer input[name='tname']").val();
		if (teamName == "")
			return;
			
		var teamUrl = url.resolve(teamName, "");
		window.location.assign(teamUrl);
	});
	
	jq(document).on("keyup", ".existingTeamContainer input[name='tname']", function(ev) {
		if (ev.which == 13)
			jq(".existingTeamContainer .tmbtn.go").trigger("click");
	});
});