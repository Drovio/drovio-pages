jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle commit details
	jq(document).on("click", ".vcsCommits .commitGroupContainer .cViewer .cvHeader", function() {
		jq(this).closest(".vcsCommits .commitGroupContainer .cViewer").toggleClass("open");
	});
});