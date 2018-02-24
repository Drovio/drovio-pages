jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#QRefresh", function() {
		jq("#dbQueriesTree").trigger("saveState");
		jq(".sqlQueryViewer").trigger("reload");
	});
	
	// Listener to refresh the explorer
	jq(document).on("core.sql.explorer.refresh", function() {
		jq("#dbQueriesTree").trigger("saveState");
		jq(".sqlQueryViewer").trigger("reload");
	});
});