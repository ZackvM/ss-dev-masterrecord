<?php

function buildProcurementGrid($whichUsr, $qryId) { 
    session_start(); 
    $sessId = session_id();
    $usrId = $whichUsr->userid;
    $arr['qryid'] = $qryId;
    $arr['sessid'] = $sessId;
    $arr['usrid'] = $usrId;
    $passdata = json_encode($arr);  
    $griddata = json_decode(callrestapi("POST","https://data.chtneast.org/datagets/getprocurementgrid",serverIdent, apikey, $passdata), true);

    if ((int)$griddata['responseCode'] === 200) { 
       //CHECK FOUND AND DISPLAY GRID
       $gdta = json_decode($griddata['message'],true);
       if ((int)$gdta['ITEMS'] < 0 ) { 
         $rtn = "NO BIOGROUPS FOUND";
       } else { 
         //BIOGROUPS FOUND    
         $itms = $gdta['ITEMS'];

         $bgTbl = "<table border=1><tr><td colspan=20>Biogroups Found: {$itms} </td></tr>";
         $bgTbl .= "<tr>
                      <th></th>
                      <th>Biogroup</th>
                      <th>Procedure Date</th>
                      <th>Procedure</th>
                      <th>Designation</th>
                      <th>UnkMet</th>
                      <th>METS/DX</th>
                      <th>Systemic Diagnosis</th>
                      <th>Uninvolved</th>
                      <th>PathRpt</th>
                      <th>A-S-R</th>
                      <th>Initials</th>
                      <th>InfC</th>
                      <th>Cx-Rx</th>
                      <th>BG Weight</th>
                    </tr>";
         $techName = "";
         foreach($gdta['DATA'] as $bg) {

            $migrated = ((int)$bg['migratedind'] === 1) ? "<i class=\"material-icons\">lock</i>" : "";

            $location = (substr($bg['fromlocation'],0,8) === "OFFSITE:") ? substr($bg['fromlocation'],8) : $bg['fromlocation'];
            if ($bg['procuringtechnician'] !== $techName) { 
              $bgTbl .= "<tr><td colspan=20>{$bg['procuringtechnician']}@{$location}</td></tr>";
              $techName = $bg['procuringtechnician'];
            }

            if (substr($bg['fromlocation'],0,7) === "OFFSITE") {
              $location = substr($bg['fromlocation'],8); 
              $bgTbl .= "<tr>
                            <td colspan=20>{$bg['bsnbr']}</td>
                         </tr>";
            } else {
  
              $prcType = (trim($bg['proceduretype']) !== "") ? trim($bg['proceduretype']) : "";
              if (trim($bg['collectionmethod']) !== "") { 
                $prcType .= (trim($prcType) !== "") ? "/{$bg['collectionmethod']}" : $bg['collectionmethod'];
              }

              $prcdte = new DateTime($bg['proceduredate']);
              $prcdtedsp = $prcdte->format('m/d/Y');
              $desig = (trim($bg['specimencategory']) !== "") ? $bg['specimencategory'] : "";
              if (trim($bg['primarysite']) !== "") { 
                $desig .= (trim($desig) !== "") ? "/" . $bg['primarysite'] : $bg['primarysite'];
              }
              if (trim($bg['primarysubsite']) !== "") { 
                  $desig .= (trim($desig) !== "") ? " [{$bg['primarysubsite']}]" : "{$bg['primarysubsite']}";
              }
              $desig .=  (trim($bs['siteposition']) !== "") ? " [" . trim($bs['siteposition']) . "]" : "";
              if (trim($bg['diagnosis']) !== "") { 
                $desig .= (trim($desig) !== "") ? "/{$bg['diagnosis']}" : $bg['diagnosis'];
              }
              if (trim($bg['diagnosismodifier']) !== "") { 
                $desig .= (trim($desig) !== "") ? " [{$bg['diagnosismodifier']}]" : "{$bg['diagnosismodifier']}";
              }
              if (trim($bg['classification']) !== "") { 
                $desig .= (trim($desig) !== "") ? " ({$bg['classification']})" : $bg['classification']; 
              } 
              $unkm =  ((int)$bs['unknownmets'] === 0) ? "-" : "Yes"; 
              $mets = trim($bg['metssite']);
              if (trim($bg['metsdx']) !== "") { 
                $mets .= (trim($mets) !== "") ? "/{$bg['metsdx']}" : "{$bg['metsdx']}";
              }
              $uninv = ((int)$bg['uninvolvedind'] === 1) ? "Y" : "N";
              $prrpt = strtoupper(substr($bg['pathologyreport'],0,1));
              $ars = (trim($bg['pxiage']) !== "") ? $bg['pxiage'] : "";
              if (trim($bg['pxisex']) !== "") { 
                $ars .= (trim($ars) !== "") ? "-" . strtoupper(substr($bg['pxisex'],0,1)) : strtoupper(substr($bg['pxisex'],0,1));
              }
              if (trim($bg['pxirace']) !== "") { 
                $ars .= (trim($ars) !== "") ? "-" . strtoupper(substr($bg['pxirace'],0,3)) : strtoupper(substr($bg['pxirace'],0,3));
              }
              $infc = strtoupper(substr($bg['informedconsent'],0,1));
              $cxrx = trim(strtoupper(substr($bg['cx'],0,1))) . "-" . trim(strtoupper(substr($bg['cx'],0,1)));
              $iweight = (trim($bg['initialweight']) !== "") ? trim($bg['initialweight']) : "";
              if (trim($bg['weightmetric']) !== "") { 
                $iweight .= (trim($iweight) !== "") ? " {$bg['weightmetric']}" : "";
              }

              //{"responseCode":200,"message":"{\"MESSAGE\":\"BIOSAMPLES LISTED\",\"ITEMS\":10,\"DATA\":[\"voidind\":0,\"voidreason\":\"\",
              //"segments\":[{"assignment\":\"QC\",\"assignmentname\":\"QC\",\"assignmentrequest\":\"\",\"voidind\":0,\"voidreason\":\"\"},


              $bgTbl .= "<tr data-pxiid='{$bg['pxiid']}'>
                            <td>{$migrated}</td>
                            <td>{$bg['bsnbr']}</td>
                            <td>{$prcdtedsp}</td>
                            <td>{$prcType}</td>
                            <td>{$desig}</td>
                            <td>{$unkm}</td>
                            <td>{$mets}</td>
                            <td>{$bs['systemicdiagnosis']}</td>
                            <td>{$uninv}</td>
                            <td>{$prrpt}</td>
                            <td>{$ars}</td>
                            <td>{$bg['pxiinitials']}</td>
                            <td>{$infc}</td>
                            <td>{$cxrx}</td>
                            <td>{$iweight}</td>
                        </tr>";
               if (count($bg['segments']) > 0) { 
                 $bgTbl .= "<tr><td colspan=25>";
                
                 $bgTbl .= "<table border=1>
                                <tr>
                                  <th>Label</th>
                                  <th>Preparation</th>
                                  <th>Container</th>
                                  <th>Qty</th>
                                  <th>Metric</th>
                                  <th>Hours Post</th>
                                  <th>Cut From</th>
                                  <th>Assignment</th>
                                </tr>";
                 foreach($bg['segments'] as $seg) {
                     $prep = $seg['prp'];
                     if (trim($seg['prpmeth']) !== "") { 
                       $prep .= (trim($prep) !== "") ? "/{$seg['prpmeth']}" : "";
                     }
                     if (trim($seg['prpdetail']) !== "") { 
                       $prep .= (trim($prep) !== "") ? " [{$seg['prpdetail']}" : $seg['prpdetail']; 
                     }
                     $cont = (trim($seg['prpcontainer']) !== "") ? $seg['prpcontainer'] : "";
                     $sz = (trim($seg['metric']) !== "") ? trim($seg['metric']) : "";
                     if (trim($seg['metricuom']) !== "") { 
                       $sz .= (trim($sz) !== "") ? " {$seg['metricuom']}" : "";
                     }
                     $ass = trim($seg['assignment']);
                     if (trim($seg['assignmentname']) !== "" && trim($seg['assignmentname']) !== "QC" && trim($seg['assignmentname']) !== "BANK") { 
                       $ass .= (trim($ass) !== "") ? " [{$seg['assignmentname']}]" : $seg['assignmentname']; 
                     }
                     if (trim($seg['assignmentrequest']) !== "") { 
                       $ass .= (trim($ass) !== "") ? "/{$seg['assignmentrequest']}" : $seg['assignmentrequest'];
                     }

                     $bgTbl .= "<tr>
                                  <td>{$seg['bgs']}</td>
                                  <td>{$prep}</td>
                                  <td>{$cont}</td>
                                  <td>" . (int)$seg['qty'] . "</td>
                                  <td>{$sz}</td>
                                  <td>{$seg['hrpost']}</td>
                                  <td>{$seg['cutfromparentid']}</td>
                                  <td>{$ass}</th>
                                </tr>";    
                 }
                 $bgTbl .= "</table>";
                 $bgTbl .= "</td></tr>"; 
              }

            }



         }
         
         $bgTbl .= "</table>";         
         
         
         $rtn = $bgTbl; 
       }
    } else { 
       //ERROR MESSAGE 
       $rtn = "ERROR: ERROR MESSAGE";

    }
    return $rtn;
}

