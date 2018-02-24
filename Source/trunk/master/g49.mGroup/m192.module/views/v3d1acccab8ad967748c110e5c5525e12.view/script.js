jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Expand releases
	jq(document).on("click", ".infobox .more", function() {
		jq(this).closest(".infobox").find(".more_releases").animate({
			height: "toggle"
		}, 500);
	});
});