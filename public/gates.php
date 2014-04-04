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
<h1>Occupied Gates</h1>

<p>Below all gates of Schiphol are shown with their occupants.</p>

<div class="container col-sm-6">
	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<th>Gate</th>
				<th>Category</th>
				<th>Occupant</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$allGates = Gates_EHAM::allGates();
			ksort($allGates);

			foreach($allGates as $gate => $cat) {
				echo '<tr><td>' . $gate . '</td><td>' . $cat . '</td>';

				if($gateAssigner->isGateAssigned($gate)) {
					$assignment = $gateAssigner->isGateAssigned($gate);

					if($assignment['callsign'] == 'unknown') {
						echo '<td class="warning">unknown</td>';
					}
					else {
						echo '<td class="danger">' . $assignment['callsign'] . '</td>';	
					}					
					$occupied = true;
				}
				else {
					echo '<td class="success">free</td>';
					$occupied = false;
				}

				if($occupied) {
					echo '<td style="text-align: right;"><a href="?release=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span> Release</a></td></tr>';
				}
				else {
					echo '<td style="text-align: right;"><a href="?occupy=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plane"></span> Mark as Occupied</a></td></tr>';
				}
			}
			?>
		</tbody>
	</table>
</div>

<?php
require('include/tpl_footer.php');

$_SESSION['gateAssigner'] = serialize($gateAssigner);
?>