function buildProcurementQueryGrid($whichUsr) {
  $currentMonth = date('m');
  $currentYear = date('Y');
  $currentDay = date('d');
  $calPrcStart = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarprocstart/{$currentYear}/{$currentMonth}",serverIdent, apikey));
  $arrInst = $whichUsr->allowedinstitutions; 
  $mnuTbl = "<table border=0 id=\"mnuDspDivInstitution\" width=100% cellpadding=0 cellspacing=0>";
  foreach($arrInst as $val) {
    $mnuTbl .= "<tr><td data-menuvalue=\"{$val[0]}\" onclick=\"byId('BSQInstitution').value = '{$val[0]}';dspDropMenu('mnuDspDivInstitution');\" class=ddMenuItemDspValue>{$val[1]}</td></tr>";
    $allowedInstitutions .= " " .  $val[0] . " - " . $val[1];
  }
  $mnuTbl .= "</table>";
  $qTbl = "<table border=0>
             <tr>
             <td class=fldLabel>Biosample Institution</td>
             <td>
                <div class=zackdropmenuholder>
                  <div class=zackdspvalue>
                  <table>
                    <tr><td><span class=inputwrapper onclick=\"dspDropMenu('mnuDspDivInstitution');\"><input type=text id=BSQInstitution class=zackmenuinput READONLY placeholder=\"Procuring Institution\"></span></td></tr>
                  </table>
                  </div>
                  <div class=\"zackdspmenu fllDivDsp\" id=\"mnuDspDivInstitution\">{$mnuTbl}</div>
                </div>
             </td>
               <td class=fldLabel>Procurement Date</td>
               <td>
                  <div class=zackdropmenuholder>
                  <div class=zackdspvalue>
                   <table>
                   <tr>
                     <td><span class=inputwrapper onclick=\"dspDropMenu('calDivProcStart');\">
                           <input type=text id=BSQProcDateStart READONLY value=\"{$currentMonth}/{$currentDay}/{$currentYear}\" class=halfzackmenuinput placeholder=\"Start Date\"></span></td></tr></table></div>
                      <div class=\"zackdspmenu hlfDivDsp\" id=calDivProcStart>{$calPrcStart}</div>
                      </div>
               </td>
               </tr>
<tr><td colspan=4 align=right style=\"padding-top: 1.5vh;padding-right: .3vw;\">
   <table class=zackbutton onclick=\"prcquery();\"><tr><td>Display Procurement</td></tr></table>
</td></tr> </table>";
return $qTbl;    
}

function buildUserDisplay($whichUsr) { 
  $at = genAppFiles;  
//{$whichUsr->displayname} ({$whichUsr->emailaddress}) <b>last logged in</b> {$lastlogin['usagedatetime']} <b>from</b> {$lastlogin['ipaddress']} ({$lastlogin['city']}
//{"statuscode":200,"userid":1,"fullname":"Zack von Menchhofen","displayname":"Zack","accesslevel":"ADMINISTRATOR","emailaddress":"zacheryv@mail.med.upenn.edu","alternateemail":"zackvm@zacheryv.com","fiveonepword":"$2y$10$rKgZw7cgO5Iu9mxuffNev.4m\/iR2IQhbX8Eaq2oruScOs3f28zQgS","changepword":0,"allow":1,"pwordexpiredate":"2018-10-18 14:37:21","profilepicfile":"l7AbAkYj.jpeg","profilephone":"215-662-4570 x10","driverexpire":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"googleiconcode":"lock_open","menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[]],["472","REPORTS","",[]],["473","UTILITIES","",[]],["474","HELP","scienceserver-help",[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]],"lastlogin":{"userid":"1","usagedatetime":"2018-06-05 12:17:54","ipaddress":"170.212.0.96","hostname":"Penn Medicine","city":"Philadelphia","region":"Pennsylvania","postal":"19104","org":"AS46274 Penn Medicine"}} 
//  $modulelist = $whichUsr->allowedmodules;
//  $allowInstitutions = $whichUsr->allowedinstitutions; 
//  $lastlogin = $whichUsr->lastlogin;      

    $usrID = substr(('00000'.$whichUsr->userid),-5);
    $profilePic = $whichUsr->profilepicfile;
    $usrName = "{$whichUsr->fullname} ({$whichUsr->displayname})"; 
    $usrEml = $whichUsr->emailaddress;
    $usrAltEml = $whichUsr->alternateemail;
    $usrPhone = json_encode($whichUsr);
    $usrAltPhn = $whichUsr->altphone;
    if (trim($profilePic) !== "") { 
      $pPic = base64file( "{$at}/publicobj/graphics/usrprofile/{$profilePic}", "usrProfilePicture", "image" );  
    } else { 
      $pPic = "DEFAULT PROFILE PICTURE";  
    }
    
    
    
  $rtnThis = <<<USRDSP

   <table id=udTopClose>
   <tr><td id=udCloseBtn onclick="openAppCard('appCardUserProf');">&times close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>        
   </table> 
          
    <table border=1>
        <tr><td rowspan=8>{$pPic}</td><td>User Id: </td><td>{$usrID}</td></tr>
        <tr><td>Name: </td><td>{$usrName} </td></tr>
        <tr><td>Email: </td><td>{$usrEml} </td></tr>
        <tr><td>Alternate Email: </td><td>{$usrAltEml} </td></tr>        
        <tr><td>Phone: </td><td>{$usrPhone} </td></tr>   
        <tr><td>Alternate Phone: </td><td>{$usrAltPhn} </td></tr>               
        <tr><td colspan=2>&nbsp;</td></tr>            
    </table>
    
    <table border=1><tr><td>
   <table border=1><tr><td>App-System Information</td><td>Change ScienceServer Password</td><td>Edit Profile Information</td></tr></table>
   </td></tr>
     <tr><td>DIVS</td></tr>
      </table>  
        
        
    
USRDSP;
  
return $rtnThis;  
}

