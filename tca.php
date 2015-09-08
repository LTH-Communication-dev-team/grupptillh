<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_grupptillh_kurs"] = Array (
	"ctrl" => $TCA["tx_grupptillh_kurs"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,kurskod,title"
	),
	"feInterface" => $TCA["tx_grupptillh_kurs"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"kurskod" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_kurs.kurskod",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_kurs.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, kurskod, title;;;;2-2-2")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_grupptillh_grupp"] = Array (
	"ctrl" => $TCA["tx_grupptillh_grupp"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,mid,maxantal,members,owner"
	),
	"feInterface" => $TCA["tx_grupptillh_grupp"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"mid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp.mid",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_grupptillh_moment",	
				"foreign_table_where" => "ORDER BY tx_grupptillh_moment.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"maxantal" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp.maxantal",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "1"
				),
				"default" => 0
			)
		),
		"members" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp.members",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"owner" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp.owner",		
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "4",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "1000",
					"lower" => "1"
				),
				"default" => 0
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, mid;;;;3-3-3, maxantal, members, owner")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_grupptillh_grupp_student"] = Array (
	"ctrl" => $TCA["tx_grupptillh_grupp_student"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,gid,sid"
	),
	"feInterface" => $TCA["tx_grupptillh_grupp_student"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"gid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp_student.gid",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_grupptillh_grupp",	
				"foreign_table_where" => "ORDER BY tx_grupptillh_grupp.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"sid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_grupp_student.sid",		
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("",0),
				),
				"foreign_table" => "tx_grupptillh_student",	
				"foreign_table_where" => "ORDER BY tx_grupptillh_student.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, gid, sid")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_grupptillh_student"] = Array (
	"ctrl" => $TCA["tx_grupptillh_student"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,fnamn,enamn,epost,telefon"
	),
	"feInterface" => $TCA["tx_grupptillh_student"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fnamn" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_student.fnamn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"enamn" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_student.enamn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"epost" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_student.epost",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"telefon" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_student.telefon",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, fnamn, enamn, epost, telefon")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);



$TCA["tx_grupptillh_moment"] = Array (
	"ctrl" => $TCA["tx_grupptillh_moment"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,kid,title,grouptype"
	),
	"feInterface" => $TCA["tx_grupptillh_moment"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"kid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment.kid",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_grupptillh_kurs",	
				"foreign_table_where" => "ORDER BY tx_grupptillh_kurs.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"grouptype" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment.grouptype",		
			"config" => Array (
				"type" => "radio",
				"items" => Array (
					Array("LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment.grouptype.I.0", "0"),
					Array("LLL:EXT:grupptillh/locallang_db.xml:tx_grupptillh_moment.grouptype.I.1", "1"),
				),
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, kid, title;;;;2-2-2, grouptype;;;;3-3-3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>