var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".navigationContent .bar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	
	// Show/hide back control
	jq(document).on("appcenter.navigation.showhide_back", function(ev, value) {
		if (value)
			jq(".navigationContent .bar .back").addClass("active");
		else
			jq(".navigationContent .bar .back").removeClass("active");
	});
	
	// Show all apps
	jq(document).on("click", ".navigationContent .bar .back", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Find all apps menu and click
		jq(".appCenterSidebar .smenu .mitem.all_apps").trigger("click");
	});
});