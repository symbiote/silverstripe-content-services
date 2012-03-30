<?php

/**
 * A class for reading content from a source
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
abstract class ContentReader {
	
	/**
	 * The ID for the wrapped content item
	 *
	 * @var mixed
	 */
	protected $id;
	
	public function __construct($id) {
		$this->id = $id;
	}

	/**
	 * Returns a content writer wrapped around the same raw item 
	 */
	public function getWriter() {
		return singleton('ContentService')->getWriter($this->getContentId());
	}
	
	/**
	 * Can content be read from here? 
	 */
	public function isReadable() {
		return !is_null($this->id);
	}
	
	/**
	 * Gets the underlying id if this item
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Return a unique way of identifying this content reader type
	 */
	public function getIdentifier() {
		return str_replace('ContentReader', '', get_class($this));
	}
	
	/**
	 * Get content identifier 
	 */
	public function getContentId() {
		return $this->getIdentifier() . ContentService::SEPARATOR . $this->id;
	}
	
	
	/**
	 * Get a url to this piece of content
	 * 
	 * @return string
	 */
	public abstract function getURL();

	/**
	 * Read this content as a string
	 * 
	 * @return string
	 */
	public abstract function read();
	
}
