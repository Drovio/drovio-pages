jq = jQuery.noConflict();
jq(document).one("ready.extra", function() {
	// Reload module
	jq(document).on("click", "#dbRefresh", function() {
		jq(this).closest(".serverList").trigger("reload");
	});
});