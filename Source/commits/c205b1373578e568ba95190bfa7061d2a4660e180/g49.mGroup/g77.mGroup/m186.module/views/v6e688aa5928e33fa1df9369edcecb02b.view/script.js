jq(document).one("ready", function() {
	
	// Update project status listener
	jq(document).on("project.updateStatus", function() {
		jq("#projectStatusModuleContainer").trigger("reload");
	});
});