jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle commit details
	jq(document).on("click", ".projectReleases .releaseTile .show_changelog", function() {
		jq(this).closest(".projectReleases .releaseTile").toggleClass("open");
	});
});