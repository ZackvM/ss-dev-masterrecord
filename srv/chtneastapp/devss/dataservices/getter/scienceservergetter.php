<?php

class objgetter { 

  public $responseCode = 503;
  public $rtnData = "";

  function __construct() { 
    $args = func_get_args(); 
    $nbrofargs = func_num_args(); 

    if (trim($args[0]) === "") { 
    } else { 
      $request = explode("/", $args[0]); 
      if (trim($request[2]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING","ITEMSFOUND" => 0, "DATA" => ""));
      } else {  
        $obj = new objlisting(); 
        if (method_exists($obj, $request[2])) { 
          $funcName = trim($request[2]); 
          //FIRST ARGUMENT IN FUNCTION BELOW IS USUALLY A RECORD IDENTIFIER 
          $dataReturned = $obj->$funcName("{$request[3]}",$args[0]); 
          $this->responseCode = $dataReturned['statusCode']; 
          $this->rtnData = json_encode($dataReturned['data']);
        } else { 
          $this->responseCode = 404; 
          $this->rtnData = json_encode(array("MESSAGE" => "END-POINT FUNCTION NOT FOUND: {$request[2]}","ITEMSFOUND" => 0, "DATA" => ""));
        }
     }
    }
  }

}

class objlisting { 

function sscalendar($request,$urirqst) { 
    $rows = array(); 
    //TODO:  Check inputs for errors
    $rq = explode("/",$urirqst);
    $rqstCal = $rq[3]; 
    $rqstMnt = $rq[4];
    $rqstYr = $rq[5];     
    $rtnCalendar = buildcalendar($rqstCal, $rqstMnt, $rqstYr);
    $rows['statusCode'] = 200; 
    $rows['data'] = array('MESSAGE' => '', 'ITEMSFOUND' => 0, 'DATA' => $rtnCalendar);
    return $rows;
}

function subpreparationmenu($request) { 
    $responseCode = 400;  
    $msg = "";
    $itemsfound = 0;
    $rtnData = array();
    if (trim($request) !== "") { 
      require(serverkeys . "/sspdo.zck");               
      $SQL = "select pr.menuvalue, pr.longvalue, pr.dsporder  from (SELECT menuid FROM four.sys_master_menus where menu = 'PREPMETHOD' and menuvalue = :preparation) as pm left join (SELECT * FROM four.sys_master_menus where menu = 'PREPDETAIL' and dspind = 1) as pr on pm.menuid = pr.parentid order by pr.dsporder";   
      $rs = $conn->prepare($SQL); 
      $rs->execute(array(':preparation' => $request));
      if ($rs->rowCount() < 1) { 
        $responseCode = 404; 
        $msg = "NO PREPARATIONS FOUND ({$request})";
      } else { 
          $itemsfound = $rs->rowCount();
          while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
            $rtnData[] = $r;   
          }          
          $responseCode = 200;
      }
    } else { 
        $msg = "NO PREPARATION METHOD SPECIFIED";
    }
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rtnData);
     return $rows;
}

function globalmenu($request) {
     $rows = array(); 
     $gMenu = $request;
     //TO LOAD ALL METHODS IN A CLASS INTO AN ARRAY USE get_class_methods
     $gm = new globalMenus(); 
     if (method_exists($gm,$gMenu)) { 
       $SQL = $gm->$gMenu($rParts[3]);
       if (trim($SQL) !== "") {
         //RUN SQL - RETURN RESULTS
         require(serverkeys . "/sspdo.zck");
         $r = $conn->prepare($SQL); 
         $r->execute(); 
         $itemsFound = $r->rowCount();
         while ($rs = $r->fetch(PDO::FETCH_ASSOC)) { 
           $data[] = $rs;
         }
         $rows['statusCode'] = 200;
         $rows['data'] = array('MESSAGE' => '', 'ITEMSFOUND' => $itemsFound, 'DATA' => $data);
       } else { 
         $rows['statusCode'] = 503;
         $rows['data'] = array('MESSAGE' => 'NO SQL RETURNED', 'ITEMSFOUND' => 0,  'DATA' => '');
       }
     } else {
        $rows['statusCode'] = 404; 
        $rows['data'] = array('MESSAGE' => 'MENU NOT FOUND', 'ITEMSFOUND' => 0, 'DATA' => $request);
     }
     return $rows;
}


