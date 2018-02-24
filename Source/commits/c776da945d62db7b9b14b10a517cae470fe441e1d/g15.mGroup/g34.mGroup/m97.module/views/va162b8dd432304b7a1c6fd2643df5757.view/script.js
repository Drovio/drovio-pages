var jq = jQuery.noConflict();


jq(document).off("click", ".moduleHeader");
jq(document).on("click", ".moduleHeader(.locked)", function() {
	if (jq(this).hasClass("open")) {
		jq(this).removeClass("open");
	}
	else {
		jq(this).addClass("open");
		var ref = jq(this).data("ref");
		jq(this).closest("#sl_"+ref).find("#mdl_"+ref).trigger("load");
	}
});