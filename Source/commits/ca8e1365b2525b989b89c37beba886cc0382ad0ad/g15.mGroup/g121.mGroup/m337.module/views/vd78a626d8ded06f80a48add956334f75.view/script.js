jq = jQuery.noConflict();
jq(document).one("ready", function() {
console.log("ready");
	// Listen to page content type change
	jq(document).on("change", ".pageEditor select[name='ptype']", function() {
	console.log("change");
		console.log(jq(this).val());
		
		// Get ptype
		var ptype = jq(this).val();
		
		// Hide all content wrappers
		jq(".pageEditor .pageContentWrapper").removeClass("selected");
		jq(".pageEditor .pageContentWrapper."+ptype).addClass("selected");
	});
});