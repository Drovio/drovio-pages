jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#refreshAjax", function() {
		jq("#ajaxPageExplorer").trigger("saveState");
		jq(".ajaxPageViewer").trigger("reload");
	});
});