function buildStandardMenu($menujson , $menuID, $fldToFill = "", $dspDiv = "", $addClearLine = 0, $clickCode = "") { 
  $mnuArr = json_decode($menujson, true);
  if ($mnuArr['responseCode'] === 200) { 

     $mnuData = json_decode($mnuArr['datareturn'], true);

       if ((int)$mnuData['ITEMSFOUND'] > 1) {
           $mnuTbl = "<table border=0 id=\"{$menuID}\" width=100% cellpadding=0 cellspacing=0>";
           if ($addClearLine === 1) { 
                $mnuTbl .= "<tr><td data-menuvalue=\"NA\" onclick=\"byId('{$fldToFill}').value = '';dspDropMenu('{$dspDiv}');\" style=\"padding: 3px;\">&nbsp;</td></tr>";
           }

         foreach ($mnuData['DATA'] as $menuValueArr) {
             if ($fldToFill <> "") {
    
               switch ($clickCode) { 
                 case 'procDownStream':
                   $clickAdd = "getDownStreamMenu('downstreamcollectiontypes','{$menuValueArr['lookupvalue']}','mnuDspDivCollectionType','prcCollectionType');";   
                 break;
                 default: 
                   $clickAdd = "";
               }
               $mnuTbl .= "<tr><td data-menuvalue=\"{$menuValueArr['codevalue']}\" onclick=\"byId('{$fldToFill}').value = '{$menuValueArr['codevalue']}';dspDropMenu('{$dspDiv}');{$clickAdd}\" class=ddMenuItemDspValue>{$menuValueArr['menuvalue']}</td></tr>";
            } else {  
                $mnuTbl .= "<tr><td data-menuvalue=\"{$menuValueArr['codevalue']}\">{$menuValueArr['menuvalue']}</td></tr>";
            }    
         }
         $mnuTbl .= "</table>";
         $rtnThis = $mnuTbl;
       } else { 
         //NO MENU ITEMS
       }        
   } else { 
     $rtnThis = "BAD MENU RESPONSE CODE";
   }
  return $rtnThis;

}

function buildStandardCalendar($caljson) { 
    $calArr = json_decode($caljson, true);
    if ($calArr['responseCode'] === 200) { 
        $calData = json_decode($calArr['datareturn'], true);
        if ($calData['ITEMSFOUND'] <> 0) { 
          $rtnThis = $calData['DATA'];
        } else { 
          $rtnThis = "NO DATA";
        }
    } else { 
     $rtnThis = "BAD MENU RESPONSE CODE";
    }

return $rtnThis;
}

function buildSearchTermResults($qryid) { 
  $qryResults = json_decode(callrestapi("GET","https://data.chtneast.org/docsrchqryresults/{$qryid}",serverIdent, apikey), true);
  if ((int)$qryResults['responseCode'] === 200) { 
      //BUILD RESUTLS
    $d = json_decode($qryResults['datareturn'], true);
    $srchFor = trim($d['MESSAGE']);
    $innerResult = "<table id=inRsltTbl>";
    foreach($d['DATA'] as $val) {
        //preg_replace('#<[^>]+>#', ' ',
        //strip_tags(trim($val['pathreport']))
      $prtxt = preg_replace('#<[^>]+>#', ' ',trim($val['pathreport']));
      $pos = stripos($prtxt,$srchFor);    
      $start = 0;
      if ($pos === false) { 
      } else { 
         if ((int)$pos > 20) { 
           $start = ($pos - 20);
         }
      }

      if (!in_array( substr($val['dnpr_nbr'],0,5) , $bglisting)) { 
        $bglisting[] = substr($val['dnpr_nbr'],0,5);
      }
      $innerResult .= "<tr class=holdRow onclick=\"pullCompleteReport('{$val['dnpr_nbr']}');\"><td><table class=abstractDsp><tr><td class=dnprnbr>{$val['dnpr_nbr']}</td></tr><tr><td class=abstract>[<i>abstract</i>] ... " . substr($prtxt, $start, 200) . " ... </td></tr></table></td></tr>";
    }
    $innerResult .= "</table>";
    $rDiv = "<div id=docSrchRsltPanel>{$innerResult}</div>";

    $rtnThis = "<table id=pathRptRsultTbl>
                      <tr><td colspan=2 id=newsearch><span onclick=\"navigateSite('document-library');\">New Search</span> | <span onclick=\"bsquery();\">Send to Coordinator Query</span></td></tr>
                      <tr><td colspan=2 id=announceLine>Found: {$d['ITEMSFOUND']} objects while searching for: \"{$srchFor}\" <input type=hidden id=foundBGListing value=" . implode(",",$bglisting) . "></td></tr>";
    $rtnThis .= "<tr><td valign=top style=\"width: 15vw;\"><div id=prRsltDspDiv>{$rDiv}</div></td><td valign=top><div id=pathologyReportDisplayPanel></div></td></tr>";
    $rtnThis .= "</table>";
  } else { 
    $rtnThis = "ERROR: {$qryResults['datareturn']}";
  }
   return $rtnThis;
}

