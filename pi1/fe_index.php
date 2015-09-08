<?php

// Exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');

// Initialize FE user object:
$feUserObj = tslib_eidtools::initFeUser();
$usergroup = $feUserObj->user['usergroup'];

// Connect to database:mysql_real_escape_string
tslib_eidtools::connectDB();

$i=0;
$action = t3lib_div::_GP("action");
$query = t3lib_div::_GP("query");
$moment = t3lib_div::_GP("moment");
$kurskod = t3lib_div::_GP("kurskod");
$studentId = t3lib_div::_GP("studentId");
$titel = t3lib_div::_GP("titel");
$gruppId = t3lib_div::_GP("gruppId");
$pid = t3lib_div::_GP("pid");
$type = t3lib_div::_GP("type");
$sid = t3lib_div::_GP("sid");

$imageSokvag = "/fileadmin/user_portraits/";

//echo "scope=$scope, action=$action, query=$query, lang=$lang, sorting=$sorting, sortorder=$sortorder";

switch($action) {
	case "listMoment":
		echo listMoment($kurskod, $moment);
		break;	
	case "listGrupp":
		echo listGrupp($query, $gruppId, $moment, $type);
		break;
	case "registerGrupp":
		echo registerGrupp($kurskod, $pid, $moment, $type);
		break;
	case "saveGrupp":
		echo saveGrupp($query, $kurskod, $pid, $moment, $type);
		break;
	case "addGrupp":
		echo addGrupp();
		break;		
	case "removeGrupp":
		echo removeGrupp($query);
		break;		
	case "listStudent":
		echo listStudent($query, $moment, $type, $pid);
		break;
	case "formStudent":
		echo formStudent($moment, $query, $titel, $pid, $type);
		break;
	case "getStudent":
		echo getStudent($query);
		break;		
	case "registerStudent":
		echo registerStudent($moment, $kurskod, $pid);
		break;	
	case "saveStudent":
		echo saveStudent($moment, $query, $kurskod, $pid);
		break;
	case "addStudent":
		echo addStudent($query, $studentId, $titel, $pid, $type);
		break;
	case "removeStudent":
		echo removeStudent($query, $gruppId, $titel, $type);
		break;
	case "loginStudent":
		echo loginStudent($moment, $query, $kurskod, $type, $pid);
		break;
	case "logout":
		session_start();
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"]);
		}
		session_destroy();
		break;
}

function listMoment($kurskod, $moment) {
	//die("$kurskod, $moment");
	$i = 0;
	if($moment) {
		$momentUrval = "AND M.uid IN(" . addslashes($moment) . ")";
		//$momentSelect = " selected = \"selected\"";
	}
	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("M.uid, M.pid, M.title, M.grouptype", "tx_grupptillh_kurs K INNER JOIN tx_grupptillh_moment M ON M.kid = K.uid", "K.kurskod = '" . addslashes($kurskod) . "' $momentUrval AND M.deleted = 0", "", "M.sorting", "") or die("84; ".mysql_error());

	while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
		$uid = $row["uid"];
		$pid = $row["pid"];
		$title = $row["title"];
		$grouptype = $row["grouptype"];
		$listContent .= "<option value=\"$uid,$grouptype\" title=\"$title\">$title</option>";
		$i++;
	}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	
	$content = "Moment:<br /><select style=\"width:150px; font-size:0.9em;\" name=\"lbMoment\" id=\"lbMoment\" size=\"8\"";
	if($type!="skapa") $content .= " onchange=\"listGrupp(this.options[this.options.selectedIndex].value, '$grouptype', '$kurskod', '$pid'); return false;\"";
	$content .= ">$listContent</select>";
	
	return $content;
}

