jq(document).one("ready", function() {
	// Show project details
	jq(document).on("click", ".project_list .rvrow .rvhd", function() {console.log("click");
		jq(this).closest(".rvrow").toggleClass("open");
	});
});