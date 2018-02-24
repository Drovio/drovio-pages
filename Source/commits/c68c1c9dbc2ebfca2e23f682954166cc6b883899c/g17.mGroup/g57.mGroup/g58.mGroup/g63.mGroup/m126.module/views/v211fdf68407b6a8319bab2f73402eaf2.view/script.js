/*jq("body").bind("DOMNodeInserted", function(){
	//alert('hi');
	console.log('gotcha');
	
	
	
});
*/

jq(document).on('content.modified', function(){
	/* Template Page Structure Delete */
	jq('#psBodyContent').off("delete.toggle")
	jq('#psBodyContent').on("delete.toggle", function(event, param) {
			var item = jq('#psBodyContent > #' + param); 
			item.find('.itemRow').css('height','auto');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
			
			
	});
	jq('#psBodyContent').off("delete.success")
	jq('#psBodyContent').on("delete.success", function(event, param) {
			var item = jq('#psBodyContent > #' + param);
			item.find('.itemRow').css('height','');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
	});
	jq('#psBodyContent').off("delete.error")
	jq('#psBodyContent').on("delete.error", function(event, param) {
			var item = jq('#psBodyContent > #' + param);
			item.find('.itemRow').css('height','');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
	});
	
	/* Template Theme Delete */
	jq("#thBodyContent").off("delete.toggle");
	jq("#thBodyContent").on("delete.toggle", function(event, param) {
			var item = jq('#thBodyContent > #' + param); 
			item.find('.itemRow').css('height','auto');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
			var r = jq('.deletePromtHolder button[data-formdissmiss]');
			console.log(r);
			item.find('.deletePromtHolder button[data-formdissmiss]').on('click', function(){ 
					var item = jq('#thBodyContent > #' + param);
					item.find('.itemRow').css('height','');
					item.find('.deletePromtHolder').toggleClass('noDisplay');
					item.find('.itemInfo').toggleClass('noDisplay');
				});		
	});
	jq("#thBodyContent").off("delete.success");
	jq('#thBodyContent').on("delete.success", function(event, param) {
			var item = jq('#thBodyContent > #' + param);
			item.find('.itemRow').css('height','');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
	});
	jq("#thBodyContent").off("delete.error");
	jq('#thBodyContent').on("delete.error", function(event, param) {
			var item = jq('#thBodyContent > #' + param);
			item.find('.itemRow').css('height','');
			item.find('.deletePromtHolder').toggleClass('noDisplay');
			item.find('.itemInfo').toggleClass('noDisplay');
	});
	
	
	
	/* Template Info Edit */
	jq("#templateInfoOverview").off("edit.toggle");
	jq('#templateInfoOverview').on("edit.toggle", function(event, param) {
			var item = jq('#templateInfoOverview > .bodyContent');
			item.find('.editorHolder').toggleClass('noDisplay');
			item.find('.viewerHolder').toggleClass('noDisplay');
			
			jq('#templateInfoOverview > button[name="infoEditorToggler"]').addClass('disabled');
			jq('#templateInfoOverview > button[name="infoEditorToggler"]').prop( "disabled", true);
			item.find('.editorHolder > button[data-formdissmiss]').on('click', function(){
					jq('#templateInfoOverview > button[name="infoEditorToggler"]').removeClass('disabled');
					jq('#templateInfoOverview > button[name="infoEditorToggler"]').prop( "disabled", false );
					
					var item = jq('#templateInfoOverview > .bodyContent');
					item.find('.editorHolder').toggleClass('noDisplay');
					item.find('.editorHolder').empty();
					item.find('.viewerHolder').toggleClass('noDisplay');
				});
	});
	jq("#templateInfoOverview").off("edit.success");
	jq('#templateInfoOverview').on("edit.success", function(event, param) {
			var item = jq('#templateInfoOverview > .bodyContent');
			item.find('.editorHolder').toggleClass('noDisplay');
			item.find('.editorHolder').empty();
			item.find('.viewerHolder').toggleClass('noDisplay');
	});
});