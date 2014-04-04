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

if($gateAssigner->handleAssign() || $gateAssigner->handleAssignManual() || $gateAssigner->handleOccupied() || $gateAssigner->handleRelease()) {
	$_SESSION['gateAssigner'] = serialize($gateAssigner);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

define('PAGE', 'search');
require_once('include/tpl_header.php');

$stamp = (file_exists('data.txt') ? file_get_contents('data.txt', NULL, NULL, 0, 10) : time());
?>
<h1>Search</h1>

<p>Find a (free) gate by specifying the callsign and aircraft type.<br />
Last update of real life data: <?php echo date("H:i:s (d-m-Y)", $stamp); ?></p>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(!empty($_POST['inputCallsign']) && !empty($_POST['inputACType'])
		&& ($_POST['inputOriginMethod'] == 'checkbox' || ($_POST['inputOriginMethod'] == 'text' && !empty($_POST['inputOrigin'])))) {

		if($_POST['inputOriginMethod'] == 'checkbox') {
			$origin = (isset($_POST['inputOrigin']) && $_POST['inputOrigin'] == 'schengen') ? 'schengen' : 'nonschengen';
		}
		else {
			$origin = strtoupper($_POST['inputOrigin']);
		}

		$gateAssigner->findGate($_POST['inputCallsign'], $_POST['inputACType'], $origin);
	}
	else {
		$gateAssigner->resetSearch();
		?>
		<div class="alert alert-danger">
			<span class="glyphicon glyphicon-warning-sign"></span>
			Please fill in all the fields...
		</div>
		<?php
	}
}

if($gateAssigner->result()) {
	$result = $gateAssigner->result();

	if($result['matchType'] == 'NONE') {
		?>
		<div class="alert alert-danger">
			<p>
				<span class="glyphicon glyphicon-warning-sign"></span>
				The gate for <strong><?php echo $result['callsign']; ?></strong> could not be determined.
				Choose one manually...<br /><br />
			</p>

			<form class="form-inline" method="get">
				<div class="form-group">
					<label for="manual" class="sr-only">Aircraft type</label>
					<select class="form-control" name="manual">
						<?php
						$freeGates = $gateAssigner->getFreeGates();

						foreach($freeGates as $gate => $cat) {
							echo '<option value="'. $gate .'">' . $gate . ' (cat. ' . $cat . ')</option>';
						}
						?>
					</select>
				</div>

				<button type="submit" class="btn btn-primary">Assign Gate</button>
			</form>
		</div>
		<?php
	}
	else {
		if(isset($_COOKIE['autoAssign']) && $_COOKIE['autoAssign'] == 'true') {
			$gateAssigner->assignFoundGate();
		}
		?>
		<div class="alert alert-success">
			<p><strong><?php echo $result['callsign']; ?></strong> can be put on gate <strong><?php echo $result['gate']; ?></strong>.</p>

			<?php
			$matchTypeIcon = Definitions::resolveMatchTypeIcon($result['matchType']);
			$matchTypeText = Definitions::resolveMatchTypeText($result['matchType']);
			echo '<p><span class="glyphicon glyphicon-' . $matchTypeIcon . '"></span> '. $matchTypeText .'</p>';
			
			if(!isset($_COOKIE['autoAssign']) || $_COOKIE['autoAssign'] != 'true') { ?>
				<p>
				<a href="?assign" class="btn btn-primary">
					Add to list
				</a>
				<a href="?occupied" class="btn btn-danger">
					This gate is occupied
				</a>
			</p>
			<?php } ?>
		</div>
		<?php
	}
}
?>

<form class="form-inline" role="form" method="post">
	<div class="form-group">
		<label for="inputCallsign" class="sr-only">Callsign</label>
		<input type="text" class="form-control" id="inputCallsign" name="inputCallsign" placeholder="Filed Callsign">
	</div>
	<div class="form-group">
		<label for="inputACType" class="sr-only">Aircraft type</label>
		<select class="form-control" name="inputACType">
			<option disabled>--- Common ---</option>
			<option value="A319">A319</option>
			<option value="A320">A320</option>
			<option value="A321">A321</option>
			<option value="B737">B737</option>
			<option value="B738">B738</option>
			<option value="B739">B739</option>
			<option value="B744">B744</option>
			<option value="DH8D">DH8D</option>
			<option value="E190">E190</option>
			<option value="F70">F70</option>
			<option value="F100">F100</option>
			<option value="MD11">MD11</option>
			<option value="RJ85">RJ85</option>
			<option disabled>--- All ---</option>
			<?php
			$aircraftTypes = Definitions::getAllAircraft();
			ksort($aircraftTypes);

			foreach($aircraftTypes as $type => $cat) {
				echo '<option value="'. $type .'">' . $type . '</option>';
			}
			?>
		</select>
	</div>

	<?php if(isset($_COOKIE['schengenMethod']) && $_COOKIE['schengenMethod'] == 'checkbox') { ?>
	<div class="form-group">
		<label class="checkbox-inline">
			<input type="hidden" name="inputOriginMethod" value="checkbox" />
			<input type="checkbox" name="inputOrigin" value="schengen" />
			Schengen flight
		</label>
	</div>

	<?php } else { ?>

	<div class="form-group">
		<label for="inputOrigin" class="sr-only">Origin</label>
		<input type="hidden" name="inputOriginMethod" value="text" />
		<input type="text" class="form-control" id="inputOrigin" name="inputOrigin" placeholder="Origin (ICAO code)">
	</div>

	<?php } ?>	

	<button type="submit" class="btn btn-primary">Find Gate</button>
</form>

<h1>List</h1>
<p>The list below shows all aircraft with gate assignments. These gates are also marked as occupied.</p>
<div class="container col-sm-6">
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<th>Callsign</th>
				<th>Gate</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach($gateAssigner->getAssignedGates() as $gate => $data) {
				if($data['callsign'] != 'unknown') {
					echo '<tr><td>' . $data['callsign'] . '</td><td>' . $gate . '</td>';
					echo '<td style="text-align: right;"><a href="?release=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a></td></tr>';
					$i++;
				}
			}

			if($i == 0) {
				echo '<tr><td colspan="3">You have not assigned any gates yet.</td></tr>';
			}
			?>
		</tbody>
	</table>
</div>

<?php
require('include/tpl_footer.php');

$_SESSION['gateAssigner'] = serialize($gateAssigner);
?>