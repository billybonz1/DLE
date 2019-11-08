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
		
	});