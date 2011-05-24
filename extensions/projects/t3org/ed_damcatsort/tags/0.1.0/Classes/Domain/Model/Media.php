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

class Tx_EdDamcatsort_Domain_Model_Media extends Tx_Extbase_DomainObject_AbstractEntity {
	
	/**
	 * category
	 * @var Tx_EdDamcatsort_Domain_Model_DamCategory
	 */
	protected $category;
	
	/**
	 * dam
	 * @var Tx_EdDamcatsort_Domain_Model_Dam
	 */
	protected $dam;

	/**
	 * Setter for category
	 *
	 * @param Tx_EdDamcatsort_Domain_Model_DamCategory
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}
	
	/**
	 * Getter for category
	 *
	 * @return Tx_EdDamcatsort_Domain_Model_DamCategory
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Setter for dam
	 *
	 * @param Tx_EdDamcatsort_Domain_Model_Dam
	 * @return void
	 */
	public function setDam($dam) {
		$this->dam = $dam;
	}
	
	/**
	 * Getter for dam
	 *
	 * @return Tx_EdDamcatsort_Domain_Model_Dam
	 */
	public function getDam() {
		return $this->dam;
	}
}

?>