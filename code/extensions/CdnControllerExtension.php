<?php

/**
 * 
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CdnControllerExtension extends Extension {

	static $store_type = 'File';

	public function requireCDN($assetPath, $uploadMissing = false) {
		// return the cdn URL for the given asset
		$type = strpos($assetPath, '.css')  ? 'css' : 'js';
		switch ($type) {
			case 'css': 
				Requirements::css($this->CDNPath($assetPath, $uploadMissing));
				break;
			case 'js': 
				Requirements::javascript($this->CDNPath($assetPath, $uploadMissing));
				break;
		}
		
	}
	
	public function CDNPath($assetPath, $uploadMissing = false) {
		if (Director::isLive()) {
			$reader = singleton('ContentService')->findReaderFor(self::$store_type, $assetPath);
			if ($reader && $reader->isReadable()) {
				return $reader->getURL();
			}

			if ($uploadMissing) {
				if (strpos($assetPath, '.css')) {
					// if we're a relative path, make absolute
					$fullPath = $assetPath;
					if ($assetPath{0} != '/') {
						$fullPath = Director::baseFolder().'/' . $assetPath;
					}
					if (!file_exists($fullPath)) {
						return $assetPath;
					}
					// upload all references too
					return singleton('ContentDeliveryService')->storeThemeFile($fullPath, false, true);
				}

				// otherwise just upload
				$writer = $this->getWriter();
				// otherwise, we need to write the file
				$writer->write(Director::baseFolder().'/'.$assetPath, $assetPath);

				return $writer->getReader()->getURL();
			}
		}
		return $assetPath;
	}

	/**
	 * @return ContentWriter
	 */
	protected function getWriter() {
		$writer = singleton('ContentService')->getWriter(self::$store_type);
		if (!$writer) {
			throw new Exception("Invalid writer type " . self::$store_type);
		}
		return $writer;
	}
}
