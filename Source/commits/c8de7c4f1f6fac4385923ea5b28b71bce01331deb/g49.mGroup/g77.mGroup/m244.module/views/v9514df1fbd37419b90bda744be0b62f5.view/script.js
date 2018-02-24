jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Show project release review details
	jq(document).on("click", ".releaseRow .rv_button", function() {
		jq(this).closest(".releaseRow").toggleClass("review");
	});
});