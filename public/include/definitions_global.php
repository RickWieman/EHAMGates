<?php

class Definitions {
	private	static $airlinesICAOtoIATA = array(
		'ADH' => 'AP', # Air One
		'ADR' => 'JP', # Adria Airways
		'AEA' => 'UX', # Air Europa
		'AFL' => 'SU', # Aeroflot
		'AFR' => 'AF', # Air France
		'AMC' => 'KM', # Air Malta
		'ASA' => 'AS', # Alaska Airlines
		'AUA' => 'OS', # Austrian Airlines
		'AUI' => 'PS', # Ukraine International Airlines
		'AZA' => 'AZ', # Alitalia
		'BAW' => 'BA', # British Airways
		'BEE' => 'BE', # Flybe
		'BER' => 'AB', # Air Berlin
		'BTI' => 'BT', # Air Baltic
		'CAI' => 'CAI', # Corendon Airlines
		'CAL' => 'CI', # China Airlines
		'CPE' => 'CPE', # (unknown)
		'CSA' => 'OK', # Czech Airlines
		'CSN' => 'CZ', # China Southern Airlines
		'CTN' => 'OU', # Croatia Airlines
		'CYP' => 'CY', # Cyprus Airways
		'DAL' => 'DL', # Delta Air Lines
		'DLH' => 'LH', # Lufthansa
		'EAL' => 'EA', # European Air Express
		'EIN' => 'EI', # Aer Lingus
		'ELL' => 'OV', # Estonian Air
		'ELO' => 'K2', # Eurolot
		'ELY' => 'LY', # El Al Israel Airlines
		'ETD' => 'EY', # Etihad Airways
		'EXS' => 'LS', # Jet2.com
		'EZY' => 'U2', # easyJet
		'FBF' => 'FBF', # Fine Airlines
		'FIN' => 'AY', # Finnair
		'GAV' => 'GAV', # Granada Aviación
		'IBS' => 'I2', # Iberia Express
		'ICE' => 'FI', # Icelandair
		'IRA' => 'IR', # Iran Air
		'JAT' => 'JU', # Jat Airways
		'KAL' => 'KE', # Korean Air
		'KLM' => 'KL', # KLM
		'KZR' => 'KC', # Air Astana
		'MAC' => '3O', # Air Arabia Maroc (?)
		'MAS' => 'MH', # Malaysia Airlines
		'MSR' => 'MS', # Egyptair
		'NAX' => 'DY', # Norwegian Air Shuttle
		'PGT' => 'PC', # Pegasus Airlines
		'RAM' => 'AT', # Royal Air Maroc
		'ROT' => 'RO', # Tarom
		'SAS' => 'SK', # Scandinavian Airlines
		'SIA' => 'SQ', # Singapore Airlines
		'SWR' => 'LX', # Swiss International Air Lines
		'SXS' => 'XQ', # SunExpress
		'TAP' => 'TP', # TAP Portugal
		'IBE' => 'IB', # Iberia Airlines
		'TFL' => 'OR', # Arkefly
		'THY' => 'TK', # Turkish Airlines
		'TRA' => 'HV', # Transavia Holland
		'UAE' => 'EK', # Emirates Airline
		'UAL' => 'UA', # United Airlines
		'VLG' => 'VY'  # Vueling Airlines
	);

