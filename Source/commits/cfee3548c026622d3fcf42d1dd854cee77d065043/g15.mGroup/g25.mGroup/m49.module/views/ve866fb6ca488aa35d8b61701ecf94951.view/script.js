var jq = jQuery.noConflict();


jq(document).off("click", "#refreshQueries");
jq(document).on("click", "#refreshQueries", function() {
	jq("#dbQueriesTree").trigger("saveState");
	jq("#queryViewer").trigger("reload");
});