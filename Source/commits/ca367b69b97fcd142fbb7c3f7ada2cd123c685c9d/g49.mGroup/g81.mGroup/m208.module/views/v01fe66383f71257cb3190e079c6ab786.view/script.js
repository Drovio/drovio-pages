jq(document).one("ready", function() {
	jq(document).on("click", ".devControlPanel .projectRow", function() {
		// Get project reference
		var referenceID = jq(this).data("ref");
		jq("#"+referenceID).trigger("load");
	});
	
	// Search projects
	jq(document).on("keyup", ".devPanel .searchContainer .searchInput", function(ev) {
		// Get input and search notes
		var search = jq(this).val();
		searchProjects(search);
	});
	
	// Enable search
	jq(document).on("focusin", ".devPanel .searchContainer .searchInput", function(ev) {
		// Get input and search notes
		var search = jq(this).val();
		searchProjects(search);
	});
	
	// Search all projects
	function searchProjects(search) {
		// If search is empty, show all notes
		if (search == "")
			jq(".devPanel .projectList .projectRow").show();
		
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Select all note rows, hide and filter by the regex then show
		jq(".devPanel .projectList .projectRow").hide().find(".title").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).closest(".projectRow").show();
		});
	}
});