var jq = jQuery.noConflict();

jq(document).on("ready.extra", function(){

	jq(document).off("open.toggler", "#translationsEditor");
	jq(document).on("open.toggler", "#translationsEditor", function(ev) {
		var jqthis = jq(ev.target).closest("[data-lt-ref]");
		var ref = jqthis.data("lt-ref").replace(/\./g, "\\.");
		jqthis.find("#"+ref).trigger("load");
	});
	
	jq(document).off("click", "#translationsEditor .editTranslation");
	jq(document).on("click", "#translationsEditor .editTranslation", function(ev) {
		var jqthis = jq(this).nextAll(".translationValue").first();
		jqthis.css("display", "none");
		var form = jqthis.nextAll("form").first();
		form.css("display", "inline-block");
		
		var parent = jqthis.parent();
		parent.add(parent.siblings()).addClass("locked");
	});
	
	jq(document).off("reset.ltp", "#translationsEditor .literalTranslations form");
	jq(document).on("reset.ltp", "#translationsEditor .literalTranslations form", function(ev) {
		var jqthis = jq(this);
		jqthis.css("display", "none");
		var tlvalue = jqthis.siblings(".translationValue").first();
		tlvalue.css("display", "");
		
		var parent = jqthis.parent();
		parent.add(parent.siblings(":not(:first-child)")).removeClass("locked");
	});
	
});