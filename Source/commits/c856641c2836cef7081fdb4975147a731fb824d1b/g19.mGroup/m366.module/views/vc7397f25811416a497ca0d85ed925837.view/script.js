jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Close dialog
	jq(document).on("click", ".teamCreatorDialog .close_button", function(ev) {
		jq(this).trigger("dispose");
	});
	
	// Show existing team form
	jq(document).on("click", ".teamCreatorDialog .rContainer .lb", function(ev) {
		jq(this).closest(".rContainer").toggleClass("open");
		if (jq(this).closest(".rContainer").hasClass("open"))
			jq(".teamCreatorDialog .rfContainer .tminp").focus();
	});
	
	// Go to team
	jq(document).on("keyup", ".teamCreatorDialog .rfContainer .tminp", function(ev) {
		// Check enter key
		var code = ev.keyCode || ev.which;
		if (code == 13)
			jq(".teamCreatorDialog .rfContainer .tmbtn").trigger("click");
	});
	jq(document).on("click", ".teamCreatorDialog .rfContainer .tmbtn", function(ev) {
		// Go to team
		var teamName = jq(".teamCreatorDialog .rfContainer input[name='tname']").val();
		if (teamName == "")
			return;
			
		var teamUrl = url.resolve(teamName, "");
		window.location.assign(teamUrl);
	});
});