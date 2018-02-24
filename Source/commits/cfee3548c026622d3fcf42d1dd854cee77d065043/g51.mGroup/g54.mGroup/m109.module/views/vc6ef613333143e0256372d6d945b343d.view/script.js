// jQuery part
// change annotation from "$" to "jq"
var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {

	jq(document).on("keydown", ".search", function() {
		var jqthis = jq(this);
		setTimeout(function(){
			var val = jqthis.val().toLowerCase();
			//var keys = jq.trim(val).split(" ");
			if (jq.trim(val) == "") {
				jq(".wrapper").find("[filter]").css("display", "");
				return;
			}
			
			//for (var k in keys)
			jq(".wrapper").find("[filter]").css("display", "none")
				.filter("[filter*='"+val+"']").css("display", "")
				.filter(".treeItem").find("[filter]").css("display", "");
		}, 200);
	});
	
	jq(document).on("click", ".objectContents", function(ev){
		if (jq(ev.target).get(0).tagName == "INPUT")
			return;
		
		var checkbox = jq(this).find("[type='checkbox']").first();
		var checkState = checkbox.prop("checked");
		checkbox.prop("checked", !checkState);	
	});
	
});