jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle object explorer
	jq(document).on("click", ".startScreen .searchContainer .explorerIcoContainer", function() {
		jq(this).closest(".startScreen").toggleClass("open");
	});
	
	// Search items and display explorer
	jq(document).on("keyup", ".startScreen .searchContainer .searchInput", function(ev) {
		// Get input
		var search = jq(this).val();
		if (search == "" || search.length < 2) {
			// Stop searching
			jq(this).closest(".startScreen").removeClass("open").removeClass("searching");
			return;
		}
		
		// Enable search
		jq(this).closest(".startScreen").addClass("open").addClass("searching");
		
		// Search SDK items and clone to search results
		var matchItems = jq(".startScreen .objectExplorer .sdkObject:contains("+search+")").clone(true).addClass("sr");
		// Transform and show full paths
		matchItems.each(function() {
			var fullPath = jq(this).data("fullpath");
			jq(this).find("span").eq(1).html(fullPath);
		});
		// Append matched items
		if (matchItems.length > 0) {
			jq(".startScreen .searchResults .results.sdk").removeClass("noDisplay");
			jq(".startScreen .searchResults .results.sdk .rlist").html("").append(matchItems);
		} else
			jq(".startScreen .searchResults .results.sdk").addClass("noDisplay");
		
		// Search Web SDK items and clone to search results
		var matchItems = jq(".startScreen .objectExplorer .wsdkObject:contains("+search+")").clone(true).addClass("sr");
		// Transform and show full paths
		matchItems.each(function() {
			var fullPath = jq(this).data("fullpath");
			jq(this).find("span").eq(1).html(fullPath);
		});
		// Append matched items
		if (matchItems.length > 0) {
			jq(".startScreen .searchResults .results.wsdk").removeClass("noDisplay");
			jq(".startScreen .searchResults .results.wsdk .rlist").html("").append(matchItems);
		} else
			jq(".startScreen .searchResults .results.wsdk").addClass("noDisplay");
	});
	
	// Enable search
	jq(document).on("focusin", ".startScreen .searchContainer .searchInput", function(ev) {
		var search = jq(this).val();
		if (search != "" && search.length > 1)
			jq(this).closest(".startScreen").addClass("open").addClass("searching");
	});
	
	// Disable search
	jq(document).on("focusout", ".startScreen .searchContainer .searchInput", function(ev) {
		var search = jq(this).val();
		if (search == "" || search.length < 2)
		jq(this).closest(".startScreen").removeClass("open").removeClass("searching");
	});
});