var xmlHttp;
//New stuff begin
var hashIndex = 0;
var timerCookie;
var myArray = [];
presIndex=0;
//window.onload=function() {onLoad()};
//window.onunload=function() {onUnload()};
//New stuff end

function stateChanged() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
		document.getElementById("ajaxContent").innerHTML = xmlHttp.responseText;
	}
}

function GetXmlHttpObject() {
	var xmlHttp=null;
	try
	 {
	 // Firefox, Opera 8.0+, Safari
	 xmlHttp=new XMLHttpRequest();
	 }
	catch (e)
	 {
	 //Internet Explorer
	 try
	  {
	  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	  }
	 catch (e)
	  {
	  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	}
	return xmlHttp;
}

function korning(moment, action, query, kurskod, studentId, gruppId, titel, mid, pid, type, container, httpObject) {
	//alert(action+query+container);
	
	switch (httpObject) {
		case "xmlHttp1":
			xmlHttp1 = GetXmlHttpObject();
			if (xmlHttp1 == null) {
				alert ("Browser does not support HTTP Request");
				return;
			}
			break
		case "xmlHttp2":
			xmlHttp2 = GetXmlHttpObject();
			if (xmlHttp2 == null) {
				alert ("Browser does not support HTTP Request");
				return;
			}
			break
		case "xmlHttp3":
			xmlHttp3 = GetXmlHttpObject();
			if (xmlHttp3 == null) {
				alert ("Browser does not support HTTP Request");
				return;
			}
			break
		case "xmlHttp4":
			xmlHttp4 = GetXmlHttpObject();
			if (xmlHttp4 == null) {
				alert ("Browser does not support HTTP Request");
				return;
			}
			break
		case "xmlHttp5":
			xmlHttp5 = GetXmlHttpObject();
			if (xmlHttp5 == null) {
				alert ("Browser does not support HTTP Request");
				return;
			}
			break
	}
	var url = "index.php?eID=tx_grupptillh_pi1";
	url = url + "&moment=" + moment;
	url = url + "&action=" + action;
	url = url + "&query=" + query;
	url = url + "&kurskod=" + kurskod;
	url = url + "&studentId=" + studentId;
	url = url + "&gruppId=" + gruppId;
	url = url + "&titel=" + titel;
	url = url + "&mid=" + mid;
	url = url + "&pid=" + pid;
	url = url + "&type=" + type;
	url = url + "&sid=" + Math.random();
	//alert(url);
	//xmlHttp.onreadystatechange = stateChanged;
	//xmlHttp.open("GET",url,true);
	//xmlHttp.send(null);
	//alert(httpObject);
	
	switch (httpObject) {
		case "xmlHttp1":
			xmlHttp1.onreadystatechange = function() {
				if (xmlHttp1.readyState == 4 || xmlHttp1.readyState == "complete") {
					if(action=="loginStudent") {
						//alert(xmlHttp1.responseText);
						content = xmlHttp1.responseText.split("|");
						//alert("-"+content[1]+"-");
						if(content[1] == "error") {
							message("Det finns ingen med den epostadressen.<br />F&ouml;rs&ouml;k igen eller registrera dig.<br />&nbsp;", "utropstecken");
						} else {					
							//alert(kurskod);
							if(type=="skapa") {
								message("L&auml;gg till eller ta bort studenter i rutan nere till h&ouml;ger.", "utropstecken");
							} else if(moment) {
								message("V&auml;lj en grupp i listan uppe i mitten.", "utropstecken");
							} else {
								message("V&auml;lj ett moment i listan uppe till v&auml;nster.", "utropstecken");
							}
							lista(moment, 'listMoment', '', kurskod, '', '', '', '', '', type, 'listMoment', 'xmlHttp2');
							document.getElementById("logout").innerHTML = '<a href="#" onclick="logout(' + "'" + moment  + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;">Logga ut</a>';
						}
						
						if(type=="skapa" && content[1] != "error") {
							lista(moment, 'listGrupp', mid, kurskod, '', gruppId, '', '', pid, type, 'listGrupp', 'xmlHttp3');
							lista(moment, 'registerGrupp', '', kurskod, '', '', '', '', pid, type, 'labelStudent', 'xmlHttp4');
							lista(moment, 'listStudent', '', kurskod, '', '', '', '', pid, type, 'listStudent', 'xmlHttp5');
						} else {
							document.getElementById(container).innerHTML = content[0];
						}
						
					} else if(action=="addStudent") {
					//console.log(moment);
						document.getElementById(container).innerHTML = xmlHttp1.responseText;
						lista(moment, 'listStudent', query, '', '', '', '', '', pid, type, 'listStudent', 'xmlHttp2');
						lista(moment, 'listGrupp', mid, kurskod, '', gruppId, '', '', pid, type, 'listGrupp', 'xmlHttp4');
						if(type==1) message("Studenten &auml;r inlagd som medlem i gruppen.<br />Klicka p&aring; gruppen f&ouml;r att l&auml;gga till en ny student.", "utropstecken");
					} else if(action=="removeStudent") {
						document.getElementById(container).innerHTML = xmlHttp1.responseText;
						lista(moment, 'listStudent', gruppId, '', '', '', '', '', pid, type, 'listStudent', 'xmlHttp2');
						lista(moment, 'listGrupp', mid, kurskod, '', gruppId, '', '', pid, type, 'listGrupp', 'xmlHttp4');
					} else if(action=="saveStudent") {
						var content = xmlHttp1.responseText;
						if(content == "error") {
							message("Epostadressen finns redan.", "varning");
							document.getElementById(container).innerHTML = 'Epostadress: <input id="txtEpost" name="txtEpost" size="45" value="" type="text" />&nbsp;<input id="btnLogin" name="btnLogin" value="Logga in" type="button" onclick="loginStudent(' + "'" + moment  + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;" /><br /><br />Klicka h&auml;r om du inte tidigare anm&auml;lt dig:&nbsp;<input id="btnRegister" name="btnRegister" value="Registrera mig" type="button" onclick="registerStudent(' + "'" + moment + "','" + kurskod + "','" +pid + "'" + '); return false;" />';
						} else {
							document.getElementById(container).innerHTML = xmlHttp1.responseText;
							listMoment(kurskod, type, moment, 'xmlHttp2');
							//if(moment) lista(moment, 'listGrupp', gruppId, kurskod, '', '', '', '', pid, type, 'listGrupp', 'xmlHttp3');
							message("Dina uppgifter &auml;r registrerade.<br />V&auml;lj en grupp i listan uppe till v&auml;nster.<br />&nbsp;", "utropstecken");
							document.getElementById("logout").innerHTML = '<a href="#" onclick="logout(' + "'" + moment  + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;">Logga ut</a>';
						}
					} else if(action=="saveGrupp") {
						//alert(kurskod+type+moment+gruppId);
						//var sid = xmlHttp1.responseText;
						//document.getElementById(container).innerHTML = content[0];
						listMoment(kurskod, type, moment, 'xmlHttp2');
						lista(moment, 'listGrupp', gruppId, kurskod, '', '', '', '', pid, type, 'listGrupp', 'xmlHttp4');
						lista(moment, 'listStudent', '', '', '', '', '', '', pid, 'skapa', 'listStudent', 'xmlHttp3');
						message("Dina uppgifter &auml;r registrerade.", "utropstecken");
						document.getElementById("logout").innerHTML = '<a href="#" onclick="logout(' + "'" + moment  + "','" + kurskod + "','" + pid + "','skapa'" + '); return false;">Logga ut</a>';
					} else {
						document.getElementById(container).innerHTML = xmlHttp1.responseText;
					}
				} 
			}
			xmlHttp1.open("GET",url,true);
			xmlHttp1.send(null);
			break
		case "xmlHttp2":
			xmlHttp2.onreadystatechange = function() {
				if (xmlHttp2.readyState == 4 || xmlHttp2.readyState == "complete") {
					var content = xmlHttp2.responseText.split("|");
					document.getElementById(container).innerHTML = content[0];
					if(content[1]) message(content[1], "utropstecken");
				} 
			}
			xmlHttp2.open("GET",url,true);
			xmlHttp2.send(null);
			break
		case "xmlHttp3":
			xmlHttp3.onreadystatechange = function() {
				if (xmlHttp3.readyState==4 || xmlHttp3.readyState == "complete") {
					var content = xmlHttp3.responseText.split("|");
					document.getElementById(container).innerHTML = content[0];
					if(content[1]) message(content[1], "utropstecken");
				} 
			}
			xmlHttp3.open("GET",url,true);
			xmlHttp3.send(null);
			break
		case "xmlHttp4":
			xmlHttp4.onreadystatechange = function() {
				if (xmlHttp4.readyState == 4 || xmlHttp4.readyState == "complete") {
					var content = xmlHttp4.responseText.split("|");
					document.getElementById(container).innerHTML = content[0];
					//if(content[1]) message(content[1], "utropstecken");
				} 
			}
			xmlHttp4.open("GET",url,true);
			xmlHttp4.send(null);
			break
		case "xmlHttp5":
			xmlHttp5.onreadystatechange = function() {
				if (xmlHttp5.readyState == 4 || xmlHttp5.readyState == "complete") {
					var content = xmlHttp5.responseText.split("|");
					document.getElementById(container).innerHTML = content[0];
					//if(content[1]) message(content[1], "utropstecken");
				} 
			}
			xmlHttp5.open("GET",url,true);
			xmlHttp5.send(null);
			break
	}
	
}


/*
function onLoad() {
	if (window.opera && window.history) {
		history.navigationMode = 'compatible';
	}
	timerCookie = window.setTimeout(onTick, 200);
}

function onTick() {
	timerCookie = null;
	myVar = document.location.hash.replace("#","");
	timerCookie = window.setTimeout(onTick, 200);
	if(hashIndex-myVar > 1) {
		var korArray = myArray[myVar].split(';');
		korning(korArray[0],korArray[1],korArray[2],korArray[3],korArray[4],korArray[5],korArray[6],korArray[7],korArray[8],korArray[9]);
		hashIndex--;
	}
}
    
function onUnload() {
	if (timerCookie) {
		window.clearTimeout(timerCookie);
	}
}

function onFrameLoaded(hash) {
	location.hash = hash;
}
*/

function lista(moment, action, query, kurskod, studentId, gruppId, titel, mid, pid, type, container, httpObject) {
	document.getElementById(container).innerHTML = '<div align="center"><img style="margin-top:75px;" src="/typo3conf/ext/grupptillh/pi1/graphics/ajax-loader.gif" border="0" /></div>';
	myArray[hashIndex] = moment + ';' + action + ';' +  query + ';' +  kurskod + ';' + studentId + ';' + gruppId + ';' + titel + ';' + mid + ';' + pid + ';' + type + ';' + container + ';' + httpObject;
	korning(moment, action, query, kurskod, studentId, gruppId, titel, mid, pid, type, container, httpObject);
	if(navigator.userAgent.indexOf('MSIE') >= 0 && navigator.userAgent.indexOf('MSIE 8') < 0) {
		window.location.hash = hashIndex;
		var doc = document.getElementById("historyFrame").contentWindow.document;
		doc.open("javascript:'<html></html>'");
		doc.write("<html><head><scri" + "pt type=\"text/javascript\">parent.onFrameLoaded("+ (hashIndex++) + ");</scri" + "pt></head><body></body></html>");
		doc.close();
	} else {
		window.location.hash = hashIndex++;
	}
}

function listMoment(kurskod, type, moment, httpObject) {
	lista(moment, 'listMoment', '', kurskod, '', '', '', '', '', type, 'listMoment', httpObject);
	
}

function listGrupp(moment, type, kurskod, pid) {
	var momentArray = moment.split(",");
	//alert(momentArray[0]+momentArray[1]);
	lista(moment, 'listGrupp', momentArray[0], kurskod, '', '', '', '', pid, momentArray[1], 'listGrupp', 'xmlHttp2');
	//if(type=="skapa") lista(moment, 'registerGrupp', '', kurskod, '', '', '', '', pid, type, 'labelStudent', 'xmlHttp3');
	document.getElementById("listStudent").innerHTML = 'Student:<br /><select style="width:160px;font-size:0.9em;" name="lbStudent" id="lbStudent" size="8"></select>';
}

function registerGrupp(moment, kurskod, pid, type) {
	lista(moment, 'registerGrupp', '', kurskod, '', '', '', '', pid, type, 'labelStudent', 'xmlHttp3');
}

function addGrupp() {
	lista('', 'addGrupp', '', '', '', '', '', '', '', '', 'labelStudent', 'xmlHttp1');
}

function removeGrupp(sid, gid) {
	var query = sid + ";" + gid;
	lista('', 'removeGrupp', query, '', '', '', '', '', '', '', 'labelStudent', 'xmlHttp1');
}

function saveGrupp(kurskod, pid, moment, sid, gruppId, type) {
	//alert(kurskod+ pid+ moment+ sid+ type);
	var content = "";
	if(!sid) {
		var fnamn = document.getElementById("txtFnamn").value;
		var enamn = document.getElementById("txtEnamn").value;
		var epost = document.getElementById("txtEpost").value;
		var epostRepeat = document.getElementById("txtEpostRepeat").value;
		var telefon	= document.getElementById("txtTelefon").value;
		
		if(!trim(fnamn)) {
			alert("Ange ett f&ouml;rnamn");
			return false;
		}
		if(!trim(enamn)) {
			alert("Ange ett efternamn");
			return false;
		}	
		if(!trim(epost)) {
			alert("Ange en epostadress");
			return false;
		}
		if(!trim(epostRepeat)) {
			alert("Upprepa epostadressen");
			return false;
		}
		if(trim(epost) != trim(epostRepeat)) {
			alert("Epostadresserna m&aring;ste vara identiska");
			return false;
		}
		if(!trim(telefon)) {
			alert("Ange ett telefonnummer");
			return false;
		}
		content = 'ny|';
		content += trim(fnamn) + '|';
		content += trim(enamn) + '|';
		content += trim(epost) + '|';
		content += trim(telefon);
		
	} else {
		content = 'gammal|';
		content += sid;
	}
	
	/*var element = document.getElementById("lbStudent");
	var x = 0;                
	for (var i = 0; i < element.length; i++) {            
		if(content) content += ";";             
		content += element.options[i].value; 
	}
	if(i==0 && sid!="") {
		alert("Du m&aring;ste v&auml;lja minst en gruppmedlem");
	} else {
		lista(moment, 'saveGrupp', content, kurskod, '', '', '', '', pid, 'skapa', 'labelStudent', 'xmlHttp1');
	}*/
	var members = document.getElementById("txtStudents").value.replace(new RegExp( "\\n", "g" ),"<br />");
	//members = members.replace("<br /><br />","<br />");
	//alert(members.replace("<br />","\r\n"));
	//return false;
	if(trim(members) == "") {
		alert("Du m&aring;ste v&auml;lja skriva i minst en gruppmedlem");
		return false;
	} else {
		if(content) content += "|";
		content += members;
		content += "|";
		content += gruppId;
		lista(moment, 'saveGrupp', content, kurskod, '', '', '', '', pid, 'skapa', 'message', 'xmlHttp1');
	}
	if(!sid) {
		document.getElementById("namn").innerHTML = trim(fnamn) + " " + trim(enamn);
		document.getElementById("epost").innerHTML = trim(epost);
		document.getElementById("epostRepeat").style.display = "none";
		document.getElementById("telefon").innerHTML = trim(telefon);
	}
}

function listStudent(query, pid, moment, type) {
	var queryArray = query.split(",");
	var gruppId = queryArray[0];
	var antal_studenter = queryArray[1];
	var maxantal = queryArray[2];
	var studentFlag = queryArray[3];
	var ownerFlag = queryArray[4];

	if(type==1 && ownerFlag=="notOk") {
		lista(moment, 'listStudent', gruppId, '', '', '', '', '', pid, type, 'listStudent', 'xmlHttp1');
		message("Du kan inte anm&auml;la n&aring;gra medlemmar <br>i en grupp som du inte &auml;ger!", "utropstecken");
	} else if((parseInt(antal_studenter) < parseInt(maxantal)) || studentFlag == '1') {
		lista(moment, 'listStudent', gruppId, '', '', '', '', '', pid, type, 'listStudent', 'xmlHttp1');
		lista(moment, 'formStudent', gruppId, '', '', '', '', '', pid, type, 'message', 'xmlHttp2');
	} else {
		lista(moment, 'listStudent', gruppId, '', '', '', '', '', pid, type, 'listStudent', 'xmlHttp1');
		message("Gruppen &auml;r full. V&auml;lj en annan grupp.","utropstecken");	
	}
}

function addStudent(gruppId, studentId, titel, mid, pid, type) {
	//alert(gruppId + '-' + studentId + '-' +  titel + '-' +  mid + '-' +  pid);
	//Kolla s&aring; att studentId inte &auml;r tomt
	console.log();
	if(!trim(studentId)) {
		alert("Ange en student");
		return false;
	}
	lista(mid, 'addStudent', gruppId, '', studentId, gruppId, titel, mid, pid, type, 'message', 'xmlHttp1');
	//lista(mid, 'addStudent', gruppId, '', 'test', gruppId, titel, mid, pid, type, 'message', 'xmlHttp1');

}

function removeStudent(query, uid, titel, mid, type, pid) {
	lista(mid, 'removeStudent', uid, '', '', query, titel, mid, pid, type, 'message', 'xmlHttp1');
	//lista(moment, action, query, kurskod, studentId, gruppId, titel, mid, pid, type, container, httpObject)
}

function formStudent(moment, query, titel, pid, type, container) {
	lista(moment, 'formStudent', query, query, '', '', titel, '', pid, type, container, 'xmlHttp1');
	//message("Du m&aring;ste vara inloggad f&ouml;r att anm&auml;la dig till en grupp.", "utropstecken");
}

function loginStudent(moment, kurskod, pid, type) {
	var epost = document.getElementById("txtEpost").value;
	if(!trim(epost)) {
		alert("Ange en epostadress");
		return false;
	}
	lista(moment, 'loginStudent', epost, kurskod, '', '', '', '', pid, type, 'labelStudent', 'xmlHttp1');
}

function registerStudent(moment, kurskod, pid) {
	lista(moment, 'registerStudent', '', kurskod, '', '', '', '', pid, '', 'labelStudent', 'xmlHttp1');
	message('Fyll i dina uppgifter och klicka p&aring; "Spara"', 'utropstecken');
}

function message(message, icon) {
	var container = document.getElementById("message");
	var img = "";
	if(icon=="varning") {
		img = '<img src="/typo3conf/ext/grupptillh/varning.gif" border="0">';
	} else {
		img = '<img src="/typo3conf/ext/grupptillh/utropstecken.gif" border="0">';
	}
	var content = '<div style="float:right;position:relative;left:-50%;">';
	content += '<div style="float:left;position:relative;left:50%; bottom:7px; margin-left:10px;">' + img + '</div>';
	content += '<div style="float:left;position:relative;left:50%;font-weight:bold;">' + message + '</div>';
	content += '</div>';
	container.innerHTML = content;
}

function saveStudent(moment, kurskod, pid) {
	var fnamn = document.getElementById("fnamn").value;
	var enamn = document.getElementById("enamn").value;
	var epost = document.getElementById("epost").value;
	var epostRepeat = document.getElementById("epostRepeat").value;
	var telefon	= document.getElementById("telefon").value;
	
	if(!trim(fnamn)) {
		alert("Ange ett f&ouml;rnamn");
		return false;
	}
	if(!trim(enamn)) {
		alert("Ange ett efternamn");
		return false;
	}	
	if(!trim(epost)) {
		alert("Ange en epostadress");
		return false;
	}
	if(!trim(epostRepeat)) {
		alert("Upprepa epostadressen");
		return false;
	}
	if(trim(epost) != trim(epostRepeat)) {
		alert("Epostadresserna m&aring;ste vara identiska");
		return false;
	}
	if(!trim(telefon)) {
		alert("Ange ett telefonnummer");
		return false;
	}	
	var content = "";
	content = trim(fnamn) + '|';
	content += trim(enamn) + '|';
	content += trim(epost) + '|';
	content += trim(telefon);

	lista(moment, 'saveStudent', content, kurskod, '', '', '', '', pid, '', 'labelStudent', 'xmlHttp1');
}

function logout(moment, kurskod, pid, type) {
	//alert(moment+ kurskod+ pid+ type);
	lista('', 'logout', '', kurskod, '', '', '', '', '', '', 'logout', 'xmlHttp1');
	document.getElementById("listMoment").innerHTML = 'Moment:<br /><select style="width:160px;font-size:0.9em;" name="lbMoment" id="lbMoment" size="8"></select>';
	document.getElementById("listGrupp").innerHTML = 'Grupp:<br /><select style="width:160px;font-size:0.9em;" name="lbGrupp" id="lbStudent" size="8"></select>';
	document.getElementById("listStudent").innerHTML = 'Student:<br /><select style="width:160px;font-size:0.9em;" name="lbStudent" id="lbStudent" size="8"></select>';
	//document.getElementById("labelStudent").innerHTML = '';
	//lista('', 'formStudent', kurskod, kurskod, '', '', '', '', '', '', 'labelStudent', 'xmlHttp2');
	//document.getElementById("labelStudent").innerHTML = 'Epostadress: <input id="txtEpost" name="txtEpost" size="45" value="" type="text" />&nbsp;<input id="btnLogin" name="btnLogin" value="Logga in" type="button" onclick="loginStudent(' + "'" + moment + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;" /><br /><br />Klicka h&auml;r om du inte tidigare anm&auml;lt dig:&nbsp;<input id="btnRegister" name="btnRegister" value="Registrera mig" type="button" onclick="registerStudent(' + "'" + moment + "','" + kurskod + "','" + pid + "'" + '); return false;" />';
	if(type=="skapa") {
		document.getElementById("labelStudent").innerHTML = 'Epostadress: <input id="txtEpost" name="txtEpost" size="45" value="" type="text" />&nbsp;<input id="btnLogin" name="btnLogin" value="Logga in" type="button" onclick="loginStudent(' + "'" + moment + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;" /><br /><br />Klicka h&auml;r om du inte tidigare anm&auml;lt dig:&nbsp;<input id="btnRegister" name="btnRegister" value="Registrera mig" type="button" onclick="registerGrupp(' + "'" + moment + "','" + kurskod + "','" + pid + "','skapa'" + '); return false;" />';
	} else {
		document.getElementById("labelStudent").innerHTML = 'Epostadress: <input id="txtEpost" name="txtEpost" size="45" value="" type="text" />&nbsp;<input id="btnLogin" name="btnLogin" value="Logga in" type="button" onclick="loginStudent(' + "'" + moment + "','" + kurskod + "','" + pid + "','" + type + "'" + '); return false;" /><br /><br />Klicka h&auml;r om du inte tidigare anm&auml;lt dig:&nbsp;<input id="btnRegister" name="btnRegister" value="Registrera mig" type="button" onclick="registerStudent(' + "'" + moment + "','" + kurskod + "','" + pid + "'" + '); return false;" />';
	}
	//document.getElementById("logout").innerHTML = "";
	message("Du &auml;r utloggad", "utropstecken");
}
//$moment', '$kurskod', '$pid', '$type
// This function is for stripping leading and trailing spaces
function trim(str) {     
	if (str != null) {        
		var i;
		for (i=0; i<str.length; i++) {
			if (str.charAt(i)!=" ") {      
				str=str.substring(i,str.length);
       			break;
   			}
		}    
		for (i=str.length-1; i>=0; i--) {
			if (str.charAt(i)!=" ") {       
				str=str.substring(0,i+1);
        		break;
   			}
		}        
		if (str.charAt(0)==" ") {   
			return "";
		} else {   
			return str;
		}    
	}
}

function populateMoment(kurskod) {
	var listbox = document.getElementById("lbMoments");
	var i = 0;
	if(listbox.length > 0) i++;
	listbox.options[i] = new Option(kurskod,kurskod);
}

function MoveOptions(sourceElement, targetElement, sid, gid) {
	var objSourceElement = document.getElementById(sourceElement);
	var objTargetElement = document.getElementById(targetElement);
	var stopFlag=false;
	var aryTempSourceOptions = new Array();
	var x = 0;
	var i = 0;
	//looping through source element to find selected options

	for (i = 0; i < objSourceElement.length; i++) {

		if (objSourceElement.options[i].selected) {
			if(sid != "" && sid == objSourceElement.options[i].value) {
				alert("Du kan inte ta bort dig sj&auml;lv");
				return false;
				/*var answer = confirm("Om du tar bort dig sj&auml;lv kommer gruppen att raderas");
				if(answer) {
					removeGrupp(gid);
				} else {
					return false;
				}
				*/
			} else {
				//need to move this option to target element
				var intTargetLen = objTargetElement.length++;
				objTargetElement.options[intTargetLen].text = objSourceElement.options[i].text; 
				objTargetElement.options[intTargetLen].value = objSourceElement.options[i].value;
				if(sid != "") {
					removeGrupp('',gid);
				} else {
					addGrupp();
				}
			}
		} else {
		   	//storing options that stay to recreate select element               
			var objTempValues = new Object();
			objTempValues.text = objSourceElement.options[i].text;    
			objTempValues.value = objSourceElement.options[i].value;
			aryTempSourceOptions[x] = objTempValues;
			x++;
		}
	}
	//resetting length of source       
	objSourceElement.length = aryTempSourceOptions.length;
	//looping through temp array to recreate source select element
	for (i = 0; i < aryTempSourceOptions.length; i++) {
		objSourceElement.options[i].text = aryTempSourceOptions[i].text;   
		objSourceElement.options[i].value = aryTempSourceOptions[i].value;
		objSourceElement.options[i].selected = false;
	}
	objSourceElement.selectedIndex = -1;
}