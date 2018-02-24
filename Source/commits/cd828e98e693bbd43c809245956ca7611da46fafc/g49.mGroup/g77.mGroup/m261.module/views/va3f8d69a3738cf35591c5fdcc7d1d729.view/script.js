jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle repository release container
	jq(document).on("click", ".projectPublisherContainer .repoReleaseContainer .hd", function() {
		// Toggle open class
		jq(this).closest(".repoReleaseContainer").toggleClass("open");
	});
	
	// Toggle new release inputs
	jq(document).on("change", ".projectPublisherContainer .repoReleaseContainer input[name='repo_release']", function() {
		if (jq(this).val() == 1) {
			// Enable new release inputs and disable package
			jq("select[name='repo_package']").prop("disabled", true).addClass("disabled");
			jq(".projectPublisherContainer .repoReleaseContainer .newReleaseContainer").find("input, select").prop("disabled", false).removeClass("disabled");
		}
		else {
			// Enable package and disable new release inputs
			jq("select[name='repo_package']").prop("disabled", false).removeClass("disabled");
			jq(".projectPublisherContainer .repoReleaseContainer .newReleaseContainer").find("input, select").prop("disabled", true).addClass("disabled");
		}
	});
	
	// Set progress title
	jq(document).on("prj.publisher.addStatusTitle", function(ev, status) {
		// Clear initial publisher
		jq(".projectPublisherContainer .publisher").closest("form").remove();
		
		// Clone title and add one to tlist
		var jqTitle = jq(".projectPublisherContainer .progressHolder .tlist .title.template").clone(true).removeClass("template");
		jqTitle.append(jq("<span>"+status+"</span>"));
		jq(".projectPublisherContainer .progressHolder .tlist").append(jqTitle);
	});
	
	// Error occurred
	jq(document).on("prj.publisher.error", function(ev, step) {
		// Set step title as error
		jq(".projectPublisherContainer .progressHolder .tlist .title:not(.template)").eq(step-1).addClass("error");
		
		// Set progress bar as error
		var progress = 100;
		jq(".projectPublisherContainer .progressHolder .progressbar").addClass("error");
	});
	
	// Set step ok and proceed to next form
	jq(document).on("prj.publisher.setStep", function(ev, step) {
		// Set progress bar
		var progress = (step / 4) * 100;
		jq(".projectPublisherContainer .progressHolder .progressbar").animate({
			width: progress+"%"
		}, 200);
	});
	
	// Set step ok and proceed to next form
	jq(document).on("prj.publisher.stepOK", function(ev, step) {
		// Set step non-template title as ok
		jq(".projectPublisherContainer .progressHolder .tlist .title:not(.template)").eq(step-1).addClass("ok");
		
		// Submit form
		jq(".projectPublisherContainer .formsHolder form").trigger("submit");
	});
});