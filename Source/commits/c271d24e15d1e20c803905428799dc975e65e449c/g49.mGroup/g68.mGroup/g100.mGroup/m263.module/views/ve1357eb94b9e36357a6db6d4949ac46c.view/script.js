jq(document).one("ready", function() {
	jq(document).on("click", "#SRCRefresh", function() {
		// Save state
		jq("#appPackageViewer").trigger("saveState");
		jq("#appPackageViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
});