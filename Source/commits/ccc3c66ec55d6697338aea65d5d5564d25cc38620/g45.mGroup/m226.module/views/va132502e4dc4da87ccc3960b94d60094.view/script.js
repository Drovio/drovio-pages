jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Init dashboard functionality
	dashboard.init();
	
	// Prevent Reload or Redirect when an application is open
	jq(window).on('beforeunload', function() {
		if (jq(".apps_pool .applicationPlayer").length > 0)
			return "There are active applications in the dashboard.";
	});
});

dashboard = {
	init: function() {
		
		// Set application listeners
		jq(document).on("click", ".apps_grid .appBox", function() {
			// Get application info from the tile
			var applicationID = jq(this).data("app").id;
			
			// Check if already loaded
			var exists = false;
			jq(".apps_pool").find(".applicationPlayer").each(function() {
				var holderAppID = jq(this).data("app").id;
				if (applicationID == holderAppID) {
					exists = true;
					return false;
				}
			});
			
			// Check if application already exists
			var status = dashboard.switchToApp(applicationID);
			if (!status) {
				// Load new app
				var extraParams = "appID="+applicationID;
				ModuleLoader.load(jq(this), 272, "", "", extraParams, null, null, null, true);
			}
		});
		
		// Switch application listener
		jq(document).on("application.switch", function(ev, appID) {
			dashboard.switchToApp(appID);
		});
	},
	switchToApp: function(applicationID) {
		// Get application holder and set active
		var jqAppHolder = dashboard.getApplicationHolder(applicationID);
		
		// Check if app exists
		if (jqAppHolder.length == 0 || jq.type(jqAppHolder) == "undefined")
			return false;
		
		jq(".apps_pool .applicationPlayer").addClass("noDisplay");
		jqAppHolder.removeClass("noDisplay");
		
		// Activate application viewer
		jq(".apps_pool").addClass("open");
		
		return true;
	},
	closeApp: function(applicationID) {
		// Get application holder and remove
		var jqAppHolder = dashboard.getApplicationHolder(applicationID);
		if (jqAppHolder.length == 0 || jq.type(jqAppHolder) != "undefined")
			jqAppHolder.remove();
		
		// Get application tile and remove
		var jqAppTile = dashboard.getApplicationTile(applicationID)
		if (jqAppTile.length == 0 || jq.type(jqAppTile) != "undefined")
			jqAppTile.remove();
		
	},
	getApplicationHolder: function(applicationID) {
		return jq(".apps_pool .applicationPlayer").filter(function() {
			return jq(this).data("app").id == applicationID;
		});
	},
	getApplicationTile: function(applicationID) {
		return jq("#activeAppsContainer .applicationTile").filter(function() {
			return jq(this).data("app").id == applicationID;
		});
	}
}