<?php

class pagecontent {

    //json_encode($rqstStr) THIS IS THE ARRAY HOLDING THE URI COMPONENTS 
    //$whichUsr THIS IS THE USER ARRAY {"statusCode":200,"loggedsession":"i46shslvmj1p672lskqs7anmu1","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":20,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"googleiconcode":"lock_open","menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"googleiconcode":"account_balance","menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""}]],["472","REPORTS","",[{"googleiconcode":"account_balance","menuvalue":"All Reports","pagesource":"all-reports","additionalcode":""}]],["473","UTILITIES","",[{"googleiconcode":"account_balance","menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474",null,null,[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]}     

public $maximizeBtn = "<i class=\"material-icons\">keyboard_arrow_up</i>";
public $minimizeBtn = "<i class=\"material-icons\">keyboard_arrow_down</i>"; 
public $closeBtn = "<i class=\"material-icons\">close</i>";
public $menuBtn = "<i class=\"material-icons\">menu</i>";
public $checkBtn = "<i class=\"material-icons\">check</i>";

function sysDialogBuilder($whichdialog, $passedData) {
 
    switch($whichdialog) {
      case 'dataCoordinatorHPROverride':

        if ( count($passedData) > 0 ) {   
          $biogroupTbl = "<form id=frmQMSSubmitter><table border=0><tr><td valign=top rowspan=2><table border=0 id=qmsSldListTbl><thead><tr><th></th><th>Biogroup</th><th>Designation</th><th>Path-Rpt</th><th>QMS Conclusion</th><th>Present Status</th><th>New Status</th><th>Slide Submitted</th></tr></thead><tbody>";
          $submittalCnt = 0;
          foreach ($passedData as $key => $val) { 
              $rowId = "";
              $bgency = cryptservice($val);
              $arr['bgency'] = cryptservice($val);
              $passdata = json_encode($arr); 
              //{"MESSAGE":"","ITEMSFOUND":0,"DATA":{"bg":"81948","qcprocstatus":"R","desigsite":"LIVER","desigdx":"","desigspeccat":"NORMAL","hprdecision":"CONFIRM","hprslidereviewed":"81948T_002"}}
              //TODO:THIS ASSUMES THAT ALL BIOGROUPS WILL ALWAYS BE FOUND ... CATCH ERRORS HERE 


              $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-menu-hpr-inventory-override",serverIdent, serverpw), true);
              $devm = "<table border=0 class=menuDropTbl style=\"min-width: 14.9vw;\">";
              foreach ($devarr['DATA'] as $devval) { 
                $devm .= "<tr><td onclick=\"fillField('fldDeviationReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
              }
              $devm .= "</table>";

              $idta = json_decode(callrestapi("POST", dataTree . "/data-doers/biogroup-hpr-status",serverIdent, serverpw, $passdata), true);  //GETS ALL SLIDES IN A GROUP THAT ARE NOT SHIPPED           
              $desig = ( trim($idta['DATA']['desigsite']) !== "") ? strtoupper(trim($idta['DATA']['desigsite'])) : "";
              $desig .= ( trim($idta['DATA']['desigdx']) !== "") ? " / " . strtoupper(trim($idta['DATA']['desigdx'])) : "";
              $desig .= ( trim($idta['DATA']['desigspeccat']) !== "") ? strtoupper(" [" . trim($idta['DATA']['desigspeccat']) . "]") : "";
              $prsQMSStat = strtoupper(trim($idta['DATA']['qmsvalue']));                         
              $prsFld = "<input type=hidden id=\"qmsPresentValue{$val}\" value=\"{$idta['DATA']['qcprocstatus']}\">"; 
              $qmsvl = ( trim($idta['DATA']['hprdecision']) !== "" ) ? trim($idta['DATA']['hprdecision']) : "";
              $qmsvl .= (trim($idta['DATA']['hprslidereviewed']) !== "" ) ?  ( trim($qmsvl) !== "" ) ? " / " . preg_replace('/[Tt]_/','',trim($idta['DATA']['hprslidereviewed'])) : preg_replace('/[Tt]_/','',trim($idta['DATA']['hprslidereviewed'])) : "";
              $prpresent = $idta['DATA']['prpresent'];
              $newQMS = '&nbsp;';
              $chkbox = "&nbsp;";
              //TODO: Make this dynamic AND NON-REPEATING!  
              switch ($idta['DATA']['qcprocstatus']) {                   
                 case 'H':
                    if (count($idta['DATA']['slidelist']) > 0) { 
                      $slideTbl = "<table border=0 class=sldOptionListTbl><tr><td colspan=4 onclick=\"fillField('fldSld{$submittalCnt}','','');\" class=sldOptionClear>[clear]</td></tr>";
                      foreach ( $idta['DATA']['slidelist'] as $sk => $sv ) {  
                        $submitslide = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['bgs'] : "";
                        $submitslideid = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['segmentid'] : "";
                        $hprind = ($sv['hprind'] == 'Y') ? "H" : "-"; //THIS IS MARKED AS HPR SLIDE
                        $hpralready = ($sv['tohpr'] == '') ? "-" : "X"; //ALREADY SUBMITTED TO QMS
                        $sldAssign = (trim($sv['assignedto']) === "") ? "-" : "A"; //ASSIGNED
                        $slideTbl .= "<tr onclick=\"fillField('fldSld{$submittalCnt}','{$sv['segmentid']}','{$sv['bgs']}');\"><td class=sldLblFld>{$sv['bgs']}</td><td class=sldusedfld>{$hpralready}</td><td class=sldassignhpr>{$hprind}</td><td class=sldassigninv>{$sldAssign}</td></tr>";
                      }
                      $slideTbl .= "</table>"; 
                      $slidepicker = "<div class=menuHolderDiv><input type=hidden value=\"{$submitslideid}\" id=\"fldSld{$submittalCnt}Value\" READONLY><input type=text value=\"{$submitslide}\" id=\"fldSld{$submittalCnt}\" style=\"width: 10vw;\" READONLY><div class=valueDropDown style=\"width: 10vw;\">{$slideTbl}</div></div>";
                      $newQMS = 'RESUBMIT';
                      $chkbox = "<input type=checkbox CHECKED id='chkBox{$submittalCnt}' onclick=\"checkBoxIndicators(this.id);\">";
                      $rowId = "tr{$submittalCnt}";
                      $submittalCnt++;
                    } else { 
                      $slidepicker = "NO SLIDES IN THIS BIOGROUP";
                    }

                    break;
                case 'N':
                    if (count($idta['DATA']['slidelist']) > 0) { 
                      $slideTbl = "<table border=0 class=sldOptionListTbl><tr><td colspan=4 onclick=\"fillField('fldSld{$submittalCnt}','','');\" class=sldOptionClear>[clear]</td></tr>";
                      foreach ( $idta['DATA']['slidelist'] as $sk => $sv ) {  
                        $submitslide = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['bgs'] : "";
                        $submitslideid = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['segmentid'] : "";
                        $hprind = ($sv['hprind'] == 'Y') ? "H" : "-"; //THIS IS MARKED AS HPR SLIDE
                        $hpralready = ($sv['tohpr'] == '') ? "-" : "X"; //ALREADY SUBMITTED TO QMS
                        $sldAssign = (trim($sv['assignedto']) === "") ? "-" : "A"; //ASSIGNED
                        $slideTbl .= "<tr onclick=\"fillField('fldSld{$submittalCnt}','{$sv['segmentid']}','{$sv['bgs']}');\"><td class=sldLblFld>{$sv['bgs']}</td><td class=sldusedfld>{$hpralready}</td><td class=sldassignhpr>{$hprind}</td><td class=sldassigninv>{$sldAssign}</td></tr>";
                      }
                      $slideTbl .= "</table>"; 
                      $slidepicker = "<div class=menuHolderDiv><input type=hidden value=\"{$submitslideid}\" id=\"fldSld{$submittalCnt}Value\" READONLY><input type=text value=\"{$submitslide}\" id=\"fldSld{$submittalCnt}\" style=\"width: 10vw;\" READONLY><div class=valueDropDown style=\"width: 10vw;\">{$slideTbl}</div></div>";
                      $newQMS = 'SUBMIT';
                      $chkbox = "<input type=checkbox CHECKED id='chkBox{$submittalCnt}' onclick=\"checkBoxIndicators(this.id);\">";
                      $rowId = "tr{$submittalCnt}";                      
                      $submittalCnt++;
                    } else { 
                      $slidepicker = "NO SLIDES IN THIS BIOGROUP";
                    }
                    break;
                case 'L':
                    if (count($idta['DATA']['slidelist']) > 0) { 
                      $slideTbl = "<table border=0 class=sldOptionListTbl><tr><td colspan=4 onclick=\"fillField('fldSld{$submittalCnt}','','');\" class=sldOptionClear>[clear]</td></tr>";
                      foreach ( $idta['DATA']['slidelist'] as $sk => $sv ) {  
                        $submitslide = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['bgs'] : "";
                        $submitslideid = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['segmentid'] : "";
                        $hprind = ($sv['hprind'] == 'Y') ? "H" : "-"; //THIS IS MARKED AS HPR SLIDE
                        $hpralready = ($sv['tohpr'] == '') ? "-" : "X"; //ALREADY SUBMITTED TO QMS
                        $sldAssign = (trim($sv['assignedto']) === "") ? "-" : "A"; //ASSIGNED
                        $slideTbl .= "<tr onclick=\"fillField('fldSld{$submittalCnt}','{$sv['segmentid']}','{$sv['bgs']}');\"><td class=sldLblFld>{$sv['bgs']}</td><td class=sldusedfld>{$hpralready}</td><td class=sldassignhpr>{$hprind}</td><td class=sldassigninv>{$sldAssign}</td></tr>";
                      }
                      $slideTbl .= "</table>"; 
                      $slidepicker = "<div class=menuHolderDiv><input type=hidden value=\"{$submitslideid}\" id=\"fldSld{$submittalCnt}Value\" READONLY><input type=text value=\"{$submitslide}\" id=\"fldSld{$submittalCnt}\" style=\"width: 10vw;\" READONLY><div class=valueDropDown style=\"width: 10vw;\">{$slideTbl}</div></div>";
                      $newQMS = 'RESUBMIT';
                      $chkbox = "<input type=checkbox CHECKED id='chkBox{$submittalCnt}' onclick=\"checkBoxIndicators(this.id);\">";                   
                      $rowId = "tr{$submittalCnt}";                      
                      $submittalCnt++;
                    } else { 
                      $slidepicker = "NO SLIDES IN THIS BIOGROUP";
                    }
                    break;
                case 'R':
                    $newQMS = '';
                    $chkbox = "&nbsp;";                   
                    $slidepicker = "";
                    break;
                case 'S':
                    $newQMS = '';
                    $chkbox = "&nbsp;";                   
                    $slidepicker = "";
                    break;
                case 'Q':
                    $newQMS = '';
                    $chkbox = "&nbsp;";
                    $slidepicker = "";
                    break;
                default:
                    if (count($idta['DATA']['slidelist']) > 0) { 
                      $slideTbl = "<table border=0 class=sldOptionListTbl><tr><td colspan=4 onclick=\"fillField('fldSld{$submittalCnt}','','');\" class=sldOptionClear>[clear]</td></tr>";
                      foreach ( $idta['DATA']['slidelist'] as $sk => $sv ) {  
                        $submitslide = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['bgs'] : "";
                        $submitslideid = ($sv['hprind'] == 'Y' && $sv['tohpr'] != 'Y') ? $sv['segmentid'] : "";
                        $hprind = ($sv['hprind'] == 'Y') ? "H" : "-"; //THIS IS MARKED AS HPR SLIDE
                        $hpralready = ($sv['tohpr'] == '') ? "-" : "X"; //ALREADY SUBMITTED TO QMS
                        $sldAssign = (trim($sv['assignedto']) === "") ? "-" : "A"; //ASSIGNED
                        $slideTbl .= "<tr onclick=\"fillField('fldSld{$submittalCnt}','{$sv['segmentid']}','{$sv['bgs']}');\"><td class=sldLblFld>{$sv['bgs']}</td><td class=sldusedfld>{$hpralready}</td><td class=sldassignhpr>{$hprind}</td><td class=sldassigninv>{$sldAssign}</td></tr>";
                      }
                      $slideTbl .= "</table>"; 
                      $slidepicker = "<div class=menuHolderDiv><input type=hidden value=\"{$submitslideid}\" id=\"fldSld{$submittalCnt}Value\"><input type=text value=\"{$submitslide}\" id=\"fldSld{$submittalCnt}\" style=\"width: 10vw;\" READONLY><div class=valueDropDown style=\"width: 10vw;\">{$slideTbl}</div></div>";
                      $newQMS = 'SUBMIT';
                      $chkbox = "<input type=checkbox CHECKED id='chkBox{$submittalCnt}' onclick=\"checkBoxIndicators(this.id);\">";                   
                      $rowId = "tr{$submittalCnt}";                      
                      $submittalCnt++;
                    } else { 
                      $slidepicker = "NO SLIDES IN THIS BIOGROUP";
                    }
              }

              $biogroupTbl .= <<<BGTBL
<tr id="{$rowId}" data-bg="{$val}" data-newqms="{$newQMS}">  
<td>{$chkbox}</td>  
<td>{$val}</td>   
<td>{$desig}</td> 
<td><center>{$prpresent}</td>  
<td>{$qmsvl}</td> 
<td>{$prsQMSStat}{$prsFld}</td>     
<td>{$newQMS}</td>
<td class=fldHolder>{$slidepicker}</td>
</tr>
BGTBL;
          }
          $biogroupTbl .= "</tbody><tfoot><tr><td colspan=7 align=right>Submittals to QMS Process: &nbsp;</td><td id=nbrQMSSubmittal>{$submittalCnt}</td></tr></tfoot>";
          $biogroupTbl .= "</table></td>";
          //BUILD HPR TRAY LIST
          //{"MESSAGE":"status message 68c33f8hkvok7q6h4ip5rb5oq0","ITEMSFOUND":0,"DATA":[{"parentid":293,"locationid":294,"locationdsp":"Tray: 001","scancode":"HPRT001","hprtraystatus":"SENT"},{"parentid":293,"locationid":330,"locationdsp":"Tray: 002","scancode":"HPRT002","hprtraystatus":"LOADED"},{"parentid":293,"locationid":331,"locationdsp":"Tray: 003","scancode":"HPRT003","hprtraystatus":"SENT"},{"parentid":293,"locationid":663,"locationdsp":"Tray: 004","scancode":"HPRT004","hprtraystatus":"SENT"},{"parentid":293,"locationid":1033,"locationdsp":"Tray: 005","scancode":"HPRT005","hprtraystatus":null},{"parentid":293,"locationid":1034,"locationdsp":"Tray: 006","scancode":"HPRT006","hprtraystatus":null},{"parentid":293,"locationid":1035,"locationdsp":"Tray: 007","scancode":"HPRT007","hprtraystatus":null},{"parentid":293,"locationid":1036,"locationdsp":"Tray: 008","scancode":"HPRT008","hprtraystatus":null},{"parentid":293,"locationid":1037,"locationdsp":"Tray: 009","scancode":"HPRT009","hprtraystatus":null},{"parentid":293,"locationid":1038,"locationdsp":"Tray: 010","scancode":"HPRT010","hprtraystatus":null},{"parentid":293,"locationid":1039,"locationdsp":"Tray: 011","scancode":"HPRT011","hprtraystatus":null},{"parentid":293,"locationid":1040,"locationdsp":"Tray: 012","scancode":"HPRT012","hprtraystatus":null}]}
          $hprtlist = json_decode(callrestapi("GET", dataTree . "/hpr-tray-list", serverIdent, serverpw), true) ;
          $hprtTbl = "<table border=0>";
          foreach($hprtlist['DATA'] as $hprk => $hprv) { 
              //TODO: MAKE THIS CHECK DYNAMIC
              if ($hprv['hprtraystatus'] === 'SENT' || $hprv['hprtraystatus'] === 'LOADED') { 
                $hprtTbl .= "<tr><td style=\"text-decoration: line-through;\">{$hprv['locationdsp']}</td></tr>";                
              } else { 
              $hprtTbl .= "<tr><td onclick=\"fillField('fldHPRTray','{$hprv['scancode']}','{$hprv['locationdsp']}');\">{$hprv['locationdsp']}</td></tr>";
              }
          }
          $hprtTbl .= "</table>";
          
          $traylist = "<table border=0><tr><th align=left>Slide Tray</th></tr><tr><td><div class=menuHolderDiv><input type=hidden id=\"fldHPRTrayValue\"><input type=text id=\"fldHPRTray\" style=\"width: 15vw;\" READONLY><div class=valueDropDown style=\"width: 15vw;\">{$hprtTbl}</div></div></td></tr><tr><th align=left>Inventory User PIN</th></tr><tr><td><input type=password id=fldUsrPIN style=\"width: 15vw;\"></td></tr><tr><td align=right><table class=tblBtn id=btnHPRSendTray style=\"width: 6vw;\" onclick=\"sendHPRTray();\"><tr><td style=\"white-space: nowrap;\"><center>Send Tray</td></tr></table></td></tr><tr><td style=\"width: 15vw; text-align: justify;\"><b>CHTNEASTERN SOP DEVIATION NOTIFICATION</b>: This is NOT a standard inventory screen and should only be used in extenuating operating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures. Please enter a reason for the deviation below.</td></tr><tr><td><b>Deviation Reason</b></td></tr><tr><td><div class=menuHolderDiv><input type=text id=fldDeviationReason style=\"width: 15vw;\"><div class=valueDropDown>{$devm}</div></div></td></tr></table>";
          $biogroupTbl .= "<td valign=top>{$traylist}</td></tr>";
          $biogroupTbl .= "<tr><td valign=top>&nbsp;</td></tr><tr><td colspan=2>Slide Option Menu Legend: X = Slide has been used in QMS / H = Slide is part of HPR Group / A = Slide is assigned to investigator</td></tr></table></form>";

        } else { 
          $biogroupTbl = "ERROR: NO BIOGROUPS SELECTED!";          
        }

        $titleBar = "QMS/HPR Inventory Override";
        $innerDialog = <<<DIALOGINNER
        {$biogroupTbl}
DIALOGINNER;
        $footerBar = "";
      break;
      case 'dataCoordinatorShipDocCreate': 
        $si = serverIdent;
        $sp = serverpw;
        $titleBar = "Shipment Document Creation";
        $dataString = $passedData; 
        $dta = json_decode($passedData, true);
        $idta = json_decode(callrestapi("GET", dataTree . "/investigator-head/{$dta['inv']}", serverIdent, serverpw),true);
        $wsStatus = (int)$idta['status'];
        if ($wsStatus === 200) {  
          $iName = $idta['DATA']['investigator'];
          $istatus = $idta['DATA']['investstatus'];
          $iinstitution = $idta['DATA']['institution'];
          $iinsttype = $idta['DATA']['institutiontype'];
          $iprimediv = $idta['DATA']['primarydivision'];
          $iemail = $idta['DATA']['investemail'];
          $sadta = json_decode(callrestapi("GET", dataTree . "/investigator-ship-address/{$dta['inv']}", serverIdent, serverpw),true);
          $wsSAStatus = (int)$sadta['status'];
          if ($wsSAStatus === 200) {  
            $shipAdd = (trim($sadta['DATA']['adattn']) !== "") ? "Attn: {$sadta['DATA']['adattn']}" : "";
            $shipAdd .= (trim($sadta['DATA']['adinstitution']) !== "") ? "\r\n{$sadta['DATA']['adinstitution']}" : "";
            $shipAdd .= (trim($sadta['DATA']['addept']) !== "") ? "\r\n{$sadta['DATA']['addept']}" : "";
            $shipAdd .= (trim($sadta['DATA']['adline1']) !== "") ? "\r\n{$sadta['DATA']['adline1']}" : "";
            $shipAdd .= (trim($sadta['DATA']['adline2']) !== "") ? "\r\n{$sadta['DATA']['adline2']}" : "";
            $locShipLine = (trim($sadta['DATA']['adcity']) !== "") ? "{$sadta['DATA']['adcity']}" : "";
            $locShipLine .= (trim($sadta['DATA']['adstate']) !== "") ? (trim($locShipLine) !== "") ? ", {$sadta['DATA']['adstate']}" : "{$sadta['DATA']['adstate']}" : "" ;
            $locShipLine .= (trim($sadta['DATA']['adzipcode']) !== "") ? (trim($locShipLine) !== "") ? " {$sadta['DATA']['adzipcode']}" : "{$sadta['DATA']['adzipcode']}" : "" ;
            $shipAdd .= "\r\n{$locShipLine}";
            //$shipAdd .= (trim($sadta['DATA']['adcountry']) !== "") ? "\r\n{$sadta['DATA']['adcountry']}" : "";
            $shipPhone = (trim($sadta['DATA']['adphone']) !== "") ? preg_replace('/\([Ee][Xx][Tt]\.\s+\)/','',"{$sadta['DATA']['adphone']}") : "";
            $shipEmail = (trim($sadta['DATA']['ademail']) !== "") ? "{$sadta['DATA']['ademail']}" : "";
          } else { 
            //NO SHIPPING ADDRESS
          }

          $badta = json_decode(callrestapi("GET", dataTree . "/investigator-bill-address/{$dta['inv']}", serverIdent, serverpw),true);
          $wsBAStatus = (int)$badta['status'];
          if ($wsBAStatus === 200) {  
            $billAdd = (trim($badta['DATA']['adattn']) !== "") ? "Attn: {$badta['DATA']['adattn']}" : "";
            $billAdd .= (trim($badta['DATA']['adinstitution']) !== "") ? "\r\n{$badta['DATA']['adinstitution']}" : "";
            $billAdd .= (trim($badta['DATA']['addept']) !== "") ? "\r\n{$badta['DATA']['addept']}" : "";
            $billAdd .= (trim($badta['DATA']['adline1']) !== "") ? "\r\n{$badta['DATA']['adline1']}" : "";
            $billAdd .= (trim($badta['DATA']['adline2']) !== "") ? "\r\n{$badta['DATA']['adline2']}" : "";
            $locBillLine = (trim($badta['DATA']['adcity']) !== "") ? "{$badta['DATA']['adcity']}" : "";
            $locBillLine .= (trim($badta['DATA']['adstate']) !== "") ? (trim($locBillLine) !== "") ? ", {$badta['DATA']['adstate']}" : "{$badta['DATA']['adstate']}" : "" ;
            $locBillLine .= (trim($badta['DATA']['adzipcode']) !== "") ? (trim($locBillLine) !== "") ? " {$badta['DATA']['adzipcode']}" : "{$badta['DATA']['adzipcode']}" : "" ;
            $billAdd .= "\r\n{$locBillLine}";
            //$billAdd .= (trim($badta['DATA']['adcountry']) !== "") ? "\r\n{$badta['DATA']['adcountry']}" : "";
            $billPhone = (trim($badta['DATA']['adphone']) !== "") ? preg_replace('/\([Ee][Xx][Tt]\.\s+\)/','',"{$badta['DATA']['adphone']}") : "";
            $billEmail = (trim($badta['DATA']['ademail']) !== "") ? "{$badta['DATA']['ademail']}" : "";
          } else { 
            //NO SHIPPING ADDRESS
          }
    
          $shCalendar = buildcalendar('shipSDCFrom'); 
$shpCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=sdcRqstShipDateValue><input type=text READONLY id=sdcRqstShipDate class="inputFld"></div>
  <div class=valueDropDown id=sdshpcal><div id=rShpCalendar>{$shCalendar}</div></div>
</div>
CALENDAR;

          $lbCalendar = buildcalendar('shipSDCToLab'); 
$labCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=sdcRqstToLabDateValue><input type=text READONLY id=sdcRqstToLabDate class="inputFld"></div>
  <div class=valueDropDown id=tolabcal><div id=rToLabCalendar>{$lbCalendar}</div></div>
</div>
CALENDAR;

//GET PO ALLOW VALUES
          $poarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ship-doc-po-values",$si,$sp),true);
          $po = "<table border=0 class=menuDropTbl>";
          foreach ($poarr['DATA'] as $poval) { 
            $po .= "<tr><td onclick=\"fillField('sdcPurchaseOrder','{$poval['lookupvalue']}','{$poval['menuvalue']}');\" class=ddMenuItem>{$poval['menuvalue']}</td></tr>";
          }
          $po .= "</table>";

          $segListR = json_decode($passedData, true);    
          $segmentTbl = "<table><tr><th>Segments To Add To Shipdoc (Scroll!)</th></tr>";
          $rowCount = 0;
          foreach($segListR as $sgK => $sgV) { 
            if (trim($sgK) !== 'inv') {
              $segmentTbl .= "<tr><td><input type=checkbox CHECKED data-segment=\"{$sgV['segmentid']}\" data-bgs=\"{$sgV['bgslabel']}\" id=\"sdcBGSList{$rowCount}\"><td>{$sgV['bgslabel']}</td></tr>";
              $rowCount++;
            }
          }
          $segmentTbl .= "</table>";

        $innerDialog = <<<DIALOGINNER
<form id=frmShipDocCreate><table border=0  id=sdcMainHolderTbl>
    <tr><td>Ship Doc</td><td>Shipment Accepted By *</td><td>Acceptor's Email *</td><td>Shipment Purchase Order # *</td><td>Requested Ship Date *</td><td>Date to Pull *</td><td>Segments For Shipment *</td></tr>
    <tr>
            <td><input type=text id=sdcShipDocNbr READONLY value="NEW"></td>
            <td><input type=text id=sdcAcceptedBy value=""></td>
            <td><input type=text id=sdcAcceptorsEmail value=""></td>
            <td><div class=menuHolderDiv><input type=text id=sdcPurchaseOrder value=""><div class=valueDropDown style="width: 20vw;">{$po}</div></div></td>
            <td>{$shpCalendar}</td>
            <td>{$labCalendar}</td>
           <td rowspan=4 valign=top id=segmentListHolder><div id=sdcSegmentListDiv><!-- SEGMENT LISTING //--> {$segmentTbl}</div> </td></tr>
    <tr><td colspan=6> <table><tr><td>Public Comments</td></tr><tr><td><TEXTAREA id=sdcPublicComments></textarea></td></tr></table> </td></tr>         

<tr><td valign=top colspan=6>
<table border=0 width=100%><tr><td id=TQAnnouncement>Investigator Information from CHTN TissueQuest</td></tr><tr><td>This information is from the central CHTN database (TissueQuest).  If you correct or change any information below you must also update it in TissueQuest!</td></tr></table>
 <table border=0>
   <tr><td>Investigator Code *</td><td>Investigator Name *</td><td>Investigator's Email *</td><td>Primary Division</td></tr>
   <tr>
     <td><input type=text id=sdcInvestCode READONLY value="{$dta['inv']}"></td>
     <td><input type=text id=sdcInvestName value="{$iName}"></td>
     <td><input type=text id=sdcInvestEmail value="{$iemail}"></td>
     <td><input type=text id=sdcInvestPrimeDiv READONLY value="{$iprimediv}"></td>
   </tr>
</table>
<table border=0><tr><td>Institution</td><td>Institution Type</td><td>TQ-Status</td></tr>
<tr>
   <td><input type=text id=sdcInvestInstitution value="{$iinstitution}"></td>
   <td><input type=text id=sdcInvestTQInstType READONLY value="{$iinsttype}"></td>
   <td><input type=text id=sdcInvestTQStatus READONLY value="{$istatus}"></td>
</tr>
</table>

<table border=0><tr><td valign=top>

<table border=0><tr><td colspan=2>Shipping Address *</td></tr>
<tr><td colspan=2><TEXTAREA id=sdcInvestShippingAddress>{$shipAdd}</TEXTAREA></td></tr>
<tr><td>Shipping Phone * (format: '(123) 456-7890 x0000' / x is optional)</td><!-- <td>Shipping Email</td> //--></tr>
<tr><td><input type=text id=sdcShippingPhone value="{$shipPhone}"></td><td><!--<input type=text id=sdcShippingEmail value="{$shipEmail}">//--></td>
</table>

</td><td valign=top>

<table border=0><tr><td colspan=2>Billing Address *</td></tr>
<tr><td colspan=2><TEXTAREA id=sdcInvestBillingAddress>{$billAdd}</TEXTAREA></td></tr>
<tr><td>Billing Phone (format: '(123) 456-7890 x0000' / x is optional)</td><!--<td>Billing Email</td>//--></tr>
<tr><td><input type=text id=sdcBillPhone value="{$billPhone}"></td><!--<td><input type=text id=sdcBillEmail value="{$billEmail}">//--></td>
</table>
</td></tr></table>

</td></tr>
<tr><td colspan=6 align=right><table class=tblBtn id=btnDialogAssign style="width: 6vw;" onclick="packCreateShipdoc();"><tr><td><center>Save</td></tr></table>    
</table></form>


DIALOGINNER;
        $footerBar = "";
    } else { 

        $innerDialog = <<<DIALOGINNER

MISSING INVESTIGATOR INFORMATION

DIALOGINNER;
        $footerBar = "";
    }
      break;
      case 'dataCoordinatorBGSAssignment': 
        $titleBar = "Segment Assignment";
        $footerBar = "";
        $dataString = $passedData; 
        $dta = json_decode($passedData, true);
        $dspSegTbl .= "<table border=0 id=assigningSegTbl><tr><td colspan=3 id=saTitle>Segments Being Assigned</td></tr><tr>";
        $cellCntr = 0;
        foreach ($dta as $ky => $vl) { 
            if ($cellCntr === 3) { 
              $dspSegTbl .= "</tr><tr>";
              $cellCntr = 0; 
            }
            $dspSegTbl .= "<td class=segmentBGSNbr>{$vl['bgslabel']}</td>";
            $cellCntr++;
        }
        $dspSegTbl .= "</table>";
        $innerDialog = <<<DIALOGINNER
<table border=0>
<tr><td colspan=2 style="font-size: 1.8vh;padding: 8px;">Please specify an investigator code and request number: <input type=hidden value='{$dataString}' id=dialogBGSListing></td><td rowspan=3 valign=top id=assigSegHolder><div id=segmentAssignListing>{$dspSegTbl}</td></tr>
<tr><td valign=top><center>
   <table border=0>
   <tr><td class=fldLabel>Investigator Id</td><td class=fldLabel>Request #</td></tr>
   <tr>
       <td style="width: 10vw;">
         <div class=suggestionHolder>
          <input type=text id=selectorAssignInv class="inputFld" onkeyup="selectorInvestigator(); byId('selectorAssignReq').value = '';byId('requestDropDown').innerHTML = ''; ">
          <div id=assignInvestSuggestion class=suggestionDisplay>&nbsp;</div>
         </div>
       </td>
       <td>
         <div class=menuHolderDiv onmouseover="byId('assignInvestSuggestion').style.display = 'none'; setAssignsRequests();">
          <input type=text id=selectorAssignReq READONLY class="inputFld" style="width: 8vw;">
          <div class=valueDropDown id=requestDropDown style="min-width: 8vw;"></div>
         </div>
       </td>
   </tr>
   <tr><td style="font-size: 1vh">(Suggestions on name, institution, inv#)</td><td></td></tr>
   </table>
</td></tr>
<tr><td colspan=2><center>
<table><tr><td>   
<table class=tblBtn id=btnDialogAssign style="width: 6vw;" onclick="sendSegmentAssignment('invest');" ><tr><td><center>Assign</td></tr></table>
</td><td>
<table class=tblBtn id=btnDialogBank style="width: 6vw;" onclick="sendSegmentAssignment('bank');"><tr><td><center>Bank</td></tr></table>
</td></tr></table>

 </td></tr>
</table>
DIALOGINNER;
      break;
      default: 
      $innerTbl = "";
    }

  $rtnthis = <<<PAGEHERE
<table border=0 cellspacing=0 cellpadding=0>
<tr><td id=systemDialogTitle>{$titleBar}</td><td onclick="closeSystemDialog();" id=systemDialogClose>{$this->closeBtn}</td></tr>
<tr><td colspan=2>
  {$innerDialog}
</td></tr>
<tr><td colspan=2>{$footerBar}</td></tr>
</table>
PAGEHERE;
return $rtnthis;
}

function segment($rqststr, $whichusr) { 
    if ((int)$whichusr->allowcoord !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE COORDINATOR SCREEN";        
    } else {    
        $rtnthis = <<<PAGEHERE
                {$rqststr[2]}
PAGEHERE;
        
    }    
    return $rtnthis;
}

function procurebiosample($rqststr, $whichusr) { 

if (trim($rqststr[2]) === "") { 
  //BUILD BASIC COLLECTION SCREEN
  $topBtnBar = generatePageTopBtnBar('procurebiosample');
  $pg = bldBiosampleProcurement( $whichusr );
} 

if (trim($rqststr[2]) !== "") { 
   //BUILD EDIT COLLECTION SCREEN
   //$topBtnBar = generatePageTopBtnBar('procurebiosample');
   //$pg = "PAGE GOES HERE";
} 

$rtnthis = <<<PAGEHERE
{$topBtnBar} 
{$pg}
PAGEHERE;
return $rtnthis;    

}

function reports($rqststr, $whichusr) { 

    //$rqststr[2] = Module Listing
    //$rqststr[2] = also denotes reportresults
    //$rqststr[3] = report name
    //$rqststr[4] = object id

  if ((int)$whichusr->allowcoord !== 1) { 
    $pg = "<h1>USER IS NOT ALLOWED TO USE THE REPORT SCREEN";        
  } else {    
   
   $accesslvl = $whichusr->accessnbr;
   if (trim($rqststr[2]) === "") { 
       $dta = json_decode(callrestapi("GET", dataTree . "/report-group-listing", serverIdent, serverpw),true);
       $itemsfound = $dta['ITEMSFOUND'];      
       $dspTbl = "<table border=0><tr>";
       $cellCntr = 0;
       foreach ($dta['DATA'] as $grpKey => $grpVal) { 
         if ($cellCntr === 8) { 
             $dspTbl .= "</tr><tr>";
             $cellCntr = 0;
         }  
         $dspTbl .= "<td class=rptGroupBtn onclick=\"navigateSite('reports/{$grpVal['groupingurl']}');\"><table class=rptGrpTitleBox><tr><td class=rptGrpTitle>{$grpVal['groupingname']}</td></tr><tr><td class=rptGrpDesc>{$grpVal['groupingdescriptions']}</td></tr></table></td>";
         $cellCntr++;
       }
       $dspTbl .= "</tr></table>";
     $pg = <<<CONTENT
{$dspTbl}
CONTENT;
   }

   if ( (trim($rqststr[2]) !== "" && trim($rqststr[2]) !== 'reportresults' ) && trim($rqststr[3]) === "") {
       //GET REPORTS IN MODULE ($rqststr[2]) 
       $dta = json_decode(callrestapi("GET", dataTree . "/group-report-listing/{$rqststr[2]}", serverIdent, serverpw),true);
       $rptsFound = count($dta['DATA'][0]['rptlist']);
       $dspTbl = "<table border=0 id=reportListBox><tr><td id=bigTitle>{$dta['DATA'][0]['groupname']}</td></tr><tr><td id=bigDesc>{$dta['DATA'][0]['groupdesc']}</td></tr><tr><td id=bigFound>Reports Found: {$rptsFound}</td></tr><tr><td>";
       if ((int)$rptsFound > 0) { 
                 $innerTbl = "<table border=0><tr>";
                 $innerCellCntr = 0;
                 foreach ($dta['DATA'][0]['rptlist'] as $k => $v) { 
                     if ($innerCellCntr === 10) { 
                         $innerTbl .= "</tr><tr>"; 
                         $innerCellCntr = 0;
                     }
                     $innerTbl .= "<td onclick=\"navigateSite('reports/{$v['groupingurl']}/{$v['urlpath']}');\" class=reportListBtn><table class=reportDefInnerTbl><tr><td class=rptTitle>{$v['reportname']}</td></tr><tr><td class=rptDescription>{$v['reportdescription']}</td></tr></table></td>";
                 }                 
                 $innerTbl .= "</tr></table>";
                 $dspTbl .= $innerTbl;
                 $innerCellCntr++;
       }
       $dspTbl .= "</td></tr></table>";       
       $pg = <<<CONTENT
{$dspTbl}
CONTENT;
     }  

   if ( trim($rqststr[2]) === 'reportresults' &&  trim($rqststr[3]) !== "") {
     //TABULAR REPORT RESULTS   
     $topBtnBar = generatePageTopBtnBar('reportresultsscreen');
     $pgContent = bldReportResultsScreen(trim($rqststr[3]), $whichusr);
     $pg = <<<CONTENT
{$pgContent}
CONTENT;
   }
     
     if ( trim($rqststr[2]) !== 'reportresults' && trim($rqststr[3]) !== "") {
       //GET REPORT PARAMETERS
       $topBtnBar = generatePageTopBtnBar('reportscreen');
       $reportParameters = bldReportParameterScreen($rqststr[3], $whichusr);        
       $pg = <<<CONTENT
{$reportParameters}
CONTENT;
     }
   }
    
  $rtnthis = <<<PAGEHERE
{$topBtnBar} 
{$pg}
PAGEHERE;

return $rtnthis;    
}

function scienceserverhelp($rqststr, $whichusr) { 
    
  $rtnthis = <<<PAGEHERE

          SCIENCESERVER HELP

PAGEHERE;
return $rtnthis;    
}


function datacoordinator($rqststr, $whichusr) { 
    if ((int)$whichusr->allowcoord !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE COORDINATOR SCREEN";        
    } else {    
        if (trim($rqststr[2]) === "") {
            $BSGrid = buildBSGrid();
            $topBtnBar = generatePageTopBtnBar('coordinatorCriteriaGrid');
            $rtnthis = <<<MAINQGRID
{$topBtnBar}
    <table border=0 id=mainQGridHoldTbl cellspacing=0 cellpadding=0>
        <tr>
            <td>&nbsp;</td>        
        </tr> 
         <tr><td colspan=4 valign=top>
               <div id=gridholdingdiv>     
                 <div class=gridDiv id=biogroupdiv>{$BSGrid}</div>            
               </div>     
         </td></tr>                    
    </table>
MAINQGRID;

        } else {

//RESULT SCREEN            
$dta = json_decode(callrestapi("GET", dataTree . "/biogroup-search/{$rqststr[2]}", serverIdent, serverpw),true);
$itemsfound = $dta['DATA']['searchresults'][0]['itemsfound'];      

if ((int)$itemsfound > 0 ) { 
$dataTbl .= <<<TOPROW
<table border=0 id="coordinatorResultTbl">
   <thead>
   <tr>
   <th></th>
   <th></th>
   <th>Label</th>
   <th>Status</th>
   <th class="cnttxt">QMS</th>
   <th class="groupingstart">Category</th>
   <th>Site-Subsite</th>
   <th>Diagnosis-Modifier</th>
   <th>Mets Site</th>
   <th class="groupingstart">Procurement</th>
   <th>Institution</th>
   <th class="groupingstart cnttxt">A</th>
   <th class="cnttxt">R</th>
   <th class="cnttxt">S</th>
   <th class="cnttxt">CX</th>
   <th class="cnttxt">RX</th>
   <th class="cnttxt">PR</th>
   <th class="cnttxt">IC</th>
   <th>PMethod</th>
   <th>Preparaion</th>
   <th class="cnttxt">HP</th>
   <th>Metric</th>
   <th class="cnttxt">Qty</th>
   <th class=groupingstart>Ship Doc</th>
   <th>Investigator</th>
   <th>Request</th>
</tr>
</thead>
<tbody>
TOPROW;

$pbident = "";
foreach ($dta['DATA']['searchresults'][0]['data'] as $fld => $val) { 
    if ($pbident <> $val['pbiosample']) { 
        //GET NEW COLOR
        $colorArray = getColor($val['pbiosample']);
        $pbSqrBgColor = " style=\"background: rgba({$colorArray[0]}, {$colorArray[1]}, {$colorArray[2]},1); \" ";
        $pbident = $val['pbiosample'];
    }
    if ($val['bsvoid'] === 1 || $val['sgvoid'] === 1) { 
      //MARK AS VOIDED
        $strikeoutInd = "strikeout";
    } else { 
        $strikeoutInd = "zck";
    }

    switch ($val['qcstatuscode']) { 
      case 'S': //SUBMITTED
        $qmsicon = "<i class=\"material-icons\">offline_bolt</i>";
        $clssuffix = "s";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      case 'L': //LAB ACTION  
        $qmsicon = "<i class=\"material-icons\">schedule</i>";  
        $clssuffix = "l";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      case 'R': //RESUBMITTED 
        $qmsicon = "<i class=\"material-icons\">history</i>";
        $clssuffix = "r";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      case 'H':
        $qmsicon = "<i class=\"material-icons\">offline_pin</i>";
        $clssuffix = "h";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      case 'Q':
        $qmsicon = "<i class=\"material-icons\">stars</i>";
        $clssuffix = "q";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      case 'N':
        $qmsicon = "<i class=\"material-icons\">play_circle_outline</i>";
        $clssuffix = "n";
        $qcstatustxt = "QMS Process: {$val['qcstatus']}";
      break;
      default:  //NO VALUE MATCHING
        $qmsicon = "<i class=\"material-icons\">help_outline</i>";
        $clssuffix = "";
        $qcstatustxt = "QMS Process: NOT STATUSED!";
    }

    $sglabel = preg_replace( '/[Tt]_/','',$val['bgs']);
    $stsDte = (trim($val['statusdate']) === "") ? "<br>&nbsp;" : "<br><center><span class=tinyText>({$val['statusdate']})</span>";    
    $assmnt = (strtoupper(substr($val['assignedinvestigator'],0,3)) === "INV") ?  "{$val['assignedinvestigatorlname']}, {$val['assignedinvestigatorfname']} ({$val['assignedinvestigator']})<br>{$val['assignedinvestigatorinstitute']}" : "" ;
    $subsitedsp = (trim($val['subsite']) === "") ? "" : ("::" . $val['subsite']);
    $modds = (trim($val['diagnosismodifier']) === "") ? "" : ("::" . $val['diagnosismodifier']);
    
    if ((int)$val['shipdocnbr'] === 0) {
        $dspSD = "";
        if (trim($val['shipmentdate']) !== "") { 
          $dspSD = "&nbsp;<br><span class=tinyText>({$val['shipmentdate']})</span>";
        } 
    } else {

        if (trim($val['shipmentdate']) !== "") { 
          $dtedspthis = "<br><span class=tinyText>({$val['shipmentdate']})</span>";
        } else { 
          $dtedspthis = "<br><span class=tinyText>&nbsp;</span>";
        }

        $dspSD = "<div class=ttholder>" . substr(('000000' . $val['shipdocnbr']),-6) . $dtedspthis;
        if (trim($val['sdstatus']) === "") { 
            $dspSD .= "<div class=tt>&nbsp;</div>";
        } else { 
            $dspSD .= "<div class=tt>Shipdoc Status: {$val['sdstatus']}<br>Status by: [INFO NOT AVAILABLE]</div>";
        }
        $dspSD .= "</div>";
    }
    
    $bscmt = preg_replace( '/-{2,}/','',preg_replace('/SS[Vv]\d/','',$val['bscomment']));
    $hrcmt = preg_replace( '/-{2,}/','',preg_replace('/SS[Vv]\d/','',$val['hprquestion']));
    $sgcmt = preg_replace('SS[Vv]\dSEGMENT COMMENTS','',$val['sgcomments']);
    $invloc = $val['scannedlocation'];
    
    $cmtDsp = "";    
    $cmtDsp .= ( trim($bscmt)  !== "" ) ? "<b>Biosample Comments</b>: {$bscmt}" : "";
    $cmtDsp .= ( trim($hrcmt) !== "") ?  "<br><b>HPR Question</b>:  {$hrcmt}" : "";
    $cmtDsp .= ( trim($sgcmt) !== "" ) ? "<br><b>Segment Comments</b>: {$sgcmt}" : "";
    $cmtDsp .= ( trim($invloc) !== "" ) ?  (trim($cmtDsp) !== "") ?  "<br><b>Inventory Location</b>: {$invloc}" : "<b>Inventory Location</b>: {$invloc}" : "";
    
    //$sgencry = cryptservice($val['segmentid']);
    $sgencry = cryptservice($val['segmentid']);
    $bgencry = cryptservice($val['pbiosample']);
    $sdencry = ( trim($val['shipdocnbr']) !== "" ) ? cryptservice($val['shipdocnbr']) : "";
    $moreInfo = ( trim($cmtDsp) !== "" ) ? "<div class=ttholder><div class=infoIconDiv><i class=\"material-icons informationalicon\">error_outline</i></div><div class=infoTxtDspDiv>{$cmtDsp}</div></div>" : "";
    
    
$dataTbl .=  <<<LINEITEM
   <tr 
     id="sg{$val['segmentid']}" 
     class="resultTblLine" 
     data-biogroup="{$val['pbiosample']}" 
     data-ebiogroup="{$bgencry}" 
     data-shipdoc="{$val['shipdocnbr']}" 
     data-eshipdoc = "{$sdencry}";
     data-bgslabel="{$sglabel}" 
     data-segmentid="{$val['segmentid']}" 
     data-selected="false" 
     data-associd="{$val['associd']}" 
     onclick="rowselector('sg{$val['segmentid']}');" 
     ondblclick="navigateSite('segment/{$sgencry}');"
  >
  <td>{$moreInfo}</td>
  <td {$pbSqrBgColor} class=colorline>&nbsp;</td>
  <td valign=top class=bgsLabel>{$sglabel}</td>
  <td valign=top>{$val['segstatus']} {$stsDte}</td>
  <td class="qms qmsiconholder{$clssuffix}"><div class=ttholder>{$qmsicon}<div class=tt>{$qcstatustxt}</div></div></td>
  <td valign=top class="groupingstart">{$val['specimencategory']}</td>
  <td valign=top>{$val['site']}{$subsitedsp}</td>
  <td valign=top>{$val['diagnosis']}{$modds}</td>
  <td valign=top>{$val['metssite']}</td>
  <td valign=top class=groupingstart>{$val['procurementdate']}</td>
  <td valign=top><div class=ttholder>{$val['procuringinstitutioncode']}<div class=tt>{$val['procuringinstitution']}</div></div></td>
  <td valign=top class="groupingstart cntr">{$val['phiage']}</td>
  <td valign=top class="cntr"><div class=ttholder>{$val['phiracecode']}<div class=tt>{$val['phirace']}</div></div></td>
  <td valign=top class="cntr">{$val['phigender']}</td>
  <td valign=top class="cntr">{$val['cxind']}</td>
  <td valign=top class="cntr">{$val['rxind']}</td>
  <td valign=top class="cntr">{$val['pathologyrptind']}</td>
  <td valign=top class="cntr">{$val['informedconsentind']}</td>
  <td valign=top>{$val['preparationmethod']}</td>
  <td valign=top>{$val['preparation']}</td>
  <td valign=top class="cntr">{$val['hourspost']}</td>
  <td valign=top>{$val['metric']} {$val['metricuom']}</td>
  <td valign=top class="cntr">{$val['qty']}</td>
  <td valign=top class=groupingstart>{$dspSD}</td>
  <td valign=top>{$assmnt}</td>
  <td valign=top>{$val['tqrequestnbr']}</td>
</tr>
LINEITEM;
}
$dataTbl .= "</tbody></table><p>&nbsp;<p>&nbsp;<p>&nbsp;<p>";
$context = generateContextMenu('coordinatorResultGrid');
$dataTbl .= "<div id=resultTblContextMenu>{$context}</div>";
} else { 
    $dataTbl = "<h1>NO BIOSAMPLES FOUND MATCHING YOUR CRITERIA.  CLICK THE \"NEW SEARCH\" BUTTON AND TRY TO BROADEN YOUR SEARCH.</h1>";
}

$objid = $dta['DATA']['head']['objid'];
$bywho = $dta['DATA']['head']['bywho'];
$onwhendsp = $dta['DATA']['head']['onwhendsp'];
$srchtrm = json_decode($dta['DATA']['head']['srchterm'], true);

$parameterGrid = <<<TBLPARAGRID

<div id=qParameterHolder><table border=0 id=qryParameterDspTbl >
<tr><td colspan=2 id=title>Query Parameters</td></tr>
<tr><td class=columnQParamName>Query Object: </td>          <td class=ColumnDataObj>{$objid}</td></tr>
<tr><td class=columnQParamName>Created By: </td>            <td class=ColumnDataObj>{$bywho}</td></tr>
<tr><td class=columnQParamName>Create On: </td>             <td class=ColumnDataObj>{$onwhendsp}</td></tr>
<tr><td class=columnQParamName>Records Found: </td>         <td class=ColumnDataObj>{$itemsfound}</td></tr>
<tr><td colspan=2 id=srchTrmParaTitle >SEARCH TERM PARAMETERS</td></tr>
<tr><td class=columnQParamName>Biogroups: </td>             <td class=ColumnDataObj>{$srchtrm['BG']}</td></tr>
<tr><td class=columnQParamName>Procuring Institution: </td> <td class=ColumnDataObj>{$srchtrm['procInst']}</td></tr>
<tr><td class=columnQParamName>Segment Status: </td>        <td class=ColumnDataObj>{$srchtrm['segmentStatus']}</td></tr>
<tr><td class=columnQParamName>QMS Status: </td>            <td class=ColumnDataObj>{$srchtrm['qmsStatus']}</td></tr>
<tr><td class=columnQParamName>Procurement Date Range: </td><td class=ColumnDataObj>{$srchtrm['procDateFrom']} - {$srchtrm['procDateTo']}</td></tr>
<tr><td class=columnQParamName>Shipment Date Range: </td>   <td class=ColumnDataObj>{$srchtrm['shipDateFrom']} - {$srchtrm['shipDateTo']}</td></tr>
<tr><td class=columnQParamName>Investigator Code: </td>     <td class=ColumnDataObj>{$srchtrm['investigatorCode']}</td></tr>
<tr><td class=columnQParamName>Request Code: </td>          <td class=ColumnDataObj>{$srchtrm['requestNbr']}</td></tr>
<tr><td class=columnQParamName>Shipment Document: </td>     <td class=ColumnDataObj>{$srchtrm['shipdocnbr']}</td></tr>
<tr><td class=columnQParamName>Shipment Document Status:</td><td class=ColumnDataObj>{$srchtrm['shipdocstatus']}</td></tr>
<tr><td class=columnQParamName>Diagnosis Designation:</td>  <td class=ColumnDataObj>{$srchtrm['site']}</td></tr>
<tr><td class=columnQParamName>Specimen Category: </td>     <td class=ColumnDataObj>{$srchtrm['specimencategory']}</td></tr>
<tr><td class=columnQParamName>Age (PHI): </td>             <td class=ColumnDataObj>{$srchtrm['phiage']}</td></tr>
<tr><td class=columnQParamName>Race (PHI): </td>            <td class=ColumnDataObj>{$srchtrm['phirace']}</td></tr>
<tr><td class=columnQParamName>Sex (PHI): </td>             <td class=ColumnDataObj>{$srchtrm['phisex']}</td></tr>
<tr><td class=columnQParamName>Procedure Type: </td>        <td class=ColumnDataObj>{$srchtrm['procType']}</td></tr>
<tr><td class=columnQParamName>Preparation Method: </td>    <td class=ColumnDataObj>{$srchtrm['PrepMethod']}</td></tr>
<tr><td class=columnQParamName>Preparation: </td>           <td class=ColumnDataObj>{$srchtrm['preparation']}</td></tr>
</table></div>

TBLPARAGRID;


$dspTbl = <<<DSPTHIS
<table border=0><tr><td>Items found: {$itemsfound} <input type=hidden value="{$rqststr[2]}" id=urlrequestid></td><td align=right>(right-click grid for context menu)</td></tr>
<tr><td colspan=2>{$dataTbl}&nbsp;</td></tr>
</table>
DSPTHIS;

$topBtnBar = generatePageTopBtnBar('coordinatorResultGrid');
$rtnthis = <<<MAINQGRID
{$topBtnBar}
<table border=0 id=mainRsltGridHoldTbl cellspacing=0 cellpadding=0>
<tr><td valign=top>
  <div id=resultHoldingDiv>
     <div id=recordResultDiv>{$dspTbl}</div>
     <div id=dspParameterGrid>{$parameterGrid}</div>
   </div>
</td></tr>                    
</table>
MAINQGRID;

 
        }
    }    
    return $rtnthis;
}

function documentlibrary($rqststr, $whichusr) { 
    $typedsp = "";
    $typevl = "";
    $strm = "";
    $tt = treeTop;
    if ( trim($rqststr[2]) === "docsrch" && trim($rqststr[3]) !== "") { 
        //QUERY TO RUN 
        $dta = json_decode(callrestapi("GET", dataTree . "/docsrch/{$rqststr[3]}", serverIdent, serverpw), true);        
        $strm = $dta['DATA']['head']['srchterm'];        
        $typevl = $dta['DATA']['head']['doctype'];        
        $dspInScreen = 0;
        switch ($typevl) { 
            case 'PATHOLOGYRPT':
                $typedsp = 'Pathology Report Text Search';
                $docobj = "pathrpt";
                $printObj = "pathology-report";
                $dspInScreen = 1;
                break;
            case 'PRBGNBR':
                $typedsp = 'Pathology Report Biogroup Search';
                $docobj = "pathrpt";
                $printObj = "pathology-report";
                $dspInScreen = 1; 
                break;
            case 'CHARTRVW':
                $typedsp = 'Chart Review';
                $docobj = "pxchart";
                $printObj = "chart-review";
                $dspInScreen = 1;
                break; 
            case 'SHIPDOC':
                $typedsp = 'Shipment Document (Ship Doc)';
                $docobj = "shipdoc";
                $printObj = "shipment-manifest";
                $dspInScreen = 0;
                break;                 
        }  
        if ($dspInScreen === 0) { 
          //NO IN SCREEN DISPLAY  
          $dtadsp = "<table width=100% border=0 id=doclibabstbl cellspacing=4>";
          $warning = "";
          if ((int)$dta['ITEMSFOUND'] > 249) { 
            $warning = "(You have reached your limit of return results.  For a more indepth query, see a CHTNEast IT Staff)";
          }        
          $dtadsp .= "<tr><td colspan=2 valign=top id=headerwarning>Documents Found: " . $dta['ITEMSFOUND'] . " {$warning}</td></tr>";
          $dtadsp .= "<tr><th valign=top class=fldLabel>Ref #</th><th valign=top class=fldLabel>Document Abstract</th></tr>";
          foreach($dta['DATA']['records'] as $rs) { 
            $selector = cryptservice($rs['prid'] . "-" . $rs['selector'], "e");
            $dtadsp .= "<tr onclick=\"openOutSidePage('{$tt}/print-obj/{$printObj}/{$selector}');\" class=datalines><td valign=top class=bgnbr>{$rs['dspmark']}</td><td valign=top class=abstracttext>{$rs['abstract']}...</td></tr>";
          }         
          $dtadsp .= "</table>";
        } else { 
          //DISPLAY IN SCREEN  
          $dtadsp = "<table width=100% border=0 id=doclibabstbl cellspacing=4>";
          $warning = "";
          if ((int)$dta['ITEMSFOUND'] > 249) { 
            $warning = "(You have reached your limit of return results.  For a more indepth query, see a CHTNEast IT Staff)";
          }        
          $dtadsp .= "<tr><td colspan=2 valign=top id=headerwarning>Documents Found: " . $dta['ITEMSFOUND'] . " {$warning}</td></tr>";

          $innerListing = "<table border=0 id=docVertList width=100%>";  
          foreach($dta['DATA']['records'] as $rs) {
            switch($docobj) {
              case 'pathrpt':
                $selector = cryptservice("PR-" . $rs['prid'] .  "-" . $rs['selector'], "e"); 
                $oselector = cryptservice( $rs['prid'], "e");  
                $innerListing .= "<tr><td valign=top><table width=100%><tr><td><b>{$rs['dspmark']}</b></td><td onclick=\"getDocumentText('{$selector}');\" class=prntIcon><i class=\"material-icons\">pageview</i></td><td onclick=\"openOutSidePage('{$tt}/print-obj/{$printObj}/{$oselector}');\" class=prntIcon><i class=\"material-icons\">print</i></td></tr><tr><td colspan=3>{$rs['abstract']}...</td></tr></table></td></tr>";
              break;
              case 'pxchart':
                $dspMark = substr("000000{$rs['dspmark']}",-6); 
                $selector = cryptservice("CR-" . $rs['dspmark'], "e");  
                $innerListing .= "<tr><td valign=top><table width=100%><tr><td><b>Chart #{$dspMark}</b></td><td class=prntIcon onclick=\"getDocumentText('{$selector}');\"><i class=\"material-icons\">pageview</i></td></tr><tr><td colspan=3>{$rs['abstract']}...</td></tr></table></td></tr>";
              break;
            }
          }         
          $innerListing .= "</table>";

          $dtadsp .= "<tr><td id=vertHold valign=top><div id=vertdivhold>{$innerListing}</div></td><td valign=top><div id=displayDocText>   </div></td></tr>";
          $dtadsp .= "</table>";            
        }
        
        
    } else { 
        //NO QUERY SPECIFIED
        $dtadsp = "";
    }
    //DOCUMENT TYPES
    $dTypes = "<table class=menuDropTbl >"
            . "<tr><td class=ddMenuItem onclick=\"fillField('fDocType','PATHOLOGYRPT','Pathology Report Text Search','ddSearchTypes');\">Pathology Report Text Search</td></tr>"
            . "<tr><td class=ddMenuItem onclick=\"fillField('fDocType','PRBGNBR','Pathology Report Biogroup Search','ddSearchTypes');\">Pathology Report Biogroup Search</td></tr>"
            . "<tr><td class=ddMenuItem onclick=\"fillField('fDocType','CHARTRVW','Chart Review','ddSearchTypes');\">Chart Review</td></tr>"
            . "<tr><td class=ddMenuItem onclick=\"fillField('fDocType','SHIPDOC','Shipment Document (Ship Doc)','ddSearchTypes');\">Shipment Document (Ship Doc)</td></tr>"            
            . "</table>"; 
$rtnthis = <<<PAGEHERE
<table width=100% border=0 cellspacing=2 cellpadding=0 id=docLibHoldTbl>
    <tr><td colspan=4 class=pageTitle>Document Library</td></tr>
    <tr><td class=fldLabel>Search Term ('like' search)</td><td class=fldLabel>Document Type</td><td></td><td></td></tr>
    <tr><td style="width: 50vw;"><input type=text id=fSrchTerm style="width: 50vw;" value="{$strm}"></td>
            <td style="width: 15vw;">
                  <div class=menuHolderDiv>
                  <div class=valueHolder><input type=hidden id=fDocTypeValue value="{$typevl}"><input type=text READONLY id=fDocType style="width: 15vw;" value="{$typedsp}"></div>
                  <div class=valueDropDown style="min-width: 15vw;" id=ddSearchTypes>{$dTypes}</div>
                  </div></td>
            <td><table class=tblBtn id=btnSearchDocuments><tr><td>Search</td></tr></table></td>
            <td></td>
    </tr>   
    <tr>
         <td colspan=4>
         <!-- RESULTS SECTION //-->
             {$dtadsp}
         </td>
    </tr>    
</table>      
PAGEHERE;
return $rtnthis;  
}
 
function root($rqstStr, $whichUsr) { 
    //$whichUsr THIS IS THE USER ARRAY {"statusCode":200,"loggedsession":"i46shslvmj1p672lskqs7anmu1","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":20,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"googleiconcode":"lock_open","menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"googleiconcode":"account_balance","menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""}]],["472","REPORTS","",[{"googleiconcode":"account_balance","menuvalue":"All Reports","pagesource":"all-reports","additionalcode":""}]],["473","UTILITIES","",[{"googleiconcode":"account_balance","menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474",null,null,[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]} 
  
  $d = date('M d, Y H:i'); 
  $rtnthis = <<<PAGEHERE

<table border=0 width=100% id=rootTable>
  <tr>
          <td>Welcome, {$whichUsr->userid} </td>
          <td align=right>Now: {$d}</td>
  </tr>
  <td colspan=2>          
      <!-- Display Area //-->      
  </td>        
</table>

PAGEHERE;
return $rtnthis;
}

function hprreview($rqststr, $whichusr) { 

if (trim($rqststr[2]) === "") { 

//HPR Query Grid and Scanner
$grid = buildHPRGrid(); 
    $pg = <<<PAGECONTENT
{$grid}
PAGECONTENT;

} else { 

if (trim($rqststr[2]) !== 'pastreview') {

    //GET SLIDE LIST AND METRICS
   $pgContent = buildHPRBenchTop($rqststr);
   //$topBtnBar = generatePageTopBtnBar('hprreviewactions');  //THE ACTION BUTTONS HAVE BEEN MOVED TO THE SCREEN REAL ESTATE
   $pg = <<<PAGECONTENT
   {$topBtnBar} 
{$pgContent}
PAGECONTENT;

} else { 

    $pgContent = "PAST HPReview";
   $topBtnBar = generatePageTopBtnBar('hprpast');
   $pg = <<<PAGECONTENT
   {$topBtnBar} 
{$pgContent}
PAGECONTENT;

}

}

return $pg;
}

function login($rqststr) {
//THIS SETS THE COOKIE
//$number_of_days = 30 ;
//$date_of_expiry = time() + 60 * 60 * 24 * $number_of_days ; 
//setcookie("ssv7_dualcode","857885",$date_of_expiry,"/");
//AUTHENTICATION WILL IGNORE COOKIES
//if(!isset($_COOKIE['ssv7_dualcode'])) {
    $addLine = "<tr><td class=label>Dual-Authentication Code <span class=pseudoLink id=btnSndAuthCode>(Send Authentication Code)</span></td></tr><tr><td><input type=text id=ssDualCode></td></tr>";
//} else {             
//}

$controlBtn = "<table><tr><td class=adminBtn id=btnLoginCtl>Login</td></tr></table>";    
    
$rtnThis = <<<PAGECONTENT

<div id=loginHolder>        
<div id=loginDialogHead>ScienceServer2018 &amp; Investigator Services Login </div>
<div id=loginGrid>
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr><td class=label>Email (as User-Id)</td></tr>
<tr><td><input type=text id=ssUser></td></tr>    
<tr><td class=label>Password</td></tr>
<tr><td><input type=password id=ssPswd></td></tr>   
{$addLine}
<tr><td align=right>{$controlBtn}</td></tr>
<tr><td><center> <span class=pseudoLink>Forgot Password</span> </td></tr>    
</table>        
</div>
<div id=loginFooter><b>Disclaimer</b>: This is the Specimen Management Data Application (SMDA) for the Eastern Division of the Cooperative Human Tissue Network.  It provides access to collection data by employees, remote site contracts and investigators of the CHTNEastern Division.   You must have a valid username and password to access this system.  If you need credentials for this application, please contact a CHTNED Manager.  Unauthorized activity is tracked and reported! To contact the offices of CHTNED, please call (215) 662-4570 or email chtnmail /at/ uphs.upenn.edu</div>    
</div>

PAGECONTENT;
return $rtnThis;        
}

function generateHeader($mobileInd, $whichpage) {
  $tt = treeTop;      
  $at = genAppFiles;
  $jsscript =  base64file( "{$at}/extlibs/Barrett.js" , "", "js", true);
  $jsscript .= "\n" . base64file( "{$at}/extlibs/BigInt.js" , "", "js");
  $jsscript .= "\n" . base64file( "{$at}/extlibs/RSA.js" , "", "js");
  //$jsscript .= "\n" . base64file( "{$at}/publicobj/extjslib/tea.js" , "", "js");
  $rtnThis = <<<STANDARDHEAD
<!-- <META http-equiv="refresh" content="0;URL={$tt}"> //-->
<!-- SCIENCESERVER IDENTIFICATION: {$tt}/{$whichpage} //-->

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="refresh" content="28800">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script lang=javascript>
var scienceserveridentification = '{$tt}/{$whichpage}';
</script>
{$jsscript}
STANDARDHEAD;
  return $rtnThis;
}    

}

function bldHPRWorkBenchSide($SGObj, $allSGObj, $pastHPRObj, $pbiosample) {  

  $tt = treeTop;
  $sgDta = $SGObj['DATA'][0];
  $allSgDta = $allSGObj['DATA'];
  $pHPRDta = $pastHPRObj['DATA'];

  $segLabel = strtoupper(preg_replace('/\_/', '', $sgDta['bgs']));
  $dspSite = strtoupper($sgDta['site']);
  $dspSite .= ( trim($sgDta['subsite']) === "") ? "" : (" / " . strtoupper($sgDta['subsite']));  
  $dspSite .= ( trim($sgDta['specimencategory']) === "") ? "" : (" (" . strtoupper($sgDta['specimencategory']) . ")");
  $dspSite = trim($dspSite);
  $dx = strtoupper($sgDta['dx']);
  $dx .= (trim($sgDta['dxmod']) === "") ? "" : (" / " . strtoupper($sgDta['dxmod']));
  $dx = trim($dx);
  $mets = strtoupper(trim($sgDta['metssite']));
  $metsdx = strtoupper(trim($sgDta['metssitedx']));
  $sysdx = strtoupper(trim($sgDta['systemicdx']));
  $cx = strtoupper(substr(trim($sgDta['cx']),0,1));
  $rx = strtoupper(substr(trim($sgDta['rx']),0,1));
  $hprind = ((int)$sgDta['hprind'] === 1) ? 'Yes' : 'No';
  $qcind = ((int)$sgDta['qcind'] === 1) ? 'Yes' : 'No';
  $pathrpt = strtoupper(trim($sgDta['pthrpt']));
  $infcon = strtoupper(trim($sgDta['infc']));
  $uninv = strtoupper(trim($sgDta['uninvolvedind']));
  $phirace = substr(strtoupper(trim($sgDta['phirace'])),0,3);
  $phisex = substr(strtoupper(trim($sgDta['phisex'])),0,1);
  $phiage = $sgDta['phiage']; 
  $phiage .= (trim($sgDta['phiageuom']) === "") ? "" : "" ; //. strtoupper(trim($sgDta['phiageuom']))
  $procedure = trim($sgDta['proceduretype']);
  $procedure .= (trim($sgDta['procedureinstitution']) === "") ? "" : "-" . trim($sgDta['procedureinstitution']);
  $procedureLine2 = (trim($sgDta['proceduredate']) === "") ? "" : trim($sgDta['proceduredate']);
  $procedureLine2 .= (trim($sgDta['proctechnician']) === "") ? "" :  " (" . trim($sgDta['proctechnician']) . ")"; 
  $procedure .= (trim($procedureLine2) !== "" ) ? "<br>{$procedureLine2}" : "";
  $hprquestion = preg_replace( '/-{2,}/','',preg_replace('/SS[Vv]\d/','', trim($sgDta['hprquestion'])));
  $bscomment = $hrcmt = preg_replace( '/-{2,}/','',preg_replace('/SS[Vv]\d/','',trim($sgDta['biosamplecomment'])));
  $prTxt = (trim($sgDta['pathologyreporttext']) === "") ? "NO PATHOLOGY REPORT FOUND" : trim($sgDta['pathologyreporttext']);
  $selector = cryptservice($sgDta['prprid'] . "-" . $sgDta['prrecordselector'], "e");
  $prTxtBtns = (trim($sgDta['pathologyreporttext']) === "") ? "" : "<td onclick=\"openOutSidePage('{$tt}/print-obj/pathology-report/{$selector}');\" align=right><i class=\"material-icons prntbtn\">print</i></td>";

  $revGridConfirm   = buildHPRConfirmGrid($pbiosample,$segLabel,$sgDta);
  $revGridAdd       = buildHPRAddGrid($pbiosample,$segLabel);
  $revGridDeny      = buildHPRDenyGrid($pbiosample,$segLabel);
  $revGridIncon     = buildHPRInconGrid($pbiosample,$segLabel);
  $revGridUnuse     = buildHPRUnuseGrid($pbiosample,$segLabel);

  if ((int)$allSGObj['ITEMSFOUND'] > 0) {
    $segPartsDsp = "<table border=0 cellspacing=0 cellpadding=0 width=100% id=constituentTbl><tr><td class=littleFieldLabelEnd>Segment</td><td class=littleFieldLabelEnd>Status</td><td class=littleFieldLabelEnd>Preparation</td><td class=littleFieldLabelEnd>Metric</td><td class=littleFieldLabelEnd>Assignment</td></tr>";
    foreach ($allSgDta as $rcd) {
      $partSegLabel = strtoupper(preg_replace('/\_/', '', $rcd['bgs']));
      $conBGDsp = substr($partSegLabel,0,5); 
      $prp = trim(strtoupper($rcd['prepmethod']));
      $met = (trim($rcd['metricdsp']) === "") ?  "" : strtoupper(trim($rcd['metricdsp']));
      $ass = (trim($rcd['invest']) === "") ? "" : strtoupper(trim($rcd['invest']));
      $segPartsDsp .= "<tr><td class=conDataCell>{$partSegLabel}</td><td class=conDataCell>{$rcd['segstatus']}</td><td class=conDataCell>{$prp}</td><td class=conDataCell>{$met}</td><td class=conDataCell>{$ass}</td></tr>";
    }
    $segPartsDsp .= "</table>";
  } else { 
    $segPartDsp = "NO CONSTITUENT SEGMENTS FOUND";
  }

  $hprDsp = "<table border=0 cellspacing=0 cellpadding=0 width=100% id=constituentTbl><tr><td class=littleFieldLabelEnd>Slide</td><td class=littleFieldLabelEnd>Reviewer</td><td class=littleFieldLabelEnd>Read On</td><td class=littleFieldLabelEnd>Decision</td><td class=littleFieldLabelEnd>Diagnosis Designation</td><td class=littleFieldLabelEnd>Reviewer Comments</td></tr>";
    $pastSlideCntr = 0;  
    foreach ($pHPRDta as $rcd) {
      if (trim($rcd['slide']) !== "") {  
      $hprSegLabel = strtoupper(preg_replace('/\_/', '', $rcd['slide']));
      $hprDX = $rcd['desig'];
      $hprDX .= (trim($rcd['speccat']) === "") ? "" : " [" . trim($rcd['speccat']) . "]";
      $hprDX = trim($hprDX);
      $hprCmt = trim($rcd['reviewercomments']);
      $hprDsp .= "<tr class=hoverRow onclick=\"navigateSite('hpr-review/past-review/{$rcd['biohprid']}');\"><td class=conDataCell>{$hprSegLabel}</td><td class=conDataCell>{$rcd['reviewer']}</td><td class=conDataCell>{$rcd['reviewedon']}</td><td class=conDataCell>{$rcd['decision']}</td><td class=conDataCell>{$hprDX}&nbsp;</td><td class=conDataCell>{$hprCmt}&nbsp;</td></tr>";
      $pastSlideCntr++;
      } 
    }
    $hprDsp .= "</table>";

    if ($pastSlideCntr < 1) { 
      $hprDsp = "NO PAST HPR PERFORMED";
    }

    $pg = <<<PAGECONTENT
<table border=0 cellspacing=0 cellpadding=0 id=workBenchHolding>
    <tr><td valign=top id=workBenchPrelimInfoHold>

            <div id=divWorkBenchPrelimInfo>
            <table border=0 cellspacing=0 cellpadding=0 width=100%>
            <tr><td class=workbenchheader>SLIDE: {$segLabel}</td></tr>
            <tr><td>
               <!-- TECHNICIAN INFO //--> 
                <table border=0 width=100% cellpadding=0 cellspacing=0>
                 <tr><td colspan=3 valign=top class=littleFieldLabel width=50%>Site / Subsite (Specimen Category)</td><td colspan=3 valign=top class=littleFieldLabelEnd width=50%>Diagnosis / Modifier</td></tr>
                 <tr><td colspan=3 valign=top class=dataFieldDsp>{$dspSite}&nbsp;</td><td colspan=3 valign=top class=dataFieldDspEnd>{$dx}&nbsp;</td></tr> 
                 <tr><td colspan=3 valign=top class=littleFieldLabel width=50%>METS Site</td><td colspan=3 valign=top class=littleFieldLabelEnd width=50%>Mets Site DX</tr>   
                 <tr><td colspan=3 valign=top class=dataFieldDsp>{$mets}&nbsp;</td><td colspan=3 valign=top class=dataFieldDspEnd>{$metsdx}&nbsp;</td></tr>  
                 <tr><td colspan=6 valign=top class=littleFieldLabelEnd>Systemic Diagnosis</td></tr>
                 <tr><td colspan=6 valign=top class=dataFieldDspEnd>{$sysdx}&nbsp;</td></tr>          
                 <tr><td valign=top class=littleFieldLabel width=17%>A/R/S</td><td valign=top class=littleFieldLabel width=17%>CX/RX</td><td valign=top class=littleFieldLabel width=17%>HPR/QC</td><td valign=top class=littleFieldLabel width=17%>PR/IC</td><td valign=top class=littleFieldLabel width=17%>Procedure</td><td valign=top class=littleFieldLabelEnd width=17%>Uninvolved</td></tr>
                 <tr> <td valign=top class=dataFieldDsp>{$phiage} / {$phirace} / {$phisex}&nbsp;</td><td valign=top class=dataFieldDsp>{$cx}/{$rx}&nbsp;</td><td valign=top class=dataFieldDsp>{$hprind}/{$qcind}&nbsp;</td><td valign=top class=dataFieldDsp>{$pathrpt}/{$infcon}&nbsp;</td><td valign=top class=dataFieldDsp>{$procedure}&nbsp;</td><td valign=top class=dataFieldDspEnd>{$uninv}</td></tr>
                 <tr><td colspan=6 valign=top class=littleFieldLabelEnd>Technician Question For HPR/QMS Review</td></tr>
                 <tr><td colspan=6 valign=top class=dataFieldDspEnd>{$hprquestion}&nbsp;</td></tr>
                 <tr><td colspan=6 valign=top class=littleFieldLabelEnd>Biosample Comment</td></tr>
                 <tr><td colspan=6 valign=top class=dataFieldDspEnd style="border: none;">{$bscomment}&nbsp;</td></tr>
                 </table>
                 <!-- END TECHNICIAN TABLE //--> 
            </td></tr>
            </table>
            </div>   

            <div id=allSegmentDsp>
            <table border=0 cellspacing=0 cellpadding=0 width=100%>
            <tr><td class=workbenchheader>CONSTITUENT SEGMENTS FOR {$conBGDsp}</td></tr>
            <tr><td>
            <!-- CONSTITUENT SEGMENTS //-->
            <div id=allSegHolder>
            {$segPartsDsp} 
            </div>
            </td></tr>
            </table> 
            </div>

            <div id=pastHPRDsp>
            <table border=0 cellspacing=0 cellpadding=0 width=100%>
            <tr><td class=workbenchheader>PAST HPR PERFORMED ON BIOGROUP {$conBGDsp}</td></tr>
            <tr><td>
            <!-- PAST HPR SEGMENTS //-->
            <div id=allSegHolder>
            {$hprDsp} 
            </div>
            </td></tr>
            </table> 
            </div>

            </td>

            <td valign=top id=buttonStripHolder>
  
               <table>
                 <tr><td><div class=sideActionBtn id=btnNewHPRReview onclick="navigateSite('hpr-review');"><div class=iconGraphic><i class="material-icons">panorama_fish_eye</i></div><div class=btnExplainer>New HPR Search</div></div></td></tr>
                 <tr><td><div class=sideActionBtn id=btnPathReportDsp onclick="changeReviewDisplay('divWorkBenchPathRptDsp');"><div class=iconGraphic><i class="material-icons">stars</i></div><div class=btnExplainer>View the Pathology Report</div></div></td></tr>
                 <tr><td>&nbsp;</td></tr>
                 <tr><td><div class=sideActionBtn id=btnConfirmHPR onclick="changeReviewDisplay('reviewersWorkBenchConfirm');"><div class=iconGraphic><i class="material-icons">check_circle_outline</i></div><div class=btnExplainer>Confirm Designation</div></div></td></tr>
                 <tr><td><div class=sideActionBtn id=btnAddHPR onclick="changeReviewDisplay('reviewersWorkBenchAdd');"><div class=iconGraphic><i class="material-icons">add_circle_outline</i></div><div class=btnExplainer>Add to Designation</div></div></td></tr>
                 <tr><td><div class=sideActionBtn id=btnDenyHPR onclick="changeReviewDisplay('reviewersWorkBenchDeny');"><div class=iconGraphic><i class="material-icons">error_outline</i></div><div class=btnExplainer>Deny Designation</div></div></td></tr>
                 <tr><td><div class=sideActionBtn id=btnUnusableHPR onclick="changeReviewDisplay('reviewersWorkBenchUnuse');"><div class=iconGraphic><i class="material-icons">highlight_off</i></div><div class=btnExplainer>Mark Biosample Unusable</div></div></td></tr>
                 <tr><td><div class=sideActionBtn id=btnInconHPR onclick="changeReviewDisplay('reviewersWorkBenchIncon');"><div class=iconGraphic><i class="material-icons">help_outline</i></div><div class=btnExplainer>Inconclusive Dsignation</div></div></td></tr>
               </table> 


            </td>
            <td valign=top>

            <div id=workBenchDisplayHolder>

              <div id=divWorkBenchPathRptDsp>
                <!-- PATH REPORT DISPLAY //--> 
                <table border=0 cellspacing=0 cellpadding=0 width=100%>
                <tr><td class=workbenchheader><table width=100% cellpadding=0 cellspacing=0><tr><td>Pathology Report</td>{$prTxtBtns}</tr></table> </td></tr>
                <tr><td>
                    <div id=hprPathRptTextDsp>
                      {$prTxt}
                    </div>
                </td></tr>
                </table>
                <!-- END PATH REPORT DISPLAY //-->
              </div>

              <div id=reviewersWorkBenchConfirm>{$revGridConfirm}</div>
              <div id=reviewersWorkBenchAdd>{$revGridAdd}</div>
              <div id=reviewersWorkBenchDeny>{$revGridDeny}</div>
              <div id=reviewersWorkBenchIncon>{$revGridIncon}</div>
              <div id=reviewersWorkBenchUnuse>{$revGridUnuse}</div>

            </div>
            </td>
    </tr>
 </table>
PAGECONTENT;
   
    return $pg;          
}

function buildHPRConfirmGrid($biogroupnbr, $slidenbr, $designation) {
  $si = serverIdent;
  $sp = serverpw;
  $site = strtoupper(trim($designation['site']));
  $subsite = strtoupper(trim($designation['subsite']));
  $dx = strtoupper(trim($designation['dx']));
  $dxm = strtoupper(trim($designation['dxmod'])); 
  $spc = strtoupper(trim($designation['specimencategory'])); 
  $mets = strtoupper(trim($designation['metssite'])); 
  $metsdx = strtoupper(trim($designation['metssitedx'])); 

  $techacc = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-technician-accuracy",$si,$sp),true);
  $tacc = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('hprFldTechAcc','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($techacc['DATA'] as $procval) { 
    $tacc .= "<tr><td onclick=\"fillField('hprFldTechAcc','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
  }
  $tacc .= "</table>";
  
  $moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",$si,$sp),true);
  $molemnu = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"triggerMolecularFill(0,'','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($moletest['DATA'] as $moleval) { 
    $molemnu .= "<tr><td onclick=\"triggerMolecularFill({$moleval['menuid']},'{$moleval['menuvalue']}','{$moleval['dspvalue']}');\" class=ddMenuItem>{$moleval['dspvalue']}</td></tr>";
  }
  $molemnu .= "</table>";
  


$pg = <<<CONFIRMFRM
<form id=frmConfirmation>
<input type=hidden id=fldBG value={$biogroupnbr}>
<input type=hidden id=fldSld value={$slidenbr}>
<div id=divHPRConfirm>

  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderconfirm><table width=100% cellpadding=0 cellspacing=0><tr><td>Confirm Diagnosis Designation for Biogroup {$biogroupnbr}</td></tr></table> </td></tr>
  </table>

