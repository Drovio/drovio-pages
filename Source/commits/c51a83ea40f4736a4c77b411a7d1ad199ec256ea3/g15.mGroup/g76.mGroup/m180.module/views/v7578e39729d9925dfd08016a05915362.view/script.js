/*
jq(document).on('content.modified', function()
{
	var filtersHeight = jq('.analyticsPlainData .filtersWrapper').height();
	var height = jq('.analyticsPlainData').height();
	var newHeight = height - filtersHeight;
	//jq('.analyticsPlainData .dataPresentation').css('height:'+newHeight.toString());
	jq('.analyticsPlainData .dataPresentation').height(newHeight);
});


jq(window).resize(function () 
{
      	var filtersHeight = jq('.analyticsPlainData .filtersWrapper').height();
	var height = jq('.analyticsPlainData').height();
	var newHeight = height - filtersHeight;
	jq('.analyticsPlainData .dataPresentation').height(newHeight);
});
*/