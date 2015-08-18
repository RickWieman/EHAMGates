<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	setcookie('autoReload', $_POST['autoReload'], (time() + 60*60*24*30));
	setcookie('autoReloadInterval', $_POST['autoReloadInterval'], (time() + 60*60*24*30));
	setcookie('schengenMethod', $_POST['schengen'], (time() + 60*60*24*30));
	setcookie('autoAssign', $_POST['autoAssign'], (time() + 60*60*24*30));
	
	header("Location: " . $_SERVER['PHP_SELF']);
	exit();
}

define('PAGE', 'config');
require('include/tpl_header.php');
?>
<h1>Personal Configuration</h1>

<p>Here you can tweak the Gate Finder to your personal needs. The changes you make are stored in cookies and preserved for 30 days.</p>

<form class="form-horizontal" role="form" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Inbound List</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Automatic reload</label>
				<div class="col-sm-10">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="autoReload" id="autoReload" value="true"<?php echo (isset($_COOKIE['autoReload']) && $_COOKIE['autoReload'] == 'true') ? ' checked' : '' ?>>
							Enable
						</label>
					</div>

				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Reload interval</label>
				<div class="col-sm-2">
					<div class="input-group">
						<input type="number" class="form-control" name="autoReloadInterval" id="autoReloadInterval" min="30" value="<?php echo (!isset($_COOKIE['autoReloadInterval'])) ? '60' : $_COOKIE['autoReloadInterval'] ?>">
						<div class="input-group-addon">seconds</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Search</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">Schengen</label>
				<div class="col-sm-10">
					<div class="radio">
						<label>
							<input type="radio" name="schengen" id="schengen1" value="origin"<?php echo (!isset($_COOKIE['schengenMethod']) || $_COOKIE['schengenMethod'] != 'checkbox') ? ' checked' : '' ?>>
							Determine by origin (default)
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="schengen" id="schengen2" value="checkbox"<?php echo (isset($_COOKIE['schengenMethod']) && $_COOKIE['schengenMethod'] == 'checkbox') ? ' checked' : '' ?>>
							Determine by checkbox
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Gate Assignment</label>
				<div class="col-sm-10">
					<div class="radio">
						<label>
							<input type="radio" name="autoAssign" id="autoAssign1" value="false"<?php echo (!isset($_COOKIE['autoAssign']) || $_COOKIE['autoAssign'] != 'true') ? ' checked' : '' ?>>
							Manually (default)
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="autoAssign" id="autoAssign2" value="true"<?php echo (isset($_COOKIE['autoAssign']) && $_COOKIE['autoAssign'] == 'true') ? ' checked' : '' ?>>
							Automatically after successful search
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<button type="reset" class="btn btn-default">Revert changes</button>
		</div>
	</div>
</form>
<?php
require('include/tpl_footer.php');
?>