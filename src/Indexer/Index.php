<?php 
/**
 * This file is part of JsonDb library.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

namespace IBT\JsonDB\Indexer;

/**
* Index class contains a parsed token and associated information.
* 
*/
class Index {

	/**
	* @var string JSON type
	*/
	private $typ;

	/**
	* @var mixed Data contained in the Index
	*/
	private $value;

	/**
	* @var string JSON path to the value (using dot notation)
	*/
	private $path;

	/**
	* @var int Depth in the JSON object key/value hierarchy
	*/
	private $depth;

	/**
	* Constructs a new Scan Index.
	*
	* @param string $typ 						Type of the value
	* @param double|string|array|boolean $value JSON value
	* @param string $path 						JSON object path
	* @param int $depth							Tree depth
	*/
	public function __construct($typ, $value, $path, $depth) {
		$this->typ = $typ;
		$this->value = $value;
		$this->path = $path;
		$this->depth = $depth;
	}

	/**
	* Retrieves a property if it exists.
	*
	* @see http://php.net/manual/en/language.oop5.overloading.php#object.get
	* @param string $property Name of property to retrieve
	* @return mixed
    * @throws Exception if the property isn't found
	*/
	public function __get($property) {
		if(property_exists($this, $property)) {
			return $this->$property;
		}
		throw new Exception('Property not found');
	}
	
	/**
	* Return internal properties for debugging purposes.
	*
	* @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
	* @return mixed
	*/
    public function __debugInfo()
    {
        return array(
			"depth" => $this->depth,
			"path" => $this->path,
			"value" => $this->value,
			"type" => $this->typ
		);
    }
}