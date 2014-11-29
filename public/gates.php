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

if($gateAssigner->handleOccupy() || $gateAssigner->handleRelease()) {
	$_SESSION['gateAssigner'] = serialize($gateAssigner);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

define('PAGE', 'gates');
require('include/tpl_header.php');
?>
<div class="row">
	<div class="col-md-9">
		<h1>All Gates</h1>

		<p>Below all gates of Schiphol are shown. The first table shows the occupied gates, the second the free gates.</p>

		<div class="row">
			<div class="col-sm-5">
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

						foreach($allGates as $gate => $callsign) {
							echo '<tr><td>' . $gate . '</td>';

							$assignment = $gateAssigner->isCallsignAssigned($callsign);

							if($callsign == 'unknown') {
								echo '<td colspan="3"><em>unknown</em></td>';
							}
							else {
								echo '<td>' . $assignment['aircraftType'] . '</td>';
								echo '<td>' . $callsign . '</td>';
								echo '<td><span class="glyphicon glyphicon-'	. Definitions::resolveMatchTypeIcon($assignment['matchType']) . '"></span></td>';
							}					
						
							echo '<td style="text-align: right;"><a href="?release=' . $gate . '" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-log-out"></span> Release</a></td></tr>';
							
						}
						?>
					</tbody>
				</table>
			
				<h3>Legend</h3>

				<p>Below is an overview of the icons used.</p>

				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th></th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach(Definitions::getAllMatchTypes() as $description) {
							echo '<tr>';
							echo '<td><span class="glyphicon glyphicon-' . $description['icon'] . '"></span></td>';
							echo '<td>' . $description['text'] . '</td>';
							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="col-sm-4 col-sm-offset-1">
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
	</div>
</div>

<?php
require('include/tpl_footer.php');

$_SESSION['gateAssigner'] = serialize($gateAssigner);
?>