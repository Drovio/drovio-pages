var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Add listeners to complete_tasks
	jq(document).on("click", ".complete_tasks .rtask.readme:not(.done), .complete_tasks .rtask.pname:not(.done)", function() {
		// Click sidebar project settings
		jq(".project-sidebar .menuItem.settings a").trigger("click");
	});
	
	// Remove tasks
	jq(document).on("click", ".complete_tasks .close_ico", function() {
		// Remove container
		jq(this).closest(".projectOverview").removeClass("with_tasks")
		jq(".complete_tasks").remove();
	});
});