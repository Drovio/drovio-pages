jq(document).one("ready.extra", function() {
	jq(document).on("click", ".newProjectWizard .pt", function() {
		// Set selected
		jq(".newProjectWizard .pt").removeClass("selected");
		jq(this).addClass("selected");
		
		// Activate next button
		jq(".nextBtn").removeClass("disabled").removeAttr("disabled");
		
		// Set next slide visible
		jq(".projectDetails").removeClass("noDisplay");
		jq("#submitBtn").removeClass("noDisplay");
		if (jq(this).hasClass("w")) {
			jq(".projectDetails.project").addClass("noDisplay");
			jq("#submitBtn").addClass("noDisplay");
		}
		else
			jq(".projectDetails.website").addClass("noDisplay");
	});
	
	jq(document).on("click", ".nextBtn", function() {
		jq(".slide1").addClass("noDisplay");
		jq(".slide2").removeClass("noDisplay");
	});
	
	jq(document).on("click", ".backBtn", function() {
		jq(".slide1").removeClass("noDisplay");
		jq(".slide2").addClass("noDisplay");
	});
});