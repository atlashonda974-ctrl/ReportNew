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
    // Get dates from parameters or use defaults (-10 to +30 days from current)
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('d-m-y', strtotime('-10 days'));
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('d-m-y', strtotime('+30 days'));
    
    // Function to properly parse date in DD-MM-YY format
    function parseDate($dateStr) {
        // Split the date by hyphen
        $parts = explode('-', $dateStr);
        
        // Check if we have 3 parts (day, month, year)
        if (count($parts) == 3) {
            // Try to create a valid date - assuming DD-MM-YY format
            return mktime(0, 0, 0, $parts[1], $parts[0], $parts[2] >= 100 ? $parts[2] : 2000 + intval($parts[2]));
        }
        return false;
    }
    
    // Parse the input dates
    $start_timestamp = parseDate($start_date);
    $end_timestamp = parseDate($end_date);
    
    if ($start_timestamp === false || $end_timestamp === false) {
        echo "Error: Invalid date format. Please use DD-MM-YY format.";
        exit;
    }
    
    // Check if the range exceeds 60 days
    $diff_days = ($end_timestamp - $start_timestamp) / (60 * 60 * 24);
    if ($diff_days > 60) {
        echo "Error: The date range should not exceed 60 days.";
        exit;
    }
    
    // Format dates for Oracle
    $oracle_start_date = date('d-M-y', $start_timestamp);
    $oracle_end_date = date('d-M-y', $end_timestamp);
    
    // Query to fetch records
    $query = "SELECT F.*, D.PPS_DESC 
    FROM AILMIS.RI_FACHD F
    LEFT JOIN AILMIS.uw_docheader D 
        ON F.GSI_DOC_REFERENCE_NO = D.GDH_DOC_REFERENCE_NO
    WHERE F.GSI_EXPIRYDATE BETWEEN TO_DATE(:start_date, 'DD-MON-YY') 
        AND TO_DATE(:end_date, 'DD-MON-YY')
        AND F.GSI_FACULTSI > 0
        AND F.GSI_FOREIGN_FACULTSI > 0";


    
    $combo = oci_parse($conn, $query);
    
    // Bind parameters with Oracle formatted dates
    oci_bind_by_name($combo, ":start_date", $oracle_start_date);
    oci_bind_by_name($combo, ":end_date", $oracle_end_date);
    
    if (!$combo) {
        $e1 = oci_error($conn);
        trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
    }
    
    $r1 = oci_execute($combo);
    
    if (!$r1) {
        $e1 = oci_error($combo);
        trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
    }
    
    echo "<h3>Date Range: $oracle_start_date to $oracle_end_date</h3>";
    echo "<p>Input dates: $start_date to $end_date</p>";
    
    $jsonarray = [];
    while ($res = oci_fetch_array($combo, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $jsonarray[] = json_encode($res);
        echo "</br>";
        echo end($jsonarray);
    }
}

oci_close($conn);
?>