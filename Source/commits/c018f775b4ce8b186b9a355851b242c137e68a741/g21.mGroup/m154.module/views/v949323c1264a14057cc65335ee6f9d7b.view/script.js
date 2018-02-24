jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Wait for the toolbar to load
	jq(document).on("account.toolbar.loaded", function() {
		// Check if there is an active view panel
		if (jq(".accountToolbarInfo .viewPanel.edit.active").length > 0) {
			// Enable view panel
			jq(".accountToolbarInfo .viewPanel").removeClass("open");
			jq(".accountToolbarInfo .viewPanel.edit.active").first().addClass("open");

			// Trigger click to open the popup
			setTimeout(function() {
				jq(".tlbNavItem.userStarter").trigger("click");
			}, 1000);
		}
		
		// On cancel button
		jq(document).on("click", ".bbtn.skip", function(ev) {
			// Stop bubbling
			ev.stopPropagation();
			
			// Go to next step
			jq(document).trigger("account.toolbar.step.next");
		});
	});
	
	// Next step
	jq(document).on("account.toolbar.step.next", function() {
		// Get current active step and close
		jq(".accountToolbarInfo .viewPanel.active.open").remove();
		
		// Set active the next step
		jq(".accountToolbarInfo").each(function() {
			jq(this).find(".viewPanel.active").first().addClass("open");
		});
	});
	
	// Reload accont toolbar
	jq(document).on("account.toolbar.reload", function() {
		// Get current active step and close
		jq(".accountToolbarInfo").trigger("reload");
	});
});