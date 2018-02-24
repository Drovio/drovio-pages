jq(document).one("ready", function() {
	jq(document).on("click", "#SDKRefresh", function() {
		// Save state
		jq("#sdkExplorer").trigger("saveState");
		jq("#sdkExplorer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.sdkPackageViewer');
	});
	
	// Listener to refresh the explorer
	jq(document).on("core.sdk.explorer.refresh", function() {
		// Save state
		jq("#sdkExplorer").trigger("saveState");
		jq("#sdkExplorer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(".sdkPackageViewer").trigger("reload");
	});
});