function buildDCQueryResults($qryid) { 
$qryResults = json_decode(callrestapi("GET","https://data.chtneast.org/dcqueryresults/{$qryid}",serverIdent, apikey), true);
$d = json_decode($qryResults['datareturn'], true);
if ((int)$d['ITEMSFOUND'] > 0) { 
    //BUILD TABLE
    if ( (int)$d['ITEMSFOUND'] > 1) { 
      $itm = "{$d['ITEMSFOUND']} items found"; 
    } else { 
      $itm = "{$d['ITEMSFOUND']} item found"; 
    }

    $dspTbl = "<table id=coorddataresults>
                 <thead>
                 <tr><th colspan=4 id=itemsFoundHead>{$itm}</th><th colspan=19 id=instrcell>Click row to select/Right-click column for context menu</th><tr>
                                 <th>&nbsp;</th>
                                 <th colspan=8 class=grpHead>PHI</th>
                                 <th colspan=8 class=grpHead>Sample Details</th>
                                 <th colspan=2 class=grpHead>HPR/QMS</th>
                                 <th colspan=2 class=grpHead>Asssignment</th>
                                 <th>&nbsp;</th>
                                 <th>&nbsp;</th>
                               </tr>  
                               <tr>
                                 <th class=colHeadr>CHTN #</th>
                                 <th class='colHeadr grpdelims'>Proc</th>
                                 <th class=colHeadr>Age</th> 
                                 <th class=colHeadr>Race</th>
                                 <th class=colHeadr>Sex</th>
                                 <th class=colHeadr>CX</th>
                                 <th class=colHeadr>RX</th>
                                 <th class=colHeadr>IC</th>
                                 <th class='colHeadr grpdelime'>PR</th>
                                 <th class=colHeadr>Segment Status</th>
                                 <th class=colHeadr>Procurement Date</th>
                                 <th class=colHeadr>Diagnosis Designation</th>
                                 <th class=colHeadr>HP</th>
                                 <th class=colHeadr>Preparation</th>
                                 <th class=colHeadr>Metric</th>
                                 <th class=colHeadr>Assoc</th>
                                 <th class='colHeadr grpdelime'>QTY</th>
                                 <th class=colHeadr>&nbsp;</th>
                                 <th class='colHeadr grpdelime'>&nbsp;</th>
                                 <th class=colHeadr>Shipping</th> 
                                 <th class='colHeadr'>Assignment</th>
                                 <th class='colHeadr'>Loc</th>
                                 <th class='colHeadr'>Cmt</th>
                               </tr></thead><tbody>";    
$rowCntr = 0;
foreach ($d['DATA'] as $bs) {  
    $dxd = "";
    $dxd .= (trim($bs['primarysite']) !== "") ? trim($bs['primarysite']) : "";
    $dxd .= (trim($bs['subsite']) !== "") ? " [{$bs['subsite']}]" : "";
    $dxd .= (trim($bs['diagnosis']) !== "") ? " / {$bs['diagnosis']}" : "";
    $dxd .= (trim($bs['subdiagnosis']) !== "") ? ":{$bs['subdiagnosis']}" : "";
    $dxd .= (trim($bs['specimencategory']) !== "") ? " ({$bs['specimencategory']})" : "";
    $dxd .= (trim($bs['metssite']) !== "") ? " / {$bs['metssite']}" : "";
    $sgs = "";
    $sgs .= (trim($bs['segstatus']) !== "") ? trim($bs['segstatus']) : ""; 
    $statdte = new DateTime($bs['statusdate']);
    $sgs .= (trim($bs['statusdate']) !== "") ? "<br>[" . $statdte->format('m/d/Y') . "]" : "";
    $prcdte = new DateTime($bs['procurementdate']);
    $prcd = (trim($bs['procurementdate']) !== "") ? $prcdte->format('m/d/Y') : "";
    $prcd .= " <br>{$bs['procuredatinstitution']}";
    $prp = "";
    $prp = (trim($bs['prepmethod']) !== "") ? "{$bs['prepmethod']}" : "";    
    if ( (trim($bs['preparation']) !== "") ) {
      if ($prp === "") { 
      } else { 
        $prp .= " / "; 
      }
      $prp .= "{$bs['preparation']}";
    }   
    if ( trim($bs['procuringtechnician']) !== "" ) { 
      //$prcd .= " / ". $bs['procuringtechnician']; 
      $prcd .= ""; 
    }
    $assigned = "";
    if ( trim($bs['assignedto']) !== "" ) { 
      $assigned = trim($bs['assignedto']);
    }
    if (trim($bs['assignedreq']) !== "") {
      if ($assigned === "") { 
        $assigned = $bs['assignedreq'];
      } else { 
        $assigned .= "/{$bs['assignedreq']}";
      }
      if (trim($bs['investigatorname']) !== "") { 
         $assigned .= "<br><span class=investigatorname>{$bs['investigatorname']}</span>";
      }
    }
    if ($bs['shipdocrefid'] !== "0000000") { 
        $shd = substr(("000000" . $bs['shipdocrefid']), -6) . "/{$bs['shipdocstatus']}"; 
        $trsd = $bs['shipdocrefid'];
    } else { 
        $shd = "";
        $trsd = "NA";
    }
    if (trim($bs['shippeddate']) !== "") {
      $shdte = new DateTime($bs['shippeddate']);
      $shd .= "<br>[" . $shdte->format('m/d/Y') . "]";
    }
    $locIcon = "";
    $locPopMenu = "";
    if (trim($bs['scannedlocation']) !== "") {
      $locIcon = "<i class=\"material-icons\" style=\"font-size: 1.8vh;\">location_on</i>";
      $locPopMenu = "getRightClickMenu('tr{$rowCntr}','LC','');";
    }

    $cmtIcon = "";
    $cmtPopMenu = "";
    if (trim($bs['questionhpr']) !== "" || trim($bs['biosamplecomment']) !== "" || trim($bs['segmentcomments']) !== "" || trim($bs['segmentvoidreason']) !== "") {
      $cmtIcon = "<i class=\"material-icons\" style=\"font-size: 1.8vh;\">comment</i>";
      $cmtPopMenu = "getRightClickMenu('tr{$rowCntr}','CM');";
    }
    
    $rce = "";
    if (trim($bs['pxirace']) !== "") { 
        $rce = strtoupper(substr($bs['pxirace'], 0,3));
    }
    $cx = strtoupper(substr($bs['dchemoind'],0,1));
    $rx = strtoupper(substr($bs['dradind'],0,1));
    $ic = strtoupper(substr($bs['dinformedconsent'],0,1));
    $prt = strtoupper(substr($bs['dpathologyreport'],0,1));
    $assoc = ((int)$bs['totalassocid'] > 1) ? $bs['totalassocid'] : "--";

    $metric = (trim($bs['metric']) !== "") ? trim($bs['metric']) : "";  
    $metric .= (trim($bs['metuom']) !== "") ? trim($bs['metuom']) : "";

    $dspTbl .= "<tr id='tr{$rowCntr}' 
                       data-selected='false' 
                       data-pbiosample={$bs['pbiosample']} 
                       data-bgs='{$bs['bgs']}'
                       data-pxi='{$bs['pxiid']}'
                       data-sd='{$trsd}'
                    onclick=\"selectRow('{$rowCntr}');\">
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$bs['bgs']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" class=grpdelim>{$bs['proctype']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$bs['pxiage']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$rce}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$bs['pxisex']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$cx}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$rx}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$ic}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','PH');return false;\" style='text-align: center;'>{$prt}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','SG');return false;\" class=grpdelim style=\"white-space:nowrap;\">{$sgs}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$prcd}</td> 
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$dxd}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$bs['hourspost']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','SG');return false;\">{$prp}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','SG');return false;\">{$metric}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$assoc}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','BG');return false;\">{$bs['qty']}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','QC');return false;\" class=grpdelim colspan=2>{$bs['dqcprocstatus']}</td>                  
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','SH');return false;\" class=grpdelim>{$shd}</td>
                  <td oncontextmenu=\"getRightClickMenu('tr{$rowCntr}','AS');return false;\">{$assigned}</td>
                  <td oncontextmenu=\"{$locPopMenu}return false;\" style='text-align: center;'>{$locIcon}&nbsp;</td>
                  <td oncontextmenu=\"{$cmtPopMenu}return false;\" style='text-align: center;'>{$cmtIcon}&nbsp;</td>
                </tr>";
++$rowCntr;
}
$dspTbl .= "</tbody></table><p>";
$rtnThis = $dspTbl;
} else { 
  //NO ITEMS FOUND DISPLAY
  $rtnThis = "NO ITEMS";

}
return $rtnThis;
}

