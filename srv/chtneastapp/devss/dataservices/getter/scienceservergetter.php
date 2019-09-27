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
 
  function inventoryhierarchy() { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];

     if ( $authuser === serverIdent) { 
         $cSQL = "SELECT locationid, scancode, typeolocation, locationdsp, ifnull(hierarchybottomind,0) as hierarchybottomind, ifnull(mastercontainerind,0) as mastercontainerind FROM four.sys_inventoryLocations where parentid = :parentid and ifnull(activelocation,0) = 1 and ifnull(physicalLocationInd,0) = 1 and mappableInd = 1 order by typeolocation, locationdsp";
         $ccSQL = "SELECT locationid, scancode, typeolocation, locationdsp, ifnull(hierarchybottomind,0) as hierarchybottomind, ifnull(mastercontainerind,0) as mastercontainerind FROM four.sys_inventoryLocations where parentid = :parentid and ifnull(activelocation,0) = 1 and ifnull(physicalLocationInd,0) = 1 order by typeolocation, locationdsp";
         $cRS = $conn->prepare($cSQL); 
         $ccRS = $conn->prepare($ccSQL); 
         $cccRS = $conn->prepare($ccSQL); 
         $ccccRS = $conn->prepare($ccSQL); 
         
         $levelCount = 0;
         $rootLocSQL = "SELECT locationid, locationdsp, scancode FROM four.sys_inventoryLocations where typeolocation = :whichroot and activelocation = 1";  
         $rlRS = $conn->prepare( $rootLocSQL );
         $rlRS->execute( array(':whichroot' => 'ROOT'));
         if ( $rlRS->rowCount() > 0 ) {
          $levelCount++;   
          while ($r = $rlRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta['root'] = $r;
              $cRS->execute(array(':parentid' => $r['locationid'])); 
              if ( $cRS->rowCount() > 0 ) {
                $cnt = 0;  
                while ( $c = $cRS->fetch(PDO::FETCH_ASSOC)) { 
                    $dta['root']['child'][$cnt]['locationid'] = $c['locationid'];
                    $dta['root']['child'][$cnt]['scancode'] = $c['scancode'];
                    $dta['root']['child'][$cnt]['locationtype'] = $c['typeolocation'];
                    $dta['root']['child'][$cnt]['locationdsp'] = $c['locationdsp'];
                    $dta['root']['child'][$cnt]['hierarchybotom'] = $c['hierarchybottomind'];
                    $dta['root']['child'][$cnt]['containerind'] = $c['mastercontainerind'];
                   
                    $ccRS->execute(array(':parentid' => $c['locationid']));
                    if ( $ccRS->rowCount() > 0 ) { 
                      $cntc = 0; 
                      while ($cc = $ccRS->fetch(PDO::FETCH_ASSOC)) { 
                        $dta['root']['child'][$cnt]['child'][$cntc]['locationid'] = $cc['locationid'];
                        $dta['root']['child'][$cnt]['child'][$cntc]['scancode'] = $cc['scancode'];
                        $dta['root']['child'][$cnt]['child'][$cntc]['locationdsp'] = $cc['locationdsp'];
                        $dta['root']['child'][$cnt]['child'][$cntc]['locationtype'] = $cc['typeolocation'];
                        $dta['root']['child'][$cnt]['child'][$cntc]['hierarchybottom'] = $cc['hierarchybottomind'];
                        $dta['root']['child'][$cnt]['child'][$cntc]['containerind'] = $cc['mastercontainerind'];

                        $cccRS->execute(array(':parentid' => $cc['locationid']));
                        if ( $cccRS->rowCount() > 0 ) { 
                          $cntcc = 0; 
                          while ($ccc = $cccRS->fetch(PDO::FETCH_ASSOC)) { 
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['locationid'] = $ccc['locationid'];
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['scancode'] = $ccc['scancode'];
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['locationdsp'] = $ccc['locationdsp'];
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['locationtype'] = $ccc['typeolocation'];
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['hierarchybottom'] = $ccc['hierarchybottomind'];
                            $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['containerind'] = $ccc['mastercontainerind'];

                              $ccccRS->execute(array(':parentid' => $ccc['locationid']));
                              if ( $ccccRS->rowCount() > 0 ) { 
                                $cntccc = 0; 
                                while ($cccc = $ccccRS->fetch(PDO::FETCH_ASSOC)) { 
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['locationid'] = $cccc['locationid'];
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['scancode'] = $cccc['scancode'];
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['locationdsp'] = $cccc['locationdsp'];
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['locationtype'] = $cccc['typeolocation'];
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['hierarchybottom'] = $cccc['hierarchybottomind'];
                                  $dta['root']['child'][$cnt]['child'][$cntc]['child'][$cntcc]['child'][$cntccc]['containerind'] = $cccc['mastercontainerind'];
                                  $cntccc++;
                                }
                              }
                            $cntcc++;
                          }
                        }
                        $cntc++;    
                      }
                    } 
                    $cnt++;
                }
              }


          }      
         } else { 
            //BIG ERROR
         }
         $itemsfound = $levelCount;
         $responseCode = 200;
     }                
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;     
  }  

  function inventorysimplehprtraylist() { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "Zack was here";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     if ( $authuser === serverIdent) {
          $prnSQL = "SELECT ifnull(loc.scancode,'') as scancode, concat(ifnull(loc.locationdsp,''), if(ifnull(sts.longvalue,'')='','', concat(' (',ifnull(sts.longvalue,''),')'))) as hprstatus FROM four.sys_inventoryLocations loc left join (SELECT dspvalue, longValue FROM four.sys_master_menus where menu = 'HPRTrayStatus') sts on loc.hprtraystatus = sts.dspvalue where parentid = 293 order by hprstatus";     
          $prnRS = $conn->prepare($prnSQL);
          $prnRS->execute();
          while ($r = $prnRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
     }                
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;     
  }  


  function hprreviewerlist() {
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "Zack was here";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     if ( $authuser === serverIdent) {
          $prnSQL = "SELECT userid, displayname FROM four.sys_userbase where allowhprreview =1";     
          $prnRS = $conn->prepare($prnSQL);
          $prnRS->execute();
          while ($r = $prnRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
     }                
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;     
  }

  function thermalprinterlist() {
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "Zack was here";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     if ( $authuser === serverIdent) {
          $prnSQL = "SELECT concat(ifnull(printername,''), ' (', ifnull(printerlocation,''), ')') as printer, formatname FROM serverControls.lblFormats where dspPrinter = 1";     
          $prnRS = $conn->prepare($prnSQL);
          $prnRS->execute();
          while ($r = $prnRS->fetch(PDO::FETCH_ASSOC)) { 
              $dta[] = $r;
          }
     }                
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;     
  }  
    
  function printthermallabellist() { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     
     if ( $authuser === serverIdent) { 
        $getSQL = "SELECT  printid as printorderid, ifnull(lbl.labelRequested,'') as lblRqst, ifnull(lbl.printerRequested,'') as printerrqst, ifnull(lbl.dataStringpayload,'') as datapayload, ifnull(lbl.bywho,'UNKNOWNRQSTR') as bywho, ifnull(lbl.onwhen,now()) as onwhen, lbl.printid, ifnull(fm.formatText,'') as formatText FROM serverControls.lblToPrint lbl left join serverControls.lblFormats fm on lbl.labelRequested = fm.formatname where ifnull(lbl.datastringpayload,'') <> '' and ifnull(lbl.printerRequested,'') <> '' and ifnull(fm.formatText,'') <> '' ";
        $getLbl = $conn->prepare($getSQL);
        $getLbl->execute();
        $itemsfound = $getLbl->rowCount();
        while ($r = $getLbl->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $r;
        }
        $dta = $rows;
        //BACKUP AND DELETE HERE
        $mvSQL = "insert into serverControls.lblPrinted (printID,labelRequested,printerRequested,dataStringpayload,byWho,onWhen,printedOn) select printID,labelRequested,printerRequested,dataStringpayload,byWho,onWhen, now() from serverControls.lblToPrint"; 
        $mvR = $conn->prepare($mvSQL); 
        $mvR->execute(); 
        $delSQL = "delete FROM serverControls.lblToPrint"; 
        $delR = $conn->prepare($delSQL);
        $delR->execute();
        $responseCode = 200;
     }
     
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;       
  }  
    
  function chtneasternenvironmentalmetrics( $whichobj, $urirqst ) { 
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];

     $sensorListSQL = "SELECT ifnull(menuValue,'') as sensorid, ifnull(longValue,'') as sensorname FROM four.sys_master_menus where menu = :sensorlistid and dspInd = 1 order by dspOrder";
     $sensorListRS = $conn->prepare($sensorListSQL);
     $sensorListRS->execute(array(':sensorlistid' => 'CORISSENSORLIST'));

     $sensor = array();
     $cntr = 0;
     while ($sensors = $sensorListRS->fetch(PDO::FETCH_ASSOC)) { 
       $id =  $sensors['sensorid']; 
       $sensor[$id]['sensorname'] = $sensors['sensorname'];
       $sensorDtaSQL = "select utctimestamp, sensorid, corisnamelabel as namelabel, readinginc, date_format(onwhen, '%H:%i') as gathertime, date_format(onwhen, '%m/%d/%Y') as gatherdate, date_format(onwhen,'%b %D, %Y :: %h:%i %p') as dtegathered from (SELECT @row := @row +1 AS rownum, s.* FROM (SELECT @row :=0) r, four.enviro_coris_lastweek s where s.sensorid = :sensorid order by s.onwhen desc ) as sensorrows where rownum % 9 = 1 limit 0,5";
       $sensorDtaRS = $conn->prepare($sensorDtaSQL); 
       $sensorDtaRS->execute(array(':sensorid' => $sensors['sensorid']));
       $readings = array();
       while ( $sread = $sensorDtaRS->fetch(PDO::FETCH_ASSOC)) { 
         $readings[] = $sread;
       }
       $sensor[$id]['readings'] = $readings;
       $cntr++;
     }
     $dta = $sensor;
     $itemsfound = $cntr;

     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;       
  }

  function chtnstaffdirectorylisting($whichobj, $urirqst) { 
       
     $responseCode = 400;
     $rows = array();
     $msgArr = array(); 
     $errorInd = 0;
     $msg = "BAD REQUEST";
     $itemsfound = 0;
     require(serverkeys . "/sspdo.zck");
     $authuser = $_SERVER['PHP_AUTH_USER']; 
     $authpw = $_SERVER['PHP_AUTH_PW'];
     
     if ($authuser === 'chtneast') {       
        if ((int)checkPostingUser($authuser, $authpw) === 200) {
            $directorySQL = "SELECT concat(ifnull(if(ifnull(inst.longvalue,'') = '', inst.dspvalue, inst.longvalue),''), ' (', ifnull(primaryinstcode,''), ')') as institution, substr(concat('00000',userid),-6) as userid , ifnull(emailaddress,'ERROR') as emailaddress, ifnull(originalaccountname,'') as originalaccountname, ifnull(username,'') as username, ifnull(profilephone,'') as profilephone, ifnull(profilepicurl,'') as profilepicurl, ifnull(dspAlternateInDir,0) as dspalternateindir, if( ifnull(dspAlternateInDir,0) = 1, ifnull(profilealtemail,''),'') as profilealtemail, if( ifnull(dspAlternateInDir,0) = 1, ifnull(altphone,''), '') as altphone, if( ifnull(dspAlternateInDir,0) = 1, ifnull(altphonetype,''), '') as altphonetype, ifnull(dspjobtitle,'') as dsptitle FROM four.sys_userbase usr left join (SELECT * FROM four.sys_master_menus where menu = 'INSTITUTION') inst on usr.primaryInstCode = inst.menuValue where allowind = 1 and dspindirectory = 1 order by institution asc, lastname";   
            $directoryRS = $conn->prepare($directorySQL);
            $directoryRS->execute(); 
            while ($r = $directoryRS->fetch(PDO::FETCH_ASSOC)) { 
               $rows[] = $r; 
            }            
            $dta = $rows;
            $msg = "";
            $responseCode = 200;
        } 
     }
     $rows['statusCode'] = $responseCode; 
     $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
     return $rows;       
  }  
    
  function gethelpticketdialog($whichobj, $urirqst) { 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $msgArr = array();
    $itemsfound = 0;
    $pdta['authuser'] = $_SERVER['PHP_AUTH_USER'];
    $dta = array('pagecontent' => bldDialogGetter('dialogHelpTicket',json_encode($pdta)));
    ( trim($dta) !== "" ) ? $responseCode = 200 : "";
    $msg = $msgArr;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;      
  }

  function searchhelpresults($whichobj, $urirqst) { 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $msgArr = array();
    $modArray = array();
    $itemsfound = 0;
    require(serverkeys . "/sspdo.zck");
    $headSQL = "select  objid, bywho, date_format(onwhen, '%M %d, %Y %H:%i') as onwhendsp, srchterm from four.objsrchdocument where doctype = 'HELP-SEARCH-REQUEST' and objid = :objid"; 
    $headR = $conn->prepare($headSQL);
    $headR->execute(array(':objid' => $whichobj));
    if ($headR->rowCount() < 1) { 
      $responseCode = 404;
      $msg = "BIOGROUP SEARCH ID OBJECT NOT FOUND";
    } else { 
      $dta['head'] = $headR->fetch(PDO::FETCH_ASSOC);
      $searchTerm = $dta['head']['srchterm'];
      $rsltArr = runhelpsearchquery($searchTerm); 
      $dta['searchresults'] = $rsltArr;
      $itemsfound = count($rsltArr);
      $msg = "";
      $responseCode = 200;
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;      
  }

  function sshlptopiclist($request, $urirqst) { 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $msgArr = array();
    $modArray = array();
    $itemsfound = 0;
    require(serverkeys . "/sspdo.zck");
    $modListSQL = "SELECT moduleid, module FROM four.base_ssv7_help_index_modulelist where dspind = 1 order by dsporder";
    $modListRS = $conn->prepare($modListSQL); 
    $modListRS->execute();
    if ($modListRS->rowCount() > 0) { 
    $modArrCnt = 0;
      while ($modList = $modListRS->fetch(PDO::FETCH_ASSOC)) { 
          $modArray[$modArrCnt]['moduleid'] = $modList['moduleid'];
          $modArray[$modArrCnt]['module'] = $modList['module'];
          $topicArray = array();
          $topicSQL = "SELECT hlpid, helptype, helpurl, screenreference, title  FROM four.base_ss7_help where helpdspind = 1 and (helptype = 'SCREEN' or helptype = 'TOPIC' or helptype = 'PDF') and belongstoindexid = :modIndex order by helpdsporder";
          $topicRS = $conn->prepare($topicSQL);
          $topicRS->execute(array(':modIndex' => $modList['moduleid'])); 
          if ($topicRS->rowCount() > 0 ) { 
              $topicCnt = 0;
              while ($topics = $topicRS->fetch(PDO::FETCH_ASSOC)) { 
                  $topicArray[$topicCnt]['helpid'] = $topics['hlpid'];
                  $topicArray[$topicCnt]['topictype'] = $topics['helptype'];
                  $topicArray[$topicCnt]['topicurl'] = $topics['helpurl'];
                  $topicArray[$topicCnt]['screenref'] = $topics['screenreference'];
                  $topicArray[$topicCnt]['topictitle'] = $topics['title'];
                  $funcSQL = "SELECT hlpid, screenreference, title, helpurl FROM four.base_ss7_help where helpdspind = 1 and helptype = 'FUNCTIONALITYDESC' and belongstoindexid = :topicid order by helpdsporder";
                  $funcRS = $conn->prepare($funcSQL); 
                  $funcRS->execute(array(':topicid' => $topics['hlpid']));
                  $funcArray = array();
                  if ($funcRS->rowCount() > 0) { 
                    $funcCnt = 0; 
                    while ($func = $funcRS->fetch(PDO::FETCH_ASSOC)) { 
                      $funcArray[] = $func;
                    }
                    $topicArray[$topicCnt]['functionslist'] = $funcArray;
                  } else { 
                    $topicArray[$topicCnt]['functionslist'] = $funcArray;
                  }
                $topicCnt++;
              }
              $modArray[$modArrCnt]['topics'] = $topicArray;
          } else {
              $modArray[$modArrCnt]['topics'] = $topicArray;
          }
        $modArrCnt++;
      }
      $dta = $modArray;
    } 
    $msg = $msgArr;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;      
  }

  function preprocessdialogaddphi($request, $urirqst) { 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $msgArr = array();
    $itemsfound = 0;
    
    $dta = array('pagecontent' => bldDialogGetter('dialogPBAddDelink','') );

    ( trim($dta) !== "" ) ? $responseCode = 200 : "";

    $msg = $msgArr;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;      
  } 

  function preprocesspathologyrptupload($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $orlist = array();
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $msgArr = array();
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $orrqst = explode("/", $urirqst);
   session_start(); 
   $usr = chkUserBySession(session_id());
   if ((int)$usr['allowind'] === 1 && (int)$usr['allowlinux'] === 1 && (int)$usr['allowcoord'] === 1  && (int)$usr['daysuntilpasswordexpire'] > 0 ) {
     $usrident = $usr['emailaddress'];
     if ( trim($orrqst[3]) <> "") {
          $pdta['user'] = $usrident;
          $pdta['sessionid'] = session_id();
          $pdta['labelnbr'] = $orrqst[3];
          $chkSQL = "SELECT sg.bgs, bs.pbiosample, bs.pathReport, ifnull(bs.pathreportid,0) as pathreportid, concat(trim(ifnull(bs.anatomicSite,'')), if(trim(ifnull(bs.tissType,''))='','', concat(' (',trim(ifnull(bs.tissType,'')),')'))) site, concat(trim(ifnull(bs.diagnosis,'')), if(trim(ifnull(bs.subdiagnos,''))='','', concat( ' :: ' , trim(ifnull(bs.subdiagnos,''))))) as diagnosis, ifnull(bs.procureInstitution,'') as procinstitution, ifnull(date_format(bs.procedureDate,'%m/%d/%Y'),'') as proceduredate, concat( if(trim(ifnull(bs.pxiAge,'')) = '', '-',trim(ifnull(bs.pxiAge,''))),'::',if(trim(ifnull(bs.pxiRace,''))='','-',ucase(trim(ifnull(bs.pxiRace,'')))),'::',if(trim(ifnull(bs.pxiGender,'')) = '','-',ucase(trim(ifnull(bs.pxiGender,''))))) ars, ifnull(bs.pxiid,'NOPXI') as pxiid FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where replace(bgs,'T_','') = :labelnbr";
          $rs = $conn->prepare($chkSQL);
          $rs->execute(array(':labelnbr' => trim($orrqst[3]))); 
          if ($rs->rowCount() === 1) {
            $r = $rs->fetch(PDO::FETCH_ASSOC);
            $pdta['pbiosample'] = $r['pbiosample'];
            $pdta['pathreportind'] = $r['pathReport'];
            $pdta['pathreportid'] = $r['pathreportid'];
            $pdta['site'] = $r['site'];
            $pdta['diagnosis'] = $r['diagnosis'];
            $pdta['procinstitution'] = $r['procinstitution'];
            $pdta['proceduredate'] = $r['proceduredate'];
            $pdta['ars'] = $r['ars'];
            $pdta['pxiid'] = $r['pxiid'];
          } else { 
            $pdta['pbiosample'] = "";
            $pdta['pathreportind'] = "";
            $pdta['pathreportid'] = "";
            $pdta['site'] = "";
            $pdta['diagnosis'] = "";
            $pdta['procinstitution'] = "";
            $pdta['proceduredate'] = "";
            $pdta['ars'] = "";
          }
          $dta = array('pagecontent' => bldDialogGetter('dataCoordUploadPR', $pdta) ); 
          $responseCode = 200;
     } else { 
       $msgArr[] .= "The Biogroup Identifier is incorrect - See a CHTN Informatics Staff Member";
     }
   } else { 
     $responseCode = 503; 
     $msgArr[] .= "USER NOT ALLOWED";
   }
   $msg = $msgArr;
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;      
  }

  function simpleorschedulewrapper($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $orlist = array();
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $orrqst = explode("/", $urirqst);
   session_start(); 
   $usr = chkUserBySession(session_id());
   if ((int)$usr['allowind'] === 1 && (int)$usr['allowproc'] === 1) { 
     $inst = $usr['presentinstitution'];
     $rtndta = self::simpleorschedule("","/data-service/simple-or-schedule/{$inst}/{$orrqst[3]}");
     $itemsfound = $rtndta['data']['ITEMSFOUND'];
     $dta = $rtndta['data']['DATA'];
     $msg = "";
     $responseCode = 200;
   }
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;      
  }

  function simpleorschedule($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $orlist = array();
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $orrqst = explode("/", $urirqst);

   if (trim($orrqst[3]) !== "" && trim($orrqst[4]) !== "") { 

       //TODO: MAKE CHECKS OF DATA TYPE OF REQUEST
       //TODO: MAKE SURE USER HAS RIGHT TO SEE INSTITUTION

       $orSQL = "SELECT ifnull(pxicode,'ERROR') as pxicode, ifnull(targetind,'') as targetind, if(ifnull(infcind,1)=0,1, ifnull(infcind,1))  as informedconsentindicator , if(linkeddonor = 1 or delinkeddonor = 1,'X','-') as linkage, ucase(ifnull(pxiini,'NO INITIALS')) as pxiinitials, ifnull(lastfourmrn,'0000') as lastfourmrn, ifnull(pxiage,'') as pxiage, ucase(ifnull(pxirace,'')) as pxirace, ucase(ifnull(pxisex,'')) as pxisex, trim(concat(ifnull(pxiage,'-'), '/', ucase(substr(ifnull(pxirace,'-'),1,4)), '/', ucase(ifnull(pxisex,'-')))) as ars, ifnull(starttime,'') starttime, ifnull(room,'') room, ucase(ifnull(surgeons,'')) surgeon, trim(ifnull(proctext,'')) as proceduretext, ifnull(studysubjectnbr,'') studysubjectnbr, ifnull(studyprotocolnbr,'') studyprotocolnbr, ifnull(chemotherapyind,'') as cx, ifnull(radiationind, '') as rx, ifnull(upennsogi,'') as sogi  FROM four.tmp_ORListing ors where date_format(listdate,'%Y%m%d') = :ordate and location = :orinstitution order by pxiini";
       $orR = $conn->prepare($orSQL);
       $orR->execute(array(':ordate' => $orrqst[4], ':orinstitution' => $orrqst[3])); 
       $itemsfound = $orR->rowCount();    
       $dta['requestDate'] = $orrqst[4];
       $dta['institution'] = $orrqst[3];
       while ($r = $orR->fetch(PDO::FETCH_ASSOC)) { 
         $orlist[] = $r;
       }
       $dta['orlisting'] = $orlist;
       $responseCode = 200;
       $msg = "";
   }

   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;      
  }

 function groupreportlisting($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $headSQL = "SELECT groupid, groupingname, groupingdescriptions FROM four.ut_reportgrouping rg where replace(groupingurl,'-','') = :groupurl and dspind = 1";
   $headRS = $conn->prepare($headSQL);
   $headRS->execute(array(':groupurl' => $request)); 
   if ($headRS->rowCount() === 1) { 
      $head = $headRS->fetch(PDO::FETCH_ASSOC);
      $grpid = $head['groupid'];
      $grpname = $head['groupingname']; 
      $grpdesc = $head['groupingdescriptions'];
      
      $subSQL = "SELECT ifnull(rg.groupingurl,'') as groupingurl,  ifnull(rl.urlpath,'') as urlpath, ifnull(rl.reportname,'') as reportname, ifnull(rl.reportdescription,'') as reportdescription, ifnull(rl.bywhom,'') as bywhom, date_format(rl.onwhen, '%m/%d/%Y') as onwhen FROM four.ut_reportlist rl left join four.ut_reportgrouping rg on rl.groupingid = rg.groupid  where rl.groupingid = :groupid and rl.dspind = 1  and rl.systemrptInd = 0 order by rl.dsporder";
      $subRS = $conn->prepare($subSQL);
      $subRS->execute(array(':groupid' => $grpid));
      $rptlist = array(); 
      while ($rr = $subRS->fetch(PDO::FETCH_ASSOC)) { 
          $rptlist[] = $rr;
      }
       $dta[] = array(
                       'groupid ' => $grpid 
                       ,'groupname' => $grpname
                       ,'groupdesc' => $grpdesc
                       ,'rptlist' => $rptlist
               );
       $msg = "";
       $responseCode = 200;
   } 
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;      
 }   

 function reportgrouplisting($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   require(serverkeys . "/sspdo.zck");
   $sql = "SELECT groupingurl, groupingname, groupingdescriptions FROM four.ut_reportgrouping where dspind = 1 order by orderind";
   $rs = $conn->prepare($sql); 
   $rs->execute();
   $itemsfound = $rs->rowCount(); 
   while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
     $dta[] = $r;
   }
   $responseCode = 200;
   $msg = "";
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;    
 } 

 function reportcriteriafielddefinition($request, $urirqst) { 
   $rows = array(); 
   $dta = array(); 
   $responseCode = 400; 
   $msg = "BAD REQUEST";
   $itemsfound = 0;
   $rq = explode("/",$urirqst); 
   if (trim($rq[3]) !== "") { 
      require(serverkeys . "/sspdo.zck");
      $sql = "SELECT ifnull(flddisplay,'') flddisplay, ifnull(typeoffield,'string') as typeoffield, ifnull(menuurl,'') as menuurl, ifnull(ondemandmenu,'') as ondemandmenu, ifnull(fielddspwidth,20) as flddspwidth, ifnull(fieldnote,'') as fieldnote FROM four.ut_report_parameterfielddefinitions where fldname = :fld";
      $rs = $conn->prepare($sql); 
      $rs->execute(array(':fld' => trim($rq[3])));
      if ($rs->rowCount() === 1) { 
        $dta[] = $rs->fetch(PDO::FETCH_ASSOC);
        $responseCode = 200; 
        $msg = "";
        $itemsfound = 1;
      }
   }
   $rows['statusCode'] = $responseCode; 
   $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
   return $rows;    
 } 

 function reportparts($request, $urirqst) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst); 
    if (trim($rq[3]) !== "") { 
      require(serverkeys . "/sspdo.zck");
      $objsql = <<<SQLSTMT
SELECT 
ifnull(ob.bywho,'') as bywho
, ifnull(date_format(ob.onwhen,'%m/%d/%Y'),'') onwhen
, ifnull(ob.reportmodule,'') reportmodule
, ifnull(ob.reportname,'') reportname
, ifnull(ob.requestjson,'') requestjson
, ifnull(ob.typeofreportrequested,'') typeofreportrequested
, ifnull(rl.reportname,'') as dspreportname
, ifnull(rl.reportdescription,'') as dspreportdescription
, ifnull(rl.bywhom,'') as rptcreator
, ifnull(date_format(rl.onwhen,'%M %d, %Y'),'') as rptcreatedon
, ifnull(rl.accesslvl,100) as rqaccesslvl
, ifnull(rg.groupingname,'') as groupingname 
FROM four.objsrchdocument ob 
left join four.ut_reportlist rl on ob.reportname = rl.urlpath 
left join four.ut_reportgrouping rg on rl.groupingid = rg.groupid 
where objid = :objid 
and doctype = :doctype 
and rl.dspind = 1
SQLSTMT;
      $objRS = $conn->prepare($objsql); 
      $objRS->execute(array(':objid' => trim($rq[3]), ':doctype' => 'REPORTREQUEST')); 
      if ($objRS->rowCount() <> 1) {
      } else {
        $dta = $objRS->fetch(PDO::FETCH_ASSOC);
        $responseCode = 200; 
        $msg = "";
        $itemsfound = 1;
      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }

 function reportdefinition($request, $urirqst) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst);
    if (trim($rq[3]) !== "") { 
      require(serverkeys . "/sspdo.zck");
      $rptSQL = "SELECT ifnull(rptgrp.groupingname,'') groupingname, ifnull(rptgrp.groupingurl,'') groupingurl, ifnull(rptlst.urlpath,'') as rpturl, ifnull(rptlst.reportname,'ERROR') reportname, ifnull(rptlst.reportdescription,'') reportdescription, ifnull(rptlst.accesslvl,100) as accesslvl, ifnull(rptlst.allowgriddsp,0) allowgriddsp, ifnull(rptlst.allowpdf,0) allowpdf, ifnull(rptlst.changecritind,0) changecritind, ifnull(rptlst.bywhom,'') bywhom, ifnull(date_format(rptlst.onwhen,'%M %d, %Y'),'') as rptcriteriacreation FROM four.ut_reportlist rptlst left join four.ut_reportgrouping rptgrp on rptlst.groupingid = rptgrp.groupid where urlpath = :rpturlid and rptlst.dspind = 1"; 
      $rs = $conn->prepare($rptSQL); 
      $rs->execute(array(':rpturlid' => $rq[3]));
      if ($rs->rowCount() <> 1) {
      } else {
        $r = $rs->fetch(PDO::FETCH_ASSOC);

        $crit = array();
        $critsql = "SELECT parameterid, ifnull(requiredind,1) as requiredind, ifnull(includenondsp,0) as includenondsp, ifnull(dsponinclude,'') as dsponinclude, ifnull(sqltextline,'') as sqltextline FROM four.ut_report_parameterlisting where reporturl = :rpturlid and dspind = 1 order by dsporder";
        $critRS = $conn->prepare($critsql);
        $critRS->execute(array(':rpturlid' => $rq[3]));
        while ($c = $critRS->fetch(PDO::FETCH_ASSOC)) { 
          $crit[] = $c;
        }        
        $dta['groupingname'] = $r['groupingname'];
        $dta['groupingurl'] = $r['groupingurl'];
        $dta['reporturl'] = $r['rpturl'];
        $dta['reportname'] = $r['reportname'];
        $dta['reportdescription'] = $r['reportdescription'];
        $dta['accesslvl'] = $r['accesslvl'];
        $dta['allowgriddsp'] = $r['allowgriddsp'];
        $dta['allowpdfdsp'] = $r['allowpdf'];
        $dta['bywhom'] = $r['bywhom'];
        $dta['rptcreation'] = $r['rptcriteriacreation'];
        $dta['criteria'] = $crit;  

        $responseCode = 200; 
        $msg = "";
        $itemsfound = 1;
      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }

 //do = Data Object
 function pasthprbysegment($request, $urirqst) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst);
    if (trim($rq[3]) === ""  || !is_numeric($rq[3])) { 
    } else { 
      $segid = $rq[3];
      $defineSQL = <<<OBJECTSQL
SELECT hpr.biohpr biohprid, ifnull(hpr.bgs,'') slide, ifnull(hpr.reviewer,'') reviewer, ifnull(date_format(hpr.reviewedOn,'%m/%d/%Y'),'') reviewedon, ifnull(hpr.decision,'') decision, concat(ifnull(hpr.site,''), if(ifnull(hpr.dx,'')='','', concat('::',ifnull(hpr.dx,'')))) as desig, ifnull(hpr.speccat,'') as speccat, ifnull(hpr.biosamplecomments,'') as reviewercomments FROM (SELECT biosamplelabel FROM masterrecord.ut_procure_segment where segmentid = :objectid) sg left join masterrecord.ut_hpr_biosample hpr on sg.biosamplelabel = hpr.bioGroupId order by hpr.reviewedOn desc
OBJECTSQL;
      $obj = runObjectSQL($defineSQL, $segid);
      if (count($obj) > 0) { 
         $dta = $obj;
         $responseCode = 200;
         $itemsfound = count($obj);
         $msg = ""; 
      } else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }

 function biogroupsegmentshortlisting($request, $urirqst) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst);
    if (trim($rq[3]) === ""  || !is_numeric($rq[3])) { 
    } else { 
      $segid = $rq[3];
      $defineSQL = <<<OBJECTSQL
select ifnull(sg.bgs,'NOSEGMENT#') as bgs, ifnull(sgs.dspvalue,'NO STATUS') as segstatus, ifnull(prepmethod,'NO PREP') as prepmethod, trim(if(ifnull(metric,'') = '', '', concat(ifnull(metric,''),' ', ifnull(muom.longvalue,'')))) as metricdsp, trim(concat(ifnull(i.investid,''), ucase(  if(ifnull(i.lname,'')='','', concat('/',ifnull(i.lname,'')))))) as invest from (SELECT biosamplelabel FROM masterrecord.ut_procure_segment where segmentid = :objectid) as primtbl left join masterrecord.ut_procure_segment sg on primtbl.biosamplelabel = sg.biosamplelabel left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') sgs on sg.segstatus = sgs.menuvalue left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') muom on sg.metricuom = muom.menuvalue left join (SELECT investid , ifnull(invest_lname,'') lname , ifnull(invest_fname,'') fname, ifnull(invest_homeinstitute,'') homeinst FROM vandyinvest.invest) i on sg.assignedto = i.investid order by sg.bgs asc
OBJECTSQL;
      $obj = runObjectSQL($defineSQL, $segid);
      if (count($obj) > 0) { 
         $dta = $obj;
         $responseCode = 200;
         $itemsfound = count($obj);
         $msg = ""; 
      } else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
      }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }

 function getpasthprreviewsbybiogroupsingleline ( $request, $urirqst ) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    if ( trim($request) === "" ) { 
    } else {
      $objectid = $request;  
      $defineSQL = <<<OBJECTSQL
SELECT biohpr,  ifnull(hpr.bgs,'') as slideread, ifnull(hpr.reviewer,'') as reviewer, date_format(hpr.reviewedOn,'%m/%d/%Y') as reviewedon, ifnull(hpr.decision,'') as decision, ifnull(hpr.vocabularydecision,'') as vocabularydecision, ifnull(hpr.speccat,'') as speccat, trim(concat(ifnull(hpr.site,''), if (ifnull(subsite,'')='','', concat(' (' , ifnull(hpr.subsite,'') , ')')))) as site, trim(concat(ifnull(hpr.dx,''), if( ifnull(hpr.subdiagnosis,'') = '','', concat(' [',ifnull(hpr.subdiagnosis,''),']')))) as dx FROM masterrecord.ut_hpr_biosample hpr where hpr.bioGroupId  = :objectid order by biohpr desc
OBJECTSQL;
     $obj = runObjectSQL($defineSQL, $objectid);       
     $itemsfound = count($obj);  
     if (count($obj) > 0) { 
         $dta = $obj;
         $responseCode = 200;
         $msg = ""; 
     }  else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
     }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }


 function getassgroupfromseg ( $request, $urirqst ) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
 
    if ( trim($request) === "" ) { 
    } else {
        $segid = $request;
        //, ifnull(uni.dspvalue,'')  as uninvolvedind
        //left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'UNINVOLVEDIND') uni on bs.uninvolvedInd = uni.menuvalue 
        //,  ifnull(uni.dspvalue,'')
      $defineSQL = <<<OBJECTSQL
select bs.associd, bs.read_label as bsreadlabel, concat( ifnull(bs.pxiage,''),'/',ifnull(bs.pxirace,''),'/', ifnull(bs.pxigender,'')) as ars, trim(concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'')='','',concat(' (',ifnull(bs.subsite,''),')')))) as site, trim(concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'') = '','',concat(' [,',ifnull(bs.subdiagnos,''),']')))) as dx, trim(ifnull(bs.metsSite,'')) as metssite, ifnull(bs.tissType,'') as specimencategory, bs.hprdecision, date_format(bs.hpron,'%m/%d/%Y') as hpron, count(1) nbrOfSegments from (SELECT bs.associd FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample  where segmentid = :objectid) as ass left join masterrecord.ut_procure_biosample bs on ass.associd = bs.associd left join masterrecord.ut_procure_segment sg on bs.pBioSample = sg.biosampleLabel where bs.voidind <> 1 and sg.voidInd <> 1 group by bs.read_Label, bs.associd, bs.pxirace, bs.pxigender, bs.pxiage, bs.anatomicSite, bs.subSite, bs.diagnosis, bs.subdiagnos, bs.metsSite, bs.tissType, bs.hprdecision, date_format(bs.hpron,'%m/%d/%Y')
OBJECTSQL;

     $obj = runObjectSQL($defineSQL, $segid);       
     $itemsfound = count($obj);  
     if (count($obj) > 0) { 
         $dta = $obj;
         $responseCode = 200;
         $msg = ""; 
     }  else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
     }
    }
 
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }

 function hprgetconstitlist ( $request, $urirqst ) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst);
    if (trim($rq[3]) === ""  || !is_numeric($rq[3])) { 
    } else { 
       $segid = $rq[3];
       $defineSQL = <<<OBJECTSQL
Select sg.bgs, ifnull(sgs.dspvalue,'') as segstatus, ifnull(date_format(sg.statusDate,'%m/%d/%Y'),'') as segstatusdate , ifnull(sg.shipdocrefid,'') as shipdocrefid, ifnull(date_format(sg.shippeddate,'%m/%d/%Y'),'') as shippeddate, concat(ifnull(sg.metric,0), if(ifnull(sg.metric,0) = 0,'',ifnull(muom.dspvalue,''))) as metricdsp, ifnull(sg.prepMethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation, ifnull(sg.qty,1) as qty, ifnull(sg.assignedTo,'') as assignedtocode, ifnull(sg.assignedReq,'') as assignedtoreq, ifnull(i.invest_lname,'') as investname, trim(concat(ifnull(i.invest_fname,''),' ', ifnull(i.invest_lname,''))) as investfullname, ifnull(i.invest_homeinstitute,'') as hinstitute, ifnull(i.invest_division,'') as investdivision, ifnull(hprslideread,0) as hprslideread, ifnull(scannedLocation,'') as scannedlocation, ifnull(toHPR,0) as tohpr  from (SELECT biosamplelabel FROM masterrecord.ut_procure_segment where segmentId = :objectid) bsgetter left join masterrecord.ut_procure_segment sg on bsgetter.biosamplelabel = sg.biosampleLabel left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') sgs on sg.segStatus = sgs.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'METRIC') muom on sg.metricuom = muom.menuvalue left join (SELECT investid, invest_lname, invest_fname, invest_homeinstitute, invest_division FROM vandyinvest.invest) as i on sg.assignedTo = i.investid order by sg.bgs 
OBJECTSQL;

     $obj = runObjectSQL($defineSQL, $segid);       
     $itemsfound = count($obj);  
     if (count($obj) > 0) { 
         $dta = $obj;
         $responseCode = 200;
         $msg = ""; 
     }  else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
     }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }



 function dosinglesegment($request, $urirqst) { 
    $rows = array(); 
    $dta = array(); 
    $responseCode = 400; 
    $msg = "BAD REQUEST";
    $itemsfound = 0;
    $rq = explode("/",$urirqst);
    if (trim($rq[3]) === ""  || !is_numeric($rq[3])) { 
    } else { 
       $segid = $rq[3];
$defineSQL = <<<OBJECTSQL
SELECT if(ifnull(sg.hprslideread,'')='','N',if (sg.hprslideread = 0, 'N', 'Y')) as hprslideread , sg.biosamplelabel, sg.segmentid, ifnull(sg.bgs,'XXXXXX') as bgs, ifnull(sg.segstatus,'') segstatuscode, ifnull(sg.statusdate,'') as statusdate, ifnull(sg.statusby,'') as statusby, ifnull(sg.shipdocrefid,'') as shipdocrefid, ifnull(sg.shippeddate,'') as shippeddate, ifnull(sg.hourspost,'') as hourspost, ifnull(sg.metric,'') as metric, ifnull(sg.metricuom,'') as metricuomcode, ifnull(sg.prepmethod,'') as prepmethod , ifnull(sg.preparation,'') as preparation, ifnull(sg.prepmodifier,'') as prepmodifier, ifnull(sg.prepadditives,'') as prepadditive, ifnull(sg.assignedto,'') as assignedto, ifnull(sg.assignedproj,'') as assignedproject, ifnull(sg.assignedreq,'') as assignedrequest, ifnull(sg.assignmentdate,'') as assigneddate, ifnull(sg.assignedby,'') as assignedby, ifnull(date_format(sg.procurementdate,'%m/%d/%Y'),'') as dspprocurementdate, ifnull(sg.procurementdate,'') as procurementdate, ifnull(sg.enteredon,'') as procurementdbdate, ifnull(sg.enteredby,'') as procuringtechnician, ifnull(sg.procuredat,'') as procuringinstitution, ifnull(sg.hprblockind,0) as hprblockind, ifnull(sg.slidegroupid,0) as slidegroupid, ifnull(sg.reqrqrbloodmatch,'') as reqrequestbloodmatch, ifnull(sg.reqchartreview,'') as reqrequestchartreview, ifnull(sg.slidefromblockid,'') as slidefromblockid, ifnull(sg.voidind,0) as voidind, ifnull(sg.segmentvoidreason,'') as segmentvoidreason, ifnull(sg.scannedlocation,'') as scannedlocation, ifnull(sg.scanloccode,'') as scanloccode, ifnull(sg.scannedstatus,'') as scannedstatus, ifnull(sg.scannedby,'') as scannedby, ifnull(sg.scanneddate,'') as scanneddate, ifnull(sg.tohpr,0) as tohprind, ifnull(sg.hprboxnbr,'') as hprboxnbr, ifnull(sg.tohprby,'') as tohprby, ifnull(date_format(sg.tohpron,'%m/%d/%Y %h:%i %p'),'') as tohpron, ifnull(sg.segmentcomments,'') as segmentcomments, ifnull(sg.qty,1) as qty, ifnull(bs.tisstype,'') as specimencategory, ifnull(bs.anatomicSite,'') site, ifnull(bs.subSite, '') subsite, ifnull(bs.diagnosis,'') as dx, ifnull(bs.subdiagnos,'') as dxmod, ifnull(bs.metssite,'') as metssite, ifnull(bs.metssitedx,'') as metssitedx, ifnull(bs.pdxsystemic,'') as systemicdx, ifnull(bs.sitePosition,'') as siteposition, ifnull(cxv.dspvalue,'') as cx, ifnull(rxv.dspvalue,'') as rx, ifnull(bs.hprind,0) as hprind, ifnull(bs.qcind,0) as qcind, ifnull(prv.dspvalue,'Pending') as pthrpt, ifnull(icv.dspvalue,'No') as infc, ifnull(uvv.dspvalue,'No') as uninvolvedind, ifnull(bs.pxirace,'Unknown') as phirace, ifnull(sxv.dspvalue, 'Unknown') as phisex, ifnull(bs.pxiage,'-') as phiage, ifnull(auv.longvalue,'') as phiageuom, ifnull(ptv.dspvalue,'') as proceduretype, ifnull(bs.procureInstitution,'') as procedureinstitution, ifnull(inst.dspvalue,'') as dspprocureinstitution, ifnull(date_format(bs.procedureDate,'%m/%d/%Y'),'') as proceduredate, ifnull(bs.createdby,'') as proctechnician, ifnull(bs.questionHPR,'') as hpquestion, ifnull(bs.biosampleComment,'') as biosamplecomment, substr(read_label,1,6) as bsreadlabel, ifnull(prpt.pathreport,'') as pathologyreporttext, ifnull(prpt.prid,'') as prprid, ifnull(prpt.selector,'') as prrecordselector    
FROM masterrecord.ut_procure_segment sg
LEFT JOIN masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') as cxv on bs.chemoind = cxv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') as rxv on bs.radind = rxv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as prv on bs.pathreport = prv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'infc') as icv on bs.pathreport = icv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'YESNO') as uvv on bs.uninvolvedInd = uvv.menuvalue        
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu='PXSEX') as sxv on bs.pxigender = sxv.menuvalue   
left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'ageuom') as auv on bs.pxiAgeUOM = auv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') as ptv on bs.procType = ptv.menuvalue       
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') as inst on bs.procureInstitution = inst.menuvalue 
left join (select prid, selector, pathreport from masterrecord.qcpathreports) as prpt on bs.pathreportid = prpt.prid
where segmentid = :objectid             
OBJECTSQL;
     $obj = runObjectSQL($defineSQL, $segid);       
     if (count($obj) === 1) { 
         $dta = $obj;
         $responseCode = 200;
         $msg = ""; 
     }  else { 
         $responseCode = 404; 
         $msg = "INDIVIDUAL DATA OBJECT NOT FOUND";
     }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound, 'DATA' => $dta);
    return $rows;    
 }
 
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
      $SQL = "select ifnull(pr.menuvalue,'') as menuvalue, pr.longvalue as longvalue, pr.dsporder  from (SELECT menuid FROM four.sys_master_menus where menu = 'PREPMETHOD' and menuvalue = :preparation) as pm left join (SELECT * FROM four.sys_master_menus where menu = 'PREPDETAIL' and dspind = 1) as pr on pm.menuid = pr.parentid where trim(pr.menuvalue) <> '' order by pr.dsporder";   
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

