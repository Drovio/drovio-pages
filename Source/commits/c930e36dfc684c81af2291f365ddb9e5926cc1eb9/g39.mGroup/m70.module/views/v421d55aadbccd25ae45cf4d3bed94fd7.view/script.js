jq(document).one("ready", function() {
	jq(document).on("click", ".tileList .tile", function() {
		// Get reference
		var ref = jq(this).attr("for");
		
		// Set tile active
		jq(".tileList .tile").removeClass("active");
		jq(this).addClass("active");
		
		// Set slide active
		jq(".slides .slide").removeClass("active");
		jq(".slide."+ref).addClass("active");
	});
});