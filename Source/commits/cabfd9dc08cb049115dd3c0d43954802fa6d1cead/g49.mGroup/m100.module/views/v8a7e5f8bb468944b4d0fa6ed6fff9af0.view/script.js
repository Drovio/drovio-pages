jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Search items and display explorer
	jq(document).on("keyup", ".dev-sidebar-container .sidebar .searchInput", function(ev) {
		// Get input
		var search = jq(this).val();
		searchSidebar(search);
	});
	
	// Enable search
	jq(document).on("focusin", ".dev-sidebar-container .sidebar .searchInput", function(ev) {
		var search = jq(this).val();
		searchSidebar(search);
	});
	
	function searchSidebar(search) {
		// If search is empty, show all rows
		if (search == "" || search.length < 2) {
			jq(".dev-sidebar-container .sidebar").find(".sd-title").show();
			jq(".dev-sidebar-container .sidebar").find(".menu-item").show();
			// Close package groups
			jq(".dev-sidebar-container .sidebar .menuContainer.packageGroup").removeClass("open");
			return;
		}
		
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Hide all items
		jq(".dev-sidebar-container .sidebar").find(".sd-title").hide();
		jq(".dev-sidebar-container .sidebar").find(".menu-item").hide();
		// Close package groups
		jq(".dev-sidebar-container .sidebar .menuContainer.packageGroup").removeClass("open");
		
		// Select all menu items
		jq(".dev-sidebar-container .sidebar").find(".menu-item").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).show();
			jq(this).closest(".menuContainer").addClass("open");
			jq(this).closest(".menuContainer").find(".sd-title").show();
		});
	}
});