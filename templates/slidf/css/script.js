$(document).ready(function(){
						   
	$("#sli li").find("ul").hide();
	
	$("#nmenu #fli").hover(
		function(){
			$(this).find(".bgMenu").show();
			$(this).css("border", "1px #ababab solid");
			$(this).css("border-right", "1px #f3f3f3 solid");
			$(this).css("background-color", "#f3f3f3");
			numlis = $(this).find("#sli").size();
			wid = numlis*230;
			
			hid = 0;
			$(this).find("#sli").each(function(){

				currhid = $(this).height();
				if(currhid > hid) {
					hid = currhid+10;
				}
			});

			$(this).find(".dots").css("height", hid+"px");
			$(this).find(".dots:last").css("height", "0px");
			$(this).find("#sli:last").css("border", "0px");
			$(this).find("ul:first").css("width", wid+"px");
			
		},
		function(){
			$(this).find(".bgMenu").hide();
			$(this).css("border", "1px #e9f0f6 solid");
			$(this).css("background-color", "#e9f0f6");
		}
	);
	
	$("ul #lis").click(function(){
		$(this).find("ul:first").toggle();
		
		hid = 0;
		$("#nmenu #fli").find("#sli").each(function(){

			currhid = $(this).height();
			if(currhid > hid) {
				hid = currhid+10;
			}
		});

		$("#nmenu #fli").find(".dots").css("height", hid+"px");
		$("#nmenu #fli").find(".dots:last").css("height", "0px");

	});
			
});


//$(document).ready(function(){
//	$("#nmenu #fli").hover(
//		function(){
//			$(this).find(".bgMenu").show();
//			$(this).css("border", "1px #ababab solid");
//			$(this).css("border-right", "1px #f3f3f3 solid");
//			$(this).css("background-color", "#f3f3f3");
//			
//			numlis = $(this).find("#sli").size();
//			wid = numlis*211;
//			
//			$(this).find("ul:first").css("width", wid+"px");
//			
//		},
//		function(){
//			$(this).find(".bgMenu").hide();
//			$(this).css("border", "1px #e9f0f6 solid");
//			$(this).css("background-color", "#e9f0f6");
//		}
//	);				   
//});