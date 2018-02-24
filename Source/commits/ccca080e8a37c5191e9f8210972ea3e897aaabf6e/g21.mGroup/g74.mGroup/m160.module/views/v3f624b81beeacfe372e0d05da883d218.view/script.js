jq = jQuery.noConflict();

jq(document).one("ready", function() {
	jq(document).on("keyup", "input[name='firstname'], input[name='lastname'], input[name='middle_name']", function() {
		// Check if middle_name input is empty
		var completeName = jq("input[name='firstname']").val()+" "+jq("input[name='middle_name']").val()+" "+jq("input[name='lastname']").val();
		var standardName = jq("input[name='firstname']").val()+" "+jq("input[name='lastname']").val();
		var reversedName = jq("input[name='lastname']").val()+" "+jq("input[name='firstname']").val();
		if (jq("input[name='middle_name']").val() == "")
			completeName = standardName;
		
		console.log(completeName);
		console.log(standardName);
		console.log(reversedName);
		
		// Set options
		var completeOption = jq("select[name='display_name']").find("[value='complete']");
		var standardOption = jq("select[name='display_name']").find("[value='standard']");
		var reversedOption = jq("select[name='display_name']").find("[value='reversed']");
		
		completeOption.html(completeName);
		reversedOption.html(reversedName);
		if (completeName == standardName) // Remove standard value (if exists)
			standardOption.remove();
		else {
			// Add or modify (if exists)
			if (standardOption.length > 0)
				standardOption.html(standardName);
			else {
				standardOption = jq("select[name='display_name']").find("[value='complete']").clone(true);
				standardOption.attr("value", "standard");
				standardOption.attr("selected", false);
				standardOption.html(standardName);
				completeOption.after(standardOption);
			}
		}
	});
});