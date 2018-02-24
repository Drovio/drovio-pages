var jq = jQuery.noConflict();


jq(document).off("click", ".endActivity");
jq(document).on("click", ".endActivity", function() {
	// Get Button
	var jqthis = jq(this);
	
	// Delay action to get input
	setTimeout(function() {
		jqthis.closest("form").submit();
	}, 1);
});