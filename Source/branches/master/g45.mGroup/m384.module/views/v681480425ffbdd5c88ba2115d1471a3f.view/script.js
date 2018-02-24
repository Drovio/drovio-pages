jq(document).one("ready", function() {
	// Load color theif from cdn
	jq.getScript("//cdn.drov.io/js/color-thief.js", function() {
		// Get image
		var imageSrc = jq(".ifp-appPlayerWrapper .ifp-navbar .app-ico img").attr("src");
		var image = new Image();
		image.crossOrigin = "Anonymous";
		image.src = imageSrc;
		
		// Init and get color
		var colorThief = new ColorThief();
		//var imageColor = colorThief.getColor(image);
	});
});