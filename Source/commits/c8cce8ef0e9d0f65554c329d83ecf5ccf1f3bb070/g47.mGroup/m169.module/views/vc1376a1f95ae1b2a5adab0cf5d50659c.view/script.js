jq(document).one("ready", function() {

	var cssHref = "";
	var jsHref = "";
	
	// Get application css url
	jq(document).on("css.application", function(ev, value) {
		cssHref = value;
	});
	
	// Get application css url
	jq(document).on("js.application", function(ev, value) {
		jsHref = value;
	});
	
	// Load view on signal
	jq(document).on("start.application", function(ev) {
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
			BootLoader.loadCSS(cssHref);
			var resource = BootLoader.addResource(rsrcID, "css", cssHref, attributes, false);
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
					BootLoader.loadJS(jsHref);
					var resource = BootLoader.addResource(rsrcID, "js", jsHref, attributes, false);
				}
			}
			
			// AsCoP Request
			var appID = url.getVar("id");
			var requestParams = "id="+appID;
			ascop.asyncRequest("/ajax/apps/load.php", "GET", requestParams, "json", jqSender, moduleLoaderCallback, null, false);
		}
	});
});