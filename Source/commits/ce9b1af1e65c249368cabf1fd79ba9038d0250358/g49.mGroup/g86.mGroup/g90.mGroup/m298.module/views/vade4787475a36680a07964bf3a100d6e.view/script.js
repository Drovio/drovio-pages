jq(document).one("ready", function() {
	jq(document).on("click", ".dstTool.refresh", function() {
		jq(this).trigger("reload");
	});
});