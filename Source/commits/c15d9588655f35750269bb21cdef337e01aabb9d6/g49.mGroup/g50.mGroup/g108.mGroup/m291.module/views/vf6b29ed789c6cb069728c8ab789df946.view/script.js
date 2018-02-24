jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toggle object explorer
	jq(document).on("click", ".sdkManualContainer .navigationContainer .explorerIcoContainer", function() {
		jq(this).closest(".navigationContainer").toggleClass("open");
	});
	
	// Search items and display explorer
	jq(document).on("keyup", ".sdkManualContainer .navigationContainer .searchInput", function(ev) {
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
		var matchItems = jq(".sdkManualContainer .objectExplorer .sdkObject:contains("+search+")").clone(true).addClass("sr");
		// Transform and show full paths
		matchItems.each(function() {
			var fullPath = jq(this).data("fullpath");
			jq(this).find("span").eq(1).html(fullPath);
		});
		// Append matched items
		if (matchItems.length > 0) {
			jq(".sdkManualContainer .navigationContainer .searchResults .results.sdk").removeClass("noDisplay");
			jq(".sdkManualContainer .navigationContainer .searchResults .results.sdk .rlist").html("").append(matchItems);
		} else
			jq(".sdkManualContainer .navigationContainer .searchResults .results.sdk").addClass("noDisplay");
		
		// Search items and clone to search results
		var matchItems = jq(".sdkManualContainer .objectExplorer .wsdkObject:contains("+search+")").clone(true).addClass("sr");
		// Transform and show full paths
		matchItems.each(function() {
			var fullPath = jq(this).data("fullpath");
			jq(this).find("span").eq(1).html(fullPath);
		});
		// Append matched items
		if (matchItems.length > 0) {
			jq(".sdkManualContainer .navigationContainer .searchResults .results.wsdk").removeClass("noDisplay");
			jq(".sdkManualContainer .navigationContainer .searchResults .results.wsdk .rlist").html("").append(matchItems);
		} else
			jq(".sdkManualContainer .navigationContainer .searchResults .results.wsdk").addClass("noDisplay");
	});
	
	// Enable search
	jq(document).on("focusin", ".sdkManualContainer .navigationContainer .searchInput", function(ev) {
		var search = jq(this).val();
		if (search != "" && search.length > 1)
			jq(this).closest(".navigationContainer").addClass("open").addClass("searching");
	});
	
	// Disable search
	jq(document).on("focusout", ".sdkManualContainer .navigationContainer .searchInput", function(ev) {
		var search = jq(this).val();
		if (search == "" || search.length < 2)
		jq(this).closest(".navigationContainer").removeClass("open").removeClass("searching");
	});
});