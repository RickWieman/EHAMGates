<?php

require_once('definitions.php');
require_once('realgates.php');

class GateFinder {

	private $occupiedGates = array();

	private $realGates;

	function __construct($useData = null) {
		$this->realGates = new RealGates($useData);
	}
	
	function resolveAircraftCat($aircraftType) {
		if(array_key_exists($aircraftType, Gates_EHAM::$aircraftCategories)) {
			return Gates_EHAM::$aircraftCategories[$aircraftType];
		}

		return false;
	}

	function resolveAirlineGate($callsign) {
		preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);

		if(array_key_exists($airlineIATA[0], Gates_EHAM::$airlinesDefaultGates)) {
			return Gates_EHAM::$airlinesDefaultGates[$airlineIATA[0]];
		}

		return false;
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
		$this->occupiedGates[] = $gate;
	}

	function isGateOccupied($gate) {
		return in_array($gate, $this->occupiedGates);
	}

	function getFreeGates($aircraftType, $origin) {
		if($this->resolveSchengenOrigin($origin)) {
			$gates = Gates_EHAM::allSchengenGates();
		}
		else {
			$gates = Gates_EHAM::allNonSchengenGates();
		}

		$aircraftCat = $this->resolveAircraftCat($aircraftType);

		$freeGates = array();

		foreach($gates as $gate => $cat) {
			if(!$this->isGateOccupied($gate) && $cat >= $aircraftCat) {
				$freeGates[$gate] = $cat;
			}
		}

		return $freeGates;
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
				$allGates = Gates_EHAM::allGates();

				// Only return the real gate if the actual aircraft type can use that gate!
				if($allGates[$realGate] >= $this->resolveAircraftCat($aircraftType)) {
					return $realGate;
				}
			}

			// Find a plausible civil gate
			return $this->findCivilGate($callsign, $aircraftType, $origin);
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

			if(array_key_exists($airlineIATA[0], Gates_EHAM::$airlinesIATA)) {
				$airlineICAO = Gates_EHAM::$airlinesIATA[$airlineIATA[0]];

				$flightnumber = preg_replace('/^[A-Z]{3}/', $airlineICAO . ' ', $callsign);
			}
			else {
				$flightnumber = preg_replace('/^([A-Z]{3})/', '$1 ', $callsign);
			}
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

		if(array_key_exists($airlineIATA[0], Gates_EHAM::$cargoGates)) {
			foreach(Gates_EHAM::$cargoGates[$airlineIATA[0]] as $gate) {
				if(!in_array($gate, $this->occupiedGates)) {
					return $gate;
				}
			}
		}

		return false;
	}

	function findCivilGate($callsign, $aircraftType, $origin) {
		$defaultGate = $this->resolveAirlineGate($callsign);
		$cat = $this->resolveAircraftCat($aircraftType);

		if($defaultGate) {
			$availableGates = $this->getFreeGates($aircraftType, $origin);

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
				$foundGates = array_keys($matches);
				return $foundGates[0];
			}
		}

		return false;
	}

}

?>