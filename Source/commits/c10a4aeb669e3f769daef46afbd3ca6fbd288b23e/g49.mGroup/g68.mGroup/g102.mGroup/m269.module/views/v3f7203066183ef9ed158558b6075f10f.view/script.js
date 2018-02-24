// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {
	// Listener for view source settings/dependencies
	jq(document).on("click", ".applicationViewEditor .objTool.settings", function() {
		if (jq(this).closest(".viewSource").find(".viewInfo").hasClass("noDisplay")) {
			jq(this).closest(".viewSource").find(".viewInfo").removeClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditor").addClass("noDisplay");
		} else {
			jq(this).closest(".viewSource").find(".viewInfo").addClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditor").removeClass("noDisplay");
		}
	});
});