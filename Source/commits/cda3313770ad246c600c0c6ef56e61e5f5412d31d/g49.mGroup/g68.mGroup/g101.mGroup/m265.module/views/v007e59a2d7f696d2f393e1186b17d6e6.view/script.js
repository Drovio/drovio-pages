jq(document).one("ready", function() {
	jq(document).on("click", "#LRefresh", function() {
		reloadAppLibraryExplorer();
	});
	
	// Listener to refresh the explorer
	jq(document).on("application.library.explorer.refresh", function() {
		reloadAppLibraryExplorer();
	});
	
	function reloadAppLibraryExplorer() {
		// Save state
		jq("#appLibViewer").trigger("saveState");
		jq("#appLibViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq('.libExplorer').trigger('reload');
	}
});