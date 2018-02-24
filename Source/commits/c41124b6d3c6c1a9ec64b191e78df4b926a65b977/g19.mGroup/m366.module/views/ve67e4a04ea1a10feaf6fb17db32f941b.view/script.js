var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".teamSelector .close_btn", function() {
		jq(this).trigger("dispose");
	});
});