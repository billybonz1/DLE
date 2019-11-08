

 function intobusket(id) {
	 
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
		
		
			curPodId = document.getElementById("idd" + id).value;
			xmlHttp.open("GET", "https://laki-kraski.com.ua/engine/modules/ajax/intobusket.php?id=" + curPodId, true);
			xmlHttp.onreadystatechange = goToBusket; 
			xmlHttp.send(null);
		
	} else {
		setTimeout('intobusket()', 1000);	
	}
	 
 }
	function goToBusket() {
		if(xmlHttp.readyState == 4) {
			if(xmlHttp.status == 200) {
				xmlRe = xmlHttp.responseXML;
				xmlDe = xmlRe.documentElement;
				xmlIt = xmlDe.firstChild.data;

				if(xmlIt == 1){
					alert("Товар добавлен в корзину.");
					bask();
				} 
				
				if(xmlIt == 0) {
					alert("Данный товар уже имеется в корзине.");
				}
				
				
					
			} else {
				alert ("Pri obrawenuu k servery vozniklu problembI: " + xmlHttp.statusText);	
			}
		}
	}
	
	


