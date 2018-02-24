var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Close dialog
	jq(document).on("account.keys.list.reload", function() {
		// Click on menu
		jq(".menu_item.keys.selected").trigger("click");
	});
});