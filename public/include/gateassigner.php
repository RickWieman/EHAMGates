<?php

require_once('gatefinder.php');

class GateAssigner {
	
	private $assignedGates = array();
	private $gateFinder;

	private $lastRequest;

	function __construct($dataSource = null) {
		$this->gateFinder = new GateFinder($dataSource);
	}

	function assignGate($gate, $matchType, $callsign = 'unknown', $aircraftType = null, $origin = null) {
		if((preg_match('/[A-Z]+[0-9]+/', $callsign) || $callsign == 'unknown')
			&& (array_key_exists($gate, Gates_EHAM::allGates())
				|| in_array($gate, Gates_EHAM::allCargoGates()))) {
			$this->assignedGates[$gate] = array(
				'callsign' => $callsign,
				'aircraftType' => $aircraftType,
				'origin' => $origin,
				'matchType' => $matchType
			);

			$this->gateFinder->occupyGate($gate);
			
			if($matchType != 'OCCUPIED') {
				$this->resetSearch();
			}

			return true;
		}

		return false;
	}

	function assignFoundGate() {
		return $this->assignGate($this->lastRequest['gate'], $this->lastRequest['matchType'], $this->lastRequest['callsign'], $this->lastRequest['aircraftType'], $this->lastRequest['origin']);
	}

	function assignManualGate($gate) {
		return $this->assignGate($gate, 'MANUAL', $this->lastRequest['callsign'], $this->lastRequest['aircraftType'], $this->lastRequest['origin']);
	}

	function releaseGate($gate) {
		if(array_key_exists($gate, $this->assignedGates)) {
			unset($this->assignedGates[$gate]);
			$this->gateFinder->releaseGate($gate);

			return true;
		}

		return false;
	}

	function findGate($callsign, $aircraftType, $origin) {
		$callsign = strtoupper($callsign);
		$origin = strtoupper($origin);

		$this->lastRequest = array(
			'callsign' 		=> $callsign,
			'aircraftType' 	=> $aircraftType,
			'origin' 		=> $origin
		);

		$gate = $this->gateFinder->findGate($callsign, $aircraftType, $origin);
		if($gate) {
			$this->lastRequest['gate'] = $gate['gate'];
			$this->lastRequest['matchType'] = $gate['match'];
		}
		else {
			$this->lastRequest['gate'] = null;
			$this->lastRequest['matchType'] = 'NONE';
		}
	}

	function alreadyOccupied() {
		if($this->result()) {
			$this->assignGate($this->lastRequest['gate'], 'OCCUPIED');

			$this->findGate($this->lastRequest['callsign'], $this->lastRequest['aircraftType'], $this->lastRequest['origin']);

			return true;
		}

		return false;
	}

	function result() {
		if(!empty($this->lastRequest)) {
			return $this->lastRequest;
		}

		return false;
	}

	function getFreeGates($aircraftType, $origin) {
		$gates = $this->gateFinder->getFreeGates($aircraftType, $origin);
		ksort($gates);

		return $gates;
	}

	function getAssignedGates() {
		ksort($this->assignedGates);

		return $this->assignedGates;
	}

	function resetSearch() {
		$this->lastRequest = null;
	}

	function isGateAssigned($gate) {
		if(array_key_exists($gate, $this->assignedGates)) {
			return $this->assignedGates[$gate];
		}

		return false;
	}

	function isCallsignAssigned($callsign) {
		foreach($this->assignedGates as $gate => $data) {
			if($data['callsign'] == $callsign) {
				$data['gate'] = $gate;
				return $data;
			}
		}

		return false;
	}

	function handleAssign() {
		if(isset($_GET['assign']) && $this->result()) {
			return $this->assignFoundGate();
		}

		return false;
	}

	function handleAssignManual() {
		if(isset($_GET['manual']) && $this->result()) {
			return $this->assignManualGate($_GET['manual']);
		}

		return false;
	}

	function handleOccupied() {
		if(isset($_GET['occupied']) && $this->result()) {
			return $this->alreadyOccupied();
		}

		return false;
	}

	function handleOccupy() {
		if(isset($_GET['occupy'])) {
			return $this->assignGate($_GET['occupy'], 'OCCUPIED');
		}

		return false;
	}

	function handleRelease() {
		if(isset($_GET['release'])) {
			return $this->releaseGate($_GET['release']);
		}

		return false;
	}
}

?>