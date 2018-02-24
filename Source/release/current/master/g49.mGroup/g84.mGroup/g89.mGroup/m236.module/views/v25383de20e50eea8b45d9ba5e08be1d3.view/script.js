jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#ajaxRefresh", function() {
		jq("#ajaxPageExplorer").trigger("saveState");
		jq(".ajaxPageViewer").trigger("reload");
	});
	
	// Listener to refresh the explorer
	jq(document).on("core.ajax.explorer.refresh", function() {
		jq("#ajaxPageExplorer").trigger("saveState");
		jq(".ajaxPageViewer").trigger("reload");
	});
});