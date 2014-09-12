<?php

/**
 * A class for reading content from a source
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
abstract class ContentReader extends ReaderWriterBase {

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
	 * Is this listable? If so, the list() method must return an array of ContentReader items
	 * that are the 'listed' items from this content reader
	 */
	public function isListable() {
		return false;
	}
	
	/**
	 * List 'child' items of this ContentReader 
	 */
	public function getList() {
		return array();
	}

	/**
	 * Does the file exist or not?
	 */
	abstract public function exists();
	
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
	
	/**
	 * Return metadata about this file
	 */
	public function getInfo() {
		return array();
	}
	
}
