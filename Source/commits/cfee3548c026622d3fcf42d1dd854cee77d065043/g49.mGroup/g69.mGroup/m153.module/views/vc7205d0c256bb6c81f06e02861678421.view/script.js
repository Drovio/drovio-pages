var jq = jQuery.noConflict();


jq(document).off("click", ".literalHeader");
jq(document).on("click", ".literalHeader:not(.locked)", function() {
	jq(this).toggleClass("open");
	if (jq(this).filter(".open").length == 0)
		return;

	var ref = jq(this).data("ref").replace(/\./g, "\\.");
	jq(this).closest("#lt_"+ref).find("#tr_"+ref).trigger("load");
});

jq(document).off("mouseenter mouseleave", ".ctrlInnerWrapper");
jq(document).on("mouseenter mouseleave", ".ctrlInnerWrapper", function() {
	jq(this).closest("li").toggleClass("active");
});

jq(document).off("click", ".ctrlInnerWrapper > span");
jq(document).on("click", ".ctrlInnerWrapper > span", function(ev) {
	jq(this).filter(":contains(+)").closest(".ctrlInnerWrapper")
		.removeClass("negative").toggleClass("positive");
	jq(this).filter(":contains(-)").closest(".ctrlInnerWrapper")
		.removeClass("positive").toggleClass("negative");
});

jq(document).on("click", ".ctrlInnerWrapper > span", function(ev) {
	var jqthis = jq(this);
	var popupContent = jqthis.filter(":only-child:contains(x)").closest(".literalTranslations")
		.find(".rmMyTl").first();
	if (popupContent.length == 0)
		return;
	
	jqthis.popup.position = "left|center";
	jqthis.popup.distanceOffset = 16;
	jqthis.closest(".ctrlInnerWrapper").popup(popupContent.clone().css("display", "block"), jqthis.closest(".literalTranslations").first());
});