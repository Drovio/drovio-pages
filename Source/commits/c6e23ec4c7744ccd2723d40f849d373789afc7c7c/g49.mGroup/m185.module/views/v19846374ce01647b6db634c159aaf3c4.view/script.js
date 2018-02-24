var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle sidebar listener
	jq(document).on("click", ".developerDashboard .navBar .sideNav .icon", function() {
		jq(this).closest(".sidebarContainer").toggleClass("openSidebar");
	});
	
	// Set trigger click
	jq(document).on("content.modified", function() {
		// Get target
		var target = jq(".selectionList .listContent.selected").data("target");
		
		// Select boxes
		selectBoxes(target);
	});
	
	// Set selection click listener
	jq(document).on("click", ".selectionList .listContent:not(.new_bundle)", function() {
		// Get target
		var target = jq(this).data("target");
		
		// Select boxes
		selectBoxes(target);
	});
	
	function selectBoxes(target) {
		// Set all projectBoxes as noDisplay
		jq(".projectList .projectBox").addClass("noDisplay");
		
		// Show all targets
		jq(".projectList .projectBox"+"."+target).removeClass("noDisplay");
	};
});