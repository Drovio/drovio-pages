var onSolveClick = function() {
	jq('.solveBugForm').removeClass('noDisplay');
};

jq(document).on('content.modified', function(){
	jq('.solveBugBtn').off('click');
	jq('.solveBugBtn').on('click', onSolveClick);
});