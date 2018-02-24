// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();
 
// let the document load
jq(document).one("ready.extra", function() {

	jq(".issuesViewer").on("scroll mousewheel wheel", function(ev) {
		var jqthis = jq(this);
		var searchBar = jq(this).find(".searchBar");
		
		if (jqthis.scrollTop() > 70)
			searchBar.addClass("docked");
		else
			searchBar.removeClass("docked");
	});
	
});