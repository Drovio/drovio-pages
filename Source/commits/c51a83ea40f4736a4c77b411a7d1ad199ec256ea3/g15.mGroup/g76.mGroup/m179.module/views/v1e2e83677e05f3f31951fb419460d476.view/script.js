jq(document).on('content.modified', function()
{
	var context = jq('.contentPage').not('.noDisplay');
	var filtersHeight = context.find('.filtersWrapper').height();
	var height = context.height();
	var newHeight = height - filtersHeight;
	//jq('.analyticsPlainData .dataPresentation').css('height:'+newHeight.toString());
	context.find('.dataPresentation').height(newHeight);
});


jq(window).resize(function () 
{
      	var context = jq('.contentPage').not('.noDisplay');
	var filtersHeight = context.find('.filtersWrapper').height();
	var height = context.height();
	var newHeight = height - filtersHeight;
	//jq('.analyticsPlainData .dataPresentation').css('height:'+newHeight.toString());
	context.find('.dataPresentation').height(newHeight);
});