function template($whichobj,$rqst) { 
    session_start();
    $responseCode = 500;
    $msg = "status message " . session_id();
    $itemsfound = 0;
    $dta = array();
    
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}        

function ssuniversalcontrols() { 
    $rows = array(); 
    require(serverkeys . "/sspdo.zck");
    $unverSQL = "SELECT googleiconcode, explainerline, menuvalue FROM four.sys_master_menus where menu = 'SSUNIVERSECONTROL' and dspind = 1 order by dspOrder";
    $top = $conn->prepare($unverSQL);
    $top->execute(); 
    $itemsFound = $top->rowCount();    
    while ($rs = $top->fetch(PDO::FETCH_ASSOC)) {
       $rows[] = $rs; 
    }      
    $rows['statusCode'] = 200; 
    $rows['data'] = array("MESSAGE" => "", "ITEMSFOUND" => $itemsFound, "DATA" => $rows);
    return $rows;  
}

function shipdocqrycriteria($whichobj, $rqst) { 
    session_start();
    $responseCode = 404;
    $msg = "status message " . session_id() . " " . $whichobj;
    $itemsfound = 0;
    $dta = array();
    require(genAppFiles . "/dataconn/sspdo.zck");  
    $getSQL = "SELECT bywhom, date_format(onwhen, '%m/%d/%Y') as qrydate, qrytype, jsoncriteria FROM four.srv_coord_qry_capture where concat(qryid,qryselector) = :qryid and qrytype = :qrytype";
    $rs = $conn->prepare($getSQL);
    $rs->execute( array(':qrytype' => 'SHIP',':qryid' => $whichobj ));
    if ($rs->rowCount() < 1) { 
       $msg = "QUERY OBJECT NOT FOUND";    
    } else { 
       $dta = $rs->fetch(PDO::FETCH_ASSOC);       
       $itemsfound = 1;
       $responseCode = 200;
       $msg = "";
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('status' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;          
}

function bankqrycriteria($whichobj,$rqst) { 
    session_start();
    $responseCode = 404;
    $msg = "status message " . session_id() . " " . $whichobj;
    $itemsfound = 0;
    $dta = array();
    require(genAppFiles . "/dataconn/sspdo.zck");  
    $getSQL = "SELECT bywhom, date_format(onwhen, '%m/%d/%Y') as qrydate, qrytype, jsoncriteria FROM four.srv_coord_qry_capture where concat(qryid,qryselector) = :qryid and qrytype = :qrytype";
    $rs = $conn->prepare($getSQL);
    $rs->execute( array(':qrytype' => 'BANK',':qryid' => $whichobj ));
    if ($rs->rowCount() < 1) { 
       $msg = "QUERY OBJECT NOT FOUND";    
    } else { 
       $dta = $rs->fetch(PDO::FETCH_ASSOC);       
       $itemsfound = 1;
       $responseCode = 200;
       $msg = "";
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('status' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}        

function biogroupsearch($whichobj, $rqst) { 
    $responseCode = 400;   
    $msg = "status message";
    $itemsfound = 0;
    $dta = array();
    if ($whichobj === "") { 
    } else { 
      require(serverkeys . "/sspdo.zck");  
      $headSQL = "select  objid, bywho, date_format(onwhen, '%m/%d/%Y') as onwhendsp, srchterm from four.objsrchdocument where doctype = 'COORDINATOR-BIO' and objid = :objid"; 
      $headR = $conn->prepare($headSQL);
      $headR->execute(array(':objid' => $whichobj));
      if ($headR->rowCount() < 1) { 
        $responseCode = 404;
        $msg = "BIOGROUP SEARCH ID OBJECT NOT FOUND";
      } else { 
        $dta['head'] = $headR->fetch(PDO::FETCH_ASSOC);
        $searchTerm = $dta['head']['srchterm'];
        $rsltArr = runbiogroupsearchquery($searchTerm);
       
        $dta['searchresults'] = $rsltArr; 

      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}



function docsrch($whichobj, $rqst) { 
    session_start();
    $responseCode = 500;   
    $msg = "status message";
    $itemsfound = 0;
    $dta = array();
    if ($whichobj === "") { 
    } else { 
      require(genAppFiles . "/dataconn/sspdo.zck");  
      $headSQL = "select  objid, srchterm, doctype, bywho, date_format(onwhen, '%m/%d/%Y') as onwhendsp from four.objsrchdocument  where objid = :objid"; 
      $headR = $conn->prepare($headSQL);
      $headR->execute(array(':objid' => $whichobj));
      if ($headR->rowCount() < 1) { 
        $responseCode = 404;
        $msg = "DOC SEARCH OBJECT NOT FOUND";
      } else { 
          $dta['head'] = $headR->fetch(PDO::FETCH_ASSOC);
          $typeRq = $dta['head']['doctype'];
          $searchTerm = $dta['head']['srchterm'];
           switch ($typeRq) { 
               case 'CHARTRVW':
                  $ssql = "SELECT trim(substr(cr.chart,1,200)) as abstract, chartid as dspmark, date_format(cr.lastupdate,'%m/%d/%Y') as dter FROM masterrecord.ut_chartreview cr  where dspind = 1 and chart like :srchtrm limit 250";
                   $strm = "%{$searchTerm}%";
               break;
               case 'PRBGNBR':
                   $ssql = "select prid, selector, trim(substr(stripHTML(pathreport),1,200)) as abstract, dnpr_nbr as dspmark from masterrecord.qcpathreports where dnpr_nbr like :srchtrm order by dnpr_nbr desc  limit 250";
                   $strm = "{$searchTerm}%";
                   break;
               case 'PATHOLOGYRPT':
                   $ssql = "select prid, selector, trim(substr(stripHTML(pathreport),1,200)) as abstract, dnpr_nbr as dspmark from masterrecord.qcpathreports where pathreport like :srchtrm order by dnpr_nbr desc  limit 250";
                   $strm = "% {$searchTerm} %";
                   break;
               case 'SHIPDOC': 
                   $ssql = "SELECT shipdocRefID prid, concat(substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand()*36+1, 1)
             ) as selector, substr(concat('000000',ifnull(shipdocRefID,'000000')),-6) as dspmark, concat(ifnull(status,'') , ' (' , ifnull(date_format(statusdate,'%m/%d/%Y'),'') , ') | shipped: ', ifnull(date_format(shipdate, '%m/%d/%Y'),'') , ' | investigator: ', ifnull(invCode,'')) as abstract FROM masterrecord.ut_shipdoc sd where shipdocrefid = :srchtrm ";
                   $strm = (int)$searchTerm;
                   break;
               //TODO:ADD A DEFAULT ERROR IF NOT ABOVE
           }

           $rs = $conn->prepare($ssql);
           $rs->execute(array(':srchtrm' => $strm));
           $itemsfound = $rs->rowCount();
           $dta['records'] = array();    
           if ((int)$itemsfound > 0) {
           //$msg = $conn->errorInfo();
              while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
                 $dta['records'][] = $r;
              }                 
           }
           $responseCode = 200;
           $msg = "SUCCESS";
      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}
 
}


class globalMenus {

    function chtnvocabularyspecimencategory() {
      return "select distinct catid as codevalue, category as menuvalue, 0 as useasdefault, catid as lookupvalue from masterrecord.voc_chtn_all where trim(ifnull(catid,'')) <> '' order by category";
    }

    function allinstitutions() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'INSTITUTION' order by mnu.dspOrder";
    }

    function allsegmentstati() {
      return "SELECT ucase(ifnull(mnu.dspvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SEGMENTSTATUS' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function allshipdocstatus() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SDStat' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function allpreparationmethods() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'PREPMETHOD' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function specimencategorylive() {
      return "SELECT distinct ucase(trim(ifnull(tisstype,''))) as codevalue, ucase(trim(ifnull(tisstype,''))) as menuvalue, 0 as useasdefault FROM masterrecord.ut_procure_biosample where trim(ifnull(tisstype,'')) <> '' order by codevalue";
    }

    function metricuoms() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'METRIC' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function qmsstatus() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'QMSStatus' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function yesno() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'YESNO' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function allproctypes() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'PROCTYPE' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function downstreamcollectiontypes($parentvalue) {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'COLLECTIONT' and mnu.dspind = 1 and mnu.parentvalue = '{$parentvalue}' order by mnu.dsporder";
    }

    function unknownmet() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'UNKNOWNMET' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function allasites() {
      return "SELECT distinct site as codevalue, site as menuvalue, 0 as useasdefault, '' as lookupvalue FROM four.voc_chtn_all where 1=1 and (trim(ifnull(site,'')) <> '' and site <> '<NONE>') order by site";
    }

    function alldxs() {
      return "SELECT distinct dx as codevalue, dx as menuvalue, 0 as useasdefault, '' as lookupvalue FROM four.voc_chtn_all where 1=1 and (trim(ifnull(dx,'')) <> '' and dx <> '<NONE>') order by dx";
    }

    function ageuoms() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'AGEUOM' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function pxirace() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'PXRACE' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function pxisex() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'PXSEX' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function pxicx() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'CX' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function pxirx() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'RX' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function prcinfc() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'INFC' and queriable = 1 and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function prcprpt() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'PRpt' and queriable = 1 and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function upennsogi() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SOGI' and queriable = 1 and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function pbrpyesnoqstn() {
        return "SELECT questionapplicationid codevalue, question menuvalue, '' useasdefault, '' lookupvalue FROM pfc.sys_project_questions order by dspOrd";
    }

    function standardsalutations() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SALUTATIONS' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function statesprovincelist() {
      return "SELECT ifnull(dspvalue,'') as codevalue, ifnull(longvalue,'') as menuvalue, ifnull(useasdefault,0) as useasdefault, '' as lookupvalue FROM four.sys_master_menus where menu = 'USSTATES' order by additionalinformation, longvalue";
    }

    function pfrpactions() {
      return "SELECT rsts.actionid as codevalue, rsts.reviewaction as menuvalue, 0 as useasdefault, '' as lookupvalue FROM pfc.appdata_project_reviewerstatus rsts where rsts.furtheractionind = 0 order by rsts.dspOrd";
    }

}

function runbiogroupsearchquery($srchrqstjson) {
  $rqstDta = json_decode($srchrqstjson, true);
  $qryArr = array();
  $rtndta = array(); 
  $errors = 0;
  $errorsMsg = "";
  $fieldsQueried = 0;
  $sqlCritAdd = "";

  foreach ($rqstDta as $fldname => $fldvalue) {
    if (trim($fldvalue) !== "" && $fldname !== "qryType") { 
      switch ($fldname) { 

        case 'BG':
           $bgvalue = str_replace("Z","1.","{$fldvalue}");
           if (strpos($bgvalue,"-") !== false || strpos($bgvalue,",") !== false) { 
             if (strpos($bgvalue,"-") !== false) { 
               //RANGE
               $bgarr = explode("-",$bgvalue);
               $sqlCritAdd .= " and ( bs.pbiosample between cast( :bgnbrOne as DECIMAL(20,8)) and cast( :bgnbrTwo as DECIMAL(20,8))  ) ";
               $qryArr += [':bgnbrOne' => "{$bgarr[0]}" ];
               $qryArr += [':bgnbrTwo' => "{$bgarr[1]}" ];
               $fieldsQueried++;
             }
             if (strpos($bgvalue,",") !== false) { 
                 //SERIES
               $seriesstr = "";
               $bgarr = explode(",",$bgvalue); 
               $cntr = 0;
               foreach($bgarr as $bgver) { 
                 if (trim($bgver) !== "") { 
                   if (trim($seriesstr) === "") { 
                     $seriesstr .= " bs.pbiosample = cast( :bgnbr{$cntr} as DECIMAL(20,8)) ";
                     $qryArr += [":bgnbr{$cntr}" => "{$bgver}" ];
                   } else { 
                     $seriesstr .= " OR bs.pbiosample = cast( :bgnbr{$cntr} as DECIMAL(20,8)) ";
                     $qryArr += [":bgnbr{$cntr}" => "{$bgver}" ];
                   }
                 }
                 $cntr++;
               }
               $sqlCritAdd .= " and (" . $seriesstr . ") ";
               $fieldsQueried++;
             }
           } else {
             $sqlCritAdd .= " and ( bs.pbiosample = cast( :bgnbr as DECIMAL(20,8))) ";
             $qryArr += [':bgnbr' => "{$bgvalue}"  ];
             $fieldsQueried++;
           }
            break;

        case 'procInst':
            $sqlCritAdd .= " and ( sg.procuredat = :procinstitution ) ";
            $qryArr += [':procinstitution' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'segmentStatus':
            $sqlCritAdd .= " and ( sg.segstatus = :segmentstatus ) ";
            $qryArr += [':segmentstatus' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'qmsStatus':
            $sqlCritAdd .= " and ( bs.qcprocstatus = :qcstatus ) ";
            $qryArr += [':qcstatus' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'procDateFrom':
            if (trim($rqstDta['procDateTo']) === "") { 
                $errors++;
                $errorsMsg = "Procurement 'To' date is missing";
            } else { 
              $sqlCritAdd .= " and ( sg.procurementdate between :procdtefrom and :procdteto ) ";
              $qryArr += [':procdtefrom' => "{$fldvalue}"];
              $qryArr += [':procdteto' => "{$rqstDta['procDateTo']}"];
              $fieldsQueried++;
            } 
            break;

        case 'procDateTo':
            break;

        case 'shipDateFrom':
            if (trim($rqstDta['shipDateTo']) === "") { 
                $errors++;
                $errorsMsg = "Shipment 'To' date is missing";
            } else { 
              $sqlCritAdd .= " and ( sg.shippeddate between :shipdtefrom and :shipdteto ) ";
              $qryArr += [':shipdtefrom' => "{$fldvalue}"];
              $qryArr += [':shipdteto' => "{$rqstDta['shipDateTo']}"];
              $fieldsQueried++;
            } 
            break;
        case 'shipDateTo':
            break;

        case 'investigatorCode':
            $sqlCritAdd .= " and ( sg.assignedto = :investigatorcode ) ";
            $qryArr += [':investigatorcode' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'shipdocnbr':
             
           $sdvalue = "{$fldvalue}";
           if (strpos($sdvalue,"-") !== false || strpos($sdvalue,",") !== false) { 
  
             if (strpos($sdvalue,"-") !== false) { 
               //RANGE
               $bgarr = explode("-",$sdvalue);
               $sqlCritAdd .= " and ( sg.shipdocrefid between :sdnbrOne and :sdnbrTwo ) ";
               $qryArr += [':sdnbrOne' => "{$bgarr[0]}" ];
               $qryArr += [':sdnbrTwo' => "{$bgarr[1]}" ];
               $fieldsQueried++;
             }
             
             if (strpos($sdvalue,",") !== false) { 
               //SERIES
               $sdseriesstr = "";
               $sdarr = explode(",",$sdvalue); 
               $sdcntr = 0;
               foreach($sdarr as $sdver) { 
                 if (trim($sdver) !== "") { 
                   if (trim($sdseriesstr) === "") { 
                     $sdseriesstr .= " sg.shipdocrefid = :sdnbr{$cntr} ";
                     $qryArr += [":sdnbr{$cntr}" => "{$sdver}" ];
                   } else { 
                     $sdseriesstr .= " OR sg.shipdocrefid = :sdnbr{$cntr} ";
                     $qryArr += [":sdnbr{$cntr}" => "{$sdver}" ];
                   }
                 }
                 $cntr++;
               }
               $sqlCritAdd .= " and (" . $sdseriesstr . ") ";
               $fieldsQueried++;
             }

           } else {
             $sqlCritAdd .= " and ( sg.shipdocrefid = :sdnbr   ) ";
             $qryArr += [':sdnbr' => "{$sdvalue}"  ];
             $fieldsQueried++;
           }
            
            break;

        case 'shipdocstatus':
            $sqlCritAdd .= " and ( sd.status = :sdstatus ) ";
            $qryArr += [':sdstatus' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'site':

            if (strpos($fldvalue,";") !== false) { 
              //DELIMITED STRING
              $dxdp = explode(";",$fldvalue); 


              $c = 0; 
              foreach ($dxdp as $dxdpv) { 
                switch ($c) { 
                  case 0;
                    if ((trim($dxdpv) !== "") && (trim($dxdvp) !== "-")) { 
                      if (strpos($dxdpv,"^") !== false) {                       
                       $truval = str_replace("^","",$dxdpv);
                       $sqlCritAdd .= " and ( bs.anatomicsite like :asite or bs.subsite like :ssite ) ";
                       $qryArr += [':asite' => "%{$truval}%"];
                       $qryArr += [':ssite' => "%{$truval}%"];
                      } else {  
                       $sqlCritAdd .= " and ( bs.anatomicsite = :asite or bs.subsite = :ssite ) ";
                       $qryArr += [':asite' => "{$dxdpv}"];
                       $qryArr += [':ssite' => "{$dxdpv}"];
                      }
                    }
                  break;
                  case 1;
                    if ((trim($dxdpv) !== "") && (trim($dxdvp) !== "-")) { 
                      if (strpos($dxdpv,"^") !== false) {                       
                       $truval = str_replace("^","",$dxdpv);
                       $sqlCritAdd .= " and ( bs.diagnosis like :dx or bs.subdiagnos like :sdx ) ";
                       $qryArr += [':dx' => "%{$truval}%"];
                       $qryArr += [':sdx' => "%{$truval}%"];
                      } else {  
                       $sqlCritAdd .= " and ( bs.diagnosis = :dx or bs.subdiagnos = :sdx ) ";
                       $qryArr += [':dx' => "{$dxdpv}"];
                       $qryArr += [':sdx' => "{$dxdpv}"];
                      }
                    }
                  break;
                  case 2: 
                    if ((trim($dxdpv) !== "") && (trim($dxdvp) !== "-")) { 
                      if (strpos($dxdpv,"^") !== false) {                       
                       $truval = str_replace("^","",$dxdpv);
                       $sqlCritAdd .= " and ( bs.metssite like :msite ) ";
                       $qryArr += [':msite' => "%{$truval}%"];
                      } else {  
                       $sqlCritAdd .= " and ( bs.metssite = :msite ) ";
                       $qryArr += [':msite' => "{$dxdpv}"];
                      }
                    }
                  break;
                }
                $c++;
              }
              $fieldsQueried++;
            } else { 
              
               if (strpos($fldvalue,"^") !== false) { 
                   //LIKE MATCH
                 $truval = str_replace("^","",$fldvalue);
                 $sqlCritAdd .= " and ( bs.anatomicsite like :asite or bs.subsite like :ssite or bs.diagnosis like :dx or bs.subdiagnos like :sdx or bs.metssite like :msite ) ";
                 $qryArr += [':asite' => "{$truval}%"];
                 $qryArr += [':ssite' => "{$truval}%"];
                 $qryArr += [':msite' => "{$truval}%"];
                 $qryArr += [':dx' => "{$truval}%"];
                 $qryArr += [':sdx' => "{$truval}%"];
                 $fieldsQueried++;
               } else {      
                 $sqlCritAdd .= " and ( bs.anatomicsite = :asite or bs.subsite = :ssite or bs.diagnosis = :dx or bs.subdiagnos = :sdx  or bs.metssite = :msite ) ";
                 $qryArr += [':asite' => "{$fldvalue}"];
                 $qryArr += [':ssite' => "{$fldvalue}"];
                 $qryArr += [':msite' => "{$fldvalue}"];
                 $qryArr += [':dx' => "{$fldvalue}"];
                 $qryArr += [':sdx' => "{$fldvalue}"];
                 $fieldsQueried++;
               }

            }
            $fieldsQueried++;
            break;

        case 'specimencategory':
            $sqlCritAdd .= " and ( bs.tisstype = :speccat ) ";
            $qryArr += [':speccat' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'phiage':

            if (strpos($fldvalue,"-") !== false) { 
               //RANGE
               $agearr = explode("-",$fldvalue);
               $sqlCritAdd .= " and ( bs.pxiage between :ageOne and :ageTwo ) ";
               $qryArr += [':ageOne' => "{$agearr[0]}" ];
               $qryArr += [':ageTwo' => "{$agearr[1]}" ];
               $fieldsQueried++;
            } else { 
              $sqlCritAdd .= " and ( bs.pxiage = :phiage ) ";
              $qryArr += [':phiage' => "{$fldvalue}"];
              $fieldsQueried++;
            }
            break;

        case 'phirace':
            $sqlCritAdd .= " and ( bs.pxirace = :phirace ) ";
            $qryArr += [':phirace' => "{$fldvalue}"];
            $fieldsQueried++;
            break; 

        case 'phisex':
            $sqlCritAdd .= " and ( bs.pxigender = :phisex ) ";
            $qryArr += [':phisex' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'procType':
            $sqlCritAdd .= " and ( bs.proctype = :proctype ) ";
            $qryArr += [':proctype' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'PrepMethod':
            $sqlCritAdd .= " and ( sg.prepmethod = :pmet ) ";
            $qryArr += [':pmet' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'preparation':
            $sqlCritAdd .= " and ( sg.preparation = :prep ) ";
            $qryArr += [':prep' => "{$fldvalue}"];
            $fieldsQueried++;
            break;
      }
    }
  }


  if ($fieldsQueried > 0) { 
    if ($errors === 0) { 
        //BEGINNINGS OF SQL
      require(serverkeys . "/sspdo.zck");  
      $masterSQL = <<<SQLSTMT
select bs.pbiosample
      , sg.segmentid
      , bs.voidind bsvoid 
      , sg.voidind sgvoid
      , ucase(ifnull(sg.bgs,'')) as bgs
      , sg.segstatus
      , ifnull(bs.qcprocstatus,'') as qcstatus
      , ifnull(bs.pxiage,'') as phiage
      , ifnull(bs.pxirace,'') as phirace
      , ifnull(bs.pxigender,'') as phigender
      , ifnull(bs.proctype,'') as proctype
      , ifnull(date_format(sg.procurementdate,'%m/%d/%Y'),'') as procurementdate 
      , ifnull(sg.shipdocrefid,0) as shipdocnbr
      , ifnull(sd.status,'') as sdstatus
      , sg.procuredat procuringinstitution 
      , ifnull(bs.tisstype,'') as specimencategory
      , ifnull(bs.anatomicsite,'') as site
      , ifnull(bs.subsite,'') as subsite
      , ifnull(bs.diagnosis,'') as diagnosis
      , ifnull(bs.subdiagnos,'') as diagnosismodifier
      , ifnull(bs.metssite,'') as metssite
      , ifnull(sg.assignedto,'') as assignedinvestigator
      , ifnull(sg.assignedReq,'') as tqrequestnbr
      , ifnull(sg.prepmethod,'') as preparationmethod
      , ifnull(sg.preparation,'') as preparation
from masterrecord.ut_procure_segment sg 
left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample 
left join masterrecord.ut_shipdoc sd on sg.shipdocrefid = sd.shipdocrefid 
where 1=1 {$sqlCritAdd} 
order by sg.bgs
limit 0, 5000
SQLSTMT;
      //RUN QUERY HERE
      $masterRS = $conn->prepare($masterSQL); 
      $masterRS->execute($qryArr);
      $sendthis['itemsfound'] = $masterRS->rowCount();
      while ($rs = $masterRS->fetch(PDO::FETCH_ASSOC)) { 
        $sendthis['data'][] = $rs;
      }
    } else { 
      //ERRORS WITH THE CRITERIA -- SEND BACK ERROR
    }
  } else { 
    //NO FIELDS QUERIED -- THAT SHOULDN'T HAPPEN BUT ITS A CATCH
  }

  $rtndta[] = $sendthis;
  //$rtndta[] = $qryArr;
  return $rtndta;
}

