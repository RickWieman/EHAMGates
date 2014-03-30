<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	setcookie('schengenMethod', $_POST['schengen'], (time() + 60*60*24*30));
}

define('PAGE', 'config');
require('include/tpl_header.php');
?>
<h1>Personal Configuration</h1>

<p>Here you can tweak the Gate Finder to your personal needs. The changes you make are stored in cookies and preserved for 30 days.</p>

<form class="form-horizontal" role="form" method="post">
	<div class="form-group">
		<label class="col-sm-2 control-label">Schengen</label>
		<div class="col-sm-10">
			<div class="radio">
				<label>
					<input type="radio" name="schengen" id="schengen1" value="origin"<?php echo (!isset($_COOKIE['schengen']) || $_COOKIE['schengen'] != 'checkbox') ? ' checked' : '' ?>>
					Determine by origin (default)
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="schengen" id="schengen2" value="checkbox"<?php echo (isset($_COOKIE['schengen']) && $_COOKIE['schengen'] == 'checkbox') ? ' checked' : '' ?>>
					Determine by checkbox
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Save changes</button>
		</div>
	</div>
</form>
<?php
require('include/tpl_footer.php');
?>