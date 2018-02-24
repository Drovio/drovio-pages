var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	console.log("manual viewer ready");
	// Handle scroll and show top button
	jq(".dev-mainpage").scroll(function() {
		var y = jq(this).scrollTop();
		if (y > 350)
			jq('.navitem.top').show();
		else
			jq('.navitem.top').hide();
	});
	
});