jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".projectHints .hints .btn.dismiss", function() {
		// Dismiss popup
		jq(this).trigger("dispose");
	});
});