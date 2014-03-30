<?php

require_once('definitions.php');
require_once('realgates.php');

class GateFinder {

	// For future use: one can mark gates as occupied.
	// These gates will then not be returned by the findGate function.
	private $occupiedGates = array();

	private $realGates;

	function __construct() {
		$this->realGates = new RealGates();
	}
	
	function resolveAircraftCat($aircraftType) {
		return Gates_EHAM::$aircraftCategories[$aircraftType];
	}

	function resolveAirlineGate($callsign) {
		preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);

		return Gates_EHAM::$airlinesDefaultGates[$airlineIATA[0]];
	}

	function resolveSchengenOrigin($origin) {
		// To allow non-specified origin
		if($origin == "schengen") {
			return true;
		}
		if($origin == "nonschengen") {
			return false;
		}

		return in_array(substr($origin, 0, 2), Gates_EHAM::$schengen);
	}

	function occupyGate($gate) {
		$occupiedGates[] = $gate;
	}

	function findGate($callsign, $aircraftType, $origin) {
		preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);

		// Determine whether flight is cargo or civil
		if(array_key_exists($airlineIATA[0], Gates_EHAM::$cargoGates)) {
			return $this->findCargoGate($callsign, $aircraftType);
		}
		else {

			// Determine whether this is a real flight
			$realGate = $this->findRealGate($callsign);

			if($realGate) {
				$allGates = array_merge(Gates_EHAM::$bravoApron, Gates_EHAM::$schengenGates, Gates_EHAM::$schengenNonSchengenGates,
					Gates_EHAM::$nonSchengenGates);

				// Only return the real gate if the actual aircraft type can use that gate!
				if($allGates[$realGate] >= $this->resolveAircraftCat($aircraftType)) {
					return $realGate;
				}
			}

			// Determine whether flight origins from Schengen country
			if($this->resolveSchengenOrigin($origin)) {
				$allSchengenGates = array_merge(Gates_EHAM::$bravoApron, Gates_EHAM::$schengenGates,
					Gates_EHAM::$schengenNonSchengenGates);

				return $this->findCivilGate($allSchengenGates, $callsign, $aircraftType);
			}
			else {
				$allNonSchengenGates = array_merge(Gates_EHAM::$schengenNonSchengenGates, Gates_EHAM::$nonSchengenGates);

				return $this->findCivilGate($allNonSchengenGates, $callsign, $aircraftType);
			}
		}
	}

	/* 
	 * Finds the real gate with multiple tries:
	 * - ICAO flight number (with up to 4 zeroes)
	 *     TRA123 -> HV (0)123
	 * - IATA flight number (with up to 4 zeroes)
	 *     EZY123 -> EZY (0)123
	*/
	function findRealGate($callsign, $useICAO = true) {
		// TODO: If $callsign alphanumeric, convert $callsign to numeric

		if($useICAO) {
			preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);
			$airlineICAO = Gates_EHAM::$airlinesIATA[$airlineIATA[0]];

			$flightnumber = preg_replace('/^[A-Z]{3}/', $airlineICAO . ' ', $callsign);
		}
		else {
			$flightnumber = preg_replace('/^([A-Z]{3})/', '$1 ', $callsign);
		}

		$gate = $this->realGates->findGateByFlightnumber($flightnumber);

		// If failed, try with more zeroes
		$i = 0;
		while(!$gate && $i <= 4) {
			$flightnumber = explode(' ', $flightnumber);
			$i = strlen($flightnumber[1]) + 1;
			$flightnumber[1] = sprintf("%0" . $i . "d", $flightnumber[1]);
			$flightnumber = implode(' ', $flightnumber);

			$gate = $this->realGates->findGateByFlightnumber($flightnumber);			
		}

		// If still failed, try using original callsign
		if(!$gate && $useICAO) {
			return $this->findRealGate($callsign, false);
		}

		return $gate;
	}

	function findCargoGate($callsign, $aircraftType) {
		preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);

		// Find a free cargo gate
		foreach(Gates_EHAM::$cargoGates[$airlineIATA[0]] as $gate) {
			if(!in_array($gate, $this->occupiedGates)) {
				return $gate;
			}
		}

		return false;
	}

	function findCivilGate($allGates, $callsign, $aircraftType) {
		$defaultGate = $this->resolveAirlineGate($callsign);
		$cat = $this->resolveAircraftCat($aircraftType);

		// First determine the available gates
		$availableGates = array();

		foreach($allGates as $gate => $gateCat) {
			if($gateCat >= $cat && !in_array($gate, $this->occupiedGates)) {
				$availableGates[$gate] = $gateCat;
			}
		}

		// Determine which gates are available for the airline
		$matches = array();
		foreach($defaultGate as $pier) {
			foreach($availableGates as $gate => $gateCat) {
				if(substr($gate, 0, 1) == $pier) {
					$matches[$gate] = $gateCat;
				}
			}
		}

		// Sort the available gates, based on their category
		// We do not want to use cat. 8 gates for cat. 2 aircraft if lower cat. gates are available
		asort($matches);

		// Return the first of the available gates
		if(count($matches) > 0) {
			return array_keys($matches)[0];
		}

		return false;
	}

}

?>