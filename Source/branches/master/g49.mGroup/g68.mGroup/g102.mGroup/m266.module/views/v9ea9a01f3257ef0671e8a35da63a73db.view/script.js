jq(document).one("ready", function() {
	// Manual refresh of the view explorer
	jq(document).on("click", "#VRefresh", function() {
		reloadAppViewExplorer();
	});
	
	// Listener to refresh the explorer
	jq(document).on("application.views.explorer.refresh", function() {
		reloadAppViewExplorer();
	});
	
	function reloadAppViewExplorer() {
		// Save state
		jq("#appViewsViewer").trigger("saveState");
		jq("#appViewsViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq('.viewExplorer').trigger('reload');
	}
});