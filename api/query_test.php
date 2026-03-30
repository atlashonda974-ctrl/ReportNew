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
$combo = oci_parse($conn, "SELECT 
    m.PLC_LOCADESC AS LOC,  
    a.FAC_DATE AS \"Date\",  
    m.FAM_EXCH_RATE AS EXCHANGE_RATE, 
    a.FAC_SEQ_NO AS Document_No,
    a.FAC_AMOUNT AS AMOUNT, 
    m.FAM_QUANTITY AS QUANTITY, 
    m.PDP_DEPTDESC AS DEPARTMENT,  
    m.FFM_ASSET_CODE AS ASSET,  
    m.FFM_ASSET_DESC AS ASSET_DESC,   
    CASE 
        WHEN a.FAC_ADD_ASSET_NATURE = 'P' THEN 'Purchased' 
        ELSE a.FAC_ADD_ASSET_NATURE 
    END AS NATURE,  
    a.FAC_ADD_DEP_START_DT AS STR_DATE,  
    a.FAC_ADD_DEP_END_DT AS END_DATE,  
    a.FAC_ADD_SUPPLIER_NAME AS SUPPLIER  
FROM AILMIS.GL_FA_ACTIVITY a  
JOIN AILMIS.GL_FA_MAIN m  
    ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE  
    AND a.PDP_DEPT_CODE = m.PDP_DEPT_CODE  
    AND a.PET_RATE_TYPE = m.PET_RATE_TYPE  
    AND a.PCR_CODE = m.PCR_CODE  
");
// with where type 
// $combo = oci_parse($conn, "SELECT 
// m.PLC_LOCADESC AS LOC,  
// a.FAC_DATE AS \"Date\",  
// m.FAM_EXCH_RATE AS EXCHANGE_RATE, 
// a.FAC_SEQ_NO AS Document_No,
// a.FAC_AMOUNT AS AMOUNT, 
// m.FAM_QUANTITY AS QUANTITY, 
// m.PDP_DEPTDESC AS DEPARTMENT,  
// m.FFM_ASSET_CODE AS ASSET,  
// m.FFM_ASSET_DESC AS ASSET_DESC,   
// CASE 
//     WHEN a.FAC_ADD_ASSET_NATURE = 'P' THEN 'Purchased' 
//     ELSE a.FAC_ADD_ASSET_NATURE 
// END AS NATURE,  
// a.FAC_ADD_DEP_START_DT AS STR_DATE,  
// a.FAC_ADD_DEP_END_DT AS END_DATE,  
// a.FAC_ADD_SUPPLIER_NAME AS SUPPLIER  
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
// ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE  
// AND a.PDP_DEPT_CODE = m.PDP_DEPT_CODE  
// AND a.PET_RATE_TYPE = m.PET_RATE_TYPE  
// AND a.PCR_CODE = m.PCR_CODE  
// WHERE a.FAC_TRANSACTION_TYPE = 'A'");
// count total
// SELECT COUNT(*) AS TOTAL_RECORDS
// FROM (
//     SELECT 
//         1
//     FROM AILMIS.GL_FA_ACTIVITY a  
//     JOIN AILMIS.GL_FA_MAIN m  
//         ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE  
//         AND a.PDP_DEPT_CODE = m.PDP_DEPT_CODE  
//         AND a.PET_RATE_TYPE = m.PET_RATE_TYPE  
//         AND a.PCR_CODE = m.PCR_CODE  
//     WHERE a.FAC_TRANSACTION_TYPE = 'A'
// )


// $combo = oci_parse($conn, "SELECT 
//     m.PLC_LOCADESC AS LOC,  
//     a.FAC_DATE AS \"Date\",  
//     m.FAM_EXCH_RATE AS EXCHANGE_RATE, 
//     a.FAC_SEQ_NO AS Document_No,
//     a.FAC_AMOUNT AS AMOUNT, 
//     m.FAM_QUANTITY AS QUANTITY, 
//     m.PDP_DEPTDESC AS DEPARTMENT,  
//     m.FFM_ASSET_CODE AS ASSET,  
//     m.FFM_ASSET_DESC AS ASSET_DESC,   
//     CASE 
//         WHEN a.FAC_ADD_ASSET_NATURE = 'P' THEN 'Purchased' 
//         ELSE a.FAC_ADD_ASSET_NATURE 
//     END AS NATURE,  
//     a.FAC_ADD_DEP_START_DT AS STR_DATE,  
//     a.FAC_ADD_DEP_END_DT AS END_DATE,  
//     a.FAC_ADD_SUPPLIER_NAME AS SUPPLIER  
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE  
//     AND a.PDP_DEPT_CODE = m.PDP_DEPT_CODE  
//     AND a.PET_RATE_TYPE = m.PET_RATE_TYPE  
//     AND a.PCR_CODE = m.PCR_CODE");        without code
// $combo = oci_parse($conn, "SELECT COUNT(*) AS total_records
// FROM AILMIS.GL_FA_ACTIVITY a  
// JOIN AILMIS.GL_FA_MAIN m  
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE  
//     AND a.PDP_DEPT_CODE = m.PDP_DEPT_CODE  
//     AND a.PET_RATE_TYPE = m.PET_RATE_TYPE  
//     AND a.PCR_CODE = m.PCR_CODE
// WHERE m.FFM_ASSET_CODE LIKE '10201%'"); // Filter for vehicles starting with 103
//
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
//     ON a.FFM_ASSET_CODE = m.FFM_ASSET_CODE"); 



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
