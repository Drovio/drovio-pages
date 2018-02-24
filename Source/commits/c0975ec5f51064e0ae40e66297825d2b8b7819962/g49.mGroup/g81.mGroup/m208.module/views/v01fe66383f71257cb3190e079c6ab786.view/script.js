jq(document).one("ready", function() {
	jq(document).on("click", ".projectRow .pTitle", function() {
		// Get project reference
		var referenceID = jq(this).data("ref");
		jq("#"+referenceID).trigger("load");
	});
});