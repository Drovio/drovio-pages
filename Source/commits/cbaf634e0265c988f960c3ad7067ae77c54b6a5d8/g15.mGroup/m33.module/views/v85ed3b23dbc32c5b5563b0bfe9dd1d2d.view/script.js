jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toolbar navigation
	jq(document).on("click", ".adminNav", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Set state (if state is the same return)
		var stateHref = jq(this).attr("href");
		if (window.location.pathname == stateHref)
			return;
		state.push(stateHref);
		
		// Trigger container
		var targetID = "adm_"+jq(this).attr("id");
		jq("#"+targetID).trigger("load");
	});
});