jq(document).one("ready", function() {
	// Update project status listener
	jq(document).on("project.updateStatus", function(ev, status) {

		// Set project status
		var projectStatus = jq(".projectStatusContainer .projectStatus");
		projectStatus.removeClass("online").removeClass("offline");
		
		if (status)
			projectStatus.addClass("online");
		else
			projectStatus.addClass("offline");
	});
});