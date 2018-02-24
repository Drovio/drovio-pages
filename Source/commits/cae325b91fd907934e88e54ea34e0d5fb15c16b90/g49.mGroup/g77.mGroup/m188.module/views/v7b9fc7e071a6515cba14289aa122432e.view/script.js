jq(document).one("ready.extra", function() {
	jq(document).on("click", ".packageRow", function() {
		jq(this).closest(".branchPackage").toggleClass("withHistory");
	});
});