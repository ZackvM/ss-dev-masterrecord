<?php

class dataposters { 

  public $responseCode = 400;
  public $rtnData = "";

function __construct() { 
    $args = func_get_args(); 
    $nbrofargs = func_num_args(); 
    $this->rtnData = $args[0];    
    if (trim($args[0]) === "") { 
    } else { 
      $request = explode("/", $args[0]); 
      if (trim($request[3]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING","ITEMSFOUND" => 0, "DATA" => array()    ));
      } else { 
        $dp = new $request[2](); 
        if (method_exists($dp, $request[3])) { 
          $funcName = trim($request[3]); 
          $dataReturned = $dp->$funcName($args[0], $args[1]); 
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

class datadoers { 
 
    function assignsegments($request, $passdata) { 
//{"segmentlist":"{\"0\":{\"biogroup\":\"81948\",\"bgslabel\":\"81948001\",\"segmentid\":\"431100\"},\"1\":{\"biogroup\":\"81948\",\"bgslabel\":\"81948003\",\"segmentid\":\"431102\"},\"2\":{\"biogroup\":\"81948\",\"bgslabel\":\"81948004\",\"segmentid\":\"431103\"}}","investigatorid":"INV3000","requestnbr":"REQ19002"}
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $rows = array(); 
      $msgArr = array();
      require(serverkeys . "/sspdo.zck");  
      $qryrqst = json_decode($passdata, true);

      if ($qryrqst['investigatorid'] === 'BANK') { 
          $assInv = 'BANKED';  //SEGMENT STATUS FROM SYS_MASTER_MENUS
          $assProj = "";
          $assReq = '';
      } else { 
          if (trim($qryrqst['requestnbr']) === "" || trim($qryrqst['investigatorid']) === "") { 
            $error = 1; 
            $msgArr[] = "Both an Investigator and a request number is required.  No Segments have been assigned.";
          } else { 
            //CHECK VALIDITY OF INV/REQ
            $chkSQL = "select rq.requestid, pr.projid, pr.investid from vandyinvest.investtissreq rq left join vandyinvest.investproj pr on rq.projid = pr.projid where rq.requestid = :rq and pr.investid = :iv";
            $chkR = $conn->prepare($chkSQL);
            $chkR->execute(array(':rq' => trim($qryrqst['requestnbr']), ':iv' => trim($qryrqst['investigatorid'])));
            if ($chkR->rowCount() < 1) { 
              $error = 1; 
              $msgArr[] = "The specified Investigator/request number combination is NOT valid ({$qryrqst['investigatorid']}/{$qryrqst['requestnbr']}).  No Segments have been assigned.";
            } else {
              $dbrq = $chkR->fetch(PDO::FETCH_ASSOC);  
              $assInv = strtoupper(trim($qryrqst['investigatorid']));  //SEGMENT STATUS FROM SYS_MASTER_MENUS
              $assProj = strtoupper(trim($dbrq['projid']));
              $assReq = strtoupper(trim($qryrqst['requestnbr']));
            }
          }
      }

      //3) CHECK SEGMENT EXISTS AND IS IN A STATE TO BE REASSIGNED - CHECKING SEGMENTS FIRST MAKES THIS SORT OF A TRANSACTIONAL COMPONENT
      $assignableSQL = "select menuvalue, dspvalue, assignablestatus from four.sys_master_menus where menu = 'SEGMENTSTATUS'";
      $assignableRS = $conn->prepare($assignableSQL); 
      $assignableRS->execute(); 
      while ($asr = $assignableRS->fetch(PDO::FETCH_ASSOC)) { 
        $assignableStatus[] = $asr;
      }
      $segList = json_decode($qryrqst['segmentlist'], true);
      foreach ($segList as $k => $v) {  
        $chkSQL = "SELECT replace(ifnull(bgs,''),'T_','') as bgs, ifnull(segstatus,'') as segstatus, ifnull(date_format(shippeddate,'%m/%d/%Y'),'') as shippeddate, ifnull(shipdocrefid,'') as shipdocrefid FROM masterrecord.ut_procure_segment where segmentid = :segmentid";
        $chkR = $conn->prepare($chkSQL); 
        $chkR->execute(array(':segmentid' => $v['segmentid'] ));
        if ($chkR->rowCount() > 0) { 
          $r = $chkR->fetch(PDO::FETCH_ASSOC);
          //CHECK SHIPDATE
          if ($r['shippeddate'] !== "") { 
            $error = 1;
            $msgArr[] = "Segment Label {$r['bgs']} has a shipment date ({$r['shippeddate']}) .  This segment is unable to be assigned.";
          }                 
          //CHECK SHIPDOCID 
          if ($r['shipdocrefid'] !== "") { 
            $error = 1;
            $sddsp = substr(('000000' . $r['shipdocrefid']),-6);
            $msgArr[] = "Segment Label {$r['bgs']} is listed on ship-doc ({$sddsp}) .  This segment is unable to be assigned.";
          }                
          //CHECK STATUS
          $statusFound = 0;
          foreach ($assignableStatus as $aKey => $aVal) {
            if (strtoupper(trim($r['segstatus'])) === strtoupper(trim($aVal['menuvalue']))) {
              $statusFound = 1;
              if ((int)$aVal['assignablestatus'] === 0) {
                $error = 1; 
                $msgArr[] = "{$r['bgs']} is statused as \"{$aVal['dspvalue']}\".  This segment status is unable to be assigned.";
              }
            }
          }
          if ($statusFound === 0) { 
            $error = 1;
            $msgArr[] = "Segment Label {$r['bgs']} has an invalid status.  This segment is unable to be assigned.";
          }                
          
        } else { 
          $error = 1; 
          $msgArr[] = "Segment does not Exist.  See CHTN Eastern IT (dbSegmentId: {$v['segmentid']})";
        }             
      } 

      if ($error === 0) { 
          session_start(); 
          $usrSQL = "SELECT originalAccountName FROM four.sys_userbase where sessionid = :sessionid";
          $usrR = $conn->prepare($usrSQL);
          $usrR->execute(array(':sessionid' =>session_id()));
          if ($usrR->rowCount() < 1) { 
            $msgArr[] = "SESSION KEY IS INVALID.  LOG OUT OF SCIENCESERVER AND LOG BACK IN";  
            $msg = json_encode($msgArr);
          } else { 
            $u = $usrR->fetch(PDO::FETCH_ASSOC);
            //4) REASSIGN SEGMENT

            foreach ($segList as $k => $v) {  
                //GET PREVIOUS STATUS AND ASSIGNMENT
                $prvSQL = "select bgs, ifnull(segstatus,'') segstatus, statusdate, ifnull(statusby,'') statusby, ifnull(assignedTo,'') assignedto , ifnull(assignedProj,'') assignedproj, ifnull(assignedReq,'') assignedreq, assignmentdate, ifnull(assignedby,'') assignedby from masterrecord.ut_procure_segment where segmentid = :segid";
                $prvR = $conn->prepare($prvSQL); 
                $prvR->execute(array(':segid' => $v['segmentid']));
                $prvRec = $prvR->fetch(PDO::FETCH_ASSOC);                 
                //SAVE OLD STATUS TO HISTORY TABLE
                $svePrvSQL = "insert into masterrecord.ut_procure_segment_history_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) values(:segid,:segstatus,:segstatusby,:segstatuson,now(),:enteredby)"; 
                $svePrvR = $conn->prepare($svePrvSQL); 
                $svePrvR->execute(array(':segid' => $v['segmentid'], ':segstatus' => $prvRec['segstatus'], ':segstatusby' => $prvRec['statusby'], ':segstatuson' => $prvRec['statusdate'], ':enteredby' => $u['originalAccountName'])); 
                //SAVE OLD ASSIGNMENT 
                $aInv = (trim($prvRec['assignedto']) === "") ? "NO-INV-ASSIGNMENT" : trim($prvRec['assignedto']); 
                $aPrj = (trim($prvRec['assignedproj']) === "") ? "NO-PROJ-ASSIGNMENT" : trim($prvRec['assignedproj']); 
                $aReq = (trim($prvRec['assignedreq']) === "") ? "NO-REQ-ASSIGNMENT" : trim($prvRec['assignedreq']);
                $aBy = (trim($prvRec['assignedby']) === "") ? "NO-BY-ASSIGNMENT" : trim($prvRec['assignedby']); 
                $prvASQL = "insert into masterrecord.ut_procure_segment_history_assignment(segmentid, previousassignment, previousproject, previousrequest, previousassignmentdate, previousassignmentby, enteredby, enteredon) values(:segmentid, :previousassignment, :previousproject, :previousrequest, :previousassignmentdate, :previousassignmentby, :enteredby, now())";
                $prvAR = $conn->prepare($prvASQL); 
                $prvAR->execute(array(':segmentid' => $v['segmentid'],':previousassignment' => $aInv,':previousproject' => $aPrj,':previousrequest' => $aReq,':previousassignmentdate' => $prvRec['assignmentdate'],':previousassignmentby' => $aBy,':enteredby' => $u['originalAccountName']));
                //WRITE NEW STATUS WITH ASSIGNMENT WITH PERSON WRITING AND DATE WRITTEN 
                if ($assInv === 'BANKED') {
                  $sts = 'BANKED'; 
                  $stsB = $u['originalAccountName']; 
                  $aTo = '';
                  $aPrj = '';
                  $aRq = '';
                } else { 
                  $sts = 'ASSIGNED'; 
                  $stsB = $u['originalAccountName']; 
                  $aTo = $assInv;
                  $aPrj = $assProj;
                  $aRq = $assReq;
                } 
                $segUpdSQL = "update masterrecord.ut_procure_segment set segstatus = :segStat, statusdate = now(), statusby = :statBy, assignedto = :aTo, assignedproj = :aPrj, assignedreq = :aRq, assignmentdate = now(), assignedby = :aBy where segmentid = :segid ";
                $segUpdR = $conn->prepare($segUpdSQL); 
                $segUpdR->execute(array(':segStat' => $sts, ':statBy' => $stsB, ':aTo' => $aTo, ':aPrj' => $aPrj, ':aRq' => $aRq, ':aBy' => $stsB, ':segid' => $v['segmentid']));

            }
            $responseCode = 200;
          //  //SEND STATUS 200 
          }                
      } else { 
        //SEND ERROR MESSAGE
        $msg = json_encode($msgArr);
      }
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $data);
      return $rows;
    } 

    function makequeryrequest($request, $passedData) { 
      $responseCode = 400; 
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $rows = array(); 
      $qryrqst = json_decode($passedData, true);
      switch ($qryrqst['qryType']) { 
        case 'BIO':
            $allow = qryCriteriaCheckBio($qryrqst);
            if ((int)$allow['errorind'] === 1 ) { 
                //ERRORS PRESENT
                $msg = $allow['errormsg'];
            } else { 
                //NO ERRORS - SAVE REQUEST
                require(serverkeys . "/sspdo.zck");  
                session_start();        
                $usrSQL = "SELECT originalAccountName FROM four.sys_userbase where sessionid = :sessionid";
                $usrR = $conn->prepare($usrSQL);
                $usrR->execute(array(':sessionid' =>session_id()));
                $msg = $usrR->rowCount();
                if ($usrR->rowCount() < 1) { 
                  $msg = "SESSION KEY IS INVALID.  LOG OUT OF SCIENCESERVER AND LOG BACK IN";  
                } else { 
                  $objid = strtolower( generateRandomString() );
                  $u = $usrR->fetch(PDO::FETCH_ASSOC);
                  $insSQL = "insert into four.objsrchdocument (objid, bywho, onwhen, srchterm, doctype) value (:objid,:whoby,now(),:srchtrm,:doctype)";
                  $insR = $conn->prepare($insSQL);
                  $insR->execute(array(':objid' => $objid, ':whoby' => $u['originalAccountName'], ':srchtrm' => $passedData, ':doctype' => 'COORDINATOR-' . trim($qryrqst['qryType'])    ));
                  $data['coordsearchid'] = $objid;
                  $responseCode = 200;
                }                
            }
            break;
        default: 
          $msg = "TYPE OF QUERY NOT SPECIFIED OR NOT RECOGNIZED";          
      }       
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $data);
      return $rows;                
    }
 
    function documenttext($request, $passedData) {
        //TODO:  Add Error Checking to all Webservice end points 
      require(serverkeys . "/sspdo.zck");  
      $responseCode = 400; 
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $rows = array(); 
      $params = json_decode($passedData, true);
      $documentrqst = cryptservice($params['documentid'],'d'); 
      $documentid = explode("-",$documentrqst);
      switch ($documentid[0]) { 
        case 'CR': 
          //GET CHART REVIEW
          $chartid = $documentid[1];
          $sql = "SELECT 'CHARTRVW' as doctype, chartid, chart, pxi, segmentref FROM masterrecord.ut_chartreview where chartid = :chartid and dspind <> 0"; 
          $rs = $conn->prepare($sql); 
          $rs->execute(array(':chartid' => $chartid)); 
        break; 
        case 'PR':
          //GET PATH REPORT
          $prid = $documentid[1];
          $selector = $documentid[2];
          $sql = "select 'PATHOLOGYRPT' as doctype, dnpr_nbr, pathreport, prid, selector from masterrecord.qcpathreports where selector = :selector and prid = :prid";
          $rs = $conn->prepare($sql); 
          $rs->execute(array(':selector' => $selector, ':prid' => $prid)); 
        break;
      }
      $itemsfound = $rs->rowCount();
      if ($rs->rowCount() < 0) {  
        $responseCode = 404; 
        $msg = "Requested document not found";
      } else { 
        $data[] = $rs->fetch(PDO::FETCH_ASSOC);
        $responseCode = 200;
      }
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $data);
      return $rows;
    }

    function suggestsomething($request, $passedData) { 
       //{"rqstsuggestion":"masterrecord-sites","given":"thyr"} 
       require(serverkeys . "/sspdo.zck");  
       $responseCode = 503;  
       $params = json_decode($passedData, true);
       switch (strtolower($params['rqstsuggestion'])) { 
         case 'masterrecord-sites': 
             $srchSQL = "SELECT distinct ucase(concat(trim(ifnull(bs.anatomicsite,'')), if(ifnull(bs.subsite,'')='','',concat(' [Sub-Site: ', trim(bs.subsite),'] ')))) suggestionlist FROM masterrecord.ut_procure_biosample bs where voidind <> 1 and (anatomicsite like :likeOne or subsite like :likeTwo) order by suggestionlist ";             
             $srchRS = $conn->prepare($srchSQL); 
             $srchRS->execute(array(':likeOne' => "{$params['given']}%", ':likeTwo' => "{$params['given']}%"));          
             break;
         case 'vandyinvest-invest':
             $srchSQL = "SELECT investid as investvalue, concat(trim(concat(  ifnull(invest_lname,''),', ', ifnull(invest_fname,'')  )) , if(ifnull(invest_homeinstitute,'')='','',concat(' / ',invest_homeinstitute)), ' [', ucase(ifnull(invest_division,'')),']') as dspinvest FROM vandyinvest.invest where 1=1 and (investid like :optOne or invest_fname like :optTwo or invest_lname like :optThree or invest_homeinstitute like :optFour or divisionid like :optFive) order by invest_lname";
             $srchRS = $conn->prepare($srchSQL); 
             $srchRS->execute(array(':optOne' => "{$params['given']}%", ':optTwo' => "{$params['given']}%", ':optThree' => "{$params['given']}%", ':optFour' => "{$params['given']}%", ':optFive' => "{$params['given']}%"));          
             break;
         case 'vandyinvest-requests':
           
             $srchSQL = "select rq.requestid  from vandyinvest.investtissreq rq left join vandyinvest.investproj pr on rq.projid = pr.projid where pr.investid = :investid";
             $srchRS = $conn->prepare($srchSQL); 
             $srchRS->execute(array(':investid' => "{$params['given']}"));          
            break; 
       }
       $rtnArray = array();
       if ($srchRS->rowCount() > 0) { 
          $responseCode = 200;
          $msg = 0; 
          $itemsfound = $srchRS->rowCount();
          while ($r = $srchRS->fetch(PDO::FETCH_ASSOC)) { 
            $rtnArray[] = $r;
          }
       } else { 
           $responseCode = 404;    
           $itemsfound = 0;
           $msg = "NO SUGGESTIONS ARE MADE";
       }
       $rows['statusCode'] = $responseCode; 
       $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $rtnArray);
       return $rows;      
    }

    function docsearch($request, $passedData) { 
       require(serverkeys . "/sspdo.zck");  
       session_start(); 
       $responseCode = 503; 
       $params = json_decode($passedData, true);
       if (trim($params['srchterm']) === "" || trim($params['doctype']) === "")  { 
          $msg = "YOU MUST SPECIFY A SEARCH TERM AND A DOCUMENT TYPE"; 
       }  else { 
          if ( trim($params['doctype']) === 'SHIPDOC'  && !is_numeric($params['srchterm']))  { 
           $msg = "WHEN SEARCHING FOR A SHIPMENT DOCUMENT ONLY NUMERIC SEARCHES ARE ALLOWED"; 
          } else {   
            $usrSQL = "SELECT originalAccountName FROM four.sys_userbase where sessionid = :sessionid";
            $usrR = $conn->prepare($usrSQL);
            $usrR->execute(array(':sessionid' =>session_id()));
            if ($usrR->rowCount() < 1) { 
              $msg = "SESSION KEY IS INVALID.  LOG OUT OF SCIENCESERVER AND LOG BACK IN";  
            } else { 
              $u = $usrR->fetch(PDO::FETCH_ASSOC);
              $objid = strtolower( generateRandomString() );
              $insSQL = "insert into four.objsrchdocument (objid, bywho, onwhen, srchterm, doctype) value (:objid,:whoby,now(),:srchtrm,:doctype)";
              $insR = $conn->prepare($insSQL);
              $insR->execute(array(':objid' => $objid, ':whoby' => $u['originalAccountName'], ':srchtrm' =>trim($params['srchterm']), ':doctype' => trim($params['doctype'])));
              $msg = $objid;
              $responseCode = 200;
           }
          }          
       }       
       $rows['statusCode'] = $responseCode; 
       $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => 0,  'DATA' => "");
       return $rows;      
    }
     
    function preprocessshipdoc($request, $passdata) { 
      $responseCode = 400; 
      require(serverkeys . "/sspdo.zck");  
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $errorInd = 0;
      $assignableSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS' and additionalInformation = 'ASSIGNSTATUS' and dspind = 1";
      $assignableRS = $conn->prepare($assignableSQL); 
      $assignableRS->execute(); 
      while ($asr = $assignableRS->fetch(PDO::FETCH_ASSOC)) { 
        $assignableStatus[] = $asr;
      }
      $pdta = json_decode($passdata, true);
      $rcdCntr = 0;
      foreach ($pdta as $key => $value ) { 
        $chkSQL = "SELECT replace(ifnull(bgs,''),'T_','') as bgs, ifnull(segstatus,'') as segstatus, ifnull(date_format(shippeddate,'%m/%d/%Y'),'') as shippeddate, ifnull(shipdocrefid,'') as shipdocrefid, ifnull(assignedTo,'') as assignedto FROM masterrecord.ut_procure_segment where segmentid = :segmentid";
        $chkR = $conn->prepare($chkSQL); 
        $chkR->execute(array(':segmentid' => $value['segmentid'] ));
        if ($chkR->rowCount() > 0) { 
          $r = $chkR->fetch(PDO::FETCH_ASSOC);
          //1. MUST BE STATUSED ASSIGNED
          //CHECK STATUS
          $statusFound = 0;
          foreach ($assignableStatus as $aKey => $aVal) {
            if (strtoupper(trim($r['segstatus'])) === strtoupper(trim($aVal['menuvalue']))) {
              $statusFound = 1;
            }
          }
          if ($statusFound === 0) { 
            $errorInd = 1;
            $msgArr[] .= "Segment Label {$r['bgs']} is not assigned.  This segment is unable to be added to a shipment document.";
          }                
          //2. NO SHIPDOC NBR
          //3. NO SHIPDATE
          if ($r['shippeddate'] !== "") { 
            $errorInd = 1;
            $msgArr[] .= "{$r['bgs']} has a shipment date ({$r['shippeddate']}).  This segment is unable to be added to a shipment document.";
          }                
          //CHECK SHIPDOCID 
          if ($r['shipdocrefid'] !== "") { 
            $errorInd = 1;
            $sddsp = substr(('000000' . $r['shipdocrefid']),-6);
            $msgArr[] .= "{$r['bgs']} is listed on ship-doc ({$sddsp}) already.  This segment is unable to be added to a shipment document.";
          }                
          //4. ALL MUST BE ASSIGNED TO THE SAME INVESTIGATOR
          if ($r['assignedto'] === '') { 
            $errorInd = 1;
            $msgArr[] .= "{$r['bgs']} does not have an assignment.  This segment is unable to be added to a shipment document.";
          } else {
          if ($rcdCntr === 0) { 
              //SET INVCHK
              $invChk = strtoupper(trim($r['assignedto'])); 
          } else { 
             if ($invChk !== strtoupper(trim($r['assignedto']))) { 
               $errorInd = 1;
               $msgArr[] .= "You can only select segments for one investigator. {$r['bgs']} is assigned to {$r['assignedto']}.  This segment is unable to be added to a shipment document.";
             }
          }
          }
          //TODO: CHECK THAT THERE ARE NO OPEN SHIPDOCS
          $rcdCntr++; 
        } else { 
          $errorInd = 1; 
          $msgArr[] .= "Segment does not Exist.  See CHTN Eastern IT (dbSegmentId: {$value['segmentid']})";
        }
      }
      
      if ($errorInd === 0) { 
        $responseCode = 200;
        $pdta['inv'] = $invChk;
        $dta = array('pagecontent' =>  bldDialog('dataCoordinatorShipDocCreate', json_encode($pdta)));
      }

      $msg = $msgArr;       
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function shipdocquickcreator($request, $passeddata) {  
      require(serverkeys . "/sspdo.zck");  
      session_start(); 
      $responseCode = 400;  
      $pdta = json_decode($passeddata, true); 
      $itemsfound = 0;
      $msgArr = array();
      $dta = array();
      $errorInd = 0;

      $assignableSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS' and additionalInformation = 'ASSIGNSTATUS' and dspind = 1";
      $assignableRS = $conn->prepare($assignableSQL); 
      $assignableRS->execute(); 
      while ($asr = $assignableRS->fetch(PDO::FETCH_ASSOC)) { 
        $assignableStatus[] = $asr;
      }

      //DATA CHECKS  
      (strtoupper(trim($pdta['sdcShipDocNbr'])) !== "NEW") ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE SHIPDOC NUMBER MUST BE LISTED AS 'NEW'")) : "";
      (trim($pdta['sdcAcceptedBy']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE NAME OF THE PERSON ACCEPTING SHIPMENT IS REQUIRED")) : ""; 
      (trim($pdta['sdcAcceptorsEmail']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE EMAIL OF THE PERSON ACCEPTING THE SHIPMENT MUST BE SPECIFIED")) : ""; 
      (trim($pdta['sdcAcceptorsEmail']) !== "" && !filter_var(trim($pdta['sdcAcceptorsEmail']), FILTER_VALIDATE_EMAIL)) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE ACCEPTOR'S EMAIL APPEARS TO BE AN INVALID EMAIL ADDRESS")) : "";
      (trim($pdta['sdcPurchaseOrder']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A PURCHASE ORDER NUMBER IS REQUIRED")) : "";
      (trim($pdta['sdcRqstShipDateValue']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A REQUESTED SHIPMENT DATE IS REQUIRED")) : "" ;
      (trim($pdta['sdcRqstShipDateValue']) !== "" &&  !verifyDate(trim($pdta['sdcRqstShipDateValue']),'Y-m-d', true)) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE DATE SPECIFIED FOR THE SHIPMENT IS INVALID")) : "" ;
      (trim($pdta['sdcRqstToLabDateValue']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A 'DATE TO PULL' IS REQUIRED")) : "" ;
      (trim($pdta['sdcRqstToLabDateValue']) !== "" &&  !verifyDate(trim($pdta['sdcRqstToLabDateValue']),'Y-m-d', true)) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE 'DATE TO PULL' IS INVALID")) : "" ;
      (trim($pdta['sdcInvestCode']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE INVESTIGATOR'S CODE MUST BE SPECIFIED")) : "" ; 
      (trim($pdta['sdcInvestCode']) !== "" && substr( strtoupper(trim($pdta['sdcInvestCode'])),0,3) <> 'INV') ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE INVESTIGATOR'S CODE IS INVALID")) : "" ; //THIS IS JUST A PSUEDO CHECK BETTER CHECKS SHOULD BE MADE
      (trim($pdta['sdcInvestEmail']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE INVESTIGATOR'S EMAIL MUST BE SPECIFIED")) : "" ; 
      (trim($pdta['sdcInvestEmail']) !== "" && !filter_var(trim($pdta['sdcInvestEmail']), FILTER_VALIDATE_EMAIL)) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE INVESTIGATOR'S EMAIL APPEARS TO BE AN INVALID EMAIL ADDRESS")) : "" ;
      (trim($pdta['sdcInvestName']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE INVESTIGATOR'S NAME MUST BE SPECIFIED")) : "" ; 
      (trim($pdta['sdcInvestShippingAddress']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A SHIPPING ADDRESS MUST BE SPECIFIED")) : "" ; 
      (trim($pdta['sdcInvestBillingAddress']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A BILLING ADDRESS MUST BE SPECIFIED")) : "" ; 
      (trim($pdta['sdcShippingPhone']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A SHIPPING PHONE NUMBER MUST BE SPECIFIED")) : "" ;  
      (trim($pdta['sdcShippingPhone']) !== "" && !preg_match('/^\(\d{3}\)\s\d{3}-\d{4}(\s[x]\d{1,7})*$/',trim($pdta['sdcShippingPhone']))) ? (list( $errorInd,$msgArr[] )=array( 1 ,"THE SHIPPING PHONE NUMBER IS IN AN INVALID FORMAT.  FORMAT IS (123) 456-7890 x0000")) : ""; 
      (trim($pdta['sdcBillPhone']) !== "" && !preg_match('/^\(\d{3}\)\s\d{3}-\d{4}(\s[x]\d{1,7})*$/',trim($pdta['sdcBillPhone']))) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE BILLING PHONE NUMBER IS IN AN INVALID FORMAT.  FORMAT IS (123) 456-7890 x0000")) : "" ; 

     $segments = json_decode($pdta['listedSegments'], true);
     (count($segments) < 1 ) ? (list( $errorInd, $msgArr[]) = array( 1 , "NO SEGMENTS HAVE BEEN SPECIFIED FOR THIS SHIPMENT DOCUMENT" )) : "" ; 

     $chkSQL = "select replace(bgs,'_','') as bgs, ifnull(shippeddate,'') as shippeddate, ifnull(segstatus,'') as segstatus, ifnull(shipdocrefid,'') as shipdocrefid, ifnull(assignedto,'') as assignedto from masterrecord.ut_procure_segment where segmentid = :segmentid and assignedto = :investcode";
     $chkR = $conn->prepare($chkSQL);
     foreach ($segments as $sgk => $sgv) {
       $chkR->execute(array(':segmentid' => $sgv['segmentid'], ':investcode' => $pdta['sdcInvestCode']));
       if ( $chkR->rowCount() < 1 ) { 
         $errorInd = 1;
         $msgArr[] = "{$sgv['bgs']} IS NOT ASSIGNED TO INVESTIGATOR {$pdta['sdcInvestCode']}. EITHER EXCLUDE THIS SEGMENT OR CORRECT THIS DATA BEFORE CONTINUING";
       } else {
         $r = $chkR->fetch(PDO::FETCH_ASSOC); 
         ($r['shippeddate'] !== "" || $r['shipdocrefid'] !== "") ? ( list( $errorInd, $msgArr[] ) = array( 1 , "SEGMENT {$sgv['bgs']} HAS EITHER A SHIPMENT DATE OR A SHIP DOCUMENT REFERENCE.  EITHER EXCLUDE THIS SEGMENT OR CORRECT THIS DATA.")) : "" ; 
         $statusFound = 0;
         foreach ($assignableStatus as $aKey => $aVal) {
           (strtoupper(trim($r['segstatus'])) === strtoupper(trim($aVal['menuvalue']))) ? $statusFound = 1 : "" ;
         }
         ($statusFound === 0) ? ( list( $errorInd, $msgArr[] ) = array( 1 , "SEGMENT {$sgv['bgs']} IS NOT STATUSED IN AN ASSIGNABLE STATE.  EITHER EXCLUDE THIS SEGMENT OR CORRECT THIS DATA.")) : "" ;
       }
     }

     if (  $errorInd === 0 ) { 
       //WRITE THE SHIPDOC - SEND BACK NUMBER
       $usrSQL = "SELECT originalAccountName as usrname FROM four.sys_userbase where sessionid = :sessionid";
       $usrR = $conn->prepare($usrSQL);
       $usrR->execute(array(':sessionid' => session_id()));
       if ($usrR->rowCount() < 1) { 
         $msgArr[] = "SESSION KEY IS INVALID.  LOG OUT OF SCIENCESERVER AND LOG BACK IN";  
       } else { 
         $u = $usrR->fetch(PDO::FETCH_ASSOC);
         $usr = $u['usrname']; 
         $sdInsSQL = "insert into masterrecord.ut_shipdoc (sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, shipaddy, shipphone, billaddy, billphone, setupby, setupon) values('OPEN', now(), :acceptedby, :acceptedbyemail, :ponbr, :rqstshipdate, :rqstpulldate, :comments, :investcode, :investname, :investemail, :investinstitution, :institutiontype, :investdivision, :oncreationinveststatus, :shipaddy, :shipphone, :billaddy, :billphone, :setupby, now())";   
         $sdR = $conn->prepare($sdInsSQL); 
         $sdR->execute(array(':acceptedby' => trim($pdta['sdcAcceptedBy']) ,':acceptedbyemail' => trim($pdta['sdcAcceptorsEmail']),':ponbr' => trim($pdta['sdcPurchaseOrder']) ,':rqstshipdate' => trim($pdta['sdcRqstShipDateValue']),':rqstpulldate' => trim($pdta['sdcRqstToLabDateValue']),':comments' => trim($pdta['sdcPublicComments']),':investcode' => strtoupper(trim($pdta['sdcInvestCode'])),':investname' => trim($pdta['sdcInvestName']),':investemail' => trim($pdta['sdcInvestEmail']),':investinstitution' => strtoupper(trim($pdta['sdcInvestInstitution'])),':institutiontype' => trim($pdta['sdcInvestTQInstType']),':investdivision' => trim($pdta['sdcInvestPrimeDiv']),':oncreationinveststatus' => trim($pdta['sdcInvestTQStatus']),':shipaddy' => trim($pdta['sdcInvestShippingAddress']),':shipphone' => trim($pdta['sdcShippingPhone']),':billaddy' => trim($pdta['sdcInvestBillingAddress']),':billphone' => trim($pdta['sdcBillPhone']),':setupby'  => $usr )); 
         $shipdocnbr = $conn->lastInsertId();         
         $dta['shipdocrefid'] = $shipdocnbr; 

         $sdStsInsSQL = "insert into masterrecord.ut_shipdochistory(shipdocrefid, status, statusdate, bywhom, ondate) values( :shipdocrefid, :status, now(), :bywhom, now())";
         $sdStsInsR = $conn->prepare($sdStsInsSQL); 
         $sdStsInsR->execute(array(':shipdocrefid' => $shipdocnbr, ':status' => 'SHIPDOCCREATED', ':bywhom' => $usr)); 

         //ADD SEGMENTS TO SHIPDOCDETAIL / UPDATE MASTERRECORD SEGMENT
         $segstsSQL = "SELECT menuvalue, additionalinformation FROM four.sys_master_menus where menu = :menu and additionalinformation = :addinformation and dspind = 1";
         $segstsR = $conn->prepare($segstsSQL); 
         $segstsR->execute( array ( ':menu' => 'SEGMENTSTATUS', ':addinformation' => 'SHIPDOCSEGSTATUS' )); 
         $segsts = $segstsR->fetch(PDO::FETCH_ASSOC); 
         $detailInsSQL = "insert into masterrecord.ut_shipdocdetails (shipdocrefid, segid, addon, addedby) value(:shipdocrefid, :segid, now(), :addedby)";
         $detailR = $conn->prepare($detailInsSQL); 
         $updSegSQL = "update masterrecord.ut_procure_segment set segstatus = :segstatus, statusDate = now(), statusby = :statby, shipdocrefid = :shipdocref where segmentid = :segmentid";
         $updSegR = $conn->prepare($updSegSQL);
         $presentSQL = "select ifnull(segstatus,'') as segstatus, ifnull(statusby,'') as segstatusby, ifnull(statusdate,'') statusdate from masterrecord.ut_procure_segment where segmentid = :segmentid";
         $presentR = $conn->prepare($presentSQL);
         $addHistorySQL = "insert into masterrecord.ut_procure_segment_history_status(segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) values(:sg,:psegstat,:pstatby,:pdte,now(),:updusr)";
         $addHistoryR = $conn->prepare($addHistorySQL);

         foreach ( $segments as $s => $sg ) {
           
           $presentR->execute(array(':segmentid' => $sg['segmentid']));
           $pr = $presentR->fetch(PDO::FETCH_ASSOC);

           //UPDATE ALL RECORDS HERE
           $addHistoryR->execute(array( ':sg' => $sg['segmentid'], ':psegstat' => $pr['segstatus'], ':pstatby' => $pr['segstatusby'], ':pdte' => $pr['statusdate'], ':updusr' => $usr ));
           $detailR->execute(array(":shipdocrefid" => $shipdocnbr, ":segid" => $sg['segmentid'], ":addedby" => $usr)); 
           $updSegR->execute(array(":segstatus" => $segsts['menuvalue'], ":statby" => $usr, ":shipdocref" => $shipdocnbr, ":segmentid" => $sg['segmentid']));
           $sdStsInsR->execute(array(':shipdocrefid' => $shipdocnbr, ':status' => "SEGMENT ADDED. SEGID={$sg['segmentid']}", ':bywhom' => $usr)); 
         }
         $responseCode = 200; 
       }
     } else { 
       //SEND BACK ERRORS
       $msg = $msgArr;
     } 
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
     return $rows;        
    }

    function preprocessassignsegments($request, $passedData) { 
       require(serverkeys . "/sspdo.zck");  
       session_start(); 
       $responseCode = 400;  
       $pdta = json_decode($passedData, true); 
       $itemsfound = 0;
       $msgArr = array();
       //$dta = array();
       $errorInd = 0;
       $assignableSQL = "select menuvalue, dspvalue, assignablestatus from four.sys_master_menus where menu = 'SEGMENTSTATUS' and dspind = 1";
       $assignableRS = $conn->prepare($assignableSQL); 
       $assignableRS->execute(); 
       while ($asr = $assignableRS->fetch(PDO::FETCH_ASSOC)) { 
         $assignableStatus[] = $asr;
       }

       foreach ($pdta as $key => $value ) { 
            $chkSQL = "SELECT replace(ifnull(bgs,''),'T_','') as bgs, ifnull(segstatus,'') as segstatus, ifnull(date_format(shippeddate,'%m/%d/%Y'),'') as shippeddate, ifnull(shipdocrefid,'') as shipdocrefid FROM masterrecord.ut_procure_segment where segmentid = :segmentid";
            $chkR = $conn->prepare($chkSQL); 
            $chkR->execute(array(':segmentid' => $value['segmentid'] ));
            if ($chkR->rowCount() > 0) { 
                $r = $chkR->fetch(PDO::FETCH_ASSOC);
                //CHECK STATUS
                $statusFound = 0;
                foreach ($assignableStatus as $aKey => $aVal) {
                  if (strtoupper(trim($r['segstatus'])) === strtoupper(trim($aVal['menuvalue']))) {
                    $statusFound = 1;
                    if ((int)$aVal['assignablestatus'] === 0) {
                      $errorInd = 1; 
                      $msgArr[] .= "{$r['bgs']} is statused as \"{$aVal['dspvalue']}\".  This segment status is unable to be assigned.";
                    }
                  }
                }
                if ($statusFound === 0) { 
                  $errorInd = 1;
                  $msgArr[] .= "Segment Label {$r['bgs']} has an invalid status.  This segment is unable to be assigned.";
                }                

                //CHECK SHIPDATE
                if ($r['shippeddate'] !== "") { 
                  $errorInd = 1;
                  $msgArr[] .= "Segment Label {$r['bgs']} has a shipment date ({$r['shippeddate']}) .  This segment is unable to be assigned.";
                }                
                //CHECK SHIPDOCID 
                if ($r['shipdocrefid'] !== "") { 
                  $errorInd = 1;
                  $sddsp = substr(('000000' . $r['shipdocrefid']),-6);
                  $msgArr[] .= "Segment Label {$r['bgs']} is listed on ship-doc ({$sddsp}) .  This segment is unable to be assigned.";
                }                
            } else { 
                $errorInd = 1; 
                $msgArr[] .= "Segment does not Exist.  See CHTN Eastern IT (dbSegmentId: {$value['segmentid']})";
            }            

       }

       if ($errorInd === 0) { 
         $responseCode = 200;
         $dta = array('pagecontent' =>  bldDialog('dataCoordinatorBGSAssignment', $passedData));
       }

       $msg = $msgArr;       
       $rows['statusCode'] = $responseCode; 
       $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
       return $rows;        
    }
    
    
}

class systemposts { 

  function sessionlogin($request, $passedData) { 
    session_start();   
    $responseCode = 503; 
    $params = json_decode($passedData, true);
    $ec = $params['ency'];
    $cred = json_decode(chtndecrypt($params['ency']), true); //{\"user\":\"z\",\"pword\":\"z\",\"dauth\":\"z\"}
    $u = $cred['user'];
    $p = $cred['pword'];
    $au = explode("-",$cred['dauth']);
    $sess = session_id(); 
    $ip = $_SERVER['REMOTE_ADDR'];
    require(serverkeys . "/sspdo.zck"); 
    $usrChk = "SELECT userid, username, emailaddress, fiveonepword FROM four.sys_userbase where allowind = 1 and emailaddress = :pword and datediff(passwordexpiredate, now()) > -1";
    $usrR = $conn->prepare($usrChk); 
    $usrR->execute(array(':pword' => $u)); 
    $ucnt = $usrR->rowCount(); 
    if ($ucnt > 0 ) { 
      $r = $usrR->fetch(PDO::FETCH_ASSOC);  
      $dbPword = $r['fiveonepword'];
      if (password_verify($p,  $dbPword)) { 
        //USER NAME AND PASSWORD CORRECT - CHECK AUTH          
         if (count($au) <> 2) { 
           //BAD AUTH
           $msg = "The Dual Authentication number you entered is an incorrect format";             
         } else {                        
           $auSQL = "select inputon, ifnull(registerUserIP,'') as registeredUserIP, ifnull(useremail,'') as useridemail, ifnull(phpsessid,'NOTSET') as phpsessid, datediff(now(), inputon) dayssince FROM serverControls.sys_ssv7_authcodes where authid = :aid and authcode = :acode and (phpsessid = :sess OR   ifnull(phpsessid,'NOTSET') = 'NOTSET')  and (useremail = :uemail OR ifnull(useremail,'') = '') "; 
           $auR = $conn->prepare($auSQL);
           $auR->execute(array(':aid' => $au[0], ':acode' => $au[1], ':sess' => $sess, ':uemail' =>$u));
           if ($auR->rowCount() === 1) {                
               //CHECK DAYS SINCE
               $authenR = $auR->fetch(PDO::FETCH_ASSOC);
               if ((int)$authenR['dayssince'] < 31) { 
                   //GOOD - SESSION CREATE LOGGEDON - CAPTURE SYSTEM ACTIVITY - REDIRECT IN JAVASCRIPT WITH STATUSCODE = 200
                   session_regenerate_id(true);
                   $newsess = session_id();
                   $updAuthSQL = "update serverControls.sys_ssv7_authcodes set phpsessid = :newsess where authid = :aid and authcode = :acode and useremail = :uemail";
                   $updAR = $conn->prepare($updAuthSQL);
                   $updAR->execute(array(':newsess' => $newsess, ':aid' => $au[0], ':acode' => $au[1], ':uemail' => $u)); 
                   if (!isset($_COOKIE['ssv7_dualcode'])) { 
                     setcookie('ssv7_dualcode', "{$au[0]}-{$au[1]}", time() + 2592000, '/','',true,true); // 2592000 = 30 days 3600 - is one hour
                   } 
                   $updUSRSQL = "update four.sys_userbase set sessionid = :sess, sessionExpire = date_add(now(), INTERVAL 7 HOUR) where emailaddress = :emluser"; 
                   $updUsrR = $conn->prepare($updUSRSQL); 
                   $updUsrR->execute(array(':sess' => $newsess, ':emluser' => $u));
                   captureSystemActivity($newsess, '', 'true', $u, '', '', $u, 'POST', 'LOGIN SUCCESS');                   
                   $_SESSION['loggedin'] = 'true';
                   $_SESSION['userid'] = $u;
                   $msg = "SUCCESS " . session_id();
                   $responseCode = 200;                    
               } else { 
                   setcookie('ssv7_dualcode', "{$au[0]}-{$au[1]}", time() - 3600, '/','',true,true); // 3600 - is one hour Delete a COOKIE
                   captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'AUTH CODE EXPIRED');
                   $msg = "The authentication code has expired.  Please request a new code";
               }
           } else { 
               captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'AUTH CODE INCORRECT');
               $msg = "The entered authentication code is not correct";
           }             
         }
      } else { 
      //BAD PASSWORD
         captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'CREDENTIALS INCORRECT');
         $msg = "Either User Name and/or password is incorrect";   
      }
    } else { 
        //NO USER FOUND
        captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'USER NOT FOUND');
        $msg = "Either User Name and/or password is incorrect";           
    }                 
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => 0,  'DATA' => "");
    return $rows;      
  }

  function requestdualcode($request, $passedData) { 
    $responseCode = 200; 
    $params = json_decode($passedData, true);
    $rquester = $params['rqstuser']; 
    if (trim($rquester) !== "") {
  
      require(serverkeys . "/sspdo.zck"); 
      $chkSQL = "SELECT userid, emailaddress, altphonecellcarrier FROM four.sys_userbase where allowind = 1 and emailaddress = :useremail";
      $chkR = $conn->prepare($chkSQL);
      $chkR->execute(array(':useremail' => $rquester));
      if ($chkR->rowCount() > 0) { 
        
        //SEND EMAIL
         $characters = '0123456789';
         $charactersLength = strlen($characters);
         $randomString = '';
         for ($i = 0; $i < 5; $i++) {
           $randomString .= $characters[rand(0, $charactersLength - 1)];
         }
         session_start();
         $capAuthSQL = "insert into serverControls.sys_ssv7_authcodes (authcode, phpsessid, useremail, inputon) values(:authcode, :sess, :uemail,  now())";
         $capAuthR = $conn->prepare($capAuthSQL);
         $capAuthR->execute(array(':authcode' => $randomString, ':sess' => session_id(), ':uemail' => $rquester));
         $capID = $conn->lastInsertId();
         $authCode = "{$capID}-{$randomString}"; 
         $rqstr = $chkR->fetch(PDO::FETCH_ASSOC);
         if (trim($rqstr['altphonecellcarrier']) !== "") { 
             $emlTo = $rqstr['altphonecellcarrier'];
         } else { 
             $emlTo = $rqstr['emailaddress'];
         }
         $rtn = sendSSCodeEmail( $emlTo, $authCode );
         $msg = session_id();
      }

    } else {
      $responseCode = 503;
      $msg = "YOU MUST SUPPLY AN EMAIL USERNAME";
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => 0,  'DATA' => "");
    return $rows;      
  }  
    
}

/*****  SUPPORTING FUNCTIONS FOR CLASSES ******/ 

function captureSystemActivity($sessionid, $sessionvariables, $loggedsession, $userid, $firstname, $lastname, $email, $requestmethod, $request) { 
    include(serverkeys . "/sspdo.zck"); 
    $insSQL = "insert into webcapture.tbl_siteusage (usagedatetime, sessionid, sessionvariables, loggedsession, userid, firstname, lastname, email, requestmethod, request)  values(now(),  :sessionid, :sessionvariables, :loggedsession, :userid, :firstname, :lastname, :email, :requestmethod, :request)";
    $insR = $conn->prepare($insSQL); 
    $insR->execute(array(
        ':sessionid' => $sessionid
       ,':sessionvariables' => $sessionvariables
       ,':loggedsession' => $loggedsession
       ,':userid' => $userid
       ,':firstname' => $firstname
       ,':lastname' => $lastname
       ,':email' => $email
       ,':requestmethod' => $requestmethod
       ,':request' => $request
    )); 
}

function sendSSCodeEmail( $emailTo, $authCode ) { 
  //TODO: CHECK THE INPUT BEFORE ALLOWING TO TABLE    
  require(serverkeys . "/sspdo.zck"); 
  $insSQL = "insert into serverControls.emailthis (toWhoAddressArray, sbjtLine, msgBody, htmlind, wheninput, bywho) values (:toWhomAddressArray,:sbjtLine,:msgBody,0,now(),:bywho)";
  $insR = $conn->prepare($insSQL);
  $insR->execute(array(':toWhomAddressArray' => '["' . $emailTo . '"]',':sbjtLine' => 'SSv7 Authentication Code',':msgBody' => 'The ScienceServer Dual Authentication Code to enter is: ' . $authCode,':bywho' => 'SSv7')); 
  return $conn->errorInfo(); 
}

function verifyDate($date, $format,  $strict = true) {
    $dateTime = DateTime::createFromFormat($format, $date);
    if ($strict) {
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
    }
    return $dateTime !== false;
}

function qryCriteriaCheckBio($rqst) { 
    $allowerror = 0;
    $msg = "";  
  //  {"qryType":"BIO","BG":"","procInst":"","segmentStatus":"","qmsStatus":"","procDateFrom":"2018-11-01","procDateTo":"2018-11-04","shipDateFrom":"2018-11-01","shipDateTo":"2018-11-04","investigatorCode":"","site":"","diagnosis":"","PrepMethod":"","preparation
    //Check Keys in Array 
    $needkeys = array("qryType","BG","procInst","segmentStatus","qmsStatus","procDateFrom","procDateTo","shipDateFrom","shipDateTo","investigatorCode","shipdocnbr","shipdocstatus","site","specimencategory","phiage","phirace","phisex","procType","PrepMethod","preparation");
    $keysExist = 1;
    foreach ($needkeys as $keyval) { 
        if (!array_key_exists($keyval, $rqst)) {
            $keysExist = 0;
        }
    }
    if ($keysExist === 0) { 
        $allowerror = 1; 
        $msg = "ERROR:  BAD REQUEST ARRAY IN BODY";
        return array('errorind' => $allowerror, 'errormsg' => $msg);        
    }
    $fieldsFilledIn = 0; 
    foreach ($rqst as $k => $v) { 
      if ($k !== "qryType") {   
          if (trim($v) !== "") { 
            $fieldsFilledIn++;
          }
      }
    } 
    if ($fieldsFilledIn < 1 ) { 
       $allowerror = 1; 
       $msg .= "- At least one criteria field must be filled in"; 
       return array('errorind' => $allowerror, 'errormsg' => $msg);                
    }
    //NO CHECK procInst,segmentStatus,qmsStatus,site,diagnosis,prepmethod, preparation, shipdocstatus, phirace, phisex

    $charallowarray = array("0","1","2","3","4","5","6","7","8","9","-",",","Z");     
    //CHECK BIOGROUP NUMBER
    if ( trim($rqst['BG']) !== "" ) { 
      //NOTE:  I DID NOT USE PREG_MATCH HERE BECAUSE ITS NOT A PATTERN THAT NEEDS TO BE VALIDATED - ZACK 
      $cleanBG = 0;
      for ($i = 0; $i < strlen(trim($rqst['BG'])); $i++) { 
        if (!in_array(substr(trim($rqst['BG']),$i,1), $charallowarray)) { 
            $cleanBG = 1;      
        }
      }
      if ($cleanBG === 1) { 
        $allowerror = 1; 
        $msg .= "\r\n- ONLY numbers, hyphens, commas and prefix-capital-Z are allowed in the biogroup field";
      }
      if (strpos(trim($rqst['BG']),"-") == true && strpos(trim($rqst['BG']),',') == true) { 
        $allowerror = 1; 
        $msg .= "\r\n- Only a Series or a Range search is allowed at any one time (Biogroup Number)";
      }
    }

    //CHECK Ship Doc Nbr
    if ( trim($rqst['shipdocnbr']) !== "" ) { 
      //NOTE:  I DID NOT USE PREG_MATCH HERE BECAUSE ITS NOT A PATTERN THAT NEEDS TO BE VALIDATED - ZACK 
      $cleanSN = 0;
      for ($i = 0; $i < strlen(trim($rqst['shipdocnbr'])); $i++) { 
        if (!in_array(substr(trim($rqst['shipdocnbr']),$i,1), $charallowarray)) { 
            $cleanSN = 1;      
        }
      }
      if ($cleanSN === 1) { 
        $allowerror = 1; 
        $msg .= "\r\n- ONLY numbers, hyphens and commas are allowed in the Ship Doc Number field";
      }
      if (strpos(trim($rqst['shipdocnbr']),"-") == true && strpos(trim($rqst['shipdocnbr']),',') == true) { 
        $allowerror = 1; 
        $msg .= "\r\n- Only a Series or a Range searches is allowed at any one time (Ship Doc Number)";
      }
    }

    $charallowagearray = array("0","1","2","3","4","5","6","7","8","9","-");     
    if ( trim($rqst['phiage']) !== "" ) { 
      //NOTE:  I DID NOT USE PREG_MATCH HERE BECAUSE ITS NOT A PATTERN THAT NEEDS TO BE VALIDATED - ZACK 
      $cleanAGE = 0;
      for ($i = 0; $i < strlen(trim($rqst['phiage'])); $i++) { 
        if (!in_array(substr(trim($rqst['phiage']),$i,1), $charallowagearray)) { 
            $cleanAGE = 1;      
        }
      }
      if ($cleanAGE === 1) { 
        $allowerror = 1; 
        $msg .= "\r\n- ONLY numbers and hyphens are allowed in the PHI Age field";
      }
    }

    //CHECK INV NUMBER
    if (trim($rqst['investigatorCode']) !== "") { 
        if (preg_match('/^inv[0-9]{1,6}/i', trim($rqst['investigatorCode'])) === 0) { 
          $allowerror = 1; 
          $msg .= "\r\n- Investigator ID must be in the format of 'INV####', eg. 'INV3000'";            
        }
    }
    //CHECK DATES
    if ( (trim($rqst['procDateFrom']) !== "" && trim($rqst['procDateTo']) === "" ) ||   (trim($rqst['procDateFrom']) === "" && trim($rqst['procDateTo']) !== "" ) ) { 
        $allowerror = 1; 
        $msg .= "\r\n- When querying by date, you must provide both dates within the range (Procurement Date).";          
    } else { 
    $procDteAllowFrom = 0;
    if (trim($rqst['procDateFrom']) !== "") { 
        $fDteChk = verifyDate(trim($rqst['procDateFrom']),'Y-m-d', true); 
        if (!$fDteChk) { 
          $allowerror = 1; 
          $msg .= "\r\n- The 'From' date in the Procurement date range is an invalid date.";              
        } else { 
          $fDte =  DateTime::createFromFormat('Y-m-d', $rqst['procDateFrom'])->setTime(0,0);
          $procDteAllowFrom = 1;
        }
    }
    $procDteAllowTo = 0;
    if (trim($rqst['procDateTo']) !== "") { 
        $tDteChk = verifyDate(trim($rqst['procDateTo']),'Y-m-d', true);         
        if (!$tDteChk) { 
          $allowerror = 1; 
          $msg .= "\r\n- The 'To' date in the Procurement date range is invalid.";              
        } else { 
          $tDte =  DateTime::createFromFormat('Y-m-d', $rqst['procDateTo'])->setTime(0,0);
          $procDteAllowTo = 1;
        }
    }    
    if ($procDteAllowFrom === 1 && $procDteAllowTo === 1) { 
        //CHECK FROM IS SMALLER THAN TO 
        if (!($fDte < $tDte)) { 
           $allowerror = 1;
           $msg .= "\r\n- When using the procurement date range, the 'from' date must be before the 'to' date.";
        }        
    }
 }

    if ( (trim($rqst['shipDateFrom']) !== "" && trim($rqst['shipDateTo']) === "" ) ||   (trim($rqst['shipDateFrom']) === "" && trim($rqst['shipDateTo']) !== "" ) ) { 
        $allowerror = 1; 
        $msg .= "\r\n- When querying by date, you must provide both dates within the range (Shipment Date).";          
    } else { 
    $shipDteAllowFrom = 0;
    if (trim($rqst['shipDateFrom']) !== "") { 
        $sfDteChk = verifyDate(trim($rqst['shipDateFrom']),'Y-m-d', true); 
        if (!$sfDteChk) { 
          $allowerror = 1; 
          $msg .= "\r\n- The 'From' date in the Shipment date range is an invalid date.";              
        } else { 
          $sfDte =  DateTime::createFromFormat('Y-m-d', $rqst['shipDateFrom'])->setTime(0,0);
          $shipDteAllowFrom = 1;
        }
    }
    $shipDteAllowTo = 0;
    if (trim($rqst['shipDateTo']) !== "") { 
        $stDteChk = verifyDate(trim($rqst['shipDateTo']),'Y-m-d', true);         
        if (!$stDteChk) { 
          $allowerror = 1; 
          $msg .= "\r\n- The 'To' date in the Shipment date range is invalid.";              
        } else { 
          $stDte =  DateTime::createFromFormat('Y-m-d', $rqst['shipDateTo'])->setTime(0,0);
          $shipDteAllowTo = 1;
        }
    }    
    if ($shipDteAllowFrom === 1 && $shipDteAllowTo === 1) { 
        //CHECK FROM IS SMALLER THAN TO 
        if (($sfDte > $stDte)) { 
           $allowerror = 1;
           $msg .= "\r\n- When using the shipment date range, the 'from' date must be before the 'to' date.";
        }        
    }
 }

  return array('errorind' => $allowerror, 'errormsg' => $msg);        
}

function qryCriteriaCheckShip($rqstJSON) { 
    //{"qryType":"SHIP","shipdocnumber":"","sdstatus":"","sdshipfromdte":"","sdshiptodte":"","investigator":""}
    $allowerror = 0;
    $msg = "";
        
    return array('allowind' => $allowerror, 'errormsg' => $msg);
}

function qryCriteriaCheckBank($rqstJSON) { 
    //{"qryType":"BANK","site":"","dx":"","specimencategory":"","prepFFPE":1,"prepFIXED":1,"prepFROZEN":1}
    $allowerror = 0;
    $msg = "";
        
    return array('allowind' => $allowerror, 'errormsg' => $msg);
}

function bldDialog($whichdialog, $passedData) {  
  $at = applicationTree; 
  require("{$at}/sscomponent_pagecontent.php"); 
  $bldr = new pagecontent(); 
  $rtnDialog = $bldr->sysDialogBuilder($whichdialog, $passedData);
  return $rtnDialog;
}


/********** EMAIL TEXTING 
AT&T: number@txt.att.net (SMS), number@mms.att.net (MMS)
T-Mobile: number@tmomail.net (SMS & MMS)
Verizon: number@vtext.com (SMS), number@vzwpix.com (MMS)
Sprint: number@messaging.sprintpcs.com (SMS), number@pm.sprint.com (MMS)
Virgin Mobile: number@vmobl.com (SMS), number@vmpix.com (MMS)
Tracfone: number@mmst5.tracfone.com (MMS)
Metro PCS: number@mymetropcs.com (SMS & MMS)
Boost Mobile: number@sms.myboostmobile.com (SMS), number@myboostmobile.com (MMS)
Cricket: number@sms.cricketwireless.net (SMS), number@mms.cricketwireless.net (MMS)
Republic Wireless: number@text.republicwireless.com (SMS)
Google Fi (Project Fi): number@msg.fi.google.com (SMS & MMS)
U.S. Cellular: number@email.uscc.net (SMS), number@mms.uscc.net (MMS)
Ting: number@message.ting.com
Consumer Cellular: number@mailmymobile.net
C-Spire: number@cspire1.com
Page Plus: number@vtext.com
 */


