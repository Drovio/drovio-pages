jq(document).one("ready", function() {
	jq(document).on("click", ".releaseLogPage .releaseRow .releaseTitle", function() {
		jq(this).closest(".releaseRow").toggleClass("open");
	});
});