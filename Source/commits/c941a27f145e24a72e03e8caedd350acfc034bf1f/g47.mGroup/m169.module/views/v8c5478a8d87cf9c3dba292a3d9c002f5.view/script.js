jq = jQuery.noConflict();
jq(document).one("ready", function() {
	function removeBlockedAd() {
		// Detect whether the google ads loaded the frame and remove the ad container (adBlock)
		if (jq(".applicationPlayer .adContainer").find("iframe").length == 0) {
			// Remove class for ads
			jq(".applicationPlayer").removeClass("with_ad");
			// Remove ad container
			jq(".applicationPlayer .adContainer").remove();
		}
	}
	// When application is running
	jq(document).on("content.modified", function() {
		//removeBlockedAd();
	});
});