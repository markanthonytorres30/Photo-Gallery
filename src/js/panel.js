

 function init_dropzone(){
 	return;
 }

 function init_panel(){

	$(".panel .item a").unbind();

	init_dropzone();

	// On clicking an item
	$(".panel .item a").click(function(){

		// Select item
		$(".panel .selected").removeClass("selected");
		$(this).parent().addClass("selected");

		// Load image
		$(".image_panel").load($(this).attr("href")+"&j=Pan",function(){
			init_image_panel($(this).attr("href"));	
		});

		// Load infos
		$(".infos").load($(this).attr("href")+"&j=Inf",function(){
			init_image_panel($(this).attr("href"));	
		});
		
		update_url($(this).attr("href"));

		// Edit layout
		$(".panel").hide().addClass("linear_panel").removeClass("panel");
		$(".image_panel,.linear_panel").slideDown("fast",function(){
			$(".image_panel a").css("height","100%");
		});


		return false;

	});
}



$("document").ready(function(){
	init_panel();
	if ($(".menu .selected:last").length > 0) $(".menu").scrollTo($(".menu .selected:last"));
});