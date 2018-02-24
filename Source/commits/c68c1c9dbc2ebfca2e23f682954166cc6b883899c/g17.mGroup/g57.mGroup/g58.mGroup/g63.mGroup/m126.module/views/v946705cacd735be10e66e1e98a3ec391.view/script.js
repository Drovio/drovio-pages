// Display / Open pageStructure selector bar
jq(document).on('click', "div[data-pageSelector='display']", function() {
	jq('#themeStructureSelector').removeClass('noDisplay');
	jq('#editorContainer').removeClass('full');
	jq('#editorContainer').addClass('semi');

});
// Hide / Close pageStructure selector bar
jq(document).on('click', "div[data-pageSelector='close']", function() {
	jq('#themeStructureSelector').addClass('noDisplay');
	jq('#editorContainer').removeClass('semi');
	jq('#editorContainer').addClass('full');

});