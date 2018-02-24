var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".adminHome .admin-navbar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
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
		var targetID = "admin_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("load");
	});
	
	// Reload content
	jq(document).on("click", ".menuItem .reload", function(ev) {
		// Reload module
		var targetID = "admin_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("reload");
	});
});