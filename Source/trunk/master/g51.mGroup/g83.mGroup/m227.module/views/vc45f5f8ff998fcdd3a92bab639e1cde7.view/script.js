jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toolbar navigation
	jq(document).on("click", ".appcenter-sideswitch", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		jq('.appCenterUserHome .appGrid').toggleClass('open');
	});
	
	// Search items and display explorer
	jq(document).on("keyup", ".appCenterUserHome .navigationContainer .searchInput", function(ev) {
		// Get input
		var search = jq(this).val();
		if (search == "" || search.length < 2) {
			// Stop searching
			jq(this).closest(".navigationContainer").removeClass("open").removeClass("searching");
			return;
		}
		
		// Enable search
		jq(this).closest(".navigationContainer").addClass("open").addClass("searching");
		
		// Search items and clone to search results
		var matchItems = jq(".appCenterUserHome .objectExplorer .sdkObject:contains("+search+")").clone(true).addClass("sr");
		// Transform and show full paths
		matchItems.each(function() {
			var fullPath = jq(this).data("fullpath");
			jq(this).find("span").eq(1).html(fullPath);
		});
		// Append matched items
		jq(".appCenterUserHome .navigationContainer .searchResults .results").html("").append(matchItems);
	});
	
	// Enable search
	jq(document).on("focusin", ".appCenterUserHome .navigationContainer .searchInput", function(ev) {
		var search = jq(this).val();
		if (search != "" && search.length > 1)
			jq(this).closest(".navigationContainer").addClass("open").addClass("searching");
	});
});