jq(document).one("ready", function() {
	// Refresh query viewer
	jq(document).on("click", "#ltRefresh", function() {
		jq("#layoutTabber").trigger("saveState");
		jq(".layoutExplorer").trigger("reload");
	});
});