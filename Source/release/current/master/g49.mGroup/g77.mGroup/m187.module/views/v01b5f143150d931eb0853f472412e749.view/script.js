jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Prevent Reload when project designer is active
	jq(window).on('beforeunload', function() {
		// Check if there is a project designer and show a message
		if (jq(".projectDesignerPage").length > 0)
			return "You are in the middle of a project development and you may loose any unsaved changes."
	});
	
	// Show hints or not
	var projectID = jq(".projectDesignerPage").data("project-id");
	var hintsStateObject = new UIStateObject("prdhcdp" + projectID);
	var hideHints = hintsStateObject.getState(false);
	if (!hideHints)
		jq("#projectHintsContainer").trigger("load");
	else
		jq("#projectHintsContainer").detach();
});