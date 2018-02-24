jq(document).one("ready", function() {

	jq(document).on("application.loadResources", function() {
		// Load application resources
		var appID = url.getVar("id");
		var appName = url.getVar("name");
		
		// Get resource id
		var rsrcID = "app_rsrc_"+appID+"_"+appName;
		
		// Set resource attributes
		var attributes = new Object();
		attributes.category = "Application";
		attributes.id = appID;
		attributes.name = appName;
		
		// Load css and js
		BootLoader.loadResource(rsrcID, "css", "/ajax/apps/css.php", attributes, null, false);
		BootLoader.loadResource(rsrcID, "js", "/ajax/apps/js.php", attributes, null, false);
	});
});