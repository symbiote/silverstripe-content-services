<?php

/**
 * Base class that manages the identifier stuff for readers and writers
 *
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
abstract class ReaderWriterBase {
	
	/**
	 * @var sets the identifier of this content. 
	 */
	protected $sourceIdentifier;
	
	/**
	 * The actual unique id that represents this content
	 *
	 * @var string 
	 */
	protected $id;
	
	
	public function __construct($id, $sourceId = null) {
		$this->id = $id;
		$this->sourceIdentifier = $sourceId;
	}
	
	/**
	 * @param string $sourceId
	 */
	public function setSourceIdentifier($sourceId) {
		$this->sourceIdentifier = $sourceId;
	}
	
	/**
	 * A signature for this content store. For example, filesystem might return
	 * 
	 * FILESYSTEM
	 * 
	 * This only needs to be unique with respect to other content stores
	 */
	public function getSourceIdentifier() {
		return $this->sourceIdentifier ? $this->sourceIdentifier : str_replace('ContentWriter', '', get_class($this));
	}
	
	/**
	 * Gets the underlying id if this item
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get content identifier that can be used to retrieve this content at a 
	 * later point in timer
	 */
	public function getContentId() {
		if (!$this->id) {
			throw new Exception("Null content identifier; content must be written before retrieving id");
		}
		return $this->getSourceIdentifier() . ContentService::SEPARATOR . $this->id;
	}
}
