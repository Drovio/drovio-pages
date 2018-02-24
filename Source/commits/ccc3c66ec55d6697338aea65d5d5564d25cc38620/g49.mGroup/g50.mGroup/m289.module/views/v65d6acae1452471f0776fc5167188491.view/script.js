jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle doc group
	jq(document).on("click", ".doc_group .navHeader .ico", function() {
		jq(this).closest(".doc_group").toggleClass("open");
	});
});