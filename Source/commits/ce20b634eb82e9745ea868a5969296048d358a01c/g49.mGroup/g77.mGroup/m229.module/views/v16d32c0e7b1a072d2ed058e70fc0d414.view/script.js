var onSolveClick = function() {
	jq('.solveBugForm').removeClass('noDisplay');
	jq('.section.solution .noSolution').addClass('noDisplay');
};

var oncloseFormClick = function() {
	jq('.solveBugForm').addClass('noDisplay');
	jq('.section.solution .noSolution').removeClass('noDisplay');
	
	//Reset form also
};

var inlineActionShow = function(ev) {
	jq(this).closest('.inlineForm').find('.actionPrompt').addClass('noDisplay');
	jq(this).closest('.inlineForm').find('.actionForm ').removeClass('noDisplay');
};

var inlineActionHide = function(ev) {
	jq(this).closest('.inlineForm').find('.actionPrompt').removeClass('noDisplay');
	jq(this).closest('.inlineForm').find('.actionForm ').addClass('noDisplay');
};

jq(document).on('content.modified', function(){
	jq('.solveBugBtn').off('click');
	jq('.solveBugBtn').on('click', onSolveClick);
	
	jq('.solveBugForm  .closeFormBtn').off('click');
	jq('.solveBugForm  .closeFormBtn').on('click', oncloseFormClick);
	
	jq('.inlineForm .actionBtn.inlineFormShow').off('click');
	jq('.inlineForm .actionBtn.inlineFormShow').on('click', inlineActionShow );
	
	jq('.inlineForm .inlineFormHide').off('click');
	jq('.inlineForm .inlineFormHide').on('click', inlineActionHide);
});