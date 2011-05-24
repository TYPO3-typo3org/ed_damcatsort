<?php

########################################################################
# Extension Manager/Repository config file for ext "ed_damcatsort".
#
# Auto generated 23-05-2011 22:30
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Essential Dots DAM category sort',
	'description' => 'Adds ability to sort media records in categories',
	'category' => 'module',
	'author' => 'Nikola Stojiljkovic',
	'author_email' => 'nikola.stojiljkovic@essentialdots.com',
	'shy' => '',
	'dependencies' => 'dam',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'dam' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:22:{s:9:"ChangeLog";s:4:"4e53";s:10:"README.txt";s:4:"ee2d";s:16:"ext_autoload.php";s:4:"04c5";s:12:"ext_icon.gif";s:4:"b280";s:17:"ext_localconf.php";s:4:"1c32";s:14:"ext_tables.php";s:4:"eb3f";s:14:"ext_tables.sql";s:4:"edbc";s:33:"Classes/Component/Action/Down.php";s:4:"9459";s:31:"Classes/Component/Action/Up.php";s:4:"c27c";s:28:"Classes/Domain/Model/Dam.php";s:4:"66c1";s:36:"Classes/Domain/Model/DamCategory.php";s:4:"2fb5";s:30:"Classes/Domain/Model/Media.php";s:4:"f15d";s:51:"Classes/Domain/Repository/DamCategoryRepository.php";s:4:"5a42";s:43:"Classes/Domain/Repository/DamRepository.php";s:4:"1076";s:45:"Classes/Domain/Repository/MediaRepository.php";s:4:"9bb6";s:31:"Classes/Hook/ProcessDatamap.php";s:4:"a90b";s:23:"Classes/Module/Sort.php";s:4:"92f0";s:27:"Configuration/TCA/Media.php";s:4:"5893";s:34:"Configuration/TypoScript/setup.txt";s:4:"e3e2";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"ff37";s:38:"Resources/Public/Icons/record_icon.gif";s:4:"ea75";s:41:"Resources/Public/Icons/record_icon__x.gif";s:4:"5106";}',
	'suggests' => array(
	),
);

?>