jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("dashboard.applications.updated", function(ev, updateInfo) {
		// Load app store on action
		var actionCallback = function() {
			// Click on app store
			jq(".appBox").each(function() {
				if (jq(this).data("app").id == 64)
					return jq(this).trigger("click");
			});

			// Dispose popup
			jq(this).trigger("dispose");
		}

		// Show notification
		pageNotification.show(jq(document), "enterprise-updates-checker", updateInfo.title, updateInfo.action_title, actionCallback, null);
	});
});