<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>{$title}</title>
	<link rel="stylesheet" type="text/css" href="{$approot}assets/style.css">
</head>
<body>
<?php
if (isset($errorMessages)) {
	foreach($errorMessages as $msg) {
		echo "\n<div class='error_box'><b>Error</b>: $msg</div>";
	}
}
if (isset($messageMessages)) {
	foreach($messageMessages as $msg) {
		echo "\n<div class='message_box'><b>Notice</b>: $msg</div>";
	}
}
if (isset($confirmMessages)) {
	foreach($confirmMessages as $msg) {
		echo "\n<div class='confirm_box'><b>Success</b>: $msg</div>";
	}
}
?>