function hprtraylist($whichobj, $rqst) { 
    session_start();
    $responseCode = 500;
    $msg = "status message " . session_id();
    $itemsfound = 0;
    $dta = array();
    require(serverkeys . "/sspdo.zck");
    $sql = "SELECT ifnull(parentid,'') as parentid, ifnull(locationid,'') as locationid, ifnull(locationdsp,'NO LOCATION DISPLAY') as locationdsp, ifnull(scancode,'0000') as scancode, ifnull(hprtraystatus,'') as hprtraystatus  FROM four.sys_inventoryLocations where typeOLocation = 'HPR TRAY'";
    $rs = $conn->prepare($sql); 
    $rs->execute(); 
    while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
        $dta[] = $r;
    }
    $responseCode = 200;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}

function immunomoleresultlist($whichobj,$rqst) { 
    session_start();
    $responseCode = 500;
    $msg = "status message " . session_id();
    $msg = $whichobj;
    $itemsfound = 0;
    $dta = array(); 
    if (trim($whichobj) !== "") { 
      require(serverkeys . "/sspdo.zck"); 
      $sql = "SELECT menuvalue, ifnull(dspvalue,'') as dspvalue  FROM four.sys_master_menus where parentid = :parentid and dspind = 1 order by menuvalue";
      $rs = $conn->prepare($sql); 
      $rs->execute(array(':parentid' => $whichobj)); 
      $itemsfound = $rs->rowCount();
      while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
          $dta[] = $r;
      }    
      $responseCode = 200;
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}       