function listGrupp($query, $gruppId, $moment, $type) {

	$strMessage = "Det finns inga grupper knutna till momentet du valt.";
	//die("$query, $gruppId, $moment, $type");
	$i = 0;
	$studentFlag = 0;
	session_start(); 
	$sid = $_SESSION["tx_grupptillh_pi1"];
	
	/*if($type=="skapa") {
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid, pid, title, owner, members", "tx_grupptillh_grupp", "mid = " . intval($moment) . " AND deleted = 0", "", "sorting") or die("111; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
			$pid = $row["pid"];
			$title = $row["title"];
			$owner = $row["owner"];
			$members = $row["members"];
			$listcontent .= "<option value=\"$uid\"";
			if($owner==$sid) $listcontent .= " selected=\"selected\"";
			$listcontent .= ">$title</option>";
			$strMessage = "Välj en grupp i listan uppe i mitten.";
			$i++;
		}		
	} else {*/
		$gruppArray = array();
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("G.uid, G.pid, G.title, G.maxantal, G.members, G.owner, GS.sid", "tx_grupptillh_grupp G LEFT JOIN tx_grupptillh_grupp_student GS ON GS.gid = G.uid", "G.mid = " . intval($query) . " AND G.deleted = 0", "", "G.sorting", "") or die("125; ".mysql_error());
		$ownerOfGroup = "";
		$ownerOfSomeGroup = false;
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$antal_studenter = 0;
			$owner="";
			$antal_studenter = 0;
			
			$uid = $row["uid"];
			if($old_uid != $uid) $i=0;
			$pid = $row["pid"];
			$studentId = $row["sid"];
			if($studentId) $i++;
			$title = $row["title"];
			$maxantal = $row["maxantal"];
			$members = $row["members"];
			if($members) {
				$antal_studenter = count(explode(",", $members));
				if(substr($members, 0, 1) == ",") $antal_studenter--;
			//} elseif($type) {
				//$antal_student = 0;
			} else {
				$antal_studenter = $i;
			}
			$owner = $row["owner"];
			$gruppArray[$uid]["pid"] = $pid;
			$gruppArray[$uid]["title"] = $title;
			$gruppArray[$uid]["maxantal"] = $maxantal;
			$gruppArray[$uid]["antal_studenter"] = $antal_studenter;
			if($owner == $sid) {
				$gruppArray[$uid]["ownerOfGroupId"] = $uid;
				$ownerOfSomeGroup = true;
			} elseif($owner) {
				$gruppArray[$uid]["ownerOfGroupId"] = "someOneElse";
			}
			if($sid == $studentId) $studentFlag = 1;
			$strMessage = "Välj en grupp i listan uppe i mitten.";
			$old_uid = $uid;
		}
/*print "<pre>";
print_r($gruppArray);
print "</pre>";
die();*/
		foreach($gruppArray as $key => $value) {
				
			$uid = $key;
			$title = $value["title"];
			$antal_studenter = $value["antal_studenter"];
			$maxantal = $value["maxantal"];
			if($value["ownerOfGroupId"] == "someOneElse") {
				$ownerOfGroup = "notOk";
			} elseif($value["ownerOfGroupId"] == $uid or $ownerOfSomeGroup == false) {
				$ownerOfGroup = "ok";
			} else {
				$ownerOfGroup = "notOk";
			}
	
			$listcontent .= "<option value=\"$uid,$antal_studenter,$maxantal,$studentFlag,$ownerOfGroup\"";
			//if($gruppId == $uid) $listcontent .= " selected=\"selected\"";
			$listcontent .= " title=\"$title: $antal_studenter($maxantal)\">$title: $antal_studenter";
			$listcontent .= "($maxantal)";
			$listcontent .= "</option>";
		}
		
	//}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	
	
	$content = "Grupper:<br /><select style=\"width:170px;font-size:0.9em;\" name=\"lbGrupp\" id=\"lbGrupp\" size=\"8\"";
	//if($type!="skapa") $content .= "onchange=\"listStudent(this.options[this.options.selectedIndex].value, '$pid', '$moment'); return false;\"";
	$content .= "onchange=\"listStudent(this.options[this.options.selectedIndex].value, '$pid', '$moment', '$type'); return false;\"";
	$content .= ">$listcontent</select>";
	
	return "$content|$strMessage";
}

