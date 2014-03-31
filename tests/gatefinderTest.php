<?php
require_once('../public/include/gatefinder.php');

class GateFinderTest extends PHPUnit_Framework_TestCase {
	
	public function testAircraftToCat1() {
		$gf = new GateFinder();

		$this->assertEquals(4, $gf->resolveAircraftCat('B738'));
	}

	public function testAircraftToCat2() {
		$gf = new GateFinder();

		$this->assertEquals(8, $gf->resolveAircraftCat('A380'));
	}

	public function testAircraftToCatUnknown() {
		$gf = new GateFinder();

		$this->assertFalse($gf->resolveAircraftCat('FOO1'));
	}

	public function testResolveGate1() {
		$gf = new GateFinder();

		$this->assertEquals(array('B', 'C', 'D', 'E', 'F', 'G'), $gf->resolveAirlineGate('KLM123'));
	}

	public function testResolveGate2() {
		$gf = new GateFinder();

		$this->assertEquals(array('C', 'D', 'E'), $gf->resolveAirlineGate('TRA 06R'));
	}

	public function testSchengen1() {
		$gf = new GateFinder();

		$this->assertTrue($gf->resolveSchengenOrigin('EBBR'));
	}

	public function testSchengen2() {
		$gf = new GateFinder();

		$this->assertFalse($gf->resolveSchengenOrigin('KLAX'));
	}

	public function testFindGate1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('TRA123', 'B738', 'EBBR');

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals(4, $allGates[$gate]);
		$this->assertTrue(in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['TRA']));
		$this->assertTrue(array_key_exists($gate, Gates_EHAM::allSchengenGates()));
	}

	public function testFindGate2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('KLM123', 'MD11', 'EDDT');

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals(7, $allGates[$gate]);
		$this->assertTrue(in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['KLM']));
		$this->assertTrue(array_key_exists($gate, Gates_EHAM::allSchengenGates()));
	}

	public function testFindGate3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('EAL123', 'A380', 'OMDB');

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals(8, $allGates[$gate]);
		$this->assertTrue(in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['EAL']));
		$this->assertTrue(array_key_exists($gate, Gates_EHAM::allNonSchengenGates()));
	}

	public function testFindGateCargo1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('MPH456', 'B744', 'KLAX');

		$this->assertTrue(in_array($gate, Gates_EHAM::$cargoGates['MPH']));
	}

	public function testFindGateCargo2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('FDX456', 'B744', 'KLAS');

		$this->assertTrue(in_array($gate, Gates_EHAM::$cargoGates['FDX']));
	}

	public function testFindGateCargo3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('ANA789', 'B744', 'KEWR');

		$this->assertTrue(in_array($gate, Gates_EHAM::$cargoGates['ANA']));
	}

	public function testFindRealGate1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findRealGate('TRA6336');

		$this->assertEquals('C18', $gate);
	}

	public function testFindRealGate2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findRealGate('KLM894');

		$this->assertEquals('F07', $gate);
	}

	public function testFindRealGate3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findRealGate('EZY2723');

		$this->assertEquals('H01', $gate);
	}

	public function testFindRealGateUnknown() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findRealGate('KLM123');

		$this->assertFalse($gate);
	}

	public function testFindGateReal1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('TRA6868', 'B738', 'LGAV');

		$this->assertEquals('D51', $gate);
	}

	public function testFindGateReal2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('KLM894', 'B744', 'ZSPD');

		$this->assertEquals('F07', $gate);
	}

	public function testFindGateReal3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('EZY2723', 'A319', 'LIMC');

		$this->assertEquals('H01', $gate);
	}

	public function testFindGateRealTooHeavy() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('TRA6868', 'MD11', 'LGAV');

		$allGates = Gates_EHAM::allGates();

		$this->assertNotEquals('D51', $gate);

		$this->assertEquals(7, $allGates[$gate]);
		$this->assertTrue(in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['TRA']));
		$this->assertTrue(array_key_exists($gate, Gates_EHAM::allSchengenGates()));
	}
}

?>