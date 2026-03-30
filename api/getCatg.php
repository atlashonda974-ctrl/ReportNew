<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');

require_once 'config.php';

if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    exit;
}

$combo = oci_parse($conn, "SELECT FFM_ASSET_CODE, FFM_ASSET_DESC
    from GL_FA_MASTER Where FFM_ASSET_TYPE = 'G' ");

if (!$combo) {
    $e1 = oci_error($conn);
    trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
}

$r1 = oci_execute($combo);
if (!$r1) {
    $e1 = oci_error($combo);
    trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
}

$data = [];
while ($res = oci_fetch_array($combo, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = $res;
}

oci_close($conn);

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);

?>
