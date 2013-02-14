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
