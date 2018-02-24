jq(document).one("ready", function() {
	jq(document).on("status.modified", "#cloggerSwitch", function() {
		if (switchButton.getStatus(jq(this))) {
			jq(".coreConfigurator .loggerDataContainer, .coreConfigurator .logPriorityRow").removeClass("noDisplay");
		}
		else {
			jq(".coreConfigurator .loggerDataContainer, .coreConfigurator .logPriorityRow").addClass("noDisplay");
		}
	});
	
	
	jq(document).on("content.modified", function() {
		// Get Bootloader resources
		var resources = BootLoader.resources;
		for (key in resources) {
			// Get only package resources
			if (jq.type(resources[key].attributes) != "undefined" && jq.type(resources[key].attributes.category) != "undefined" && resources[key].attributes.category != "Packages")
				continue;
			
			// Search if resource already exist
			var continueFlag = false;
			jq('#coreResourceProfiler .bootResource').each(function() {
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
				jqLi.appendTo(jq("#coreResourceProfiler .resourceListContainer .cssResources"));
			else
				jqLi.appendTo(jq("#coreResourceProfiler .resourceListContainer .jsResources"));
		}
	});

	// Resource Management listeners
	jq(document).on("click", "#coreResourceProfiler .rsrcReload", function() {
		// Get closest boot resource
		var jqResource = jq(this).closest(".bootResource");
		var resourceUrl = jqResource.find(".rsrcUrl").text();
		var attributes = url.getUrlVar(resourceUrl);
		var urlParts = resourceUrl.split("?");
		var cleanUrl = urlParts[0];
		BootLoader.reloadResource(jqResource.data("id"), jqResource.data("type"), cleanUrl, attributes);
	});
	
	jq(document).on("click", "#coreResourceProfiler .rsrcRemove", function() {
		// Get closest boot resource
		var jqResource = jq(this).closest(".bootResource");
		var flag = BootLoader.removeResource(jqResource.data("id"), jqResource.data("type"));
		if (flag)
			jqResource.remove();
	});
});