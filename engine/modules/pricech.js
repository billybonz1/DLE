
 var xmlHttp = createXmlHttpRequestObject();
 function createXmlHttpRequestObject() {
	var xmlHttp;
	if(window.ActiveXObject) {
		try {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");	
		}
		catch (e) 
		{
			xmlHttp = false;	
		}
	} else {
		try {
			xmlHttp = new XMLHttpRequest();	
		}
		catch (e)
		{
			xmlHttp = false;	
		}
	}
	if(xmlHttp) {
		return xmlHttp;	
	}
 }

 function process(g,p,b) {
 
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
		xmlHttp.open("GET", "https://laki-kraski.com.ua/engine/modules/pricech.php?b=" + b + "&g=" + g, true);
		xmlHttp.onreadystatechange = handleServerResponse; 
		xmlHttp.send(null);
	} else {
		setTimeout('process()', 1000);	
	}
 }
	function handleServerResponse() {
	
		if(xmlHttp.readyState == 4) {
		
			if(xmlHttp.status == 200) {
				
				xmlResponse = xmlHttp.responseXML;
				xmlDocumentElement = xmlResponse.documentElement;
				id = xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId = "num" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId2 = "num2" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId3 = "num3" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId4 = "num4" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId5 = "num5" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				xmlId6 = "num6" + xmlDocumentElement.getElementsByTagName("g").item(0).firstChild.data;
				
				xmlB = xmlDocumentElement.getElementsByTagName("b").item(0).firstChild.data;
				xmlKod = xmlDocumentElement.getElementsByTagName("kod");
				xmlPackage = xmlDocumentElement.getElementsByTagName("pack");
				xmlCvet = xmlDocumentElement.getElementsByTagName("cvet");
				xmlBlesk = xmlDocumentElement.getElementsByTagName("blesk");
				xmlPriceyp = xmlDocumentElement.getElementsByTagName("priceyp");
				xmlPricelitr = xmlDocumentElement.getElementsByTagName("pricelitr");
					var packageArrows = "<img src='https://laki-kraski.com.ua/templates/real/images/razv.png' style='margin-left:5px;'>";
					
				document.getElementById(xmlId).innerHTML = "<p>" + xmlKod.item(0).firstChild.data + packageArrows + "</p>";
				document.getElementById(xmlId2).innerHTML = "<p>" + xmlPackage.item(0).firstChild.data + "</p>";
				document.getElementById(xmlId3).innerHTML = "<p>" + xmlCvet.item(0).firstChild.data + "</p>";
				document.getElementById(xmlId4).innerHTML = "<p>" + xmlBlesk.item(0).firstChild.data +"</p>";
				document.getElementById(xmlId5).innerHTML = "<p>" + xmlPriceyp.item(0).firstChild.data + " грн</p>";
				document.getElementById(xmlId6).innerHTML = "<p>" + xmlPricelitr.item(0).firstChild.data + " грн</p>";

				document.getElementById("idd" + id).value= xmlB;
				cng(id);
				
				
			}
		}
	}
	
	




