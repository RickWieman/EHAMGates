<?php

class VatsimParser {
	private $serverList = 'http://status.vatsim.net/status.txt';
	private $allPilots = array();
	
	function __construct($useServerList = null) {
		if($useServerList != null) {
			$this->serverList = $useServerList;	
		}
	}

	function lastServerList() {
		return (file_exists('data-vatsim-servers.txt') ? file_get_contents('data-vatsim-servers.txt', NULL, NULL, 0, 10) : 0);
	}

	function lastDataFetch() {
		return (file_exists('data-vatsim.txt') ? file_get_contents('data-vatsim.txt', NULL, NULL, 0, 10) : 0);
	}

	function fetchServerList($force = false) {
		$cacheDuration = 60 * 60;
		
		// Reload only when cache is expired
		if(time() - $this->lastServerList() > $cacheDuration || $force) {
			$data = file_get_contents($this->serverList);
		
			if($data) {
				$data = time() . $data;
				file_put_contents('data-vatsim-servers.txt', $data);

				return $data;
			}
		}
		
		return file_get_contents('data-vatsim-servers.txt');
	}

	function fetchData($force = false, $useServerUrl = null) {
		$cacheDuration = 60 * 2;

		// Reload only when cache is expired
		if(time() - $this->lastDataFetch() > $cacheDuration || $force) {
			if($useServerUrl) {
				$data = file_get_contents($useServerUrl);
			}
			else {
				$serverList = $this->fetchServerList();
				preg_match_all("/url0\=(.*)/", $serverList, $servers);

				$random = rand(0, count($servers[1])-1);
				$server = trim($servers[1][$random]);

				$data = file_get_contents($server);
			}
		
			if($data) {
				$data = time() . $data;
				file_put_contents('data-vatsim.txt', $data);

				return $data;
			}
		}
		
		return file_get_contents('data-vatsim.txt');
	}

	function parseAircraftType($filedAircraftType) {
		return preg_replace('/([A-Z]*\/)?([A-Z0-9\-]*)(\/[A-Z]*)?/', '$2', $filedAircraftType);
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
					$allPilots[$fields[0]] = array(
						'origin' => $fields[11],
						'actype' => $this->parseAircraftType($fields[9]),
						'flightrules' => $fields[21],
						'lat' => $fields[5],
						'long' => $fields[6],
						'groundspeed' => $fields[8]
					);
				}
			}
		}

		$this->allPilots = $allPilots;

		return $allPilots;
	}
}