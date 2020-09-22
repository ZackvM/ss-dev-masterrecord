<?php 


class transientdataservices { 

  public $responseCode = 400;
  public $rtnData = "";

function __construct() { 
    $args = func_get_args(); 
    $nbrofargs = func_num_args(); 
    //$this->rtnData = $args[0] . " " . $args[1];    
    if (trim($args[0]) === "") { 
    } else { 
      $request = explode("/", $args[0]); 
      if (trim($request[2]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING " . json_encode($request),"ITEMSFOUND" => 0, "DATA" => array()    ));
      } else { 
        $dp = new tifunctions(); 
        if (method_exists($dp, $request[2])) { 
          $funcName = trim($request[2]); 
          $dataReturned = $dp->$funcName($args[0], $args[1]); 
          $this->responseCode = $dataReturned['statusCode']; 
          $this->rtnData = json_encode($dataReturned['data']);
        } else { 
          $this->responseCode = 404; 
          $this->rtnData = json_encode(array("MESSAGE" => "END-POINT FUNCTION NOT FOUND: {$request[3]}","ITEMSFOUND" => 0, "DATA" => ""));
        }
      }
    }
}

}

class tifunctions {

  function runbanksearch( $rqst, $passeddata ) {
    //{"requester":"","requesteddatapage":0,"requestedsite":"THYROID","requesteddiagnosis":"BEGIGN","requestedcategory":"","requestedpreparation":["PB", "FROZEN"]}
    $responseCode = 400;
    $rows = array();
    $msgArr = array(); 
    $errorInd = 0;
    $itemsfound = 0;
    $pdta = json_decode( $passeddata, true); 
    //TODO:  DATA CHECKS GO HERE
    //TODO:  BUILD DATA CHECK CLASS
    
    require(serverkeys . "/sspdo.zck");
//    foreach ($pdta['requestedpreparation'] as $friend) {
//      $friendsArray[] = PDO::quote($friend);
//    }
//    $friendsArray2 = join(', ', $friendsArray);

    for ( $i = 0; $i < count( $pdta['requestedpreparation'] ); $i++ ) {
      $friendsArray[] = "'" . $pdta['requestedpreparation'][$i] . "'";
    }
    $friendsArray2 = implode(',',$friendsArray );

    $transSQL = <<<TSQLSTMT
SELECT 'EST' as divisioncode, replace(sg.bgs,'_','') as divisionallabel	, concat(trim(ucase(ifnull(bs.anatomicSite,''))), if(ifnull(bs.subsite,'') = '','',concat(' / ', ifnull(bs.subsite,''))))   as site , concat(trim(ucase(ifnull(bs.diagnosis,''))), if(ifnull(bs.subdiagnos,'') = '','',concat(' / ',ifnull(bs.subdiagnos,'')))) as diagnosis, trim(ucase(ifnull(bs.tissType,''))) as specimencategory, replace( trim(ucase(ifnull(bs.metsSite,''))),' /','') as metssite , trim(ucase(ifnull(pty.dspvalue,''))) as proceduretype, sg.hourspost, concat(trim(ucase(ifnull(sg.prepmethod,''))), if(ifnull(sg.preparation,'')='','', concat(': ',trim(ucase(ifnull(sg.preparation,'')))))) as preparation, if( ifnull(sg.metric,'')='xx' OR ifnull(sg.metric,'') = '','', concat(ifnull(sg.metric,''),ifnull(uom.dspvalue,''))) metric, if( ifnull(bs.pxiAge,0) = 0, '', concat(ifnull(bs.pxiAge,''),ifnull(aum.longvalue,''))) as age, trim(ucase(ifnull( bs.pxiRace,'' ))) as race, trim(ucase(ifnull(sx.dspvalue,''))) as sex, trim(ucase(ifnull(cx.dspvalue,'Unknown'))) as chemotherapy, trim(ucase(ifnull(rx.dspvalue,'Unknown'))) as radiation, 'https://dev.chtneast.org/print-obj/pathology-report/' as prurlbase, ifnull(bs.pathreportid,'') as pathologyrptdocid, '' as transinvvalue, '' as additionallabel, '' as additionaldata FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'METRIC') uom on sg.metricUOM = uom.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') pty on bs.procType = pty.menuvalue left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'AGEUOM') aum on bs.pxiAgeUOM = aum.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PXSEX') sx on bs.pxiGender = sx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') rx on bs.radind = rx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') cx on bs.chemoInd = cx.menuvalue where sg.voidind <> 1 and sg.segstatus = 'BANKED' and bs.tisstype like :spc and (bs.anatomicsite like :site1 or bs.subsite like :site2 or bs.metsSite like :site3 ) and (bs.diagnosis like :dx1 or bs.subdiagnos like :dx2 ) and ( sg.prepmethod IN ( {$friendsArray2} ) ) and trim(prepmethod) <> 'SLIDE'
TSQLSTMT;

    $bankRS = $conn->prepare( $transSQL );
    $bankRS->execute(array( ':spc' => "{$pdta['requestedcategory']}%", ':site1' => "{$pdta['requestedsite']}%" , ':site2' => "{$pdta['requestedsite']}%" , ':site3' => "{$pdta['requestedsite']}%", ':dx1' => "{$pdta['requesteddiagnosis']}%", ':dx2' => "{$pdta['requesteddiagnosis']}%"   )); 

    $itemsfound = $bankRS->rowCount();
    while ( $b = $bankRS->fetch(PDO::FETCH_ASSOC) ) {
      $record['divisioncode'] = $b['divisioncode'];
      $record['divisionallabel'] = $b['divisionallabel'];
      $record['site'] = $b['site'];
      $record['diagnosis'] = $b['diagnosis'];
      $record['specimencategory'] = $b['specimencategory'];
      $record['metssite'] = $b['metssite'];
      $record['proceduretype'] = $b['proceduretype'];
      $record['hourspost'] = $b['hourspost'];
      $record['preparation'] = $b['preparation'];
      $record['metric'] = $b['metric'];
      $record['age'] = $b['age'];
      $record['race'] = $b['race'];
      $record['sex'] = $b['sex'];
      $record['chemotherapy'] = $b['chemotherapy'];
      $record['radiation'] = $b['radiation'];
      $record['pathologyrpturl'] = ( trim($b['pathologyrptdocid']) !== "" ) ? $b['prurlbase'] . cryptservice( $b['pathologyrptdocid'], 'e' ) : "";
      $record['transinvvalue'] = $b['transinvvalue'];
      $record['additionallabel'] = $b['additionallabel'];
      $record['additionaldata'] = $b['additionaldata'];
      $dta[] = $record;
    }

    $responseCode = 200;
    $msg = $msgArr;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;         
  }


}
