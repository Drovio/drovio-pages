jq(document).on("click", ".appDetails .close_ico", function() {
	jq(this).trigger("dispose");
});