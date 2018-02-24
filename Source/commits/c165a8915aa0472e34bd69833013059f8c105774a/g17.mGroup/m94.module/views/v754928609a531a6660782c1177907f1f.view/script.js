jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("click", "li.login", function() {
		jq(this).toggleClass("open");
		if (jq(this).hasClass("open")) {
			jq(this).popup.position = "bottom|right";
			jq(this).popup(jq(".loginDialogContainer").clone());
		}
	});
});