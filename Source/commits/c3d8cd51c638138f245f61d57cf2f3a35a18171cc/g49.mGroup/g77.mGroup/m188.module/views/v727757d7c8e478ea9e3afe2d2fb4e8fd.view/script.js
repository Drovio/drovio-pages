jq = jQuery.noConflict();
jq(document).one("ready.extra", function() {
	// Toggle commit details
	jq(document).on("click", ".vcsCommits .commitGroupContainer .cViewer .cvHeader", function() {
		jq(this).closest(".vcsCommits .commitGroupContainer .cViewer").toggleClass("open");
	});
});

// Add containers for name resolving
moduleGroup.addContainer(".vcsCommitsContainer");
module.addContainer(".vcsCommitsContainer");
sqlDomain.addContainer(".vcsCommitsContainer");

// Re-trigger in case of missing content
jq(document).trigger("content.modified");