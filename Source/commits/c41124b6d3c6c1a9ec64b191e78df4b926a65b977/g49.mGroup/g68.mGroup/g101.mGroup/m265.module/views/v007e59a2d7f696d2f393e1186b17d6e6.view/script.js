jq(document).one("ready", function() {
	jq(document).on("click", "#LRefresh", function() {
		// Save state
		jq("#appLibViewer").trigger("saveState");
		jq("#appLibViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
	
	// Listener to refresh the explorer
	jq(document).on("application.library.explorer.refresh", function() {
		jq(".libExplorer").trigger("reload");
	});
});