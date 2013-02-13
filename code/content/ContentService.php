<?php

/**
 * Entry point interface for accessing content related functionality
 * 
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class ContentService {
	
	const SEPARATOR = ':||';

	protected $defaultStore;
	
	protected $stores = array(
		'File' => array(
			'ContentReader'		=> 'FileContentReader',
			'ContentWriter'		=> 'FileContentWriter',
		)
	);

	public function __construct($defaultStore = 'File') {
		$this->defaultStore = $defaultStore;
	}
	
	/**
	 * Set the list of stores available
	 *
	 * @param array $store 
	 */
	public function setStores($store) {
		$this->stores = $store;
	}
	
	/**
	 * Get the list of configured store types
	 *
	 * @return array
	 */
	public function getStoreTypes() {
		return $this->stores;
	}

	/**
	 * Gets a writer for a DataObject
	 * 
	 * This is most useful for when a file has not yet been written so doesn't
	 * have a file identfier yet, and you want to write content for the given
	 * data object to a particular field
	 *
	 * @param DataObject $object
	 *				The object to get a writer for
	 * @param String $field
	 *				The field being written to 
	 * @param String $type
	 *				Explicitly state what the content store type will be
	 * @return ContentWriter
	 */
	public function getWriterFor(DataObject $object = null, $field = 'FilePointer', $type = null) {
		if ($object && $field && $object->hasField($field)) {
			$val = $object->$field;
			if (strlen($val)) {
				$reader = $this->getReader($val);
				if ($reader->isReadable()) {
					return $reader->getWriter();
				}
			}
		}
		
		if (!$type) {
			// specifically expecting to be handling File objects, but allows other 
			// objects to play too
			if ($object && $object->hasMethod('getEffectiveContentStore')) {
				$type = $object->getEffectiveContentStore();
			} else {
				$type = $this->defaultStore;
			}
		}
		
		// looks like we're getting a writer with no underlying file (as yet)
		return $this->getWriter($type);
	}

	/**
	 *
	 * @param string $identifier
	 *				Identifier in the format type://uniqueid
	 * @return ContentReader
	 */
	public function getReader($identifier) {
		return $this->createReaderWriter($identifier, 'ContentReader');
	}
	
	/**
	 * @param string $identifier
	 * @return ContentWriter
	 */
	public function getWriter($identifier) {
		return $this->createReaderWriter($identifier, 'ContentWriter');
	}
	
	/**
	 * Handles creation of a reader/writer
	 *
	 * @param type $identifier
	 * @param type $readwrite
	 * @return cls 
	 */
	protected function createReaderWriter($identifier, $readwrite) {
		$id = null;
		if (strpos($identifier, self::SEPARATOR)) {
			list($type, $id) = explode(self::SEPARATOR, $identifier);
		} else {
			$type = $identifier;
		}

		if (!$type) {
			throw new Exception("Invalid content store type $type");
		}
		
		$type = ucfirst($type);
		if (isset($this->stores[$type])) {
			$class = $this->stores[$type][$readwrite];
			return Injector::inst()->create($class, $id);
		} else {
			$cls = $type . $readwrite;
			if (class_exists($cls)) {
				return new $cls($id);
			}
		}
	}
	
	/**
	 * Gets a content reader for the given store type over the asset given in 
	 * assetName. This is used for finding if an asset is stored remotely or 
	 * not
	 * 
	 * Returns NULL if that asset doesn't exist. 
	 *
	 * @param string $storeType
	 * @param string $assetName 
	 * 
	 * @return ContentReader
	 */
	public function findReaderFor($storeType, $assetName) {
		$writer = $this->getWriter($storeType);
		$contentId = $storeType . self::SEPARATOR . $writer->nameToId($assetName);
		$reader = $this->getReader($contentId);
		return $reader ? ($reader->isReadable() ? $reader : null) : null;
	}
}
