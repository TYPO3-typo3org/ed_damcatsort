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

class Tx_EdDamcatsort_Domain_Model_DamCategory extends Tx_Extbase_DomainObject_AbstractEntity {
	
	/**
	 * crdate
	 * @var DateTime
	 */
	protected $crdate;
	
	/**
	 * tstamp
	 * @var DateTime
	 */
	protected $tstamp;
	
	/**
	 * parentId
	 * @var int
	 */
	protected $parentId;
	
	/**
	 * title
	 * @var string
	 */
	protected $title;
	
	/**
	 * navTitle
	 * @var string
	 */
	protected $navTitle;
	
	/**
	 * subtitle
	 * @var string
	 */
	protected $subtitle;
	
	/**
	 * keywords
	 * @var string
	 */
	protected $keywords;
	
	/**
	 * description
	 * @var string
	 */
	protected $description;

	/**
	 * @var Tx_EdDamcatsort_Domain_Model_MediaRepository
	 */
	protected $mediaRepository;
	
	/**
	 * @var Tx_EdDamcatsort_Domain_Model_Media
	 */
	protected $_firstMedia;
	
	public function getFirstMedia() {
		if (!$this->_firstMedia) {
			$this->mediaRepository = t3lib_div::makeInstance('Tx_EdDamcatsort_Domain_Model_MediaRepository');
			$this->_firstMedia = $this->mediaRepository->findOneByCategory($this); 
		}
		return $this->_firstMedia;
	}
	
	/**
	 * Setter for crdate
	 *
	 * @param DateTime
	 * @return void
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
	}
	
	/**
	 * Getter for crdate
	 *
	 * @return DateTime
	 */
	public function getCrdate() {
		return $this->crdate;
	}
	
	/**
	 * Setter for tstamp
	 *
	 * @param DateTime
	 * @return void
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}
	
	/**
	 * Getter for tstamp
	 *
	 * @return DateTime
	 */
	public function getTstamp() {
		return $this->tstamp;
	}	

	/**
	 * Setter for parentId
	 *
	 * @param int
	 * @return void
	 */
	public function setParentId($parentId) {
		$this->parentId = $parentId;
	}
	
	/**
	 * Getter for parentId
	 *
	 * @return int
	 */
	public function getParentId() {
		return $this->parentId;
	}
	
	/**
	 * Setter for title
	 *
	 * @param string
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Getter for title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Setter for navTitle
	 *
	 * @param string
	 * @return void
	 */
	public function setNavTitle($navTitle) {
		$this->navTitle = $navTitle;
	}
	
	/**
	 * Getter for navTitle
	 *
	 * @return string
	 */
	public function getNavTitle() {
		return $this->navTitle;
	}
	
	/**
	 * Setter for subtitle
	 *
	 * @param string
	 * @return void
	 */
	public function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
	}
	
	/**
	 * Getter for subtitle
	 *
	 * @return string
	 */
	public function getSubtitle() {
		return $this->subtitle;
	}
	
	/**
	 * Setter for keywords
	 *
	 * @param string
	 * @return void
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	/**
	 * Getter for keywords
	 *
	 * @return string
	 */
	public function getKeywords() {
		return $this->keywords;
	}
	
	/**
	 * Setter for description
	 *
	 * @param string
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Getter for description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}	
}

?>