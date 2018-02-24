jq(document).one("ready.extra", function() {
	jq(document).off("nextStep.publisher");
	jq(document).on("nextStep.publisher", function(ev, value) {
		jq(".step"+(value-1)).addClass("disabled");
		jq(".step"+(value-1)).find(".stepContext").empty();
		jq(".step"+value).removeClass("disabled");
	});
});