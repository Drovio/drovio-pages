var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Load module editor
	jq(document).on("click", ".moduleHeader", function() {
		// Load module
		if (!jq(this).hasClass("open")) {
			var ref = jq(this).data("ref");
			jq(this).closest("#sl_"+ref).find("#mdl_"+ref).trigger("load");
		}
		
		// Toggle class
		jq(this).toggleClass("open");
	});
});