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
}
else {
	// GDH_DOC_REFERENCE_NO, KNOCKOFFAMOUNT, PVT_VCHTTYPE, LVH_VCHDNO, PDP_DEPT_CODE 
	
	// 				 INNER JOIN ailmis.uw_docheader  ON ailmis.uw_docheader.GDH_DOC_REFERENCE_NO = ailmis.GL_COLLECTION.DOCREF 
	
	// WHERE PLC_LOC_CODE = '20107' AND PDP_DEPT_CODE = '13' AND GIH_DOCUMENTNO = '00704' AND GIH_YEAR = '2024'
	



				$query = "SELECT * FROM AILMIS.uw_docheader";
                
				 
			
				
				$combo = oci_parse($conn, $query);
				
                                                if (!$combo) {
                                                                $e1 = oci_error($conn);
                                                                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                                                }

                                                // Perform the logic of the query
                                                $r1 = oci_execute($combo);
                                                if (!$r1) {
                                                                $e1 = oci_error($combo);
                                                                trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
                                                }
												$count = 0;

												 $posts_arr = array();
                                                  while($res = oci_fetch_array($combo, OCI_ASSOC+OCI_RETURN_NULLS)) {
															   //array_push($posts_arr, $res);
															   $jsonarray[] = json_encode($res);
															   echo "</br>";
															   echo $jsonarray[$count];
															   $count++;
														
                                                }
												
												
												
												
}
// Close the Oracle connection
//oci_close($conn);
?>
