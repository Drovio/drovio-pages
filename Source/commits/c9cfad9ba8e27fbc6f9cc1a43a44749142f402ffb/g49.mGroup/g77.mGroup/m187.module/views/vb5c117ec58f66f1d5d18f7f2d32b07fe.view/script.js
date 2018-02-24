jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".projectHints .btn.dismiss", function() {
		// See if dismiss permanently
		var dismiss_permanent = jq(".projectHints .footer input[name='dismiss_permanent']:checked").length == 1;
		if (dismiss_permanent) {
			// Get cookie value
			var cookieValue = cookies.get("prdhcdp");
			
			// Get project id
			var projectID = jq(this).closest(".projectHints").data("pid");
			
			// Add project to cookie and renew cookie
			var newCookieValue = (cookieValue == null ? "" : cookieValue+":")+projectID;
			cookies.set("prdhcdp", newCookieValue, 365, "/");
		}
			
		// Dismiss popup
		jq(this).trigger("dispose");
	});
});