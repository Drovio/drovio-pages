jq(document).one("ready", function() {
	// Reload library
	jq(document).on("click", "#libRefresh", function() {
		jq("#wsPagesLib").trigger("saveState");
		jq(this).trigger("reload");
	});
});