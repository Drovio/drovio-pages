jq = jQuery.noConflict();
jq(document).one("ready.extra", function() {
	// Reload module
	jq(document).on("click", "#dbRefresh", function() {
		jq(".serverList").trigger("reload");
	});
});