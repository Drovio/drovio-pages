// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready", function() {
	
	// Add mGroup and module resolving functions for privileges
	moduleGroup.addContainer(".groupPrivilegesEditor");
	module.addContainer(".groupPrivilegesEditor");
});