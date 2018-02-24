jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload module
	jq(document).on("click", "#mfRefresh", function() {
		jq(this).closest(".mfExplorerContainer").trigger("reload");
	});
});