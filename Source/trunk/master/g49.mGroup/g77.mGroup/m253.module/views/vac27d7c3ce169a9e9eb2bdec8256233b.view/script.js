var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload literal scopes
	jq(document).on("literal_scopes.reload", function(ev, scopeName) {
		// Reload payments container
		var extraParams = new Array();
		extraParams['sname'] = scopeName;
		jq("#literalScopeExplorerContainer").trigger("reload", [false, extraParams]);
	});
});