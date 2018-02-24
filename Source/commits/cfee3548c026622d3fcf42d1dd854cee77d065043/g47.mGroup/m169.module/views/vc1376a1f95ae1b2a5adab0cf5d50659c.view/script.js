jq(document).one("ready", function() {

	// Load appCenter Library Resources
	BootLoader.loadCSS("/ajax/appcenter/lib/styles.php");
	BootLoader.loadJS("/ajax/appcenter/lib/scripts.php");
	
	// Load application css
	var appID = url.getVar("id");
	BootLoader.loadCSS("/ajax/appcenter/app/css.php?id="+appID);
	
	// Load application view (with a little timeout to view the loading)
	setTimeout(loadView, 800);
	
	function loadView() {
		var jqSender = jq("#applicationContainer");
		callback = function(report) {
			ModuleProtocol.handleReport(jqSender, report, "");
			
			// Load application js
			var appID = url.getVar("id");
			BootLoader.loadJS("/ajax/appcenter/app/js.php?id="+appID);
		}
		
		// Bootloader callback
		var bootLoaderCallback = function(report) { BootLoader.processReport(report, callback); }
		
		// AsCoP Request
		var appID = url.getVar("id");
		var requestParams = "";
		ascop.asyncRequest("/ajax/appcenter/app/loadView.php?id="+appID, "GET", requestParams, "html", jqSender, bootLoaderCallback, null, false);
	}
});