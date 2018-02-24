jq(document).on("content.modified", function() {
	if (switchButton.getStatus(jq("#testSwitch")))
		jq("#testerConfig").removeClass("noDisplay");
	else
		jq("#testerConfig").addClass("noDisplay");
});

jq("#testSwitch").on("status.modified", function() {
	if (switchButton.getStatus(jq(this)))
		jq("#testerConfig").removeClass("noDisplay");
	else
		jq("#testerConfig").addClass("noDisplay");
});