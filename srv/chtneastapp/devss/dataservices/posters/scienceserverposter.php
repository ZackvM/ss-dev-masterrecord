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
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING " . json_encode($request),"ITEMSFOUND" => 0, "DATA" => array()    ));
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

    function inventoryactiondestroy ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 
      $usrpin = chtndecrypt($pdta['ipin'], true);
      $at = genAppFiles;

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sid and allowind = 1 and allowInvtry = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sid' => $sessid));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO STATUS SEGMENTS AS DESTROYED. LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     ( count($pdta['bgslist']) < 1 ) ? list($errorInd, $msgArr[]) = array(1,"YOU HAVE NOT SCANNED ANY SAMPLES.") :  "";

     if ( $errorInd === 0 ) { 
       $chkSQL = "SELECT bgs, ifnull(segstatus,'') as segstatus, ifnull(sgs.longValue,'') as segstatusdsp, ifnull(sgs.assignablestatus,0) as assignablestatus, ifnull(shipdocrefid,'') as shipdocrefid, ifnull(shippeddate,'') as shippeddate, ifnull(voidind,0) as voidind FROM masterrecord.ut_procure_segment sg left join ( SELECT menuvalue, longvalue, assignablestatus FROM four.sys_master_menus where menu = 'segmentstatus' ) as sgs on sg.segstatus = sgs.menuvalue where replace(bgs,'_','') = :bgs";
       $chkRS = $conn->prepare( $chkSQL );
       foreach ( $pdta['bgslist'] as $k => $v ) {
         $chkRS->execute(array( ':bgs' => preg_replace('/^ED/i','', preg_replace('/\_/','',$v)) ));
         if ( $chkRS->rowCount() < 1 ) { 
             list($errorInd, $msgArr[]) = array(1,"{$v} WAS NOT FOUND IN MASTERRECORD.  IF THIS IS IN ERROR, SEE CHTNINFORMATICS.");
         } else {
             $bgs = $chkRS->fetch(PDO::FETCH_ASSOC);
             //{"bgs":"47604A1H","segstatus":"PENDDEST","segstatusdsp":"Pending Destroy","assignablestatus":1,"shipdocrefid":"","shippeddate":"","voidind":0}
             ( (int)$bgs['voidind'] <>  0  ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, IS A VOIDED BIOSAMPLE SEGMENT. PROCESS ABORTED.") : "";
             ( trim($bgs['segstatus']) !== 'PENDDEST'  ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, IS STATUSED '" . strtoupper($bgs['segstatusdsp']) . "' AND MAY NOT BE STATUSED AS 'DESTROYED'. PROCESS ABORTED.") : "";
             ( trim($bgs['shipdocrefid']) !== "" ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, APPEARS ON SHIP-DOC " . substr(('000000' . $bgs['shipdocrefid']),-6) . " AND MAY NOT BE STATUSED AS 'DESTROYED'. PROCESS ABORTED.") : "";
             ( trim($bgs['shippeddate']) !== "" ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, HAS A SHIPMENT DATE AND MAY NOT BE STATUSED AS 'DESTROYED'. PROCESS ABORTED.") : "";
         } 
       }

       if ( $errorInd === 0 ) { 
         //Go AHEAD AND RESTATUS

         $hisSQL = "insert into masterrecord.history_procure_segment_status( segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) SELECT segmentid, segstatus, statusBy, statusDate, now(), :u, 'DESTROYED' FROM masterrecord.ut_procure_segment where replace(bgs,'_','')  = :bgs";
         $hisRS = $conn->prepare( $hisSQL );

         $updSQL = "update masterrecord.ut_procure_segment set segstatus = 'DESTROYED', statusdate = now(), statusby = :updater where replace(bgs,'_','')  = :bgs ";
         $updRS = $conn->prepare( $updSQL );

         foreach ( $pdta['bgslist'] as $k => $v ) {
           $hisRS->execute(array( ':bgs' => preg_replace('/^ED/i','',preg_replace('/\_/','',$v)), ':u' => $u['emailaddress']  ));
           $updRS->execute(array( ':bgs' => preg_replace('/^ED/i','', preg_replace('/\_/','',$v)), ':updater' => $u['emailaddress'] )); 
         }    
         $responseCode = 200;
       }
     }

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;         
    } 



    function inventoryactionpdestroy ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 
      $usrpin = chtndecrypt($pdta['ipin'], true);
      $at = genAppFiles;
      //{"bgslist":{"0":"47604A1H","1":"87871T001"},"ipin":"wz7o0LLerYW0ufUK+prP+OYRUL4GmevO9N/8h9DKjoInRoQYLxw5yVF7mzePXS5xNpsjDUAeJ+UPmLVsBJSa8+NlmFBRLW/zZXlMYPhpVTZNnuZo3pq22ESZnth/SkD4ipJIlDSpc3/pCl3i5JWHZhIT+zFy7etzBYnZGlL23wzZRViKrUtk0rhahVMeJLc+fHAdKr0PHSWmZtiwtEnx+7Zw+3zY0DDmvCSzS8XJBa69P77WsLESpfI/yoNDYD+751GEklTG0FxKfXcQomotCa4ATORckrM/+4GMFaxmEcpeldHApoqMix1JleoRH9aelE3ZJz3Wh+94AJfrQxtimA=="}

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sid and inventorypinkey = :ipin and allowind = 1 and allowInvtry = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sid' => $sessid, ':ipin' => $usrpin ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO STATUS SEGMENTS AS PENDING DESTROY.  CHECK YOUR INVENTORY OVER-RIDE PIN.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     ( count($pdta['bgslist']) < 1 ) ? list($errorInd, $msgArr[]) = array(1,"YOU HAVE NOT SCANNED ANY SAMPLES.") :  "";

     if ( $errorInd === 0 ) { 
       $chkSQL = "SELECT bgs, ifnull(segstatus,'') as segstatus, ifnull(sgs.longValue,'') as segstatusdsp, ifnull(sgs.assignablestatus,0) as assignablestatus, ifnull(shipdocrefid,'') as shipdocrefid, ifnull(shippeddate,'') as shippeddate, ifnull(voidind,0) as voidind FROM masterrecord.ut_procure_segment sg left join ( SELECT menuvalue, longvalue, assignablestatus FROM four.sys_master_menus where menu = 'segmentstatus' ) as sgs on sg.segstatus = sgs.menuvalue where replace(bgs,'_','') = :bgs";
       $chkRS = $conn->prepare( $chkSQL );
       foreach ( $pdta['bgslist'] as $k => $v ) {
         $chkRS->execute(array( ':bgs' => preg_replace('/^ED/i','', preg_replace('/\_/','',$v)) ));
         if ( $chkRS->rowCount() < 1 ) { 
             list($errorInd, $msgArr[]) = array(1,"{$v} WAS NOT FOUND IN MASTERRECORD.  IF THIS IS IN ERROR, SEE CHTNINFORMATICS.");
         } else {
             $bgs = $chkRS->fetch(PDO::FETCH_ASSOC);
             //{"bgs":"47604A1H","segstatus":"PENDDEST","segstatusdsp":"Pending Destroy","assignablestatus":1,"shipdocrefid":"","shippeddate":"","voidind":0}
             ( (int)$bgs['voidind'] <>  0  ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, IS A VOIDED BIOSAMPLE SEGMENT. PROCESS ABORTED.") : "";
             ( (int)$bgs['assignablestatus'] === 0  ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, IS STATUSED '" . strtoupper($bgs['segstatusdsp']) . "' AND MAY NOT BE STATUSED AS 'PENDING DESTROY'. PROCESS ABORTED.") : "";
             ( trim($bgs['shipdocrefid']) !== "" ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, APPEARS ON SHIP-DOC " . substr(('000000' . $bgs['shipdocrefid']),-6) . " AND MAY NOT BE STATUSED AS 'PENDING DESTROY'. PROCESS ABORTED.") : "";
             ( trim($bgs['shippeddate']) !== "" ) ? list($errorInd, $msgArr[]) = array(1,"SEGMENT LABEL, {$v}, HAS A SHIPMENT DATE AND MAY NOT BE STATUSED AS 'PENDING DESTROY'. PROCESS ABORTED.") : "";
         } 
       }

       if ( $errorInd === 0 ) { 
         //Go AHEAD AND RESTATUS

         $hisSQL = "insert into masterrecord.history_procure_segment_status( segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) SELECT segmentid, segstatus, statusBy, statusDate, now(), :u, 'PENDDEST' FROM masterrecord.ut_procure_segment where replace(bgs,'_','')  = :bgs";
         $hisRS = $conn->prepare( $hisSQL );

         $updSQL = "update masterrecord.ut_procure_segment set segstatus = 'PENDDEST', statusdate = now(), statusby = :updater where replace(bgs,'_','')  = :bgs ";
         $updRS = $conn->prepare( $updSQL );

         foreach ( $pdta['bgslist'] as $k => $v ) {
           $hisRS->execute(array( ':bgs' => preg_replace('/^ED/i','',preg_replace('/\_/','',$v)), ':u' => $u['emailaddress']  ));
           $updRS->execute(array( ':bgs' => preg_replace('/^ED/i','', preg_replace('/\_/','',$v)), ':updater' => $u['emailaddress'] )); 
         }    
         $responseCode = 200;
       }
     }

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;         
    } 

    function usertogglemodheader ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 
      $at = genAppFiles;
      $si = serverIdent;
      $sp = serverpw;
      

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and accessnbr > 42"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     
     $emlid = cryptservice( $pdta['uency'] , 'd') ;  
     $msgArr[] = $emlid;
         //$backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, :userupdater as historyby FROM four.sys_userbase where emailaddress = :emladd";
         //$backupRS = $conn->prepare($backupSQL);
         //$backupRS->execute( array(':emladd' => $emlid, ':userupdater' => "{$u['emailaddress']} (USER ADMIN [MOD HEAD CHANGE])"   ));
     

         $msgArr[] = $pdta['toggleind'];
         $msgArr[] = $pdta['togglevalue'];
     
     
     
     
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;         
    }
    

    function usergetspecificsdsp ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 
      $at = genAppFiles;
      $si = serverIdent;
      $sp = serverpw;

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and accessnbr > 42"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     //DATA CHECKS HERE
     if ( $errorInd === 0 ) {
         $emlid = cryptservice( $pdta['uency'] , 'd') ;

         $userSQL = "SELECT emailaddress, userid, failedlogins, friendlyname, lastname, originalaccountname"
                 . ", allowInd as ind_allow_System_Access"
                 . ", informaticsind as ind_allow_Informatics"
                 . ", freshNotificationInd as ind_allow_Fresh_Notification"
                 . ", allowWeeklyUpdate as ind_allow_Weekly_Email_Notification"
                 . ", allowlinux as ind_allow_Donor_Vault_Access"
                 . ", allowProc as ind_allow_Procurement"
                 . ", allowCoord as ind_allow_Data_Coordinator"
                 . ", allowHPR as ind_allow_HPR"
                 . ", allowQMS as ind_allow_QMS"
                 . ", allowHPRInquirer as ind_allow_HPR_Inquiry"
                 . ", allowHPRReview as ind_allow_HPR_Review"
                 . ", allowInvtry as ind_allow_Inventory"
                 . ", allowfinancials as ind_allow_Financials"
                 . ", displayName, userName, dspjobtitle, primaryFunction, primaryInstCode, inst.dspvalue as primaryInst, date_format(passwordExpireDate,'%m/%d/%Y') as passwordExpireDate, inputBy, date_format(inputon, '%m/%d/%Y') as inputon, accessLevel, al.dspvalue accesslvldsp , accessNbr, date_format(lastUpdatedOn,'%m/%d/%Y') as lastupdatedon, lastUpdatedBy, inventorypinkey, sex, profilePicURL, profileAltEmail, profilePhone, ifnull(dlExpireDate,'') as dlexpiredate, altPhone, altPhoneType, altPhoneCellCarrier, cellcarriercode FROM four.sys_userbase ub left join ( SELECT ifnull(menuvalue,'UNKNOWN') as menuvalue, dspvalue FROM four.sys_master_menus where menu = 'accssLVL') al on ub.accessLevel = al.menuvalue left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION' ) as inst on ub.primaryinstcode = inst.menuvalue where emailaddress = :passedEmail";
         $userRS = $conn->prepare( $userSQL ); 
         $userRS->execute(array(':passedEmail' => $emlid  ));
         if ( $userRS->rowCount() !== 1 ) {  
             $msgArr[] = $emlid . " not found in system.  See CHTNEastern Informatics";
         } else { 
             $user = $userRS->fetch(PDO::FETCH_ASSOC);
             $uacct = strtoupper( $user['emailaddress'] );
             $uacctnbr = substr(("000000" . $user['userid']) ,-6);
             if ( $user['profilePicURL'] == 'avatar_male' ||  $user['profilePicURL'] == 'avatar_female' ) { 
               $profilepic = base64file("{$at}/publicobj/graphics/usrprofile/{$user['profilePicURL']}.png", "profilePicDsp", "png", true, " class=sidebarprofilepicture ");
             } else {
               $profilepic = base64file("{$at}/publicobj/graphics/usrprofile/{$user['profilePicURL']}", "profilePicDsp", "png", true, " class=sidebarprofilepicture ");
             }

             //TODO:  Make Webservice
             $accLvlSQL = "SELECT concat(ifnull(menuvalue,'UNKNOWN'),':', ifnull(assignablestatus,0)) as menuvalue, dspvalue FROM four.sys_master_menus where menu = 'accssLVL' and dspind = 1 order by dsporder";
             $accessLvlRS = $conn->prepare( $accLvlSQL ); 
             $accessLvlRS->execute();
             //<tr><td align=right onclick=\"fillField('updFldAccessLvl','','');\" class=ddMenuClearOption>[clear]</td></tr>
             $acclvl = "<table border=0 class=menuDropTbl>";
             while ($hprtval = $accessLvlRS->fetch(PDO::FETCH_ASSOC)) {
               $acclvl .= "<tr><td onclick=\"fillField('updFldAccessLvl','{$hprtval['menuvalue']}','{$hprtval['dspvalue']}');\" class=ddMenuItem>{$hprtval['dspvalue']}</td></tr>";
             }
             $acclvl .= "</table>";
       
             $procinstarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/allinstitutions",$si,$sp),true);
             //<tr><td align=right onclick=\"fillField('qryProcInst','','');\" class=ddMenuClearOption>[clear]</td></tr>
             $proc = "<table border=0 class=menuDropTbl>";
             foreach ($procinstarr['DATA'] as $procval) { 
               $proc .= "<tr><td onclick=\"fillField('updFldPrimaryInst','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
             }
             $proc .= "</table>";

             $sex = "<table border=0 class=menuDropTbl>";
             $sex .= "<tr><td onclick=\"fillField('updFldUserGender','F','Female');\" class=ddMenuItem>Female</td></tr>";
             $sex .= "<tr><td onclick=\"fillField('updFldUserGender','M','Male');\" class=ddMenuItem>Male</td></tr>";
             $sex .= "<tr><td onclick=\"fillField('updFldUserGender','O','Other/Non-Specified');\" class=ddMenuItem>Other/Non-Specified</td></tr>";
             $sex .= "</table>";

             $sexValue = $user['sex'];
             switch ( $sexValue ) {
               case 'M':
                 $sexDsp = 'Male';
                 break;
               case 'F':
                 $sexDsp = 'Female';
                 break;
               case 'O':
                 $sexDsp = 'Other/Non-Specified';
                 break;    
             }

             $ccarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/cell-carriers",$si,$sp),true);
             $ccDfltVal = $user['cellcarriercode'];
             $ccDfltDsp = "";
             $cc = "<table border=0 class=menuDropTbl>";
             foreach ($ccarr['DATA'] as $ccval) {
               if ( $ccDfltVal === $ccval['lookupvalue'] ) { 
                 $ccDfltDsp = $ccval['menuvalue']; 
               } 
               $cc .= "<tr><td onclick=\"fillField('updFldCellCarrier','{$ccval['lookupvalue']}','{$ccval['menuvalue']}');\" class=ddMenuItem>{$ccval['menuvalue']}</td></tr>";
             }
             $cc .= "</table>";
             
             $allows = "";
             $allowCntr = 0;
             foreach ( $user as $ukey => $uval ) {
               if ( substr( $ukey,0,10) === 'ind_allow_' ) {  
                $chkd = ( (int)$uval === 1 ) ? "CHECKED" : "";
                $allowedInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" onchange=\"sendChangeModHeaderRequest('{$ukey}',this.checked);\" class=\"checkboxThreeInput\" id=\"checkbox{$allowCntr}Input\"  {$chkd}  /><label for=\"checkbox{$allowCntr}Input\"></label></div>";
                $allows .= "<div class=allowDspDiv><div class=allowLabelDsp>" . preg_replace('/_/',' ', preg_replace('/ind_allow_/','',$ukey)) . "</div><div class=allowIndicator>{$allowedInd}</div></div>";
                $allowCntr++;
               }
             }

             $instSQL = "SELECT ucase(instlist.menuvalue) as institutioncode, instlist.dspvalue as institutionname, ifnull(ainst.onoffind,0) as onoffind FROM four.sys_master_menus as instlist left join (select * from four.sys_userbase_allowinstitution where userid = :userid and onoffind = 1) ainst on instlist.menuid = ainst.institutionmenuid where menu = 'INSTITUTION'  order by instlist.menuvalue ";
             $instRS = $conn->prepare( $instSQL );
             $instRS->execute( array( ':userid' => $user['userid'] ));
         
             $divInstList = "<div id=instListHolder>";
             while ( $i = $instRS->fetch(PDO::FETCH_ASSOC) ) {
               $chkd = ( (int)$i['onoffind'] === 1 ) ? "CHECKED" : "";
               $iAllowedInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" class=\"checkboxThreeInput\" id=\"checkbox{$i['institutioncode']}Input\"  {$chkd}  /><label for=\"checkbox{$i['institutioncode']}Input\"></label></div>";
               $divInstList .= "<div class=instListDiv><div class=iAllowName>{$i['institutionname']}</div><div class=iAllowHold>  {$iAllowedInd} </div></div>";
             }
             $divInstList .= "</div>";


             $modSQL = "SELECT md.menuid as modid, ucase(md.menuvalue) as module, ifnull(modu.onoffind,0) as onoffind FROM four.sys_master_menus md left join (select * from four.sys_userbase_modules where userid = :userid and onoffind = 1) modu on md.menuid = modu.moduleid where md.menu = 'SS5MODULES' order by md.dsporder";
             $modRS = $conn->prepare($modSQL);
             $modRS->execute(array( ':userid' => $user['userid'] )); 

             $divModList = "<div id=modListHolder>";
             while ( $m = $modRS->fetch(PDO::FETCH_ASSOC)) { 
               $chkd = ( (int)$m['onoffind'] === 1 ) ? "CHECKED" : "";
               $mAllowedInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" class=\"checkboxThreeInput\" id=\"checkbox{$m['modid']}Input\"  {$chkd}  /><label for=\"checkbox{$m['modid']}Input\"></label></div>";
               $divModList .= "<div class=modListDiv><div class=mAllowName>{$m['module']}</div><div class=mAllowHold>  {$mAllowedInd} </div></div>";
             }
             $divModList .= "</div>";


             $dta = <<<PGCONTENT
<div id=userSideHeader>USER ACCOUNT: {$uacct} ({$uacctnbr})<input type=hidden id=updFldIdent value="{$pdta['uency']}"></div>

<div id=mainPageHolder>

  <div id=picDspBar>   
    <div id=profilePictHold>{$profilepic}</div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Account Name</div><div class=dataElementDataFld>{$uacct}</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Account #</div><div class=dataElementDataFld>{$uacctnbr}</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>User Created On / By</div><div class=dataElementDataFld>{$user['inputon']} ({$user['inputBy']})</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Last Modified On</div><div class=dataElementDataFld>{$user['lastupdatedon']}</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Last Modified By</div><div class=dataElementDataFld>{$user['lastUpdatedBy']}</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Password Expires On</div><div class=dataElementDataFld>{$user['passwordExpireDate']}</div></div>
    <div class=dataElementHoldSide><div class=dataElementLabelSml>Failed Logins</div><div class=dataElementDataFld>{$user['failedlogins']}</div></div>
    <div class=dataElementHoldSide>
         <div class=dataElementLabelSml>Choose a Profile Picture File (Click Save...)</div>
         <div class=dataElementDataFld> <input type="file" name="file" id="ufile" class="inputfile" />  </div>
    </div>



  </div>

  <div id=dataDspBar>

    <div id=dataLineOne>
      <div class=dataElementHold id=divUserSex><div class=dataElementLabel>Gender</div><div class=dataElementData><div class=menuHolderDiv><input type=hidden id=updUserGenderValue value="{$sexValue}"><input type=text value="{$sexDsp}" id=updFldUserGender READONLY><div class=valueDropDown id=dropDownUserGender>{$sex}</div></div></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Friendly Name</div><div class=dataElementData><input type=text value="{$user['friendlyname']}" id=updFldFriendly></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Last Name</div><div class=dataElementData><input type=text value="{$user['lastname']}" id=updFldLastName></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Original Acct Name</div><div class=dataElementData><input type=text value="{$user['originalaccountname']}" id=updFldOriginalAcct READONLY></div></div>
      <div class=dataElementHold><div class=dataElementLabel>User's Name</div><div class=dataElementData><input type=text value="{$user['userName']}" id=updFldUsersName></div></div>
    </div>

    <div id=dataLineTwo>
      <div class=dataElementHold id=divDspJobTitle><div class=dataElementLabel>Job Title</div><div class=dataElementData><input type=text value="{$user['dspjobtitle']}" id=updFldJobTitle></div></div>
      <div class=dataElementHold id=divDspAccessLvl><div class=dataElementLabel>System Access</div><div class=dataElementData><div class=menuHolderDiv><input type=hidden id=updFldAccessLvlValue value="{$user['accessLevel']}:{$user['accessNbr']}"><input type=text value="{$user['accesslvldsp']}" id=updFldAccessLvl READONLY><div class=valueDropDown id=dropDownAccLvl>{$acclvl}</div></div></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Over-Ride PIN</div><div class=dataElementData><input type=text value="{$user['inventorypinkey']}" id=updFldOverridePIN></div></div>
    </div>

    <div id=dataLineThree>
      <div class=dataElementHold id=divDspAccessLvl><div class=dataElementLabel>Primary Institution</div><div class=dataElementData><div class=menuHolderDiv><input type=hidden id=updFldPrimaryInstValue value="{$user['primaryInstCode']}"><input type=text value="{$user['primaryInst']}" id=updFldPrimaryInst READONLY><div class=valueDropDown id=dropDownInst>{$proc}</div></div></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Primary Function</div><div class=dataElementData><input type=text value="{$user['primaryFunction']}" id=updFldPrimaryFunction></div></div>
    </div>

    <div id=dataLineFour>
      <div class=dataElementHold><div class=dataElementLabel>Office Phone</div><div class=dataElementData><input type=text value="{$user['profilePhone']}" id=updFldProfilePhone></div></div>
      <div class=dataElementHold><div class=dataElementLabel>SMS Phone (Cell #)</div><div class=dataElementData><input type=text value="{$user['altPhone']}" id=updFldSMSPhone></div></div>
      <div class=dataElementHold><div class=dataElementLabel>SMS Cell Carrier</div><div class=dataElementData><div class=menuHolderDiv><input type=hidden id=updFldCellCarrierValue value="{$ccDfltVal}"><input type=text value="{$ccDfltDsp}" id=updFldCellCarrier READONLY><div class=valueDropDown id=dropDownCC>{$cc}</div></div></div></div>
      <div class=dataElementHold><div class=dataElementLabel>Driver Expire (YYYY-mm-dd)</div><div class=dataElementData><input type=text value="{$user['dlexpiredate']}" id=updFldDLExpire></div></div>
    </div>

    <div id=dataLineFive>
      <div class=dataElementHold><div class=dataElementLabel>Alternate Email</div><div class=dataElementData><input type=text value="{$user['profileAltEmail']}" id=updFldAltEmail></div></div>
      <div align=right style="padding: 1vh 1vw 0 0;"> <button>Save</button> </div> 
    </div>

    <div id=dataLineAllows>
      <div id=allowHeader>Allowed System Components</div> 
      {$allows} 
    </div>

    <div id=dataLineModsAndInsts>
     <div class=modinst> <div class=allowHeaderInst>Allowed Institutions</div>{$divInstList}</div>
     <div class=modinst> <div class=allowHeaderInst>Allowed Modules</div>{$divModList}</div>
    </div>


  </div>


</div>


PGCONTENT;
           $responseCode = 200;
         }
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                               
    }

    function usersendunlock ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and accessnbr > 42"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     //DATA CHECKS HERE
     if ( $errorInd === 0 ) {
         $emlid = cryptservice( $pdta['uency'] , 'd') ;

         $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, :userupdater as historyby FROM four.sys_userbase where emailaddress = :emladd";
         $backupRS = $conn->prepare($backupSQL);
         $backupRS->execute( array(':emladd' => $emlid, ':userupdater' => "{$u['emailaddress']} (USER ADMIN [EMAIL RESET])"   ));

         $updSQL = "update four.sys_userbase set failedlogins = 0, lastupdatedon = now(), lastupdatedby = :updater where emailAddress = :emlid";
         $updRS = $conn->prepare($updSQL); 
         $updRS->execute(array( ':updater' => "{$u['emailaddress']} (UNLOCK UTIL)", ':emlid' => $emlid ));

         $responseCode = 200;
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                               
    } 

    function usersendresetpassword ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and accessnbr > 42"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       
     
     //DATA CHECKS HERE
     if ( $errorInd === 0 ) {
         $emlid = cryptservice( $pdta['uency'] , 'd') ;
         //TODO: CHECK USER EXISTS
         
         $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, :userupdater as historyby FROM four.sys_userbase where emailaddress = :emladd";
         $backupRS = $conn->prepare($backupSQL);
         $backupRS->execute( array(':emladd' => $emlid, ':userupdater' => "{$u['emailaddress']} (USER ADMIN [EMAIL RESET])"   ));

         $randomBytes = strtoupper(generateRandomString(9));
         $options = [ 'cost' => 12, ];        
         $pword =  password_hash($randomBytes, PASSWORD_BCRYPT, $options);
         $updSQL = "update four.sys_userbase set fiveonepword = :fiveonepword, allowind = 1, passwordexpiredate = date_add(now(), INTERVAL 2 day), lastupdatedon = now(), lastupdatedby = :thisadmin where emailaddress = :emailaddress";
         $updRS = $conn->prepare( $updSQL );
         $updRS->execute ( array ( ':fiveonepword' => $pword, ':thisadmin' => "{$u['emailaddress']} (PWORD RESET)", ':emailaddress' => $emlid   ));

         $sndMsg = <<<EMAILBODY
<table border>
<tr><td><h2>ScienceServer Password Reset Email</h2></td></tr>
<tr><td>Dear ______ <p>You or a system administrator has requested a password reset for access to ScienceServer at CHTNEast.  The following password is {$randomBytes}.  This password will valid for 2 days.  Please log into the system and use the 'Manage Access' Utility under the User Profile Sidebar.  <p>Thanks, <br>ScienceServer</td></tr>
</table> 
EMAILBODY;
         $usrinput = 'ADMIN-PASSWORD-RESET (' . $u['emailaddress'] . ")";

          $eText = "The following password: {$randomBytes} is valid for 2 days.  Once logged into ScienceServer, go to the 'Manage Access' utility under the User Profile Sidebar to change the password to something more permanent.";
          $emaillist[] = "zacheryv@mail.med.upenn.edu";
          $emaillist[] = $emlid;
          $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgBody, htmlind, wheninput, bywho) value(:towhoaddressarray, 'CHTNEastern ScienceServer PWord Reset', :msgBody, 1, now(), 'PWORD RESET EMAILER')";
          $emlRS = $conn->prepare( $emlSQL );
          $emlRS->execute(array ( ':towhoaddressarray' => json_encode( $emaillist ), ':msgBody' => "<table border=1><tr><td><CENTER>THE MESSAGE BELOW IS FROM THE SCIENCESERVER SYSTEM AT CHTNEAST.ORG ({$u['friendlyname']}/{$u['emailaddress']}) .  PLEASE DO NOT RESPONSED TO THIS EMAIL.  CONTACT THE CHTNEASTERN OFFICE DIRECTLY EITHER BY EMAIL chtnmail@uphs.upenn.edu OR BY CALLING (215) 662-4570.</td></tr><tr><td>{$eText}</td></tr></table>"));
       
       $responseCode = 200;     
         
     }     
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;                               
    }
    
    function usertoggleallow ( $request, $passdata ) { 
      $responseCode = 400;
      $rows = array();
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start(); 
      $sessid = session_id();      
      $pdta = json_decode($passdata, true); 
     
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress, accessnbr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and accessnbr > 42"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       
     
     //DATA CHECKS HERE
     
     if ( $errorInd === 0 ) {
         $emlid = cryptservice( $pdta['uency'] , 'd') ;
         //TODO: CHECK USER EXISTS

         $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, :userupdater as historyby FROM four.sys_userbase where emailaddress = :emladd";
         $backupRS = $conn->prepare($backupSQL);
         $backupRS->execute( array(':emladd' => $emlid, ':userupdater' => "{$u['emailaddress']} (USER ADMIN [ALLOW TOGGLE])"   ));
         
         if ( $pdta['toggleind'] ) { 
             $updSQL = "update four.sys_userbase set allowInd = 1, lastupdatedon = now(), lastupdatedby = :thisadmin where emailaddress = :emailaddress";
         } else { 
             $updSQL = "update four.sys_userbase set allowInd = 0, lastupdatedon = now(), lastupdatedby = :thisadmin where emailaddress = :emailaddress";
         }
         $updRS = $conn->prepare($updSQL); 
         $updRS->execute(array(':thisadmin' => "{$u['emailaddress']} (USER ADMIN)", ':emailaddress' => $emlid ));
         $responseCode = 200;   
     }        
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;               
        
    }

    function shipdocspecialserviceadd ( $request, $passdata ) { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     session_start(); 
     $sessid = session_id(); 
     //{"sdsrvcency":"dUFtblliYlB6SW45QTBxZ3V2ZVlrQT09","spcsrvcvalue":"SRVC.11","spcsrvcrate":"25","spcsrvcqty":"1.2"}
     $pdta = json_decode($passdata, true); 
     
     $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
     $rs = $conn->prepare($chkUsrSQL); 
     $rs->execute(array(':sessid' => $sessid ));
     if ( $rs->rowCount() <  1 ) {
       (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
     } else { 
       $u = $rs->fetch(PDO::FETCH_ASSOC);
     }       

     if ( $errorInd === 0 ) {
       $sd = cryptservice($pdta['sdsrvcency'], 'd' );
       $sdChkSQL = "SELECT sdstatus, institutiontype, investcode, investname FROM masterrecord.ut_shipdoc where shipdocrefid = :sdnbr and sdstatus <> 'CLOSED' "; 
       $sdChkRS = $conn->prepare( $sdChkSQL );
       $sdChkRS->execute( array( ':sdnbr' => (int)$sd ));
       ( $sdChkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "EITHER SHIPDOC {$sd} IS CLOSED OR DOESN'T EXIST.  YOU MAY NOT ADD SERVICES TO IT.")) : "";

       if ( $errorInd === 0 ) {

         $srvcCode = $pdta['spcsrvcvalue'];
         $srvcFeeTypeSQL = "SELECT ifnull(dspvalue,'') as dspvalue, ifnull(additionalinformation,'HR') as qtymetric FROM four.sys_master_menus where menuvalue = :srvcCode and dspind = 1";
         $srvcFeeTypeRS = $conn->prepare($srvcFeeTypeSQL); 
         $srvcFeeTypeRS->execute(array(':srvcCode' => $srvcCode));

         ( $srvcFeeTypeRS->rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE SERVICE FEE TYPE WAS NOT FOUND.  SEE CHTNEASTN INFORMATICS")) : "";

         if ( $errorInd === 0 ) { 
           $srvcFeeType = $srvcFeeTypeRS->fetch(PDO::FETCH_ASSOC);
    
           ( trim($pdta['spcsrvcrate']) === '' || trim($pdta['spcsrvcqty']) === '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Both Rate and Qty/HR are required fields")) : "";
           ( !is_numeric($pdta['spcsrvcrate']) || !is_numeric($pdta['spcsrvcqty']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Both Rate and Qty/HR can only be numeric fields")) : "";
           ( (double)$pdta['spcsrvcrate'] > 0 ) ? "" : (list( $errorInd, $msgArr[] ) = array(1 , "Rate must be greater than zero"));
           ( (double)$pdta['spcsrvcqty'] > 0 ) ? "" : (list( $errorInd, $msgArr[] ) = array(1 , "Qty/HR must be greater than zero"));
 
           if ( $errorInd === 0 ) {
             if ( $srvcFeeType['qtymetric'] === 'FL') {
               $ttlChrg =   ( (double)$pdta['spcsrvcrate'] * (int)$pdta['spcsrvcqty'] );
               $thisRate = (double)$pdta['spcsrvcrate'];
               $thisMetric = (int)$pdta['spcsrvcqty'];
             } else {  
               $ttlChrg =   ( (double)$pdta['spcsrvcrate'] * (double)$pdta['spcsrvcqty'] );
               $thisRate = (double)$pdta['spcsrvcrate'];
               $thisMetric = (double)$pdta['spcsrvcqty'];
             }
             $insSQL = "insert into masterrecord.ut_shipdoc_spcsrvfee ( shipdocrefid, dspInd, srvcfeecode, srvfeedsp, basecharge, qtymetric, totalfee, inputon, inputby) values(:sd, 1, :srvcfeecode, :srvfeedsp, :basecharge, :qtymetric, :totalfee, now(), :inputby)";
             $insRS = $conn->prepare($insSQL); 
             $insRS->execute(array(':sd' => $sd,':srvcfeecode' => $srvcCode,':srvfeedsp' => $srvcFeeType['dspvalue'],':basecharge' => $thisRate,':qtymetric' => $thisMetric,':totalfee' => $ttlChrg,':inputby' => $u['emailaddress']));
             $responseCode = 200;
           }
         }
       }
     } 

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;            
    }

    function vaulticbiogroupupdate ( $request, $passdata ) { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode($passdata, true); 
     foreach ( $pdta as $key => $value ) {
       if ( !cryptservice($key,'d') ) { 
         $locarr[ $key ] = $value; 
       } else { 
         $locarr[ cryptservice($key,'d') ] = chtndecrypt( $value );
       }
     }
     ( !array_key_exists('fileselector', $locarr) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'fileselector' DOES NOT EXIST.")) : ""; 
     ( !array_key_exists('bgroupdelimit', $locarr) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'bgroupdelimit' DOES NOT EXIST.")) : ""; 
     ( !array_key_exists('icid', $locarr) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'icid' DOES NOT EXIST.")) : ""; 
     if ( $errorInd === 0 ) {
       ( trim($locarr['fileselector']) === '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FILE SELECTOR CANNOT BE EMPTY.")) : "";
       ( trim($locarr['icid']) === '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "INFORMED CONSENT ID MUST BE SUPPLIED.")) : "";
       ( trim($locarr['bgroupdelimit']) === '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SPECIFIED ANY BIOGROUPS.")) : "";
       if ( $errorInd === 0 ) {  
         $bgList = explode( ',', trim( str_replace(' ','',$locarr['bgroupdelimit'])));
         $bgChkSQL = "SELECT pbiosample FROM masterrecord.ut_procure_biosample where replace(read_Label,'_','') = :bglabel and ifnull(dvaulticselector,'') = '' and voidind <> 1 "; 
         $bgChkRS = $conn->prepare($bgChkSQL);
         foreach ( $bgList as $k => $v ) { 
           $bgChkRS->execute(array(':bglabel' => $v));
           ( $bgChkRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Biogroup Label {$v} either doesn't exist or already has an informed consent uploaded.")) : "";
         }
         if ( $errorInd === 0 ) { 
           $updSQL = "update masterrecord.ut_procure_biosample set informedConsent = 2, dvaulticselector = :fselector, dvaulticuploadon = now(), dvaulticuploadby = :uname where replace(read_Label,'_','') = :bglbl";
           $updRS = $conn->prepare( $updSQL );            
           foreach ( $bgList as $bgk => $bgv ) {   
              $updRS->execute(array( ':bglbl' => $bgv, ':fselector' => $locarr['fileselector'], ':uname' => $locarr['vaultuser'] ));
              $responseCode = 200;
           }
         }
       }
     } 
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;            
    }

    function vaultcheckpxiids ( $request, $passdata ) { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode( $passdata, true);
 
     $chkSQL = "SELECT replace(read_Label,'_','') as readlabel FROM masterrecord.ut_procure_biosample where pxiid = :pxiid";
     $chkRS = $conn->prepare($chkSQL);
     foreach ( $pdta['pxilist'] as $value ) { 
       $chkRS->execute(array(':pxiid' => $value)); 
       if ( $chkRS->rowCount() > 0 ) { 
         while ( $r = $chkRS->fetch(PDO::FETCH_ASSOC)) {   
           $itemsfound++;
           //$bg = $chkRS->fetch(PDO::FETCH_ASSOC);
           $dta[] = $r['readlabel'];    
         }
       } 
     }

     $responseCode = 200;

     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;            
    }

    function vaultmarkprno ( $request, $passdata ) { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $pdta = json_decode( $passdata, true);
     //[\"{\\\"user\\\":\\\"zacheryv@mail.med.upenn.edu\\\",\\\"bglist\\\":\\\"[\\\\\\\"88338T\\\\\\\",\\\\\\\"88339T\\\\\\\"]\\\",\\\"reason\\\":\\\"These are normal foreskin samples and should never have had the indicator for a pathology report\\\"

     ( !array_key_exists('user', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'user' DOES NOT EXIST.")) : ""; 
     ( !array_key_exists('bglist', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'bglist' DOES NOT EXIST.")) : ""; 
     ( !array_key_exists('reason', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'reason' DOES NOT EXIST.")) : ""; 


     if ( $errorInd === 0 ) {

       //CHECK USER 
       $chkUSQL = "SELECT userid FROM four.sys_userbase where emailAddress = :useremail and allowind = 1 and allowlinux = 1 and allowCoord = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and TIMESTAMPDIFF(MINUTE, now(), pxisessionexpire) > 0";       
       $chkURS = $conn->prepare( $chkUSQL );
       $chkURS->execute( array( ':useremail' => $pdta['user'] ));
       ( $chkURS->rowCount() < 1 ) ? ( list( $errorInd, $msgArr[] ) = array( 1 , "USER NOT ALLOWED ACCESS TO THE MAIN SCIENCESERVER DATABASE - EITHER SESSION EXPIRED OR CREDENTIALS INCORRECT") ) : "";
       $bglist = json_decode( $pdta['bglist'], true);
       ( count( $bglist ) < 1 ) ? ( list( $errorInd, $msgArr[] ) = array( 1 , "NO BIOGROUP READ LABELS SUPPLIED IN REQUEST") ) : "";
       ( trim($pdta['reason']) === "" ) ? ( list( $errorInd, $msgArr[] ) = array( 1 , "No Reason for changing biogroup's Pathology Report Status given") ) : "";
   
       if ( $errorInd === 0 ) { 

         $histSQL = "insert into masterrecord.history_procure_biosample_pathrpt (pbiosample, pathreportind, prid, changeby, changeon, reason) SELECT pbiosample, ifnull(pathreport,0) as pathreportind, ifnull(pathreportid,0) as pathreportid, :user, now(), :reason FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') = :bglabel";
         $histRS = $conn->prepare($histSQL);
         $updSQL = "update masterrecord.ut_procure_biosample set pathReport = 0 where replace(read_Label,'_','') = :bglabel";
         $updRS = $conn->prepare($updSQL);

         foreach ( $bglist as $v ) { 
           $histRS->execute(array(':user' => 'zacheryv@mail.med.upenn.edu', ':bglabel' => $v, ':reason' => $pdta['reason']  ));
           $updRS->execute(array(':bglabel' => $v ));
         }
         $responseCode = 200;
       }
     }
     $msg = $msgArr;
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array( 'RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;            
    }
    
        
    function vaultconsentdocquestions( $request, $passdata ) {
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = $whichobj;
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $cquestionSQL = "SELECT menuvalue, dspvalue, additionalInformation FROM four.sys_master_menus where menu = 'CONSENTDOCQUESTIONS' and parentID = :pid and dspind = 1 order by dsporder";
     $cqRS = $conn->prepare($cquestionSQL);
     $cqRS->execute(array(':pid' => $whichobj));
     if ( $cqRS->rowCount() > 0 ) {
       $itemsfound = $cqRS->rowCount();  
       while ($r = $cqRS->fetch(PDO::FETCH_ASSOC)) { 
         $dta[] = $r;
       }
       $responseCode = 200;
     }
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;     
  }
    
    function vaultconsentdocumentlisting ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;        
      
      $prSQL = "SELECT menuvalue, dspvalue, useasdefault FROM four.sys_master_menus where menu = 'CONSENTDOCUMENTS' and dspind = 1 order by dsporder";
      $prRS = $conn->prepare($prSQL); 
      $prRS->execute();
      $itemsfound = $prRS->rowCount(); 
      while ($r = $prRS->fetch(PDO::FETCH_ASSOC) ) { 
          $dta[] = $r;
      }
 
      $responseCode = 200;
          
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
    
    function vaultgetuser ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
     
      
      ( !array_key_exists('pxiguidency', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'pxiguidency' DOES NOT EXIST.")) : ""; 
      
      $usrguid = explode("::",cryptservice($pdta['pxiguidency'],'d'));
      //$usrguid = cryptservice($pdta['pxiguidency'],'d');       
      $usrSQL = "SELECT ifnull(friendlyName,'') as friendlyname, ifnull(emailaddress,'') as userid, ifnull(date_format( pxisessionexpire, '%H:%i'),'') as expiretime, ifnull(originalAccountName,'') as origacctname"
              . ", ifnull(accessLevel,'') as accesslevel, ifnull(accessNbr,0) as accessnbr "
              . "FROM four.sys_userbase "
              . "where pxiguidident = :pxiguid "
              . "and timestampdiff(MINUTE,now(),pxisessionexpire) > 0 "
              . "and allowind = 1 "
              . "and allowlinux = 1 "
              . "and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 "
              . "and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0";
      $usrRS = $conn->prepare($usrSQL);
      $usrRS->execute(array(':pxiguid' => $usrguid[0]));
      if ( $usrRS->rowCount() <> 1 ) {
        (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  USER NOT FOUND {$usrguid[0]}"));
      } else { 
          $u = $usrRS->fetch(PDO::FETCH_ASSOC);
      }
      
      if ( $errorInd === 0 ) { 
         $dta = $u;    
         $responseCode = 200;    
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
   
    function vaultuserloginpwcheck ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;

      ( !array_key_exists('ency', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'ency' DOES NOT EXIST.")) : ""; 
      
      if ( $errorInd === 0 ) { 
        $vaultpw = json_decode( chtndecrypt($pdta['ency']), true );

        ( !array_key_exists('user', $vaultpw) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'user' DOES NOT EXIST.")) : "";
        ( !array_key_exists('pword', $vaultpw) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'pword' DOES NOT EXIST.")) : "";
       
        if ( $errorInd === 0 ) {
         $usrSQL = "SELECT userid, emailaddress FROM four.sys_userbase where allowInd = 1 and allowlinux = 1 and TIMESTAMPDIFF(MINUTE,now(),pxipasswordexpire) > 0 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and emailaddress = :usr and pxipassword = :pword";
         $usrRS = $conn->prepare($usrSQL);
         $usrRS->execute(array(':usr' => $vaultpw['user'], ':pword' => $vaultpw['pword']));
         if ( $usrRS->rowCount() < 1 ) {
           (list( $errorInd, $msgArr[] ) = array(1 , "USER NOT FOUND OR NOT ALLOWED ACCESS"));
         } else {
           $urecord = $usrRS->fetch(PDO::FETCH_ASSOC);  
           $msgArr[] = $vaultpw['user'] . " :: " .  $vaultpw['pword'] . " :: " . $urecord['userid'] . " :: " . $urecord['emailaddress'];
           $newpxiid = guidv4();
           $updSQL = "update four.sys_userbase set pxiguidident = :pxiident, pxisessionexpire = date_add(now(), interval 30 minute) where emailaddress = :usremail and userid = :usrid";
           $updRS = $conn->prepare($updSQL);
           $updRS->execute(array(':usremail' => $urecord['emailaddress'], ':usrid' => $urecord['userid'], ':pxiident' => $newpxiid));
           
           $ip = clientipserver();
           $trckSQL = "insert into four.sys_lastLogins(userid, usremail, logdatetime, fromip) values(:userid, :usremail, now(), :ip)";
           $trckR = $conn->prepare($trckSQL);
           $trckR->execute(array(':userid' => $urecord['userid'], ':usremail' => $urecord['emailaddress'], ':ip' => 'VAULT-LOGIN: ' . $ip));
           
           captureSystemActivity($newpxiid, '', 'true', $urecord['emailaddress'], '', '', $urecord['emailaddress'], 'POST', 'VAULT LOGIN SUCCESS');
           
           $dta = $newpxiid;
           $responseCode = 200;
         }
        }
       
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
    
    function vaultuserlogincheck ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id();
      
      $ssid = explode("::", cryptservice($pdta['idstr'], 'd', true, $sessid));
      $chkUsrSQL = "SELECT userid, emailaddress, originalaccountname, presentinstitution, altphonecellcarrier "
              . "FROM four.sys_userbase "
              . "where sessionid = :sessionid and allowInd = 1 and allowlinux = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0";
      $chkRS = $conn->prepare($chkUsrSQL); 
      $chkRS->execute(array(':sessionid' => $ssid[0]));
      if ( $chkRS->rowCount() <> 1 ) { 
        $msgArr[] = "USER NOT ALLOWED - SCIENCESERVER DOES NOT KNOW WHO YOU ARE.";
      } else {
        $u = $chkRS->fetch(PDO::FETCH_ASSOC);
        $rndStr = strtoupper(generateRandomString(8));                                
        $dta = $u;
        
        //SEND PASSWORD TEXT
        $sndSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgbody, htmlind, wheninput, bywho) value (:phone,'SSv7 Donor-Vault Password',:dvmsg,0,now(),:usrinput)";
        $sndMsg = 'Here is the single use password to the CHTNEastern\'s Donor Vault: ' . $rndStr;
        $usrinput = 'DONOR-VAULT-REQUEST (' . $u['emailaddress'] . ")";
        $sndRS = $conn->prepare($sndSQL); 
        $sndRS->execute(array(':phone' => "[\"{$u['altphonecellcarrier']}\"]",':dvmsg' => $sndMsg, ':usrinput' => $usrinput));
        
        $captureUsrPWSQL = "update four.sys_userbase set pxipassword = :encypw, pxipasswordexpire = date_add(now(), interval 15 minute) where sessionid = :ssid and allowInd = 1 and allowlinux = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0";
        $captureUsrPWRS = $conn->prepare($captureUsrPWSQL); 
        $captureUsrPWRS->execute(array('encypw' => $rndStr, ':ssid' => $ssid[0] ));
        
        $dta = cryptservice($sessid,'e');
        
        $responseCode = 200;
      }
      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                    
    }
    
    function vaultgetuseremail ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
        
      $ss = cryptservice( $pdta['usrency'], 'd');
      $chkSQL = "SELECT emailaddress FROM four.sys_userbase where allowInd = 1 and sessionid = :session";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':session' => $ss ));
      if ( $chkRS->rowCount() < 1 ) { 
        $dta = "NO EMAIL LISTED FOR SESSION";
      } else { 
        $chk = $chkRS->fetch(PDO::FETCH_ASSOC);
        $dta = $chk['emailaddress'];
      }
      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
    
    function vaultretrievependingprs ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;        
      
      $prSQL = "SELECT pbiosample, replace(read_label ,'_','') as readlabel, ifnull(prc.dspvalue,'') as proctype, ifnull(qms.dspvalue,'') as qmsprocstatus, ifnull(procureinstitution,'') as procureinstitution, ifnull(date_format(createdon,'%Y-%m-%d'),'') procurementdate, ifnull(date_format(proceduredate,'%m/%d/%Y'),'') as proceduredate, ucase(trim(concat(trim(concat(pxiage,' ', ifnull(agu.dspvalue,''))),' / ', pxirace,' / ', ifnull(psx.dspvalue,'')))) ars, ucase(trim(concat(ifnull(tissType,''), ' / ', concat(ifnull(anatomicSite,''), if(ifnull(subsite,'') = '','',concat(' [', ifnull(subsite,''),']'))), ' / ' , concat(ifnull(diagnosis,''), if(ifnull(subdiagnos,'') ='','',concat(' [',ifnull(subdiagnos,''),']')))))) as dx, ifnull(pxiid,'') as pxiid, ifnull(assocID,'') as associd, date_format(createdon,'%Y-%m')  procurementdatedsp FROM masterrecord.ut_procure_biosample bs left join (SELECT menu, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus') qms on bs.QCProcStatus = qms.menuvalue left join (SELECT menu, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') prc on bs.proctype = prc.menuvalue left join (SELECT menu, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'AGEUOM') agu on bs.pxiageuom = agu.menuvalue left join (SELECT menu, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PXSEX') psx on bs.pxiGender = psx.menuvalue where pathReport = 2 and ( proceduredate > '2018-01-01' and proceduredate < now()) and voidind <> 1 order by 12 desc";
      $prRS = $conn->prepare($prSQL); 
      $prRS->execute();
      $itemsfound = $prRS->rowCount(); 
      while ($r = $prRS->fetch(PDO::FETCH_ASSOC) ) { 
          $dta[] = $r;
      }
 
      $responseCode = 200;
          
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('RESPONSECODE' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
    
    //////////////// ^^^ VAULT ABOVE /////////////////
    
    
    
    
    
    
    
    
    
    
    function invtrysegmentstatuser ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      //{"location":"FRZB383","scanlist":["88322T001","88321T003"]}
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       

      ( !array_key_exists('location', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'location' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['location']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU DID NOT SPECIFY WHERE YOU ARE PLACING THESE BIOSAMPLES")) : "";        

      //CHECK VALID INVENTORY LOCATION
      $locSQL = "SELECT btm.scancode, trim(concat(ifnull(nxtblvl.locationnote,''), if(ifnull(nxtalvl.locationnote,'')='','',concat(' :: ',ifnull(nxtalvl.locationnote,''))), if(ifnull(nxtlvl.locationnote,''),'',concat(' :: ', ifnull(nxtlvl.locationnote,''))), if(ifnull(btm.locationnote,'')='','', concat(' :: ',ifnull(btm.locationnote,''))), if(ifnull(btm.typeolocation,'')='','',concat(' [', ifnull(btm.typeolocation,''),']')))) as locationdsc FROM four.sys_inventoryLocations btm left join (SELECT locationid, locationnote, parentid FROM four.sys_inventoryLocations) as nxtlvl on btm.parentid = nxtlvl.locationid left join (SELECT locationid, locationnote, parentid FROM four.sys_inventoryLocations) as nxtalvl on nxtlvl.parentid = nxtalvl.locationid left join (SELECT locationid, locationnote, parentid FROM four.sys_inventoryLocations) as nxtblvl on nxtalvl.parentid = nxtblvl.locationid where scancode = :scanlabel and hierarchyBottomInd = 1 and hprtrayind = 0 and activelocation = 1";
      $locRS = $conn->prepare($locSQL); 
      $locRS->execute(array(':scanlabel' => $pdta['location']));

      if ( $locRS->rowCount() < 1 ) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SCANNED LOCATION WAS NOT FOUND.  OPERATION CEASED!"));
      } else { 
        $l = $locRS->fetch(PDO::FETCH_ASSOC); 
        (list( $errorInd, $msgArr[] ) = array(1 , $l['scancode'] . " -- " . $l['locationdsc']  ));
      }
      //CHECK NO SEGMENT IS SHIPPED
      $scnlst = $pdta['scanlist'];
      
      //START HERE 2019-10-28
      
      //USE THIS STATEMENT INSTEAD 
      //SELECT replace(bgs,'_','') as bgs, segmentid, segstatus, shippedDate FROM masterrecord.ut_procure_segment where replace(bgs,'_','') IN ('54098T001','44945A1PBDX1','88823T001') AND ((segstatus = 'SHIPPED' OR segstatus = 'DESTROYED') OR shippeddate is not null);      
      ////NOT THIS ONE 
      //$segChkSQL = "SELECT replace(bgs,'_','') as bgs, segmentid, segstatus, shippedDate FROM masterrecord.ut_procure_segment where replace(bgs,'_','') = :bgs";
      //$segRS = $conn->prepare($segChkSQL);
      //foreach ( $scnlst as $k => $v ) {
      //  $segRS->execute(array(':bgs' => $v));
      //  if ( $segRS->rowCount() < 1 ) { 
      //      (list( $errorInd, $msgArr[] ) = array(1 , "LABEL {$v} NOT FOUND.  OPERATION CEASED!"));
      //  } else { 
      //      $tst = $segRS->fetch(PDO::FETCH_ASSOC);
      //      if ( strtoupper($tst['segstatus'] ) === 'SHIPPED' || strtoupper($tst['segstatus'] ) === 'DESTROYED' ) { 
      //        (list( $errorInd, $msgArr[] ) = array(1 , "LABEL {$v} IS MARKED AS " . strtoupper($tst['segstatus'])  . ".  INVENTORY OPERATIONS CANNOT BE PERFORMED ON SEGMENTS IN THIS STATUS.  OPERATION CEASED!"));
      //      } else { 
      //        //STATUS IS GOOD  
      //      }
      //  }

      //}
      //WHAT IS THE STATUS? 
      
      
      
      
      if ( $errorInd === 0 ) { 
        

        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }

    function invtryhprtrayscanpreprocess ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       

      ( !array_key_exists('scancode', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'labeltext' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['scancode']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Biosample Label' cannot be blank")) : "";        

      if ( $errorInd === 0 ) { 
        



        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }

    function rqstinventorylabelencrypt ( $request, $passdata ) { 
      //{"labeltext":"32701A2A","printer":"INVCARD","printformat":"PRINTCARD"}
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      
      ( !array_key_exists('labeltext', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'labeltext' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['labeltext']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Biosample Label' cannot be blank")) : "";        

      
      if ( $errorInd === 0 ) { 
        $dta['dataencryption'] = cryptservice( $pdta['labeltext'],'e');
        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }

    function printthisinventorylabel ( $request, $passdata ) { 
        //{"labeltext":"55843T001","printer":"Slide Label","printformat":"newslideFormat"}
        //{"labeltext":"55843T001","printer":"BBP81Coord","printformat":"newslideFormat"}
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id(); 
      
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      
      ( !array_key_exists('labeltext', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'labeltext' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['labeltext']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Biosample Label' cannot be blank")) : "";        
      ( !array_key_exists('printer', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'printer' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['printer']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A 'Labelling System' must be specified")) : "";        
      ( !array_key_exists('printformat', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'printformat' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['printformat']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A 'labelling system' must be specified")) : "";        

      if ($errorInd === 0 ) {
          $prntVal['FIELD01'] = $pdta['labeltext'];
          $prntVal['FIELD02'] = $pdta['labeltext'];
          $prntVal['FIELD03'] = $pdta['labeltext'];
          $prntVal['FIELD04'] = $pdta['labeltext'];
          $msgArr[] = $pdta['printer'] . " ... " . $pdta['printformat'] . " ... " . json_encode($prntVal);

          $insSQL = "insert into serverControls.lblToPrint( labelrequested, printerrequested, datastringpayload, bywho, onwhen) values(:formatname,:printername,:datastring,:usr,now())";
          $insRS = $conn->prepare($insSQL); 
          $insRS->execute(array(':formatname' => $pdta['printformat'], ':printername' => $pdta['printer'], ':datastring' => json_encode($prntVal), ':usr' => $u['usr']));

//          $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }

    function furtheractionemailsendticket ( $request, $passdata ) {
      //{"ticket":"a2JSdkdxZENhb2ZsTkRCMG1PbnFhZz09","recip":"proczack","emailtext":"This is a text element","dialogid":"wsTk2zPbLanzwuo"}
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $at = genAppFiles;
      session_start(); 
      $sessid = session_id(); 
      
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       

      ( !array_key_exists('ticket', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'ticket' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('recip', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'recip' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('emailtext', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'emailtext' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'dialogid' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['ticket']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  TICKET MUST CONTAIN A VALUE.")) : ""; 
      ( trim($pdta['recip']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  'Team Member to Email' MUST CONTAIN A VALUE.")) : ""; 

      if ($errorInd === 0 ) {
  
        $docid = cryptservice($pdta['ticket'],'d'); 
        $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .5in;  \" ");
        //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = "FAT{$docid}";
        $fileName = 'fat' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .5in;\"   ");
        //********************END BARCODE CREATION
        $ticketSQL = <<<TICKETSQL
SELECT substr(concat('000000',idlabactions),-6) as ticketnbr, actionstartedind, ifnull(date_format(startedondate,'%m/%d/%Y'),'') as startedondate, ifnull(startedby,'') as startedby, ifnull(frommodule,'Unknown Module') frommodule, ifnull(objshipdoc,'') as objshipdoc, ifnull(objhprid,'') as objhprid, ifnull(objpbiosample,'') as objpbiosample, ifnull(objbgs,'') as objbgs, ifnull(assignedagent,'') as assignedagent, ifnull(actioncode,'UNKNOWN') as actioncode, ifnull(actiondesc,'') as actiondesc, ifnull(actionnote,'') as actionnote, ifnull(notifyoncomplete,0) as notifyoncomplete, ifnull(date_format(duedate,'%Y-%m-%d'),'') as duedateval, ifnull(date_format(duedate,'%m/%d/%Y'),'') as duedate, ifnull(actionrequestedby,'UNKNOWN') as actionrequestedby, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as actionrequestedon, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'') as actioncompleteon, ifnull(actioncompletedby,'') as actioncompletedby, faaction.assignablestatus as actiongridtype, prioritymarkcode, faprio.dspvalue as prioritydsp, lastagent, ifnull(agentlist.dspagent,'') as lastagentdsp FROM masterrecord.ut_master_furtherlabactions fa LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAACTIONLIST') faaction on fa.actioncode = faaction.menuvalue LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') faprio on fa.prioritymarkcode = faprio.menuvalue left join (SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP') as agentlist on fa.lastagent = agentlist.originalaccountname where idlabactions = :ticket and activeind = 1
TICKETSQL;
        $ticketRS = $conn->prepare($ticketSQL);
        $ticketRS->execute(array(':ticket' => (int)$docid));
        if ( $ticketRS->rowCount() === 1 ) {
          $ticket = $ticketRS->fetch(PDO::FETCH_ASSOC);
          $duedate = ( trim($ticket['duedate']) === '01/01/1900' ) ? "" : trim($ticket['duedate']);
          $notify = ( (int)$ticket['notifyoncomplete'] === 0 ) ? "No" : "Yes";
    $faListSQL = "SELECT actionlist.menuvalue detailactioncode, actionlist.dspvalue detailaction, ifnull(actionlist.additionalInformation,0) as completeactionind, doneaction.whoby, ifnull(date_format(doneaction.whenon,'%m/%d/%Y'),'') as whenon, doneaction.comments, ifnull(doneaction.finishedstepind,0) finishedstep, ifnull(doneaction.finishedby,'') as finishedby, ifnull(date_format(doneaction.finishedon,'%m/%d/%Y %H:%i'),'') as finisheddate  FROM four.sys_master_menus actionlist left join (SELECT fadetailactioncode, whoby, whenon, comments, finishedstepind, finishedby, finishedon FROM masterrecord.ut_master_faction_detail where faticket = :ticketnbr ) doneaction on actionlist.menuvalue = doneaction.fadetailactioncode  where actionlist.parentid = :actioncodeid and actionlist.menu = 'FADETAILACTION' and actionlist.dspind = 1 order by actionlist.dsporder";
    $faListRS = $conn->prepare($faListSQL);
    $faListRS->execute(array(':ticketnbr' => (int)$docid, ':actioncodeid' => $ticket['actiongridtype']));
    $action = "<table border=0 cellspacing=0 cellpadding=0 width=100% id=actTbl><thead><tr><td class='col6 actTblHead'><center>#</td><td class='col1 actTblHead'>Action</td><td class='col3 actTblHead'>Performed By :: When</td><td class='col4 actTblHead'>Comments</td></tr></thead><tbody>";
    $faActionDsp = "";
    $faActionStepCount = 0;
    while ( $r = $faListRS->fetch(PDO::FETCH_ASSOC)) { 
      $onwhen = ( trim($r['whenon']) !== "" ) ? " :: {$r['whenon']}" : "";
      $finishedind = (int)$r['finishedstep'];
      $fad = "";
      $stepCountDsp = "&nbsp;";
      $comcheckdsp = "";
      if ( $faActionDsp !== $r['detailaction'] ) { 
          $fad = $r['detailaction'];
          $faActionDsp = $r['detailaction'];  
          $comcheckdsp = ( $finishedind === 1) ? "<i class=\"material-icons\">check_circle_outline</i>" : "";
          $faActionStepCount++;
          $stepCountDsp = "{$faActionStepCount}.";          
      } else { 
          $fad = "&nbsp;";
          $actionPop = "";
      } 
      $action .= "<tr><td>{$stepCountDsp}</td><td>{$fad}</td><td>{$r['whoby']} {$onwhen}</td><td>{$r['comments']}</td></tr>";
    }
    $action .= "</tbody></table>";
           $docText = <<<PRTEXT
             <html><head><style>
                   @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons);
                   html {margin: 0;}
                   body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
                   .line {border-bottom: 1px solid rgba(0,0,0,1); height: 2pt; }
                   .label { font-size: 8pt; font-weight: bold; }
                   .datadsp { font-size: 10pt; }  
                   .holdersqr { border: 1px solid rgba(201,201,201,1); padding: 2px; }
                   .actTblHead {font-size: 8pt; font-weight: bold; background: rgba(0,0,0,1); color: rgba(255,255,255,1); padding: 5px 2px; }
                   #actTbl tbody tr td { border-bottom: 1px solid rgba(201,201,201,1); border-right: 1px solid rgba(201,201,201,1); font-size: 9pt; padding: 5px 2px; } 
                </style>
                </head>
                <body>
                <table border=0 width=100% style="border: 1px solid #000;">
                <tr><td valign=top rowspan=2>{$favi}</td><td style="font-size: 16pt; font-weight: bold; padding: 15px 0 0 0; text-align:center; ">ScienceServer Further Action Request Ticket</td><td align=right valign=top rowspan=2>{$qrcode}</td></tr>
                <tr><td style="font-size: 12pt; font-weight: bold; padding: 0 0 0 0; text-align:center; ">Ticket: {$ticket['ticketnbr']}</td></tr>
                  <tr><td colspan=3>
                    <table width=100%>
                      <tr>
                        <td valign=top class="holdersqr" width=20%><div class=label>Requested On</div><div class=datadsp>{$ticket['actionrequestedon']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=20%><div class=label>Requested By</div><div class=datadsp>{$ticket['actionrequestedby']} ({$ticket['frommodule']})&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=20%><div class=label>Notify When Complete</div><div class=datadsp>{$notify}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=20%><div class=label>Priority</div><div class=datadsp>{$ticket['prioritydsp']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=20%><div class=label>Due Date</div><div class=datadsp>{$duedate}&nbsp;</div></td>
                      </tr>
                    </table>
                   </td></tr>
                  <tr><td colspan=3>
                    <table width=100%>
                      <tr>
                        <td valign=top class="holdersqr"><div class=label>Request Type</div><div class=datadsp>{$ticket['actiondesc']}&nbsp;</div></td>
                      </tr>
                    </table>
                   </td></tr>
                  <tr><td colspan=3>
                    <table width=100%>
                      <tr>
                        <td valign=top class="holdersqr"><div class=label>Request Notes</div><div class=datadsp>{$ticket['actionnote']}&nbsp;</div></td>
                      </tr>
                    </table>
                   </td></tr>
                   <tr><td colspan=3>
                    <table width=100%>
                      <tr>
                        <td valign=top class="holdersqr" width=15%><div class=label>Assigned Agent</div><div class=datadsp>{$ticket['assignedagent']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=15%><div class=label>Last Agent </div><div class=datadsp>{$ticket['lastagentdsp']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=15%><div class=label>Started By</div><div class=datadsp>{$ticket['startedby']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=15%><div class=label>Started On</div><div class=datadsp>{$ticket['startedondate']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=15%><div class=label>Completed On</div><div class=datadsp>{$ticket['actioncompleteon']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=15%><div class=label>Completed By</div><div class=datadsp>{$ticket['actioncompletedby']}&nbsp;</div></td>
                      </tr>
                    </table>
                   </td></tr>
                  <tr><td colspan=3>
                    <table width=100%>
                      <tr>
                        <td valign=top class="holdersqr" width=25%><div class=label>Ship-Doc Reference</div><div class=datadsp>{$ticket['objshipdoc']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=25%><div class=label>HPR Reference</div><div class=datadsp>{$ticket['objhprid']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=25%><div class=label>Biogroup Reference</div><div class=datadsp>{$ticket['objpbiosample']}&nbsp;</div></td>
                        <td valign=top class="holdersqr" width=25%><div class=label>Segment Reference</div><div class=datadsp>{$ticket['objbgs']}&nbsp;</div></td>
                      </tr>
                    </table>
                   </td></tr>
                  <tr><td colspan=3>
                    {$action} 
                  </td></tr>
                </table>
                </body>
                </html>
PRTEXT;

          $filehandle = generateRandomString();                
          $prDocFile = genAppFiles . "/tmp/FAT{$filehandle}.html";
          $prDhandle = fopen($prDocFile, 'w');
          fwrite($prDhandle, $docText);
          fclose;
          $prPDF = genAppFiles . "/publicobj/documents/fatickets/fat{$filehandle}.pdf";
          $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED  {$crnbr}\"     {$prDocFile} {$prPDF}";
          $output = shell_exec($linuxCmd);



          //{"ticket":"a2JSdkdxZENhb2ZsTkRCMG1PbnFhZz09","recip":"proczack","emailtext":"This is a text element","dialogid":"wsTk2zPbLanzwuo"}

          $recipEmlSQL = "SELECT emailaddress FROM four.sys_userbase where originalaccountname = :acct";
          $recipRS = $conn->prepare($recipEmlSQL);
          $recipRS->execute(array(':acct' => $pdta['recip']));
          if ( $recipRS->rowCount() === 1 ) { 
              $eml = $recipRS->fetch(PDO::FETCH_ASSOC);
              $emaillist[] = $eml['emailaddress'];
          }
          $eText = preg_replace( '/\\n/','<br>', preg_replace( '/\\n\\n/','<p>',$pdta['emailtext'] ));
          $emaillist[] = "zackvm@zacheryv.com";
          $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgBody, htmlind, wheninput, bywho, srverattachment, attachmentname) value(:towhoaddressarray, 'CHTN-EASTERN QMS FOLLOWUP EMAIL', :msgBody, 1, now(), 'QA-QMS EMAILER', :afile, 'FURTHER ACTION TICKET')";
          $emlRS = $conn->prepare( $emlSQL );
          $emlRS->execute(array ( ':towhoaddressarray' => json_encode( $emaillist ), ':msgBody' => "<table border=1><tr><td><CENTER>THE MESSAGE BELOW IS FROM THE FURTHER ACTION TICKETING SYSTEM AT CHTNEASTERN ({$u['friendlyname']}/{$u['emailaddress']}) .  PLEASE DO NOT RESPONSED TO THIS EMAIL.  CONTACT THE CHTNEASTERN OFFICE DIRECTLY EITHER BY EMAIL chtnmail@uphs.upenn.edu OR BY CALLING (215) 662-4570.</td></tr><tr><td>{$eText}</td></tr></table>",':afile' => genAppFiles . "/publicobj/documents/fatickets/fat{$filehandle}.pdf" ));

          $dta['dialogid'] = $pdta['dialogid'];
          $responseCode = 200;
        } else {  
            $docText = "TICKET {$docid} NOT FOUND";
            (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  TICKET DOES NOT EXIST."));
        }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }

    function invtrylocationheirach ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
      
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      
      ( !array_key_exists('scanlabel', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'scanlabel' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['scanlabel']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  SCANLABEL MUST CONTAIN A VALUE.")) : ""; 

      if ($errorInd === 0 ) {
//          $sql = "SELECT btmlvl.scancode, trim(concat(if(ifnull(lvl4.locationdsp,'')='','',concat(ifnull(lvl4.locationdsp,''),' :: ')), if(ifnull(lvl3.locationdsp,'')='','',concat(ifnull(lvl3.locationdsp,''),' :: ')), if(ifnull(lvl2.locationdsp,'')='','',concat(ifnull(lvl2.locationdsp,''),' :: ')), if(ifnull(lvl1.locationdsp,'')='','',concat(ifnull(lvl1.locationdsp,''))))) as pathdsp, concat(ifnull(btmlvl.locationdsp,''), if(ifnull(btmlvl.typeolocation,'')='','',concat(' [',ifnull(btmlvl.typeolocation,''),']'))) thislocation FROM four.sys_inventoryLocations btmlvl left join (SELECT locationid, typeolocation, locationdsp, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl1 on btmlvl.parentid = lvl1.locationid left join (SELECT locationid, typeolocation, locationdsp, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl2 on lvl1.parentid = lvl2.locationid left join (SELECT locationid, typeolocation, locationdsp, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl3 on lvl2.parentid = lvl3.locationid left join (SELECT locationid, typeolocation, locationdsp, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl4 on lvl3.parentid = lvl4.locationid where ifnull(hierarchyBottomInd,0) = 1 and ifnull(hprtrayind,0) = 0 and ifnull(activelocation,0) = 1 and ifnull(physicalLocationInd,0) = 1 and scancode = :scancode"; 
          $sql = "SELECT btmlvl.scancode, trim(concat(if(ifnull(lvl3.locationnote,'')='','',concat(ifnull(lvl3.locationnote,''),' :: ')), if(ifnull(lvl2.locationnote,'')='','',concat(ifnull(lvl2.locationnote,''),' :: ')), if(ifnull(lvl1.locationnote,'')='','',concat(ifnull(lvl1.locationnote,''),' :: ')), if(ifnull(btmlvl.locationnote,'')='','',concat(ifnull(btmlvl.locationnote,''))))) as pathdsp, concat(ifnull(btmlvl.locationdsp,''), if(ifnull(btmlvl.typeolocation,'')='','',concat(' [',ifnull(btmlvl.typeolocation,''),']'))) thislocation FROM four.sys_inventoryLocations btmlvl left join (SELECT locationid, typeolocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl1 on btmlvl.parentid = lvl1.locationid left join (SELECT locationid, typeolocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl2 on lvl1.parentid = lvl2.locationid left join (SELECT locationid, typeolocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl3 on lvl2.parentid = lvl3.locationid left join (SELECT locationid, typeolocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1) as lvl4 on lvl3.parentid = lvl4.locationid where ifnull(hierarchyBottomInd,0) = 1 and ifnull(hprtrayind,0) = 0 and ifnull(activelocation,0) = 1 and ifnull(physicalLocationInd,0) = 1 and scancode = :scancode";
          $rs = $conn->prepare($sql);
          $rs->execute(array(':scancode' => $pdta['scanlabel'] ));
          if ( $rs->rowCount() === 1 ) { 
              $dta = $rs->fetch(PDO::FETCH_ASSOC);
              $responseCode = 200;
          } else { 
              $dta = $passdata;
          }
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;     
    }
    
    function invtrylabeldxdesignation ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
      
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowInvtry = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      
      //{"scanlabel":"88346T001"}
      ( !array_key_exists('scanlabel', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'scanlabel' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['scanlabel']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  SCANLABEL MUST CONTAIN A VALUE.")) : ""; 




      if ($errorInd === 0 ) {

          if ( preg_match('/^ED/i',$pdta['scanlabel']) ) { 
            $pdta['scanlabel'] = preg_replace('/^ED/i','',$pdta['scanlabel']);
          }


          $sql = "SELECT mn.dspvalue as segstatus, ucase(trim(concat(ifnull(sg.prepmethod,''), if(ifnull(sg.preparation,'')='','',concat(' [',ifnull(sg.preparation,''),']'))))) as prp, ucase(trim(concat(ifnull(bs.tisstype,''),' :: ', concat(concat(ifnull(bs.anatomicsite,''), if(ifnull(bs.subsite,'')='','', concat( ' [',ifnull(bs.subsite,''),']' ))) ,' :: ', concat(if(ifnull(bs.diagnosis,'')='','',  ifnull(bs.diagnosis,'')), if(ifnull(bs.subdiagnos,'')='','',concat(' [', ifnull(bs.subdiagnos,''),']')))))))  desig FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') as mn on sg.segstatus = mn.menuvalue where replace(sg.bgs,'_','') = :bgs"; 
          $rs = $conn->prepare($sql);
          $rs->execute(array(':bgs' => $pdta['scanlabel'] ));
          if ( $rs->rowCount() === 1 ) { 
              $dta = $rs->fetch(PDO::FETCH_ASSOC);
              $responseCode = 200;
          } else { 
              $dta = $pdta['scanlabel'];
          }
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;      
    }

    function furtheractioneditticket ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowCoord = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      //{"ticket":"\\\"bEZjT3NITUhaSDVkQnAzVEY0Y0Ridz09\\\"","duedate":"2019-10-11","BGRef":"83404 / 83404T001","SDRef":"0000","HPRRef":"18360","agent":"Gina","ticketnote":"This is a note that goes here"}
      ( !array_key_exists('ticket', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'ticket' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('duedate', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'duedate' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('agent', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'agent' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('ticketnote', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'ticketnote' DOES NOT EXIST.")) : ""; 
      ( trim($pdta['ticket']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO TICKET SPECIFIED.")) : "";
      ( trim($pdta['duedate']) !== "" && !ssValidateDate( $pdta['duedate'], 'Y-m-d') ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE SPECIFIED 'DUE DATE' IS INVALID.")) : "";

      //CHECK TICKET EXISTS
      $ticket = cryptservice( $pdta['ticket'], 'd');
      $chkTicketSQL = "SELECT idlabactions, ifnull(actioncompletedon,'') as actioncompletedon, ifnull(assignedagent,'') assignedagent  FROM masterrecord.ut_master_furtherlabactions where activeind = 1 and idlabactions = :ticket";
      $chkRS = $conn->prepare($chkTicketSQL);
      $chkRS->execute(array(':ticket' => (int)$ticket  ));
      if ( $chkRS->rowCount() <> 1 ) {
        (list( $errorInd, $msgArr[] ) = array(1 , "{$ticket} DOES NOT EXIST IN THE DATABASE.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER"));
      } else { 
        //CHECK TICKET NOT CLOSED  
        $ticketChk = $chkRS->fetch(PDO::FETCH_ASSOC);
        if ( trim($ticketChk['actioncompletedon']) !== "" ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "TICKET {$ticket} HAS ALREADY BEEN COMPLETED AND MAY NOT BE EDITED."));
        }
      }

      //TODO: CHECK PASSED VALUES
      if ( trim($pdta['agent']) !== "" ) { 
        $chkAgentSQL = "SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP' and originalAccountName = :agent order by friendlyname";
        $chkAgentRS = $conn->prepare($chkAgentSQL); 
        $chkAgentRS->execute(array(':agent' => $pdta['agent'] ));
        if ( $chkAgentRS->rowCount() < 1 ) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "THE SPECIFIED AGENT ({$pdta['agent']}) DOES NOT EXIST "));
        }
      }
      $changeAgent = 0;
      $changeAgent = ( strtolower(trim($pdta['agent'])) !== trim(strtolower($ticketChk['assignedagent'])) ) ? 1 : 0;


      if ($errorInd === 0 ) {
        //MAKE BACK UP OF TICKET
          $buSQL = "insert into masterrecord.history_master_furtherlabactions (inputon, inputby, idlabactions, activeind, actionstartedind, startedondate, startedby, frommodule, objhprid, objshipdoc, objpbiosample, bgreadlabel, objbgs, assignedagent, lastagent, actioncode, actiondesc, actionnote, notifyOnComplete, duedate, prioritymarkcode, actionrequestedby, actionrequestedon, actioncompletedon, actioncompletedby, lasteditedon, lasteditby) SELECT now(), 'FA-EDIT', idlabactions, activeind, actionstartedind, startedondate, startedby, frommodule, objhprid, objshipdoc, objpbiosample, bgreadlabel, objbgs, assignedagent, lastagent, actioncode, actiondesc, actionnote, notifyOnComplete, duedate, prioritymarkcode, actionrequestedby, actionrequestedon, actioncompletedon, actioncompletedby, lasteditedon, lasteditby FROM masterrecord.ut_master_furtherlabactions where idlabactions = :ticketnbr";
          $buRS = $conn->prepare($buSQL); 
          $buRS->execute(array(':ticketnbr' => $ticket));

          $updSQL = "update masterrecord.ut_master_furtherlabactions set lasteditedon = now(), lasteditby = :updater";
          $updArr[':updater'] = strtoupper( $u['usr'] );
          if ( $changeAgent === 1 ) {
            $updSQL .= " , lastagent = assignedagent , assignedagent = :newassignedagent ";
            $updArr[':newassignedagent'] = $pdta['agent'];
          }
          if ( trim($pdta['duedate']) !== "" ) {
            $updSQL .= ",duedate = :duedate ";
            $updArr[':duedate'] = $pdta['duedate']; 
          } 
          $updSQL .= ",actionnote = :actionnote ";
          $updArr[':actionnote'] = trim( $pdta['ticketnote'] ); 
          $updSQL .= "where idlabactions = :ticket";
          $updArr[':ticket'] = $ticket;
          $updRS = $conn->prepare($updSQL);
          $updRS->execute( $updArr );
          $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                 
    }

    function furtheractionactionnote ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowCoord = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid ));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS FURTHER ACTION LOG FILES.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
      //{"ticket":"116","dateperformed":"09/02/2019","action":"SENT","dialog":"ZsZho9ioPFQdsKj","notes":"These are notes ","complete":"0"}

      //(list( $errorInd, $msgArr[] ) = array(1 , "{$u['usr']}"));

      ( !array_key_exists('ticket', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'ticket' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('dateperformed', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'dateperformed' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('action', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'action' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('dialog', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'dialog' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('notes', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'notes' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('complete', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'complete' DOES NOT EXIST.")) : "";  
      ( !array_key_exists('taskcomplete', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'taskcomplete' DOES NOT EXIST.")) : "";   
      ( trim($pdta['ticket']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO TICKET SPECIFIED.")) : "";
      ( trim($pdta['action']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO ACTION CODE WAS SPECIFIED.")) : "";
      ( trim($pdta['dialog']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO DIALOG CODE WAS SPECIFIED.")) : "";
      ( trim($pdta['dateperformed']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO ACTION PERFORMANCE DATE SPECIFIED.")) : "";
      ( !is_numeric( $pdta['complete'] )) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR: COMPLETE MUST BE NUMERIC AND EQUAL TO EITHER 0 OR 1")) : "";
      ( !is_numeric( $pdta['taskcomplete'] )) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR: TASK COMPLETE MUST BE NUMERIC AND EQUAL TO EITHER 0 OR 1")) : "";

      //CHECK ACTION CODE
      if ( trim($pdta['action']) !== "" ) {  
        $chkSQL = "SELECT ifnull(additionalinformation,0) as completeind, assignablestatus as requirednote  FROM four.sys_master_menus where menu = 'FADETAILACTION' and menuvalue = :actioncode";
        $chkRS = $conn->prepare($chkSQL);
        $chkRS->execute(array(':actioncode' => $pdta['action'] ));
        $complete = 0;
        if ( $chkRS->rowCount() <> 1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "ERROR: ACTION CODE VALUE NOT FOUND."));
        } else { 
          $a = $chkRS->fetch(PDO::FETCH_ASSOC); 
          ( (int)$a['requirednote'] === 1 && trim( $pdta['notes'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THIS ACTION REQUIRES A NOTE ALSO BE MADE.")) : ""; 
          $complete = (int)$a['completeind'];
        }
      } else { 
         (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO ACTION CODE WAS SPECIFIED."));
      }
  
      //CHECK DATE VALUE
      ( !ssValidateDate( $pdta['dateperformed'], 'm/d/Y') ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE ACTION DATE IS INVALID.")) : "";


      if ($errorInd === 0 ) {
        $chkForUpdate = "SELECT idlabactions FROM masterrecord.ut_master_furtherlabactions where idlabactions = :ticket and actionstartedind = 0";
        $chkForRS = $conn->prepare($chkForUpdate);
        $chkForRS->execute(array(':ticket' => $pdta['ticket'] )); 
        if ( $chkForRS->rowCount() === 1 ) { 
            //UPDATE TICKET
            $updSQL = "update masterrecord.ut_master_furtherlabactions set actionstartedind = 1, startedondate = now(), startedby = :usr where idlabactions = :ticket";
            $updRS = $conn->prepare($updSQL); 
            $updRS->execute(array(':usr' => $u['usr'], ':ticket' => (int)$pdta['ticket'] ));
        }
        
        $pdte = strtotime($pdta['dateperformed']);
        $newformat = date('Y-m-d',$pdte);
        
        $detSQL = "insert into masterrecord.ut_master_faction_detail ( faticket, fadetailactioncode, whoby, whenon, comments) values ( :ticket, :actioncode, :usr, :dateperformed, :notes)";
        $detRS = $conn->prepare($detSQL);
        $detRS->execute(array(':ticket' => (int)$pdta['ticket'], ':actioncode' => $pdta['action'], ':usr' => $u['usr'], ':dateperformed' => $newformat, ':notes' => $pdta['notes']));        
        if ( (int)$pdta['taskcomplete'] === 1 ) { 
             //MAKE TASK AS COMPLETE
            $updSQL = "update masterrecord.ut_master_faction_detail set finishedstepind = 1, finishedby = :comptech, finishedon = now() where faticket =:ticket and faDetailActionCode = :actioncode ";
            $updRS = $conn->prepare($updSQL);
            $updRS->execute(array(':comptech' => $u['usr'], ':ticket' => (int)$pdta['ticket'], ':actioncode' => $pdta['action']));
        }

        if ( $complete === 1 ) {  
          $comChkSQL = "SELECT * FROM masterrecord.ut_master_furtherlabactions where actioncompletedon is not null and idlabactions = :ticket";
          $comChkRS = $conn->prepare($comChkSQL); 
          $comChkRS->execute(array( ':ticket' => (int)$pdta['ticket'] )); 
          if ( $comChkRS->rowCount() === 1 ) { 
          } else {
            $comSQL = "update masterrecord.ut_master_furtherlabactions set actioncompletedon = now() , actioncompletedby = :usr where idlabactions = :ticketnbr";
            $comRS = $conn->prepare($comSQL); 
            $comRS->execute(array(':usr' => $u['usr'], ':ticketnbr' => (int)$pdta['ticket'] ));
          }
        }
        $dta = $pdta['dialog'];
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                 
    }

    function deactivatepbfa ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
            
      $faid = $pdta['faid'];
      $chkSQL = "select objpbiosample, actionstartedind FROM masterrecord.ut_master_furtherlabactions where idlabactions = :faid and actionstartedind = 0"; 
      $chkRS = $conn->prepare($chkSQL); 
      $chkRS->execute( array(':faid' => $faid ) );
      
      if ( $chkRS->rowCount() < 1 ) { 
          $msgArr[] = "THIS FURTHER ACTION DOES NOT EXIST OR IS ALREADY MARKED AS STARTED SO CANNOT BE 'DELETED'";
          $errorInd = 1;
      } else { 
          $pb = $chkRS->fetch(PDO::FETCH_ASSOC);
          $errorInd = 0;
      }
      
      if ( $errorInd === 0 ) {
          $updSQL = "update masterrecord.ut_master_furtherlabactions set activeind = 0 where idlabactions = :faid"; 
          $updRS = $conn->prepare($updSQL);
          $updRS->execute( array(':faid' => $faid ) );

          $dta = $pb['objpbiosample'];           
          $responseCode = 200; 
      }              
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                 
    }
    
    function displaypbfatbl ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
            
      if ( $errorInd === 0 ) {
         $pastFASQL = "SELECT  substr(concat('000000',idlabactions),-6) as faid , if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind, ifnull(actionstartedind,0) as actionstartedind, ifnull(frommodule,'') as requestingModule , if(ifnull(objbgs,'') = '',  substr( ifnull(objpbiosample,''), 1, 5),   ifnull(objbgs,'')) as biosampleref  , ifnull(assignedagent,'') as assignedagent , ifnull(faact.dspvalue,'') as actiondescription, ifnull(actionnote,'') as actionnote , ifnull(fapri.dspvalue,'-') as dspPriority , if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate , ifnull(actionrequestedby,'') as requestedby , ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon FROM masterrecord.ut_master_furtherlabactions fa left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue where objpbiosample = :pbiosample and activeind = 1 order by idlabactions desc";
         $pastFARS = $conn->prepare( $pastFASQL ); 
         $pastFARS->execute(array(':pbiosample' => $pdta['bgref']));
         if ( $pastFARS->rowCount() < 1 ) {
           //TODO: SET PF Default
           $pfaTbl = " - No Further Actions Listed for this Biogroup - ";  
         } else {
           $pfaTbl = "<table border=0 id=faActionDspTbl><thead><tr><td></td><td>Ticket #</td><td>Completed</td><td>Biosample</td><td>Module</td><td>Action</td><td>Assigned Agent</td><td>Priority<br>Due Date</td><td>Requested By</td></tr></thead><tbody>";   
           while ( $f = $pastFARS->fetch(PDO::FETCH_ASSOC)) { 
              if ( $f['actionstartedind'] === 0) { 
                  $rmBtn = "<td onclick=\"deactivateFA('{$f['faid']}');\"><center><i class=\"material-icons rmbtn\">delete_forever</i></td>"; 
              } else { 
                  $rmBtn = "<td>-</td>";                   
              }
              
              
              $pfaTbl .= "<tr>{$rmBtn}<td>{$f['faid']}</td><td>{$f['completedind']}</td><td>{$f['biosampleref']}</td><td>{$f['requestingModule']}</td><td>{$f['actiondescription']}<br>{$f['actionnote']}</td><td>{$f['assignedagent']}</td><td>{$f['dspPriority']}<br>{$f['duedate']}</td><td>{$f['requestedby']}<br>{$f['requestedon']}</td></tr>";
           }
           $pfaTbl .= "</tbody></table>";
         }
         $dta = $pfaTbl;
         $responseCode = 200; 
      }              
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }

    function savefurtheraction ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }



      if ( $errorInd === 0 ) {        

      ( !array_key_exists('rqstPayload', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'rqstPayload' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('bioReference', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'bioReference' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('actionsValue', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'actionsValue' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('actionNote', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'actionNote' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('priority', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'priority' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('notifycomplete', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'notifycomplete' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('agent', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'agent' DOES NOT EXIST.")) : ""; 
      

      if ( $errorInd === 0 ) {
  
        ( trim( $pdta['rqstPayload'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  'rqstPayload' CANNOT BE EMPTY")) : "";
        ( trim( $pdta['bioReference'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Biosample Ref #' IS A REQUIRED FIELD - PLEASE SUPPLY A VALUE AND TRY AGAIN.")) : "";
        ( trim( $pdta['actionsValue'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Action to Take' IS A REQUIRED FIELD - PLEASE SUPPLY A VALUE AND TRY AGAIN.")) : "";
        ( trim( $pdta['priority'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'Priority' IS A REQUIRED FIELD - PLEASE SUPPLY A VALUE AND TRY AGAIN.")) : "";

        if ( $errorInd === 0 ) { 
          $chkSQL = "SELECT dspvalue FROM four.sys_master_menus where menu = :menuval and menuvalue = :chkval";
          $chkRS = $conn->prepare($chkSQL); 
          $chkRS->execute(array(':menuval' => 'FAACTIONLIST', ':chkval' => trim($pdta['actionsValue']) )); 
          if ( $chkRS->rowCount() <> 1 ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "'Action to Take' MENU VALUE NOT FOUND - PLEASE SUPPLY A TRUE VALUE AND TRY AGAIN."));
          } else { 
            $chkAct = $chkRS->fetch(PDO::FETCH_ASSOC);
            $actionDsp = $chkAct['dspvalue'];
          }

          $chkRS->execute(array(':menuval' => 'FAPRIORITYSCALE', ':chkval' => trim($pdta['priority']) ));
          if ( $chkRS->rowCount() <> 1 ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "'Priority' MENU VALUE NOT FOUND - PLEASE SUPPLY A TRUE VALUE AND TRY AGAIN."));
          }
          if ( trim( $pdta['duedate'] ) !== "" ) { 
            if ( ssValidateDate( $pdta['duedate'], 'm/d/Y') <> 1 ) {
              (list( $errorInd, $msgArr[] ) = array(1 , "THE SUPPLIED DUE DATE IS INVALID ({$pdta['duedate']})")); 
            } else { 
              $d = DateTime::createFromFormat('m/d/Y', $pdta['duedate']);
              $dd = $d->format('Y-m-d');
            }
          } else { 
             $dd = "1900-01-01";
          }

          if ( $errorInd === 0 ) { 
            //"{\"rqstPayload\":\"{\\\"requestingmodule\\\":\\\"QMS-QA\\\",\\\"biohpr\\\":\\\"018623\\\",\\\"slidebgs\\\":\\\"83239T003\\\",\\\"bgreadlabel\\\":\\\"83239T\\\",\\\"pbiosample\\\":\\\"83239.00000000\\\"}\",\"bioReference\":\"83239T003\",\"actionsValue\":\"BIOSAMPLEPROC\",\"actionNote\":\"This is a note\",\"agent\":\"proczack\",\"priority\":\"FANORMAL\",\"duedate\":\"08\/30\/2019\"}"  
              $rqstpayload = json_decode( $pdta['rqstPayload'], true );       
              $notify = ( (int)$pdta['notifycomplete'] === 1 ) ? 1 : 0;
              
              $mod = ( trim( $rqstpayload['requestingmodule']) !== "" ) ?  $rqstpayload['requestingmodule'] : "UNKNOWN";
              $biohpr = ( trim( $rqstpayload['biohpr'] ) !== "" ) ? (int)$rqstpayload['biohpr'] : 0; 
              $pbiosamp = ( trim( $rqstpayload['pbiosample'] ) !== "" ) ? $rqstpayload['pbiosample'] : ""; 
              $bgread = ( trim( $rqstpayload['bgreadlabel'] ) !== "" ) ? $rqstpayload['bgreadlabel'] : ""; 
              $bioref = ( trim( $pdta['bioReference'] ) !== "" ) ? $pdta['bioReference'] : ""; 
              $agnt = ( trim( $pdta['agent'] ) !== "" ) ? $pdta['agent'] : ""; 
              $actval = ( trim( $pdta['actionsValue'] ) !== "" ) ? $pdta['actionsValue'] : "UNKNOWN"; 
              $actnote =  ( trim( $pdta['actionNote'] ) !== "" ) ? $pdta['actionNote'] : ""; 
              $prio = ( trim( $pdta['priority'] ) !== "" ) ? $pdta['priority'] : "FANORMAL"; 
              
              $faInsSQL = "insert into masterrecord.ut_master_furtherlabactions ( frommodule, activeind, objhprid, objpbiosample, bgreadlabel, objbgs, assignedagent, actioncode, actiondesc, actionnote, notifyOnComplete, duedate, prioritymarkcode, actionrequestedby, actionrequestedon) values ( :frommodule, :activeind, :objhprid, :objpbiosample, :bgreadlabel, :objbgs, :assignedagent, :actioncode, :actiondesc, :actionnote, :notifyOnComplete, :duedate, :prioritymarkcode, :actionrequestedby, now())"; 
              $faInsRS = $conn->prepare($faInsSQL); 
              $faInsRS->execute(array(
                ':frommodule' => $mod
               ,':activeind' => 1
               ,':objhprid' => $biohpr
               ,':objpbiosample' => $pbiosamp
               ,':bgreadlabel' => $bgread
               ,':objbgs' => $bioref
               ,':assignedagent' => $agnt 
               ,':actioncode' => $actval 
               ,':actiondesc' => $actionDsp
               ,':actionnote' => $actnote
               ,':notifyOnComplete' => $notify
               ,':duedate' => $dd
               ,':prioritymarkcode' => $prio
               ,':actionrequestedby' => $u['usr']
              ));

              $dta['pbiosample'] = $rqstpayload['pbiosample'];
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

    function qmssendemailletter ( $request, $passdata ) { 
//{"emaillist":["mohammad.zafari@verseautx.com","tanya@verseautx.com"],"includeme":false,"includechtn":true,"includepr":true,"emailtext":"Dear Dr. Tanya Novobrantseva:\n\nYo!  Here's your Pathology Report for 87906T003 ...","bgs":"87906T003","shipdocrefid":"005536","shippeddate":"06/20/2019","prepmethod":"FRESH","preparation":"DMEM","dxspecimencategory":"MALIGNANT","dxsite":"KIDNEY","dxssite":"","dxdx":"CARCINOMA","dxmod":"RENAL CELL - CLEAR CELL -CONVENTIONAL","designation":"[MALIGNANT] KIDNEY :: CARCINOMA / RENAL CELL - CLEAR CELL -CONVENTIONAL ","courier":"UPS","tracknbr":"","salesorder":"006884","dialogid":"L6msAO690cor9x5"}
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview, emailaddress FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       

      //TODO:  DATA CHECKS - CHECK EMAIL ADDRESSES CORRECT / CHECK BOOLEAN FIELDS / CHECK BGS FORMAT
      ( !array_key_exists('emaillist', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'emaillist' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('includeme', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'includeme' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('includepr', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'includepr' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('includechtn', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'includechtn' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('emailtext', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'emailtext' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('bgs', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'bgs' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'dialogid' DOES NOT EXIST.")) : ""; 
      

      ( !is_bool( $pdta['includeme'] ) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY ELEMENT 'includeme' IS NOT A BOOLEAN")) : "";
      ( !is_bool( $pdta['includepr'] ) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY ELEMENT 'includepr' IS NOT A BOOLEAN")) : "";
      ( !is_bool( $pdta['includechtn'] ) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY ELEMENT 'includechtn' IS NOT A BOOLEAN")) : "";
      ( trim( $pdta['emailtext'] ) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY ELEMENT 'emailtext' CANNOT BE EMPTY")) : "";

      foreach ( $pdta['emaillist'] as $k => $v ) {
        ( !filter_var( $v, FILTER_VALIDATE_EMAIL) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "{$v} IS AN INVALID EMAIL ADDRESS")) : "";
      }


      //TODO:  REMOVE THIS FOR PRODUCTION - THIS LINE BLANKS THE PASSED ARRAY OF EMAILS 
      $pdta['emaillist'] = array();

      if ( $pdta['includeme'] ) { 
        $pdta['emaillist'][] = $u['emailaddress'];
      }
      if ( $pdta['includechtn'] ) { 
        $pdta['emaillist'][] = "chtnmail@uphs.upenn.edu";
      }
      $pdta['emaillist'][] = "zackvm.zv@gmail.com";


      if ( $pdta['includepr'] ) {
          $prSQL = "SELECT bs.pathreportid, pr.pathreport FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pbiosample left join masterrecord.qcpathreports pr on bs.pathreportid = pr.prId where replace(bgs,'_','') = :bgs";
        $prRS = $conn->prepare($prSQL);
        $prRS->execute(array(':bgs' => preg_replace('/_/','',$pdta['bgs']) ));
        if ( $prRS->rowCount() === 1 ) { 
           $prtxt = $prRS->fetch(PDO::FETCH_ASSOC);  
           $pdta['emailtext'] = "{$pdta['emailtext']}\n\n<table border=1><tr><td>PATHOLOGY REPORT FOR BIOSAMPLE LABELLED {$pdta['bgs']}</td></tr><tr><td>" . $prtxt['pathreport'] . "</td></tr></table>"; 
        } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  REQUESTED PATHOLOGY REPORT NOT FOUND."));
        }
      }


      if ( $errorInd === 0 ) {        

        $eText = preg_replace( '/\\n/','<br>', preg_replace( '/\\n\\n/','<p>',$pdta['emailtext'] ));

        $msgArr[] = json_encode( $pdta['emaillist'] );
        $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgBody, htmlind, wheninput, bywho) value(:towhoaddressarray, 'CHTN-EASTERN QMS FOLLOWUP EMAIL', :msgBody, 1, now(), 'QA-QMS EMAILER')";
        $emlRS = $conn->prepare( $emlSQL );
        $emlRS->execute(array ( ':towhoaddressarray' => json_encode( $pdta['emaillist'] ), ':msgBody' => "<table border=1><tr><td><CENTER>THE MESSAGE BELOW IS FROM THE QUALITY MANAGEMENT TEAM AT CHTNEASTERN.  PLEASE DO NOT RESPONSED TO THIS EMAIL.  CONTACT THE CHTNEASTERN OFFICE DIRECTLY EITHER BY EMAIL chtnmail@uphs.upenn.edu OR BY CALLING (215) 662-4570.</td></tr><tr><td>{$eText}</td></tr></table>" ));

        $dta['dialogid'] = $pdta['dialogid'];
        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;             

    }

    function qmsgetemailletter ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
      
      //TODO:  WRITE DATA CHECKS
      ( !array_key_exists('qmsletter', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'qmsletter' DOES NOT EXIST.")) : ""; 

      if ( $errorInd === 0 ) {        

        $letSQL = "SELECT qmsletters FROM four.sys_master_menus where menu = 'QMSLETTERS' and menuvalue = :thisletter and dspind = 1 order by dsporder";
        $letRS = $conn->prepare($letSQL);
        $letRS->execute(array(':thisletter' => $pdta['qmsletter'] ));

        if ( $letRS->rowCount() === 1 ) { 
          $letrec = $letRS->fetch(PDO::FETCH_ASSOC);
          preg_match_all('/\^[A-Za-z0-9]+/',$letrec['qmsletters'], $matches, PREG_OFFSET_CAPTURE ) ; 
          $ltr = $letrec['qmsletters'];
          foreach ( $matches[0] as $key => $value ) { 
            $strrplc = $value[0];  
            $newstr = $pdta[  preg_replace('/^\^/','',$value[0]) ];
            $ltr = str_replace( $strrplc, $newstr,  $ltr);
          }
          $dta = $ltr;
          $responseCode = 200;
        } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "NO QMS LETTER FOUND"));
        }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;             
    } 

    function qainvestigatoremailerdata ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 
      
      //TODO:  WRITE DATA CHECKS
      
      if ( $errorInd === 0 ) {        
        
        $bgs =  cryptservice( $pdta['refBGS'] , true);
        $headSQL = "SELECT replace(sg.bgs,'_','') as bgs,  substr(concat('000000', ifnull(sg.shipdocrefid,'')),-6) as shipdocrefid, ifnull(date_format(sg.shippeddate,'%m/%d/%Y'),'') as shippeddate, ifnull(sg.prepmethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation , ifnull(bs.tissType,'') as specimencategory, ifnull(bs.anatomicSite,'') as asite, ifnull(bs.subSite,'') as ssite, ifnull(bs.diagnosis,'') as diagnosis, ifnull(bs.subdiagnos,'') as modifier, ifnull(bs.metsSite,'') as metsfrom, ifnull(date_format(sd.actualshipdate,'%m/%d/%Y'),'') as actualshippeddate, ifnull(sd.acceptedbyemail,'') as acceptoremail, ifnull(sd.acceptedby,'') as acceptedby, ifnull(sd.investemail,'') as investemail, ifnull(sd.investcode,'') as investcode, ifnull(sd.investname,'') as investname, ifnull(sd.investinstitution,'') as investinstitution, ifnull(sd.investdivision,'') as investdivision, ifnull(sd.courier,'') as courier, ifnull(sd.shipmentTrackingNbr,'') as trackingnbr, substr(if(ifnull(sd.salesorder,'') = '','', concat('000000',ifnull(sd.salesorder,''))),-6) as salesorder , ifnull(sd.setupby,'') as setupby FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pbiosample left join masterrecord.ut_shipdoc sd on sg.shipDocRefID = sd.shipDocRefID where replace(sg.bgs, '_','') = :bgs";
        $headRS = $conn->prepare($headSQL);
        $headRS->execute(array(':bgs' => preg_replace('/_/','',$bgs)));
        if ( $headRS->rowCount() === 1) { 
        $h = $headRS->fetch(PDO::FETCH_ASSOC);
        $dta['head'] = $h;
        
        $iEmailSQL = "SELECT add_type, add_email, add_attn FROM vandyinvest.eastern_address where investid = :investid"; 
        $iEmailRS = $conn->prepare($iEmailSQL);
        $iEmailRS->execute(array(':investid' => $h['investcode']));
        
        $iEmail = array(); 
        while ( $ie = $iEmailRS->fetch(PDO::FETCH_ASSOC)) { 
            $iEmail[] = $ie;
        }
        $dta['investemails'] = $iEmail;  
        
        $conSQL = "SELECT con_email, concat(ifnull(con_fname,'') , ' ', ifnull(con_lname,'') ,', ', ifnull(con_title,'')) as condspname, ifnull(con_comment,'') as concomments FROM vandyinvest.eastern_contact where investid = :investid"; 
        $conRS = $conn->prepare($conSQL); 
        $conRS->execute(array(':investid' => $h['investcode']));
        $conemail = array(); 
        while ( $c = $conRS->fetch(PDO::FETCH_ASSOC)) { 
            $conemail[] = $c;
        }
        $dta['contactemail'] = $conemail;


        $qmsLtrsSQL = "SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSLETTERS' and dspind = 1 order by dsporder"; 
        $qmsLtrsRS = $conn->prepare($qmsLtrsSQL);
        $qmsLtrsRS->execute();
        
        $qmsLtrs = array(); 
        while ( $lt = $qmsLtrsRS->fetch(PDO::FETCH_ASSOC)) { 
            $qmsLtrs[] = $lt;
        }
        $dta['qmsletters'] = $qmsLtrs;  

        $msgArr[] = $h['investcode'];  
        $responseCode = 200;
        } else { 
            //TODO:  POP ERROR
        }
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;             
    }
   
    function qmsmassreassignsegments ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();        
       
      ////{"seglist":"[{\"87906T001\":\"451197\"},{\"87906T002\":\"451198\"}]","assignInvCode":"Pending Destroy","assignReqCode":"","segComments":"These are segment comments", "dialogid": "DIALOGID"}
      ( !array_key_exists('seglist', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'seglist' DOES NOT EXIST.")) : ""; 
      ( !array_key_exists('assignInvCode', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'assignInvCode' DOES NOT EXIST.")) : "";        
      ( !array_key_exists('assignReqCode', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'assignReqCode' DOES NOT EXIST.")) : "";     
      ( !array_key_exists('segComments', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'segComments' DOES NOT EXIST.")) : "";  
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FATAL ERROR:  ARRAY KEY 'dialogid' DOES NOT EXIST.")) : "";         

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
         $u = $rs->fetch(PDO::FETCH_ASSOC);
      }       
       
      if ( trim($pdta['assignInvCode']) === "" ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "The 'Investigator id' field must have a value"));    
      } else { 
          //TODO:  DON'T HARD CODE THIS
          if ( strtoupper(trim($pdta['assignInvCode'])) !== 'BANK' && strtoupper(substr(trim($pdta['assignInvCode']), 0, 3)) !== 'INV' && strtoupper(trim($pdta['assignInvCode'])) !== 'PENDING DESTROY' && strtoupper(trim($pdta['assignInvCode'])) !== 'PERMANENT COLLECTION'  ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "The 'Investigator id' field must contain either an investigator's INV code, the status of 'Bank', 'Permanent Collection' or 'Pending Destroy'."));                                
          } else { 
              $iv = "";
              $pv = "";
              $rv = "";
              if ( strtoupper(substr(trim($pdta['assignInvCode']), 0, 3)) === 'INV' ) { 
                  if ( trim($pdta['assignReqCode']) === "" ) { 
                      (list( $errorInd, $msgArr[] ) = array(1 , "When specifying an investigator, a request number must also be specified."));                                
                  } else {
                      $chkI2RSQL = "SELECT i.investid, pr.projid, rq.requestid FROM vandyinvest.invest i  left join vandyinvest.investproj pr on i.investid = pr.investid left join vandyinvest.investtissreq rq on pr.projid = rq.projid  where i.investid = :inv  and requestid = :req";
                      $chkI2RRS = $conn->prepare($chkI2RSQL); 
                      $chkI2RRS->execute(array(':inv' => trim($pdta['assignInvCode']), ':req' => trim($pdta['assignReqCode']))); 
                      if ($chkI2RRS->rowCount() <> 1 ) {
                          (list( $errorInd, $msgArr[] ) = array(1 , "The Investigator - request combination was not found in the request database.  This is not a valid assignment."));
                      } else { 
                          $i = $chkI2RRS->fetch(PDO::FETCH_ASSOC); 
                          $iv = $i['investid'];
                          $pv = $i['projid'];
                          $rv = $i['requestid'];
                      }
                  }                  
              }
          }          
      }
        
      //CHECK SEGMENT ARRAY seglist
      $segArr = json_decode($pdta['seglist'], true);
      ( count($segArr) < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "No Segments have been specified to be re-assigned."  )) : "";
      
      $chkSegSQL =  "SELECT sg.segmentid, sg.bgs, ifnull(sg.segStatus,'') segstatus, ifnull(stschk.menuvalue,'') as restock, ifnull(sg.shipDocRefID,'') as shipdocrefid, ifnull(sg.shippedDate,'') as shippeddate FROM masterrecord.ut_procure_segment sg left join (SELECT menuvalue, assignablestatus, academValue, commercialValue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS' and academValue = 'RESTOCK') as stschk on sg.segstatus = stschk.menuvalue where segmentid = :segid and ifnull(stschk.menuvalue,'') <> '' and ifnull(sg.shipDocRefID,'') = '' and ifnull(shippedDate,'') = '' ";
      $chkSegRS = $conn->prepare($chkSegSQL);
      foreach ( $segArr as $segKey => $segVal ) {
        foreach ( $segVal as $ky => $vl ) { 
                  $chkSegRS->execute(array(':segid' => (int)$vl ));
                  if ( $chkSegRS->rowCount() <> 1 ) { 
                    (list( $errorInd, $msgArr[] ) = array(1 , "The segment, {$ky}, is not in a re-assignable status."));
                  }                 
         }          
      }
      
      
      if ( $errorInd === 0 ) {        
                   
         $newStatus = 'BANKED';
         switch ( strtoupper(substr(trim($pdta['assignInvCode']), 0, 3)) ) { 
             case 'BAN':
               $newStatus = 'BANKED';                 
               break; 
             case 'INV':
               $newStatus = 'ASSIGNED';
               break;
             case 'PEN': 
               $newStatus = 'PENDDEST';                 
               break;
             case 'PER': 
               $newStatus = 'PERMCOLLECT';                                  
               break;
         } 
         
         $usrdsp = "{$u['usr']}/QMS-REASSIGN";
         
         $bckSegStsSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) select segmentid, segstatus, statusBy, statusDate, now(), :usr, :newstatus from  masterrecord.ut_procure_segment where segmentid = :sid";
         $bckSegStsRS = $conn->prepare($bckSegStsSQL); 
         $bckAssSQL = "insert into masterrecord.history_procure_segment_assignment(segmentid, previousassignment, previousproject, previousrequest, previousassignmentdate, previousassignmentby, enteredon, enteredby) select segmentid, ifnull(assignedTo,'NO-INV-ASSIGNMENT') as assignedto, ifnull(assignedProj,'NO-PROJ-ASSIGNMENT') as assignedproj, ifnull(assignedReq,'NO-REQ-ASSIGNMENT') as assignedreq, ifnull(assignmentdate, now()) as assignmentdate, ifnull(assignedby,'NO-BY-ASSIGNMENT'), now(), :usr from masterrecord.ut_procure_segment where segmentid = :sid";
         $bckAssRS = $conn->prepare($bckAssSQL);
         $segUpdate = "update masterrecord.ut_procure_segment set segstatus = :newstatus, statusdate = now(), statusby = :usrdspb, assignedto = :invid, assignedproj = :projid, assignedreq = :reqid, assignmentdate = now(), assignedby = :usrdspa, segmentcomments = trim(concat(ifnull(segmentComments,''), ' ' , :addcmts)), lastUpdatedBy = :usrdsp, lastUpdatedOn = now() where segmentid = :segid";
         $segUpdateRS = $conn->prepare($segUpdate);
         foreach ( $segArr as $segKey => $segVal ) {
            foreach ( $segVal as $ky => $vl ) { 
              //BACK UP SEGMENT TO HISTORY FILE 
              $bckSegStsRS->execute(array(':usr' => $usrdsp, ':newstatus' => $newStatus, ':sid' => (int)$vl));
              $bckAssRS->execute(array(':sid' => (int)$vl, ':usr' => $usrdsp  ));
              //WRITE NEW DATA 
              $segUpdateRS->execute(array(':newstatus' => $newStatus,':usrdspb' => $usrdsp,':invid' => $iv,':projid' => $pv,':reqid' => $rv,':usrdspa' => $usrdsp,':addcmts' => trim($pdta['segComments']) ,':usrdsp' => $usrdsp,':segid' => (int)$vl ));
            }
         }         
        
        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }
    
    function markqainconcomplete ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
      }
        
      $biohpr = (int)cryptservice( $pdta['encyreviewid'], 'd' );
      $bg = cryptservice( $pdta['encybg'] , 'd' );
      $chkBGSQL = "SELECT * FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') = :bg and hprresult = :biohpr and qcind = 0";
      $chkBGRS = $conn->prepare($chkBGSQL);
      $chkBGRS->execute(array(':bg' => $bg, ':biohpr' => $biohpr));
      if ( $chkBGRS->rowCount() < 1 ) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP WAS NOT FOUND.  EITHER THERE IS NO HPR RECORD, IT DOESN'T EXIST OR IT IS ALREADY MARKED AS QA COMPLETE.  IF YOU FEEL THIS IS IN ERROR, SEE A CHTNEASTERN INFORMATICS PERSON."));
      }

if ( $errorInd === 0 ) { 
        $backupSQL = "insert into masterrecord.history_procure_biosample_qms (pbiosample, readlabel, qcvalv2, hprindicator, hprmarkbyon, qcindicator, qcmarkbyon, qcprocstatus, labaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresultid, slidereviewed, hpron, hprby, historyrecordon, historyrecordby) SELECT pbiosample, read_label, qcvalv2, HPRInd, hprmarkbyon, QCInd, qcmarkbyon, qcprocstatus, labactionaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresult, hprslidereviewed, hpron, hprby, now(), :usr FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') like :bg and hprresult = :hprresult and qcind = 0";
        $backRS = $conn->prepare( $backupSQL ); 
        $backRS->execute( array( ':bg' => "{$bg}%", ':hprresult' => $biohpr, ':usr' => "{$u['usr']}/QMS-QA-PROCESS"));

        $qmsSQL = "update masterrecord.ut_procure_biosample set qcprocstatus = 'L', qmsstatusby = :statby, qmsstatuson = now(), qmsnote = '' where replace(read_label,'_','') = :bg and hprresult = :biohpr and qcind = 0";
        $qmsRS = $conn->prepare($qmsSQL); 
        $qmsRS->execute( array( ':statby' => 'QMS-QA-PROCESS-' . strtoupper($u['usr']), ':bg' => $bg, ':biohpr' => $biohpr));

        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;                            
    }
   
    function markqafinalcomplete ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
      }

      $biohpr = (int)cryptservice( $pdta['encyreviewid'], 'd' );
      $bg = cryptservice( $pdta['encybg'] , 'd' );
      $chkBGSQL = "SELECT * FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') = :bg and hprresult = :biohpr and qcind = 0";
      $chkBGRS = $conn->prepare($chkBGSQL);
      $chkBGRS->execute(array(':bg' => $bg, ':biohpr' => $biohpr));
      if ( $chkBGRS->rowCount() < 1 ) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THE BIOGROUP WAS NOT FOUND.  EITHER THERE IS NO HPR RECORD, IT DOESN'T EXIST OR IT IS ALREADY MARKED AS QA COMPLETE.  IF YOU FEEL THIS IS IN ERROR, SEE A CHTNEASTERN INFORMATICS PERSON."));
      }

      if ( $errorInd === 0 ) { 
        $backupSQL = "insert into masterrecord.history_procure_biosample_qms (pbiosample, readlabel, qcvalv2, hprindicator, hprmarkbyon, qcindicator, qcmarkbyon, qcprocstatus, labaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresultid, slidereviewed, hpron, hprby, historyrecordon, historyrecordby) SELECT pbiosample, read_label, qcvalv2, HPRInd, hprmarkbyon, QCInd, qcmarkbyon, qcprocstatus, labactionaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresult, hprslidereviewed, hpron, hprby, now(), :usr FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') like :bg and hprresult = :hprresult and qcind = 0";
        $backRS = $conn->prepare( $backupSQL ); 
        $backRS->execute( array( ':bg' => "{$bg}%", ':hprresult' => $biohpr, ':usr' => "{$u['usr']}/QMS-QA-PROCESS"));

        $qmsSQL = "update masterrecord.ut_procure_biosample set qcind = 1, QCMarkByOn = concat(:usr,' ', now()), qcprocstatus = 'Q', qmsstatusby = :statby, qmsstatuson = now(), qmsnote = '' where replace(read_label,'_','') = :bg and hprresult = :biohpr and qcind = 0";
        $qmsRS = $conn->prepare($qmsSQL); 
        $qmsRS->execute( array( ':usr' => "{$u['usr']}", ':statby' => 'QMS-QA-PROCESS-' . strtoupper($u['usr']), ':bg' => $bg, ':biohpr' => $biohpr));

        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;              
    }

    function qamoletestfinal ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();
 
      //{"uninvolvedvalue":"0"
      //,"tumorgrade":"3","tumorscale":"FIGO"
      //,"percentagetumor":"90","percentagecellularity":"90","percentagenecrosis":"1","percentageneoplasticstroma":"1"
      //,"percentagenonneoplasticstroma":"10","percentageacellularmucin":"1"
      //,"moleteststring":"[[\"ALK\",\"ALK (Anaplastic Lymphoma Kinase)\",\"ALK-NEGATIVE\",\"Negative (-)\",\"Z\"]]"}  

      //TODO:  DATA CHECKS 
      //TODO: Figure out how to NOT hard Code Percentage Values 
      ( !array_key_exists('encyhpr', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'encyhpr' is missing.  Fatal Error")) : "";
      ( !array_key_exists('encybg', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'encybg' is missing.  Fatal Error")) : "";
      ( !array_key_exists('uninvolvedvalue', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'uninvolvedvalue' is missing.  Fatal Error")) : "";
      ( !array_key_exists('tumorgrade', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'tumorgrade' is missing.  Fatal Error")) : "";
      ( !array_key_exists('tumorscale', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'tumorscale' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentagetumor', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentagetumor' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentagecellularity', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentagecellularity' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentagenecrosis', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentagenecrosis' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentageneoplasticstroma', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentageneoplasticstroma' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentagenonneoplasticstroma', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentagenonneoplasticstroma' is missing.  Fatal Error")) : "";
      ( !array_key_exists('percentageacellularmucin', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'percentageacellularmucin' is missing.  Fatal Error")) : "";
      ( !array_key_exists('moleteststring', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'moleteststring' is missing.  Fatal Error")) : "";
      
      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
      }
      //CHECK VALID BG
      $bg = cryptservice($pdta['encybg'],'d');
      $chkBGSQL = "SELECT * FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') = :bg and qcind = 0"; 
      $chkBGRS = $conn->prepare($chkBGSQL);
      $chkBGRS->execute(array(':bg' => preg_replace('/_/','', $bg)  ));
      ( $chkBGRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "EITHER THE BIOGROUP WAS NOT FOUND OR IT HAS ALREADY BEEN MARKED QMS-QA COMPLETE ({$bg}).  IF YOU FEEL THIS IS IN ERROR, SEE A CHTNEAST INFORMATICS STAFF MEMBER")) : "";
      
      //TODO: CHECK THAT MENU VALUES ARE VALID OPTIONS
          if ( trim($pdta['moleteststring']) !== "" ) { 
            $mt = json_decode( $pdta['moleteststring'], true); 
            foreach ( $mt as $mkey => $mval) {
              if ( trim($mval[0]) === "" ) { 
                (list( $errorInd, $msgArr[] ) = array(1 , "A Molecular Test Value must be specified for all entered molecular tests"));
              } else {   
                $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'MOLECULARTEST' and menuvalue = :suppliedvalue";
                $chkRS = $conn->prepare($chkSQL);
                $chkRS->execute(array(':suppliedvalue' => $mval[0] ));
                ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$mval[0]}' DOES NOT APPEAR AS A VALUE ON THE HPR-MOLECULAR MENU TREE. SEE CHTNEastern Informatics!")) : "";
              }
            }
          }      


      if ( $errorInd === 0 ) { 

          $bgdta = $chkBGRS->fetch(PDO::FETCH_ASSOC);
          $pbiosample = $bgdta['pBioSample'];
          ////BACKUP BG
          $bckSQL = "insert into masterrecord.history_procure_biosample_vocab (pbiosample, uninvolvedind, tumorgrade, tumorscale, speccat, collectedsite, subsite, siteposition, diagnosis, modifier, metssite, systemicdx, bywho, onwhen) SELECT pBioSample, uninvolvedind, tumorgrade, tumorscale, ifnull(tissType,'') as tisstype, ifnull(anatomicSite,'') as site, ifnull(subSite,'') as subsite, ifnull(sitePosition,'') as siteposition, ifnull(diagnosis,'') as diagnosis, ifnull(subdiagnos,'') as modifier, ifnull(metsSite,'') as metssite, ifnull(pdxSystemic,'') as systemic, :user, now() FROM masterrecord.ut_procure_biosample where read_label = replace(:bg,'_','')"; 
          $bckRS = $conn->prepare($bckSQL);
          $bckRS->execute(array(':bg' => preg_replace('/_/','',$bg) , ':user' => $u['usr']));
                
          //UPDATE UNINVOLVED 
          $uniSQL = "update masterrecord.ut_procure_biosample set uninvolvedInd = :uninvolvedvalue where replace(read_label,'_','') = :bg and qcind = 0";
          $uniRS = $conn->prepare($uniSQL);
          $uniRS->execute(array(':uninvolvedvalue' => $pdta['uninvolvedvalue'], ':bg' => preg_replace('/_/','',$bg)));
                    
          //UPDATE TUMOR PERCENTAGE
          $tmrSQL = "update masterrecord.ut_procure_biosample set tumorgrade = :tumorgrade, tumorscale = :tumorscale where replace(read_label,'_','') = :bg and qcind = 0";
          $tmrRS = $conn->prepare($tmrSQL);
          $tmrRS->execute(array(':tumorgrade' => $pdta['tumorgrade'], ':tumorscale' => $pdta['tumorscale'], ':bg' => preg_replace('/_/','',$bg)));
          
          //UPDATE PERCENTAGES
          $updPrcSQL = "update masterrecord.ut_procure_biosample_samplecomposition set dspind = 0, updateon = now(), updateby = :usr where replace(readlabel,'_','') = :bg and dspind = 1";         
          $updPrcRS = $conn->prepare($updPrcSQL); 
          $updPrcRS->execute(array(':bg' =>preg_replace('/_/','',$bg) , ':usr' => $u['usr'] . "/QMS-QA" ));

          //TODO:  DO NOT HARD CODE THESE VALUES
          $prcSQL = "insert into masterrecord.ut_procure_biosample_samplecomposition (readlabel, prctype, prcvalue, dspind, inputon, inputby) values (:readlabel,:prctypevalue,:prcvalue,1,now(),:usr)";                
          $prcRS = $conn->prepare($prcSQL);

          if ( trim($pdta['percentagetumor']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'PERCENTAGETUMOR', ':prcvalue' => $pdta['percentagetumor'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }
          
          if ( trim($pdta['percentagecellularity']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'PERCENTAGECELLULARITY', ':prcvalue' => $pdta['percentagecellularity'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }

          if ( trim($pdta['percentagenecrosis']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'TUMORNECROSIS', ':prcvalue' => $pdta['percentagenecrosis'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }          
      
          if ( trim($pdta['percentageneoplasticstroma']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'NEOPLASTICSTROMA', ':prcvalue' => $pdta['percentageneoplasticstroma'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }       

          if ( trim($pdta['percentagenonneoplasticstroma']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'NONNEOPLASTICSTROMA', ':prcvalue' => $pdta['percentagenonneoplasticstroma'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }           

          if ( trim($pdta['percentageacellularmucin']) !== "") { 
             $prcRS->execute(array(':readlabel' =>  preg_replace('/_/','',$bg), ':prctypevalue' => 'ACELLULARMUCIN', ':prcvalue' => $pdta['percentageacellularmucin'], ':usr' => $u['usr'] . "/QMS-QA")); 
          }            
          
          //UPDATE MOLECULAR TESTS
          $updmolesql = "update masterrecord.ut_procure_biosample_molecular set dspind = 0, updatedby = :usr, updatedon = now() where pbiosample = :pbiosample and dspind = 1";
          $updmoleRS = $conn->prepare($updmolesql);
          $updmoleRS->execute(array(':pbiosample' => $pbiosample, ':usr' => "{$u['usr']}/QMS-QA"));          
          $moleInsSQL = "insert into masterrecord.ut_procure_biosample_molecular (pbiosample, bgprcnbr, testid, testresultid, molenote, onwhen, onby, dspind) values(:pbiosample, :bgprcnbr, :testid, :testresultid, :molenote, now(), :onby, 1)";
          $moleInsRS = $conn->prepare($moleInsSQL); 
          foreach ( $mt as $mkey => $mval) {           
            $moleInsRS->execute(array(':pbiosample' => $pbiosample, ':bgprcnbr' => preg_replace('/_/','',$bg), ':testid' => trim($mval[0]), ':testresultid' => trim($mval[2]), ':molenote' => trim($mval[4]), ':onby' => "{$u['usr']}/QMS-QA" ));
         }
          $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;              
    }

    function copyhprtobs ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();

      ( !array_key_exists('biohpr', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'biohpr' is missing.  Fatal Error")) : "";

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowQMS = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO QMS-QA.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
      }

      $biohpr =  cryptservice($pdta['biohpr'],'d');
      $hprExSQL = "SELECT biogroupid FROM masterrecord.ut_hpr_biosample where biohpr = :biohpr";
      $hprExRS = $conn->prepare($hprExSQL);
      $hprExRS->execute(array(':biohpr' => (int)$biohpr));
      if ( $hprExRS->rowCount() === 1 ) { 
          $hprBG = $hprExRS->fetch(PDO::FETCH_ASSOC);
      } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "HPR REVIEW NOT FOUND BY REFERENCE NUMBER.  NOTIFY A CHTNInformatics STAFF MEMBER"));
      }
      
      //CHECK VALID BG
      $bg = $hprBG['biogroupid'];
      $chkBGSQL = "SELECT * FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') like :bg and hprresult = :hprid and qcind = 0"; 
      $chkBGRS = $conn->prepare($chkBGSQL);
      $chkBGRS->execute(array(':bg' => preg_replace('/_/','', $bg) . "%", ':hprid' => $biohpr));
      ( $chkBGRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "EITHER THE BIOGROUP WAS NOT FOUND OR IT HAS ALREADY BEEN MARKED QMS-QA COMPLETE ({$bg}).  IF YOU FEEL THIS IS IN ERROR, SEE A CHTNEAST INFORMATICS STAFF MEMBER")) : "";
      
      if ( $errorInd === 0 ) { 
          $backSQL = "insert into masterrecord.history_procure_biosample_vocab (pbiosample, uninvolvedind, tumorgrade, tumorscale, speccat, collectedsite, subsite, siteposition, diagnosis, modifier, metssite, systemicdx, bywho, onwhen) SELECT pBioSample, uninvolvedind, tumorgrade, tumorscale, ifnull(tissType,'') as tisstype, ifnull(anatomicSite,'') as site, ifnull(subSite,'') as subsite, ifnull(sitePosition,'') as siteposition, ifnull(diagnosis,'') as diagnosis, ifnull(subdiagnos,'') as modifier, ifnull(metsSite,'') as metssite, ifnull(pdxSystemic,'') as systemic, :user, now() FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') like :bg and hprresult = :hprid and qcind = 0 "; 

        $backupRS = $conn->prepare($backSQL);
        $backupRS->execute(array(':bg' => preg_replace( '/_/','', $bg ) . "%" , ':hprid' => (int)$biohpr, ':user' => "QA-HPR-COPY-OP-BACKUP/{$u['usr']}" )); 

        $moveSQL = "update masterrecord.ut_procure_biosample bs, (SELECT specCat, site, subSite, siteposition, dx, subdiagnosis, Mets, systemiccomobid  FROM masterrecord.ut_hpr_biosample where biohpr = :hprid) hpr set bs.tissType = hpr.speccat, bs.anatomicSite = hpr.site, bs.subSite = hpr.subsite, bs.sitePosition = hpr.siteposition, bs.diagnosis = hpr.dx, bs.subdiagnos = hpr.subdiagnosis, bs.metsSite = hpr.mets, bs.pdxSystemic = hpr.systemiccomobid where replace(read_label,'_','') like :bg and hprresult = :bshprid and qcind = 0 ";
        $moveRS = $conn->prepare($moveSQL); 
        $moveRS->execute(array(':hprid' => (int)$biohpr, ':bg' => preg_replace('/_/','', $bg) . "%" , ':bshprid' => (int)$biohpr ));

        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    } 

    function qareviewworkbenchdata ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();

      $reviewid =  cryptservice($pdta['reviewid'],'d');

      //TODO:  DO DATA CHECKS
      if ( $errorInd === 0 ) { 

        $headSQL = <<<HEADHPRSQL
SELECT 
substr(concat('000000',hpr.biohpr),-6) biohpr
, replace(hpr.bgs,'_','') as slidebgs
, ifnull(date_format(hpr.reviewedon,'%m/%d/%Y'),'') as reviewedon 
, ifnull(hpr.reviewer,'') as reviewer
, ifnull(hpr.inputby,'') as inputby
,ifnull(hpr.reviewassignind,0) as reviewassignind                
, ifnull(hpr.decision,'') as hprdecisionvalue
, ifnull(dcs.dspvalue,'') as hprdecision
, ifnull(hpr.speccat,'') as hprspeccat
, ifnull(hpr.site,'') as hprsite
, ifnull(hpr.subsite,'') as hprsubsite
, ifnull(hpr.dx,'') as hprdx
, ifnull(hpr.subdiagnosis,'') as hprdxmod
, ifnull(hpr.mets,'') as hprmets
, ifnull(hpr.systemiccomobid,'') as hprcomobid
, ifnull(hpr.tumorgrade,'') as hprtumorgrade
, ifnull(hpr.tumorscale,'') as hprtumorscalevalue
, ifnull(tsc.dspvalue,'') as hprtumorscaledsp
, ifnull(hpr.uninvolvedsample,'') as hpruninvolvedvalue
, ifnull(uni.dspvalue,'') as hpruninvolveddsp
, ifnull(hpr.rarereason,'') as hprrarereason
, ifnull(hpr.generalcomments,'') as hprgeneralcomments
, ifnull(hpr.specialinstructions,'') as hprspecialinstructions
, ifnull(hpr.inconclusivetxt,'') as hprinconclusivetext
, ifnull(hpr.unusabletxt,'') as hprunusabletext
, ifnull(bs.pbiosample,'') as pbiosample
, replace(ifnull(bs.read_label,''),'_','') as bsreadlabel
, ifnull(bs.hprind,0) as hprind
, ifnull(bs.qcind,0) as qcind    
, ifnull(bs.tisstype,'') as bsspeccat
, ifnull(bs.anatomicsite,'') as bsanatomicsite
, ifnull(bs.subsite,'') as bssubsite
, ifnull(bs.diagnosis,'') as bsdx
, ifnull(bs.subdiagnos,'') as bsdxmod
, ifnull(bs.metssite,'') as bsmets
, ifnull(bs.pdxsystemic,'') as bscomo                
, ifnull(bs.associd,'') as associd
, ifnull(bs.chemoind,'') as bschemoindvalue 
, ifnull(cxv.dspvalue,'') as bschemoinddsp
, ifnull(bs.radind,'') as bsradindvalue
, ifnull(rxv.dspvalue,'') as bsradinddsp
, ifnull(bs.proctype,'') as bsproctypevalue
, ifnull(prc.dspvalue,'') as bsproctypedsp
, ifnull(bs.procureinstitution,'') as bsprocureinstitution
, ifnull(inst.dspvalue,'') as bsprocureinstitutiondsp
, ifnull(date_format(bs.createdOn,'%m/%d/%Y'),'') as procurementdate
, ifnull(bs.pxiage,'') as bspxiage
, ifnull(auom.dspvalue,'') as bspxiageuom
, ifnull(bs.pxiageuom,'') as bspxiageuomvalue
, ifnull(bs.pxiGender,'') as  bspxisex
, ifnull(sx.dspvalue,'') as bspxisexdsp
, ifnull(bs.pxirace,'') as bspxirace
, ifnull(bs.pxiid,'') as bspxiid
, ifnull(bs.biosamplecomment,'') as bscomments
, ifnull(bs.questionhpr,'') as bshprqstn
, ifnull(bs.pathreportid,0) as pathreportid
, ifnull(prt.pathreport,'') as pathreport
, ifnull(prt.uploadedBy,'') as pruploadedby
, ifnull(date_format(prt.uploadedon,'%m/%d/%Y'),'') as uploadedon 
FROM masterrecord.ut_hpr_biosample hpr
left join masterrecord.ut_procure_biosample bs on hpr.biogroupid = bs.pbiosample
left join (SELECT menuValue, dspValue FROM four.sys_master_menus where menu = 'HPRDECISION') as dcs on hpr.decision = dcs.menuvalue
left join (SELECT menuValue, dspValue FROM four.sys_master_menus where menu = 'HPRTUMORSCALE') as tsc on hpr.tumorscale = tsc.menuvalue
left join (SELECT menuValue, dspValue FROM four.sys_master_menus where menu = 'UNINVOLVEDIND') as uni on hpr.uninvolvedSample = uni.menuvalue
left join masterrecord.qcpathreports prt on bs.pathreportid = prt.prid
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'cx') as cxv on bs.chemoInd = cxv.menuvalue
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'rx') as rxv on bs.radInd = rxv.menuvalue
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'proctype') as prc on bs.procType = prc.menuvalue
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on bs.procureInstitution = inst.menuvalue
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'AGEUOM') as auom on bs.pxiAgeUOM = auom.menuvalue
left join (SELECT menu, menuValue, dspValue FROM four.sys_master_menus where menu = 'PXSEX') as sx on bs.pxiGender = sx.menuvalue
where biohpr = :reviewid
HEADHPRSQL;
        $headRS = $conn->prepare($headSQL);
        $headRS->execute(array(':reviewid' => $reviewid));
        while ($h = $headRS->fetch(PDO::FETCH_ASSOC)) { 
          $dta['hprhead'] = $h;
        }

        $prcSQL = <<<PRCSQL
select  
ifnull(prcval.menuvalue,'') as ptypeval
, ifnull(prcval.dspvalue,'') ptypedsp
, ifnull(hprprc.prcvalue,'') as prcvalue 
from 
(SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HPRPERCENTAGE' and dspind = 1 order by dsporder) as prcval
left join (SELECT * FROM masterrecord.ut_hpr_percentages where biohpr = :reviewid) hprprc on prcval.menuvalue = hprprc.prctypevalue
PRCSQL;

        $prcRS = $conn->prepare($prcSQL); 
        $prcRS->execute(array(':reviewid' => $reviewid));
        $prcgroup = array();        
        while ( $prc = $prcRS->fetch(PDO::FETCH_ASSOC)) { 
            $prcgroup[] = $prc;            
        }
        $dta['percentvalues'] = $prcgroup;

        $moleSQL = <<<MOLESQL
SELECT moletestvalue, moletest, resultindexvalue, resultindex, resultdegree FROM masterrecord.ut_hpr_moleculartests where biohpr = :reviewid
MOLESQL;
        $moleRS = $conn->prepare($moleSQL); 
        $moleRS->execute(array(':reviewid' => $reviewid));
        $molegrp = array();
        while ( $mole = $moleRS->fetch(PDO::FETCH_ASSOC)) { 
          $molegrp[] = $mole;
        }
        $dta['moleculartests'] = $molegrp;

        $assSQL = <<<ASSSQL
select  
  dta.readlabel, concat(dta.bgsbg
  , if (dta.minseg = dta.maxseg, dta.minseg, concat(dta.minseg,'-',dta.maxseg))) as bgs
  ,  dta.prepmethod, dta.preparation, dta.metric, dta.mmdsp, dta.segstatus, sgst.dspvalue as segstatusdsp   , dta.shippeddate, dta.shipdocrefid, ifnull(sd.sdstatus,'') as sdstatus, ifnull(sd.salesorder,'') as salesorder, ifnull(sd.salesorderamount,'') as salesorderamount
  , dta.slidegroupid, dta.assignedto, dta.investlname, dta.investfname
  , dta.investinstitution, dta.assignedreq, dta.procurementdate, dta.specimencategory
  , dta.site, dta.subsite, dta.dx, dta.subdx, dta.metsite, dta.hprind, dta.qcind, dta.createdby
  , dta.bsprocurementdate
  , dta.qcprocstatus
  , qmsstat.dspvalue as qmsstatus 
  , dta.hprresult, dta.hprdecision
  , qmsdec.dspvalue as hprdecdsp 
  , dta.hpron
  from 
  (Select 
  conglom.readlabel, conglom.bgsbg
  , min(conglom.segmentlabel) minseg
  , max(conglom.segmentlabel) maxseg
  ,  conglom.prepmethod, conglom.preparation
  , conglom.metric
  , conglom.mmdsp
  , conglom.segstatus  , conglom.shippeddate, conglom.shipdocrefid
  , conglom.slidegroupid, conglom.assignedto, conglom.investlname, conglom.investfname
  , conglom.investinstitution, conglom.assignedreq, conglom.procurementdate, conglom.specimencategory
  , conglom.site, conglom.subsite, conglom.dx, conglom.subdx, conglom.metsite, conglom.hprind, conglom.qcind, conglom.createdby
  , conglom. bsprocurementdate
  , conglom.qcprocstatus, conglom.hprresult, conglom.hprdecision, conglom.hpron 
  from 
  (SELECT 
  replace(bs.read_label,'_','') as readlabel, substr(replace(sg.bgs,'_',''),1,6) as bgsbg, ifnull(sg.segmentlabel,'') as segmentlabel, ifnull(sg.prepmethod,'') as prepmethod
  , ifnull(sg.preparation,'') as preparation, ifnull(sg.metric,'') as metric, ifnull(mmnu.longvalue,'') as mmdsp, ifnull(sg.segstatus,'') as segstatus, ifnull(date_format(sg.shippeddate,'%m/%d/%Y'),'') as shippeddate  , ifnull(sg.shipdocrefid,'') as shipdocrefid
  , ifnull(sg.SlideGroupID,'') as slidegroupid, ifnull(sg.assignedTo,'') as assignedto, ifnull(i.invest_lname,'') as investlname, ifnull(i.invest_fname,'') as investfname
  , ifnull(i.invest_homeinstitute,'') as investinstitution, ifnull(sg.assignedReq,'') as assignedreq 
  , ifnull(date_format(sg.procurementDate,'%m/%d/%Y'),'') as procurementdate
  , ifnull(bs.tisstype,'') as specimencategory, ifnull(bs.anatomicsite,'') as site, ifnull(bs.subsite,'') as subsite, ifnull(bs.diagnosis,'') as dx
  , ifnull(bs.subdiagnos,'') as subdx, ifnull(bs.metssite,'') as metsite, ifnull(bs.hprind,0) as hprind, ifnull(bs.qcind,0) as qcind
  , ifnull(bs.createdby,'') as createdby
  , ifnull(date_format(bs.createdon,'%m/%d/%Y'),'') as bsprocurementdate
  , ifnull(bs.qcprocstatus,'') as qcprocstatus
  , ifnull(bs.hprresult,0) as hprresult
  , ifnull(bs.hprdecision,'') as hprdecision
  , ifnull(date_format(bs.hpron,'%m/%d/%Y'),'') as hpron  
  FROM masterrecord.ut_procure_biosample bs
  left join masterrecord.ut_procure_segment sg on bs.pbiosample = sg.biosamplelabel
  left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') as mmnu on sg.metricuom = mmnu.menuvalue                 
  left join vandyinvest.invest i on sg.assignedto = i.investid
  where bs.associd = :associd and bs.voidind <> 1 and sg.voidind <> 1 
  ) conglom

  group by conglom.readlabel, conglom.bgsbg, conglom.prepmethod, conglom.preparation, conglom.metric, conglom.mmdsp, conglom.segstatus, conglom.shippeddate, conglom.shipdocrefid
  , conglom.slidegroupid, conglom.assignedto, conglom.investlname, conglom.investfname
  , conglom.investinstitution, conglom.assignedreq, conglom.procurementdate, conglom.specimencategory
  , conglom.site, conglom.subsite, conglom.dx, conglom.subdx, conglom.metsite, conglom.hprind, conglom.qcind, conglom.createdby
  , conglom.bsprocurementdate
  , conglom.procurementdate, conglom.qcprocstatus, conglom.hprresult, conglom.hprdecision, conglom.hpron
  order by conglom.readlabel, min(conglom.segmentlabel)) dta  
  left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus' ) qmsstat on dta.qcprocstatus = qmsstat.menuvalue
  left join ( SELECT menu, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HPRDECISION' ) qmsdec on dta.hprdecision = qmsdec.menuvalue
  left join masterrecord.ut_shipdoc sd on dta.shipdocrefid = sd.shipdocrefid
  left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') as sgst on dta.segstatus = sgst.menuvalue
  order by readlabel, bgs
ASSSQL;
        $assRS = $conn->prepare($assSQL);
        $assRS->execute(array(':associd' => $dta['hprhead']['associd'] ));
        $assgroup = array();
        while ($a = $assRS->fetch(PDO::FETCH_ASSOC)) { 
          $assgroup[] = $a;
        }
        $dta['associativelisting'] = $assgroup;

        $pristineSQL = <<<PRISTINESQL
SELECT ifnull(dxds.speccat,'') as speccat
, ifnull(dxds.primarysite,'') as primarysite
, ifnull(dxds.primarysubsite,'') as primarysubsite
, ifnull(dxds.diagnosis,'') dx
, ifnull(dxds.diagnosismodifier,'') as dxmod
, ifnull(dxds.metssite,'') as metssite
, ifnull(dxds.systemdiagnosis,'') as systemdiagnosis
, ifnull(dxds.classification,'') as classification  
, ifnull(dxds.refBy,'') as refby
, ifnull(date_format(dxds.refon,'%m/%d/%Y'),'') as refon 
, ifnull(uni.dspvalue,'') as uninvolved
FROM four.ref_procureBiosample_designation dxds
left join ( select menuvalue, dspvalue from four.sys_master_menus where menu = 'UNINVOLVEDIND') uni on dxds.unknownmet = uni.menuvalue
where pbiosample = :pbiosample and activeind = 1
PRISTINESQL;
        $pristRS = $conn->prepare($pristineSQL); 
        $pristRS->execute(array(':pbiosample' => $dta['hprhead']['pbiosample']));
        $pristGroup = array();
        while ($p = $pristRS->fetch(PDO::FETCH_ASSOC)) { 
          $pristGroup[] = $p;
        }
        $dta['pristine'] = $pristGroup; 
 
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function qmsquelist ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array();
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();

      //TODO:  DO DATA CHECKS
      if ( $errorInd === 0 ) {

        $addhprdecision = "";  
        switch ( $pdta['decisiondisplay'] ) { 
          case 'confirm':
            $addhprdecision = " and bs.hprdecision = :decisioncode ";
            $hprdecisioncode = $pdta['decisiondisplay'];
            break;
          case 'additional':
            $addhprdecision = " and bs.hprdecision = :decisioncode ";
            $hprdecisioncode = $pdta['decisiondisplay'];
            break;
          case 'denied':
            $addhprdecision = " and bs.hprdecision = :decisioncode ";
            $hprdecisioncode = $pdta['decisiondisplay'];
            break;
          case 'unusable':
            $addhprdecision = " and bs.hprdecision = :decisioncode ";
            $hprdecisioncode = $pdta['decisiondisplay'];
            break;
          case 'inconclusive':
            $addhprdecision = " and bs.hprdecision = :decisioncode ";
            $hprdecisioncode = $pdta['decisiondisplay'];
            break;


        }

        $queSQL = "SELECT replace(ifnull(bs.read_label,''),'_','') as readlabel, ifnull(bs.tisstype,'') as procspeccat, ifnull(bs.anatomicsite,'') as procsite, ifnull(bs.subsite,'') as procsubsite, ifnull(bs.diagnosis,'') as procdiagnosis"
                . ", ifnull(bs.subdiagnos,'') as procsubdiagnosis, ifnull(bs.QCProcStatus,'') as qmsprocstatusvalue, ifnull(hsts.dspvalue,'') as qmsstatusdsp, ifnull(bs.HPRDecision,'') as hprdecisionvalue"
                . ", ifnull(hdc.dspvalue,'') as hprdecisiondsp, ifnull(hpr.speccat,'') as hprspeccat, ifnull(hpr.site,'') as hprsite, ifnull(hpr.subsite,'') as hprsubsite, ifnull(hpr.dx,'') as hprdiagnosis"
                . ", ifnull(hpr.subdiagnosis,'') as hprsubdiagnosis, ifnull(bs.HPRResult,0) as hprresultid, replace(ifnull(bs.HPRSlideReviewed,''),'_','') as hprslidereviewed, ifnull(bs.HPRBy,'') as hprby "
                . ", ifnull(date_format(bs.HPROn, '%m/%d/%Y'),'') as hpron "
                . "FROM masterrecord.ut_procure_biosample bs "
                . "left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus') as hsts on bs.qcprocstatus = hsts.menuvalue "
                . "left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HPRDECISION') as hdc on bs.hprdecision = hdc.menuvalue "
                . "left join (SELECT biohpr, speccat, site, subsite, dx, subdiagnosis FROM masterrecord.ut_hpr_biosample) as hpr on bs.hprresult = hpr.biohpr "
                . "where hprind = 1 and qcind = 0 and bs.qcprocstatus = 'H' {$addhprdecision} order by pbiosample asc";
          $queRS = $conn->prepare($queSQL);
          if ( $addhprdecision !== "" ) { 
              $queRS->execute(array(':decisioncode' => $hprdecisioncode )); 
          } else {
              $queRS->execute(); 
          }
          
          $itemsfound = $queRS->rowCount(); 
          while ( $r = $queRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }

          $msgArr[] = $passdata;
          $responseCode = 200;
      } 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function hprreturnslidetray ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();
      //DO DATA CHECKS
      //{"rtnTrayId":"HPRT002","dialogid":"QxQm1ZoO39CFtTq","locationscancode":"RTN003","returnlocationnote":"Location Note goes here","notfinishedreason":"SLIDESNOTRIGHT","notfinishednote":"Not Finished Note "}
      ( !array_key_exists('rtnTrayId', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'rtnTrayId' is missing.  Fatal Error")) : "";
      ( !array_key_exists('locationscancode', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'returnlocationnote' is missing.  Fatal Error")) : "";
      ( !array_key_exists('returnlocationnote', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'returnlocationnote' is missing.  Fatal Error")) : "";
      ( trim($pdta['rtnTrayId']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The Slide Tray Id is blank")) : "";
      ( trim($pdta['locationscancode']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The Slide Tray Pick-up Location cannot be blank.")) : "";
      ( array_key_exists('notfinishedreason',$pdta) && trim($pdta['notfinishedreason']) === "") ? (list( $errorInd, $msgArr[] ) = array(1 , "The Reason the tray was not complete must be specified.")) : "";
 
      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowHPR = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ( $rs->rowCount() <  1 ) {
          (list( $errorInd, $msgArr[] ) = array(1 , "USER IS NOT ALLOWED ACCESS TO HPR.  LOG OUT AND BACK IN IF YOU FEEL THIS IS IN ERROR."));
      } else { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
      }


      if ( $errorInd === 0 ) { 

        $chkSQL = "SELECT * FROM masterrecord.ut_procure_segment where HPRBoxNbr = :trayscancode and ifnull(hprslideread,0) = 0";
        $chkRS = $conn->prepare($chkSQL);
        $chkRS->execute(array(':trayscancode' => $pdta['rtnTrayId']));
        if ( $chkRS->rowCount() < 1 ) { 
          //COMPLETED TRAY
          $traySTSSQL = "update four.sys_inventoryLocations set hprtraystatus = 'REVIEWCOMPLETE', hprtraystatusby = :usr, hprtrayheldwithin = :heldwith, hprtrayheldwithinnote = :heldwithinnote, hprtraystatuson = now() where scancode = :scncode";
          $traySTSRS = $conn->prepare($traySTSSQL);
          $traySTSRS->execute(array(':usr' => $u['usr'], ':scncode' => $pdta['rtnTrayId'], ':heldwith' => $pdta['locationscancode'], ':heldwithinnote' => trim($pdta['returnlocationnote'])   )); 
          $tryHisSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, tray, traystatus, historyon, historyby, trayheldwithin, trayheldwithinnote) values(:trayscancode, :tray, :traystatus, now(), :historyby, :trayheldwithin, :trayheldwithinnote)";
          $tryHisRS = $conn->prepare($tryHisSQL);
          $tryHisRS->execute(array(':trayscancode' => $pdta['rtnTrayId'], ':tray' => $pdta['rtnTrayId'], ':traystatus' => 'REVIEWCOMPLETE', ':historyby' => $u['usr'], ':trayheldwithin' =>  $pdta['locationscancode'], ':trayheldwithinnote' => trim($pdta['returnlocationnote'])));
          $responseCode = 200;
        } else { 
            //PARTIAL TRAY
          $traySTSSQL = "update four.sys_inventoryLocations set hprtraystatus = 'PARTIALCOMPLETE', hprtraystatusby = :usr, hprtrayheldwithin = :heldwith, hprtrayheldwithinnote = :heldwithinnote, hprtrayreasonnotcomplete = :reasonnotcomplete, hprtrayreasonnotcompletenote = :reasonnotcompletenote  where scancode = :scncode";
          $traySTSRS = $conn->prepare($traySTSSQL);
          $traySTSRS->execute(array(':usr' => $u['usr']
                                  , ':scncode' => $pdta['rtnTrayId'] 
                                  , ':heldwith' => $pdta['locationscancode']
                                  , ':heldwithinnote' => trim($pdta['returnlocationnote'])
                                  , ':reasonnotcomplete' => $pdta['notfinishedreason']
                                  , ':reasonnotcompletenote' => trim($pdta['notfinishednote']))); 

          $tryHisSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, tray, traystatus, historyon, historyby, trayheldwithin, trayheldwithinnote, reasonnotfinished, reasonnotfinishednote) values(:trayscancode, :tray, :traystatus, now(), :historyby, :trayheldwithin, :trayheldwithinnote , :reasonnotfinished, :reasonnotfinishednote  )";
          $tryHisRS = $conn->prepare($tryHisSQL);
          $tryHisRS->execute(array(':trayscancode' => $pdta['rtnTrayId']
                                 , ':tray' => $pdta['rtnTrayId']
                                 , ':traystatus' => 'PARTIALCOMPLETE'
                                 , ':historyby' => $u['usr']
                                 , ':trayheldwithin' =>  $pdta['locationscancode']
                                 , ':trayheldwithinnote' => trim($pdta['returnlocationnote'])
                                 , ':reasonnotfinished' => $pdta['notfinishedreason']
                                 , ':reasonnotfinishednote' => trim($pdta['notfinishednote'])
                             ));
          $responseCode = 200;
        }
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rptlist);
      return $rows;
    }

    function hprsaveinconreview ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();
      //DO DATA CHECKS

      ( !array_key_exists('onbehalf', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'onbehalf' is missing.  Fatal Error")) : "";
      ( !array_key_exists('segid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'segid' is missing.  Fatal Error")) : "";
      ( !array_key_exists('inconreason', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'inconreason' is missing.  Fatal Error")) : "";
      ( !array_key_exists('inconfurtheractions', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'inconfurtheractions' is missing.  Fatal Error")) : "";
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'dialogid' is missing.  Fatal Error")) : "";

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowHPR = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
        if ( (int)$u['allowhprreview'] === 0 ) {
          if ( (int)$pdta['onbehalf'] === 0 ) {   
            (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER IS NOT A LISTED REVIEWER FOR HPR/QMS SO THE 'PROXY FOR' FIELD IS REQUIRED."));
          } else { 
            $chkReviewerSQL = "SELECT displayname FROM four.sys_userbase where userid = :uid and allowhprreview = 1";
            $chkReviewerRS = $conn->prepare($chkReviewerSQL);
            $chkReviewerRS->execute(array(':uid' => (int)$pdta['onbehalf'] ));
            
            if ( $chkReviewerRS->rowCount() === 0 ) { 
                (list( $errorInd, $msgArr[] ) = array(1 , "PROXIED REVIEWER IS NOT VALID.  TRY AGAIN"));
            } else { 
                $rvr = $chkReviewerRS->fetch(PDO::FETCH_ASSOC);
                $reviewer = "{$rvr['displayname']}";
            }
          }
        } else { 
          $reviewer = $u['usr'];
        } 
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }

      $segChkSQL = "SELECT sg.biosamplelabel, sg.bgs, ifnull(sg.hprslideread,0) as hprslideread, replace(read_label,'_','') as readlabel FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where sg.segmentid = :segid";
      $segChkRS = $conn->prepare($segChkSQL);
      $segChkRS->execute(array(':segid' => (int)$pdta['segid'] ));
      ( $segChkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SEGMENT SPECIFIED IS INVLAID.  FATAL ERROR!")) : $bg = $segChkRS->fetch(PDO::FETCH_ASSOC);

      if ( trim($pdta['inconfurtheractions']) !== "" ) { 
        $fa = json_decode( $pdta['inconfurtheractions'] , true);
        $reviewassignind = 0;
        foreach ( $fa as $fkey => $fval ) {
          $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRFURTHERACTION' and menuvalue = :suppliedvalue";
          $chkRS = $conn->prepare($chkSQL);
          $chkRS->execute(array(':suppliedvalue' => $fval[0] ));
          if ( $fval[0] === 'REVIEWREASSIGN') { 
              $reviewassignind = 1;
          }           
          ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$fval[0]}' DOES NOT APPEAR AS A VALUE ON THE HPR-FURTHER-ACTION MENU TREE. SEE CHTNEastern Informatics!")) : "";
        }
      } else { 
          (list( $errorInd, $msgArr[] ) = array(1 , "'AT LEAST ONE FURTHER ACTION MUST BE STATED ON THE 'FURTHER ACTION' BOX."));
      }

      if ( trim($pdta['inconreason']) === "" ) {
        (list( $errorInd, $msgArr[] ) = array(1 , "'REASON INCONCLUSIVE' IS REQUIRED. STATE THE REASON THIS BIOSAMPLE CANNOT BE GIVEN A DIAGNOSIS DESIGNATION"));
      }

      if ( $errorInd === 0 ) {
         $decision = 'INCONCLUSIVE';
         //TODO:  IN THE FAR FUTURE MAKE THIS TRANSACTIONAL - THAT WOULD BE COOL 
         //INSERT HEAD  
         $insHPRHeadSQL = "insert into masterrecord.ut_hpr_biosample (bgs, biogroupid,  bgreference,  reviewer, reviewedon, inputby,  decision, reviewassignind,  inconclusivetxt) values (:bgs, :biogroupid, :bgreference, :reviewer, now(), :inputby, :decision, :reviewassignind,  :inconclusivetxt )";
         $insHPRHeadRS = $conn->prepare($insHPRHeadSQL); 
         $insHPRHeadRS->execute(array(':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':biogroupid' => $bg['biosamplelabel'], ':bgreference' => $bg['biosamplelabel'], ':reviewer' => $reviewer, ':inputby' => $u['usr'], ':decision' => $decision, ':reviewassignind' => $reviewassignind, ':inconclusivetxt' => trim($pdta['inconreason'])));
         $hprheadid = $conn->lastInsertId();

         ////INSERT FURTHER ACTIONS

         if ( trim($pdta['inconfurtheractions']) !== "" ) { 
           $faInsSQL = "insert into masterrecord.ut_hpr_factions (biohpr, actiontypevalue, actiontype, actionnote, actionindicator, actionrequestedon) values (:biohpr, :actiontypevalue, :actiontype, :actionnote, 1, now())";
           $faInsRS = $conn->prepare($faInsSQL);
           
           //$masterfaSQL = "INSERT INTO masterrecord.ut_master_furtherlabactions (frommodule,objhprid,objpbiosample,objbgs,actioncode,actiondesc,actionnote,actionrequestedby,actionrequestedon) VALUES ('HPR',:hprid,:biosampleref,:bgs,:actioncode,:actiondesc,:actionnote,:rqstby,now())";
           //$masterfaRS = $conn->prepare($masterfaSQL);

           $fa = json_decode( $pdta['inconfurtheractions'] , true);
           foreach ( $fa as $fkey => $fval ) {
             $faInsRS->execute(array( ':biohpr' => $hprheadid, ':actiontypevalue' => $fval[0], ':actiontype' => $fval[1], ':actionnote' => trim($fval[2])));
             //$masterfaRS->execute(array(':hprid' => $hprheadid, ':biosampleref' => $bg['biosamplelabel'], ':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':actioncode' => $fval[0], ':actiondesc' => $fval[1], ':actionnote' => trim($fval[2]), ':rqstby' => $reviewer));
           } 
         }

         //BACKUP BIOGROUP
         $buSQL = "insert into masterrecord.history_procure_biosample_qms (pbiosample, readlabel, qcvalv2, hprindicator, hprmarkbyon, qcindicator, qcmarkbyon, qcprocstatus, labaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresultid, slidereviewed, hpron, hprby, historyrecordon, historyrecordby)  SELECT pbiosample, read_label, qcvalv2, hprind, hprmarkbyon, qcind, qcmarkbyon, qcprocstatus, labactionaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresult, hprslidereviewed, hpron, hprby, now() as historyrecordon, 'HPR-REVIEW-MODULE' as  historyrecordby FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
         $buRS = $conn->prepare($buSQL); 
         $buRS->execute(array(':pbiosample' => $bg['biosamplelabel']));

         //WRITE BIOGROUP
         $updBSSQL = "update masterrecord.ut_procure_biosample set hprind = 1,hprmarkbyon = concat(:reviewerdsp,'::', now()),QCProcStatus = 'H',hprdecision = :decision, hprresult = :hprid, hprslidereviewed = :bgs, hprby = :reviewer, hpron = now() where pbiosample = :pbiosample";
         $updBSRS = $conn->prepare($updBSSQL);
         $updBSRS->execute(array(':reviewerdsp' => $reviewer, ':decision' => strtoupper($decision), ':hprid' => $hprheadid, ':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':reviewer' => $reviewer, ':pbiosample' => $bg['biosamplelabel']));

         //UPDATE BIOSAMPLE SEGMENT SLIDE VIEWED
         $updSReadSQL = "update masterrecord.ut_procure_segment set hprslideread = 1 where replace(bgs,'_','') = :slidenbr";
         $updSReadRS = $conn->prepare($updSReadSQL);
         $updSReadRS->execute(array(':slidenbr' => strtoupper(preg_replace('/_/','',$bg['bgs']))));

         $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rptlist);
      return $rows;
    }

    function hprsavereview ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id();
      //DO DATA CHECKS
      //MAKE SURE ALL ARRAY KEYS EXIST
      ( !array_key_exists('onbehalf', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'onbehalf' is missing.  Fatal Error")) : "";
      ( !array_key_exists('segid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'segid' is missing.  Fatal Error")) : "";
      ( !array_key_exists('decision', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'decision' is missing.  Fatal Error")) : "";
      ( !array_key_exists('specimencategory', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'specimencategory' is missing.  Fatal Error")) : "";
      ( !array_key_exists('site', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'site' is missing.  Fatal Error")) : "";
      ( !array_key_exists('ssite', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'ssite' is missing.  Fatal Error")) : "";
      ( !array_key_exists('diagnosis', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'diagnosis' is missing.  Fatal Error")) : "";
      ( !array_key_exists('diagnosismodifier', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'diagnosismodifier' is missing.  Fatal Error")) : "";
      ( !array_key_exists('metsfrom', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'metsfrom' is missing.  Fatal Error")) : "";
      ( !array_key_exists('systemic', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'systemic' is missing.  Fatal Error")) : "";
      ( !array_key_exists('uninvolved', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'uninvolved' is missing.  Fatal Error")) : "";
      ( !array_key_exists('tumorgrade', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'tumorgrade' is missing.  Fatal Error")) : "";
      ( !array_key_exists('tumorgradescale', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'tumorgradescale' is missing.  Fatal Error")) : "";
      ( !array_key_exists('techaccuracy', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'techaccuracy' is missing.  Fatal Error")) : "";
      ( !array_key_exists('complexion', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'complexion' is missing.  Fatal Error")) : "";
//      ( !array_key_exists('hprfurtheraction', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'hprfurtheraction' is missing.  Fatal Error")) : "";
      ( !array_key_exists('hprmoleculartests', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'hprmoleculartests' is missing.  Fatal Error")) : "";
      ( !array_key_exists('rarereason', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'rarereason' is missing.  Fatal Error")) : "";
      ( !array_key_exists('specialinstructions', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'specialinstructions' is missing.  Fatal Error")) : "";
      ( !array_key_exists('generalcomments', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'generalcomments' is missing.  Fatal Error")) : "";
      ( !array_key_exists('reviewassignind' , $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array key 'reviewassignind' is missing.  Fatal Error")) : "";

      //CHECK USER IS AN HPR REVIEWER
      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr, ifnull(allowHPRReview,0) as allowhprreview FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 and allowHPR = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
        if ( (int)$u['allowhprreview'] === 0 ) {
          if ( (int)$pdta['onbehalf'] === 0 ) {   
            (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER IS NOT A LISTED REVIEWER FOR HPR/QMS SO THE 'PROXY FOR' FIELD IS REQUIRED."));
          } else { 
            $chkReviewerSQL = "SELECT displayname FROM four.sys_userbase where userid = :uid and allowhprreview = 1";
            $chkReviewerRS = $conn->prepare($chkReviewerSQL);
            $chkReviewerRS->execute(array(':uid' => (int)$pdta['onbehalf'] ));
            
            if ( $chkReviewerRS->rowCount() === 0 ) { 
                (list( $errorInd, $msgArr[] ) = array(1 , "PROXIED REVIEWER IS NOT VALID.  TRY AGAIN"));
            } else { 
                $rvr = $chkReviewerRS->fetch(PDO::FETCH_ASSOC);
                $reviewer = "{$rvr['displayname']}";
            }
          }
        } else { 
          $reviewer = $u['usr'];
        } 
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
      //CHECK SEGID EXISTS AND IS VALID
      $segChkSQL = "SELECT sg.biosamplelabel, sg.bgs, ifnull(sg.hprslideread,0) as hprslideread, replace(read_label,'_','') as readlabel FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where sg.segmentid = :segid";
      $segChkRS = $conn->prepare($segChkSQL);
      $segChkRS->execute(array(':segid' => (int)$pdta['segid'] ));
      ( $segChkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SEGMENT SPECIFIED IS INVLAID.  FATAL ERROR!")) : $bg = $segChkRS->fetch(PDO::FETCH_ASSOC);

      //TODO:   TURN THIS INTO A FUNCTION - ITS A REPEATING ALGORITHM
      $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRDECISION' and menuvalue = :suppliedvalue";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':suppliedvalue' => $pdta['decision']));
      ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$pdta['decision']}' IS NOT A VALID VALUE ON THE HPR-DECISION MENU TREE")) : "";

      $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'UNINVOLVEDIND' and menuvalue = :suppliedvalue";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':suppliedvalue' => $pdta['uninvolved']));
      ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE UNINVOLVED VALUE WAS NOT FOUND ON THE HPR-DECISION MENU TREE")) : "";

      if ( trim( $pdta['tumorgradescale'] ) !== "" ) {
        $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRTUMORSCALE' and menuvalue = :suppliedvalue";
        $chkRS = $conn->prepare($chkSQL);
        $chkRS->execute(array(':suppliedvalue' => $pdta['tumorgradescale']));
        ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE TUMOR GRADE VALUE WAS NOT FOUND ON THE HPR-DECISION MENU TREE")) : "";
      }
      
      $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRTECHACCURACY' and menuvalue = :suppliedvalue";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':suppliedvalue' => $pdta['techaccuracy'] ));
      ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE TECHNICIAN ACCURACY VALUE WAS NOT FOUND ON THE HPR-DECISION MENU TREE")) : "";

      foreach ( $pdta['complexion'] as $ckey => $cval ) {
        $mvalue = preg_replace('/^prc_/','',$ckey);
        if ( trim($cval) !== "" ) {
          $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRPERCENTAGE' and menuvalue = :suppliedvalue";
          $chkRS = $conn->prepare($chkSQL);
          $chkRS->execute(array(':suppliedvalue' => strtoupper($mvalue)));
          ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$mvalue}' DOES NOT APPEAR AS A VALUE ON THE HPR-DECISION MENU TREE. SEE CHTNEastern Informatics!")) : "";
          ( !is_numeric($cval) )  ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$cval}' is Not a Numeric value ({$mvalue})")) : "";

        }
      }

  //    if ( trim($pdta['hprfurtheraction']) !== "" ) { 
  //      $fa = json_decode( $pdta['hprfurtheraction'] , true);
  //      //(list( $errorInd, $msgArr[] ) = array(1 , $fa[0][0] ));
  //      foreach ( $fa as $fkey => $fval ) {
  //        $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'HPRFURTHERACTION' and menuvalue = :suppliedvalue";
  //        $chkRS = $conn->prepare($chkSQL);
  //        $chkRS->execute(array(':suppliedvalue' => $fval[0]  ));
  //        ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$fval[0]}' DOES NOT APPEAR AS A VALUE ON THE HPR-FURTHER-ACTION MENU TREE. SEE CHTNEastern Informatics!")) : "";
  //      }
  //    }

      if ( trim($pdta['hprmoleculartests']) !== "" ) { 
        $mt = json_decode( $pdta['hprmoleculartests'], true); 
        foreach ( $mt as $mkey => $mval) {

            if ( trim($mval[0]) === "" ) { 
              (list( $errorInd, $msgArr[] ) = array(1 , "A Molecular Test Value must be specified for all entered molecular tests"));
            } else {   
              $chkSQL = "SELECT * FROM four.sys_master_menus where menu = 'MOLECULARTEST' and menuvalue = :suppliedvalue";
              $chkRS = $conn->prepare($chkSQL);
              $chkRS->execute(array(':suppliedvalue' => $mval[0] ));
              ( $chkRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "'{$mval[0]}' DOES NOT APPEAR AS A VALUE ON THE HPR-MOLECULAR MENU TREE. SEE CHTNEastern Informatics!")) : "";
            }

        }
      }

      $unusableInd = 0;
      if ( array_key_exists('unusabletxt', $pdta) ) { 
          if ( trim($pdta['unusabletxt']) === "" ) {
            (list( $errorInd, $msgArr[] ) = array(1 , "FOR UNUSABLE/NOT-FIT-FOR-PURPOSE BIOGROUPS, YOU MUST SUPPLY A REASON.")); 
          } else { 
             $unusableInd = 1;
          }
      }

      if ( $errorInd === 0 ) {
         //TODO:  IN THE FAR FUTURE MAKE THIS TRANSACTIONAL - THAT WOULD BE COOL 

         //INSERT HEAD  
          $insHPRHeadSQL = "insert into masterrecord.ut_hpr_biosample (bgs, biogroupid,  bgreference,  reviewer, reviewedon, inputby,  reviewassignind, decision, vocabularydecision, speccat,  site,  subsite,  dx,  subdiagnosis,  mets,  systemiccomobid,  tumorgrade,  tumorscale,  uninvolvedsample,  rarereason,  specialinstructions, generalComments,  technicianaccuracy, unusabletxt) values (:bgs, :biogroupid, :bgreference, :reviewer, now(), :inputby, :reviewassignind, :decision, :vocabularydecision, :speccat, :site, :subsite, :dx, :subdiagnosis, :mets, :systemiccomobid, :tumorgrade, :tumorscale, :uninvolvedsample, :rarereason, :specialinstructions, :generalcomments, :technicianaccuracy, :unusabletxt )";
          $insHPRHeadRS = $conn->prepare($insHPRHeadSQL); 
          
          $rr = ( $pdta['reviewassignind'] === 'true' ) ? 1 : 0;

         if ( $unusableInd === 0 ) {
           $insHPRHeadRS->execute(array(':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':biogroupid' => $bg['biosamplelabel'], ':bgreference' => $bg['biosamplelabel'], ':reviewer' => $reviewer, ':inputby' => $u['usr'], ':reviewassignind' => $rr, ':decision' => strtoupper($pdta['decision']), ':vocabularydecision' => strtoupper($pdta['decision']), ':speccat' => strtoupper(trim($pdta['specimencategory'])), ':site' => strtoupper(trim($pdta['site'])), ':subsite' => strtoupper(trim($pdta['ssite'])), ':dx' => strtoupper(trim($pdta['diagnosis'])), ':subdiagnosis' => strtoupper(trim($pdta['diagnosismodifier'])), ':mets' => strtoupper(trim($pdta['metsfrom'])), ':systemiccomobid' => strtoupper(trim($pdta['systemic'])),':tumorgrade' => strtoupper(trim($pdta['tumorgrade'])), ':tumorscale' => strtoupper(trim($pdta['tumorgradescale'])), ':uninvolvedsample' => strtoupper(trim($pdta['uninvolved'])), ':rarereason' => trim($pdta['rarereason']), ':specialinstructions' => trim($pdta['specialinstructions']), ':generalcomments' => trim($pdta['generalcomments']), ':technicianaccuracy' => trim($pdta['techaccuracy']),':unusabletxt' => '' ));
         } else { 
           $insHPRHeadRS->execute(array(':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':biogroupid' => $bg['biosamplelabel'], ':bgreference' => $bg['biosamplelabel'], ':reviewer' => $reviewer, ':inputby' => $u['usr'], ':decision' => 'UNUSABLE', ':reviewassignind' => $rr, ':vocabularydecision' => strtoupper($pdta['decision']), ':speccat' => strtoupper(trim($pdta['specimencategory'])), ':site' => strtoupper(trim($pdta['site'])), ':subsite' => strtoupper(trim($pdta['ssite'])), ':dx' => strtoupper(trim($pdta['diagnosis'])), ':subdiagnosis' => strtoupper(trim($pdta['diagnosismodifier'])), ':mets' => strtoupper(trim($pdta['metsfrom'])), ':systemiccomobid' => strtoupper(trim($pdta['systemic'])),':tumorgrade' => strtoupper(trim($pdta['tumorgrade'])), ':tumorscale' => strtoupper(trim($pdta['tumorgradescale'])), ':uninvolvedsample' => strtoupper(trim($pdta['uninvolved'])), ':rarereason' => trim($pdta['rarereason']), ':specialinstructions' => trim($pdta['specialinstructions']), ':generalcomments' => trim($pdta['generalcomments']), ':technicianaccuracy' => trim($pdta['techaccuracy']), ':unusabletxt' => trim($pdta['unusabletxt'])  ));
         }
         $hprheadid = $conn->lastInsertId();

         ////INSERT FURTHER ACTIONS
//         if ( trim($pdta['hprfurtheraction']) !== "" ) { 
//           $faInsSQL = "insert into masterrecord.ut_hpr_factions (biohpr, actiontypevalue, actiontype, actionnote, actionindicator, actionrequestedon) values (:biohpr, :actiontypevalue, :actiontype, :actionnote, 1, now())";
//           $faInsRS = $conn->prepare($faInsSQL);
//           $masterfaSQL = "INSERT INTO masterrecord.ut_master_furtherlabactions (frommodule,objhprid,objpbiosample,objbgs,actioncode,actiondesc,actionnote,actionrequestedby,actionrequestedon) VALUES ('HPR',:hprid,:biosampleref,:bgs,:actioncode,:actiondesc,:actionnote,:rqstby,now())";
//           $masterfaRS = $conn->prepare($masterfaSQL);
//
//          $fa = json_decode( $pdta['hprfurtheraction'] , true);
//          foreach ( $fa as $fkey => $fval ) {
//            $faInsRS->execute(array( ':biohpr' => $hprheadid, ':actiontypevalue' => $fval[0], ':actiontype' => $fval[1], ':actionnote' => trim($fval[2])));
//            $masterfaRS->execute(array(':hprid' => $hprheadid, ':biosampleref' => $bg['biosamplelabel'], ':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':actioncode' => $fval[0], ':actiondesc' => $fval[1], ':actionnote' => trim($fval[2]), ':rqstby' => $reviewer));
//          } 
//         }
         //INSERT PERCENTAGES
         $insPrcSQL = "insert into masterrecord.ut_hpr_percentages ( biohpr, prcTypeValue, prcType, prcValue, inputon) values(:biohpr, :prcTypeValue, :prcType, :prcValue, now())";
         $insPrcRS = $conn->prepare($insPrcSQL);
         foreach ( $pdta['complexion'] as $ckey => $cval ) {
           $mvalue = preg_replace('/^prc_/','',$ckey);
           if ( trim($cval) !== "" ) {
             $insPrcRS->execute(array(':biohpr' => $hprheadid, ':prcTypeValue' => strtoupper($mvalue), ':prcType' => '', ':prcValue' => $cval));
           }
         }
         //INSERT MOLECULAR
         $insMoleSQL = "insert into masterrecord.ut_hpr_moleculartests (biohpr, moletestvalue, moletest, resultindexvalue, resultindex, resultdegree, inputon) value (:biohpr, :moletestvalue, :moletest, :resultindexvalue, :resultindex, :resultdegree, now())";
         $insMoleRS = $conn->prepare($insMoleSQL);
         if ( trim($pdta['hprmoleculartests']) !== "" ) { 
           $mt = json_decode( $pdta['hprmoleculartests'], true); 
           foreach ( $mt as $mkey => $mval) { 
            $insMoleRS->execute(array(':biohpr' => $hprheadid, ':moletestvalue' => trim($mval[0]), ':moletest' => trim($mval[1]), ':resultindexvalue' => trim($mval[2]), ':resultindex' => trim($mval[3]), ':resultdegree' => trim($mval[4])));
          }
         }
         //WRITE BIOSAMPLE MOLECULAR - LET QA DO THIS !!!!! COMMENTED 7/22/2019 ZACK
         //$updSQL = "update masterrecord.ut_procure_biosample_molecular set dspind = 0, updatedby = 'HPR-REVIEW-MODULE', updatedon = now() where bgprcnbr = :readlabel"; 
         //$updRS = $conn->prepare($updSQL); 
         //$updRS->execute(array(':readlabel' => $bg['readlabel']));
         //$moleInsSQL = "insert into masterrecord.ut_procure_biosample_molecular (pbiosample, bgprcnbr, testid, testresultid, molenote, onwhen, onby, dspind) values(:pbiosample, :bgprcnbr, :testid, :testresultid, :molenote, now(), :onby, 1)";
         //$moleInsRS = $conn->prepare($moleInsSQL); 
         //if ( trim($pdta['hprmoleculartests']) !== "" ) { 
         //  $mt = json_decode( $pdta['hprmoleculartests'], true); 
         //  foreach ( $mt as $mkey => $mval) { 
         //   $moleInsRS->execute(array(':pbiosample' => $bg['biosamplelabel'], ':bgprcnbr' => $bg['readlabel'], ':testid' => trim($mval[0]), ':testresultid' => trim($mval[2]), ':molenote' => trim($mval[4]), ':onby' => $reviewer ));
         //}
         //}
         
         //WRITE BIOSAMPLE COMPOSITION (PERCENTAGES) - LET QA DO THIS!!!!! COMMENTED 7/22/2019 ZACK
         //$updSQL = "update masterrecord.ut_procure_biosample_samplecomposition set dspind = 0, updateon = now(), updateby = 'HPR-REVIEW-SCREEN' where readlabel = :readlabel"; 
         //$updRS = $conn->prepare($updSQL); 
         //$updRS->execute(array(':readlabel' => $bg['readlabel']));
         //$insPrcSQL = "insert into masterrecord.ut_procure_biosample_samplecomposition (readlabel, prctype, prcvalue, dspind, inputon, inputby) values (:readlabel, :prctype, :prcvalue, 1, now(), :inputby)";
         //$insPrcRS = $conn->prepare($insPrcSQL);
         //foreach ( $pdta['complexion'] as $ckey => $cval ) {
         //  $mvalue = preg_replace('/^prc_/','',$ckey);
         //  if ( trim($cval) !== "" ) {
         //    $insPrcRS->execute(array(':readlabel' => $bg['readlabel'], ':prctype' => strtoupper($mvalue), ':prcvalue' => $cval, ':inputby' => $reviewer));
         //  }
         //}
         //UPDATE BIOSAMPLE WITH DECISION (AFTER BACKING UP TABLE) 
         $buSQL = "insert into masterrecord.history_procure_biosample_qms (pbiosample, readlabel, qcvalv2, hprindicator, hprmarkbyon, qcindicator, qcmarkbyon, qcprocstatus, labaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresultid, slidereviewed, hpron, hprby, historyrecordon, historyrecordby)  SELECT pbiosample, read_label, qcvalv2, hprind, hprmarkbyon, qcind, qcmarkbyon, qcprocstatus, labactionaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresult, hprslidereviewed, hpron, hprby, now() as historyrecordon, 'HPR-REVIEW-MODULE' as  historyrecordby FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
         $buRS = $conn->prepare($buSQL); 
         $buRS->execute(array(':pbiosample' => $bg['biosamplelabel']));


         $updBSSQL = "update masterrecord.ut_procure_biosample set hprind = 1,hprmarkbyon = concat(:reviewerdsp,'::', now()),QCProcStatus = 'H',hprdecision = :decision, hprresult = :hprid, hprslidereviewed = :bgs, hprby = :reviewer, hpron = now() where pbiosample = :pbiosample";
         $updBSRS = $conn->prepare($updBSSQL);

         if ( $unusableInd === 0 ) {
           $updBSRS->execute(array(':reviewerdsp' => $reviewer, ':decision' => strtoupper($pdta['decision']), ':hprid' => $hprheadid, ':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':reviewer' => $reviewer, ':pbiosample' => $bg['biosamplelabel']));
         } else { 
           $updBSRS->execute(array(':reviewerdsp' => $reviewer, ':decision' => 'UNUSABLE' , ':hprid' => $hprheadid, ':bgs' => strtoupper(preg_replace('/_/','',$bg['bgs'])), ':reviewer' => $reviewer, ':pbiosample' => $bg['biosamplelabel']));
         }

         //UPDATE BIOSAMPLE SEGMENT SLIDE VIEWED
         $updSReadSQL = "update masterrecord.ut_procure_segment set hprslideread = 1 where replace(bgs,'_','') = :slidenbr";
         $updSReadRS = $conn->prepare($updSReadSQL);
         $updSReadRS->execute(array(':slidenbr' => strtoupper(preg_replace('/_/','',$bg['bgs']))));

         $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rptlist);
      return $rows;
    }

    function markreportfavorite( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start(); 
      $sessid = session_id(); 

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as usr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }


      ( !array_key_exists('reporturl', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'reporturl' is missing.  Fatal Error")) : "";
      ( trim($pdta['reporturl']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A Report URL must be passed")) : "";

      if ( $errorInd === 0 ) { 
  
        $getRptSQL = "SELECT reportid FROM four.ut_reportlist where urlpath = :rpturl"; 
        $getRptRS = $conn->prepare($getRptSQL); 
        $getRptRS->execute(array(':rpturl' => trim($pdta['reporturl'])));
        //TODO: CATCH THE ERROR WHERE NO REPORT IS FOUND - ASSUMING THAT THERE IS ALWAYS A MATCHING URL PATH
        $rptIdRS = $getRptRS->fetch(PDO::FETCH_ASSOC); 
        $rptId = $rptIdRS['reportid'];

        $usrChkSQL = "SELECT rtorid, dspind FROM four.ut_reportgroup_to_reportlist rtr where reportid = :rptid and userfav = :usr";
        $usrChkRS = $conn->prepare($usrChkSQL); 
        $usrChkRS->execute(array(':rptid' => $rptId, ':usr' => $u['usr'])); 
        if ( $usrChkRS->rowCount() < 1 ) { 
            //ADD FAVORITE
            $countSQL = "select count(1) cnt from  four.ut_reportgroup_to_reportlist where userfav = :userfav"; 
            $countRS = $conn->prepare($countSQL); 
            $countRS->execute(array(':userfav' => $u['usr']));
            $cntR = $countRS->fetch(PDO::FETCH_ASSOC);
            $cnt = ( $cntR['cnt'] + 1);
            $thisSQL = "insert into four.ut_reportgroup_to_reportlist ( reportid, dspind, dsporder, userfav ) values(:reportid, 1, :cntr, :userfav)";
            $thisRS = $conn->prepare($thisSQL); 
            $thisRS->execute(array(':reportid' => $rptId, ':userfav' => $u['usr'], ':cntr' => $cnt ));
        } else { 
            $rd = $usrChkRS->fetch(PDO::FETCH_ASSOC);
            if ( (int)$rd['dspind'] === 1 ) { 
            //  //TURN OFF FAVORITE
              $thisSQL = "update four.ut_reportgroup_to_reportlist set dspind = 0 where reportid = :rptid and userfav = :user";
            } else { 
              //Turn On
              $thisSQL = "update four.ut_reportgroup_to_reportlist set dspind = 1 where reportid = :rptid and userfav = :user";
            }
            $thisRS = $conn->prepare($thisSQL);
            $thisRS->execute(array( ':rptid' => $rptId, ':user' => $u['usr'] ));
        }
        $responseCode = 200;

      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rptlist);
      return $rows;
    }

    function userreportlisting ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $usr = explode("::" , cryptservice( $pdta['user'] , 'd'));

      $chkUsrSQL = "SELECT friendlyname, originalaccountname as user, accessnbr  FROM four.sys_userbase where 1=1 and originalaccountname = :givenuser  and sessionid = :sessid and allowInd = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 "; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':givenuser' => $usr[0], ':sessid' => $usr[1]));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER. {$usr[0]}"));
      }

      if ( $errorInd === 0 ) {  
        $friendly = $u['friendlyname'] . " " . $u['accessnbr'];
        $rptSQL = <<<SQLSTMT
select * from (
(SELECT 0 as dsporder, rtr.rtorid,   0 as groupid, 'MY-FAVORITE-REPORTS' as groupingname, 'myfavoritereports' as groupurl, 'My Favorite Report Listing' as groupdescriptions, rl.urlpath, rl.reportname, rl.reportdescription, rl.accesslvl
FROM four.ut_reportgroup_to_reportlist rtr 
left join four.ut_reportlist rl on rtr.reportid = rl.reportid
where rtr.dspind = 1 and rl.dspind = 1 and rtr.userfav = :usr and rl.accesslvl <= :accesslvlusr
order by rtorid desc) 
UNION
(SELECT rg.orderind, rtr.dsporder,     rg.groupid, rg.groupingname, rg.groupingurl, rg.groupingdescriptions, rl.urlpath, rl.reportname, rl.reportdescription, rl.accesslvl 
FROM four.ut_reportgrouping rg left join four.ut_reportgroup_to_reportlist rtr on rg.groupid = rtr.reportgroupid left join four.ut_reportlist rl on rtr.reportid = rl.reportid 
where rg.dspind = 1 and rtr.dspind = 1 and rl.dspind = 1 and rl.accesslvl <= :accesslvl  
order by orderind, rtorid asc)) unn
order by unn.dsporder, unn.rtorid
SQLSTMT;
        $rptRS = $conn->prepare($rptSQL); 
        $rptRS->execute(array(':usr' => $u['user'], ':accesslvlusr' => $u['accessnbr'], ':accesslvl' => $u['accessnbr']));
        $rptgurl = "";
        $rptlist = array();
        while ( $r = $rptRS->fetch(PDO::FETCH_ASSOC) ) { 
            if ( $rptgurl !== $r['groupurl'] ) { 
              $rptlist[ $r['groupurl'] ]['name'] = $r['groupingname'];
              $rptlist[ $r['groupurl'] ]['description'] = $r['groupdescriptions'];
              $rptgurl = $r['groupurl'];
            }
            $rptlist[ $r['groupurl'] ]['list'][] = array( 'reporturl' => $r['urlpath'], 'reportname' => $r['reportname'], 'description' => $r['reportdescription'] ) ; 
        }
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $rptlist);
      return $rows;
    }

    function hprvocabbrowserdxoverride ( $request, $passdata ) { 
      //SEE function searchvocabularyterms and searchVocabByTerm
      $responseCode = 400; 
      $errorInd = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      require(serverkeys . "/sspdo.zck");

      ( !array_key_exists('srchterm', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'srchterm' is missing.  Fatal Error")) : "";
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'dialogid' is missing.  Fatal Error")) : "";
      ( trim($pdta['srchterm']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Search Term cannot be blank")) : "";
      ( trim($pdta['dialogid']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Dialog ID cannot be blank")) : "";

      if ( $errorInd === 0 ) { 
         $prepareSrchTerm = ('%' . preg_replace('/\s/','%', preg_replace('/\s{2,}/',' ', trim($pdta['srchterm']))) . '%');
         $lookupSQL = "SELECT distinct diagnosis FROM four.sys_master_menu_vocabulary where ifnull(diagnosis,'') <> ''  and ifnull(diagnosis,'') like :srchterm  order by diagnosis";
         $vocabRS = $conn->prepare($lookupSQL); 
         $vocabRS->execute(array(':srchterm' => $prepareSrchTerm));
         $itemsfound = $vocabRS->rowCount(); 
         if ( $itemsfound > 0 ) { 
           //BUILD TABLE
             $dta = "<table><tr><td>Vocabulary Terms Found: {$itemsfound}</td></tr></table><table border=0 id=hprVocabResultTbl cellspacing=0 cellpadding=0><thead><tr><th>Diagnosis</th><th>Diagnosis Modifier</th></tr></thead></tbody>";
             while ($r = $vocabRS->fetch(PDO::FETCH_ASSOC)) {
               $dx = explode(" \ ",$r['diagnosis']);
               $dta .= "<tr ondblclick=\"makeHPRDXOverride('{$pdta['dialogid']}','{$dx[0]}','{$dx[1]}');\"><td>{$dx[0]}</td><td>{$dx[1]}</td></tr>";             
             }
             $dta .= "</tbody></table>";
         } else { 
           $dta = "<table border=0><tr><td style=\"font-size: 1.5vh; font-weight: bold; padding: 1vh 1vw;\">No vocabulary terms match your search request ('{$pdta['srchterm']}' '{$prepareSrchTerm}')</td></tr></table"; 
         }
         $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode;   
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function hprvocabbrowser ( $request, $passdata ) { 
      //SEE function searchvocabularyterms and searchVocabByTerm
      $responseCode = 400; 
      $errorInd = 0;
      $msg = "";
      $itemsfound = 0;
      $dta = array();
      $msgArr = array();
      $pdta = json_decode($passdata,true);
      require(serverkeys . "/sspdo.zck");

      ( !array_key_exists('srchterm', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'srchterm' is missing.  Fatal Error")) : "";
      ( !array_key_exists('includess', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'includess' is missing.  Fatal Error")) : "";
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'dialogid' is missing.  Fatal Error")) : "";
      ( trim($pdta['srchterm']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Search Term cannot be blank")) : "";
      ( trim($pdta['dialogid']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Dialog ID cannot be blank")) : "";
      //( is_bool($pdta['includess']) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Sub-Site Include Must Be Boolean")) : "";
      if ( $errorInd === 0 ) { 
         $prepareSrchTerm = ('%' . preg_replace('/\s/','%', preg_replace('/\s{2,}/',' ', trim($pdta['srchterm']))) . '%');
         if ( !$pdta['includess'] ) {
         $lookupSQL = "SELECT distinct ifnull(specimenCategory,'') as specimencategory, ifnull(site,'') as site, ifnull(diagnosis,'') as diagnosis FROM four.sys_master_menu_vocabulary where 1=1 and concat(ifnull(specimenCategory,''),'::',ifnull(site,''),'::', ifnull(Diagnosis,'')) like :searchterm and trim(ifnull(specimencategory,'')) <> '' order by specimencategory, site, diagnosis";
         } else { 
         $lookupSQL = "SELECT distinct ifnull(specimenCategory,'') as specimencategory, ifnull(site,'') as site, if ( ifnull(subsite,'') = 'NONE','', ifnull(subsite,''))    as subsite,  ifnull(diagnosis,'') as diagnosis FROM four.sys_master_menu_vocabulary where 1=1 and concat(ifnull(specimenCategory,''),'::',ifnull(site,''),'::', ifnull(Diagnosis,'')) like :searchterm and trim(ifnull(specimencategory,'')) <> '' order by specimencategory, site, diagnosis";
         }
         $vocabRS = $conn->prepare($lookupSQL); 
         $vocabRS->execute(array(':searchterm' => $prepareSrchTerm));
         $itemsfound = $vocabRS->rowCount(); 
         if ( $itemsfound > 0 ) { 
             //BUILD TABLE
           $dta = "<table><tr><td>Vocabulary Terms Found: {$itemsfound} </td></tr></table>";
           if ( !$pdta['includess'] ) { 
             $dta .= "<table border=0 id=hprVocabResultTbl cellspacing=0 cellpadding=0><thead><tr><th>Specimen Category</th><th>Site</th><th>Diagnosis</th><th>Diagnosis Modifier</th></tr></thead><tbody>";
             while ($r = $vocabRS->fetch(PDO::FETCH_ASSOC)) {
               $dx = explode(" \ ",$r['diagnosis']);
               $dta .= "<tr ondblclick=\"makeHPRDesignation('{$pdta['dialogid']}','{$r['specimencategory']}','{$r['site']}','','{$dx[0]}','{$dx[1]}');\"><td>{$r['specimencategory']}</td><td>{$r['site']}</td><td>{$dx[0]}</td><td>{$dx[1]}</td></tr>";             
             }
           } else {
             $dta .= "<table border=0 id=hprVocabResultTbl><thead><tr><th>Specimen Category</th><th>Site</th><th>Sub-Site</th><th>Diagnosis</th><th>Diagnosis Modifier</th></tr></thead></tbody>";
             while ($r = $vocabRS->fetch(PDO::FETCH_ASSOC)) {
               $dx = explode(" \ ",$r['diagnosis']);
               $dta .= "<tr ondblclick=\"makeHPRDesignation('{$pdta['dialogid']}','{$r['specimencategory']}','{$r['site']}','{$r['subsite']}','{$dx[0]}','{$dx[1]}');\"><td>{$r['specimencategory']}</td><td>{$r['site']}</td><td>{$r['subsite']}</td><td>{$dx[0]}</td><td>{$dx[1]}</td></tr>";             
             }
           }
           $dta .= "</tbody></table>";
         } else { 
           $dta = "<table border=0><tr><td style=\"font-size: 1.5vh; font-weight: bold; padding: 1vh 1vw;\">No vocabulary terms match your search request ('{$pdta['srchterm']}' '{$prepareSrchTerm}')</td></tr></table"; 
         }
         $responseCode = 200;
      }
      $msg = $msgArr;
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
      $srchTrm = preg_replace("/[^[:alnum:]]/iu", '',$pdta['srchTrm']);

      $sidePanelSQL = "SELECT  if(ifnull(sg.hprslideread,'')='','N',if (sg.hprslideread = 0, 'N', 'Y')) as hprslideread , bs.pbiosample, replace(sg.bgs,'_','') as bgs, sg.biosamplelabel, sg.segmentid, ifnull(sg.prepmethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation, date_format(sg.procurementdate,'%m/%d/%Y') as procurementdate, sg.enteredby as procuringtech, ucase(ifnull(sg.procuredAt,'')) as procuredat, ifnull(inst.dspvalue,'') as institutionname, ucase(concat(concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'')='','',concat('/',ifnull(bs.subsite,'')))), ' ', concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat('/',ifnull(bs.subdiagnos,'')))), ' ' ,if(trim(ifnull(bs.tissType,'')) = '','',concat('(',trim(ifnull(bs.tissType,'')),')')))) as designation, ifnull(HPRDecision,'') as hprdecision, ifnull(HPRSlideReviewed,'') as hprslidereviewed, ifnull(HPRBy,'') as hprby, ifnull(date_format(hpron,'%m/%d/%Y'),'') as hpron   FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') inst on sg.procuredAt = inst.menuvalue where 1=1 ";
      $bldSidePanel = 0;
      $typeOfSearch = "";
      switch ($srchTrm) { 
        case (preg_match('/\b\d{1,3}\b/',$srchTrm) ? true : false) :
          $sidePanelSQL .= "and sg.hprboxnbr = :hprboxnbr";
          $qryArr = array(':hprboxnbr' => ('HPRT' . substr(('0000' . $srchTrm),-3)));
          $bldSidePanel = 1;
          $typeOfSearch = "HPR Inventory Tray " . substr(('0000' . $srchTrm),-3);
          $searchtype = "T"; 
          $tray = 'HPRT' . substr(('000' . $srchTrm),-3);
          break;
        //case (preg_match('/\b\d{5}\b/',$srchTrm) ? true : false) :
        //  $sidePanelSQL .= "and sg.prepMethod = :prpmet and sg.biosamplelabel  = :biogroup and sg.segstatus <> :segstatus"; 
        //  $qryArr = array(':biogroup' => (int)$srchTrm, ':prpmet' => 'SLIDE', ':segstatus' => 'SHIPPED');
        //  $bldSidePanel = 1;
        //  $typeOfSearch = "Slides in Biogroup " . $srchTrm;
        //  break;         
        case (preg_match('/\bED\d{5}.{1,}\b/i', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and concat('ED',replace(sg.bgs,'_','')) = :edbgs  and sg.prepMethod = :prpmet and sg.segstatus <> :segstatus"; 
          $qryArr = array(':edbgs' =>  str_replace('_','',strtoupper($srchTrm)) , ':prpmet' => 'SLIDE', ':segstatus' => 'SHIPPED');
          $bldSidePanel = 1;
          $typeOfSearch = "Slide Label Search for " .  $srchTrm;
          $searchtype = "S";    
          $tray = "";
          break;
        case (preg_match('/\b\d{5}[a-zA-Z]{1,}.{1,}\b/', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and replace(sg.bgs,'_','') = :bgs and sg.prepMethod = :prpmet and sg.segstatus <> :segstatus"; 
          $qryArr = array(':bgs' =>  str_replace('_','',strtoupper($srchTrm)), ':prpmet' => 'SLIDE', ':segstatus' => 'SHIPPED' );
          $bldSidePanel = 1;
          $typeOfSearch = "Slide Label Search for " . $srchTrm;
          $searchtype = "S";          
          $tray = "";
          break;
        case (preg_match('/\bHPRT\d{3}\b/i', $srchTrm) ? true : false) :  
          $sidePanelSQL .= "and sg.hprboxnbr = :hprboxnbr "; 
          $qryArr = array(':hprboxnbr' =>  $srchTrm);
          $bldSidePanel = 1;
          $typeOfSearch = "HPR Inventory Tray " . preg_replace('/HPRT/i','',$srchTrm);
          $searchtype = "T";     
          $tray = strtoupper($srchTrm);
          break;
        default:
         //DEFAULT 
      }
      $sidePanelSQL .= " order by 3 asc";
      
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
            $dta[$item]['hprslideread']      = $rs['hprslideread'];
            $dta[$item]['pbiosample']        = $rs['biosamplelabel'];
            $dta[$item]['prepmethod']        = $rs['prepmethod'];
            $dta[$item]['preparation']       = $rs['preparation'];
            $dta[$item]['segmentid']         = $rs['segmentid'];
            $dta[$item]['procurementdate']   = $rs['procurementdate'];
            $dta[$item]['procuringtech']     = $rs['procuringtech'];
            $dta[$item]['institution']       = $rs['procuredat'];
            $dta[$item]['institutionname']   = $rs['institutionname'];
            $dta[$item]['designation']       = $rs['designation'];
            $dta[$item]['recentdecision']       = $rs['hprdecision'];
            $dta[$item]['recentslideread']       = $rs['hprslidereviewed'];
            $dta[$item]['recentreviewby']       = $rs['hprby'];
            $dta[$item]['recentreviewon']       = $rs['hpron'];
            $dta[$item]['traysrch']              = $srchTrm;
            $dta[$item]['srchtype']              = $searchtype;
            $dta[$item]['tray'] = $tray;
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

    function hprsendemail( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start();
      $sessid = session_id();
      //{"recipientlist":"[\"recip65\",\"recip1\"]","messagetext":"message this","dialogid":"lJakKGiAMKh2eQy"}

      $chkUsrSQL = "SELECT friendlyname, emailaddress, profilephone, originalaccountname FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and allowHPR = 1"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }

      ( !array_key_exists('recipientlist', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'recipientlist' is missing.  Fatal Error")) : "";
      ( !array_key_exists('messagetext', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'messagetext' is missing.  Fatal Error")) : "";
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'dialogid' is missing.  Fatal Error")) : "";
      ( trim($pdta['messagetext']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MESSAGE CAN'T BE BLANK")) : "";
      ( trim($pdta['dialogid']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "DIALOG ID IS BLANK BUT IS REQUIRED")) : "";
      ( trim($pdta['recipientlist']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "RECIPIENTLIST IS BLANK - SELECT SOME RECIPIENTS")) : "";
      ( count(json_decode($pdta['recipientlist'])) < 1  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "RECIPIENT LIST IS BLANK ... SELECT SOME RECIPIENTS TO CONTINUE")) : "";

      if ( $errorInd === 0 ) {  
        //EMAIL LOOKUP
        $el = array();
        $emlSQL = "SELECT emailaddress FROM four.sys_userbase where concat('recip',userid) = :recip and allowind = 1 and allowHPRInquirer = 1";
        $emlRS = $conn->prepare( $emlSQL );
        foreach ( json_decode($pdta['recipientlist']) as $rkey => $rval ) { 
          $emlRS->execute(array(':recip' => $rval));
          if ( $emlRS->rowCount() > 0 ) { 
             $eml = $emlRS->fetch(PDO::FETCH_ASSOC); 
             $el[] = $eml['emailaddress'];
          } 
        }
        $sbjt = "Histopathologic Review Message [SCIENCESERVER v7]";
        $tmedsp = date('m/d/Y h:i A');
        $omsgtxt = $pdta['messagetext'];
        $htmlized = preg_replace('/\n\n/','<p>',$pdta['messagetext']);
        $htmlized = preg_replace('/\r\n/','<p>', $htmlized);
        $htmlized = preg_replace('/\r\r/','<p>', $htmlized);
        $htmlized = preg_replace('/\n/','<br>',$htmlized);
        $msgbody = <<<MBODY
<table border=1 cellpadding=0 cellspacing=0 style="font-family: arial; font-size: 12pt;"><tr><td style="padding: 20px;">This message is from the Histopathologic Review Module in ScienceServer v7.  It was sent by {$u['friendlyname']} at {$tmedsp}.  The message is displayed below. <p><center><b>DO NOT REPLY TO THIS MESSAGE BUT REPLY TO {$u['friendlyname']} AT {$u['emailaddress']} OR BY CALLING {$u['profilephone']}.</b></td></tr> 
<tr><td style="border: 1px solid #000; padding: 8px;">{$htmlized}</td></tr>
</table>
MBODY;
        $insSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgbody, htmlind, wheninput, bywho, sentind) value (:towhoaddressarray, :sbjtline, :msgbody, 1, now(), :bywho, 0)";
        $insRS = $conn->prepare($insSQL); 
        $insRS->execute(array(':towhoaddressarray' => json_encode($el), ':sbjtline' => $sbjt, ':msgbody' => $msgbody, ':bywho' => $u['originalaccountname']));
        $responseCode = 200;
        $dta = $pdta['dialogid']; //THIS WILL CLOSE THE DIALOG SCEEN
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }
   
    function getmontheventlist ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sessid = cryptservice( $pdta['sess'], 'd');       
      $chkUsrSQL = "SELECT originalaccountname as usr, presentinstitution as presentinstitution FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
          $u = $rs->fetch(PDO::FETCH_ASSOC);
          $dateLookupSQL = "SELECT ifnull(cal.eventid,'') as eventid, ifnull(cal.eyear,'1999') as eyear, ifnull(cal.emonth,'1') as emonth, ifnull(cal.eday,'1') as eday, ifnull(cal.alldayind,1) as allday, ifnull(cal.alldayind,1) as allday, if( ifnull(cal.alldayind,1) = 1, '',ifnull(cal.eventstarttime,'')) as stime, if( ifnull(cal.alldayind,1) = 1, 'All Day', concat( ifnull(cal.eventstarttime,''), if(ifnull(cal.eventendtime,'')='','',concat('&#45;',ifnull(cal.eventendtime,''))))) as eventendtime, ifnull(cal.eventtype,'RMDR') as eventtype, ifnull(evt.dspvalue,'General Staff Reminder') as dspeventtype, ifnull(evt.dspcolor,'') as dspeventcolor, if(ifnull(cal.eventtitle,'')='',concat(ifnull(cal.icdonorinitials,''), if(ifnull(cal.icsurgeon,'')='','',concat('::', ifnull(cal.icsurgeon,'')))),ifnull(cal.eventtitle,'')) as eventtitle, ifnull(cal.eventdesc,'') as eventdesc FROM four.sys_master_calendar cal left join (SELECT menuvalue, dspvalue, googleiconcode as dspcolor FROM four.sys_master_menus where menu = 'EVENTTYPE' and dspind = 1) as evt on cal.eventtype = evt.menuvalue  where dspind = :dspind and eyear = :yr  and emonth = :mn and ( dspForWhom = 'ALLINST' OR dspForWhom = :presinst OR lcase(dspForWhom) = :usr) order by cast(eday as unsigned), stime";
          $dateLookupRS = $conn->prepare($dateLookupSQL);
          $dateLookupRS->execute(array(':dspind' => 1,':yr' => (int)$pdta['year'],':mn' => (int)$pdta['month'],':presinst' => $u['presentinstitution'],':usr' => strtolower($u['usr'])));
          while ($r = $dateLookupRS->fetch(PDO::FETCH_ASSOC)) { 
            $dta[] = $r;
          }
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    } 

    function rootcalendareventdelete ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start();
      $sessid = session_id();
      //{"calEventDte":"18","calEventDialogId":"QBxy3znNsOlvcgP"}
      $chkUsrSQL = "SELECT originalaccountname as usr, presentinstitution as loc FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
      
      ( trim($pdta['calEventId']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE CALENDAR EVENT ID IS BLANK - SEE CHTNEASTERN INFORMATICS PERSONNEL")) : "" ;
      ( !is_numeric($pdta['calEventId']) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE CALENDAR EVENT ID IS NON NUMERIC - SEE CHTNEASTERN INFORMATICS PERSONNEL")) : "" ;
      
      $chkSQL ="SELECT eventid as eventFound FROM four.sys_master_calendar where inputby = :usr and  eventdate > date_sub(now(), interval 1 day) and dspind = :dsp and eventid = :evid";
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':usr' => $u['usr'], ':dsp' => 1, ':evid' => (int)$pdta['calEventId']  ));
      
      ( $chkRS->rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "NO EVENT EXISTS.  EITHER YOU DON'T HAVE ACCESS RIGHTS TO DELETE IT, IT IS AN EVENT IN THE PAST OR THE ID DOESN'T EXIST.  IF YOU BELEIVE THAT THIS IS IN ERROR, CONTACT A CHTNEASTERN INFORMATICS PERSON")) : "" ;
      
      
      if ( $errorInd === 0 ) {
     
          $updSQL = "update four.sys_master_calendar set dspind = :newdsp, modifiedon = now(), modifiedby = :logusr where inputby = :usr and  eventdate > date_sub(now(), interval 1 day) and dspind = :olddsp and eventid = :evid";
          $updRS = $conn->prepare($updSQL);
          $updRS->execute(array( 
              ':newdsp' => 0
            , ':logusr' => $u['usr']
            , ':usr' => $u['usr']
            , ':olddsp' => 1
            , ':evid' => (int)$pdta['calEventId']  
            ));    
          $dta['dialogid'] = $pdta['calEventDialogId'];
          $responseCode = 200;
      }
        
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;
    }

    function rootcalendareventsave ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start();
      $sessid = session_id();

      $chkUsrSQL = "SELECT originalaccountname, presentinstitution as usr FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
      //{{\"calEventDte\":\"2019-05-15\",\"calEventStartTime\":\"\",\"calEventEndTime\":\"\",\"calEventAllDayInd\":false,\"calEventType\":\"\",\"calEventPHIIni\":\"\",\"calEventSurgeon\":\"\",\"calEventTitle\":\"\",\"calEventDesc\":\"\",\"calEventForWho\":\"ALLINST\"}"}}
      //DATACHECKS
      ( trim($pdta['calEventDte']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE CALENDAR EVENT MUST BE SPECIFIED")) : "" ;
      ( trim($pdta['calEventDte']) !== "" &&  !verifyDate(trim($pdta['calEventDte']),'Y-m-d', true) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE DATE SPECIFIED FOR THE CALENDAR EVENT IS INVALID")) : "" ;
      ( $pdta['calEventAllDayInd'] && ( trim($pdta['calEventStartTime']) !== "" || trim($pdta['calEventEndTime']) !== "" )) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE EVENT CANNOT BE BOTH 'ALL DAY' AND HAVE A START AND/OR END TIME")) : "";
      ( !$pdta['calEventAllDayInd'] && ( trim($pdta['calEventStartTime']) === "" || trim($pdta['calEventEndTime']) === "" )) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE EVENT CANNOT BE WITHOUT A TIME INDICATOR")) : "";
      ( !$pdta['calEventAllDayInd'] && ( date('H:i', strtotime($pdta['calEventStartTime'])) > date('H:i', strtotime($pdta['calEventEndTime'])))) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE START TIME CANNOT BE AFTER THE END TIME")) : "";
      ( trim($pdta['calEventType']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE EVENT TYPE IS REQUIRED")) : "" ;
      //TODO:  DON'T HARD CODE THIS
      ( trim($pdta['calEventType']) === "INFCEVT" && ( trim($pdta['calEventPHIIni']) === "" || trim($pdta['calEventSurgeon']) === "" ))  ? (list( $errorInd, $msgArr[] ) = array( 1 , "WHEN SPECIFYING AN INFORMED CONSENT WATCH, A DONOR'S INITIALS AND THE MD MUST BE SPECIFIED (NO HIPAA INFORMATION ALLOWED!)")) : "" ;
      ( trim($pdta['calEventType']) === "INFCEVT" && ( trim($pdta['calEventForWho']) === 'ALLINST' || strtolower(trim($pdta['calEventForWho'])) === strtolower($u['originalaccountname']))) ?  (list( $errorInd, $msgArr[] ) = array( 1 , "INFORMED CONSENTS MUST LIST A SINGLE INSTITUTION ONLY (NOT ALL INSTITUTIONS AND NOT INDIVIDUALS)")) : "" ;

      ( trim($pdta['calEventTitle']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "AN EVENT TITLE IS REQUIRED" )) : "";
      ( trim($pdta['calEventDesc']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "AN EVENT DESCRIPTION IS REQUIRED" )) : "";
      //TODO: MAKE SURE USER HAS ACCESS TO THIS INSTITUTION IF SPECIFIED
      ( trim($pdta['calEventForWho']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A 'DISPLAY AT' VALUE IS REQUIRED" )) : "";
      //TODO:  CHECK THAT ALL MENU VALUES ARE ACCEPTABLE VALUES

      if ( $errorInd === 0 ) { 
        //SAVE EVENT 
        $evDate = DateTime::createFromFormat('Y-m-d', $pdta['calEventDte']);
        $evY = (int)$evDate->format('Y');
        $evM = (int)$evDate->format('m');
        $evD = (int)$evDate->format('d');
        $eDate = $evDate->format('Y-m-d');
        $eAllDay = ( $pdta['calEventAllDayInd'] ) ? 1 : 0;
        $eStart = trim($pdta['calEventStartTime']);        
        $eEnd = trim($pdta['calEventEndTime']);
        $eType = trim($pdta['calEventType']);
        $eICDonor = ( trim($pdta['calEventType']) === 'INFCEVT' ) ? strtoupper(trim($pdta['calEventPHIIni'])) : "";
        $eICMD = ( trim($pdta['calEventType']) === 'INFCEVT' ) ? strtoupper(trim($pdta['calEventSurgeon'])) : "";
        $eTitle = ( trim($pdta['calEventType']) !== 'INFCEVT' ) ? substr( trim($pdta['calEventTitle']), 0, 10) : "";
        $eDesc = ( trim($pdta['calEventType']) !== 'INFCEVT' ) ? trim($pdta['calEventDesc']) : "";
        $eFor = trim($pdta['calEventForWho']);

        (list( $errorInd, $msgArr[] ) = array(1 , "{$evY} // {$evM} // {$evD} // {$eFor}" ));

        $insSQL = "insert into four.sys_master_calendar (eyear, emonth, eday, eventdate, eventstarttime, eventendtime, allDayInd, eventtype, icdonorinitials, icsurgeon, eventtitle, eventdesc, dspForWhom, inputon, inputby) values (:eyear, :emonth, :eday, :eventdate, :eventstarttime, :eventendtime, :allDayInd, :eventtype, :icdonorinitials, :icsurgeon, :eventtitle, :eventdesc, :dspForWhom, now(), :inputby)";
        $iRS = $conn->prepare($insSQL); 
        $iRS->execute(array(
          ':eyear' => $evY
         ,':emonth' => $evM
         ,':eday' => $evD
         ,':eventdate' => $eDate 
         ,':eventstarttime' => $eStart
         ,':eventendtime' => $eEnd
         ,':allDayInd' => $eAllDay
         ,':eventtype' => $eType
         ,':icdonorinitials' => $eICDonor
         ,':icsurgeon' => $eICMD
         ,':eventtitle' => $eTitle
         ,':eventdesc' => $eDesc
         ,':dspForWhom' => $eFor
         ,':inputby' => $u['originalaccountname']
        ));

        $dta['dialogid'] = $pdta['calEventDialogId'];
        $responseCode = 200;

      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    }
    
    function shipdocoverridesalesorder ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start();
      $sessid = session_id();
      
      $sd = cryptservice($pdta['sdency'],'d');
      //$dspsd = substr('000000' . $sd, -6);      

      $chkUsrSQL = "SELECT originalaccountname as usr FROM four.sys_userbase where 1=1 and sessionid = :sessid and (allowInd = 1 and allowlinux = 1 and allowCoord = 1 and allowfinancials = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  USER MUST BE A MEMBER OF THE FINANCIAL USERS GROUP.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
      
      ( !array_key_exists('sonbr', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'sonbr' is missing.  Fatal Error")) : "";
      ( !array_key_exists('soamt', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'soamt' is missing.  Fatal Error")) : "";
      ( !array_key_exists('sdency', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'sdency' is missing.  Fatal Error")) : "";
      ( !array_key_exists('dialogid', $pdta) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Passed Data Array Key 'dialogid' is missing.  Fatal Error")) : "";
      
      ( trim($pdta['sonbr']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SUPPLIED A SALES ORDER NUMBER.")) : "";
      ( !is_numeric(trim($pdta['sonbr'])) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SALES ORDER NUMBERS MUST BE NUMERIC")) : "";

      ( trim($pdta['soamt']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "YOU HAVE NOT SUPPLIED A SALES ORDER AMOUNT.")) : "";
      ( !is_numeric(trim($pdta['soamt'])) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SALES ORDER AMOUNT MUST BE NUMERIC")) : "";
       
      ( trim($sd) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SHIP DOC NUMBER IS INCORRECT")) : "";
      
      if ( $errorInd === 0 ) { 

          //UPDATE SHIPDOC
          $insHistSQL = "insert into masterrecord.history_shipdoc (historyon, historyby, shipdocrefid, sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, actualshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, tqcourierid, courier, couriernbr, shipmentTrackingNbr, shipAddy, shipphone, billAddy, billphone, setupon, setupby, salesorder, salesorderamount, SAPified, SOBY, SOON, reconciledInd, reconciledshiplogon,  reconciledBy, closedOn, closedBy, surveyEmailSent, lasteditby, lastediton) SELECT now(), :usr, shipdocrefid, sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, actualshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, tqcourierid, courier, couriernbr, shipmentTrackingNbr, shipAddy, shipphone, billAddy, billphone, setupon, setupby, salesorder, salesorderamount, SAPified, SOBY, SOON, reconciledInd, reconciledshiplogon, reconciledBy, closedOn, closedBy, surveyEmailSent, lasteditby, lastediton FROM masterrecord.ut_shipdoc where shipdocrefid = :sd";
          $insHistRS = $conn->prepare($insHistSQL);
          $insHistRS->execute(array(':usr' => $u['usr'], ':sd' => $sd));
          
          $updSQL = "update masterrecord.ut_shipdoc set salesorder = :so, salesorderamount = :soamt,  soby = :usr, soon = now() where shipdocrefid = :sd";
          $updRS = $conn->prepare($updSQL);
          $updRS->execute(array(':so' => $pdta['sonbr'] , ':soamt' => (float)$pdta['soamt'], ':usr' => $u['usr'], ':sd' => $sd));             
          $dta['dialogid'] = $pdta['dialogid']; 

          //todo:  Get Investigator email address
          //$iEmail[] = "dfitzsim@pennmedicine.upenn.edu";
          //$iEmail[] = "zacheryv@pennmedicine.upenn.edu";
          //$iEmail[] = "zackvm@zacheryv.com";

          //if ( $iEmail !== "" ) { 
          //  $emlSQL = "insert into serverControls.emailthis (towhoaddressarray, sbjtline, msgBody, htmlind, wheninput, bywho) value(:towhoaddressarray, 'CHTN-EASTERN SHIPMENT FOLLOWUP EMAIL', :msgBody, 1, now(), :bywho)";
          //  $emlRS = $conn->prepare( $emlSQL );
          //  $emlRS->execute(array ( ':towhoaddressarray' => json_encode( $iEmail ), ':msgBody' => "<table border=1><tr><td><CENTER>THE MESSAGE BELOW IS ABOUT YOUR RECENT SHIPMENT WITH SURVEY LINK.<p>PLEASE DO NOT RESPONSED TO THIS EMAIL. CONTACT THE CHTNEASTERN OFFICE DIRECTLY EITHER BY EMAIL chtnmail@uphs.upenn.edu OR BY CALLING (215) 662-4570.</td></tr><tr><td>SURVEY LINK TEXT</td></tr></table>", ':bywho' =>  "SALES-ORDER-ADD-{$u['usr']}" ));
//          }
          $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;         
    }

    function shipdocoverrideshipdate ( $request, $passdata ) {
      //{"sdency":"aEU0c21vMFU5eUtYazJ5aldxUGNBQT09","sdshipdte":"2019-05-01","couriertrck":"fr5t4f","deviationreason":"Inventory Module Not Working","usrpin":"is3t9hxECdrYlSixuGctsz0TkexFjU4p4dscrmWJn510deukKIebgDL4whrfqXafaHHBCY5xD1UdGltCG/1jY8YlROOGOGjwExPxu4+PWbW/9IqgvOfffV6ZEMUyrBv2L0yNoJrGZtCJ2gbTfBdlp+R5fNMOnC4g/ac8kQWRwxxRwxSIV4Px7r3OrGohKTWUeQ+rmkKw/TFDaXKEJaeK0Qf7jeuhzPbt2djU8uj3FlekRydOyBdh0IB49zieEm+Rfc/y/sxc10FjELbPPmyQQOv4zdpaeRNzbJWDShwq0Y6RvN559TP42Vq6bQJITvMp6vlF5WTcEPouk1N056p5cw=="}  
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      session_start();
      $sessid = session_id();

      //DATA CHECKS
      $sd = cryptservice($pdta['sdency'],'d');
      $dspsd = substr('000000' . $sd, -6);
      ( !array_key_exists('sdency', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdency' missing from passed data")) : "";
      ( !array_key_exists('sdshipdte', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdshipdte' missing from passed data")) : "";
      ( !array_key_exists('couriertrck', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'courier track' missing from passed data")) : "";
      ( !array_key_exists('deviationreason', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'deviationreason' missing from passed data")) : "";
      ( !array_key_exists('usrpin', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'usrpin' missing from passed data")) : "";

      $usrpin = "";
      ( array_key_exists('usrpin',$pdta) && chtndecrypt($pdta['usrpin'], true) === "" ) ? (list( $errorind, $msgArr[] ) = array(1 , "Inventory User Pin is required. Please supply a value.")) : (list( $usrpin ) = array( chtndecrypt($pdta['usrpin'], true))); 
      ( array_key_exists('sdency', $pdta) && trim($pdta['sdency']) === "" ) ? (list( $errorind, $msgArr[] ) = array(1 , "Ship-Doc Number is missing")) : "";
      ( array_key_exists('sdshipdte', $pdta) && trim($pdta['sdshipdte']) === "" ) ? (list( $errorind, $msgArr[] ) = array(1 , "Ship-Doc Ship Date cannot be blank.  Please supply a value.")) : "";
      ( array_key_exists('couriertrck', $pdta) && trim($pdta['couriertrck']) === "" ) ? (list( $errorind, $msgArr[] ) = array(1 , "Ship-Doc Courier Tracking Number cannot be blank.  Please supply a value.")) : "";
      ( array_key_exists('deviationreason', $pdta) && trim($pdta['deviationreason']) === "" ) ? (list( $errorind, $msgArr[] ) = array(1 , "Ship-Doc Shipping Deviation Reason is blank.  Please supply a value.")) : "";

      $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and sessionid = :sessid and (allowInd = 1 and allowCoord = 1) and inventorypinkey = :pinkey and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
      $rs = $conn->prepare($chkUsrSQL); 
      $rs->execute(array(':sessid' => $sessid, ':pinkey' => $usrpin));
      if ($rs->rowCount() === 1) { 
        $u = $rs->fetch(PDO::FETCH_ASSOC);
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER WITH PIN INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
      }
    
      ( trim($pdta['sdshipdte']) !== "" &&  !verifyDate(trim($pdta['sdshipdte']),'Y-m-d', true) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE DATE SPECIFIED FOR THE SHIPMENT IS INVALID")) : "" ;
      $givendate = new DateTime($pdta['sdshipdte']);
      $currentdate = new DateTime();
      ( $givendate > $currentdate ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "SHIPMENT DATE CANNOT BE IN THE FUTURE")) : "" ;

      $valChkSQL = "SELECT * FROM four.sys_master_menus where menu = :mnu and dspvalue = :mnuvalue";
      $valChkRS = $conn->prepare($valChkSQL); 
      $valChkRS->execute(array(':mnuvalue' => $pdta['deviationreason'], ':mnu' => 'DEVIATIONREASON_HPROVERRIDE')); 
      ( $valChkRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE REASON FOR SOP DEVIATION IS NOT VALID")) : "" ;

      //MAKE SURE THAT THE SHIPDOC IS NOT ALREADY CLOSED
      $sdChkSQL = "SELECT shipdocrefid FROM masterrecord.ut_shipdoc where shipdocrefid = :sd and sdstatus <> 'CLOSED' and ifnull(shipmentTrackingNbr,'') = ''";
      $sdChkRS = $conn->prepare($sdChkSQL);
      $sdChkRS->execute(array( ':sd' => $sd ));
      ( $sdChkRS-> rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "SHIPDOC {$sd} IS CLOSED, ALREADY HAS A COURIER TRACKING NUMBER OR DOES NOT EXIST. IT CANNOT BE MODIFIED")) : "" ;


      if ( $errorInd === 0 ) {
          //UPDATE SHIPDOC AND ALL SEGMENTS WITH SHIPDOC SHIPDATE AND THEN CLOSED SHIPDOC

          //BACK-UP SCAN HISTORY
          $inventoryHistSQL = "insert into masterrecord.history_procure_segment_inventory (segmentid, bgs, scannedlocation, scannedinventorycode, inventoryscanstatus, scannedby, scannedon, historyon, historyby) select segmentid, bgs, scannedlocation, scanloccode, scannedstatus, scannedby, scanneddate, now(), :usr from masterrecord.ut_procure_segment where shipdocrefid = :sd";
          $inventoryHistRS = $conn->prepare($inventoryHistSQL); 
          $inventoryHistRS->execute(array(':usr' => $u['originalaccountname'],':sd' => $sd));

          //BACK-UP STATUS HISTORY
          $segStatSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) SELECT segmentid, segstatus, statusby, statusdate, now(), :usr, 'SHIPPED'  FROM masterrecord.ut_procure_segment where shipdocrefid = :sd";
          $segStatRS = $conn->prepare($segStatSQL); 
          $segStatRS->execute(array( ':usr' => $u['originalaccountname'],':sd' => $sd));

          //UPDATE SEGMENTS
          $updSegSQL = "update masterrecord.ut_procure_segment set shippeddate = :shipdate, segstatus = 'SHIPPED', statusdate = now(), statusby = :usr, scanloccode = 'SHIPPEDOUT', scannedlocation = 'SHIPPED TO INVESTIGATOR', scannedstatus = 'SHIPPED TO INVESTIGATOR', scanneddate = now() where shipdocrefid = :sd";
          $updSegRS = $conn->prepare($updSegSQL);
          $updSegRS->execute(array(':usr' => $u['originalaccountname'], ':sd' => $sd, ':shipdate' => $pdta['sdshipdte'] ));

          //UPDATE SHIPDOC
          $insHistSQL = "insert into masterrecord.history_shipdoc (historyon, historyby, shipdocrefid, sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, actualshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, tqcourierid, courier, couriernbr, shipmentTrackingNbr, shipAddy, shipphone, billAddy, billphone, setupon, setupby, salesorder, SAPified, SOBY, SOON, reconciledInd, reconciledshiplogon, reconciledBy, closedOn, closedBy, surveyEmailSent, lasteditby, lastediton) SELECT now(), :usr, shipdocrefid, sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, actualshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, tqcourierid, courier, couriernbr, shipmentTrackingNbr, shipAddy, shipphone, billAddy, billphone, setupon, setupby, salesorder, SAPified, SOBY, SOON, reconciledInd, reconciledshiplogon, reconciledBy, closedOn, closedBy, surveyEmailSent, lasteditby, lastediton FROM masterrecord.ut_shipdoc where shipdocrefid = :sd";
          $insHistRS = $conn->prepare($insHistSQL);
          $insHistRS->execute(array(':usr' => $u['originalaccountname'], ':sd' => $sd));

          //CLOSE SHIPDOC      
          $updShpDocSQL = "update masterrecord.ut_shipdoc set sdstatus = 'CLOSED', actualshipdate = :shipdate, shipmentTrackingNbr = :tracknbr, lasteditby = :usr, lastediton = now() where shipdocrefid = :sd"; 
          $updShpDocRS = $conn->prepare($updShpDocSQL);
          $updShpDocRS->execute(array( ':sd' => $sd, ':usr' => $u['originalaccountname'], ':shipdate' => $pdta['sdshipdte'], ':tracknbr' => $pdta['couriertrck'] ));

          //TODO: CAPTURE DEVIATION REASON AND ALL OTHER DEVIATION REASONS THROUGH OUT CODE ....
          $dta['dialogid'] = $pdta['dialogid'];
          //$dta['r'] = array( ':sd' => $sd, ':usr' => $u['originalaccountname'], ':shipdate' => $pdta['sdshipdte'], ':tracknbr' => $pdta['couriertrck'] );
          $responseCode = 200;
      }
      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }  

    function bgslookuprequest( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);

      $sdency = $pdta['sdency'];
       
      $bgs = trim($pdta['bgs']);
      ( $bgs === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE CHTN LABEL # FIELD CANNOT BE LEFT BLANK WHEN PERFOMING A LOOKUP FUNCTION")) : "";

      if ( $errorInd === 0 ) {
          $lookupSQL = "SELECT sg.segmentid, replace(sg.bgs,'_','') as bgs, ucase(concat(ifnull(sg.prepmethod,''), if(ifnull(sg.preparation,'')='','',concat(' / ',ifnull(sg.preparation,''))))) as prepdsp, ucase(concat(ifnull(bs.tisstype,''), if(ifnull(bs.anatomicsite,'')='','',concat(' :: ',ifnull(bs.anatomicsite,''))), if(ifnull(bs.diagnosis,'') =  '','',concat(' :: ',ifnull(bs.diagnosis,''))), if(ifnull(bs.subdiagnos,'') = '','',concat(' (',ifnull(bs.subdiagnos,''),')')))) as dx, ifnull(segstat.longvalue,'') as segmentstatus, if(ifnull(sg.assignedto,'')='','',concat(' :: ', ifnull(sg.assignedto,'')))  as assignedto  FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'segmentstatus') as segstat on sg.segstatus = segstat.menuvalue where replace(sg.bgs,'_','') = :bgs order by bgs asc";
          $lookupRS = $conn->prepare($lookupSQL); 
          $lookupRS->execute(array(':bgs' => preg_replace( '/[\_]/', '', $pdta['bgs']) ));
          if ( $lookupRS->rowCount() === 0 ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "NO CHTN LABELLED SEGMENTS MATCH YOUR SEARCH REQUEST ({$pdta['bgs']})"));
            $responseCode = 404;
          } else {
            $bgsCnt = 0; 
            $bgsMasterCnt = 1111;
            $bgsTbl = "<table border=0><tr>"; 
            while ( $sg = $lookupRS->fetch(PDO::FETCH_ASSOC) ) { 
                if ($bgsCnt === 3) { 
                  $bgsTbl .= "</tr><tr>";
                  $bgsCnt = 0;
                }
                   $bgsDsp = "<table border=0><tr><td class=clsBGS>{$sg['bgs']}</td><td class=clsPrp>&nbsp;[{$sg['prepdsp']}]</td></tr><td colspan=2 class=clsDX>{$sg['dx']}</td></tr><tr><td class=statAss colspan=2>{$sg['segmentstatus']}{$sg['assignedto']}</td></tr></table>";

                   //$sdency = cryptservice($sd,'e');
                   $sid = cryptservice($sg['segmentid'],'e'); 
                $bgsTbl .= "<td onclick=\"addSegmentToShipDoc('{$sdency}','{$sid}',{$bgsMasterCnt});\" ><div id='sgAssList{$bgsMasterCnt}' class=\"offeredSegCell\">{$bgsDsp}</div></td>";
                $bgsCnt++;
                $bgsMasterCnt++;
            }
            $bgsTbl .= "</tr></table>";
            $dta['return'] = $bgsTbl;
            $responseCode = 200;
          }
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }

    function shipdocaddsegment ( $request, $passdata ) { 
      //{"sdency":"ZkFBTkJsZUM5SXhVcEtvOURscGUwQT09","segid":"QXQwUVNrL08vL25uc3gxZjJxK2MvQT09","dspnbr":1}  
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      $goodUser = 0;

      //DATACHECKS     
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
          $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
          $goodUser = 1;
      }

      if ( $goodUser === 1 ) { 
        //DATACHECKS
        $sd = cryptservice($pdta['sdency'],'d');
        $dspsd = substr(('000000' . $sd),-6);
        $sid = cryptservice($pdta['segid'],'d');
        $sdChkSQL = "SELECT shipdocrefid, investcode FROM masterrecord.ut_shipdoc where shipdocrefid = :sd and sdstatus <> :closedstatus";
        $sdChkRS = $conn->prepare($sdChkSQL); 
        //TODO:  Make this dynamic
        $sdChkRS->execute(array(':sd' => $sd, ':closedstatus' => 'CLOSED'));
        ( $sdChkRS->rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THIS SHIPMENT DOCUMENT ({$dspsd}) IS ALREADY MARKED AS CLOSED.  YOU MAY NOT MODIFY IT.")) : $sddta = $sdChkRS->fetch(PDO::FETCH_ASSOC);
        //SHIPDOC INVESTIGATOR $sddta['investcode']
        $bgsChkSQL = "SELECT assignedto, segstatus, replace(bgs,'_','') as bgs, ifnull(shippeddate,'') as shippeddate FROM masterrecord.ut_procure_segment where segmentid = :segid and ( segstatus = 'BANKED' OR segstatus = 'ASSIGNED')";
        $bgsChkRS = $conn->prepare($bgsChkSQL); 
        $bgsChkRS->execute(array(':segid' => $sid));
        ( $bgsChkRS->rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT SPECIFIED MUST BE STATUSED 'ASSIGNED' OR 'BANKED'.  YOU MAY NOT ADD IT TO THIS SHIP-DOC.")) : $sgdta = $bgsChkRS->fetch(PDO::FETCH_ASSOC);
        if ( trim($sgdta['segstatus']) === 'ASSIGNED' ) { 
          //CHECK ASSIGNMENT
          (strtoupper(trim($sddta['investcode'])) !== strtoupper(trim($sgdta['assignedto']))) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE ASSIGNED SEGMENTS MUST BE ASSIGNED TO THE INVESTIGATOR ON THE SHIPDOC. YOU MAY NOT ADD IT TO THIS SHIP-DOC.")) : "";
        }
        (trim($sgdta['shippeddate']) !== "") ? (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT HAS ALREADY BEEN SHIPPED. YOU MAY NOT ADD IT TO THIS SHIP-DOC.")) : "";
        //MAKE SURE NOT ON SHIPDOC ALREADY
        $detChkSQL = "SELECT shipdocDetId FROM masterrecord.ut_shipdocdetails where shipdocrefid = :sd and segid = :sid";
        $detRS = $conn->prepare($detChkSQL); 
        $detRS->execute(array(':sd' => $sd, ':sid' => $sid)); 
        ( $detRS->rowCount() <> 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT IS ALREADY ON THE SHIPDOC. YOU MAY NOT ADD IT AGAIN TO THIS SHIP-DOC.")) : "";
      }

      if ( $errorInd === 0 ) { 
        $addSQL = "insert into masterrecord.ut_shipdocdetails ( shipdocrefid, segid, addon, addedby, pulledind) values(:sd,:sid,now(),:usr,0)";
        $addRS = $conn->prepare($addSQL); 
        $addRS->execute(array(':sd' => $sd, ':sid' => $sid, ':usr' => $u['originalaccountname']));
        
        $logSQL = "insert into masterrecord.history_shipdoc_actions (shipdocrefid, status, statusdate, bywhom, ondate, segmentreference) values(:sd, :stsmsg, now(),:usr,now(),:sid)";
        $logRS = $conn->prepare($logSQL); 
        $logRS->execute(array(':sd' => $sd, ':stsmsg' => "SEGMENT ADDED. SEGID={$sid}",':usr' => $u['originalaccountname'], ':sid' => $sid));
        
        $bckSegStsSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) select segmentid, segstatus, statusBy, statusDate, now(), :usr, :newstatus from  masterrecord.ut_procure_segment where segmentid = :sid";
        $bckSegStsRS = $conn->prepare($bckSegStsSQL); 
        $bckSegStsRS->execute(array(':usr' => $u['originalaccountname'], ':newstatus' => 'ONPICKLIST', ':sid' => $sid));
        
        $updSegSQL = "update masterrecord.ut_procure_segment set segstatus = :newsts, statusdate = now(), statusby = :usr, shipdocrefid = :sd where segmentid = :sid";
        $updSegRS = $conn->prepare($updSegSQL); 
        $updSegRS->execute(array(':sid' => $sid, ':sd' => $sd, ':usr' => $u['originalaccountname'], ':newsts' => 'ONPICKLIST'));
        
        $dta['dspid'] = $pdta['dspnbr'];
        $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }

    function lookupshipdocqry ( $request, $passdata ) {
      //{"qryshipdoc":"5435"}  
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      $goodUser = 0;

      //DATACHECKS     
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
          $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
          $goodUser = 1;
      }

      if ( $goodUser === 1 ) { 
        ( trim($pdta['qryshipdoc']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A Shipment Document Number query is required")) : "";
      }


      if ( $errorInd === 0 ) { 
          //$pdta['qryshipdoc']
          $lookupSQL = "SELECT substr(concat('000000',shipdocrefid),-6) as shipdocrefnbr, ucase(ifnull(sdstatus,'')) as shipdocstatus, date_format(statusdate,'%m/%d/%Y') as statusdate, ifnull(investcode,'') as investcode, ifnull(investname,'') as investname, if(ifnull(salesorder,'')='','',substr(concat('000000',ifnull(salesorder,'')),-6)) as salesorder FROM masterrecord.ut_shipdoc where shipdocrefid like :lookupvalue order by shipdocrefid desc";
          $lookupRS = $conn->prepare($lookupSQL); 
          $lookupRS->execute(array(':lookupvalue' => "%{$pdta['qryshipdoc']}%"));

          if ( $lookupRS->rowCount() === 0 ) { 
             (list( $errorInd, $msgArr[] ) = array(1 , "NO SHIPDOCs FOUND"));
          } else {
            $itemsfound = $lookupRS->rowCount();

            $rsltTbl = "<table border=0 id=shipDocQryRsltTbl><tr><td colspan=15 id=rsltCount>Shipment Documents: " . $lookupRS->rowCount() . "</td></tr><tr><th>Ship-Doc #</th><th>Status / Date</th><th>Investigator</th><th>Sales Order #</th></tr>";
            while ($r = $lookupRS->fetch(PDO::FETCH_ASSOC)) {
              $urlency = cryptservice( $r['shipdocrefnbr'], 'e');  
              $rsltTbl .= "<tr class=rowDsp onclick=\"navigateSite('shipment-document/{$urlency}');\"><td class=dspCell>{$r['shipdocrefnbr']}</td><td class=dspCell>{$r['shipdocstatus']} ({$r['statusdate']})</td><td class=dspCell>{$r['investname']} ({$r['investcode']})</td><td class=dspCell>{$r['salesorder']}</td></tr>";
            }
            $rsltTbl .= "</table>";
            $dta = $rsltTbl;          
          }
          $responseCode = 200;
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }

    function shipdocremovespcsrvfee ( $request, $passdata ) {
      //{"sdency":"dUFtblliYlB6SW45QTBxZ3V2ZVlrQT09","ssfid":"7","dspcell":"ybwZoeBs"}  
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      $goodUser = 0;

      $dta['dspcellid'] = $pdta['dspcell'];
      $sd = cryptservice( $pdta['sdency'], 'd');
      
      //DATACHECKS     
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT emailaddress, originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
          $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
          $goodUser = 1;
      }

      ( !$sd ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Shipment Document Number missing.  See CHTNEastern Informatics")) : "";
      ( trim($pdta['ssfid']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Shipment Document Special Service Fee Id missing.  See CHTNEastern Informatics")) : "";

      if ( $goodUser === 1 && $errorInd === 0 ) {
        $updSQL = "update masterrecord.ut_shipdoc_spcsrvfee set dspind = 0, lastupdated = now(), lastupdatedby = :usr where shipdocrefid = :sd and srvcfeeid = :ssfid";
        $updRS = $conn->prepare($updSQL);
        $updRS->execute(array(':usr' => $u['emailaddress'], ':sd' => $sd, ':ssfid' => $pdta['ssfid'] ));
        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }

    function shipdocremovesegment ( $request, $passdata ) {
      //dialogid	TWaECoPecCCtJEN // dspcell	BKgdTzqg // rnote	// rreason	// sdency	ZkFBTkJsZUM5SXhVcEtvOURscGUwQT09 // segid	449032 // segstatus	  
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      $goodUser = 0;

      $dta['dspcellid'] = $pdta['dspcell'];
      $dta['dialogid'] = $pdta['dialogid'];
      $sd = cryptservice( $pdta['sdency'], 'd');
      $dspsd = substr(('000000' . $sd),-6);
      $sid = $pdta['segid'];
      
      //DATACHECKS     
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
          $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
          $goodUser = 1;
      }


      if ( $goodUser === 1 ) { 
        
        ( trim($pdta['segstatus']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A \"SEGMENT STATUS\" IS REQUIRED")) : "";
        //CHECK THAT SEGSTATUS iS VALID VALUE

        if ( trim($pdta['segstatus']) !== "" ) {
          $chkSQL = "SELECT menuid FROM four.sys_master_menus where menu = :menu and academvalue = :limiter and dspind = :dspind and menuvalue = :menuvalue"; 
          $chkRS = $conn->prepare($chkSQL);
          $chkRS->execute(array(':menu' => 'SEGMENTSTATUS',':limiter' => 'RESTOCK',':dspind' => 1,':menuvalue' => $pdta['segstatus']));
          ( $chkRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE SEGMENT STATUS DOES NOT EXIST - SEE A CHTNEASTERN INFORMATICS PERSON")) : "";
        }

        ( trim($pdta['rreason']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A \"REMOVAL REASON\" IS REQUIRED")) : "";
        //CHECK VALID REASON VALUE
        if ( trim($pdta['rreason']) !== "" ) { 
          $chkaSQL = "SELECT menuid FROM four.sys_master_menus where menu = :menu and dspind = :dspind and menuvalue = :menuvalue"; 
          $chkaRS = $conn->prepare($chkaSQL);
          $chkaRS->execute(array(':menu' => 'SEGMENTRESTOCKREASON', ':dspind' => 1, ':menuvalue' => trim($pdta['rreason']) ));
          ( $chkaRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE REMOVAL REASON DOES NOT EXIST - SEE A CHTNEASTERN INFORMATICS PERSON")) : "";
        }

        //1. Make Sure Segment is real
        $chkSQL = "SELECT ucase(replace( ifnull(bgs,''),'_','')) as bgs, ifnull(segstatus,'') as segstatus, ifnull(date_format(shippedDate,'%Y-%m-%d'),'') as shippeddate, ifnull(assignedto,'') as assignedto, ifnull(assignedreq,'') as assignedreq FROM masterrecord.ut_procure_segment where segmentid = :segmentid and shipDocRefID = :shipdocref";
        $chkRS = $conn->prepare($chkSQL); 
        $chkRS->execute(array(':segmentid' => $sid, ':shipdocref' => $sd)); 
        if ( $chkRS->rowCount() <> 1 ) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "EITHER THE PASSED SEGMENT ID OR THE SHIPDOC ENCRYPTION ID FAILED - SEE A CHTNEASTERN INFORMATICS PERSON"));
        } else { 
           $segR = $chkRS->fetch(PDO::FETCH_ASSOC);
           $segBGS = $segR['bgs'];
           $segStat = $segR['segstatus'];
           $segSDte = $segR['shippeddate'];
           $segAss = $segR['assignedto'];
           $segReq = $segR['assignedreq'];
           //2. Make sure its not shipped
           ( trim($segBGS) === "" || strtoupper(trim($segStat)) === "SHIPPED" || trim($segSDte) !== "" || trim($segAss) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THERE IS A PROBLEM WITH THE SEGMENT.  EITHER THE SEGMENT IS SHIPPED OR THERE IS NO INVESTIGATOR ASSIGNMENT.  SEE A CHTNEASTERN INFORMATICS PERSON IF THIS IS IN ERROR.")) : "";
        }
        //3. Make sure it exists on the referenced shipdoc
        $onSDSQL = "SELECT * FROM masterrecord.ut_shipdocdetails where shipdocrefid = :sd and segid = :segid and pulledind = :pulledind";
        $onSDRS = $conn->prepare($onSDSQL); 
        $onSDRS->execute(array(':sd' => $sd, ':segid' => $sid, ':pulledind' => 0));
        ( $onSDRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "EITHER THE SEGMENT DOES NOT EXIST ON THE SHIPDOC OR IS IN A PULLED STATE AND SO CANNOT BE REMOVED.")) : "";

        //4. Make sure the shipdoc is not closed
        $sdChkSQL = "SELECT shipdocrefid  FROM masterrecord.ut_shipdoc where shipdocrefid = :sd and sdstatus <> :closedstatus";
        $sdChkRS = $conn->prepare($sdChkSQL); 
        //TODO:  Make this dynamic
        $sdChkRS->execute(array(':sd' => $sd, ':closedstatus' => 'CLOSED'));
        ( $sdChkRS->rowCount() === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THIS SHIPMENT DOCUMENT ({$dspsd}) IS ALREADY MARKED AS CLOSED.  YOU MAY NOT MODIFY IT.")) : "";

      }

      if ( $errorInd === 0 && $goodUser === 1 ) { 

          //WRITE DELETION ACTION to masterrecord.history_shipdoc_actions
          $bckSQL = "insert into masterrecord.history_shipdoc_actions (shipdocrefid, status, statusdate, bywhom, ondate, removalreason, removalnote, segmentreference) values (:sd,:message,now(), :usr, now(),:rvr, :rvn, :sid)";
          $bckRS = $conn->prepare($bckSQL);
          $bckRS->execute(array(':sd' => $sd, ':message' => "SEGMENT DELETED. SEGID={$sid} BGS={$segBGS}",':usr' => $u['originalaccountname'], ':rvr' => trim($pdta['rreason']), ':rvn' => trim($pdta['rnote']), ':sid' => $sid ));

          //WRITE HISTORY to masterrecord.history_procure_segment_status // masterrecord.history_procure_segment_assignment 
          $sgBckSQL = "insert masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby, newstatus) SELECT segmentid, segstatus, statusby, statusdate, now(), :usr, :newstatus FROM masterrecord.ut_procure_segment where segmentId = :sid";
          $sgBckRS = $conn->prepare($sgBckSQL);
          $sgBckRS->execute(array( ':sid' => $sid, ':usr' => $u['originalaccountname'], ':newstatus' => trim($pdta['segstatus']) ));
          
          //UPDATE SEGMENT (ASSIGNED / BANKED / PENDDEST / XNFIPI ) 
          $bckAssSQL = "insert into masterrecord.history_procure_segment_assignment(segmentid, previousassignment, previousproject, previousrequest, previousassignmentdate, previousassignmentby, enteredon, enteredby) select segmentid, ifnull(assignedTo,'NO-INV-ASSIGNMENT') as assignedto, ifnull(assignedProj,'NO-PROJ-ASSIGNMENT') as assignedproj, ifnull(assignedReq,'NO-REQ-ASSIGNMENT') as assignedreq, ifnull(assignmentdate, now()) as assignmentdate, ifnull(assignedby,'NO-BY-ASSIGNMENT'), now(), :usr from masterrecord.ut_procure_segment where segmentid = :sid";
          $bckAssRS = $conn->prepare($bckAssSQL);
          $bckAssRS->execute(array(':sid' => $sid, ':usr' => $u['originalaccountname']));
          //TODO: MAKE THE STATUSES DYNAMIC
          switch ( strtoupper(trim($pdta['segstatus'])) ) { 
            case 'ASSIGNED':
                $updSQL = "update masterrecord.ut_procure_segment set segstatus = :sts, statusdate = now(), statusby = :usr, shipDocRefID = null where segmentId = :sid";
                break;
            case 'BANKED':
                $updSQL = "update masterrecord.ut_procure_segment set segstatus = :sts, statusdate = now(), statusby = :usr, shipDocRefID = null, assignedto = '', assignedReq = '', assignmentdate = now(), assignedby = '' where segmentId = :sid";
                break;
            case 'PENDDEST':
                $updSQL = "update masterrecord.ut_procure_segment set segstatus = :sts, statusdate = now(), statusby = :usr, shipDocRefID = null, assignedto = '', assignedReq = '', assignmentdate = now(), assignedby = '' where segmentId = :sid";
                break;
            //case 'XNFIPI':
            //    $updSQL = "update masterrecord.ut_procure_segment set segstatus = :sts, statusdate = now(), statusby = :usr, shipDocRefID = null, assignedto = '', assignedReq = '', assignmentdate = now(), assignedby = '' where segmentId = :sid";
            //    break;
          }
          $updRS = $conn->prepare($updSQL);
          $updRS->execute(array(':usr' => $u['originalaccountname'], ':sid' => $sid, ':sts' => strtoupper(trim($pdta['segstatus']))));

          //Remove Segment from shipdoc record 
          //TODO:  THIS IS BAD BAD - SHOULD MAKE IT A DSPIND FIELD INSTEAD OF DELETION - BUT ...
          $delSQL = "delete FROM masterrecord.ut_shipdocdetails where shipdocrefid = :shpdoc and segid = :segid";
          $delRS = $conn->prepare($delSQL); 
          $delRS->execute(array(':shpdoc' => $sd, ':segid' => $sid));
          //TODO: IF ALL SEGMENTS DELETED FROM SHIPDOC - VOID SHIPDOC!!
          $finalChkRS = $conn->prepare("Select * from masterrecord.ut_shipdocdetails where shipdocrefid = :shipdocrefid");
          $finalChkRS->execute(array(':shipdocrefid' => $sd)); 
          if ( $finalChkRS->rowCount() < 1 ) { 
            //MARK AS VOID 
            $voidSQL = "update masterrecord.ut_shipdoc set sdstatus = 'VOID', statusdate = now(), statusby = :usr where shipdocrefid = :sd";
            $voidRS = $conn->prepare($voidSQL); 
            $voidRS->execute(array(':usr' => $u['originalaccountname'], ':sd' => $sd));
            $logSQL = "insert into masterrecord.history_shipdoc_actions ( shipdocrefid, status, statusdate, bywhom, ondate) values(:sd,'VOID',now(),:usr,now())";
            $logRS = $conn->prepare($logSQL); 
            $logRS->execute(array(':sd' => $sd, ':usr' => $u['originalaccountname'])); 
          }
          $responseCode = 200; 
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }

    function shipdocscreensave ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
        $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
      }
      //DATACHECKS     
      $sdnbr = cryptservice( $pdta['sdency'] , 'd');
      $dspsd = substr('000000' . $sdnbr, -6);

      ( !array_key_exists('sdency', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdency' missing from passed data")) : "";
      ( !array_key_exists('sdcRqstShipDateValue', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcRqstShipDateValue' missing from passed data")) : "";
      ( !array_key_exists('sdcRqstToLabDateValue', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcRqstToLabDateValue' missing from passed data")) : "";
      ( !array_key_exists('sdcAcceptedBy', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcAcceptedBy' missing from passed data")) : "";
      ( !array_key_exists('sdcAcceptorsEmail', $pdta) || !filter_var(trim($pdta['sdcAcceptorsEmail']), FILTER_VALIDATE_EMAIL)) ? (list( $errorind, $msgArr[] ) = array(1 , "The Acceptor's email address is invalid or the array key 'sdcAcceptorsEmail' is missing")) : "";
      ( !array_key_exists('sdcPurchaseOrder', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcPurchaseOrder' missing from passed data")) : "";
      //( !array_key_exists('sdcShipDocSalesOrder', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcShipDocSalesOrder' missing from passed data")) : "";
      ( !array_key_exists('sdcInvestShippingAddress', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcInvestShippingAddress' missing from passed data")) : "";
      ( !array_key_exists('sdcShippingPhone', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcShippingPhone' missing from passed data")) : "";
      ( !array_key_exists('sdcCourierInfoValue', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcCourierInfoValue' missing from passed data")) : "";
      ( !array_key_exists('sdcInvestBillingAddress', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcInvestBillingAddress' missing from passed data")) : "";
      ( !array_key_exists('sdcBillPhone', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcBillPhone' missing from passed data")) : "";
      ( !array_key_exists('sdcPublicComments', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'sdcPublicComments' missing from passed data")) : "";

      //1. check the the shipdoc is open/locked!!!!!
      //TODO:  MAKE STATUSES DYNAMIC
      $chkSDSQL = "SELECT sdstatus, investcode FROM masterrecord.ut_shipdoc where shipdocrefid = :sdrefid and ( sdstatus <> :sdstatus and sdstatus <> :sdstattoo )";
      $chkSDRS = $conn->prepare($chkSDSQL); 
      $chkSDRS->execute( array( ':sdrefid' => $sdnbr, ':sdstatus' => 'CLOSED', ':sdstattoo' => 'VOIDED' ));
      ( $chkSDRS->rowCount() <> 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SHIP DOC ({$dspsd}) DOESN'T EXIST OR IS NOT STATUSED TO BE EDITED")) : $sd = $chkSDRS->fetch(PDO::FETCH_ASSOC);

      //2: VALID DATES ON SHIPREQUEST AND TO LAB
      ( trim($pdta['sdcRqstShipDateValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "A REQUESTED SHIPMENT DATE IS REQUIRED")) : "" ;
      ( trim($pdta['sdcRqstShipDateValue']) !== "" &&  !verifyDate(trim($pdta['sdcRqstShipDateValue']),'Y-m-d', true) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE DATE SPECIFIED FOR THE SHIPMENT IS INVALID")) : "" ;
      //3: SHIP & BILL ADDRESS AREN'T BLANK
      ( trim($pdta['sdcInvestShippingAddress']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE SHIPPING ADDRESS IS REQUIRED")) : "" ;
      ( trim($pdta['sdcInvestBillingAddress']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE BILLING ADDRESS IS REQUIRED")) : "" ;
      //4: PO Nbr IS NOT BLANK
      ( trim($pdta['sdcPurchaseOrder']) === "" ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "A PURCHASE ORDER VALUE IS REQUIRED")) : "" ;
      //5: COURIER NUMBER IS VALID IF FILLED IN FOR THIS INVESTIGATOR
      if ( trim($pdta['sdcCourierInfoValue']) !== "" ) { 
        $chkCourierSQL = "SELECT courier_name, courier_num, courierid FROM vandyinvest.eastern_courier where courierid = :courierid and investid = :investid";
        $chkCourierRS = $conn->prepare($chkCourierSQL); 
        $chkCourierRS->execute(array(':courierid' => trim($pdta['sdcCourierInfoValue']), ':investid' => $sd['investcode']));
        if ( $chkCourierRS->rowCount() <> 1 ) {
          (list( $errorind, $msgArr[] ) = array(1 , "THE COURIER IS NOT VALID FOR THIS INVESTIGATOR"));
        } else { 
          $courierrecord = $chkCourierRS->fetch(PDO::FETCH_ASSOC);
          $courierid = (int)$pdta['sdcCourierInfoValue'];
          $courier = $courierrecord['courier_name'];
          $courierNbr = $courierrecord['courier_num'];
        }
      } else { 
          $courierid = 0;
          $courier = "";
          $courierNbr = "";
      }
      //6: SHIP AND BILL PHONE ARE NOT BLANK AND ARE VALID 
      (trim($pdta['sdcShippingPhone']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A SHIPPING PHONE NUMBER MUST BE SPECIFIED")) : "" ;  
      (trim($pdta['sdcBillPhone']) === "") ? (list( $errorInd, $msgArr[] ) = array( 1 , "A BILLING PHONE NUMBER MUST BE SPECIFIED")) : "" ;  
      (trim($pdta['sdcShippingPhone']) !== "" && !preg_match('/^\(\d{3}\)\s\d{3}-\d{4}(\s[x]\d{1,7})*$/',trim($pdta['sdcShippingPhone']))) ? (list( $errorInd,$msgArr[] )=array( 1 ,"THE SHIPPING PHONE NUMBER IS IN AN INVALID FORMAT.  FORMAT IS (123) 456-7890 x0000")) : ""; 
      (trim($pdta['sdcBillPhone']) !== "" && !preg_match('/^\(\d{3}\)\s\d{3}-\d{4}(\s[x]\d{1,7})*$/',trim($pdta['sdcBillPhone']))) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE BILLING PHONE NUMBER IS IN AN INVALID FORMAT.  FORMAT IS (123) 456-7890 x0000")) : "" ; 
      //7. CHECK SALES ORDER
      //( trim($pdta['sdcShipDocSalesOrder']) !== "" && !is_numeric($pdta['sdcShipDocSalesOrder']) ) ? (list( $errorInd, $msgArr[] ) = array( 1 , "THE SALES ORDER NUMBER MUST BE A NUMERIC VALUE")) : "" ; 

      if ( $errorInd === 0 ) {
        //GET ORIGINAL VALUES
        //$oSQL = "SELECT *  FROM masterrecord.ut_shipdoc where shipdocrefid = :sdrefid";
        //$oRS = $conn->prepare($oSQL); 
        //$oRS->execute(array(':sdrefid' => $sdnbr));
        //$osd = $oRS->fetch(PDO::FETCH_ASSOC);
        //DO DATA BACKUP
        $backupSQL = "insert into masterrecord.history_shipdoc (shipdocrefid,sdstatus,statusdate,acceptedby,acceptedbyemail,ponbr,rqstshipdate,rqstpulldate,comments,investcode,investname,investemail,investinstitution,institutiontype,investdivision,oncreationinveststatus,tqcourierid,courier,couriernbr,shipmentTrackingNbr,shipAddy,shipphone,billAddy,billphone,setupon,setupby,salesorder,SAPified,SOBY,SOON,reconciledInd,reconciledshiplogon,reconciledBy,closedOn,closedBy,surveyEmailSent,lasteditby,lastediton,historyon,historyby) SELECT *, now(), :usr FROM masterrecord.ut_shipdoc where shipdocrefid = :sdnbr";
        $backupRS = $conn->prepare($backupSQL); 
        $backupRS->execute(array( ':usr' => $u['originalaccountname'], ':sdnbr' => (int)$sdnbr)); 
        //WRITE DATA 
        $updSQL = "update masterrecord.ut_shipdoc set acceptedby = :accptedby, acceptedbyemail = :acceptedbyemail, ponbr = :ponbr, rqstshipdate = :rqstshipdate, rqstpulldate = :rqstpulldate, comments = :comments, tqcourierid = :tqcourierid, courier = :courier, couriernbr = :couriernbr, shipAddy = :shipaddy, shipphone = :shipphone, billAddy = :billaddy, billphone = :billphone, lasteditby = :lasteditby, lastediton = now() where shipdocrefid = :sdnbr";
        $updRS = $conn->prepare($updSQL);
        //, salesorder = :salesorder //removed from $updSQL above
        //$so = ( trim($pdta['sdcShipDocSalesOrder']) !== "") ? (int)$pdta['sdcShipDocSalesOrder'] : null;  
        //,':salesorder' => $so (TAKEN OUT OF SQL BELOW ON 2019-05-21)
        $updVal = array(':accptedby' => trim($pdta['sdcAcceptedBy']),':sdnbr' => (int)$sdnbr,':acceptedbyemail' => trim($pdta['sdcAcceptorsEmail']) ,':ponbr' => trim($pdta['sdcPurchaseOrder']),':rqstshipdate' => $pdta['sdcRqstShipDateValue'],':rqstpulldate' => $pdta['sdcRqstToLabDateValue'],':comments' => trim($pdta['sdcPublicComments']),':tqcourierid' => $courierid ,':courier' => $courier,':couriernbr' => $courierNbr,':shipaddy' => trim($pdta['sdcInvestShippingAddress']),':shipphone' => trim($pdta['sdcShippingPhone']),':billaddy' => trim($pdta['sdcInvestBillingAddress']),':billphone' => trim($pdta['sdcBillPhone']),':lasteditby' => trim($u['originalaccountname']) );
        $updRS->execute($updVal); 
        $responseCode = 200; 
      } 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    } 

    function shipmentdocumentdata ( $request, $passdata ) { 
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      if ( $authuser === "chtneast" && $authpw === serverpw ) { 
        require(serverkeys . "/sspdo.zck");
        $pdta = json_decode($passdata, true);
        $sdnbr = cryptservice( $pdta['sdency'] , 'd');

        $sdHeadSQL = "SELECT shipdocrefid, ifnull(sdstatus,'CLOSED') as sdstatus, ifnull(date_format(statusdate,'%m/%d/%Y'),'') as statusdate, ifnull(acceptedby,'') as acceptedby, ifnull(acceptedbyemail,'') as acceptedbyemail, ifnull(ponbr,'') as ponbr,ifnull(date_format(rqstshipdate,'%Y-%m-%d'),'') as rqstshipdateval, ifnull(date_format(rqstshipdate,'%m/%d/%Y'),'') as rqstshipdate, ifnull(date_format(rqstpulldate,'%Y-%m-%d'),'') as rqstpulldateval, ifnull(date_format(rqstpulldate,'%m/%d/%Y'),'') as rqstpulldate, ifnull(comments,'') as comments, ifnull(investcode,'') as investcode, ifnull(investname,'') as investname, ifnull(investemail,'') as investemail, ifnull(investinstitution,'') as investinstitution, ifnull(institutiontype,'') as institutiontype, ifnull(investdivision,'') as investdivision, ifnull(oncreationinveststatus,'') as tqstatusoncreation, ifnull(shipmentTrackingNbr,'') as shipmenttrackingnbr, ifnull(shipAddy,'') as shipmentaddress, ifnull(shipphone,'') as shipmentphone, ifnull(billAddy,'') as billaddress, ifnull(billphone,'') as billphone, ifnull(date_format(setupon,'%m/%d/%Y'),'') as setupon, ifnull(setupby,'') as setupby, ifnull(salesorder,0) as salesorder, ifnull(salesorderamount,0) as salesorderamount, ifnull(SOBY,'') as salesorderby, ifnull(date_format(SOON,'%m/%d/%Y'),'') as salesorderon, ifnull(courier,'') courier, ifnull(couriernbr,'') as couriernbr, ifnull(tqcourierid,0) tqcourierid  FROM masterrecord.ut_shipdoc where shipdocrefid = :sdnbr";
        $sdHeadRS = $conn->prepare($sdHeadSQL);
        $sdHeadRS->execute(array(':sdnbr' => $sdnbr));
        if ( $sdHeadRS->rowCount() > 0 ) { 
          $itemsfound = $sdHeadRS->rowCount();
          while ($s = $sdHeadRS->fetch(PDO::FETCH_ASSOC)) { 
            $dta['sdhead'][] = $s;
          }
     
          $detSQL = "SELECT sdd.shipdocDetId, sdd.shipdocrefId, sg.segmentid, ifnull(date_format(sdd.addon,'%m/%d/%Y'),'') as addtosdon, ifnull(sdd.addedBy,'') as addtosdby,  ifnull(sdd.pulledind,0) as pulledind, ifnull(date_format(sdd.pulledOn,'%m/%d/%Y'),'') as pulledon, ifnull(sdd.pulledby,'') as pulledby , sg.bgs, ucase(trim(concat(ifnull(bs.tissType,''), if(ifnull(bs.anatomicSite,'')='','',concat(' :: ',ifnull(bs.anatomicSite,''))) , if(ifnull(bs.subSite,'')='','', concat(' (',ifnull(bs.subSite,''),')')) , if(ifnull(bs.diagnosis,'')='','',concat(' :: ',ifnull(bs.diagnosis,''))), if(ifnull(bs.subdiagnos,'')='','',concat(' (', ifnull(bs.subdiagnos,''),')')), if(ifnull(bs.metsSite,'')='','',concat(' METS: ',ifnull(bs.metsSite,'')))))) as dxdesig,  if( trim(ifnull(sg.metric,'')) = '','',concat( ifnull(sg.metric,''), ifnull(uom.dspvalue,''))) as metric, concat(ifnull(sg.prepMethod,''), if(ifnull(sg.Preparation,'')='','',concat(' / ',ifnull(sg.Preparation,'')))) preparation, ifnull(sg.qty,1) as qty, ifnull(sg.scannedLocation,'') as inventorylocation FROM masterrecord.ut_shipdocdetails sdd left join masterrecord.ut_procure_segment sg on sdd.segID = sg.segmentId left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'METRIC') uom on sg.metricUOM = uom.menuvalue left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where sdd.shipdocrefid = :sdnbr union SELECT srvcfeeid, shipdocrefid, 0, date_format(inputon, '%m/%d/%Y') as addedtosdon, inputby addedtosdby, 0, '', '', 'Special Service Fee', '', concat('@ $',ifnull(basecharge,'0')) as basecharge, srvfeedsp, concat(ifnull(qtymetric,''),'/$',format(ifnull(totalfee,''),2)), '' FROM masterrecord.ut_shipdoc_spcsrvfee where shipdocrefid = :sdnbra and dspind = 1 ";
          $detRS = $conn->prepare($detSQL); 
          $detRS->execute(array(':sdnbr' => $sdnbr, ':sdnbra' => $sdnbr ));
          if ( $detRS->rowCount() > 0 ) { 
            while ($sd = $detRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta['sddetail'][] = $sd;
            }
          } else { 
              $dta['sddetail'][] = null;
          }
          $responseCode = 200;    
        } else { 
          (list( $errorind, $msgArr[] ) = array(1 , "ERROR:  NO SHIPMENT DOCUMENT FOUND (REQUESTED: " . substr('000000' . $sdnbr,-6) . ")"));
          $responseCode = 404;
        }
      } 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows; 
        
    }
    
    function markqms ( $request, $passdata ) {
      $rows = array(); 
      $responseCode = 503;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $sess = session_id();
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      $authchk = cryptservice($authpw,'d', true, $authuser);
      if ( $authuser !== $authchk || $authuser !== $sess ) {
         (list( $errorInd, $msgArr[] ) = array(1 , "The User's authentication method does not match.  See a CHTNEastern Informatics staff member."));
      } 
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
        $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
      }        
      ( !array_key_exists('qmsaction', $pdta)) ? (list( $errorind, $msgArr[] ) = array(1 , "array key 'qms-action' missing from passed data")) : "";
      ( !array_key_exists('bglabel', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'bg-label' missing from passed data")) : "";
      ( !array_key_exists('furtheraction', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'further-action' missing from passed data")) : "";
      ( !array_key_exists('furtheractionnote', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'further-note' missing from passed data")) : "";
      ( !array_key_exists('moleculartests', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'molecular-tests' missing from passed data")) : "";
      ( !array_key_exists('prcacellmucin', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-acell-mucin' missing from passed data")) : "";
      ( !array_key_exists('prccell', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-cellularity' missing from passed data")) : "";
      ( !array_key_exists('prcepipth', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-epipthelial' missing from passed data")) : "";
      ( !array_key_exists('prcinflame', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-inflammation' missing from passed data")) : "";
      ( !array_key_exists('prcnecro', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-necro' missing from passed data")) : "";
      ( !array_key_exists('prcneoplaststrom', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-neo-plastic-stroma' missing from passed data")) : "";
      ( !array_key_exists('prcnonneoplast', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-non-neo-plastic-stroma' missing from passed data")) : "";
      ( !array_key_exists('prctumor', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'percent-tumor' missing from passed data")) : "";
      ( !array_key_exists('qmsnote', $pdta)) ? (list( $errorInd, $msgArr[] ) = array(1 , "Array Key 'qms-note' missing from passed data")) : "";

      ( $pdta['qmsaction'] !== "L" && $pdta['qmsaction'] !== "Q" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "The only values allowed in the QMS Action field are either 'L' or 'Q'")) : "";
      //TODO:  CONTINUE DATA CHECKS
 
      if ( $errorInd === 0 ) { 
         //DO BACKUPS OF DATA
         $backupSQL = "insert into masterrecord.history_procure_biosample_qms (pbiosample, readlabel, qcvalv2, hprindicator, hprmarkbyon, qcindicator, qcmarkbyon, qcprocstatus, labaction, labactionnote, qmsstatusby, qmsstatuson, hprdecision, hprresultid, slidereviewed, hpron, hprby, historyrecordon, historyrecordby) SELECT pbiosample, read_label, ifnull(qcvalv2,'') as qcval2, ifnull(hprind,0) as hprind, ifnull(hprmarkbyon,'') as hprmarkbyon, ifnull(qcind,0) as qcind, ifnull(qcmarkbyon,'') as qcmarkbyon, ifnull(qcprocstatus,'') as qcprocstatus, ifnull(labactionaction,'') as labactionaction, ifnull(labactionnote,'') as labactionnote, ifnull(qmsstatusby,'') as qmsstatusby, ifnull(qmsstatuson,'') as qmsstatuson, ifnull(hprdecision,'') as hprdecision, ifnull(hprresult,'') as hprresult, ifnull(hprslidereviewed,'') as hprslidereviewed, hpron as hpron, ifnull(hprby,'') as hprby, now(), :usr as usr FROM masterrecord.ut_procure_biosample where replace(read_label,'_','') = :readlabel";
        $backupRS = $conn->prepare($backupSQL); 
        $backupRS->execute(array(':readlabel' => $pdta['bglabel'], ':usr' => $u['originalaccountname'])); 

        switch ( $pdta['qmsaction'] ) { 
          case 'Q':

              $moTests = json_decode($pdta['moleculartests'], true);
              $moSQL = "update masterrecord.ut_procure_biosample_molecular set dspind = 0, updatedby = :usr, updatedon = now() where replace(bgprcnbr,'_','') = :readlabel";
              $moR = $conn->prepare($moSQL); 
              $moR->execute(array(':readlabel' => $pdta['bglabel'], ':usr' => $u['originalaccountname'] ));
            
              $moInsSQL = "insert into masterrecord.ut_procure_biosample_molecular (bgprcnbr, testid, testresultid, molenote, onwhen, onby, dspind) value (:readlabel, :testid, :testresultid, :molenote, now(), :onby, 1)";
              $moIns = $conn->prepare($moInsSQL);
              if ( count($moTests) > 0 ) { 
                foreach ($moTests  as $key => $value ) { 
                    $moIns->execute(array(
                        ':readlabel' => $pdta['bglabel']
                      , ':testid' => $value[0]
                      , ':testresultid' => $value[2]
                      , ':molenote' => trim($value[4])
                      , ':onby' => $u['originalaccountname'] 
                   ));
                }
              }

              $prcUpdSQL = "update masterrecord.ut_procure_biosample_samplecomposition set dspind = 0, updateon = now(), updateby = :usr where readlabel = :readlabel";
              $prcUpdR = $conn->prepare($prcUpdSQL); 
              $prcUpdR->execute(array(':usr' => $u['originalaccountname'], ':readlabel' => $pdta['bglabel']));

              $prcInsSQL = "insert into masterrecord.ut_procure_biosample_samplecomposition (readlabel, prctype, prcvalue, dspind, inputon, inputby) value (:readlabel, :prctype, :prcvalue, 1, now(), :inputby)";
              $prcIns = $conn->prepare($prcInsSQL);
              if ( trim($pdta['prctumor']) !== "" && is_numeric($pdta['prctumor'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-TMR', ':prcvalue' => $pdta['prctumor'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prccell']) !== "" && is_numeric($pdta['prccell'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-CELL', ':prcvalue' => $pdta['prccell'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcnecro']) !== "" && is_numeric($pdta['prcnecro'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-NECR', ':prcvalue' => $pdta['prcnecro'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcacellmucin']) !== "" && is_numeric($pdta['prcacellmucin'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-ACEL', ':prcvalue' => $pdta['prcacellmucin'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcneoplaststrom']) !== "" && is_numeric($pdta['prcneoplaststrom'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-NEOP', ':prcvalue' => $pdta['prcneoplaststrom'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcnonneoplast']) !== "" && is_numeric($pdta['prcnonneoplast'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-NNEO', ':prcvalue' => $pdta['prcnonneoplast'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcepipth']) !== "" && is_numeric($pdta['prcepipth'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-EPIP', ':prcvalue' => $pdta['prcepipth'], ':inputby' => $u['originalaccountname'])); }
              if ( trim($pdta['prcinflame']) !== "" && is_numeric($pdta['prcinflame'])) { $prcIns->execute(array(':readlabel' => $pdta['bglabel'], ':prctype' => 'PRC-INFLM', ':prcvalue' => $pdta['prcinflame'], ':inputby' => $u['originalaccountname'])); }

              //WRITE DATA
              $laSQL = "update masterrecord.ut_procure_biosample set qcvalv2 = '', qcind = 1, qcmarkbyon = :u, qcprocstatus = :qmsstat, qmsstatusby = :usr, qmsnote = :qmsnote, qmsstatuson = now() where replace(read_label,'_','') = :readlabel";
              $laR = $conn->prepare($laSQL); 
              $laR->execute(array(':readlabel' => $pdta['bglabel'], ':usr' => $u['originalaccountname'], ':u' => date('Y-m-d') . " {$u['originalaccountname']}", ':qmsnote' => $pdta['qmsnote'] ,':qmsstat' => $pdta['qmsaction'] ));
              $responseCode = 200;

              break;
          case 'L':
              //WRITE DATA
              //TODO: ASK DEE IF ALREADY QC'ed BIOSAMPLES SHOULD BE ALLOWED TO BE PUT BACK IN A STATE OF LAB ACTION
              $laSQL = "update masterrecord.ut_procure_biosample set qcvalv2 = '', qcind = 0, qcmarkbyon = '', qcprocstatus = :qmsstat, qmsstatusby = :usr, qmsstatuson = now(), labactionaction = :laval, labactionnote = :lanote where replace(read_label,'_','') = :readlabel";
              $laR = $conn->prepare($laSQL); 
              $laR->execute(array(':readlabel' => $pdta['bglabel'], ':lanote' => $pdta['furtheractionnote'], ':laval' => $pdta['furtheraction'], ':usr' => $u['originalaccountname'], ':qmsstat' => $pdta['qmsaction']));
              $responseCode = 200;
            break;
        }

      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    } 

    function getbgqmsstat( $request, $passdata ) { 
      $rows = array(); 
      //$dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      $pdta = json_decode($passdata, true);
      $qmssql = "SELECT ifnull(read_label,'') as readlabel, ifnull(bs.hprind,0) as hprind, ifnull(bs.hprmarkbyon,'') as hprmarkbyon, ifnull(bs.QCInd,0) as qcind, ifnull(bs.qcmarkbyon,'') as qcmarkbyon, ifnull(bs.qcprocstatus,'') as qcprocstatus, ifnull(qc.longvalue,'') as qcprocstatusdsp, ifnull(bs.qmsstatusby,'') as qmsstatusby, ifnull(date_format(bs.qmsstatuson,'%m/%d/%Y'),'') as qmsstatuson, ifnull(bs.hprdecision,'') as hprdecision, ifnull(bs.hprresult,'') as hprresult, ifnull(bs.hprslidereviewed,'') as hprslidereviewed, ifnull(bs.hprby,'') as hprby, ifnull(date_format(bs.hpron,'%m/%d/%Y'),'') as hpron , ifnull(hpr.reviewer,'') as hprreviewer, ifnull(date_format(hpr.reviewedon,'%m/%d/%Y'),'') as hprreviewedon, ucase(ifnull(hpr.decision,'')) as hprdecision  FROM masterrecord.ut_procure_biosample bs left join (SELECT  menuvalue, longvalue FROM four.sys_master_menus where menu = 'QMSStatus') qc on bs.qcprocstatus = qc.menuvalue left join masterrecord.ut_hpr_biosample hpr on bs.hprresult = hpr.biohpr where read_label like :likebg order by bs.read_label";
      $qmsRS = $conn->prepare($qmssql);
      $qmsRS->execute(array(':likebg' => "{$pdta['bglookup']}%"));
      if ( $qmsRS->rowCount() < 1) { 
          //ERROR - SHOULD ALWAYS HAVE AT LEAST ONE
      } else { 
          $itemsfound = $qmsRS->rowCount();
          while ($r = $qmsRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
      }
      
      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;       
    }
     
    function pristinebarcoderun ( $request, $passdata ) { 
      $rows = array(); 
      $dta = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      //,"DATA":"{\"presentinstitution\":\"HUP\",\"requesteddate\":\"2019-06-24\",\"usrsession\":\"caid05p3oaurs9fmvdnpnfuu46\"
      $pdta = json_decode($passdata, true);
      $authuser = $_SERVER['PHP_AUTH_USER']; 
      $authpw = $_SERVER['PHP_AUTH_PW'];      
      ////TODO: CHECK USER FOR PRISTINE BARCODE FUNCTION
      ( $authuser !== 'chtneast') ?   ( list ($errorInd, $msgArr[]) = array(1,'THE SERVER DID NOT IDENTIFY ITSELF CORRECTLY')) : "";
      
      $chkSQL = "SELECT emailaddress, friendlyname, originalaccountname FROM four.sys_userbase where sessionid = :sessid and allowind = 1 and allowproc = 1 and presentinstitution = :presentinst";    
      $chkRS = $conn->prepare($chkSQL);
      $chkRS->execute(array(':sessid' => $pdta['usrsession'], ':presentinst' => $pdta['presentinstitution'] ));
      ( $chkRS->rowCount() < 1) ?  ( list ($errorInd, $msgArr[]) = array(1,'USER NOT FOUND OR NOT ALLOWED ACCESS TO PROCUREMENT')) : "";
      
      if ($errorInd === 0 ) {
          $u = $chkRS->fetch(PDO::FETCH_ASSOC);
          $dataSQL = "SELECT sg.pbiosample, sg.bgs, sg.prp, sg.prpmet, sg.metric, uom.longvalue, bs.*, px.*  FROM four.ut_procure_segment sg  left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') as uom on sg.metricuom = uom.menuvalue left join (SELECT pbiosample, speccat, primarysite, classification, primarysubsite, diagnosis, diagnosismodifier, metssite, systemdiagnosis FROM four.ref_procureBiosample_designation where activeind = 1) as bs on sg.pbiosample = bs.pbiosample left join (SELECT pbiosample, pxirace, pxigender, pxiage, pxiAgeUOM FROM four.ref_procureBiosample_PXI px where activeind = 1) px on sg.pbiosample = px.pbiosample where sg.procuredat = :presinst and date_format(inputon, '%Y-%m-%d') = :procdte and (prp <> 'SLIDE' and prp <> 'PB') and activeind = 1 and voidind <> 1 ";
          $dataRS = $conn->prepare($dataSQL); 
          $dataRS->execute(array(':presinst' => $pdta['presentinstitution'], ':procdte' => $pdta['requesteddate'] ));
          $itemsfound = $dataRS->rowCount(); 
          while ( $r = $dataRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
        $responseCode = 200;
      }      
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;   
    }

    function inventorybarcodestatus ( $request, $passdata ) {   
      $rows = array(); 
      $responseCode = 400;
      $msgArr = array(); 
      $errorInd = 0;
      $msg = "BAD REQUEST";
      $itemsfound = 0;
      require(serverkeys . "/sspdo.zck");
      session_start();
      $sess = session_id();
      $pdta = json_decode($passdata, true);

      $bc = $pdta['barcode'];
      if ( strtoupper(substr($pdta['barcode'],0,3)) === 'FRZ' ) { 
          //LOOKUP CONTAINER
          $locLookupSQL = "SELECT mainscan.typeolocation, mainscan.scancode, mainscan.locationnote, ifnull(lvl1up.locationnote,'') as lvl1parent,  ifnull(lvl2up.locationnote,'') as lvl2parent FROM four.sys_inventoryLocations mainscan left join ( select locationid, locationnote, parentid from four.sys_inventoryLocations) as lvl1up on mainscan.parentId = lvl1up.locationid left join ( select locationid, locationnote, parentid from four.sys_inventoryLocations) as lvl2up on lvl1up.parentId = lvl2up.locationid where mainscan.scancode = :locscancode and activelocation = 1"; 
          $locRS = $conn->prepare($locLookupSQL); 
          $locRS->execute(array(':locscancode' => $bc));
          if ( $locRS->rowCount() === 1 ) { 
              while ( $r = $locRS->fetch(PDO::FETCH_ASSOC) ) { 
                $dta = $r;
              }
              $dta['scantype'] = "LOCATION";
              $itemsfound = 1;
          } else { 
           //location error
           $dta['scantype'] = "LOCATION";
           $itemsfound = 0;
          }          
      } else { 
          //LOOK UP SAMPLE STATUS
          $bc = $pdta['barcode'];
          $biosampSQL = "SELECT replace(sg.bgs,'_','') as bgs, upper(ifnull(sg.segstatus,'')) as segstatus, upper(ifnull(sg.prepmethod,'')) as prepmethod, concat(ifnull(bs.tisstype,''), if(ifnull(bs.anatomicsite, '')='','',concat('::',ifnull(bs.anatomicsite, ''))), if(ifnull(bs.diagnosis,'')='','', concat('::',ifnull(bs.diagnosis,''))), if(ifnull(bs.subdiagnos,'')='','',concat('::',ifnull(bs.subdiagnos,'')))) as dx FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where replace(sg.bgs,'_','') = :scanbc";
          $biosampRS = $conn->prepare($biosampSQL);
          $biosampRS->execute(array(':scanbc' => $bc));
          if ( $biosampRS->rowCount() === 1) { 
              while ($r = $biosampRS->fetch(PDO::FETCH_ASSOC)) { 
                  $dta = $r;
              }
              $dta['scantype'] = "BIOSAMPLE";
              $itemsfound = 1;
          } else { 
              //BIOSAMPLE ERROR
              
          }
          
          
      }
      $responseCode = 200; 
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
 
    function coordinatoraddsegment ( $request, $passdata ) { 
      $rows = array(); 
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
      $chkUsrSQL = "SELECT originalaccountname, presentinstitution FROM four.sys_userbase where allowind = 1 and allowCoord = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
      $chkUsrR = $conn->prepare($chkUsrSQL); 
      $chkUsrR->execute(array(':sess' => $sess));
      if ($chkUsrR->rowCount() <> 1) { 
        (list( $errorInd, $msgArr[] ) = array(1 , "Authentication Error:  Either your Session has expired, your password has expired, or don't have access to the coordinator function"));
      } else { 
        $u = $chkUsrR->fetch(PDO::FETCH_ASSOC);
      }        
      
      //DATA CHECKS        
      ( trim($pdta['bgNum']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ERROR:  NO BIOGROUP SPECIFIED - SEE A CHTNEASTERN INFORMATICS PERSON.")) : "";
      ( !$pdta['noParentInd'] && trim($pdta['parentSegment']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "IF NO PARENT SEGMENT IS SPECIFIED YOU MUST EXPLICITLY MARK THE 'NO PARENT' CHECK BOX")) : "";  
      if ( trim($pdta['preparationMethodValue']) === "") { 
         (list( $errorInd, $msgArr[] ) = array(1 , "A PREPARATION METHOD IS REQUIRED")); 
      } else { 
        $prpChkSQL = "SELECT menuid FROM four.sys_master_menus where menu = :mnu and menuvalue = :prepmet";
        $prpChkRS = $conn->prepare($prpChkSQL);
        $prpChkRS->execute(array(':mnu' => 'PREPMETHOD', ':prepmet' => $pdta['preparationMethodValue']));
        if ($prpChkRS->rowCount() < 1) { 
          (list( $errorInd, $msgArr[] ) = array(1 , "PREPARATION METHOD ({$pdta['preparationMethod']}) NOT FOUND AS A VALID MENU OPTION."));
        } else { 
           $prp = $prpChkRS->fetch(PDO::FETCH_ASSOC);
           if (  trim($pdta['preparationValue']) === ""  ) { 
               (list( $errorInd, $msgArr[] ) = array(1 , "A PREPARATION IS REQUIRED")); 
           } else { 
               $prpDChkSQL = "SELECT * FROM four.sys_master_menus where menu = :mnu and parentid = :parentid and menuvalue = :prpdvalue";
               $prpDRS = $conn->prepare($prpDChkSQL); 
               $prpDRS->execute(array(':mnu' => "PREPDETAIL", ':parentid' => $prp['menuid'], ':prpdvalue' => $pdta['preparationValue']));
               if ( $prpDRS->rowCount() < 1 ) { 
                   (list( $errorInd, $msgArr[] ) = array(1 , "PREPARATION DETAIL ({$pdta['preparationValue']}) WAS NOT FOUND AS A CHILD PREPARATION FOR THE PREPARATION METHOD"));
               }
           }
        }
      }
      
      ( strtoupper(trim($pdta['preparationMethodValue'])) !== 'SLIDE' && trim($pdta['addMetric']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FOR ALL PREPARATIONS BUT SLIDES, A METRIC MUST BE STATED")) : "";
      ( strtoupper(trim($pdta['preparationMethodValue'])) !== 'SLIDE' && !is_numeric( trim($pdta['addMetric'])) ) ? (list( $errorInd, $msgArr[] ) = array(1 , "METRIC VALUES MUST BE NUMERIC")) : "";
      ( strtoupper(trim($pdta['preparationMethodValue'])) !== 'SLIDE' && !(floatval($pdta['addMetric']) > 0) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC VALUE MUST BE GREATER THAN ZERO")) : ""; 
      ( strtoupper(trim($pdta['preparationMethodValue'])) !== 'SLIDE' && trim($pdta['addMetricUOMValue']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "FOR ALL PREPARATIONS BUT SLIDES, A METRIC UNIT OF MEASURE (UOM) MUST BE STATED")) : "";
      
      if ( trim($pdta['addMetricUOMValue']) !== "" ) { 
          $chkMetSQL = "SELECT menuid FROM four.sys_master_menus where menu = :mnu  and menuvalue = :val";
          $chkMetRS = $conn->prepare($chkMetSQL);
          $chkMetRS->execute(array(':mnu' => 'METRIC', ':val' => $pdta['addMetricUOMValue'] ));
          if ( $chkMetRS->rowCount() < 1) {
             (list( $errorInd, $msgArr[] ) = array(1 , "THE METRIC UNIT OF MEASURE (UOM) IS NOT A VALID MENU OPTION"));
          }
      }
      
      if ( $pdta['preparationContainer'] !== "" ) {
          $chkSQL = "SELECT menuid FROM four.sys_master_menus where menu = :mnu and dspvalue = :val";
          $chkRS = $conn->prepare($chkSQL);
          $chkRS->execute(array(':mnu' => 'CONTAINER', ':val' => $pdta['preparationContainer']));
          ( $chkRS->rowCount() < 1 ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE CONTAINER VALUE ({$pdta['preparationContainer']}) IS NOT A VALID MENU OPTION")) : "";
      }
      
      ( trim($pdta['assignInv']) === "" ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE ASSIGNMENT CANNOT BE BLANK")) : "";
      ( trim($pdta['assignInv']) !== "" && strtoupper(trim($pdta['assignInv'])) !== "BANK" && strtoupper(substr(trim($pdta['assignInv']),0,3)) !== 'INV' ) ? (list( $errorInd, $msgArr[]) = array(1 ,"THE ASSIGNMENT FIELD MUST BE 'BANK' OR A VALID INVESTIGATOR CODE")) : "";
     
      if ( trim($pdta['assignInv']) !== "" && strtoupper(trim($pdta['assignInv'])) !== "BANK"  && strtoupper(substr(trim($pdta['assignInv']),0,3)) === 'INV' ) { 
         $invSQL = "SELECT ucase(concat(ifnull(invest_lname,''),', ', ifnull(invest_fname,''))) as investname FROM vandyinvest.invest where investid = :icode";
         $invRS = $conn->prepare($invSQL); 
         $invRS->execute(array(':icode' => strtoupper(trim($pdta['assignInv']))));
         if ( $invRS->rowCount() < 1 ) { 
             (list( $errorInd, $msgArr[] ) = array(1 , "INVESTIGATOR (" . strtoupper($pdta['assignInv']) . ") NOT FOUND.  IF YOU BELIEVE THIS TO BE IN ERROR, SEE A CHTNEASTERN INFORMATICS PERSON"));
         } else { 
             $inv = $invRS->fetch(PDO::FETCH_ASSOC);
             $i = $inv['investname'];
         }
      }
      if ( strtoupper(trim($pdta['assignInv'])) === 'BANK' ) {  $i = "BANK"; } 
      ( strtoupper(trim($pdta['assignInv'])) !== "BANK" && substr(strtoupper(trim($pdta['assignReq'])),0,3) !== 'REQ'  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE REQUEST NUMBER MUST BE REQ##### ")) : "";
      if ( ( trim($pdta['assignInv']) !== "" && strtoupper(trim($pdta['assignInv'])) !== 'BANK' ) && trim($pdta['assignReq']) !== "" ) {
        $iChkSQL = "SELECT rq.requestid, pr.projid, i.investid FROM vandyinvest.investtissreq rq left join vandyinvest.investproj pr on rq.projid = pr.projid left join vandyinvest.invest i on pr.investid = i.investid where requestid = :rq and i.investid = :iv";
        $iRS = $conn->prepare($iChkSQL); 
        $iRS->execute(array(':rq' => strtoupper(trim($pdta['assignReq'])), ':iv' => strtoupper(trim($pdta['assignInv']))  ));
        ( $iRS->rowCount() < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE INVESTIGATOR/REQUEST COMBINATION SUPPLIED IS INVALID ({$pdta['assignInv']}/{$pdta['assignReq']}).  IF YOU FEEL THIS IS IN ERROR, SEE A CHTNEASTERN INFORMATICS PERSON")) : "";
      } 

      ( trim($pdta['definitionRepeater']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'REPEAT' VALUE MUST NOT BE BLANK ")) : "";
      ( !is_numeric($pdta['definitionRepeater'])) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'REPEAT' VALUE MUST BE A NUMBER")) : "";
      ( (int)$pdta['definitionRepeater'] < 1 || (int)$pdta['definitionRepeater'] > 124 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'REPEAT' VALUE CAN ONLY BE BETWEEN 1 AND 125")) : "";
      
      //CHECK CONSUMED/PARENT IF SPECIFIED
      if (  trim($pdta['parentSegment'] ) !== '' && $pdta['parentExhaustedInd'] ) { 
        $segChkSQL = "SELECT ifnull(prepmethod,'') as prepmethod, ifnull(preparation,'') as preparation, ifnull(segstatus,'') as segstatus, ifnull(shipdocrefid,'') as shipdocrefid, ifnull(shippeddate,'') as shippeddate FROM masterrecord.ut_procure_segment where replace(bgs,'_','') = :bgs"; 
        $segChkRS = $conn->prepare($segChkSQL); 
        $segChkRS->execute(array(':bgs' => $pdta['parentSegment'] ));
        $segChk = $segChkRS->fetch(PDO::FETCH_ASSOC);   
        //trim($segChk['segstatus']) !== 'ASSIGNED' &&
        ( trim($segChk['prepmethod']) === 'SLIDE' || trim($segChk['prepmethod']) === 'MICRO' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED PARENT SEGMENT ({$pdta['parentSegment']}) MAY NOT HAVE A PREPARATION METHOD OF 'SLIDE' OR 'MICRO-ARRAY' ")) : "";
        (  trim($segChk['segstatus']) !== 'BANKED' && trim($segChk['segstatus']) !== 'PERMCOLLECT' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED PARENT SEGMENT ({$pdta['parentSegment']} / {$segChk['segstatus']} ) IS NOT STATUSED TO ALLOW MODIFICATION ( BANKED/PERMANENT COLLECTION) ")) : "";
        ( trim($segChk['shipdocrefid']) !== '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED PARENT SEGMENT ({$pdta['parentSegment']}) IS LISTED ON A SHIP-DOC (" . substr(('000000' . $segChk['shipdocrefid']),-6) . ")")) : "";
        ( trim($segChk['shippeddate']) !== '' ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED PARENT SEGMENT ({$pdta['parentSegment']}) HAS ALREADY BEEN SHIPPED")) : "";          
      }
      
      
      //CHECK SLIDE PARENT ON HISTO SHEET  UPDATE SQL ONLY UPDATES PARENT SO CHECK NOT NECESSARY
      if ( $pdta['addToHisto'] ) { 
           
          
          
      }
      
      if ( $pdta['printSlideInd'] === 1 ) { 
        (list( $errorInd, $msgArr[] ) = array(1 , " AT THIS TIME, THE PRINT FUNCTION IS NOT OPERATIONAL!"));
      }         
      
      if ( $errorInd === 0 ) { 
        //SAVE SEGMENTS
        $cntr = 0;  
//        $nxtSegSQL = "SELECT CAST(max(segmentlabel) AS UNSIGNED) as topSegment FROM masterrecord.ut_procure_segment where biosamplelabel = :pbiosample";
        $nxtSegSQL = "SELECT ifnull(CAST(max(segmentlabel) AS UNSIGNED),0) as topSegment FROM masterrecord.ut_procure_segment where biosamplelabel = :pbiosample and ( cast(segmentlabel as unsigned) * 1 ) > 0"; 
        $nxtRS = $conn->prepare($nxtSegSQL);
      
        $entrySQL = "insert into masterrecord.ut_procure_segment(biosampleLabel, SegmentLabel, bgs, segStatus, statusDate, statusBy, HoursPost, Metric, metricUOM, prepMethod, Preparation, QTY, assignedTo, assignedReq, assignmentdate, assignedby, procurementDate, SlideGroupID, enteredBy, enteredOn, internalComments, segmentComments, slideFromBlockId, procuredAt, prpcontainer) values (:biosampleLabel, :segmentLabel, :bgs, 'ONOFFER', now(), :statusBy, :hoursPost, :metric, :metricUOM, :prepMethod, :preparation, 1, :assignedTo, :assignedReq, now(), :assignedby, now(), :slideGroupID, :enteredBy, now(), 'MASTERRECORD COORDINATOR SCREEN ENTRY', :segmentComments, :slideFromBlockId, :procuredAt, :prpcontainer)";
        $entryRS = $conn->prepare($entrySQL);

        //PARENT SEGMENT HOURS POST
        $bgReadLabelSQL = "SELECT replace(read_Label,'_','') as readlabel, ifnull(anatomicsite,'') as asite, ifnull(tisstype,'') as speccat FROM masterrecord.ut_procure_biosample where pbiosample = :bgnum";
        $bgReadLabelRS = $conn->prepare($bgReadLabelSQL); 
        $bgReadLabelRS->execute(array(':bgnum' => $pdta['bgNum'] ));
        $bgReadLabel = $bgReadLabelRS->fetch(PDO::FETCH_ASSOC);

        if ( trim($pdta['parentSegment']) === "" ) { 
          $parentHRPost = 0;
        } else {
          $hrpostSQL = "SELECT ifnull(HoursPost,0) as hourspost FROM masterrecord.ut_procure_segment where replace(bgs,'_','') = :segment";           
          $hrpostRS = $conn->prepare($hrpostSQL);
          $hrpostRS->execute(array(':segment' => trim($pdta['parentSegment'])));
          if ( $hrpostRS->rowCount() < 1 ) { 
            $parentHRPost = 0;
          } else { 
            $hrpost = $hrpostRS->fetch(PDO::FETCH_ASSOC);
            $parentHRPost = $hrpost['hourspost'];   
          }  
        } 

        $slideGroup = generateRandomString(15);

        $nxtRS->execute(array(':pbiosample' => $pdta['bgNum']));
        $nxt = $nxtRS->fetch(PDO::FETCH_ASSOC);
        while ( $cntr < intval($pdta['definitionRepeater'])) {  
           $newBGS = $bgReadLabel['readlabel'] . substr(('000' . (((int)$nxt['topSegment'] + $cntr) + 1)),-3);
           $entryRS->execute(array(
              ':biosampleLabel' => $pdta['bgNum']  
             ,':segmentLabel' => substr(('000' . (((int)$nxt['topSegment'] + $cntr)  + 1)),-3)
             ,':bgs' =>  $newBGS
             ,':statusBy' => $u['originalaccountname']  
             ,':hoursPost' => $parentHRPost
             ,':metric' => trim($pdta['addMetric'])
             ,':metricUOM' => (int)$pdta['addMetricUOMValue']
             ,':prepMethod' => $pdta['preparationMethodValue']
             ,':preparation' => $pdta['preparationValue']
             ,':assignedTo' => $pdta['assignInv']
             ,':assignedReq' => $pdta['assignReq']
             ,':assignedby' => $u['originalaccountname']
             ,':slideGroupID' => $slideGroup
             ,':enteredBy' => $u['originalaccountname']
             ,':segmentComments' => $pdta['segComments']
             ,':slideFromBlockId' => trim($pdta['parentSegment'])
             ,':procuredAt' => $u['presentinstitution']
             ,':prpcontainer' => $pdta['preparationContainerValue']
           ));
           $cntr++;
        }
       
      if ( $pdta['parentExhaustedInd'] ) { 
          //MARK PARENT PENDING DESTROY
          $histSQL = "insert into masterrecord.history_procure_segment_status (segmentid, previoussegstatus, previoussegstatusupdater, previoussegdate, enteredon, enteredby) SELECT segmentId, segstatus, statusby, statusdate, now(), :usr FROM masterrecord.ut_procure_segment where replace(bgs,'_','') = :parentbgs";
          $histRS = $conn->prepare($histSQL);
          $histRS->execute(array(':usr' => $u['originalaccountname'], ':parentbgs' => trim($pdta['parentSegment'])));
          //TODO:  MAKE THIS SEGMENT STATUS 'PENDDEST' DYNAMIC
          $updSQL = "update masterrecord.ut_procure_segment set segStatus = 'CONSUMED', statusdate = now(), statusBy = :bywho  where replace(bgs,'_','') = :parentbgs";
          $updRS = $conn->prepare($updSQL); 
          $updRS->execute(array(':bywho' => $u['originalaccountname'], ':parentbgs' => trim($pdta['parentSegment']))); 
      }

      //ADD TO HISTO SHEET
      if ( $pdta['addToHisto'] ) {          
          if ( trim($pdta['preparationMethodValue']) === 'SLIDE' ) { 
              //ADD SLIDE TO PARENT
              $prepSQL = "SELECT longvalue FROM four.sys_master_menus where menu = 'PREPDETAIL' and menuvalue = :slidetype"; 
              $prepRS = $conn->prepare($prepSQL);
              $prepRS->execute(array(':slidetype' => $pdta['preparationValue']  ));
              $prep = $prepRS->fetch(PDO::FETCH_ASSOC);
              
              $updSQL = "update four.ut_tmp_histoadd set nbrofslides = :nbrofslides, slidetype = :slideType  where concat(ifnull(casebg,''),ifnull(casesegment,'')) = :parentnbr ";
              $updRS = $conn->prepare( $updSQL ); 
              $updRS->execute ( array ( ':nbrofslides' => (int)$pdta['definitionRepeater'], ':slideType' => $prep['longvalue'], ':parentnbr' => trim($pdta['parentSegment']) ));
          } else { 
              if ( trim($pdta['preparationMethodValue']) === 'PB') {
                $insSQL ="insert into four.ut_tmp_histoadd ( casebg, casesegment, label, specat, nbrofblocks, addedby, addedon, dspind) values( :casebg, :casesegment, :label, :speccat, :nbrofblocks, :addedby, now(), :dspind)";
                $insRS = $conn->prepare( $insSQL );
                $insRS->execute( array( ':casebg' =>  $bgReadLabel['readlabel']    ,':casesegment' => substr(('000' . ((int)$nxt['topSegment'] + 1)),-3) ,':label' => $bgReadLabel['asite']  ,':speccat' => $bgReadLabel['speccat'],':nbrofblocks' => (int)$pdta['definitionRepeater'] ,':addedby' => $u['originalaccountname'],':dspind' => 1 ));
              }
          }

      }
                
        $responseCode = 200;   
      }

      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;        
    }
    
    function dialogactionbgdefinitiondesignationsave ( $request, $passdata ) { 
      $rows = array(); 
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

      //DATA CHECKS
      //{"refbg":"87106","speccat":"MALIGNANT","collectedsite":"THYROID","collectedsubsite":"","diagnosismodifier":"CARCINOMA :: FOLLICULAR","metsfromsite":"LYMPH NODE","siteposition":"","systemicdx":""}
      ( trim($pdta['refbg']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "NO BIOGROUP SPECIFIED")) : "";
      //IF NOT MALIGNANT - NO METS SITE
      ( trim($pdta['metsfromsite']) !== "" && strtoupper(trim($pdta['speccat'])) !== "MALIGNANT" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "TO SPECIFY A \"METS FROM\" SITE, A BIOSAMPLE MUST BE MALIGNANT.")) : "";
      //MAKE SURE ALLOWABLE CHTN VOCAB
      ( trim($pdta['speccat']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "A 'SPECIMEN CATEGORY' IS REQUIRED")) : "";
      ( trim($pdta['collectedsite']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE 'COLLECTED SITE' IS REQUIRED")) : "";
      ( trim($pdta['collectedsubsite']) !== "" && trim($pdta['collectedsite']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFYING A SUB-SITE, THE COLLECTED SITE MUST BE SPECIFIED")) : "";

      if ($errorInd === 0) { 
          //{"speccat":"MALIGNANT","psite":"BLADDER","subsite":"SEROSA","dx":"CARCINOMA :: UROTHELIAL (TRANSITIONAL CELL)","metssite":"KIDNEY","siteposition":"LEFT","pdxsystemic":"ZACKITIS"}
          $chkArr['speccat'] = strtoupper(trim($pdta['speccat']));
          $chkArr['psite'] = strtoupper(trim($pdta['collectedsite']));
          $chkArr['subsite'] = strtoupper(trim($pdta['collectedsubsite']));
          $chkArr['dx'] = strtoupper(trim($pdta['diagnosismodifier']));
          $chkArr['metssite'] = strtoupper(trim($pdta['metsfromsite']));
          $chkArr['siteposition'] = strtoupper(trim($pdta['siteposition']));
          $chkArr['pdxsystemic'] = strtoupper(trim($pdta['systemicdx']));
          $vocchk = self::validatechtnvocabulary( "", json_encode($chkArr));          
          if ( $pdta['dxoverride']) {
          } else {
            ( (int)$vocchk['data']['DATA']['mainvocchk'] === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "MAIN DIAGNOSIS DESIGNATION DOES NOT EXIST IN THE OFFICIAL CHTN NETWORK VOCABULARY TABLES")) : "";
          }
          ( (int)$vocchk['data']['DATA']['systemicvocchk'] === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "SYSTEMIC DIAGNOSIS DOES NOT EXIST IN THE OFFICIAL CHTN NETWORK VOCABULARY TABLES")) : "";
          ( (int)$vocchk['data']['DATA']['metsvocchk'] === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE METS FROM DESIGNATION IS NOT A VALID CHTN NETWQORK VOCABULARY VALUE")) : "";
      }



      if ( $errorInd === 0 ) {
         //MAKE DATA BACKUP   
         $bckSQL = "insert into masterrecord.history_procure_biosample_vocab (pbiosample, uninvolvedind, tumorgrade, tumorscale, speccat, collectedsite, subsite, siteposition, diagnosis, modifier, metssite, systemicdx, bywho, onwhen) SELECT pBioSample, uninvolvedind, tumorgrade, tumorscale, ifnull(tissType,'') as tisstype, ifnull(anatomicSite,'') as site, ifnull(subSite,'') as subsite, ifnull(sitePosition,'') as siteposition, ifnull(diagnosis,'') as diagnosis, ifnull(subdiagnos,'') as modifier, ifnull(metsSite,'') as metssite, ifnull(pdxSystemic,'') as systemic, :user, now() FROM masterrecord.ut_procure_biosample where read_label = replace(:bg,'_','')"; 
        $bckRS = $conn->prepare($bckSQL);
        $bckRS->execute(array(':bg' => $pdta['refbg'], ':user' => $u['originalaccountname']));

        $dxhld = explode(" :: ", $pdta['diagnosismodifier']);
        $updSQL = "update masterrecord.ut_procure_biosample set tissType = :sp, anatomicSite = :st, subSite = :sst, siteposition = :pos, diagnosis = :dx, subdiagnos = :mod, metsSite = :mets, pdxSystemic = :systemic where pbiosample = :bg";
        $updRS = $conn->prepare($updSQL);
        $updRS->execute(array(
           ':bg' => $pdta['refbg']
          ,':sp' => strtoupper(trim($pdta['speccat']))
          ,':st' =>  strtoupper(trim($pdta['collectedsite']))
          ,':sst' =>  strtoupper(trim($pdta['collectedsubsite']))
          ,':pos' =>  strtoupper(trim($pdta['siteposition']))
          ,':dx' =>  strtoupper(trim($dxhld[0])) 
          ,':mod' =>  strtoupper(trim($dxhld[1]))
          ,':mets' =>  strtoupper(trim($pdta['metsfromsite']))
          ,':systemic' =>  strtoupper(trim($pdta['systemicdx']))   
        ));

        $responseCode = 200;
      }


      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;           
    }

    function dialogactionbgdefinitionencountersave ( $request, $passdata ) { 
      $rows = array(); 
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

      //DATA CHECKS - MAKE SURE ALL VALUES ARE APPROPRIATE
      ( !preg_match('/^[0-9]{1,2}?/',$pdta['agemetric']) ) ?  (list( $errorInd, $msgArr[] ) = array(1 , "THE AGE MUST BE SPECIFIED AS A EITHER A ONE OR TWO DIGITAL NUMBER")) : "";

      $valChkSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'AGEUOM' and dspind = 1 and dspvalue = :chkval";  
      $chkRS = $conn->prepare($valChkSQL); 
      $chkRS->execute( array( ':chkval' => $pdta['agemetricuom'] ));
      if ($chkRS->rowCount() > 0) { 
        $rs = $chkRS->fetch(PDO::FETCH_ASSOC); 
        $ageuomval = $rs['menuvalue'];
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THIS AGE METRIC UNIT OF MEASURE VALUE ({$pdta['agemetricuom']}) WAS NOT FOUND IN THE DATA TABLES"));
      }

      $valChkSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'PXRACE' and dspind = 1 and dspvalue = :chkval";  
      $chkRS = $conn->prepare($valChkSQL); 
      $chkRS->execute( array( ':chkval' => $pdta['race'] ));
      if ($chkRS->rowCount() > 0) { 
        $rs = $chkRS->fetch(PDO::FETCH_ASSOC); 
        $raceval = $rs['menuvalue'];
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THIS DONOR RACE VALUE ({$pdta['race']}) WAS NOT FOUND IN THE DATA TABLES"));
      }

      $valChkSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'PXSEX' and dspind = 1 and dspvalue = :chkval";  
      $chkRS = $conn->prepare($valChkSQL); 
      $chkRS->execute( array( ':chkval' => $pdta['sex'] ));
      if ($chkRS->rowCount() > 0) { 
        $rs = $chkRS->fetch(PDO::FETCH_ASSOC); 
        $sexval = $rs['menuvalue'];
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THIS DONOR SEX VALUE ({$pdta['sex']}) WAS NOT FOUND IN THE DATA TABLES"));
      }

      $valChkSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'CX' and dspind = 1 and dspvalue = :chkval";  
      $chkRS = $conn->prepare($valChkSQL); 
      $chkRS->execute( array( ':chkval' => $pdta['cxind'] ));
      if ($chkRS->rowCount() > 0) { 
        $rs = $chkRS->fetch(PDO::FETCH_ASSOC); 
        $cxval = $rs['menuvalue'];
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THIS DONOR CHEMO-THERAPY INDICATION VALUE ({$pdta['cxind']}) WAS NOT FOUND IN THE DATA TABLES"));
      }

      $valChkSQL = "SELECT menuvalue FROM four.sys_master_menus where menu = 'RX' and dspind = 1 and dspvalue = :chkval";  
      $chkRS = $conn->prepare($valChkSQL); 
      $chkRS->execute( array( ':chkval' => $pdta['rxind'] ));
      if ($chkRS->rowCount() > 0) { 
        $rs = $chkRS->fetch(PDO::FETCH_ASSOC); 
        $rxval = $rs['menuvalue'];
      } else { 
        (list( $errorInd, $msgArr[] ) = array(1 , "THIS DONOR RADIATION-THERAPY INDICATION VALUE ({$pdta['rxind']}) WAS NOT FOUND IN THE DATA TABLES"));
      }

      if ($errorInd === 0 ) {   
        //$passdata ={"agemetric":"53","agemetricuom":"Years","race":"Unknown","sex":"Female","cxind":"Unknown","rxind":"Unknown","subjectnbr":"324354","protocolnbr":"987-6543","pbiosample":"87106","pxiid":"13fd4ff4-7006-4769-9e49-5a0b457e7393"}  
        //MAKE BACKUP COPY of original values 
        $bckSQL = "insert into masterrecord.history_procure_biosample_donor (pbiosample, pxiid, age, ageuom, sex, race, chemoind, radind, subjectnbr, protocolnbr, historyon, historyby) SELECT pbiosample, pxiid, ifnull(pxiage,'') as pxiage, ifnull(pxiageuom,'') as pxiageuom, ifnull(pxirace,'') as pxirace, ifnull(pxigender,'') as pxigender, ifnull(chemoInd,'') as chemoind, ifnull(radInd,'') as radind , ifnull(subjectNbr,'') as subjectnbr, ifnull(protocolNbr,'') as protocolnbr, now(), :usr FROM masterrecord.ut_procure_biosample where pxiid = :pxiid and pbiosample = :pbiosample";
        $bckRS = $conn->prepare($bckSQL); 
        $bckRS->execute(array(':usr' => $u['originalaccountname'], ':pxiid' => $pdta['pxiid'], ':pbiosample' => $pdta['pbiosample'])); 
        //write new values
        $updSQL = "UPDATE masterrecord.ut_procure_biosample set pxiage = :ageval, pxiageuom = :ageuomval, pxirace = :pxirace, pxigender = :pxisex, chemoind = :cx, radind = :rx, subjectnbr = :sbjt, protocolnbr = :prtcl where pxiid = :pxiid and pbiosample = :pbiosample";
        $updRS = $conn->prepare($updSQL); 
        $updRS->execute(array(':ageval' => (int)$pdta['agemetric'],':ageuomval' => $ageuomval,':pxirace' => $raceval,':pxisex' => $sexval,':cx' => $cxval,':rx' => $rxval,':sbjt' => trim($pdta['subjectnbr']),':prtcl' => trim($pdta['protocolnbr']),':pxiid' => $pdta['pxiid'],':pbiosample' => $pdta['pbiosample']));

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
      $cntMain = 0;
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
          $captureHistSQL = "insert into masterrecord.history_procure_biosample_comments (biosample, previouscomment, commenttype, commentupdatedon, commentupdatedby) SELECT pbiosample, biosamplecomment, 'BIOSAMPLECOMMENT', now(), :usr FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample"; 
          $updateSQL = "update masterrecord.ut_procure_biosample set biosamplecomment = :newComment where pbiosample = :pbiosample";
        } 
        if ( $key['commenttype'] === 'HPRQ' ) { 
          $captureHistSQL = "insert into masterrecord.history_procure_biosample_comments (biosample, previouscomment, commenttype, commentupdatedon, commentupdatedby) SELECT pbiosample, questionHPR, 'HPRQUESTION', now(), :usr FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample"; 
          $updateSQL = "update masterrecord.ut_procure_biosample set questionHPR = :newComment where pbiosample = :pbiosample";
        }
        $capR = $conn->prepare($captureHistSQL); 
        $capR->execute(array( ':usr' => $u['originalaccountname'],  ':pbiosample' => $recordid));
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
      $chkUsrSQL = "SELECT originalaccountname, emailaddress FROM four.sys_userbase where allowind = 1 and sessionid = :sess  and timestampdiff(day, now(), passwordexpiredate) > 0";
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
           case 'chartbldr': 
             $primeFocus = "";
             $left = '5vw';
             $top = '12vh';
             break;               
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
           case 'predit':
             $primeFocus = "";
             $left = '5vw';
             $top = '12vh';               
             break; 
           case 'masterAddSegment':
             $primeFocus = "";
             $left = '27vw';
             $top = '13vh';               
            break;
           case 'bgRqstFA': 
             $primeFocus = "";
             $left = '2vw';
             $top = '12vh';               
            break;                              
           case 'masterQMSAction':
             $primeFocus = "";
             $left = '13vw';
             $top = '13vh';
             break;
           case 'preprocremovesdsegment':
             $primeFocus = "";
             $left = '33vw';
             $top = '13vh';
             break;
           case 'shipdocaddsegment':
             $primeFocus = "";
             $left = '30vw';
             $top = '10vh';
             break;
           case 'shipdocshipoverride':
             $primeFocus = "";
             $left = '20vw';
             $top = '30vh';
             break;
           case 'shipdocaddso':
             $primeFocus = "";
             $left = '20vw';
             $top = '30vh';
             break;
           case 'eventCalendarEventAdd':
             $primeFocus = "";
             $left = '12vw';
             $top = '15vh';
             break;
           case 'enlargeDashboardGraphic':
             $primeFocus = "";
             $left = '5vw';
             $top = '5vh';
             break;
           case 'hprBigPathRpt':
             $primeFocus = "";
             $left = '12vw';
             $top = '12vh';
             break;         
           case 'hprAssistEmailer':
             $primeFocus = "";
             $left = '12vw';
             $top = '12vh';
             break;  
           case 'hprprviewer': 
             $left = '10vw';
             $top = '12vh';
             break;
           case 'hprDesignationSpecifier':
             $primeFocus = "srchHPRVocab";  
             $left = '10vw';
             $top = '12vh';
             break;
           case 'hprDXOverride':
             $primeFocus = "srchHPRVocab";  
             $left = '20vw';
             $top = '20vh';
             break;
           case 'hprMetastaticSiteBrowser':
             //$primeFocus = "srchHPRVocab";  
             $left = '30vw';
             $top = '15vh';
             break;
           case 'hprSystemicListBrowser':
             //$primeFocus = "srchHPRVocab";  
             $left = '30vw';
             $top = '15vh';
             break;
           case 'hprInconclusiveDialog':
             $primeFocus = "reasonInconclusiveTxt";  
             $left = '35vw';
             $top = '12vh';
             break;    
           case 'hprUnusuableDialog':
             $primeFocus = "ususableReasonTxt";  
             $left = '35vw';
             $top = '12vh';
             break;    
           case 'trayreturndialog': 
             $primeFocus = "";  
             $left = '35vw';
             $top = '12vh';
             break;
           case 'datacoordhprdisplay':
             $primeFocus = "";  
             $left = '15vw';
             $top = '12vh';
             break;
           case 'hprreturnslidetray':
             $primeFocus = "";  
             $left = '11vw';
             $top = '11vh';
             break; 
           case 'irequestdisplay':
             $primeFocus = "";  
             $left = '5vw';
             $top = '2vh';
             break; 
           case 'qmsRestatusSegments':
             $primeFocus = "qmsGlobalSelectorAssignInv";  
             $left = '10vw';
             $top = '10vh';
             break; 
           case 'qmsManageMoleTst':
             $primeFocus = "qmsGlobalSelectorAssignInv";  
             $left = '10vw';
             $top = '10vh';
             break; 
           case 'qmsInvestigatorEmailer':
             $primeFocus = "textWriterArea";  
             $left = '8vw';
             $top = '8vh';
             break;                
           case 'furtheractionperformer':
             $primeFocus = "";  
             $left = '8vw';
             $top = '8vh';
             break;                
           case 'rqstLocationBarcode':
             $primeFocus = "";  
             $left = '8vw';
             $top = '8vh';
             break;                
           case 'rqstSampleBarcode':
             $primeFocus = "";  
             //$primeFocus = "bccodevalue";  
             $left = '8vw';
             $top = '8vh';
             break;
           case 'donorvault':
             $primeFocus = "";  
             $left = '8vw';
             $top = '8vh';
             break;
           case 'faSendTicket':
             $primeFocus = "";  
             $left = '8vw';
             $top = '8vh';
             break;
           case 'shipdocspcsrvfee':
             $primeFocus = "";  
             $left = '8vw';
             $top = '8vh';
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

    function masterbgqms ( $request, $passdata ) { 
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
        $moleSQL = "SELECT mo.molecularid, concat(ifnull(mot.longvalue,''), ' (' , ifnull(mot.dspvalue,''),')') as moletest, rslt.longvalue as testreslt,   mo.molenote, date_format(mo.onwhen,'%m/%d/%Y') as inputdate, mo.onby FROM masterrecord.ut_procure_biosample_molecular mo left join (SELECT longvalue, dspvalue, menuvalue FROM four.sys_master_menus where menu = 'MOLECULARTEST') mot on mo.testid = mot.menuvalue left join (SELECT longvalue, dspvalue, menuvalue FROM four.sys_master_menus where menu ='MOLECULARTESTRESULTS') rslt on mo.testResultId = rslt.menuvalue where dspind = 1 and bgprcnbr = :readlabel";
        $moleRS = $conn->prepare($moleSQL);
        $moleRS->execute(array(':readlabel' => $bgn));
        if ( $moleRS->rowCount() > 0 ) { 
            while ($mr = $moleRS->fetch(PDO::FETCH_ASSOC)) { 
               $dta['molecular'][] = $mr;
            }
        } else { 
            //NO DATA
            $dta['molecular'] = null;
        }
        $prcSQL = "SELECT prc.prcid, mprc.dspvalue prctype, prcvalue, date_format(inputon, '%m/%d/%Y') as inputon, inputby FROM masterrecord.ut_procure_biosample_samplecomposition prc left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSPERCENTMAKEUP') as mprc on prc.prctype = mprc.menuvalue where dspind = 1 and readlabel = :readlabel";
        $prcRS = $conn->prepare($prcSQL); 
        $prcRS->execute(array(':readlabel' => $bgn));
        if ( $prcRS->rowCount() > 0 ) { 
            while ( $pr = $prcRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta['percents'][] = $pr;
            }
        } else { 
            $dta['percents'] = null;
        }
        
        $noteSQL = "SELECT replace(bs.read_label,'_','') as readlabel, ifnull(qmsnote,'') as qmsnote, qmsstatusby, date_format(qmsstatuson,'%m/%d/%Y') as qmsstatuson  FROM masterrecord.ut_procure_biosample bs where replace(read_label,'_','') = :readlabel";
        $noteRS= $conn->prepare($noteSQL);
        $noteRS->execute(array(':readlabel' => $bgn));
        if ( $prcRS->rowCount() > 0 ) { 
            while ( $nr = $noteRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta['qmsnote'][] = $nr;
            }
        } else { 
            $dta['qmsnote'] = null;
        }        
        $responseCode = 200;
      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;              
    }
    
    function masterbglabactionnotes ( $request, $passdata ) { 
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
        $labSQL = "SELECT replace(bs.read_label,'_','') as readlabel, bs.labactionaction, mnu.labactionactiondsp, bs.labactionnote FROM masterrecord.ut_procure_biosample bs left join (SELECT menuvalue, longValue as labactionactiondsp FROM four.sys_master_menus where menu = 'QMSLABACTIONS') as mnu on bs.labactionaction = mnu.menuvalue where replace(read_label,'_','') = :bgn";
        $labRS = $conn->prepare($labSQL);
        $labRS->execute(array(':bgn' => $bgn)); 
        while ($r = $labRS->fetch(PDO::FETCH_ASSOC)) { 
            $dta[] = $r;
        }
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
             $dta['pathologyrptdocid']      = $bg['pathologyrptdocid'];
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

           if ( trim($bg['associd']) !== "" ) {
           $assSQL = "SELECT pbiosample, ucase(replace(bs.read_label,'_','')) as readlabel, ucase(ifnull(bs.tissType,'')) as specimencategory, ucase(trim(concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'') ='','',concat(' (',ifnull(bs.subSite,''), ')'))))) as site, ucase(trim(concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' [',ifnull(bs.subdiagnos,''),']'))))) as diagnosis, ucase(trim(ifnull(bs.metsSite,''))) as metssite, ucase(trim(ifnull(bs.pdxSystemic,''))) as systemic, ifnull(bs.HPRInd,0) as hprind, ifnull(bs.QCInd,0) as qcind, ucase(ifnull(qmsval.dspvalue,'')) as qmsstatus , ucase(ifnull(hprval.dspvalue,'')) as hprdecision, ifnull(bs.HPRResult,0) as hprresult FROM masterrecord.ut_procure_biosample bs left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HPRDECISION') as hprval on bs.hprdecision = hprval.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus') as qmsval on bs.QCProcStatus = qmsval.menuvalue where assocID = :associd and pbiosample <>  :bgn and bs.voidind <> 1 order by pbiosample asc";
           $assRS = $conn->prepare($assSQL);
           $assRS->execute(array(':associd' => $bg['associd'], ':bgn' => $bgn)); 
           $ass = array(); 
           if ( $assRS->rowCount() < 1) { 
           } else { 
             while ( $asses = $assRS->fetch(PDO::FETCH_ASSOC)) { 
               $ass[] = $asses;
             }
           }
             $dta['associativegroup'] = $ass;
           } else { 
             $dta['associativegroup'] = "";
           }
           
         }     
         $responseCode = 200;
        }      



      }
      $msg = $msgArr;
      $rows['statusCode'] = $responseCode; 
      $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
      return $rows;   
    }

    function returnhprtrayinventory ( $request, $passdata ) { 
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
       ( trim($pdta['devreason']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "THE REASON FOR DEVIATING FROM CHTNEASTERN SOPs MUST BE SPECIFIED." )) : "";       
       $chkSQL = "SELECT * FROM four.sys_master_menus where menu like 'DEVIATIONREASON_HPROVERRIDE' and dspind = 1 and dspValue = :value";
       $chkR = $conn->prepare($chkSQL);
       $chkR->execute(array(':value' => $pdta['devreason']));
       if ($chkR->rowCount() <> 1) { 
         (list( $errorInd, $msgArr[] ) = array(1 , "Deviation Reason is not allowable"));    
       }
       ( trim($pdta['hprboxid']) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "HPR TRAY/BOX SCANCODE CANNOT BE BLANK.  SEE A CHTNEastern INFORMATICS STAFF PERSON.")) : "";
       //TODO: CHECK TO SEE IF THIS IS AN HPR TRAY
       ( (int)count( $pdta['slides'] ) < 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "NO SLIDES LISTED FOR THIS ACTION")) : "";

       $segSQL = "SELECT segmentid FROM masterrecord.ut_procure_segment where replace(bgs,'_','') = :bgs"; 
       $segRS = $conn->prepare($segSQL);
       //TODO: MAKE THIS ITS OWN FUNCTION - THIS IS IMPORTANT FOR INVENTORY 
       $invSQL = "SELECT ilc.scancode, trim(concat(ifnull(ilc.locationnote,''),  if ( ifnull(ilc.locationdsp,'') = '','',  if(ifnull(ilc.locationdsp,'') = ifnull(ilc.locationnote,''),'',concat(' (',ifnull(ilc.locationdsp,''),')'))), if(ifnull(ilc.typeolocation,'') = '','',concat(' [',ifnull(ilc.typeolocation,''),']')))) thisloc, trim(concat(ifnull(lvl1.locationnote,'') , if(ifnull(lvl1.locationdsp,'') = '','',concat(' (',ifnull(lvl1.locationdsp,''),')')))) as parent, if ( trim(concat(ifnull(lvl2.locationnote,'') , if(ifnull(lvl2.locationdsp,'') = '','',concat(' (',ifnull(lvl2.locationdsp,''),')')))) = trim(concat(ifnull(lvl1.locationnote,'') , if(ifnull(lvl1.locationdsp,'') = '','',concat(' (',ifnull(lvl1.locationdsp,''),')')))),'',trim(concat(ifnull(lvl2.locationnote,'') , if(ifnull(lvl2.locationdsp,'') = '','',concat(' (',ifnull(lvl2.locationdsp,''),')'))))) as loc2, if ( trim(concat(ifnull(lvl3.locationnote,'') , if(ifnull(lvl3.locationdsp,'') = '','',concat(' (',ifnull(lvl3.locationdsp,''),')')))) = trim(concat(ifnull(lvl2.locationnote,'') , if(ifnull(lvl2.locationdsp,'') = '','',concat(' (',ifnull(lvl2.locationdsp,''),')')))),'',trim(concat(ifnull(lvl3.locationnote,'') , if(ifnull(lvl3.locationdsp,'') = '','',concat(' (',ifnull(lvl3.locationdsp,''),')'))))) as loc3, if ( trim(concat(ifnull(lvl4.locationnote,'') , if(ifnull(lvl4.locationdsp,'') = '','',concat(' (',ifnull(lvl4.locationdsp,''),')')))) = trim(concat(ifnull(lvl3.locationnote,'') , if(ifnull(lvl3.locationdsp,'') = '','',concat(' (',ifnull(lvl3.locationdsp,''),')')))),'',trim(concat(ifnull(lvl4.locationnote,'') , if(ifnull(lvl4.locationdsp,'') = '','',concat(' (',ifnull(lvl4.locationdsp,''),')'))))) as loc4 FROM four.sys_inventoryLocations ilc left join four.sys_inventoryLocations lvl1 on ilc.parentid = lvl1.locationid left join four.sys_inventoryLocations lvl2 on lvl1.parentid = lvl2.locationid left join four.sys_inventoryLocations lvl3 on lvl2.parentid = lvl3.locationid left join four.sys_inventoryLocations lvl4 on lvl3.parentid = lvl4.locationid where ilc.scancode = :scancode";
       $invRS = $conn->prepare($invSQL); 

       $invRS->execute(array(':scancode' => $pdta['hprboxid']));
       $presenttray = $invRS->fetch(PDO::FETCH_ASSOC);
       $ptrayloc = $presenttray['thisloc'];

       $segdetaillist = array();
       foreach ( $pdta['slides'] as $skey => $sval ) { 
         $segid = "";
         $itorytree = "";  
         ( trim($skey) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ALL SLIDES MUST HAVE LABEL NUMBERS")) : "";
         ( trim($sval) === "" ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ALL SLIDES MUST LIST AN INVENTORY LOCATION, SLIDE {$skey} DOES NOT LIST AN INVENTORY LOCATION.")) : "";

         $segRS->execute(array(':bgs' => $skey));
         $segid = "";
         if ( $segRS->rowCount() <> 1 ) { 
            (list( $errorInd, $msgArr[] ) = array(1 , "BAD BGS ({$skey}). SEE A CHTNEastern INFORMATICS PERSON.")); 
         } else { 
             $seg = $segRS->fetch(PDO::FETCH_ASSOC);
             $segid = $seg['segmentid'];
         }

         $invRS->execute(array(':scancode' => $sval));
         $invLoc = "";
         if ( $invRS->rowCount() <> 1 ) {
            (list( $errorInd, $msgArr[] ) = array(1 , "BAD SCANCODE ({$sval}). SEE A CHTNEastern INFORMATICS PERSON.")); 
         } else {
            $itory = $invRS->fetch(PDO::FETCH_ASSOC);
            $itorytree = $itory['thisloc'] . " :: " . $itory['parent']; 
         }
         
         $segdetaillist[$skey] = array('segid' => $segid, 'itorytree' => $itorytree, 'newscancode' => $sval, 'bgs' => $skey, 'presentlocdesc' => $ptrayloc );
       }

       //CHECK TRAY IF ALL SLIDES ARE INCLUDED
       $segChkSQL = "SELECT replace(bgs,'_','') as bgs FROM masterrecord.ut_procure_segment where HPRBoxNbr = :boxid"; 
       $segChkRS = $conn->prepare($segChkSQL); 
       $segChkRS->execute(array(':boxid' => $pdta['hprboxid'])); 

       $foundind = 1;
       while ( $s = $segChkRS->fetch(PDO::FETCH_ASSOC)) {
           if ( array_key_exists($s['bgs'],$pdta['slides']) ) {
           } else {
               $foundind = 0;
           }
       }
       ( $foundind === 0 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "ALL SLIDES IN THE DATABASE HAVE NOT BEEN ACCOUNTED FOR. SEE A CHTNEastern INFORMATICS PERSON.")) : "";


       if ( $errorInd === 0 ) {
  
           //1) write each segment to history_procure_segment_hprsubmission showing that its been returned
           $hprsubSQL = "insert into masterrecord.history_procure_segment_hprsubmission (segmentid, tohprby, historyon, historyby ) values ( :segid, :usr ,now(), 'HPR-INV-TRAY-RTN-OVERRIDE' )";
           $hprsubrs = $conn->prepare($hprsubSQL); 

           //2) write each segment to history_procure_segment_inventory for new location 
           $seghistSQL = "insert into masterrecord.history_procure_segment_inventory ( segmentid, bgs, scannedlocation, scannedinventorycode, inventoryscanstatus, scannedby, scannedon, historyon, historyby) value ( :segid, :bgs , :locinvtydesc, :scancode, 'HPR TRAY RETURNED',:usr ,now(),now(),'HPR-RTN-OVERRIDE')";
           $seghistrs = $conn->prepare($seghistSQL);

           //3) update each segment ut_procure_segment with new location          
           $segSQL = "update masterrecord.ut_procure_segment set scannedLocation = :invtrylocdesc, scanloccode = :scancode, scannedstatus = :scannedstatus, scannedby = :usr, scanneddate = now(), tohpr = 0, hprslideread = 0, HPRBoxNbr = '' where segmentid = :segid";
           $segrs = $conn->prepare($segSQL);
           
           //4) finally make a record in history_hpr_tray_status
           $hprHistSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, historyby, historyon) values (:trayscancode, 'HPR-TRAY-EMPTIED-RETURNED', now())";
           $hprhistrs = $conn->prepare($hprHistSQL);

         //5) update sys_inventoryLocations
           $invUpdSQL= "update four.sys_inventoryLocations set hprtraystatus = 'NOTUSED', hprtrayheldwithin = '', hprtrayheldwithinnote = '',hprtrayreasonnotcomplete = '',hprtrayreasonnotcompletenote = '', hprtraystatusby = :usr, hprtraystatuson = now() where scancode = :scancode";
           $invupdrs = $conn->prepare($invUpdSQL);

        //6) write to the deviation table
          $devSQL = "insert into masterrecord.tbl_operating_deviations(module, whodeviated, whendeviated, operationsarea, functiondeviated, reasonfordeviation, payload) value('data-coordination', :whodeviated, now(), 'inventory', 'inventory-hpr-tray-return-override' , :reasonfordeviation, :payload)";
          $devRS = $conn->prepare($devSQL); 

         foreach ( $segdetaillist as $dtlkey => $dtldtl ) {
           $hprsubrs->execute(array(':segid' => $dtldtl['segid'] ,':usr' => $u['originalaccountname']   ));
           $seghistrs->execute(array(':segid' => $dtldtl['segid'], ':bgs' => $dtlkey, ':locinvtydesc' => $dtldtl['itorytree'], ':scancode' => $dtldtl['newscancode'], ':usr' => $u['originalaccountname'] ));
           $segrs->execute(array(':invtrylocdesc' => $dtldtl['itorytree'], ':scancode' => $dtldtl['newscancode'], ':scannedstatus' =>'INVENTORY-HPR-TRAY-RETURN-OVERRIDE' , ':usr' => $u['originalaccountname'], ':segid' => $dtldtl['segid']));           
         }
         $hprhistrs->execute(array(':trayscancode' => $pdta['hprboxid'] ));
         $invupdrs->execute(array(':usr' => $u['originalaccountname'], ':scancode' => $pdta['hprboxid'] ));
         $devRS->execute(array(':whodeviated' => $u['originalaccountname'], ':reasonfordeviation' => $pdta['devreason'], ':payload' => $passdata));         
         $responseCode = 200;
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
        //TODO:  MAKE SURE THAT THE USER HAS THE RIGHT TO RUN THIS REPORT
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
         $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, 'UPDATE-BY-SYSTEM' as historyby FROM four.sys_userbase where sessionid = :sessid";
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
         $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, 'UPDATE-BY-SYSTEM' as historyby FROM four.sys_userbase where sessionid = :sessid and pwordresetcode = BINARY :codechange";
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
          $backupSQL = "insert into four.sys_userbase_history (userid,failedlogins,friendlyName,lastname,emailAddress,fiveonepword,zackOnly,changePWordInd,originalAccountName,informaticsInd,freshNotificationInd,allowInd,allowWeeklyUpdate,allowlinux,pxipassword,pxipasswordexpire,pxiguidident,pxisessionexpire,allowProc,allowCoord,allowHPR,allowQMS,allowHPRInquirer,allowHPRReview,allowInvtry,allowfinancials,sessionid,presentinstitution,sessionExpire,ssv5guid,sessionNeverExpires,userName,displayName,dspjobtitle,primaryFunction,primaryInstCode,passwordExpireDate,pwordResetCode,pwordResetExpire,altinfochangecode,altinfochangecodeexpire,inputOn,inputBy,accessLevel,accessNbr,lastUpdatedOn,lastUpdatedBy,logCardId,inventorypinkey,logCardExpDte,dspAlternateInDir,dspindirectory,dsporderindirectory,sex,profilePicURL,profilePhone,profileAltEmail,dlExpireDate,altPhone,altPhoneType,altPhoneCellCarrier,cellcarriercode,historyon,historyby)  SELECT *, now() as historyinputon, 'UPDATE-BY-SYSTEM' as historyby FROM four.sys_userbase where sessionid = :sessid and altinfochangecode = BINARY :altchange";
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
     $chkESQL = "SELECT * FROM four.tmp_ORListing where location = :loc and PXICode = :eid and date_format(ListDate,'%m/%d/%Y') = :procdate";
     $chkERS = $conn->prepare($chkESQL); 
     $chkERS->execute(array(':loc' => $presUserInst, ':eid' => $eid, ':procdate' => $pdta['procedureDate']  ));
     ( $chkERS->rowCount() !== 1 ) ? (list( $errorInd, $msgArr[] ) = array(1 , "Encounter Id Not Found in Data Tables.  See a CHTNEASTERN Informatics staff member {$pdta['procedureDate']}")) : "";

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
                            where location = :presInst and PXICode = :pxicode and date_format(ListDate,'%m/%d/%Y') = :procdate
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
                           ,':pxicode' => $eid
                           ,':procdate' => $pdta['procedureDate']  ));
            
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

    if ( $errorInd === 0 ) { 
     
     if ( $pdta['prid'] === 'NEWPRPT') { 
         $htmlized = preg_replace('/\n\n/','<p>',$pdta['prtxt']);
         $htmlized = preg_replace('/\r\n/','<p>', $htmlized);
         $htmlized = preg_replace('/\n/','<br>',$htmlized);

         $insSQL = "insert into  masterrecord.qcpathreports( selector, pathreport, pxiid, uploadedby, uploadedon, biospecimen, dnpr_nbr) values ( :selector, :pathreport, :pxiid, :uploadedby, now(), :biospecimen, :dnpr_nbr)";
         $insRS = $conn->prepare($insSQL);
         $insRS->execute(array(
             ':selector' => generateRandomString(8)
            ,':pathreport' => $htmlized
            ,':pxiid' => $pdta['pxiid']
            ,':uploadedby' => $usrrecord['originalaccountname']
            ,':biospecimen' => $pdta['bg']
            ,':dnpr_nbr' => $pdta['labelNbr']
         ));

         $prid = $conn->lastInsertId();
         $yesvalSQL = "SELECT menuvalue as yesvalue FROM four.sys_master_menus where menu = 'PRpt' and queriable = 0";
         $yesvalRS = $conn->prepare($yesvalSQL); 
         $yesvalRS->execute(); 
         $yesval = $yesvalRS->fetch(PDO::FETCH_ASSOC); 

         $histSQL = "insert into masterrecord.history_procure_biosample_pathrpt (pbiosample, pathreportind, prid, changeby, changeon) SELECT pbiosample, ifnull(pathreport,0) as pathreportind, ifnull(pathreportid,0) as pathreportid, :user, now() FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
         $histRS = $conn->prepare($histSQL);
         $histRS->execute(array(':user' => $usrrecord['originalaccountname'], ':pbiosample' => $pdta['bg']));

         $updSQL = "update masterrecord.ut_procure_biosample set pathreport = :yval, pathreportid = :prid where pbiosample = :pbiosample";
         $updRS = $conn->prepare($updSQL); 
         $updRS->execute(array(':yval' => $yesval['yesvalue'], ':prid' => $prid, ':pbiosample' => $pdta['bg']));
   
         $responseCode = 200;
         $dta['dialogid'] = $pdta['dialogid'];
     } else { 
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
                             ,':prtxt' => $htmlized
                             ,':lasteditby' => $usrrecord['originalaccountname']
                             ,':pxiid' => $pdta['pxiid']
                             ,':biospecimennbr' => $pdta['bg']
                             ,':editreason' => $pdta['editreason']
                         ));
         $responseCode = 200;
         $dta['dialogid'] = $pdta['dialogid'];
       }
     }

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
    where pxicode = :donorid and location = :usrpresentloc and date_format(listdate,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') 
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
             $invHisRS->execute(array(':segmentid' => $sld['slideid'],':bgs' => $sld['slidenbr'],':scannedlocation' => $traydsp,':scannedinventorycode' => $invscancode,':inventoryscanstatus' => 'HPR-SUBMIT-OVERRIDE',':scannedby' => $usr['originalaccountname'],':historyby' => 'COORDINATOR SCREEN HPR INVENTORY OVERRIDE'));
             $hprHisRS->execute(array(':segmentid' => $sld['slideid'],':tohprby' => $usr['originalaccountname']));
          }
          
          // MARK INVENTORY SLIDE TRAY AS LOCKED OUT
          $traySTSSQL = "update four.sys_inventoryLocations set hprtraystatus = 'SUBMITTED', hprtraystatusby = :usr, hprtraystatuson = now() where scancode = :scncode";
          $traySTSRS = $conn->prepare($traySTSSQL);
          $traySTSRS->execute(array(':usr' => $usr['originalaccountname'], ':scncode' => $invscancode)); 
          $tryHisSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, tray, traystatus, historyon, historyby) values(:trayscancode, :tray, :traystatus, now(), :historyby)";
          $tryHisRS = $conn->prepare($tryHisSQL);
          $tryHisRS->execute(array(':trayscancode' => $invscancode, ':tray' => $traydsp, ':traystatus' => 'SUBMITTED', ':historyby' => $usr['originalaccountname']));
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
          if ($qryrqst['investigatorid'] === 'PENDDESTROY' ) { 
            $assInv = 'PENDDEST';  //SEGMENT STATUS FROM SYS_MASTER_MENUS
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
          if ( (int)$r['shipdocrefid'] <> 0 ) { 
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
                    if ( $assInv === 'PENDDEST') { 
                      $sts = 'PENDDEST'; 
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
            case 'ASTREQ':
                //TODO:  MAKE DATA CHECKS
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
                  $insR->execute(array(':objid' => $objid, ':whoby' => $u['originalAccountName'], ':srchtrm' => $passedData, ':doctype' => trim($qryrqst['qryType'])    ));
                  $data['astsearchid'] = $objid;
                  $responseCode = 200;
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
          $msg = "YOU MUST SPECIFY A SEARCH TERM AND/OR A DOCUMENT TYPE"; 
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
          if ( (int)$r['shipdocrefid'] <> 0  ) { 
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
     
     if ( trim($pdta['sdcCourierInfoValue']) !== "" ) { 
         //CHECK VALID COURIER ID FOR INVESTIGATOR
         $courierChkSQL = "SELECT courierid, ucase(trim(ifnull(courier_name,''))) as courier, ucase(trim(ifnull(courier_num,''))) as couriernbr  FROM vandyinvest.eastern_courier where investid = :invest and courierid = :courier";
         $courierChkRS = $conn->prepare($courierChkSQL);
         $courierChkRS->execute(array(':invest' => $pdta['sdcInvestCode'], ':courier' => $pdta['sdcCourierInfoValue']  ));
         if ( $courierChkRS->rowCount() <> 1) {
             ( list( $errorInd, $msgArr[] ) = array( 1 , "THE COURIER ACCOUNT SELECTED FOR {$pdta['sdcInvestCode']} IS NOT VALID"));
         } else { 
             $cr =  $courierChkRS->fetch(PDO::FETCH_ASSOC);
             $crID = ( trim($cr['courierid']) === '' ) ? 0 : trim($cr['courierid']);
             $crName = ( trim($cr['courier']) === '' ) ? "" : trim($cr['courier']);
             $crNbr = ( trim($cr['couriernbr']) === '' ) ? "" : trim($cr['couriernbr']);
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

         //TODO: Status should be dynamic!
         $sdInsSQL = "insert into masterrecord.ut_shipdoc (sdstatus, statusdate, acceptedby, acceptedbyemail, ponbr, rqstshipdate, rqstpulldate, comments, investcode, investname, investemail, investinstitution, institutiontype, investdivision, oncreationinveststatus, shipaddy, shipphone, billaddy, billphone, setupby, setupon, courier, couriernbr, tqcourierid) values('OPEN', now(), :acceptedby, :acceptedbyemail, :ponbr, :rqstshipdate, :rqstpulldate, :comments, :investcode, :investname, :investemail, :investinstitution, :institutiontype, :investdivision, :oncreationinveststatus, :shipaddy, :shipphone, :billaddy, :billphone, :setupby, now(), :courier, :couriernbr, :tqcourierid )";   
         $sdR = $conn->prepare($sdInsSQL); 
         $sdR->execute(array(':acceptedby' => trim($pdta['sdcAcceptedBy']) ,':acceptedbyemail' => trim($pdta['sdcAcceptorsEmail']),':ponbr' => trim($pdta['sdcPurchaseOrder']) ,':rqstshipdate' => trim($pdta['sdcRqstShipDateValue']),':rqstpulldate' => trim($pdta['sdcRqstToLabDateValue']),':comments' => trim($pdta['sdcPublicComments']),':investcode' => strtoupper(trim($pdta['sdcInvestCode'])),':investname' => trim($pdta['sdcInvestName']),':investemail' => trim($pdta['sdcInvestEmail']),':investinstitution' => strtoupper(trim($pdta['sdcInvestInstitution'])),':institutiontype' => trim($pdta['sdcInvestTQInstType']),':investdivision' => trim($pdta['sdcInvestPrimeDiv']),':oncreationinveststatus' => trim($pdta['sdcInvestTQStatus']),':shipaddy' => trim($pdta['sdcInvestShippingAddress']),':shipphone' => trim($pdta['sdcShippingPhone']),':billaddy' => trim($pdta['sdcInvestBillingAddress']),':billphone' => trim($pdta['sdcBillPhone']),':setupby'  => $usr, ':courier' => $crName, ':couriernbr' => $crNbr, ':tqcourierid' => $crID  )); 

         $shipdocnbr = $conn->lastInsertId();         
         $dta['shipdocrefid'] = $shipdocnbr; 
         
         $sdStsInsSQL = "insert into masterrecord.history_shipdoc_actions(shipdocrefid, status, statusdate, bywhom, ondate, segmentreference) values( :shipdocrefid, :status, now(), :bywhom, now(), :sid)";
         $sdStsInsR = $conn->prepare($sdStsInsSQL); 
         $sdStsInsR->execute(array(':shipdocrefid' => $shipdocnbr, ':status' => 'SHIPDOCCREATED', ':bywhom' => $usr, ':sid' => 0)); 

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
           $sdStsInsR->execute(array(':shipdocrefid' => $shipdocnbr, ':status' => "SEGMENT ADDED. SEGID={$sg['segmentid']}", ':bywhom' => $usr, ':sid' => $sg['segmentid']  )); 
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
    //TODO TURN REPEATING CODE IN THIS CODE BLOCK INTO A FUNCTION
    session_start();   
    $responseCode = 503; 
    $params = json_decode($passedData, true);
    $ec = $params['ency'];
    $cred = json_decode(chtndecrypt($params['ency']), true); 
    $u = $cred['user'];
    $p = $cred['pword'];
    $au = trim($cred['dauth']);
    $sess = session_id(); 
    $ip = clientipserver();
    require(serverkeys . "/sspdo.zck"); 
    $usrChk = "SELECT userid, username, emailaddress, fiveonepword FROM four.sys_userbase where allowind = 1 and failedlogins < 6 and emailaddress = :pword and datediff(passwordexpiredate, now()) > -1";
    $usrR = $conn->prepare($usrChk); 
    $usrR->execute(array(':pword' => $u)); 

    $lockoutSQL = "update four.sys_userbase set failedlogins = (failedlogins + 1) where emailaddress = :useremail";
    $lockout = $conn->prepare($lockoutSQL);
    $lockoutChkSQL = "SELECT failedlogins FROM four.sys_userbase where emailaddress = :useremail";
    $lockoutChkRS = $conn->prepare($lockoutChkSQL);
    $lockAcctSQL = "update four.sys_userbase set allowind = 0 where emailaddress = :useremail";
    $lockAcctRS = $conn->prepare($lockAcctSQL);

    if ( $usrR->rowCount() === 1 ) { 
      $r = $usrR->fetch(PDO::FETCH_ASSOC);  
      $dbPword = $r['fiveonepword'];
      $usrid = $r['userid'];
      if (password_verify($p,  $dbPword)) { 
        //USER NAME AND PASSWORD CORRECT - CHECK AUTH          
           $auSQL = "select ifnull(useremail,'') as useridemail, ifnull(phpsessid,'NOTSET') as phpsessid, datediff(now(), inputon) dayssince,  TIMESTAMPDIFF(HOUR,inputon,now()) as timesince FROM serverControls.sys_ssv7_authcodes where authcode = :acode and useremail = :uemail and TIMESTAMPDIFF(HOUR,inputon,now()) < 12 and registerUserIP = :ruip"; 
           $auR = $conn->prepare($auSQL);
           $auR->execute(array(':acode' => $au, ':uemail' =>$u, ':ruip' => clientipserver()));
           if ($auR->rowCount() === 1) {                
               //CHECK DAYS SINCE
               $authenR = $auR->fetch(PDO::FETCH_ASSOC);

                   //GOOD - SESSION CREATE LOGGEDON - CAPTURE SYSTEM ACTIVITY - REDIRECT IN JAVASCRIPT WITH STATUSCODE = 200
                   session_regenerate_id(true);
                   $newsess = session_id();
                   $updAuthSQL = "update serverControls.sys_ssv7_authcodes set phpsessid = :newsess, timesused = (timesused + 1) where authcode = :acode and useremail = :uemail";
                   $updAR = $conn->prepare($updAuthSQL);
                   $updAR->execute(array(':newsess' => $newsess, ':acode' => $au, ':uemail' => $u, ':ruip' => clientipserver()    )); 
                 
                   if (!isset($_COOKIE['ssv7auth'])) {
                       $date = date();
                       $dte = date('Y-m-d H:i:s', strtotime($date. ' +12 hours'));
                       $cookieArr = json_encode(array("dualcode" =>  cryptservice(  $au , 'e'), "expiry" =>  time() + 43200, 'expirydate' => $dte ));
                       setcookie('ssv7auth', "{$cookieArr}", time() + 43200, '/','',true,true); // 2592000 = 30 days 3600 - is one hour
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
                   $lockoutResetSQL = "update four.sys_userbase set failedlogins = 0 where emailaddress = :useremail";
                   $lockoutResetRS = $conn->prepare($lockoutResetSQL);
                   $lockoutResetRS->execute(array( ':useremail' => $u ));

                   $msg = "SUCCESS " . session_id();
                   $responseCode = 200;                    

           } else { 

               //BAD OR EXPIRED AUTH CODE
               $lockout->execute(array( ':useremail' => $u ));        
               $lockoutChkRS->execute(array(':useremail' => $u ));
               if ( $lockoutChkRS->rowCount() > 0 ) { 
                 $lockout = $lockoutChkRS->fetch(PDO::FETCH_ASSOC);
                 if ( (int)$lockout['failedlogins'] < 6 ) {
                   $msgAdd = " (Login Attempts: {$lockout['failedlogins']} of 5)";
                 } else { 
                   $lockAcctRS->execute(array(':useremail' => $u));
                   $msgAdd = " (Account has been locked out.  See CHTNEastern Informatics)";
                 }
               }
               captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'AUTH CODE INCORRECT');
               $msg = trim("Either User-Name, password and/or dual-authentication code is incorrect{$msgAdd}");
           }             
      } else { 
      //BAD PASSWORD
 
         $lockout->execute(array( ':useremail' => $u ));        
         $lockoutChkRS->execute(array(':useremail' => $u ));
         if ( $lockoutChkRS->rowCount() > 0 ) { 
           $lockout = $lockoutChkRS->fetch(PDO::FETCH_ASSOC);
           if ( (int)$lockout['failedlogins'] < 6 ) {
               $msgAdd = " (Login Attempts: {$lockout['failedlogins']} of 5)";
           } else { 
               $lockAcctRS->execute(array(':useremail' => $u));
               $msgAdd = " (Account has been locked out.  See CHTNEastern Informatics)";
           }
         }

         captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'CREDENTIALS INCORRECT');
         $msg = trim("Either User-Name, password and/or dual-authentication code is incorrect{$msgAdd}");   
      }
    } else { 
        //NO USER FOUND
        captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'USER NOT FOUND');
        $msg = trim("Either User-Name, password and/or dual-authentication code is incorrect");           
    }                


    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => 0,  'DATA' => "");
    return $rows;      
  }

  function sessionloginbu($request, $passedData) { 
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
               if ((int)$authenR['dayssince'] < 7) { 

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
               $msg = "Either User-Name, password and/or authentication code is incorrect";
           }             
         }
      } else { 
      //BAD PASSWORD
         captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'CREDENTIALS INCORRECT');
         $msg = "Either User-Name, password and/or authentication code is incorrect";   
      }
    } else { 
        //NO USER FOUND
        captureSystemActivity($sess, '', 'false', $u, '', '', $u, 'POST', 'USER NOT FOUND');
        $msg = "Either User-Name, password and/or authentication code is incorrect";           
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
         for ($i = 0; $i < 6; $i++) {
           $randomString .= $characters[rand(0, $charactersLength - 1)];
         }
         session_start();
         $capAuthSQL = "insert into serverControls.sys_ssv7_authcodes (authcode, phpsessid, useremail, inputon, registerUserIP) values(:authcode, :sess, :uemail,  now(), :ruip)";
         $capAuthR = $conn->prepare($capAuthSQL);
         $capAuthR->execute(array(':authcode' => $randomString, ':sess' => session_id(), ':uemail' => $rquester, ':ruip' => clientipserver()));
         $capID = $conn->lastInsertId();
         $authCode = "{$randomString}"; 
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
    $needkeys = array("qryType","BG","procInst","segmentStatus","qmsStatus","hprTrayInvLoc","procDateFrom","procDateTo","shipDateFrom","shipDateTo","investigatorCode","shipdocnbr","shipdocstatus","site","specimencategory","phiage","phirace","phisex","procType","PrepMethod","preparation");
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

    //CHECK HPR Tray Location 

    if ( trim($rqst['hprTrayInvLoc']) !== "" ) {
        if ( !preg_match('/\bHPRT\d{3}\b/', trim($rqst['hprTrayInvLoc']) )) {
          //NOT A PROPER TRAY ID  
          $allowerror = 1; 
          $msg .= "\r\n- Invalid HPR Slide Tray Inventory Location";
        } else { 
            //PROPER TRAY ID
          if ( (int)$fieldsFilledIn > 1 ) {
            $allowerror = 1; 
            $msg .= "\r\n- When querying for an HPR Slide Tray Inventory Location, you may not query for any other locations. ";
          }
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
  $rtnthis = "SELECT bs.pbiosample, replace(bs.read_label,'_','') as readlabel, ifnull(bs.pristineselector,'') as pristineselector, ifnull(voidind,0) as voidind, ifnull(inst.dspinstitution, bs.procureInstitution) as dspinstitution , ifnull(bs.createdby,'') as technician, ifnull(date_format(createdon,'%m/%d/%Y'),'') as procuredate, ifnull(bs.associd,'') as associd, upper(concat(ifnull(pt.dspvalue,''), if(ifnull(ct.dspvalue,'')='','',concat(' :: ',ifnull(ct.dspvalue,'')))))  as collectproctype, trim(ifnull(bs.tisstype,'')) as speccat, trim(concat(ifnull(bs.anatomicsite,''), if(ifnull(bs.subSite,'')='','',concat(' :: ',ifnull(bs.subsite,''))))) as site  , trim(concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' :: ',ifnull(bs.subdiagnos,''))))) as diagnosis, trim(concat(ifnull(bs.metssite,''), if(ifnull(bs.metsSiteDX,'')='','',concat(' :: ',ifnull(bs.metsSiteDX,''))))) mets, trim(ifnull(bs.siteposition,'')) as siteposition, trim(ifnull(bs.pdxSystemic,'')) as systemicdx, trim(date_format(bs.procedureDate,'%m/%d/%Y')) as proceduredate, trim(ifnull(bs.pxiID,'')) as pxiid, upper(concat(ifnull(bs.pxiage,''), if(ifnull(auom.ageuomdsp,'')='','',concat(' ',auom.ageuomdsp)))) as pxiage , upper(ifnull(bs.pxiRace,'')) as pxirace, upper(ifnull(sx.sexdsp,'')) as pxisex, upper(ifnull(cx.dspvalue,'')) as cxind, upper(ifnull(rx.dspvalue,'')) as rxind, upper(ifnull(ic.dspvalue,'')) as icind, upper(ifnull(pr.dspvalue,'')) as prind, ifnull(bs.pathreportid,0) as pathologyrptdocid, ifnull(bs.subjectnbr,'') as subjectnbr, ifnull(bs.protocolnbr,'') as protocolnbr, ifnull(bs.hprind,0) as hprind, ifnull(bs.hprmarkbyon,'') as hprmarkbyon, ifnull(bs.qcind,0) as qcind, ifnull(bs.qcmarkbyon,'') as qcmarkbyon, ifnull(bs.qcvalv2,'') as qcvalue, ifnull(bs.qcprocstatus,'') as qcprocstatus, ifnull(bs.qmsstatusby,'') as qmsstatusby , ifnull(date_format(bs.qmsstatuson,'%m/%d/%Y'),'') as qmsstatuson, ifnull(bs.hprdecision,'') as hprstatus , ifnull(bs.hprresult,0) as hprresult, replace(ifnull(bs.hprslidereviewed,''),'_','') as hprslidereviewed, ifnull(bs.hprby,'') as hprby , ifnull(date_format(bs.hpron,'%m/%d/%Y'),'') as hpron, trim(ifnull(bs.biosamplecomment,'')) as biosamplecomment, trim(ifnull(bs.questionHPR,'')) as questionhpr FROM masterrecord.ut_procure_biosample bs left join (SELECT menuvalue, if(ifnull(longvalue,'') = '', ifnull(dspvalue,''), ifnull(longvalue,'')) as dspinstitution FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on bs.procureInstitution = inst.menuvalue left join (SELECT  menuvalue, ifnull(dspvalue,'') as ageuomdsp  FROM four.sys_master_menus where menu = 'AGEUOM') as auom on bs.pxiAgeUOM = auom.menuvalue left join (SELECT menuvalue, ifnull(dspvalue,'') as sexdsp  FROM four.sys_master_menus where menu = 'PXSEX') as sx on bs.pxiGender = sx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') as cx on bs.chemoind = cx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') as rx on bs.radind = rx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INFC') as ic on bs.informedconsent = ic.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as pr on bs.pathreport = pr.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') as pt on bs.proctype = pt.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'COLLECTIONT') as ct on bs.collectiontype = ct.menuvalue where bs.pbiosample = :biog";    
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
