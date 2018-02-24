jq(document).one("ready", function() {
	jq(document).on("click", "#refreshSDK", function() {
		// Save state
		jq(".webSDKExplorer").trigger("saveState");
		jq(".webSDKExplorer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
});