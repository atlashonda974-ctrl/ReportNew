<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get date parameters from request or default to today
$dateFrom = isset($_GET['datefrom']) ? $_GET['datefrom'] : date('d-M-Y');
$dateTo   = isset($_GET['dateto']) ? $_GET['dateto'] : date('d-M-Y');

// Connect to Oracle
$conn = oci_connect('AILMIS', 'AILMIS', 'orcl');
if (!$conn) {
    $e = oci_error();
    die(json_encode(['status' => 'error', 'message' => htmlentities($e['message'])]));
}

// Query login history with all joins but select only needed fields
$query = "
    SELECT 
        ulh.SAH_USERCODE,
        TO_CHAR(ulh.SAH_LOGINDATE, 'DD-MON-YYYY HH24:MI:SS') AS SAH_LOGINDATE,
        ulh.SAH_IPADDRESS,
        ulh.SAH_MESSAGETYPE,
        ulh.SAH_MESSAGE,
        TO_CHAR(ulh.SAH_LOGOUTDATE, 'DD-MON-YYYY HH24:MI:SS') AS SAH_LOGOUTDATE,
        uw.PLC_DESC
    FROM AILMIS.USR_LOGINHIST ulh
    LEFT JOIN AILMIS.USR_INFO ui ON ulh.SAH_USERCODE = ui.SUS_USERCODE
    LEFT JOIN AILMIS.uw_location uw ON ui.PLC_LOCACODE = uw.PLC_LOC_CODE
    WHERE ulh.SAH_USERCODE IS NOT NULL
      AND TRUNC(ulh.SAH_LOGINDATE) BETWEEN TO_DATE(:datefrom, 'DD-MON-YYYY')
                                       AND TO_DATE(:dateto, 'DD-MON-YYYY')
";

// Prepare and bind parameters
$statement = oci_parse($conn, $query);
oci_bind_by_name($statement, ":datefrom", $dateFrom);
oci_bind_by_name($statement, ":dateto", $dateTo);

// Execute
$r = oci_execute($statement);
if (!$r) {
    $e = oci_error($statement);
    die(json_encode(['status' => 'error', 'message' => htmlentities($e['message'])]));
}

// Fetch results
$final = [];
while ($row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $final[] = $row;
}

// Close connection
oci_close($conn);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'data'   => $final
]);
?>
