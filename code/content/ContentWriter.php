<?php

/**
 * A class that can write content out somewhere, and refer to that content by
 * a uri type structure, eg filesystem://1
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
abstract class ContentWriter extends ReaderWriterBase {
	
	
	/**
	 * A possibly set reader object that wraps around the content this writer
	 * will also be writing to
	 *
	 * @var ContentReader
	 */
	protected $reader;

	/**
	 * Where the content this writer will write is coming from
	 *
	 * @var resource|filename|ContentReader
	 */
	protected $source;
	
	public function __construct($id = null, $sourceId = null) {
		if ($id && $id instanceof ContentReader) {
			$this->reader = $id;
			$id = $this->reader->getId();
		}
		$this->sourceIdentifier = $sourceId;
		$this->id = $id;
	}

	/**
	 * Get a content reader that can read the underlying content item
	 */
	public function getReader() {
		return singleton('ContentService')->getReader($this->getContentId());
	}

	/**
	 * Sets the source of the content that this writer will eventually write
	 * out. 
	 *
	 * @param mixed $source 
	 */
	public function setSource($source) {
		$this->source = $source;
	}
	
	/**
	 * Get content reader wrapper around a given piece of content
	 *
	 * @param mixed $content 
	 */
	protected function getReaderWrapper($content) {
		if (!$content) {
			$content = $this->source;
		}

		$reader = null;
		
		if (is_resource($content)) {
			$data = null;
			while (!feof($content)) {
				$data .= fread($content, 8192);
			}
			fclose($content);
			$reader = new RawContentReader($data);
		} else if ($content instanceof ContentReader) {
			$reader = $content;
		} else if (is_string($content)) {
			// assumed to be a file
			if (file_exists($content) && is_readable($content)) {
				// naughty, but it's the exception that proves the rule...
				$reader = new FileContentReader($content);
			} else {
				$reader = new RawContentReader($content);
			}
		}

		return $reader;
	}
	
	
	/**
	 * Convert a name to an ID that a content reader would use to refer to
	 * that content. 
	 * 
	 * Externally, this is used when trying to determine whether a particular 
	 * content store has an asset and the client code only knows the 
	 * userspace name for the content. 
	 * 
	 * This is used heavily by the theme assets management to determine
	 * whether the file needs to be pushed to the CDN or not
	 *
	 * @param string $fullname
	 *
	 * @return string
	 */
	public abstract function nameToId($fullname);

	/**
	 * Write content to storage
	 *
	 * @param mixed $content 
	 *				Either a content reader, file 
	 *				resource from fopen, or a string representing a file to 
	 *				the contents of. If the content
	 *				property is null, it is expected that the 'source' var will
	 *				be used as the content instead
	 * @param string $name
	 *				The name that is used to refer to this piece of content, 
	 *				if needed. The name can contain directory separators if desired,
	 *				and if a file exists with the same name, it will be overwritten.
	 */
	public abstract function write($content = null, $name = '');
	
	/**
	 * Delete the item represented by this writer
	 */
	public abstract function delete();
}
