<?php
session_start();

if(!isset($_SESSION['assignedList'])) {
	$_SESSION['assignedList'] = array();
}

require_once('include/definitions.php');
require_once('include/gatefinder.php');

$allGates = Gates_EHAM::allGates();
$gf = new GateFinder();

// Occupy gate
if(isset($_GET['occupy']) && in_array($_GET['occupy'], $allGates)) {
	$_SESSION['assignedList'][$_GET['occupy']] = 'unknown';
	
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

// Free gate
if(isset($_GET['free']) && in_array($_GET['free'], $allGates)) {
	unset($_SESSION['assignedList'][$_GET['free']]);

	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

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
				<th>Categorie</th>
				<th>Occupant</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			ksort($allGates);

			foreach($allGates as $gate => $cat) {
				echo '<tr><td>' . $gate . '</td><td>' . $cat . '</td>';

				if(array_key_exists($gate, $_SESSION['assignedList'])) {
					if($_SESSION['assignedList'][$gate] == 'unknown') {
						echo '<td class="warning">unknown</td>';
					}
					else {
						echo '<td class="danger">' . $_SESSION['assignedList'][$gate] . '</td>';	
					}					
					$occupied = true;
				}
				else {
					echo '<td class="success">free</td>';
					$occupied = false;
				}

				if($occupied) {
					echo '<td style="text-align: right;"><a href="?free=' . $gate . '" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span> Mark as Free</a></td></tr>';
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
?>