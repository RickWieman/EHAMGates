<?php
require_once('definitions_global.php');

class Gates_EHAM {
	
	private static $aircraftExtraGates = array(
		// Bravo Apron
		'B463' => array('B91','B92','B93','B94','B95'),
		'CL60' => array('B51','B52','B53','B54','B55',
						'B61','B62','B63','B64','B65','B66',
						'B71','B72','B73','B74','B75','B76',
						'B81','B82','B83','B84','B85',
						'B91','B92','B93','B94','B95'),
		'CRJ7' => array('B91','B92','B93','B94','B95'),
		'CRJ9' => array('B91','B92','B93','B94','B95'),
		'E135' => array('B51','B52','B53','B54','B55',
						'B61','B62','B63','B64','B65','B66',
						'B71','B72','B73','B74','B75','B76',
						'B81','B82','B83','B84','B85',
						'B91','B92','B93','B94','B95'),
		'E145' => array('B51','B52','B53','B54','B55',
						'B61','B62','B63','B64','B65','B66',
						'B71','B72','B73','B74','B75','B76',
						'B81','B82','B83','B84','B85',
						'B91','B92','B93','B94','B95'),
		'E170' => array('B51','B52','B53','B54','B55',
						'B61','B62','B63','B64','B65','B66',
						'B71','B72','B73','B74','B75','B76',
						'B81','B82','B83','B84','B85',
						'B91','B92','B93','B94','B95'),
		'E190' => array('B51','B52','B53','B54','B55',
						'B91','B92','B93','B94','B95'),
		'F70' => array('B51','B52','B53','B54','B55',
						'B61','B62','B63','B64','B65','B66',
						'B71','B72','B73','B74','B75','B76',
						'B81','B82','B83','B84','B85',
						'B91','B92','B93','B94','B95'),
		'F100' => array('B51','B52','B53','B54','B55',
						'B91','B92','B93','B94','B95'),

		// Other gates	
		'A330' => array('E05'),
		'A332' => array('E02','E05'),
		'A333' => array('E02','E05'),
		'A340' => array('E05'),
		'A342' => array('E05'),
		'A343' => array('E05'),
		'A380' => array('E18','G09'),
		'A388' => array('E18','G09'),
		'B731' => array('D14'),
		'B732' => array('D14'),
		'B733' => array('D14'),
		'B734' => array('D14'),
		'B735' => array('D14'),
		'B736' => array('D14'),
		'B737' => array('D14'),
		'B738' => array('D14'),
		'B739' => array('D14'),
		'B744' => array('D43'),
		'B752' => array('C05','C07','C09'),
		'B753' => array('C05','C07','C09'),
		'B764' => array('E05'),
		'B772' => array('D07','D43','D47','D57','E02','E05','E06'),
		'E195' => array('D14'),
		'MD11' => array('D02','D51','D53','E02','E03','E05'),
		'MD87' => array('D14'),
		'MD93' => array('B23','B27','D16','D18','D22','D24','D26','D28')
	);

	private static $aircraftNotOnGates = array(
		'A318'   => array('D56'),
		'A319'   => array('D56'),
		'A320'   => array('D56'),
		'A321'   => array('C15','C18','D23','D56'),
		'BAE146' => array('C07'),
		'B773'   => array('F02','G04','G06','G08'),
		'MD80'   => array('C15'),
		'MD82'   => array('C16'),
		'MD87'   => array('C15')

	);

