jq(document).one("ready", function() {
	jq(document).on("click", ".crvList .rvrow .rvhd", function() {
		jq(this).closest(".rvrow").toggleClass("open");
	});
});