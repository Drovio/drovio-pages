var jq = jQuery.noConflict();


jq(document).off("click", "#toggleNonBasicCurrencies");
jq(document).on("click", "#toggleNonBasicCurrencies", function() {
	if (jq(this).hasClass("open")) {
		jq(this).removeClass("open").html("Show");
		jq("#nonBasicCurrenciesContainer").addClass("noDisplay");
	}
	else {
		jq(this).addClass("open").html("Hide");
		jq("#nonBasicCurrenciesContainer").removeClass("noDisplay").trigger("load");
	}
});