jq(document).one("ready", function() {
	jq(document).on("click", "#TRefresh", function() {
		// Save state
		jq("#tplEplorerTree").trigger("saveState");
		jq("#tplEplorerTree").find(".treeView").trigger("saveState");
		
		// Reload module
		jq(this).closest('.moduleContainer').trigger('reload');
	});
});