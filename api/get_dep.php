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

// $combo = oci_parse($conn, "SELECT 
//     a.FFM_ASSET_CODE AS \"Asset Code\",
//     a.FAC_DSP_BILL_AMOUNT AS \"Sale Proceed\",
//     a.FAC_ACTUAL_AMOUNT AS \"Original Cost\",
//     a.FAC_ACTUAL_ACC_DEP_AMT AS \"Accumulated Depreciation\",
//     a.FAC_DATE AS \"Date of Sale\",
//     a.FAC_DSP_PARTY_NAME AS \"Party Member\",
//     m.FAM_ACQUISITION_DATE AS \"Date of Purchase\",
//     m.FFM_ASSET_DESC AS \"Asset Description\"
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE
// ");
//with where type D
$combo = oci_parse($conn, "SELECT 
    a.FFM_ASSET_CODE AS \"Asset Code\",
    a.FAC_DSP_BILL_AMOUNT AS \"Sale Proceed\",
    a.FAC_ACTUAL_AMOUNT AS \"Original Cost\",
    a.FAC_ACTUAL_ACC_DEP_AMT AS \"Accumulated Depreciation\",
    a.FAC_DATE AS \"Date of Sale\",
    a.FAC_DSP_PARTY_NAME AS \"Party Member\",
    m.FAM_ACQUISITION_DATE AS \"Date of Purchase\",
    m.FFM_ASSET_DESC AS \"Asset Description\"
FROM AILMIS.GL_FA_ACTIVITY a  
JOIN AILMIS.GL_FA_MAIN m  
    ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE
WHERE a.FAC_TRANSACTION_TYPE = 'D'");


// $combo = oci_parse($conn, "SELECT 
//     a.FFM_ASSET_CODE AS \"Asset Code\",
//     a.FAC_DSP_BILL_AMOUNT AS \"Sale Proceed\",
//     a.FAC_ACTUAL_AMOUNT AS \"Original Cost\",
//     a.FAC_ACTUAL_ACC_DEP_AMT AS \"Accumulated Depreciation\",
//     a.FAC_DATE AS \"Date of Sale\",
//     a.FAC_DSP_PARTY_NAME AS \"Party Member \",
//     m.FAM_ACQUISITION_DATE AS \"Date of Purchase\",
//     m.FFM_ASSET_DESC AS \"Asset Description\"
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE"); without code
// $combo = oci_parse($conn, "SELECT 
//     a.FFM_ASSET_CODE AS \"Asset Code\",
//     a.FAC_DSP_BILL_AMOUNT AS \"Sale Proceed\",
//     a.FAC_ACTUAL_AMOUNT AS \"Original Cost\",
//     a.FAC_ACTUAL_ACC_DEP_AMT AS \"Accumulated Depreciation\",
//     a.FAC_DATE AS \"Date of Sale\",
//     a.FAC_DSP_PARTY_NAME AS \"Party Member\",
//     m.FAM_ACQUISITION_DATE AS \"Date of Purchase\",
//     m.FFM_ASSET_DESC AS \"Asset Description\"
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE");

// oci_execute($combo);

// // Count the number of rows
// $record_count = 0;
// while (oci_fetch_array($combo, OCI_ASSOC)) {
//     $record_count++;
// }

// echo "Total Records: " . $record_count;




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