function buildDCQueryGrid() { 
$currentMonth = date('m');
$currentYear = date('Y');

$calPrcStart = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarprocstart/{$currentYear}/{$currentMonth}",serverIdent, apikey));
$calPrcEnd = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarprocend/{$currentYear}/{$currentMonth}",serverIdent, apikey));
$calShpStart = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarshipstart/{$currentYear}/{$currentMonth}",serverIdent, apikey));
$calShpEnd = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarshipend/{$currentYear}/{$currentMonth}",serverIdent, apikey));

//buildStandardMenu($menujson , $menuID, $fldToFill = "", $dspDiv = "", $addClearLine = 0) { 
$mnuInstitution = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allinstitutions",serverIdent, apikey), "mnuBSQInstitution", "BSQInstitution", "mnuDspDivInstitution", 1);
$mnuSegStatus = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allsegmentstati",serverIdent, apikey), "mnuBSQSegmentStatus", "BSQSegmentStatus", "mnuDspDivSegStatus", 1);
$mnuPrepMethod = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allpreparationmethods",serverIdent, apikey), "mnuBSQPreparations", "BSQPreparation", "mnuDspDivPrepMethod", 1);
$mnuSDStatus = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allshipdocstatus",serverIdent, apikey), "mnuSDStatus", "SDQSDStatus", "mnuDspDivSDStatus", 1);
$mnuSPLive = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/specimencategorylive",serverIdent, apikey), "mnuSPLive", "BSQDxDesigSP", "mnuDivDspDXDesigSPSearch", 1);
$mnuQMSStat = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/qmsstatus",serverIdent, apikey), "mnuQMSStat", "QMSQQMSStatus", "mnuDspDivQMSStatus", 1);

//////////////////////////////////////
//TODO:ADD QUERY FOR PROTOCOL AND SUBJECT NUMBER 
//TODO:ADD QUERY FOR HPR/QMS STATUS/RESULTS
//////////////////////////////////////

$bsq = <<<BIOSGRID
<div id=biosamplegriddiv class=querycards>
<form id=BSQFrm class=queryGridForm>
<table border=0>
<tr><td colspan=25 class=gridTitle>Biosample Query</td></tr>
<tr><td class=fldLabel>Biosample Number(s)</td><td colspan=2><table><tr><td><input type=text id=BSQBiosample placeholder="Biosample Number, Series or Range" class=zackmenuinput></td></tr></table></td></tr>
<tr><td class=fldLabel>Procurement/Cut Date</td>
    <td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue>
      <table>
        <tr>
          <td><span class=inputwrapper onclick="dspDropMenu('calDivProcStart');"><input type=text id=BSQProcDateStart READONLY value="" class=halfzackmenuinput placeholder="Start Date"></span></td></tr></table></div>
    <div class="zackdspmenu hlfDivDsp" id=calDivProcStart>{$calPrcStart}</div>
    </div>
    </td><td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('calDivProcEnd');"><input type=text id=BSQProcDateEnd READONLY class=halfzackmenuinput placeholder="End Date"></span></td></tr></table></div>
    <div class="zackdspmenu hlfDivDsp" id=calDivProcEnd>{$calPrcEnd}</div>
    </div>
</td></tr>
<tr><td class=fldLabel>Biosample Institution</td><td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue>
       <table>
         <tr>
           <td><span class=inputwrapper onclick="dspDropMenu('mnuDspDivInstitution');"><input type=text id=BSQInstitution class=zackmenuinput READONLY placeholder="Procuring Institution (Biosample)"></span></td>
         </tr>
       </table>
    </div>
    <div class="zackdspmenu fllDivDsp" id="mnuDspDivInstitution">{$mnuInstitution}</div>
    </div>
</td></tr>
<tr><td class=fldLabel>Shipment Date</td>
 <td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('calDivShipStart');"><input type=text id=BSQShipDateStart READONLY class=halfzackmenuinput placeholder="Start Date"></span></td></tr></table></div>
    <div class="zackdspmenu hlfDivDsp" id=calDivShipStart>{$calShpStart}</div>
    </div>
</td>
<td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('calDivShipEnd');"><input type=text id=BSQShipDateEnd READONLY class=halfzackmenuinput placeholder="End Date"></td></tr></table></div>
    <div class="zackdspmenu hlfDivDsp" id=calDivShipEnd>{$calShpEnd}</div>
    </div>
</td></tr>
<tr><td class=fldLabel>Segment Status</td>
    <td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('mnuDspDivSegStatus');"><input type=text id=BSQSegmentStatus READONLY class=zackmenuinput placeholder="Segment Status"></td></tr></table></div>
    <div class="zackdspmenu fllDivDsp" id="mnuDspDivSegStatus">{$mnuSegStatus}</div>
    </div>
</td></tr>
<tr><td class=fldLabel>Assignment</td>
   <td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><input type=text id=BSQAssignment class=halfzackmenuinput placeholder="INV# (Type to search)" onkeyup="searchInv(this.value);"></td><td></td></tr></table></div>
    <div class="zackdspmenu fllDivDsp" id=mnuDspDivAssignmentSearch></div>
    </div>
</td><td>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><input type=text id=BSQReq class=halfzackmenuinput placeholder="REQ#"></td><td></td></tr></table></div>
    <div class=zackdspmenu id=mnuDspDivReqSearch></div>
    </div>
</td></tr>
<tr><td class=fldLabel>Site Designation</td>
<td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><input type=text id=BSQDxDesigSite class=zackmenuinput placeholder="Site (like value)"></td><td></td></tr></table></div>
    <div class=zackdspmenu id=mnuDivDspDXDesigSiteSearch></div>
    </div>
</td></tr>
<tr><td class=fldLabel>Specimen Category</td>
<td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('mnuDivDspDXDesigSPSearch');"><input type=text id=BSQDxDesigSP READONLY class=zackmenuinput placeholder="Specimen Category"></td></tr></table>
    <div class="zackdspmenu fllDivDsp" id=mnuDivDspDXDesigSPSearch>{$mnuSPLive}</div>
    </div>
</td></tr>
<tr><td class=fldLabel>Diagnosis Designation</td>
<td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><input type=text id=BSQDxDesigDx class=zackmenuinput placeholder="Diagnosis (like value)"></td><td></td></tr></table></div>
    <div class=zackdspmenu id=mnuDivDspDXDesigDXSearch></div>
    </div>
</td></tr>
<tr><td class=fldLabel>Preparation</td>
    <td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('mnuDspDivPrepMethod');"><input type=text id=BSQPreparation READONLY class=zackmenuinput placeholder="Preparation Method"></td></tr></table></div>
    <div class="zackdspmenu fllDivDsp" id="mnuDspDivPrepMethod">{$mnuPrepMethod}</div>
    </div>
</td></tr>
<tr><td colspan=3 align=right style="padding-top: 1.5vh;padding-right: .3vw;">
   <table class=zackbutton onclick="bsqquery();"><tr><td>Run Query</td></tr></table>
</td></tr>
</table>
</form>
</div>
BIOSGRID;

$shq = <<<SHIPDOCQ
<div id=shipdocgriddiv class=querycards>
<form id=SHPFrm class=queryGridForm>
<table border=0>
<tr><td colspan=25 class=gridTitle>Ship-Doc Query</td></tr>
<tr><td class=fldLabel>Ship-Doc Number(s)</td><td colspan=2><table><tr><td><input type=text id=SDQSDNumber class=zackmenuinput placeholder="ShipDoc Number, Range or Series"></td></tr></table></td></tr>
<tr><td class=fldLabel>Shipdoc Status</td>
    <td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('mnuDspDivSDStatus');"><input type=text id=SDQSDStatus READONLY class=zackmenuinput placeholder="Ship Doc Status Indicators"></span></td></tr></table></div>
    <div class="zackdspmenu fllDivDsp" id="mnuDspDivSDStatus">{$mnuSDStatus}</div>
    </div>
</td></tr>
<tr><td colspan=3 align=right style="padding-top: 1.5vh;padding-right: .3vw;">
<table class=zackbutton onclick="shdquery();"><tr><td>Run Query</td></tr></table>
</td></tr>
</table>
</form>
</div>
SHIPDOCQ;

$qms = <<<QMSSTAT
<div id=qmsgriddiv class=querycards>
<form id=QMSFrm class=queryGridForm>
<table border=0>
<tr><td colspan=25 class=gridTitle>HPR/QMS Process</td></tr>
<tr><td class=fldLabel>HPR/QMS Process Status</td>
    <td colspan=2>
    <div class=zackdropmenuholder>
    <div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick="dspDropMenu('mnuDspDivQMSStatus');"><input type=text id=QMSQQMSStatus READONLY class=zackmenuinput placeholder="QMS Process Status"></span></td></tr></table></div>
    <div class="zackdspmenu fllDivDsp" id="mnuDspDivQMSStatus">{$mnuQMSStat}</div>
    </div>
</td></tr>
<!-- <tr><td class=fldLabel>QMS Process Status</td><td colspan=2><table><tr><td><input type=text id=SDQSDNumber class=zackmenuinput placeholder="HPR/QMS Process Status"></td></tr></table></td></tr> //-->
<tr><td colspan=3 align=right style="padding-top: 1.5vh;padding-right: .3vw;">
<!-- <table class=zackbutton onclick="shdquery();"><tr><td>Run Query</td></tr></table> //-->
</td></tr>
</table>
</form>
</div>
QMSSTAT;

$rtnThis = <<<DCQUERY
<table border=0>
<tr><td>
   <table border=0>
     <tr>
       <td onclick="changeQueryGrid('biosamplegriddiv');" class="zackbutton topQryButtons">Biosamples</td>
       <td onclick="changeQueryGrid('shipdocgriddiv');" class="zackbutton topQryButtons">Ship Docs</td>
       <td onclick="changeQueryGrid('qmsgriddiv');" class="zackbutton topQryButtons">QMS Process</td>
     </tr>
   </table>
</td></tr>
<tr><td><div id=qryGridHolder>{$bsq}{$shq}{$qms}</div></td></tr>
</table>
DCQUERY;
return $rtnThis;
}

