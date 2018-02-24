jq(document).one("ready", function() {
	jq(document).on("click", ".projectRow .pTitle", function() {
		// Get closest projectRow and toggle open class
		jq(this).closest(".projectRow").toggleClass("open");
	});
});