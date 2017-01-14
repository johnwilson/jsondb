<?php

/*
 * This file is part of JsonDb.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

use IBT\JsonDB\Client;

class CollectionTest extends PHPUnit_Framework_TestCase {
	
	public function testCollectionCRUD() {
		$c = new Client();
		$c->setup();

		$col = $c->newCollection("users");
		
		// insert json
		$j = '{"name":"jason bourne", "category":"agent"}';
		$oid = $col->insert($j);

		// retrieve and check
		$obj = $col->get($oid);
		$this->assertEquals($obj->name, "jason bourne");
		$this->assertEquals($obj->category, "agent");

		// update
		$obj->category = "ghost";
		$ok = $col->update(json_encode($obj));
		$this->assertTrue($ok);

		// retrieve and check
		$obj = $col->get($oid);
		$this->assertEquals($obj->name, "jason bourne");
		$this->assertEquals($obj->category, "ghost");

		// delete
		$ok = $col->delete($oid);
		$this->assertTrue($ok);
		$obj = $col->get($oid);
		$this->assertNull($obj);
	}

	public function testCollectionFind() {
		// load test data
		$str = file_get_contents(__DIR__ . "/test.json");
		$j = json_decode($str, true);

		$c = new Client();
		$c->setup();

		$c->newCollection("dummy");
		$col = $c->newCollection("users");
		
		// insert json
		for ($i=0; $i < count($j); $i++) { 
			$tmp = json_encode($j[$i]);
			$col->insert($tmp);
		}

		// count
		$this->assertEquals($col->count(), 11);

		// implement filters
		$f = $col->newFilter();
		$f->whereIn('gender', ['female']);
		$res = $f->run();
		$this->assertEquals(count($res), 3);

		$f = $col->newFilter();
		$f->where('gender', '=', 'male');
		$res = $f->run();
		$this->assertEquals(count($res), 8);

		$f = $col->newFilter();
		$f->where('name', '=', 'Lowery Sheppard');
		$res = $f->run();
		$this->assertEquals(count($res), 1);
		$this->assertEquals($res[0], '575e76e84a60553b98f42544');

		$f = $col->newFilter();
		$f->where('gender', '=', 'female')->where('age', '>', 30);
		$res = $f->run();
		$this->assertEquals(count($res), 2);
	}
}