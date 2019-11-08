$(function(){
var m=0;
	setInterval(function(){
		wid = $("body").width();
		/*widl = $("#showlogin").width();
		if ( widl > 700) 
		{
			$(".unit-rating").hide();
		} */
			var rightHeight = $("#rightCol").height();
			var leftHeight = $("#leftCol").height();
			leftHeight = leftHeight+5;
			rightHeight = rightHeight+5;
			if(leftHeight < rightHeight) {
				$("#centLine").css("height", rightHeight + "px");
			} 
		
		if(wid != m) {
		if(wid < 1230 && !$("#nav").is(".showleft")) {
			$(".rat").css("display", "none");
			$(".costs").css("margin-top", "25px");
			var rightHeight = $("#rightCol").height();
			var leftHeight = $("#leftCol").height();
			leftHeight = leftHeight+5;
			rightHeight = rightHeight+5;


		
			bodyWidth = $("body").width();
		
			
			rightColWidth = bodyWidth;
			
			topWidth = bodyWidth - 16;
			$(".header").css("width", "1000px");
			if(bodyWidth >= 1000) {
				$(".footer").css("width", bodyWidth + "px");
				$(".topNavigatin").css("width", topWidth + "px");
			} else {
				$(".footer").css("width", "1000px");
				$(".topNavigatin").css("width", "1000px");
			}

			
				
			$("#rightCol").css("position", "absolute");
			$("#rightCol").css("left", "0px");
			$("#leftCol").css("display", "none");
			$("#knopka").css("display", "block");

			
			
			

			
			ssd = rightColWidth-30;
			
			$("#iid").css("width", ssd + "px");
			$("#iiid").css("width", ssd + "px");
			
			if(ssd <= 850) {
				$("#poltitle").css("width", "850px");
				$("#full").css("width", "850px");
			} else {
				$("#poltitle").css("width", ssd + "px");
				$("#full").css("width", ssd + "px");
			}
			
			
			
			
		} else {
			$(".rat").css("display", "block");
			$(".costs").css("margin-top", "0px");
			
			var rightHeight = $("#rightCol").height();
			var leftHeight = $("#leftCol").height();
			leftHeight = leftHeight+5;
			rightHeight = rightHeight+5;
			if(leftHeight > rightHeight) {
				$("#centLine").css("height", leftHeight + "px");
				$("#rightCol").css("height", leftHeight + "px");
	
			} 
			if (leftHeight < rightHeight) {
				$("#centLine").css("height", rightHeight + "px");
			}
		
			bodyWidth = $("body").width();
		
			
			rightColWidth = bodyWidth - 368;
			
			topWidth = bodyWidth - 16;
			$(".header").css("width", "1000px");
			if(bodyWidth >= 1000) {
				$(".footer").css("width", bodyWidth + "px");
				$(".topNavigatin").css("width", topWidth + "px");
			} else {
				$(".footer").css("width", "1000px");
				$(".topNavigatin").css("width", "1000px");
			}
			if(rightColWidth >= 632) {
				$("#rightCol").css("width", rightColWidth + "px");
				$("#rightCol").css("position", "absolute");
				$("#rightCol").css("left", "368px");
			} else {
				$("#rightCol").css("width", "632px");
				$("#rightCol").css("position", "absolute");
				$("#rightCol").css("left", "368px");
			}
			ssd = rightColWidth-30;
			
			$("#iid").css("width", ssd + "px");
			$("#iiid").css("width", ssd + "px");
			
			if(ssd <= 850) {
				$("#poltitle").css("width", "850px");
				$("#full").css("width", "850px");
			} else {
				$("#poltitle").css("width", ssd + "px");
				$("#full").css("width", ssd + "px");
			}
		$("#leftCol").css("display", "block");
		$("#knopka").css("display", "none");
		}
	/*	if(wid < 1100) {
			$(".hiden").css("display", "none");
			$(".costs").css("float", "left");
			$(".fl").css("float", "left");
			$(".costs").css("margin-left", "-30px");
			$(".showlogin").css("margin-right", "0px");
			$(".showing").css("display", "block");
			$(".showing").css("float", "left");
			$(".showing").css("margin-left", "30px");
			$(".podd").css("display", "block");
			$(".option").css("font-size", "11px");
			$(".select").css("font-size", "11px");
			$(".select").css("width", "490px");
		} else {*/
			$(".hiden").css("display", "block");
			$(".showlogin").css("margin-right", "100px");
			$(".costs").css("float", "right");
			$(".fl").css("float", "right");
			$(".showing").css("display", "none");
			$(".podd").css("display", "none");
			$(".option").css("font-size", "14px");
			$(".select").css("font-size", "14px");
			$(".select").css("width", "100%");
		//}
		}
		m = wid;
	}, 500);
	
});