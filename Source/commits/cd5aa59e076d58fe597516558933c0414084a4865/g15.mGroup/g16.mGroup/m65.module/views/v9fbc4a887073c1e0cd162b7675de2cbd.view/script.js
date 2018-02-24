// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", ".objTool.settings", function() {
		if (jq(this).closest(".viewSource").find(".viewInfo").hasClass("noDisplay")) {
			jq(this).closest(".viewSource").find(".viewInfo").removeClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditor").addClass("noDisplay");
		} else {
			jq(this).closest(".viewSource").find(".viewInfo").addClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditor").removeClass("noDisplay");
		}
	});
});