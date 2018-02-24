var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".projectHome .project-navbar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	// Update project status listener
	jq(document).on("project.updateStatus", function(ev, status) {
		// Set project status
		var projectStatus = jq(".projectStatusContainer .projectStatus");
		projectStatus.removeClass("online").removeClass("offline");
		// Add class according to status
		var classToAdd = (status ? "online" : "offline");
		projectStatus.addClass(classToAdd);
	});
	
	// Sidebar navigation
	jq(document).on("click", ".menuItem a", function(ev) {
		// Allow designer to redirect
		if (jq(this).closest(".menuItem").hasClass("designer"))
			return;
			
		// Prevent default action
		ev.preventDefault();

		// Set state (if state is the same return)
		var stateHref = jq(this).attr("href");
		if (window.location.pathname == stateHref)
			return;
		state.push(stateHref);
		
		// Load module
		var targetID = "prj_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("load");
	});
	
	// Reload content
	jq(document).on("click", ".menuItem .reload", function(ev) {
		// Reload module
		var targetID = "prj_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("reload");
	});
});