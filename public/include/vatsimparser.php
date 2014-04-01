<?php

class VatsimParser {
	private $dataSource = 'http://info.vroute.net/vatsim-data.txt';
	private $allPilots = array();
	
	function __construct($useData = null) {
		if($useData != null) {
			$this->dataSource = $useData;	
		}
	}

	function fetchData() {
		$cacheDuration = 60 * 5;
		$stamp = (file_exists('data-vatsim.txt') ? file_get_contents('data-vatsim.txt', NULL, NULL, 0, 10) : 0);

		// Reload only when cache is expired
		if(time() - $stamp > $cacheDuration) {
			$data = file_get_contents($this->dataSource);
		
			file_put_contents('data-vatsim.txt', time() . $data);

			return $data;
		}
		
		return file_get_contents('data-vatsim.txt');
	}

	function parseData() {
		$data = $this->fetchData();

		$data = explode('!CLIENTS:', $data);
		$data = explode('!SERVERS:', $data[1]);
		$data = explode("\n", $data[0]);

		$allPilots = array();

		/*
		PILOT DATA CONTAINS:
		callsign
		cid
		realname
		clienttype
		frequency
		latitude
		longitude
		altitude
		groundspeed
		planned_aircraft
		planned_tascruise
		planned_depairport
		planned_altitude
		planned_destairport
		server
		protrevision
		rating
		transponder
		facilitytype
		visualrange
		planned_revision
		planned_flighttype
		planned_deptime
		planned_actdeptime
		planned_hrsenroute
		planned_minenroute
		planned_hrsfuel
		planned_minfuel
		planned_altairport
		planned_remarks
		planned_route
		planned_depairport_lat
		planned_depairport_lon
		planned_destairport_lat
		planned_destairport_lon
		atis_message
		time_last_atis_received
		time_logon
		heading
		QNH_iHg
		QNH_Mb
		*/
		foreach($data as $client) {
			$fields = explode(':', $client);

			if(array_key_exists(3, $fields) && $fields[3] == 'PILOT') {
				if($fields[13] == 'EHAM') {
					$allPilots[$fields[0]] = $fields[11];
				}
			}
		}

		$this->allPilots = $allPilots;

		return $allPilots;
	}
}
?>