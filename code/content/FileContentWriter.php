<?php

/**
 * A content writer that writes data to disk
 *
 * @author marcus@symbiote.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContentWriter extends ContentWriter {
	
	/** 
	 * Where should file assets be written to initially? 
	 * 
	 * @var string
	 */
	public $basePath = 'content';

	public function nameToId($fullname) {
		$name = basename($fullname);
		$idPath = md5(dirname($fullname));
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

		return $this->getFilesystemName();
	}
	
	protected function getFilesystemName() {
		if (!$this->id) {
			throw new Exception("Cannot find filesystem location for null ID");
		}
		
		if ($this->id{0} == '/') {
			return $this->id;
		}
		// SS specific bit here
		if ($this->basePath{0} == '/') {
			$path = $this->basePath . '/' . $this->id; 
		} else {
			$path = Director::baseFolder() . '/' . $this->basePath . '/' . $this->id; 
		}
		return $path;
	}

	public function delete() {
		$id = $this->getId();
		if (!$id) {
			return;
		}
		unlink($this->getFilesystemName());
	}
}
