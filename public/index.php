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

if($gateAssigner->handleRelease()) {
	$_SESSION['gateAssigner'] = serialize($gateAssigner);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

$vp = new VatsimParser();
$vatsimData = $vp->parseData();

define('PAGE', 'vatsim');
require('include/tpl_header.php');
?>
<div class="col-sm-6">
	<div class="row">
		<h1>Inbound List</h1>

		<p>VATSIM data gets updated every 2 minutes (server list every hour), real life data gets updated every 15 minutes.
			The last VATSIM update was <strong><?php echo date("i:s", time()-$vp->lastDataFetch()); ?> minutes ago</strong>.</p>

		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th>C/S</th>
					<th class="hidden-xs">A/C</th>
					<th class="hidden-xs">ORGN</th>
					<th>GATE</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(count($vatsimData) == 0) {
					echo '<tr><td colspan="5">No inbound flights at the moment.</td></tr>';
				}

				foreach($vatsimData as $callsign => $data) {
					$result = $gateAssigner->isCallsignAssigned($callsign);
					if($result) {
						$assigned = true;
					}
					else {
						$gateAssigner->findGate($callsign, $data['actype'], $data['origin']);

						$result = $gateAssigner->result();

						if(isset($_GET['callsign']) && $_GET['callsign'] == $callsign) {
							if($gateAssigner->handleAssign() || $gateAssigner->handleAssignManual() || $gateAssigner->handleOccupied()) {
								$_SESSION['gateAssigner'] = serialize($gateAssigner);

								header("Location: " . $_SERVER['PHP_SELF']);
								exit();
							}
						}
						$assigned = false;
					}

					$rowClass = ($data['flightrules'] == 'V') ? 'text-muted' : '';

					echo '<tr class="'. $rowClass .'"><td>' . $callsign . '</td><td class="hidden-xs">' . $result['aircraftType'] . '</td><td class="hidden-xs">' . $result['origin'] . '</td>';
					echo '<td><span class="glyphicon glyphicon-' . Definitions::resolveMatchTypeIcon($result['matchType']) . '"></span> ' . $result['gate'] . '</td>';
					echo '<td style="text-align: right;">';
					if($assigned) {
						echo '<a href="?release='. $result['gate'] .'" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-log-out"></span> Release</a>';
					}
					elseif($result['matchType'] != 'NONE') {
						echo '<a href="?callsign='. $callsign .'&amp;assign" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-log-in"></span> Assign</a>';
						echo ' <a href="?callsign='. $callsign .'&amp;occupied" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-ban-circle"></span> Occupied</a>';
					}
					else {
						?>
						<form class="form-inline" method="get">
							<input type="hidden" name="callsign" value="<?php echo $callsign; ?>" />
							<label for="manual" class="sr-only">Aircraft type</label>
							<select class="form-control-xs" name="manual">
								<?php
								$freeGates = $gateAssigner->getFreeGates($result['aircraftType'], $result['origin']);

								foreach($freeGates as $gate => $cat) {
									echo '<option value="'. $gate .'">' . $gate . ' (' . $cat . ')</option>';
								}
								?>
							</select>
							<button type="submit" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-log-in"></span> Assign</button>
						</form>
						<?php
					}
					echo '</td></tr>';
				}
				$gateAssigner->resetSearch();
				?>
			</tbody>
		</table>
	</div>

	<div class="row">
		<h2>Legend</h2>

		<p>Greyed out flights are VFR flights, the others are IFR flights. Below is an overview of the icons used in the Gate column.</p>

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
				<tr><td><span class="glyphicon glyphicon-warning-sign"></span></td><td>The gate could not be determined.</td></tr>
			</tbody>
		</table>
	</div>
</div>
<?php
require('include/tpl_footer.php');

$_SESSION['gateAssigner'] = serialize($gateAssigner);
?>