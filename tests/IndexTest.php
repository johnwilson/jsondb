<?php

/*
 * This file is part of JsonDb.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

use IBT\JsonDB\Indexer\Index;

class IndexTest extends PHPUnit_Framework_TestCase {

	public function testIndexToArray() {
		$idx = new Index("string", "hello", "user.greeting", "1");
		
		$this->assertEquals(1, $idx->depth);
		$this->assertEquals("user.greeting", $idx->path);
		$this->assertEquals("hello", $idx->value);
		$this->assertEquals("string", $idx->typ);
	}
}