$(document).ready( function() {
	$($("table tr").not(":first").detach().get().reverse())
		.filter(":has(td[name='importance'].importance_critical)").insertAfter($("table tr").last()).end().end()
		.filter(":has(td[name='importance'].importance_medium)").insertAfter($("table tr").last()).end().end()
		.filter(":has(td[name='importance'].importance_low)").insertAfter($("table tr").last()).end().end();
	$("button").on("click", function(){
		$(".instructions").toggle();
	});
});