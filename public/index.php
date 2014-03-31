<?php
session_start();

if(!isset($_SESSION['assignedList'])) {
	$_SESSION['assignedList'] = array();
}

require_once('include/definitions.php');
require_once('include/gatefinder.php');

$gf = new GateFinder();

// Add gate assignment
if(isset($_GET['add']) && isset($_GET['gate']) && preg_match('/[A-Z]+[0-9]+/', $_GET['add'])
	&& array_key_exists($_GET['gate'], Gates_EHAM::allGates())) {
	$_SESSION['assignedList'][$_GET['gate']] = $_GET['add'];
	
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Delete gate assignment
if(isset($_GET['delete']) && array_key_exists($_GET['delete'], Gates_EHAM::allGates())) {
	unset($_SESSION['assignedList'][$_GET['delete']]);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Mark assigned gates as occupied
foreach($_SESSION['assignedList'] as $gate => $callsign) {
	$gf->occupyGate($gate);
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
			$origin = $_POST['inputOrigin'];
		}
		
		$gate = $gf->findGate($_POST['inputCallsign'], $_POST['inputACType'], $origin);

		if(!$gate) {
			?>
			<div class="alert alert-danger">
				Sorry, no gate could be determined for that combination...
			</div>
			<?php
		}
		else {
			if(isset($_COOKIE['autoAssign']) && $_COOKIE['autoAssign'] == 'true') {
				$_SESSION['assignedList'][$gate] = $_POST['inputCallsign'];
				$gf->occupyGate($gate);
			}
			?>
			<div class="alert alert-success">
				You can put <strong><?php echo $_POST['inputCallsign']; ?></strong>
				on gate <strong><?php echo $gate; ?></strong>

				<?php if(!isset($_COOKIE['autoAssign']) || $_COOKIE['autoAssign'] != 'true') { ?>
					<br />
					<a href="?add=<?php echo $_POST['inputCallsign']; ?>&amp;gate=<?php echo $gate; ?>">
						Add to list
					</a>
				<?php } ?>
			</div>
			<?php
		}
	}
	else {
		?>
		<div class="alert alert-danger">
			Controleer of je alle velden wel hebt ingevuld...
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
			<option disabled>--- Common types ---</option>
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
			<option disabled>--- All types ---</option>
			<?php
			$aircraftTypes = Gates_EHAM::$aircraftCategories;
			ksort($aircraftTypes);

			foreach($aircraftTypes as $type => $cat) {
				echo '<option value="'. $type .'">' . $type . '</option>';
			}
			?>
		</select>
	</div>

	<?php if(isset($_COOKIE['schengenMethod']) && $_COOKIE['schengenMethod'] == 'checkbox') { ?>
	<div class="checkbox">
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

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">Find Gate</button>
		</div>
	</div>
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
			asort($_SESSION['assignedList']);

			$i = 0;
			foreach($_SESSION['assignedList'] as $gate => $callsign) {
				if($callsign != 'unknown') {
					echo '<tr><td>' . $callsign . '</td><td>' . $gate . '</td>';
					echo '<td style="text-align: right;"><a href="?delete=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a></td></tr>';
				}
				$i++;
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
?>