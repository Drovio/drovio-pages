jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle object explorer
	jq(document).on("click", ".sdkMenuContainer .sdk-title", function() {
		jq(this).closest(".packageGroup").toggleClass("open");
	});
	
	// Select document and close sidebar
	jq(document).on("click", ".sdkMenu .menu-item", function() {
		jq(this).closest(".dev-domain").removeClass("ws");
	});
});