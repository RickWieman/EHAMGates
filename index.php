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
	&& in_array($_GET['gate'], Gates_EHAM::allGates())) {
	$_SESSION['assignedList'][$_GET['gate']] = $_GET['add'];
	
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Delete gate assignment
if(isset($_GET['delete']) && in_array($_GET['delete'], Gates_EHAM::allGates())) {
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
?>
<h1>Search</h1>

<p>Find a (free) gate by specifying the callsign and aircraft type.</p>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if($_POST['inputOriginMethod'] == 'checkbox') {
		$origin = (isset($_POST['inputOrigin']) && $_POST['inputOrigin'] == 'schengen') ? 'schengen' : 'nonschengen';
	}
	else {
		$origin = $_POST['inputOrigin'];
	}
	
	$gate = $gf->findGate($_POST['inputCallsign'], $_POST['inputACType'], $_POST['inputOrigin']);

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
?>

<form class="form-horizontal" role="form" method="post">
	<div class="form-group">
		<label for="inputCallsign" class="col-sm-2 control-label">Callsign</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="inputCallsign" name="inputCallsign" placeholder="As filed">
		</div>
	</div>
	<div class="form-group">
		<label for="inputACType" class="col-sm-2 control-label">Aircraft type</label>
		<div class="col-sm-10">
			<select class="form-control" name="inputACType">
				<option disabled>--- Common types ---</option>
				<option value="B737">B737</option>
				<option value="B738">B738</option>
				<option value="MD11">MD11</option>
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
	</div>

	<?php if(isset($_COOKIE['schengenMethod']) && $_COOKIE['schengenMethod'] == 'checkbox') { ?>
	<div class="form-group">
		<label for="inputOrigin" class="col-sm-2 control-label">Schengen?</label>
		<div class="col-sm-10">
			<div class="checkbox">
				<label>
					<input type="hidden" name="inputOriginMethod" value="checkbox" />
					<input type="checkbox" name="inputOrigin" value="schengen" />
					Flight originates from a Schengen country
				</label>
			</div>
		</div>
	</div>

	<?php } else { ?>

	<div class="form-group">
		<label for="inputOrigin" class="col-sm-2 control-label">Origin</label>
		<div class="col-sm-10">
			<input type="hidden" name="inputOriginMethod" value="text" />
			<input type="text" class="form-control" id="inputOrigin" name="inputOrigin" placeholder="ICAO code">
		</div>
	</div>

	<?php } ?>	

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Find Gate</button>
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
			if(count($_SESSION['assignedList']) == 0) {
				echo '<tr><td colspan="3">You have not assigned any gates yet.</td></tr>';
			}

			asort($_SESSION['assignedList']);
			
			foreach($_SESSION['assignedList'] as $gate => $callsign) {
				echo '<tr><td>' . $callsign . '</td><td>' . $gate . '</td>';
				echo '<td style="text-align: right;"><a href="?delete=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span> Delete</a></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
require('include/tpl_footer.php');
?>