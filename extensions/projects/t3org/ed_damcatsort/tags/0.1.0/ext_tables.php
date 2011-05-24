<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

tx_dam::register_action ('Tx_EdDamcatsort_Component_Action_Up', 'EXT:ed_damcatsort/Classes/Component/Action/Up.php:&Tx_EdDamcatsort_Component_Action_Up');
tx_dam::register_action ('Tx_EdDamcatsort_Component_Action_Down', 'EXT:ed_damcatsort/Classes/Component/Action/Down.php:&Tx_EdDamcatsort_Component_Action_Down');

	// force making of persistent object (fix for the tx_ -> Tx_ naming convention)
$tempObj = t3lib_div::getUserObj('EXT:ed_damcatsort/Classes/Component/Action/Up.php:&Tx_EdDamcatsort_Component_Action_Up', $checkPrefix = FALSE);
$tempObj = t3lib_div::getUserObj('EXT:ed_damcatsort/Classes/Component/Action/Down.php:&Tx_EdDamcatsort_Component_Action_Down', $checkPrefix = FALSE);

t3lib_extMgm::allowTableOnStandardPages('tx_eddamcatsort_media'); 

$TCA["tx_eddamcatsort_media"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:ed_damcatsort/Resources/Private/Language/locallang_db.xml:tx_eddamcatsort_media',		
		'label' => 'uid',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		"sortby" => "sorting",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."Configuration/TCA/Media.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."Resources/Public/Icons/record_icon.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, category, dam",
	)
);

if (TYPO3_MODE=="BE")	{

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'Tx_EdDamcatsort_Module_Sort',
		t3lib_extMgm::extPath('ed_damcatsort').'Classes/Module/Sort.php',
		'LLL:EXT:ed_damcatsort/Resources/Private/Language/locallang_db.xml:module.title'
	);
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Extbase DAM Table configuration');

?>