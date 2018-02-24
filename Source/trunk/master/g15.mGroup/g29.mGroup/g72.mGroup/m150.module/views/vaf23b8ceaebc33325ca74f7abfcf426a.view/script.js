var jq = jQuery.noConflict();


jq(document).off("click", "#toggleNonBasicLangs");
jq(document).on("click", "#toggleNonBasicLangs", function() {
	if (jq(this).hasClass("open")) {
		jq(this).removeClass("open").html("Show");
		jq("#nonBasicLangsContainer").addClass("noDisplay");
	}
	else {
		jq(this).addClass("open").html("Hide");
		jq("#nonBasicLangsContainer").removeClass("noDisplay").trigger("load");
	}
});