function createzackdropdownelement($definitionArray) { 
    //Array = array("dropDivId" => "mnuDspDivInstitution", "fieldId" => "prcInstitution", "fieldName" => "prcInstitution", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procuring Institution", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => $mnuTbl, "defaultValue" => "")
 
   $addStyle = (trim($definitionArray['fieldAddStyle']) !== "" ) ? " style=\"{$definitionArray['fieldAddStyle']}\" " : "";
   $RDOnly = ((int)$definitionArray['readOnlyInd'] === 1) ? " READONLY " : "";
   $plcHld = (trim($definitionArray['fieldPlaceHolder']) !== "") ? " placeholder = '{$definitionArray['fieldPlaceHolder']}' " : "";
   $divValues = (trim($definitionArray['divValueTbl']) !== "") ? $definitionArray['divValueTbl'] : "";
   $dftVal = (trim($definitionArray['defaultValue']) !== "") ? " value=\"{$definitionArray['defaultValue']}\" " : "";
   $clickAction = (trim($definitionArray['clickAction']) !== "") ? $definitionArray['clickAction'] : " dspDropMenu('{$definitionArray['dropDivId']}'); ";

   $elementRtn = "<div class=zackdropmenuholder><div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick=\"{$clickAction}\"><input type=text name='{$definitionArray['fieldName']}' id='{$definitionArray['fieldId']}' class='{$definitionArray['fieldClassName']}' {$addStyle} {$RDOnly} {$plcHld} {$dftVal}></span></td></tr></table></div><div class=\"{$definitionArray['divClassName']}\" id=\"{$definitionArray['dropDivId']}\">{$divValues}</div></div>";
   return $elementRtn;
} 

