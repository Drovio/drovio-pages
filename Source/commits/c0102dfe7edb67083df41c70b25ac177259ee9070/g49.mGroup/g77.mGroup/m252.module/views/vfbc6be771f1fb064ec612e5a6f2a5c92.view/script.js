jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", ".tRow .status", function () {
		var tRow = jq(this).closest(".tRow");
		tRow.toggleClass("open");
		if (tRow.hasClass("open"))
			tRow.find(".tContent").trigger("load");
	});
});