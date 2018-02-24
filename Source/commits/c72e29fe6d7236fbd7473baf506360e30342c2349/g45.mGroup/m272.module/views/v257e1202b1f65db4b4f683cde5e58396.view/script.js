jq(document).one("ready", function() {
	jq(document).on("click", ".applicationUpdater .actions .dismiss", function() {
		// Close update info
		jq(this).closest(".appPlayerWrapper").removeClass("withStatus");
		jq(this).closest(".applicationUpdater").remove();
	});
	
	// Show new version details
	jq(document).on("click", ".applicationUpdater .actions .updtr.details", function() {
		// show popup of version details
		jq(this).popup.position = "bottom|center";
		jq(this).popup.withFade = true;
		jq(this).popup(jq(this).closest(".applicationUpdater").find(".newVerDetails").clone());
	});
});