jq(document).one("ready", function() {
	// Preview code
	jq(document).on("click", ".hentry .preview", function() {
		jq(this).closest(".hentry").find(".hContents").toggleClass("noDisplay");
	});
});