  <table border=0 width=100% cellspacing=0 cellpadding=0>
    <tr><td class=littleFieldLabelWork>Specimen Category</td><td class=littleFieldLabelWork>Site</td><td class=littleFieldLabelWork>Sub-Site</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldSpecCatConfirm value="{$spc}" READONLY></td><td class=fieldHolder><input type=text id=hprFldSiteConfirm value="{$site}" READONLY></td><td class=fieldHolder><input type=text id=hprFldSubSiteConfirm value="{$subsite}" READONLY></td></tr>
  </table>

  <table border=0 width=100% cellspacing=0 cellpadding=0>
    <tr><td class=littleFieldLabelWork>Diagnosis</td><td class=littleFieldLabelWork>Diagnosis Modifier</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldDiagnosisConfirm value="{$dx}" READONLY></td><td class=fieldHolder><input type=text id=hprFldDXModifierConfirm value="{$dxm}" READONLY></td></tr>
  </table>
 
  <table border=0 width=100% cellspacing=0 cellpadding=0>
    <tr><td class=littleFieldLabelWork>Metastatic Site</td><td class=littleFieldLabelWork>Metastatic Diagnosis</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldMetsSiteConfirm value="{$mets}" READONLY></td><td class=fieldHolder><input type=text id=hprFldMetsDXConfirm value="{$metsdx}" READONLY></td></tr>
  </table>

