jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Push url state
	jq(document).on("click", ".enp-appPlayerWrapper .enp-dashboard", function(ev) {
		// Click on the dashboard icon
		jq(".ds-navbar .navitem.apps").trigger("click");
	});
});