	private static $airlinesDefaultGates = array(
		'ADH' => array('H'),
		'ADR' => array('B'),
		'AEA' => array('C'),
		'AFL' => array('G'),
		'AFR' => array('C'),
		'AMC' => array('B'),
		'ASA' => array('E'),
		'AUA' => array('B'),
		'AUI' => array('D'),
		'AZA' => array('B', 'C'),
		'BAW' => array('D'),
		'BEE' => array('D'),
		'BER' => array('H'),
		'BTI' => array('B'),
		'CAI' => array('G'),
		'CAL' => array('F'),
		'CPE' => array('G'),
		'CSA' => array('D'),
		'CSN' => array('D', 'E', 'F'),
		'CTN' => array('D'),
		'CYP' => array('D', 'G'),
		'DAL' => array('B', 'C', 'E'),
		'DLH' => array('B'),
		'EAL' => array('G'),
		'EIN' => array('D'),
		'ELL' => array('B'),
		'ELO' => array('B'),
		'ELY' => array('G'),
		'ETD' => array('D'),
		'EXS' => array('H'),
		'EZY' => array('H'),
		'FBF' => array('D'),
		'FIN' => array('B'),
		'GAV' => array('H'),
		'IBS' => array('D'),
		'ICE' => array('C'),
		'IRA' => array('E'),
		'JAT' => array('D'),
		'KAL' => array('R'),
		'KLM' => array('B', 'C', 'D', 'E', 'F', 'G'),
		'KZR' => array('D'),
		'MAC' => array('D', 'G'),
		'MAS' => array('G'),
		'MSR' => array('G'),
		'NAX' => array('D', 'H'),
		'PGT' => array('G'),
		'RAM' => array('G'),
		'ROT' => array('D'),
		'SAS' => array('C'),
		'SIA' => array('G'),
		'SWR' => array('B'),
		'SXS' => array('D'),
		'TAP' => array('B'),
		'IBE' => array('B'),
		'TFL' => array('B', 'D', 'E', 'G'),
		'THY' => array('G'),
		'TRA' => array('C', 'D', 'E'),
		'UAE' => array('G'),
		'UAL' => array('E', 'G'),
		'VLG' => array('B')
	);

	private static $cargoAirlinesDefaultGates = array(
		'ABW' => array('S94', 'S95'),
		'ANA' => array('R72', 'R74', 'R77', 'R80', 'R81', 'R82', 'R83', 'R84', 'R85', 'R86', 'R87'),
		'CCA' => array('R72', 'R74', 'R77', 'R80', 'R81', 'R82', 'R83', 'R84', 'R85', 'R86', 'R87'),
		'CPE' => array('R72', 'R74', 'R77', 'R80', 'R81', 'R82', 'R83', 'R84', 'R85', 'R86', 'R87'),
		'FDX' => array('S72', 'S74'),
		'QTR' => array('R72', 'R74', 'R77', 'R80', 'R81', 'R82', 'R83', 'R84', 'R85', 'R86', 'R87'),
		'MPH' => array('S72', 'S74', 'S77'),
		'SQC' => array('R72', 'R74', 'R77', 'R80')
	);

	private static $busGates = array(
		'D06'
	);

	private static $schengenGatesToApron = array(
		'D60' => 'D02',
		'D62' => 'D04',
		'D64' => 'D08',
		'D66' => 'D10',
		'D68' => 'D12',
		'D76' => 'D46',
		'D82' => 'D52',
		'D86' => 'D56',

		'D59' => 'D03',
		'D61' => 'D05',
		'D63' => 'D07',
		'D71' => 'D41',
		'D73' => 'D43',
		'D77' => 'D47',
		'D79' => 'D49',
		'D81' => 'D51',
		'D83' => 'D53',
		'D85' => 'D55',
		'D87' => 'D57',

		'M01' => 'H01',
		'M02' => 'H02',
		'M03' => 'H03',
		'M04' => 'H04',
		'M05' => 'H05',
		'M06' => 'H06',
		'M07' => 'H07'
	);

	private static $bravoApron = array(
		'A31' => 4,
		'A32' => 8,
		'A33' => 4,
		'A34' => 8,
		'A35' => 4,
		'A36' => 8,
		'A37' => 4,

		'A41' => 3,
		'A43' => 3,
		'A45' => 3,
		'A46' => 3,
		'A48' => 3,
		'A49' => 3,

		'B51' => 2,
		'B52' => 2,
		'B53' => 2,
		'B54' => 2,
		'B55' => 2,
		'B56' => 2,

		'B61' => 2,
		'B62' => 2,
		'B63' => 2,
		'B64' => 2,
		'B65' => 2,
		'B66' => 2,

		'B71' => 2,
		'B72' => 2,
		'B73' => 2,
		'B74' => 2,
		'B75' => 2,
		'B76' => 2,

		'B81' => 2,
		'B82' => 2,
		'B83' => 2,
		'B84' => 2,
		'B85' => 2,

		'B91' => 2,
		'B92' => 2,
		'B93' => 2,
		'B94' => 2,
		'B95' => 2 
	);

