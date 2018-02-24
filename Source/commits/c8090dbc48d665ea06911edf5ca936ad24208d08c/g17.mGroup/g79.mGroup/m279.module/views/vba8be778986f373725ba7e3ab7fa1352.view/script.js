var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Add listeners to complete_tasks
	jq(document).on("click", ".complete_tasks .rtask.server:not(.done), .complete_tasks .rtask.url:not(.done), .complete_tasks .rtask.meta:not(.done)", function() {
		// Click sidebar project settings
		jq(".website-sidebar .menuItem.settings a").trigger("click");
	});
	
	// Remove tasks
	jq(document).on("click", ".complete_tasks .close_ico", function() {
		// Remove container
		jq(this).closest(".websiteOverview").removeClass("with_tasks")
		jq(".complete_tasks").remove();
	});
});