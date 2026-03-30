<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\surveyor_report;
use App\Models\Sursurveyor_appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class MainController extends Controller
{
    // 172.16.22.204

    public function getLocations(Request $request){

        $urlMonth = "http://172.16.22.204/riskApi/monthwise.php";
        $month = file_get_contents($urlMonth);

        $loc = DB::table('locations')->pluck('flocid')->toArray();
        $paid = DB::select('select DISTINCT sr.frepref
        from surveyor_reports sr where NOT EXISTS(SELECT 1 FROM surveyor_reports WHERE sr.frepref = frepref AND fpaid = "N") ');


        $follow_result = DB::select('select * from locations where ffollow >= CURDATE() AND ffollow < CURDATE() + INTERVAL 16 DAY');
        $inspection_result = DB::select('select * from locations where fnxtinsp >= CURDATE() AND fnxtinsp < CURDATE() + INTERVAL 16 DAY');
        
        
        $result = (array) $paid;
        for($i = 0; $i < count($result); $i++){
            $paid1[$i] = $result[$i]->frepref;
        }         
        
        $fdate = DB::select('select DISTINCT loc.flocid from locations loc WHERE loc.ffollow = CURDATE()');
        $resultDate = (array) $fdate;
        $fdate1 = [];
        for($i = 0; $i < count($resultDate); $i++){
            $fdate1[$i] = $resultDate[$i]->flocid;
        }   
        
        $toDate = Carbon::now()->format('d-M-Y');
        $sum = 100000000;
        $pol = "";
        $insu = "";
        if($request->isMethod('post')){
            $data = $request->all();
            $selectedDate = $data['datepicker'];
            $pol = $data['poltyp'];
            $insu = $data['instyp'];
            $sum = $data['sumVal'];
            $sum = str_replace( ',', '', $sum);
            $toDate = date('d-M-Y', strtotime($selectedDate));
        }
		if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/main.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu";
        $sum = number_format(  intval($sum),0 );
        $response = file_get_contents($url);
        $newsData = json_decode($response);
		if($pol == ""){$pol = 0;}
        if($insu == ""){$insu = "All";}
        return view('main', compact('newsData', 'loc', 'month', 'toDate', 'sum', 'paid1', 'pol', 'fdate1', 'insu', 'follow_result', 'inspection_result'));
    }

    public function locExcel($toDate, $sum, $pol, $insu){

        $sum1 = str_replace( ',', '', $sum);
        $toDate1 = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/main.php?expiry=$toDate&sum=$sum1&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
    

        $timestamp = time();
        $filename = 'Location_Report_' . $timestamp . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $count = 1;
        $isPrintHeader = false;
        foreach ($newsData as $key => $value) {
            if (! $isPrintHeader) {
                echo "SR#"; echo "\t";
                echo "LOC CODE"; echo "\t";
                echo "LOCATION"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "AIL RETENTION"; echo "\t";
                echo "\n";
                $isPrintHeader = true;
            }

            echo $count;  echo "\t";
            echo $value->PRS_RISK_CODE;  echo "\t";
            echo $value->PRS_DESC;  echo "\t";
            echo $value->SUMINSURED;  echo "\t";
            echo $value->PREMIUM;  echo "\t";
            echo $value->RETENTION;  echo "\t";

            echo "\n";
            $count++;
        }
        exit();

    }


    public function locExcel2($toDate, $sum, $pol, $insu){

        $sum1 = str_replace( ',', '', $sum);
        $toDate1 = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        set_time_limit(0);
        $url = "http://172.16.22.204/riskApi/main.php?expiry=$toDate&sum=$sum1&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
    

        $timestamp = time();
        $filename = 'Location_Report_' . $timestamp . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $count = 1;
        $space = " ";
        $isPrintHeader = false;
        foreach ($newsData as $key => $value) {
            $countSub = 1;
            if (! $isPrintHeader) {
                echo "SR#"; echo "\t";
                echo "LOC CODE"; echo "\t";
                echo "LOCATION"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "AIL RETENTION"; echo "\t";

                echo "DOCUMENT NO"; echo "\t";
                echo "CLIENT"; echo "\t";
                echo "TYPE"; echo "\t";
                echo "LOCATION"; echo "\t";
                echo "ISSUE DATE"; echo "\t";
                echo "COMM DATE"; echo "\t";
                echo "EXPIRY DATE"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "AIL RETENTION"; echo "\t";
                echo "FAC SI"; echo "\t";
                echo "FAC PRE"; echo "\t";
                echo "\n";
                $isPrintHeader = true;
            }

            echo $count;  echo "\t";
            echo $value->PRS_RISK_CODE;  echo "\t";
            echo $value->PRS_DESC;  echo "\t";
            echo $value->SUMINSURED;  echo "\t";
            echo $value->PREMIUM;  echo "\t";
            echo $value->RETENTION;  echo "\t";
            echo "\n";

            $code = $value->PRS_RISK_CODE;
            set_time_limit(0);
            $urlDetail = "http://172.16.22.204/riskApi/locDetail.php?code=$code&expiry=$toDate&pol=$pol";
            $responseDetail = file_get_contents($urlDetail);
            $newsDataDetail = json_decode($responseDetail);



            foreach ($newsDataDetail as $key => $value) {
               
    
                echo $count; echo "\t";
                echo $space; echo "\t";
                echo $space; echo "\t";
                echo $space; echo "\t";
                echo $space; echo "\t";
                echo $space; echo "\t";

                echo $value->GSI_DOC_REFERENCE_NO; echo "\t";
                echo $value->PPS_DESC; echo"\t";
                echo $value->PDT_DOCTYPE; echo"\t";
                echo $value->PRS_DESC;  echo "\t";
                echo $value->GSI_ISSUEDATE;  echo "\t";
                echo $value->GSI_COMMDATE; echo "\t";
                echo $value->GSI_EXPIRYDATE; echo "\t";
                echo $value->SUMINSURED; echo "\t";
                echo $value->PREMIUM; echo "\t";
                echo $value->RETENTION; echo "\t";
                echo $value->GSI_FACULTSI; echo "\t";
                echo $value->GSI_FACULTPREM; echo "\t";
                echo "\n";
                $countSub++;
            }

            $count++;
        }
        exit();

    }


    public function locPDF($toDate, $sum, $pol, $insu){

        $urlMonth = "http://172.16.22.204/riskApi/monthwise.php";
        $month = file_get_contents($urlMonth);

        $loc = DB::table('locations')->pluck('flocid')->toArray();

        $paid = DB::select('select DISTINCT sr.frepref
        from surveyor_reports sr where NOT EXISTS(SELECT 1 FROM surveyor_reports WHERE sr.frepref = frepref AND fpaid = "N") ');
        $result = (array) $paid;
        for($i = 0; $i < count($result); $i++){
            $paid1[$i] = $result[$i]->frepref;
        }      

        $sum1 = str_replace( ',', '', $sum);
        $toDate1 = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/main.php?expiry=$toDate&sum=$sum1&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('Prints.main', compact('newsData', 'month', 'loc','toDate', 'paid1', 'sum', 'pol'));

    }

      //mainDetail
    
      public function mainDetail($code){

        $path = public_path().'/';
        $path = str_replace('\\', '/', $path);
        
        $loc = Location::where('flocid', $code)->first();;
        $rep = surveyor_report::where('frepref', $code)->get();
        // $app = Sursurveyor_appointment::where('flocid', $code)->get();
        $app = DB::table('sursurveyor_appointments')->join('surveyors', 'sursurveyor_appointments.fsuvid', 'surveyors.id')
        ->select('sursurveyor_appointments.*', 'surveyors.fsuvdsc as surv')
        ->where('flocid', $code)->get();
        return view('mainDetail', compact('loc', 'rep', 'app', 'path'));

    }

    

    //Clients

    public function getClients(Request $request){

        $loc = DB::table('locations')->pluck('flocid')->toArray();
        $toDate = Carbon::now()->format('d-M-Y');
        $sum = 100000000;
        $pol = "";
        $insu = "";
        if($request->isMethod('post')){
            $data = $request->all();
            $selectedDate = $data['datepicker'];
            $pol = $data['poltyp'];
            $insu = $data['instyp'];
            $sum = $data['sumVal'];
            $sum = str_replace( ',', '', $sum);
            $toDate = date('d-M-Y', strtotime($selectedDate));
        }
		if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/all_insured.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        $sum = number_format(  intval($sum),0 );
		if($pol == ""){$pol = 0;}
        if($insu == ""){$insu = "All";}
        return view('mainClient', compact('newsData', 'loc', 'sum', 'toDate', 'pol', 'insu'));
    }


    public function clientExcel($toDate, $sum, $pol, $insu){

        $sum = str_replace( ',', '', $sum);
        $toDate1 = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/all_insured.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);

        $timestamp = time();
        $filename = 'Clients_Report_' . $timestamp . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $count = 1;
        $isPrintHeader = false;
        foreach ($newsData as $key => $value) {
            if (! $isPrintHeader) {
                echo "SR#"; echo "\t";
                echo "LOC CODE"; echo "\t";
                echo "LOCATION"; echo "\t";
                echo "CLIENT"; echo "\t";
                echo "ISSUE DATE"; echo "\t";
                echo "COMM DATE"; echo "\t";
                echo "EXPIRY DATE"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "AIL RETENTION"; echo "\t";
                echo "\n";
                $isPrintHeader = true;
            }

            echo $count;  echo "\t";
            echo $value->PRS_RISK_CODE;  echo "\t";
            echo $value->PRS_DESC;  echo "\t";
            echo $value->PPS_DESC;  echo "\t";
            echo $value->GSI_ISSUEDATE;  echo "\t";
            echo $value->GSI_COMMDATE;  echo "\t";
            echo $value->GSI_EXPIRYDATE;  echo "\t";
            echo $value->GSI_COTOTALSI;  echo "\t";
            echo $value->GSI_COTOTALPREM;  echo "\t";
            echo $value->GSI_COMPANYRETENTION;  echo "\t";
            echo "\n";
            $count++;
        }
        exit();

    }

    public function clientPDF($toDate, $sum, $pol, $insu){

        $loc = DB::table('locations')->pluck('flocid')->toArray();
        $urlMonth = "http://172.16.22.204/riskApi/monthwise.php";
        $month = file_get_contents($urlMonth);

        $sum = str_replace( ',', '', $sum);
        $toDate1 = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        $url = "http://172.16.22.204/riskApi/all_insured.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('Prints.clients', compact('newsData', 'loc'));

    }
    

    //Loc Detail
    public function locDetail($code, $expiry, $pol){

		if($pol == 0){
			$pol = "";
		}
        $url = "http://172.16.22.204/riskApi/locDetail.php?code=$code&expiry=$expiry&pol=$pol";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        if($pol == ""){
			$pol = 0;
		}
        return view('locDetail', compact('newsData', 'code', 'expiry', 'pol'));
    }

    public function locDetailExcel($code, $expiry, $pol){

        if($pol == 0){
			$pol = "";
		}
        $url = "http://172.16.22.204/riskApi/locDetail.php?code=$code&expiry=$expiry&pol=$pol";
        $response = file_get_contents($url);
        $newsData = json_decode($response);

        $timestamp = time();
        $filename = 'LocDetail_Report_' . $timestamp . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $count = 1;
        $isPrintHeader = false;
        foreach ($newsData as $key => $value) {
            if (! $isPrintHeader) {
                echo "SR#"; echo "\t";
                echo "DOCUMENT NO"; echo "\t";
                echo "CLIENT"; echo "\t";
                echo "LOCATION"; echo "\t";
                echo "ISSUE DATE"; echo "\t";
                echo "COMM DATE"; echo "\t";
                echo "EXPIRY DATE"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "AIL RETENTION"; echo "\t";
                echo "FAC SI"; echo "\t";
                echo "FAC PRE"; echo "\t";
                echo "\n";
                $isPrintHeader = true;
            }

            echo $count; echo "\t";
            echo $value->GSI_DOC_REFERENCE_NO; echo "\t";
            echo $value->PPS_DESC; echo"\t";
            echo $value->PRS_DESC;  echo "\t";
            echo $value->GSI_ISSUEDATE;  echo "\t";
            echo $value->GSI_COMMDATE; echo "\t";
            echo $value->GSI_EXPIRYDATE; echo "\t";
            echo $value->SUMINSURED; echo "\t";
            echo $value->PREMIUM; echo "\t";
            echo $value->RETENTION; echo "\t";
            echo $value->GSI_FACULTSI; echo "\t";
            echo $value->GSI_FACULTPREM; echo "\t";
            echo "\n";
            $count++;
        }
        exit();

    }

    public function locDetailPDF($code, $expiry, $pol){

        if($pol == 0){
			$pol = "";
		}
        $url = "http://172.16.22.204/riskApi/locDetail.php?code=$code&expiry=$expiry&pol=$pol";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('Prints.locDetail', compact('newsData'));

    }
    

    // UW
    public function getUWLocations(Request $request){


        $toDate = Carbon::now()->format('d-M-Y');
        $sum = 100000000;
        $pol = "";
        $insu = "";
        $doc = "";
        if($request->isMethod('post')){
            $data = $request->all();
            $selectedDate = $data['datepicker'];
            $pol = $data['poltyp'];
            $doc = $data['doc'];
            $insu = $data['instyp'];
            $sum = $data['sumVal'];
            $sum = str_replace( ',', '', $sum);
            $toDate = date('d-M-Y', strtotime($selectedDate));
        }
		if($insu == "All"){$insu = "";}
        if($doc == "All"){$doc = "";}
        $url = "http://172.16.22.204/riskApi/main_uw_cover.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu&doc=$doc";
        $sum = number_format(  intval($sum),0 );
        $response = file_get_contents($url);
        $newsData = json_decode($response);
		if($pol == ""){$pol = 0;}
        if($insu == ""){$insu = "All";}
        if($doc == ""){$doc = "All";}
        return view('UW.uw_data', compact('newsData', 'sum', 'toDate', 'pol', 'insu', 'doc'));
    }


    public function uwExcel($toDate, $sum, $pol, $insu, $doc){

        $sum = str_replace( ',', '', $sum);
        $toDate = date('d-M-Y', strtotime($toDate));
        if($pol == 0){$pol = "";}
        if($insu == "All"){$insu = "";}
        if($doc == "All"){$doc = "";}
        $url = "http://172.16.22.204/riskApi/main_uw_cover.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu&doc=$doc";
        $response = file_get_contents($url);
        $newsData = json_decode($response);

        $timestamp = time();
        $filename = 'UW_Report_' . $timestamp . '.xls';
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $count = 1;
        $isPrintHeader = false;
        foreach ($newsData as $key => $value) {
            if (! $isPrintHeader) {
                echo "SR#"; echo "\t";
                echo "LOC CODE"; echo "\t";
                echo "CLIENT"; echo "\t";
                echo "BRANCH"; echo "\t";
                echo "DOC NUM"; echo "\t";
                echo "DOC TYPE"; echo "\t";
                echo "ISSUE DATE"; echo "\t";
                echo "COMM DATE"; echo "\t";
                echo "EXPIRY DATE"; echo "\t";
                echo "SUM INSURED"; echo "\t";
                echo "PREMIUM"; echo "\t";
                echo "\n";
                $isPrintHeader = true;
            }

            if($value->PDT_DOCTYPE == "T" ) {$type = "Cover Note";}
            else if($value->PDT_DOCTYPE == "P" ) {$type = "Policy";}
            else if($value->PDT_DOCTYPE == "C" ) {$type = "Certificate";}
            else if($value->PDT_DOCTYPE == "E" ) {$type = "Endorsment";}
            else if($value->PDT_DOCTYPE == "O" ) {$type = "Open Policy";}
            else if($value->PDT_DOCTYPE == "N" ) {$type = "Renewal";}
            else if($value->PDT_DOCTYPE == "A" ) {$type = "Amendment";}
            else {$type = "OTHERS";}

            echo $count;  echo "\t";
            echo $value->PLC_LOC_CODE;  echo "\t";
            echo $value->PPS_DESC;  echo "\t";
            echo $value->PLC_LOCADESC;  echo "\t";
            echo $value->GDH_DOC_REFERENCE_NO;  echo "\t";
            echo $type;  echo "\t";
            echo $value->GDH_ISSUEDATE;  echo "\t";
            echo $value->GDH_COMMDATE;  echo "\t";
            echo $value->GDH_EXPIRYDATE;  echo "\t";
            echo $value->GDH_TOTALSI;  echo "\t";
            echo $value->GDH_GROSSPREMIUM;  echo "\t";
            echo "\n";
            $count++;
        }
        exit();

    }

    public function uwPDF($toDate, $sum, $pol, $insu, $doc){

        $urlMonth = "http://172.16.22.204/riskApi/monthwise.php";
        $month = file_get_contents($urlMonth);
        if($pol == 0){$pol = "";}
        if($insu == 0){$insu = "";}
        if($doc == 0){$doc = "";}
        $sum = str_replace( ',', '', $sum);
        $toDate = date('d-M-Y', strtotime($toDate));
        $url = "http://172.16.22.204/riskApi/main_uw_cover.php?expiry=$toDate&sum=$sum&pol=$pol&insu=$insu&doc=$doc";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('Prints.uw', compact('newsData'));

    }
    


    public function uwLocDetail($code, $type, $dept, $business, $record, $doc, $year){
        $url = "http://172.16.22.204/riskApi/uw_detail.php?code=$code&type=$type&dept=$dept&bus=$business&record=$record&doc=$doc&year=$year";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('UW.uw_detail', compact('newsData'));
    }

    public function uwDetail($doc){


        $urlDoc = "http://172.16.22.204/riskApi/main_uw_cover_single.php?doc=$doc";
        $responseDoc = file_get_contents($urlDoc);
        $docData = json_decode($responseDoc);

        $code = $docData[0]->PLC_LOC_CODE;
        $type = $docData[0]->PDT_DOCTYPE;
        $dept = $docData[0]->PDP_DEPT_CODE;
        $business = $docData[0]->PBC_BUSICLASS_CODE;
        $record = $docData[0]->GDH_RECORD_TYPE;
        $doc = $docData[0]->GDH_DOCUMENTNO;
        $year = $docData[0]->GDH_YEAR;
        
        $url = "http://172.16.22.204/riskApi/uw_detail.php?code=$code&type=$type&dept=$dept&bus=$business&record=$record&doc=$doc&year=$year";
        $response = file_get_contents($url);
        $newsData = json_decode($response);
        return view('UW.uw_detail', compact('newsData'));
    }

}
