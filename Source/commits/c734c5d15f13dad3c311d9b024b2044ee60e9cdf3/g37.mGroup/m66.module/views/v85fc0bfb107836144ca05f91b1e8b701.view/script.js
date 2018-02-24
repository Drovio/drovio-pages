jq = jQuery.noConflict();

jq(document).one("ready", function() {
	jq(document).on("click", ".loginPage .whiteBox .ricnt label", function() {
		// Set selected
		jq(".loginPage .whiteBox .rocnt").removeClass("selected");
		jq(this).closest(".rocnt").addClass("selected");
		var id = jq(this).closest(".rocnt").attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
	
	jq(document).on("mouseenter", ".loginPage .whiteBox .ricnt label", function() {;
		// Get id
		var id = jq(this).closest(".rocnt").attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
	
	jq(document).on("mouseleave", ".loginPage .whiteBox .ricnt label", function() {
		// Reset selected
		var rcnts = jq(".loginPage .whiteBox .rocnt.selected");
		var id = rcnts.attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
});