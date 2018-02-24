// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", ".websitePageObjectEditor .objTool.settings", function() {
		if (jq(this).closest(".pageSource").find(".pageInfo").hasClass("noDisplay")) {
			jq(this).closest(".pageSource").find(".pageInfo").removeClass("noDisplay");
			jq(this).closest(".pageSource").find(".codeEditorContainer").addClass("noDisplay");
		} else {
			jq(this).closest(".pageSource").find(".pageInfo").addClass("noDisplay");
			jq(this).closest(".pageSource").find(".codeEditorContainer").removeClass("noDisplay");
		}
	});
	
	// Add mGroup and module resolving functions for #innerCodes
	moduleGroup.addContainer(".innerCodes");
	module.addContainer(".innerCodes");
});