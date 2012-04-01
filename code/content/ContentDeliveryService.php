<?php

/**
 * Provides an interface to content delivery networks
 * 
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class ContentDeliveryService {
	
	/**
	 * A the type of content store to use
	 * 
	 * @var string
	 */
	protected $storeIn = 'File';
	
	/**
	 *
	 * @var ContentService
	 */
	protected $contentService;
	
	public function __construct() {
		$svc = singleton('ContentService');
		/* @var ContentService $svc */
		$this->contentService = $svc;
	}
	
	public function setStoreIn($v) {
		$this->storeIn = $v;
	}
	
	/**
	 * Store the contents of a folder on a CDN. 
	 * 
	 * If processReferences is set, relative URL references are attempted to be 
	 * detected and stored remotely as well, with the file to be stored rewritten 
	 * to refer to the CDN value. This really is only useful for CSS 
	 *
	 * @param string $folder
	 * @param boolean $processReferences 
	 */
	public function storeThemeFile($file, $forceUpdate = false, $processReferences = false) {
		$relativeName = trim(str_replace(Director::baseFolder(), '', $file), '/');
		
		if (!$forceUpdate) {
			// see if the file already exists, if not we do NOT do an update
			$reader = $this->contentService->findReaderFor($this->storeIn, $relativeName);
			if ($reader) {
				return;
			}
		}

		$clear = false;
		if ($processReferences) {
			$clear = true;
			$file = $this->processFileReferences($file);
		}

		// otherwise, lets get a content writer
		$writer = $this->contentService->getWriter($this->storeIn);
		$writer->write($file, $relativeName);
		if ($clear) {
			@unlink($file);
		}

		$id = $writer->getContentId();
		return $writer->getReader()->getURL();
	}
	
	protected function processFileReferences($file, $forceUpdate = false) {
		$content = file_get_contents($file);
		
		$processed = array();
		
		if (preg_match_all('/url\((.*?)\)/', $content, $matches)) {
			foreach ($matches[1] as $segment) {
				$segment = trim($segment, '\'"');
				
				if (strpos($segment, '#') !== false) {
					$segment = substr($segment, 0, strpos($segment, '#'));
				}
				
				if (isset($processed[$segment])) {
					continue;
				}

				if (strpos($segment, '//') !== false  || $segment{0} == '/') {
					continue;
				}

				$realPath = realpath(dirname($file) .'/' . $segment);
				if (!strlen($realPath) || !file_exists($realPath)) {
					continue;
				}

				$replacement = $this->storeThemeFile($realPath, $forceUpdate);
				
				$content = str_replace($segment, $replacement, $content);
				$processed[$segment] = $replacement;
			}
		}
		
		if (count($processed)) {
			// we need to upload a temp version of the file
			$file = $file . '.cdn';
			file_put_contents($file, $content);
		}
		
		return $file;
	}
}