  <table border=1 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderconfirm><table width=100% cellpadding=0 cellspacing=0><tr><td><center>Biosample Configuration Annotation</td></tr></table> </td></tr>
  </table>

 <table border=0 width=100% cellspacing=0 cellpadding=0> 
 <tr>
 <td valign=top width=33%>
  <table border=0 cellspacing=0 cellpadding=0>
    <tr><td class=littleFieldLabelWork>Tumor</td><td class=littleFieldLabelWork>Tumor Cellularity</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldPRCTumorConfirm value=""></td>
        <td class=fieldHolder><input type=text id=hprFldPRCCellConfirm value=""></td></tr>
    <tr><td class=littleFieldLabelWork>Tumor Necrosis</td><td class=littleFieldLabelWork>Acellular Mucin</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldPRCNecroConfirm value=""></td>
        <td class=fieldHolder><input type=text id=hprFldPRCACellConfirm value=""></td></tr> 
    <tr><td class=littleFieldLabelWork>Neoplastic Stroma</td><td class=littleFieldLabelWork>Non Neoplastic</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldPRCNeoPlasticConfirm value=""></td>
        <td class=fieldHolder><input type=text id=hprFldPRCNonNeoConfirm value=""></td></tr> 
    <tr><td class=littleFieldLabelWork>Epithelial Cell</td><td class=littleFieldLabelWork>Inflammation</td></tr>
    <tr><td class=fieldHolder><input type=text id=hprFldPRCEpiCellConfirm value=""></td>
        <td class=fieldHolder><input type=text id=hprFldPRCInflamConfirm value=""></td></tr> 
  </table>
  </td>
  <td colspan=2 valign=top width=66%> 
    
