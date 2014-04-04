<?php
require_once('../public/include/definitions_global.php');

class DefinitionsTest extends PHPUnit_Framework_TestCase {
	
	public function testAircraftToCat1() {
		$this->assertEquals(4, Definitions::resolveAircraftCat('B738'));
	}

	public function testAircraftToCat2() {
		$this->assertEquals(8, Definitions::resolveAircraftCat('A380'));
	}

	public function testAircraftToCatUnknown() {
		$this->assertFalse(Definitions::resolveAircraftCat('FOO1'));
	}

	public function testSchengen1() {
		$this->assertTrue(Definitions::resolveSchengenOrigin('EBBR'));
	}

	public function testSchengen2() {
		$this->assertFalse(Definitions::resolveSchengenOrigin('KLAX'));
	}
}

?>