function registerGrupp($kurskod, $pid, $moment, $type) {
	//die("$kurskod, $pid, $moment, $type");
	session_start(); 
	$sid = $_SESSION["tx_grupptillh_pi1"];
	
	if($sid) {
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("S.fnamn, S.enamn, S.epost, S.telefon, G.uid, G.members", "tx_grupptillh_student S LEFT JOIN tx_grupptillh_grupp G ON S.uid = G.owner", "S.pid = " . intval($pid) . " AND S.deleted = 0 AND S.uid = " . intval($sid), "", "", "") or die("157; ".mysql_error());
		
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
			$fnamn = $row["fnamn"];
			$enamn = $row["enamn"];
			$epost = $row["epost"];
			$telefon = $row["telefon"];
			$members = $row["members"];
		}
	} else {
		$mandatory = " *";
	}
	
	$content .= "
	<div>
		<div style=\"float:left; width:380px;\">
		
			<div style=\"clear:both; height:25px;\"><b>Personuppgifter</b></div>
			
			<div style=\"clear:both; height:30px;\">
				<div style=\"float:left; width:125px; \">
					<label id=\"lblNamn\">Namn$mandatory</label>
				</div>
				<div id=\"namn\" style=\"float:left;\">";
					if($sid) {
						$content .= "$fnamn $enamn";
					} else {
						$content .= "<input name=\"txtFnamn\" id=\"txtFnamn\" type=\"text\" value=\"\" size=\"16\" /> <input name=\"txtEnamn\" id=\"txtEnamn\" type=\"text\" value=\"\" size=\"16\" />";
					}
				$content .= "</div>
			</div>
			
			<div style=\"clear:both; height:30px;\">
				<div style=\"float:left; width:125px; \">
					<label id=\"lblEpost\">Epost$mandatory</label>
				</div>
				<div id=\"epost\" style=\"float:left;\">";
					if($sid) {
						$content .= "$epost";
					} else {
						$content .= "<input name=\"txtEpost\" id=\"txtEpost\" type=\"text\" value=\"\" size=\"37\" />";
					}
				$content .= "</div>
			</div>";
			
			if(!$sid) {
				$content .= "<div id=\"epostRepeat\" style=\"clear:both; height:30px;\">
				<div style=\"float:left; width:125px;white-space:nowrap;\">
					<label id=\"lblEpost\">Upprepa epost$mandatory</label>
				</div>
				<div style=\"float:left;\">
					<input name=\"txtEpostRepeat\" id=\"txtEpostRepeat\" type=\"text\" value=\"\" size=\"37\" />
				</div>
			</div>";
			}
			
			$content .= "<div style=\"clear:both; height:30px;\">
				<div style=\"float:left; width:125px;\">
					<label id=\"lblTelefon\">Telefon$mandatory</label>
				</div>
				<div id=\"telefon\" style=\"float:left;\">";
					if($sid) {
						$content .= "$telefon";
					} else {
						$content .= "<input name=\"txtTelefon\" id=\"txtTelefon\" type=\"text\" value=\"\" size=\"37\" />";
					}
				$content .= "</div>
			</div>
		
		</div>
		
		<div style=\"float:left;\">
			<div style=\"clear:both; height:25px;\"><b>Gruppmedlemmar</b></div>
			
			<div style=\"clear:both; height:30px;\">
				<div style=\"float:left;\">
					<textarea name=\"txtStudents\" id=\"txtStudents\" cols=\"20\" rows=\"10\">$members</textarea>";
					//<select name=\"possibleStudents\" id=\"possibleStudents\" size=\"10\" style=\"width:150px;\" onchange=\"MoveOptions('possibleStudents', 'lbStudent', '', ''); return false;\">
						//$possibleStudents
					//</select>			
				$content .= "</div>
			</div>
		</div>
	</div>
	
	<div style=\"clear:both; height:30px; margin-top:20px;\">";
	//if($sid) {
	//	$content .= "<input name=\"remove\" id=\"remove\" type=\"button\" value=\"Ta bort gruppen\" onclick=\"removeGrupp('$sid', '$gid'); return false;\" />";
	//} else {
		$content .= "<input name=\"spara\" id=\"spara\" type=\"button\" value=\"Spara\" onclick=\"saveGrupp('$kurskod', '$pid', '$moment', '$sid', '$uid', 'skapa'); return false;\" />";
		if(!$sid) $content .= "&nbsp;<input name=\"avbryt\" id=\"avbryt\" type=\"button\" value=\"Avbryt\" onclick=\"formStudent('$moment', '$kurskod', '', '$pid', 'skapa', 'labelStudent'); return false;\" />";
	//}
	$content .= "</div>";
	return $content;
}

