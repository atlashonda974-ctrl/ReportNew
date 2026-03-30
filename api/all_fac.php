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
    $query = "
        SELECT 
            usr_info.SUS_USERCODE,
            usr_info.SUS_NAME,
            usr_info.SUS_ACTIVE,
            usr_info.SUS_LASTLOGIN,
            usr_info.SUS_LASTPASSCHANGE,
            usr_info.PLC_LOCACODE,  -- Include PLC_LOCACODE from USR_INFO
            usr_loginlog.SUL_LOGINDATE,
            usr_loginlog.SUL_IPADDRESS,
            usr_chngpass.SSP_DATETIME,
            usr_chngpass.SSP_OPERATION,
            usr_chngpass.SSP_REMARKS,
            usr_chngpass.SSP_NETWORKID,
            uw_location.PLC_DESC  -- Get the location description
        FROM 
            AILMIS.USR_INFO usr_info
        JOIN 
            AILMIS.USR_LOGINLOG usr_loginlog ON usr_info.SUS_USERCODE = usr_loginlog.SUS_USERCODE
        LEFT JOIN 
            AILMIS.USR_CHNGPASS usr_chngpass ON usr_info.SUS_USERCODE = usr_chngpass.SSP_USERCODE
        LEFT JOIN 
            AILMIS.uw_location ON usr_info.PLC_LOCACODE = uw_location.PLC_LOC_CODE  -- Join condition
        WHERE 
            usr_info.SUS_USERCODE IS NOT NULL
    ";

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