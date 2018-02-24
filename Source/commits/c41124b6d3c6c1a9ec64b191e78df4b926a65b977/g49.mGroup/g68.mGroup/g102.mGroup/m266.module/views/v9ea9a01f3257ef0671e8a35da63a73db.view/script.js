jq(document).one("ready", function() {
	// Manual refresh of the view explorer
	jq(document).on("click", "#VRefresh", function() {
		// Save state
		jq("#appViewsViewer").trigger("saveState");
		jq("#appViewsViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
	
	// Listener to refresh the explorer
	jq(document).on("application.views.explorer.refresh", function() {
		jq(".viewExplorer").trigger("reload");
	});
});