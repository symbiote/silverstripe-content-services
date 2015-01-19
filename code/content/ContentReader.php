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
	 * 
	 * @return ContentWriter
	 */
	public function getWriter() {
		return singleton('ContentService')->getWriter($this->getContentId());
	}
	
	/**
	 * Can content be read from the item pointed at by the content ID this object wraps around?
	 * 
	 * @return boolean
	 */
	public function isReadable() {
		return !is_null($this->id);
	}
	
	/**
	 * Is this listable? If so, the getList() method must return an array of ContentReader items
	 * that are the 'listed' items from this content reader. 
	 * 
	 * @return boolean
	 */
	public function isListable() {
		return false;
	}
	
	/**
	 * Returns a list (array) of 'child' items of this ContentReader. The 
	 * items contained in the listing are ContentReader items
	 * 
	 * @return array
	 */
	public function getList() {
		return array();
	}

	/**
	 * Does the item exist or not?
	 * 
	 * @returns boolean
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
	 * Return metadata about this file, if applicable. 
	 * 
	 * For now, this is expected to be implementation dependent, there may
	 * be a specific wrapper object for this data at a later point. 
	 * 
	 * @return array()
	 */
	public function getInfo() {
		return array();
	}
	
}
