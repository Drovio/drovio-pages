jq(document).one("ready", function() {
	jq(document).on("click", "#SourceRefresh", function() {
		// Save state
		jq("#sourceExplorer").trigger("saveState");
		jq("#sourceExplorer").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.srcPackageExplorer').trigger('reload');
	});
});