   <table border=0 width=100% cellpadding=0 cellspacing=0>
       <tr><td class=littleFieldLabelWork colspan=2>Indicated Immuno/Molecular Test Results</td><td rowspan=4><table class=tblBtn onclick="manageMoleTest(1);"><tr><td><i class="material-icons">playlist_add</i></td></tr></table></td></tr>
       <tr><td class=fieldHolder  valign=top colspan=2><div class=menuHolderDiv><input type=hidden id=hprFldMoleTestValue><input type=text id=hprFldMoleTest READONLY><div class=valueDropDown id=moleTestDropDown>{$molemnu}</div></div></td></tr>
       <!-- TODO:  BIOSAMPLE ANALYTIC TESTING DATE (BAT) //-->
       <tr><td class=littleFieldLabelWork>Result Index</td><td class=littleFieldLabelWork>Scale Degree</td></tr>
       <tr><td class=fieldHolder  valign=top><div class=menuHolderDiv><input type=hidden id=hprFldMoleResultValue><input type=text id=hprFldMoleResult READONLY><div class=valueDropDown id=moleResultDropDown> </div></div></td><td class=fieldHolder  valign=top><input type=text id=hprFldMoleScale></td></tr>
       <tr><td colspan=3 class=fieldHolder  valign=top>
           <input type=hidden id=molecularTestJsonHolderConfirm>
           <div id=dspDefinedMolecularTestsConfirm>
           </div>
    </table>
   </td> </tr>
       
