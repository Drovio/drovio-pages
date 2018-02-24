// Prevent Reload when project designer is active
jq = jQuery.noConflict();
jq(window).on('beforeunload', function() {
	// Check if there is a project designer and show a message
	if (jq(".projectDesignerPage").length > 0)
		return "You are in the middle of a project development and you may loose any unsaved changes."
});