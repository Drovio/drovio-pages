jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle advanced settings
	jq(document).on("click", ".wsPublisher .advancedSettingsContainer .hd", function() {
		// Toggle open class
		jq(this).closest(".advancedSettingsContainer").toggleClass("open");
	});
	
	// Set progress title
	jq(document).on("website.addStatusTitle", function(ev, status) {
		// Clear initial publisher
		jq(".wsPublisher .publisher").closest("form").remove();
		
		// Clone title and add one to tlist
		var jqTitle = jq(".wsPublisher .progressHolder .tlist .title.template").clone(true).removeClass("template");
		jqTitle.append(jq("<span>"+status+"</span>"));
		jq(".wsPublisher .progressHolder .tlist").append(jqTitle);
	});
	
	// Error occurred
	jq(document).on("website.error", function(ev, step) {
		// Set step title as error
		jq(".wsPublisher .progressHolder .tlist .title:not(.template)").eq(step-1).addClass("error");
		
		// Set progress bar as error
		var progress = 100;
		jq(".wsPublisher .progressHolder .progressbar").addClass("error");
	});
	
	// Set step ok and proceed to next form
	jq(document).on("website.setStep", function(ev, step) {
		// Set progress bar
		var progress = (step / 4) * 100;
		jq(".wsPublisher .progressHolder .progressbar").animate({
			width: progress+"%"
		}, 200);
	});
	
	// Set step ok and proceed to next form
	jq(document).on("website.stepOK", function(ev, step) {
		// Set step non-template title as ok
		jq(".wsPublisher .progressHolder .tlist .title:not(.template)").eq(step-1).addClass("ok");
		
		// Submit form
		jq(".wsPublisher .formsHolder form").trigger("submit");
	});
	
	/*
	// Process completed
	jq(document).on("website.completed", function(ev) {
		// Set all titles as ok
		jq(".wsPublisher .progressHolder .tlist .title:not(.template)").addClass("ok");
		
		// Set progress bar to 100
		var progress = 100;
		jq(".wsPublisher .progressHolder .progressbar").animate({
			width: progress+"%"
		}, 200);
	});*/
});