  <tr>
      <tr><td class=littleFieldLabelWork>Biosample Comment</td><td class=littleFieldLabelWork>Rare Reason</td><td class=littleFieldLabelWork>Technician Accuracy</td></tr>
      <tr><td class=fieldHolder  valign=top><TEXTAREA id=hprFldBSCommentsConfirm></textarea></td>
      <td class=fieldHolder  valign=top><TEXTAREA id=hprFldRareCommentsConfirm></textarea></td>
<td class=fieldHolder  valign=top><div class=menuHolderDiv><input type=hidden id=hprFldTechAccValue><input type=text id=hprFldTechAcc READONLY><div class=valueDropDown id=TechAccDropDown>{$tacc}</div></div></td></tr>
</table>



</div>
</form>
CONFIRMFRM;

  return $pg;
}

function buildHPRAddGrid($biogroupnbr, $slidenbr) {

$pg = <<<CONFIRMFRM
<form id=frmAdditional>
<input type=hidden id=fldBG value={$biogroupnbr}>
<input type=hidden id=fldSld value={$slidenbr}>
<div id=divHPRAddition>
  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderadd><table width=100% cellpadding=0 cellspacing=0><tr><td>Diagnosis Designation Additions for Biogroup {$biogroupnbr}</td></tr></table> </td></tr>
  </table>
</div>
</form>
CONFIRMFRM;
  return $pg;
}

