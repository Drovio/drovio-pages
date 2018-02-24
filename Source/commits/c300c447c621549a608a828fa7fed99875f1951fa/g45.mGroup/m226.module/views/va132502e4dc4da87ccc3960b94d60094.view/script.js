jq = jQuery.noConflict();

jq(document).one("ready", function() {
	
	jq(document).on("content.modified", function() {
		// Set dashboard sizes for the first time
		setDashboardSizes();
		
		// Trigger window resize
		jq(window).off("resize");
		jq(window).on("resize", function() {
			setDashboardSizes();
		});
		
		jq(document).on("click", ".navBall", function() {
			scrollTo(jq(this).index());
		});
	});
	
	
	// Set listener for mouse wheel inside the dashboard
	jq(document).on("mousewheel wheel DOMMouseScroll", ".bizDashboard .slideContainer", function(ev) {
		var evt = window.event || ev;
		var delta = evt.detail ? evt.detail * (-120) : evt.wheelDelta;
		//console.log(delta);
	});
	
	
	// Set arrow listener
	jq(document).on("keydown", function(ev) {
		if (ev.keyCode == 38) {
			// Scroll up
			scroll(-1);
		} else if (ev.keyCode == 40) {
			// Scroll down
			scroll(1);
		}
	});
	
	var scroll = function(orientation) {
		// Switch navBall
		var numBalls = jq(".navBall").length;
		var currentBall = jq(".navBall.active").index();
		var nextBall = (currentBall + orientation);
		nextBall = nextBall < 0 ? 0 : nextBall;
		nextBall = nextBall >= numBalls ? numBalls - 1 : nextBall;
		scrollTo(nextBall);
	}
	
	var scrollTo = function(index) {
		// Switch navBall
		jq(".navBall").removeClass("active");
		jq(".navBall").eq(index).addClass("active");
		
		// Set slide according to nextBall
		var slideHeight = jq(".slide").first().outerHeight();
		var marginTop = -slideHeight * index;
		jq(".slide").first().animate({
			"margin-top": marginTop
		}, 200);
	}
	
	var setDashboardSizes = function() {
		// Set navigation to center
		var navBarHeight = jq(".navBar").first().height();
		var navBallsHeight = jq(".navBalls").first().height();
		var margin = navBarHeight - navBallsHeight;
		margin = margin < 0 ? 0 : margin;
		jq(".navBalls").first().css("margin-top", margin/2 +"px");
		
		// Set slide's grid to center
		var sliderHeight = jq(".slideContainer").first().height();
		var gridHeight = jq(".slide .grid").first().height();
		var margin = sliderHeight - gridHeight;
		margin = margin < 0 ? 0 : margin;
		//jq(".slide .grid").css("margin-top", margin/2 +"px");
		
		// Set gridbox size
		var gbWidth = jq(".slide .grid .gb").first().width();
		jq(".slide .grid .gb").css("height", gbWidth+"px");
	}
});