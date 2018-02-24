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
});