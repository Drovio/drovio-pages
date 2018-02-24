var jq=jQuery.noConflict();
jq(document).one("ready", function() {
	// Add server to list
	jq(document).on("website.servers.add", function(ev, serverInfo) {
		// Clone template item
		var templateItem = jq('.serverSettings .serversList .serverItem').first();
		var serverItem = templateItem.clone(true).html(serverInfo.name);
		
		// Copy attributes
		var templateAttr = templateItem.data("attr");
		var serverAttr = new Array();
		for (var k in templateAttr)
			serverAttr[k] = templateAttr[k];
			
		// Set server values
		serverAttr.sid = serverInfo.id;
		serverItem.data("attr", serverAttr);
		serverItem.data('sid', serverInfo.id);
		
		// Append item to list
		jq('.serverSettings .serversList .serverPool').append(serverItem);
		
		// Activate item
		serverItem.trigger("click");
	});
	
	// Remove server from list
	jq(document).on("website.servers.delete", function(ev, serverID) {
		// Find server item and remove
		jq('.serverSettings .serversList .serverPool .serverItem').each(function() {
			if (jq(this).data("sid") == serverID) {
				jq(this).remove();
				return false;
			}
		});
		
		// Empty server holder
		jq('.serverSettings .serverEditorHolder').html('');
	});
});