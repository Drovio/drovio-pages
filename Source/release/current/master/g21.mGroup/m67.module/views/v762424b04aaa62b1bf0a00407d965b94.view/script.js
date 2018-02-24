jq = jQuery.noConflict();
jq(document).one("ready", function() {
	
	// Show locale dialog
	jq(document).on("click", ".pgFooter a.locale", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Load ajax geoloc dialog
		var geolocUrl = "/ajax/global/geoloc.php";
		HTMLServerReport.request(geolocUrl, "GET", null, jq(this), null, null, true, null);
	});
});