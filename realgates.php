<?php

require_once('definitions.php');

class RealGates {
	private $allRealGates;

	function parseData() {
		$data = file_get_contents('data.html'); // Data source: http://schiphol.dutchplanespotters.nl/

		// Find flight table body
		$data = explode('<table class="flights" cellpadding="2" cellspacing="0">', $data);
		$data = explode('</tbody>', $data[1]);
		$data = explode('<tbody>', $data[0]);

		// One flight per row
		$flights = explode('</tr>', $data[1]);

		$realGates = array();

		// Process all flights
		foreach($flights as $flight) {
			$info = array();

			// Process the columns of the flight (by stripping garbage)
			foreach(explode('</td>', $flight) as $flightInfo) {
				$info[] = trim(strip_tags($flightInfo));
			}

			// If a gate assignment exists for the flight
			if(!empty($info[5])) {

				// Add extra 0 to all gate numbers < 10
				$gate = preg_replace('/^([A-Z])(\d)$/', '${1}0${2}', $info[5]);
				
				// Convert Non-Schengen gates into 'real' gates
				if(array_key_exists($gate, Gates_EHAM::$schengenGatesToApron)) {
					$gate = Gates_EHAM::$schengenGatesToApron[$gate];
				}
				
				$realGates[$info[6]] = $gate;
			}
		}

		$this->allRealGates = $realGates;

		return $realGates;
	}

	function getAllRealGates() {
		if(empty($this->allRealGates)) {
			$this->parseData();
		}

		return $this->allRealGates;
	}

	function findGateByFlightnumber($flightnumber) {
		$realGates = $this->getAllRealGates();

		if(array_key_exists($flightnumber, $realGates)) {
			return $realGates[$flightnumber];
		}

		return false;
	}
}

?>