jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("change", ".myActiveSessions input[type='radio']", function() {
		jq(this).closest("form").trigger("submit");
	});
});