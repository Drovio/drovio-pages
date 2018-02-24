jq(document).one("ready", function() {
	// Reload translations
	jq(document).on("translations.reload", function() {
		jq("#literalTranslationsContainer").trigger("reload");
	});
});