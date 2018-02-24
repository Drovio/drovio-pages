var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".navigationContent .bar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	
	// Show/hide back control
	jq(document).on("appcenter.navigation.showhide_back", function(ev, value) {
		if (value)
			jq(".navigationContent .bar .back, .navigationContent .bar .appInfo").addClass("active");
		else
			jq(".navigationContent .bar .back, .navigationContent .bar .appInfo").removeClass("active");
	});
	
	// Set href to appInfo link
	jq(document).on("appcenter.navigation.appinfo_href", function(ev, value) {
		// Set href attribute to app info link
		jq(".navigationContent .bar .appInfo").attr("href", value);
	});
	
	// Show all apps
	jq(document).on("click", ".navigationContent .bar .back", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Find all apps menu and click
		jq(".appCenterSidebar .smenu .mitem.featured").trigger("click");
	});
});