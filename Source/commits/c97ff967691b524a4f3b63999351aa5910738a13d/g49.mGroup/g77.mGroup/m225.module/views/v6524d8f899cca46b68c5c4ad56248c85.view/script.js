var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Add listeners to complete_tasks
	jq(document).on("click", ".complete_tasks .rtask.readme, .complete_tasks .rtask.pname", function() {
		// Click sidebar project settings
		jq(".project-sidebar .menuItem.settings a").trigger("click");
	});
});