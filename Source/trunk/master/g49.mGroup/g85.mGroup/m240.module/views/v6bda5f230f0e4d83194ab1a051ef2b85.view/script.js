// Add container for name resolving
moduleGroup.addContainer("#projectCommitManager");
module.addContainer("#projectCommitManager");

// let the document load
jq(document).one("ready.extra", function() {

	// Bubbles "saveState" event to the treeView and "reload" event to the module
	// Saves the treeview state and reloads the module.
	jq(document).on("click", "#mRefresh", function() {
		jq("#moduleExplorerTree").trigger("saveState");
		jq(".moduleExplorer").trigger("reload");
	});
	
	// Listener to refresh the explorer
	jq(document).on("pages.explorer.refresh", function() {
		jq("#moduleExplorerTree").trigger("saveState");
		jq(".moduleExplorer").trigger("reload");
	});
});