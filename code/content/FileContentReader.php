<?php

/**
 * Content reader to read content from the filesystem 
 * 
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContentReader extends ContentReader {

	public function getURL() {
		$path = FileContentWriter::$base_path;
		if ($path{0} == '/') {
			$path = str_replace(Director::baseFolder(), '', $path);
		}
		return Director::absoluteBaseURL() . $path . '/' . $this->getId();
	}
	
	/**
	 * Read content back to the user
	 *
	 * @return string
	 */
	public function read() {
		$base = FileContentWriter::$base_path;
		$path = '';
		$id = $this->getId();
		
		if ($id{0} == '/') {
			$path = $id;
		} else {
			if ($base{0} == '/') {
				$path = $base . '/' . $this->getId();
			} else {
				$path = Director::baseFolder() . '/' . $base . '/' . $this->getId();
			}
		}
		
		if (!is_readable($path)) {
			throw new Exception("Expected path $path is not readable");
		}

		return file_get_contents($path);
	}
}
