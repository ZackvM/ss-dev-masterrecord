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
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $rows = array(); 
      $qryrqst = json_decode($passdata, true);

      $msg = $qryrqst['investigatorid'];

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
    
    function hprstatusbybiogroup($request, $passedData) { 
        //["81948","82852"]
       $responseCode = 503;  
       $bgList = json_decode($passedData, true); 
       $itemsfound = 0;
       $dta = array();
       //if (count($bgList['biogroups']) < 0) { 
       //    $responseCode = 400;
       //    $msg = "YOU HAVE NOT SELECTED ANY BIOGROUPS"; 
       //} else {
           //LOOK UP BIOGROUPS
       //    $errorind = 0; 
       //    $errormsg = "";
       //    foreach ($bgList['biogroups'] as $bgkey => $bgval) { 
           //1) CHECK GET QMS STATUS OF BIOGROUP
           //2) CHECK THAT THERE IS A PATHOLOGY REPORT
           //3) SEE IF SEGID IS A SLIDE
           //4) SEE IF SEGID IS ASSIGNED TO QC
         //    $bg = $bgval; 
         //    $segid = $bgList['segments'][$bgkey];   
         //      $msg .= " .. {$bgkey} => {$bgval} // {$bgList['segments'][$bgkey]} " ; 
         //  }
       //}        
       //$dta = count($bgList['biogroups']); ///TESTING LINE ONLY
       $rows['statusCode'] = $responseCode; 
       $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
       return $rows;    
    }
    
    function assignbiogroup($request, $passedData) { 
       require(serverkeys . "/sspdo.zck");  
       session_start(); 
       $responseCode = 400;  
       $pdta = json_decode($passedData, true); 
       $itemsfound = 0;
       $msgArr = array();
       //$dta = array();
       //{"0":{"biogroup":"81948","segmentid":"431100"},"1":{"biogroup":"81948","segmentid":"431101"},"2":{"biogroup":"81948","segmentid":"431104"}}
       $errorInd = 0;
       $assignableSQL = "select menuvalue, dspvalue, assignablestatus from four.sys_master_menus where menu = 'SEGMENTSTATUS'";
       $assignableRS = $conn->prepare($assignableSQL); 
       $assignableRS->execute(); 
       while ($asr = $assignableRS->fetch(PDO::FETCH_ASSOC)) { 
         $assignableStatus[] = $asr;
       }

       foreach ($pdta as $key => $value ) { 
           //$dta .= " ... {$key} {$value['segmentid']}";
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


