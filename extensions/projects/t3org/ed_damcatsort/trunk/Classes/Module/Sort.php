<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nikola Stojiljkovic <nikola.stojiljkovic(at)essentialdots.com>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

$LANG->includeLLFile('EXT:lang/locallang_mod_web_list.xml');

require_once(PATH_txdam.'lib/class.tx_dam_listrecords.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db_lang_ovl.php');

class Tx_EdDamcatsort_Module_Sort extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 *
	 * @return	array
	 */
	function modMenu() {
		global $LANG;

		return array(
			'tx_dam_list_list_showThumb' => '',
			'tx_dam_list_list_showAlternateBgColors' => '',
			'tx_dam_list_list_sortField' => '',
			'tx_dam_list_list_sortRev' => '',
			'tx_dam_list_displayFields' => '',
			'tx_dam_list_langSelector' => '',
		);
	}

	/**
	 * Initialize the class and set some HTML header code
	 *
	 * @return	void
	 */
	function head()	{
		global $LANG;

			// Init gui items and ...
		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');

		$this->pObj->guiItems->registerFunc('getOptions', 'footer');

		$this->pObj->addOption('funcCheck', 'tx_dam_list_list_showThumb', $LANG->getLL('showThumbnails'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_list_showAlternateBgColors', $LANG->getLL('showAlternateBgColors'));
	}

	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main() {
		global $BE_USER, $LANG, $BACK_PATH, $TCA;

		$content = '';

		$table = 'tx_dam';
		t3lib_div::loadTCA($table);

			// show record of the wanted language only - no overlays
		$langQuery = '';
		if ($lang = intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector'])) {
			$lgOvlFields = tx_dam_db::getLanguageOverlayFields ('tx_dam', 'tx_dam_lgovl');
	
			$languageField = $TCA[$table]['ctrl']['languageField'];
			$transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];
	
			$this->pObj->selection->setSelectionLanguage($lang);
	
			$this->pObj->selection->qg->query['FROM']['tx_dam as tx_dam_lgovl'] = implode(', ', $lgOvlFields).', tx_dam.uid as __uid';
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl_selfjoin'] = 'AND tx_dam.uid=tx_dam_lgovl.'.$transOrigPointerField;
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.'.$languageField] = 'AND tx_dam_lgovl.'.$languageField.'='.$lang;
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.deleted'] = 'AND tx_dam_lgovl.deleted=0';
		}

			// Update tx_eddamcatsort_media with new files
		$sql = "INSERT INTO tx_eddamcatsort_media (pid, category, dam, sorting) 
		        SELECT d.pid, c.uid as category, d.uid as dam, 0-d.uid
		          FROM tx_dam_mm_cat mm
		               INNER JOIN tx_dam d ON (d.uid = mm.uid_local)
		               INNER JOIN tx_dam_cat c ON (c.uid = mm.uid_foreign)
		               LEFT JOIN tx_eddamcatsort_media m ON (mm.uid_local = m.dam AND mm.uid_foreign = m.category)
		         WHERE m.uid IS NULL";
		
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $sql);

			// Use the current selection to create a query and count selected records
		$this->pObj->selection->addSelectionToQuery();

		$selection = $this->pObj->selection->sl->sel;

		if (count($selection)!=1 ||
		    count($selection['SELECT'])!=1 || 
		    count($selection['SELECT']['txdamCat'])!=1) {
			
		    return $LANG->sL('LLL:EXT:ed_damcatsort/Resources/Private/Language/locallang_db.xml:tx_dam_list_list.noCatSelected',1); 
		}

		foreach ($selection['SELECT']['txdamCat'] as $key => $value) {
			if ($value==1) {
				$category = $key;
			}
		}

		$this->pObj->selection->qg->query['SELECT']['tx_eddamcatsort_media'] = 'tx_eddamcatsort_media.uid as media_uid, tx_eddamcatsort_media.category';
		$this->pObj->selection->qg->query['LEFT_JOIN']['tx_eddamcatsort_media'] = 'tx_eddamcatsort_media.dam = tx_dam.uid';
		$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_eddamcatsort_media'] = 'AND tx_eddamcatsort_media.category = '.$category.' AND tx_eddamcatsort_media.dam IS NOT NULL AND !tx_eddamcatsort_media.deleted AND !tx_eddamcatsort_media.hidden';

			// Use the current selection to create a query and count selected records
		$this->pObj->selection->execSelectionQuery(TRUE);

			// output header: info bar, result browser, ....
		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);

		if($this->pObj->selection->pointer->countTotal) {
				// init db list object
			$dblist = t3lib_div::makeInstance('tx_dam_listrecords');
			$dblist->setParameterName('form', $this->pObj->formName);
			$dblist->init($table);
			
			$dblist->setActionsEnv(array(
				'currentLanguage' => $langCurrent,
				'allowedLanguages' => $langRows,
			));

				// process multi action if needed
			if ($processAction = $dblist->getMultiActionCommand()) {
				if ($processAction['onItems'] === '_all') {
					$uidList = array();
					$res = $this->pObj->selection->execSelectionQuery();

					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$uidList[] = $row['uid'];
					}
					$itemList = implode(',', $uidList);
				} else {
					$itemList = $processAction['onItems'];
					$uidList = t3lib_div::trimExplode(',', $itemList, true);
				}

				if ($uidList) {
					switch ($processAction['actionType']) {
						case 'url':
							$url = str_replace('###ITEMLIST###', $itemList, $processAction['action']);
							header('Location: '.$url);
							exit;
						break;
						case 'tce-data':
							$params = '';
							foreach ($uidList as $uid) {
								$params .= str_replace('###UID###', $uid, $processAction['action']);
							}
							$url = $GLOBALS['SOBE']->doc->issueCommand($params, -1);


							$url = $BACK_PATH.'tce_db.php?&redirect='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).'&vC='.$BE_USER->veriCode().'&prErr=1&uPT=1'.$params;

							header('Location: '.$url);
							exit;
						break;
					}
				}
			}

			t3lib_div::loadTCA($table);		

				// set fields to display
			$titleColumn = $TCA[$table]['ctrl']['label'];

			$allFields = tx_dam_db::getFieldListForUser($table);

			$selectedFields = t3lib_div::_GP('tx_dam_list_list_displayFields');
			$selectedFields = is_array($selectedFields) ? $selectedFields : explode(',', $this->pObj->MOD_SETTINGS['tx_dam_list_list_displayFields']);
	
				// remove fields that can not be selected
			if (is_array($selectedFields)) {
				$selectedFields = array_intersect($allFields, $selectedFields);
				$selectedFields = array_merge(array($titleColumn), $selectedFields);
			} else {
				$selectedFields = array();
				$selectedFields[] = $titleColumn;
			}

				// store field list
			$this->pObj->MOD_SETTINGS['tx_dam_list_list_displayFields'] = implode(',', $selectedFields);
			$GLOBALS['BE_USER']->pushModuleData($this->pObj->MCONF['name'], $this->pObj->MOD_SETTINGS);
			

				// set query and sorting
			$this->pObj->selection->qg->query['ORDERBY']['tx_dam'] = ' tx_eddamcatsort_media.sorting ';

				// exec query
			$this->pObj->selection->addLimitToQuery();
			$res = $this->pObj->selection->execSelectionQuery();

				// init iterator for query
			$conf = array(	
				'table' => 'tx_dam',
				'countTotal' => $this->pObj->selection->pointer->countTotal	
			);
			
			if ($langCurrent>0 && $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']!=='exclusive') {
				$dbIterator = new tx_dam_iterator_db_lang_ovl($res, $conf);
				$dbIterator->initLanguageOverlay($table, $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
			} else {
				$dbIterator = new tx_dam_iterator_db($res, $conf);
			}
						
				// make db list
			$dblist->setDataObject($dbIterator);

				// add columns to list
			$dblist->clearColumns();
			$cc = 0;
			foreach ($selectedFields as $field) {
				$fieldLabel = is_array($TCA[$table]['columns'][$field]) ? preg_replace('#:$#', '', $LANG->sL($TCA[$table]['columns'][$field]['label'])) : '['.$field.']';
				$dblist->addColumn($field, $fieldLabel);
				$cc++;
				if($cc == 1) {
						// add control at second column
					$dblist->addColumn('_CONTROL_', '');
					$cc++;
				}
			}

			$dblist->showActions = true;

				// Enable/disable display of thumbnails
			$dblist->showThumbs = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showThumb'];
				// Enable/disable display of AlternateBgColors
			$dblist->showAlternateBgColors = $this->pObj->config_checkValueEnabled('alternateBgColors', true);

			$dblist->setPointer($this->pObj->selection->pointer);
			$dblist->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev']);
			$dblist->setParameterName('sortField', 'SET[tx_dam_list_list_sortField]');
			$dblist->setParameterName('sortRev', 'SET[tx_dam_list_list_sortRev]');

			$this->pObj->doc->JScodeArray['dblist-JsCode'] = $dblist->getJsCode();

				// get all avalibale languages for the page
			if ($languageSwitch = $this->pObj->languageSwitch($langRows, intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']))) {
				$this->pObj->markers['LANGUAGE_SELECT'] = '<div class="languageSwitch">'.$languageSwitch.'</div>';
			} else {
				$this->pObj->markers['LANGUAGE_SELECT'] = '';
			}
			$content.= $dblist->getListTable();

			$fieldSelectBoxContent = $this->fieldSelectBox($table, $allFields, $selectedFields);
			$content.= $this->pObj->buttonToggleDisplay('fieldselector', $LANG->getLL('field_selector'), $fieldSelectBoxContent);
		} else {
				// no search result: showing selection box
			if ($languageSwitch = $this->pObj->languageSwitch($langRows, intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']))) {
				$this->pObj->markers['LANGUAGE_SELECT'] = '<div class="languageSwitch">'.$languageSwitch.'</div>';
			} else {
				$this->pObj->markers['LANGUAGE_SELECT'] = '';
			}			
		}

		return $content;
	}





	/********************************
	 *
	 * selector for fields to display
	 *
	 ********************************/


	/**
	 * Create the selector box for selecting fields to display from a table:
	 *
	 * @param	string		Table name
	 * @param	array		all fields
	 * @param	array		selected fields
	 * @param	boolean		If true, form-fields will be wrapped around
	 * @return	string		HTML table with the selector box (name: displayFields['.$table.'][])
	 */
	function fieldSelectBox($table, $allFields, $selectedFields, $formFields = true) {
		global $TCA, $LANG;

		t3lib_div::loadTCA($table);

		$formElements = array('', '');
		if ($formFields) {
			$formElements = array('<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">', '</form>');
		}

			// Create an option for each field:
		$opt = array();
		$opt[] = '<option value=""></option>';
		foreach ($allFields as $fN) {
				// Field label
			$fL = is_array($TCA[$table]['columns'][$fN]) ? preg_replace('/:$/', '', $LANG->sL($TCA[$table]['columns'][$fN]['label'])) : '['.$fN.']';
			$opt[] = '<option value="'.$fN.'"'. (in_array($fN, $selectedFields) ? ' selected="selected"' : '').'>'.htmlspecialchars($fL).'</option>';
		}

			// Compile the options into a multiple selector box:
		$lMenu = '<select size="'.t3lib_div::intInRange(count($allFields) + 1, 3, 8).'" multiple="multiple" name="tx_dam_list_displayFields[]">'.implode('', $opt).'</select>';

			// Table with the select box:
		$content .= $formElements[0].'
		            <table border="0" cellpadding="0" cellspacing="0" class="bgColor4" id="typo3-dblist-fieldSelect">
		            	<tr>
		            		<td>'.$lMenu.'</td>
		            		<td><input type="Submit" name="search" value="&gt;&gt;"></td>
		            	</tr>
		            </table>
		            '.$formElements[1];
		
		return $content;
	}


	/**
	 * Makes the list of fields the user can select/view for a table
	 *
	 * @param	string		Table name
	 * @param	boolean		If set, users access to the field (non-exclude-fields) is NOT checked.
	 * @param	boolean		$useExludeFieldList: ...
	 * @return	array		Array, where values are fieldnames to include in query
	 */
	function makeAllFieldList($table, $dontCheckUser = false, $useExludeFieldList = true) {
		global $TCA, $BE_USER;

			// Init fieldlist array:
		$fieldListArr = array();

			// Check table:
		if (is_array($TCA[$table])) {
			t3lib_div::loadTCA($table);

			$exludeFieldList = t3lib_div::trimExplode(',', $TCA[$table]['interface']['excludeFieldList'],1);

				// Traverse configured columns and add them to field array, if available for user.
			foreach ($TCA[$table]['columns'] as $fN => $fieldValue) {
				if (($dontCheckUser || ((!$fieldValue['exclude'] || $BE_USER->check('non_exclude_fields', $table.':'.$fN)) && $fieldValue['config']['type'] != 'passthrough')) AND (!$useExludeFieldList || !in_array($fN, $exludeFieldList))) {
					$fieldListArr[$fN] = $fN;
				}
			}

				// Add special fields:
			if ($dontCheckUser || $BE_USER->isAdmin()) {
				$fieldListArr['uid'] = 'uid';
				$fieldListArr['pid'] = 'pid';
				if ($TCA[$table]['ctrl']['tstamp'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['crdate'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['cruser_id'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['cruser_id'];
				if ($TCA[$table]['ctrl']['sortby'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['sortby'];
				if ($TCA[$table]['ctrl']['versioning'])
					$fieldListArr['t3ver_id'] = 't3ver_id';
			}
		}
		
			// doesn't make sense, does it?
		unset ($fieldListArr['l18n_parent']);
		unset ($fieldListArr['l18n_diffsource']);

		return $fieldListArr;
	}

	/**
	 * Make selector box for creating new translation for a record or switching to edit the record in an existing language.
	 * Displays only languages which are available for the current page.
	 *
	 * @param	integer		pid of the record
	 * @param	integer		uid of the current language
	 * @param	boolean		If true, form-fields will be wrapped around
	 * @return	string		<select> HTML element (if there were items for the box anyways...)
	 */
	function languageSwitch($pid, $currentLanguage, $formFields = true) {
		$content = '';

			// get all avalibale languages for the page
		$langRows = $this->getLanguages($pid);

			// page available in other languages than default language?
		if (is_array($langRows) && count($langRows)) {

			$langSelItems=array();
			foreach ($langRows as $lang) {
				if ($GLOBALS['BE_USER']->checkLanguageAccess($lang['uid']))	{
					$langSelItems[$lang['uid']]=$lang['title'];
				}
			}

				// If any languages are left, make selector:
			if (count($langSelItems)>1)		{
				$content .= $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_general.xml:LGL.language',1).' ';
				$content .= t3lib_befunc::getFuncMenu('', 'SET[tx_dam_list_langSelector]', $currentLanguage, $langSelItems);
				if ($formFields) {
					$content = '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">'.$content.'</form>';
				}
			}
		}
		return $content;
	}

	/**
	 * Returns sys_language records.
	 *
	 * @param	integer		Page id: If zero, the query will select all sys_language records from root level which are NOT hidden. If set to another value, the query will select all sys_language records that has a pages_language_overlay record on that page (and is not hidden, unless you are admin user)
	 * @return	array		Language records including faked record for default language
	 */
	function getLanguages($id)	{
		global $LANG;

		$modSharedTSconfig = t3lib_BEfunc::getModTSconfig($id, 'mod.SHARED');

		$languages = array(
			0 => array(
				'uid' => 0,
				'pid' => 0,
				'hidden' => 0,
				'title' => strlen($modSharedTSconfig['properties']['defaultLanguageLabel']) ? $modSharedTSconfig['properties']['defaultLanguageLabel'].' ('.$GLOBALS['LANG']->sl('LLL:EXT:lang/locallang_mod_web_list.xml:defaultLanguage').')' : $GLOBALS['LANG']->sl('LLL:EXT:lang/locallang_mod_web_list.xml:defaultLanguage'),
				'flag' => $modSharedTSconfig['properties']['defaultLanguageFlag'],
			)
		);

		$exQ = $GLOBALS['BE_USER']->isAdmin() ? '' : ' AND sys_language.hidden=0';
		if ($id)	{
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'sys_language.*',
							'pages_language_overlay,sys_language',
							'pages_language_overlay.sys_language_uid=sys_language.uid AND pages_language_overlay.pid='.intval($id).$exQ,
							'pages_language_overlay.sys_language_uid,sys_language.uid,sys_language.pid,sys_language.tstamp,sys_language.hidden,sys_language.title,sys_language.static_lang_isocode,sys_language.flag',
							'sys_language.title'
						);
		} else {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'sys_language.*',
							'sys_language',
							'sys_language.hidden=0',
							'',
							'sys_language.title'
						);
		}
		if ($rows) {
			foreach ($rows as $row) {
				$languages[$row['uid']] = $row;
			}
		}
		return $languages;
	}
}

?>