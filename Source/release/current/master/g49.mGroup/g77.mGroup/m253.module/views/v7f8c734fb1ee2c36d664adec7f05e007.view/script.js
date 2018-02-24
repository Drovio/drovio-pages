jq(document).one("ready", function() {
	// Refresh scopes
	jq(document).on("click", ".scTool.refresh", function() {
		jq(this).trigger("reload");
	});
	
	// Click on the selected scope
	jq(document).on("content.modified", function() {
		// Find the init scope (if any)
		jq("li.sc.init").removeClass("init").trigger("click");
	});
});