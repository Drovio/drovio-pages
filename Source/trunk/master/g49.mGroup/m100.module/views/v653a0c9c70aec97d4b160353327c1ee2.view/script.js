jq = jQuery.noConflict();
jq(document).one("ready", function() {

	jq(document).on("click", "[data-tid]", function() {
		// Get team id
		var teamID = jq(this).data("tid");
		selectProjects(teamID);
	});
	
	jq(document).on("content.modified", function() {
		var teamID = jq("[data-tid].selected").data("tid");
		selectProjects(teamID);
	});
	
	function selectProjects(teamID) {
		// Set all team containers display none
		jq(".tcontainer").addClass("noDisplay");
		
		// Display only the selected team
		jq(".titem").removeClass("selected");
		jq(".titem.tid"+teamID).addClass("selected");
		jq(".tcontainer.tid_"+teamID).removeClass("noDisplay");
	}
});