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

require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');

class Tx_EdDamcatsort_Component_Action_Down extends tx_dam_actionbase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');

	/**
	 * If set the action is for itmes with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = true;
	
	var $moveId = 0;

	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] == 'record'); # AND ($this->itemInfo['__table'] == 'tx_dam');
		}
		return $valid;
	}

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] == 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
			 	$valid = ($this->env['permsEdit'] AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']);
			}
			
			if (!$this->itemInfo['media_uid'])
			{
				$valid = false;
			}
			else
			{
				$sql = "SELECT t1.uid
									FROM tx_eddamcatsort_media t1, tx_eddamcatsort_media t2, tx_dam d1, tx_dam d2
								WHERE not t1.deleted and
								      not t2.deleted and 
								      not d1.deleted and
								      not d2.deleted and
								      t1.dam = d1.uid and
								      t2.dam = d2.uid and
								      t1.pid = t2.pid and
								      t1.sorting > t2.sorting and
								      t1.category = ".$this->itemInfo['category']." and 
								      t2.category = ".$this->itemInfo['category']." and 
								      t2.uid = ".$this->itemInfo['media_uid']."
						ORDER BY t1.sorting asc
								LIMIT 0,1";		
	
				$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $sql);
				
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
				{
					$this->moveId = 0-intval($row['uid']);
				}
				else
				{
					$valid = false;
					$this->moveId = 0;
				}
			}
		}
		return $valid;
	}

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		$iconFile = 'gfx/button_down.gif';

		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="11" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		if (!$this->itemInfo['media_uid'] || $this->moveId==0)
		{
			$icon = '<img width="16" height="16" alt="" src="clear.gif"/>';
		}

		return $icon;
	}

	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:moveDown');
	}

	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$params='&cmd[tx_eddamcatsort_media]['.$this->itemInfo['media_uid'].'][move]='.$this->moveId;
		
		$onClick = 'return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');';

		$commands['href'] = '#';
		$commands['onclick'] = $onClick;

		return $commands;
	}
}

?>