function saveGrupp($query, $kurskod, $pid, $moment, $type) {
	//die("$query - $kurskod - $pid - $moment - $type");
	$i=0;
	$queryArray = explode("|", $query);
	if($queryArray[0]=="ny") {
		$fnamn = addslashes($queryArray[1]);
		$enamn = addslashes($queryArray[2]);
		$epost = addslashes($queryArray[3]);
		$telefon = addslashes($queryArray[4]);
		$members = addslashes($queryArray[5]);
		$insertArray = array(
			"pid" => intval($pid),
			"fnamn" => remove_pipe_grid($fnamn),
			"enamn" => remove_pipe_grid($enamn),
			"epost" => remove_pipe_grid($epost),
			"telefon" => remove_pipe_grid($telefon),
			"tstamp" => time(),
			"crdate" => time()
		);
		$namn = addslashes($fnamn) . " " . addslashes($enamn);
		
		$res = $GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_grupptillh_student", $insertArray) or die("302; ".mysql_error());
		$sid = $GLOBALS['TYPO3_DB']->sql_insert_id() or die("285; ".mysql_error());
					
		session_start(); 
		$_SESSION["tx_grupptillh_pi1"] = $sid;
		$startIndex=4;
		$content .= "<div class=\"result\"><b>Förnamn: </b>$fnamn</div>";
		$content .= "<div class=\"result\"><b>Efternamn: </b>$enamn</div>";
		$content .= "<div class=\"result\"><b>Epost: </b>$epost</div>";
		$content .= "<div class=\"result\"><b>Telefon: </b>$telefon</div>";
		
		$res = $GLOBALS["TYPO3_DB"]->sql_query( "INSERT INTO tx_grupptillh_grupp(pid, mid, title, members, owner, tstamp, crdate) SELECT " . intval($pid) . ", " . intval($moment) . ", concat('Grupp ', MAX(uid) + 1, '-$namn'), '$members', $sid, " . time() . "," . time() . " FROM tx_grupptillh_grupp") or die("320; ".mysql_error());
	} else {
		$sid = $queryArray[1];
		$members = $queryArray[2];
		$gruppId = $queryArray[3];
		$members = str_replace("<br /><br />", "\n", $members);
		$members = str_replace("<br />", "\n", $members);
		$updateArray = array(
			"members" => addslashes(remove_pipe_grid($members)),
			"tstamp" => time()
		);
		
		if($gruppId) {
			$res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("tx_grupptillh_grupp", "owner = " . intval($sid), $updateArray) or die("333; ".mysql_error());
		} else {
			$res = $GLOBALS["TYPO3_DB"]->sql_query( "INSERT INTO tx_grupptillh_grupp(pid, mid, title, members, owner, tstamp, crdate) SELECT $pid, $moment, concat('Grupp ', MAX(uid) + 1, '-', (SELECT CONCAT(fnamn, ' ', enamn) FROM tx_grupptillh_student WHERE uid=" . intval($sid) . ")), '$members', " . intval($sid) . ", " . time() . "," . time() . " FROM tx_grupptillh_grupp") or die("335; ".mysql_error());

		}
	}
}

function removeGrupp($query) {
	$queryArray = explode(";", $query);
	if($queryArray[0]) {
		$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp_student", "uid=" . intval($queryArray[1])) or die("344; ".mysql_error());
		$GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp", "uid=" . intval($queryArray[0])) or die("345; ".mysql_error());
	} else {
		$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp_student", "uid=" . intval($queryArray[1]) . " AND sid = " . intval($queryArray[0])) or die("347; ".mysql_error());
	}
}

function loginStudent($moment, $epost, $kurskod, $type, $pid) {
	$i = 0;
	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid, fnamn, enamn, epost, telefon", "tx_grupptillh_student", "epost = '" . addslashes($epost) . "' AND deleted = 0", "", "", "") or die("353; ".mysql_error());
	while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
		$uid = $row["uid"];
		$fnamn = $row["fnamn"];
		$enamn = $row["enamn"];
		$epost = $row["epost"];
		$telefon = $row["telefon"];
	}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	
	if($uid) {
		session_start(); 
		$_SESSION["tx_grupptillh_pi1"] = $uid;
	
		$content .= "
		<div style=\"margin-top:10px;\">
		<b>Namn: </b>$fnamn $enamn
		</div>
		<div style=\"margin-top:10px;\">
		<b>Epost: </b>$epost
		</div>
		<div style=\"margin-top:10px;\">
		<b>Telefon: </b>$telefon
		</div>
		";
	} else {
		$content .= "
		Epostadress: <input id=\"txtEpost\" name=\"txtEpost\" size=\"45\" value=\"\" type=\"text\" />&nbsp;<input id=\"btnLogin\" name=\"btnLogin\" value=\"Logga in\" type=\"button\" onclick=\"loginStudent('$moment', '$kurskod', '$pid', '$type'); return false;\" />
		<br /><br />
		Klicka här om du inte tidigare anmält dig:&nbsp;<input id=\"btnRegister\" name=\"btnRegister\" value=\"Registrera mig\" type=\"button\" onclick=\"registerStudent('$moment', '$kurskod', '$pid'); return false;\" />|error";
	}
	
	return $content;
}
	