	private static $aircraftCategories = array(
		'BE40' => 1,
		'H25B' => 1,
		'BA11' => 1,
		'CNJ' => 1,

		'DH8A' => 2,
		'DH8B' => 2,
		'DH8C' => 2,
		'DH8D' => 2,
		'F50' => 2,
		'F60' => 2,
		'AT43' => 2,
		'AT44' => 2,
		'AT45' => 2,
		'AT72' => 2,
		'SB20' => 2,
		'ATP' => 2,
		'JS41' => 2,

		'F70' => 3,
		'F100' => 3,
		'B731' => 3,
		'B732' => 3,
		'B733' => 3,
		'B734' => 3,
		'B735' => 3,
		'E135' => 3,
		'E145' => 3,
		'E170' => 3,
		'E190' => 3,
		'B461' => 3,
		'B462' => 3,
		'B463' => 3,
		'T134' => 3,
		'A318' => 3,
		'CRJ1' => 3,
		'CRJ2' => 3,
		'CRJ7' => 3,
		'CRJ9' => 3,
		'CRJX' => 3,
		
		'A318' => 4,
		'A319' => 4,
		'A320' => 4,
		'A321' => 4,
		'B736' => 4,
		'B737' => 4,
		'B738' => 4,
		'B739' => 4,
		'B722' => 4,
		'DC9' => 4,
		'E195' => 4,
		'MD80' => 4,
		'MD81' => 4,
		'MD82' => 4,
		'MD83' => 4,
		'MD87' => 4,
		'MD88' => 4,
		'YK42' => 4,

		'B752' => 5,
		'MD90' => 5,
		'A310' => 5,
        
		'T154' => 5,
		'T204' => 5,
		'B753' => 6,
		'B762' => 6,
		'B763' => 6,
		'B764' => 6,
		'A300' => 6,
		'A306' => 6,
		'A30B' => 6,
		'L101' => 6,
		'DC10' => 6,

		'MD11' => 7,
		'A330' => 7,
		'A332' => 7,
		'A333' => 7,
		'A340' => 7,
		'A342' => 7,
		'A343' => 7,
		'A345' => 7,
		'A346' => 7,
		'B741' => 7,
		'B742' => 7,
		'B743' => 7,
		'B77L' => 7,
		'IL96' => 7,

		'CONC' => 8,
		'B772' => 8,
		'B773' => 8,
		'B77F' => 8,
		'B77W' => 8,
		'B744' => 8,
		'B748' => 8,
		'B74D' => 8,
		'B74R' => 8,
		'B74S' => 8,
		'B783' => 8,
		'B788' => 8,
		'B789' => 8,

		'A388' => 9,
		'B748' => 9
	);

	private static $invalidAircraftTypeMapping = array(
		'732' => 'B732',
		'733' => 'B733',
		'734' => 'B734',
		'735' => 'B735',
		'736' => 'B736',
		'737' => 'B737',
		'738' => 'B738',
		'739' => 'B739',
		'737-200' => 'B732',
		'737-300' => 'B733',
		'737-400' => 'B734',
		'737-500' => 'B735',
		'737-600' => 'B736',
		'737-700' => 'B737',
		'737-800' => 'B738',
		'737-900' => 'B739',
		'747' => 'B744',
		'B747' => 'B744',
		'742' => 'B742',
		'743' => 'B743',
		'744' => 'B744',
		'748' => 'B748',
		'747-200' => 'B742',
		'747-300' => 'B743',
		'747-400' => 'B744',
		'747-800' => 'B748',
		'752' => 'B752',
		'753' => 'B753',
		'757-200' => 'B752',
		'757-300' => 'B753',
		'762' => 'B762',
		'763' => 'B763',
		'764' => 'B764',
		'767-200' => 'B762',
		'767-300' => 'B763',
		'767-400' => 'B764',
		'777' => 'B772',
		'772' => 'B772',
		'B77F' => 'B77L',
		'777F' => 'B77L',
		'777L' => 'B77L',
		'77L' => 'B77L',
		'773' => 'B773',
		'777W' => 'B77W',
		'777-200' => 'B772',
		'777-200LR' => 'B77L',
		'777-300' => 'B773',
		'777-300ER' => 'B77W',
		'312' => 'A310',
		'313' => 'A310',
		'A310-200' => 'A310',
		'A310-300' => 'A310',
		'318' => 'A318',
		'319' => 'A319',
		'320' => 'A320',
		'A320-100' => 'A320',
		'A320-200' => 'A320',
		'321' => 'A321',
		'A321-100' => 'A321',
		'A321-200' => 'A321',
		'330' => 'A330',
		'332' => 'A332',
		'333' => 'A333',
		'A330-200' => 'A332',
		'A330-300' => 'A333',
		'342' => 'A342',
		'343' => 'A343',
		'345' => 'A345',
		'346' => 'A346',
		'A340-200' => 'A342',
		'A340-300' => 'A343',
		'A340-500' => 'A345',
		'A340-600' => 'A346',
		'T20' => 'T204',
		'Tu-204' => 'T204',
		'Tu-214' => 'T204',
		'TU3' => 'T134',
		'Tu134' => 'T134',
		'TU5' => 'T154',
		'Tu154' => 'T154',
		'A380' => 'A388'
	);

