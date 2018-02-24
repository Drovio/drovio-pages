jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("content.modified", function() {
		jq(document).off("switch.nav");
		jq(document).on("switch.nav", function(ev, value) {
			jq("."+value).trigger("click");
		});
	});
});