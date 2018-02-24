// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", ".ftvToolbar > .ftvTool.refresh", function() {
		jq(this).trigger("saveState")
			.trigger("reload");
	});
	
	// Closes all open treeItems.
	jq(document).on("click", ".ftvToolbar > .ftvTool.collapse", function() {
		jq(this).closest(".treeView").find(".treeItem.open").trigger("toggleState");
	});

	/*
	var packageCounters = null;
	// Store localy the most used packages for this client.
	jq(document).on("change", "[name^='imports']", function(){
		var jqthis = jq(this);
		var diff = 1;
		if (!jqthis.prop("checked"))
			diff = -1;
			
		if( typeof(Storage) !== "undefined") {
			// LocalStorage and sessionStorage support! 
			if (packageCounters == null)
				packageCounters = jQuery.parseJSON(localStorage.modulePackageCounters);
			if (packageCounters == null)
				packageCounters = new Object();
			
			var name = jqthis.attr("name");
			
			if (typeof(packageCounters[name]) == "undefined")
				packageCounters[name] = 1
			else
				packageCounters[name] += diff;
			
			localStorage.modulePackageCounters = JSON.stringify(packageCounters);
		}
	});
	
	jq(document).on("content.modified", function(){
		if (typeof(localStorage.modulePackageCounters) == "undefined")
			return;
	
		var packageCounters = jQuery.parseJSON(localStorage.modulePackageCounters);
		var sortableCounters = [];
		for (var counter in packageCounters)
			sortableCounters.push([counter, packageCounters[counter]])
		sortableCounters.sort(function(a, b) {return b[1] - a[1]});
		
		var topCounters = sortableCounters.slice(0, 8).reverse();
		var lis = jq();
		
		for (var counter in topCounters){
			var elem = jq("[name='"+topCounters[counter][0]+"']").closest(".dataGridRow");
			elem.each(function(){
				jq(this).closest(".dataGridContentWrapper").prepend(jq(this));
			});
		}
	});
	*/
});