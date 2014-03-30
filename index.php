<?php
define('PAGE', 'search');
require('include/tpl_header.php');
?>
<h1>Search</h1>

<p>Find a (free) gate by specifying the callsign and aircraft type.</p>

<form class="form-horizontal" role="form">
	<div class="form-group">
		<label for="inputCallsign" class="col-sm-2 control-label">Callsign</label>
		<div class="col-sm-10">
			<input type="email" class="form-control" id="inputCallsign" placeholder="As filed">
		</div>
	</div>
	<div class="form-group">
		<label for="inputACType" class="col-sm-2 control-label">Aircraft type</label>
		<div class="col-sm-10">
			<select class="form-control">
				<option disabled>--- Common types ---</option>
				<option>B737</option>
				<option>B738</option>
				<option>MD11</option>
				<option disabled>--- All types ---</option>
				<?php
				$aircraft = Gates_EHAM::$aircraftCategories;
				ksort($aircraft);

				foreach($aircraft as $type => $cat) {
					echo '<option>' . $type . '</option>';
				}
				?>
			</select>
		</div>
	</div>

	<?php if(isset($_COOKIE['schengen']) && $_COOKIE['schengen'] == 'checkbox') { ?>
	<div class="form-group">
		<label for="inputOrigin" class="col-sm-2 control-label">Schengen?</label>
		<div class="col-sm-10">
			<div class="checkbox">
				<label>
					<input type="checkbox" value="">
					Flight originates from a Schengen country
				</label>
			</div>
		</div>
	</div>

	<?php } else { ?>

	<div class="form-group">
		<label for="inputOrigin" class="col-sm-2 control-label">Origin</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="inputOrigin" placeholder="ICAO code">
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