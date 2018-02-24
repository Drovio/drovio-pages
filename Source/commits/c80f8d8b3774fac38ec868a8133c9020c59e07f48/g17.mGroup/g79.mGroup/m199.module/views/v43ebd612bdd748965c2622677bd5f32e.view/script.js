$('#i_f100751981_next_step').click(function(){
	alert("Step 2!!");
});










/*					
jq(document).on('ready.extra', function(){
		// Add event for replicating form values
		jq(document).on('keyup', '#createForm input[name = "wsTitle"], #createForm input[name = "wsName"], #createForm textarea[name = "wsDescription"]', function(ev) {
			var name = jq(this).attr('name');
			var value = jq(this).val();
			if (!value)
				value = jq(this).attr('value');
			jq('#finishForm input[name = "'+ name +'"]').val(value);
		});
		
		// Dispose event on the popup
		jq(document).on('click', '#finishPPdisopse', function(){
			jq(this).trigger("dispose");
		});
		
		// dispose error report
		jq(document).on('click', '.wizardErrorReport span.disposeReport', function(){
			jq('.wizardErrorReport').addClass('noDisplay');
			jq('.wizardErrorReport > span.errorContent').html('');
		});
	});
	
jq(document).on('content.modified', function(){
	jq(document).off("step.success");
	jq(document).one("step.success", function(event, param) {
			event.stopPropagation();
			stepForward();
	});
	
	jq(document).off("step.skip");
	jq(document).one("step.skip", function(event, param) {
			event.stopPropagation();
			var skips = parseInt(param, 10);
			for(i=0; i < skips; i++)
			{
				stepForward(true);
			}
	});
	
	jq(document).off("web.wizard.error");
	jq(document).one("web.wizard.error", function(event, param) {
			event.stopPropagation();
			var host = window.location.href;
			var newUrl = url.removeVar(host, 'id');
			window.history.pushState('', '', newUrl);
			
			// Set error
			jq('.wizardErrorReport').removeClass('noDisplay');
			jq('.wizardErrorReport span.errorContent').html(param);
	});
});	

function stepForward(skip)
{
 	var current = jq('.step.current');
	var currentStep = parseInt(current.data('num'), 10);
	var next = jq('.step[data-num="'+ (currentStep + 1) +'"]');
	
	next.addClass('current');
	if(skip)
		next.addClass('skipped');
	current.removeClass('current');
	if(!current.hasClass('skipped'))
		current.addClass('visited');
}

function stepBackward()
{

}

jq(document).on('click', '[data-finishpp="show"]', function(ev) {
						jq(this).popup.type = "persistent toggle";
						//jq(this).popup.position = 'bottom|right';
						
						var popupContent = jq('.contentHolder > .finishPopup').clone(true);
						popupContent.removeClass('noDisplay')
						jq(this).popup(popupContent);
					});
*/