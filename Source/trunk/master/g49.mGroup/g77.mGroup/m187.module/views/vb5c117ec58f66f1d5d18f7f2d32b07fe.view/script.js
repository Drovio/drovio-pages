jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".projectHints .btn.dismiss", function() {
		// See if dismiss permanently
		var dismiss_permanent = jq(".projectHints .footer input[name='dismiss_permanent']:checked").length == 1;
		if (dismiss_permanent) {
			// Set local storage
			var projectID = jq(this).closest(".projectHints").data("pid");
			dlocalStorage.set("prdhcdp" + projectID, 1, false);
		}
			
		// Dismiss popup
		jq(this).trigger("dispose");
	});
});