<?php

/*
 * This file is part of JsonDb.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

use IBT\JsonDB\Client;

class ClientTest extends PHPUnit_Framework_TestCase {
	public function testClientListAndCreate() {
		$c = new Client();
		$c->setup();

		// make sure collection list is empty
		$this->assertEmpty($c->listCollection());
		
		//create new collection
		$c->newCollection("users");
		$l = $c->listCollection();
		$this->assertContains("users", $l);
		$this->assertTrue(count($l) == 1);
		$col = $c->getCollection("users");
		$this->assertTrue($col->getName() == "users");
	}
}