var jq = jQuery.noConflict();

jq(document).off("click", ".pl");
jq(document).on("click", ".pl", function() {
	// Get Button
	var jqthis = jq(this);

	// Delay action to get input
	setTimeout(function() {
		jqthis.closest("form").submit();
	}, 1);
});