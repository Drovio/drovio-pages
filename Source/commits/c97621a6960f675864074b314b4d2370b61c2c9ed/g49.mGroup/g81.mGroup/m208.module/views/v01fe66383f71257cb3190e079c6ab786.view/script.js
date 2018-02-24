jq(document).one("ready", function() {
	jq(document).on("click", ".devControlPanel .projectRow", function() {
		// Get project reference
		var referenceID = jq(this).data("ref");
		jq("#"+referenceID).trigger("load");
	});
});