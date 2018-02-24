var jq = jQuery.noConflict();


jq(document).off("click", ".settingsItem");
jq(document).on("click", ".settingsItem", function() {
	jq(this).closest(".settingsGroup").find(".settingsContent").trigger("load");
});