function buildHPRDenyGrid($biogroupnbr, $slidenbr) {
$pg = <<<CONFIRMFRM
<form id=frmDenial>
<input type=hidden id=fldBG value={$biogroupnbr}>
<input type=hidden id=fldSld value={$slidenbr}>
<div id=divHPRDeny>
  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderdeny><table width=100% cellpadding=0 cellspacing=0><tr><td>Denied Diagnosis Designation for Biogroup {$biogroupnbr}</td></tr></table> </td></tr>
  </table>
</div>
</form>
CONFIRMFRM;
  return $pg;
}

function buildHPRInconGrid($biogroupnbr, $slidenbr) {
$pg = <<<CONFIRMFRM
<form id=frmIncon>
<input type=hidden id=fldBG value={$biogroupnbr}>
<input type=hidden id=fldSld value={$slidenbr}>
<div id=divHPRIncon>
  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderincon><table width=100% cellpadding=0 cellspacing=0><tr><td>Diagnosis Designation Inconclusive for Biogroup {$biogroupnbr}</td></tr></table> </td></tr>
  </table>
</div>
</form>
CONFIRMFRM;
  return $pg;
}

function buildHPRUnuseGrid($biogroupnbr, $slidenbr) {
$pg = <<<CONFIRMFRM
<form id=frmUnused>
<input type=hidden id=fldBG value={$biogroupnbr}>
<input type=hidden id=fldSld value={$slidenbr}>
<div id=divHPRUnuse>
  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td class=workbenchheaderunuse><table width=100% cellpadding=0 cellspacing=0><tr><td>Unusable Biosample {$biogroupnbr}</td></tr></table> </td></tr>
  </table>
</div>
</form>
CONFIRMFRM;
  return $pg;
}

function buildHPRGrid() { 
$grid = <<<HPRGRID
<div id=hprInnerScan>
<table>
<tr><th class=fldLabel>Scan or Type Tray # / Biogroup / Segment label</th></tr>
<tr><td><input type=text id=fldHPRScan></td></tr>
<tr><td align=right>
  <table class=tblBtn id=btnHPRScanSearch style="width: 6vw;"><tr><td><center>Go!</td></tr></table>
</td></tr>
</table>
</div>
HPRGRID;
return $grid;
}

function buildHPRBenchTop($whichQryId) { 

  $dta = json_decode(callrestapi("GET", dataTree . "/hpr-request-code/{$whichQryId[2]}", serverIdent, serverpw), true);
  if ((int)$dta['ITEMSFOUND'] === 1) { 
    //GET WORKBENCH
    
    $pdta = json_encode(array('srchTrm' => $dta['DATA'][0]));
    $sidedta = json_decode(callrestapi("POST", dataTree . "/data-doers/hpr-workbench-side-panel",serverIdent, serverpw, $pdta), true);          
    if ((int)$sidedta['ITEMSFOUND'] < 1) { 
        //NO SLIDES FOUND
        $pg = "<div id=hprwbHeadErrorHolder><H1>{$sidedta['MESSAGE'][0]} - See a CHTNEastern Staff if you feel this is incorrect.</div>";
    } else {
        $sidePanelTbl = "<table border=0 cellspacing=0 cellpadding=0 id=sidePanelSlideListTbl>"; 
        $sidePanelTbl .= "<tr><th class=workbenchheader>{$sidedta['MESSAGE'][0]}</th></tr>";
        foreach ($sidedta['DATA'] as $skey => $sval) {
           $freshDsp = ((int)$sval['freshcount'] > 0) ? "[CONTAINS DIRECT SHIPMENT]" : "";
           $cntr = ($skey + 1); 
           $sidePanelTbl .= <<<SLIDELINE
<tr class=rowBacker><td onclick="requestSegmentInfo('{$sval['segmentid']}',{$sval['pbiosample']});" class=rowHolder>
    <table border=0 class=slide>
      <tr>
        <td rowspan=3 class=slidecountr>{$cntr}</td>
        <td colspan=3 class=slidenbr valign=top>{$sval['bgs']} / {$sval['preparation']}</td>
      </tr>
      <tr><td colspan=3 class=slidedesignation valign=top>{$sval['designation']}</td></tr>
      <tr><td valign=top class=slidedate><b>Procurement</b>: {$sval['procurementdate']}</td><td valign=top class=slidetech><b>Tech</b>: {$sval['procuringtech']}</td></tr>
      <tr><td valign=top colspan=3 class=slidefreshdsp>{$freshDsp}</td></tr>
    </table>
</td></tr>
SLIDELINE;
        }
        $sidePanelTbl .= "<tr><td class=slidesfound><b>Slides Found</b>: {$cntr}</td></tr>";
        $sidePanelTbl .= "</table>";


        $pg = <<<PGCONTNT
<table border=0 id=masterWorkBenchTbl>
  <tr>
    <td valign=top id=sidePanelTD><div id=sidePanel>{$sidePanelTbl}</div></td>
    <td valign=top id=workBenchTD><div id=workBench>&nbsp;</div></td>
  </tr>
</table>
PGCONTNT;
    } 
    
$grid = <<<HPRGRID
  {$pg}
HPRGRID;
} else { 
  //DISPLAY ERROR
$grid = <<<HPRGRID
<div id=hprwbHeadErrorHolder><H1>No Workbench was found for the given URL Criteria Code - See a CHTNEastern Staff if you feel this is incorrect.</div>
HPRGRID;
}

  return $grid;
}