function listStudent($gruppId, $moment, $type, $pid) {
	
	//		die("$gruppId; $moment; $type; $sid");
	$i = 0;
	session_start(); 
	$sid = $_SESSION["tx_grupptillh_pi1"];
	
	if($type) {
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("owner, members", "tx_grupptillh_grupp", "pid = " . intval($pid) . " AND deleted = 0 AND uid = " . intval($gruppId), "", "", "") or die("395; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$members = $row["members"];
			$owner = $row["owner"];
		}
	/*if($type=="skapa") {
$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("members", "tx_grupptillh_grupp", "pid = " . intval($pid) . " AND deleted = 0 AND owner = " . intval($sid), "", "", "") or die("395; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$members = $row["members"];
		}	*/	
	} else {
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("S.uid AS studentId, S.fnamn, S.enamn, S.epost, S.telefon, G.uid, G.title, G.maxantal", "tx_grupptillh_grupp G LEFT JOIN tx_grupptillh_grupp_student GS ON G.uid = GS.gid LEFT JOIN tx_grupptillh_student S ON GS.sid = S.uid", "G.uid = " . intval($gruppId) . " AND G.deleted = 0", "", "S.enamn, S.fnamn", "") or die("400; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
			$studentId = $row["studentId"];
			$fnamn = $row["fnamn"];
			$enamn = $row["enamn"];
			$epost = $row["epost"];
			$telefon = $row["telefon"];
			$title = $row["title"];
			$maxantal = $row["maxantal"];
			if($fnamn) {
				$listVal .= "<option value=\"$studentId\"";
				if($studentId==$sid) $listVal .= " selected = \"selected\"";
				$listVal .= " title=\"$fnamn $enamn\">$fnamn $enamn</option>";
			}
		}
	}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	$content .= "Studenter:<br />";
	//if($type=="skapa") {
	if($type) {
		$membersArray = explode(",", $members);
		//$content .= "<textarea disabled=\"disabled\" name=\"listStudents\" id=\"listStudents\" cols=\"20\" rows=\"7\">"
		$content .= "<select style=\"width:160px;font-size:0.9em;\" name=\"listStudents\" id=\"listStudents\" size=\"8\"";
		if($sid==$owner) $content .= " onchange=\"formStudent('$moment', '$gruppId', this.options[this.options.selectedIndex].value+','+this.options.selectedIndex, '$pid', '$type', 'message'); return false;\"";
		$content .= "/>";
		//
		foreach($membersArray as $key => $value) {
			if($value) $content .= "<option value=\"$value\" title=\"$value\">$value</option>";
		}
		//$content .= "</textarea>";
		$content .= "</select>";
	} else {
		$content .= "<select style=\"width:160px;font-size:0.9em;\" name=\"lbStudent\" id=\"lbStudent\" size=\"8\">$listVal</select>";
	}
	
	return $content;
}

