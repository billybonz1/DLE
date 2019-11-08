$(document).ready(function(){
		asd = $("span");
		
		for(var i=0; i<asd.length; i++) {
			curr = asd[i].getAttribute("style");
			if(curr == "font-size: xx-small;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "9px"
					  }
	
				);
			}
			
			if(curr == "font-size: x-small;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "10px"
					  }
	
				);
			}
			
			if(curr == "font-size: small;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "12px"
					  }
	
				);
			}
			
			if(curr == "font-size: medium;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "16px"
					  }
	
				);
			}
			
			if(curr == "font-size: large;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "18px"
					  }
	
				);
			}
			
			if(curr == "font-size: x-large;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "24px"
					  }
	
				);
			}
			
			if(curr == "font-size: xx-large;") {
				$("span:eq(" + i +")").css(
					{
						fontSize: "32px"
					  }
	
				);
			}
		}
		
		
		
		$(".nnd img").each(function(){
			imgs = $(this).attr("src");
			if(imgs[0]+imgs[1]+imgs[2]+imgs[3]+imgs[4]+imgs[5]+imgs[6] != "http://") {
			$(this).attr("src", "https://laki-kraski.com.ua/"+imgs);
			}
		});
		
		/*$(".topArrow").click(function(){
			$(this).css("background-image", "url(https://laki-kraski.com.ua/templates/real/images/click_top.png)");
		});*/
	});