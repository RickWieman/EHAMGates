<?php
define('PAGE', 'search');
require('include/tpl_header.php');

$gf = new GateFinder();
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
		echo '<p class="bg-danger">Sorry, no gate could be determined for that combination...</p>';
	}
	else {
		echo '<p class="bg-success">You can put ' . $_POST['inputCallsign'] . ' on gate ' . $gate . '.';
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
				$aircraft = Gates_EHAM::$aircraftCategories;
				ksort($aircraft);

				foreach($aircraft as $type => $cat) {
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
<?php
require('include/tpl_footer.php');
?>