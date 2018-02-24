jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Init application
	jq(".appBox.init").trigger("click");
});