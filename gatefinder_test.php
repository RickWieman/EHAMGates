<?php
require('gatefinder.php');

function testResult($expected, $returned) {
	if($expected == $returned) {
		return " [OK]\n";
	}
	return " [FAIL: expected " . $expected . ', but got ' . $returned . "]\n";
}

$gf = new GateFinder;

echo "- Aircraft to Categories -\n";
echo 'B738' . testResult(4, $gf->resolveAircraftCat('B738'));
echo 'MD11' . testResult(7, $gf->resolveAircraftCat('MD11'));
echo 'A380' . testResult(8, $gf->resolveAircraftCat('A380'));

echo "- Callsigns to Piers - \n";

echo 'KLM123' . testResult(array('B', 'C', 'D', 'E', 'F', 'G'), $gf->resolveAirlineGate('KLM123'));
echo 'TRA 06R' . testResult(array('C', 'D', 'E'), $gf->resolveAirlineGate('TRA 06R'));

echo "- Schengen or Non-Schengen - \n";
echo 'KLAX' . testResult(false, $gf->resolveSchengenOrigin('KLAX'));
echo 'EBBR' . testResult(true, $gf->resolveSchengenOrigin('EBBR'));

echo "- Find Gate -\n";
echo "TRA123, B738, EBBR:\n";
	$gate = $gf->findGate('TRA123', 'B738', 'EBBR');
	echo '* cat. 4' . testResult(4, Gates_EHAM::$gates[$gate]); 
	echo '* preferent' . testResult(true, in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['TRA']));
echo "KLM123, MD11, EDDT:\n";
	$gate = $gf->findGate('KLM123', 'MD11', 'EDDT');
	echo '* cat. 7' . testResult(7, Gates_EHAM::$gates[$gate]); 
	echo '* preferent' . testResult(true, in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['KLM']));
?>