var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	var userGroupModules = new Array();
	
	// Filter all modules by user group
	jq(document).on("change", "#userGroupSelector select", function() {
		// Get user group value
		var userGroup = jq(this).val();
		
		// Remove classes if user group is "no group"
		if (userGroup == "null")
			jq(".exColumn .exColItem").removeClass("groupOn").removeClass("groupOff");
		else {
			var callback = function(report) {
				userGroupModules[report[0].payload.gid] = report[0].payload.modules;
				filterModules(report[0].payload.gid);
			}
			var extraParams = jq(this).serialize();
			ModuleLoader.load(jq(this), 184, "userGroupModules", "", extraParams, callback);
		}
		
		filterModules = function(gid) {
			// Get modules
			var modules = userGroupModules[gid];
			
			// Set all items as group off
			jq(".exColumn .exColItem.page").not(".open").not(".public").addClass("groupOff");
			
			// Set user group items as group on
			for (var key in modules) {
				var module_id = modules[key];
				jq("#mid_"+module_id+".page").not(".open").not(".public").removeClass("groupOff").addClass("groupOn");
			}
		}
	});
});