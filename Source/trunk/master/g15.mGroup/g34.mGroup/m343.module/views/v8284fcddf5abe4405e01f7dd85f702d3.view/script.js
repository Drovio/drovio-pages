var jq=jQuery.noConflict();
jq(document).one("ready", function() {
	
	// Add mGroup and module resolving functions for privileges
	moduleGroup.addContainer(".groupPrivilegesEditor");
	module.addContainer(".groupPrivilegesEditor");
});