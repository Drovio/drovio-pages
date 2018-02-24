jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#QRefresh", function() {
		jq("#dbQueriesTree").trigger("saveState");
		jq(".queryViewer").trigger("reload");
	});
});