function immunomoletestlist($whichobj,$rqst) { 
    session_start();
    $responseCode = 500;
    $msg = "status message " . session_id();
    $itemsfound = 0;
    $dta = array(); 
    require(serverkeys . "/sspdo.zck");
    $sql = "SELECT menuid, menuvalue, concat(ifnull(dspvalue,''),  concat(' (', ifnull(longvalue,''), ')')) as dspvalue  FROM four.sys_master_menus where menu like 'MOLECULARTEST' and dspind = 1 order by menuvalue";
    $rs = $conn->prepare($sql); 
    $rs->execute(); 
    while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
        $dta[] = $r;
    }    
    $responseCode = 200;
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
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

function investigatorhead($whichobj, $rqst) { 
    session_start();
    $responseCode = 404;
    $msg = "No Investigator specified";
    $itemsfound = 0;
    if (trim($whichobj) !== "" ) { 
    $dta = array();
    require(genAppFiles . "/dataconn/sspdo.zck");  
    $getSQL = "select i.investid, trim(concat(ifnull(i.invest_salutation,''),' ',ifnull(i.invest_fname,''),' ', ifnull(i.invest_lname,''))) as investigator, i.invest_status as investstatus, i.invest_homeinstitute as institution, i.invest_institutiontype as institutiontype, i.invest_division as primarydivision, e.add_email as investemail from vandyinvest.invest i left join (select investid, add_email from vandyinvest.eastern_address where investid = :investidb and add_type = 'INVESTIGATOR') e on i.investid = e.investid where i.investid = :investid";
    $rs = $conn->prepare($getSQL);
    $rs->execute( array(':investid' => strtoupper($whichobj), ':investidb' => strtoupper($whichobj)));
    if ($rs->rowCount() < 1) { 
       $msg = "QUERY OBJECT NOT FOUND";    
    } else { 
       $dta = $rs->fetch(PDO::FETCH_ASSOC); 
       
       $courierSQL = "SELECT courierid, ucase(trim(ifnull(courier_name,''))) as courier, ucase(trim(ifnull(courier_num,''))) as couriernbr, trim(ifnull(courier_comment,'')) as couriercmt  FROM vandyinvest.eastern_courier where investid = :investid and ( trim(ifnull(courier_num,'')) <> '' OR courier_name like '%local%' )  order by courierid desc";
       $courierRS = $conn->prepare($courierSQL);
       $courierRS->execute(array(':investid' => strtoupper($whichobj)));

       while ($r = $courierRS->fetch(PDO::FETCH_ASSOC)) { 
           $dta['courier'][] = $r;
       }        
       
       $itemsfound = 1;
       $responseCode = 200;
       $msg = "";
    }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('status' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}

function investigatorbilladdress($whichobj, $rqst) { 
    session_start();
    $responseCode = 404;
    $msg = "No Investigator specified";
    $itemsfound = 0;
    if (trim($whichobj) !== "" ) { 
    $dta = array();
    require(genAppFiles . "/dataconn/sspdo.zck");  
    $getSQL = "select ifnull(add_address1,'') adline1, ifnull(add_address2,'') adline2, ifnull(add_attn,'') adattn, ifnull(add_institution,'') adinstitution, ifnull(add_department,'') addept, ifnull(add_city,'') adcity, ifnull(add_state,'') adstate, ifnull(add_zipcode,'') adzipcode, ifnull(add_country,'') adcountry ,ifnull(add_email,'') ademail, ifnull(add_phone,'') adphone  from vandyinvest.eastern_address where investid = :investid and add_type = 'BILLING' order by addressid desc limit 1";
    $rs = $conn->prepare($getSQL);
    $rs->execute( array(':investid' => strtoupper($whichobj)));
    if ($rs->rowCount() < 1) { 
       $msg = "QUERY OBJECT NOT FOUND";    
    } else { 
       $dta = $rs->fetch(PDO::FETCH_ASSOC);       
       $itemsfound = 1;
       $responseCode = 200;
       $msg = "";
    }
    }
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('status' => $responseCode, 'MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}

function investigatorshipaddress($whichobj, $rqst) { 
    session_start();
    $responseCode = 404;
    $msg = "No Investigator specified";
    $itemsfound = 0;
    if (trim($whichobj) !== "" ) { 
    $dta = array();
    require(genAppFiles . "/dataconn/sspdo.zck");  
    $getSQL = "select ifnull(add_address1,'') adline1, ifnull(add_address2,'') adline2, ifnull(add_attn,'') adattn, ifnull(add_institution,'') adinstitution, ifnull(add_department,'') addept, ifnull(add_city,'') adcity, ifnull(add_state,'') adstate, ifnull(add_zipcode,'') adzipcode, ifnull(add_country,'') adcountry ,ifnull(add_email,'') ademail, ifnull(add_phone,'') adphone  from vandyinvest.eastern_address where investid = :investid and add_type = 'SHIPPING' order by addressid desc limit 1";
    $rs = $conn->prepare($getSQL);
    $rs->execute( array(':investid' => strtoupper($whichobj)));
    if ($rs->rowCount() < 1) { 
       $msg = "QUERY OBJECT NOT FOUND";    
    } else { 
       $dta = $rs->fetch(PDO::FETCH_ASSOC);       
       $itemsfound = 1;
       $responseCode = 200;
       $msg = "";
    }
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
        $responseCode = 200;
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
             ) as selector, substr(concat('000000',ifnull(shipdocrefid,'000000')),-6) as dspmark, concat(ifnull(sdstatus,'') , ' (' , ifnull(date_format(statusdate,'%m/%d/%Y'),'') , ') | Requested Ship Date: ', ifnull(date_format(rqstshipdate, '%m/%d/%Y'),'') , ' | investigator: ', ifnull(investcode,'')) as abstract FROM masterrecord.ut_shipdoc sd where shipdocrefid = :srchtrm ";
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

function hprrequestcode($whichobj, $rqst) { 
    session_start();
    $responseCode = 400;   
    $msg = "status message";
    $itemsfound = 0;
    $dta = array();
    if ($whichobj === "") { 
    } else {
      require(genAppFiles . "/dataconn/sspdo.zck");  
      $objGetSQL = "SELECT srchterm FROM four.objsrchdocument where objid = :objid";
      $objGetRS = $conn->prepare($objGetSQL); 
      $objGetRS->execute(array(':objid' => $whichobj)); 
      if ($objGetRS->rowCount() < 1) { 
        $responseCode = 404;
      } else { 
        $itemsfound = $objGetRS->rowCount();
        $rs = $objGetRS->fetch(PDO::FETCH_ASSOC); 
        $dta[] = $rs['srchterm'];
        $responseCode = 200;
      }
    } 
    $rows['statusCode'] = $responseCode; 
    $rows['data'] = array('MESSAGE' => $msg, 'ITEMSFOUND' => $itemsfound,  'DATA' => $dta);
    return $rows;  
}

}

class globalMenus {

    function astreqpreps() { 
      return "SELECT distinct prep_grouptype as codevalue, prep_grouptype as menuvalue, 0 as useasdefault, prep_grouptype as lookupvalue FROM vandytmp.yesterday_eastern_tissueprep where ifnull(prep_grouptype,'') <> '' order by prep_grouptype";
    }

    function astreqspecimencategories () { 
        return "select ifnull(splst.speccat,'') as codevalue, ifnull(splst.speccat,'') as menuvalue, 0 as useasdefault, ifnull(splst.speccat,'') as lookupvalue from (SELECT distinct ifnull(req_tissuetype,'') as speccat FROM vandyinvest.investtissreq order by 1) splst";
    }
    
    function astrequeststatuses() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'ASTREQUESTSTATUS' and dspind = 1 order by dsporder";
    }

    function furtheractionactions() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' and dspind = 1 order by dsporder";
    }

    function furtheractionpriorities() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE' and dspind = 1 order by dsporder";
    }

    function hprreturnnonfinishedreasons() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HPRRTNINCOMPLETEREASON' and dspind = 1 order by dsporder";
    }

    function hprreturnlocations() { 
      return "SELECT ifnull(scancode,'') as codevalue, ifnull(locationdsp,'') as menuvalue, 0 as useasdefault, ifnull(scancode,'') as lookupvalue FROM four.sys_inventoryLocations where typeOLocation = 'HPRRTNPROCESS' and activelocation = 1 order by locationdsp";
    }

    function hprfurtheractions() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HPRFURTHERACTION' and dspind = 1 order by dsporder";
    }

    function hprtechaccuracy() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HPRTECHACCURACY' and dspind = 1 order by dsporder";
    }

    function hprtumorgradescale() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HPRTUMORSCALE' and dspind = 1 order by dsporder";
    }

    function hprpercentages() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(longvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HPRPERCENTAGE' and dspind = 1 order by dsporder";
    }

    function shipdocrestockreasons() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'SEGMENTRESTOCKREASON' and dspind = 1 order by dsporder";
    }
    
    function shipdocrestocksegmentvalues() { 
        return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS' and academValue = 'RESTOCK' and dspind = 1 order by dsporder";
    }

    function bgpristinevoidreasons() { 
      return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'DEVIATIONREASON_BGPRISTINEVOID' and dspind = 1 order by dsporder";
    }

    function cellcarriers() { 
      return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'CELLCARRIER' and dspind = 1 order by dsporder";

    }

    function ssmoduleslist() { 
      return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'SS5MODULES' and dspind = 1 order by dsporder";
    }

    function helpticketreasons() { 
      return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'HELPTICKETREASON' and queriable = 1 and dspind = 1 order by dsporder";
    }

    function vocabularysystemicdxlist() { 
      return "SELECT distinct trim(ifnull(dxid,'')) as codevalue, trim(ifnull(diagnosis,'')) as menuvalue, 0 as useasdefault, trim(ifnull(diagnosis,'')) as lookupvalue FROM four.sys_master_menu_vocabulary where systemicindicator =1 order by menuvalue";
    }

    function uninvolvedindicatoroptions() { 
      return "SELECT trim(ifnull(menuvalue,'')) as codevalue, trim(ifnull(longvalue,'')) as menuvalue, ifnull(useasdefault,0) as useasdefault, trim(ifnull(menuvalue,'')) as lookupvalue FROM four.sys_master_menus where menu = 'UNINVOLVEDIND' and dspind = 1 order by dsporder";
    }
 
    function vocabularysitepositions() {
      return "SELECT trim(ifnull(menuvalue,'')) as codevalue, trim(ifnull(longvalue,'')) as menuvalue, ifnull(useasdefault,0) as useasdefault, trim(ifnull(menuvalue,'')) as lookupvalue FROM four.sys_master_menus where menu = 'ASITEPOSITIONS' and dspind = 1 order by dsporder";
    }

    function vocabularyspecimencategory() { 
      return "SELECT distinct trim(ifnull(specimencategory,'')) as codevalue, trim(ifnull(specimencategory,'')) as menuvalue, 0 as useasdefault, trim(ifnull(specimencategory,'')) as lookupvalue FROM four.sys_master_menu_vocabulary where trim(ifnull(specimenCategory,'')) <> '' order by codevalue";
    }

    function fourmenuprcpathologyreport() { 
      return "SELECT ifnull(menuvalue,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'PRpt' and queriable = 1 and dspind = 1 order by dsporder";
    }

    function fourmenuprcproceduretype() { 
      return "SELECT ifnull(menuid,'') as codevalue, ifnull(dspvalue,'') as menuvalue , ifnull(useasdefault,0) as useasdefault, ifnull(menuvalue,'') as lookupvalue FROM four.sys_master_menus where menu = 'PROCTYPE' and dspind = 1 order by dsporder";
    }

    function inventorylocationsphysical() { 
      return "Select lvl1.scancode as codevalue, concat(ifnull(lvl4.inventoryloc,'') , ' :: ',  ifnull(lvl3.inventoryloc,'') , ' :: ', ifnull(lvl2.inventoryloc,'') , ' :: ' , ifnull(lvl1.locationnote,''), ' [' ,ifnull(lvl1.typeOLocation,'') , ']') as menuvalue, lvl1.locationid as useasdefault, lvl3.freezerelprocode as lookupvalue FROM four.sys_inventoryLocations lvl1 left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid from four.sys_inventoryLocations) as lvl2 on lvl1.parentid = lvl2.locationid left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid, FreezerELPROCode from four.sys_inventoryLocations) as lvl3 on lvl2.parentid = lvl3.locationid left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid from four.sys_inventoryLocations) as lvl4 on lvl3.parentid = lvl4.locationid where lvl1.hierarchyBottomInd = 1 and lvl1.physicalLocationInd = 1 and activelocation = 1 order by lvl4.inventoryloc, lvl3.inventoryloc, lvl1.typeolocation desc, lvl1.locationnote";
    }

    function inventorylocationstoragecontainers() { 
      return "Select lvl1.scancode as codevalue, concat(ifnull(lvl4.inventoryloc,'') , ' :: ',  ifnull(lvl3.inventoryloc,'') , ' :: ', ifnull(lvl2.inventoryloc,'') , ' :: ' , ifnull(lvl1.locationnote,''), ' [' ,ifnull(lvl1.typeOLocation,'') , ']') as menuvalue, lvl1.locationid as useasdefault, lvl3.freezerelprocode as lookupvalue FROM four.sys_inventoryLocations lvl1 left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid from four.sys_inventoryLocations) as lvl2 on lvl1.parentid = lvl2.locationid left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid, FreezerELPROCode from four.sys_inventoryLocations) as lvl3 on lvl2.parentid = lvl3.locationid left join (select locationid, ifnull(locationnote,'') as inventoryloc, parentid from four.sys_inventoryLocations) as lvl4 on lvl3.parentid = lvl4.locationid where lvl1.hierarchyBottomInd = 1 and lvl1.physicalLocationInd = 1 and activelocation = 1 and (typeolocation = 'STORAGE CONTAINER') order by lvl4.inventoryloc, lvl3.inventoryloc, lvl1.typeolocation desc, lvl1.locationnote";
    }

    function hprtechnicianaccuracy() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'HPRTECHACCURACY' order by mnu.dspOrder";
    }
    
    function devmenuhprinventoryoverride() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'DEVIATIONREASON_HPROVERRIDE' order by mnu.dspOrder";
    }

    function devmenupathologyreportupload() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'DEVIATIONREASON_PRUPLOAD' order by mnu.dspOrder";
    }

    function timevaluelist() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'DAILYHOURS' order by mnu.dspOrder";
    }
    
    function calendareventtypes() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'EVENTTYPE' order by mnu.dspOrder";
    }

    function devmenushipdocactualship() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'DEVIATIONREASON_HPROVERRIDE' order by mnu.dspOrder";
    }

    function allowedassignableinformedconsent() { 
      return "SELECT menu, ucase(ifnull(mnu.menuvalue,'')) as codevalue, ucase(ifnull(mnu.dspvalue,'')) as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where menu = 'infc' and queriable = 1 order by dspOrder";
    }

    function allowedtechnicianassignabledonortargets() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ucase(ifnull(mnu.dspvalue,'')) as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'pxTARGET' and mnu.dspind = 1 order by mnu.dsporder";
    }

    function encounternotetypes() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ucase(ifnull(mnu.dspvalue,'')) as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where menu = 'caseNotesType' and dspInd = 1 order by dsporder";
    }

    function deveditpathrptreasons() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'PREDITREASON' order by mnu.dspOrder";
    }

    function chtnvocabularyspecimencategory() {
      return "select distinct catid as codevalue, specimencategory as menuvalue, 0 as useasdefault, catid as lookupvalue from four.sys_master_menu_vocabulary where trim(ifnull(catid,'')) <> '' order by specimencategory";
    }

    function shipdocpovalues() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'SHIPDOCPO' order by mnu.dspOrder";
    }

    function allinstitutions() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.dspInd = 1 and  mnu.menu = 'INSTITUTION' order by mnu.dspOrder";
    }

    function allsegmentstati() {
      return "SELECT ucase(ifnull(mnu.dspvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SEGMENTSTATUS' and mnu.dspInd = 1 order by mnu.dsporder";
    }
    
    function menusegmentstatus() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'SEGMENTSTATUS' and mnu.dspInd = 1 order by mnu.dsporder";
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

    function metricuomslong() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.longvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'METRIC' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function metricuoms() {
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'METRIC' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function qmsstatus() {
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'QMSStatus' and mnu.dspInd = 1 order by mnu.dsporder";
    }

    function qmsassignablestatus() { 
        return "SELECT ucase(ifnull(menuvalue,'')) as codevalue, ifnull(dspvalue,'') as menuvalue, 0 as useasdefault, ucase(ifnull(menuvalue,'')) as lookupvalue FROM four.sys_master_menus where menu = 'QMSStatus' and dspind = 1 and assignablestatus = 1 order by dsporder";
    }

    function qmslabactions() { 
      return "SELECT ucase(ifnull(mnu.menuvalue,'')) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'QMSLABACTIONS' and mnu.dspInd = 1 order by mnu.dsporder";
    }
    
    function fouryesno() {
      return "SELECT ifnull(mnu.menuvalue,0) as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'YESNO' and mnu.dspInd = 1 order by mnu.dsporder";
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

    function preparationcontainers() { 
      return "SELECT ifnull(mnu.dspvalue,'') as codevalue, ifnull(mnu.dspvalue,'') as menuvalue, ifnull(mnu.useasdefault,0) as useasdefault, ucase(ifnull(mnu.menuvalue,'')) as lookupvalue FROM four.sys_master_menus mnu where mnu.menu = 'CONTAINER' and mnu.dspInd = 1 order by mnu.dsporder";
    }

}

function chkUserBySession($givensessionid) {
   require(serverkeys . "/sspdo.zck");
   $usrArr = array();
   $usrSQL = "SELECT allowcoord, allowlinux, allowind, allowhpr, allowinvtry, allowproc, presentinstitution, TIMESTAMPDIFF(MINUTE,now(),sessionexpire) minutesuntilsessionexpiration, sessionexpire, TIMESTAMPDIFF(DAY, now(), passwordexpiredate) daysuntilpasswordexpire, passwordexpiredate, emailaddress FROM four.sys_userbase where sessionid = :sessionid";
   $usrR = $conn->prepare($usrSQL);
   $usrR->execute(array(':sessionid' => $givensessionid));
   if ($usrR->rowCount() === 1) { 
     $usrArr = $usrR->fetch(PDO::FETCH_ASSOC);
   } else { 
   } 
   return $usrArr;
}

function runhelpsearchquery($srchrqstjson) { 
  require(serverkeys . "/sspdo.zck");
  $rqstDta = json_decode($srchrqstjson, true);
  $sql = "SELECT helpurl, screenreference, title, subtitle, txt, ifnull(bywhomemail,'') as byemail, ifnull(date_format(initialdate,'%M %d, %Y'),'') as initialdte, ifnull(lasteditbyemail,'') as lstemail, ifnull(date_format(lastedit,'%M %d, %Y'),'') as lstdte FROM four.base_ss7_help where 1=1 and ((ifnull(txt,'') like :textsrch) or (ifnull(title,'') like :titlesrch) or (ifnull(subtitle,'') like :subtitlesrch))";
  $rs = $conn->prepare($sql); 
  $rs->execute(array(":textsrch" => "%{$rqstDta['searchterm']}%", ":titlesrch" => "%{$rqstDta['searchterm']}%", ":subtitlesrch" => "%{$rqstDta['searchterm']}%"));
  $rtnData = array();
  if ( $rs->rowCount() > 0 ) {
    $rsltCntr = 0;  
    while ( $r = $rs->fetch(PDO::FETCH_ASSOC)) {
      $rtnData[$rsltCntr]['matchesfound']       = $rs->rowCount();  
      $rtnData[$rsltCntr]['helpurl']            = $r['helpurl'];
      $urldsp                                   = preg_replace("/{$rqstDta['searchterm']}/i",'<b>$0</b>', $r['helpurl'] ); 
      $rtnData[$rsltCntr]['urldsp']             = $urldsp;
      $rtnData[$rsltCntr]['screenreference']    = $r['screenreference'];
      $rtnData[$rsltCntr]['title']              = $r['title'];
      $titleDsp                                 = preg_replace("/{$rqstDta['searchterm']}/i",'<b>$0</b>', $r['title'] );
      $rtnData[$rsltCntr]['titledsp']           = $titleDsp;
      $rtnData[$rsltCntr]['subtitle']           = $r['subtitle'];
      $subtitleDsp                              = preg_replace("/{$rqstDta['searchterm']}/i",'<b>$0</b>', $r['subtitle'] );
      $rtnData[$rsltCntr]['subtitledsp']        = $subtitleDsp;
      $rtnData[$rsltCntr]['byemail']            = $r['byemail'];
      $rtnData[$rsltCntr]['initialdate']        = $r['initialdte'];
      $txtStr                                   = preg_replace('/(<[Pp]>)|(\\n)|(<[Bb][Rr]>)|(\\r)|(<[Bb]>)|(<\\[Bb][Rr])|(<\/br>)|(<\\[Bb]>)|(<\/[Bb]>)|(PICTURE:\{.{1,}\})/',' ', $r['txt']);
      preg_match("/{$rqstDta['searchterm']}/i",$txtStr, $matches,  PREG_OFFSET_CAPTURE);
      if ( (int)$matches[0][1] < 21) { 
        $txtStr = substr($txtStr,0,350) . " ...";
      } else { 
        $txtStr = " ... " . substr($txtStr,(int)$matches[0][1] - 20, 350) . " ...";
      }
      $rtnData[$rsltCntr]['abstract']           = preg_replace("/{$rqstDta['searchterm']}/i","<b>$0</b>",$txtStr);
      $rsltCntr++;
    }
  } else { 
  }
  return $rtnData; 
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
             //COMMENTED ON 2019-02-25 FOR THE LINES BELOW  
             //$sqlCritAdd .= " and ( bs.pbiosample = cast( :bgnbr as DECIMAL(20,8))) ";
             //$qryArr += [':bgnbr' => "{$bgvalue}"  ];
             
             //CORRECTED 2019-02-25 TO PULL ALL DECIMAL COMPONENTS OF A BIOGROUP 
             //TODO:  MAKE ALL THE ABOVE CODE RANGE FOR THE DECIMAL NUMBERS  
             $sqlCritAdd .= " and ( bs.pbiosample >= cast( :bgnbrOne as DECIMAL(20,8)) and bs.pbiosample < cast( :bgnbrTwo as DECIMAL(20,8))  ) ";
             $qryArr += [':bgnbrOne' => "{$bgvalue}" ];
             $bgTwo = ($bgvalue + 1);
             $qryArr += [':bgnbrTwo' => "{$bgTwo}" ];
             
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

        case 'requestNbr': 
            $sqlCritAdd .= " and ( sg.assignedReq = :reqnbr ) ";
            $qryArr += [':reqnbr' => "{$fldvalue}"];
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
            $sqlCritAdd .= " and ( sd.sdstatus = :sdstatus ) ";
            $qryArr += [':sdstatus' => "{$fldvalue}"];
            $fieldsQueried++;
            break;

        case 'site':
            $siteparts = explode(" :: ", $fldvalue);
            if (trim($siteparts[0]) !== "") { 
              $sqlCritAdd .= " and ( bs.anatomicsite = :sitesite or bs.metssite = :metssite ) ";
              $qryArr += [':sitesite' => "{$siteparts[0]}", ':metssite' => "{$siteparts[0]}"];
              $fieldsQueried++;                  
            }   
            if (trim($siteparts[1]) !== "") { 
              $sqlCritAdd .= " and ( bs.subsite = :subsite) ";
              $qryArr += [':subsite' => "{$siteparts[1]}"];
              $fieldsQueried++;                  
            }                       
            break;

        case 'diagnosis':
              $sqlCritAdd .= " and ( bs.diagnosis like :dx or bs.subdiagnos like :sdx ) ";
              $qryArr += [':dx' => "%{$fldvalue}%", ':sdx' => "%{$fldvalue}%"];
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
        case 'hprTrayInvLoc':
            $sqlCritAdd .= " and ( sg.HPRBoxNbr = :hprslidetrayloc ) ";
            $qryArr += [':hprslidetrayloc' => "{$fldvalue}"];
            $fieldsQueried++;
            break;
      }
    }
  }


  if ($fieldsQueried > 0) { 
    if ($errors === 0) { 
        //BEGINNINGS OF SQL
        require(serverkeys . "/sspdo.zck"); 
/*SELECT iloc.scancode, iloc.locationdsp, ifnull(tsts.longvalue,'')  as hprtraystatusdsp,    iloc.hprtrayheldwithin, iloc.hprtrayheldwithinnote, iloc.hprtrayreasonnotcompletenote , ifnull(date_format(iloc.hprtraystatuson,'%m/%d/%Y'),'') as hprtraystatuson 
FROM four.sys_inventoryLocations iloc
left join (SELECT dspvalue, longvalue FROM four.sys_master_menus where menu = 'HPRTrayStatus') as tsts on iloc.hprtraystatus = tsts.dspvalue
where parentId = 293 */

      $masterSQL = <<<SQLSTMT
select bs.pbiosample
      , sg.segmentid
      , bs.voidind bsvoid 
      , sg.voidind sgvoid
      , ucase(ifnull(sg.bgs,'')) as bgs
      , sg.segstatus as segstatuscode
      , ifnull(mnuseg.dspvalue,'ERROR') as segstatus        
      , ifnull(date_format(sg.statusdate,'%m/%d/%Y'),'') as statusdate
      , ifnull(sg.statusby,'') as statusby
      , ifnull(bs.qcprocstatus,'') as qcstatuscode
      , trim(ifnull(hprdecision,'')) as hprdecision
      , ifnull(hprresult,0) as hprresultid
      , trim(ifnull(hprby,'')) as hprreviewer
      , ifnull(date_format(hpron,'%m/%d/%Y'),'') as reviewedon
      , ucase(ifnull(mnuqms.dspvalue,'')) as qcstatus
      , ifnull(bs.pxiage,'') as phiage
      , ucase(substr(ifnull(bs.pxirace,''),1,3)) as phiracecode
      , ucase(ifnull(bs.pxirace,'')) as phirace
      , ifnull(bs.pxigender,'') as phigender
      , ifnull(bs.proctype,'') as proctype
      , ifnull(mnuprctype.dspvalue,'') as proctypedsp
      , ifnull(date_format(sg.procurementdate,'%m/%d/%Y'),'') as procurementdate 
      , ifnull(date_format(sg.shippeddate,'%m/%d/%Y'),'') as shipmentdate 
      , ifnull(sg.shipdocrefid,0) as shipdocnbr
      , ifnull(sd.sdstatus,'') as sdstatus
      , ifnull(sg.procuredat,'') procuringinstitutioncode
      , ifnull(mnuinst.dspvalue,'') as procuringinstitution
      , ifnull(bs.tisstype,'') as specimencategory
      , ifnull(bs.anatomicsite,'') as site
      , ifnull(bs.subsite,'') as subsite
      , ifnull(bs.diagnosis,'') as diagnosis
      , ifnull(bs.subdiagnos,'') as diagnosismodifier
      , ifnull(bs.metssite,'') as metssite
      , ifnull(sg.assignedto,'') as assignedinvestigator
      , ifnull(i.invest_fname,'') as assignedinvestigatorfname
      , ifnull(i.invest_lname,'') as assignedinvestigatorlname
      , ifnull(i.invest_homeinstitute,'') as assignedinvestigatorinstitute
      , ifnull(sg.assignedReq,'') as tqrequestnbr
      , ifnull(sg.prepmethod,'') as preparationmethod
      , ifnull(sg.preparation,'') as preparation
      , ifnull(sg.hourspost,'') as hourspost
      , ifnull(sg.metric,'') as metric
      , ifnull(sg.metricuom,'') as metricuomcode 
      , ifnull(mnumet.dspvalue,'') as metricuom
      , ifnull(sg.qty,'') as qty
      , ifnull(sg.scannedlocation,'') as scannedlocation
      , ifnull(sg.HPRBoxNbr,'') as hprboxnbr
      , ifnull(htry.locationdsp,'') as htrydsp
      , ifnull(htry.hprtraystatusdsp,'') as htrystatus
      , ifnull(htry.hprtraystatuson,'') as hprtraystatuson
      , ifnull(htry.heldwithin,'') as heldwithin
      , ifnull(htry.hprtrayheldwithinnote,'') as heldwithinnote
      , substr(ifnull(mnucx.dspvalue,''),1,1) as cxind
      , substr(ifnull(mnurx.dspvalue,''),1,1) as rxind
      , substr(ifnull(mnupr.dspvalue,''),1,1) as pathologyrptind
      , ifnull(bs.pathreportid,0) as pathologyrptdocid 
      , substr(ifnull(mnuinfc.dspvalue,''),1,1) as informedconsentind
      , substr(ifnull(mnuch.dspvalue,''),1,1) as chartindicator       
      , ifnull(bs.associd,'') as associd
      , ifnull(bs.biosamplecomment,'') as bscomment
      , ifnull(bs.questionhpr,'') as hprquestion
      , ifnull(sg.segmentcomments,'') as sgcomments
from masterrecord.ut_procure_segment sg 
left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample 
left join masterrecord.ut_shipdoc sd on sg.shipdocrefid = sd.shipdocrefid 
left join vandyinvest.invest i on sg.assignedto = i.investid 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') mnuinst on sg.procuredAt = mnuinst.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'QMSStatus') mnuqms on bs.qcprocstatus = mnuqms.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'METRIC') as mnumet on sg.metricuom = mnumet.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'cx') as mnucx on bs.chemoind = mnucx.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'rx') as mnurx on bs.radind = mnurx.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as mnupr on bs.pathreport = mnupr.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INFC') as mnuinfc on bs.informedconsent = mnuinfc.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'BGCHARTIND') as mnuch on bs.chartind = mnuch.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PROCTYPE') as mnuprctype on bs.proctype = mnuprctype.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus mnu where mnu.menu = 'SEGMENTSTATUS') as mnuseg on sg.segstatus = mnuseg.menuvalue
left join (SELECT iloc.scancode, iloc.locationdsp, ifnull(tsts.longvalue,'')  as hprtraystatusdsp, ifnull(rtn.locationdsp,'')  as heldwithin, ifnull(iloc.hprtrayheldwithinnote,'') as hprtrayheldwithinnote, ifnull(iloc.hprtrayreasonnotcompletenote,'') as hprtrayreasonnotcompletenote, ifnull(date_format(iloc.hprtraystatuson,'%m/%d/%Y'),'') as hprtraystatuson FROM four.sys_inventoryLocations iloc left join (SELECT dspvalue, longvalue FROM four.sys_master_menus where menu = 'HPRTrayStatus') as tsts on iloc.hprtraystatus = tsts.dspvalue left join four.sys_inventoryLocations rtn on iloc.hprtrayheldwithin = rtn.scancode where iloc.parentId = 293) as htry on sg.hprboxnbr = htry.scancode              
where 1=1 and sg.voidind <> 1 and bs.voidind <> 1   {$sqlCritAdd} 
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
      //TODO:ERRORS WITH THE CRITERIA -- SEND BACK ERROR
    }
  } else { 
    //TODO:NO FIELDS QUERIED -- THAT SHOULDN'T HAPPEN BUT ITS A CATCH
  }
  $rtndta[] = $sendthis;
  return $rtndta;
}

function runObjectSQL($sql, $id) { 
    session_start();
    $obj = array();
    require(serverkeys . "/sspdo.zck");  
    $objRS = $conn->prepare($sql);
    $objRS->execute(array(':objectid' => $id));
    while ($rs = $objRS->fetch(PDO::FETCH_ASSOC)) { 
        $obj[] = $rs; 
    }
    return $obj;
}

function bldDialogGetter($whichdialog, $passedData) {  
  $at = applicationTree; 
  require("{$at}/sscomponent_pagecontent.php"); 
  $bldr = new pagecontent(); 
  $rtnDialog = $bldr->sysDialogBuilder($whichdialog, $passedData);
  return $rtnDialog;
}



/*  OLD GOOGLE STYLE SITE SEARCH 
 *            if (strpos($fldvalue,";") !== false) { 
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
 */
