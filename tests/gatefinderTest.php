<?php
require_once('../public/include/gatefinder.php');

class GateFinderTest extends PHPUnit_Framework_TestCase {
	
	public function testResolveGate1() {
		$this->assertEquals(array('B', 'C', 'D', 'E', 'F', 'G'), Gates_EHAM::resolveAirlineGate('KLM123'));
	}

	public function testResolveGate2() {
		$this->assertEquals(array('C', 'D', 'E'), Gates_EHAM::resolveAirlineGate('TRA 06R'));
	}

	public function testFindGate1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('TRA123', 'B738', 'EBBR');

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals('RANDOM', $gate['match']);

		$this->assertEquals(4, $allGates[$gate['gate']]);
		$this->assertTrue(in_array(substr($gate['gate'], 0, 1), Gates_EHAM::resolveAirlineGate('TRA')));
		$this->assertTrue(array_key_exists($gate['gate'], Gates_EHAM::allSchengenGates()));
	}

	public function testFindGate2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('KLM123', 'MD11', 'EDDT');

		$allGates = Gates_EHAM::allGates();
		$extraGates = Gates_EHAM::getExtraGates('MD11');

		$this->assertEquals('RANDOM', $gate['match']);

		$this->assertTrue($allGates[$gate['gate']] == 7 || in_array($gate['gate'], $extraGates));
		$this->assertTrue(in_array(substr($gate['gate'], 0, 1), Gates_EHAM::resolveAirlineGate('KLM')));
		$this->assertTrue(array_key_exists($gate['gate'], Gates_EHAM::allSchengenGates()));
	}

	public function testFindGate3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('EAL123', 'A380', 'OMDB');

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals('RANDOM', $gate['match']);

		$this->assertEquals(8, $allGates[$gate['gate']]);
		$this->assertTrue(in_array(substr($gate['gate'], 0, 1), Gates_EHAM::resolveAirlineGate('EAL')));
		$this->assertTrue(array_key_exists($gate['gate'], Gates_EHAM::allNonSchengenGates()));
	}

	public function testFindGateCargo1() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('MPH456', 'B744', 'KLAX');

		$this->assertTrue(in_array($gate['gate'], Gates_EHAM::resolveCargoAirlineGate('MPH')));
		$this->assertEquals('CARGO', $gate['match']);
	}

	public function testFindGateCargo2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('FDX456', 'B744', 'KLAS');

		$this->assertTrue(in_array($gate['gate'], Gates_EHAM::resolveCargoAirlineGate('FDX')));
		$this->assertEquals('CARGO', $gate['match']);
	}

	public function testFindGateCargo3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('ANA789', 'B744', 'KEWR');

		$this->assertTrue(in_array($gate['gate'], Gates_EHAM::resolveCargoAirlineGate('ANA')));
		$this->assertEquals('CARGO', $gate['match']);
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

		$this->assertEquals('D51', $gate['gate']);
		$this->assertEquals('RL', $gate['match']);
	}

	public function testFindGateReal2() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('KLM894', 'B744', 'ZSPD');

		$this->assertEquals('F07', $gate['gate']);
		$this->assertEquals('RL', $gate['match']);
	}

	public function testFindGateReal3() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('EZY2723', 'A319', 'LIMC');

		$this->assertEquals('H01', $gate['gate']);
		$this->assertEquals('RL', $gate['match']);
	}

	public function testFindGateRealTooHeavy() {
		$gf = new GateFinder('testdata.txt');
		$gate = $gf->findGate('TRA6868', 'MD11', 'LGAV');

		$allGates = Gates_EHAM::allGates();
		$extraGates = Gates_EHAM::getExtraGates('MD11');

		$this->assertNotEquals('D51', $gate['gate']);
		$this->assertEquals('RL_HEAVY', $gate['match']);

		$this->assertTrue($allGates[$gate['gate']] == 7 || in_array($gate['gate'], $extraGates));
		$this->assertTrue(in_array(substr($gate['gate'], 0, 1), Gates_EHAM::resolveAirlineGate('TRA')));
		$this->assertTrue(array_key_exists($gate['gate'], Gates_EHAM::allSchengenGates()));
	}

	public function testOccupied() {
		$gf = new GateFinder('testdata.txt');
		$gate1 = $gf->findGate('TRA123', 'B738', 'EBBR');

		$gf->occupyGate($gate1['gate']);
		$this->assertTrue($gf->isGateOccupied($gate1['gate']));

		$gate = $gf->findGate('TRA123', 'B738', 'EBBR');
		$this->assertNotEquals($gate1['gate'], $gate['gate']);

		$allGates = Gates_EHAM::allGates();

		$this->assertEquals(4, $allGates[$gate['gate']]);
		$this->assertTrue(in_array(substr($gate['gate'], 0, 1), Gates_EHAM::resolveAirlineGate('TRA')));
		$this->assertTrue(array_key_exists($gate['gate'], Gates_EHAM::allSchengenGates()));
	}
}

?>