
$(document).ready(function(){

	var cookieName = 'menu';
	var cookieOptions = {expires: 1000 * 60 * 60 * 2, path: '/'};

	$(".box1 li ul:not(.xtree .box1 li ul)").hide();
	
	$(".box1 h3").click(function(){
		$(this).addClass("level1");
		$(this).next().slideToggle('slow');
	});
	

	$(".box1 ul ul ul h4:not('.box1 ul ul ul ul h4')").mouseover(function(){
	
		$(".rating").hide();
		$(".box1 ul ul ul ul:not('.box1 ul ul ul ul ul ul')").hide();
		$("#mybaner").remove();
		$(this).next().css('left', $(this).parent().width());
		
		$(this).next().append($("#gbaner"));//.css("height","50");
		$(this).next().show();

		var position = $(this).offset();
		var scrl = $("body").scrollTop();
		var hs = window.document.documentElement.scrollTop;
		var sm = screen.availHeight - position.top - 300 + scrl + hs;
		if (sm < 0)
		{
			$(this).next().css('top', - $(this).next().height() + 22);
		} else
		{
			$(this).next().css('top', "0");
		}
		
			

	});	
	
	$(".box1 ul ul ul:not('.box1 ul ul ul ul')").mouseleave(function(){
		$(".box1 ul ul ul ul:not('.box1 ul ul ul ul ul ul')").hide();
    });	
	
	$(".box1").mouseleave(function(){
		$(".box1 ul ul ul ul:not('.box1 ul ul ul ul ul ul')").hide();
		$(".rating").show();
    });
	
	
	$("#leftCol").mouseleave(function(){		
		wid = $("body").width();
		if(wid < 1230) {
			$("#leftCol").css("display", "none");
			$("#nav").removeClass("showleft");
			$("#rightCol").css("z-index", "0");
			$("#knopka").css("left", "0px");
			$("#knopka").css("display", "block");
			}
	});
	
	
	$(".box1 ul ul ul ul ul").mouseleave(function(){
		if($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().is(".box1"))
			$(".box1 ul ul ul ul ul:not('.box1 ul ul ul ul ul ul')").hide();
		
    });

		

	$(".box1 ul ul h4").click(function(){
		$(this).addClass("level1");
		var position = $(this).offset();
		var bh = $("body").height();
		var h=$(this).next().height();
		var s = $("body").scrollTop() + window.document.documentElement.scrollTop;
		var bd2 = $(this).outerHeight();
		var sm = bh - position.top - bd2 - 5 - h + s;
//		var sm = bh - position.top - bd2 - 5 - h + s - 30;
		if($(this).next().is(":hidden") && sm<0)
						jQuery.scrollTo(s - sm, 0);
		if($(this).parent().parent().parent().parent().parent().is(".box1")){
			$(this).next().slideToggle();
			}
		else
		{
			if ($(this).next().is(":hidden")) {
				if($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().is(".box1"))	
				{
						
					//$(".box1 ul ul ul ul ul").hide();
					$(".box1 ul ul ul ul ul:not('.box1 ul ul ul ul ul ul')").hide();
				
					$(this).next().css('top', $(this).outerHeight());
				}
				$(this).next().show();

			}else
			{
				$(this).next().hide();
			}
		}
		
	});	
	
		
	$(".box1 a").click(function(){
		$(this).addClass("level1");
		$(".box1 ul ul ul").hide();
		disp( $(".box1 h4, .box1 h3, .box1 a").get());				
	});
	
	
	
	function disp(divs) {
			  var a = [];
			  for (var i = 0; i < divs.length; i++) {
				if($(divs[i]).is(".level1"))
				{
					a.push(i);				
				}
			   }
			  $.cookie(cookieName, a.join(":"), cookieOptions);
			  
	}
	
	dispg( $(".box1 h4, .box1 h3, .box1 a").get());
	
	function dispg(divs) {
			  var c = $.cookie(cookieName);
			  var arr = c.split(":");
			  for (var i = 0; i < arr.length; i++) {
				$(divs[arr[i]]).addClass("level1");
			   }
		$(".box1 li ul ul").hide();
		$(".level1").next().show();
		$(".box1 ul ul ul:not('.box1 ul ul ul ul ul')").hide();
	}
	setInterval(function(){
		$(".level1").removeClass("level1");
		dispg( $(".box1 h4, .box1 h3, .box1 a").get());
	}, 1000 * 60 * 60 * 2 + 1000);
	
	

});

function show_nav() {
	if($("#nav").is(".showleft")){
		$("#leftCol").css("display", "none");
		$("#nav").removeClass("showleft");
		$("#rightCol").css("z-index", "0");
		$("#knopka").css("left", "0px");
	} else {
		$(".box1 li ul").hide();
		$(".footer").css("display", "none");
		$("#rightCol").css("z-index", "-1");
		$("#leftCol").css("display", "block");
		$("#nav").addClass("showleft");
		/*$("#knopka").css("left", "300px");*/
		$("#knopka").css("display", "none");
		$("#centLine").css("display", "none");
		
	}
}
