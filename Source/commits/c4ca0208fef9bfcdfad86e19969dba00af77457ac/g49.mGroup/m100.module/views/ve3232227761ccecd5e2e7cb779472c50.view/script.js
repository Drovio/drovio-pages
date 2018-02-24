jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Check if docContainer is empty and click on sidebar
	if (jq(".docContainer").children().length == 0)
		jq("ul.menu .menu-item").first().find("a").trigger("click");
});