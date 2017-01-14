<?php

/*
 * This file is part of JsonDb.
 *
 * @author John Wilson
 * @copyright 2016 John Wilson
 * 
 */

use IBT\JsonDB\Indexer\Scanner;

class ScannerTest extends PHPUnit_Framework_TestCase {

	public function testScan() {
		$s = new Scanner();
		$j = '
{
    "fighter":"iron monkey",
    "points":96,
    "available":false,
    "dob":null,
    "styles":[
        {
            "name":"功夫",
            "name_eng":"kung-fu"
        }
    ]
}
';
		$r = $s->scan(json_decode($j));
		
		$kv = array();
		foreach ($r as $index) {
			$kv[$index->path] = $index;
		}

		$this->assertEquals($kv["fighter"]->value, "iron monkey");
		$this->assertEquals($kv["points"]->value, 96);
		$this->assertEquals($kv["available"]->value, false);
		$this->assertEquals($kv["dob"]->value, null);
		$this->assertEquals($kv["styles.0.name"]->value, "功夫");
		$this->assertEquals($kv["styles.0.name_eng"]->value, "kung-fu");
	}
}