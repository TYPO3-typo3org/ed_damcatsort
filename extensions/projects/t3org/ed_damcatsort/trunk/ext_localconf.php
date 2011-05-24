<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:ed_damcatsort/Classes/Hook/ProcessDatamap.php:&Tx_EdDamcatsort_Hook_ProcessDatamap';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamap_preProcessFieldArray'][] = 'EXT:ed_damcatsort/Classes/Hook/ProcessDatamap.php:&Tx_EdDamcatsort_Hook_ProcessDatamap';

	// force making of persistent object (fix for the tx_ -> Tx_ naming convention)
$tempObj = t3lib_div::getUserObj('EXT:ed_damcatsort/Classes/Hook/ProcessDatamap.php:&Tx_EdDamcatsort_Hook_ProcessDatamap', $checkPrefix = FALSE);

if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}

	// PHP5 compatiblity
if (!function_exists('stripos')) {
	require_once(PATH_txdam.'compat/stripos.php');
}
if (!function_exists('str_ireplace')) {
	require_once(PATH_txdam.'compat/str_ireplace.php');
}

	// base DAM api
require_once(PATH_txdam.'lib/class.tx_dam.php');

?>