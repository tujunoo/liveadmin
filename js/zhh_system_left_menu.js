
$(function(){
	$('ul [class="ul_hide"]').hide();
	
	//显示与隐藏子级菜单
	/*$("#dir_menu_tree div").click( function(){
		if($(this).parent().parent().find("ul").length != 0){
			if($(this).parent().parent().find("ul").eq(0).css("display") == "none"){
				$(".ul_hide").slideUp();
				$(".div_add").removeClass("div_select");
				$(".div_add").removeClass("div_cut");
				
				$(this).addClass("div_select");
				$(this).addClass("div_cut");
				$(this).parent().parent().find("ul").slideDown(300);
			}else{
				$(this).removeClass("div_select");
				$(this).removeClass("div_cut");
				$(this).parent().parent().find("ul").slideUp(300);
			}
		};
	});
	*/
	//显示与隐藏子级菜单
	$("#dir_menu_tree .ul_show").each(function(i){
		$("#dir_menu_tree .ul_show").eq(i).find(".div_add").bind(
		"click",function(){
			$(this).toggleClass("div_select");
			$(this).toggleClass("div_cut");
			$(this).siblings(".ul_hide").slideToggle(300);
		  });
	
	});

	// 控制左边菜单的高度自适应
	$(window).bind('load scroll resize', function(){
		$(".leftcontentbox").height($(window).height()-40);
       $(".leftcontent").height($(window).height() - 55);
       $("#admin-main1").height($(window).height() - 40);
       $("#admin-main-con").height($(window).height() - 10);
	});
	
	$("#leftshowtit").click(function(){
		$("#leftshow_content").toggle();
	})
	
	
});