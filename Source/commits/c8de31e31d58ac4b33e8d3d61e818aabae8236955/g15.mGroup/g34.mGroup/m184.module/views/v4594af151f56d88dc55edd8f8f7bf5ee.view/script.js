jq(document).one("ready.extra", function() {
	var userGroupModules = new Array();
	
	// Filter all modules by user group
	jq(document).on("change", "#userGroupSelector select", function() {
		// Get user group value
		var userGroup = jq(this).val();
		
		// Remove classes if user group is "no group"
		if (userGroup == "null")
			jq(".exColItem").removeClass("groupOn").removeClass("groupOff");
		else {
			// Check if user group modules are already here
			if (jq.type(userGroupModules[userGroup]) !== "undefined") {
				filterModules(userGroup);
			} else {
				var callback = function(report) {
					userGroupModules[report.body[0].context.gid] = report.body[0].context;
					filterModules(report.body[0].context.gid);
				}
				var extraParams = jq(this).serialize();
				ModuleProtocol.getModule(jq(this), 184, "userGroupModules", extraParams, callback);
			}
		}
		
		filterModules = function(gid) {
			// Get modules
			var modules = userGroupModules[gid];
			
			// Set all items as group off
			jq(".exColItem.page").addClass("groupOff");
			
			// Set user group items as group on
			for (key in modules) {
				var module_id = modules[key];
				jq("#mid_"+module_id+".page").removeClass("groupOff").addClass("groupOn");
			}
		}
	});
});