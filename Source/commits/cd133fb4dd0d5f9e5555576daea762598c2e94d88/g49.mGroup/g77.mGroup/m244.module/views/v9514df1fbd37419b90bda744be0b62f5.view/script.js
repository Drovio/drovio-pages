jq(document).one("ready", function() {
	jq(document).on("click", ".releaseLogPage .releaseRow .rhd", function() {
		jq(this).closest(".releaseRow").toggleClass("open");
	});
});