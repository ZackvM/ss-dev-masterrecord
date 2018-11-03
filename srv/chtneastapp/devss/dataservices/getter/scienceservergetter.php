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
    require_once(genAppFiles . "/dataconn/sspdo.zck");  
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
    require_once(genAppFiles . "/dataconn/sspdo.zck");  
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

function docsrch($whichobj, $rqst) { 
    session_start();
    $responseCode = 500;   
    $msg = "status message";
    $itemsfound = 0;
    $dta = array();
    if ($whichobj === "") { 
    } else { 
      require_once(genAppFiles . "/dataconn/sspdo.zck");  
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
      return "SELECT distinct ucase(trim(ifnull(tisstype,''))) as codevalue, ucase(trim(ifnull(tisstype,''))) as menuvalue, 0 as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM masterrecord.ut_procure_biosample order by codevalue";
//where trim(ifnull(tisstype,'')) <> ''
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