function buildBSGrid() { 
  $si = serverIdent;
  $sp = serverpw;
  //DROP MENU BUILDER ********************************************************************************* //
  $procinstarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/allinstitutions",$si,$sp),true);
  $proc = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryProcInst','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($procinstarr['DATA'] as $procval) { 
    $proc .= "<tr><td onclick=\"fillField('qryProcInst','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
  }
  $proc .= "</table>";
  
  $racearr = json_decode(callrestapi("GET", dataTree . "/globalmenu/pxirace",$si,$sp),true);
  $race = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('phiRace','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($racearr['DATA'] as $raceval) { 
    $race .= "<tr><td onclick=\"fillField('phiRace','{$raceval['lookupvalue']}','{$raceval['menuvalue']}');\" class=ddMenuItem>{$raceval['menuvalue']}</td></tr>";
  }
  $race .= "</table>";

  $sexarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/pxisex",$si,$sp),true);
  $sex = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('phiSex','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($sexarr['DATA'] as $sexval) { 
    $sex .= "<tr><td onclick=\"fillField('phiSex','{$sexval['lookupvalue']}','{$sexval['menuvalue']}');\" class=ddMenuItem>{$sexval['menuvalue']}</td></tr>";
  }
  $sex .= "</table>";

  $ptypearr = json_decode(callrestapi("GET", dataTree . "/globalmenu/allproctypes",$si,$sp),true);
  $ptype = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryProcType','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($ptypearr['DATA'] as $ptypeval) { 
    $ptype .= "<tr><td onclick=\"fillField('qryProcType','{$ptypeval['lookupvalue']}','{$ptypeval['menuvalue']}');\" class=ddMenuItem>{$ptypeval['menuvalue']}</td></tr>";
  }
  $ptype .= "</table>";

  $segstatusarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/menusegmentstatus",$si,$sp),true);
  $seg = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qrySegStatus','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($segstatusarr['DATA'] as $segval) { 
    $seg .= "<tr><td onclick=\"fillField('qrySegStatus','{$segval['lookupvalue']}','{$segval['menuvalue']}');\" class=ddMenuItem>{$segval['menuvalue']}</td></tr>";
  }
  $seg .= "</table>";

  $hprstatusarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/qmsstatus",$si,$sp),true);
  $hpr = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryHPRStatus','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($hprstatusarr['DATA'] as $hprval) { 
    $hpr .= "<tr><td onclick=\"fillField('qryHPRStatus','{$hprval['lookupvalue']}','{$hprval['menuvalue']}');\" class=ddMenuItem>{$hprval['menuvalue']}</td></tr>";
  }
  $hpr .= "</table>";

  $preparr = json_decode(callrestapi("GET", dataTree . "/globalmenu/allpreparationmethods",$si,$sp),true);
  $prp = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryPreparationMethod','','');updatePrepmenu('');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($preparr['DATA'] as $prpval) {
    $prp .= "<tr><td onclick=\"fillField('qryPreparationMethod','{$prpval['lookupvalue']}','{$prpval['menuvalue']}');updatePrepmenu('{$prpval['lookupvalue']}');\" class=ddMenuItem>{$prpval['menuvalue']}</td></tr>";
  }
  $prp .= "</table>";

  $spcarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/specimencategorylive",$si,$sp),true);
  $spc = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryDXDSpecimen','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($spcarr['DATA'] as $spcval) {
    $spc .= "<tr><td onclick=\"fillField('qryDXDSpecimen','{$spcval['codevalue']}','{$spcval['menuvalue']}');\" class=ddMenuItem>{$spcval['menuvalue']}</td></tr>";
  }
  $spc .= "</table>";

$shpstatusarr = json_decode( callrestapi("GET", dataTree . "/globalmenu/allshipdocstatus",$si,$sp) , true);
$shps = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryShpStatus','','');\" class=ddMenuClearOption>[clear]</td></tr>";
foreach ($shpstatusarr['DATA'] as $shpval) { 
  $shps .= "<tr><td onclick=\"fillField('qryShpStatus','{$shpval['lookupvalue']}','{$shpval['menuvalue']}');\" class=ddMenuItem>{$shpval['menuvalue']}</td></tr>";
}
$shps .= "</table>";
$shpsts = "<div class=menuHolderDiv><input type=hidden id=qryShpStatusValue><input type=text id=qryShpStatus class=\"inputFld\" style=\"width: 15vw;\" READONLY><div class=valueDropDown style=\"width: 15vw;\">{$shps}</div></div>";

$fsCalendar = buildcalendar('shipBSQFrom'); 
$shpFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=shpQryFromDateValue><input type=text READONLY id=shpQryFromDate class="inputFld" style="width: 17vw;"></div>
  <div class=valueDropDown style="min-width: 17vw;" id=fcal><div id=shpfCalendar>{$fsCalendar}</div></div>
</div>
CALENDAR;
  
$tsCalendar = buildcalendar('shipBSQTo'); 
$shpToCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=shpQryToDateValue><input type=text READONLY id=shpQryToDate class="inputFld" style="width: 17vw;"></div>
  <div class=valueDropDown style="min-width: 17vw;" id=tcal><div id=shptCalendar>{$tsCalendar}</div></div>
</div>
CALENDAR;

$fCalendar = buildcalendar('biosampleQueryFrom'); 
$bsqFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=bsqueryFromDateValue><input type=text READONLY id=bsqueryFromDate class="inputFld" style="width: 18vw;"></div>
  <div class=valueDropDown style="width: 18vw;"><div id=bsqCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;

$tCalendar = buildcalendar('biosampleQueryTo'); 
$bsqToCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=bsqueryToDateValue><input type=text READONLY id=bsqueryToDate class="inputFld" style="width: 18vw;"></div>
  <div class=valueDropDown style="width: 18vw;"><div id=bsqtCalendar>{$tCalendar}</div></div>
</div>
CALENDAR;
  //DROP MENU BUILDER END ********************************************************************************* //

$grid = <<<BSGRID
<table border=0 width=100%>
<tr><td class=pageTitle>Biogroup Query Grid</td></tr>
<tr><td>

<table border=0>
<tr><td class=fldLabel>Biogroup Number</td><td class=fldLabel>Procuring Institution</td><td class=fldLabel>Segment Status</td><td class=fldLabel>HPR Status</td></tr>
<tr>
  <td><input type=text id=qryBG class="inputFld" style="width: 20vw;"></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryProcInstValue><input type=text id=qryProcInst READONLY class="inputFld" style="width: 20vw;"><div class=valueDropDown style="width: 20vw;">{$proc}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qrySegStatusValue><input type=text id=qrySegStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="width: 15vw;">{$seg}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qryHPRStatusValue><input type=text id=qryHPRStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="width: 15vw;">{$hpr}</div></div></td>
</tr>
<tr><td>(Single, range or series)</td></tr>
</table>

<table>
<tr><td colspan=2 class=fldLabel>Procurement Date Range (From/To)</td><td colspan=2 class=fldLabel>Shipping Date Range (From/To)</td></tr>
<tr>
  <td>{$bsqFromCalendar}</td>
  <td>{$bsqToCalendar}</td>
  <td>{$shpFromCalendar}</td>
  <td>{$shpToCalendar}</td>
</tr>
</table>

<table>  
<tr><td class=fldLabel>Assigned To (Investigator Id)</td><td class=fldLabel>TQ-Request Id</td><td class=fldLabel>Ship Doc Number</td><td class=fldLabel>Ship Doc Status</td></tr>
<tr>
  <td><div class=suggestionHolder><input type=text id=qryInvestigator class="inputFld"><div id=investSuggestion class=suggestionDisplay>&nbsp;</div></div></td>
  <td><input type=text id=qryREQ class="inputFld" style="width: 10vw;"></td>
  <td><input type=text id=qryShpDocNbr class="inputFld" style="width: 20vw;"></td>
  <td>{$shpsts}</td>
</tr>
<tr><td>(Type Inv#: for suggestion list type Divisional Code, Name, INV# or Institution.)</td><td>(REQ#)</td><td>(Single, range or series)</td></tr>
</table>

<table>
<tr><td class=fldLabel>Diagnosis Designation Search Term</td><td class=fldLabel>Specimen Category</td></tr>
<tr>
  <td><input type=text id=qryDXDSite class="inputFld" style="width: 54vw;"></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryDXDSpecimenValue class="inputFld"><input type=text id=qryDXDSpecimen class="inputFld" style="width: 16vw;"><div class=valueDropDown style="width: 16vw;">{$spc}</div></div>     </td>
</tr>
<tr><td colspan=2>(Read instructions in 'help' to query in 'Diagnosis Designation Search Term'.  Delimit with ; [semi-colon] / - [hyphen] to denote blank fields  / 'like' match indicator ^ [caret]. Order: (site-subsite);(diagnosis-modifier);(metssite))</td></tr>
</table>

<table>
<tr><td class=fldLabel>PHI-Age</td><td class=fldLabel>Race</td><td class=fldLabel>Sex</td><td class=fldLabel>Procedure</td><td colspan=2 class=fldLabel>Preparation</td></tr>
<tr>
  <td><input type=text id=phiAge class="inputFld" style="width: 6vw;"></td>
  <td><div class=menuHolderDiv><input type=hidden id=phiRaceValue><input type=text id=phiRace READONLY class="inputFld" style="width: 12vw;"><div class=valueDropDown style="width: 12vw;">{$race}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=phiSexValue><input type=text id=phiSex READONLY class="inputFld" style="width: 6vw;"><div class=valueDropDown style="width: 6vw;">{$sex}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryProcTypeValue><input type=text id=qryProcType READONLY class="inputFld" style="width: 8vw;"><div class=valueDropDown style="width: 8vw;">{$ptype}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryPreparationMethodValue><input type=text id=qryPreparationMethod READONLY class="inputFld" style="width: 18.3vw;"><div class=valueDropDown style="width: 18.3vw;">{$prp}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryPreparationValue><input type=text id=qryPreparation READONLY class="inputFld" style="width: 19vw;"><div class=valueDropDown style="width: 19vw;" id=preparationDropDown>&nbsp;</div></div></td>
</tr>
<tr><td>(Single or Range)</td></tr>
</table>

</td></tr>
<tr><td align=right style="padding: 3vh 26vw 0 0;">
  <table class=tblBtn id=btnGenBioSearchSubmit style="width: 6vw;"><tr><td><center>Search</td></tr></table>
</td></tr>
</table>

BSGRID;
return $grid; 
}

function generatePageTopBtnBar($whichpage) { 

//TODO:  DUMP THE BUTTONS INTO A DATABASE AND GRAB WITH A WEBSERVICE    
//TODO: MOVE ALL JAVASCRIPT TO JAVASCRIPT FILE

switch ($whichpage) { 

case 'procurebiosample':
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell>
    <table class=topBtnDisplayer id=btnPBClearGrid border=0><tr><td>   </td><td>Clear Grid</td></tr></table>
  </td>
</tr>
BTNTBL;
break;
case 'reportresultsscreen':
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnRRExport><tr><td><i class="material-icons">import_export</i></td><td>Export</td></tr></table></td>
</tr>
BTNTBL;
break;    
case 'reportscreen':
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnClearRptGrid><tr><td><i class="material-icons">layers_clear</i></td><td>Clear Grid</td></tr></table></td>
</tr>
BTNTBL;
break;    
case 'coordinatorCriteriaGrid':
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer onclick="clearCriteriaGrid();"><tr><td><i class="material-icons">layers_clear</i></td><td>Clear Grid</td></tr></table></td>
</tr>
BTNTBL;
break;   
case 'hprreviewactions': 
$innerBar = <<<BTNTBL
<!--
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">layers_clear</i></td><td>New Review</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPathReportDsp><tr><td><i class="material-icons">description</i></td><td>Pathology Report</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnConfirmHPR><tr><td><i class="material-icons">check_circle_outline</i></td><td>Confirm</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddHPR><tr><td><i class="material-icons">add_circle_outline</i></td><td>Additional</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnDenyHPR><tr><td><i class="material-icons">error_outline</i></td><td>Denied</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnUnusableHPR><tr><td><i class="material-icons">highlight_off</i></td><td>Unusable</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnInconHPR><tr><td><i class="material-icons">help_outline</i></td><td>Inconclusive</td></tr></table></td>
</tr>
//-->
BTNTBL;
break; 
case 'coordinatorResultGrid':
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltNew><tr><td><i class="material-icons">fiber_new</i></td><td>New Search</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltExport><tr><td><i class="material-icons">import_export</i></td><td>Export Results</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltToggle><tr><td><i class="material-icons">get_app</i></td><td>Toggle Select</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltParams><tr><td><i class="material-icons">settings</i></td><td>View Parameters</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltAssignSample><tr><td><i class="material-icons">person_add</i></td><td>Assign</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltMakeSD><tr><td><i class="material-icons">local_shipping</i></td><td>Create Shipdoc</td></tr></table></td>  
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltSubmitHPR><tr><td><i class="material-icons">assignment</i></td><td>Submit HPR Over-Ride</td></tr></table></td>           
</tr>
BTNTBL;
    break;
}

$rtnthis = <<<RTNTHIS
<div id=pageTopButtonBar>
  <table border=0 cellspacing=0 cellpadding=0 id=topBtnBarTbl>
    <tr><td id=topBtnBarHorizontalSpacer rowspan=2></td><td id=topBtnBarVerticalSpacer>&nbsp;</td></tr> 
{$innerBar}
</table>
</div>
RTNTHIS;
return $rtnthis;
}