function buildBlankBiosampleCollection($whichUsr) { 
  //$modulelist = $whichUsr->allowedmodules;
  //$allowInstitutions = $whichUsr->allowedinstitutions; 
  //$lastlogin = $whichUsr->lastlogin;  
  $currentMonth = date('m');
  $currentYear = date('Y');
  $currentDay = date('d'); 
  $calProcure = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarprocurement/{$currentYear}/{$currentMonth}",serverIdent, apikey));
  $calProcedure = buildStandardCalendar(callrestapi("GET","https://data.chtneast.org/generatecalendar/general/calendarprocedure/{$currentYear}/{$currentMonth}",serverIdent, apikey));

  //CHECK PRESENT LOC AND MAKE THIS THE DEFAULT
  $arrInst = $whichUsr->allowedinstitutions;
  $mnuTbl = "<table border=0 id=\"mnuDspDivInstitution\" width=100% cellpadding=0 cellspacing=0>";
  foreach($arrInst as $val) {
    $mnuTbl .= "<tr><td data-menuvalue=\"{$val[0]}\" onclick=\"byId('prcInstitution').value = '" . trim($val[1]) ."';dspDropMenu('mnuDspDivInstitution');\" class=ddMenuItemDspValue>" . trim($val[1]) . "</td></tr>";
    $allowedInstitutions .= " " .  $val[0] . " - " . $val[1];
  }
  $mnuTbl .= "</table>";

  //($menujson , $menuID, $fldToFill = "", $dspDiv = "", $addClearLine = 0, $clickCode = "")  
  $metricUOM = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/metricuoms",serverIdent, apikey),"mnuIWeightUOM","prcIWeightUOM","mnuDspIWeightUOM",0);
  $iWeightMnu = createzackdropdownelement(array("dropDivId" => "mnuDspIWeightUOM", "fieldId" => "prcIWeightUOM", "fieldName" => "prcIWieghtUOM", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 4vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "UOM", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$metricUOM}", "defaultValue" => "", "clickAction" => ""));
  
  $mnuProcTypes = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allproctypes",serverIdent, apikey),"mnuProcedureTypes","prcProcedureType","mnuDspDivProcType",0,"procDownStream");

  $mnuUnknown = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/unknownmet",serverIdent, apikey),"mnuUnknownMetInd","prcUnknownMets","mnuDspDivUnknownMet",0);
  $unknownmet = createzackdropdownelement(array("dropDivId" => "mnuDspDivUnknownMet", "fieldId" => "prcUnknownMets", "fieldName" => "prcUnknownMets", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 6vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Unknown Met", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnuUnknown}", "defaultValue" => "NA", "clickAction" => ""));

  $prcInstitutionDrop = createzackdropdownelement(array("dropDivId" => "mnuDspDivInstitution", "fieldId" => "prcInstitution", "fieldName" => "prcInstitution", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procuring Institution", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => $mnuTbl, "defaultValue" => "", "clickAction" => ""));
  $prcProcSrt = createzackdropdownelement(array("dropDivId" => "calDivProcurement", "fieldId" => "prcProcurementDate", "fieldName" => "prcProcurementDate", "fieldClassName" => "halfzackmenuinput", "fieldAddStyle" => "width: 6vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procurement Date", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => $calProcure, "defaultValue" => "{$currentMonth}/{$currentDay}/{$currentYear}", "clickAction" => ""));
  $prcProceeSrt = createzackdropdownelement(array("dropDivId" => "calDivProcedure", "fieldId" => "prcProcedureDate", "fieldName" => "prcProcedureDate", "fieldClassName" => "halfzackmenuinput", "fieldAddStyle" => "width: 6vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procedure Date", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => $calProcedure, "defaultValue" => "{$currentMonth}/{$currentDay}/{$currentYear}", "clickAction" => ""));
  $procTypeFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivProcType", "fieldId" => "prcProcedureType", "fieldName" => "prcProcedureType", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 8vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procedure Type", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => $mnuProcTypes, "defaultValue" => "", "clickAction" => ""));
  $collType = createzackdropdownelement(array("dropDivId" => "mnuDspDivCollectionType", "fieldId" => "prcCollectionType", "fieldName" => "prcCollectionType", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 9vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Collection Type", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => ""));

  $specCatFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivSpecCat", "fieldId" => "prcSpecimenCategory", "fieldName" => "prcSpecimenCategory", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 10vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Specimen Category", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => "desigSearch('prcSpecimenCategory','mnuDspDivSpecCat','prcSpecimenCategory');"));
  $siteFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivSite", "fieldId" => "prcPrimarySite", "fieldName" => "prcPrimarySite", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 11vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Site (anatomic)", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => "desigSearch('prcPrimarySite','mnuDspDivSite','prcPrimarySite');"));
  $subSiteFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivSubSite", "fieldId" => "prcSubSite", "fieldName" => "prcSubsite", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 12vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Sub-Site", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => "desigSearch('prcSubSite','mnuDspDivSubSite','prcSubSite');"));
  $dxFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivDX", "fieldId" => "prcDiagnosis", "fieldName" => "prcDiagnosis", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 12vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Diagnosis", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => "desigSearch('prcDiagnosis','mnuDspDivDX','prcDiagnosis');"));
  $dxModFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivDXMod", "fieldId" => "prcDiagnosisModifier", "fieldName" => "prcDiagnosisModifier", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 12vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Diagnosis Modifier", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "", "defaultValue" => "", "clickAction" => "desigSearch('prcDiagnosisModifier','mnuDspDivDXMod','prcDiagnosisModifier');"));


  $mnupathrpt = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/prcprpt",serverIdent,apikey),"mnuPathReport","prcPathologyReport","mnuDspDivPathReport",0);
  $prcpathreport = createzackdropdownelement(array("dropDivId" => "mnuDspDivPathReport", "fieldId" => "prcPathologyReport", "fieldName" => "prcPathologyReport", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 6vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnupathrpt}", "defaultValue" => "", "clickAction" => ""));

  $mnuUninv = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/yesno",serverIdent,apikey),"mnuUninvolvedSampleYN","prcUninvolvedSample","mnuUninvolved",0);
  $uninvMnu = createzackdropdownelement(array("dropDivId" => "mnuUninvolved", "fieldId" => "prcUninvolvedSample", "fieldName" => "prcUninvolvedSample", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 4vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "0", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnuUninv}", "defaultValue" => "No", "clickAction" => ""));
  $mnuAASite = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/allasites",serverIdent, apikey),"mnuAllAnatomicSites","prcMetSite","mnuDspDivMSite",1);
  $mSiteFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivMSite", "fieldId" => "prcMetSite", "fieldName" => "prcMetSite", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Metastized from", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "{$mnuAASite}", "defaultValue" => "", "clickAction" => ""));
  $mnuADX = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/alldxs",serverIdent, apikey),"mnuAllDXSites","prcMetDX","mnuDspDivMDX",1);
  $mDXFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivMDX", "fieldId" => "prcMetDX", "fieldName" => "prcMetDX", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Metastized Diagnosis", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "{$mnuADX}", "defaultValue" => "", "clickAction" => ""));
  $mnuSDX = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/alldxs",serverIdent, apikey),"mnuAllSDX","prcSDX","mnuDspDivSDX",1);
  $mSDXFld = createzackdropdownelement(array("dropDivId" => "mnuDspDivSDX", "fieldId" => "prcSDX", "fieldName" => "prcSDX", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Systemic Diagnosis", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "{$mnuSDX}", "defaultValue" => "", "clickAction" => ""));

  $mnuAgeUOM = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/ageuoms",serverIdent, apikey),"mnuAgeUOM","prcAgeUOM","mnuDspDivAgeUOM",0);
  $ageUOM = createzackdropdownelement(array("dropDivId" => "mnuDspDivAgeUOM", "fieldId" => "prcAgeUOM", "fieldName" => "prcAgeUOM", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "UOM-Age", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => "{$mnuAgeUOM}", "defaultValue" => "years", "clickAction" => ""));
  $mnupxirace = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/pxirace",serverIdent, apikey),"mnuPXIRace","prcPXIRace","mnuDspDivPXIRace",0);
  $pxiRace = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXIRace", "fieldId" => "prcPXIRace", "fieldName" => "prcPXIRace", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 12vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Donor Race", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnupxirace}", "defaultValue" => "", "clickAction" => ""));
  $mnupxisex = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/pxisex",serverIdent,apikey),"mnuPXISex","prcPXISex","mnuDspDivPXISex",0);
  $pxisex = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXISex", "fieldId" => "prcPXISex", "fieldName" => "prcPXISex", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnupxisex}", "defaultValue" => "", "clickAction" => ""));
  $mnucxvals = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/pxicx",serverIdent,apikey),"mnuPXICX","prcPXICX","mnuDspDivPXICX",0);
  $pxicx = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXICX", "fieldId" => "prcPXICX", "fieldName" => "prcPXICX", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnucxvals}", "defaultValue" => "", "clickAction" => ""));
  $mnurxvals = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/pxirx",serverIdent,apikey),"mnuPXIRX","prcPXIRX","mnuDspDivPXIRX",0);
  $pxirx = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXIRX", "fieldId" => "prcPXIRX", "fieldName" => "prcPXIRX", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnurxvals}", "defaultValue" => "", "clickAction" => ""));
  $mnuifcvals = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/prcinfc",serverIdent,apikey),"mnuPXIIFC","prcPXIIFC","mnuDspDivPXIIFC",0);
  $pxiifc = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXIIFC", "fieldId" => "prcPXIIFC", "fieldName" => "prcPXIIFC", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnuifcvals}", "defaultValue" => "", "clickAction" => ""));
  $mnupxisogi = buildStandardMenu(callrestapi("GET","https://data.chtneast.org/globalmenu/upennsogi",serverIdent, apikey),"mnuPXISOGI","prcPXISOGI","mnuDspDivPXISOGI",1);
  $pxiSOGI = createzackdropdownelement(array("dropDivId" => "mnuDspDivPXISOGI", "fieldId" => "prcPXISOGI", "fieldName" => "prcPXISOGI", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 10.5vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "UPenn SOGI", "divClassName" => "zackdspmenu hlfDivDsp", "divValueTbl" => "{$mnupxisogi}", "defaultValue" => "", "clickAction" => ""));

  $bsTop = "<table border=0 class=sectionTable>
              <tr>
                <td colspan=8 class=sectionHeadr>Biosample Details</td></tr>
                <tr><td>Procurement Location</td><td>Procurement Date</td><td>Procedure Date</td><td>Procedure Type</td><td>Collection Type</td><td colspan=2>Initial Weight</td><td>Uninvolved Sample</td></tr>
                <tr>
                  <td>{$prcInstitutionDrop}</td>
                  <td>{$prcProcSrt}</td>
                  <td>{$prcProceeSrt}</td>
                  <td>{$procTypeFld}</td>
                  <td>{$collType}</td>
                  <td><input type=text id=prcInitialWeight class=zackmenuinput style=\"width: 4vw;text-align: right;\" placeholder=0></td>
                  <td>{$iWeightMnu}</td>
                  <td>{$uninvMnu}</td></tr></table>"; 

  $bsDtl = "<table border=0 class=sectionTable><tr><td colspan=5 class=sectionHeadr>Diagnosis Designation</td></tr>
            <tr><td>Specimen Category</td><td colspan=2>Site-Subsite</td><td colspan=2>Diagnosis-Modifier</td></tr>
            <tr>
              <td>{$specCatFld}</td>
              <td>{$siteFld}</td>
              <td>{$subSiteFld}</td>
              <td>{$dxFld}</td>
              <td>{$dxModFld}</td></tr>
            </table>

            <table border=0>
            <tr><td>Unknown Met</td><td>METS From Site</td><td>METS Diagnosis</td><td>Pathology Report</td><td>Systemic Diagnosis</td></tr>
            <tr>
              <td>{$unknownmet}</td>
              <td>{$mSiteFld}</td>
              <td>{$mDXFld}</td>
              <td>{$prcpathreport}</td>
              <td>{$mSDXFld}</td></tr>
            </table>";

   $pxiTbl = <<<PXITBL

