jq(document).one("ready.extra", function() {
	jq(document).on("click", ".navBtn.older.active", function() {
		// Get page
		var nextPage = jq(".commitsInnerContainer").data("info").page + 1;
		var totalPages = jq(".commitsInnerContainer").data("info").totalPages;
		
		// Set attributes
		var attrs = new Array();
		attrs['page'] = nextPage;
		attrs['bn'] = jq(".commitsInnerContainer").data("info").bn;
		attrs['pagination'] = true;
		
		// Add extra parameters
		var extraParams = "";
		for (var name in attrs)
			extraParams += "&"+name+"="+encodeURIComponent(attrs[name]);
			
		// Make the call
		ModuleLoader.load(jq(this), 188, "repositoryCommits", "", extraParams);
		
		// Update page
		jq(".commitsInnerContainer").data("info").page = nextPage;
		updatePagination(nextPage, totalPages);
	});
	
	jq(document).on("click", ".navBtn.newer.active", function() {
		// Get page
		var nextPage = jq(".commitsInnerContainer").data("info").page - 1;
		var totalPages = jq(".commitsInnerContainer").data("info").totalPages;
		
		// Set attributes
		var attrs = new Array();
		attrs['page'] = nextPage;
		attrs['bn'] = jq(".commitsInnerContainer").data("info").bn;
		attrs['pagination'] = true;
		
		// Add extra parameters
		var extraParams = "";
		for (var name in attrs)
			extraParams += "&"+name+"="+encodeURIComponent(attrs[name]);
			
		// Make the call
		ModuleLoader.load(jq(this), 188, "repositoryCommits", "", extraParams);
		
		// Update page
		jq(".commitsInnerContainer").data("info").page = nextPage;
		updatePagination(nextPage, totalPages);
	});
	
	var updatePagination = function(nextPage, totalPages) {
		if (nextPage+1 >= totalPages) {
			jq(".navBtn.newer").addClass("active");
			jq(".navBtn.older").removeClass("active");
		} else if (nextPage <= 0) {
			jq(".navBtn.older").addClass("active");
			jq(".navBtn.newer").removeClass("active");
		} else {
			jq(".navBtn.newer").addClass("active");
			jq(".navBtn.older").addClass("active");
		}
	}
});