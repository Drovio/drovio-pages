var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload roadmap
	jq(document).on("roadmap.list.reload", function() {
		jq("#roadmapList").trigger("reload");
	});
});