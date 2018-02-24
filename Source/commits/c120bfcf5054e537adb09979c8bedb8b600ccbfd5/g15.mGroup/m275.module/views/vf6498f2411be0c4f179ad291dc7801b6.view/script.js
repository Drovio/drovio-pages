jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Show project release review details
	jq(document).on("click", ".project_tile .rv_button", function() {
		jq(this).closest(".project_tile").toggleClass("review");
	});
	
	// Reload projects
	jq(document).on("rvProjects.reload", function() {
		jq(".marketReviewManager").trigger("reload");
	});
});