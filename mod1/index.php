<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Tomas Havner <tomas.havner@kansli.lth.se>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Grupptillhorighet' for the 'grupptillh' extension.
 *
 * @author	Tomas Havner <tomas.havner@kansli.lth.se>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:grupptillh/mod1/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_grupptillh_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	 /**
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
				"2" => $LANG->getLL("function2"),
				"3" => $LANG->getLL("function3"),
			)
		);
		parent::menuConfig();
	}*/

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<style type="text/css">
				<!--
				.grupptillh_adminTable { 
					margin-left:10px; width:700px; border:1px #a2aab8 solid;
				}
				.grupptilh_adminTable td { 
					padding:4px; margin:0px; 
				}
				-->
				</style>
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						//alert(URL);
						document.location = URL;
					}
					
					function removeStudent(URL) {
						//alert(pageid + "," + studentid + "," + gruppid + "," + type);
						var answer = confirm("&Auml;r du s&auml;ker p&aring; att du vill radera posten?");
						if(answer) {
							//var URL = "index.php?id="+pageid+"&amp;studentid="+studentid+"&amp;SET[function]=3&amp;gruppid="+gruppid+"&amp;type="+type;   
						//	document.write(URL);
							document.location = URL;
						} else {
							return false;
						}
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_cs($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		$pageid = t3lib_div::_GP("id");
		$set = t3lib_div::_GP("SET");
		$screen = t3lib_div::_GP("screen");
		$type = t3lib_div::_GP("type");
		$kursid = t3lib_div::_GP("kursid");
		$momentid = t3lib_div::_GP("momentid");
		$studentid = t3lib_div::_GP("studentid");
		$gruppid = t3lib_div::_GP("gruppid");
		$type = t3lib_div::_GP("type");
		if(!$screen) $screen = "0";
		
		if($set) {
			//$switchCore = (string)$this->MOD_SETTINGS["function"];
			$switchCore = (string)$set["function"];
		}
		
			//Printa sidhuvud
		$content .= $this->printHeader($pageid, $kursid, $momentid);
		
		switch($switchCore)	{
			case 1:
				$content .= $this->printStudentList($pageid, $momentid, $kursid);
				$this->content .= $content;
			break;
			case 2:
				$content .= $this->printGroupList($pageid, $momentid, $kursid);
				$this->content .= $content;
			break;
			case 3:
				$this->removeStudent($pageid, $momentid, $studentid, $gruppid, $type, $kursid);
				if($type=="all") {
					$content .= $this->printStudentList($pageid, $momentid, $kursid);
				} else {
					$content .= $this->printGroupList($pageid, $momentid, $kursid);
				}
				$this->content .= $content;
			break;			
			default:
				$content .= "<h1>V&auml;lkommen till administration av gruppanm&auml;lan!</h1>";
				$this->content .= $content;
		}
	}
	
	/*
	SELECT * FROM tx_grupptillh_kurs t;
	SELECT * FROM tx_grupptillh_moment t;
	SELECT * FROM tx_grupptillh_student t;
	*/
	function removeStudent($pageid, $momentid, $studentid, $gruppid, $type, $kursid) {
		//return "$pageid, $momentid, $studentid, $gruppid, $type";
		if($type=="inside") {
			$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("members", "tx_grupptillh_grupp", "uid=" . intval($gruppid), "", "", "") or die("354: ".mysql_error());
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$members = $row["members"];
			$membersArray = explode("\n", $members);
			unset($membersArray[$studentid]);
			$members = implode("\n", $membersArray);
			
			$updateArray = array(
				"members" => $members,
				"tstamp" => time()
			);
			$res = $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("tx_grupptillh_grupp", "uid=" . intval($gruppid), $updateArray) or die("250: ".mysql_error());
		} elseif($type=="rel") {
			$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp_student", "sid=" . intval($studentid) . " AND gid=" . intval($gruppid)) or die("252: ".mysql_error());
		} elseif($type=="all") {
			$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_grupp_student", "sid=" . intval($studentid)) or die("254: ".mysql_error());
			$res = $GLOBALS["TYPO3_DB"]->exec_DELETEquery("tx_grupptillh_student", "uid=" . intval($studentid)) or die("255: ".mysql_error());
		}
	}
	
	function printCourseList($pageid, $kursid) {
		$content .= "<option value=\"\" selected=\"selected\">V&auml;lj en kurs</option>";
		$kurskod = "";
		$title = "";
		
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid, kurskod, title", "tx_grupptillh_kurs", "deleted=0", "", "kurskod", "") or die("264: ".mysql_error());
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$uid = $row["uid"];
			$kurskod = $row["kurskod"];
			$title = $row["title"];
			$content .= "<option value=\"$uid\"";
			if($uid==$kursid) $content .= " selected=\"selected\"";
			$content .= ">$kurskod, $title</option>";
		}
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		return $content;
	}
	
	function printMomentList($pageid, $momentid, $kursid) {
		$content .= "<option value=\"\" selected=\"selected\">V&auml;lj ett moment</option>";
		$title = "";
		
		if($kursid) {
			$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid, title", "tx_grupptillh_moment", "kid = " . intval($kursid) . " AND deleted=0", "", "title", "") or die("354: ".mysql_error());
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$uid = $row["uid"];
				$title = $row["title"];
				$content .= "<option value=\"$uid\"";
				if($uid==$momentid) $content .= " selected=\"selected\"";
				$content .= ">$title</option>";
			}
			$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		};
		return $content;
	}	

	function printStudentList($pageid, $momentid, $kursid) {

		$i = 0;
		$bgcolor = "";
		
		$content .= $this->printTableHeader();
		
		$content .= "<tr>";
		$content .= "<td colspan=\"5\" style=\"height:20px; color:#ffffff; background-color:#cccccc; padding:3px; font-weight:bold; border-bottom:1px black solid;\">Lista per student</td>";
		$content .= "</tr>";
		
		$content .= "<tr>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Fnamn</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Enamn</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Epost</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Telefon</td>";
		$content .= "<td style=\"height:20px; width:50px; background-color:$bgcolor;\">&nbsp;</td>";
		$content .= "</tr>";
		
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("uid as studentid, fnamn, enamn, epost, telefon", "tx_grupptillh_student", "deleted=0", "", "enamn,fnamn,epost", "") or die("314: ".mysql_error());
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$gruppid = $row["gruppid"];
			$title = $row["title"];
			$members = $row["members"];
			$studentid = $row["studentid"];
			$fnamn = $row["fnamn"];
			$enamn = $row["enamn"];
			$epost = $row["epost"];
			$telefon = $row["telefon"];
			if($i%2) {
				$bgcolor = "#ffffff";
			} else {
				$bgcolor = "";
			}
			
			$content .= "<tr>";
			/*if($old_studentid != $studentid) {
				$content .= "<td colspan=\"6\" style=\"height:20px; background-color:$bgcolor; font-weight:bold; border:3px;\">$title</td>";
				$content .= "</tr>";
				$content .= "<tr>";
				$i++;
			}*/

			if($i%2) {
				$bgcolor = "#ffffff";
			} else {
				$bgcolor = "";
			}
			$content .= "<td style=\"height:20px; width:175px; background-color:$bgcolor;\">$fnamn</td>";
			$content .= "<td style=\"height:20px; width:175px; background-color:$bgcolor;\">$enamn</td>";
			$content .= "<td style=\"height:20px; width:175px; background-color:$bgcolor;\">$epost</td>";
			$content .= "<td style=\"height:20px; width:175px; background-color:$bgcolor;\">$telefon</td>";
			$content .= "<td style=\"height:20px; width:25px; background-color:$bgcolor;\">";
			$content .= "<img src=\"garbage.gif\" border=\"0\" onclick=\"removeStudent('index.php?id=$pageid&amp;studentid=$studentid&amp;gruppid=$gruppid&amp;type=all&amp;momentid=$momentid&amp;kursid=$kursid&amp;SET[function]=3');\">";
			$content .= "</td>";
			$content .= "</tr>";
			
			$old_studentid = $studentid;
			$i++;
		}
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		$content .= "</table>";
		
		return $content;	
	}
	
	function printGroupList($pageid, $momentid, $kursid) {
		//die($momentid);
		$i = 0;
		$bgcolor = "";
		
		$content .= $this->printTableHeader();
		$content .= "<tr>";
		$content .= "<td colspan=\"5\" style=\"height:20px; color:#ffffff; background-color:#cccccc; padding:3px; font-weight:bold; \">Lista per grupp</td>";
		$content .= "</tr>";
		
		$content .= "<tr>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Fnamn</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Enamn</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Epost</td>";
		$content .= "<td style=\"height:20px; width:175px; font-weight:bold; background-color:$bgcolor;\">Telefon</td>";
		$content .= "<td style=\"height:20px; width:25px; background-color:$bgcolor;\">&nbsp;</td>";
		$content .= "</tr>";
		
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("G.uid AS gruppid, G.title, G.members, S.uid AS studentid, S.fnamn, S.enamn, S.epost, S.telefon", "tx_grupptillh_grupp G LEFT JOIN tx_grupptillh_grupp_student GS ON G.uid=GS.gid LEFT JOIN tx_grupptillh_student S ON GS.sid = S.uid", "G.mid = " . intval($momentid) . " AND G.deleted=0", "", "G.uid", "") or die("354: ".mysql_error());
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$gruppid = $row["gruppid"];
			$title = $row["title"];
			$members = $row["members"];
			$studentid = $row["studentid"];
			$fnamn = $row["fnamn"];
			$enamn = $row["enamn"];
			$epost = $row["epost"];
			$telefon = $row["telefon"];
			if($i%2) {
				$bgcolor = "#ffffff";
			} else {
				$bgcolor = "";
			}
			
			if($old_gruppid != $gruppid) {
				$content .= "<tr>";
				$content .= "<td colspan=\"5\" style=\"height:20px; background-color:$bgcolor; font-weight:bold; border-top:1px black solid;\">$title</td>";
				$content .= "</tr>";
				$content .= "<tr>";
				$i++;
				if($i%2) {
					$bgcolor = "#ffffff";
				} else {
					$bgcolor = "";
				}
			}
			
			if($studentid) {
				$content .= "<tr>";
				$content .= "<td style=\"height:20px; background-color:$bgcolor;\">$fnamn</td>";
				$content .= "<td style=\"height:20px; background-color:$bgcolor;\">$enamn</td>";
				$content .= "<td style=\"height:20px; background-color:$bgcolor;\">$epost</td>";
				$content .= "<td style=\"height:20px; background-color:$bgcolor;\">$telefon</td>";
				$content .= "<td style=\"height:20px; background-color:$bgcolor;\"><img src=\"garbage.gif\" border=\"0\" onclick=\"removeStudent('index.php?id=$pageid&amp;studentid=$studentid&amp;gruppid=$gruppid&amp;type=rel&amp;momentid=$momentid&amp;kursid=$kursid&amp;SET[function]=3');\"></td>";
				$content .= "</tr>";
			} else {
				$i++;
			}
			if($members) {
				$y = 0;
				$membersArray = explode("\n", $members);
				foreach($membersArray as $key => $value) {
					$i++;
					if($i%2) {
						$bgcolor = "#ffffff";
					} else {
						$bgcolor = "";
					}
					$content .= "<tr>";
					$content .= "<td colspan=\"4\" style=\"height:20px; background-color:$bgcolor;\">$value</td>";
					$content .= "<td style=\"height:20px; background-color:$bgcolor;\"><img src=\"garbage.gif\" border=\"0\" onclick=\"removeStudent('index.php?id=$pageid&amp;studentid=$y&amp;gruppid=$gruppid&amp;type=inside&amp;momentid=$momentid&amp;kursid=$kursid&amp;SET[function]=3');\"></td>";
					$content .= "</tr>";
					$y++;
				}
			}
			//if($old_gruppid != $gruppid) 
			$old_gruppid = $gruppid;
			$i++;
		}
		
		$content .= "</table>";
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
		
		return $content;	
	}
	
	function printTableHeader() {
		$content = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"grupptillh_adminTable\">";
		//$content .= "<tr><td colspan=\"6\" style=\"\"><img src=\"garbage.gif\" border=\"0\"></td></tr>";
		return $content;
	}
	
	function printHeader($pageid, $kursid, $momentid) {
		global $LANG;
		if(!$pageid) $disabled = " disabled=\"disabled\"";
			$content .= "<table border=\"0\" style=\"margin-left:10px; width:700px; margin-bottom:10px; padding:5px;\">";
			$content .= "<tr>";
			
			$content .= "<td align=\"center\" width=\"50%\">";
			$content .= "<input type=\"button\"  value=\"Lista studenter\" onclick=\"jumpToUrl('index.php?id=$pageid&amp;kursid=$kursid&amp;SET[function]=1');\" />";

			$content .= "</td>";
			
			$content .= "<td align=\"center\" width=\"50%\"><select name=\"jumpMenu\" id=\"jumpMenu\" onchange=\"jumpToUrl('index.php?id=$pageid&amp;momentid=$momentid&amp;kursid='+this.options[this.options.selectedIndex].value);\">" . $this->printCourseList($pageid, $kursid) . "</select></td>";
			
			$content .= "<td align=\"center\" width=\"50%\"><select name=\"jumpMenu\" id=\"jumpMenu\" onchange=\"jumpToUrl('index.php?id=$pageid&amp;kursid=$kursid&amp;SET[function]=2&amp;momentid='+this.options[this.options.selectedIndex].value);\">" . $this->printMomentList($pageid, $momentid, $kursid) . "</select></td>";
			
			//$content .= "<td align=\"center\" width=\"20%\" ><input $disabled onclick=\"jumpToUrl('index.php?id=$pageid&amp;screen=$screen&amp;type=$type&amp;SET[function]=1');\" type=\"button\" name=\"btnList\" id=\"btnList\" value=\"" . $LANG->getLL("list_items") . "\" title=\"" . $LANG->getLL("list_items") . "\" /></td>";
			
			$content .= "</tr>";
			$content .= "</tr></table>";
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/grupptillh/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/grupptillh/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_grupptillh_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>