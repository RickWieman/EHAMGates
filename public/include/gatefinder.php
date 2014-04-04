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
		preg_match('/^[A-Z]{2,3}/', $callsign, $airlineIATA);

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
		// If there are extra gates for the specified aircraft, load the array
		$extraGates = array();
		if(array_key_exists($aircraftType, Gates_EHAM::$aircraftExtraGates)) {
			$extraGates = Gates_EHAM::$aircraftExtraGates[$aircraftType];
		}

		// If there are gates the specified aircraft cannot use, load the array
		$withoutGates = array();
		if(array_key_exists($aircraftType, Gates_EHAM::$aircraftNotOnGates)) {
			$withoutGates = Gates_EHAM::$aircraftNotOnGates[$aircraftType];
		}

		// Get gates based on Schengen/Non-Schengen
		if($this->resolveSchengenOrigin($origin)) {
			$gates = Gates_EHAM::allSchengenGates();
		}
		else {
			$gates = Gates_EHAM::allNonSchengenGates();
		}

		$aircraftCat = $this->resolveAircraftCat($aircraftType);

		$cargoGates = array_fill_keys(Gates_EHAM::allCargoGates(), 8);
		$gates = array_merge($gates, $cargoGates);

		$freeGates = array();

		foreach($gates as $gate => $cat) {
			if(!$this->isGateOccupied($gate) && !in_array($gate, $withoutGates) 
				&& (($cat >= $aircraftCat) || in_array($gate, $extraGates))) {
				$freeGates[$gate] = $cat;
			}
		}

		return $freeGates;
	}

	function gateMatchIcon($matchText) {
		switch($matchText) {
			case 'CARGO':
				return 'shopping-cart';
			case 'RL':
				return 'eye-open';
			case 'RL_HEAVY':
				return 'plane';
			case 'RL_NOTYET':
				return 'eye-close';
			case 'RANDOM':
				return 'glyphicon-list-alt';
		}
	}

	function findGate($callsign, $aircraftType, $origin) {
		preg_match('/^[A-Z]{2,3}/', $callsign, $airlineIATA);

		// Determine whether flight is cargo or civil
		if(array_key_exists($airlineIATA[0], Gates_EHAM::$cargoGates)) {
			$gate = $this->findCargoGate($callsign, $aircraftType);

			return array('gate' => $gate, 'match' => 'CARGO');
		}
		else {
			$match = 'RANDOM';

			// Determine whether this is a real flight
			$realGate = $this->findRealGate($callsign);

			if($realGate) {
				if($realGate == 'UNKNOWN') {
					$match = 'RL_NOTYET';
				}
				else {
					$allGates = Gates_EHAM::allGates();

					// Only return the real gate if the actual aircraft type can use that gate!
					if($allGates[$realGate] >= $this->resolveAircraftCat($aircraftType)) {
						if($this->isGateOccupied($realGate)) {
							$match = 'RL_OCCUPIED';
						}
						else {
							return array('gate' => $realGate, 'match' => 'RL');
						}
					}
					else {
						$match = 'RL_HEAVY';
					}
				}
			}

			// Find a plausible civil gate
			$gate = $this->findCivilGate($callsign, $aircraftType, $origin);

			if(!$gate) {
				$match = 'NONE';
			}

			return array('gate' => $gate, 'match' => $match);
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
			preg_match('/^[A-Z]{2,3}/', $callsign, $airlineIATA);

			if(array_key_exists($airlineIATA[0], Gates_EHAM::$airlinesIATA)) {
				$airlineICAO = Gates_EHAM::$airlinesIATA[$airlineIATA[0]];

				$flightnumber = preg_replace('/^[A-Z]{2,3}/', $airlineICAO . ' ', $callsign);
			}
			else {
				$flightnumber = preg_replace('/^([A-Z]{2,3})/', '$1 ', $callsign);
			}
		}
		else {
			$flightnumber = preg_replace('/^([A-Z]{2,3})/', '$1 ', $callsign);
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
		preg_match('/^[A-Z]{2,3}/', $callsign, $airlineIATA);

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