function formStudent($moment, $query, $titel, $pid, $type) {
	$titleArray = explode(",", $titel);
	$titel = $titleArray[0];
	$titelIndex = $titleArray[1];
	$kurskod = $query;
	$studentFlag = 0;
	//die("$moment-$query-$titel-$pid-$type");
	session_start(); 
	if(isset($_SESSION["tx_grupptillh_pi1"])) {
		
		$sid = $_SESSION["tx_grupptillh_pi1"];

		//$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid", "tx_grupptillh_grupp_student", "gid = " . intval($query) . " AND sid = " . intval($sid) . " AND deleted = 0", "", "", "") or die("404; ".mysql_error());
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("GS.uid", "tx_grupptillh_grupp_student GS LEFT JOIN tx_grupptillh_grupp G ON G.uid = GS.gid", "G.mid = " . intval($moment) . " AND GS.sid = " . intval($sid) . " AND GS.deleted = 0 AND G.deleted = 0", "", "", "") or die("406; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
			//$gruppId = $row["gruppId"];
		}

		//$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("title, mid", "tx_grupptillh_grupp G", "uid = " . intval($query) . " AND deleted = 0", "", "", "") or die("442; ".mysql_error());
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("G.title, G.mid, GS.sid", "tx_grupptillh_grupp G LEFT JOIN tx_grupptillh_grupp_student GS ON G.uid = GS.gid", "G.uid = " . intval($query) . " AND G.deleted = 0", "", "", "") or die("413; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$title = $row["title"];
			$mid = $row["mid"];
			if($row["sid"]==$sid) $studentFlag = 1;
		}
		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		//Villkor som talar om att man ska lägga till
		if($type and $titel) {
			$content = "<input id=\"btnRemove\" name=\"btnRemove\" style=\"font-weight:bold;\" value=\"Ta bort $titel ur gruppen: $title\" type=\"button\" onclick=\"removeStudent('$query','$uid','$titel,$titelIndex','$mid','$type','$pid'); return false;\" />";
		} else if($type) {
			$content = "Student:&nbsp;<input name=\"txtStudent\" id=\"txtStudent\" type=\"text\" size=\"25\" />
			&nbsp;<input id=\"btnAdd\" name=\"btnAdd\" style=\"font-weight:bold;\" value=\"Lägg till i $title\" type=\"button\" onclick=\"addStudent('$query',document.getElementById('txtStudent').value, '$title','$mid','$pid','$type'); return false;\" /><br />Du blir ägare till gruppen genom att registrera övriga gruppmedlemmar här<br />(Klicka på studentens namn i listan för att ta bort honom/henne.)";
		} else if($uid and $studentFlag == 0) {
			$content .= message("Du kan inte anmäla dig till två grupper på samma moment.", "utropstecken");
		} else if($uid) {
			$content = "<input id=\"btnRemove\" name=\"btnRemove\" style=\"font-weight:bold;\" value=\"Ta bort mig från: $title\" type=\"button\" onclick=\"removeStudent('$query','$uid','$title','$mid','$type','$pid'); return false;\" />";
		} else {
			$content .= "<input id=\"btnAttach\" name=\"btnAttach\" style=\"font-weight:bold;\" value=\"Anmäl mig till: $title\" type=\"button\" onclick=\"addStudent('$query','$sid', '$title','$mid','$pid','$type'); return false;\" />";
		}
	} else {
		$content = "
		Epostadress: <input id=\"txtEpost\" name=\"txtEpost\" size=\"45\" value=\"\" type=\"text\" />&nbsp;<input id=\"btnLogin\" name=\"btnLogin\" value=\"Logga in\" type=\"button\" onclick=\"loginStudent('$moment', '$query', '$pid', '$type'); return false;\" />
		<br /><br />";
		/*if($type=="skapa") {
			$content .= "Klicka här om du inte tidigare anmält dig:&nbsp;<input id=\"btnRegister\" name=\"btnRegister\" value=\"Registrera mig\" type=\"button\" onclick=\"registerGrupp('$moment', '$kurskod', '$pid', '$type'); return false;\" />";
		} else {*/
			$content .= "Klicka här om du inte tidigare anmält dig:&nbsp;<input id=\"btnRegister\" name=\"btnRegister\" value=\"Registrera mig\" type=\"button\" onclick=\"registerStudent('$moment', '$query', '$pid'); return false;\" />";
		//}
	}
	return $content;
}

function getStudent($sid) {
	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("fnamn, enamn, epost, telefon", "tx_grupptillh_student", "uid = " . intval($sid) . " AND deleted = 0", "", "", "") or die("469; ".mysql_error());
	while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
		$fnamn = $row["fnamn"];
		$enamn = $row["enamn"];
		$epost = $row["epost"];
		$telefon = $row["telefon"];
	}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	
	$content .= "
	<div style=\"margin-top:10px;\">
	<b>Namn</b>: $fnamn $enamn
	</div>
	<div style=\"margin-top:10px;\">
	<b>Epost</b>: $epost
	</div>
	<div style=\"margin-top:10px;\">
	<b>Telefon</b>: $telefon
	</div>
	";
	
	return $content;
}

