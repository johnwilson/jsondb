<?php
/**
 * This file is part of JsonDb library.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

namespace IBT\JsonDB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
* Client class serves as an entry point to the library.
* 
* ```php
* $c = new Client();
* $c->setup();
* ```
*/
class Client {

	/**
	* Collection Table name
	*/
	const T_COLLECTIONS = 'jsondb_collection';

	/**
	* JSON data Table name
	*
	*/
	const T_JSON = 'jsondb_json';
	
	/**
	* JSON indices Table name
	*
	*/
	const T_INDEX = 'jsondb_index';

	/**
	* Configuration information
	*
	* This is set in the jsondb.php file
	*
	* @var array
	*/
	public static $config;


	/**
	* Configuration manager for Illuminate framework
	*
	* @var \Illuminate\Database\Capsule\Manager
	* @see https://laravel.com/api/5.2/Illuminate/Database/Capsule/Manager.html.
	*/
	private $capsule;

	/**
	* Constructor, creates a new Client
	*
	*/
	public function __construct() {
		// setup db connection
		$capsule = new Capsule();
		$capsule->addConnection(self::$config["db"]);
		$this->capsule = $capsule;
	}

	/**
	* Creates/Recreates required tables in database
	*
	*/
	public function setup() {
		$sb = $this->connection()->getSchemaBuilder();

		// drop existing tables
		$sb->dropIfExists(self::T_INDEX);
		$sb->dropIfExists(self::T_JSON);
		$sb->dropIfExists(self::T_COLLECTIONS);

		// collections table
		$sb->create(self::T_COLLECTIONS, function(Blueprint $table){
			$table->increments('id');
			$table->string("name");
			$table->unique("name");
		});

		// json document table
		$sb->create(self::T_JSON, function(Blueprint $table){
			$table->increments('id');
			
			// document id
			$table->string("doc_id", 36);
			$table->unique("doc_id");
			
			// collection id
			$table->integer('cid')->unsigned();
			$table->foreign('cid')->references('id')
					->on(self::T_COLLECTIONS)
					->onDelete('cascade');

			$table->text('data');
		});
		
		// index table
		$sb->create(self::T_INDEX, function(Blueprint $table){
			$table->increments('id');
			
			$table->integer('cid')->unsigned();
			$table->foreign('cid')->references('id')
					->on(self::T_COLLECTIONS)
					->onDelete('cascade');

			// object id (primary key)
			$table->integer('oid')->unsigned();
			$table->foreign('oid')->references('id')
					->on(self::T_JSON)
					->onDelete('cascade');
			
			$table->integer('depth');
			$table->string('typ');
			
			$table->string('path');
			$table->index('path');
			
			// values
			$table->text('vjson')->nullable();
			$table->boolean('vboolean')->nullable();
			$table->text('vstring')->nullable();
			$table->float('vfloat')->nullable();
			$table->boolean('vnull')->nullable();
		});
	}

	/**
	* Current database connection
	* @return \Illuminate\Database\Connection
	*/
	public function connection() {
		return $this->capsule->getConnection();
	}

	/**
	* Create a new Collection
	* @param string $name Collection name
	* @return \IBT\JsonDB\Collection
    * @throws Exception if the collection name already exists/is invalid
	*/
	public function newCollection($name) {
		$name = strtolower($name);

		if(!Collection::isValidName($name)) {
			throw new Exception("The collection name $name isn't valid");
		}

		if(in_array($name, $this->listCollection())) {
			throw new Exception("The collection name $name already exists");
		}

		$conn = $this->connection();
		$id = $conn->table(self::T_COLLECTIONS)->insertGetId(
			['name' => $name]
		);
		
		return new Collection($id, $name, $this);
	}

	/**
	* Return existing Collection
	* @param string $name Collection name
	* @return \IBT\JsonDB\Collection
    * @throws Exception if the collection name doesn't exist
	*/
	public function getCollection($name) {
		$name = strtolower($name);
		$conn = $this->connection();

		// check for existence
		$row = $conn->table(self::T_COLLECTIONS)->select("id", "name")->where("name", $name)->first();
		if(!$row) {
			throw new Exception("The collection name $name doesn't exist");
		}

		return new Collection($row->id, $row->name, $this);
	}

	/**
	* List all collections
	* @return array
	*/
	public function listCollection() {
		$conn = $this->connection();
		$name = $conn->table(self::T_COLLECTIONS)->pluck("name");
		return $name;
	}
}