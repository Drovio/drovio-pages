jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listen to page content type change
	jq(document).on("change", ".pageEditor select[name='ptype']", function() {
		// Get ptype
		var ptype = jq(this).val();
		
		// Hide all content wrappers
		jq(".pageEditor .pageContentWrapper").removeClass("selected");
		jq(".pageEditor .pageContentWrapper."+ptype).addClass("selected");
	});
});