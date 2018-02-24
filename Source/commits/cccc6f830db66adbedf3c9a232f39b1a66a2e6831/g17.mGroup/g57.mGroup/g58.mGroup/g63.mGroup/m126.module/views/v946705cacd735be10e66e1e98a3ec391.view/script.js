jq(document).on("content.modified", "#psBodyContent", function(){
	console.log("11111111111111");
	jq("#overview_addPs").trigger("dispose.popup");
	
	// Get all items with date-effects = glow
	// Apply glow
	
	//Remove date-effects = glow attribute
});

jq(document).on("content.modified", "#pageStructureOverview", function(){
	console.log(this);
	jq(this).find('.moduleContainer').each(function(){
				var id = jq(this).attr('id');
				console.log(this);
				jq(this).find('.controls > span').data('attr').holder = id;
			});
	
});


jq(document).on("content.modified", "#thBodyContent", function(){
	console.log("11111111111111")	
	jq("#overview_addTh").trigger("dispose.popup");
});

jq(document).on('click', "div[data-pageSelector='display']", function() {
	jq('#themeStructureSelector').removeClass('noDisplay');
	jq('#editorContainer').removeClass('full');
	jq('#editorContainer').addClass('semi');

});

jq(document).on('click', "div[data-pageSelector='hide']", function() {
	jq('#themeStructureSelector').addClass('noDisplay');
	jq('#editorContainer').removeClass('semi');
	jq('#editorContainer').addClass('full');

});