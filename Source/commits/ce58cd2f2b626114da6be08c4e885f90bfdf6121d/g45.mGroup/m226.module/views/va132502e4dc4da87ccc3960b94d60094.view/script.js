jq = jQuery.noConflict();

jq(document).one("ready", function() {
	// Init dashboard functionality
	dashboard.init();
	
	// Prevent Reload or Redirect when an application is open
	jq(window).on('beforeunload', function() {
		if (jq(".apps_pool .applicationPlayer").length > 0)
			return "There are active applications in the dashboard.";
	});
});

dashboard = {
	scrolling: false,
	scrollDelta: 0,
	init: function() {
		// Set content modified listener
		jq(document).on("content.modified", function() {
			// Set dashboard sizes for the first time
			dashboard.setSlides();
			
			// Set slides visible
			jq(".bossDashboardPage .slideContainer").removeClass("loading");
		});
		
		// Trigger window resize
		jq(window).on("resize", function() {
			dashboard.setSlides();
		});
		
		// Set event listeners for showing the dashboard
		jq(document).on("click", "#bsDashboard", function() {
			jq(".apps_pool").removeClass("open");
		});
		
		// Set nav balls navigation listeners
		jq(document).on("click", ".navBall", function() {
			dashboard.scrollTo(jq(this).index());
		});
		
		// Set listener for mouse wheel inside the dashboard
		jq(document).on("mousewheel wheel DOMMouseScroll", ".bizDashboard .slideContainer", function(ev) {
			var evt = window.event || ev;
			var delta = evt.detail ? evt.detail * (-120) : evt.wheelDelta;
			var deltaAbs = Math.abs(delta);
			
			// Check maximum delta and if delta is decreasing and stop
			if (deltaAbs > 100 || deltaAbs - dashboard.scrollDelta < 0) {
				dashboard.scrollDelta = deltaAbs;
				return false;
			}
			
			// Set current delta
			dashboard.scrollDelta = deltaAbs;
			
			// Scroll
			if (deltaAbs > 50 && !dashboard.scrolling) {
				if (delta < 0)
					dashboard.scroll(1);
				else
					dashboard.scroll(-1);
			}
			else
				return false;
			
		});
		
		// Set arrow listener for scrolling the slides
		jq(document).on("keydown", function(ev) {
			if (ev.keyCode == 38) {
				// Scroll up
				dashboard.scroll(-1);
			} else if (ev.keyCode == 40) {
				// Scroll down
				dashboard.scroll(1);
			}
		});
		
		// Set application listeners
		jq(document).on("click", ".slideContainer .slide .app", function() {
			// Get application info from the tile
			var applicationID = jq(this).data("app").id;
			
			// Check if already loaded
			var exists = false;
			jq(".apps_pool").find(".applicationPlayer").each(function() {
				var holderAppID = jq(this).data("app").id;
				if (applicationID == holderAppID) {
					exists = true;
					return false;
				}
			});
			
			// Check if application already exists
			var status = dashboard.switchToApp(applicationID);
			if (!status) {
				// Load new app
				var extraParams = "appID="+applicationID;
				ModuleLoader.load(jq(this), 272, "", "", extraParams, null, null, null, true);
			}
		});
		
		// Resize app
		jq(document).on("click", ".slideContainer .slide .app .size", function(ev) {
			// Stop bubbling
			ev.stopPropagation();
			
			// Resize
			var jqGb = jq(this).closest(".gb");
			var size = jqGb.data("size");
			var newSize = (size > 1 ? 1 : 4);
			jqGb.data("size", newSize);
			jqGb.removeClass("s"+size);
			jqGb.addClass("s"+newSize);
			
			// Update context
			var char = (newSize > 1 ? "<" : ">");
			jq(this).html(char);
		});
		
		// Switch application listener
		jq(document).on("application.switch", function(ev, appID) {
			dashboard.switchToApp(appID);
		});
		
		/*
		// Change theme action
		jq(document).on("click", ".dashSettings .settings.ico", function() {
			// Get theme template
			var themeTemplate = jq(".apps_grid .thm").first().clone().removeAttr("class").addClass("thm").addClass("none");
			
			// Add theme to themes
			jq(".apps_grid .thm").first().after(themeTemplate);
			
			// Add wait time for transition and remove old theme
			setTimeout(function() {
				themeTemplate.addClass("th15");
				setTimeout(function() {
					jq(".apps_grid .thm").first().remove();
					jq(".apps_grid .thm").first().removeClass("none");
				}, 5000);
			}, 10);
		});*/
	},
	switchToApp: function(applicationID) {
		// Get application holder and set active
		var jqAppHolder = dashboard.getApplicationHolder(applicationID);
		
		// Check if app exists
		if (jqAppHolder.length == 0 || jq.type(jqAppHolder) == "undefined")
			return false;
		
		jq(".apps_pool .applicationPlayer").addClass("noDisplay");
		jqAppHolder.removeClass("noDisplay");
		
		// Activate application viewer
		jq(".apps_pool").addClass("open");
		
		return true;
	},
	closeApp: function(applicationID) {
		// Get application holder and remove
		var jqAppHolder = dashboard.getApplicationHolder(applicationID);
		if (jqAppHolder.length == 0 || jq.type(jqAppHolder) != "undefined")
			jqAppHolder.remove();
		
		// Get application tile and remove
		var jqAppTile = dashboard.getApplicationTile(applicationID)
		if (jqAppTile.length == 0 || jq.type(jqAppTile) != "undefined")
			jqAppTile.remove();
		
	},
	getApplicationHolder: function(applicationID) {
		return jq(".apps_pool .applicationPlayer").filter(function() {
			return jq(this).data("app").id == applicationID;
		});
	},
	getApplicationTile: function(applicationID) {
		return jq("#activeAppsContainer .applicationTile").filter(function() {
			return jq(this).data("app").id == applicationID;
		});
	},
	setSlides: function() {
		// Set navigation to center
		var navBarHeight = jq(".navBar").first().height();
		var navBallsHeight = jq(".navBalls").first().height();
		var margin = navBarHeight - navBallsHeight;
		margin = margin < 0 ? 0 : margin;
		jq(".navBalls").first().css("margin-top", margin/2 +"px");
		
		// Set slide's grid to center
		var sliderHeight = jq(".slideContainer").first().height();
		var sliderWidth = jq(".slideContainer").first().width();
		var vPadding = 70;
		var hPadding = 150;
		var maxGridWidth = sliderWidth - hPadding*2;
		var minGridWidth = 800;
		maxGridWidth = maxGridWidth > 1200 ? 1200 : maxGridWidth;
		var gridHeight = sliderHeight - vPadding*2;
		var gridWidth = gridHeight * 2;
		
		// Check and set max width
		if (gridWidth > maxGridWidth) {
			gridWidth = maxGridWidth;
			gridHeight = gridWidth/2;
		}
		
		// Check min width
		if (gridWidth < minGridWidth) {
			gridWidth = minGridWidth;
			gridHeight = gridWidth / 2;
		}
		vPadding = (sliderHeight - gridHeight) / 2;
		hPadding = (sliderWidth - gridWidth) / 2;
		jq(".slide").css("padding", vPadding + "px " + hPadding + "px");
	},
	scrollTo: function(index) {
		dashboard.scrolling = true;
		
		// Switch navBall
		jq(".navBall").removeClass("active");
		jq(".navBall").eq(index).addClass("active");
		
		// Set slide according to nextBall
		var slideHeight = jq(".slide").first().outerHeight();
		var marginTop = -slideHeight * index;
		jq(".slide").first().animate({
			"margin-top": marginTop
			}, {
			duration: 200,
			complete: function() {
				dashboard.scrolling = false;
			}
		});
	},
	scroll: function(orientation) {
		// Switch navBall
		var numBalls = jq(".navBall").length;
		var currentBall = jq(".navBall.active").index();
		var nextBall = (currentBall + orientation);
		nextBall = nextBall < 0 ? 0 : nextBall;
		nextBall = nextBall >= numBalls ? numBalls - 1 : nextBall;
		dashboard.scrollTo(nextBall);
	}
}