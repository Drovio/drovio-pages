jq = jQuery.noConflict();
jq(document).one("ready.extra", function() {
	// Reload module
	jq(document).on("click", "#accRefresh", function() {
		jq(this).closest(".accountList").trigger("reload");
	});
});