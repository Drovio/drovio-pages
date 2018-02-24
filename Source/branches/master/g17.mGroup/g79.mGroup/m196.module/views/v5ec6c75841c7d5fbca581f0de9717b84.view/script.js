var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".webDesigner .website-navbar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	
	// Sidebar navigation
	jq(document).on("click", ".webDesigner .website-sidebar .webMenu .menuItem a", function(ev) {
		// Prevent default action
		ev.preventDefault();

		// Set state (if state is the same return)
		var stateHref = jq(this).attr("href");
		if (window.location.pathname == stateHref)
			return;
		state.push(stateHref);
		
		// Load module
		var targetID = "web_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("load");
	});
	
	// Reload content
	jq(document).on("click", ".webDesigner .website-sidebar .webMenu .menuItem .reload", function(ev) {
		// Reload module
		var targetID = "web_"+jq(this).closest(".menuItem").data("ref");
		jq("#"+targetID).trigger("reload");
	});
});