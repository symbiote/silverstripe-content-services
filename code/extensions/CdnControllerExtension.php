<?php

/**
 * 
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CdnControllerExtension extends Extension {

	static $store_type = 'File';

	public function requireCDN($type, $assetPath) {
		// return the cdn URL for the given asset
	}
	
	public function CDNPath($assetPath) {
		if (Director::isLive()) {
			$reader = singleton('ContentService')->findReaderFor(self::$store_type, $assetPath);
			if ($reader && $reader->isReadable()) {
				return $reader->getURL();
			}

			// otherwise, we need to write the file
			$writer->write(Director::baseFolder().'/'.$assetPath, $assetPath);

			return $writer->getReader()->getURL();
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
