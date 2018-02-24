jq(document).one("ready", function() {
	
	// Toggle settings on server configuration
	jq(document).on("click", ".wsOverview .status .boxStatus.server .configure", function() {
		jq("#settingControl").trigger("click");
	});
});