<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_grupptillh_kurs"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_kurs',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_grupptillh_kurs.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, kurskod, title",
	)
);

$TCA["tx_grupptillh_grupp"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_grupptillh_grupp.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, mid, maxantal, members, owner",
	)
);

$TCA["tx_grupptillh_grupp_student"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp_student',		
		'label' => 'uid',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_grupptillh_grupp_student.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, gid, sid",
	)
);

$TCA["tx_grupptillh_student"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_student',		
		'label' => 'enamn',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"default_sortby" => "ORDER BY enamn",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_grupptillh_student.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, fnamn, enamn, epost, telefon",
	)
);

$TCA["tx_grupptillh_moment"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_grupptillh_moment.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, kid, title, grouptype",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(Array('LLL:EXT:grupptillh/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform'; //New
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:grupptillh/flexform_ds_pi1.xml'); //New


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Grupptillhorighet");


if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txgrupptillhM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}
?>