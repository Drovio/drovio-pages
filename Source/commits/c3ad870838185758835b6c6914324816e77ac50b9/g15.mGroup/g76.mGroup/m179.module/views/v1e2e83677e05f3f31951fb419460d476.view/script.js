jq(document).on('content.modified', function()
{
	var filtersHeight = jq('.contentPage .filtersWrapper').height();
	var height = jq('.contentPage').height();
	var newHeight = height - filtersHeight;
	//jq('.analyticsPlainData .dataPresentation').css('height:'+newHeight.toString());
	jq('.contentPage .dataPresentation').height(newHeight);
});


jq(window).resize(function () 
{
      	var filtersHeight = jq('.contentPage .filtersWrapper').height();
	var height = jq('.contentPage').height();
	var newHeight = height - filtersHeight;
	jq('.contentPage .dataPresentation').height(newHeight);
});