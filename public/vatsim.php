<?php
session_start();

require_once('include/vatsimparser.php');
require_once('include/gatefinder.php');

$vp = new VatsimParser();
$gf = new GateFinder();

// Add gate assignment
if(isset($_GET['add']) && isset($_GET['gate'])
	&& (preg_match('/[A-Z]+[0-9]+/', $_GET['add']) || $_GET['add'] == 'unknown')
	&& (array_key_exists($_GET['gate'], Gates_EHAM::allGates())
		|| in_array($_GET['gate'], Gates_EHAM::allCargoGates()))) {
	$_SESSION['assignedList'][$_GET['gate']] = $_GET['add'];
	
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Delete gate assignment
if(isset($_GET['delete']) && (array_key_exists($_GET['delete'], Gates_EHAM::allGates())
	|| in_array($_GET['delete'], Gates_EHAM::allCargoGates()))) {
	unset($_SESSION['assignedList'][$_GET['delete']]);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Mark assigned gates as occupied
foreach($_SESSION['assignedList'] as $gate => $callsign) {
	$gf->occupyGate($gate);
}

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
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($vp->parseData() as $callsign => $data) {
				if(in_array($callsign, $_SESSION['assignedList'])) {
					$gate = array_search($callsign, $_SESSION['assignedList']);
					$assigned = true;
				}
				else {
					$gate = $gf->findGate($callsign, $data['actype'], $data['origin']);
					$gate = $gate['gate'];
					$assigned = false;
				}

				echo '<tr><td>' . $callsign . '</td><td>' . $data['actype'] . '</td><td>' . $data['origin'] . '</td>';
				echo '<td>' . $gate . '</td>';
				echo '<td>';
				if($assigned) {
					echo '<a href="?delete='. $gate .'" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-log-out"></span> Release</a>';
				}
				elseif($gate) {
					echo '<a href="?add='. $callsign .'&amp;gate='. $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-log-in"></span> Assign</a>';
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