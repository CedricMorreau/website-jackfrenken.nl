<?php

// Useful function
function fillValue($val, $data, $separator) {

	if (empty($val))
		$val = $data;
	else
		$val .= $separator . $data;

	return $val;
}

// Add a log
function addLog($logFile, $logData, $timestamp) {

	$log = $logFile;

	$currentData = file_get_contents($log);

	$newData = '[' . $timestamp . ']' . $logData . "\n" . $currentData;

	file_put_contents($log, $newData);
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// MSSQL stuff
function mssql_escape_quotes($val) {

	return str_replace("'", "''", $val);
}

?>