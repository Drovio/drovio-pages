jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Init application
	jq(".appBox.init").trigger("click");
	
	// Click for updates
	jq(document).on("click", ".ntf_updates .updates_action", function() {
		// Initialize app store application
		jq(".appBox").each(function() {
			if (jq(this).data("app").id == 64)
				return jq(this).trigger("click");
		});
	});
});