	private static $schengenGates = array(
		'B13' => 4,
		'B15' => 4,
		'B17' => 4,
		'B23' => 4,
		'B27' => 4,
		'B31' => 4,
		'B35' => 4,

		'B16' => 3,
		'B20' => 3,
		'B24' => 3,
		'B28' => 3,
		'B32' => 3,
		'B36' => 3,

		'C04' => 4,
		'C06' => 4,
		'C08' => 4,
		'C10' => 4,
		'C12' => 4,
		'C14' => 4,
		'C16' => 4,
		'C18' => 4,

		'C05' => 4,
		'C07' => 4,
		'C09' => 4,
		'C11' => 4,
		'C13' => 4,
		'C15' => 4,

		'D02' => 6,
		'D04' => 6,
		'D08' => 6,
		'D10' => 5,
		'D12' => 5,
		'D14' => 3,
		'D16' => 4,
		'D18' => 4,
		'D22' => 4,
		'D24' => 4,
		'D26' => 4,
		'D28' => 4,

		'D23' => 4,
		'D27' => 4,
		'D31' => 4
	);

	private static $schengenNonSchengenGates = array(
		'D46' => 4,
		'D52' => 4,
		'D56' => 4,

		'D03' => 7,
		'D05' => 4,
		'D07' => 7,
		'D41' => 4,
		'D43' => 7,
		'D47' => 7,
		'D49' => 4,
		'D51' => 6,
		'D53' => 6,
		'D55' => 4,
		'D57' => 7,

		'H01' => 4,
		'H02' => 4,
		'H03' => 4,
		'H04' => 4,
		'H05' => 4,
		'H06' => 4,
		'H07' => 4
	);

	private static $nonSchengenGates = array(
		'E02' => 6,
		'E04' => 4,
		'E06' => 7,
		'E08' => 8,
		'E18' => 8,
		'E20' => 8,
		'E22' => 8,
		'E24' => 8,

		'E03' => 6,
		'E05' => 6,
		'E07' => 8,
		'E09' => 8,
		'E17' => 8,
		'E19' => 8,

		'F02' => 8,
		'F04' => 8,
		'F06' => 8,
		'F08' => 8,

		'F03' => 8,
		'F05' => 8,
		'F07' => 8,
		'F09' => 8,

		'G02' => 4,
		'G04' => 8,
		'G06' => 8,
		'G08' => 8,

		'G03' => 8,
		'G05' => 8,
		'G07' => 8,
		'G09' => 8,
	);

	private static $cargoGates = array(
		'R72' => 8,
		'R74' => 8,
		'R77' => 8,
		'R80' => 8,
		'R81' => 8,
		'R82' => 8,
		'R83' => 8,
		'R84' => 8,
		'R85' => 6,
		'R86' => 8,
		'R87' => 5,

		'S72' => 8,
		'S74' => 8,
		'S77' => 8,
		'S79' => 8,
		'S82' => 8,
		'S84' => 8,
		'S87' => 8,
		'S90' => 8,
		'S92' => 8,
		'S94' => 8,
		'S95' => 8
	);

	static function allGates() {
		return array_merge(self::$bravoApron, self::$schengenGates,
			self::$schengenNonSchengenGates, self::$nonSchengenGates);
	}

	static function allApronVOPs() {
		return self::$bravoApron;
	}

	static function allSchengenGates() {
		return array_merge(self::$bravoApron, self::$schengenGates,
			self::$schengenNonSchengenGates);
	}

	static function allNonSchengenGates() {
		return array_merge(self::$schengenNonSchengenGates, self::$nonSchengenGates);
	}

	static function allCargoGates() {
		return self::$cargoGates;
	}

	static function resolveGate($callsign, $gateList) {
		$airlineCode = Definitions::resolveAirlineCode($callsign);

		if($airlineCode && array_key_exists($airlineCode, $gateList)) {
			return $gateList[$airlineCode];
		}

		return false;
	}

	static function resolveAirlineGate($callsign) {
		return self::resolveGate($callsign, self::$airlinesDefaultGates);
	}

	static function resolveCargoAirlineGate($callsign) {
		return self::resolveGate($callsign, self::$cargoAirlinesDefaultGates);
	}

	static function getExtraGates($aircraftType) {
		if(array_key_exists($aircraftType, self::$aircraftExtraGates)) {
			return self::$aircraftExtraGates[$aircraftType];
		}

		return array();
	}

	static function getExcludedGates($aircraftType) {
		if(array_key_exists($aircraftType, self::$aircraftNotOnGates)) {
			return self::$aircraftNotOnGates[$aircraftType];
		}

		return array();
	}

	static function convertSchengenGateToVOP($gate) {
		if(array_key_exists($gate, self::$schengenGatesToApron)) {
			return self::$schengenGatesToApron[$gate];
		}

		return $gate;
	}

	static function isBusGate($gate) {
		return in_array($gate, self::$busGates);
	}
}

?>