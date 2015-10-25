<?php
// If request not set, .htaccess is no good
if (!isset($_GET['r'])) {
	echo '<h3>Error: Invalid .htaccess.</h3><p>I can\'t find the request query.</p>';
}
else {
	require_once('../application/config.php');
	require_once('../application/sparkerphp.php');

	$req = preg_replace('/[^a-zA-Z0-9\-_\.\/]/', '', $_GET['r']);
	$sparkerphp = new SparkerPHP($req);
	echo $sparkerphp->render();
}

// Print debugging info
if (__DEBUG__) {
	$exec_time = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1000, 3).'ms';
	$exec_size = (memory_get_peak_usage(true)/1024).'kB';
	echo "Execution time: $exec_time\nExecution size: $exec_size";
}
?>
