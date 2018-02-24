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
	
	// Show error notification
	jq(document).on("team_creator.error", function(ev, info) {
		// Load app store on action
		var actionCallback = function() {
			// Click on app store
			jq(".createTeamContainer input[name='tname']").focus().select();

			// Dispose popup
			jq(this).trigger("dispose");
		}

		// Show notification
		pageNotification.show(jq(document), "team_creator_error", info.title, info.action_title, actionCallback, null);
	});
	
	// Toggle pricing features
	jq(document).on("click", ".section-pricing .pricing__body li.more, .section-pricing .pricing__body li.less", function(ev) {
		// Toggle more class
		jq(this).closest(".pricing__body").toggleClass("more");
	});
});