<table border=0 class=sectionTable>
<tr><td colspan=25 class=sectionHeadr>Donor Information</td></tr>
<tr><td>PXI-ID</td><td>Initials</td><td colspan=2>Age</td><td>Race</td><td>Sex</td><td>Chemo</td><td>Radiation</td><td>Informed</td><td>SOGI</td></tr>
<tr>
<td><input type=text id=prcPXIID READONLY class=zackmenuinput style="width: 3vw;" placeholder="Delink"></td>
<td><input type=text id=prcPXIIni READONLY class=zackmenuinput style="width: 3vw;"></td>
<td><input type=text id=prcPXIAge class=zackmenuinput style="width: 2vw;"></td>
<td>{$ageUOM}</td>
<td>{$pxiRace}</td>
<td>{$pxisex}</td>
<td>{$pxicx}</td>
<td>{$pxirx}</td>
<td>{$pxiifc}</td>
<td>{$pxiSOGI}</td>
</tr>

</table>
PXITBL;

  $cmtFlds = <<<COMMENTFIELDS

<table border=0 class=sectionTable><tr><td colspan=25 class=sectionHeadr>Comments</td></tr>
<tr><td>Biosample Comments</td><td>Comments/Question for HPR Reviewer</td></tr>
<tr><td><TEXTAREA id=prcBSCmts class=zackmenuinput style="overflow:auto;resize:none; width: 30vw;"></TEXTAREA></td><td><TEXTAREA id=prcHPRQstn class=zackmenuinput style="overflow:auto;resize:none; width: 30vw;"></TEXTAREA></td></tr>
</table>

COMMENTFIELDS;

$rtnThis = <<<RTNTHIS
<form id=procurementCollectionScreen>
<table border=0><tr><td coslpan=2><table border=0><tr><td>BG#</td></tr><tr><td><input type=text id=prcBGNbr class=zackmenuinput READONLY style="width: 5vw;" placeholder=''></td></tr></table></td></tr>
<tr><td colspan=2>{$bsTop}</td><td valign=top rowspan=2><div id=segmentcollectionside></div></td></tr>
<tr><td>{$bsDtl}{$pxiTbl}{$cmtFlds} </td></tr>
</table>
</form>

RTNTHIS;
//THIS MAKES A NICE CHECK BOX <td><label class="chkboxcontainer"><input type="checkbox" id=prcUninvolved><span class="checkmark"></span>Uninvolved Sample</label>
return $rtnThis;
}

function buildPulledBiosampleCollection($whichUsr, $bgnbr) { 

//PULL BIOGROUP NUMBER WITH SEGMENTS - CHECK THAT USER HAS RIGHTS - CHECK MIGRATION/LOCK STATUS    
$modulelist = $whichUsr->allowedmodules;
$allowInstitutions = $whichUsr->allowedinstitutions; 
$lastlogin = $whichUsr->lastlogin;  
  
$rtnThis = <<<RTNTHIS
{$bgnbr}

RTNTHIS;
return $rtnThis;

}

