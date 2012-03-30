<?php

/**
 * A db field that wraps a pointer to a content item. The field itself is aware
 * of content storage processes so that form fields can saved an upload 
 * directly
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class FileContent extends DBField {
	protected $store = 'File';

	/**
	 * Construct a string type field with a set of optional parameters
	 * @param $name string The name of the field
	 * @param $options array An array of options e.g. array('nullifyEmpty'=>false).  See {@link StringField::setOptions()} for information on the available options
	 */
	public function __construct($name = null, $store=null) {
		if ($store && class_exists($store.'ContentReader')) {
			$this->store = $store;
		} else if ($store) {
			throw new Exception("$store does not exist; cannot use FileContent field with this type");
		}
		parent::__construct($name);
	}

	/**
	 * (non-PHPdoc)
	 * @see core/model/fieldtypes/DBField#hasValue()
	 */
	function hasValue() {
		return ($this->value || $this->value == '0') || ( !$this->nullifyEmpty && $this->value === '');
	}

	/**
	 * (non-PHPdoc)
	 * @see core/model/fieldtypes/DBField#prepValueForDB($value)
	 */
	function prepValueForDB($value) {
		if(!$this->nullifyEmpty && $value === '') {
			return DB::getConn()->prepStringForDB($value);
		} else {
			return parent::prepValueForDB($value);
		}
	}
	
	function requireField() {
		$parts = array(
			'datatype'=>'varchar',
			'precision'=> 255,
			'character set'=>'utf8',
			'collate'=>'utf8_general_ci',
			'arrayValue'=>$this->arrayValue
		);
		
		$values = array(
			'type' => 'varchar',
			'parts' => $parts
		);
			
		DB::requireField($this->tableName, $this->name, $values);
	}
	
	/**
	 * Gets a file reader for the content store wrapped by this FileContent 
	 * field
	 */
	public function getReader() {
		return singleton('ContentService')->getReader($this->getValue());
	}
	
	public function URL() {
		return $this->getReader()->getURL();
	}
}
