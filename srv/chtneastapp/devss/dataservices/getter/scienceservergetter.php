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
    require(genAppFiles . "/dataconn/sspdo.zck");
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
                  $ssql = "SELECT trim(substr(cr.chart,1,200)) as abstract, date_format(cr.lastupdate,'%m/%d/%Y') as dspmark FROM masterrecord.ut_chartreview cr  where dspind = 1 and chart like :srchtrm limit 250";
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
               //ADD A DEFAULT ERROR IF NOT ABOVE
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

