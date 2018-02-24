jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle object explorer
	jq(document).on("click", ".sdkMenuContainer .sdk-title", function() {
		jq(this).closest(".packageGroup").toggleClass("open");
	});
});