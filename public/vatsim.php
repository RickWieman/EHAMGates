<?php
define('PAGE', 'vatsim');
require('include/tpl_header.php');

require_once('include/vatsimparser.php');

$vp = new VatsimParser();
?>
<h1>VATSIM Inbound List</h1>

<?php
print_r($vp->parseData());

require('include/tpl_footer.php');
?>