jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Stop propagation for remove button and don't select item
	jq(document).on("click", "#removeBtn", function(ev) {
		ev.stopPropagation();
	});
	
	// Remove deleted row from explorer
	jq(document).on("file_logs.reload", function(ev, fname) {
		// Remove frow
		var frow = jq(".logExplorer .fTitle:contains("+fname+")").closest(".fRow");
		if (frow.hasClass("selected"))
			jq(".projectFileLogs .panel.context").html("");
		
		// Remove row
		frow.remove();
	});
});