<?php

require_once('definitions_eham.php');

class RealGates {
	private $dataSource = 'http://schiphol.dutchplanespotters.nl/';
	private $cacheFile = 'data.txt';
	private $allRealGates;
	private $airlinePiers;

	function __construct($useData = null) {
		if($useData != null) {
			$this->dataSource = $useData;
		}
	}

	function isCacheExpired() {
		$cacheDuration = 60 * 15;
		$stamp = (file_exists($this->cacheFile) ? file_get_contents($this->cacheFile, NULL, NULL, 0, 10) : 0);

		return time() - $stamp > $cacheDuration;
	}

	function fetchData($force = false) {
		if($this->isCacheExpired() || $force) {
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
		$airlinePiers = array();

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

				// Store pier(s) of the airline
				$airlineIATA = explode(' ', $info[6]);
				$airlineIATA = $airlineIATA[0];
				if(!array_key_exists($airlineIATA, $airlinePiers)) {
					$airlinePiers[$airlineIATA] = array();
				}

				$pier = substr($gate, 0, 1);
				if(!array_key_exists($pier, $airlinePiers[$airlineIATA])) {
					$airlinePiers[$airlineIATA][$pier] = 1;
				}
				else {
					$airlinePiers[$airlineIATA][$pier]++;
				}
			}
			else {
				$realGates[$info[6]] = 'UNKNOWN';
			}
		}

		$this->allRealGates = $realGates;
		$this->airlinePiers = $airlinePiers;

		return $realGates;
	}

	function getAllRealGates() {
		if(empty($this->allRealGates) || $this->isCacheExpired()) {
			$this->parseData();
		}

		return $this->allRealGates;
	}

	function getAllAirlinePiers() {
		if(empty($this->airlinePiers) || $this->isCacheExpired()) {
			$this->parseData();
		}

		return $this->airlinePiers;
	}

	function findGateByFlightnumber($flightnumber) {
		$realGates = $this->getAllRealGates();

		if(array_key_exists($flightnumber, $realGates)) {
			return $realGates[$flightnumber];
		}

		return false;
	}

	function findPierByAirlineIATA($airline) {
		$airlinePiers = $this->getAllAirlinePiers();

		if(array_key_exists($airline, $airlinePiers)) {
			return $airlinePiers[$airline];
		}

		return false;
	}
}

?>