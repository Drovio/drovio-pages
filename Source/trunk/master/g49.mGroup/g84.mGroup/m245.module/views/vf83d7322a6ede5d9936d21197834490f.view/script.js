jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle console dependencies
	jq(document).on("click", ".consoleTool.settings", function() {
		jq(".coreTester .console .headers").toggleClass("noDisplay");
	});
});