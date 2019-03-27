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
          $this->rtnData = json_encode(array("MESSAGE" => "END-POINT FUNCTION NOT FOUND: {$request[3]}","ITEMSFOUND" => 0, "DATA" => ""));
        }
      }
    }
}

}

class datadoers {

    function pristinebarcoderun ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      //TODO: CHECK USER FOR PRISTINE BARCODE FUNCTION



      if ($errorInd === 0 ) {


      
        $responseCode = 200;
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;   
    }

    function validatechtnvocabulary ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      session_start();
      $sess = session_id();
      $pdta = json_decode($passdata, true);
      //{"speccat":"MALIGNANT","psite":"BLADDER","subsite":"SEROSA","dx":"CARCINOMA :: UROTHELIAL (TRANSITIONAL CELL)","metssite":"KIDNEY","siteposition":"LEFT","pdxsystemic":"ZACKITIS"}
      //$authuser = $_SERVER['PHP_AUTH_USER']; 
      //$authpw = $_SERVER['PHP_AUTH_PW'];      
      require(serverkeys . "/sspdo.zck");

      
      //CHECK MAIN VOCAB
      $mainSQL = "SELECT vocabid FROM four.sys_master_menu_vocabulary where 1 = 1 and specimencategory = :spc and site = :ste";
      $mainSQL .= ( trim($pdta['subsite']) !== "" ) ? " and subsite = :sste " : "";
      $mainSQL .= ( trim($pdta['dx']) !== "" ) ? " and REPLACE(diagnosis,'\\\', '::') = :dx " : "";
      $mainRS = $conn->prepare($mainSQL);
      $exeArr[':spc'] = $pdta['speccat'];
      $exeArr[':ste'] = $pdta['psite'];
      if ( trim($pdta['subsite']) !== "" ) { $exeArr[':sste'] = $pdta['subsite']; }
      if ( trim($pdta['dx']) !== "" ) { $exeArr[':dx'] = $pdta['dx']; }
      $mainRS->execute($exeArr);
      $cntMain = $mainRS->rowCount();

      $cntSys = 0; 
      if ( trim($pdta['pdxsystemic']) !== "" ) { 
        $sysSQL = "SELECT * FROM four.sys_master_menu_vocabulary where systemicIndicator = 1  and diagnosis = :sysdx";
        $sysRS = $conn->prepare($sysSQL); 
        $sysRS->execute(array(':sysdx' => $pdta['pdxsystemic']));
        $cntSys = $sysRS->rowCount();
      } else { 
        $cntSys = 1;
      }

      $cntMets = 0;
      if ( trim($pdta['metssite']) !== "" ) { 
        $metsSQL = "select * from (SELECT distinct site as chksite FROM four.sys_master_menu_vocabulary where ifnull(site,'') <> '') vocsitecheck where vocsitecheck.chksite = :metssite";
        $metsRS = $conn->prepare($metsSQL); 
        $metsRS->execute(array(':metssite' => $pdta['metssite']));
        $cntMets = $metsRS->rowCount();
      } else { 
        $cntMets = 1;
      }


