jq(document).one("ready", function() {
	// Submit jquery tester
	jq(document).on("change", ".inps_tester", function() {
		// Submit form
		jq(this).closest("form").trigger("submit");
	});
	
	// On/Off log
	jq(document).on("status.modified", "#cloggerSwitch", function() {
		if (switchButtonForm.getStatus(jq(this)))
			jq(".coreConfigurator .coreLoggerContainer").removeClass("noDisplay");
		else
			jq(".coreConfigurator .coreLoggerContainer").addClass("noDisplay");
	});
	
	
	jq(document).on("content.modified", function() {
		// Get Bootloader resources
		var resources = BootLoader.resources;
		for (key in resources) {
			// Get only package resources
			if (jq.type(resources[key].attributes) != "undefined" && jq.type(resources[key].attributes.category) != "undefined" && resources[key].attributes.category != 1)
				continue;

			// Search if resource already exist
			var continueFlag = false;
			jq('.coreConfigurator #coreResourceProfiler .bootResource').each(function() {
				if (jq(this).data("id") == resources[key].id && jq(this).data("type") == resources[key].type)
					continueFlag = true;
			});
			if (continueFlag)
				continue;
			
			// Create li
			var jqLi = jq("<li />").addClass("bootResource");
			jqLi.attr("id", "rsrc_"+resources[key].id);
			jqLi.data("id", resources[key].id);
			jqLi.data("type", resources[key].type);
			
			jq("<div/>").addClass("rsrcIco").appendTo(jqLi);
			jq("<div>"+resources[key].url+"</div>").addClass("rsrcUrl").appendTo(jqLi);
			if (!resources[key].static) {
				jq("<div>Reload</div>").addClass("rsrcReload").appendTo(jqLi);
				jq("<div>Remove</div>").addClass("rsrcRemove").appendTo(jqLi);
			}
			
			// Append to list container
			if (resources[key].type == "css")
				jqLi.appendTo(jq(".coreConfigurator #coreResourceProfiler .resourceListContainer .cssResources"));
			else
				jqLi.appendTo(jq(".coreConfigurator #coreResourceProfiler .resourceListContainer .jsResources"));
		}
	});

	// Resource Management listeners
	jq(document).on("click", ".coreConfigurator #coreResourceProfiler .rsrcReload", function() {
		// Get closest boot resource
		var jqResource = jq(this).closest(".bootResource");
		var resourceUrl = jqResource.find(".rsrcUrl").text();
		var attributes = url.getUrlVar(resourceUrl);
		BootLoader.reloadResource(jqResource.data("id"), jqResource.data("type"), resourceUrl, attributes);
	});
	
	jq(document).on("click", ".coreConfigurator #coreResourceProfiler .rsrcRemove", function() {
		// Get closest boot resource
		var jqResource = jq(this).closest(".bootResource");
		var flag = BootLoader.removeResource(jqResource.data("id"), jqResource.data("type"));
		if (flag)
			jqResource.remove();
	});
});