jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload module
	jq(document).on("click", "#pgRefresh", function() {
		jq(this).closest(".pageExplorerContainer").trigger("reload");
	});
	
	// Open-close items
	jq(document).on("click", ".pageExplorer .pgItem .pgItemContent", function(ev) {
		// Check if show button is pressed and avoid toggle open
		if (jq(ev.target).closest(".show").length > 0)
			return;
		
		// Toggle open setate
		jq(this).closest(".pgItem").toggleClass("open");
	});
});