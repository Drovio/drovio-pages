jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Push history
	jq(document).on("click", ".myRelations .navigation .navItem", function() {
		// Get ref
		var ref = jq(this).data("ref");
		
		// Push history
		var newUrl = url.resolve("my", "relations/"+ref);
		state.push(newUrl);
	});
});