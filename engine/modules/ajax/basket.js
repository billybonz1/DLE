window.onload = bask;
function bask() {

	if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
		
		
	
			xmlHttp.open("GET", "https://laki-kraski.com.ua/engine/modules/ajax/basket.php", true);
			xmlHttp.onreadystatechange = doBasket; 
			xmlHttp.send(null);
		
	} else {
		setTimeout('bask()', 1000);	
	}
	 
 }
	function doBasket() {
		if(xmlHttp.readyState == 4) {
			if(xmlHttp.status == 200) {
				
			xmlRes = xmlHttp.responseXML;
			xmlDel = xmlRes.documentElement;
			xmlKol = xmlDel.getElementsByTagName("kols").item(0).firstChild.data;
			xmlPrs = xmlDel.getElementsByTagName("prss").item(0).firstChild.data;
			xmlComp = xmlDel.getElementsByTagName("compa").item(0).firstChild.data;

			document.getElementById("cols").innerHTML = xmlKol;
			document.getElementById("prs").innerHTML = xmlPrs;
			document.getElementById("comp").innerHTML = xmlComp;
			
			
			}
		}
	}
	
	

