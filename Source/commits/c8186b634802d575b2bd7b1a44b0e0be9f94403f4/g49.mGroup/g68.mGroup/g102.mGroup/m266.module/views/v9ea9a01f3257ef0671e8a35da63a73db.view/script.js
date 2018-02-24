jq(document).one("ready", function() {
	jq(document).on("click", "#VRefresh", function() {
		// Save state
		jq("#appViewsViewer").trigger("saveState");
		jq("#appViewsViewer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
});