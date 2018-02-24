jq(document).one("ready", function() {
	jq(document).on("click", ".scTool.refresh", function() {
		jq(this).trigger("reload");
	});
});