function registerStudent($moment, $kurskod, $pid) {
	$content .= "
	<div style=\"clear:both; height:30px;\">
		* = Obligatoriska fält
	</div>	
	
	<div style=\"clear:both; height:30px;\">
		<div style=\"float:left; width:115px; \">
			<label id=\"lblNamn\">Namn *</label>
		</div>
		<div style=\"float:left;\">
			<input name=\"fnamn\" id=\"fnamn\" type=\"text\" value=\"\" size=\"20\" /> <input name=\"enamn\" id=\"enamn\" type=\"text\" value=\"\" size=\"20\" />
		</div>
	</div>
	
	<div style=\"clear:both; height:30px;\">
		<div style=\"float:left; width:115px; \">
			<label id=\"lblEpost\">Epost *</label>
		</div>
		<div style=\"float:left;\">
			<input name=\"epost\" id=\"epost\" type=\"text\" value=\"\" size=\"45\" />
		</div>
	</div>
	
	<div style=\"clear:both; height:30px;\">
		<div style=\"float:left; width:115px;\">
			<label id=\"lblEpostRepeat\">Upprepa epost *</label>
		</div>	
		<div style=\"float:left;\">
			<input name=\"epostRepeat\" id=\"epostRepeat\" type=\"text\" value=\"\" size=\"45\" />
		</div>
	</div>
	
	<div style=\"clear:both; height:30px;\">
		<div style=\"float:left; width:115px;\">
			<label id=\"lblTelefon\">Telefon *</label>
		</div>
		<div style=\"float:left;\">
			<input name=\"telefon\" id=\"telefon\" size=\"20\" type=\"text\" value=\"\" />
		</div>
	</div>
	<div style=\"clear:both; height:30px;\">
		<input name=\"spara\" id=\"spara\" type=\"button\" value=\"Spara\" onclick=\"saveStudent('$moment', '$kurskod', '$pid'); return false;\" />&nbsp;<input name=\"avbryt\" id=\"avbryt\" type=\"button\" value=\"Avbryt\" onclick=\"formStudent('$moment', '$kurskod', '', '$pid', '$type', 'labelStudent'); return false;\" />
	</div>";
	return $content;	
}

function saveStudent($moment, $query, $kurskod, $pid) {
	//die("$moment, $query, $kurskod, $pid");
	$i=0;
	$queryArray = explode("|", $query);
	
	$fnamn = addslashes($queryArray[0]);
	$enamn = addslashes($queryArray[1]);
	$epost = addslashes($queryArray[2]);
	$telefon = addslashes($queryArray[3]);
	
	$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid", "tx_grupptillh_student", "epost = '" . addslashes($epost) . "' AND deleted = 0", "", "", "") or die("601; ".mysql_error());
	while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
		$uid = $row["uid"];
	}
	$GLOBALS["TYPO3_DB"]->sql_free_result($res);	
	
	if($uid) {
		$content .= "error";
	} else {
		$insertArray = array(
			"pid" => intval($pid),
			"fnamn" => remove_pipe_grid($fnamn),
			"enamn" => remove_pipe_grid($enamn),
			"epost" => remove_pipe_grid($epost),
			"telefon" => remove_pipe_grid($telefon),
			"tstamp" => time(),
			"crdate" => time()
		);
				
		$res = $GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_grupptillh_student", $insertArray) or die("569; ".mysql_error());
		$sid = $GLOBALS['TYPO3_DB']->sql_insert_id() or die("570; ".mysql_error());
			
		if(sendMail($fnamn,$enamn,$epost,$telefon,$kurskod)) {
			$mailResult = "Mail was sent to $epost.";
		} else {
			$mailResult = "There was a problem and the message was probably not sent.";
		}
		
		session_start(); 
		$_SESSION["tx_grupptillh_pi1"] = $sid;
		
		$content .= "<div class=\"result\"><b>$mailResult</b></div>";
		$content .= "<div class=\"result\"><b>Förnamn: </b>$fnamn</div>";
		$content .= "<div class=\"result\"><b>Efternamn: </b>$enamn</div>";
		$content .= "<div class=\"result\"><b>Epost: </b>$epost</div>";
		$content .= "<div class=\"result\"><b>Telefon: </b>$telefon</div>";
	}
	return $content;
}

function addStudent($gruppId, $studentId, $titel, $pid, $type) {
	//die("$gruppId, $studentId, $titel, $pid, $type");
	session_start(); 
	$sid = $_SESSION["tx_grupptillh_pi1"];

	if(is_numeric($studentId)) {
		$insertArray = array(
			"pid" => intval($pid),
			"gid" => intval($gruppId),
			"sid" => intval($studentId),
			"tstamp" => time(),
			"crdate" => time()
		);	
		$res = $GLOBALS["TYPO3_DB"]->exec_INSERTquery("tx_grupptillh_grupp_student", $insertArray) or die("635; ".mysql_error());
		
		$content = message("Du är anmäld till: $titel.", "utropstecken");
	} else {
	
		$studentId = str_replace(",", "", $studentId);
		$ownerName = "";
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("fnamn, enamn", "tx_grupptillh_student", "deleted=0 and uid = " . intval($sid), "", "", "") or die("646; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$fnamn = $row["fnamn"];
			$enamn = $row["enamn"];
		}
		
		
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("members", "tx_grupptillh_grupp", "deleted=0 and uid = " . intval($gruppId), "", "", "") or die("652; ".mysql_error());	
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$members = $row["members"];
		}		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		if(!$members) $ownerName = "$fnamn $enamn,";
		//$res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("tx_grupptillh_grupp", "uid = " . intval($gruppId), $updateArray) or die("608; ".mysql_error());
		$res = $GLOBALS["TYPO3_DB"]->sql_query( "UPDATE tx_grupptillh_grupp SET members = CONCAT(members, ',', '$ownerName $studentId'), owner = " . intval($sid) . ", tstamp=" . time() . " WHERE uid = " . intval($gruppId)) or die("609; ".mysql_error());
	}
	
	return $content;
}

