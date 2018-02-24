jq = jQuery.noConflict();
jq(document).one("ready", function() {
	function checkAdHeight()
	{
		// Get sidebar height
		var sidebaTotalHeight = jq(".appCenterSidebar").height();
		var sideMenuHeight = jq(".appCenterSidebar .sideMenuContainer").height();
		var adContainerHeight = jq(".appCenterSidebar .adContainer").height();
		if (sidebaTotalHeight - sideMenuHeight < adContainerHeight)
			jq(".appCenterSidebar .adContainer").addClass("hidden");
		else
			jq(".appCenterSidebar .adContainer").removeClass("hidden");
	}
	checkAdHeight();
	window.onresize = function() {
		checkAdHeight();
	};
	
	function removeBlockedAd() {
		// Detect whether the google ads loaded the frame and remove the ad container (adBlock)
		if (jq(".appCenterSidebar .adContainer").find("iframe").length == 0) {
			// Remove ad container
			jq(".appCenterSidebar .adContainer").remove();
		}
	}
	
	// When the page is loaded
	jq(document).on("content.modified", function() {
		removeBlockedAd();
	});
});