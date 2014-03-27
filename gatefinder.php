<?php

require('definitions.php');

class GateFinder {

	// For future use: one can mark gates as occupied.
	// These gates will then not be returned by the findGate function.
	private $occupiedGates = array('C15', 'E02');
	
	function resolveAircraftCat($aircraftType) {
		return Gates_EHAM::$aircraftCategories[$aircraftType];
	}

	function resolveAirlineGate($callsign) {
		preg_match('/^[A-Z]{3}/', $callsign, $airlineIATA);

		return Gates_EHAM::$airlinesDefaultGates[$airlineIATA[0]];
	}

	function resolveSchengenOrigin($origin) {
		return in_array(substr($origin, 0, 2), Gates_EHAM::$schengen);
	}

	function findGate($callsign, $aircraftType, $origin) {
		if($this->resolveSchengenOrigin($origin)) {
			return $this->findSchengenGate($callsign, $aircraftType);
		}
		else {
			// TODO: Assign Non-Schengen gate!
		}
	}

	function findSchengenGate($callsign, $aircraftType) {
		// TODO: Schengen range check!

		$defaultGate = $this->resolveAirlineGate($callsign);
		$cat = $this->resolveAircraftCat($aircraftType);

		// First determine the available gates
		$availableGates = array();

		foreach(Gates_EHAM::$gates as $gate => $gateCat) {
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