	private static $schengen = array(
		'EB', # Belgium
		'EK', # Denmark
		'ED', # Germany (Civil)
		'ET', # Germany (Military)
		'EE', # Estonia
		'EP', # Poland
		'EF', # Finland
		'LF', # France
		'LG', # Greece
		'LH', # Hungary
		'LJ', # Slovenia
		'BI', # Iceland
		'LI', # Italy
		'EL', # Luxembourg
		'EV', # Latvia
		'LZ', # Slovakia
		'EH', # Netherlands
		'EN', # Norway
		'LO', # Austria
		'EY', # Lithuania
		'LK', # Czech Republic
		'LP', # Portugal
		'LE', # Spain
		'ES', # Sweden
		'LM', # Malta
		'LS'  # Switzerland
	);

	private static $matchTypes = array(
		'CARGO'			=> array('icon' => 'shopping-cart',
								 'text' => 'This is a cargo flight (based on callsign).'),
		'RL' 			=> array('icon' => 'eye-open',
								 'text' => 'Real life flight!'),
		'RL_BUS'		=> array('icon' => 'transfer',
								 'text' => 'Real life flight on a bus gate.'),
		'RL_HEAVY'		=> array('icon' => 'plane',
								 'text' => 'Real life flight, but the aircraft type is too heavy for actual gate.'),
		'RL_NOTYET'		=> array('icon' => 'eye-close',
								 'text' => 'Real life flight, but no real life gate available yet.'),
		'RL_OCCUPIED'	=> array('icon' => 'flash',
								 'text' => 'Real life flight, but the actual gate is occupied.'),
		'RANDOM'		=> array('icon' => 'list-alt',
								 'text' => 'Based on airline defaults and aircraft category.'),
		'MANUAL'		=> array('icon' => 'user',
								 'text' => 'Assigned manually.')
	);

	static function getAllAircraft() {
		return self::$aircraftCategories;
	}

	static function resolveAircraftCat($aircraftType) {
		if(array_key_exists($aircraftType, self::$invalidAircraftTypeMapping)) {
			$aircraftType = self::$invalidAircraftTypeMapping[$aircraftType];
		}

		if(array_key_exists($aircraftType, self::$aircraftCategories)) {
			return self::$aircraftCategories[$aircraftType];
		}

		return false;
	}

	static function resolveSchengenOrigin($origin) {
		// To allow non-specified origin
		if(strtolower($origin) == 'schengen') {
			return true;
		}
		if(strtolower($origin) == 'nonschengen') {
			return false;
		}

		return in_array(substr($origin, 0, 2), self::$schengen);
	}

	static function convertAirlineICAOtoIATA($airline) {
		if(array_key_exists($airline, self::$airlinesICAOtoIATA)) {
			return self::$airlinesICAOtoIATA[$airline];
		}

		return $airline;
	}

	static function convertAirlineIATAtoICAO($airline) {
		if(in_array($airline, self::$airlinesICAOtoIATA)) {
			return array_search($airline, self::$airlinesICAOtoIATA);
		}

		return $airline;
	}
	
	static function resolveAirlineCode($callsign) {
		if(!preg_match('/^[A-Z]{3}/', $callsign, $airlineCode)) {
			if(!preg_match('/^[A-Z]{2}/', $callsign, $airlineCode)) {
				return false;
			}
			return Definitions::convertAirlineIATAtoICAO($airlineCode[0]);
		}
		
		return $airlineCode[0];
	}

	static function resolveMatchTypeIcon($matchType) {
		if(array_key_exists($matchType, self::$matchTypes)) {
			return self::$matchTypes[$matchType]['icon'];
		}

		return 'warning-sign';
	}

	static function resolveMatchTypeText($matchType) {
		if(array_key_exists($matchType, self::$matchTypes)) {
			return self::$matchTypes[$matchType]['text'];
		}

		return 'The gate could not be determined.';
	}

	static function getAllMatchTypes() {
		return self::$matchTypes;
	}

}