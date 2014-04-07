<?php
if(!defined('PAGE')) {
	die('Not for single use.');
}

ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>EHAM Gate Finder</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
</head>
<body>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php">EHAM Gate Finder</a>
			</div>
			<div class="collapse navbar-collapse" id="menu">
				<ul class="nav navbar-nav">
					<li<?php echo (PAGE == 'vatsim') ? ' class="active"' : '' ?>><a href="index.php">Inbound List</a></li>
					<li<?php echo (PAGE == 'search') ? ' class="active"' : '' ?>><a href="search.php">Search</a></li>
					<li<?php echo (PAGE == 'gates') ? ' class="active"' : '' ?>><a href="gates.php">All Gates</a></li>
					<li<?php echo (PAGE == 'config') ? ' class="active"' : '' ?>><a href="configure.php">Configure</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">