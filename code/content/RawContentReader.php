<?php

/**
 * Simple wrapper around raw content items
 * 
 * Use this if you're wanting to write some raw text, eg
 * 
 * $writer->write(new RawContentReader('raw text'));
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class RawContentReader extends ContentReader {
	protected $raw;
	
	public function __construct($data) {
		// set a dummy for the id
		$this->id = 1;
		$this->raw = $data;
	}
	
	public function read() {
		return $this->raw;
	}
	
	/** 
	 * Never link to raw content 
	 *
	 * @return string
	 */
	public function getURL() {
		return '';
	}
}
