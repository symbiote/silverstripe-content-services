<?php

/**
 * Content reader to read content from the filesystem 
 * 
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContentReader extends ContentReader {

	/** 
	 * Where should file assets be written to initially? 
	 * 
	 * @var string
	 */
	public $basePath = 'content';
	
	public function getURL() {
		$path = $this->basePath;
		
		if ($path{0} == '/') {
			$path = str_replace(Director::baseFolder(), '', $path);
		}
		return Director::absoluteBaseURL() . $path . '/' . $this->getId();
	}
	
	public function isReadable() {
		$path = $this->getPath($this->getId());
		return is_readable($path);
	}
	
	/**
	 * Read content back to the user
	 *
	 * @return string
	 */
	public function read() {
		
		$id = $this->getId();
		$path = $this->getPath($id);
		if (!is_readable($path)) {
			throw new Exception("Expected path $path is not readable");
		}

		return file_get_contents($path);
	}
	
	protected function getPath($id) {
		$base = $this->basePath;
		if ($id{0} == '/') {
			$path = $id;
		} else {
			if ($base{0} == '/') {
				$path = $base . '/' . $id;
			} else {
				$path = Director::baseFolder() . '/' . $base . '/' . $id;
			}
		}
		return $path;
	}
}
