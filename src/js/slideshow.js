

var slideshow_status = 0;
var timer = 0;

function run_slideshow(){
	$("#next a").click();
}

function toggleFullScreen() {
  var doc = window.document;
  var docEl = doc.getElementById("image_panel");

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
    requestFullScreen.call(docEl);
  }
  else {
    cancelFullScreen.call(doc);
  }
}

function start_slideshow(){	
	slideshow_status = 1;
	timer = setInterval('run_slideshow()',7000);
	$(".image_panel").css("position","fixed");
	$(".image_panel").css("z-index",5000);
	$(".image_panel").animate({bottom:'0'},200);
	hide_links();

	toggleFullScreen();

}

function play_pause_slideshow(){
	if(slideshow_status == 1){
		pause_slideshow();
	}else{
		play_slideshow();
	}
}

function play_slideshow(){
	start_slideshow();
	$("#pause").show();
	$("#play").hide();
}

function pause_slideshow(){
	slideshow_status = 2;
	clearInterval(timer);
	$("#play").show();
	$("#pause").hide();
}

function stop_slideshow(){
	slideshow_status = 0;
	clearInterval(timer);
	$(".image_panel").animate({bottom:'150'},200);
	$(".image_panel").css("position","absolute");
	$(".image_panel").css("z-index",50);
	show_links();
	toggleFullScreen();
}

function init_slideshow_panel(){
	$("#image_bar #pause").hide();
	$("#image_bar #play").hide();
	$("#image_bar #stop").hide();

	$("#slideshow").unbind();
	$("#slideshow").click(function(){
		start_slideshow();
		return false;
	});
	
	$("#stop").unbind();
	$("#stop").click(function(){
		stop_slideshow();
		return false;
	});
	
	$("#pause").unbind();
	$("#pause").click(function(){
		pause_slideshow();
		return false;
	});
	
	$("#play").unbind();
	$("#play").click(function(){
		play_slideshow();
		return false;
	});
}

function show_links(){
	$('#image_bar #back').show();
	$('#image_bar #img').show();
	$('#image_bar #get').show();
	$('#image_bar #slideshow').show();
	$('#image_bar #pause').hide();
	$('#image_bar #play').hide();
	$('#image_bar #stop').hide();
}

function hide_links(){
	$('#image_bar #back').hide();
	$('#image_bar #img').hide();
	$('#image_bar #get').hide();
	$('#image_bar #slideshow').hide();
	$('#image_bar #stop').show();
	if(slideshow_status == 1){
		$('#image_bar #pause').show();
		$('#image_bar #play').hide();
	}else{
		$('#image_bar #play').show();
		$('#image_bar #pause').hide();
	}
}
