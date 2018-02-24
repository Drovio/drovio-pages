jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#refreshQueries", function() {
		jq("#dbQueriesTree").trigger("saveState");
		jq("#sqlQueryViewer").trigger("reload");
	});
});