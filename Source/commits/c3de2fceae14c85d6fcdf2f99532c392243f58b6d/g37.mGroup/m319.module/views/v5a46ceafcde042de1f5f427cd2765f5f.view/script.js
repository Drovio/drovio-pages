jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".loginPopup .ricnt label", function() {
		// Set selected
		jq(".loginPopup .rocnt").removeClass("selected");
		jq(this).closest(".rocnt").addClass("selected");
		var id = jq(this).closest(".rocnt").attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
	
	jq(document).on("mouseenter", ".loginPopup .ricnt label", function() {;
		// Get id
		var id = jq(this).closest(".rocnt").attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
	
	jq(document).on("mouseleave", ".loginPopup .ricnt label", function() {
		// Reset selected
		var rcnts = jq(".loginPopup .rocnt.selected");
		var id = rcnts.attr("id");
		
		// Set notes selected
		jq(".rnotes .nt").removeClass("selected");
		jq(".nt."+id).addClass("selected");
	});
	
	// Close popup
	jq(document).on("click", ".loginPopup .header .close", function() {
		jq(this).trigger("dispose");
	});
});