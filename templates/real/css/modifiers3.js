$(function(){
	function changesize() {
		var rightHeight = $("#rightCol").height();
		var leftHeight = $("#leftCol").height();
		leftHeight = leftHeight+50;
		rightHeight = rightHeight+50;
		if(leftHeight > rightHeight) {
			$("#rightCol").css("height", leftHeight + "px");
			$("#centLine").css("height", leftHeight + "px");
		} else {
			$("#centLine").css("height", rightHeight + "px");
		}
	}
	changesize();
	$(window).resize(function(){
		changesize();
	});
	$(".title").click(function(){changesize();});
	$(".dtree a").click(function(){changesize();});
});