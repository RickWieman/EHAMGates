<?php

require_once('definitions_eham.php');

class RealGates {
	private $dataSource = 'http://schiphol.dutchplanespotters.nl/';
	private $cacheFile = 'data.txt';
	private $allRealGates;

	function __construct($useData = null) {
		if($useData != null) {
			$this->dataSource = $useData;	
		}
	}

	function fetchData($force = false) {
		$cacheDuration = 60 * 15;
		$stamp = (file_exists($this->cacheFile) ? file_get_contents($this->cacheFile, NULL, NULL, 0, 10) : 0);

		// Reload only when cache is expired
		if(time() - $stamp > $cacheDuration || $force) {
			$data = file_get_contents($this->dataSource);
		
			if($data) {
				$data = time() . $data;
				file_put_contents($this->cacheFile, $data);

				return $data;
			}
		}
		
		return file_get_contents($this->cacheFile);
	}

	function parseData() {
		$data = $this->fetchData();
		$realGates = array();

		// Find flight table body
		$data = explode('<table class="flights" cellpadding="2" cellspacing="0">', $data);
		$data = explode('</tbody>', $data[1]);
		$data = explode('<tbody>', $data[0]);

		// One flight per row; everything after last </tr> is garbage -> pop
		$flights = explode('</tr>', $data[1]);
		array_pop($flights);

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
				
				$realGates[$info[6]] = Gates_EHAM::convertSchengenGateToVOP($gate);
			}
			else {
				$realGates[$info[6]] = 'UNKNOWN';
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