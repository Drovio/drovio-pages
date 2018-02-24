jq(document).one("ready", function() {
	jq(document).on("click", "#SRCRefresh", function() {
		reloadAppSourceExplorer();
	});
	
	// Listener to refresh the explorer
	jq(document).on("application.source.explorer.refresh", function() {
		reloadAppSourceExplorer();
	});
	
	// Set timeout to reload
	setInterval(function() {
		reloadAppSourceExplorer();
	}, 31000);
	
	function reloadAppSourceExplorer() {
		// Save state
		jq("#appPackageViewer").trigger("saveState");
		jq("#appPackageViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq('.packageExplorer').trigger('reload');
	}
});