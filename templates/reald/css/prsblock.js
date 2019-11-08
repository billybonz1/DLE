s=1;

function cng(v) {
	
	if(s==1) {
		document.getElementById(v).style.display = "block"; 
		document.getElementById("cvet" + v).style.display = "block";
		document.getElementById("blesk" + v).style.display = "block";
		document.getElementById("priceyp" + v).style.display = "block";
		document.getElementById("pricelitr" + v).style.display = "block";
		document.getElementById("upack" + v).style.display = "block";
		s=2;
	} else if(s==2) {
		document.getElementById(v).style.display = "none";
		document.getElementById("cvet" + v).style.display = "none"; 
		document.getElementById("blesk" + v).style.display = "none";
		document.getElementById("priceyp" + v).style.display = "none";
		document.getElementById("pricelitr" + v).style.display = "none";
		document.getElementById("upack" + v).style.display = "none";
		s=1;
	}
	
}

function fade(f1,f2) {
	tagArray11 = document.getElementsByClassName("tag11" + f2);
	tagArray22 = document.getElementsByClassName("tag22" + f2);
	tagArray33 = document.getElementsByClassName("tag33" + f2);
	tagArray44 = document.getElementsByClassName("tag44" + f2);
	tagArray55 = document.getElementsByClassName("tag55" + f2);
	tagArray66 = document.getElementsByClassName("tag66" + f2);
	

		tagArray11[f1].style.color = "#015283";
		tagArray22[f1].style.color = "#015283";
		tagArray33[f1].style.color = "#015283";
		tagArray44[f1].style.color = "#015283";
		tagArray55[f1].style.color = "#015283";
		tagArray66[f1].style.color = "#015283";
		
	
	
}

function fadeout(f1,f2) {
	tagArray11 = document.getElementsByClassName("tag11" + f2);
	tagArray22 = document.getElementsByClassName("tag22" + f2);
	tagArray33 = document.getElementsByClassName("tag33" + f2);
	tagArray44 = document.getElementsByClassName("tag44" + f2);
	tagArray55 = document.getElementsByClassName("tag55" + f2);
	tagArray66 = document.getElementsByClassName("tag66" + f2);
	
	
		tagArray11[f1].style.color = "#007cc7";
		tagArray22[f1].style.color = "#007cc7";
		tagArray33[f1].style.color = "#007cc7";
		tagArray44[f1].style.color = "#007cc7";
		tagArray55[f1].style.color = "#007cc7";
		tagArray66[f1].style.color = "#007cc7";
	
}