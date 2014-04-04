<?php
require_once('../public/include/gateassigner.php');

class GateAssignerTest extends PHPUnit_Framework_TestCase {
	private $ga;

	public function setUp() {
		$this->ga = new GateAssigner('testdata.txt');
		$this->ga->findGate('TRA6868', 'B738', 'LGAV');
	}

	public function testFindGate() {
		$result = $this->ga->result();

		$this->assertEquals('D51', $result['gate']);
		$this->assertEquals('RL', $result['matchType']);
	}

	public function testAssignGate() {
		$result = $this->ga->result();

		$this->ga->assignFoundGate();

		$this->assertArrayHasKey($result['gate'], $this->ga->getAssignedGates());
		$this->assertNotFalse($this->ga->isGateAssigned($result['gate']));
		$this->assertFalse($this->ga->result());
	}

	public function testReleaseGate() {
		$result = $this->ga->result();

		$this->ga->assignFoundGate();
		$this->assertArrayHasKey($result['gate'], $this->ga->getAssignedGates());

		$this->ga->releaseGate($result['gate']);
		$this->assertArrayNotHasKey($result['gate'], $this->ga->getAssignedGates());
	}

	public function testAssignedCallsign() {
		$result = $this->ga->result();

		$this->ga->assignFoundGate();

		$assigned = $this->ga->isCallsignAssigned('TRA6868');

		$this->assertEquals('TRA6868', $assigned['callsign']);
	}
}
?>