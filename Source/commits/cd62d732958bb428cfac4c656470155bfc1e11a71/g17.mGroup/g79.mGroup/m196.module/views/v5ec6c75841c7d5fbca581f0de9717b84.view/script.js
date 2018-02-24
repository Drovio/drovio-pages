jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Toolbar navigation
	jq(document).on("click", ".wsNav", function(ev) {
		// Prevent default action
		ev.preventDefault();
		
		// Set state (if state is the same return)
		var stateHref = jq(this).attr("href");
		if (window.location.pathname == stateHref)
			return;
		state.push(stateHref);
		
		// Trigger container
		var targetID = "ws_"+jq(this).attr("id");
		jq("#"+targetID).trigger("load");
	});
	
	// Toggle settings panel
	jq(document).on('click', "#settingControl", function(){
		jq(".websiteDesingerPage .settingsPanel").toggleClass("open");
	});
});