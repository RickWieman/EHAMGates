<?php

class Callsigns {
	private static $extraCallsignsFile = 'data-callsigns.txt';
	private static $retention = 3600; // 60 minutes

	static function findFlightnumber($callsign) {
		$getFlightnumber = file_get_contents('http://planefinder.net/data/endpoints/search_ajax.php?searchText=' . $callsign);
		$getFlightnumber = json_decode($getFlightnumber, true);

		if($getFlightnumber) {
			foreach($getFlightnumber['flights'] as $data) {
				if($data['subtitle'] == $callsign) {
					return preg_replace('/^([A-Z0-9]{2})/', '$1 ', $data['title']);
				}
			}
		}

		return false;
	}

	static function getCallsignsFromFile() {
		$data = (file_exists(self::$extraCallsignsFile)) ? file_get_contents(self::$extraCallsignsFile) : '{}';
		$data = json_decode($data, true);

		return $data;
	}

	static function addFlightnumber($callsign, $flightnumber) {
		$data = self::getCallsignsFromFile();
		$data[$callsign]['flightnumber'] = $flightnumber;
		$data[$callsign]['added'] = time();

		file_put_contents(self::$extraCallsignsFile, json_encode($data));

		return $data[$callsign];
	}

	static function convertAlphanumeric($callsign) {
		$storedCallsigns = self::getCallsignsFromFile();

		if(array_key_exists($callsign, $storedCallsigns)) {
			$data = $storedCallsigns[$callsign];

			if($data['added'] + self::$retention < time()) {
				$findRemote = self::findFlightnumber($callsign);

				if($findRemote) {
					$data = self::addFlightnumber($callsign, $findRemote);
				}
				else {
					$data = self::addFlightnumber($callsign, $data['flightnumber']);
				}
			}

			return $data['flightnumber'];
		}
		
		$findRemote = self::findFlightnumber($callsign);
		self::addFlightnumber($callsign, $findRemote);
		
		return $findRemote;
	}
}
