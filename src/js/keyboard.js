
$("document").ready(function(){
	$("body").keyup(function(event){
	var keyCode = event.which;
	if (keyCode == 0 && event.keyCode != undefined)
		keyCode = event.keyCode;
	
		switch(keyCode)
		{
			case $.ui.keyCode.RIGHT	:
				if($("#image_bar #next").is(":visible")){
					$("#image_bar #next a").click();
				}
				event.preventDefault();
				break;
			
			case $.ui.keyCode.LEFT	:
				if($("#image_bar #prev").is(":visible")){
					$("#image_bar #prev a").click();
				}
				event.preventDefault();
				break;
				
			case $.ui.keyCode.UP	:
				if ($("#image_bar #back").is(":visible")){
					$("#image_bar #back").click();
				}else if($("#image_bar #stop").is(":visible")){
					$("#image_bar #stop").click();
				}
				event.preventDefault();
				break;

			case $.ui.keyCode.SPACE :
				if($("#image_bar #pause").is(":visible")){
					$("#image_bar #pause").click();
				}else if($("#image_bar #play").is(":visible") || $("#image_bar #slideshow").is(":visible")){
					$("#image_bar #play").click();
				}
				event.preventDefault();
				break;

			case $.ui.keyCode.ESCAPE :
				if($("#image_bar #stop").is(":visible")){
					$("#image_bar #stop").click();
				}
				event.preventDefault();
				break;
		}

	});

});
