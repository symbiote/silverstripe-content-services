<?php

/**
 * A content writer that writes data to disk
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContentWriter extends ContentWriter {
	
	public static $base_path = 'content';
	
	/** 
	 * Where should file assets be written to initially? 
	 * 
	 * @var string
	 */
	public $basePath = 'content';
	
	public function nameToId($fullname) {
		$name = basename($fullname);
		$idPath = md5($fullname);
		$first = substr($idPath, 0, 3);
		$second = substr($idPath, 3, 29);
		return "$first/$second/$name";
	}

	public function write($content = null, $name = '') {
		$docopy = false;
		$reader = $this->getReaderWrapper($content);
		// this call will set $this->id so subsequent references to this will work
		$target = $this->getTarget($name);
		// SS specific
		Filesystem::makeFolder(dirname($target));
		if ($docopy) {
			@copy($content, $target);
		} else {
			file_put_contents($target, $reader->read());
		}
	}

	protected function getTarget($fullname) {
		// if we've got an ID, it means we're doing an overwrite, and in that case
		// the path is encoded in the ID
		if (!$this->id) {
			// set our ID
			$this->id = $this->nameToId($fullname);
		}

		if (!strlen($fullname)) {
			throw new Exception("Cannot write unnamed file data. Make sure to call write() with a filename");
		}

		// SS specific bit here
		if ($this->basePath{0} == '/') {
			$path = $this->basePath . '/' . $this->id; 
		} else {
			$path = Director::baseFolder() . '/' . $this->basePath . '/' . $this->id; 
		}

		return $path;
	}
}
