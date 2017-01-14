<?php

/**
 * This file is part of JsonDb library.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

namespace IBT\JsonDB;

use IBT\JsonDB\Indexer\Scanner;

/**
* Collection class groups json documents.
* 
* ```php
* $c = new Client();
* $c->setup();
* $col = $c->newCollection("users");
* $col->insert('{"name":"jason bourne", "category":"agent"}');
* ```
*/
class Collection {

	/**
	* @var string Name of the Collection
	*/
	private $name;

	/**
	* @var int Collection database primary key
	*/
	private $id;

	/**
	* @var Client JsonDB Client
	*/
	private $client;

	/**
	* Constructor, creates a new Collection
	*
	* @param int $id Database primary key
	* @param string $name Collection name
	* @param Client $client
	*/
	function __construct($id, $name, Client $client) {
		$this->id = $id;
		$this->name = $name;
		$this->client = $client;
	}

	/**
	* Validate Collection name
	*
	* @param string $name Collection name
	* @return boolean
	*/
	static function isValidName($name) {
		// only accept alpha characters
		$p = '/^[a-zA-Z]+$/';
		$m = preg_match($p, $name);
		if($m) {
			return true;
		}

		return false;
	}

	/**
	* Collection name
	*
	* @return string
	*/
	function getName() {
		return $this->name;
	}

	/**
	* Collection id
	*
	* @return int
	*/
	function getId() {
		return $this->id;
	}

	/**
	* Column name from JSON type
	*
	* @param string $typ
	* @return string
    * @throws Exception if the JSON type isn't supported
	*/
	public static function getColumn($typ) {
		switch ($typ) {
			case 'float':
				return 'vfloat';
			case 'boolean':
				return 'vboolean';
			case 'string':
				return 'vstring';
			case 'array':				
			case 'object':
				return 'vjson';
			case 'null':
				return 'vnull';
			default:
				throw new Exception('Unknown JSON type');
				break;
		}
	}

	/**
	* Save JSON string (create/update)
	*
	* @param string $doc JSON string
	* @return string
	*/
	private function save($doc) {
		// create index
		$scr = new Scanner();
		$index_list = $scr->scan($doc);

		$conn = $this->client->connection();
		$cid = $this->id;
		
		$conn->transaction(function() use ($index_list, $conn, $cid, $doc) {
			// add data
			
			$json = json_encode($doc);
			$id = $conn->table(Client::T_JSON)->insertGetId(
				['doc_id' => $doc['_id'], 'cid' => $cid, 'data' => $json]
			);

			// add index
			$rows = array();
			foreach ($index_list as $item) {
				$row = [
					'cid' => $cid,
					'oid' => $id,
					'depth' => $item->depth,
					'typ' => $item->typ,
					'path' => $item->path,
					'vfloat' => NULL,
					'vboolean' => NULL,
					'vstring' => NULL,
					'vjson' => NULL,
					'vnull' => NULL
				];

				// insert value in appropriate column
				$c = Collection::getColumn($item->typ);
				$row[$c] = $item->value;
				$rows[] = $row;
			}
			$conn->table(Client::T_INDEX)->insert($rows);
		});

		return $doc['_id'];
	}

	/**
	* Check if document exists
	*
	* @param string $id document id
	* @return boolean
	*/
	private function exists($id) {
		$conn = $this->client->connection();
		$row = $conn->table(Client::T_JSON)->select("doc_id")->where("doc_id", $id)->first();
		if(!$row) {
			return false;
		}

		return true;
	}

	/**
	* Database manager
	*
	* @return Client
	*/
	function getClient() {
		return $this->client;
	}

	/**
    * Inserts a json document into db.
    *
    * @param string $json JSON string to insert
    * @return string Returns document id
    * @throws Exception if the document id isn't valid
    */
	function insert($json) {
		$doc = json_decode($json, true);

		// check if json has id
		if(!array_key_exists('_id', $doc)) {
			$doc['_id'] = uniqid('oid');
		} else {
			// only accept alpha numeric characters
			$p = '/^[a-z0-9]+$/';
			$m = preg_match($p, $doc['_id']);
			if(!$m) {
				throw new Exception('Invalid document id.');
			}
		}
		
		return $this->save($doc);			
	}

	/**
	* Retrieve JSON document
	*
	* @param string $id document id
	* @return mixed
	*/
	function get($id) {
		$conn = $this->client->connection();
		$doc = $conn->table(Client::T_JSON)
			->where('doc_id', $id)
			->where('cid', $this->id)
			->first();

		if(is_null($doc)) {
			return NULL;
		}

		return json_decode($doc->data);
	}

	/**
	* Delete JSON document
	*
	* @param string $oid document id
	* @return boolean
	*/
	function delete($oid) {
		$conn = $this->client->connection();
		return $conn->table(Client::T_JSON)->where('doc_id', $oid)->delete() > 0;
	}

	/**
    * Updates a JSON document in db.
    *
    * @param string $json JSON string to insert
    * @return string Returns document id
    * @throws Exception if the original document id isn't present
    */
	function update($json) {
		$doc = json_decode($json, true);

		// check if json has id
		if(!array_key_exists('_id', $doc)) {
			throw new Exception('Missing \'_id\' in JSON document');
		}

		$oid = $doc['_id'];

		if($this->exists($oid)) {
			$this->delete($oid);
			$this->save($doc);
			return true;
		}

		return false;
	}

	/**
	* Total number of JSON documents
	*
	* @return int
	*/
	function count() {
		$conn = $this->client->connection();
		$tbl = $conn->table(Client::T_JSON);
		return $tbl->select($conn->raw('distinct(doc_id)'))->count();
	}

	/**
	* New Filter to query JSON document content
	*
	* @return \IBT\JsonDB\Filter
	*/
	function newFilter() {
		return new Filter($this);
	}
}