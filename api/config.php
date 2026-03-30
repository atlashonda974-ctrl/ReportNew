<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
// Create connection to Oracle
$conn = oci_connect('AILMIS', 'AILMIS', 'orcl');

?>
