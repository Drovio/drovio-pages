jq(document).one("ready", function() {
	// Get reload project's action
	jq(document).on("rvProjects.reload", function() {
		jq("#pendingProjects").trigger("reload");
		jq("#reviewedProjects").trigger("reload");
	});
});