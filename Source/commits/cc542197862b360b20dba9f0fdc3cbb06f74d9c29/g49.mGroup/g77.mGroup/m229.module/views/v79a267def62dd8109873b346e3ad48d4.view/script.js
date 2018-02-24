var onBugSelect = function() {
	jq('.projectIssuesPage .itemsHolder .issue-summary').removeClass('selected');
	jq(this).closest('.issue-summary').addClass('selected');
	
	var bid = jq(this).closest('.issue-summary').attr('id');
	bid = bid.replace('issue-summary-','');
	
	jq('.sideBox.actions .actionList').removeClass('noDisplay');
	jq('.sideBox.actions .noActions').addClass('noDisplay');
	
	jq('.sideBox.actions').data('targetid', bid);
};

var onBugDeselect = function() {
	jq(this).closest('.issue-summary').removeClass('selected'); 
	
	jq('.sideBox.actions .actionList').addClass('noDisplay');
	jq('.sideBox.actions .noActions').removeClass('noDisplay');
	
	jq('.sideBox.actions').removeData('targetid');
};

jq(document).on('content.modified', function() {
	jq('.editBtn').off('click');
	jq('.editBtn').on('click', onBugSelect);
	
	jq('.deselectBtn').off('click');
	jq('.deselectBtn').on('click', onBugDeselect);

});