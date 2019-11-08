/*$(function(){

	setInterval(function(){
			$("#leftCol").css("display", "block");
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
		
	}, 500);
	
});*/