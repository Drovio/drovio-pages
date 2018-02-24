var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".developerDashboard .navBar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	
	// Set trigger click
	jq(document).on("content.modified", function() {
		// Get target and select projects
		var target = jq(".selectionList .listContent.selected").data("target");
		selectBoxes(target);
	});
	
	// Set selection click listener
	jq(document).on("click", ".selectionList .listContent:not(.new_bundle)", function() {
		// Get target and select projects
		var target = jq(this).data("target");
		selectBoxes(target);
	});
	
	// Select project boxes according to given target (from the side menu)
	function selectBoxes(target) {
		// Set all projectBoxes as noDisplay
		jq(".developerDashboard .mainContainer .projectBox").addClass("noDisplay");
		
		// Show all targets
		jq(".developerDashboard .mainContainer .projectBox."+target).removeClass("noDisplay");
	};
	
	
	// Search projects
	jq(document).on("keyup", ".developerDashboard .searchContainer .searchInput", function(ev) {
		// Get input and search projects
		var search = jq(this).val();
		searchProjects(search);
	});
	
	// Enable search
	jq(document).on("focusin", ".developerDashboard .searchContainer .searchInput", function(ev) {
		// Get input and search projects
		var search = jq(this).val();
		searchProjects(search);
	});
	
	// Search all projects
	function searchProjects(search) {
		// If search is empty, show all projects
		if (search == "") {
			// Show all boxes
			jq(".developerDashboard .mainContainer .projectBox").removeClass("noDisplay");
			
			// Click sidenav selected item
			jq(".developerDashboard .sidebar .listContent.selected").trigger("click");
			return;
		}
		
		// Hide all boxes
		jq(".developerDashboard .mainContainer .projectBox").addClass("noDisplay");
		
		// Filter all boxes
		jq(".developerDashboard .mainContainer .projectBox:not(.new) .projectTitle:contains("+search+")").closest(".projectBox").removeClass("noDisplay");
	}
});