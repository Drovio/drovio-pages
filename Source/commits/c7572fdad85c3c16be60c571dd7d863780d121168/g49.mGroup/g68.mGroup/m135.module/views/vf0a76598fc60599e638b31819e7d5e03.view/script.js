jq(document).one("ready", function() {
	// Refresh app explorer
	jq(document).on("click", "#refreshApp", function() {
		jq("#AppExplorerTree").trigger("saveState");
		jq("#appSectionViewer").trigger("reload");
	});
});