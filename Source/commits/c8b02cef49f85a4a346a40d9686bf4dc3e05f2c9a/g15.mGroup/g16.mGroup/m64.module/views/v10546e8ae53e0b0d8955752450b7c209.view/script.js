// Add container for name resolving
moduleGroup.addContainer("#projectCommitManager");
module.addContainer("#projectCommitManager");

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", "#refreshModules", function() {
		jq("#moduleExplorerTree").trigger("saveState");
		jq(".moduleExplorer").trigger("reload");
	});
});