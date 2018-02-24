// Add container for name resolving
moduleGroup.addContainer("#projectCommitManager");
module.addContainer("#projectCommitManager");

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", ".ftvToolbar > .ftvTool.refresh", function() {
		jq(this).trigger("saveState")
			.trigger("reload");
	});
	
	// Closes all open treeItems.
	jq(document).on("click", ".ftvToolbar > .ftvTool.collapse", function() {
		jq(this).closest(".treeView").find(".treeItem.open").trigger("toggleState");
	});
});