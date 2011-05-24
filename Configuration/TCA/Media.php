<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_eddamcatsort_media"] = Array (
	"ctrl" => $TCA["tx_eddamcatsort_media"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,category,dam"
	),
	"feInterface" => $TCA["tx_eddamcatsort_media"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"dam" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:ed_damcatsort/Resources/Private/Language/locallang_db.xml:tx_eddamcatsort_media.dam",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_dam",	
				"foreign_table_where" => "ORDER BY tx_dam.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"category" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:ed_damcatsort/Resources/Private/Language/locallang_db.xml:tx_eddamcatsort_media.category",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_dam_cat",	
				"foreign_table_where" => "ORDER BY tx_dam_cat.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, category, dam")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

?>