function generateContextMenu($whichpage) { 

//TODO: DUMP THE BUTTONS IN TO A DATABASE AND GRAB WITH A WEBSERVICE
    
//TODO: ADD THESE BACK IN AS YOU ADD FUNCTIONALITY
//<tr><td class=contextOptionHolder><table id=cntxEditBG><tr><td><i class="material-icons cmOptionIcon">bubble_chart</i></td><td id=EDITBGDSP class=cmOptionText>Edit Biogroup</td></tr></table></td></tr>        
//<tr><td class=contextOptionHolder><table onclick="alert('Edit Segment');"><tr><td><i class="material-icons cmOptionIcon">blur_circular</i></td><td id=EDITSEGDSP class=cmOptionText>Edit Segment</td></tr></table></td></tr>        
//<tr><td class=contextOptionHolder><table onclick="alert('Edit Shipdoc');"><tr><td><i class="material-icons cmOptionIcon">local_shipping</i></td><td class=cmOptionText id=EDITSHPDOC>Edit Shipment Document</td></tr></table></td></tr>     
//<tr><td class=contextOptionHolder><table onclick="alert('View Documents');"><tr><td><i class="material-icons cmOptionIcon">file_copy</i></td><td class=cmOptionText>View Documents/Pathology Report</td></tr></table></td></tr>     
//<tr><td class=contextOptionHolder><table onclick="alert('View Documents');"><tr><td><i class="material-icons cmOptionIcon">file_copy</i></td><td class=cmOptionText>View Documents/Chart Review</td></tr></table></td></tr>     
//<tr><td class=contextOptionHolder><table onclick="alert('Associative Groups');"><tr><td><i class="material-icons cmOptionIcon">link</i></td><td class=cmOptionText>Associative Group</td></tr></table></td></tr>       
//<tr><td class=contextOptionHolder><table onclick="alert('HPR Results');"><tr><td><i class="material-icons cmOptionIcon">stars</i></td><td class=cmOptionText>View HPR Results</td></tr></table></td></tr> 

switch ($whichpage) { 
case 'coordinatorResultGrid':
$innerBar = <<<BTNTBL
<tr>
    <td class=contextOptionHolder><table id=cntxPrntSD><tr><td><i class="material-icons cmOptionIcon">file_copy</i></td><td class=cmOptionText id=PRINTSD>View Documents/Shipment Document</td></tr></table></td>
</tr>     
BTNTBL;
    break;
}

$rtnthis = <<<RTNTHIS
  <table border=0 cellspacing=0 cellpadding=0 id=contentMenuTbl> 
{$innerBar}
</table>
RTNTHIS;
return $rtnthis;
}

function bldReportResultsScreen($whichrpt, $usr) { 
  $rptarr = json_decode(callrestapi("GET", dataTree . "/report-parts/{$whichrpt}",serverIdent, serverpw), true);
  $whorequested = $rptarr['DATA']['bywho'];
  $onwhen = $rptarr['DATA']['onwhen'];
  $rptmod = $rptarr['DATA']['reportmodule'];
  $rptCreated = "{$rptarr['DATA']['rptcreator']} / {$rptarr['DATA']['rptcreatedon']}";
  $rptdesc = $rptarr['DATA']['dspreportdescription'];
  $dsprptname = $rptarr['DATA']['dspreportname'];
  $modDsp = $rptarr['DATA']['groupingname'];
  $modlink = $rptarr['DATA']['reportmodule'];
  $rptaccesslvl = $rptarr['DATA']['rqaccesslvl'];
  $uaccess = $usr->accessnbr;
  $uid = $usr->userid;

$hdTbl = <<<HEADTBL
<table>
<tr><td>Module: </td><td>{$modDsp}</td><td onclick="navigateSite('reports/{$modlink}');">(Click for reports in module)</td></tr>
<tr><td>Name: </td><td colspan=2>{$dsprptname}</td></tr>
<tr><td>Description: </td><td colspan=2>{$rptdesc}</td></tr>
<tr><td>Created: </td><td colspan=2>{$rptCreated}</td></tr>
<tr><td>Requested By: </td><td colspan=2>{$whorequested}</td></tr>
<tr><td>Requested On: </td><td colspan=2>{$onwhen}</td></tr>
</table>
HEADTBL;

//CHECK ACCESS LEVEL IS CORRECT
if ($rptaccesslvl > $uaccess) { 
    $rtnpage = "<h1>ACCESS DENIED TO USER ({$uid}/Access Level: {$uaccess})";
} else { 
    
  $pdta = json_encode($rptarr);
  $tbldta = json_decode(callrestapi("POST", dataTree . "/data-doers/grab-report-data",serverIdent, serverpw, $pdta), true);           
  
  $jsonToExport = json_encode( $tbldta['DATA'] );
  
  
  $resultTbl = "<table><tr><td id=reportresultitemfound>Records Found: {$tbldta['ITEMSFOUND']}</td></tr></table><div id=jsonToExport>"  . $jsonToExport . "</div>";
  foreach ($tbldta['DATA'] as $records) { 
    $rowTbl .= "<tr>"; 
    $headRow = "<tr>";
    foreach ($records as $constitKey => $constitVal) {
      $columnvalue = preg_replace('/\_/','&nbsp;',$constitKey);  
      $headRow .= "<th>{$columnvalue}</th>";  
      $rowTbl .= "<td>{$constitVal}&nbsp;</td>";
    }
    $rowTbl .= "</tr>";
    $headRow .= "</tr>";
  }  
  //TODO: ADD TFOOTER AT SOME POINT IN THE FUTURE FOR TOTALS
  $resultTbl .= "<table border=0 id=reportResultDataTbl><thead>{$headRow}</thead><tbody>{$rowTbl}</tbody></table>";
  $rtnpage = <<<PAGESTUFF
<table border=0 id=reportDefinitionTbl>
<tr><td valign=top id=reportIdentification>{$hdTbl}</td></tr>
<tr><td valign=top><div id=recordsDisplay>{$resultTbl}</div></td></tr>
<tr><td valign=bottom id=reportFooterBar>{$ftTbl}</td></tr>
</table>
PAGESTUFF;
}

return $rtnpage;
}

function bldSidePanelORSched($institution, $procedureDate) { 

$prcCalendarMaker = buildcalendar('procedureprocurequery'); 
$prcCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder>
      <input type=hidden id=fldPRCProcedureDateValue>
       <input type=text READONLY id=fldPRCProcedureDate class="inputFld" style="width: 17vw;"></div>
  <div class=valueDropDown style="min-width: 17vw;" id=procedurecal><div id=procureProcedureCalendar>{$prcCalendarMaker}</div></div>
</div>
CALENDAR;


return "{$prcCalendar}   {$institution} :: {$procedureDate}";
    
}

function bldBiosampleProcurement($usr) { 
    // {"statusCode":200,"loggedsession":"4tlt57qhpjfkugau1seif6glo0","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":58,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"menuvalue":"Procure Biosample","pagesource":"procure-biosample","additionalcode":""}]],["433","DATA COORDINATOR","",[{"menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""},{"menuvalue":"Consult Library","pagesource":"val-consult-library","additionalcode":""},{"menuvalue":"Slide Image Library","pagesource":"image-library","additionalcode":""},{"menuvalue":"QMS Actions","pagesource":"qms-actions","additionalcode":""}]],["472","REPORTS","",[{"menuvalue":"All Reports","pagesource":"reports","additionalcode":""},{"menuvalue":"Barcode Run","pagesource":"reports\/inventory\/barcode-run","additionalcode":""},{"menuvalue":"Daily Procurement Sheet","pagesource":"reports\/procurement\/daily-procurement-sheet","additionalcode":""}]],["473","UTILITIES","",[{"menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474","HELP","scienceserver-help",[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]],"lastlogin":{"lastlogdate":"Mon Dec 17th, 2018 at 14:59","fromip":"170.212.0.91"},"accessnbr":"43"} 
    if ((int)$usr->allowprocure <> 1) { 
      //USER NOT ALLOWED TO PROCURE
      $holdingTbl = "<h1>USER NOT ALLOWED TO PROCURE BIOSAMPLES";
    } else { 
      $today = new DateTime('now');
      $tdydte = $today->format('m/d/Y');
      $orsched = bldSidePanelORSched( $usr->presentinstitution, $tdydte );
    
      
    
    
    
    
    $holdingTbl = <<<HOLDINGTBL
            <table border=1 width=100% id=procurementAddHoldingTbl>
                   <tr>
                      <td rowspan=2>Collection Grid</td><td class=sidePanel valign=top>Today's Collection Summary</td>
                   </tr>
                   <tr>
                       <td class=sidePanel valign=top>{$orsched}</td>
                   </tr>
            </table> 
HOLDINGTBL;
    }
    return $holdingTbl;
}


function bldReportParameterScreen($whichrpt, $usr) { 

$rptarr = json_decode(callrestapi("GET", dataTree . "/report-definition/{$whichrpt}",serverIdent, serverpw), true);
$rpt = $rptarr['DATA'];
$rptaccesslvl = $rpt['accesslvl'];
$uaccess = $usr->accessnbr;
$uid = $usr->userid;
//CHECK ACCESS LEVEL IS CORRECT
if ($rptaccesslvl > $uaccess) { 
    $rtnpage = "<h1>ACCESS DENIED TO USER ({$uid}/Access Level: {$uaccess})";
} else { 

  $rptgroupingname = $rpt['groupingname'];
  $rptgroupurl = $rpt['groupingurl'];
  $rptname = $rpt['reportname'];
  $rpturl = "{$rptgroupurl}/{$rpt['reporturl']}";
  $rptdesc = $rpt['reportdescription']; 
  $rptallowgriddsp = $rpt['allowgriddsp'];
  $rptallowpdf = $rpt['allowpdfdsp'];
  $rptbywhom = $rpt['bywhom'];
  $rptcreation = $rpt['rptcreation'];

  $hdTbl = <<<TBLTBL
      <table border=0 cellspacing=0 cellpadding=0 id=defHead>
        <tr onclick="navigateSite('reports/{$rptgroupurl}');"><td>Module:&nbsp;</td><td>&nbsp;{$rptgroupingname}</td><td>&nbsp;(Click for reports in module)</td></tr>
        <tr><td>Name: &nbsp;</td><td colspan=2>&nbsp;{$rptname} <input type=hidden id=reporturlname value="{$rpturl}"></td></tr>
        <tr><td>Description: &nbsp;</td><td colspan=2>&nbsp;{$rptdesc}</td></tr>
        <tr><td>Created: &nbsp;</td><td colspan=2>&nbsp;{$rptbywhom} / {$rptcreation}</td></tr>
      </table>
TBLTBL;

  //BUILD CRITERIA
  if (count($rpt['criteria']) < 1) { 
    $paraTbl = "<h1>NO CRITERIA PARAMETERS ARE LISTED FOR THIS REPORT";
  } else {
    $parameterCntr = 0;  
    $paraTbl = "<form id=reportParameterGrid><table border=0><tr><td colspan=2>REPORT OPTION PARAMETER LISTING</td></tr>";
    foreach ($rpt['criteria'] as $key => $value) { 
        //MARK REQUIRED CRITERIA
        $chkbox = ((int)$value['requiredind'] !== 1 && (int)$value['includenondsp'] !== 1) ? "<input type=checkbox id=\"fldParaChkBx{$parameterCntr}\" id=\"fldParaChkBx{$parameterCntr}\" data-sqlwhere=\"{$value['sqltextline']}\">" : "<input type=checkbox disabled=\"disabled\" checked=\"checked\" id=\"fldParaChkBx{$parameterCntr}\" data-sqlwhere=\"{$value['sqltextline']}\">";
        preg_match_all("/:\b[a-zA-Z]{1,}\b/", $value['sqltextline'], $out);


        $rowOne = "&nbsp;";
        $rowTwo = "&nbsp;";
        if ((int)$value['includenondsp'] === 1) { 
          $rowOne = "";
          $rowTwo = "{$value['dsponinclude']}";
        } else { 
          foreach($out[0] as $critval) { 
            $flddef = str_replace(":","",$critval); 
            $rptfld = json_decode(callrestapi("GET", dataTree . "/report-criteria-field-definition/{$flddef}",serverIdent, serverpw), true);
            if ((int)$rptfld['ITEMSFOUND'] === 1) {
              $fld = "";  
              switch ($rptfld['DATA'][0]['typeoffield']) {
                case 'int':
                  $fld = "<input type=text id=\"fldPara{$flddef}\" data-datatype='int' data-paracount=\"{$parameterCntr}\" data-criterianame=\"{$critval}\" style=\"width: {$rptfld['DATA'][0]['flddspwidth']}vw; text-align: right;\">";
                  break;
                case 'date':
                  $fld = "<input type=text id=\"fldPara{$flddef}\" data-datatype='date' data-paracount=\"{$parameterCntr}\" data-criterianame=\"{$critval}\" style=\"width: {$rptfld['DATA'][0]['flddspwidth']}vw;\"><br>Dates Must be YYYY-mm-dd";
                  break;
                case 'string':
                  $fld = "<input type=text id=\"fldPara{$flddef}\" data-datatype='string' data-paracount=\"{$parameterCntr}\" data-criterianame=\"{$critval}\" style=\"width: {$rptfld['DATA'][0]['flddspwidth']}vw;\">";
                  break;
                case 'menu':
                  $fld = ( trim($rptfld['DATA'][0]['menuurl']) !== "" ) ? getRptParaMenu( trim($rptfld['DATA'][0]['menuurl']), $flddef, $parameterCntr, $critval, $rptfld['DATA'][0]['flddspwidth'] ) : "";
                  break;
              }
              $rowOne .= "<td>{$rptfld['DATA'][0]['flddisplay']}</td>";
              $rowTwo .= "<td>{$fld}</td>";
            } else { 
              $rowOne .= "<td>ERROR</td>";
              $rowTwo .= "<td>FIELD DEFINITION MISSING (DO NOT RUN!)</td>"; 
            } 
          }
        }



        $critTbl = "<table><tr>{$rowOne}</tr><tr>{$rowTwo}</tr></table>";
        $paraTbl .= "<tr><td>{$chkbox}</td><td>{$critTbl}</td></tr>"; 
        $parameterCntr++;
    }
    $paraTbl .= "</table></form>";
  }
//END BUILD CRITERIA
$gridBtn = ((int)$rptallowgriddsp === 1) ? "<table class=tblBtn id=btnGenRptData><tr><td>View Data</td></tr></table>" : "";
$pdfBtn = ((int)$rptallowpdf === 1) ? "<table class=tblBtn id=btnGenRptPDF><tr><td>Print Report</td></tr></table>" : "";
  $ftTbl = <<<TBLTBL
<table width=100%><tr><td><center><table><tr><td>{$gridBtn}</td><td>{$pdfBtn}</td></tr></table></td></tr></table>
TBLTBL;

$rtnpage = <<<PAGESTUFF
<table border=0 id=reportDefinitionTbl>
<tr><td valign=top id=reportIdentification>{$hdTbl}</td></tr>
<tr><td valign=top> {$paraTbl} </td></tr>
<tr><td valign=bottom id=reportFooterBar>{$ftTbl}</td></tr>
</table>
PAGESTUFF;
}
return $rtnpage;
}

function generateRptParaMenu() { 
//NOT PRESENTLY ACTIVE  PLANNED USE:  GENERATE MENUS BASED ON USER PROFILE 
}

function getRptParaMenu($whichmenuurl , $flddef, $paracnt, $criteriavalue, $dspwidth) { 
  $si = serverIdent;
  $sp = serverpw;
  $mnuarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/{$whichmenuurl}",$si,$sp),true);
  $menu = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPara{$flddef}','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($mnuarr['DATA'] as $mnuval) { 
    $menu .= "<tr><td onclick=\"fillField('fldPara{$flddef}','{$mnuval['codevalue']}','{$mnuval['menuvalue']}');\" class=ddMenuItem>{$mnuval['menuvalue']}</td></tr>";
  }
  $menu .= "</table>";
 
  $rtnThis = <<<RTNTHIS
<div class=menuHolderDiv>
   <input type=hidden id="fldPara{$flddef}Value" data-datatype='menu' data-paracount='{$paracnt}' data-criterianame='{$criteriavalue}'>
   <input type=text id="fldPara{$flddef}" READONLY class="inputFld" style="width: {$dspwidth}vw;">
<div class=valueDropDown style="width: {$dspwidth}vw;">{$menu}</div>
</div>
RTNTHIS;
   return $rtnThis;
}

