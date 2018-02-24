// Application Javasript Preloader
var jq;
JSPreloader = {
	app_id: null,
	akey: "",
	origin: "",
	initialized: false,
	init : function(app_id, akey, dev) {
		// Check if already initialized
		if (this.initialized)
			return;
		
		// Set initialized
		this.initialized = true;
		
		// Set local variables
		this.app_id = app_id;
		this.akey = akey;
		this.dev = dev;
		
		// Set host origin
		this.origin = window.location.origin;
		
		// Check if jQuery is loaded and load
		if (!window.jQuery)
			jq.getScript(scriptSource);
	},
	loadScript : function(scriptName, successCallback) {
		// Check if jQuery is loaded
		if (!window.jQuery)
			return false;
		
		// Create script url
		var scriptSource = "https://api.drov.io/" + (this.dev == 1 ? "dev/" : "") + "apps/js/" + this.app_id + "/" + this.akey + "/" + scriptName + "?origin=" + this.origin;
		jq.getScript(scriptSource).done(function(script, textStatus) {
			if (typeof successCallback == 'function') {
				successCallback.call(sender, response, status, xhr);
			}
		}).fail(function(jqxhr, settings, exception) {
			console.error("Error loading script");
		});
	},
	loadView : function(viewName, method, requestData, sender, successCallback, errorCallback, extraOptions) {
		// Create script url
		var viewLoaderUrl = "https://api.drov.io/" + (this.dev == 1 ? "dev/" : "") + "apps/" + this.app_id + "/" + this.akey + "/" + viewName + "?origin=" + this.origin;
		
		// Create extra options
		var ajaxOptions = {
			errorCallback: errorCallback,
			withCredentials: true,
		}
		
		// Extend ajax options
		ajaxOptions = jq.extend(ajaxOptions, extraOptions);
		
		// Make request
		this.request(viewLoaderUrl, method, requestData, sender, successCallback, ajaxOptions);
	},
	request : function(ajaxUrl, method, requestData, sender, successCallback, ajaxOptions) {
		// Init object for default ajax options
		var options = {
			withCredentials: false,
			cache: true,
			crossDomain: true,
			processData: false,
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			dataType: "json",
			xhr: jq.ajaxSettings.xhr()
		}
		
		// Extend ajax options
		options = jq.extend(options, ajaxOptions);
		
		// Make request
		var request = jq.ajax({
			url: ajaxUrl,
			type: method,
			data: requestData,
			context: sender,
			dataType: options.dataType,
			contentType: options.contentType,
			processData: options.processData,
			cache: options.cache,
			crossDomain: options.crossDomain,
			success: function(response, status, xhr) {
				// run successCallback function, if any
				if (typeof successCallback == 'function') {
					successCallback.call(sender, response, status, xhr);
				}
			},
			complete: function(jqXHR, textStatus) {
				// run completeCallback function, if any
				if (typeof options.completeCallback == 'function') {
					options.completeCallback.call(sender, jqXHR);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error("There was an error with the request.");
				// run errorCallback function, if any
				if (typeof options.errorCallback == 'function') {
					options.errorCallback.call(sender, jqXHR);
				}
			},
			statusCode: {
				404: function() {
					// Report error
				},
				500: function() {
					// Report error
				}
			},
			xhrFields: { 
				withCredentials: options.withCredentials
			},
			xhr: function() {
				// Check if xhr is function and execute it
				if (typeof options.xhr == 'function')
					return options.xhr.call(sender);
				
				// Otherwise just return the object
				return options.xhr
			}
		});

		return request;
	}
}


// Initialize JSPreloader callback
var jQueryLoadCallback = function() {
	// Initialize Application Preloader
	var devEnv = false;
	if (typeof dev != "undefined")
		devEnv = dev;
	JSPreloader.init(app_id, akey, devEnv);

	// Load script
	var successCallback = null;
	if (typeof callback != "undefined")
		successCallback = callback;
	JSPreloader.loadScript(app_js, successCallback);
}


// Check if jQuery is loaded and load
if (!window.jQuery) {
	getScript("http://cdn.drov.io/js/jquery/jquery-1.7.2.min.js", function() {
		// Initialize Preloader
		jq = jQuery.noConflict();
		jQueryLoadCallback.call(this);
	});
} else {
	// Initialize Preloader
	jQueryLoadCallback.call(this);
}


// getScript equivalent without jQuery
function getScript(source, callback) {
	// Create script element
	var script = document.createElement('script');
	script.async = 1;
	// Append
	var prior = document.getElementsByTagName('script')[0];
	prior.parentNode.insertBefore(script, prior);

	// Add listeners
	script.onload = script.onreadystatechange = function( _, isAbort ) {
		if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState) ) {
			script.onload = script.onreadystatechange = null;
			script = undefined;

			// Check and call callback
			if (!isAbort && callback)
				callback();
		}
	};

	// Set script source
	script.src = source;
}