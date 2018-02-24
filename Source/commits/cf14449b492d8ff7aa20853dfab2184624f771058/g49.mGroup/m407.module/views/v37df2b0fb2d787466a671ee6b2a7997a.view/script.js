var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload roadmap
	jq(document).on("roadmap.list.reload", function() {
		jq("#roadmapList").trigger("reload");
	});
	
	// Generate hashtag
	jq(document).on("keyup", ".rdFormContainer input[name='title']", function() {
		// Get current title
		var roadmapTitle = jq(this).val();
		
		// Generate hashtag
		var roadmapHashtag = roadmapTitle.replace(/ /g, "_").toLowerCase();
		jq(".rdFormContainer input[name='hashtag']").val(roadmapHashtag);
	});
});