jq(document).one("ready", function() {
	// Reload library
	jq(document).on("click", "#wTRefresh", function() {
		jq("#tplEplorerTree").trigger("saveState");
		jq(this).trigger("reload");
	});
});