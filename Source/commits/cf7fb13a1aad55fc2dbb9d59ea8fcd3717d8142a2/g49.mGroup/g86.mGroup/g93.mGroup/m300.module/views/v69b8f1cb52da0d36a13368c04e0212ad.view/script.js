jq(document).one("ready", function() {
	// Refresh app explorer
	jq(document).on("click", "#refreshExt", function() {
		jq("#ExtExplorerTree").trigger("saveState");
		jq("#extSectionViewer").trigger("reload");
	});
});