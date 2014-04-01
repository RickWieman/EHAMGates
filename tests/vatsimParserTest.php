<?php
require_once('../public/include/vatsimparser.php');

$vp = new VatsimParser('testdata-vatsim.txt');

print_r($vp->parseData());

?>