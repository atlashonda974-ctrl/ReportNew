<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection to Oracle
$conn = oci_connect('AILMIS', 'AILMIS', 'orcl');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    exit;
} else {
    // Updated SQL query to include PLC_LOCACODE from USR_INFO
    $query = "SELECT * FROM uw_insured WHERE PPS_PARTY_CODE LIKE '41%' ORDER BY PPS_PARTY_CODE";

    $combo = oci_parse($conn, $query);

    if (!$combo) {
        $e1 = oci_error($conn);
        trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
    }

    // Perform the logic of the query
    $r1 = oci_execute($combo);
    if (!$r1) {
        $e1 = oci_error($combo);
        trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
    }

    $count = 0;
    $posts_arr = array();
    while ($res = oci_fetch_array($combo, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $jsonarray[] = json_encode($res);
        echo "</br>";
        echo $jsonarray[$count];
        $count++;
    }
}

// Close the Oracle connection
oci_close($conn);
?>