function removeStudent($uid, $gruppId, $titel, $type) {
	session_start(); 
	$sid = $_SESSION["tx_grupptillh_pi1"];

	//die("$uid - $titel - $type - $gruppId - $sid");
	$titelArray = explode(",", $titel);
	$studentTitel = addslashes($titelArray[0]);
	$studentIndex = $titelArray[1];
	
	if($type) {
		//Få fram ägarens namn
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("S.fnamn, S.enamn, G.members", "tx_grupptillh_student S LEFT JOIN tx_grupptillh_grupp G ON S.uid = G.owner", "S.deleted=0 and S.uid = " . intval($sid), "", "", "") or die("686; ".mysql_error());
		while ($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)) {
			$namn = $row["fnamn"];
			$namn .= " " . $row["enamn"];
			$members = $row["members"];
		}
		if(substr($members, 0, 1)==",") $members = substr($members, 1);
		
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);

		$membersArray = explode(",", $members);
		unset($membersArray[$studentIndex]);
		$members = implode(",", $membersArray);

		$res = $GLOBALS["TYPO3_DB"]->sql_query( "UPDATE tx_grupptillh_grupp SET members = '$members' WHERE uid=" . intval($gruppId) . " and (members = '" . addslashes($namn) . "' or '" . addslashes($namn) . "' != '" . addslashes($studentTitel) . "')") or die("699; ".mysql_error());
		$res2 = mysql_affected_rows();
		$res = $GLOBALS["TYPO3_DB"]->sql_query( "UPDATE tx_grupptillh_grupp SET owner = 0 WHERE members = ''") or die("700; ".mysql_error());
		if($res2 == 0) { 
			$content = message("Du kan inte ta bort dig själv<br />så länge det finns andra kvar i gruppen.", "utropstecken");
		} elseif($res2 == 1) {
			$content = message("$studentTitel är borttagen från gruppen.", "utropstecken");
		}
	} else {
		$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp_student", "uid=" . intval($uid)) or die("601; ".mysql_error());
		$content = message("Du är borttagen från gruppen.", "utropstecken");
	}
		
	return $content;
}

function sendMail($fnamn,$enamn,$epost,$telefon,$kurskod) {
	//$epost = "tomas.havner@kansli.lth.se";
	$to = $epost;
	$subject = "Registrering för att kunna anmäla sig till grupper på $kurskod";
	$message = "Hej $fnamn $enamn!" . "\r\n" . "\r\n";
	$message .= "Använd din epostadress för att logga in och anmäla dig till grupper på $kurskod." . "\r\n" . "\r\n";
	$message .= "Detta är ett automatgenererat mail. Svara INTE på detta mail." . "\r\n" . "\r\n";
	$message .= "mvh" . "\r\n" . "\r\n" . "LTH";
	$headers = 'From: noreply@lth.se' . "\r\n" .
    'Reply-To: noreply@lth.se' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
}

function message($message, $icon) {
	$img = "";
	if($icon=="varning") {
		$img = '<img src="/typo3conf/ext/grupptillh/varning.gif" border="0">';
	} else {
		$img = '<img src="/typo3conf/ext/grupptillh/utropstecken.gif" border="0">';
	}
	$content = '<div style="float:right;position:relative;left:-50%;">';
	$content .= '<div style="float:left;position:relative;left:50%; bottom:7px; margin-left:10px;">' . $img . '</div>';
	$content .= '<div style="float:left;position:relative;left:50%;">' . $message . '</div>';
	$content .= '</div>';
	return $content;
}

function remove_pipe_grid($input) {
	$input = str_replace("|", " ", $input);
	$input = str_replace(";", " ", $input);
	return $input;
}
?>