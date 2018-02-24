jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload module
	jq(document).on("click", "#pgRefresh", function() {
		jq(this).closest(".pageExplorerContainer").trigger("reload");
	});
	
	// Open-close items
	jq(document).on("click", ".pageExplorer .pgItem .pgItemContent", function() {
		jq(this).closest(".pgItem").toggleClass("open");
	});
	
	// Show listener
	jq(document).on("click", ".pageExplorer .pgItem .pgItemContent .show", function(ev) {
		// Stop bubbling and prevent open-close item
		ev.stopPropagation();
	});
});