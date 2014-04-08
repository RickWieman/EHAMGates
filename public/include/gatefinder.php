<?php

require_once('definitions_global.php');
require_once('definitions_eham.php');
require_once('definitions_eham_callsigns.php');
require_once('realgates.php');

class GateFinder {

	private $occupiedGates = array();

	private $realGates;

	function __construct($useData = null) {
		$this->realGates = new RealGates($useData);
	}
	
	function occupyGate($gate) {
		$this->occupiedGates[] = $gate;
	}

	function releaseGate($gate) {
		if(in_array($gate, $this->occupiedGates)) {
			$key = array_search($gate, $this->occupiedGates);
			unset($this->occupiedGates[$key]);
		}
	}

	function isGateOccupied($gate) {
		return in_array($gate, $this->occupiedGates);
	}

	function getFreeGates($aircraftType, $origin) {
		if(Definitions::resolveSchengenOrigin($origin)) {
			$gates = Gates_EHAM::allSchengenGates();
		}
		else {
			$gates = Gates_EHAM::allNonSchengenGates();
		}

		$cargoGates = Gates_EHAM::allCargoGates();
		$gates = array_merge($gates, $cargoGates);

		$extraGates = Gates_EHAM::getExtraGates($aircraftType);
		$excludedGates = Gates_EHAM::getExcludedGates($aircraftType);

		$aircraftCat = Definitions::resolveAircraftCat($aircraftType);
		
		$freeGates = array();

		foreach($gates as $gate => $cat) {
			if(!$this->isGateOccupied($gate) && !in_array($gate, $excludedGates) 
				&& (($cat >= $aircraftCat) || in_array($gate, $extraGates))) {
				$freeGates[$gate] = $cat;
			}
		}

		return $freeGates;
	}

	function findGate($callsign, $aircraftType, $origin) {
		if(!preg_match('/^[A-Z]{2,3}\d+[A-Z]*$/', $callsign)) {
			return false;
		}

		// Determine whether this is a real flight
		$realGate = $this->findRealGate($callsign);

		if($realGate) {
			if($realGate == 'UNKNOWN') {
				$match = 'RL_NOTYET';
			}
			else {
				$allGates = Gates_EHAM::allGates();

				// Only return the real gate if the actual aircraft type can use that gate!
				if($allGates[$realGate] >= Definitions::resolveAircraftCat($aircraftType)) {
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

		// Find a plausible cargo gate
		$gate = $this->findCargoGate($callsign, $aircraftType);

		if($gate) {
			return array('gate' => $gate, 'match' => 'CARGO');
		}

		// Find a plausible civil gate
		$gate = $this->findCivilGate($callsign, $aircraftType, $origin);

		if($gate) {
			$match = (isset($match)) ? $match : 'RANDOM';
			return array('gate' => $gate, 'match' => $match);
		}

		return array('gate' => null, 'match' => 'NONE');
	}

	/* 
	 * Finds the real gate with multiple tries:
	 * - ICAO flight number (with up to 4 zeroes)
	 *     TRA123 -> HV (0)123
	 * - IATA flight number (with up to 4 zeroes)
	 *     EZY123 -> EZY (0)123
	*/
	function findRealGate($callsign, $useIATA = true) {
		$flightnumber = Callsigns_EHAM::convertAlphanumeric($callsign);
		
		if(!$flightnumber) {
			$flightnumber = preg_replace('/^([A-Z]{2,3})/', '$1 ', $callsign);

			if($useIATA) {
				if(preg_match('/^[A-Z]{3}/', $callsign, $airlineICAO)) {
					$airlineIATA = Definitions::convertAirlineICAOtoIATA($airlineICAO[0]);

					$flightnumber = preg_replace('/^[A-Z]{3}/', $airlineIATA . ' ', $callsign);
				}
			}
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
		if(!$gate && $useIATA) {
			return $this->findRealGate($callsign, false);
		}

		return $gate;
	}

	function findCargoGate($callsign, $aircraftType) {
		$cargoGates = Gates_EHAM::resolveCargoAirlineGate($callsign);
		
		if($cargoGates) {
			foreach($cargoGates as $gate) {
				if(!in_array($gate, $this->occupiedGates)) {
					return $gate;
				}
			}
		}

		return false;
	}

	function findCivilGate($callsign, $aircraftType, $origin) {
		$defaultGate = Gates_EHAM::resolveAirlineGate($callsign);
		$cat = Definitions::resolveAircraftCat($aircraftType);

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