// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {
	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", ".moduleViewEditor .objTool.settings", function() {
		if (jq(this).closest(".viewSource").find(".viewInfo").hasClass("noDisplay")) {
			jq(this).closest(".viewSource").find(".viewInfo").removeClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditorContainer").addClass("noDisplay");
		} else {
			jq(this).closest(".viewSource").find(".viewInfo").addClass("noDisplay");
			jq(this).closest(".viewSource").find(".codeEditorContainer").removeClass("noDisplay");
		}
	});
	
	// Listen for tab change in inner tab control
	jq(document).on("tab_changed", ".moduleViewTabControl", function() {
		// Get current tabber
		var jqTabber = jq(this);
		
		// Trigger timer to get selected page
		setTimeout(function () {
			var selectedPage = tabControl.getSelectedPage(jqTabber);
			selectedPage.find(".cmEditor").each(function() {
				var codeMirror = jq(this).data("CodeMirrorInstance");
				if (codeMirror != undefined)
					setTimeout(function () {
						codeMirror.refresh();
					}, 100);
			});
		}, 10);
	});
	
	// Add mGroup and module resolving functions for #innerCodes
	moduleGroup.addContainer(".innerCodes");
	module.addContainer(".innerCodes");
});