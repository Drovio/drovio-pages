jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#ajaxRefresh", function() {
		jq("#ajaxPageExplorer").trigger("saveState");
		jq(".ajaxPageViewer").trigger("reload");
	});
});