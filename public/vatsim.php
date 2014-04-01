<?php
define('PAGE', 'vatsim');
require('include/tpl_header.php');

require_once('include/vatsimparser.php');
require_once('include/gatefinder.php');

$vp = new VatsimParser();
$gf = new GateFinder();
?>
<h1>VATSIM Inbound List</h1>

<div class="container col-sm-6">
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<th>Callsign</th>
				<th>Aircraft</th>
				<th>Origin</th>
				<th>Gate</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($vp->parseData() as $callsign => $data) {
				echo '<tr><td>' . $callsign . '</td><td>' . $data['actype'] . '</td><td>' . $data['origin'] . '</td>';
				echo '<td>' . $gf->findGate($callsign, $data['actype'], $data['origin']) . '</td>';
				echo '<td></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
require('include/tpl_footer.php');
?>