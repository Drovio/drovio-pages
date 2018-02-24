jq = jQuery.noConflict();
jq(document).one("ready", function() {

	jq(document).on("click", "[data-tid]", function() {
		// Get team id
		var teamID = jq(this).data("tid");
		
		// Set all team containers display none
		jq(".tcontainer").addClass("noDisplay");
		
		// Display only the selected team
		jq(".titem").removeClass("selected");
		jq(".titem.tid"+teamID).addClass("selected");
		jq(".tcontainer.tid_"+teamID).removeClass("noDisplay");
	});
	
	jq(document).on("content.modified", function() {
		jq("[data-tid].selected").trigger("click");
	});
});