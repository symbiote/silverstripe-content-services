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
	
	/**
	 * The base URL to prefix things with
	 *
	 * @var string
	 */
	public $baseUrl = null;
	
	public function getURL() {
		
		return $this->getBaseUrl() . '/' /*. $path . '/' */ . $this->getId();
	}
	
	public function getBaseUrl() {
		// if none configured, use a URL created from the SS base url
		if (!$this->baseUrl) {
			$path = $this->basePath;
		
			if ($path{0} == '/') {
				$path = str_replace(Director::baseFolder(), '', $path);
			}
			
			$this->baseUrl = Director::absoluteBaseURL() . $path;
		}
		return rtrim($this->baseUrl, '/');
	}
	
	public function isReadable() {
		$path = $this->getPath($this->getId());
		return is_readable($path);
	}
	
		/**
	 * An S3 object is listable if its content type is a directory
	 * 
	 * @return boolean
	 */
	public function isListable() {
		$path = $this->getPath($this->getId());
		
		return is_dir($path);
	}
	
	public function getList() {
		$list = array();

		if ($this->isListable()) {
			$path = $this->getPath($this->getId());
			$files = glob($path . '/*');
			foreach ($files as $file) {
				$contentId = $this->getSourceIdentifier() . ContentService::SEPARATOR  . $file;
				$reader = singleton('ContentService')->getReader($contentId);
				$list[] = $reader;
			}
		}

		return $list;
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

	public function exists() {
		$path = $this->getPath($this->getId());
		return file_exists($path);
	}
}
