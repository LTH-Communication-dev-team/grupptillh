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
 * Plugin 'Grupptillhorighet' for the 'grupptillh' extension.
 *
 * @author	Tomas Havner <tomas.havner@kansli.lth.se>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_grupptillh_pi1 extends tslib_pibase {
	var $prefixId = 'tx_grupptillh_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_grupptillh_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'grupptillh';	// The extension key.
	var $pi_checkCHash = TRUE;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
                
                setcookie("fe_typo_dummyuser", "dummyuser", time()+3600, "/");
		
		$this->pi_initPIflexForm();
		$piFlexForm = $this->cObj->data["pi_flexform"];
       	$index = $GLOBALS["TSFE"]->sys_language_uid;
		$sDef = current($piFlexForm["data"]);       
		$lDef = array_keys($sDef);
		$kurskod = $this->pi_getFFvalue($piFlexForm, "kurskod", "sDEF", $lDef[$index]);
		$moment = $this->pi_getFFvalue($piFlexForm, "moment", "sDEF", $lDef[$index]);
		$pid = $this->cObj->data['pages'];
		$type = $this->pi_getFFvalue($piFlexForm, "type", "sDEF", $lDef[$index]);
		if(!pid) return "<p>Ange startpunkt!</p>";
		if(!kurskod) return "<p>Ange kurskod!</p>";
		if(!moment) return "<p>Ange minst ett moment!</p>";
		
		$GLOBALS["TSFE"]->additionalHeaderData["tx_grupptillh_js"] = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"/typo3conf/ext/grupptillh/grupptillh.js\"></script>"; 
		$GLOBALS["TSFE"]->additionalHeaderData["tx_grupptillh_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/typo3conf/ext/grupptillh/grupptillh.css\" />";
				
		$content .= "<fieldset class=\"fieldset\"><legend style=\"margin-bottom:15px;\">&nbsp;Registrera grupptillh&ouml;righet&nbsp;</legend>";
		
		$content .= "<div style=\"clear:both; height:150px;\" id=\"\">";
				
		$content .= "<div id=\"listMoment\" class=\"tx_grupptillh_listbox_moment\">Moment:<br />
		<select style=\"width:150px; font-size:0.9em;\" name=\"lbMoment\" id=\"lbMoment\" size=\"8\"></select>
		</div>";
		
		$content .= "<div id=\"listGrupp\" class=\"tx_grupptillh_listbox_grupp\">
		Grupp:<br /><select style=\"width:170px;font-size:0.9em;\" name=\"lbGrupp\" id=\"lbGrupp\" size=\"8\"></select>
		</div>";
		//<input name=\"valjGrupp\" id=\"valjGrupp\" type=\"button\" value=\"V&auml;lj grupp\" onclick=\"getStudents(); return false;\" />
			
		$content .= "<div id=\"listStudent\" class=\"tx_grupptillh_listbox_student\">
		Studenter:<br /><select style=\"width:160px;font-size:0.9em;\" name=\"lbStudent\" id=\"lbStudent\" size=\"8\"></select>
		</div>";
		
		$content .= "</div>";
		
		$content .= "<div id=\"message\" align=\"center\" class=\"tx_grupptillh_message\">";
		$content .= "";
		$content .= "</div>";
		
		$content .= "<div id=\"labelStudent\" style=\"clear:both; margin-top:15px; height:90px;\"></div>";
		
		$content .= "<div id=\"formStudent\" style=\"clear:both; margin-top:15px; margin-bottom:50px;\">";
		$content .= "</div>";
		
		$content .= "<div id=\"logout\" style=\"clear:both; height:50px;\">";
		session_start();
		if(isset($_SESSION['tx_grupptillh_pi1'])) $content .= "<a href=\"#\" onclick=\"logout('$moment','$kurskod','$pid','$type'); return false;\">Logga ut</a>";
		$content .= "</div>";
		
		$content .= "<iframe id=\"historyFrame\" src=\"/typo3conf/ext/institutioner/pi1/HistoryFrame.htm\" style=\"display:none;\"></iframe>";

		$content .= "<script language=\"javascript\">";
		
		if(isset($_SESSION['tx_grupptillh_pi1'])) {
			//$content .= "lista('$moment', 'listMoment', '', '$kurskod', '', '', '', '', '', '', 'listMoment', 'xmlHttp1');";
			$content .= "listMoment('$kurskod', '$type', '$moment', 'xmlHttp1');";
			//if($moment) $content .= "lista('$moment', 'listGrupp', '$moment', '', '', '', '', '', '', '$type', 'listGrupp', 'xmlHttp2');";
		 	if($type=="skapa") {
				$content .= "lista('$moment', 'listGrupp', '', '$kurskod', '', '', '', '', '$pid', '$type', 'listGrupp', 'xmlHttp2');";
				//$content .= "lista('$moment', 'registerGrupp', '', '$kurskod', '', '', '', '', '$pid', '$type', 'labelStudent', 'xmlHttp3');";
				$content .= "lista('$moment', 'listStudent', '', '$kurskod', '', '', '', '', '$pid', '$type', 'listStudent', 'xmlHttp4');";
			} else {
				$content .= "lista('$moment', 'getStudent', '" . $_SESSION['tx_grupptillh_pi1'] . "', '$kurskod', '', '', '', '', '', '$type', 'labelStudent', 'xmlHttp3');";
			}
		} else {
			//$content .= "lista('$moment', 'listMoment', '$kurskod', '', '', '', '', '', '', '', 'listMoment', 'xmlHttp1');";
			$content .= "formStudent('$moment', '$kurskod', '', '$pid', '$type', 'labelStudent');";
			$content .= "message('Du m&aring;ste vara inloggad f&ouml;r att anm&auml;la dig till en grupp.', 'utropstecken');";
		}
        $content .= "</script>";
		
		
		$content .= "<noscript><p></p><p>JavaScript is turned off in your web browser. Turn it on to take full advantage of this site, then refresh the page.</p></noscript>";

		$content .= "</fieldset>";

		$content .= "<div style=\"clear:both; margin-bottom:30px;\"></div>";
	
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/grupptillh/pi1/class.tx_grupptillh_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/grupptillh/pi1/class.tx_grupptillh_pi1.php']);
}

?>