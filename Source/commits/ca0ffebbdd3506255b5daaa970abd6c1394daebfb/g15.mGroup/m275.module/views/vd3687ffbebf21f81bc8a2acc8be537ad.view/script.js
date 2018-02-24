jq(document).one("ready", function() {
	jq(document).on("click", ".rvList .rvrow .rvhd", function() {
		jq(this).closest(".rvrow").toggleClass("open");
	});
});