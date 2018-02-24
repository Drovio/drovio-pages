var jq = jQuery.noConflict();


jq(document).off("click", "#toggleNonBasicTimezones");
jq(document).on("click", "#toggleNonBasicTimezones", function() {
	if (jq(this).hasClass("open")) {
		jq(this).removeClass("open").html("Show");
		jq("#nonBasicTimezonesContainer").addClass("noDisplay");
	}
	else {
		jq(this).addClass("open").html("Hide");
		jq("#nonBasicTimezonesContainer").removeClass("noDisplay").trigger("load");
	}
});