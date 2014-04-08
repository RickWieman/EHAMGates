<?php
session_start();

require_once('include/definitions_global.php');
require_once('include/gateassigner.php');

// Initialize GateAssigner: Either get it from the SESSION or create a new instance.
if(isset($_SESSION['gateAssigner'])) {
	$gateAssigner = unserialize($_SESSION['gateAssigner']);
}

if(!isset($_SESSION['gateAssigner']) || !$gateAssigner instanceof GateAssigner) {
	$gateAssigner = new GateAssigner();
}

$gateAssigner->handleOccupy();
$gateAssigner->handleRelease();

define('PAGE', 'gates');
require('include/tpl_header.php');
?>
<h1>All Gates</h1>

<p>Below all gates of Schiphol are shown. The first table shows the occupied gates, the second the free gates.</p>



<div class="container row">
	<div class="col-sm-4">
		<h2>Occupied</h2>

		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th>GATE</th>
					<th>A/C</th>
					<th>C/S</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$allGates = $gateAssigner->getAssignedGates();
				ksort($allGates);

				if(count($allGates) == 0) {
					echo '<tr><td colspan="5">All gates are free.</td></tr>';
				}

				foreach($allGates as $gate => $data) {
					echo '<tr><td>' . $gate . '</td>';

					$assignment = $gateAssigner->isGateAssigned($gate);

					if($assignment['callsign'] == 'unknown') {
						echo '<td colspan="3"><em>unknown</em></td>';
					}
					else {
						echo '<td>' . $data['aircraftType'] . '</td>';
						echo '<td>' . $assignment['callsign'] . '</td>';
						echo '<td><span class="glyphicon glyphicon-'	. Definitions::resolveMatchTypeIcon($assignment['matchType']) . '"></span></td>';
					}					
				
					echo '<td style="text-align: right;"><a href="?release=' . $gate . '" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-log-out"></span> Release</a></td></tr>';
					
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-3 col-sm-offset-1">
		<h2>Free</h2>

		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th>GATE</th>
					<th>CAT.</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$allGates = Gates_EHAM::allGates();
				ksort($allGates);

				foreach($allGates as $gate => $cat) {
					if(!$gateAssigner->isGateAssigned($gate)) {
						echo '<tr><td>' . $gate . '</td><td>' . $cat . '</td>';
						echo '<td style="text-align: right;"><a href="?occupy=' . $gate . '" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-ban-circle"></span> Occupy</a></td></tr>';
					}					
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php
require('include/tpl_footer.php');

$_SESSION['gateAssigner'] = serialize($gateAssigner);
?>