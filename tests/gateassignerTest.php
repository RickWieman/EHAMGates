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

	public function testImpossibleGate() {
		$ga = new GateAssigner('testdata.txt');
		$ga->findgate('EZY123', 'A388', 'EDDT');

		$result = $ga->result();

		$this->assertNull($result['gate']);
		$this->assertEquals('NONE', $result['matchType']);
	}

	public function testAssignGate() {
		$result = $this->ga->result();

		$this->ga->assignFoundGate();

		$this->assertArrayHasKey($result['gate'], $this->ga->getAssignedGates());
		$this->assertArrayHasKey('TRA6868', $this->ga->getAssignedCallsigns());
		$this->assertNotFalse($this->ga->isGateAssigned($result['gate']));
		$this->assertNotFalse($this->ga->isCallsignAssigned('TRA6868'));
		$this->assertFalse($this->ga->result());
	}

	public function testAssignGateManual() {
		$this->ga->assignManualGate('D53');

		$this->assertArrayHasKey('D53', $this->ga->getAssignedGates());
		$this->assertArrayHasKey('TRA6868', $this->ga->getAssignedCallsigns());
		$this->assertNotFalse($this->ga->isGateAssigned('D53'));
		$this->assertNotFalse($this->ga->isCallsignAssigned('TRA6868'));
		$this->assertFalse($this->ga->result());
	}

	public function testAssignWrongGate() {
		$assign = $this->ga->assignGate('Z01', 'MANUAL');

		$this->assertFalse($assign);
		$this->assertArrayNotHasKey('Z01', $this->ga->getAssignedGates());
		$this->assertArrayNotHasKey('TRA6868', $this->ga->getAssignedCallsigns());
		$this->assertFalse($this->ga->isGateAssigned('Z01'));
	}

	public function testReleaseCallsign() {
		$this->ga->assignFoundGate();
		$this->assertArrayHasKey('TRA6868', $this->ga->getAssignedCallsigns());

		$this->ga->releaseCallsign('TRA6868');
		$this->assertArrayNotHasKey('TRA6868', $this->ga->getAssignedCallsigns());
	}

	public function testReleaseCallsignFail() {
		$this->assertFalse($this->ga->isCallsignAssigned('TRA6868'));

		$release = $this->ga->releaseCallsign('TRA6868');

		$this->assertFalse($release);
		$this->assertFalse($this->ga->isCallsignAssigned('TRA6868'));
	}

	public function testReleaseGate() {
		$result = $this->ga->result();

		$this->ga->assignFoundGate();
		$this->assertArrayHasKey($result['gate'], $this->ga->getAssignedGates());

		$this->ga->releaseGate($result['gate']);
		$this->assertArrayNotHasKey($result['gate'], $this->ga->getAssignedGates());
	}

	public function testReleaseGateFail() {
		$this->assertFalse($this->ga->isGateAssigned('A31'));

		$release = $this->ga->releaseGate('A31');

		$this->assertFalse($release);
		$this->assertFalse($this->ga->isGateAssigned('A31'));
	}

	public function testAssignedCallsign() {
		$this->ga->assignFoundGate();

		$assigned = $this->ga->isCallsignAssigned('TRA6868');

		$this->assertNotFalse($assigned);
	}

	public function testAssignedCallsignFalse() {
		$assigned = $this->ga->isCallsignAssigned('ABC001');

		$this->assertFalse($assigned);
	}

	public function testAlreadyOccupied() {
		$result1 = $this->ga->result();

		$this->ga->alreadyOccupied();

		$this->assertArrayHasKey($result1['gate'], $this->ga->getAssignedGates());
		$this->assertEquals('unknown', $this->ga->isGateAssigned($result1['gate']));
		$this->assertArrayNotHasKey('TRA6868', $this->ga->getAssignedCallsigns());

		$result2 = $this->ga->result();

		$this->assertNotEquals($result2['gate'], $result1['gate']);
	}
}
?>