jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Get edit click
	jq(document).on("click", ".managedAccounts .srow .edit", function() {
		// Toggle open class
		var srow = jq(this).closest(".srow");
		srow.toggleClass("open");
		
		// Load module
		if (srow.hasClass("open"))
			srow.find(".sbody .sContainer").trigger("load");
	});
});