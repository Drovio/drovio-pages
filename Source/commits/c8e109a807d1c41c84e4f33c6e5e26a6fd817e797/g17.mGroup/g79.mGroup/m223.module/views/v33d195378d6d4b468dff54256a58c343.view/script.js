var jq=jQuery.noConflict();

// let the document load
jq(document).one("ready.extra", function() {
	jq(document).off("website.servers.add");
	jq(document).on("website.servers.add", function(odj, params) {
		var arr = params.split(':');
		/*
		var item = jq('.serversList .serverItem.itemTemplate').clone;
		item.attr('data-serverid', arr[0]);
		item.find('span.serverName').html(arr[1]);
		jq('.serversList .serverPool').append(item);
		*/
	});
	
	jq(document).off("website.servers.delete");
	jq(document).on("website.servers.delete", function(odj, params) {
		var serverPool = jq('.serversList .serverPool');
		var server = serverPool.find('[data-serverid="' + params + '"]').remove();
	});
});