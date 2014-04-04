<?php

class Definitions {
	private	static $airlinesICAOtoIATA = array(
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
		
		'A319' => 4,
		'A320' => 4,
		'A321' => 4,
		'B736' => 4,
		'B737' => 4,
		'B738' => 4,
		'B739' => 4,
		'B722' => 4,
		'DC9' => 4,
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
		'IL96' => 7,

		'CONC' => 8,
		'B772' => 8,
		'B773' => 8,
		'B77L' => 8,
		'B77W' => 8,
		'B744' => 8,
		'B748' => 8,
		'B74D' => 8,
		'B74R' => 8,
		'B74S' => 8,
		'B783' => 8,
		'B788' => 8,
		'B789' => 8,
		'A380' => 8,
		'A388' => 8
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

	static function getAllAircraft() {
		return self::$aircraftCategories;
	}

	static function resolveAircraftCat($aircraftType) {
		if(array_key_exists($aircraftType, self::$aircraftCategories)) {
			return self::$aircraftCategories[$aircraftType];
		}

		return false;
	}

	static function resolveSchengenOrigin($origin) {
		// To allow non-specified origin
		if($origin == "schengen") {
			return true;
		}
		if($origin == "nonschengen") {
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
}