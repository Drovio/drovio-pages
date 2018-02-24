jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("translations.switchto.literals", function(ev, context) {
		// Set visibility
		jq(".projectLiteralTranslations .toolbar").addClass("lt");
		jq(".projectLiteralTranslations .navContent").addClass("lt");
		
		// Add mi
		jq(".mi.current").html(context);
	});
	
	jq(document).on("click", ".mi.all", function() {
		jq(".projectLiteralTranslations .toolbar").removeClass("lt");
		jq(".projectLiteralTranslations .navContent").removeClass("lt");
	});
});