      $dta =  array( 'mainvocchk' => $cntMain, 'systemicvocchk' =>  $cntSys, 'metsvocchk' => $cntMets);



      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;           
    }

    function dialogactionsavecomments ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      session_start();
      $sess = session_id();
      $pdta = json_decode($passdata, true);
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      require(serverkeys . "/sspdo.zck");
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, emailaddress FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
        $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
      }

      //{"comment":"HPR - 90T, RARE!","key":"{\"commenttype\":\"BIOSAMPLE\",\"record\":\"M0lsaFhOUURZaE9UZlJIVHY0aklidz09\",\"access\":\"cHNyWDdYa2dmaEJPRkdvaFF2MzdTS01TZWlGajBjMGxwOTFFSnp3NWtOdz0=\"}"}
      ( !array_key_exists('comment', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'comments' missing from passed data")) : "";
      ( !array_key_exists('key', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'key' missing from passed data")) : ( trim($pdta['key']) === '' )  ? (list( $errorInd, $msgArr[] ) = array(1 , "Key Cannot be empty - See a CHTNEastern Informatics Person")) : "";


      if ( $errorInd === 0 ) { 
        $key = json_decode($pdta['key'] , true);
        ( !array_key_exists('commenttype', $key) || !array_key_exists('record', $key) || !array_key_exists('access', $key) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Key Data Structure is incorrect")) : "";        
      }

      if ( $errorInd === 0 ) {
        ( trim($key['commenttype']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "COMMENT TYPE CANNOT BE EMPTY")) : "";
        ( trim($key['record']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "RECORD CANNOT BE EMPTY")) : "";
        ( trim($key['access']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SECURITY ISSUE")) : "";
      }

      if ( $errorInd === 0 ) {
        ( trim($key['commenttype']) !== 'HPRQ' && trim($key['commenttype']) !== 'BIOSAMPLE' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "COMMENT TYPE CAN ONLY BE BIOSAMPLE OR HPR QUESTION")) : "";
        $recordid = cryptservice( $key['record'],'d' );
        ( trim($recordid) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "RECORD ID IS INCORRECT")) : "";
        $access = cryptservice( $key['access'] , 'd' );
        ( trim($access) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SECURITY ISSUE")) : "";
      }

      if ( $errorInd === 0 ) {
        $accessSQL = "select * from serverControls.ss_srvIdents where accesscode = :akey and sessid = :sess and timestampdiff(minute, onwhen, now()) < 60";
        $accessR = $conn->prepare($accessSQL);
        $accessR->execute(array(':sess' => $sess, ':akey' => $access));
        ( $accessR->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SECURITY KEY DOES NOT EXIST OR HAS EXPIRED.  SEE A CHTN INFORMATICS PERSON")) : "";
      }

      if ( $errorInd === 0 ) { 
        //COMMIT CHANGES
        if ( $key['commenttype'] === 'BIOSAMPLE') {
          $captureHistSQL = "insert into masterrecord.history_procure_biosample_comments (biosample, previouscomment, commenttype, commentupdatedon, commentupdatedby) SELECT pbiosample, biosamplecomment, 'BIOSAMPLECOMMENT', now(), 'proczack' FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample"; 
          $updateSQL = "update masterrecord.ut_procure_biosample set biosamplecomment = :newComment where pbiosample = :pbiosample";
        } 
        if ( $key['commenttype'] === 'HPRQ' ) { 
          $captureHistSQL = "insert into masterrecord.history_procure_biosample_comments (biosample, previouscomment, commenttype, commentupdatedon, commentupdatedby) SELECT pbiosample, questionHPR, 'HPRQUESTION', now(), 'proczack' FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample"; 
          $updateSQL = "update masterrecord.ut_procure_biosample set questionHPR = :newComment where pbiosample = :pbiosample";
        }
        $capR = $conn->prepare($captureHistSQL); 
        $capR->execute(array(':pbiosample' => $recordid));
        $updR = $conn->prepare($updateSQL);
        $updR->execute(array(':newComment' => $pdta['comment'], ':pbiosample' => $recordid));
        $delSrvSQL = "delete FROM serverControls.ss_srvIdents where timestampdiff( minute, onwhen, now()) > 60";
        $delSrvR = $conn->prepare($delSrvSQL); 
        $delSrvR->execute();  
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;           
    }
    
    function preprocessgeneratedialog ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      session_start();
      $sess = session_id();
      $pdta = json_decode($passdata, true);
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      require(serverkeys . "/sspdo.zck");
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, emailaddress FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
        $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
      }
       //{"whichdialog":"CMTEDIT","objid":"BGC:82454"}
      ( !array_key_exists('whichdialog', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'whichdialog' missing from passed data")) : ( trim($pdta['whichdialog']) === '' )  ? (list( $errorInd, $msgArr[] ) = array(1 , "Dialog Not Specified")) : "";
      ( !array_key_exists('objid', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'objid' missing from passed data")) : ( trim($pdta['objid']) === '' )  ? (list( $errorInd, $msgArr[] ) = array(1 , "Object Not Specified")) : "";

       //TODO - CHECK VALID OBJID      
      if ( $errorInd === 0 ) { 
 
         $pdta['dialogid'] = generateRandomString(15); 
         $newpassdata = json_encode($pdta); 
         $dlgPage = bldDialogGetter($pdta['whichdialog'], $newpassdata );

         $left = '5vw';
         $top = '13vh';
         $primeFocus = '';
         switch ( $pdta['whichdialog'] ) { 
           case 'dlgCMTEDIT':
             $primeFocus = "fldDspBGComment";
             $left = '25vw';
             $top = '15vh';
             break;
           case 'dlgEDTENC':
             $primeFocus = "";
             $left = '5vw';
             $top = '12vh';
             break;
           case 'dlgEDTDX':
             $primeFocus = "";
             $left = '34vw';
             $top = '12vh';
             break;
         }

         $dta = array("pageElement" => $dlgPage, "dialogID" => $pdta['dialogid'], 'left' => $left, 'top' => $top, 'primeFocus' => $primeFocus);
         $responseCode = 200;
       }
       
       $msg = $msgArr;
       $rows['statusCode'] = $responseCode; 
       $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
       return $rows;           
    } 

    function masterbgrecord ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      if ( $authuser === "chtneast" && $authpw === serverpw ) { 
  
  
        require(serverkeys . "/sspdo.zck");
        $pdta = json_decode($passdata, true);
        $bgn = cryptservice($pdta['bgency'],'d',false);

        //TODO:  DATA CHECKS AND SECURITY CHECKS???
        
        if ($errorInd === 0 ) {
         $masterSQL = getLongSQLStmts('masterbgscreen');   
         $bgrs = $conn->prepare($masterSQL);
         $bgrs->execute(array(':biog' => $bgn));
         if ( $bgrs->rowCount() === 1 ) {
             $itemsfound = 1;
             $bg = $bgrs->fetch(PDO::FETCH_ASSOC);
             
             $dta['bgnbr']                          = $bgn;
             $dta['readlabel']                     = $bg['readlabel'];
             $dta['pristineselector']           = $bg['pristineselector'];
             $dta['voidind']                        = $bg['voidind'];
             $dta['procureinstitution']       = $bg['dspinstitution'];
             $dta['technician']                   = $bg['technician'];
             $dta['proceduredate']             = $bg['proceduredate'];
             $dta['associativeid']               = $bg['associd'];
             $dta['collecttype']                  = $bg['collectproctype'];
             $dta['specimencategory']       = $bg['speccat'];             
             $dta['collectedsite']               = $bg['site'];
             $dta['diagnosis']                    = $bg['diagnosis'];
             $dta['mets']                           = $bg['mets'];
             $dta['siteposition']                = $bg['siteposition'];
             $dta['systemicdx']                  = $bg['systemicdx'];
             $dta['proceduredate']                  = $bg['proceduredate'];             
             $dta['pxiid']                  = $bg['pxiid'];             
             $dta['phiage']                  = $bg['pxiage'];             
             $dta['phirace']                  = $bg['pxirace'];             
             $dta['phisex']                  = $bg['pxisex'];             
             $dta['cxind']                  = $bg['cxind'];             
             $dta['rxind']                  = $bg['rxind'];
             $dta['icind']                  = $bg['icind'];
             $dta['prind']                  = $bg['prind'];
             $dta['subjectnbr']                  = $bg['subjectnbr'];
             $dta['protocolnbr']                  = $bg['protocolnbr'];
             $dta['hprind']                  = $bg['hprind'];
             $dta['hprmarkbyon']                  = $bg['hprmarkbyon'];
             $dta['qcind']                  = $bg['qcind'];
             $dta['qcmarkbyon']                  = $bg['qcmarkbyon'];
             $dta['qcvalue']                  = $bg['qcvalue'];
             $dta['qcprocstatus']                  = $bg['qcprocstatus'];
             $dta['qmsstatusby']                  = $bg['qmsstatusby'];
             $dta['qmsstatuson']                  = $bg['qmsstatuson'];
             $dta['hprstatus']                  = $bg['hprstatus'];
             $dta['hprresult']                  = $bg['hprresult'];
             $dta['hprslidereviewed']                  = $bg['hprslidereviewed'];
             $dta['hprby']                  = $bg['hprby'];
             $dta['hpron']                  = $bg['hpron'];
             $dta['biosamplecomment']                  = $bg['biosamplecomment'];
             $dta['questionhpr']                  = $bg['questionhpr'];

           $sgSQL = getLongSQLStmts('masterbgsegscreen');             
           $sgRS = $conn->prepare($sgSQL);
           $sgRS->execute(array(':pbiosample' => $bgn));
           $sg = array();
           if ($sgRS->rowCount() < 1) { 
               //NO SEGMENTS
           } else { 
               while ($sgr = $sgRS->fetch(PDO::FETCH_ASSOC)) { 
                   $sg[] = $sgr;
               }
           }
           
           $dta['segments'] = $sg;
             
             
         }     
         $responseCode = 200;
        }      



      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;   
    }

    function quicksegmentstatusupdate ( $request, $passdata ) { 
       $rows = array(); 
       //$dta = array(); 
       $responseCode = 400;
       $msgArr = array(); 
       $errorInd = 0;
       $msg = "BAD REQUEST";
       $itemsfound = 0;
       session_start();
       require(serverkeys . "/sspdo.zck");
       $pdta = json_decode($passdata, true);
      
       //CHECK USER IS CORRECT AND COORD AND ALLOWED 
       $usr = chtndecrypt($pdta['userid']);
       $sess = session_id();      
       $authuser = $_SERVER['PHP_AUTH_USER']; 
       $authpw = $_SERVER['PHP_AUTH_PW'];      
       $authchk = cryptservice($authpw,'d', true, $authuser);
       if ( $authuser !== $authchk || $authuser !== $sess ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
       } 
       $chkUsrSQL = "SELECT originalaccountname, emailaddress FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and allowInvtry = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0 and inventorypinkey = :pinkey";
       $chkUsrR = $conn->prepare($chkUsrSQL); 
       $chkUsrR->execute(array(':sess' => $sess, ':pinkey' => $usr));

       if ($chkUsrR->rowCount() <> 1) { 
         (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your inventory pin key is incorrect or you are not allowed to perform this action."));
       } else { 
         $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
       }
       //CHECK DEVREASON IS GIVEN
       ( trim($pdta['devReason']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE REASON FOR DEVIATING FROM CHTNEASTERN SOPs MUST BE SPECIFIED." )) : "";       
       $chkSQL = "SELECT * FROM four.sys_master_menus where menu like 'DEVIATIONREASON_HPROVERRIDE' and dspind = 1 and dspValue = :value";
       $chkR = $conn->prepare($chkSQL);
       $chkR->execute(array(':value' => $pdta['devReason']));
       if ($chkR->rowCount() <> 1) { 
         (list( $errorInd, $msgArr[] ) = array(1 , "Deviation Reason is not allowable"));    
       }
       //CHECK THAT SAMPLES ARE LISTED
       $segidCnt = 0;        
       $allSegExist = 1;
       foreach ($pdta as $key => $val) {
         if ($key !== 'userid' && $key !== 'devReason') { 
            $segChkSQL = "SELECT replace(bgs,'_','') as bgs FROM masterrecord.ut_procure_segment where segmentid = :segid and segstatus = :segstatus and voidind = 0";
            $segChkR = $conn->prepare($segChkSQL); 
            //TODO:MAKE THIS DYNAMIC
            $segChkR->execute(array(':segid' => $key, ':segstatus' => 'ONOFFER'  ));
            if ( $segChkR->rowCount() <> 1) { 
                (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT ({$val['bgs']}) was either not found or has had a different status than 'ON OFFER'.  Refresh your screen and try again."));
                $allSegExist = 0;
            } 
           $segidCnt++;
         }
       }
       ($segidCnt === 0) ? (list( $errorInd, $msgArr[] ) = array(1 , "NO BIOSAMPLES SPECIFIED")) : "";    
       //CHECK THAT SAMPLES EXIST
       ($allSegExist === 0) ? (list( $errorInd, $msgArr[] ) = array(1 , "NOT ALL THE SEGMENTS EXIST IN THE DATABASE AS GIVEN.  REFRESH YOUR SCREEN AND TRY AGAIN - OR SEE A CHTNEASTERN INFORMATICS PERSON.")) : "";          
       //TODO:CHECK THAT VALUE FOR NEW STATUS IS GIVEN AND CORRECT
       //TODO:CHECK: OPTIONAL LOCATIONCODE IS VALID - IF NOT DEFAULT TO A CHECKIN LOCATION 

       if ( $errorInd === 0 ) {
         $stsSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) SELECT segmentid, segstatus, statusby, statusdate, now(), concat(:user,'/CHECKIN-PROCESS') as updater FROM masterrecord.ut_procure_segment where segmentid = :segid";
         $locSQL = "insert into masterrecord.history_procure_segment_inventory(segmentid, bgs, scannedlocation, scannedinventorycode, inventoryscanstatus, scannedby, scannedon, historyon, historyby) values(:segmentid, :bgs, :scannedlocation, :scannedinventorycode, :inventoryscanstatus, :scannedby, now(), now(), :historyby)";
         $stsUpdSQL = "update masterrecord.ut_procure_segment set segstatus = :newStatus, statusdate = now(), statusby = :user, scannedLocation = :scndesc, scanloccode = :scncode, scannedstatus = 'CHECK IN TO INVENTORY', scannedby = :usr, internalcomments = concat(ifnull(internalcomments,''),' ','OVERRIDE CHECKIN SCREEN'), scanneddate = now() where segmentid = :segid";
         foreach ($pdta as $key => $val) {
           if ($key !== 'userid' && $key !== 'devReason') { 
             //WRITE SEGMENT HISTORY FILES
             $stsRS = $conn->prepare($stsSQL); 
             $stsRS->execute(array(':segid' => $key, ':user' => $u['originalaccountname']));
             //TODO:  MAKE DYNAMIC WITH WEBSERVICE //OR = Over Ride
             $stsLookupSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'segmentstatus' and dspvalue = :dspStatus";
             $stsLookupRS = $conn->prepare($stsLookupSQL); 
             $stsLookupRS->execute(array(':dspStatus' => $val['statusupdate']));
             if ($stsLookupRS->rowCount() === 1) {
                 $srs = $stsLookupRS->fetch(PDO::FETCH_ASSOC); 
                 $nwSts = $srs['menuvalue']; 
             } else { 
                 $nwSts = 'BANKED';
             }
             $scnRS = $conn->prepare($locSQL);
             $updRS = $conn->prepare($stsUpdSQL);  
             //CHANGE STATUS
             if ( trim($val['loccode']) !== "" ) { 
               $scnRS->execute(array(':segmentid' => $key , ':bgs' => $val['bgs'], ':scannedlocation' => $val['locdesc'], ':scannedinventorycode' => $val['loccode'], ':inventoryscanstatus' => 'CHECK IN TO INVENTORY', ':scannedby' => $u['originalaccountname'], ':historyby' => 'CHECK-IN OVERRIDE COORDINATOR SCREEN')); 
               $updRS->execute(array(':segid' => $key,':newStatus' => $nwSts,':user' => $u['originalaccountname'],':scndesc' => $val['locdesc'], ':scncode' => $val['loccode'], ':usr' => $u['originalaccountname'] ));
             } else { 
               $scnRS->execute(array(':segmentid' => $key , ':bgs' => $val['bgs'], ':scannedlocation' => 'OVERRIDE CHECKIN PROCESS'  , ':scannedinventorycode' => 'ORCHECKIN', ':inventoryscanstatus' => 'CHECK IN TO INVENTORY', ':scannedby' => $u['originalaccountname'], ':historyby' => 'CHECK-IN OVERRIDE COORDINATOR SCREEN'));
               $updRS->execute(array(':segid' => $key,':newStatus' => $nwSts,':user' => $u['originalaccountname'],':scndesc' => 'OVERRIDE CHECKIN PROCESS', ':scncode' => 'ORCHECKIN', ':usr' => $u['originalaccountname']));
             }
             $responseCode = 200;
           }
         }      
       } 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }          

    function  labelprintrequest ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);

      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } else {        
          
          $chkUSQL = "SELECT originalaccountname FROM four.sys_userbase where allowind = 1 and timestampdiff(day, now(), passwordExpireDate) > 0 and sessionid = :sess"; 
          $chkURS = $conn->prepare($chkUSQL); 
          $chkURS->execute(array(':sess' => $authuser));
          if ($chkURS->rowCount() === 1) { 
              $uRS = $chkURS->fetch(PDO::FETCH_ASSOC);
              $usr = $uRS['originalaccountname'];
          } else { 
              (list( $errorInd, $msgArr[] ) = array(1 , "USER NOT ALLOWED FUNCTION"));
          }
          ( !array_key_exists("payload", $pdta) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "PAYLOAD KEY IS MISSING")) : "";
          ( !array_key_exists("formatname", $pdta) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "LABEL FORMAT NAME IS MISSING")) : "";
          ( !array_key_exists("qty", $pdta) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE QTY KEY IS MISSING")) : "";
          if (array_key_exists("payload", $pdta)) {
            $pl = json_decode($pdta['payload'], true);  
             if ( count($pl) < 1 ) { 
               (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT PRINT PAYLOAD IS EMPTY" ));    
             }
          }
          ( trim($pdta['formatname']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE FORMAT NAME IS NOT SPECIFIED" )) : "";   
          ( trim($pdta['qty']) === "" || !is_numeric($pdta['qty'])  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE QUANTITY FIELD MUST NOT BE EMPTY AND MUST BE A NUMBER" )) : "";   
          $chkFrmSQL = "SELECT  linuxlprname, formatname, formattext FROM serverControls.lblFormats where dspPrinter = 1 and formatname = :formatname";
          $chkFrmRS = $conn->prepare($chkFrmSQL);
          $chkFrmRS->execute(array(':formatname' => $pdta['formatname']));
           if ( $chkFrmRS->rowCount() <> 1) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "THE SPECIFIED FORMAT WAS NOT FOUND" ));    
           } else { 
             $fmt = $chkFrmRS->fetch(PDO::FETCH_ASSOC);
             $formattext = $fmt['formattext'];
             $printer = $fmt['linuxlprname'];
             $labelname = $fmt['formatname'];
             preg_match_all('/#FIELD\d{1,}#/', $formattext, $mtcharr, PREG_OFFSET_CAPTURE);                   
           }
      }
      
      if ($errorInd === 0 ) {
        foreach ($pl as $skey => $sval ) { 
          //$pattern = preg_quote('#$%^&*()+=-[]\';,./{}|\":<>?~', '#');
          $label = preg_replace( '/[^A-Za-z0-9]/', '', $sval );    
          $elementArr = array();
          for ($i = 0; $i < count($mtcharr[0]); $i++) { 
            $elementArr[strtoupper(str_replace('#','',$mtcharr[0][$i][0]))] = $label;
          }
          $insSQL = "insert into serverControls.lblToPrint (labelRequested, printerRequested, dataStringpayload, byWho, onWhen)  values(:labelrequested, :printerrequested,:datastringpayload, :bywho, now())";
          $insRS = $conn->prepare($insSQL);
          $insRS->execute(array(':labelrequested' => $labelname, ':printerrequested' => $printer,':datastringpayload' => json_encode($elementArr), ':bywho' => $usr));
        }
        $responseCode = 200;
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;   
    }

    function segmentmasterrecord ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);

      $segmentSQL = "SELECT sg.biosamplelabel,  sg.segmentid, ifnull(sg.segmentlabel,'') as segmentlabel, ifnull(sg.bgs,'') as bgs, ifnull(sg.segstatus,'') as segstatus, ifnull(date_format(sg.statusdate,'%m/%d/%Y'),'') as statusdate, ifnull(sg.statusby,'') as statusby, ifnull(sg.shipdocrefid,'') as shipdocrefid, ifnull(date_format(sg.shippedDate,'%m/%d/%Y'),'') as shippeddate, ifnull(sg.hourspost,'') as hourspost, ifnull(sg.metric,'') as metric, ifnull(muom.dspvalue,'') as metricuom, ifnull(muom.longvalue,'') as metricuomlong, ifnull(sg.prepmethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation, ifnull(sg.qty,1) as qty, ifnull(sg.assignedto,'') as assignedto, ifnull(sg.assignedReq,'') as assignedrequest, ifnull(sg.assignedby,'') as assignedby , ifnull(date_format(sg.assignmentdate,'%m/%d/%Y'),'') as assignmentdate, ifnull(sg.hprblockind,0) as hprblockind , ifnull(date_format(sg.enteredon,'%m/%d/%Y'),'') as procurementdate, ifnull(sg.enteredby,'') as procurementtech, ifnull(sg.procuredat,'') as procuredat, ifnull(sg.segmentcomments,'') as segmentcomments, ifnull(sg.voidind,0) as voidind , ifnull(sg.segmentvoidreason,'') as segmentvoidreason, ifnull(sg.scannedlocation,'') as scannedlocation, ifnull(sg.scanloccode,'') as scanloccode, ifnull(sg.scannedstatus,'') as scannedstatus, ifnull(sg.scannedby,'') as scannedby, ifnull(date_format(sg.scanneddate,'%m/%d/%Y'),'') as scanneddate, ifnull(sg.tohpr,0) as tohprind , ifnull(sg.reconcilInd,'') as reconcilind, ifnull(sg.reconcilBy,'') as reconcilby, ifnull(date_format(sg.reconcilOn,'%m/%d/%Y'),'') as reconcilon FROM masterrecord.ut_procure_segment sg left join (SELECT menu, menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') as muom on sg.metricuom = muom.menuvalue where segmentid = :segmentid";
      $segmentRS = $conn->prepare($segmentSQL); 
      $segmentRS->execute(array(':segmentid' => $pdta['segmentid']));
      if ($segmentRS->rowCount() === 1) { 
        //GET SEGDATA
        $dta = $segmentRS->fetch(PDO::FETCH_ASSOC);
      } else { 
        //BAD DATA
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    }

    function generatesystemreportrequest($request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);

      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } else {
  
        //GET USER ALLOWANCE  
        $usrSQL = "SELECT originalaccountname, emailaddress, allowind, allowproc, allowcoord, allowhpr, allowinvtry, allowfinancials, presentinstitution, timestampdiff(day,now(),passwordexpiredate) as daystilexpire, accesslevel, accessnbr FROM four.sys_userbase where sessionid = :sess and allowind = 1 and timestampdiff(day,now(),passwordexpiredate) > 0 ";
        $usrR = $conn->prepare($usrSQL); 
        $usrR->execute(array(':sess' => $authuser)); 
        if ($usrR->rowCount() < 1) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "USER NOT ALLOWED"));
        } else { 
            //GET USER
            $u = $usrR->fetch(PDO::FETCH_ASSOC);
            $pdta['user'][] = $u;
        }

        //GET REPORT PARAMETERS AND FILL THEM IN 
        $rptsqlSQL = "SELECT ifnull(selectClause,'') as selectclause, ifnull(fromClause,'') as fromclause, ifnull(whereclause,'') as whereclause, ifnull(summaryfield,'') as summaryfield, ifnull(groupbyClause,'') as groupbyclause, ifnull(orderbyClause,'') as orderby, ifnull(accesslvl,100) as accesslevel, ifnull(allowpdf,0) as allowpdf FROM four.ut_reportlist where urlpath = :rpturl";
      $rptsqlRS = $conn->prepare($rptsqlSQL); 
      $rptsqlRS->execute(array(':rpturl' =>  $pdta['rptRequested'] )); 
     
        if ( $rptsqlRS->rowCount() <> 1) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "REPORT DEFINITION NOT FOUND"));
        } else {
          $rptsql = $rptsqlRS->fetch(PDO::FETCH_ASSOC);
          $pdta['request']['rptsql'] = $rptsql;
        }
      }

      
      $msgArr[] = $pdta['rptRequested'];
      if ($errorInd === 0 ) { 
        //BUILD REPORT REQUEST with name and user AND GET REPORT ENCRY CODE HERE
        $objid = strtolower( generateRandomString() ); 
        $insSQL = "insert into four.objsrchdocument (objid, bywho, onwhen, doctype, reportmodule, reportname, requestjson, typeofreportrequested) value (:objid,:whoby,now(),:doctype,:reportmodule,:reportname,:requestjson,:typeofreportrequested)";
        $insR = $conn->prepare($insSQL);
        $insR->execute(array(':objid' => $objid,':whoby' => $u['originalaccountname'], ':doctype' => 'REPORTREQUEST', ':reportmodule' => 'system-reports', ':reportname' => $pdta['rptRequested'],':requestjson' => json_encode($pdta),':typeofreportrequested'=> 'PDF'));
        $dta['reportobject'] = $objid;
        $dta['reportobjectency'] = cryptservice($objid, 'e');
        $dta['typerequested'] = 'PDF';
        $dta['reportmodule'] = 'system-reports';
        $dta['reportname'] = $pdta['rptRequested'];
        $itemsfound = 1;
        $responseCode = 200;                      
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    }

    function frontsscalendar( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode(func_get_arg(1), true);

      //TODO:  CHECK ALL DATA ELEMENTS ARE CORRECT AND CORRECT TYPE (ei. Month/Year values)
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      ( $authuser !== $authchk ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member.")) : "";
       
      $dte = explode("/",$pdta['monthyear']); 
      $u = userDetailsCal($authuser);
      $dta = buildcalendar($pdta['whichcalendar'], $dte[0], $dte[1], $u[0]['friendlyname'], $u[0]['emailaddress'], $authuser);  

      if ( trim($dta) !== "" ) { 
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    } 

    function markbgmigration ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      
      //DATA CHEKCS
      ( !array_key_exists( "bgselector", $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Data Element (BG selector) is missing.")) : "";
      ( trim($pdta['bgselector']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "BG SELECTOR is a required field")) : "";
      $chkSQL = "SELECT pbiosample, fromlocation FROM four.ut_procure_biosample where selector = :selector and voidind = 0 and (ifnull(migrated,0) <> 100 and ifnull(migrated,0) <> 2)  and timestampdiff(hour, inputon, now()) < 12 and recordStatus = 2";
      $chkRS = $conn->prepare($chkSQL); 
      $chkRS->execute(array(':selector' => trim($pdta['bgselector'])));
      ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "No Biogroup found matching request.  Either the biogroup doesn't exist, is locked, is already voided or has been migrated to master-record")) : "";
      $chk = $chkRS->fetch(PDO::FETCH_ASSOC); 
      $bg = $chk['pbiosample']; 
      $frm = $chk['fromlocation'];
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      ( $authuser !== $authchk ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member.")) : "";
      $chkUSQL = "SELECT ifnull(allowproc,0) as allowproc, ifnull(allowind,0) as allowind, ifnull(presentinstitution,'') as presentinstitution, ifnull(originalaccountname,'') as originalaccountname, ifnull(emailaddress,'') as usr FROM four.sys_userbase where sessionid = :usrCode and timestampdiff(hour, now(), sessionExpire) > 0 and timestampdiff(day, now(), passwordExpireDate) > 0";
      $chkURS = $conn->prepare($chkUSQL); 
      $chkURS->execute(array(':usrCode' => $authuser));
      ( $chkURS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed this function:  Either the user doesn't exist, has expired, or isn't allowed this function ")) : "";
      $usr = $chkURS->fetch(PDO::FETCH_ASSOC);
      ( (int)$usr['allowproc'] !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( (int)$usr['allowind'] !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['originalaccountname'] === ""  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['presentinstitution'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['presentinstitution'] !== $frm ) ? (list( $errorInd, $msgArr[] ) = array(1 , "You may not migrate a sample that is outside your present institution")) : "";      
      //END DATA CHECKS
      
      if ( $errorInd === 0 ) { 
          $updSQL = "update four.ut_procure_biosample set migrated = 2 where  selector = :selector"; 
          $updRS = $conn->prepare($updSQL); 
          $updRS->execute(array(':selector' => $pdta['bgselector']));
          
          //TODO: CHECK THE VALIDITY OF THE UPDATE
          $responseCode = 200;
      }
      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    }
    
    function bgchecksbeforevoid ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 200;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $bgselector = cryptservice($pdta['bgency'],'d',false);
      $chkSQL = "SELECT pbiosample, fromlocation FROM four.ut_procure_biosample where selector = :selector and recordstatus = 2 and ifnull(voidind,0) = 0 and ifnull(migrated,0) = 0 and timestampdiff(hour, inputon, now()) < 12";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':selector' => $bgselector));
      if ( $chkRS->rowCount() < 1) { 
          $responseCode = 404;
          $msgArr = "404";
      } else { 
          $bg = $chkRS->fetch(PDO::FETCH_ASSOC);
          $dta = $bg;
          $responseCode = 200;
      } 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
    }

    function searchvocabularyterms ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 200;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      //TODO:  DO I NEED TO CHECK THE USER HERE OR IS THIS LOW LEVEL ENOUGH TO LET IT RUN FREE 
      $pdta = json_decode($passdata, true);
      if ( trim($pdta['srchterm']) === "") { 
        $dta = "<table><tr><td><h3>Search Term Missing</h3></td></tr></table>";
      } else { 
          $rtnArr = searchVocabByTerm( trim($pdta['srchterm']) );
          if ( count($rtnArr) > 0 ) {


              $dta = "<table border=0 cellspacing=0 cellpadding=0 id=vocabularyDisplayTable><tr><td class=\"headercell \">Specimen<br>Category</td><td class=\"headercell \">Site</td><td class=\"headercell \">Sub-Site</td><td class=\"headercell \">Diagnosis \ Modifier </td></tr>"; 
              foreach ( $rtnArr as $ky => $vl) {
                $dx = explode(" \ ", $vl['diagnosis'] );
                $dta .= "<tr><td class=\"datacell vocDspSpeccat\" valign=top>" . strtoupper($vl['specimencategory']) . "</td><td class=\"datacell vocDspSite\" valign=top>" . strtoupper($vl['site']) . "</td><td class=\"datacell vocDspSSite\" valign=top>" . strtoupper($vl['subsite']) . "</td><td class=\"datacell\" valign=top>" . strtoupper($vl['diagnosis']) . "</td></tr>";    
              }     
              $dta .= "</table>";


          } else { 
            $dta = "<table><tr><td><h3>No Vocabulary Terms Found Matching \"" . trim($pdta['srchterm']) . "\"</h3></td></tr></table>";
          }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
    }

    function financialcreditcardpaymentdetail ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = $pdta['sessionid'];
      $user = $pdta['user'];
      $ency = $pdta['encycode'];

      //CHECK USER RIGHTS
      $chkSQL = "SELECT friendlyName FROM four.sys_userbase where sessionid = :sess and emailAddress = :usr and allowfinancials = 1 and allowind = 1 and timestampdiff(day, now(), passwordExpireDate ) > 0 ";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':sess' => $sess, ':usr' => $user));
      if ( $chkRS->rowCount() < 1 ) { 
          //USER NOT ALLOWED
        $msgArr[] = "NOTALLOWED";
      } else { 
        $msgArr[] = "GOOD";
        $refid =  $ency;
        $sql = "SELECT transaction_uuid, transaction_type, signed_date_time, bill_to_investigator, bill_to_forename, bill_to_surname, bill_to_company_name, bill_to_address_line1, bill_to_address_line2, bill_to_address_city, bill_to_address_state, bill_to_address_postal_code, bill_to_address_country, bill_to_phone, bill_to_email, pay_invoices, amount, decision, auth_code, reason_code, req_card_type, auth_time, message, auth_trans_ref_no FROM webcapture.web_PayCapture where concat(webPageId, reference_number) = :refnbr";
        $rs = $conn->prepare($sql); 
        $rs->execute(array(':refnbr' => $refid));
        while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
          $dta[] = $r;
        }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
    }    

    function financialcreditcardpayments ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = $pdta['sessionid'];
      $user = $pdta['user'];
      $nbrODays = $pdta['nbrofdays'];

      //CHECK USER RIGHTS
      $chkSQL = "SELECT friendlyName FROM four.sys_userbase where sessionid = :sess and emailAddress = :usr and allowfinancials = 1 and allowind = 1 and timestampdiff(day, now(), passwordExpireDate ) > 0 ";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':sess' => $sess, ':usr' => $user));
      if ( $chkRS->rowCount() < 1 ) { 
          //USER NOT ALLOWED
        $msgArr[] = "NOTALLOWED";
      } else { 
        $msgArr[] = "GOOD";
        $lastDatesSQL = "SELECT distinct date_format(str_to_date(signed_date_time,'%Y-%m-%d'),'%Y-%m-%d') ondte FROM webcapture.web_PayCapture order by ondte desc limit :nbrOfDays";
        $lastDatesRS = $conn->prepare($lastDatesSQL); 
        $lastDatesRS->execute(array(':nbrOfDays' => (int)$nbrODays));
        $cnt = 0;
        while ($d = $lastDatesRS->fetch(PDO::FETCH_ASSOC)) { 
          $dta[$cnt]['transDate'] = $d['ondte'];

          $detSQL = "SELECT concat(webpageid, reference_number) as reference_number, decision, if(trim(ifnull(bill_to_investigator,''))='',trim(ifnull(bill_to_surname,'')),trim(ifnull(bill_to_investigator,''))) as billto, pay_invoices, amount FROM webcapture.web_PayCapture where str_to_date(signed_date_time,'%Y-%m-%d') = :dateString order by reference_number";
          $detR = $conn->prepare($detSQL);
          $detR->execute(array(':dateString' => $d['ondte']));
          $dta[$cnt]['countrecords'] = $detR->rowCount();
          $detArr = array();
          while ($r = $detR->fetch(PDO::FETCH_ASSOC)) { 
            $detArr[] = $r;
          }
          $dta[$cnt]['detaillines'] = $detArr;
          $cnt++;
        }

      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
    }    
    
    function collectiongridresultstbl ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      
      $cgriddta = self::collectiongridresults( $request, $passdata);
      if ( (int)$cgriddta['statusCode'] === 200 ) { 
          //BUILD TABLE
          $cgrid = json_decode($cgriddta['data']['DATA'], true); 
       $cntr = 0;
       $rowControl = 0;
       foreach ($cgrid as $ky => $vl) {
         $tchn = ( trim($vl['technician']) === "" ) ? "" : " / {$vl['technician']}";
         $inst = ( trim($vl['dspinstitution']) === "") ? "" : "{$vl['dspinstitution']} ";
         $lock = ( (int)$vl['migratedind'] === 0 && $vl['linkage'] !== "" ) ? "lock_open" : "lock";
         $void = ( (int)$vl['voidind'] === 1) ? " strthru" : "";
         $selector = ( trim($vl['selector']) === "" ) ? "" : " onclick = \"navigateSite('procure-biosample/" . cryptservice($vl['selector'],'e') . "');\" ";
         $sbj = ( trim($vl['subjectnumber']) === "" ) ? "" : "{$vl['subjectnumber']} / ";
         $proc = ( trim($vl['proctype']) === "") ? "" : "{$vl['proctype']} /";
         $coltype = ( trim($vl['collecttype']) === "" ) ? "" : "{$vl['collecttype']}";
        $pr = ( trim($vl['prpt']) === "") ? "" : "{$vl['prpt']} /";
        $rc = ( trim($vl['pxirace']) === "") ? "" : " / {$vl['pxirace']}";
        $sx = ( trim($vl['pxisex']) === "") ? "" : " / {$vl['pxisex']}";        
      $segTable = "";
        if ( count($vl['segmentlist']) === 0) { 
        } else { 
          $segTable = "<table border=0 cellspacing=0 class=segmentHolderTbl><tr><td colspan=10 class=segmentHeader>SEGMENTS</td></tr><tr><td class=segLbl>Segment Label</td><td class=segLbl>HPR</td><td class=segLbl>QTY</td><td class=segLbl>Preparation</td><td class=segLbl>Container</td><td class=segLbl>Hours Post</td><td class=segLbl>Metric</td><td class=segLbl>Cut From</td><td class=segLbl>Assignment</td><td class=segLbl>Collection Time</td></tr>";
          foreach ($vl['segmentlist'] as $sgky => $sgvl) { 
            $prpd = ( trim($sgvl['prpdetail']) === "" ) ? "" : " / {$sgvl['prpdetail']}"; 
            $void = ( (int)$sgvl['voidind'] === 1) ? " strthru" : "";
            $hpr = ( (int)$sgvl['hprind'] === 1) ? "Y" : "N";
            $colmet = ( trim($sgvl['proctime']) === "") ? "" : "{$sgvl['proctime']}";
            $ass = ( strtolower(substr(trim($sgvl['assigninvestid']),0,3)) === "inv") ?  "{$sgvl['assigndspname']} ({$sgvl['assigninvestid']} / {$sgvl['assignrequestid']})" : strtoupper(trim($sgvl['assigninvestid']));
            $segTable .= <<<SGTBL
                   <tr>
                       <td class="cgsgelem_label {$void}">{$sgvl['pbiosample']}T{$sgvl['segdsplbl']}</td>
                       <td class="cgsgelem_hpr {$void}">{$hpr}</td>
                       <td class="cgsgelem_qty {$void}">{$sgvl['dspqty']}</td>
                       <td class="cgsgelem_prp {$void}">{$sgvl['prepmethod']}{$prpd}</td> 
                       <td class="cgsgelem_con {$void}">{$sgvl['container']}</td> 
                       <td class="cgsgelem_hp {$void}">{$sgvl['hrpost']}</td> 
                       <td class="cgsgelem_met {$void}">{$sgvl['metric']}{$sgvl['shortuom']}</td>                        
                       <td class="cgsgelem_from {$void}">{$sgvl['cutfromblockid']}</td> 
                       <td class="cgsgelem_ass {$void}">{$ass}</td>
                       <td class="cgsgelem_tme cgsgendcap {$void}">{$colmet}</td>
                  </tr>
SGTBL;
          }
          $segTable .= "</table>";
        }
        $rowC = ( $rowControl === 0 ) ?  "rowColorA" : "rowColorB";
        
         $inner .= <<<BSLINE
<tr class="displayRows" {$selector} >
  <td class="lockdsp topper {$rowC}"><i class="material-icons">{$lock}</i></td>
  <td class="cgelem_bgnbr{$void} topper {$rowC}">{$vl['pbiosample']}&nbsp;</td>
  <td class="cgelem_instTmeTech{$void} topper {$rowC}">{$vl['timeprocured']}{$tchn}<br>{$inst}&nbsp;</td>
  <td class="cgelem_proccoltype{$void} topper {$rowC}">{$proc}<br>{$coltype}&nbsp;</td>
  <td class="cgelem_spcat{$void} topper {$rowC}">{$vl['specimencategory']}&nbsp;</td>
  <td class="cgelem_site{$void} topper {$rowC}">{$vl['site']}&nbsp;</td>
  <td class="cgelem_dx{$void} topper {$rowC}">{$vl['diagnosis']}&nbsp;</td>
  <td class="cgelem_unk{$void} topper {$rowC}">{$vl['unknownmet']}&nbsp;</td>
  <td class="cgelem_metsf{$void} topper {$rowC}">{$vl['metsdx']}&nbsp;</td>
  <td class="cgelem_metric{$void} topper {$rowC}">{$vl['metuom']}&nbsp;</td>
  <td class="cgelem_prpt{$void} topper {$rowC}">{$pr}<br>{$vl['informedconsent']}&nbsp;</td>
  <td class="cgelem_sbjt{$void} topper {$rowC}">{$sbj}<br>{$vl['protocolnumber']}&nbsp;</td>
  <td class="cgelem_age{$void} topper {$rowC} endcell">{$vl['pxiage']}  {$rc} {$sx}&nbsp;</td>
</tr>
<tr><td colspan=15 class="{$rowC}">
  <table border=0 cellspacing=0 >
  <tr>
      <td style="width: 3vw;">&nbsp;</td>
      <td>{$segTable}</td>          
   </tr></table>
</td></tr>
BSLINE;
         $cntr++;
         if ($rowControl === 0) { 
             $rowControl = 1; 
         } else { 
             $rowControl = 0;
         }
       }
         
       $dta = <<<BSSEGTBL
<table border=0 cellspacing=0 id=collectionGridDspTbl>
<tr><td colspan=15 align=right>Total Biogroups: {$cntr} </td></tr>
<tr>
<td class="datalbl">&nbsp;</td>
  <td class="datalbl cgelem_bgnbr " valign=top>Biogroup #</td>
  <td class="datalbl cgelem_instTmeTech " valign=top>Collection Time / Technician<br>Institution</td>
  <td class="datalbl cgelem_proccoltype " valign=top>Procedure /<br>Collection Type</td>
  <td class="datalbl cglem_spcat">Specimen Category</td>
  <td class="datalbl cglem_site">Site (Sub-Site) / Position</td>
  <td class="datalbl cglem_dx">Diagnosis (Modifier)</td>
  <td class="datalbl cglem_unk">Unknown MET/NAT</td>
  <td class="datalbl cglem_metsf">METS From</td>
  <td class="datalbl cgelem_metric " valign=top>Initial Metric</td>
  <td class="datalbl cgelem_prpt " valign=top>Path Rpt /<br>Consent</td>
  <td class="datalbl cgelem_sbjt " valign=top>Subject # /<br>Protocol #</td>
  <td class="datalbl cgelem_age endcell" valign=top>Donor Age / Race / Sex</td>
</tr>
{$inner}
</table>
BSSEGTBL;
        $responseCode = 200;      
      } else { 
        //BUILD NO FIND TABLE
          $dta = "<table><tr><td><h3>No Procurement Records Match The Entered Criteria</h3></td></tr></table>";
          $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
    }
    
    function collectiongridresults( $request, $passdata ) {
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");

      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $allowData = 0;
      $pdta = json_decode($passdata, true);
      if ($authuser !== "chtneast" ) { 

          
          if ( array_key_exists('usrsession', $pdta) ) {
            //$allowData = 1;              
           //CHECK USER
            $authchk = cryptservice($authpw,'d', true, $authuser);
            $allowData = ( $authuser !== $authchk ) ? 0 : 1; 
            $allowData = ($authuser !== $pdta['usrsession']) ? 0 : 1;   
            $getUsrSQL = "SELECT presentinstitution FROM four.sys_userbase where sessionid = :sess and allowInd = 1 and allowproc = 1 and TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";                
            $getUsrRS = $conn->prepare($getUsrSQL);
            $getUsrRS->execute(array(':sess' => $pdta['usrsession']));
            if ($getUsrRS->rowCount() === 1) { 
              //CHECK PRESENT LOC
              $getUsr = $getUsrRS->fetch(PDO::FETCH_ASSOC);
              if ($getUsr['presentinstitution'] !== $pdta['presentinstitution']) { 
                $allowData = 0;
              }
            } else { 
              $allowData = 0;
            }            
          } else { 
            $allowData = 0;
          } 
        
        
        } else { 
          if ($authpw === serverpw) { 
            $allowData = 1;
            if ( array_key_exists('usrsession', $pdta) ) {                            
                $getUsrSQL = "SELECT presentinstitution FROM four.sys_userbase where sessionid = :sess and allowInd = 1 and allowproc = 1 and TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";                
                $getUsrRS = $conn->prepare($getUsrSQL);
                $getUsrRS->execute(array(':sess' => $pdta['usrsession']));
                if ($getUsrRS->rowCount() === 1) { 
                    //CHECK PRESENT LOC
                    $getUsr = $getUsrRS->fetch(PDO::FETCH_ASSOC);
                    if ($getUsr['presentinstitution'] !== $pdta['presentinstitution']) { 
                      $allowData = 0;
                    }
                } else { 
                    $allowData = 0;
                }
            } else { 
                 $allowData = 0;
            }
          }
      }
      
      //TODO: CHECK DATE FORMAT AND VALID 
      
      if ($allowData === 1) {
          
          //$dta = $pdta['requesteddate'];
          $sql = getLongSQLStmts('cgridmain');
           $rs = $conn->prepare($sql);
           $rs->execute(array(':procdate' => $pdta['requesteddate'], ':procloc' => $pdta['presentinstitution'], ':procdateremote' => $pdta['requesteddate'], ':proclocremote' => $pdta['presentinstitution'])); 
           if ( $rs->rowCount() > 0 ) {
             $cntr = 0; 
             while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
               $dta[$cntr]['pbiosample'] = $r['pbiosampledspnbr'];
               $dta[$cntr]['institution'] = $r['procuringinstitution'];
               $dta[$cntr]['dspinstitution'] = $r['dspinstitution'];
               $dta[$cntr]['selector'] = $r['selector'];
               $dta[$cntr]['linkage'] = $r['pbiosamplelink'];
               $dta[$cntr]['timeprocured'] = $r['timeprocured'];
               $dta[$cntr]['technician'] = $r['technician'];
               $dta[$cntr]['migratedind'] = $r['migrated'];
               $dta[$cntr]['migratedon'] = $r['migratedon'];
               $dta[$cntr]['proctype'] = $r['proctype'];
               $dta[$cntr]['collecttype'] = $r['collecttype'];
               $dta[$cntr]['metuom'] = $r['metuom'];
               $dta[$cntr]['prpt'] = $r['pathologyrpt'];
               $dta[$cntr]['pxiage'] = $r['pxiage'];
               $dta[$cntr]['pxirace'] = $r['pxirace'];
               $dta[$cntr]['pxisex'] = $r['pxisex'];
               $dta[$cntr]['subjectnumber'] = $r['subjectnumber'];
               $dta[$cntr]['protocolnumber'] = $r['protocolnumber'];
               $dta[$cntr]['informedconsent'] = $r['informedconsent'];
               $dta[$cntr]['specimencategory'] = $r['specimencategory'];
               $dta[$cntr]['site'] = $r['asite'];
               $dta[$cntr]['diagnosis'] = $r['diagnosismodifier'];
               $dta[$cntr]['metsdx'] = $r['metsdx'];
               $dta[$cntr]['unknownmet'] = $r['unknownmet'];
               $dta[$cntr]['bscomment'] = $r['bscomment'];
               $dta[$cntr]['hprcomment'] = $r['hprcomment'];
               $dta[$cntr]['voidind'] = $r['voidind'];
               $dta[$cntr]['voidreason'] = $r['voidreason'];

               $segArr = array();
               if (trim($r['pbiosamplelink']) !== "") { 
                 //GET SEGMENTS
                   $segListSQL = "SELECT sg.pbiosample, min(sg.seglabel) as minlbl, if(min(sg.seglabel) = max(sg.seglabel), min(sg.segLabel), concat(min(sg.seglabel),'-',max(sg.seglabel))) as segdsplbl, sum(sg.qty) as dspqty, sg.prp, sg.prpmet, sg.groupingid , ifnull(sg.hrpost,0) as hrpost, ifnull(sg.metric,'') as metric, if(ifnull(sg.metric,'') = '','',ifnull(uom.dspvalue,'')) as shortuom, if(ifnull(sg.metric,0) = '','',ifnull(uom.longvalue,'')) as longuom , ifnull(prpm.dspvalue,'') as prepmethod, ifnull(prpd.longvalue,'') as prpdetail, ifnull(sg.prpcontainer,'') as containercode, ifnull(pcnt.longvalue,'') as container, ifnull(sg.cutfromblockid,'') as cutfromblockid, ifnull(sg.hprind,0) as hprind, ifnull(sg.procuredAt,'') as procuredat, ifnull(sg.procuredby,'') as procuredby , ifnull(sg.dspname,'') as assigndspname, ifnull(sg.investid,'') as assigninvestid, ifnull(sg.requestid,'') as assignrequestid, ifnull(sg.voidind,0) as voidind, ifnull(sg.voidreason,'') as voidreason, ifnull(date_format(sg.inputOn, '%H:%i (%m/%d/%Y)'),'') as proctime, sgcomments FROM four.ut_procure_segment sg left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') uom on sg.metricuom = uom.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPMETHOD') prpm on sg.prp = prpm.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPDETAIL') prpd on sg.prpMet = prpd.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'CONTAINER') pcnt on sg.prpcontainer = pcnt.menuvalue where sg.pbiosample = :bsgroup and sg.activeind = 1 group by sg.pbiosample, sg.prp, sg.prpmet, sg.groupingid , hrpost, metric, shortuom, longuom, prepmethod, prpdetail, containercode, container, cutfromblockid, hprind, procuredat, procuredby, assigndspname, assigninvestid, assignrequestid, voidind, voidreason, proctime, sgcomments order by minlbl";
                   $sgRS = $conn->prepare($segListSQL); 
                   $sgRS->execute(array(':bsgroup' => $r['pbiosamplelink']));
                   while ($s = $sgRS->fetch(PDO::FETCH_ASSOC)) { 
                     $segArr[] = $s;  
                   }
               }
               $dta[$cntr]['segmentlist'] = $segArr;
               $cntr++;
             }
             $responseCode = 200;
           } else { 
             $responseCode = 404;
           }
  
          
      }  else { 
                   
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => json_encode($dta));
      return $rows;                                
    }

    function voidbg ( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode( $passdata, true );

      //DATA CHECKS 
      ( !array_key_exists( "bgency", $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Data Element (BG Encryption) is missing.")) : "";
      ( trim($pdta['bgency']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "BGENCY is a required field")) : "";
      //CHECK BG 
      $bgselect = cryptservice($pdta['bgency'],'d',false);
      $chkSQL = "SELECT pbiosample, fromlocation FROM four.ut_procure_biosample where selector = :selector and voidind = 0 and ifnull(migrated,0) <> 100 and timestampdiff(hour, inputon, now()) < 12 and recordStatus = 2";
      $chkRS = $conn->prepare($chkSQL); 
      $chkRS->execute(array(':selector' => $bgselect));
      ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "No Biogroup found matching request.  Either the biogroup doesn't exist, is locked, is already voided or has been migrated to master-record")) : "";
      $chk = $chkRS->fetch(PDO::FETCH_ASSOC); 
      $bg = $chk['pbiosample']; 
      $frm = $chk['fromlocation'];
      ( !array_key_exists( "reasoncode", $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Data Element (ReasonCode) is missing.")) : "";
      ( trim($pdta['reasoncode']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "You must provide a reason for voiding this biogroup")) : "";
      ( !array_key_exists( "reason", $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Data Element (Reason) is missing.")) : "";
      ( !array_key_exists( "reasonnotes", $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Data Element (ReasonNotes) is missing.")) : "";
      //CHECK USER
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      ( $authuser !== $authchk ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member.")) : "";
      $chkUSQL = "SELECT ifnull(allowproc,0) as allowproc, ifnull(allowind,0) as allowind, ifnull(presentinstitution,'') as presentinstitution, ifnull(originalaccountname,'') as originalaccountname, ifnull(emailaddress,'') as usr FROM four.sys_userbase where sessionid = :usrCode and timestampdiff(hour, now(), sessionExpire) > 0 and timestampdiff(day, now(), passwordExpireDate) > 0";
      $chkURS = $conn->prepare($chkUSQL); 
      $chkURS->execute(array(':usrCode' => $authuser));
      ( $chkURS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed this function:  Either the user doesn't exist, has expired, or isn't allowed this function ")) : "";
      $usr = $chkURS->fetch(PDO::FETCH_ASSOC);
      ( (int)$usr['allowproc'] !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( (int)$usr['allowind'] !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['originalaccountname'] === ""  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['presentinstitution'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "User not allowed function")) : "";
      ( $usr['presentinstitution'] !== $frm ) ? (list( $errorInd, $msgArr[] ) = array(1 , "You may not void a sample that is outside your present institution")) : "";
      //( 1 === 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "{$bg} {$frm}")) : "";
      //END DATA CHECKS

      if ( $errorInd === 0 ) { 
        $voidBGSQL = "update four.ut_procure_biosample set voidind = 1, voidcode = :voidcode, voidReason = :voidreason, voidby = :voidby, voidon = now(), voidtext = :voidtext  where selector = :selector";
        $voidBGRS = $conn->prepare($voidBGSQL);
        $voidBGRS->execute(array(':selector' => $bgselect, ':voidcode' => trim($pdta['reasoncode']), ':voidreason' => trim($pdta['reason']), ':voidby' => trim($usr['originalaccountname']), ':voidtext' => trim($pdta['reasonnotes']) ));
        $segVoidSQL = "update four.ut_procure_segment  set voidind = 1, voidreason =:segvoidreason where pbiosample = :bg";
        $segVoidRS = $conn->prepare($segVoidSQL);
        $segVoidRS->execute(array(':bg' => $bg, ':segvoidreason' => 'BIOGROUP VOIDED BY ' . $usr['originalaccountname']));

         $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                        
    } 

    function segmentcreatedefinedpieces( $request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sg = json_decode($passdata, true);
      $fldList = array(
          'SegmentBGSelectorId' => array('RQ','BG Encryption Holder')
          , 'AddHP' => array('RQ','Hours Post')
          ,'AddMetric' => array('RQ','Metric')
          ,'AddMetricUOMValue' => array( 'RQ', 'Metric (Unit of Measure)')
          ,'PreparationMethodValue' => array('RQ','Preparation Method')
          ,'PreparationMethod' => array('RQ','Preparation Method')
          ,'Preparation' => array('RQ','Preparation')
          ,'PreparationContainerValue' => array('','Container')
          ,'selectorAssignInv' => array('RQ','Assignment')
          ,'selectorAssignReq' => array('','Request #')
          ,'SGComments' => array('','Segment Comments')
          ); 
      //START DATA CHECKS

      //CHECK ALL FIELDS THAT SHOULD EXIST DO EXIST
      foreach($fldList as $k => $v) {
        if (!array_key_exists($k,$sg)) {          
          (list( $errorInd, $msgArr[] ) = array(1 , "The Field ({$k}) is missing from the data payload.  See a CHTNEastern Informatics Person."));  
        } else {

          if (trim($v[0]) === 'RQ') { 
            //CHECK THAT REQUIRED FIELDs HAVE VALUEs      
            if (trim($sg[$k]) === "") {
               if ( strtoupper( trim($sg['PreparationMethodValue']))  === 'SLIDE' && ($k ===  'AddMetric' || $k === 'AddMetricUOMValue') ) { 
               } else { 
                 (list( $errorInd, $msgArr[] ) = array(1 , "'{$v[1]}' IS REQUIRED.")); 
               }
            }
          }  
        }

      }


      ((( trim($sg['selectorAssignInv']) !== 'BANK' &&  trim($sg['selectorAssignInv']) !== 'QC' ) &&  trim($sg['selectorAssignInv']) !== '' ) && trim($sg['selectorAssignReq']) === '') ? (list( $errorInd, $msgArr[] ) = array(1 , "FOR ASSIGNED SEGMENTS, A REQUEST NUMBER MUST BE SPECIFIED."))  : "";
      //TODO:  GET DISPLAY NAME
      
      if (  ( trim($sg['selectorAssignInv']) !== 'BANK' && trim($sg['selectorAssignInv']) !== 'QC' ) &&  trim($sg['selectorAssignInv']) !== '') { 
          $invSQL = "SELECT concat(ifnull(invest_lname,''),', ',ifnull(invest_fname,'')) as dspname FROM vandyinvest.invest where investid = :invcode";
          $invRS = $conn->prepare($invSQL);
          $invRS->execute(array(':invcode' => trim($sg['selectorAssignInv'])));
          $invDspName = "";
          if ($invRS->rowCount() === 1) { 
              $inv = $invRS->fetch(PDO::FETCH_ASSOC); 
              $invDspName = $inv['dspname'];
          }
      }

      $bgid = cryptservice($sg['SegmentBGSelectorId'],'d',false);
      $lookupSQL = "SELECT bs.pbiosample, bsd.fromlocation as fromlocation FROM four.ut_procure_biosample bs left join (select * from four.ref_procureBiosample_details where activeind = 1) bsd on bs.pbiosample = bsd.pbiosample where ifnull(bs.migrated,0) = 0 and ifnull(bs.voidind,0) = 0  and  timestampdiff(DAY, bs.inputon, now()) = 0 and bs.selector = :selectorid";
      $lookupR = $conn->prepare($lookupSQL); 
      $lookupR->execute(array(':selectorid' => $bgid ));
      if ((int)$lookupR->rowCount() === 1) {
          $lookup = $lookupR->fetch(PDO::FETCH_ASSOC);
          // TODO: Make Sure Tech Has RIGHTS to procure at Institution and check PRCTechInstitute and PRCProcedureInstitutionValue match
          ( preg_match('/[^0-9\.]/', $sg['AddHP']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE HOURS POST MUST BE A NUMBER (DECIMAL)")) : "";
          ( preg_match('/[^0-9\.]/', $sg['AddMetric']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC NUMBER MUST BE A NUMBER (DECIMAL)")) : "";
          if ( trim($sg['AddMetricUOMValue']) !== "" ) {  
           $chkMetSQL = "SELECT * FROM four.sys_master_menus where menu = 'metric' and dspind = 1 and queriable = 1 and menuvalue = :metuom";
           $chkMetR = $conn->prepare($chkMetSQL); 
           $chkMetR->execute(array(':metuom' => trim($sg['AddMetricUOMValue'])));
           ( $chkMetR->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC UOM IS INVALID.  SEE CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
          }

        $authuser = $_SERVER['PHP_AUTH_USER']; 
        $authpw = $_SERVER['PHP_AUTH_PW'];
        $authchk = cryptservice($authpw,'d', true, $authuser);
        ( trim($authchk) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER AUTHENTICATION FAILED.  EITHER YOUR SESSION HAS EXPIRED OR YOU ARE NOT VALID")) : ""; 
        ( trim($authchk) !== trim($authuser) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER AUTHENTICATION FAILED.  EITHER YOUR SESSION HAS EXPIRED OR YOU ARE NOT VALID")) : ""; 
        $usr = userDetails( $authchk ); 
        ( trim($lookup['fromlocation']) !== trim($usr[0]['presentinstitution']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MAY NOT ADD SEGMENTS FOR A BIOGROUP THAT WAS PROCURED AT AN INSTITUTION FOR WHICH YOU ARE PRESENTLY NOT WORKING")) : "";         
        //TODO: CHECK OTHER DROPMENU VALUES
        //TODO: CHECK ASSIGNMENTS ARE VALID          
      }  else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP CAN'T ACCEPT SEGMENTS.  EITHER THE GROUP DOESN'T EXIST, HAS ALREADY BEEN LOCKED, IS VOIDED OR IS MORE THAN A DAY OLD."));
      }
      
      if (array_key_exists('AdditiveDiv', $sg )) {
          switch ( $sg['AdditiveDiv'] ) { 
              case 'slide': 
                  ( ( trim($sg['selectorAssignInv']) !== "QC" ) && trim($sg['SlideCutFromBlock']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFYING A SLIDE PREPARATION, AN FFPE BLOCK MUST BE SPECIFIED FROM WHICH TO CUT THE SLIDE.")) : "";
                  ( (trim($sg['selectorAssignInv']) !== "QC" ) && !preg_match("/[0-9]{5}T[0-9]{3}/", trim($sg['SlideCutFromBlock']), $match)) ? (list( $errorInd, $msgArr[] ) = array(1 ,"SPECIFIED SEGMENT BLOCK FROM WHICH TO CUT SLIDE MUST BE IN FORMAT OF 00000T000.")) : ""; 
                  ( trim($sg['SlideQtyNbr']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFYING A SLIDE PREPARATION, THE NUMBER OF SLIDES TO CUT MUST BE SPECIFIED.")) : "";                         
                  ( preg_match('/[^0-9]/',trim($sg['SlideQtyNbr'])) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'SLIDE QTY' FIELD MUST BE SPECIFIED AS A WHOLE NUMBER.")) : "";                         
                  break;
              case 'pb': 
                  $addList = json_decode($sg['AdditiveDivValue'], true);
                  foreach ($addList as $v) { 
                      ( (int)$v['qty'] < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'SLIDE QTY' FIELD MUST BE SPECIFIED AS A WHOLE NUMBER GREATER THAN 0."))  : "";
                      (  preg_match('/[^0-9]/',trim($v['qty'])) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'SLIDE QTY' FIELD MUST BE SPECIFIED AS A WHOLE NUMBER."))   : "";
                      (  trim($v['typeofslide']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'TYPE OF SLIDE' MUST BE SPECIFIED."))   : "";
                  }
                  break;
              case 'fresh': 
                  break;
          }
      }
      
  
      //END DATA CHECKS
      if ( $errorInd === 0 ) { 
      if (array_key_exists('AdditiveDiv', $sg )) {
          switch ( $sg['AdditiveDiv'] ) { 
             case 'slide':
                 if ( strtoupper(trim($sg['selectorAssignInv'])) === "QC" ) { 
                     $groupingid = generateRandomString(20);                  
                     $segLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), 0, 4, trim($sg['PreparationMethodValue']), trim($sg['PreparationValue']), "", trim($sg['SlideCutFromBlock']), 1, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], 'BANK', "", "", $sg['SGComments'], '', $groupingid );  
                 } else { 
                   $groupingid = generateRandomString(20);                  
                   for ($sldcnt = 0; $sldcnt < (int)$sg['SlideQtyNbr']; $sldcnt++) { 
                     $segLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), 0, 4, trim($sg['PreparationMethodValue']), trim($sg['PreparationValue']), "", trim($sg['SlideCutFromBlock']), 0, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], trim($sg['selectorAssignInv']), $invDspName, $sg['selectorAssignReq'], $sg['SGComments'], '', $groupingid ); 
                   }
                 }

                  break;
              case 'pb': 
                $groupingid = generateRandomString(20);
                $segLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), trim($sg['AddMetric']), trim($sg['AddMetricUOMValue']), trim($sg['PreparationMethodValue']), trim($sg['PreparationValue']), trim($sg['PreparationContainerValue']), trim($sg['SlideCutFromBlock']), 0, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], trim($sg['selectorAssignInv']), $invDspName, $sg['selectorAssignReq'], $sg['SGComments'], '', "");
                //TODO: PRINT LABEL --- IN ADD SEGMENT FUNCTION 
                $addSldList = json_decode($sg['AdditiveDivValue'], true);
                  foreach ($addSldList as $sldval) { 
                    //GET TYPE OF SLIDE AND LOOP QTY
                    $typeSlide = strtoupper($sldval['typeofslide']);
                    $qty = (int)$sldval['qty'];
                    for ($s = 0; $s < $qty; $s++) { 
                        $slideLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), "", 4, "SLIDE", trim($typeSlide), "", $segLbl, 0, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], trim($sg['selectorAssignInv']), $invDspName, $sg['selectorAssignReq'],'', '', $groupingid);
                    }                 
                  }
                  break;
              case 'fresh': 
                  //$groupingid = generateRandomString(20);
                  $segLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), trim($sg['AddMetric']), trim($sg['AddMetricUOMValue']), trim($sg['PreparationMethodValue']), trim($sg['PreparationValue']), trim($sg['PreparationContainerValue']), trim($sg['SlideCutFromBlock']), 0, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], trim($sg['selectorAssignInv']), $invDspName, $sg['selectorAssignReq'], $sg['SGComments'], $sg['AdditiveDivValue'], "");
                  break;
          }
      } else { 
          $segLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($sg['AddHP']), trim($sg['AddMetric']), trim($sg['AddMetricUOMValue']), trim($sg['PreparationMethodValue']), trim($sg['PreparationValue']), trim($sg['PreparationContainerValue']), '', 0, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], trim($sg['selectorAssignInv']), $invDspName, $sg['selectorAssignReq'], $sg['SGComments'], "", "");          
      }
           //TODO:  CHECK THAT THE SEGMENTS ARE MADE
           $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                        
    }

    function segmentcreateqmspieces( $request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $bg = json_decode($passdata, true); 
      $bgid = cryptservice($bg['bgency'],'d',false);
      // TODO: Make Sure Tech Has RIGHTS to procure at Institution and check PRCTechInstitute and PRCProcedureInstitutionValue match
      $lookupSQL = "SELECT bs.pbiosample, bsd.fromlocation as fromlocation FROM four.ut_procure_biosample bs left join (select * from four.ref_procureBiosample_details where activeind = 1) bsd on bs.pbiosample = bsd.pbiosample where ifnull(bs.migrated,0) = 0  and ifnull(bs.voidind,0) = 0 and bs.selector = :selectorid";
      $lookupR = $conn->prepare($lookupSQL); 
      $lookupR->execute(array(':selectorid' => $bgid ));
      if ((int)$lookupR->rowCount() === 1) { 
        //CHECK QMS PIECES
        $lookup = $lookupR->fetch(PDO::FETCH_ASSOC);
        $chkSQL = "SELECT * FROM four.ut_procure_segment where activeind = 1 and hprind = 1 and pbiosample = :pbiosample";
        $chkR = $conn->prepare($chkSQL);
        $chkR->execute(array(':pbiosample' => $lookup['pbiosample']));
        ( (int)$chkR->rowCount() !== 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THIS BIOGROUP ALREADY HAS QMS SEGMENTS ATTACHED.  YOU MAY NOT ADD QMS SEGMENTS USING THE 'ADD QMS' BUTTON.")) : "";
        //{"bgency":"Q1pYNVpnSnJnM1VlNzRtOHhrN29LZz09","hrpost":".25","metric":".8","metuom":"4"}
        //E) initial metric and UOM are a number and a valid menu option 
        ( trim($bg['hrpost']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE HOURS POST FIELD IS REQUIRED")) : "";
        ( preg_match('/[^0-9\.]/', $bg['hrpost']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE HOURS POST MUST BE A NUMBER (DECIMAL)")) : "";
        
        ( trim($bg['metric']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC MEASURE IS REQUIRED")) : "";
        ( preg_match('/[^0-9\.]/', $bg['metric']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC NUMBER MUST BE A NUMBER (DECIMAL)")) : "";
        
        if ( trim($bg['metuom']) !== "" ) {  
          $chkMetSQL = "SELECT * FROM four.sys_master_menus where menu = 'metric' and dspind = 1 and queriable = 1 and menuvalue = :metuom";
          $chkMetR = $conn->prepare($chkMetSQL); 
          $chkMetR->execute(array(':metuom' => trim($bg['metuom'])));
          ( $chkMetR->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC UOM IS INVALID.  SEE CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
        } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC UOM IS REQUIRED"));
        }
        $days = biogroupdayssince($bgid);
        ( count($days[0]) === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SYSTEM ERROR:  BG SELECTOR NOT FOUND ON DATE CHECK.  SEE A CHTN INFORMATICS STAFF.")) : "";
        ( (int)$days[0]['dbwrite'] > 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DAYS SINCE DATABASE WRITE DATE IS GREATER THAN ZERO.  YOU MAY NOT ADD QMS SEGMENTS AFTER THE DATE OF BIOSAMPLE PROCUREMENT.")) : ""; 
        ( (int)$days[0]['procdate'] > 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DAYS SINCE PROCUREMENT DATE IS GREATER THAN ZERO.  YOU MAY NOT ADD QMS SEGMENTS AFTER THE DATE OF BIOSAMPLE PROCUREMENT.")) : ""; 
        $authuser = $_SERVER['PHP_AUTH_USER']; 
        $authpw = $_SERVER['PHP_AUTH_PW'];
        $authchk = cryptservice($authpw,'d', true, $authuser);
        ( trim($authchk) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER AUTHENTICATION FAILED.  EITHER YOUR SESSION HAS EXPIRED OR YOU ARE NOT VALID")) : ""; 
        ( trim($authchk) !== trim($authuser) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER AUTHENTICATION FAILED.  EITHER YOUR SESSION HAS EXPIRED OR YOU ARE NOT VALID")) : ""; 
        $usr = userDetails( $authchk ); 
        //[{"sessionid":"3kuck2rbedsl93nes0637r13e7","originalaccountname":"proczack","presentinstitution":"HUP","primaryinstcode":"HUP"}]
        ( trim($lookup['fromlocation']) !== trim($usr[0]['presentinstitution']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MAY NOT ADD SEGMENTS FOR A BIOGROUP THAT WAS PROCURED AT AN INSTITUTION FOR WHICH YOU ARE PRESENTLY NOT WORKING")) : ""; 
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP CAN'T ACCEPT QMS SEGMENTS.  EITHER THE GROUP DOESN'T EXIST, HAS ALREADY BEEN LOCKED OR IS VOIDED"));
      }
      if ( $errorInd === 0 ) { 
          //TODO:  MAKE PREPS DYNAMIC 
          $groupingid = generateRandomString(20);
          $pbSegLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($bg['hrpost']), trim($bg['metric']), trim($bg['metuom']), "PB", "FFPE", "", "", 1, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], "BANK", "BANK", "","","",$groupingid);
          $sldSegLbl = addSegmentToBiogroup($lookup['pbiosample'], trim($bg['hrpost']), "", 4, "SLIDE", "H&E SLIDE", "", $pbSegLbl, 1, trim($usr[0]['presentinstitution']), $usr[0]['originalaccountname'], "QC", "QC", "", "","",$groupingid);
          //TODO:  CHECK THAT THE SEGMENTS ARE MADE
          $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                        
    }

    function getprocurementsegmentlisttable ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $bg = json_decode($passdata, true);

      $sglist = self::getprocurementsegmentlist($request,$passdata);
      
      if ( (int)$sglist['statusCode'] === 200 ) { 
 
          if ( count($sglist['data']['DATA']) > 0 ) { 

              $segAnnounce = ( (int)$sglist['data']['ITEMSFOUND'] > 1 ) ? "Segments Found: {$sglist['data']['ITEMSFOUND']}" : "{$sglist['data']['ITEMSFOUND']} Segment Found";
           //<tr><td colspan=9 id=segmentCountLine>{$segAnnounce}</td></tr>

           $sgTbl = "<table border=0 id=procurementScreenSegmentList>";
           $sgTbl .= "<thead><tr><th>BGS</th><th>HPR</th><th>HR</th><th>Preparation</th><th>Metric</th><th>Cut From</th><th>Assignment</th><th>Procured By-At-Time</th></tr></thead><tbody>";

           foreach ( $sglist['data']['DATA'] as $key => $value) { 


//THIS BUILDS THE TABLE ROWS               
               
             $hprind = ( (int)$value['hprind'] === 1 ) ? "X" : "-";
             $prep = ( trim($value['prpcode']) === "" ) ? "" : "{$value['prpcode']}";
             if ( trim($value['prpmetdetail']) !== "" ) { 
               $prep .= ( trim($prep) === "") ? "{$value['prpmetdetail']}" : " / {$value['prpmetdetail']}"; 
             }
             if ( trim($value['container']) !== "" ) { 
               $prep .= ( trim($prep) === "") ? "{$value['container']}" : " ({$value['container']})"; 
             }
             $metric = ( trim($value['metric']) === "" ) ? "&nbsp;" : "{$value['metric']}{$value['shortuom']}";
             $cutblock = ( trim($value['cutfromblockid']) === "" ) ? "&nbsp;" : "{$value['cutfromblockid']}";
             if ( strtolower(substr(trim($value['assigninvestid']),0,3)) !== 'inv') {  
                 $assignment = (trim($value['assigninvestid']) === 'QC') ? "&nbsp;" : trim($value['assigninvestid']);
             } else { 
                 $assignment = (trim($value['assigninvestid']) !== 'QC') ? "{$value['assigninvestid']} / {$value['assignrequestid']} ({$value['assigndspname']})" : "";
             }
             //TODO:  BUILD VOID DSP (strikeout)             
             $procmet = trim($value['procuredby']);
             $procmet .= ( trim($value['procuredat']) === "" ) ? "" : "@{$value['procuredat']}"; 
             $proctime = $value['proctime'];

             
             $sgTbl .= "<tr><td class=ptSegBGS>{$value['bgs']}</td><td class=ptSegHPR>{$hprind}</td><td class=ptSegHRP>{$value['hrpost']}</td><td class=ptSegPRP>{$prep}</td><td class=ptSegMET>{$metric}</td><td class=ptSegCUT>{$cutblock}</td><td class=ptSegASS>{$assignment}</td><td>{$procmet} - {$proctime}</td></tr>";
             
//STOP TABLE ROWS             
             
             
           }

           $sgTbl .= "</tbody></table>";
           $responseCode = 200;
        } else { 
          //ERROR:  RESPONSECODE GOOD BUT NO SEGMENTS - THIS SHOULDN'T HAPPEN
        }
      } else { 
          //NO SEGMENTS EXIST
          $sgTbl = "<table><tr><td style=\"font-size: 1.3vh; font-weight: bold;\">NO SEGMENTS FOUND FOR THIS BIOGROUP</td></tr></table>";
      }
      $dta = $sgTbl;
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function getprocurementsegmentlist ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $bg = json_decode($passdata, true);
      $segSQL = getLongSQLStmts('segproclist');

      $sgR = $conn->prepare($segSQL);
      $sgR->execute(array(':selector' => $bg['selector'] , ':selectorunion' => $bg['selector']));
      if ( $sgR->rowCount() > 0 ) {
        $itemsfound = $sgR->rowCount();  
        while ($r = $sgR->fetch(PDO::FETCH_ASSOC)) { 
          $dta[] = $r;
        }
        $responseCode = 200;
      } else { 
        $responseCode = 404;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function getprocurementbiogroup( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $bg = json_decode($passdata, true);
      $bgSQL = <<<BGSQLHERE
SELECT bg.pbiosample, ifnull(bg.migrated,0) as migratedind
, ifnull(bg.migratedon,'') as migratedon, bg.recordstatus
, concat(ifnull(usr.displayname,''), ' (',ifnull(bg.inputBy,'ERROR'),')') as procuretechnician
, bg.fromlocation, institution.institutiondsp
, pdate.procdate as procdate
, det.proctype as proctype, proctype.dspvalue as procurementtype, det.collectionmethod, colltype.dspvalue as collectiondsp
, det.uninvolvedind, det.pathreportind, det.initialmetric, det.initialuom as bginitialuom, det.fromlocation, det.bywho
, ifnull(bg.voidind,0) as voidind, ifnull(bg.voidreason,'') as voidreason
, ifnull(bg.selector,'') as selector 
, ifnull(phi.pxiid,'ERROR') as pxiid
, ifnull(date_format(phi.proceduredate, '%m/%d/%Y'),'') as proceduredate
, ifnull(phi.procedureassoccode,'') as associativecode
, ifnull(phi.pxiinitials,'') as phiinitials
, ifnull(phi.pxirace,'') as phirace
, ifnull(phi.pxigender,'') as phisex
, ifnull(phi.pxiage, '') as phiage
, ifnull(phi.pxiageuom,'') as phiageuom
, ucase(ifnull(cxm.dspvalue,'')) as cx
, ucase(ifnull(rxm.dspvalue,'')) as rx

, ifnull(phi.callback,'-') as callback
, ucase(ifnull(phi.sogi,'')) as sogi
, ifnull(phi.subjectnumber,'') as subjectnbr
, ifnull(phi.protocolnumber,'') as protocolnbr
, ifnull(phi.informedconsent,'') as informedconsentvalue
, ifnull(icm.dspvalue,'') as informedconsentdsp

, ifnull(bcmt.comment,'') as bgcomment
, ifnull(hcmt.comment,'') as qmcomment

, ifnull(dxd.dxdoverride,0) as dxdoverride
, ifnull(dxd.specimencategory,'') as specimencategory
, ifnull(dxd.asite,'') as asite
, ifnull(dxd.ssite,'') as ssite
, ifnull(dxd.dx,'') as dx
, ifnull(dxd.metssite,'') as mets
, ifnull(dxd.metsdx,'') as metssitedx
, ifnull(dxd.systemdiagnosis,'') as systemdiagnosis
, ifnull(dxd.siteposition,'') as siteposition
, ifnull(dxd.unknownmet,2) as unknownmet
, ifnull(uin.dspvalue,'') as unknownmetdsp
, ifnull(prt.dspvalue,'') as pathreportdsp

FROM four.ut_procure_biosample bg
left join (SELECT originalaccountname, displayname FROM four.sys_userbase) as usr on bg.inputby = usr.originalaccountname
left join (SELECT menuvalue, ifnull(longvalue, ifnull(dspvalue,'')) as institutiondsp FROM four.sys_master_menus where menu = 'INSTITUTION') institution on bg.fromlocation = institution.menuvalue
left join (SELECT pbiosample, date_format(refdate,'%m/%d/%Y') as procdate  FROM four.ref_procureBiosample_dates where activeind = 1 and dateDesignation = 'PROCUREMENT') as pdate on bg.pbiosample = pdate.pbiosample
left join (SELECT * FROM four.ref_procureBiosample_details where activeind = 1) as det on bg.pbiosample = det.pbiosample
left join (SELECT * FROM four.sys_master_menus where menu = 'PROCTYPE') as proctype on det.proctype = proctype.menuvalue
left join (SELECT * FROM four.sys_master_menus where menu = 'COLLECTIONT') as colltype on det.collectionmethod = colltype.menuvalue
left join (SELECT * FROM four.ref_procureBiosample_PXI where activeind = 1) as phi on bg.pBioSample = phi.pbiosample
left join (SELECT * FROM four.sys_master_menus where menu = 'CX') cxm on phi.cx = cxm.menuvalue
left join (SELECT * FROM four.sys_master_menus where menu = 'RX') rxm on phi.rx = rxm.menuvalue
left join (SELECT * FROM four.sys_master_menus where menu = 'INFC') icm on phi.informedconsent = icm.menuvalue
left join (SELECT pbiosample, comment FROM four.ref_procureBiosample_comment where activeind = 1 and moduleReference = 'BIOSPECIMENCMT' and trim(comment) <> '') as bcmt on bg.pBioSample = bcmt.pbiosample
left join (SELECT pbiosample, comment FROM four.ref_procureBiosample_comment where activeind = 1 and moduleReference = 'HPRQUESTION' and trim(comment) <> '') as hcmt on bg.pBioSample = hcmt.pbiosample
left join (SELECT pbiosample, dxdoverride, ifnull(speccat,'') as specimencategory, ifnull(primarysite,'') as asite, ifnull(primarysubsite,'') as ssite, concat( ifnull(diagnosis,''), if(ifnull(diagnosismodifier,'') ='' ,'', concat(' :: ',ifnull(diagnosismodifier,'')))) as dx, ifnull(metssite,'') as metssite, ifnull(metsdx,'') as metsdx, ifnull(systemdiagnosis,'') as systemdiagnosis, ifnull(siteposition,'') as siteposition, unknownmet FROM four.ref_procureBiosample_designation where activeind = 1 and reffrommodule = 'PROCUREMENT') as dxd on bg.pbiosample = dxd.pbiosample
left join (SELECT * FROM four.sys_master_menus where menu = 'UNINVOLVEDIND') uin on dxd.unknownmet = uin.menuvalue
left join (SELECT * FROM four.sys_master_menus where menu = 'PRpt') prt on det.pathreportind = prt.menuvalue

where selector = :objSelector
BGSQLHERE;

      $bgRS = $conn->prepare($bgSQL);
      $bgRS->execute(array(':objSelector' => $bg['selector']));
      if ($bgRS->rowCount() === 1) { 
          //FOUND
        $bg = $bgRS->fetch(PDO::FETCH_ASSOC); 
        $dta['bgnbr'] = (int)$bg['pbiosample'];
        $dta['bgfromlocationcode'] = $bg['fromlocation'];
        $dta['bgfromlocation'] = $bg['institutiondsp'];
        $dta['bgprocurementdate'] = $bg['procdate'];
        $dta['bgprocurementtypevalue'] = $bg['proctypevalue'];
        $dta['bgprocurementtype'] = $bg['procurementtype'];
        $dta['bgcollectiontypevalue'] = $bg['collectionmethod'];
        $dta['bgcollectiontype'] = $bg['collectiondsp'];
        $dta['bgproctech'] = $bg['procuretechnician'];
        $dta['bginitialmetric'] = $bg['initialmetric'];
        $dta['bginitialmetricuomvalue'] = $bg['bginitialuom'];
        $dta['bginitialmetricuom'] = $bg['imetricuom'];
        $dta['bgpathreport'] = $bg['pathreportdsp'];

        $dta['bgdxoverride'] = $bg['dxdoverride'];
        $dta['bgdxspeccat'] = $bg['specimencategory'];
        $dta['bgdxasite'] = $bg['asite'];
        $dta['bgdxssite'] = $bg['ssite'];
        $dta['bgdxdx'] = $bg['dx'];
        $dta['bgdxmets'] = $bg['mets'];
        $dta['bgdxmetsdx'] = $bg['metssitedx'];
        $dta['bgdxsystemdx'] = $bg['systemdiagnosis'];
        $dta['bgdxsiteposition'] = $bg['siteposition'];
        $dta['bgdxunknown'] = $bg['unknownmet'];
        $dta['bgdxuninv'] = $bg['unknownmetdsp'];

        $dta['bgphiid'] = $bg['pxiid'];
        $dta['bgphiproceduredate'] = $bg['proceduredate'];
        $dta['bgphiinitials'] = $bg['phiinitials'];

        $dta['bgphirace'] = $bg['phirace'];
        $dta['bgphisex'] = $bg['phisex'];
        $dta['bgphiage'] = $bg['phiage'];
        $dta['bgphiageuom'] = $bg['phiageuom'];
        $dta['bgphicx'] = $bg['cx'];
        $dta['bgphirx'] = $bg['rx'];

        $dta['bgphicallback'] = $bg['callback'];
        $dta['bgphisogi'] = $bg['sogi'];
        $dta['bgphisbjtnbr'] = $bg['subjectnbr'];
        $dta['bgphiprtclnbr'] = $bg['protocolnbr'];
        $dta['bgphiinformed'] = $bg['informedconsentdsp'];

        $dta['bgbcomments'] = $bg['bgcomment'];
        $dta['bgqcomments'] = $bg['qmcomment'];

        $dta['bgmigratedind'] = (int)$bg['migratedind'];
        $dta['bgmigratedon'] = $bg['migratedon'];
        $dta['bgrecordstatus'] = (int)$bg['recordstatus'];
        $dta['bgvoidind'] = (int)$bg['voidind'];
        $dta['bgvoidreason'] = $bg['voidreason'];
        $dta['bgdbselector'] = $bg['selector'];

        $responseCode = 200;
      } else { 
          //NOT FOUND ERROR
          $responseCode = 404;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function preprocesspreparationadditions( $request, $passdata ) { 
      $responseCode = 400; 
      $msg = "BAD REQUEST";
      $msgArr = array();
      $itemsfound = 0;
      $prpRqst = json_decode($passdata, true);   
      require(serverkeys . "/sspdo.zck");
      //$dta = array('pagecontent' => bldDialogGetter('dialogAddBGSegments',$passdata) );
      $pager = "";
      switch (strtoupper($prpRqst['additionsforprep'])) {

      case 'PB':
          //TODO: IT WOULD BE GREAT IF THESE VALUES WERE A WEBSERVICE
          $SQL = "select ifnull(pr.menuvalue,'') as menuvalue, pr.longvalue, pr.dsporder  from (SELECT menuid FROM four.sys_master_menus where menu = 'PREPMETHOD' and menuvalue = :preparation) as pm left join (SELECT * FROM four.sys_master_menus where menu = 'PREPDETAIL' and dspind = 1) as pr on pm.menuid = pr.parentid where trim(pr.menuvalue) <> '' order by pr.dsporder";  
          $rs = $conn->prepare($SQL); 
          $rs->execute(array(':preparation' => 'SLIDE'));

          $sldprp = "<table border=0 class=menuDropTbl>";
          while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
            $sldprp .= "<tr><td onclick=\"fillField('additionalPreparation','{$r['lookupvalue']}','{$r['menuvalue']}');\" class=ddMenuItem>{$r['menuvalue']}</td></tr>";
          }
          $sldprp .= "</table>"; 

          $itmTbl = "<table border=0 id=segAddslideItmTbl><tr><th style=\"width: 1vw;\"></th><th style=\"width: 8vw;\">Type of Slide</th><th style=\"width: 3vw;\">Qty</th></tr></table>";


          $pager = <<<PAGERHERE
<input type=hidden id=fldSEGAdditiveDiv value="pb"><input type=hidden id=fldSEGAdditiveDivValue value="">
<table border=0 width=100%>
<tr><td class=addTblHeader>Add Slide(s) From This Block</td></tr>
<tr><td>These slides will be added and assigned to the same investigator to which the block is assigned.  Leave blank to add zero additional slides.<br>To delete an added slide item, click it.</td></tr>
<tr><td class=addHolder>
<table border=0>
  <tr><td class=prcFldLbl>Type of Slide</td><td class=prcFldLbl>Quantity</td><td></td><td></td></tr>
<tr><td valign=top><div class=menuHolderDiv><input type=hidden id=additionalPreparationValue><input type=text id=additionalPreparation READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 20vw;" id=ddSEGAddPreparationDropDown>{$sldprp}</div></div></td><td valign=top><input type=text id=addPBQtySlide></td><td valign=top><table class=tblBtn id=btnADDQMSSegs style="width: 6vw;" onclick="requestAdditionalSlides();"><tr><td style=" font-size: 1.1vh; "><center>Add</td></tr></table></td></tr><tr><td colspan=5><div id=addSlideDspBox>{$itmTbl}</div></td></tr>
</table>

</td></tr>
</table>
PAGERHERE;
          break;


        case 'SLIDE':
          $sldNbr = "<input type=hidden id=fldSEGAdditiveDiv value=\"slide\"><input type=hidden id=fldSEGAdditiveDivValue value=\"\"><table><tr><td class=prcFldLbl>Cut from Block <span class=reqInd>*</span></td><td class=prcFldLbl>Slide Qty <span class=reqInd>*</span></td></tr><tr><td><input type=text id=fldSEGSlideCutFromBlock></td><td><input type=text id=fldSEGSlideQtyNbr value=1></td></tr></table>";  
          $pager = <<<PAGERHERE
<table border=0 width=100%>
<tr><td class=addTblHeader>Quantity of Slides</td></tr>
<tr><td class=addHolder>{$sldNbr}</td></tr>
</table>
PAGERHERE;
          break; 


        case 'FRESH':
          $freshList = $conn->prepare("SELECT dspvalue, menuvalue FROM four.sys_master_menus where menu = 'PREPARATIONADDITIONSFRESH' and dspind = 1 order by dsporder");
          $freshList->execute();
          $frshCnt = $freshList->rowCount();  
          $freshAdds = ""; 
          if ( (int)$frshCnt > 0 ) {
            $freshAdds = "<input type=hidden id=fldSEGAdditiveDiv value=\"fresh\"><input type=hidden id=fldSEGAdditiveDivValue value=\"\"><div id=freshadditivebtns><table><tr>";
            $btnCount = 0;
            while ($r = $freshList->fetch(PDO::FETCH_ASSOC)) {
              if ($btnCount === 4) { 
                $freshAdds .= "</tr><tr>";
                $btnCount = 0; 
              }
              $freshAdds .= "<td> <table class=tblBtn id='add-{$r['menuvalue']}' onclick=\"selectThisAdditive(this.id);\" style=\"width: 6vw;\" data-aselected='0' data-additivevalue=\"{$r['menuvalue']}\"><tr><td style=\"font-size: 1.1vh;\"><center>{$r['dspvalue']}</td></tr></table></td>";
              $btnCount++;    
            }
            $freshAdds .= "</tr></table></div>";
          }

          $pager = <<<PAGERHERE
<table border=0 width=100%>
<tr><td class=addTblHeader>Additives for Fresh Samples</td></tr>
<tr><td class=addHolder>{$freshAdds}</td></tr>
</table>
PAGERHERE;
          break;

        default:


      }


      $dta = array('pagecontent' => $pager );
      ( trim($dta) !== "" ) ? $responseCode = 200 : "";
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;      
    }

    function preprocesslabelprint( $request, $passdata ) { 
      $responseCode = 400; 
      $msg = "BAD REQUEST";
      $msgArr = array();
      $itemsfound = 0;
    
      $dta = array('pagecontent' => bldDialogGetter('dialogPrintThermalLabels', $passdata) );

      ( trim($dta) !== "" ) ? $responseCode = 200 : "";

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;              
    }
    
   function preprocessinventoryoverride( $request, $passdata ) { 
      $responseCode = 400; 
      $msg = "BAD REQUEST";
      $msgArr = array();
      $itemsfound = 0;
    
      $dta = array('pagecontent' => bldDialogGetter('dialogInventoryOverride', $passdata) );

      ( trim($dta) !== "" ) ? $responseCode = 200 : "";

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;      
    }

   function preprocessvoidbg($request, $passdata) { 
      $responseCode = 400; 
      $msg = "BAD REQUEST";
      $msgArr = array();
      $itemsfound = 0;
    
      $dta = array('pagecontent' => bldDialogGetter('dialogVoidBG',$passdata) );

      ( trim($dta) !== "" ) ? $responseCode = 200 : "";

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;      
    }

   function preprocessaddbgsegments($request, $passdata) { 
      $responseCode = 400; 
      $msg = "BAD REQUEST";
      $msgArr = array();
      $itemsfound = 0;
    
      $dta = array('pagecontent' => bldDialogGetter('dialogAddBGSegments',$passdata) );

      ( trim($dta) !== "" ) ? $responseCode = 200 : "";

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;      
    }

   function initialbgroupsave($request, $passdata) {       
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $bg = json_decode($passdata, true);

/*
 * 
 * PASSDATA = 
 * {"PRCBGNbr":"","PRCPresentInstValue":"HUP","PRCPresentInst":"Hospital of The University of Pennsylvania (HUP)","PRCProcDate":"01/29/2019","PRCProcedureTypeValue":"S","PRCProcedureType":"Surgery","PRCCollectionTypeValue":"EXC","PRCCollectionType":"Excision","PRCTechnician":"PROCZACK","PRCInitialMetric":".3","PRCMetricUOMValue":"4","PRCMetricUOM":"Grams","PRCPXIId":"f969cf77-7a38-4cf9-9378-43bec942207f","PRCPXIInitials":"A.B.","PRCPXIAge":"56","PRCPXIAgeMetric":"yrs","PRCPXIRace":"UNKNOWN","PRCPXISex":"F","PRCPXIInfCon":"NO","PRCPXIDspCX":"UNKNOWN","PRCPXIDspRX":"UNKNOWN","PRCPXILastFour":"3919","PRCPXISubjectNbr":"12346","PRCPXIProtocolNbr":"09877","PRCPXISOGI":"NO DATA","PRCDXOverride":false,"PRCSpecCatValue":"BENIGN","PRCSpecCat":"BENIGN","PRCSiteValue":"S73","PRCSite":"THYROID","PRCSSiteValue":"","PRCSSite":"","PRCDXModValue":"","PRCDXMod":"","PRCUnInvolvedValue":"0","PRCUnInvolved":"NA (Not Applicable to Biosample)","PRCPathRptValue":"2","PRCPathRpt":"Pending","PRCMETSSiteValue":"","PRCMETSSite":"","PRCMETSDXValue":"","PRCMETSDX":"","PRCSitePositionValue":"","PRCSitePosition":"","PRCSystemListValue":"","PRCSystemList":"","PRCBSCmts":"These are Biosamples Comments for a MINIMUM DATA SET FOR A BG","PRCHPRQ":"These are Biosamples Comments for a MINIMUM DATA SET FOR A BG","PRCProcedureInstitutionValue":"HUP","PRCProcedureDateValue":"2019-01-29","PRCProcedureDate":"01/29/2019"}
 *  
 */

      //1) CHECK DATA 
      if ( $errorInd === 0 ) { $chkone   = initialBGDataCheckOne($bg);   if ( (int)$chkone['errorind'] === 1)   { $errorInd = 1; $msgArr = $chkone['msgarr'];   } }
      if ( $errorInd === 0 ) { $chktwo   = initialBGDataCheckTwo($bg);   if ( (int)$chktwo['errorind'] === 1)   { $errorInd = 1; $msgArr = $chktwo['msgarr'];   } }
      if ( $errorInd === 0 ) { $chkthree = initialBGDataCheckThree($bg); if ( (int)$chkthree['errorind'] === 1) { $errorInd = 1; $msgArr = $chkthree['msgarr']; } }
      //TODO: FINISH DATA CHECKS
      //D) Make Sure Tech Has RIGHTS to procure at Institution and check PRCTechInstitute and PRCProcedureInstitutionValue match
      //E) initial metric and UOM are a number and a valid menu option 
      //F) CHECK All Menu Values - PRCProcedureTypeValue; PRCCollectionTypeValue; PRCMetricUOMValue; PRCPXICXValue; PRCPXIRXValue; PRCUpennSOGIValue;  
      //G) Make sure PXI exists (after write mark PXI as received)
      //H) CHECK VOCABULARY


      if ($errorInd === 0) { 
        //2) WRITE DATA IF ALL CHECKS PASS - NO LABEL PRINTING NECESSARY AT BGROUP LEVEL

        //******** WRITE BIOGROUP GET NUMBER ************//  
        //******** THIS IS A NON-TRANSACTIONAL WRITE ****//
        //TODO:  LOOK INTO MAKING THIS TRANSACTIONAL, IF POSSIBLE
        $selector = generateRandomString(15);
        $bgNbr = createBGHeader( $selector, $bg );
        $insDET = createBGDetail( $bgNbr, $bg );
        $insDte = createBGDateDetails( $bgNbr, $bg );
        $insCmt = createBGComment( $bgNbr, $bg); 
        $insDXD = createBGDXD( $bgNbr, $bg );
        $insPXI = createBGPXI( $bgNbr, $bg );

        //( 1 === 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , $bgNbr . " " . cryptservice($selector,'e',false) . " " . $insPXI )) : "";
        //3) SEND BACK encrypted data selector
        $responseCode = 200;
        $dta = cryptservice($selector,'e',false);
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function updatemypresentinstitution($request, $passdata) { 
      $rows = array(); 
      $institutionlist = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();
      $me = json_decode($passdata, true);        

      $myinstitutionsql = "SELECT indsp.menuvalue, indsp.dspvalue FROM four.sys_userbase usr left join four.sys_userbase_allowinstitution inst on usr.userid = inst.userid left join ( SELECT menuid, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION' ) indsp on inst.institutionmenuid = indsp.menuid where usr.allowind = 1 and usr.sessionid = :sessid and TIMESTAMPDIFF(MINUTE, now(), usr.passwordExpireDate) > 5 and inst.onoffind = 1 and indsp.menuvalue = :institutionrequested ";
      $myInstitutionRS = $conn->prepare($myinstitutionsql); 
      $myInstitutionRS->execute(array(':sessid' => $sessid, ':institutionrequested' => $me['requestedInstitution'])); 
      if ($myInstitutionRS->rowCount() === 1) { 
        //ALLOW CHANGE
        $myInst = $myInstitutionRS->fetch(PDO::FETCH_ASSOC);   
         //BACKUP USER 
         $backupSQL = "insert into four.sys_userbase_history SELECT * FROM four.sys_userbase where sessionid = :sessid";
         $backupRS = $conn->prepare($backupSQL);
         $backupRS->execute( array(':sessid' => $sessid ));
         $updSQL = "update four.sys_userbase set presentinstitution = :changeinstto, lastUpdatedBy = 'SET-PRESENT-INST-FUNC', lastUpdatedOn = now() where sessionid = :sessid";
         $updRS = $conn->prepare($updSQL); 
         $updRS->execute(array(':changeinstto' =>  $myInst['menuvalue'], ':sessid' => $sessid)); 

      } else { 
        ( 1 === 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "USER IS NOT ALLOWED ACCESS TO THIS INSTITUTION/OR USER IS DISALLOWED ACCESS TO SCIENCESERVER" )) : "";
      }
      if ( $errorInd === 0 ) { 
        //RELOAD
        $responseCode = 200;    
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function updatemypassword($request, $passdata) { 
      $rows = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();
      $me = json_decode($passdata, true);        
 
      $crd = json_decode(chtndecrypt($me['ency']), true);
      //1) Check Current Password is correct
      $usrChk = "SELECT fiveonepword FROM four.sys_userbase where allowind = 1 and sessionid = :sessid and pwordresetcode = BINARY :resetcode and datediff(passwordexpiredate, now()) > -1 and TIMESTAMPDIFF(MINUTE, now(), pwordResetExpire) > 5";
      $usrChkR = $conn->prepare($usrChk); 
      $usrChkR->execute(array(':sessid' => $sessid , ':resetcode' => $crd['changecode'])); 
      if ( $usrChkR->rowCount() === 1 ) { 
        $usr = $usrChkR->fetch(PDO::FETCH_ASSOC);
        (!password_verify( $crd['currentpassword'], $usr['fiveonepword']) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "SPECIFIED CURRENT PASSWORD IS INCORRECT.")) : "";
      } else { 
        (list( $errorInd, $msgArr[] ) = array( 1 , "USER NOT FOUND. YOU ARE EITHER NOT A VALID USER, YOUR RESET CODE IS INCORRECT OR YOUR PASSWORD HAS EXPIRED FOR TOO LONG."));
      } 
      //2) check that new and confirm passwords match
      ( $crd['newpassword'] !== $crd['confirmpassword'] ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE 'NEW' AND 'CONFIRM' PASSWORD FIELDS DON'T MATCH.")) :"";
      //3) check the strength of the new password 
      ( (int)strlen( $crd['newpassword'] ) < 8 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "YOUR PASSWORD MUST BE MORE THAN 7 CHARACTERS LONG")) :""; 
      ( !preg_match("/(?=[A-Z])/",$crd['newpassword'])) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PASSWORDS ARE REQUIRED TO HAVE AT LEAST 1 UPPERCASE LETTER" )) :"";
      ( !preg_match("/(?=[a-z])/",$crd['newpassword'])) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PASSWORDS ARE REQUIRED TO HAVE AT LEAST 1 LOWERCASE LETTER" )) :"";
      ( !preg_match("/(?=[0-9])/",$crd['newpassword'])) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PASSWORDS ARE REQUIRED TO HAVE AT LEAST 1 NUMERIC CHARACTER" )) :"";
      ( ctype_alnum($crd['newpassword']) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PASSWORDS ARE REQUIRED TO HAVE ONE SPECIAL CHARACTER (Not a space)" )) :"";
      // TODO:  WRITE THESE SECURITY CHECKS
      // - make sure that password is not the same as last password
      // - make sure that the password is not a standard password word (password, qwerty, 12345 etc)
      // - check not a dictionary word
      if ($errorInd === 0) { 
         //WHEN PASSWORD HAS CHANGED BLANK RESET CODE IN DATABASE
         $randomBytes = $crd['newpassword'];
         $options = [ 'cost' => 12 ];
         $npword =  password_hash( $randomBytes, PASSWORD_BCRYPT, $options );
         //BACKUP USER 
         $backupSQL = "insert into four.sys_userbase_history SELECT * FROM four.sys_userbase where sessionid = :sessid and pwordresetcode = BINARY :codechange";
         $backupRS = $conn->prepare($backupSQL);
         $backupRS->execute(array(':sessid' => $sessid, ':altchange' => $crd['changecode']));
         $usrUpdSQL  = "update four.sys_userbase set fiveonepword = :newpword, pwordresetcode = NULL, pwordResetExpire = NULL, passwordexpiredate = date_add(now(), INTERVAL 6 month), lastUpdatedBy = 'CHANGE-PASSWORD-FUNC', lastUpdatedOn = now() where allowind = 1 and sessionid = :sessid and pwordresetcode = :resetcode and datediff(passwordexpiredate, now()) > -1 and TIMESTAMPDIFF(MINUTE, now(), pwordResetExpire) > 0";
         $usrUpdR = $conn->prepare($usrUpdSQL);
         $usrUpdR->execute(array( ':newpword' => $npword, ':sessid' => $sessid, ':resetcode' => $crd['changecode'])); 
         $nbrofrows = $usrUpdR->rowCount(); 

         //IF RowCount = 1 - Reset Session! 
         if ( (int)$nbrofrows === 1) {  
           session_regenerate_id(true);
           //unset all session variables
           session_unset();
           // destroy the session
           session_destroy();
           $responseCode = 200;
         }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    } 

   function updateaboutme($request, $passdata) { 
      $rows = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();
      $me = json_decode($passdata, true);        
      $changecode =  ( array_key_exists('unlockcode', $me) ) ? $me['unlockcode'] : "";
      
      ( trim($me['alternateemail']) !== "" && !filter_var(trim($me['alternateemail']), FILTER_VALIDATE_EMAIL)) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE ALTERNATE EMAIL APPEARS TO BE AN INVALID EMAIL ADDRESS")) : "";
      ( trim($me['officephone']) !== "" && !preg_match('/^\d{3}-\d{3}-\d{4}(\s[x]\d{1,7})?$/',trim($me['officephone']))) ? (list( $errorInd,$msgArr[] )=array( 1 ,"THE OFFICE PHONE NUMBER IS IN AN INVALID FORMAT. FORMAT IS: 123-456-7890 x0000 (Extensions are optional)")):""; 
      ( trim($me['alternatephone']) !== "" && !preg_match('/^\d{3}-\d{3}-\d{4}$/',trim($me['alternatephone']))) ? (list( $errorInd,$msgArr[] )=array( 1 ,"THE ALTERNATE PHONE NUMBER IS IN AN INVALID FORMAT.  FORMAT IS: 123-456-7890 AND MUST BE A CELL NUMBER WITH SMS CAPABILITIES.")) : ""; 
      ( (int)$me['directorydisplay'] !== 0 && (int)$me['directorydisplay'] !== 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "MALFORMED DATA (directory display).  SEE A CHTNEASTERN INFORMATICS STAFF.")) : "";
      
      //TODO: Make a function to check menu values
      $ccCheckSQL = "SELECT additionalInformation, menuvalue  FROM four.sys_master_menus where menu = 'CELLCARRIER'  and menuvalue = BINARY :cc";
      $ccCheckRS = $conn->prepare($ccCheckSQL); 
      $ccCheckRS->execute(array(':cc' => $me['cellcarrier']));
      ( $ccCheckRS->rowCount() !== 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "MALFORMED DATA (Cell Carrier).  SEE A CHTNEASTERN INFORMATICS STAFF.")) : "";
      if ($ccCheckRS->rowCount() === 1) {
          $cc = $ccCheckRS->fetch(PDO::FETCH_ASSOC);
          $cellsuffix = $cc['additionalInformation'];
          $cellCarrierCode = $cc['menuvalue'];
      }
      
      $usrChkSQL = "SELECT userid, sex FROM four.sys_userbase where sessionid = :sessid and altinfochangecode = BINARY :altchange and TIMESTAMPDIFF(MINUTE, now(), altinfochangecodeexpire) > 4";
      $usrChkRS = $conn->prepare($usrChkSQL);
      $usrChkRS->execute(array(':sessid' => $sessid, ':altchange' => $changecode));
      ( $usrChkRS->rowCount() !== 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "INCORRECT 'UNLOCK CODE' OR THE CODE HAS EXPIRED.")) : "";
       
      if ( $usrChkRS->rowCount() === 1 ) { 
          $usr = $usrChkRS->fetch(PDO::FETCH_ASSOC);
          $basicavatar = ($usr['sex'] === 'M') ? "avatar_male" : "avatar_female";          
      }
      
      
      if (trim($me['base64picture']) !== "") { 
          //CHECK Picture
          ( !preg_match('/^data:image\/png/', $me['base64picture']) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PICTURE DOESN'T SEEM TO BE A PNG FORMATTED PICTURE.")) : "";          
         $image_parts = explode(";base64,", $me['base64picture']);          
         $data = base64_decode($image_parts[1]);
         $image = imagecreatefromstring($data); 
         $w = imagesx($image);
         $h = imagesy($image);
         ( (int)$w === 0 || (int)$h === 0 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PICTURE DOESN'T SEEM TO HAVE DIMENSIONS.")) : "";
         ( (int)$w > 1000 || (int)$h > 1000 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "PICTURE NEEDS TO BE APPROXIMATELY 500x500 PIXELS.  RESIZE PICTURE AND TRY AGAIN.")) : "";    
      }

      if ($errorInd === 0) { 
        
          $cellnbr = preg_replace('/[^0-9]/','', $me['alternatephone']) . $cellsuffix;
          
          //BUILD PICTURE
          if ( trim($me['base64picture']) !== "" ) {
           $imagefilename = generateRandomString(8) . ".png";  
           imagepng($image,  genAppFiles . "/publicobj/graphics/usrprofile/{$imagefilename}");
           imagedestroy($im);
          } else { 
           $imagefilename = "";  
          }

          //BACKUP USER 
          $backupSQL = "insert into four.sys_userbase_history SELECT * FROM four.sys_userbase where sessionid = :sessid and altinfochangecode = BINARY :altchange";
          $backupRS = $conn->prepare($backupSQL);
          $backupRS->execute(array(':sessid' => $sessid, ':altchange' => $changecode));
          
          //removeprofilepic
          if ((int)$me['removeprofilepic'] === 1) {
            //REMOVE PROFILE PICTURE
                 $updSQL = <<<UPDSQL
update four.sys_userbase set altinfochangecode = null, altinfochangecodeexpire = null, dspAlternateInDir = :dspindirectory, profileAltEmail = :profAltEmail, profilePhone = :officephone, altphone = :profAltPhone
, cellcarriercode = :builtPhoneNumber, altphonecellcarrier = :cellcarriercode, profilePicURL = :getrightavatar, lastUpdatedBy = 'ABOUT-ME-SYSTEM-UPDATE', lastUpdatedOn = now()
where sessionid = :sessid and altinfochangecode = BINARY :altchange
UPDSQL;
        $rs = $conn->prepare($updSQL);
        $rs->execute(
                array( ':dspindirectory' => (int)$me['directorydisplay'], ':profAltEmail' => trim($me['alternateemail']), ':officephone' =>  trim($me['officephone']), ':profAltPhone' => trim($me['alternatephone']) , ':builtPhoneNumber' => $cellCarrierCode, ':cellcarriercode' =>  $cellnbr, ':getrightavatar' => $basicavatar, ':sessid' => $sessid, ':altchange' => $changecode ));
          } else { 
          }
   
          if ($imagefilename === "") { 
              //NO PICTURE UPLOADED
                 $updSQL = <<<UPDSQL
update four.sys_userbase set altinfochangecode = null, altinfochangecodeexpire = null, dspAlternateInDir = :dspindirectory, profileAltEmail = :profAltEmail, profilePhone = :officephone, altphone = :profAltPhone, cellcarriercode = :builtPhoneNumber, altphonecellcarrier = :cellcarriercode, lastUpdatedBy = 'ABOUT-ME-SYSTEM-UPDATE', lastUpdatedOn = now()
where sessionid = :sessid and altinfochangecode = BINARY :altchange
UPDSQL;
        $rs = $conn->prepare($updSQL);
        $rs->execute(
                array(':dspindirectory' => (int)$me['directorydisplay'], ':profAltEmail' => trim($me['alternateemail']), ':officephone' =>  trim($me['officephone']), ':profAltPhone' => trim($me['alternatephone']) , ':builtPhoneNumber' => $cellCarrierCode, ':cellcarriercode' =>  $cellnbr, ':sessid' => $sessid, ':altchange' => $changecode));                            
          } else { 
              //PICTURE UPLOADED
                 $updSQL = <<<UPDSQL
update four.sys_userbase 
set altinfochangecode = null, altinfochangecodeexpire = null, dspAlternateInDir = :dspindirectory, profileAltEmail = :profAltEmail, profilePhone = :officephone, altphone = :profAltPhone, cellcarriercode = :builtPhoneNumber, altphonecellcarrier = :cellcarriercode, profilePicURL = :getrightavatar, lastUpdatedBy = 'ABOUT-ME-SYSTEM-UPDATE', lastUpdatedOn = now()
where sessionid = :sessid and altinfochangecode = BINARY :altchange
UPDSQL;
        $rs = $conn->prepare($updSQL);
        $rs->execute(
                array(':dspindirectory' => (int)$me['directorydisplay'], ':profAltEmail' => trim($me['alternateemail']), ':officephone' =>  trim($me['officephone']), ':profAltPhone' => trim($me['alternatephone']), ':builtPhoneNumber' => $cellCarrierCode, ':cellcarriercode' =>  $cellnbr, ':getrightavatar' => $imagefilename, ':sessid' => $sessid, ':altchange' => $changecode));              
          }          
          //UPDATE SQL WITH PICTURE UPLOAD         
          $responseCode = 200;
      }                  
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function livemasterrecordsitelisting($request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      if ( trim($passdata) === "" ) { 
          //WHOLE LISTING
          $siteSQL = "SELECT distinct concat( ifnull(anatomicSite,''), if(ifnull(subsite,'')='','', concat(' :: ',ifnull(subsite,'')) )) as sitesubsite FROM masterrecord.ut_procure_biosample where 1=1  order by sitesubsite";
          $siteRS = $conn->prepare($siteSQL);
          $siteRS->execute();
          while ($r = $siteRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
      } else { 
         $delimterArr = json_decode($passdata, true);
         
         
      }
      $msg = $passdata;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function getpwchangecode($request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];
      $r = cryptservice( $authpw, 'd', true, $authuser );
      $sessid = session_id();
      if ($authuser === $r && $sessid === $r ) {
        $sql = "update four.sys_userbase set pwordResetCode = :altcode, pwordResetExpire = date_add(now(), interval 5 hour) where sessionid = :sessionid";
        $rs = $conn->prepare($sql); 
        $altcode = strtoupper(generateRandomString(6));
        $rs->execute(array(':altcode' => $altcode, ':sessionid' => $authuser)); 
        $usrSQL = "SELECT displayname, emailaddress FROM four.sys_userbase where sessionid = :usersession";
        $usrRS = $conn->prepare($usrSQL); 
        $usrRS->execute(array(':usersession' => $authuser)); 
        $usr = $usrRS->fetch(PDO::FETCH_ASSOC);        
        $msg =  <<<MSGTXT
    <table border=0>
      <tr><td>Password Change Code [ScienceServer Profile]</td></tr>
      <tr><td>{$usr['displayname']}:  You requested an password change code to change your password in the ScienceServer Interface.  Code: <b>{$altcode}</b> will be active for the next five hours.  Do NOT share this code with anyone as sharing this code compromises the security of your ScienceServer Account.<p>If you did not request this change password code, please report this email to the CHTNEastern Informatics staff immediately!</td></tr>
      <tr><td align=right> {$usr['emailaddress']} / {$sessid} </td></tr>
    </table>
MSGTXT;
        $sbjctLine = "Change Password Code [SCIENCESERVER SECURITY CODE]";
        $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtLine, msgbody, htmlind, wheninput, bywho) value (:towhoaddressarray, :sbjtLine, :msgbody, 1, now(), 'SS-HELP-TICKET-SYSTEM')";
        $emlRS = $conn->prepare($emlSQL);
        $emlRS->execute(array(':towhoaddressarray' => "[\"{$usr['emailaddress']}\",\"zackvm.zv@gmail.com\"]",':sbjtLine' => $sbjctLine, ':msgbody' => $msg));
        $msg = "";
        $responseCode = 200;
      }
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function getalternateunlockcode($request, $passdata) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];
      $r = cryptservice( $authpw, 'd', true, $authuser );
      $sessid = session_id();
      if ($authuser === $r && $sessid === $r ) {
        //create code 
        $sql = "update four.sys_userbase set altinfochangecode = :altcode, altinfochangecodeexpire = date_add(now(), interval 5 hour) where sessionid = :sessionid";
        $rs = $conn->prepare($sql); 
        $altcode = strtoupper(generateRandomString(5));
        $rs->execute(array(':altcode' => $altcode, ':sessionid' => $authuser)); 
        $usrSQL = "SELECT displayname, emailaddress FROM four.sys_userbase where sessionid = :usersession";
        $usrRS = $conn->prepare($usrSQL); 
        $usrRS->execute(array(':usersession' => $authuser)); 
        $usr = $usrRS->fetch(PDO::FETCH_ASSOC);        
        $msg =  <<<MSGTXT
    <table border=0>
      <tr><td>'About Me' Unlock Code [ScienceServer Profile]</td></tr>
      <tr><td>{$usr['displayname']}:  You requested an unlock code to edit your security information in the ScienceServer Interface.  Code: <b>{$altcode}</b> will be active for the next five hours.  Do NOT share this code with anyone as sharing this code compromises the security of your ScienceServer Account.<p>If you did not request this 'About me' unlock code, please report this email to the CHTNEastern Informatics staff.</td></tr>
      <tr><td align=right> {$usr['emailaddress']} / {$sessid} </td></tr>
    </table>
MSGTXT;
        $sbjctLine = "About Me Security Code [SCIENCESERVER PROFILE]";
        $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtLine, msgbody, htmlind, wheninput, bywho) value (:towhoaddressarray, :sbjtLine, :msgbody, 1, now(), 'SS-HELP-TICKET-SYSTEM')";
        $emlRS = $conn->prepare($emlSQL);
        $emlRS->execute(array(':towhoaddressarray' => "[\"{$usr['emailaddress']}\",\"zackvm.zv@gmail.com\"]",':sbjtLine' => $sbjctLine, ':msgbody' => $msg));
        $msg = "";
        $responseCode = 200;
      }
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function savelinuxorschedphi($request, $passdata) { 
     $rows = array(); 
     //$dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     
     if ($authuser === 'chtneast') { 
       if ((int)checkPostingUser($authuser, $authpw) === 200) { 
           $philist = json_decode($passdata, true);
           if (array_key_exists('philisting', $philist)) { 
//{"philisting":[{"ORSchedid":"3768aab9-5f20-4214-b25c-7c1d19499ec2","ORDate":"2019-01-18","forLocation":"HUP","orschdtid":"018901f9-9862-4347-b8b2-666968bb5bcf","paini":"B.S.","starttime":"3:23","surgeon":"","room":"36","age":"73","race":"","sex":"M","procdetails":"(M1) COLONOSCOPY FLEXIBLE DIAGNOSTICLRB- N\/A_","proctargetstatus":"","informedconsent":"0","callbackref":"2879"},{"ORSchedid":"3768aab9-5f20-4214-b25c-7c1d19499ec2","ORDate":"2019-01-18","forLocation":"HUP","orschdtid":"01c59591-b796-4aa2-9b8e-f2f5832c2cd0","paini":"M.W.","starttime":"2:00","surgeon":"GOLDBERG, DAVID","room":"75","age":"50","race":"","sex":"F","procdetails":"(M1) COLONOSCOPY FLEXIBLE DIAGNOSTICLRB- N\/A_","proctargetstatus":"","informedconsent":"0","callbackref":"2748"},{"ORSchedid":"3768aab9-5f20-4214-b25c-7c1d19499ec2","ORDate":"2019-01-18","forLocation":"HUP","orschdtid":"0229409b-f613-4e67-8b4b-39099af546c4","paini":"D.L.","starttime":"2:05","surgeon":"","room":"36","age":"68","race":"","sex":"M","procdetails":"(M1) EGD FLEXIBLE TRANSORAL W\/ BIOPSYLRB- N\/A (p+) SIGMOIDOSCOPY FLEXIBLE W\/ BIOPSY SINGLE\/MULTIPLELRB- N\/A_","proctargetstatus":"","informedconsent":"0","callbackref":"9634"},{"ORSchedid":"3768aab9-5f20-4214-b25c-7c1d19499ec2","ORDate":"2019-01-18","forLocation":"HUP","orschdtid":"02738e0d-dc83-4f5a-9fd8-c2814aaadab4","paini":"R.M.","starttime":"10:30","surgeon":"BEWTRA, MEENAKSHI","room":"70","age":"78","race":"","sex":"F","procdetails":"(M1) COLONOSCOPY FLEXIBLE DIAGNOSTICLRB- N\/A_","proctargetstatus":"","informedconsent":"0","callbackref":"2181"},{"ORSchedid":"3768aab9-5f20-4214-b25c-7c1d19499ec2","ORDate":"2019-01-18","forLocation":"HUP","orschdtid":"02e8f374-3e91-4ffe-9385-3baf63b1bcfc","paini":"S.D.","starttime":"11:30","surgeon":"LICHTENSTEIN, GARY","room":"81","age":"56","race":"","sex":"M","procdetails":"(M1) COLONOSCOPY FLEXIBLE DIAGNOSTICLRB- N\/A_","proctargetstatus":"","informedconsent":"0","callbackref":"0130"}]}                 
                 foreach ($philist['philisting'] as $ky => $val) { 
                      //RUN THROUGH ONCE TO DO DATA CHECKS
                      //TODO:  WRITE DATA CHECKS
                 
                 }
                 
                 if ($errorInd === 0 ) { 
                    $cleanSQL = "insert into four.tmp_ORListing_unused_bu SELECT * FROM four.tmp_ORListing where TIMESTAMPDIFF(month, listdate, now()) > 13 and (trim(ifnull(targetInd,'')) = '' or trim(ifnull(targetInd,'')) = '-')";
                    $cleanRS = $conn->prepare($cleanSQL); 
                    $cleanRS->execute();
                    $linkSSSQL = "insert into four.tmp_ORListing (location, listdate, starttime, room, surgeons, pxicode, pxiini, lastfourmrn, pxiage, ageuomcode, pxirace, pxisex, proctext, targetind, infcind, lastupdatedby , lastupdateon, ORKey, linkeddonor, linkby) values (:location, :listdate, :starttime, :room, :surgeons, :pxicode, :pxiini, :lastfourmrn, :pxiage, :ageuomcode, :pxirace, :pxisex, :proctext, :target, :ic, :lastupdatedby, now(), :orkey, 1, :delinkedby)";  
                    $linkSSRS = $conn->prepare($linkSSSQL);                    
                    $c = 0;
                    foreach ($philist['philisting'] as $ky => $val) { 
                      $linkSSRS->execute(
                              array(
                                 ':location' => $val['forLocation']
                                ,':listdate' => $val['ORDate']
                                ,':starttime' => $val['starttime']
                                ,':room' => $val['room']
                                ,':surgeons' => $val['surgeon']
                                ,':pxicode' => $val['orschdtid']
                                ,':pxiini' => $val['paini']
                                ,':lastfourmrn' => $val['callbackref']
                                ,':pxiage' => $val['age']
                                ,':ageuomcode' => 1
                                ,':pxirace' => $val['race']
                                ,':pxisex' => $val['sex']
                                ,':proctext' => $val['procdetails']
                                ,':target' => $val['proctargetstatus']
                                ,':ic' => $val['informedconsent']
                                ,':lastupdatedby' => 'PHI-SYSTEM-UPDATE'
                                ,':orkey' => $val['ORSchedid']
                                ,':delinkedby' => 'PHI-SYSTEM-UPDATE'
                              ));                                                
                        $c++;
                    }                 
                    $responseCode = 200; 
                 }                 
           }
       }
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;
    }
    
   function savedelinkedphi($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];

     if ((int)checkPostingUser($authuser, $authpw) === 200) { 
       //CHECK USER PRESENT LOCATION  
       $pdta = json_decode($passdata,true);
       ( !array_key_exists('phicontainer',$pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED DATA PAYLOAD. SEE CHTNEASTERN INFORMATICS STAFF.")) : ""; 
       ( count($pdta['phicontainer']) === 0) ? (list( $errorInd, $msgArr[] ) = array(1 , "NO DONOR PHI PASSED TO SERVICE.  SEE CHTNEASTERN INFORMATICS STAFF.")) : ""; 
       $ageUOMChkSQL = "SELECT ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'AGEUOM' and mnu.dspInd = 1";
       $ageUOMChkRS = $conn->prepare($ageUOMChkSQL);
       $ageUOMChkRS->execute(); 
       $ageuom = array();
       $cntr = 0;
       while ($r = $ageUOMChkRS->fetch(PDO::FETCH_ASSOC)) { 
        $ageuom[$cntr] = $r['lookupvalue'];
        $cntr++;
       }
       $ageuom[$cntr] = "-";
       $raceChkSQL = "SELECT mnu.menuvalue FROM four.sys_master_menus mnu where mnu.menu = :menu and  mnu.dspInd = 1";
       $raceChkRS = $conn->prepare($raceChkSQL);
       $raceChkRS->execute(array(':menu' => 'PXRACE')); 
       $race = array();
       $cntr = 0;
       while ($r = $raceChkRS->fetch(PDO::FETCH_ASSOC)) { 
        $race[$cntr] = $r['menuvalue'];
        $cntr++;
       }
       $race[$cntr] = "-";
       $sexChkSQL = "SELECT mnu.menuvalue FROM four.sys_master_menus mnu where mnu.menu = :menu and  mnu.dspInd = 1";
       $sexChkRS = $conn->prepare($sexChkSQL);
       $sexChkRS->execute(array(':menu' => 'PXSEX')); 
       $sex = array();
       $cntr = 0;
       while ($r = $sexChkRS->fetch(PDO::FETCH_ASSOC)) { 
        $sex[$cntr] = $r['menuvalue'];
        $cntr++;
       }
       $sex[$cntr] = "-";
       //START DATA CHECK
       foreach ($pdta['phicontainer'] as $ky => $vl) { 
         //DATA CHECK 1
         ( !array_key_exists('proceduredate', $vl) || !array_key_exists('delinkedind', $vl) || !array_key_exists('initials', $vl) || !array_key_exists('age', $vl) || !array_key_exists('ageuom', $vl) || !array_key_exists('race', $vl) || !array_key_exists('sex', $vl) || !array_key_exists('lastfour', $vl) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED DATA PAYLOAD. SEE CHTNEASTERN INFORMATICS STAFF.")) : ""; 
         //DATA CHECK 2
         if ( array_key_exists('proceduredate', $vl) ) { 
            ( trim($vl['proceduredate']) === "" ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "MISSING PROCEDURE DATE PASSED")) : "";
            ( !verifyDate( $vl['proceduredate'], 'Y-m-d') ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "INVALID PROCEDURE DATE PASSED")) : "";
         }  
         //DATA CHECK 3
         if ( array_key_exists('initials', $vl) ) { 
             ( strlen( trim($vl['initials']) ) > 4 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "VALUE FOR INITIALS MUST BE LESS THAN 5 CHARACTERS")) : "";
             ( strlen( trim($vl['initials']) ) < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "INITIALS MUST BE PROVIDED")) : "";
         }  
         //DATA CHECK 4
         if ( array_key_exists('age', $vl) ) { 
            ( !is_numeric( $vl['age'] )    ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "AGE MUST BE A NUMBER")) : "";
            (  (int)$vl['age'] > 99  ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "INVALID AGE VALUE (0-99)")) : "";
         }  
         //DATA CHECK 5
         if ( array_key_exists('ageuom',$vl)) { 
             (  trim($vl['ageuom']) === "") ? (list( $errorInd, $msgArr[] ) = array(1 , "AGE UOM IS MISSING")) : "";
             ( !in_array($vl['ageuom'], $ageuom) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "AGE UOM IS NOT VALID.  SEE A CHTNEASTERN INFORMATICS STAFF")) : "";
         }
         //DATA CHECK 6 
         if ( array_key_exists('race',$vl)) { 
             (  trim($vl['race']) === "") ? (list( $errorInd, $msgArr[] ) = array(1 , "DONOR RACE IS MISSING")) : "";
             ( !in_array($vl['race'], $race) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DONOR RACE IS INVALID.")) : "";
         }
         //DATA CHECK 7
         if ( array_key_exists('sex',$vl)) { 
             (  trim($vl['sex']) === "") ? (list( $errorInd, $msgArr[] ) = array(1 , "DONOR SEX IS MISSING")) : "";
             ( !in_array($vl['sex'], $sex) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DONOR SEX IS INVALID.")) : "";
         }
         ( $vl['delinkedind'] === 0 && !array_key_exists('proceduretext',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "PROCEDURE TEXT IS MISSING")) : "";
         ( $vl['delinkedind'] === 0 && (array_key_exists('proceduretext',$vl)  && trim($vl['proceduretext']) === "")) ? (list( $errorInd, $msgArr[] ) = array(1 , "LINKED DONORS MUST HAVE A PROCEDURE TEXT COMPONENT")) : "";
         ( $vl['delinkedind'] === 0 && !array_key_exists('surgeon',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SURGEON NAME IS MISSING")) : "";
         ( $vl['delinkedind'] === 0 && !array_key_exists('room',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "OPERATING ROOM IS MISSING")) : "";
         ( $vl['delinkedind'] === 0 && !array_key_exists('starttime',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "START TIME IS MISSING")) : "";
         ( $vl['delinkedind'] === 0 && !array_key_exists('institution',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "LINKED INSTITUTION IS MISSING")) : "";
         ( $vl['delinkedind'] === 0 && !array_key_exists('linuxpxicode',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "LINUX PXI CODE IS MISSING")) : "";

         ( !array_key_exists('interface',$vl) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DEFINED INTERFACE IS NOT SPECIFIED")) : "";
       }
       //END DATA CHECKS

       if ($authuser <> 'chtneast') { 
         $locationSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where sessionid = :sessionid";
         $locationRS = $conn->prepare($locationSQL);
         $locationRS->execute(array(':sessionid' => $authuser));
         if ($locationRS->rowCount() === 1) { 
           $location = $locationRS->fetch(PDO::FETCH_ASSOC); 
           $defaultlocation = $location['presentinstitution'];
           $username = $location['originalaccountname'];
         } else { 
           //TODO:  CHECK THIS SOMEHOW
             $defaultlocation = 'HUP';
             $username = "----";
         }
       } else {
         //TODO:  ERROR CHECK THIS MAKE SURE THAT THE INSTITUTION KEY IS FILLED IN 
           $defaultlocation = 'HUP';
           $username = "SCIENCESERVER-SYSTEM";
       }

       if ( $errorInd === 0 ) {
           //{"phicontainer":[{"proceduredate":"2019-01-16","delinkedind":0,"initials":"zvm","age":"48","ageuom":"1","race":"White","sex":"M","lastfour":"1555","proceduretext":"PROCEDURE TEXT GOES HERE","surgeon":"DR. ZACK","room":"3434", "starttime":"14:25","institution":"HUP","linuxpxicode":"dsdsdsdsdsds","target":"","informedconsent":0,"orkey":"8s9sd899sd","interface":"LINUX"},{"proceduredate":"2019-01-16","delinkedind":1,"initials":"sy","age":"42","ageuom":"1","race":"White","sex":"M","lastfour":"1111","interface":"SS"}]}
           //IF USER NOT CHTNEAST && DELINK = 1 - CHECK PRESENT LOCATION 
           //CHECK FOR TARGET OTHERWISE = UNTARGETED
           //CHECK FOR INFORMEDCONSENT = OTHERWISE = 0 
           //1) RUN ORTMP CLEANER
           $cleanSQL = "insert into four.tmp_ORListing_unused_bu SELECT * FROM four.tmp_ORListing where TIMESTAMPDIFF(month, listdate, now()) > 13 and (trim(ifnull(targetInd,'')) = '' or trim(ifnull(targetInd,'')) = '-')";
           $cleanRS = $conn->prepare($cleanSQL); 
           $cleanRS->execute();
           $delinkSSSQL = "insert into four.tmp_ORListing (location, listdate, pxicode, pxiini, lastfourmrn, pxiage, ageuomcode, pxirace, pxisex, proctext, targetind, infcind, lastupdatedby , lastupdateon, delinkeddonor, delinkby) values (:location, :listdate, :pxicode, :pxiini, :lastfourmrn, :pxiage, :ageuomcode, :pxirace, :pxisex, :proctext, 'T', 0, :lastupdatedby, now(), 1, :delinkedby)";  
           $delinkSSRS = $conn->prepare($delinkSSSQL);

           foreach ($pdta['phicontainer'] as $dkey => $donor) { 
             if ( $donor['delinkedind'] === 1 ) { 
               switch ( $donor['interface'] ) { 
                 case 'SS':
                    $genPXICode = "DAD_" . generateRandomString(15);
                    $dta = $genPXICode; 
                    $delinkSSRS->execute(array(':location' => $defaultlocation, ':listdate' => $donor['proceduredate'], ':pxicode' => $genPXICode,':pxiini' => strtoupper( $donor['initials'] ) , ':lastfourmrn' => $donor['lastfour'], ':pxiage' => $donor['age'],':ageuomcode' => $donor['ageuom'],':pxirace' => $donor['race'], ':pxisex' => $donor['sex'], ':proctext' =>   "ADDED FROM ScienceServer Interface Donor Delink by {$username} " . date("Y-m-d H:i:s"), ':lastupdatedby' => $username, ':delinkedby' => $username     ));
                 break;
                 case 'LINUX':
                     //TODO:  BUILD DELINKED DONOR FROM LINUX SERVER
                 break;
               }
             } else { 
                //LINKED DONOR FROM LINUXSERVER
                //TODO:  INPUT LINKED DONOR RECORD FROM OR LINUX SCHEDULER
             }
           } 
           $responseCode = 200;
       } else { 
       }

     }

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
    } 

   function submithelpticket( $request, $passdata ) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     ( trim($pdta['submitter']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "Submitter's Name is Required (All Fields for a help ticket submission are required")) : ""; 
     ( trim($pdta['submitteremail']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "Submitter's Email is Required (All Fields for a help ticket submission are required")) : ""; 
     ( trim($pdta['reason']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "Ticket Reason is Required (All Fields for a help ticket submission are required")) : ""; 
     ( trim($pdta['recreateind']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "Recreation Indicator is Required (All Fields for a help ticket submission are required")) : ""; 
     ( trim($pdta['ssmodule']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "An affected ScienceServer Module is Required (All Fields for a help ticket submission are required")) : ""; 
     ( trim($pdta['description']) === "")  ? (list( $errorInd, $msgArr[] ) = array(1 , "Description is Required (All Fields for a help ticket submission are required")) : ""; 

     if ( $errorInd === 0 ) { 

       $insTicketSQL = "insert into four.app_helpTicket(bywho, bywhoemail, onwhen, reasoncode, affectedSSModule, recreateind, description) values (:bywho, :bywhoemail, now(), :reasoncode, :module, :recreateind, :description)";
       $insTicketRS = $conn->prepare($insTicketSQL);
       $insTicketRS->execute(array(':bywho' => $pdta['submitter'], ':bywhoemail' => $pdta['submitteremail'], ':reasoncode' => $pdta['reason'], ':module' => $pdta['ssmodule'], ':recreateind' => $pdta['recreateind'], ':description' => $pdta['description']));
       $ticketNbr = $conn->lastInsertId();   

       $itStaff = "SELECT emailaddress FROM four.sys_userbase where informaticsind = :indicator";
       $itStaffRS = $conn->prepare($itStaff); 
       $itStaffRS->execute(array(':indicator' => 1)); 
       $itEmailList = "\"zackvm.zv@gmail.com\",\"{$pdta['submitteremail']}\"";
       while ($itr = $itStaffRS->fetch(PDO::FETCH_ASSOC)) { 
         $itEmailList .= ",\"{$itr['emailaddress']}\""; 
       }       
       $dta['ticketnbr'] = substr(('000000' . $ticketNbr), -6);
       $msg =  <<<MSGTXT
    <table border=0>
      <tr><td colspan=2>HELP TICKET REQUEST FROM SCIENCESERVER</td></tr>
      <tr><td><b>Ticket #</b>:&nbsp;</td><td> {$dta['ticketnbr']} </td></tr>
      <tr><td><b>Submited</b>:&nbsp;</td><td> {$pdta['submitter']} / {$pdta['submitteremail']} </td></tr>
      <tr><td><b>Reason</b>:&nbsp;</td><td> {$pdta['reason']} </td></tr>
      <tr><td><b>Module</b>:&nbsp;</td><td> {$pdta['ssmodule']} </td></tr>
      <tr><td><b>Re-create</b>:&nbsp;</td><td> {$pdta['recreateind']} </td></tr>
      <tr><td colspan=2><b>Issue Description</b></td></tr><tr><td colspan=2> {$pdta['description']} </td></tr>
    </table>
MSGTXT;
       $sbjctLine = "SCIENCESERVER ISSUE TICKET {$dta['ticketnbr']}";
       $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtLine, msgbody, htmlind, wheninput, bywho) value (:towhoaddressarray, :sbjtLine, :msgbody, 1, now(), 'SS-HELP-TICKET-SYSTEM')";
       $emlRS = $conn->prepare($emlSQL);
       $emlRS->execute(array(':towhoaddressarray' => "[{$itEmailList}]",':sbjtLine' => $sbjctLine, ':msgbody' => $msg));
       $responseCode = 200;
     } else { 
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
    } 

   function submithelpsearch( $request, $passdata ) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      
     $searchTerm = $pdta['searchterm'];
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW']; 

     //( 1 === 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "{$searchTerm}")) : "";
     ( trim($searchTerm) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The Search Term Field has been left blank")) : ""; 
     //( checkPostingUser($pdta['sessid'],$authpw) !== 200 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER IS INVALID")) : "";
     ( session_id() !== $authuser) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER IS INVALID")) : "";
     $usrSQL = "SELECT displayname, originalaccountname, emailaddress FROM four.sys_userbase where sessionid = :sess AND TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";
     $usrRS = $conn->prepare($usrSQL); 
     $usrRS->execute(array(':sess' => $authuser)); 

     if ($usrRS->rowCount() === 1) { 
        $usr = $usrRS->fetch(PDO::FETCH_ASSOC); 
        $oAccount = "{$usr['displayname']} ({$usr['originalaccountname']})";
        $eAccount = "{$usr['emailaddress']}";
     } else { 
       ( $usrRS->rowCount() !== 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT LISTED OR HAS AN EXPIRED PASSWORD.")) : "";
     }    


     if ( $errorInd === 0 ) { 
       
       $objid = strtolower( generateRandomString() );
       $insSQL = "insert into four.objsrchdocument (objid, bywho, onwhen, srchterm, doctype) value (:objid,:whoby,now(),:srchtrm,:doctype)";
       $insR = $conn->prepare($insSQL);
       $insR->execute(array(':objid' => $objid, ':whoby' => $oAccount, ':srchtrm' => $passdata, ':doctype' => 'HELP-SEARCH-REQUEST'    ));
       $dta['searchid'] = $objid;
       $responseCode = 200;

     } else { 
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
    } 

   function saveencounterdonor( $request, $passdata ) {
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW']; 

     //DATA-CHECKS
     //1) IS USER VALID AND ALLOWED - BY INSTITUTION/SESSION/ALLOWED TECH
     ( $pdta['sessid'] !== session_id() ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER IS INVALID")) : "";
     ( session_id() !== $authuser) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER IS INVALID")) : "";
     ( checkPostingUser($pdta['sessid'],$authpw) !== 200 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER IS INVALID")) : "";

     $usrSQL = "SELECT displayname, originalaccountname, presentinstitution FROM four.sys_userbase where sessionid = :sess AND TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0 and allowProc = 1";
     $usrRS = $conn->prepare($usrSQL); 
     $usrRS->execute(array(':sess' => $authuser)); 
     ( $usrRS->rowCount() < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT LISTED AS A PROCUREMENT TECH, LISTED AS PRESENTLY AT THIS LOCATION OR HAS AN EXPIRED PASSWORD.")) : "";

     if ($usrRS->rowCount() === 1) { 
        $usr = $usrRS->fetch(PDO::FETCH_ASSOC); 
        $presUserInst = $usr['presentinstitution'];
        $oAccount = "{$usr['displayname']} ({$usr['originalaccountname']})";
     }     

     //4) IS AGE NUMERIC
     ( !is_numeric($pdta['age'])) ? (list( $errorInd, $msgArr[] ) = array(1 , "The specified donor age must be numeric")) : "";
     
     //2) DOES ENCOUNTER EXIST
     $eid = cryptservice($pdta['encyeid'], 'd');
     ( trim($eid) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Encounter Id is missing - See a CHTNEastern Informatics Staff member")) : "";
     $chkESQL = "SELECT * FROM four.tmp_ORListing where location = :loc and PXICode = :eid";
     $chkERS = $conn->prepare($chkESQL); 
     $chkERS->execute(array(':loc' => $presUserInst, ':eid' => $eid));
     ( $chkERS->rowCount() !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Encounter Id Not Found in Data Tables.  See a CHTNEASTERN Informatics staff member")) : "";

     //( 1 === 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "{$eid} {$oAccount} {$presUserInst}")) : "";
     
     //3) DO ALL FIELDS THAT HAVE VALUES HAVE TRUE VALUES
     $mnuValueSQL = "SELECT * FROM four.sys_master_menus where menu = :whichmenu and menuvalue = :whichvalue and dspind = 1";
     $mnuValueQSQL = "SELECT * FROM four.sys_master_menus where menu = :whichmenu and menuvalue = :whichvalue and queriable = 1 and dspind = 1";
     $mnuDspSQL = "SELECT * FROM four.sys_master_menus where menu = :whichmenu and dspvalue = :whichvalue and dspind = 1";
     $mnuValRS = $conn->prepare($mnuValueSQL);
     $mnuValQRS = $conn->prepare($mnuValueQSQL);
     $mnuDspRS = $conn->prepare($mnuDspSQL);

     $mnuValRS->execute(array(':whichmenu' => 'INFC', ':whichvalue' => $pdta['informedindvalue']));
     ( $mnuValRS->rowCount() !== 1) ?  (list( $errorInd, $msgArr[] ) = array(1 , "INFORMED CONSENT MENU VALUE IS INVALID" )) : "";  

     $mnuValQRS->execute(array(':whichmenu' => 'pxTARGET', ':whichvalue' => $pdta['targetindvalue']));
     ( $mnuValQRS->rowCount() !== 1) ?  (list( $errorInd, $msgArr[] ) = array(1 , "TARGET STATUS MENU VALUE IS INVALID" )) : "";  

     $mnuDspRS->execute(array('whichmenu' => 'AGEUOM', ':whichvalue' => $pdta['ageuomvalue'] ));
     ( $mnuDspRS->rowCount() !== 1) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE AGE-UOM IS INVALID" )) : "";  

     $mnuValRS->execute(array(':whichmenu' => 'PXRACE', ':whichvalue' => $pdta['racevalue'] ));
     ( $mnuValRS->rowCount() !== 1) ?  (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED DONOR RACE IS INVALID" )) : "";
     
     $mnuDspRS->execute(array( ':whichmenu' => 'PXSEX', ':whichvalue' => $pdta['sex'] ));
     ( $mnuDspRS->rowCount() !== 1) ?  (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED DONOR SEX IS INVALID" )) : "";
     
     //TODO - CHECK CX/RX/SOGI VALUES
     
          
     (  trim($pdta['targetindvalue']) === "N" &&  trim($pdta['notrcvdnote']) === ""  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFYING AN ENCOUNTER WAS NOT RECEIVED, YOU MUST SUPPLY A REASON" )) : "";
     //The 'Last Four' field is Optional
     
     if ( $errorInd === 0 ) { 
         //SAVE ENCOUNTER TO HISTORY
         $copySQL = "insert into four.tmp_ORListing_history SELECT * FROM four.tmp_ORListing where location = :loc and PXICode = :eid";
         $copyRS = $conn->prepare($copySQL);
         $copyRS->execute(array(':loc' => $presUserInst, ':eid' => $eid));
//         $dta[] = $copyRS->debugDumpParams();
         if ( $copyRS->rowCount() > 0) { 
            //TODO:  FIX AGE UOM Value
            $updSQL = <<<UPDSQL
                    update four.tmp_ORListing 
                        set targetInd = :target
                            , infcInd = :infc
                            , pxiAge = :pxage
                            , ageuomcode = :pxageuom
                            , pxiRace = :pxrace
                            , pxiSex = :pxsex
                            , lastfourmrn = :lastfour
                            , studysubjectnbr = :ssbjctnbr
                            , studyprotocolnbr = :sprotnbr
                            , chemotherapyind = :cxind
                            , radiationind = :rxind
                            , upennsogi = :sogi
                            , lastupdatedby = :lastby
                            , lastupdateon = now() 
                            where location = :presInst and PXICode = :pxicode
UPDSQL;
            $updR = $conn->prepare($updSQL);
            //TODO:  Make this dynamic
            switch ($pdta['targetindvalue']) { 
                case 'T': 
                    $tg = 'T'; 
                    break; 
                case 'N':
                    $tg = 'N'; 
                break;
                default:
                    $tg = "";
            }

//{"encyeid":"d2hsSi9MU2MxaHdOS2Y1dkZja0hyczlhdWptTmdubEVheFRPWm52YUltSTNVWWJhTjNkRzNSVFZ1Y01abEd5WA==","sessid":"mm6ka3ndrakkmeth8vblt60sh1","institution":"HUP","targetind":"TARGET","targetindvalue":"T","informedind":"PENDING","informedindvalue":"1","age":"61","ageuom":"Years","ageuomvalue":"Years","race":"American Indian","racevalue":"American Indian","sex":"Male","sexvalue":"Male","lastfour":"9228","notrcvdnote":""}
//TODO:  Make the CX/RX/SOGI VALUES BELOW DYNAMIC
            $updR->execute(array( 
                            ':target' => $tg 
                           ,':infc' => $pdta['informedindvalue']
                           ,':pxage' => $pdta['age']
                           ,':pxageuom' => $pdta['ageuomvalue']
                           ,':pxrace' => $pdta['racevalue']
                           ,':pxsex' => substr($pdta['sex'],0,1)
                           ,':lastfour' => $pdta['lastfour']
                           ,':ssbjctnbr' => trim($pdta['subjectnbr'])
                           ,':sprotnbr' => trim($pdta['protocolnbr'])
                           ,':cxind' => ( trim($pdta['cx']) === "") ? "Unknown" : trim($pdta['cx'])
                           ,':rxind' => ( trim($pdta['rx']) === "") ? "Unknown" : trim($pdta['rx'])
                           ,':sogi' => ( trim($pdta['sogi']) === "") ? "NO DATA" : trim($pdta['sogi']) 
                           ,':lastby' => $oAccount
                           ,':presInst' => $presUserInst
                           ,':pxicode' => $eid ));
            
            if ($tg === 'N') { 
                $noteInsSQL = "insert into  four.tmp_ORListing_casenotes (donorphiid, notetype, notetext, dspind, bywho, onwhen)  values (:donorphiid, :notetype, :notetext, 1, :bywho, now() ) "; 
                $noteInsR = $conn->prepare($noteInsSQL); 
                $noteInsR->execute(array(':donorphiid' => $eid, ':notetype' => 'Reason Not Received',  ':notetext' => $pdta['notrcvdnote'], ':bywho' => $oAccount ));
            }
           $responseCode = 200; 
         } else { 
             (list( $errorInd, $msgArr[] ) = array(1 , $copyRS->rowCount() ));
         }
     }
     
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }

   function saveencounternote( $request, $passdata ) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      

     $eid = cryptservice($pdta['encyeid'],'d');
     $institution = $pdta['institution'];
     $sess = $pdta['sessid'];
     $locsess = session_id();
     $notetype = $pdta['notetype'];
     $notetext = $pdta['ecnote'];

     //DATA CHECKS: Check Encounter NOT Blank/Exists; Check Encounter is AT institution; User By Session is AT institution; notetype is on the menu listing; notetext is not blank 
     //( 1 === 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "{$eid}")) : "";
     ( trim($eid) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ENCOUNTER DBID IS MISSING.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER.")) : "";
     ( trim($institution) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ENCOUNTER INSTITUTION IS MISSING.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER.")) : "";
     ( trim($sess) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "USER SESSION IS MISSING. CLOSE YOUR BROSWER AND RE-LOGIN")) : "";
     ( trim($notetype) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "TO SAVE AN ENCOUNTER NOTE, YOU MUST SPECIFY A NOTE TYPE")) : "";
     ( trim($notetext) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "TO SAVE AN ENCOUNTER NOTE, YOU MUST TYPE A NOTE")) : "";


     ( $sess !== $locsess ) ? (list( $errorInd, $msgArr[] ) = array(1 , "No No No - Bad Boys and Girls!")) : "";
     //Check USER
     $usrSQL = "SELECT displayname, originalaccountname, presentinstitution FROM four.sys_userbase where sessionid = :sess AND TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0 and allowProc = 1";
     $usrRS = $conn->prepare($usrSQL); 
     $usrRS->execute(array(':sess' => $sess)); 
     ( $usrRS->rowCount() < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT LISTED AS A PROCUREMENT TECH, LISTED AS PRESENTLY AT THIS LOCATION OR HAS AN EXPIRED PASSWORD.")) : "";

     if ($usrRS->rowCount() === 1) { 
        $usr = $usrRS->fetch(PDO::FETCH_ASSOC); 
        $presUserInst = $usr['presentinstitution'];
        $oAccount = "{$usr['displayname']} ({$usr['originalaccountname']})";
     }

     //CHECK ENCOUNTER EXISTS
     //TODO:CHANGE TIME RANGE TO LESS THAN 7  --- FOR REAL SCHEDULES
     $encounterExistsSQL = "SELECT count(1) encountercount FROM four.tmp_ORListing where pxicode = :encounterid and location = :institution and TIMESTAMPDIFF(DAY, listdate, now()) < 365";
     $encounterRS = $conn->prepare($encounterExistsSQL); 
     $encounterRS->execute(array(':encounterid' => $eid, ':institution' => $presUserInst)); 
     //$encounterRS->execute(array(':encounterid' => $eid, ':institution' => $institution)); 
     ( $encounterRS->rowCount() < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "ENCOUNTER DOES NOT EXIST IN THE DATABASE")) : "";

     //CHECK MENU OPTION
     $mnuSQL = "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, mnu.dspValue FROM four.sys_master_menus mnu where menu = :menuToQuery and dspInd = 1 and ucase(trim(dspvalue)) = :notetype";
     $mnuRS = $conn->prepare($mnuSQL);
     $mnuRS->execute(array(':menuToQuery' => 'caseNotesType', ':notetype' => strtoupper($notetype)));  
     ( $mnuRS->rowCount() < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE TYPE OF NOTE SPECIFIED IS NOT VALID")) : "";
       
    
     if ($errorInd === 0) { 
       $insSQL = "insert into four.tmp_ORListing_casenotes (donorphiid, notetype, notetext, dspind, bywho, onwhen)  values(:encounterid, :notetype, :notetext, 1, :bywho, now())";
       $insRS = $conn->prepare($insSQL); 
       $insRS->execute(array(':encounterid' => $eid, ':notetype' => $notetype, ':notetext' => $notetext, ':bywho' => $oAccount));       
       $responseCode = 200;
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }

   function pathologyreportcoordinatoredit( $request, $passdata ) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      
     $sessid = session_id();
     $usrpin = chtndecrypt($pdta['usrpin'], true);

     //DATA CHECKS 
     //TODO: MAKE SURE ALL MENU VALUES ARE VALID 
     ( trim($usrpin) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST ENTER YOUR OVERRIDE PIN TO SAVE THIS DOCUMENT")) : "";
     ( !$pdta['hipaacert'] ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST CERTIFY THAT THERE IS NO HIPAA INFORMATION IN THE TEXT BY CLICKING THE HIPAA CERTIFY CHECK BOX")) : "";
     ( $pdta['sess'] !== $sessid ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOUR SESSION DOESN'T MATCH THE PASSED SESSION.  LOG BACK INTO SCIENCESERVER")) : "";
     ( $pdta['labelNbr'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SPECIFIED A BIOGROUP SEGMENT LABEL.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
     ( $pdta['bg'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SPECIFIED A BIOGROUP.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
     ( $pdta['user'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SCIENCESERVER DOES NOT RECOGNIZE YOUR USER NAME OR USER NAME IS MISSING")) : "";
     ( trim($pdta['prtxt']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST SPECIFY SOME HIPAA REDACTED PATHOLOGY REPORT TEXT")) : "";
     ( trim($pdta['editreason']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU ARE EDITING A PATHOLOGY REPORT DOCUMENT.  YOU MUST PROVIDE A REASON FOR DOING SO.")) : "";

     if ( $errorInd === 0 ) { 
        //CHECK USER
        $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and originalaccountname = :userid and sessionid = :sessid and (allowInd = 1 and allowlinux = 1 and allowCoord = 1) and inventorypinkey = :pinkey and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
        $rs = $conn->prepare($chkUsrSQL); 
        $rs->execute(array(':userid' => $pdta['user'], ':sessid' => $sessid, ':pinkey' => $usrpin));
        if ($rs->rowCount() === 1) { 
          $usrrecord = $rs->fetch(PDO::FETCH_ASSOC);
        } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER ({$pdta['user']}) INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
        }
     }  

     //COPY ORIGINAL REPORT 
     $copySQL = "insert into masterrecord.qcpathreports_history(prid, selector, pathreport, pxiid, uploadedBy, uploadedon, biospecimen, procedureMark, dnpr_nbr, deleteInd, div_code, lastedited, lasteditby, reasonforedit) select prid, selector, pathreport, pxiid, uploadedBy, uploadedon, biospecimen, procedureMark, dnpr_nbr, deleteInd, div_code, lastedited, lasteditby, reasonforedit from masterrecord.qcpathreports where prid = :prid"; 
     $copyRS = $conn->prepare($copySQL); 
     $copyRS->execute(array(':prid' => $pdta['prid']));
     $rowsCopied = $copyRS->rowCount();   
     if ($rowsCopied < 1) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "DATABASE WAS UNABLE TO MAKE A COPY OF PATHOLOGY REPORT.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER."));
     }

     //SAVE EDITS
     if ($errorInd === 0) {  
       $htmlized = preg_replace('/\n\n/','<p>',$pdta['prtxt']);
       $htmlized = preg_replace('/\r\n/','<p>', $htmlized);
       $htmlized = preg_replace('/\n/','<br>',$htmlized);

       $editSQL = "update masterrecord.qcpathreports set pathreport = :prtxt, lastedited = now(), lasteditby = :lasteditby, pxiid = :pxiid, biospecimen = :biospecimennbr, reasonforedit = :editreason where prid = :prid";
       $editRS = $conn->prepare($editSQL); 
       $editRS->execute(array(':prid' => $pdta['prid']
                             ,':prtxt' => "{$htmlized}"
                             ,':lasteditby' => $usrrecord['originalaccountname']
                             ,':pxiid' => $pdta['pxiid']
                             ,':biospecimennbr' => $pdta['bg']
                             ,':editreason' => $pdta['editreason']
                         ));
       $responseCode = 200;
     }

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }

   function preprocesspathologyrptedit($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      
     $sessid = session_id();

     $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and sessionid = :sessid and (allowInd = 1 and allowlinux = 1 and allowCoord = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid));
     if ($rs->rowCount() === 1) { 
       $usrrecord = $rs->fetch(PDO::FETCH_ASSOC);
     } else { 
       (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
     }

     if ($errorInd === 0) { 
       $prid = cryptservice( $pdta['docid'], 'd');
       
       $pathrptSQL = "select qcpr.prid, qcpr.pathreport,  bs.read_Label as bgs, bs.pbiosample, bs.pathReport, ifnull(bs.pathreportid,0) as pathreportid, concat(trim(ifnull(bs.anatomicSite,'')), if(trim(ifnull(bs.tissType,''))='','', concat(' (',trim(ifnull(bs.tissType,'')),')'))) site, concat(trim(ifnull(bs.diagnosis, '')), if(trim(ifnull(bs.subdiagnos,''))='','', concat( ' :: ' , trim(ifnull(bs.subdiagnos,''))))) as diagnosis, ifnull(bs.procureInstitution,'') as procinstitution, ifnull(date_format(bs.procedureDate,'%m/%d/%Y'),'') as proceduredate, concat( if(trim(ifnull(bs.pxiAge,'')) = '', '-',trim(ifnull(bs.pxiAge,''))),'::',if(trim(ifnull(bs.pxiRace,''))='','-',ucase(trim(ifnull(bs.pxiRace,'')))),'::',if(trim(ifnull(bs.pxiGender,'')) = '','-',ucase(trim(ifnull(bs.pxiGender,''))))) ars, ifnull(bs.pxiid,'NOPXI') as pxiid from masterrecord.qcpathreports qcpr left join masterrecord.ut_procure_biosample bs on qcpr.prid = bs.pathreportid where qcpr.prid = :prid";
       $pathrptRS = $conn->prepare($pathrptSQL); 
       $pathrptRS->execute(array(':prid' => $prid));
       if ( $pathrptRS->rowCount() === 1 ) {  
            $r = $pathrptRS->fetch(PDO::FETCH_ASSOC);
            $pdta['pbiosample'] = $r['pbiosample'];
            $pdta['labelnbr'] = $r['bgs'];
            $pdta['user'] = $usrrecord['originalaccountname'];
            $pdta['sessionid'] = $sessid;
            $pdta['pathreportind'] = $r['pathReport'];
            $pdta['pathreportid'] = $r['pathreportid'];
            $pdta['site'] = $r['site'];
            $pdta['diagnosis'] = $r['diagnosis'];
            $pdta['procinstitution'] = $r['procinstitution'];
            $pdta['proceduredate'] = $r['proceduredate'];
            $pdta['ars'] = $r['ars'];
            $pdta['pxiid'] = $r['pxiid'];
            $pdta['pathologyrpt'] = "{$r['pathreport']}";
            $pdta['prid'] = $r['prid']; 
            $dta = array('pagecontent' => bldDialogGetter('dataCoordEditPR', $pdta) ); 
            $responseCode = 200;
       } else { 
         (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  PATHOLOGY REPORT NOT FOUND IN DATABASE."));
       }
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }  

   function pathologyreportuploadoverride($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400;
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     session_start();      
     $sessid = session_id();
     $usrpin = chtndecrypt($pdta['usrpin'], true);
     $px = $pdta['pxiid'];
     //DATA CHECKS  
     ( trim($usrpin) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST ENTER YOUR OVERRIDE PIN TO SAVE THIS DOCUMENT")) : "";
     ( !$pdta['hipaacert'] ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST CERTIFY THAT THERE IS NO HIPAA INFORMATION IN THE TEXT BY CLICKING THE HIPAA CERTIFY CHECK BOX")) : "";
     ( $pdta['sess'] !== $sessid ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOUR SESSION DOESN'T MATCH THE PASSED SESSION.  LOG BACK INTO SCIENCESERVER")) : "";
     ( $pdta['labelNbr'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SPECIFIED A BIOGROUP SEGMENT LABEL.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
     ( $pdta['bg'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SPECIFIED A BIOGROUP.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER")) : "";
     ( $pdta['user'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SCIENCESERVER DOES NOT RECOGNIZE YOUR USER NAME OR USER NAME IS MISSING")) : "";
     ( trim($pdta['prtxt']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU MUST SPECIFY SOME HIPAA REDACTED PATHOLOGY REPORT TEXT")) : "";
     ( $pdta['deviation'] === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU ARE DEVIATING FROM CHTNEASTERN SOPs AND MUST THEREFORE SPECIFY A REASON")) : "";

     if ( $errorInd === 0 ) { 
        //CHECK USER
        $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and emailaddress = :userid and sessionid = :sessid and (allowInd = 1 and allowlinux = 1 and allowCoord = 1) and inventorypinkey = :pinkey and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
        $rs = $conn->prepare($chkUsrSQL); 
        $rs->execute(array(':userid' => $pdta['user'], ':sessid' => $sessid, ':pinkey' => $usrpin));
        if ($rs->rowCount() === 1) { 
          $usrrecord = $rs->fetch(PDO::FETCH_ASSOC);
        } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
        }
     }  

     if ( $errorInd === 0 ) { 
       //IF ERROR IS STILL ZERO - THEN WRITE PR
       $htmlized = preg_replace('/\n\n/','<p>',$pdta['prtxt']);
       $htmlized = preg_replace('/\r\n/','<p>', $htmlized);
       $htmlized = preg_replace('/\n/','<br>',$htmlized);
       $selector = strtoupper(generateRandomString(8));

       $insSQL = "insert into masterrecord.qcpathreports(selector, pathreport, pxiid, uploadedby, uploadedon, biospecimen, dnpr_nbr) values(:selector, :pathreport, :pxiid, :uploadedby, now(), :biospecimen, :dnpr_nbr)";
       $insR = $conn->prepare($insSQL); 
       $insR->execute(array(":selector" => $selector,":pathreport" => "{$htmlized}",":pxiid" => $px,":uploadedby" => $usrrecord['originalaccountname'],":biospecimen" => $pdta['bg'],":dnpr_nbr" => $pdta['labelNbr'] ));
       $prid = $conn->lastInsertId();
       $bioUpd = "update masterrecord.ut_procure_biosample set pathreport = 1, pathreportid = :prid where pbiosample = :bg"; 
       $bioUpdR = $conn->prepare($bioUpd); 
       $bioUpdR->execute(array(':prid' => $prid, ':bg' => $pdta['bg']));
       $responseCode = 200;
     }

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   } 

   function anondonorobject($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400; 
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     $donorid = cryptservice( $pdta['donorid'] , 'd' );
     $requestedInstitution = $pdta['presentinstitution']; 
     $session = $pdta['sessionid'];

     $donorSQL = <<<SQLSTMT
    SELECT pxicode
          , ifnull(date_format(listdate,'%m/%d/%Y'),'') listdate
          , ifnull(location,'') as location
          , ifnull(inst.institution,'') as dspinstitution
          , ifnull(starttime,'') as starttime
          , ifnull(surgeons,'') as surgeon
          , ifnull(pxiini,'') as donorinitials
          , ifnull(lastfourmrn,'') as lastfour
          , ifnull(pxiage,'') as donorage
          , ifnull(ageuom.dspValue,'') donorageuom
          , ifnull(pxirace,'') as donorrace
          , ifnull(pxisex,'') as donorsex
          , ifnull(proctext,'') as proctext
          , ifnull(targetind,0) as targetind
          , ucase(ifnull(trg.dspvalue,'')) as targetdsp
          , if(ifnull(infcind,1)=0,1, ifnull(infcind,1))as infcind
          , ic.menuvalue as icdsp
          , ifnull(orl.studysubjectnbr,'') as studysubjectnbr
          , ifnull(orl.studyprotocolnbr,'') as studyprotocolnbr   
          , ifnull(orl.chemotherapyind,'') as cx
          , ifnull(orl.radiationind,'') as rx 
          , ifnull(orl.upennsogi,'') as sogi   
          , ifnull(linkeddonor,'') as linkeddonorind
          , ifnull(linkby,'') as linkby
          , ifnull(delinkeddonor,'') as delinkeddonorind
          , ifnull(delinkby,'') as delinkedby 
    FROM four.tmp_ORListing orl 
    left join (SELECT menuvalue, ifnull(longvalue, dspvalue) as institution FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on orl.location = inst.menuvalue
    left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'ageuom') ageuom on orl.ageuomcode = ageuom.menuvalue 
    left join (SELECT menuvalue, dspvalue, useasdefault, menuvalue as lookupvalue FROM four.sys_master_menus where menu = 'pxTARGET') as trg on orl.targetind = trg.menuvalue
    left join (SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ucase(ifnull(mnu.dspvalue,'')) as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where menu = 'infc') ic on if(ifnull(orl.infcind,1)=0,1, ifnull(orl.infcind,1)) = ic.codevalue
    where pxicode = :donorid and location = :usrpresentloc
SQLSTMT;
     $rs = $conn->prepare($donorSQL); 
     $rs->execute(array(':donorid' => $donorid, ':usrpresentloc' => $requestedInstitution)); 
 
     if ($rs->rowCount() === 1) { 
       $r = $rs->fetch(PDO::FETCH_ASSOC);

       $dta['pxicode'] = $r['pxicode'];
       $dta['listdate'] = $r['listdate'];
       $dta['location'] = $r['location'];
       $dta['locationname'] = $r['dspinstitution'];
       $dta['starttime'] = $r['starttime'];
       $dta['surgeons'] = $r['surgeon']; 
       $dta['donorinitials'] = $r['donorinitials'];
       $dta['lastfour'] = $r['lastfour'];
       $dta['donorage'] = $r['donorage'];
       $dta['ageuom'] = $r['donorageuom'];
       $dta['donorrace'] = $r['donorrace'];
       $dta['donorsex'] = $r['donorsex'];
       $dta['proctext'] = $r['proctext']; 
       $dta['targetind'] = $r['targetind'];
       $dta['targetdsp'] = $r['targetdsp'];
       $dta['informedind'] = $r['infcind']; 
       $dta['informeddsp'] = $r['icdsp']; 
       $dta['studysubjectnbr'] = $r['studysubjectnbr'];
       $dta['studyprotocolnbr'] = $r['studyprotocolnbr'];
       $dta['studyprotocolnbr'] = $r['studyprotocolnbr'];
       $dta['cx'] = $r['cx'];
       $dta['rx'] = $r['rx'];
       $dta['sogi'] = $r['sogi'];
       $dta['linkeddonor'] = $r['linkeddonorind']; 
       $dta['delinkeddonor'] = $r['delinkeddonorind'];
       $dta['delinkeddonorby'] = $r['delinkedby'];

       $noteSQL = "SELECT notetype, notetext, bywho, date_format(onwhen, '%m/%d/%Y %H:%i') as enteredon FROM four.tmp_ORListing_casenotes where donorphiid = :donorid and dspind = 1 order by enteredon desc";
       $noteRS = $conn->prepare($noteSQL); 
       $noteRS->execute(array(':donorid' => $donorid)); 
       $notes = array(); 
       while ($noteR = $noteRS->fetch(PDO::FETCH_ASSOC)) { 
         $notes[] = $noteR;
       }
       $dta['casenotes'] = $notes;

       $responseCode = 200; 
       $msg = "";
       $itemsfound = 1;
     } else { 
       $responseCode = 404; 
       $msg = "DONOR NOT FOUND";
     }
     //$dta = $donorid ;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }

   function alldownstreamdiagnosis($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400; 
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
       $sql = "SELECT distinct dxid, replace(ifnull(diagnosis,''),'\\\\','::') diagnosis FROM four.sys_master_menu_vocabulary where trim(ifnull(dxid,'')) <> '' order by diagnosis";     
       $rs = $conn->prepare($sql); 
       $rs->execute(); 
       if ($rs->rowCount() > 0) { 
         $itemsfound = $rs->rowCount(); 
         while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
           $dta[] = $r;
         }
         $responseCode = 200;
         $msg = "";
      } else { 
       $responseCode = 404; 
       $msg = "NO SITES FOUND";
     }
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }
    
   function diagnosismetsdownstream($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400; 
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     //TODO: CHECK THAT A VALUE IS PASSED
     $steid = $pdta['siteid']; 
     if ( trim($steid) !== "" ) {
     $sql = "SELECT distinct ifnull(dxid,'') as dxid, replace(ifnull(diagnosis,''),'\\\\','::') as diagnosis FROM four.sys_master_menu_vocabulary where catid = :spccat and siteid  = :givensite order by diagnosis";     
     $rs = $conn->prepare($sql); 
     $rs->execute(array(':givensite' => $steid , ':spccat' => 'C6' )); 
     if ($rs->rowCount() > 0) { 
       $itemsfound = $rs->rowCount(); 
       while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
           $dta[] = $r;
       }
       $responseCode = 200;
       $msg = "";
     } else { 
       $responseCode = 404; 
       $msg = "NO SITES FOUND";
     }
     }

     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   }

   function diagnosisdownstream($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400; 
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     //TODO: CHECK THAT VALUES WERE PASSED
     $spc = $pdta['specimencategory'];
     $ste = $pdta['site'];  

     if ( trim($spc) !== "" && trim($ste) !== "" ) {
     $sql = "SELECT distinct ifnull(dxid,'') as dxid, replace(ifnull(diagnosis,''),'\\\\','::') as diagnosis FROM four.sys_master_menu_vocabulary where specimenCategory = :spc and siteid  = :givensite order by diagnosis";     
     $rs = $conn->prepare($sql); 
     $rs->execute(array(':spc' => $spc, ':givensite' => $ste)); 
     if ($rs->rowCount() > 0) { 
       $itemsfound = $rs->rowCount(); 
       while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
           $dta[] = $r;
       }
       $responseCode = 200;
       $msg = "";
     } else { 
       $responseCode = 404; 
       $msg = "NO SITES FOUND";
     }
     }

     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                        
   } 

   function subsitesbyspecimencategory($request, $passdata) { 
     $rows = array(); 
     $dta = array(); 
     $responseCode = 400; 
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata,true);
     $spc = $pdta['specimencategory'];
     $ste = $pdta['site'];
   
     $sql = "SELECT distinct ifnull(subsid,'') as ssiteid, ifnull(subsite,'') as subsite FROM four.sys_master_menu_vocabulary where specimenCategory = :specimencategory and trim(ifnull(site,''))  = :site and (trim(ifnull(subsite,'')) <> 'NONE' and trim(ifnull(subsite,'')) <> '') order by subsite";     
     $rs = $conn->prepare($sql); 
     $rs->execute(array(':specimencategory' => $spc, ':site' => $ste)); 
     if ($rs->rowCount() > 0) { 
       $itemsfound = $rs->rowCount(); 
       while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
           $dta[] = $r;
       }
       $responseCode = 200;
       $msg = "";
     } else { 
       $responseCode = 404; 
       $msg = "NO SITES FOUND";
     }
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;          
   }

   function  sitesbyspecimencategory($request, $passdata) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $pdta = json_decode($passdata,true);
   $spc = $pdta['specimencategory'];
   
   $sql = "SELECT distinct ifnull(siteid,'') as siteid, ifnull(site,'') as site, ifnull(pathrptrequiredvalue,2) as pathrptrequiredvalue FROM four.sys_master_menu_vocabulary where specimenCategory = :specimencategory and trim(ifnull(site,'')) <> '' order by site";     
   $rs = $conn->prepare($sql); 
   $rs->execute(array(':specimencategory' => $spc)); 
   if ($rs->rowCount() > 0) { 
       $itemsfound = $rs->rowCount(); 
       while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
           $dta[] = $r;
       }
       $responseCode = 200;
       $msg = "";
   } else { 
       $responseCode = 404; 
       $msg = "NO SITES FOUND";
   }
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;          
  }
    
   function procurementdiagnosisdesignationsearch($request, $passdata) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $pdta = json_decode($passdata,true);
   $spc = $pdta['speccat'];
   $keywords = preg_split("/[\s,]+/", trim($pdta['searchterm']));
   $trmarr = array();
   $rtnData = array(); 
   //TODO:  MAKE SURE THAT SEARCH TERM IS MORE THAN 3 CHARACTERS - ERROR CHECK THE SEARCH REQUEST
   $sql = "SELECT dxd, vocabularyversionnbr FROM four.voc_dxd_search where 1=1 ";
   for ($i = 0; $i < count($keywords); $i++) {
       $sqladd .= ($i === 0 ) ? " ( dxd like :trm{$i} ) " : " and ( dxd like :trm{$i} ) ";
       $trmarr[":trm{$i}"] = ( $i === 0 ) ? "%{$keywords[$i]}%" : "%{$keywords[$i]}%";
   }
   $sqladd .= " and ( dxd like :trm{$i} ) ";
   $trmarr[":trm{$i}"] = "%.. {$spc}%";
   $sql .= " and ({$sqladd}) order by dxd";
   $rs = $conn->prepare($sql); 
   $rs->execute($trmarr); 
   if ( $rs->rowCount() > 0 ) {
     $itemsfound = $rs->rowCount();
     $itemCount = 0;
     while ($r = $rs->fetch(PDO::FETCH_ASSOC)) {
        $rtnTerm = preg_split("/ .. /", $r['dxd']);
        $dta[$itemCount]['site'] = (trim($rtnTerm[0]) !== "") ?  strtoupper(trim($rtnTerm[0])) : "";
        $dta[$itemCount]['dx'] = (trim($rtnTerm[2]) !== "") ?  strtoupper(trim($rtnTerm[2])) : "";
        $dta[$itemCount]['sdx'] = (trim($rtnTerm[3]) !== "") ?  strtoupper(trim($rtnTerm[3])) : "";
        $dta[$itemCount]['specimencategory'] = (trim($rtnTerm[1]) !== "") ?  strtoupper(trim($rtnTerm[1])) : "";
        $dta[$itemCount]['vocabVersionNbr'] = $r['vocabularyversionnbr'];
        $itemCount++;
     }
     //$dta[] = $rtnData;
     $msg = "";
     $responseCode = 200;
   } else { 
     //NONE FOUND
   }
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;    
 } 

   function generatesubmenu($request, $passdata) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $pdta = json_decode($passdata,true);
   //{"whichdropdown":"PRCCollectionType","whichmenu":"COLLECTIONT","lookupvalue":"S"}
   $dspmenu = $pdta['whichdropdown'];
   $rqstedMenu = $pdta['whichmenu'];
   $lookupvalue = $pdta['lookupvalue'];

   //TODO:  CHECK THAT VALUES ARE VALID

   $sql = "SELECT  menuvalue, dspvalue, useasdefault, menuid as lookupvalue FROM four.sys_master_menus where dspind = 1 and  menu = :rqstmenu and parentvalue = :givenlookupvalue order by dsporder";
   $rs = $conn->prepare($sql);
   $rs->execute(array( ':rqstmenu' => $rqstedMenu, ':givenlookupvalue' => $lookupvalue ));
   $itemsfound = $rs->rowCount();
   while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
     $dta[] = $r;
   }
   //$dta['dspmenu'] = $dspmenu;
   $msg = $dspmenu;
   $responseCode = 200;

   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;    
 } 

   function grabreportdata($request, $passdata) { 
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      require(serverkeys . "/sspdo.zck");  
      //TODO:  REINSERT SECURITY!
//      session_start();        
//      $usrSQL = "SELECT originalAccountName, allowcoord, accessnbr FROM four.sys_userbase where sessionid = :sessionid";
//      $usrR = $conn->prepare($usrSQL);
//      $usrR->execute(array(':sessionid' =>session_id()));
//      if ($usrR->rowCount() < 1) { 
//        $msgArr[] = "SESSION KEY IS INVALID (" . session_id() . ").  LOG OUT OF SCIENCESERVER AND LOG BACK IN"; 
//        $error = 1;
//        $responseCode = 401;
//      } else { 
//        $u = $usrR->fetch(PDO::FETCH_ASSOC);
//      } 
//      if ( ($u['accessnbr'] < $pdta['DATA']['rqaccesslvl']) || ((int)$u['allowcoord'] <> 1)) {
//        $msgArr[] = "USER NOT ALLOWED FUNCTION"; 
//        $error = 1;
//        $responseCode = 401;
//      }

      if ($error === 0) { 

          
        $rqjson = json_decode($pdta['DATA']['requestjson'], true);
       
        if ( count($rqjson['request']['wherelist']) < 1) { 
          $msgArr[] = "NO PARAMETER/CRITERIA WAS SPECIFIED IN WHERE CLAUSE - SEE CHTNED IT STAFF FOR ASSISTANCE";
          $msg = $msgArr;
        } else { 
            
          $select = $rqjson['request']['rptsql']['selectclause'];
          $from = $rqjson['request']['rptsql']['fromclause'];
          $orderby =  (trim($rqjson['request']['rptsql']['orderby']) === '') ? '' :  " ORDER BY {$rqjson['request']['rptsql']['orderby']}";
          $where = "where 1=1 ";
          foreach ($rqjson['request']['wherelist'] as $val) { 
            $where .= " and ({$val}) ";
          }
          //TODO:  ADD THESE COMPONENTS IN TO SQL
         $groupby =  (trim($rqjson['request']['rptsql']['groupbyclause']) === '') ? '' : " GROUP BY {$rqjson['request']['rptsql']['groupbyclause']}";
//        $summaryfield = $r['rptsql']['summaryfield'];

          $sqlstmt = "SELECT  {$select}  FROM  {$from}  {$where}  {$groupby}  {$orderby}";
          $valuelist = $rqjson['request']['valuelist'];
          $rs = $conn->prepare($sqlstmt); 
          $rs->execute($valuelist);
          $itemsfound = $rs->rowCount();  
          while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
            $dta[] = $r;
          }
          //$dta = $sqlstmt . " " .  json_encode($valuelist);
          $responseCode = 200;
          $msg = "";
        }
      } else { 
        $msg = $msgArr;
      }
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function createreportobj($request, $passdata) { 
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      require(serverkeys . "/sspdo.zck");  
      foreach ($pdta['request']['valuelist'] as $key => $value) { 
        if (trim($value) === "") {     
          $msgArr[] .= "Selected criteria ({$key}) must have a specified value";
          $error = 1;
        }
      }

      $rpt = explode("/",$pdta['request']['requestedreporturl']);
      if (count($rpt) === 2) { 
      $rptsqlSQL = "SELECT ifnull(selectClause,'') as selectclause, ifnull(fromClause,'') as fromclause, ifnull(summaryfield,'') as summaryfield, ifnull(groupbyClause,'') as groupbyclause, ifnull(orderbyClause,'') as orderby, ifnull(accesslvl,100) as accesslevel, ifnull(allowgriddsp,0) as allowgriddsp, ifnull(allowpdf,0) as allowpdf FROM four.ut_reportlist where urlpath = :rpturl";
      $rptsqlRS = $conn->prepare($rptsqlSQL); 
      $rptsqlRS->execute(array(':rpturl' => $rpt[1])); 
      if ($rptsqlRS->rowCount() <> 1) { 
        $error = 1; 
        $msgArr[] = "MAL-FORMED REPORT URL - SEE A CHTNED IT PERSON";
      } else { 
        $rptsql = $rptsqlRS->fetch(PDO::FETCH_ASSOC);
        $pdta['request']['rptsql'] = $rptsql;
      }
      } else { 
        $error = 1; 
        $msgArr[] = "REPORT URL IN REQUEST DOES NOT CONTAIN ALL COMPONENTS"; 
      }

      session_start();        
      $usrSQL = "SELECT originalAccountName, allowcoord, accessnbr FROM four.sys_userbase where sessionid = :sessionid";
      $usrR = $conn->prepare($usrSQL);
      $usrR->execute(array(':sessionid' =>session_id()));
      if ($usrR->rowCount() < 1) { 
        $msgArr[] = "SESSION KEY IS INVALID.  LOG OUT OF SCIENCESERVER AND LOG BACK IN"; 
        $error = 1;
        $responseCode = 401;
      } 
      //TODO:CHECK ACCESS LEVEL and COORDINATOR ALLOWED
      //TODO:CHECK OTHER DATA ELEMENTS (PROPER DATES, NUMBERS, ETC...)
      //TODO:CHECK THAT REQUIRED FIELDS HAVE VALUES AND HAVE NOT SOMEHOW BEEN UNCHECKED

      if ($error === 0) { 
          //SAVE REQUEST
          $objid = strtolower( generateRandomString() );
          $u = $usrR->fetch(PDO::FETCH_ASSOC);
          $insSQL = "insert into four.objsrchdocument (objid, bywho, onwhen, doctype, reportmodule, reportname, requestjson, typeofreportrequested) value (:objid,:whoby,now(),:doctype,:reportmodule,:reportname,:requestjson,:typeofreportrequested)";
          $insR = $conn->prepare($insSQL);
          $insR->execute(array(':objid' => $objid, ':whoby' => $u['originalAccountName'], ':doctype' => 'REPORTREQUEST', ':reportmodule' => $rpt[0], ':reportname' => $rpt[1], ':requestjson' => json_encode($pdta), ':typeofreportrequested'=> $pdta['request']['typeofrequest']));
          $dta['reportobject'] = $objid;
          $dta['reportobjectency'] = cryptservice($objid, 'e');
          $dta['typerequested'] = $pdta['request']['typeofrequest'];
          $dta['reportmodule'] = $rpt[0];
          $dta['reportname'] = $rpt[1];
          $itemsfound = 1;
          $responseCode = 200;                       
      } else { 
        $msg = $msgArr;
      }
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    } 

   function hprworkbenchbuilder($request, $passdata) {  
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      
      if ($pdta['segmentid'] === "" || !$pdta['segmentid'] || !$pdta['pbiosample'] || $pdta['pbiosample'] === "" ) { 
          //BAD REQUEST
          //TODO:  BUILD ERROR RESPONSE
      } else {

        $segData = json_decode(callrestapi("GET", dataTree. "/do-single-segment/" . $pdta['segmentid'],serverIdent, serverpw), true);
        $allSegData = json_decode(callrestapi("GET", dataTree. "/biogroup-segment-short-listing/" . $pdta['segmentid'],serverIdent, serverpw), true);
        $pHPRSegData = json_decode(callrestapi("GET", dataTree. "/past-hpr-by-segment/" . $pdta['segmentid'],serverIdent, serverpw), true);
        require(genAppFiles . "/frame/sscomponent_pagecontent.php");
        $dta['workbenchpage'] = bldHPRWorkBenchSide($segData, $allSegData,$pHPRSegData, $pdta['pbiosample']); 
        $responseCode = 200;
        $msg = $msgArr;        

      }
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function hprworkbenchsidepanel($request, $passdata) {  
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      $srchTrm = $pdta['srchTrm'];
      $sidePanelSQL = "SELECT bs.pbiosample, replace(sg.bgs,'_','') as bgs, sg.biosamplelabel, sg.segmentid, ifnull(sg.prepmethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation, date_format(sg.procurementdate,'%m/%d/%Y') as procurementdate, sg.enteredby as procuringtech, ucase(ifnull(sg.procuredAt,'')) as procuredat, ifnull(inst.dspvalue,'') as institutionname, ucase(concat(concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'')='','',concat('/',ifnull(bs.subsite,'')))), ' ', concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat('/',ifnull(bs.subdiagnos,'')))), ' ' ,if(trim(ifnull(bs.tissType,'')) = '','',concat('(',trim(ifnull(bs.tissType,'')),')')))) as designation FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') inst on sg.procuredAt = inst.menuvalue where 1=1 ";
      $bldSidePanel = 0;
      $typeOfSearch = "";
      switch ($srchTrm) { 
        case (preg_match('/\b\d{1,4}\b/',$srchTrm) ? true : false) :
          $sidePanelSQL .= "and sg.hprboxnbr = :hprboxnbr";
          $qryArr = array(':hprboxnbr' => ('HPRT' . substr(('0000' . $srchTrm),-3)));
          $bldSidePanel = 1;
          $typeOfSearch = "HPR Inventory Tray " . substr(('0000' . $srchTrm),-3);
          break;
        case (preg_match('/\b\d{5}\b/',$srchTrm) ? true : false) :
          $sidePanelSQL .= "and sg.prepMethod = :prpmet and sg.biosamplelabel  = :biogroup and sg.segstatus <> :segstatus"; 
          $qryArr = array(':biogroup' => (int)$srchTrm, ':prpmet' => 'SLIDE', ':segstatus' => 'SHIPPED');
          $bldSidePanel = 1;
          $typeOfSearch = "Biogroup Slides for " . $srchTrm;
          break;         
        case (preg_match('/\bED\d{5}.{1,}\b/i', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and concat('ED',replace(sg.bgs,'_','')) = :edbgs "; 
          $qryArr = array(':edbgs' =>  str_replace('_','',strtoupper($srchTrm)));
          $bldSidePanel = 1;
          $typeOfSearch = "Slide Label Search for " .  $srchTrm;
          break;
        case (preg_match('/\b\d{5}[a-zA-Z]{1,}.{1,}\b/', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and replace(sg.bgs,'_','') = :bgs "; 
          $qryArr = array(':bgs' =>  str_replace('_','',strtoupper($srchTrm)));
          $bldSidePanel = 1;
          $typeOfSearch = "Slide Label Search for " . $srchTrm;
          break;
        case (preg_match('/\bHPRT\d{3}\b/i', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and sg.hprboxnbr = :hprboxnbr "; 
          $qryArr = array(':hprboxnbr' =>  $srchTrm);
          $bldSidePanel = 1;
          $typeOfSearch = "HPR Inventory Tray " . preg_replace('/HPRT/i','',$srchTrm);
          break;
        default:
         //DEFAULT 
      }
      if ($bldSidePanel === 1) {  
        require(serverkeys . "/sspdo.zck");  
        $listRS = $conn->prepare($sidePanelSQL); 
        $listRS->execute($qryArr);

        if ($listRS->rowCount() < 1) { 
          $responseCode = 404;
          $itemsfound = 0; 
          $msgArr[] = "NO ITEMS FOUND MATCHING YOUR CRITERIA ({$srchTrm}) ";
        } else { 
          $itemsfound = $listRS->rowCount();
          $item = 0;
          while ($rs = $listRS->fetch(PDO::FETCH_ASSOC)) { 
            $dta[$item]['bgs']               = $rs['bgs'];
            $dta[$item]['pbiosample']        = $rs['biosamplelabel'];
            $dta[$item]['prepmethod']        = $rs['prepmethod'];
            $dta[$item]['preparation']       = $rs['preparation'];
            $dta[$item]['segmentid']         = $rs['segmentid'];
            $dta[$item]['procurementdate']   = $rs['procurementdate'];
            $dta[$item]['procuringtech']     = $rs['procuringtech'];
            $dta[$item]['institution']       = $rs['procuredat'];
            $dta[$item]['institutionname']   = $rs['institutionname'];
            $dta[$item]['designation']       = $rs['designation'];
            //CHECK FOR FRESH
            $frshSQL = "SELECT count(1) as cnt FROM masterrecord.ut_procure_segment where prepmethod = 'FRESH' and voidind <> 1 and (segstatus = 'SHIPPED' or segstatus = 'ASSIGNED' or segstatus = 'ONOFFFER') and biosamplelabel = :biosamplelabel";
            $frshRS = $conn->prepare($frshSQL); 
            $frshRS->execute(array(':biosamplelabel' => $rs['biosamplelabel']));
            $frsh = $frshRS->fetch(PDO::FETCH_ASSOC); 
            $dta[$item]['freshcount'] = $frsh['cnt'];
            $item++;
          }
          $msgArr[] = $typeOfSearch;
          $responseCode = 200;
        }
      } else { 
        $msgArr[] = "BAD SEARCH STRING";
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

   function inventoryhprtrayoverride($request, $passdata) { 
      $responseCode = 400; 
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata, true);  
      require(serverkeys . "/sspdo.zck");  
      session_start();      
      $sessid = session_id();
      $usrpin = chtndecrypt($pdta['usrPIN'], true);
      $usrChkSQL = "SELECT ifnull(emailaddress,'') as emailaddress, ifnull(originalaccountname,'') as originalaccountname, ifnull(allowind,0) as allowind, ifnull(allowcoord,0) as allowcoord, ifnull(allowinvtry,0) as allowinventory, ifnull(sessionid,'') as sessionid FROM four.sys_userbase where sessionid = :sessid and inventorypinkey = :usrpin and allowind = 1 and allowcoord = 1 and allowinvtry = 1";
      $usrChkR = $conn->prepare($usrChkSQL); 
      $usrChkR->execute(array(':sessid' => $sessid, ':usrpin' => $usrpin));
      if ($usrChkR->rowCount() < 1) { 
          $responseCode = 401;
          $msgArr[] = "USER NOT ALLOWED TO PERFORM THIS INVENTORY ACTION";
      } else {
    
        $usr = $usrChkR->fetch(PDO::FETCH_ASSOC);
        $traydsp = trim($pdta['hprtray']);
        $invscancode = trim($pdta['invscancode']);
        $devreason = trim($pdta['deviationreason']);
        $slidessent = count($pdta['slidelist']);

        if ($traydsp === "") { 
          $error = 1; 
          $msgArr[] = "YOU DID NOT SPECIFY AN HPR TRAY (INVENTORY LOCATION TRAY)";
        }      

        if ($devreason === "") { 
            $error = 1; 
            $msgArr[] = "YOU ARE DEVIATING FROM CHTN EASTERN STANDARD OPERATING PROCEDURES.  YOU MUST SUPPLY A REASON FOR THE DEVIATION";
        }

        if ($invscancode === "") {
          $error = 1; 
          $msgArr[] = "YOU DID NOT SPECIFY AN HPR TRAY (INVENTORY LOCATION SCANCODE MISSING)"; 
        } else { 
          $trayChkSQL = "SELECT * FROM four.sys_inventoryLocations where scancode = :scancode and physicallocationind = 1";
          $trayChkR = $conn->prepare($trayChkSQL); 
          $trayChkR->execute(array(':scancode' => $invscancode));
          if ($trayChkR->rowCount() < 1) { 
            $error = 1; 
            $msgArr[] = "THE HPR TRAY SCANCODE IS MISSING OR IS NOT VALID";               
          }
        }
        
        if ($slidessent < 1) { 
            $error = 1; 
            $msgArr[] = "YOU HAVE NOT SPECIFIED ANY SLIDES TO SEND TO THE QMS PROCESS";
        }

        if ($slidessent > 16) { 
            $error = 1; 
            $msgArr[] = "ONLY 16 SLIDES FIT IN A SLIDE TRAY";
        }

        $apprvSlideCntr = 0;
        $slideListArr = array();
        foreach ($pdta['slidelist'] as $slidekey => $slidevalue) { 
             /************************** 
             * ALL SEGMENT CHECK SHOULD GO HERE IN THIS FOREACH LOOP
             * //0 = BIOGROUP / 1 = NEW QMS STATUS / 2 = SLIDE NUMBER // 3 = SLIDE SEGMENT ID
             ***************************/
             $qmsChk = bgqmsstatus($slidevalue[0]);      
             if ((int)$qmsChk['responsecode'] !== 200) { 
               $error = 1;
               $msgArr[] = "{$slidelist[0]} BIOGROUP NOT FOUND IN QMS STATUS.  SEE A CHTNEASTERN IT STAFF PERSON.";
             } else {
               if (trim($qmsChk['data'][0]['qcprocstatus']) === 'Q') { 
                 $error = 1; 
                 $msgArr[] = "{$slidevalue[0]} IS ALREADY MARKED AS QMS COMPLETE";
               }           
               if (trim($slidevalue[2]) === "") { 
                 $error = 1; 
                 $msgArr[] = "YOU HAVE NOT SPECIFIED A SLIDE TO USE FOR BIOGROUP'S QMS ({$slidevalue[0]})";
               }  
               //TODO:  CHECK THAT SLIDE IS A REAL SLIDE
               switch($slidevalue[1]) { 
                 case 'RESUBMIT': 
                   $qmscondition = 'R';
                   break;
                 case 'SUBMIT':
                    $qmscondition = 'S';
                    break;
                 default:
                   $qmscondition = 'S';
               }
               //TODO: CHECK TO MAKE SURE THE SEGMENT ID IS NOT SHIPPED
               if ($error === 0) { 
                  //Add Segment to dbWriteArray
                  $slideListArr[$apprvSlideCntr]['bg'] = $slidevalue[0];
                  $slideListArr[$apprvSlideCntr]['readlabel'] = $qmsChk['data'][0]['readlabel'];
                  $slideListArr[$apprvSlideCntr]['qmsnew'] = $slidevalue[1];
                  $slideListArr[$apprvSlideCntr]['slidenbr'] = $slidevalue[2];
                  $slideListArr[$apprvSlideCntr]['slideid'] = $slidevalue[3];
                  $slideListArr[$apprvSlideCntr]['qmscondition'] = $qmscondition;
                  $apprvSlideCntr++;
               }         
             }
        }

        //TEST SLIDE [{"bg":"84285","readlabel":"84285T_","qmsnew":"SUBMIT","slidenbr":"84285003","slideid":"445661","qmscondition":"S"}    
        if ($error !== 0) { 
        } else {
           //DO ALL DB WRITING HERE 
           //UPDATE BIOSAMPLE QMSSTATUS 
           $updSQL = "update masterrecord.ut_procure_biosample set qcprocstatus = :newQ, qmsstatusby = :bywho, qmsstatuson = now() where pbiosample = :pbiosample";
           $updR = $conn->prepare($updSQL);  
           //ADD TO HISTORY TABLE
           $hisSQL = "insert into masterrecord.history_procure_biosample_qms(pbiosample, readlabel, qcprocstatus, qmsstatusby,  qmsstatuson, historyrecordon, historyrecordby) values(:pbiosample, :readlabel, :qmsstatus, :qmsstatusby, now(), now(), :historyrecordby)";
           $hisRS = $conn->prepare($hisSQL);
           //MARK SEGMENT WITH NEW LOCATION
           $updSegLocSQL = "update masterrecord.ut_procure_segment set scannedLocation = :dsplocation, scanloccode = :invscancode, scannedStatus = 'INVENTORY-HPRTRAY-OVERRIDE', scannedBy = :usr, scannedDate = now(), toHPR = 1, hprboxnbr = :invscancodea, toHPRBy = :usra, toHPROn = now() where segmentid = :segmentid";
           $segLocRS = $conn->prepare($updSegLocSQL);   
           //COPY TO SEGMENT LOCATION HISTORY TABLE
           $invHisSQL = "insert into masterrecord.history_procure_segment_inventory (segmentid, bgs, scannedlocation, scannedinventorycode, inventoryscanstatus, scannedby, scannedon, historyon, historyby) value(:segmentid, :bgs, :scannedlocation, :scannedinventorycode, :inventoryscanstatus, :scannedby, now(), now(), :historyby)";
           $invHisRS = $conn->prepare($invHisSQL);
           //HPR SUBMISSION TABLE AND HPR HISTORY TABLE
           $hprHisSQL = "insert into masterrecord.history_procure_segment_hprsubmission (segmentid, tohpron, tohprby, historyon, historyby) values(:segmentid, now(), :tohprby, now(), 'HPR-INV-TRAY-OVERRIDE')";
           $hprHisRS = $conn->prepare($hprHisSQL); 
 

          foreach ($slideListArr as $sld) {   
            //ACTUAL DATABASE WRITING HERE
             $updR->execute(array(':newQ' => $sld['qmscondition'], ':bywho' =>$usr['originalaccountname'], ':pbiosample' => $sld['bg'] ));
             $hisRS->execute(array(':pbiosample' => $sld['bg'], ':readlabel' => $sld['readlabel'], ':qmsstatus' => $sld['qmscondition'],':qmsstatusby' => $usr['originalaccountname'] , ':historyrecordby' => 'HPR OVERRIDE TRAY LOAD' ));
             $segLocRS->execute(array(':dsplocation' => $traydsp, ':invscancode' => $invscancode, ':usr' =>$usr['originalaccountname'], ':invscancodea' => $invscancode, ':usra' =>$usr['originalaccountname'], ':segmentid' => $sld['slideid']));
             $invHisRS->execute(array(':segmentid' => $sld['slideid'],':bgs' => $sld['bg'],':scannedlocation' => $traydsp,':scannedinventorycode' => $invscancode,':inventoryscanstatus' => 'HPR-SUBMIT-OVERRIDE',':scannedby' => $usr['originalaccountname'],':historyby' => 'COORDINATOR SCREEN HPR INVENTORY OVERRIDE'));
             $hprHisRS->execute(array(':segmentid' => $sld['slideid'],':tohprby' => $usr['originalaccountname']));
          }
          
          // MARK INVENTORY SLIDE TRAY AS LOCKED OUT
          $traySTSSQL = "update four.sys_inventoryLocations set hprtraystatus = 'SENT', hprtraystatusby = :usr, hprtraystatuson = now() where scancode = :scncode";
          $traySTSRS = $conn->prepare($traySTSSQL);
          $traySTSRS->execute(array(':usr' => $usr['originalaccountname'], ':scncode' => $invscancode)); 
          $tryHisSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, tray, traystatus, historyon, historyby) values(:trayscancode, :tray, :traystatus, now(), :historyby)";
          $tryHisRS = $conn->prepare($tryHisSQL);
          $tryHisRS->execute(array(':trayscancode' => $invscancode, ':tray' => $traydsp, ':traystatus' => 'SENT', ':historyby' => $usr['originalaccountname']));
          $devSQL = "insert into masterrecord.tbl_operating_deviations(module, whodeviated, whendeviated, operationsarea, functiondeviated, reasonfordeviation, payload) value('data-coordination', :whodeviated, now(), 'inventory', 'inventory-hpr-tray-override' , :reasonfordeviation, :payload)";
          $devRS = $conn->prepare($devSQL); 
          $devRS->execute(array(':whodeviated' => $usr['originalaccountname'], ':reasonfordeviation' => $devreason, ':payload' => $passdata));

          $responseCode = 200;
        }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }
 
   function biogrouphprstatus($request, $passdata) { 
      $dta = array(); 
      require(serverkeys . "/sspdo.zck");  
      $pdta = json_decode($passdata,true);
      $pbiosample = cryptservice($pdta['bgency'], 'd'); 
      $qmsSQL = "select bs.pbiosample, ifnull(bs.tisstype,'') as spccat, ifnull(bs.anatomicsite,'') as site, ifnull(bs.diagnosis,'') as dx, substr(ifnull(prr.dspvalue,'No'),1,1) as pathreportdsp, ifnull(bs.qcvalv2,''), ifnull(bs.hprmarkbyon,'') as hprmarkon, ifnull(bs.hprind,0) as hprind, ifnull(bs.qcind,0) as qcind, ifnull(bs.qcmarkbyon,'') as qcmarkbyon, ifnull(bs.qcprocstatus,'') as qcprocstatus, ifnull(qc.dspvalue,'') as qmsvalue, ifnull(bs.hprdecision,'') as hprdecision, ifnull(bs.hprresult,0) as hprresultrecord, ifnull(hprslidereviewed,'') as hprslidereviewed, ifnull(hprby,'') as hprby, ifnull(hpron,'') as hpron from masterrecord.ut_procure_biosample bs left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as prr on bs.pathreport = prr.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus') as qc on bs.qcprocstatus = qc.menuvalue where bs.pbiosample = :pbiosample";
      $qmsR = $conn->prepare($qmsSQL); 
      $qmsR->execute(array(':pbiosample' => $pbiosample));
      if ($qmsR->rowCount() <> 1) {       
        $rows['statusCode'] = 404;
        $dta['bg'] = $pbiosample . " BAD PBIOSAMPLE";
        $msg = "NO BIOGROUP FOUND MATCHING {$pbiosample}";
      } else {    
        $qms = $qmsR->fetch(PDO::FETCH_ASSOC);

        $slideListSQL = "select segmentid, replace(bgs,'T_','') as bgs, if(hprblockind=1,'Y','N') as hprind, ifnull(assignedto,'') as assignedto, if(tohpr=1,'Y','') as tohpr from masterrecord.ut_procure_segment sg where sg.biosampleLabel = :pbiosample and sg.prepmethod = 'SLIDE' and sg.segstatus <> 'SHIPPED'";
        $slideListR = $conn->prepare($slideListSQL); 
        $slideListR->execute(array(':pbiosample' => $pbiosample)); 
        $slideListArr = array();
        while ($sl = $slideListR->fetch(PDO::FETCH_ASSOC)) { 
          $slideListArr[] = $sl;
        }
        $dta['bg'] = $pbiosample;
        $dta['qcprocstatus'] = $qms['qcprocstatus'];
        $dta['qmsvalue'] = $qms['qmsvalue'];
        $dta['desigsite'] = $qms['site'];
        $dta['desigdx'] = $qms['dx'];
        $dta['desigspeccat'] = $qms['spccat'];
        $dta['prpresent'] = $qms['pathreportdsp'];
        $dta['hprdecision'] = $qms['hprdecision'];
        $dta['hprslidereviewed'] = $qms['hprslidereviewed'];
        $dta['slidelist'] = $slideListArr;
        $rows['statusCode'] = 200; 
      }
      $rows['data'] = array('MESSAGE' => '', 'ITEMSFOUND' => 0, 'DATA' => $dta);
      return $rows;
    }

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
                $svePrvSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) values(:segid,:segstatus,:segstatusby,:segstatuson,now(),:enteredby)"; 
                $svePrvR = $conn->prepare($svePrvSQL); 
                $svePrvR->execute(array(':segid' => $v['segmentid'], ':segstatus' => $prvRec['segstatus'], ':segstatusby' => $prvRec['statusby'], ':segstatuson' => $prvRec['statusdate'], ':enteredby' => $u['originalAccountName'])); 
                //SAVE OLD ASSIGNMENT 
                $aInv = (trim($prvRec['assignedto']) === "") ? "NO-INV-ASSIGNMENT" : trim($prvRec['assignedto']); 
                $aPrj = (trim($prvRec['assignedproj']) === "") ? "NO-PROJ-ASSIGNMENT" : trim($prvRec['assignedproj']); 
                $aReq = (trim($prvRec['assignedreq']) === "") ? "NO-REQ-ASSIGNMENT" : trim($prvRec['assignedreq']);
                $aBy = (trim($prvRec['assignedby']) === "") ? "NO-BY-ASSIGNMENT" : trim($prvRec['assignedby']); 
                $prvASQL = "insert into masterrecord.history_procure_segment_assignment(segmentid, previousassignment, previousproject, previousrequest, previousassignmentdate, previousassignmentby, enteredby, enteredon) values(:segmentid, :previousassignment, :previousproject, :previousrequest, :previousassignmentdate, :previousassignmentby, :enteredby, now())";
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
             $srchSQL = "select rq.requestid, ifnull(rq.req_status,'') as rqstatus  from vandyinvest.investtissreq rq left join vandyinvest.investproj pr on rq.projid = pr.projid where pr.investid = :investid order by rq.req_status";
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

    function preprocessphiedit($request, $passdata) { 
      $responseCode = 400; 
      require(serverkeys . "/sspdo.zck");  
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $errorInd = 0;
      $pdta = json_decode($passdata, true);

      //MAKE CHECKS HERE 
      if ( 1 !== 1) { 
        $errorInd = 1;
        $msgArr[] .= "User not allowed edit function on this donor record";
        $msgArr[] .= $pdta['phicode'];
      }               

      if (trim($pdta['phicode']) === "") { 
        $errorInd = 1;
        $msgArr[] .= "Missing Donor Record ID.  See a CHTNEastern Informatics Staff.";
      } 
      //END CHECKS HERE

      if ($errorInd === 0) { 
        session_start();
        $sessid = session_id();
        $usrSQL = "SELECT presentinstitution FROM four.sys_userbase where sessionid = :sessid";
        $usrRS = $conn->prepare($usrSQL);
        $usrRS->execute(array(':sessid' => $sessid)); 
        if ( $usrRS->rowCount() === 1 ) {       
          $r = $usrRS->fetch(PDO::FETCH_ASSOC);
          $pdta['presentinstitution'] = $r['presentinstitution'];
          $pdta['sessionid'] = $sessid;
          $dta = array('pagecontent' => bldDialog('procureBiosampleEditDonor', $pdta) ); 
          $responseCode = 200;
        } else {   
          $errorInd = 1;
          $msgArr[] .= "You are not a recognized user (USER KEY has expired).  Log back into scienceServer to continue.";
        }
      }

      $msg = $msgArr;       
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
      return $rows;        
    }

    function preprocessoverridehpr($request, $passdata) { 
      $responseCode = 400; 
      require(serverkeys . "/sspdo.zck");  
      $error = 0;
      $msg = "";
      $itemsfound = 0;
      $data = array();
      $errorInd = 0;
      $pdta = json_decode($passdata, true);
      $bgArr = array();
      //{"0":{"biogroup":"84278","bgslabel":"84278001","segmentid":"445631"},"1":{"biogroup":"84278","bgslabel":"84278002","segmentid":"445632"}}
      foreach ($pdta as $key => $value) {
        if (!in_array($value['biogroup'], $bgArr)) {    
            $msgArr[] = "{$value['biogroup']}";
            $bgArr[] = $value['biogroup'];
        }
      }
      //DATA CHECKS  
      ( count($bgArr) < 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "NO BIOGROUPS WERE SUBMITTED FOR QMS/HPR OVER-RIDE")) : "";
      if ($errorInd === 0) { 
        $responseCode = 200;
        $dta = array('pagecontent' =>  bldDialog('dataCoordinatorHPROverride', $bgArr));
      } else { 
        $msg = $msgArr;
      }       
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
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
         $addHistorySQL = "insert into masterrecord.history_procure_segment_status(segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) values(:sg,:psegstat,:pstatby,:pdte,now(),:updusr)";
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
    $cred = json_decode(chtndecrypt($params['ency']), true); 
    $u = $cred['user'];
    $p = $cred['pword'];
    $au = explode("-",$cred['dauth']);
    $sess = session_id(); 
    $ip = clientipserver();
    require(serverkeys . "/sspdo.zck"); 
    $usrChk = "SELECT userid, username, emailaddress, fiveonepword FROM four.sys_userbase where allowind = 1 and emailaddress = :pword and datediff(passwordexpiredate, now()) > -1";
    $usrR = $conn->prepare($usrChk); 
    $usrR->execute(array(':pword' => $u)); 
    if ( $usrR->rowCount() === 1 ) { 
      $r = $usrR->fetch(PDO::FETCH_ASSOC);  
      $dbPword = $r['fiveonepword'];
      $usrid = $r['userid'];
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

                   $trckSQL = "insert into four.sys_lastLogins(userid, usremail, logdatetime, fromip) values(:userid, :usremail, now(), :ip)";
                   $trckR = $conn->prepare($trckSQL);
                   $trckR->execute(array(':userid' => $usrid, ':usremail' => $u, ':ip' => $ip));

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

function addSegmentToBiogroup($whichBG = "", $hrpost = 0, $metric = 0, $metricUOM = 4, $prp = "", $prpMet = "", $prpContainer = "", $cutFromBlockId = "", $HPRInd = 0, $procuredAt = "", $procuredBy = "", $InvestId = "", $dspName = "", $requestId = "", $cmts = "", $additions = "", $groupingid = "") { 
  include(serverkeys . "/sspdo.zck"); 
  $nxtSeg = substr(("000" . nextbgsegmentnumber($whichBG)) , -3); 
  $bgs = "{$whichBG}T{$nxtSeg}";
  $insSQL = "insert into four.ut_procure_segment (pbiosample, activeind, seglabel, bgs, hrpost, metric, metricuom, prp, prpmet, prpContainer, cutfromblockid, qty, hprind, procuredat, procuredby, investid, dspname, projid, requestid, inputon, sgcomments, additionalInformation, groupingid) values(:pbiosample, 1, :seglabel, :bgs, :hrpost, :metric, :metricuom, :prp, :prpmet, :prpContainer, :cutfromblockid, 1, :hprind, :procuredat, :procuredby, :assigncode, :assigndspname, 0, :requestNbr, now(), :sgcomments, :addInformation, :groupingid)";
  $insR = $conn->prepare($insSQL);
  $insR->execute(array(
      ':pbiosample' => $whichBG 
     ,':seglabel' => $nxtSeg
     ,':bgs' => $bgs
     ,':hrpost' => $hrpost
     ,':metric' => $metric
     ,':metricuom' => $metricUOM
     ,':prp' => $prp
     ,':prpmet' => $prpMet
     ,':prpContainer' => $prpContainer
     ,':cutfromblockid' => $cutFromBlockId
     ,':hprind' => $HPRInd 
     ,':procuredat' => $procuredAt
     ,':procuredby' => $procuredBy
     ,':assigncode' => $InvestId
     ,':assigndspname' => $dspName
     ,':requestNbr' => $requestId
     ,':sgcomments' => $cmts
     ,':addInformation' => $additions
     ,':groupingid' => $groupingid     
 ));
  $lst = $conn->lastInsertId();
  //TODO: MAKE THIS DYNAMIC THERE IS A FUNCTION: labelprintrequest() USE THIS FUNCTION FOR NOW THIS IS A HARD CODE 
  if ( strtoupper( $procuredAt ) === 'HUP' ) {
     $insSQL = "insert into serverControls.lblToPrint (labelRequested, printerRequested, dataStringpayload, byWho, onWhen) values(:formatname,:linuxprinter,:payloadstring,:usr,now())";
     $insRS = $conn->prepare($insSQL);
     if ( strtoupper( $prp ) === 'PB' ) { 
       //PRINT FFPE LABEL ON hades
       $insRS->execute(array( ':formatname' => 'hades',':linuxprinter' => 'Hades',':payloadstring' => json_encode(array('FIELD01' => $bgs, 'FIELD02' => $bgs)),':usr' => $procuredBy));
     }
     if ( strtoupper( $prp ) !== 'PB' && strtoupper( $prp ) !== 'SLIDE' ) {
       //PRINT OTHER LABEL ON anubis
       $insRS->execute(array( ':formatname' => 'anubis',':linuxprinter' => 'Anubis',':payloadstring' => json_encode(array('FIELD01' => $bgs, 'FIELD02' => $bgs)),':usr' => $procuredBy));
     }
  } 
  //PRINT LABEL END
  return $bgs; //REMOVED SPACE FROM AFTER BGS - 20190313 ZACK
}

function userDetailsCal($whichusr) { 
  include(serverkeys . "/sspdo.zck"); 
  $rtnArr = array();
  $usrSQL = "SELECT sessionid, friendlyname, emailaddress FROM four.sys_userbase where sessionid = :sessionid and allowInd = 1";
  $usrRS = $conn->prepare($usrSQL); 
  $usrRS->execute(array(':sessionid' => $whichusr)); 
  if ($usrRS->rowCount() === 0) { 
  } else { 
    while ($u = $usrRS->fetch(PDO::FETCH_ASSOC)) { 
      $rtnArr[] = $u;
    }
  } 
  return $rtnArr;
}

function userDetails($whichusr) { 
  include(serverkeys . "/sspdo.zck"); 
  $rtnArr = array();
  $usrSQL = "SELECT sessionid, originalaccountname, presentinstitution, primaryinstcode FROM four.sys_userbase where sessionid = :sessionid and allowInd = 1 and allowProc = 1";
  $usrRS = $conn->prepare($usrSQL); 
  $usrRS->execute(array(':sessionid' => $whichusr)); 
  if ($usrRS->rowCount() === 0) { 
  } else { 
    while ($u = $usrRS->fetch(PDO::FETCH_ASSOC)) { 
      $rtnArr[] = $u;
    }
  } 
  return $rtnArr;
}

function captureSystemActivity($sessionid, $sessionvariables, $loggedsession, $userid, $firstname, $lastname, $email, $requestmethod, $request) { 
    //TODO:  ADD THIS FUNCTION INTO ALL OTHER FUNCTIONS

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

// Function to get the client ip address
function clientipserver() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
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

function bgqmsstatus($whichbg) { 
  $responseCode = 400;
  if (trim($whichbg) !== "") {   
    $pbg = (float)$whichbg; 
    if (is_numeric($pbg) && ($pbg > 0)) { 
      require(serverkeys . "/sspdo.zck"); 
      $sql = "SELECT ifnull(pBioSample,0) as pbiosample, read_Label as readlabel, ifnull(QCVALv2,'') as qcvalue, ifnull(HPRInd,0) as hprindicator, ifnull(hprMarkByOn,'') as hprmarkbyon, ifnull(QCInd,'') as qcindicator, ifnull(QCMarkByOn,'') as qcmarkbyon, ifnull(QCProcStatus,'') as qcprocstatus, ifnull(HPRDecision,'') as hprdecision, ifnull(HPRResult,'') as hprresult, ifnull(HPRSlideReviewed,'') as hprslidereviewed, ifnull(HPRBy,'') as hprby, ifnull(HPROn,'') as hpron FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
      $rs = $conn->prepare($sql);
      $rs->execute(array(':pbiosample' => $pbg));
      if ($rs->rowCount() === 1) { 
        $r = $rs->fetch(PDO::FETCH_ASSOC); 
        $dta[] = $r;
        $responseCode = 200;
      } else { 
        $responseCode = 404;
      }
    }
  }  
  return array('responsecode' => $responseCode, 'data' => $dta);
}

function bgsslide($whichbgs) { 
  $responseCode = 400; 
  $dta = array();
  if (trim($whichbgs) !== "") { 
    require(serverkeys . "/sspdo.zck"); 
    $sql = "SELECT segmentid FROM masterrecord.ut_procure_segment where replace(bgs,'T_','') = :bgs and segstatus <> 'SHIPPED' and  prepmethod = 'SLIDE'";
    $rs = $conn->prepare($sql); 
    $rs->execute(array(':bgs' => $whichbgs));
    if ($rs->rowCount() === 1) { 
      $r = $rs->fetch(PDO::FETCH_ASSOC); 
      $dta[] = $r;
      $responseCode = 200;
    } else { 
      $responseCode = 404;
    } 
  } else { 
    $responseCode = 404;
  }
  return array('responsecode' => $responseCode, 'data' => $dta);
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

function bldDialogGetter($whichdialog, $passedData) {  
  $at = applicationTree; 
  require("{$at}/sscomponent_pagecontent.php"); 
  $bldr = new pagecontent(); 
  $rtnDialog = $bldr->sysDialogBuilder($whichdialog, $passedData);
  return $rtnDialog;
}




/* INITIAL PRISTINE BIOGROUP CHECKS
 * //$passdata = {"PRCBGNbr":"","PRCProcDate":"01/22/2019","PRCProcedureTypeValue":"S","PRCProcedureType":"Surgery","PRCCollectionTypeValue":"EXC","PRCCollectionType":"Excision","PRCTechInstitute":"PROCZACK :: HUP","PRCInitialMetric":".8","PRCMetricUOMValue":"4","PRCMetricUOM":"Grams",
      //"PRCPXIId":"57bacaa1-551c-4ae3-8cc8-a6ef76ffb8c7","PRCPXIInitials":"A.G.","PRCPXIAge":"33","PRCPXIAgeMetric":"yrs","PRCPXIRace":"UNKNOWN"
      //,"PRCPXISex":"M","PRCPXILastFour":"4741","PRCPXIInfCon":"NO","PRCPXICXValue":"Unknown","PRCPXICX":"Unknown","PRCPXIRXValue":"Unknown","PRCPXIRX":"Unknown","SubjectNbr":"s","ProtocolNbr":"s","PRCUpennSOGIValue":"NO DATA","PRCUpennSOGI":"No Data","PRCDXOverride":false,"PRCSpecCatValue":"MALIGNANT","PRCSpecCat":"MALIGNANT","PRCSiteValue":"S73","PRCSite":"THYROID","PRCSSiteValue":"SS101","PRCSSite":"LOBE",
      //"PRCDXModValue":"D469","PRCDXMod":"CARCINOMA :: FOLLICULAR","PRCUnInvolvedValue":"0","PRCUnInvolved":"NA (Not Applicable to Biosample)"
      //,"PRCPathRptValue":"2","PRCPathRpt":"Pending","PRCMETSSiteValue":"S62","PRCMETSSite":"CERVIX","PRCMETSDXValue":"D442","PRCMETSDX":"CARCINOMA","PRCSitePositionValue":"ANTERIOR","PRCSitePosition":"ANTERIOR","PRCSystemListValue":"D804","PRCSystemList":"HEMORRHOIDS","PRCBSCmts":"cmts","PRCHPRQ":"qstn","PRCProcedureInstitutionValue":"HUP","PRCProcedureDateValue":"2019-01-18","PRCProcedureDate":"01/18/2019"} 
 * 
 * 
 * REQUIRED FIELDS
 * PRCProcDate; PRCPRocedureTypeValue; PRCTechInstitute; PRCInitialMetric; PRCMetricUOMValue; PRCPXIId; PRCPXICXValue; PRCPXIRXValue; PRCUnInvolvedValue; PRCPathRptValue; PRCProcedureInstitutionValue; PRCProcedureDateValue
 * 
 */

function createBGPXI( $bgNbr, $bg ) { 
  require(serverkeys . "/sspdo.zck");
  //GET ORSchedId, ProcedureDate, Institution from Schedule

  $orsql = "SELECT orlistid, ifnull(orkey,'') as orkey FROM four.tmp_ORListing where date_format(listdate,'%Y-%m-%d') = :proceduredate and location = :givenlocation and pxicode = :pxicode";
  $orrs = $conn->prepare($orsql); 
  $orrs->execute(array(':proceduredate' => $bg['PRCProcedureDateValue'], ':givenlocation' => $bg['PRCProcedureInstitutionValue'], ':pxicode' => $bg['PRCPXIId'])); 
  if ($orrs->rowCount() === 0) { 
      //TODO: MAKE SURE THAT THERE IS A DATA CHECK FOR THIS IN THE DATA CHECK SECTION!!!! 
      //THIS IS REALLY BAD ... REALLY REALLY BAD
  } else { 
    $or = $orrs->fetch(PDO::FETCH_ASSOC); 
  }

  //BUILD ASSOCIATIVE ID
  $assChkSQL = "SELECT procedureAssocCode FROM four.ref_procureBiosample_PXI where pxiid = :pxiid and proceduredate = :proceduredte and fromInstitution = :fromlocation and orkey = :orkey";
  $assChkR = $conn->prepare($assChkSQL); 
  $assChkR->execute(array(':pxiid' => $bg['PRCPXIId'], ':proceduredte' => $bg['PRCProcedureDateValue'] , ':fromlocation' => $bg['PRCProcedureInstitutionValue'] , ':orkey' => $or['orkey'] ));
  if ( $assChkR->rowCount() === 0 ) { 
    $associd = generateRandomString(20);
  } else { 
    $ass = $assChkR->fetch(PDO::FETCH_ASSOC); 
    $associd = $ass['procedureAssocCode'];
  }


  $cxvalsql = "SELECT menuvalue FROM four.sys_master_menus where menu = 'CX' and dspValue = :value";
  $cxv = $conn->prepare($cxvalsql);
  $cxv->execute(array(':value' => $bg['PRCPXICX'])); 
  if ($cxv->rowCount() === 0) { 
    $cx = 2; 
  } else { 
    $cxr = $cxv->fetch(PDO::FETCH_ASSOC); 
    $cx = (int)$cxr['menuvalue'];
  }

  $rxvalsql = "SELECT menuvalue FROM four.sys_master_menus where menu = 'RX' and dspValue = :value";
  $rxv = $conn->prepare($rxvalsql);
  $rxv->execute(array(':value' => $bg['PRCPXIRX'])); 
  if ($rxv->rowCount() === 0) { 
    $rx = 2; 
  } else { 
    $rxr = $rxv->fetch(PDO::FETCH_ASSOC); 
    $rx = (int)$rxr['menuvalue'];
  }

  $ivalsql = "SELECT menuvalue FROM four.sys_master_menus where menu = 'INFC' and dspValue = :value";
  $ixv = $conn->prepare($ivalsql);
  $ixv->execute(array(':value' => $bg['PRCPXIInfCon'])); 
  if ($ixv->rowCount() === 0) { 
    $ic = 1; 
  } else { 
    $ixr = $ixv->fetch(PDO::FETCH_ASSOC); 
    $ic = (int)$ixr['menuvalue'];
  }

  //WRITE DATA
  $insSQL = "insert into four.ref_procureBiosample_PXI (pbiosample, activeInd, proceduredate, fromInstitution, orkey, procedureAssocCode, pxiid, pxiInitials, pxiRace, pxiGender, pxiAge, pxiAgeUOM, SOGI, CX, RX, subjectnumber, protocolnumber, InformedConsent, callback, byWho, inputOn, fromModule) values (:pbiosample, 1, :proceduredate, :fromInstitution, :orkey, :procedureAssocCode, :pxiid, :pxiInitials, :pxiRace, :pxiGender, :pxiAge, :pxiAgeUOM, :SOGI, :CX, :RX, :subjectnumber, :protocolnumber, :InformedConsent, :callback, :byWho, now(), 'PROCUREMENT')";
  $insRS = $conn->prepare($insSQL); 
  $insRS->execute(array(
   ':pbiosample' => $bgNbr 
  ,':proceduredate' => $bg['PRCProcedureDateValue']
  ,':fromInstitution' => $bg['PRCProcedureInstitutionValue']
  ,':orkey' => $or['orkey']
  ,':procedureAssocCode' => $associd
  ,':pxiid' => $bg['PRCPXIId']
  ,':pxiInitials' => $bg['PRCPXIInitials']
  ,':pxiRace' => $bg['PRCPXIRace']
  ,':pxiGender' => $bg['PRCPXISex']
  ,':pxiAge' => $bg['PRCPXIAge']
  ,':pxiAgeUOM' => $bg['PRCPXIAgeMetric']
  ,':SOGI' => $bg['PRCPXISOGI']
  ,':CX' => $cx
  ,':RX' => $rx
  ,':subjectnumber' => $bg['PRCPXISubjectNbr']
  ,':protocolnumber' => $bg['PRCPXIProtocolNbr']
  ,':InformedConsent' => $ic
  , ':callback' => $bg['PRCPXILastFour']
  ,':byWho' => strtolower($bg['PRCTechnician'])
  ));

  //TODO:  UPDATE LINUX SERVER RECORD WHEN ONLINE
  
  //UPDATE ORSCHED (backup and then write received)
  $copySQL = "insert into four.tmp_ORListing_history SELECT * FROM four.tmp_ORListing where ORListID = :orlistid";
  $copyRS = $conn->prepare($copySQL);
  $copyRS->execute(array(':orlistid' => $or['orlistid']));

  $updPXISQL = "update four.tmp_ORListing set targetind = 'R', lastupdatedby = 'PROCUREMENT-MODULE-BG-CREATE', lastupdateon = now() where orlistid = :orlistid"; 
  $updPXIRS = $conn->prepare($updPXISQL); 
  $updPXIRS->execute(array(":orlistid" => $or['orlistid']));

  return $or['orkey'] . " " . $or['orlistid'];
}

function createBGDXD( $bgNbr, $bg) { 
  require(serverkeys . "/sspdo.zck");
  ////GET CLASSIFICATION
  $classChkSQL = "SELECT ucase(ifnull(classification,'')) as classification FROM four.sys_master_menu_vocabulary_site_classifications where site = :site"; 
  $classChkRS = $conn->prepare($classChkSQL); 
  $classChkRS->execute(array(':site' => $bg['PRCSite'])); 
  $siteClass = "";
  if ( $classChkRS->rowCount() === 1 ) { 
    $classChk = $classChkRS->fetch(PDO::FETCH_ASSOC); 
    $siteClass = $classChk['classification'];
  }  
  $dx = explode(" :: ", $bg['PRCDXMod']);
  $unknownmet = 2; //DOESN'T APPLY
  if ($bg['PRCSpecCatValue'] === "MALIGNANT") { 
    if (trim($bg['PRCMETSSITE']) === "") { 
       $unknownmet = 1; //UNKNOWN MET SITE
    } else {
       $unknownmet = 0; //KNOWN MET
    }
  }
  $dxdOverride = ( $bg['PRCDXOverride'] ) ? 1 : 0; 
  $dxdSQL = "insert into four.ref_procureBiosample_designation (pBioSample, activeInd, specCat, primarySite, classification, sitePosition, primarySubSite, diagnosis, diagnosisModifier, unknownMet, metsSite, metsDX, systemDiagnosis, refFromModule, refBy, refOn, dxdOverride) values(:pBioSample, 1, :specCat, :primarySite, :classification, :sitePosition, :primarySubSite, :diagnosis, :diagnosisModifier, :unknownMet, :metsSite, :metsDX, :systemDiagnosis, 'PROCUREMENT', :refBy, now(), :dxdOverride)";
  $dxdRS = $conn->prepare($dxdSQL); 
  $dxdRS->execute(array(
                      ':pBioSample' => $bgNbr
                    , ':specCat' => trim($bg['PRCSpecCatValue'])
                    , ':primarySite' => trim($bg['PRCSite'])
                    , ':classification' => trim($siteClass)
                    , ':sitePosition' => trim($bg['PRCSitePosition'])
                    , ':primarySubSite' => trim($bg['PRCSSite'])
                    , ':diagnosis' => trim($dx[0])
                    , ':diagnosisModifier' => trim($dx[1])
                    , ':unknownMet' => (int)$unknownmet
                    , ':metsSite' => trim($bg['PRCMETSSite'])
                    , ':metsDX' => trim($bg['PRCMETSDX'])
                    , ':systemDiagnosis' => trim($bg['PRCSystemList'])
                    , ':refBy' => strtolower(trim($bg['PRCTechnician']))
                    , ':dxdOverride' => (int)$dxdOverride
                ));
  return 1;
}

function createBGComment( $bgNbr, $bg ) { 

  require(serverkeys . "/sspdo.zck");
  $cmtSQL = "insert into four.ref_procureBiosample_comment (pbiosample, activeind, comment, reffrommodule, moduleReference, refby, refon, dspind) values(:pbiosample, 1, :comment, 'PROCUREMENT', :moduleReference, :refby, now(), 1)";
  $cmtRS = $conn->prepare($cmtSQL); 
  if ( trim($bg['PRCBSCmts']) !== "" ) { 
    $cmtRS->execute(array(':pbiosample' => $bgNbr,':comment' => trim($bg['PRCBSCmts']),':moduleReference' => 'BIOSPECIMENCMT',':refby' => strtolower($bg['PRCTechnician'])));
  }
  if ( trim($bg['PRCHPRQ']) !== "" ) { 
    $cmtRS->execute(array(':pbiosample' => $bgNbr,':comment' => trim($bg['PRCHPRQ']),':moduleReference' => 'HPRQUESTION',':refby' => strtolower($bg['PRCTechnician'])));
  }
  return 1;
}

function createBGDateDetails( $bgNbr, $bg ) {
  
  require(serverkeys . "/sspdo.zck");
  $dteSQL = "insert into four.ref_procureBiosample_dates (pbiosample, activeind, refdate, reffrommodule, datedesignation, refby, refon) values( :pbiosample, 1, :refdate, 'PROCUREMENT', :datedesignation, :refby, now())";
  $dteRS = $conn->prepare($dteSQL);

  //PROCEDURE DATE
  $dteRS->execute(array(
    ':pbiosample' => $bgNbr
   ,':refdate' => $bg['PRCProcedureDateValue']
   ,':datedesignation' => 'PROCEDURE'
   ,':refby' => strtolower($bg['PRCTechnician'])
  ));

  //PROCUREMENT DATE
  $pDte = DateTime::createFromFormat('m/d/Y', $bg['PRCProcDate'] );
  $dteRS->execute(array(
    ':pbiosample' => $bgNbr
   ,':refdate' => $pDte->format('Y-m-d')
   ,':datedesignation' => 'PROCUREMENT'
   ,':refby' => strtolower($bg['PRCTechnician'])
  ));

  return 1;
}

function createBGDetail( $bgNbr, $bg ) { 
  require(serverkeys . "/sspdo.zck");
  $bgDetSQL = "insert into four.ref_procureBiosample_details ( activeInd, pbiosample, readLabel, procType, collectionMethod, uninvolvedInd, pathReportInd, initialmetric, initialUOM, fromLocation, byWho, inputOn, fromModule ) values ( 1, :pbiosample, :readLabel, :procType, :collectionMethod, :uninvolvedInd, :pathReportInd, :initialmetric, :initialUOM, :fromLocation, :byWho, now(), 'PROCUREMENT' )";
  $bgDetRS = $conn->prepare($bgDetSQL);
  $bgDetRS->execute(array(
   ':pbiosample' =>  $bgNbr
 , ':readLabel' => "{$bgNbr}T_"
 , ':procType' => "{$bg['PRCProcedureTypeValue']}"
 , ':collectionMethod' => "{$bg['PRCCollectionTypeValue']}" 
 , ':uninvolvedInd' => $bg['PRCUnInvolvedValue']
 , ':pathReportInd' => $bg['PRCPathRptValue']
 , ':initialmetric' => $bg['PRCInitialMetric']
 , ':initialUOM' => $bg['PRCMetricUOMValue']
 , ':fromLocation' => $bg['PRCPresentInstValue']
 , ':byWho' => strtolower($bg['PRCTechnician'])
  ));
 return 1;
}

function createBGHeader( $selector, $bg ) { 
  require(serverkeys . "/sspdo.zck");
  $bgNbrSQL = "insert into four.ut_procure_biosample ( selector, recordstatus, fromlocation, inputon, inputby ) values ( :selector, 2, :fromlocation, now(), :technician )";
  $bgNbrRS = $conn->prepare($bgNbrSQL); 
  $bgNbrRS->execute(array(':selector' => $selector, ':fromlocation' => $bg['PRCPresentInstValue'], ':technician' => strtolower($bg['PRCTechnician']) )); 
  $bgNbr = $conn->lastInsertId();
  return $bgNbr;
}

function initialBGDataCheckThree($bg) { 
    //C) Check Date Values/Not Invalid - PRCProcDate; PRCProcedureDateValue (Make Sure Procedure Date is not more than 4 days old);
    $errorInd = 0; 
    $msgArr = array(); 
    //( 1 === 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "{$bg['PRCProcDate']}")) : "";
    ( !verifyDate( $bg['PRCProcDate'], 'm/d/Y') ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCUREMENT DATE IS NOT A VALID DATE")) : ""; 
    ( !verifyDate( $bg['PRCProcedureDateValue'], 'Y-m-d') ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCEDURE DATE IS NOT A VALID DATE")) : ""; 

    //$dateTime = DateTime::createFromFormat($format, $date);
    //$proc_date = DateTime::createFromFormat('m/d/Y',$bg['PRCProcDate']);
    //$current_date = new DateTime(); 
    //( $proc_date <> $current_date ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCUREMENT DATE MUST BE TODAY")) : "";


    return array('errorind' => $errorInd, 'msgarr' => $msgArr);    
}

function initialBGDataCheckTwo($bg) { 
    //B) Check Required fields
    $errorInd = 0; 
    $msgArr = array(); 
    //REQUIRED FIELDS
    ( trim($bg['PRCBGNbr']) !== "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  BIOGROUP CANNOT EXIST ON INITIAL SAVE")) : "";
    ( trim($bg['PRCPresentInstValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PRESENT INSTITUTION WHERE THE COLLECTION IS OCCURING IS REQUIRED")) : "";
    ( trim($bg['PRCProcDate']) === "") ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCUREMENT DATE IS REQUIRED (ALL FIELDS WITH AN ASTERICK ARE REQUIRED)")) : "";
    ( trim($bg['PRCProcedureTypeValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE TYPE OF PROCEDURE IS REQUIRED")) : "";
    ( trim($bg['PRCTechnician']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCURING TECHNICIAN IS REQUIRED")) : "";
    ( trim($bg['PRCInitialMetric']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE INITIAL METRIC IS REQUIRED")) : "";    
    ( !is_numeric(trim($bg['PRCInitialMetric']))  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE INITIAL METRIC MEASURE MUST BE A NUMBER.")) : "";    
    //TODO: FIGURE OUT HOW TO CAST AS DOUBLE TO NON-NEG NON-ZERO
    ( $bg['PRCInitialMetric'] === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE INITIAL METRIC MEASURE MUST BE GREATER THAN ZERO.")) : ""; 
    
    ( trim($bg['PRCMetricUOMValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE INITIAL METRIC UOM MUST BE SPECIFIED")) : "";    
    ( trim($bg['PRCPXIId']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A DONOR FROM THE OPERATIVE SCHEDULE MUST BE SPECIFIED.  CLICK A DONOR FROM THE LIST")) : "";
    ( trim($bg['PRCPXIInitials']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S INITIALS ARE MISSING.  IF THIS IS A LINKED DONOR, SEE A CHTNEASTERN INFORMATICS PERSON.  FOR DELINKED DONORS, RE-ENTER THE DELINK.")) : "";        
    ( trim($bg['PRCPXIAge']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S AGE IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD")) : "";       
    ( trim($bg['PRCPXIAgeMetric']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S AGE METRIC IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD")) : "";            
    ( trim($bg['PRCPXIRace']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S RACE IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD")) : "";        
    ( trim($bg['PRCPXISex']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S SEX IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD")) : "";        
    ( trim($bg['PRCPXIDspCX']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S CHEMOTHERAPY INDICATOR IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD.")) : "";        
    ( trim($bg['PRCPXIDspRX']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE DONOR'S RADIATION INDICATOR IS MISSING.  EDIT THE DONOR BEFORE SELECTING THAT RECORD.")) : "";           
    ( trim($bg['PRCSpecCatValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP'S SPECIMEN CATEGORY MUST BE SPECIFIED.")) : "";
    ( trim($bg['PRCSiteValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP'S SITE MUST BE SPECIFIED.")) : "";
    //( trim($bg['PRCSiteValue']) === "MALIGNANT" && trim($bg['PRCMETSSiteValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFIFYING MALIGNANT SAMPLES A METS FROM SITE SHOULD BE SPECIFIED")) : "";
    ( trim($bg['PRCUnInvolvedValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP'S UNINVOLVED/NAT INDICATOR MUST BE SPECIFIED")) : "";
    ( trim($bg['PRCPathRptValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP'S PATHOLOGY REPORT INDICATOR MUST BE SPECIFIED")) : "";
    ( trim($bg['PRCProcedureInstitutionValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCEDURE INSTITUTION MUST BE SPECIFIED.")) : "";
    ( trim($bg['PRCProcedureInstitutionValue']) !== trim($bg['PRCPresentInstValue']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCEDURE INSTITUTION AND PROCURING INSTITUTION MUST BE THE SAME.")) : ""; 
    ( trim($bg['PRCProcedureDateValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE PROCEDURE DATE MUST BE SPECIFIED.")) : "";
    return array('errorind' => $errorInd, 'msgarr' => $msgArr);    
}

function initialBGDataCheckOne($bg) { 
    //A) MAKE SURE ALL USED FIELDS EXISTS
    $errorInd = 0; 
    $msgArr = array(); 
    //CHECK ARRAY KEYS EXIST IN PASSED DATA
      ( !array_key_exists('PRCBGNbr', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCBGNbr)")) : "";
      ( !array_key_exists('PRCPresentInstValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPresentInstValue)")) : "";
      ( !array_key_exists('PRCProcDate', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCProcDate)")) : "";
      ( !array_key_exists('PRCProcedureTypeValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCProcedureTypeValue)")) : "";
      ( !array_key_exists('PRCCollectionTypeValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCCollectionTypeValue)")) : "";
      ( !array_key_exists('PRCTechnician', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCTechnician)")) : "";
      ( !array_key_exists('PRCInitialMetric', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCInitialMetric)")) : "";      
      ( !array_key_exists('PRCMetricUOMValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCInitialMetric)")) : "";            
      ( !array_key_exists('PRCPXIId', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIId)")) : "";      
      ( !array_key_exists('PRCPXIInitials', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIInitials)")) : "";
      ( !array_key_exists('PRCPXIAge', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIAge)")) : "";      
      ( !array_key_exists('PRCPXIAgeMetric', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIAgeMetric)")) : "";      
      ( !array_key_exists('PRCPXIRace', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIRace)")) : "";      
      ( !array_key_exists('PRCPXISex', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXISex)")) : "";            
      ( !array_key_exists('PRCPXIInfCon', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIInfCon)")) : "";      
      ( !array_key_exists('PRCPXIDspCX', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXICXValue)")) : "";
      ( !array_key_exists('PRCPXIDspRX', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPXIRXValue)")) : "";
      ( !array_key_exists('PRCDXOverride', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCDXOverride)")) : "";      
      ( !array_key_exists('PRCSpecCatValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCSpecCatValue)")) : "";      
      ( !array_key_exists('PRCSiteValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCSite)")) : "";      
      ( !array_key_exists('PRCSSiteValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCSSite)")) : "";      
      ( !array_key_exists('PRCDXModValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCDXMod)")) : "";
      ( !array_key_exists('PRCUnInvolvedValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCUnInvolvedValue)")) : "";
      ( !array_key_exists('PRCPathRptValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCPathRptValue)")) : "";
      ( !array_key_exists('PRCMETSSiteValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCMETSSite)")) : "";      
      ( !array_key_exists('PRCMETSDXValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCMETSDX)")) : ""; 
      ( !array_key_exists('PRCMETSDX', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCMETSDX)")) : ""; 
      ( !array_key_exists('PRCSitePositionValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCSitePosition)")) : ""; 
      ( !array_key_exists('PRCSystemListValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCSystemList)")) : "";
      ( !array_key_exists('PRCBSCmts', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCBSCmts)")) : "";
      ( !array_key_exists('PRCHPRQ', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCHPRQ)")) : "";
      ( !array_key_exists('PRCProcedureInstitutionValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCProcedureInstitutionValue)")) : "";
      ( !array_key_exists('PRCProcedureDateValue', $bg) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MALFORMED PAYLOAD (MISSING: PRCProcedureDateValue)")) : "";
    return array('errorind' => $errorInd, 'msgarr' => $msgArr);    
}

function biogroupdayssince($selector) { 
    require(serverkeys . "/sspdo.zck");
    $rtn = array();
    $daySinceSQL = "SELECT TIMESTAMPDIFF(DAY,bs.inputon,now()) dbwrite, TIMESTAMPDIFF(DAY,prc.refdate,now()) as procdate, TIMESTAMPDIFF(DAY,pcd.refdate,now()) as prcddate FROM four.ut_procure_biosample bs left join (SELECT pbiosample, refdate FROM four.ref_procureBiosample_dates where activeind = 1 and dateDesignation = 'PROCUREMENT') prc on bs.pBioSample = prc.pbiosample left join (SELECT pbiosample, refdate FROM four.ref_procureBiosample_dates where activeind = 1 and dateDesignation = 'PROCEDURE') pcd on bs.pBioSample = pcd.pbiosample where bs.selector = :selector";

  $daySinceRS = $conn->prepare($daySinceSQL);
  $daySinceRS->execute(array(':selector' => $selector));

  if ($daySinceRS->rowCount() === 0) { 
  } else { 
    while ($r = $daySinceRS->fetch(PDO::FETCH_ASSOC)) { 
      $rtn[] = $r;
    }
  }
  return $rtn;
}

function nextbgsegmentnumber($whichBG) { 
  require(serverkeys . "/sspdo.zck");
  $sgSQL = "SELECT ifnull(convert(segLabel, unsigned integer),0) as seglbl FROM four.ut_procure_biosample bs left join four.ut_procure_segment sg on bs.pbiosample = sg.pbiosample where bs.pbiosample = :selector order by convert(segLabel, unsigned integer) desc";
  $sgR = $conn->prepare($sgSQL);
  $sgR->execute(array(':selector' => $whichBG));
  $sg = $sgR->fetch(PDO::FETCH_ASSOC); 

  return (((int)$sg['seglbl']) + 1); 
}

function searchVocabByTerm($whichterm) { 
  require(serverkeys . "/sspdo.zck");
  $rtnArr = array(); 
  if ( trim($whichterm) !== "" ) {
      //SEARCH
      $sql = "SELECT vocabid, ifnull(specimenCategory,'') as specimencategory, ifnull(site,'') as site, ifnull(subsite,'') as subsite, ifnull(diagnosis,'') as diagnosis FROM four.sys_master_menu_vocabulary where 1=1 and ifnull(specimenCategory,'') <> '' and match(specimenCategory,site, Subsite, Diagnosis) against(:srchtrm IN BOOLEAN MODE) order by specimencategory, site, subsite, diagnosis";
      $rs = $conn->prepare($sql); 
      $rs->execute(array(':srchtrm' => "{$whichterm}*"  ));
      if ( $rs->rowCount() > 0 ) { 
        while ( $r = $rs->fetch(PDO::FETCH_ASSOC) ) { 
          $rtnArr[] = $r;
        }
      } 
  } else { 
  } 
  return $rtnArr;
}

function zeroOut($var){
   return ($var < 0 ? 0 : $var);
}

function getLongSQLStmts($whichStmt) { 

switch ($whichStmt) {
  case 'masterbgsegscreen':
  $rtnthis = "SELECT sg.segmentid, replace(ifnull(sg.bgs,''),'_','') as bgs, ifnull(sg.segmentlabel,'000') as segmentlabel, ifnull(date_format(sg.procurementdate,'%m/%d/%Y'),'') as procurementdate, ifnull(sts.dspvalue,'ERROR') as segstatus, ifnull(date_format(sg.statusdate, '%m/%d/%Y'),'') as statusdate, ifnull(statusby,'') as statusby, substr(concat('000000',ifnull(sg.shipDocRefId,'000000')),-6) as shipdocref, ifnull(reconcilind,0) as reconcilind, ifnull(date_format(sg.shippeddate, '%m/%d/%Y'),'') as shippeddate , ifnull(sg.hourspost,'') as hourspost, ifnull(sg.metric,0) as metric, ifnull(uom.dspvalue,'') as muom, upper(ifnull(sg.prepmethod,'')) as prepmethod , upper(ifnull(sg.preparation,'')) as preparation, ifnull(sg.assignedreq,'') as assignedrequest, upper(ifnull(sg.assignedto,'')) as investid , concat( ifnull(i.nameone,''), ', ', ifnull(i.nametwo,'')) as iname, ifnull(date_format(sg.assignmentdate,'%m/%d/%Y'),'') as assigneddate, ifnull(sg.assignedby,'') as assignedby, ifnull(sg.qty,0) as qty, ifnull(sg.hprblockind,0) as hprblockind, ifnull(sg.slidegroupid,'') as slidegroupid, ifnull(sg.scannedstatus,'') as scannedstatus, ifnull(sg.scannedlocation,'') as scannedlocation, ifnull(sg.scanloccode,'') as scannedloccode, ifnull(sg.scannedby,'') as scannedby, ifnull(date_format(sg.scanneddate,'%m/%d/%Y'),'') as scanneddate, ifnull(sg.procuredat,'') as procureinstitution, ifnull(inst.dspinstitution,'') as dspinstitution,   ifnull(enteredby,'') as cuttech, ifnull(sg.segmentcomments,'') as segmentcomments FROM masterrecord.ut_procure_segment sg left join (SELECT menuvalue, if(ifnull(longvalue,'') = '', dspvalue, ifnull(longvalue,'')) as dspvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') as sts on sg.segstatus = sts.menuvalue left join (SELECT menuvalue, if(ifnull(longvalue,'') = '', ifnull(dspvalue,''), ifnull(longvalue,'')) as dspinstitution FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on sg.procuredat = inst.menuvalue left join (SELECT  menuvalue, if(ifnull(dspvalue,'') = '', longvalue, ifnull(dspvalue,'')) as dspvalue FROM four.sys_master_menus where menu = 'METRIC') as uom on sg.metricuom = uom.menuvalue left join (SELECT investid, invest_fname as nameone, invest_lname as nametwo FROM vandyinvest.invest) as i on sg.assignedto = i.investid where biosamplelabel = :pbiosample order by segmentlabel";
  break;
  case 'masterbgscreen':
  $rtnthis = "SELECT bs.pbiosample, replace(bs.read_label,'_','') as readlabel, ifnull(bs.pristineselector,'') as pristineselector, ifnull(voidind,0) as voidind, ifnull(inst.dspinstitution, bs.procureInstitution) as dspinstitution , ifnull(bs.createdby,'') as technician, ifnull(date_format(createdon,'%m/%d/%Y'),'') as procuredate, ifnull(bs.associd,'') as associd, upper(concat(ifnull(pt.dspvalue,''), if(ifnull(ct.dspvalue,'')='','',concat(' :: ',ifnull(ct.dspvalue,'')))))  as collectproctype, trim(ifnull(bs.tisstype,'')) as speccat, trim(concat(ifnull(bs.anatomicsite,''), if(ifnull(bs.subSite,'')='','',concat(' :: ',ifnull(bs.subsite,''))))) as site  , trim(concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' :: ',ifnull(bs.subdiagnos,''))))) as diagnosis, trim(concat(ifnull(bs.metssite,''), if(ifnull(bs.metsSiteDX,'')='','',concat(' :: ',ifnull(bs.metsSiteDX,''))))) mets, trim(ifnull(bs.siteposition,'')) as siteposition, trim(ifnull(bs.pdxSystemic,'')) as systemicdx, trim(date_format(bs.procedureDate,'%m/%d/%Y')) as proceduredate, trim(ifnull(bs.pxiID,'')) as pxiid, upper(concat(ifnull(bs.pxiage,''), if(ifnull(auom.ageuomdsp,'')='','',concat(' ',auom.ageuomdsp)))) as pxiage , upper(ifnull(bs.pxiRace,'')) as pxirace, upper(ifnull(sx.sexdsp,'')) as pxisex, upper(ifnull(cx.dspvalue,'')) as cxind, upper(ifnull(rx.dspvalue,'')) as rxind, upper(ifnull(ic.dspvalue,'')) as icind, upper(ifnull(pr.dspvalue,'')) as prind, ifnull(bs.subjectnbr,'') as subjectnbr, ifnull(bs.protocolnbr,'') as protocolnbr, ifnull(bs.hprind,0) as hprind, ifnull(bs.hprmarkbyon,'') as hprmarkbyon, ifnull(bs.qcind,0) as qcind, ifnull(bs.qcmarkbyon,'') as qcmarkbyon, ifnull(bs.qcvalv2,'') as qcvalue, ifnull(bs.qcprocstatus,'') as qcprocstatus, ifnull(bs.qmsstatusby,'') as qmsstatusby , ifnull(date_format(bs.qmsstatuson,'%m/%d/%Y'),'') as qmsstatuson, ifnull(bs.hprdecision,'') as hprstatus , ifnull(bs.hprresult,0) as hprresult, replace(ifnull(bs.hprslidereviewed,''),'_','') as hprslidereviewed, ifnull(bs.hprby,'') as hprby , ifnull(date_format(bs.hpron,'%m/%d/%Y'),'') as hpron, trim(ifnull(bs.biosamplecomment,'')) as biosamplecomment, trim(ifnull(bs.questionHPR,'')) as questionhpr FROM masterrecord.ut_procure_biosample bs left join (SELECT menuvalue, if(ifnull(longvalue,'') = '', ifnull(dspvalue,''), ifnull(longvalue,'')) as dspinstitution FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on bs.procureInstitution = inst.menuvalue left join (SELECT  menuvalue, ifnull(dspvalue,'') as ageuomdsp  FROM four.sys_master_menus where menu = 'AGEUOM') as auom on bs.pxiAgeUOM = auom.menuvalue left join (SELECT menuvalue, ifnull(dspvalue,'') as sexdsp  FROM four.sys_master_menus where menu = 'PXSEX') as sx on bs.pxiGender = sx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') as cx on bs.chemoind = cx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') as rx on bs.radind = rx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INFC') as ic on bs.informedconsent = ic.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as pr on bs.pathreport = pr.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') as pt on bs.proctype = pt.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'COLLECTIONT') as ct on bs.collectiontype = ct.menuvalue where bs.pbiosample = :biog";    
   break;
   case 'cgridmain':
   $rtnthis = <<<SQLSTMT
select ifnull(bslist.pbiosampledspnbr,'ERROR') as pbiosampledspnbr
     , ifnull(bslist.selector,'') as selector
     , ifnull(bslist.procinstitution,'ERROR') as procuringinstitution
     , ifnull(inst.dspvalue,'') as dspinstitution 
     , ifnull(bslist.bslinkage,'ERROR') as pbiosamplelink
     , ifnull(bslist.timeprocured,'') as timeprocured
     , ifnull(bslist.inputby,'') as technician
     , ifnull(bslist.migrated,0) as migrated
     , ifnull(date_format(bslist.migratedon,'%H:%i (%m/%d/%Y)'),'') as migratedon
     , ifnull(ptype.dspvalue,'') as proctype
     , ifnull(ctype.dspvalue,'') as collecttype
     , concat(ifnull(dtl.initialmetric,''),' ', ifnull(mtuom.dspvalue,'')) as metuom
     , ifnull(prpt.dspvalue,'')  as pathologyrpt
     , concat(ifnull(pxi.pxiAge,''),' ', ifnull(pxi.pxiAgeUOM,'')) as pxiage
     , ifnull(pxi.pxirace,'') as pxirace
     , ifnull(pxi.pxigender,'') as pxisex
     , ifnull(pxi.subjectnumber,'') as subjectnumber
     , ifnull(pxi.protocolnumber,'') as protocolnumber
     , ifnull(pxi.InformedConsent,'') as informedconsent
     , ifnull(desig.specimencategory,'') as specimencategory
     , trim(concat(ifnull(desig.primarysite,''),' ',if(ifnull(desig.primarysubsite,'') = '','',concat(' (',ifnull(desig.primarysubsite,''),')')),  if( ifnull(desig.siteposition,'') = '','', concat( ' / ', ifnull(desig.siteposition,''))))) as asite
     , trim(concat(ifnull(desig.diagnosis,''), if(ifnull(desig.diagnosismodifier,'')='','', concat(' (',ifnull(desig.diagnosismodifier,''),')')))) as diagnosismodifier
     , concat(ifnull(desig.metssite,''), if(ifnull(desig.metsdx,'') ='','',concat(' (',ifnull(desig.metsdx,''),')'))) as metsdx
     , ifnull(unknownmet,'') as unknownmet 
     , ifnull(voidind,0) as voidind
     , ifnull(voidreason,'') as voidreason
     , ifnull(bscmt.comment,"") as bscomment
     , ifnull(hprcmt.comment,"") as hprcomment
from 
(SELECT substr(pbiosample,1,5) as pbiosampledspNbr, fromlocation as procinstitution, pbiosample as bslinkage, ifnull(migrated,0) as migrated, ifnull(date_format(migratedon, '%m/%d/%Y %H:%i'),'') as migratedon, date_format(inputon, '%H:%i') as timeprocured, inputby, selector, voidind, voidreason FROM four.ut_procure_biosample where date_format(inputon,'%Y-%m-%d') = :procdate and fromLocation = :procloc and recordstatus = 2 
union 
SELECT substr(pbiosample,1,5)
     , fromlocation procinstitution
     , ''
     , ifnull(migrated,0) as migrated
     , ifnull(date_format(migratedon, '%m/%d/%Y %H:%i'),'') as migratedon
     , date_format(inputon, '%H:%i') as timeprocured
     ,''
     , '' as selector
     , ifnull(voidind,0) as voidind
     , ifnull(voidreason,'') as voidreason 
FROM four.ut_procure_biosample 
where date_format(inputon,'%Y-%m-%d') = :procdateremote and fromLocation <> :proclocremote and recordstatus = 2) as bslist 
left join (select * from four.ref_procureBiosample_details where activeind = 1) as dtl on bslist.bslinkage = dtl.pbiosample 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') as ptype on dtl.proctype = ptype.menuvalue 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'COLLECTIONT') as ctype on dtl.collectionmethod = ctype.menuvalue 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'METRIC') as mtuom on dtl.initialUOM = mtuom.menuvalue 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as prpt on dtl.pathreportind = prpt.menuvalue 
left join (SELECT menuvalue, if( ifnull(longvalue,'') = '',ifnull(dspvalue,''), ifnull(longvalue,'')) as dspvalue  FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on bslist.procinstitution = inst.menuvalue 
left join (SELECT pbiosample, pxiage, pxirace, pxiageuom, pxigender, subjectnumber, protocolnumber, ic.dspvalue as informedconsent FROM four.ref_procureBiosample_PXI pxi 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INFC') ic on pxi.informedconsent = ic.menuvalue where activeind = 1) as pxi on bslist.bslinkage = pxi.pbiosample 
left join (SELECT pbiosample , ifnull(speccat,'') as specimencategory, ifnull(primarysite,'') as primarysite , ifnull(primarysubsite,'') as primarysubsite, ifnull(diagnosis,'') as diagnosis , ifnull(diagnosismodifier,'') as diagnosismodifier, ifnull(metssite,'') as metssite , ifnull(metsdx,'') as metsdx , ifnull(siteposition,'') as siteposition, ifnull(systemdiagnosis,'') as systemicdiagnosis , ifnull(classification,'') as classification, ifnull(uni.dspvalue,'') as unknownmet 
           FROM four.ref_procureBiosample_designation desig 
           left join (SELECT menuvalue, dspvalue 
                      FROM four.sys_master_menus where menu = 'UNINVOLVEDIND') as uni on desig.unknownMet = uni.menuvalue 
                      where activeind = 1) as desig on bslist.bslinkage = desig.pbiosample 
left join (SELECT pbiosample, comment FROM four.ref_procureBiosample_comment where trim(ifnull(comment,'')) <> '' and activeind = 1 and modulereference = 'BIOSPECIMENCMT') as bscmt on bslist.bslinkage = bscmt.pbiosample
left join (SELECT pbiosample, comment FROM four.ref_procureBiosample_comment where trim(ifnull(comment,'')) <> '' and activeind = 1 and modulereference = 'HPRQUESTION') as hprcmt on bslist.bslinkage = hprcmt.pbiosample
order by pbiosampledspnbr desc 
SQLSTMT;
   break;

   case 'segproclist':
      $rtnthis = <<<SEGSQL
select * from (SELECT sg.pbiosample, sg.seglabel, sg.bgs, ifnull(sg.hrpost,0) as hrpost, ifnull(sg.metric,'') as metric, if(ifnull(sg.metric,'') = '','',ifnull(uom.dspvalue,'')) as shortuom, if(ifnull(sg.metric,0) = '','',ifnull(uom.longvalue,'')) as longuom, ifnull(prpm.dspvalue,'') as prepmethod, ifnull(sg.prp,'') as prpcode, ifnull(prpd.longvalue,'') as prpdetail, ifnull(sg.prpmet,'') as prpmetdetail, ifnull(sg.prpcontainer,'') as containercode, ifnull(pcnt.longvalue,'') as container, ifnull(sg.cutfromblockid,'') as cutfromblockid, ifnull(sg.qty,1) as qty, ifnull(sg.hprind,0) as hprind, ifnull(sg.procuredAt,'') as procuredat, ifnull(sg.procuredby,'') as procuredby , ifnull(sg.dspname,'') as assigndspname, ifnull(sg.investid,'') as assigninvestid, ifnull(sg.requestid,'') as assignrequestid, ifnull(sg.voidind,0) as voidind, ifnull(sg.voidreason,'') as voidreason, ifnull(date_format(sg.inputON, '%H:%i (%m/%d/%Y)'),'') as proctime FROM four.ut_procure_segment sg left join four.ut_procure_biosample bs on sg.pbiosample = bs.pbiosample left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') uom on sg.metricuom = uom.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPMETHOD') prpm on sg.prp = prpm.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPDETAIL') prpd on sg.prpMet = prpd.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'CONTAINER') pcnt on sg.prpcontainer = pcnt.menuvalue where bs.selector = :selector and sg.activeind = 1 and (if(sg.hprind = 0, sg.prp <> 'SLIDE', '1=1' )) union SELECT sg.pbiosample, min(sg.seglabel) as seglabel, concat(sg.pbiosample,'T', min(sg.seglabel),  if(max(sg.seglabel) = min(sg.seglabel),'', concat('-',max(sg.seglabel)) ),' (' , count(1), ')') bgs  ,ifnull(sg.hrpost,0) as hrpost, if(ifnull(sg.metric,0)=0,'',ifnull(sg.metric,0)) as metric, if(ifnull(sg.metric,0) = 0,'',ifnull(uom.dspvalue,'')) as shortuom, if(ifnull(sg.metric,0) = 0,'',ifnull(uom.longvalue,'')) as longuom, ifnull(prpm.dspvalue,'') as prepmethod, ifnull(sg.prp,'') as prpcode, ifnull(prpd.longvalue,'') as prpdetail, ifnull(sg.prpmet,'') as prpmetdetail, ifnull(sg.prpcontainer,'') as containercode, ifnull(pcnt.longvalue,'') as container, ifnull(sg.cutfromblockid,'') as cutfromblockid, ifnull(sg.qty,1) as qty, ifnull(sg.hprind,0) as hprind, ifnull(sg.procuredAt,'') as procuredat, ifnull(sg.procuredby,'') as procuredby , ifnull(sg.dspname,'') as assigndspname, ifnull(sg.investid,'') as assigninvestid, ifnull(sg.requestid,'') as assignrequestid, ifnull(sg.voidind,0) as voidind, ifnull(sg.voidreason,'') as voidreason, ifnull(date_format(sg.inputON, '%H:%i (%m/%d/%Y)'),'') as proctime FROM four.ut_procure_segment sg left join four.ut_procure_biosample bs on sg.pbiosample = bs.pbiosample left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') uom on sg.metricuom = uom.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPMETHOD') prpm on sg.prp = prpm.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'PREPDETAIL') prpd on sg.prpMet = prpd.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'CONTAINER') pcnt on sg.prpcontainer = pcnt.menuvalue where bs.selector = :selectorunion and sg.activeind = 1 and (if(sg.hprind = 0, sg.prp = 'SLIDE', ' sg.hprind = 0' )) group by sg.pbiosample, ifnull(sg.hrpost,0), if(ifnull(sg.metric,0)=0,'',ifnull(sg.metric,0)), if(ifnull(sg.metric,0) = 0,'',ifnull(uom.dspvalue,'')), if(ifnull(sg.metric,0) = 0,'',ifnull(uom.longvalue,'')), ifnull(prpm.dspvalue,''), ifnull(sg.prp,''), ifnull(prpd.longvalue,''), ifnull(sg.prpmet,''), ifnull(sg.prpcontainer,''), ifnull(pcnt.longvalue,''), ifnull(sg.cutfromblockid,''), ifnull(sg.qty,1), ifnull(sg.hprind,0), ifnull(sg.procuredAt,''), ifnull(sg.procuredby,'')  , ifnull(sg.dspname,''), ifnull(sg.investid,'') , ifnull(sg.requestid,'') , ifnull(sg.voidind,0) , ifnull(sg.voidreason,'') , ifnull(date_format(sg.inputON, '%H:%i (%m/%d/%Y)'),'') ) conglomTbl order by seglabel
SEGSQL;
   break;
}
return $rtnthis;

}
