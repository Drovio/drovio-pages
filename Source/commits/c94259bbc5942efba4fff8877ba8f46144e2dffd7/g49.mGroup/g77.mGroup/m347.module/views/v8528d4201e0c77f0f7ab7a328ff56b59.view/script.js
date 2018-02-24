jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("translations.switchto.literals", function(ev, context) {
		// Set visibility
		jq(".locTranslations .toolbar").addClass("lt");
		jq(".locTranslations .navContent").addClass("lt");
		
		// Add mi
		jq(".mi.current").html(context);
	});
	
	jq(document).on("click", ".mi.all", function() {
		jq(".locTranslations .toolbar").removeClass("lt");
		jq(".locTranslations .navContent").removeClass("lt");
	});
});