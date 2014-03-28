<?php
require('gatefinder.php');

function testResult($expected, $returned) {
	if($expected == $returned) {
		return " [OK]\n";
	}
	return " [FAIL: expected " . $expected . ', but got ' . $returned . "]\n";
}

$gf = new GateFinder;

$allGates = array_merge(Gates_EHAM::$bravoApron, Gates_EHAM::$schengenGates, Gates_EHAM::$schengenNonSchengenGates,
	Gates_EHAM::$nonSchengenGates);

$schengenGates = array_merge(Gates_EHAM::$bravoApron, Gates_EHAM::$schengenGates, Gates_EHAM::$schengenNonSchengenGates);
$nonSchengenGates = array_merge(Gates_EHAM::$schengenNonSchengenGates, Gates_EHAM::$nonSchengenGates);

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
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* cat. 4' . testResult(4, $allGates[$gate]); 
	echo '* preferent' . testResult(true, in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['TRA']));
	echo '* Schengen' . testResult(true, array_key_exists($gate, $schengenGates));
echo "KLM123, MD11, EDDT:\n";
	$gate = $gf->findGate('KLM123', 'MD11', 'EDDT');
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* cat. 7' . testResult(7, $allGates[$gate]); 
	echo '* preferent' . testResult(true, in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['KLM']));
	echo '* Schengen' . testResult(true, array_key_exists($gate, $schengenGates));
echo "EAL123, A380, OMDB:\n";
	$gate = $gf->findGate('EAL123', 'A380', 'OMDB');
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* cat. 8' . testResult(8, $allGates[$gate]); 
	echo '* preferent' . testResult(true, in_array(substr($gate, 0, 1), Gates_EHAM::$airlinesDefaultGates['EAL']));
	echo '* Non-Schengen' . testResult(true, array_key_exists($gate, $nonSchengenGates));
echo "MPH456, B744, KLAX:\n";
	$gate = $gf->findGate('MPH456', 'B744', 'KLAX');
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* preferent' . testResult(true, in_array($gate, Gates_EHAM::$cargoGates['MPH']));
echo "FDX456, B744, KLAS:\n";
	$gate = $gf->findGate('FDX456', 'B744', 'KLAS');
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* preferent' . testResult(true, in_array($gate, Gates_EHAM::$cargoGates['FDX']));
echo "ANA789, B744, KEWR:\n";
	$gate = $gf->findGate('ANA789', 'B744', 'KEWR');
	echo 'Assigned Gate = ' . $gate . "\n";
	echo '* preferent' . testResult(true, in_array($gate, Gates_EHAM::$cargoGates['ANA']));
?>