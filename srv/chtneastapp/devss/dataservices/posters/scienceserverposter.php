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
          
     (  trim($pdta['targetindvalue']) === "N" &&  trim($pdta['notrcvdnote']) === ""  ) ? (list( $errorInd, $msgArr[] ) = array(1 , "WHEN SPECIFYING AN ENCOUNTER WAS NOT RECEIVED, YOU MUST SUPPLY A REASON" )) : "";
     //The 'Last Four' field is Optional
     
     if ( $errorInd === 0 ) { 
         //SAVE ENCOUNTER TO HISTORY
         $copySQL = "insert into four.tmp_ORListing_history (location,ListDate,PXICode,startTime,room,surgeons,pxiIni,lastfourmrn,pxiAge,ageuomcode,pxiRace,pxiSex,procText,targetInd,infcInd, lastupdatedby, lastupdateon, ORListID,ORKey,linkedDonor,linkBy,delinkedDonor,delinkBy,BSNbr_old,oripxiRace_old,oriTargetInd_old,caseNotes_old)  SELECT * FROM four.tmp_ORListing where location = :loc and PXICode = :eid";
         $copyRS = $conn->prepare($copySQL);
         $copyRS->execute(array(':loc' => $presUserInst, ':eid' => $eid));
//         $dta[] = $copyRS->debugDumpParams();
         if ( $copyRS->rowCount() > 0) { 
            //TODO:  FIX AGE UOM Value
            $updSQL = "update four.tmp_ORListing set targetInd = :target, infcInd = :infc, pxiAge = :pxage, ageuomcode = :pxageuom, pxiRace = :pxrace, pxiSex = :pxsex, lastfourmrn = :lastfour, lastupdatedby = :lastby, lastupdateon = now() where location = :presInst and PXICode = :pxicode"; 
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
            $updR->execute(array( ':target' => $tg ,':infc' => $pdta['informedindvalue'], ':pxage' => $pdta['age'],':pxageuom' => $pdta['ageuomvalue'],':pxrace' => $pdta['racevalue'],':pxsex' => substr($pdta['sex'],0,1), ':lastfour' => $pdta['lastfour'], ':lastby' => $oAccount, ':presInst' => $presUserInst, ':pxicode' => $eid ));
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
       $dta['linkeddonor'] = $r['linkeddonorind']; 
       $dta['delinkeddonor'] = $r['delinkeddonorind'];

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

  function   sitesbyspecimencategory($request, $passdata) { 
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

          $sqlstmt = "SELECT {$select} FROM {$from} {$where} {$groupby} {$orderby}";
          $valuelist = $rqjson['request']['valuelist'];
          $rs = $conn->prepare($sqlstmt); 
          $rs->execute($valuelist);
          $itemsfound = $rs->rowCount();  
          while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
            $dta[] = $r;
          }
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
      //{"request":{"valuelist":{":bcrpbiosample":"82555",":bcrinstitution":"HUP"},"wherelist":{"0":"subtbl.pbiosample = :bcrpbiosample","1":"procurementinstitution = :bcrinstitution"},"typeofrequest":"PDF","requestedreporturl":"barcoderun"}}
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
            $msgArr[] = "THE HPR TRAY SCANCODE MISSING IS NOT VALID";               
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
