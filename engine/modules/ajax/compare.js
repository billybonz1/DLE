function compare(cid){
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
		xmlHttp.open("GET", "https://laki-kraski.com.ua/engine/modules/ajax/compare.php?cid=" + cid, true);
		xmlHttp.onreadystatechange = coToCompare; 
		xmlHttp.send(null);
	} else {
		setTimeout('compare()', 1000); //1000	
	}
}

function coToCompare(){
	if(xmlHttp.readyState == 4) {
		if(xmlHttp.status == 200) {
			xmlRespp = xmlHttp.responseXML;
			xmlDoc = xmlRespp.documentElement;
			xmlAc = xmlDoc.getElementsByTagName("comparea").item(0).firstChild.data;
			if(xmlAc == 0) {alert("Данная продукция уже есть в таблице сравнения.");}
			if(xmlAc == 1) {alert("Данная продукция добавлена в таблицу сравнения."); bask();}
			
			
		}
	}
}