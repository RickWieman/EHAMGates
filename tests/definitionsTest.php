<?php
require_once('../public/include/definitions_global.php');
require_once('../public/include/definitions_eham.php');

class DefinitionsTest extends PHPUnit_Framework_TestCase {
	public function testCanTranslateInvalidAircraft() {
		$this->assertTrue(Definitions::canTranslateAircraftType('A380'));
	}

	public function testCannotTranslateInvalidAircraft() {
		$this->assertFalse(Definitions::canTranslateAircraftType('FOO1'));
	}

	public function testTranslateInvalidAircraft1() {
		$this->assertEquals('A388', Definitions::translateInvalidAircraft('A380'));
	}

	public function testTranslateInvalidAircraft2() {
		$this->assertEquals('B737', Definitions::translateInvalidAircraft('737'));
	}

	public function testTranslateValidAircraft() {
		$this->assertEquals('B744', Definitions::translateInvalidAircraft('B744'));
	}

	public function testAircraftToCat1() {
		$this->assertEquals(4, Definitions::resolveAircraftCat('B738'));
	}

	public function testAircraftToCat2() {
		$this->assertEquals(9, Definitions::resolveAircraftCat('A388'));
	}

	public function testInvalidAircraftToCat1() {
		$this->assertEquals(9, Definitions::resolveAircraftCat('A380'));
	}

	public function testInvalidAircraftToCat2() {
		$this->assertEquals(4, Definitions::resolveAircraftCat('737'));
	}

	public function testAircraftToCatUnknown() {
		$this->assertFalse(Definitions::isValidAircraftType('FOO1'));
		$this->assertEquals(1, Definitions::resolveAircraftCat('FOO1'));
	}

	public function testSchengen1() {
		$this->assertTrue(Definitions::resolveSchengenOrigin('EBBR'));
	}

	public function testSchengen2() {
		$this->assertFalse(Definitions::resolveSchengenOrigin('KLAX'));
	}

	public function testBusGate() {
		$this->assertTrue(Gates_EHAM::isBusGate('D06'));
	}

	public function testBusGateFalse1() {
		$this->assertFalse(Gates_EHAM::isBusGate('FOO'));
	}

	public function testBusGateFalse2() {
		$this->assertFalse(Gates_EHAM::isBusGate('D05'));
	}
}

?>