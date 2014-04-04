<?php
session_start();

require_once('include/definitions_global.php');
require_once('include/vatsimparser.php');
require_once('include/gateassigner.php');

// Initialize GateAssigner: Either get it from the SESSION or create a new instance.
if(isset($_SESSION['gateAssigner'])) {
	$gateAssigner = unserialize($_SESSION['gateAssigner']);
}

if(!isset($_SESSION['gateAssigner']) || !$gateAssigner instanceof GateAssigner) {
	$gateAssigner = new GateAssigner();
}

$vp = new VatsimParser();

define('PAGE', 'vatsim');
require('include/tpl_header.php');

$stamp = (file_exists('data-vatsim.txt') ? file_get_contents('data-vatsim.txt', NULL, NULL, 0, 10) : time());
?>
<h1>VATSIM Inbound List</h1>

<p>Last update of VATSIM data: <?php echo date("H:i:s (d-m-Y)", $stamp); ?></p>

<div class="container col-sm-6">
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<th>Callsign</th>
				<th>Aircraft</th>
				<th>Origin</th>
				<th>Gate</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($vp->parseData() as $callsign => $data) {

				// TODO: In this iteration, check whether a GET request has been sent. If so, check whether *callsign* matches iteration. Then continue with GateAssigner.

				$assigned = $gateAssigner->isCallsignAssigned($callsign);
				if($assigned) {
					$gate['gate'] = $assigned['gate'];
					$gate['match'] = $assigned['matchType'];
					$assigned = true;
				}
				else {
					$gate['gate'] = '';
					$gate['match'] = '';
					//$gate = $gf->findGate($callsign, $data['actype'], $data['origin']);
					//$gate = $gate['gate'];
					$assigned = false;
				}

				echo '<tr><td>' . $callsign . '</td><td>' . $data['actype'] . '</td><td>' . $data['origin'] . '</td>';
				echo '<td>' . $gate['gate'] . '</td>';
				echo '<td><span class="glyphicon glyphicon-' . Definitions::resolveMatchTypeIcon($gate['match']) . '"></span></td>';
				echo '<td>';
				if($assigned) {
					echo '<a href="?release='. $gate['gate'] .'" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-log-out"></span> Release</a>';
				}
				elseif($gate['gate']) {
					echo '<a href="?add='. $callsign .'&amp;gate='. $gate['gate'] . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-log-in"></span> Assign</a>';
				}
				echo '</td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
require('include/tpl_footer.php');
?>