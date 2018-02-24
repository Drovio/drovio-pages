jq(document).one("ready", function() {

	// Load view on content.modified
	jq(document).on("start.application", function() {
		// Load application resources
		var appID = url.getVar("id");
		
		// Get resource id
		var rsrcID = "app_rsrc_"+appID;
		
		// Set resource attributes
		var attributes = new Object();
		attributes.category = "Application";
		attributes.appID = appID;
		
		// Load css
		if (!BootLoader.checkLoaded(rsrcID, "css")) {
			var href = ajaxTester.resolve("/ajax/apps/css.php?id="+appID);
			BootLoader.loadCSS(href);
			var resource = BootLoader.addResource(rsrcID, "css", href, attributes, false);
		}
		
		// Load application view (with a little timeout to view the loading)
		setTimeout(loadView, 800);
		function loadView() {
			var jqSender = jq("#applicationContainer");
			
			// Module Loader
			var moduleLoaderCallback = function(report) {
				// Parse report like a module
				ModuleProtocol.handleReport(jqSender, report, "");
				
				// Load js
				if (!BootLoader.checkLoaded(rsrcID, "js")) {
					var href = ajaxTester.resolve("/ajax/apps/js.php?id="+appID);
					BootLoader.loadJS(href);
					var resource = BootLoader.addResource(rsrcID, "js", href, attributes, false);
				}
			}
			
			// AsCoP Request
			var appID = url.getVar("id");
			var requestParams = "id="+appID;
			ascop.asyncRequest("/ajax/apps/tester.php", "GET", requestParams, "json", jqSender, moduleLoaderCallback, null, false);
		}
	});
});