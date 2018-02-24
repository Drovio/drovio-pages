var jq=jQuery.noConflict();

var advanceStep = function(stepId){
	// Go to progress tracker visualization and
	// marked current step as complete and
	// next step as pending
	jq('.progressHolder').removeClass('noDisplay');

	jq('.progressHolder .step[data-sid="'+stepId+'"]').addClass('done');

	console.log('Going to next step');
};

// let the document load
jq(document).one("ready.extra", function() {
	jq(document).off("website.publish.commit");
	jq(document).on("website.publish.commit", function(odj, params) {
		advanceStep("commit");
		jq('#repoManager form').trigger('submit');
		
	});
	jq(document).off("website.publish.release");
	jq(document).on("website.publish.release", function(odj, params) {
		advanceStep("release");
		jq('#projectReleaser form').trigger('submit');
		
	});
	jq(document).off("website.publish.upload");
	jq(document).on("website.publish.upload", function(odj, params) {
		advanceStep("upload");
		console.log('Site Uploading Is disabled');
		jq('#websiteUploader form').trigger('submit');
		
	});
	
	jq(document).off("website.publish.finish");
	jq(document).on("website.publish.finish", function(odj, params) {
		advanceStep("finish");
	});
	
});