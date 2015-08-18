<?php
require_once('../public/include/realgates.php');

class RealGatesTest extends PHPUnit_Framework_TestCase {
	public function testFindGateByFlightNumber() {
		$rg = new RealGates('testdata.txt');
		$gate = $rg->findGateByFlightnumber('HV 6336');

		$this->assertEquals('C18', $gate);
	}

	public function testFindGateByFlightNumberUnknown() {
		$rg = new RealGates('testdata.txt');
		$gate = $rg->findGateByFlightnumber('KL 123');

		$this->assertFalse($gate);
	}

	public function testAllGatesImplemented() {
		$rg = new RealGates('testdata.txt');

		$allRealGates = array_filter($rg->getAllRealGates(), function($value) {
			return $value != "UNKNOWN";
		});
		$allImplementedGates = array_keys(array_merge(Gates_EHAM::allGates(), Gates_EHAM::allCargoGates()));
		$allImplementedGates = array_merge($allImplementedGates, Gates_EHAM::allBusGates());

		$missingGates = array_diff($allRealGates, $allImplementedGates);

		$this->assertEquals(0, count($missingGates));
	}

	public function testAirlinePier1() {
		$rg = new RealGates('testdata.txt');
		$piers = $rg->findPierByAirlineIATA('KL');

		$this->assertArrayHasKey('D', $piers);
		$this->assertEquals(114, $piers['D']);
	}

	public function testAirlinePier2() {
		$rg = new RealGates('testdata.txt');
		$piers = $rg->findPierByAirlineIATA('DL');

		$this->assertArrayHasKey('E', $piers);
		$this->assertEquals(8, $piers['E']);
	}

	public function testAirlineUnknownPier() {
		$rg = new RealGates('testdata.txt');
		$piers = $rg->findPierByAirlineIATA('ZZ');

		$this->assertFalse($piers);
	}
}
?>