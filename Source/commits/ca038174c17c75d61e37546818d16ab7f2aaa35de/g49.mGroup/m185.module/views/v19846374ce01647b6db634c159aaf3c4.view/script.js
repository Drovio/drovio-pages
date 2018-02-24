jq = jQuery.noConflict();

jq(document).one("ready", function() {
	// Set selection click listener
	jq(document).on("click", ".selectionList .listContent", function() {
		// Get target
		var target = jq(this).data("target");
		
		// Set all projectBoxes as noDisplay
		jq(".projectList .projectBox").addClass("noDisplay");
		
		// Show all targets
		jq(".projectList .projectBox"+"."+target).removeClass("noDisplay");
	});
});