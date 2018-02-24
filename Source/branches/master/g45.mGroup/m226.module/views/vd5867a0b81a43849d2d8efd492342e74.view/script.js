jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Init application
	jq(".appBox.init").trigger("click");
	
	// Check for updates
	if (jq(".appsGrid").data("updates") != undefined) {
		// Show notification
		setTimeout(function() {
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
			pageNotification.show(jq(document), "enterprise-updates", jq(".appsGrid").data("updates").title, jq(".appsGrid").data("updates").action_title, actionCallback, null);
		}, 2000);
	}
	
	// Check for updates every 2 minutes
	setInterval(function() {
		// Reload application updater
		jq("#app_updater_container").trigger("reload");
	}, 2 * 60 * 1000);
});