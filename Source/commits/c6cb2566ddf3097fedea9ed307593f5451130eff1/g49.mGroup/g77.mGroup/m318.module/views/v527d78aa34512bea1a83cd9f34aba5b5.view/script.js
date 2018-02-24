jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Stop propagation for remove button
	jq(document).on("click", "#removeBtn", function(ev) {
		ev.stopPropagation();
	});
	
	// Remove deleted row from explorer
	jq(document).on("file_logs.reload", function(ev, fname) {
		// Remove frow
		jq(".logExplorer .fTitle:contains("+fname+")").closest(".fRow").remove();
	});
});