// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {
	// Listen for tab change in inner tab control
	jq(document).on("tab_changed", ".queryTabber", function() {
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
});