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
      case 'dialogHelpTicket':
        $titleBar = "ScienceServer HelpTicket Submission";
        //$footerBar = "DONOR RECORD";
        $innerDialog = bldHelpTicketDialogBox($passedData);
      break;  
      case 'dialogPBAddDelink': 
        $titleBar = "Add Delinked Donor";
        $innerDialog = bldQuickAddDelinkdialog();
      break;
      case 'dataCoordUploadPR':
        $titleBar = "Quick Pathology Report Upload";
        $innerDialog = bldQuickPRUpload( $passedData );
        break;
      case 'dialogVoidBG':
        $titleBar = "Void Biogroup";
        $innerDialog = bldDialogVoidBG( $passedData );
      break;
      case 'dialogAddBGSegments':
        $titleBar = "Add Biogroup Segments";
        $innerDialog = bldDialogAddSegment( $passedData );
      break; 
      case 'dataCoordEditPR':
        $titleBar = "Quick Pathology Report Editor";
        $innerDialog = bldQuickPREdit( $passedData );
        break;
      case 'dialogInventoryOverride':
        $titleBar = "Inventor Check-in Over-Ride Deviation Screen";
        $innerDialog = bldQuickInventoryOverride( $passedData );
        break;
      case 'dialogPrintThermalLabels':
        $titleBar = "Print Label";
        $innerDialog = bldQuickPrintThermalLabels( $passedData );          
        break;
      case 'procureBiosampleEditDonor':
        $titleBar = "Quick-Edit Donor Record";
        $innerDialog = bldQuickEditDonor( $passedData );
        break;
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

function biogroupdefinition( $rqststr, $usr ) { 
  $u = json_encode($usr);
  $r = json_encode($rqststr);
  $url = explode("/",$_SERVER['REQUEST_URI']);   
  $bg = cryptservice($url[2],'d',false);
if ( trim($bg) !== '' ) { 
  //TODO:  CHECK THAT THIS IS AN ACTUAL BGNUMBER  
$bgdsp = getBiogroupDefitionDisplay($bg, $url[2]);
$rtnthis = <<<PAGEHERE
{$bgdsp}
PAGEHERE;
} else { 
  ///NO ENCRYPTED BIOGROUP
  $rtnthis = "BAD ENCRYPTION CODE IN URL";  
}
  return $rtnthis;
}


function paymenttracker( $rqststr, $usr ) {
    session_start();
    $dta['sessionid']   = session_id();
    $dta['user']        = $usr->useremail;
    $dta['nbrofdays']   = 10; //MAKE THIS CHANGABLE!  
    $pdta = json_encode($dta);
    $rsltdta = json_decode(callrestapi("POST", dataTree . "/data-doers/financial-credit-card-payments",serverIdent, serverpw, $pdta), true);    
    if ( $rsltdta['MESSAGE'][0] !== "GOOD") {
$rtnthis = <<<PAGEHERE
<h1>USER NOT ALLOWED ACCESS TO FINANCIALS
PAGEHERE;
    } else {
//{"transDate":"2019-03-05","countrecords":1,"detaillines":[{"reference_number":"15518001418949","decision":"ACCEPT","billto":"EPESH1","pay_invoices":"6616","amount":"50"}]}
$dteList = $rsltdta['DATA'];        

if ( trim($rqststr[2]) !== "" ) { 
    //GET DETAILS
    $ddta['sessionid'] = session_id();
    $ddta['user'] = $usr->useremail; 
    $ddta['encycode'] = $rqststr[2];
    $pddta = json_encode($ddta);

    //{"MESSAGE":["GOOD"],"ITEMSFOUND":0,"DATA":[{"bill_to_address_line2":"159 Goessmann Lab","bill_to_address_city":"Amherst","bill_to_address_state":"MA","bill_to_address_postal_code":"01003","bill_to_address_country":"US"}]} 
    $detaildta = json_decode(callrestapi("POST", dataTree . "/data-doers/financial-credit-card-payment-detail",serverIdent, serverpw, $pddta), true);    
    $dType = strtoupper($detaildta['DATA'][0]['transaction_type']);
    $dDteTime = ($detaildta['DATA'][0]['decision'] !== "ERROR") ? DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $detaildta['DATA'][0]['signed_date_time'])->format('m/d/Y') : "";   
    $dAuthTime = ($detaildta['DATA'][0]['decision'] !== "ERROR") ? DateTime::createFromFormat('Y-m-d\THis\Z', $detaildta['DATA'][0]['auth_time'])->format('m/d/Y H:i \U\T\C') : "";   
    $dInv = strtoupper($detaildta['DATA'][0]['bill_to_investigator']);
    $dName = strtoupper($detaildta['DATA'][0]['bill_to_forename']) . " " . strtoupper($detaildta['DATA'][0]['bill_to_surname']);
    switch ($detaildta['DATA'][0]['req_card_type']) { 
      case '001':
          $card = "VISA";
          break;
      case '002':
          $card = "MASTERCARD";
          break;
      case '003':
          $card = "AMERICAN EXPRESS";
          break;
      case '004': 
          $card = "DISCOVER";
          break;
      default: 
          $card = "UNKNOWN CARD TYPE";    
    }

    setlocale(LC_MONETARY, 'en_US');
    $money = ( trim($detaildta['DATA'][0]['amount']) !== "" && is_numeric($detaildta['DATA'][0]['amount']) ) ? money_format("$%.2n", $detaildta['DATA'][0]['amount']) : $detaildta['DATA'][0]['amount']; 
    $addr = ( trim( $detaildta['DATA'][0]['bill_to_address_line1']) !== "" ) ? trim( $detaildta['DATA'][0]['bill_to_address_line1']) : "";
    $addr .= ( trim( $detaildta['DATA'][0]['bill_to_address_line2']) !== "" ) ?  ( trim($addr) !== "" ) ? "\n" . trim( $detaildta['DATA'][0]['bill_to_address_line2']) :  trim( $detaildta['DATA'][0]['bill_to_address_line2']) : "";



    //TODO:  MAKE PRINT OBJECTS
    $displayThis = <<<DETTBL
<table border=0 width=100%>
<tr><td align=right><table><tr><td><table class=tblBtn id=btnPrintThis onclick="openOutSidePage('{$tt}/print-obj/financials/{$rqststr[2]}');" style="width: 6vw;"><tr><td><center><i class="material-icons topbtns">print</i></td></tr></table></td><td><table class=tblBtn id=btnPrintTen onclick="openOutSidePage('{$tt}/print-obj/financials/last-ten');" style="width: 6vw;"><tr><td><center><i class="material-icons topbtns">list_alt</i></td></tr></table></td></tr></table></td></tr>
<tr><td>
  <table border=0>
    <tr><td class=dspFldLabel>Transaction ID</td><td class=dspFldLabel>Transaction Type</td><td class=dspFldLabel>Transaction Date</td><td class=dspFldLabel>Transaction Status</td></tr>
    <tr><td><input type=text id=dspFldTransUUID READONLY value="{$detaildta['DATA'][0]['transaction_uuid']}"></td><td><input type=text id=dspFldTransType READONLY value="{$dType}"></td><td><input type=text id=dspFldTransDate READONLY value="{$dDteTime}"></td><td><input type=text id=dspFldTransStat READONLY value="{$detaildta['DATA'][0]['decision']}"></td></tr>
    <tr><td class=dspFldLabel>Auth Code</td><td class=dspFldLabel>Auth Reference</td><td class=dspFldLabel>Auth Date-Time</td><td class=dspFldLabel>Card Type</td></tr>
    <tr><td><input type=text id=dspFldAuthCode READONLY value="{$detaildta['DATA'][0]['auth_code']}"></td><td><input type=text id=dspFldAuthRefNo READONLY value="{$detaildta['DATA'][0]['auth_trans_ref_no']}"></td><td><input type=text id=dspFldAuthAuthTime READONLY value="{$dAuthTime}"></td><td><input type=text id=dspFldCard READONLY value="{$card}"></td></tr>
<tr><td class=dspFldLabel colspan=3>Invoices Paid</td><td class=dspFldLabel>Amount Paid</td></tr>
<tr><td colspan=3><input type=text id=dspFldAuthInvoices READONLY value="{$detaildta['DATA'][0]['pay_invoices']}"></td><td><input type=text id=dspFldAuthAmt READONLY value="{$money}"></td>

<tr><td class=dspFldLabel colspan=4>Authorization Message</td></tr><tr><td colspan=4><input type=text id=dspFldAuthMsg READONLY value="{$detaildta['DATA'][0]['message']}"></td></tr></table>

<p> &nbsp; <p>

    <table>
    <tr>
      <td class=dspFldLabel>Investigator ID</td>
      <td class=dspFldLabel>Name</td>
      <td class=dspFldLabel>Email</td>
    </tr>
    <tr>
      <td><input type=text id=dspFldTransINVID READONLY value="{$dInv}"></td>
      <td><input type=text id=dspFldTransName READONLY value="{$dName}"></td>
      <td><input type=text id=dspFldTransEmail READONLY value="{$detaildta['DATA'][0]['bill_to_email']}"></td>
    </tr>
    
    <tr><td colspan=2 class=dspFldLabel>Institution</td><td class=dspFldLabel>Phone</td></tr>
    <tr><td colspan=2> <input type=text id=dspFldTransCoName READONLY value="{$detaildta['DATA'][0]['bill_to_company_name']}"></td><td valign=top><input type=text id=dspFldTransPhone READONLY value="{$detaildta['DATA'][0]['bill_to_phone']}"></td>
  </table>

  <table>
    <tr><td colspan=3 class=dspFldLabel>Address</td></tr>
    <tr><td colspan=3><textarea id=dspFldTransAddress READONLY>{$addr}</textarea></td></tr>
    <tr><td><input type=text id=dspFldTransAddCity READONLY value="{$detaildta['DATA'][0]['bill_to_address_city']}"></td><td><input type=text id=dspFldTransAddState READONLY value="{$detaildta['DATA'][0]['bill_to_address_state']}"></td> <td><input type=text id=dspFldTransAddZip READONLY value="{$detaildta['DATA'][0]['bill_to_address_postal_code']}"></td>  </tr> 
  </table> 




</td></tr>
</table>
DETTBL;
 
} else { 
    $displayThis = "&nbsp;";
}

$listTbl = "<table border=0 id=payListTable>";
$rowCount = 0;
foreach ( $dteList as $datekey => $datevalue ) { 

    $detTbl = "<table border=0>";   
    foreach ( $datevalue['detaillines'] as $dKey => $dVal ) { 
      $refid = $dVal['reference_number'];
      switch ( $dVal['decision'] ) { 
        case 'ACCEPT':
          $icon = "<i class=\"material-icons goodpay\">check_circle</i>";
        break;
        default:
          $icon = "<i class=\"material-icons badpay\">remove_circle</i>";
      }
      $iname = strtoupper( $dVal['billto'] );
      $money = money_format("$%.2n", $dVal['amount']);
 
      $detTbl .= <<<DLINE
<tr onclick="navigateSite('payment-tracker/{$refid}');">
    <td class=payiconholder>{$icon}</td>
    <td class=payinvestname>{$iname}</td>
    <td class=payinvoices>{$dVal['pay_invoices']}</td>
    <td class=mohknee>{$money}</td>
</tr>
DLINE;
    } 
    $detTbl .= "</table>";

   $listTbl .= "<tr onclick=\"displayPaymentDetail('{$rowCount}');\"><td valign=top class=payDateDsp>{$datevalue['transDate']}</td><td valign=top class=payCounter>Payments: {$datevalue['countrecords']}</td></tr>"; 
   $listTbl .= "<tr><td colspan=2 valign=top><div id='detailDiv{$rowCount}' class=\"payDetailDiv\">{$detTbl}</div></td></tr>";
   $rowCount++;
} 
$listTbl .= "</table>";

$rtnthis = <<<PAGEHERE
<table border=0 width=100% height=100%>
  <tr><td colspan=2 id=mainPayHoldTblTitle>Payment Tracker</td></tr>
  <tr><td valign=top style="width: 16vw; border-right: 1px solid rgba(0,0,0,.4);">{$listTbl}</td><td valign=top>{$displayThis}&nbsp;</td></tr>
</table>
PAGEHERE;

    }

  return $rtnthis;
}

function collectiongrid( $rqststr, $usr) { 

    if ((int)$usr->allowprocure !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE PROCUREMENT SCREEN";        
    } else {
        $today = new DateTime('now');
        $tdydte = $today->format('m/d/Y');
        $tdydtev = $today->format('Y-m-d');
        $insmnu = bldUsrAllowInstDrop($usr);
        $cGridCalendar = bldCGridControlCalendar($tdydtev, $tdydte);         
        session_start();
        $dta['presentinstitution'] = $usr->presentinstitution;
        $dta['requesteddate'] = $tdydtev;
        $dta['usrsession'] = session_id();
        $pdta = json_encode($dta);        
        $rsltdta = json_decode(callrestapi("POST", dataTree . "/data-doers/collection-grid-results-tbl",serverIdent, serverpw, $pdta),true);          
               
        $rtnthis = <<<PAGEHERE
   <table border=0 cellspacing=0 cellpadding=0>
                <tr><td>
                     <table border=0><tr><td>{$insmnu}</td><td>{$cGridCalendar}</td><td><table class=tblBtn id=btnRefresh style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Refresh</td></tr></table></td></tr></table>
                </td></tr>
                <tr><td>
                     <div id=cResultGridHolder>
                     <div id=waitForMe>&nbsp; <!-- PUT A WAIT GIF HERE, IF YOU WANT //--> </div>
                     <div id=cResults>
                     {$rsltdta['DATA']}
                     
                     </div>
                     </div>
                     </td></tr>
   </table>
       
PAGEHERE;
    }

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
  $topBtnBar = generatePageTopBtnBar('procurebiosample' , $whichusr);
  $pg = bldBiosampleProcurement( $whichusr );
} 

if (trim($rqststr[2]) !== "") { 
   $bgBldr = explode("/",$_SERVER['REQUEST_URI']); 
   //BUILD EDIT COLLECTION SCREEN
   $topBtnBar = generatePageTopBtnBar('procurebiosampleedit');
   $pg = bldBiosampleProcurementEdit( $whichusr, cryptservice($bgBldr[2],'d',false) );
} 

$rtnthis = <<<PAGEHERE
{$topBtnBar} 
{$pg}
PAGEHERE;
return $rtnthis;    

}

function reports($rqststr, $whichusr) { 

//  if ((int)$whichusr->allowcoord !== 1     ) { 
//    $pg = "<h1>USER IS NOT ALLOWED TO USE THE REPORT SCREEN";        
//  } else {    
   
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
//   }
    
  $rtnthis = <<<PAGEHERE
{$topBtnBar} 
{$pg}
PAGEHERE;

return $rtnthis;    
}

function scienceserverhelp($rqststr, $whichusr) { 

$tt = treeTop; 
$givenSearchTerm = "";  

if ( trim($rqststr[2]) !== "" ) {
    if ( trim($rqststr[2]) === "querysearch" ) { 
      //GET SEARCH RESULTS
      $rsltdta = json_decode(callrestapi("GET", dataTree . "/search-help-results/{$rqststr[3]}", serverIdent, serverpw),true);
      $searchtermarr = json_decode($rsltdta['DATA']['head']['srchterm'], true);
      $givenSearchTerm = $searchtermarr['searchterm'];
      $itemsfound =$rsltdta['ITEMSFOUND']; 
      $objid = $rsltdta['DATA']['head']['objid'];
      $bywho = $rsltdta['DATA']['head']['bywho'];
      $onwhen = $rsltdta['DATA']['head']['onwhendsp'];

      foreach ($rsltdta['DATA']['searchresults'] as $rkey => $rval) { 
        $inner .= "<tr><td colspan=2>";
        $inner .= "<table class=zoogleTbl onclick=\"navigateSite('scienceserver-help/{$rval['helpurl']}');\">";
        $inner .= "<tr><td class=zoogleTitle>{$rval['titledsp']}</td></tr>";
        $inner .= "<tr><td class=zoogleURL>{$tt}/scienceserver-help/{$rval['urldsp']}</td></tr>";
        $inner .= "<tr><td class=zoogleAbstract>{$rval['abstract']}</td></tr>";
        //$inner .= "<tr><td align=right>Author: {$rval['byemail']} ({$rval['initialdate']})</td></tr>";
        $inner .= "</table>";
        $inner .= "</td></tr>";
      }
      $helpFile = <<<RTNTHIS
<table border=0 cellspacing=0 cellpadding=0 id=resultsSearchTbl>
<tr><td id=title colspan=2>Search Results</td></tr>
<tr><td id=itemsfound>Items found: {$itemsfound}</td><td id=bywhowhen align=right valign=top> Query By: {$bywho} ({$onwhen}) </td></tr>
{$inner}
</table>
RTNTHIS;
    } else {
      //GET HELP FILE
      //TODO - PULL FROM A WEB SERVICE    
      require(genAppFiles . "/dataconn/sspdo.zck"); 
      $hlpSQL = "SELECT ifnull(helptype,'') as hlpType, ifnull(title,'') as hlpTitle, ifnull(subtitle,'') as hlpSubTitle, ifnull(bywhomemail,'') as byemail, ifnull(date_format(initialdate,'%M %d, %Y'),'') as initialdte, ifnull(lasteditbyemail,'') as lstemail, ifnull(date_format(lastedit,'%M %d, %Y'),'') as lstdte, ifnull(txt,'') as htmltxt , ifnull(helpurl,'') as helpurl FROM four.base_ss7_help where replace(helpurl,'-','') = :dataurl ";
      $hlpR = $conn->prepare($hlpSQL); 
      $hlpR->execute(array(':dataurl' => trim($rqststr[2])));
      if ($hlpR->rowCount() < 1) { 
      } else {           
        $hlp = $hlpR->fetch(PDO::FETCH_ASSOC);        
        if ( strtoupper(trim($hlp['hlpType'])) === "PDF") { 
          //GET PDF
          $hlpTitle = $hlp['hlpTitle'];
          $hlpSubTitle = $hlp['hlpSubTitle'];  
          $pth = base64file(genAppFiles  . "{$hlp['htmltxt']}", "HELPDSPPDF","pdfhlp",true);
          $helpFile = <<<RTNTHIS
   <div id=hlpMainHolderDiv>
   <div id=hlpMainTitle>{$hlpTitle}</div> 
   <div id=hlpMainSubTitle>{$hlpSubTitle}</div>
   <div id=hlpMainText>
        {$pth}       
        <p>&nbsp;
   </div>
   </div>         
RTNTHIS;
        } else {         
          $hlpTitle = $hlp['hlpTitle'];
          $hlpSubTitle = $hlp['hlpSubTitle'];
          $hlpEmail = $hlp['byemail'];
          $hlpDte = ( trim($hlp['initialdte']) !== "" ) ? " / {$hlp['initialdte']}" : "";
          $hlpTxt = putPicturesInHelpText( $hlp['htmltxt'] );
          $hlpurl = cryptservice( $hlp['helpurl'] );
          $printTopicBtn = "<td><table class=tblBtn id=btnPrintThis onclick=\"openOutSidePage('{$tt}/print-obj/help-file/{$hlpurl}');\" style=\"width: 6vw;\"><tr><td><center><i class=\"material-icons helpticket\">print</i></td></tr></table></td>";
          $helpFile = <<<RTNTHIS
   <div id=hlpMainHolderDiv>
   <div id=hlpMainTitle>{$hlpTitle}</div> 
   <div id=hlpMainSubTitle>{$hlpSubTitle}</div>            
   <div id=hlpMainByLine>{$hlpEmail} {$hlpDte}</div>             
   <div id=hlpMainText>
        {$hlpTxt}
        <p>&nbsp;
   </div>
   </div>         
RTNTHIS;
        }
        
      }
    }
} else { 
  $helpFile = "<div id=instructionDiv>SELECT A SCIENCESERVER HELP FILE FROM THE OPTION LIST ON THE LEFT</div>";
}
    
$dta = json_decode(callrestapi("GET", dataTree . "/sshlp-topic-list", serverIdent, serverpw),true);
$t = "<div id=mainHelpFileHolder>";
$modCntr = 0;
$topcCntr = 0;
foreach ($dta['DATA'] as $key => $val) { 
  $expnd = ((int)count($val['topics']) > 0) ? "E-" : "";  //HAS TOPICS AND SUB FUNCTION
  $t .= "<div class=ssHlpModDiv><table cellspacing=0 cellpadding=0 class=hlpModuleTbl><tr><td><i class=\"material-icons\">keyboard_arrow_right</i></td><td>{$val['module']}</td></tr></table>";
  if ((int)count($val['topics']) > 0) { 
    foreach ( $val['topics'] as $tky => $tvl ) {
      //$topicon = ($tvl['topictype'] === "TOPIC") ? "<i class=\"material-icons topicicon\">library_books</i>" :  ($tvl['topictype'] === "PDF") ? "" : "<i class=\"material-icons topicicon\">desktop_windows</i>"; 
      switch ($tvl['topictype']) { 
          case 'TOPIC': 
              $topicon = "<i class=\"material-icons topicicon\">library_books</i>";
              break;
          case 'PDF':
              $topicon = "<i class=\"material-icons topicicon\">picture_as_pdf</i>";
              break;
          case 'SCREEN':
              $topicon = "<i class=\"material-icons topicicon\">desktop_windows</i>";    
              break;
          default:
          $topicon = "<i class=\"material-icons topicicon\">desktop_windows</i>";
      }
      
      $t .= "<div class=\"hlpTopicDiv\"><table cellspacing=0 cellpadding=0 onclick=\"navigateSite('scienceserver-help/{$tvl['topicurl']}');\" border=0 class=hlpTopicTbl><tr><td class=iconholdercell>{$topicon}</td><td>{$tvl['topictitle']}</td></tr></table>";
      if ((int)count($tvl['functionslist']) > 0 ) { 
        foreach ( $tvl['functionslist'] as $fky => $fvl ) { 
          $t .= "<div class=\"hlpFunctionDiv\"><table cellspacing=0 cellpadding=0 onclick=\"navigateSite('scienceserver-help/{$fvl['helpurl']}');\" class=hlpFuncTbl border=0><tr><td class=iconholdercell><i class=\"material-icons funcIcon\">arrow_right</i></td><td>{$fvl['title']}</td></tr></table></div>";
        }
      }
      $t .= "</div>";
      $topcCntr++;
    }
  }
  $t .= "</div>";
  $modCntr++;
}
$t .= "</div>";

$rtnthis = <<<PAGEHERE
<table border=0 id=sshHoldingTable>
   <tr><td colspan=2 id=head> 
       <table style="border-collapse: collapse;" width=100%><tr><td id=ssHelpFilesHeaderDsp> SCIENCESERVER HELP FILES </td>
                 <td align=right>
                   <table style="border-collapse: collapse;"><tr><td> <input type=text id=fldHlpSrch value="{$givenSearchTerm}"> </td>
                              <td><table class=tblBtn id=btnSearchHelp style="width: 6vw;"><tr><td><center>Search</td></tr></table></td>
                              {$printTopicBtn}
                              <td><table class=tblBtn id=btnHelpTicket style="width: 6vw;"><tr><td><center><i class="material-icons helpticket">build</i></td></tr></table></td></tr></table>
            </td></tr></table>
        </td></tr>
<tr><td valign=top id=topicDivHolder><div id=divDspTopicList>{$t}</div></td><td valign=top> {$helpFile}  </td></tr>
</table>
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
   <th class=groupingstart>Ship Doc /<br>Ship Date</th>
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
    $stsDte = (trim($val['statusdate']) === "") ? "&nbsp;" : "Status Date: {$val['statusdate']}";
    $stsDte .= (trim($val['statusby']) === "") ? "" : "<br>Status by: {$val['statusby']}";

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

        $sdencry = ( trim($val['shipdocnbr']) !== "" ) ? cryptservice($val['shipdocnbr']) : "";
        $dspSD = "<div class=ttholder>" . substr(('000000' . $val['shipdocnbr']),-6) . $dtedspthis;
        if (trim($val['sdstatus']) === "") { 
            $dspSD .= "<div class=tt>&nbsp;</div>";
        } else { 
            $dspSD .= "<div class=tt>Shipdoc Status: {$val['sdstatus']}<br>Status by: [INFO NOT AVAILABLE]<p><div onclick=\"displayShipDoc(event,'{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">print</i> Print Ship-Doc (" . substr(('000000' . $val['shipdocnbr']),-6) . ")</div></div>";
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
    
    $sgencry = cryptservice($val['segmentid']);
    $bgencry = cryptservice($val['pbiosample']);
    $moreInfo = ( trim($cmtDsp) !== "" ) ? "<div class=ttholder><div class=infoIconDiv><i class=\"material-icons informationalicon\">error_outline</i></div><div class=infoTxtDspDiv>{$cmtDsp}</div></div>" : "";

    $prDocId = "";
    switch (trim($val['pathologyrptind'])) { 
      case 'Y':
        if ((int)$val['pathologyrptdocid'] !== 0) {  
          $prDocId = ((int)$val['pathologyrptdocid'] > 0) ? cryptservice( (int)$val['pathologyrptdocid'], "e" ) : 0;
          $dspBG = substr($sglabel,0,5);
          $pRptDsp = <<<PRPTNOTATION
<div class=ttholder>{$val['pathologyrptind']}
   <div class=tt>
     <div class=quickLink onclick="printPRpt(event,'{$prDocId}');"><i class="material-icons qlSmallIcon">print</i> Print Pathology Report ({$dspBG})</div>
     <div class=quickLink onclick="editPathRpt(event,'{$prDocId}');"><i class="material-icons qlSmallIcon">file_copy</i> Edit Pathology Report ({$dspBG})</div>
   </div>
</div>
PRPTNOTATION;
        } else { 
          $pRptDsp = <<<PRPTNOTATION
<div class=ttholder>{$val['pathologyrptind']}
   <div class=tt>
     Biogroup has multiple pathology Report References.  See a CHTNEastern Informatics Staff Member
   </div>
</div>
PRPTNOTATION;
        }
      break;
      case 'P':
        $dspBG = substr($sglabel,0,5); 
        $pRptDsp = <<<PRPTNOTATION
<div class=ttholder>{$val['pathologyrptind']}
   <div class=tt>
     <div class=quickLink onclick="getUploadNewPathRpt(event,'{$sglabel}');"><i class="material-icons qlSmallIcon">file_copy</i> Upload Pathology Report (Biogroup: {$dspBG})</div>
   </div>
</div>
PRPTNOTATION;
      break; 
      default: 
        $pRptDsp = "{$val['pathologyrptind']}";
    }

//TODO: ADD ABILITY TO PULL ASSOCIATIVE RECORD    
    
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
     data-printprid = "{$prDocId}"
     data-printsdid = "{$sdencry}"
     onclick="rowselector('sg{$val['segmentid']}');" 
     ondblclick="navigateSite('biogroup-definition/{$bgencry}');"
  >
  <td>{$moreInfo}</td>
  <td {$pbSqrBgColor} class=colorline>&nbsp;</td>
  <td valign=top class=bgsLabel>{$sglabel}</td>
  <td valign=top><div class=ttholder>{$val['segstatus']}<div class=tt>{$stsDte}</div></div></td>
  <td class="qms qmsiconholder{$clssuffix}"><div class=ttholder>{$qmsicon}<div class=tt>{$qcstatustxt}</div></div></td>
  <td valign=top class="groupingstart">{$val['specimencategory']}</td>
  <td valign=top>{$val['site']}{$subsitedsp}</td>
  <td valign=top>{$val['diagnosis']}{$modds}</td>
  <td valign=top>{$val['metssite']}</td>
  <td valign=top class=groupingstart>{$val['procurementdate']}</td>
  <td valign=top align=right><div class=ttholder>{$val['procuringinstitutioncode']} / {$val['proctype']}<div class=tt align=left>{$val['procuringinstitution']}<br>Procedure: {$val['proctypedsp']}</div></div></td>
  <td valign=top class="groupingstart cntr">{$val['phiage']}</td>
  <td valign=top class="cntr"><div class=ttholder>{$val['phiracecode']}<div class=tt>{$val['phirace']}</div></div></td>
  <td valign=top class="cntr">{$val['phigender']}</td>
  <td valign=top class="cntr">{$val['cxind']}</td>
  <td valign=top class="cntr">{$val['rxind']}</td>
  <td valign=top class="cntr">{$pRptDsp}</td>
  <td valign=top class="cntr">{$val['informedconsentind']}</td>
  <td valign=top>{$val['preparationmethod']}</td>
  <td valign=top>{$val['preparation']}</td>
  <td valign=top class="cntr">{$val['hourspost']}</td>
  <td valign=top style="white-space:nowrap;">{$val['metric']}{$val['metricuom']}</td>
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
<tr><td class=columnQParamName>Diagnosis Designation:</td>  <td class=ColumnDataObj>{$srchtrm['site']} / {$srchtrm['diagnosis']}</td></tr>
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
<table border=0><tr><td>Items found: {$itemsfound} <input type=hidden value="{$rqststr[2]}" id=urlrequestid></td><td align=right>(Hover mouse on grid for context menu)</td></tr>
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
                $cselector = cryptservice("CR-" . $rs['dspmark'], "e");  
                $innerListing .= "<tr><td valign=top><table width=100%><tr><td><b>Chart #{$dspMark}</b></td><td class=prntIcon onclick=\"getDocumentText('{$cselector}');\"><i class=\"material-icons\">pageview</i></td><td onclick=\"openOutSidePage('{$tt}/print-obj/{$printObj}/{$cselector}');\" class=prntIcon><i class=\"material-icons\">print</i></td></tr><tr><td colspan=3>{$rs['abstract']}...</td></tr></table></td></tr>";
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
  

  $fsCalendar = buildcalendar('mainroot', date('m'), date('Y'), $whichUsr->friendlyname, $whichUsr->useremail, $whichUsr->loggedsession );  
  $rtnthis = <<<PAGEHERE
<table border=0 id=rootTable>
    <tr><td>&nbsp;</td><td style="width: 42vw;" align=right valign=top><div id="mainRootCalendar">{$fsCalendar}</div></td></tr>     
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
$ssversion = scienceserverTagVersion;


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
<div id=loginvnbr>(SMDA Version: {$ssversion} [ScienceServer])</div>    
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

  $sitearr = json_decode(callrestapi("POST", dataTree . "/data-doers/live-masterrecord-site-listing",serverIdent, serverpw, ""), true);
    $sitemnu = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryDXDSite','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($sitearr['DATA'] as $siteval) {
    $sitemnu .= "<tr><td onclick=\"fillField('qryDXDSite','{$siteval['sitesubsite']}','{$siteval['sitesubsite']}');\" class=ddMenuItem>{$siteval['sitesubsite']}</td></tr>";
  }
  $sitemnu .= "</table>";
 
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
  <td><div class=menuHolderDiv><input type=hidden id=qryProcInstValue><input type=text id=qryProcInst READONLY class="inputFld" style="width: 20vw;"><div class=valueDropDown style="min-width: 20vw;">{$proc}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qrySegStatusValue><input type=text id=qrySegStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$seg}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qryHPRStatusValue><input type=text id=qryHPRStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$hpr}</div></div></td>
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
<tr><td class=fldLabel>Specimen Category</td><td class=fldLabel>Site/Sub-Site (METS)</td><td class=fldLabel>Diagnosis Search Term (like Match)</td></tr>
<tr>
  <td><div class=menuHolderDiv><input type=hidden id=qryDXDSpecimenValue READONLY class="inputFld"><input type=text id=qryDXDSpecimen class="inputFld" style="width: 16.5vw;"><div class=valueDropDown style="min-width: 16.5vw;">{$spc}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryDXDSiteValue class="inputFld"><input type=text id=qryDXDSite READONLY class="inputFld" style="width: 27vw;"><div class=valueDropDown style="min-width: 16.5vw;">{$sitemnu}</div></div></td>
  <td><input type=text id=qryDXDDiagnosis class="inputFld" style="width: 27vw;"></td>
</tr>
</table>

<table>
<tr><td class=fldLabel>PHI-Age</td><td class=fldLabel>Race</td><td class=fldLabel>Sex</td><td class=fldLabel>Procedure</td><td colspan=2 class=fldLabel>Preparation</td></tr>
<tr>
  <td><input type=text id=phiAge class="inputFld" style="width: 6vw;"></td>
  <td><div class=menuHolderDiv><input type=hidden id=phiRaceValue><input type=text id=phiRace READONLY class="inputFld" style="width: 12vw;"><div class=valueDropDown style="min-width: 12vw;">{$race}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=phiSexValue><input type=text id=phiSex READONLY class="inputFld" style="width: 6vw;"><div class=valueDropDown style="min-width: 6vw;">{$sex}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryProcTypeValue><input type=text id=qryProcType READONLY class="inputFld" style="width: 8vw;"><div class=valueDropDown style="min-width: 8vw;">{$ptype}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryPreparationMethodValue><input type=text id=qryPreparationMethod READONLY class="inputFld" style="width: 18.3vw;"><div class=valueDropDown style="min-width: 18.3vw;">{$prp}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryPreparationValue><input type=text id=qryPreparation READONLY class="inputFld" style="width: 19vw;"><div class=valueDropDown style="min-width: 19vw;" id=preparationDropDown><center>(Select a Preparation Method)</div></div></td>
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

function generatePageTopBtnBar($whichpage, $whichusr) { 

//TODO:  DUMP THE BUTTONS INTO A DATABASE AND GRAB WITH A WEBSERVICE    
//TODO:  MOVE ALL JAVASCRIPT TO JAVASCRIPT FILE

switch ($whichpage) { 

case 'procurebiosampleedit':
$pxibtn = "";
//<!-- <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPRCSaveEdit border=0><tr><td><!--ICON //--></td><td>Save Edit</td></tr></table></td> //-->
//<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBCVocabSrch border=0><tr><td><!--ICON //--></td><td>Vocabulary Search</td></tr></table></td>
$innerBar = <<<BTNTBL
<tr>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBClearGrid border=0><tr><td><!--ICON //--></td><td>Clear Grid</td></tr></table></td>       
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBCSegment border=0><tr><td><!--ICON //--></td><td>Define Segment</td></tr></table></td>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBCLock border=0><tr><td><!--ICON //--></td><td>Release BG</td></tr></table></td>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBCVoid border=0><tr><td><!--ICON //--></td><td>Void BG</td></tr></table></td>
</tr>
BTNTBL;
    break;
case 'procurebiosample':
    $pxibtn = "";
    foreach($whichusr as $key =>$val) { 
      if ($key === 'allowpxi' && (int)$val === 1) {
        $pxibtn = "<tr class=btnBarDropMenuItem id=btnPBAddPHI ><td><i class=\"material-icons\">arrow_right</i></td><td>Add Donor (HUP Only) &nbsp;</td></tr>";
      }
    }
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBClearGrid border=0><tr><td><!--ICON //--></td><td>Clear Grid</td></tr></table></td>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPRCSave border=0><tr><td><!--ICON //--></td><td>Save BG</td></tr></table></td>        
  <td class=topBtnHolderCell>
         <div class=ttholder>                                                                                    
           <table class=topBtnDisplayer id=btnPBPHI border=0><tr><td><!--ICON //--></td><td>Donor</td></tr></table>
           <div class=tt> 
             <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
               <tr class=btnBarDropMenuItem id=btnPBAddDelink><td><i class="material-icons">arrow_right</i></td><td>Add Delinked Donor &nbsp;</td></tr>     
               {$pxibtn}     
             </table>
           </div>
         </div>
  </td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPBCVocabSrch border=0><tr><td><!--ICON //--></td><td>Vocabulary Search</td></tr></table></td>
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
  <td class=topBtnHolderCell>
    <div class=ttholder><table class=topBtnDisplayer id=btnBarRsltPrintAction><tr><td><i class="material-icons">print</i></td><td>Print</td></tr></table>
    <div class=tt>
      <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
        <tr class=btnBarDropMenuItem id=btnPrintAllPathologyRpts><td><i class="material-icons">arrow_right</i></td><td>Print Selected Pathology Reports</td></tr>     
        <tr class=btnBarDropMenuItem id=btnPrintAllShipDocs><td><i class="material-icons">arrow_right</i></td><td>Print Selected Ship-Docs</td></tr>     
        <tr class=btnBarDropMenuItem id=btnPrintAllLabels><td><i class="material-icons">arrow_right</i></td><td>Print Selected Labels</td></tr>     
      </table>
    </div>
    </div>
  </td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltExport><tr><td><i class="material-icons">import_export</i></td><td>Export Results</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltToggle><tr><td><i class="material-icons">get_app</i></td><td>Toggle Select</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltParams><tr><td><i class="material-icons">settings</i></td><td>View Parameters</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltAssignSample><tr><td><i class="material-icons">person_add</i></td><td>Assign</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltMakeSD><tr><td><i class="material-icons">local_shipping</i></td><td>Create Shipdoc</td></tr></table></td>  
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltSubmitHPR><tr><td><i class="material-icons">assignment</i></td><td>Submit HPR Override</td></tr></table></td>           
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltInventoryOverride><tr><td><i class="material-icons">blur_linear</i></td><td>Check-In Override</td></tr></table></td>           
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

function bldSidePanelORSched($institution, $procedureDate, $procedureDateValue) { 

  $prcCalendarMaker = buildcalendar('procedureprocurequery'); 
  //<td class=prcFldLbl>Display Order</td>
  $prcCalendar = <<<CALENDAR
<table border=0><tr><td class=prcFldLbl>Institution Code</td><td class=prcFldLbl>Date</td></tr>
<tr><td><input type=text readonly id=fldPRCProcedureInstitutionValue value="{$institution}"></td><td>
<div class=menuHolderDiv>
  <div class=valueHolder>
      <input type=hidden id=fldPRCProcedureDateValue value="{$procedureDateValue}">
      <input type=text READONLY id=fldPRCProcedureDate class="inputFld" value="{$procedureDate}"></div>
  <div class=valueDropDown style="min-width: 17vw;" id=procedurecal><div id=procureProcedureCalendar>{$prcCalendarMaker}</div></div>
</div>
</td><td>&nbsp;</td></tr>
</table>

CALENDAR;
return "{$prcCalendar}";
}

function bldQuickPrintThermalLabels( $passdata ) { 
   $pdta = json_decode( $passdata, true );
   $lArr = array(); 
   $cntr = 0;
   foreach ($pdta as $ky => $vl) { 
       $lArr['BS-'.$cntr] = $vl['bgslabel'];
       $cntr++;
   }
   $lArrPayload = json_encode($lArr);
   $prntarr = json_decode(callrestapi("GET", dataTree . "/thermal-printer-list",serverIdent, serverpw), true);
   $prnmenu = "<table border=0 class=menuDropTbl style=\"min-width: 19.8vw;\">";
   foreach ($prntarr['DATA'] as $pky => $pval) { 
     $prnmenu .= "<tr><td onclick=\"fillField('fldDialogLabelPrnter','{$pval['formatname']}','{$pval['printer']}');\" class=ddMenuItem>{$pval['printer']}</td></tr>";  
   }
   $prnmenu .= "</table>";
   
   $dialog = <<<DIALOG
           <input type=hidden value='{$lArrPayload}' id=segmentListingPayLoad>
   <table border=0 style="width: 24vw;">
   <tr><td style="font-size: 1.6vh; line-height: 1.6em; text-align: justify;">This dialog will print to the CHTNEast thermal barcode printers.  Select a printer from the list, specify the quantity of EACH label and then click the print button.</td></tr>
   <tr><td>
   <table border=0 cellspacing=2 cellpadding=0>
       <tr><td class=fldLabel>Printer</td><td class=fldLabel>Qty</td></tr>
       <tr>
           <td><div class=menuHolderDiv><input type=hidden id=fldDialogLabelPrnterValue><input type=text id=fldDialogLabelPrnter style="font-size: 1.8vh; width: 20vw;"><div class=valueDropDown>{$prnmenu}</div></div></td>
           <td><input type=text id=fldDialogLabelQTY style="font-size: 1.8vh; width: 3vw; text-align: right;" value=1></td>
       </tr>
        <tr><td colspan=2 align=right><table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="sendLblPrintRequest();"><tr><td style="white-space: nowrap;"><center>Print</td></tr></table></td></tr>
        </table>
   </td></tr>        
</table>           
              
DIALOG;
   return $dialog; 
}

function bldQuickInventoryOverride( $passdata ) { 

    $at = genAppFiles;
    $waitpic = base64file("{$at}/publicobj/graphics/zwait2.gif", "waitgifADD", "gif", true);         
    $invListWS = json_decode(callrestapi("GET", dataTree . "/global-menu/inventory-location-storagecontainers",serverIdent, serverpw), true);
    $pdta = json_decode( $passdata, true );
    $cntr = 1; 
    
              $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-menu-hpr-inventory-override",serverIdent, serverpw), true);
              $devm = "<table border=0 class=menuDropTbl style=\"min-width: 14.9vw;\">";
              foreach ($devarr['DATA'] as $devval) { 
                $devm .= "<tr><td onclick=\"fillField('fldDeviationReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
              }
              $devm .= "</table>";
    
    $devTbl = <<<DEVTBL
            <table border=0>
            <tr><td  class=checkInHead align=left>Inventory User PIN</td></tr>
            <tr><td><input type=password id=fldUsrPIN style="width: 15vw;"></td></tr>
            <tr><td style="width: 15vw; text-align: justify;"><b>CHTNEASTERN SOP DEVIATION NOTIFICATION</b>: This is NOT a standard inventory screen and should only be used in extenuating operating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures. Please enter a reason for the deviation below.</td></tr>
            <tr><td class=checkInHead>Deviation Reason</td></tr>
            <tr><td><div class=menuHolderDiv><input type=text id=fldDeviationReason style="width: 15vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
            <tr><td align=right><table class=tblBtn id=btnInvOverride style="width: 6vw;" onclick="updateStatuses();"><tr><td style="white-space: nowrap;"><center>Update Status</td></tr></table></td></tr>
                </table>
DEVTBL;
    
    
              $segList = "<form id=frmCheckInOverride><div id=waiterIndicator style=\"font-size: 1.5vh;\"><center>{$waitpic}<br>Please wait ...</div><table border=0><tr><td class=checkInHead>&nbsp;</td><td class=checkInHead>CHTN #</td><td class=checkInHead>Preparation</td><td class=checkInHead>New Status</td><td class=checkInHead>Inventory Location</td></tr>";
    foreach  ( $pdta as $key => $val ) {
      $payload = json_encode(array("segmentid" => $val['segmentid']));
      $sgdta = json_decode(callrestapi("POST", dataTree . "/data-doers/segment-masterrecord",serverIdent, serverpw, $payload), true);      
      $sg = $sgdta['DATA'];
      $invmenu = "";
      $dspInvMenu = "";
      $newStatus = "";

      if ($sg['segstatus'] === 'ONOFFER') { 
          //ALLOW CHECKIN
        $invmenu = "<table border=0 class=menuDropTbl>";
        foreach ($invListWS['DATA'] as $pky => $pval) { 
           $invmenu .= "<tr><td onclick=\"fillField('fldInvLoc.{$sg['segmentid']}','{$pval['codevalue']}','{$pval['menuvalue']}');\" class=ddMenuItem>{$pval['menuvalue']}</td></tr>";  
        }
        $invmenu .= "</table>";  
        $dspInvMenu = "<div class=menuHolderDiv><input type=hidden id=fldInvLoc.{$sg['segmentid']}Value><input type=text id=fldInvLoc.{$sg['segmentid']} READONLY class=inventoryLocDsp><div class=valueDropDown>{$invmenu}</div></div>";
        
        $newStatus = "";
        if ( $sg['assignedto'] === 'QC' ||  ( $sg['prepmethod'] === 'SLIDE' && (int)$sg['hprblockind'] === 1 )  ) { 
            //PERMANENT COLLECTION
            $newStatus = "<input type=text READONLY value='PERMANENT COLLECTION' id='fldNewStatus.{$sg['segmentid']}' class=inventoryNewStatus>";
        }
        if (strtoupper(substr($sg['assignedto'], 0,3)) === 'INV') { 
            //ASSIGNED
            $newStatus = "<input type=text READONLY value='ASSIGNED' id='fldNewStatus.{$sg['segmentid']}' class=inventoryNewStatus>";
        }
        if ($newStatus === "") { 
            //BANK
            $newStatus = "<input type=text READONLY value='BANKED' id='fldNewStatus.{$sg['segmentid']}' class=inventoryNewStatus>";
        }
        $segList .= "<tr><td style=\"font-size: 1.8vh; font-weight: bold; background: rgba(48,57,71,1); color: rgba(255,255,255,1); width: 2vw; text-align: center; \">{$cntr})</td><td><input type=text READONLY id=\"fldSegId.{$sg['segmentid']}\" value=\"{$val['bgslabel']}\" class=dspInvOverrideSegmentLabel></td><td style=\"font-size: 1.8vh;\">{$sg['prepmethod']}</td><td>{$newStatus}</td><td>{$dspInvMenu}</td></tr>";
      } else { 
          //GIVE ERROR
        $segList .= "<tr><td style=\"font-size: 1.8vh;  font-weight: bold; background: rgba(48,57,71,1); color: rgba(255,255,255,1); width: 2vw; text-align: center;\">{$cntr})</td><td><input type=text READONLY id=\"dspSegId{$sg['segmentid']}\" value=\"{$val['bgslabel']}\" class=dspInvOverrideSegmentLabel></td><td colspan=3 style=\"font-size: 1.8vh; color: rgba(237, 35, 0,1);\"> ONLY '<b>ON OFFER</b>' SEGMENTS CAN BE CHECKED IN USING THIS UTILITY.</td></tr>";
      }
      $cntr++;
    }

    //MAKE DEVIATION MENU AND INVKEY AND GO BUTTON
    $segList .= "</table></form>";   
    $segListTblRtn = "<table border=0><tr><td valign=top>{$segList}</td><td valign=top>{$devTbl}</td></tr></table>";
    
    return $segListTblRtn;
}

function bldQuickPREdit($passeddata) { 

$pdta = $passeddata;
$errorInd = 0;
$errorMsg = "";

  (trim($pdta['pbiosample']) === "" || !is_numeric($pdta['pbiosample'])) ? (list( $errorInd, $msgArr[] ) = array(1 , "DATABASE BIOGROUP ID IS INCORRECT.  SEE A CHTNEASTERN INFORMATICS STAFF MEMBER.")) : "";

if ($errorInd === 0) {
  $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-edit-pathrpt-reasons",serverIdent, serverpw), true);
  $devm = "<table border=0 class=menuDropTbl>";
  foreach ($devarr['DATA'] as $devval) {
    $devm .= "<tr><td onclick=\"fillField('fldDialogPRUPEditReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
  }
  $devm .= "</table>";


  $prTxt = preg_replace('/<p\s?\/?>/i',"\n\n",preg_replace('/<br\s?\/?>/i', "\n", $pdta['pathologyrpt']));


$bg = $pdta['pbiosample'];
$rtnThis = <<<RTNTHIS
<input type=hidden id=fldDialogPRUPLabelNbr value='{$pdta['labelnbr']}'>
<input type=hidden id=fldDialogPRUPBG value='{$pdta['pbiosample']}'>
<input type=hidden id=fldDialogPRUPUser value='{$pdta['user']}'>
<input type=hidden id=fldDialogPRUPSess value='{$pdta['sessionid']}'>
<input type=hidden id=fldDialogPRUPPXI value='{$pdta['pxiid']}'>
<input type=hidden id=fldDialogPRUPPRID value='{$pdta['prid']}'>
<table border=0 id=PRUPHoldTbl cellspacing=0 cellpadding=0>
<tr><td colspan=7 id=VERIFHEAD>INFORMATION VERIFICATION</td></tr>
<tr><td class=lblThis style="width: 8vw;">Biogroup</td><td class=lblThis style="width: 15vw;">Site</td><td class=lblThis style="width: 15vw;">Diagnosis</td><td class=lblThis style="width: 8vw;">Institution</td><td class=lblThis style="width: 10vw;">Procedure Date</td><td class=lblThis style="width: 10vw;">A/R/S</td><td></td></tr>
<tr><td class=dspVerif>{$bg}</td><td class=dspVerif>{$pdta['site']}</td><td class=dspVerif>{$pdta['diagnosis']}</td><td class=dspVerif>{$pdta['procinstitution']}</td><td class=dspVerif>{$pdta['proceduredate']}</td><td class=dspVerif>{$pdta['ars']}</td><td></td></tr>

<tr><td colspan=7 class=headhead>Pathology Report Text</td></tr>
<tr><td colspan=7 style="padding: 0 0 0 .4vw;"><TEXTAREA id=fldDialogPRUPPathRptTxt>{$prTxt}</TEXTAREA></td></tr>
<tr><td colspan=7 style="padding: .4vh .8vw .4vh .8vw;"><input type=checkbox id=HIPAACertify><label for=HIPAACertify id=HIPAABoxLabel>By clicking this box, I ({$pdta['user']}) certify that this Pathology Report Text DOES NOT contain any HIPAA patient identifiers.  This includes:  names, birthdays, addresses, phone numbers, email addresses, physician names, pathology assistance names, technician names, institution names, dates, etc.  I have made myself familiar with the HIPAA identifiers and certify that ALL HIPAA idenitifers have been removed as per CHTNEastern Standard Operating Procedures.  This pathology report is redacted so that the donor/patient cannot be identified.</td></tr>

<tr><td colspan=7><center>
<table><tr><td class=headhead valign=bottom>Override PIN (Inventory PIN)</td><td valign=bottom class=headhead> Reason for Edit </td></tr>
<tr><td style="padding: 0 0 0 .5vw;"><input type=password id=fldUsrPIN style="width: 11vw; font-size: 1.3vh;"></td><td style="padding: 0 0 0 .5vw;"><div class=menuHolderDiv><input type=text id=fldDialogPRUPEditReason style="font-size: 1.3vh; width: 13vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
</table>
</td></tr>

<tr><td colspan=7 align=right style="padding: 0 20vw 0 0;"><table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="editPathologyReportText();"><tr><td style="white-space: nowrap;"><center>Save</td></tr></table></td></tr>

</table>
RTNTHIS;
} else { 
  //ERROR DISPLAY GOES HERE
    $rtnThis = <<<RTNTHIS
{$errorMsg}
RTNTHIS;
}
  return $rtnThis;
}

function bldHelpTicketDialogBox($passedData) { 
    //GET USER 

  //TODO:  MAKE THIS A WEBSERVICE  
  require(serverkeys . "/sspdo.zck");
  $pdta = json_decode($passedData,true);
  $usrSQL = "SELECT originalaccountname, emailaddress, displayname FROM four.sys_userbase where sessionid = :sessid and allowind = 1";
  $usrRS = $conn->prepare($usrSQL);
  $usrRS->execute(array(':sessid' => $pdta['authuser']));
  
  if ($usrRS->rowCount() === 1) { 
  
    $u = $usrRS->fetch(PDO::FETCH_ASSOC);

    $htrarr = json_decode(callrestapi("GET", dataTree . "/global-menu/help-ticket-reasons",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($htrarr['DATA'] as $agval) {
        $agm .= "<tr><td onclick=\"fillField('fldHTR','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
    }
    $agm .= "</table>";
    $htrmnu = "<div class=menuHolderDiv><input type=hidden id=fldHTRValue value=\"{$givendspcode}\"><input type=text id=fldHTR READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddHTR>{$agm}</div></div>";

    $ynarr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-yes-no",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($ynarr['DATA'] as $agval) {
        $agm .= "<tr><td onclick=\"fillField('fldRepYN','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
    }
    $agm .= "</table>";
    $repmnu = "<div class=menuHolderDiv><input type=hidden id=fldRepYNValue value=\"{$givendspcode}\"><input type=text id=fldRepYN READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddRepYN>{$agm}</div></div>";


    $modarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ss-modules-list",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($modarr['DATA'] as $agval) {
        $agm .= "<tr><td onclick=\"fillField('fldMod','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
    }
    $agm .= "</table>";
    $modmnu = "<div class=menuHolderDiv><input type=hidden id=fldModValue value=\"{$givendspcode}\"><input type=text id=fldMod READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddMod>{$agm}</div></div>";






$rtnThis = <<<RTNTHIS
<style>
#instrTbl { width: 40vw; font-size: 1.5vh; text-align: justify; line-height: 1.6em;  }
.dialogLabels { font-size: 1.3vh; font-weight: bold; color: rgba(48,57,71,1); padding: .4vh .4vw; }

#htSubmitter { width: 10vw; font-size: 1.3vh; }
#htSubmitterEmail {  width: 29vw; font-size: 1.3vh; }
#fldMod { width: 14vw; font-size: 1.3vh; }
#ddMod { min-width: 14vw; }
#fldHTR { width: 15vw; font-size: 1.3vh; } 
#ddHTR {min-width: 15vw; }
#fldRepYN { width: 9.5vw; font-size: 1.3vh; }
#ddRepYN { min-width: 9.5vw; }
#htDescription { width: 39vw; height: 15vh; }
.cellholder {padding: 0 .1vw; }
</style>

<table id=instrTbl>
<tr>
   <td class=cellholder style="padding: .8vh .5vw;"><b>Instructions</b>: Fill out the form below to report a bug, an error or request a function be added to ScienceServer.  Each submission will be assigned a ticket number and immediately emailed to a CHTNEAST Informatics Staff person.</td></tr>
</table>

<table cellpadding=0 cellspacing=0 border=0><tr><td class=dialogLabels>Submitter</td><td class=dialogLabels>Submitter's Email</td></tr>
<tr><td class=cellholder><input type=text id=htSubmitter READONLY value="{$u['displayname']} ({$u['originalaccountname']})"></td><td><input type=text READONLY id=htSubmitterEmail value="{$u['emailaddress']}"></td></tr>
</table>

<table cellpadding=0 cellspacing=0 border=0><tr><td class=dialogLabels>Module</td><td class=dialogLabels>Ticket Reason</td><td class=dialogLabels>Can you recreate error?</td></tr>
<tr><td class=cellholder>{$modmnu}</td><td class=cellholder>{$htrmnu}</td><td class=cellholder>{$repmnu}</td></tr>
</table>

<table>
<tr><td class=dialogLabels>Describe Error/Bug/Function Request.  Cut/Paste any relevent error messages etc.</td></tr>
<tr><td class=cellholder><TEXTAREA id=htDescription></TEXTAREA></td></tr>
</table>
<table width=100%>
<tr><td align=right> <table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="submitHelpTicket();"><tr><td style="white-space: nowrap;"><center>Save</td></tr></table> </td></tr>
</table>

RTNTHIS;
  } else { 
    $rtnThis = "USER ERROR!";
  }
  return $rtnThis;
}

function bldCGridControlCalendar($tdydtev, $tdydte) { 
        $fCalendar = buildcalendar('cGridDateControl'); 
        $cGridCalendar = <<<CALENDAR
<div class=menuHolderDiv>
<div class=valueHolder>
    <div class=inputiconcontainer>
    <div class=inputmenuiconholder style="padding: 15px 6px;"><i class="material-icons menuDropIndicator">menu</i></div>
   <input type=hidden id=cGridDateValue value='{$tdydtev}'><input type=text READONLY id=cGridDate class="inputFld" style="width: 18vw;font-size: 1.3vh; " value='{$tdydte}'>
   </div>    
   </div>
<div class=valueDropDown style="width: 18vw;"><div id=cGridCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;

return $cGridCalendar;
}

function bldUsrAllowInstDrop($usr) { 
  $inscnt = 0;
  $insm = "<table border=0 class=menuDropTbl>";
  $igivendspvalue = "";
  $igivendspcode = "";
  foreach ($usr->allowedinstitutions as $inskey => $insval) {
    $instList .= ( $inscnt > 0 ) ? " <br> {$insval[1]} ({$insval[0]}) " : " {$insval[1]} ({$insval[0]}) ";
    $inscnt++;
    if ( trim($usr->presentinstitution) === $insval[0]) {
      $primeinstdsp = $insval[1];
      $igivendspvalue = "{$insval[1]}";
      $igivendspcode = $insval[0];
    }
    $insm .= "<tr><td onclick=\"fillField('presentInst','{$insval[0]}','{$insval[1]}');\" class=ddMenuItem>{$insval[1]} ({$insval[0]})</td></tr>";
  }
  $insm .= "</table>";
  
  $insmnu =  "<input type=hidden id=presentInstValue value=\"{$igivendspcode}\">  "
                              . "<input type=text id=presentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\" style=\"font-size: 1.3vh;\">";
  //$insmnu = "<div class=menuHolderDiv>"
  //                    . "<input type=hidden id=presentInstValue value=\"{$igivendspcode}\">  "
  //                    . "<div class=inputiconcontainer>"
  //                            . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div>"
  //                            . "<input type=text id=presentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\">"
  //                   . "</div>"
  //                   . "<div class=valueDropDown id=ddpresentInst>{$insm}</div>"
  //                   . "</div>";     
    return $insmnu;
}

function bldQuickAddDelinkdialog() { 

    $at = genAppFiles;
    $waitpic = base64file("{$at}/publicobj/graphics/zwait2.gif", "waitgifADD", "gif", true);         
    $fldInitials = "<input type=text id=fldADDPXIInitials maxlength=3>";
    $fldAge = "<input type=text id=fldADDPXIAge value=\"\">";

    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/age-uoms",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
      if ((int)$agval['useasdefault'] === 1 ) { 
          $givendspcode = $agval['lookupvalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldADDAgeUOM','{$agval['lookupvalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $ageuommnu = "<div class=menuHolderDiv><input type=hidden id=fldADDAgeUOMValue value=\"{$givendspcode}\"><input type=text id=fldADDAgeUOM READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddADDAgeUOM>{$agm}</div></div>";

    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-race",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
      if ($agval['menuvalue'] === $donorrace ) { 
          $givendspcode = $agval['codevalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldADDRace','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $racemnu = "<div class=menuHolderDiv><input type=hidden id=fldADDRaceValue value=\"{$givendspcode}\"><input type=text id=fldADDRace READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddADDRace>{$agm}</div></div>";

    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-sex",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
        $agm .= "<tr><td onclick=\"fillField('fldADDSex','{$agval['lookupvalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $sexmnu = "<div class=menuHolderDiv><input type=hidden id=fldADDSexValue value=\"\"><input type=text id=fldADDSex READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown id=ddDNRSex>{$agm}</div></div>";

      $lastFourDsp = "<input type=text id=fldDNRLastFour value=\"\" maxlength=4>";

$rtnThis = <<<RTNTHIS
<div id=divAddDelink style="display: block;">
<table class=ENCHEAD><tr><td style="padding: 0 0 0 0;">ADD DELINKED DONOR</td></tr></table>
<table style="width: 40vw; font-size: 1.5vh; line-height: 1.6em; text-align: justify;padding: 1vh .4vw;"><tr><td>This dialog allows you to add donor information to TODAY's Operative Schedule.  This is a delinked donor - meaning that ScienceServer retains no link to the actual donor's information.  This dialog is primarily used at CHTNEastern Remote Collection Institutions.  If you are at the primary CHTNEast Institution (UPHS), please verify that THIS donor is not in the donor database before adding a delink.</td></tr></table>
<table> 
<tr><td class=DNRLbl2>Initials *</td><td class=DNRLbl2>Age *</td><td class=DNRLbl2>Race *</td><td class=DNRLbl2>Sex *</td><td class=DNRLbl2>Callback Four</td><td rowspan=2 valign=bottom><table class=tblBtn id=btnADDDelink style="width: 6vw;" onclick="saveDelink();"><tr><td style=" font-size: 1.2vh; "><center>Save</td></tr></table></td></tr>
<tr>
  <td>{$fldInitials}</td>
  <td><table><tr><td>{$fldAge}</td><td>{$ageuommnu}</td></tr></table>
  <td>{$racemnu}</td>
  <td>{$sexmnu}</td>
  <td>{$lastFourDsp}</td>
</tr>
<tr><td colspan=6 style="font-size: 1.1vh; font-weight: bold; color: rgba(237, 35, 0,1);">* required field</td></tr>

</table>
</div>
<div id=addDelinkSwirly style="display: none; width: 40vw; font-size: 1.7vh; "><center>
{$waitpic}
<br>
Please wait ... Saving delink donor information
</div>
RTNTHIS;
return $rtnThis;
}

function bldDialogVoidBG ( $passeddata ) { 

  $pdta = json_decode($passeddata, true);
  $errorInd = 0;
  $errorMsg = "";
  //DATA CHECKS GO HERE
   
 
  //DATA CHECKS END HERE

  $ency = cryptservice($pdta['bgselector'],'e',false);
  $payload = json_encode(array("bgency" => $ency));
  $bgdta = json_decode(callrestapi("POST", dataTree . "/data-doers/bg-checks-before-void",serverIdent, serverpw, $payload), true);
  //TODO:  Convert All Error Messages to a Web Service for easy editing
  if ( (int)$bgdta['MESSAGE'] === 404 ) { 
    $rtnThis = <<<ERRORSCREEN
<table id=bgvoiderrortbl><tr><td>The specified biogroup is already voided, locked or too old to edit.<p>If you feel that this is incorrect, please see a CHTN Eastern Informatics staff member.</td></tr></table>
ERRORSCREEN;
    $errorInd = 1;    
  }

  if ($errorInd === 0) {
      //VOID BIOGROUP {$ency} {$bgdta['DATA']['pbiosample']} {$bgdta['DATA']['fromlocation']}
    $voidreasons = json_decode(callrestapi("GET", dataTree . "/globalmenu/bg-pristine-void-reasons",serverIdent,serverpw),true);
    $vreason = "<table border=0 class=menuDropTbl>";
    foreach ($voidreasons['DATA'] as $vreasonval) {
      $vreason .= "<tr><td onclick=\"fillField('fldBGVReason','{$vreasonval['lookupvalue']}','{$vreasonval['menuvalue']}');\" class=ddMenuItem>{$vreasonval['menuvalue']}</td></tr>";
    }
    $vreason .= "</table>";

    $rtnThis = <<<RTNTHIS

    <table border=0><tr><td colspan=2 style="width: 25vw; font-size: 1.3vh; line-height: 1.2em; text-align: justify; padding: 1vh .8vw; box-sizing: border-box;">This will void biogroup number &laquo;{$bgdta['DATA']['pbiosample']}&raquo; and all child segments.  This will take the biogroup out of the normal CHTN work process (i.e. it will NOT appear in the master-record).  You must provide a reason below.</td></tr>  
    <tr><td class=fldLabel>Reason For Void <span class=reqInd>*</span></td></tr> 
    <tr><td><input type=hidden id=fldBGVency value='{$ency}'><div class=menuHolderDiv><input type=hidden id=fldBGVReasonValue><input type=text id=fldBGVReason READONLY class="inputFld" style="width: 25vw;"><div class=valueDropDown style="min-width: 25vw;">{$vreason}</div></div></td></tr>
    <tr><td class=fldLabel>Further Explanation</td></tr>
    <tr><td><TEXTAREA id=fldBGVText style="width: 25vw; box-sizing: border-box; padding: 10px; font-size: 1.3vh;height: 15vh; "></textarea></td></tr>
    <tr><td align=right>  <table class=tblBtn id=btnSaveSeg style="width: 6vw;" onclick="doBGVoid();"><tr><td style=" font-size: 1.1vh;"><center>Void {$bgdta['DATA']['pbiosample']}</td></tr></table> </td></tr>
    </table>


RTNTHIS;
}

return $rtnThis;

}


function bldDialogAddSegment( $passeddata ) { 

  $pdta = json_decode($passeddata, true);
  $errorInd = 0;
  $errorMsg = "";
  //DATA CHECKS GO HERE

if ($errorInd === 0) {
  $si = serverIdent;
  $sp = serverpw;
  $pdta['selector'] = $pdta['bgrecordselector'];
  $encySelector = cryptservice($pdta['selector'],'e',false);
  $payload = json_encode($pdta);
  $bgdta = json_decode(callrestapi("POST", dataTree . "/data-doers/get-procurement-biogroup",serverIdent, serverpw, $payload), true);
  $bg = $bgdta['DATA'];
  $jsonbg = json_encode($bg);
  //$proctypearr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-menu-prc-proceduretype",$si,$sp),true);

  $dxd = ( trim($bg['bgnbr']) !== "" ) ? "[{$bg['bgnbr']}]" : "";
  $dxd .= ( trim($bg['bgdxspeccat']) !== "" ) ? " {$bg['bgdxspeccat']}" : "";
  $dxd .= ( trim($bg['bgdxasite']) !== "" ) ? " {$bg['bgdxasite']}" : "";
  $dxd .= ( trim($bg['bgdxssite']) !== "" ) ? " ({$bg['bgdxssite']})" : "";
  $dxd .= ( trim($bg['bgdxdx']) !== "" ) ? " / {$bg['bgdxdx']}" : "";
  $dxd .= ( trim($bg['bgdxmets']) !== "" ) ? " (METS: {$bg['bgdxmets']})" : "";
  $dxd .= " <input type=hidden id=fldSEGSegmentBGSelectorId value=\"{$encySelector}\"> ";

  $preparr = json_decode(callrestapi("GET", dataTree . "/globalmenu/all-preparation-methods",$si,$sp),true);
  $prp = "<table border=0 class=menuDropTbl>";
    //<tr><td align=right onclick=\"fillField('qryPreparationMethod','','');updatePrepmenu('');\" class=ddMenuClearOption>[clear]</td></tr>
  foreach ($preparr['DATA'] as $prpval) {
    $prp .= "<tr><td onclick=\"fillField('fldSEGPreparationMethod','{$prpval['lookupvalue']}','{$prpval['menuvalue']}');updatePrepmenu('{$prpval['lookupvalue']}');updatePrepAddDisplay('{$prpval['lookupvalue']}');\" class=ddMenuItem>{$prpval['menuvalue']}</td></tr>";
  }
  $prp .= "</table>";
  
  $prepconarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/preparation-containers",$si,$sp),true);
  $prpcon = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldSEGPreparationContainer','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($prepconarr['DATA'] as $prpconval) {
    $prpcon .= "<tr><td onclick=\"fillField('fldSEGPreparationContainer','{$prpconval['lookupvalue']}','{$prpconval['menuvalue']}');\" class=ddMenuItem>{$prpconval['menuvalue']}</td></tr>";
  }
  $prpcon .= "</table>";

  $metarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/metric-uoms-long",$si,$sp),true);
  $met = "<table border=0 class=menuDropTbl>";
  foreach ($metarr['DATA'] as $metval) {
    $met .= "<tr><td onclick=\"fillField('fldSEGAddMetricUOM','{$metval['lookupvalue']}','{$metval['menuvalue']}');\" class=ddMenuItem>{$metval['menuvalue']}</td></tr>";
  }
  $met .= "</table>";

$rtnThis = <<<RTNTHIS

<div id=divSegmentAddHolder>
<table border=0>
<tr><td id=segBGDXD>{$dxd}</td></tr>
</table> 

<table border=0>
<tr>
  <td class=prcFldLbl>Hours Post <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Metric <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Preparation Method <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Preparation <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Container</td></tr>
<tr>
   <td><input type=text id=fldSEGAddHP></td>
   <td><table><tr><td><input type=text id=fldSEGAddMetric></td><td><div class=menuHolderDiv><input type=hidden id=fldSEGAddMetricUOMValue><input type=text id=fldSEGAddMetricUOM READONLY class="inputFld" style="width: 8vw;"><div class=valueDropDown style="min-width: 8vw;">{$met}</div></div></td></tr></table>  </td>
   <td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationMethodValue><input type=text id=fldSEGPreparationMethod READONLY class="inputFld" style="width: 10vw;"><div class=valueDropDown style="min-width: 10vw;">{$prp}</div></div></td>
   <td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationValue><input type=text id=fldSEGPreparation READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 20vw;" id=ddSEGPreparationDropDown><center>(Select a Preparation Method)</div></div></td><td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationContainerValue><input type=text id=fldSEGPreparationContainer READONLY class="inputFld" style="width: 10vw;"><div class=valueDropDown style="min-width: 10vw;">{$prpcon}</div></div></td></tr>
</table>      

<table width=100% border=0>
<tr>
  <td><div id=preparationAdditions>Prep Additions</div></td>
</tr>
</table>


<table border=0 id=assignTbl>
  <tr>
   <td class=prcFldLbl>Assignment <span class=reqInd>*</span></td>
   <td class=prcFldLbl>Request #</td>
   <td rowspan=2 valign=bottom style="padding: 0;"><table class=tblBtn id=btnSaveSeg onclick="markAsBank();" style="width: 6vw;"><tr><td style=" font-size: 1.1vh;"><center>Bank</td></tr></table></td>
  </tr>
  <tr>
    <td valign=top>
         <div class=suggestionHolder>
          <input type=text id=fldSEGselectorAssignInv class="inputFld" onkeyup="selectorInvestigator(); byId('fldSEGselectorAssignReq').value = '';byId('requestDropDown').innerHTML = ''; ">
          <div id=assignInvestSuggestion class=suggestionDisplay>&nbsp;</div>
         </div>
    </td>
    <td valign=top>
        <div class=menuHolderDiv onmouseover="byId('assignInvestSuggestion').style.display = 'none'; setAssignsRequests();">
        <input type=text id=fldSEGselectorAssignReq READONLY class="inputFld">
        <div class=valueDropDown id=requestDropDown style="min-width: 10vw;"></div>
        </div>
    </td>
   </tr>
   <tr><td colspan=3 id=noteBlock>(SLIDE ONLY: Enter 'QC' in Assignment for HPR Slides that don't have matching HPR FFPEs)</td></tr>
</table>

<table border=0 width=80%>
<tr><td class=prcFldLbl>Segment Comments</td></tr>   
<tr><td><TEXTAREA id=fldSEGSGComments></TEXTAREA></td></tr>   
</table>
   
   
<table width=100%>
<tr><td align=right>

  <table cellspacing=0 cellpadding=0 border=0><tr>
    <td><table class=tblBtn id=btnADDQMSSegs style="width: 6vw;" onclick="addQMSSegments();"><tr><td style=" font-size: 1.1vh; "><center>Add QMS</td></tr></table></td>
    <td><table class=tblBtn id=btnSaveSeg style="width: 6vw;" onclick="addDefinedSegment();"><tr><td style=" font-size: 1.1vh;"><center>Save Segment</td></tr></table></td>
  </tr></table>

</td></tr>
</table>
</div>

RTNTHIS;

  }

  return $rtnThis;

//<td><table class=tblBtn id=btnADDNoQMS onclick="displayNOQMSReason();" style="width: 6vw;"><tr><td style=" font-size: 1.1vh;"><center>No QMS</td></tr></table></td>
  //<td class=prcFldLbl>Cut from Block (Slides Only)</td><td><input type=text id=fldSEGCutFrom></td>
  //<table border=1>
//<tr> 
//    <td class=prcFldLbl>Additives</td>
//</tr>
//<tr>
//    
//    <td>/ADDITIVES/</td>
//</tr>
//</table>

}



function bldQuickPRUpload($passeddata) { 
    
$pdta = $passeddata;
$errorInd = 0;
$errorMsg = "";

if (trim($pdta['pbiosample']) === "" || !is_numeric($pdta['pbiosample'])) {  $errorInd = 1; $errorMsg .= "##Database Biogroup ID is incorrect.  See a CHTNEastern Informatics Staff Member"; }  
if ( $pdta['pathreportind'] !== 2 ) {  $errorInd = 1; $errorMsg .= "##This biogroup is already marked as having a Pathology Report.  You should 'Edit' instead of uploading"; }  
if ( $pdta['pathreportid'] !== 0 ) {  $errorInd = 1; $errorMsg .= "##This biogroup is already marked as having a Pathology Report.  You should 'Edit' instead of uploading"; } 

if ($errorInd === 0) {
  $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-menu-pathology-report-upload",serverIdent, serverpw), true);
  $devm = "<table border=0 class=menuDropTbl>";
  foreach ($devarr['DATA'] as $devval) {
    $devm .= "<tr><td onclick=\"fillField('fldDialogPRUPDeviationReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
  }
  $devm .= "</table>";

$bg = $pdta['pbiosample'];
$rtnThis = <<<RTNTHIS
<input type=hidden id=fldDialogPRUPLabelNbr value='{$pdta['labelnbr']}'>
<input type=hidden id=fldDialogPRUPBG value='{$pdta['pbiosample']}'>
<input type=hidden id=fldDialogPRUPUser value='{$pdta['user']}'>
<input type=hidden id=fldDialogPRUPSess value='{$pdta['sessionid']}'>
<input type=hidden id=fldDialogPRUPPXI value='{$pdta['pxiid']}'>
<table border=0 id=PRUPHoldTbl cellspacing=0 cellpadding=0>
<tr><td colspan=7 id=VERIFHEAD>INFORMATION VERIFICATION</td></tr>
<tr><td class=lblThis style="width: 8vw;">Biogroup</td><td class=lblThis style="width: 15vw;">Site</td><td class=lblThis style="width: 15vw;">Diagnosis</td><td class=lblThis style="width: 8vw;">Institution</td><td class=lblThis style="width: 10vw;">Procedure Date</td><td class=lblThis style="width: 10vw;">A/R/S</td><td></td></tr>
<tr><td class=dspVerif>{$bg}</td><td class=dspVerif>{$pdta['site']}</td><td class=dspVerif>{$pdta['diagnosis']}</td><td class=dspVerif>{$pdta['procinstitution']}</td><td class=dspVerif>{$pdta['proceduredate']}</td><td class=dspVerif>{$pdta['ars']}</td><td></td></tr>

<tr><td colspan=7 class=headhead>Pathology Report Text</td></tr>
<tr><td colspan=7 style="padding: 0 0 0 .4vw;"><TEXTAREA id=fldDialogPRUPPathRptTxt></TEXTAREA></td></tr>
<tr><td colspan=7 style="padding: .4vh .8vw .4vh .8vw;"><input type=checkbox id=HIPAACertify><label for=HIPAACertify id=HIPAABoxLabel>By clicking this box, I ({$pdta['user']}) certify that this Pathology Report Text DOES NOT contain any HIPAA patient identifiers.  This includes:  names, birthdays, addresses, phone numbers, email addresses, physician names, pathology assistance names, technician names, institution names, dates, etc.  I have made myself familiar with the HIPAA identifiers and certify that ALL HIPAA idenitifers have been removed as per CHTNEastern Standard Operating Procedures.  This pathology report is redacted so that the donor/patient cannot be identified.</td></tr>

<tr><td colspan=7><center>
<table><tr><td class=headhead valign=bottom>Override PIN (Inventory PIN)</td><td style="width: 12vw;" valign=bottom> <div class="ttholder headhead">SOP DEVIATION (?)<div class=tt style="width: 25vw;">This is NOT a standard operational screen and should only be used in extenuating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures.<br>Please enter a reason for the deviation below.</div></div>   </td></tr>
<tr><td style="padding: 0 0 0 .5vw;"><input type=password id=fldUsrPIN style="width: 11vw; font-size: 1.3vh;"></td><td style="padding: 0 0 0 .5vw;"><div class=menuHolderDiv><input type=text id=fldDialogPRUPDeviationReason style="font-size: 1.3vh; width: 13vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
</table>
</td></tr>

<tr><td colspan=7 align=right style="padding: 0 20vw 0 0;"><table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="uploadPathologyReportText();"><tr><td style="white-space: nowrap;"><center>Upload</td></tr></table></td></tr>

</table>
RTNTHIS;
} else { 
  //ERROR DISPLAY GOES HERE
    $rtnThis = <<<RTNTHIS
{$errorMsg}
RTNTHIS;
}
  return $rtnThis;
}

function bldQuickEditDonor($passeddata) { 
    
  $pdta = array();  
  $pdta['donorid'] = cryptservice($passeddata['phicode'],'e');
  $pdta['presentinstitution'] = $passeddata['presentinstitution'];
  $pdta['sessionid'] = $passeddata['sessionid'];
  
  $passdata = json_encode($pdta); 
  $at = genAppFiles;
  $doarr = json_decode( callrestapi("POST",dataTree."/data-doers/anon-donor-object",serverIdent,serverpw,$passdata), true );
  if ((int)$doarr['ITEMSFOUND'] === 1) { 
    //{"MESSAGE":"","ITEMSFOUND":1,"DATA":{"pxicode":"311fd9c5-ff5e-4fcf-ae68-b49a9659bcaa","listdate":"05\/07\/2018","location":"HUP","locationname":"Hospital of The University of Pennsylvania","starttime":"2:30","surgeons":"GARCIA, FERMIN","donorinitials":"A.B","lastfour":"9228","donorage":"61","ageuom":"Years","donorrace":"-","donorsex":"M","proctext":"M1 ELECTROPHYSIOLOGIC EVALUATION TRANSEPTAL W TREATMENT OF ATRIAL FIBRILLATION BY PULMONARY VEIN ISOLATIONLRB NA","targetind":"-","informedind":0,"linkeddonor":"0","delinkeddonor":"0"}} 
    $pxicode = $doarr['DATA']['pxicode'];
    $ORDate = $doarr['DATA']['listdate'];
    $locationdsp = $doarr['DATA']['locationname'];
    $location = $doarr['DATA']['location'];
    $starttime = $doarr['DATA']['starttime'];
    $surgeons = $doarr['DATA']['surgeons'];
    $donorinitials = $doarr['DATA']['donorinitials'];
    $lastfour = $doarr['DATA']['lastfour'];
    $donorage = $doarr['DATA']['donorage'];
    $donorageuom = $doarr['DATA']['ageuom'];
    $donorrace = $doarr['DATA']['donorrace'];
    $donorsex = $doarr['DATA']['donorsex'];
    $proceduretext = $doarr['DATA']['proctext'];
    $targetind = $doarr['DATA']['targetind'];
    $targetdsp = $doarr['DATA']['targetdsp'];
    $informedconsent = $doarr['DATA']['informedind'];
    $informedconsentdsp = $doarr['DATA']['informeddsp'];
    $subjectnbr = $doarr['DATA']['studysubjectnbr'];
    $protocolnbr = $doarr['DATA']['studyprotocolnbr'];
    $cx = trim($doarr['DATA']['cx']);
    $rx = trim($doarr['DATA']['rx']);
    $sogi = trim($doarr['DATA']['sogi']);
    $sogival = $doarr['DATA']['studyprotocolnbr'];
    
    $linkeddonor = ((int)$doarr['DATA']['linkeddonor'] === 1) ? 1  : 0;
    $delinkeddonor = ((int)$doarr['DATA']['delinkeddonor'] === 1) ? 1 : 0;
    $delinkedby = trim($doarr['DATA']['delinkeddonorby']);
    //{"notetext":"And another note goes here","bywho":"proczack","enteredon":"01\/04\/2019 08:32"},{"notetext":"Case Note: This is a test case Note","bywho":"proczack","enteredon":"01\/04\/2019 08:31"}]
    $casenotes = $doarr['DATA']['casenotes'];

    //TODO: Check dynamically WHAT THE RECEIVED VALUE IS
    if ($targetind === 'R') { 
      $targetmnu = "[{$targetdsp}]";
    } else { 
      //TODO:  WRITE SCRIPT TO CONVERT ALL TARGETS NIGHTLY TO 'NOT RECEIVED'
      $trgarr = json_decode(callrestapi("GET", dataTree . "/global-menu/allowed-technician-assignable-donor-targets",serverIdent, serverpw), true);
      $trgm = "<table border=0 class=menuDropTbl>";
      $givendspvalue = "";
      $givendspcode = "";
      foreach ($trgarr['DATA'] as $trgval) {
        if ( $trgval['codevalue'] === $targetind ) { 
          $givendspcode = $trgval['codevalue'];
          $givendspvalue = $trgval['menuvalue'];
        }
        $trgm .= "<tr><td onclick=\"fillField('fldDNRTarget','{$trgval['codevalue']}','{$trgval['menuvalue']}');\" class=ddMenuItem>{$trgval['menuvalue']}</td></tr>";
      }
      $trgm .= "</table>";
      $targetmnu = "<div class=menuHolderDiv><input type=hidden id=fldDNRTargetValue value=\"{$givendspcode}\"><input type=text id=fldDNRTarget READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddDNRTargetValue>{$trgm}</div></div>";
    }

    //INFORMED CONSENT 
    //TODO: Check dynamically WHAT THE RECEIVED VALUE IS
    if ((int)$informedconsent === 2) { 
      $infcmnu = $informedconsentdsp;
    } else { 
      $icarr = json_decode(callrestapi("GET", dataTree . "/global-menu/allowed-assignable-informed-consent",serverIdent, serverpw), true);
      $icm = "<table border=0 class=menuDropTbl>";
      $givenicdspvalue = "";
      $givenicdspcode = "";
      foreach ($icarr['DATA'] as $icval) {
        if ((int)$icval['codevalue'] === (int)$informedconsent ) { 
          $givenicdspcode = $icval['codevalue'];
          $givenicdspvalue = $icval['menuvalue'];
        }
        $icm .= "<tr><td onclick=\"fillField('fldDNRInformedConsent','{$icval['codevalue']}','{$icval['menuvalue']}');\" class=ddMenuItem>{$icval['menuvalue']}</td></tr>";
      }
      $icm .= "</table>";
      $infcmnu = "<div class=menuHolderDiv><input type=hidden id=fldDNRInformedConsentValue value=\"{$givenicdspcode}\"><input type=text id=fldDNRInformedConsent READONLY class=\"inputFld\" value=\"{$givenicdspvalue}\"><div class=valueDropDown id=ddDNRInformedConsent>{$icm}</div></div>";
    }

    //TODO:  MAKE A MENU BUILDER FUNCTION THAT WILL BUILD ALL SCIENCESERVER DROP DOWNS
    $fldAge = "<input type=text id=fldDNRAge value=\"{$donorage}\">";
    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/age-uoms",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
      if ((int)$agval['useasdefault'] === 1 ) { 
          $givendspcode = $agval['codevalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldDNRAgeUOM','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $ageuommnu = "<div class=menuHolderDiv><input type=hidden id=fldDNRAgeUOMValue value=\"{$givendspcode}\"><input type=text id=fldDNRAgeUOM READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddDNRAgeUOM>{$agm}</div></div>";

    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-race",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    $dfltrce = "";
    $dfltrceval = "";
    foreach ($agarr['DATA'] as $agval) {
        if ($agval['menuvalue'] === $donorrace ) { 
          $givendspcode = $agval['codevalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        if ((int)$agval['useasdefault'] === 1) { 
            $dfltrce = $agval['menuvalue'];
            $dfltrceval = $agval['codevalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldDNRRace','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      if ($givendspcode === "" && $dfltrce !== "") { 
          $givendspcode = $dfltrceval;
          $givendspvalue = $dfltrce;          
      }
      $agm .= "</table>";
      $racemnu = "<div class=menuHolderDiv><input type=hidden id=fldDNRRaceValue value=\"{$givendspcode}\"><input type=text id=fldDNRRace READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddDNRRace>{$agm}</div></div>";

    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-sex",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
      if ( substr(trim($agval['codevalue']),0,1) === $donorsex || trim($agval['codevalue']) === $donorsex ) { 
          $givendspcode = $agval['codevalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldDNRSex','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $sexmnu = "<div class=menuHolderDiv><input type=hidden id=fldDNRSexValue value=\"{$givendspcode}\"><input type=text id=fldDNRSex READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddDNRSex>{$agm}</div></div>";


    $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/encounter-note-types",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($agarr['DATA'] as $agval) {
      if ( (int)$agval['useasdefault'] === 1 ) { 
          $givendspcode = $agval['codevalue'];
          $givendspvalue = $agval['menuvalue'];
        }
        $agm .= "<tr><td onclick=\"fillField('fldDNREncNotesType','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
      }
      $agm .= "</table>";
      $enctypemnu = "<div class=menuHolderDiv><input type=hidden id=fldDNREncNotesTypeValue value=\"{$givendspcode}\"><input type=text id=fldDNREncNotesType READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddDNREncNotesType>{$agm}</div></div>";

      $lFourVal = substr($lastfour,0,4);
      $lastFourDsp = "<input type=text id=fldDNRLastFour value=\"$lFourVal\" maxlength=4>";

      $caseNotesTbl = "<table border=0 cellspacing=0 cellpadding=0>"; 
      foreach ($casenotes as $key => $csval) {
        $lineTbl = "<table class=noteTextLineTbl border=0 cellpadding=0 cellspacing=0><tr><td class=noteTypeLine>{$csval['notetype']}</td></tr><tr><td class=noteTextLineText>{$csval['notetext']}</td></tr>";
        $lineTbl .= "<tr><td class=noteTextLineEntry>{$csval['enteredon']} :: {$csval['bywho']}</td></tr></table>";
        $caseNotesTbl .= "<tr><td>{$lineTbl}</td></tr>";
      }
      $caseNotesTbl .= "</table>";
      $encyEID = cryptservice($passeddata['phicode'],'e');
      $sessid = $passeddata['sessionid'];
      $presentinstitution = $passeddata['presentinstitution'];

      //SOGI
      $sogiData = dropmenuPennSOGI($sogi);
      $sogimenu = $sogiData['menuObj'];
      //CHEMO MENU
      $cxData = dropmenuCXIndicator($cx); 
      $cxmenu = $cxData['menuObj'];
      //RAD MENU
      $rxData = dropmenuRXIndicator($rx); 
      $rxmenu = $rxData['menuObj'];

      $waitpic = base64file("{$at}/publicobj/graphics/zwait2.gif", "waitgif", "gif", true);         
      $rtnThis = <<<RTNTHIS
              
<div id=displayEncounterDiv>              
<input type=hidden id=fldDNReid value="{$encyEID}">
<input type=hidden id=fldDNRSess value="{$sessid}">
<input type=hidden id=fldDNRPresInst value="{$presentinstitution}">

<table class=ENCHEAD><tr><td style="padding: 0 0 0 0;">ENCOUNTER</td></tr></table>

<table></table>

<table>
       <tr><td class=DNRLbl>Encounter ID: </td><td class=DNRDta>{$pxicode} </td><td class=DNRLbl>Institution: </td><td class=DNRDta>{$locationdsp} ({$location})</td></tr>
       <tr><td class=DNRLbl>Procedure Date: </td><td class=DNRDta>{$ORDate} @ {$starttime}</td><td class=DNRLbl>Surgeon: </td><td class=DNRDta>{$surgeons} </td></tr>
</table>

<table><tr><td class=DNRLbl>Procedure Description</td></tr><tr><td class=procedureTextDsp>{$proceduretext} </td></tr></table>

<table class=ENCHEAD><tr><td style="padding: .8vh 0 0 0;">DONOR SPECIFICS</td></tr></table>
<table border=0>
  <tr>
    <td class=DNRLbl2>Target</td>
    <td class=DNRLbl2>Informed Consent</td>
    <td class=DNRLbl2>Age</td>
    <td class=DNRLbl2>Race</td>
    <td class=DNRLbl2>Sex</td>
    <td class=DNRLbl2>Callback Ref</td>
  </tr>
<tr>
  <td> {$targetmnu} </td>
  <td> {$infcmnu} </td>
  <td><table><tr><td>{$fldAge}</td><td>{$ageuommnu}</td></tr></table></td>
  <td>{$racemnu}</td>
  <td>{$sexmnu}</td>
  <td>{$lastFourDsp}</td>
</tr>
<tr>
<td colspan=2 class=DNRLbl2>Study Subject #</td>
<td class=DNRLbl2>Study Protocol #</td>
<td class=DNRLbl2>UPenn SOGI</td>
<td class=DNRLbl2>Chemo</td>
<td class=DNRLbl2>Rad</td>
</tr>
<tr>
<td colspan=2><input type=text id=fldADDSubjectNbr value="{$subjectnbr}"></td>
<td><input type=text id=fldADDProtocolNbr value="{$protocolnbr}"></td>
<td>{$sogimenu}</td>
<td>{$cxmenu}</td>
<td>{$rxmenu}</td>
</tr>

<tr><td colspan=6> 
<div id=notRcvdNoteDsp>
<table><tr><td class=DNRLbl2>Not Received Reason</td></tr><tr><td><input type=text id=fldDNRNotReceivedNote></td></tr></table>
</div>
</td></tr>
<tr><td colspan=6 align=right>  <table class=tblBtn id=btnDNRSaveEncounter style="width: 6vw;" onclick="saveDonorSpecifics();"><tr><td><center>Save</td></tr></table>  </td></tr>    
</table>


<table class=ENCHEAD><tr><td style="padding: 3vh 0 0 0;">NOTES</td></tr></table>
<table>
<tr><td><table style="width: 41vw;" border=0><tr><td class=DNRLbl2>Encounter Note</td><td class=DNRLbl2>Encounter Note Type</td> <td align=right rowspan=2 valign=bottom> <table class=tblBtn id=btnDNRSaveEncounterNote style="width: 6vw;" onclick="saveEncounterNote();"><tr><td><center>Save Note</td></tr></table> </td> </tr><td><input type=text id=fldDNREncounterNote></td><td>{$enctypemnu}</td></tr></table></td></tr>
<tr><td class=DNRLbl2>Encounter Notes</td></tr><tr><td><div id=displayPreviousCaseNotes>{$caseNotesTbl}</div></td></tr>
</table>

<!-- <table>
<tr><td>Linked: </td><td> &nbsp;  </td></tr>
<tr><td>De-Linked By: </td><td> {$delinkedby}&nbsp; </td></tr>
</table> //-->
</div>
<div id=waitIcon><center>
{$waitpic}
<div id=waitinstruction></div>
   </div>
RTNTHIS;


  } else { 
    //BUILD ERROR
    $rtnThis = <<<RTNTHIS
ERROR: NO DONOR RECORD FOUND.  SEE A CHTNEAST INFORMATICS STAFF MEMBER
RTNTHIS;
  } 
   
return $rtnThis;
}

function bldORScheduleTbl($orarray) { 

  $institution = $orarray['DATA']['institution'];
  $ordate = $orarray['DATA']['requestDate'];

  foreach ($orarray['DATA']['orlisting'] as $ky => $val) { 
    $target = $val['targetind'];
    switch ($target) { 
      case 'T':
          $target = "<i class=\"material-icons targeticon\">check_box_outline_blank</i>";
          $targetBck = "targetwatch";
          break;
      case 'R':    
          $target = "<i class=\"material-icons targeticon\">check_box</i>";
          $targetBck = "targetrcvd";
          break;
      case 'N':    
          $target = "<i class=\"material-icons targeticon\">indeterminate_check_box</i>";
          $targetBck = "targetnot";
          break;
      default:
          $target = "-";
          $targetBck = "";
    }

    $informed = $val['informedconsentindicator'];
    switch ((int)$informed) { 
      case 1: //NO
          $icicon = "N";
          break;
      case 2: //YES
          $icicon = "Y";
          break;
      case 3:  //PENDING
          $icicon = "P";
          break;
      default:
          $icicon = "";
    } 
    $addeddonor = ($val['linkage'] === "X") ? "<i class=\"material-icons addicon\">input</i>" : "";
    $lastfour = $val['lastfourmrn'];
    $prace = (trim($val['pxirace']) === "-") ? "" : trim($val['pxirace']);
    $roomdsp = ( trim($val['room']) !== "") ? " in OR {$val['room']}" : "";
    $studyNbrLineDsp = ( trim($val['studysubjectnbr']) !== "" || trim($val['studyprotocolnbr']) !== "") ? "<tr><td valign=top class=smallORTblLabel>Subject/Protocol </td><td valign=top>{$val['studysubjectnbr']} :: {$val['studyprotocolnbr']}</td></tr>" : "";
    $cxrxDsp = ( trim($val['cx']) !== "" || trim($val['rx']) !== "") ? "<tr><td valign=top class=smallORTblLabel>CX/RX </td><td valign=top>" . strtoupper(substr($val['cx'],0,1)) . "::" .  strtoupper(substr($val['rx'],0,1)) . "</td></tr>" : "";
    
    $proc = <<<PROCCELL
<table class=procedureSpellOutTbl border=0>
  <tr><td valign=top class=smallORTblLabel>A-R-S::Callback</td><td valign=top>{$val['ars']} :: {$lastfour}</td></tr>
  {$studyNbrLineDsp}
  {$cxrxDsp}
  <tr><td valign=top class=smallORTblLabel>Procedure</td><td valign=top class=procTxtDsp>{$val['proceduretext']}</td></tr>
  <tr><td valign=top class=smallORTblLabel>Surgeon</td><td valign=top>{$val['surgeon']}</td></tr>
  <tr><td valign=top class=smallORTblLabel>Start Time</td><td valign=top>{$val['starttime']} {$roomdsp}</td></tr>
  <tr><td colspan=2><div class=btnEditPHIRecord onclick="editPHIRecord(event,'{$val['pxicode']}');">Edit Donor Record</div></td></tr>
</table>
PROCCELL;
    $ageuom = "yrs";
    //oncontextmenu=\"return false;\"
    $innerTbl .= <<<ORLINE
    <tr  onclick="fillPXIInformation('{$val['pxicode']}','{$val['pxiinitials']}','{$val['pxiage']}','{$ageuom}','{$prace}','{$val['pxisex']}','{$informed}','{$lastfour}','{$val['studysubjectnbr']}', '{$val['studyprotocolnbr']}','{$val['cx']}','{$val['rx']}','{$val['sogi']}');" class=displayRows>
      <td valign=top class=dspORInitials>{$val['pxiinitials']}</td>
      <td valign=top class="dspORTarget {$targetBck}">{$target}</td>
      <td valign=top class=dspORInformed>{$icicon}</td>
      <td valign=top class=dspORAdded>{$addeddonor}</td>
      <td valign=top class=dspProcCell> {$proc} </td>
    </tr>
ORLINE;
  }

  $rtnTbl = <<<ORSCHEDTBL
          <div id=divORHolder>

           <div id=headerTbl>
           <table border=0 id=PXIDspTblHD>
              <thead><tr>
                       <th class=dspORInitials>INI</th>
                       <th class=dspORTarget>TRG</th>
                       <th class=dspORInformed>IC</th>
                       <th class=dspORAdded>ADD</th>
                       <th class=dspProcCell>PROCEDURE</th>
                     </tr></thead>
           </table>
           </div>

          <div id=dataPart>
          <table id=procDataDsp>
               <tbody id=PXIDspBody>
                {$innerTbl}
               </tbody>
          </table>
          </div>

          </div>
ORSCHEDTBL;
return $rtnTbl;

}

function bldProcurementGridEdit( $usr, $selector ) { 
    
  $pdta['selector'] = $selector;
  $payload = json_encode($pdta);
  $bgdta = json_decode(callrestapi("POST", dataTree . "/data-doers/get-procurement-biogroup",serverIdent, serverpw, $payload), true);
  $bg = $bgdta['DATA'];

if ( $usr->presentinstitution !== $bg['bgfromlocationcode'] || $usr->allowprocure !== 1) {
    $rtnTbl['grid'] = "<h1>USER NOT ALLOWED (PRIMARY INSTITUTION DOESN'T MATCH OR NOT ALLOWED PROCUREMENT)";
      $rtnTbl['sidebar'] = "";
}  else { 
  
  $sgdta = json_decode(callrestapi("POST", dataTree . "/data-doers/get-procurement-segment-list-table",serverIdent, serverpw, $payload), true);
  $jsonbg = json_encode($bg);
  $insmnu = "<input type=hidden id=fldPRCPresentInstValue value=\"{$bg['bgfromlocationcode']}\"><input type=text id=fldPRCPresentInst READONLY class=\"inputFld lockfield\" value=\"{$bg['bgfromlocation']}\">";
$tday = $bg['bgprocurementdate'];
//DROP MENU BUILDER ********************************************************************** //
  //Initial UOM Menu
    $muomIData = dropmenuInitialMetric( $bg['bginitialmetricuomvalue'] ); 
    $muommenu = $muomIData['menuObj'];
  //SPECIMEN CATEGORY
    //$spcData = dropmenuInitialSpecCat( $bg['bgdxspeccat'] );
    //$spcmenu = $spcData['menuObj'];
  //PATHOLOGY REPORT
    //$prptData = dropmenuPathRptAllowables();
    //$prptmenu = $prptData['menuObj'];  
  //UNINVOLVED SAMPLE
    //$univData = dropmenuUninvolvedIndicator();
    //$uninvmenu = $univData['menuObj'];
  //SITE POSITIONS
    //$asiteposData = dropmenuVocASitePositions(); 
    //$aspmenu = $asiteposData['menuObj'];
  //SYSTEMIC LIST 
    //$sysData = dropmenuSystemicDXListing(); 
    //$sysdxmenu = $sysData['menuObj'];

  //BASE SITE-SUBSITE MENU
    //$sitesubsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSite READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>"; 

   //$subsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSSite READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCSSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>";
   
   //BASE DX-MOD Menu
     //$dxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCDXModValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCDXMod READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCDXMod><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category & Site)</div></div></div>";

   //METASTATIC SITE MENU DROPDOWN
     //$metsData = dropmenuMetsMalignant();
     //$metssite = $metsData['menuObj'];
     //$metsdxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCMETSDXValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCMETSDX READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCMETSDX><center><div style=\"font-size: 1.4vh\">(Choose a Metastatic site)</div></div></div>";

     $dxover = ( $bg['bgdxoverride'] === 0 ) ? "" : " CHECKED ";

//DROP MENU BUILDER END ******************************************************************//

$tech = $bg['bgproctech'];
$giveSelector = $selector;

//BUILD SEGMENT TABLE


//END BUILD SEGMENT TABLE

//{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"bgnbr":85052,"bgmigratedind":0,"bgmigratedon":"","bgrecordstatus":2,"bgfromlocationcode":"HUP","bgvoidind":0,"bgvoidreason":""}}
//OPEN DIV &#9871; // CLOSED DIV &#9870;


  $rtnTbl['grid'] = <<<GRIDTBL

<table border=0 cellpadding=0 cellspacing=0 class=procGridHoldingTable >
<tr><td>

<table border=0 cellspacing=0 cellpadding=0 id=bgProcSideBarDspTbl>
<tr><td id=dspBGN>Biogroup: {$bg['bgnbr']} <input type=hidden id=bgSelectorCode value="{$giveSelector}"></td></tr>
</table>

</td></tr>

<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader><span class=openDivIndicator id=openDivIndBSMetrics onclick="hideProcGridLine('BSGridLineSampleMetrics',this.id);">&#9870;</span>Biogroup Sample Metrics</td></tr>
  <tr><td class=procGridHoldingLine id=BSGridLineSampleMetrics style="display: none;">
   <table border=0 cellspacing=10>
     <tr><td class=prcFldLbl>Procuring Institution</td><td class=prcFldLbl>Procurement Date </td><td class=prcFldLbl>Procedure Type</td><td class=prcFldLbl>Collection Type</td><td class=prcFldLbl>Technician</td><td class=prcFldLbl>Initial Metric <span class=reqInd>*</span></td><td>&nbsp;</td></tr>
     <tr>      
       <td><input type=hidden id=fldPRCBGNbr value={$bg['bgnbr']} READONLY>{$insmnu}</td> 
       <td><input type=text readonly id=fldPRCProcDate class=lockfield value="{$tday}"></td>
       <td><input type=hidden id=fldPRCProcedureTypeValue value="{$bg['bgprocurementtypevalue']}"><input type=text id=fldPRCProcedureType READONLY class="inputFld lockfield" value="{$bg['bgprocurementtype']}"></td>
       <td><input type=hidden id=fldPRCCollectionTypeValue value="{$bg['bgcollectiontypevalue']}"> <input type=text id=fldPRCCollectionType READONLY class="inputFld lockfield" value="{$bg['bgcollectiontype']}">   </td>
       <td><input type=text id=fldPRCTechnician class=lockfield value="{$tech}" READONLY></td>
       <td><table><tr><td><input type=text id=fldPRCInitialMetric readonly class=lockfield value={$bg['bginitialmetric']}></td><td> {$muommenu} </td></tr></table></td>
       <td></td>
     </tr>
   </table>
 </td></tr>


<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader><span class=openDivIndicator id=openDivIndBSPHI onclick="hideProcGridLine('BSGridLinePHI',this.id);">&#9870;</span>Donor Metrics</td></tr>
  <tr><td class=procGridHoldingLine id=BSGridLinePHI style="display: none;">

   <table border=0 cellspacing=10>
    <tr>
      <td class=prcFldLbl>Initials </td>
      <td class=prcFldLbl>Age </td>
      <td class=prcFldLbl>Race </td>       
      <td class=prcFldLbl>Sex </td>
      <td class=prcFldLbl>Chemo-Therapy </td>
      <td class=prcFldLbl>Radiation </td>       
      <td class=prcFldLbl>Consent </td>
      <td class=prcFldLbl>Callback</td>
      <td class=prcFldLbl>Subject #</td>
      <td class=prcFldLbl>Protocol #</td>
      <td class=prcFldLbl>UPenn-SOGI</td>
    </tr>      
    <tr>
      <td><input type=text id=fldPRCPXIId READONLY value="{$bg['bgphiid']}">
          <input type=text id=fldPRCPXIInitials READONLY value="{$bg['bgphiinitials']}" class=lockfield></td>
      <td><table><tr><td><input type=text id=fldPRCPXIAge READONLY value="{$bg['bgphiage']}" class=lockfield></td><td><input type=text id=fldPRCPXIAgeMetric READONLY value="{$bg['bgphiageuom']}" class=lockfield></td></tr></table></td>
      <td><input type=text id=fldPRCPXIRace READONLY value="{$bg['bgphirace']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXISex READONLY value="{$bg['bgphisex']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXIDspCX READONLY value="{$bg['bgphicx']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXIDspRX READONLY value="{$bg['bgphirx']}" class=lockfield></td> 
      <td><input type=text id=fldPRCPXIInfCon READONLY value="{$bg['bgphiinformed']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXILastFour READONLY value="{$bg['bgphicallback']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXISubjectNbr READONLY value="{$bg['bgphisbjtnbr']}" class=lockfield></td>
      <td><input type=text id=fldPRCPXIProtocolNbr READONLY value="{$bg['bgphiprtclnbr']}" class=lockfield></td> 
      <td><input type=text id=fldPRCPXISOGI READONLY value="{$bg['bgphisogi']}" class=lockfield></td>       
    </tr>
    <tr><td colspan=20 style="font-size: .8vh; font-weight: bold;">(PHI Refid: {$bg['bgphiid']} on {$bg['bgphiproceduredate']})</td></tr>
  </table>

  </td></tr>

<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader><span class=openDivIndicator id=openDivIndBSDXD onclick="hideProcGridLine('BSGridLineDXD',this.id);">&#9871;</span>Diagnosis Designation</td></tr>
<tr><td class=procGridHoldingLine id=BSGridLineDXD>

<table border=0 cellspacing=10>
  <tr><td class=prcFldLbl>Specimen Category</td>
      <td class=prcFldLbl>Site</td>
      <td class=prcFldLbl>Sub-Site</td>
      <td><div><input type=checkbox id=fldPRCDXOverride {$dxover} readonly><label for=fldPRCDXOverride>DX Override</label></div></td>
      <td class=prcFldLbl>Uninvolve/NAT</td>
      <td class=prcFldLbl>Path-Rpt </td></tr>
  <tr><td valign=top><input type=text id=fldPRCSpecCat READONLY class="inputFld lockfield" value="{$bg['bgdxspeccat']}"></td>
      <td valign=top><input type=text id=fldPRCSite READONLY class="inputFld lockfield" value="{$bg['bgdxasite']}"></td>
      <td valign=top><input type=text id=fldPRCSSite READONLY class="inputFld lockfield" value="{$bg['bgdxssite']}"></td>
      <td valign=top> <input type=text id=fldPRCDXMod READONLY class="inputFld lockfield" value="{$bg['bgdxdx']}"></td>
      <td> <input type=text id=fldPRCUnInvolved READONLY class="inputFld lockfield" value="{$bg['bgdxuninv']}"></td>
      <td><input type=text id=fldPRCPathRpt READONLY class="inputFld lockfield" value="{$bg['bgpathreport']}"></td></tr>
  <tr><td colspan=6> 
    <table cellpadding=0 cellspacing=0 border=0><tr><td> 
     <div id=metsFromDsp style="display: block;">
       <table cellspacing=0 cellpadding=0 border=0>
         <tr><td class=prcFldLbl>Metastatic From</td><td class=prcFldLbl>Metastatic Diagnosis</td></tr>
         <tr><td style="padding: 10px 10px 0 0;"><input type=text id=fldPRCMETSSite READONLY class="inputFld lockfield" value="{$bg['bgdxmets']}"> </td><td style="padding: 10px 10px 0 0;"><input type=text id=fldPRCMETSDX READONLY class="inputFld lockfield" value="{$bg['bgdxmetsdx']}"> </td> </tr>
       </table>
     </div>
    </td><td>  
    <table cellpadding=0 cellspacing=0 border=0>
      <tr><td class=prcFldLbl>Position</td><td class=prcFldLbl>Systemic Diagnosis</td></tr>
      <tr><td style="padding: 10px 10px 0 0;"><input type=text id=fldPRCSitePosition READONLY class="inputFld lockfield" value="{$bg['bgdxsiteposition']}"></td><td style="padding: 10px 10px 0 0;"><input type=text id=fldPRCSystemList READONLY class="inputFld lockfield" value="{$bg['bgdxsystemdx']}"></td></tr>   
    </table>
    </td></tr></table>
</td></tr>
</table>

</td></tr>


<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader><span class=openDivIndicator id=openDivIndBSCmt onclick="hideProcGridLine('BSGridLineCmt',this.id);">&#9870;</span>Comments</td></tr>
<tr><td class=procGridHoldingLine id=BSGridLineCmt style="display: none;">

<table cellspacing=10 border=0>
<tr><td class=prcFldLbl>Biosample Comments</td><td class=prcFldLbl>Question for HPR/QMS</td></tr>
<tr><td valign=top><TEXTAREA id=fldPRCBSCmts class=lockfield style="height: 6vh;" READONLY>{$bg['bgbcomments']}</TEXTAREA></td><td valign=top><TEXTAREA id=fldPRCHPRQ class=lockfield style="height: 6vh;" READONLY>{$bg['bgqcomments']}</TEXTAREA></td></tr>
</table>

</td></tr>


<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader><span class=openDivIndicator id=openDivIndBSSegments onclick="hideProcGridLine('BSGridLineSegments',this.id);">&#9871;</span>Segments</td></tr>
<tr><td class=procGridHoldingLine id=BSGridLineSegments>
<!-- SEGMENTS //-->
<div id=procBSSegmentDsp>  
{$sgdta['DATA']}
</div>

</td></tr>

</table>

GRIDTBL;

$migrated = ( (int)$bg['bgmigratedind'] === 0 ) ? "No" : "Yes";
$migratedOn = ( trim($bg['bgmigratedon']) === "" ) ? "" : $bg['migratedon'];
$voided = ( (int)$bg['bgvoidind'] === 0 ) ? "No" : "Yes"; 
$voidreason = trim($bg['bgvoidreason']);


  $rtnTbl['sidebar'] = <<<SIDEBAR
<!-- SIDEBAR STUFF CAN GO HERE -- LOCK??? //-->

<table border=0 style="font-size: 1vh;" width=100%>
<tr><td colspan=2 style="border-bottom: 1px solid #000;"><center><b>Database Information</b></td></tr>
<tr><td><b>Migrated</b>: </td><td> {$migrated} </td></tr>
<tr><td><b>Migrated On</b>: </td><td> {$migratedOn} </td></tr>
<tr><td><b>Record Selector<b>: </td><td> {$bg['bgdbselector']} </td></tr>
<tr><td><b>Voided</b>: </td><td>{$voided}</td></tr>
<tr><td><b>Void Reason</b>: </td><td>{$voidreason}</td></tr>
</table>

SIDEBAR;

}

  return $rtnTbl;    
}

function bldProcurementGrid($usr) {   
  $si = serverIdent;
  $sp = serverpw;
  //DROP MENU BUILDER ********************************************************************************* //
  //PROCEDURE TYPE MENU
    $procedureTypeData = dropmenuProcedureTypes(); 
    $procedureType = $procedureTypeData['menuObj'];
    $procDefValue = $procedureTypeData['defaultLookupValue'];
    $procDefDspValue = $procedureTypeData['defaultDspValue'];
  //COLLECTION TYPE MENU 
    $collectionTypeData = dropmenuCollectionType($procDefValue);
    $collectionType = $collectionTypeData['menuObj'];
  //INITIAL METRIC MENU 
    $muomIData = dropmenuInitialMetric(); 
    $muommenu = $muomIData['menuObj'];
  //SPECIMEN CATEGORY
    $spcData = dropmenuInitialSpecCat();
    $spcmenu = $spcData['menuObj'];
  //PATHOLOGY REPORT
    $prptData = dropmenuPathRptAllowables();
    $prptmenu = $prptData['menuObj'];  
  //UNINVOLVED SAMPLE
    $univData = dropmenuUninvolvedIndicator();
    $uninvmenu = $univData['menuObj'];
  //SITE POSITIONS
    $asiteposData = dropmenuVocASitePositions(); 
    $aspmenu = $asiteposData['menuObj'];
  //SYSTEMIC LIST 
    $sysData = dropmenuSystemicDXListing(); 
    $sysdxmenu = $sysData['menuObj'];
  //CHEMO MENU
  //  $cxData = dropmenuCXIndicator(); 
  //  $cxmenu = $cxData['menuObj'];
  //CHEMO MENU
  //  $rxData = dropmenuRXIndicator(); 
  //  $rxmenu = $rxData['menuObj'];
  //SOGI
  //  $sogiData = dropmenuPennSOGI();
  //  $sogimenu = $sogiData['menuObj'];

  //UNKNOWNMET Unknown Metastatic Location
  //THIS SHOULD BE PROGRAMMATICALLY ASSESSED - IF MALIGNANT AND NO METS FROM DETERMINES FIELD

  //BASE SITE-SUBSITE MENU
    $sitesubsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSite READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>"; 

   $subsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSSite READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCSSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>";
   
   //BASE DX-MOD Menu
     $dxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCDXModValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCDXMod READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCDXMod><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category & Site)</div></div></div>";

   //METASTATIC SITE MENU DROPDOWN
     $metsData = dropmenuMetsMalignant();
     $metssite = $metsData['menuObj'];
     $metsdxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCMETSDXValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCMETSDX READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCMETSDX><center><div style=\"font-size: 1.4vh\">(Choose a Metastatic site)</div></div></div>";
     
$inscnt = 0;
  $insm = "<table border=0 class=menuDropTbl>";
  $igivendspvalue = "";
  $igivendspcode = "";
  foreach ($usr->allowedinstitutions as $inskey => $insval) {
    $instList .= ( $inscnt > 0 ) ? " <br> {$insval[1]} ({$insval[0]}) " : " {$insval[1]} ({$insval[0]}) ";
    $inscnt++;
    if ( trim($usr->presentinstitution) === $insval[0]) {
      $primeinstdsp = $insval[1];
      $igivendspvalue = "{$insval[1]} ({$insval[0]})";
      $igivendspcode = $insval[0];
    }
    $insm .= "<tr><td onclick=\"fillField('fldPRCPresentInst','{$insval[0]}','{$insval[1]}');\" class=ddMenuItem>{$insval[1]} ({$insval[0]})</td></tr>";
  }
  $insm .= "</table>";
//  $insmnu = "<div class=menuHolderDiv>"
//                      . "<input type=hidden id=fldPRCPresentInstValue value=\"{$igivendspcode}\">  "
//                      . "<div class=inputiconcontainer>"
//                              . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div>"
//                              . "<input type=text id=fldPRCPresentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\">"
//                     . "</div>"
//                     . "<div class=valueDropDown id=ddfldPRCPresentInst>{$insm}</div>"
//                     . "</div>";     
  $insmnu = "<input type=hidden id=fldPRCPresentInstValue value=\"{$igivendspcode}\"><input type=text id=fldPRCPresentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\">";

   //DROP DOWN MENU BUILDER END ******************************************************************** //

  $inst = $usr->presentinstitution;
  $tech = strtoupper($usr->userid);
  $tday = date('m/d/Y');
  
$rtnTbl = <<<RTNTBL
<table border=0 cellpadding=0 cellspacing=0 class=procGridHoldingTable >
<tr><td class=BSDspSectionHeader>Biogroup Sample Metrics</td></tr>
<tr><td class=procGridHoldingLine>
   <table border=0 cellspacing=10>
     <tr><td class=prcFldLbl>Procuring Institution <span class=reqInd>*</span></td><td class=prcFldLbl>Procurement Date <span class=reqInd>*</span></td><td class=prcFldLbl>Procedure Type <span class=reqInd>*</span></td><td class=prcFldLbl>Collection Type</td><td class=prcFldLbl>Technician <span class=reqInd>*</span></td><td class=prcFldLbl>Initial Metric <span class=reqInd>*</span></td><td>&nbsp;</td></tr>
     <tr>      
       <td><input type=hidden id=fldPRCBGNbr value="" READONLY>{$insmnu}</td> 
       <td><input type=text readonly id=fldPRCProcDate value="{$tday}"></td>
       <td>{$procedureType}</td>
       <td>{$collectionType}</td>
       <td><input type=text id=fldPRCTechnician value="{$tech}" READONLY></td>
       <td><table><tr><td><input type=text id=fldPRCInitialMetric value=0></td><td>{$muommenu}</td></tr></table></td>
       <td></td>
     </tr>
   </table>
</td></tr>

<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader>Donor Metrics</td></tr>
<tr><td class=procGridHoldingLine>

   <table border=0 cellspacing=10>
    <tr>
      <td class=prcFldLbl>Initials <span class=reqInd>*</span></td>
      <td class=prcFldLbl>Age <span class=reqInd>*</span></td>
      <td class=prcFldLbl>Race <span class=reqInd>*</span></td>       
      <td class=prcFldLbl>Sex <span class=reqInd>*</span></td>
      <td class=prcFldLbl>Chemo-Therapy <span class=reqInd>*</span></td>
      <td class=prcFldLbl>Radiation <span class=reqInd>*</span></td>       
      <td class=prcFldLbl>Consent <span class=reqInd>*</span></td>
      <td class=prcFldLbl>Callback</td>
      <td class=prcFldLbl>Subject #</td>
      <td class=prcFldLbl>Protocol #</td>
      <td class=prcFldLbl>UPenn-SOGI</td>
    </tr>      
    <tr>
      <td><input type=text id=fldPRCPXIId READONLY><input type=text id=fldPRCPXIInitials READONLY></td>
      <td><table><tr><td><input type=text id=fldPRCPXIAge READONLY></td><td><input type=text id=fldPRCPXIAgeMetric READONLY></td></tr></table></td>
      <td><input type=text id=fldPRCPXIRace READONLY></td>
      <td><input type=text id=fldPRCPXISex READONLY></td>
      <td><input type=text id=fldPRCPXIDspCX READONLY></td>
      <td><input type=text id=fldPRCPXIDspRX READONLY></td> 
      <td><input type=text id=fldPRCPXIInfCon READONLY></td>
      <td><input type=text id=fldPRCPXILastFour READONLY></td>
      <td><input type=text id=fldPRCPXISubjectNbr READONLY></td>
      <td><input type=text id=fldPRCPXIProtocolNbr READONLY></td> 
      <td><input type=text id=fldPRCPXISOGI READONLY></td>       
    </tr></table>

 </td></tr>

<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader>Diagnosis Designation</td></tr>
<tr><td class=procGridHoldingLine>

<table border=0 cellspacing=10>
  <tr><td class=prcFldLbl>Specimen Category <span class=reqInd>*</span></td><td class=prcFldLbl>Site <span class=reqInd>*</span></td><td class=prcFldLbl>Sub-Site</td><td><div><input type=checkbox id=fldPRCDXOverride><label for=fldPRCDXOverride>DX Override</label></div></td><td class=prcFldLbl>Uninvolve/NAT <span class=reqInd>*</span></td><td class=prcFldLbl>Path-Rpt <span class=reqInd>*</span></td></tr>
  <tr><td valign=top> {$spcmenu} </td><td valign=top> {$sitesubsite} </td><td> {$subsite} </td><td valign=top> {$dxmod} </td><td>{$uninvmenu}</td><td>{$prptmenu}</td></tr>
  <tr><td colspan=6> 
    <table cellpadding=0 cellspacing=0 border=0><tr><td> 
     <div id=metsFromDsp>
       <table cellspacing=0 cellpadding=0 border=0>
         <tr><td class=prcFldLbl>Metastatic From</td><td class=prcFldLbl>Metastatic Diagnosis</td></tr>
         <tr><td style="padding: 10px 10px 0 0;"> {$metssite} </td><td style="padding: 10px 10px 0 0;"> {$metsdxmod} </td> </tr>
       </table>
     </div>
    </td><td>  
    <table cellpadding=0 cellspacing=0 border=0>
      <tr><td class=prcFldLbl>Position</td><td class=prcFldLbl>Systemic Diagnosis</td></tr>
      <tr><td style="padding: 10px 10px 0 0;"> {$aspmenu} </td><td style="padding: 10px 10px 0 0;"> {$sysdxmenu} </td></tr>   
    </table>
    </td></tr></table>
</td></tr>
</table>


</td></tr>

<tr><td class=BSDspSpacer>&nbsp;</td></tr>
<tr><td class=BSDspSectionHeader>Comments</td></tr>
<tr><td class=procGridHoldingLine>

<table cellspacing=10 border=0>
<tr><td class=prcFldLbl>Biosample Comments</td><td class=prcFldLbl>Question for HPR/QMS</td></tr>
<tr><td><TEXTAREA id=fldPRCBSCmts></TEXTAREA></td><td><TEXTAREA id=fldPRCHPRQ></TEXTAREA></td></tr>
</table>

</td></tr>

</table>



RTNTBL;
  return $rtnTbl;    
}


function dropmenuPennSOGI( $dspvalue ) { 
//upennsogi
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/upenn-sogi",$si,$sp),true);
  //<tr><td align=right onclick=\"fillField('fldPRCUpennSOGI','','');\" class=ddMenuClearOption>[clear]</td></tr>
  $asp = "<table border=0 class=menuDropTbl>";
  $aspDefaultValue = "";
  $aspDefaultDsp = "";
  $dfltVal = "";
  $dfltCod = "";
  foreach ($asparr['DATA'] as $aspval) {
    if ( strtoupper($aspval['menuvalue']) === strtoupper($dspvalue) ) { 
      $aspDefaultValue = strtoupper($aspval['codevalue']);
      $aspDefaultDsp = strtoupper($aspval['menuvalue']);        
    }  
     if ((int)$aspval['useasdefault'] === 1) { 
      $dfltVal = strtoupper($aspval['menuvalue']);
      $dfltCod = strtoupper($aspval['codevalue']);        
    }
    $asp .= "<tr><td onclick=\"fillField('fldPRCUpennSOGI','" . strtoupper($aspval['lookupvalue']) . "','" . strtoupper($aspval['menuvalue']) . "');\" class=ddMenuItem>" . strtoupper($aspval['menuvalue']) . "</td></tr>";
  }
  $asp .= "</table>";
  if (trim($aspDefaultDsp) === "" && trim($dfltVal) !== "") { 
      $aspDefaultValue = $dfltCod;
      $aspDefaultDsp = $dfltVal;              
  }
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCUpennSOGIValue value=\"{$aspDefaultValue}\"><input type=text id=fldPRCUpennSOGI READONLY class=\"inputFld\" value=\"{$aspDefaultDsp}\"><div class=valueDropDown id=ddPRCUpennSOGI>{$asp}</div></div>";
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);

}

function dropmenuRXIndicator( $dspvalue ) { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-rx",$si,$sp),true);
  $asp = "<table border=0 class=menuDropTbl>";
  $aspDefaultValue = "";
  $aspDefaultDsp = "";
  $dfltVal = "";
  $dfltCod = "";
  foreach ($asparr['DATA'] as $aspval) {
    if ($aspval['menuvalue'] === $dspvalue ) { 
      $aspDefaultValue = $aspval['codevalue'];
      $aspDefaultDsp = $aspval['menuvalue'];        
    }  
     if ((int)$aspval['useasdefault'] === 1) { 
      $dfltVal = $aspval['menuvalue'];
      $dfltCod = $aspval['codevalue'];        
    }
    $asp .= "<tr><td onclick=\"fillField('fldPRCPXIRX','{$aspval['codevalue']}','{$aspval['menuvalue']}');\" class=ddMenuItem>{$aspval['menuvalue']}</td></tr>";
  }
  $asp .= "</table>";
  if (trim($aspDefaultDsp) === "" && trim($dfltVal) !== "") { 
      $aspDefaultValue = $dfltCod;
      $aspDefaultDsp = $dfltVal;              
  }
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCPXIRXValue value=\"{$aspDefaultValue}\"><input type=text id=fldPRCPXIRX READONLY class=\"inputFld\" value=\"{$aspDefaultDsp}\"><div class=valueDropDown id=ddPRCPXIRX>{$asp}</div></div>";
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);
}

function dropmenuCXIndicator( $dspvalue ) { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-cx",$si,$sp),true);
  $asp = "<table border=0 class=menuDropTbl>";
  $aspDefaultValue = "";
  $aspDefaultDsp = "";
  $dfltVal = "";
  $dfltCod = "";
  foreach ($asparr['DATA'] as $aspval) {
    if ($aspval['menuvalue'] === $dspvalue ) { 
      $aspDefaultValue = $aspval['codevalue'];
      $aspDefaultDsp = $aspval['menuvalue'];        
    }  
    if ((int)$aspval['useasdefault'] === 1) { 
      $dfltVal = $aspval['menuvalue'];
      $dfltCod = $aspval['codevalue'];        
    }
    $asp .= "<tr><td onclick=\"fillField('fldPRCPXICX','{$aspval['codevalue']}','{$aspval['menuvalue']}');\" class=ddMenuItem>{$aspval['menuvalue']}</td></tr>";
  } 
  $asp .= "</table>";
  if (trim($aspDefaultDsp) === "" && trim($dfltVal) !== "") { 
      $aspDefaultValue = $dfltCod;
      $aspDefaultDsp = $dfltVal;              
  }
  
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCPXICXValue value=\"{$aspDefaultValue}\"><input type=text id=fldPRCPXICX READONLY class=\"inputFld\" value=\"{$aspDefaultDsp}\"><div class=valueDropDown id=ddPRCPXICX>{$asp}</div></div>";
 
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);
}

function dropmenuSystemicDXListing() { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-systemic-dx-list",$si,$sp),true);
  $asp = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCSystemList','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  $aspDefaultValue = "";
  $aspDefaultDsp = "";
  foreach ($asparr['DATA'] as $aspval) {
    $asp .= "<tr><td onclick=\"fillField('fldPRCSystemList','{$aspval['codevalue']}','{$aspval['menuvalue']}');\" class=ddMenuItem>{$aspval['menuvalue']}</td></tr>";
  }
  $asp .= "</table>";
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCSystemListValue value=\"\">"
          . "<div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSystemList READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCSystemList>{$asp}</div></div>";
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);
}

function dropmenuVocASitePositions() { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-site-positions",$si,$sp),true);
  $asp = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCSitePosition','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  $aspDefaultValue = "";
  $aspDefaultDsp = "";
  foreach ($asparr['DATA'] as $aspval) {
      if ( (int)$aspval['useasdefault'] === 1 ) {
        $aspDefaultValue = $aspval['lookupvalue']; 
        $aspDefaultDsp = $aspval['menuvalue'];
      }
    $asp .= "<tr><td onclick=\"fillField('fldPRCSitePosition','{$aspval['lookupvalue']}','{$aspval['menuvalue']}');\" class=ddMenuItem>{$aspval['menuvalue']}</td></tr>";
  }
  $asp .= "</table>";
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCSitePositionValue value=\"{$aspDefaultValue}\">"
  . "<div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSitePosition READONLY class=\"inputFld\" value=\"{$aspDefaultDsp}\"></div><div class=valueDropDown id=ddPRCSitePosition>{$asp}</div></div>";
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);
}



function dropmenuProcedureTypes($passvalue = "", $lock = 0 ) {  
  $si = serverIdent;
  $sp = serverpw;
  $proctypearr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-menu-prc-proceduretype",$si,$sp),true);

  if ( $lock === 1 ) { 


  } else {
    $proct = "<table border=0 class=menuDropTbl>";
    $procTDefaultValue = "";
    $procTDefaultDsp = "";
    foreach ($proctypearr['DATA'] as $procval) {
      if ( (int)$procval['useasdefault'] === 1 ) {
        $procTDefaultValue = $procval['lookupvalue']; 
        $procTDefaultDsp = $procval['menuvalue'];
      }
      $proct .= "<tr><td onclick=\"fillField('fldPRCProcedureType','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
    }
    $proct .= "</table>";
    $procedureType = "<div class=menuHolderDiv>"
                   . "<input type=hidden id=fldPRCProcedureTypeValue value=\"{$procTDefaultValue}\">"
                   . "<div class=inputiconcontainer>"
                   . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div>"
                   . "<input type=text id=fldPRCProcedureType READONLY class=\"inputFld\" value=\"{$procTDefaultDsp}\">"
                   . "</div>"
                   . "<div class=valueDropDown id=ddPRCProcedureType>{$proct}</div>"
                   . "</div>";
  }

  return array('menuObj' => $procedureType,'defaultDspValue' => $procTDefaultDsp, 'defaultLookupValue' => $procTDefaultValue);
}




function dropmenuCollectionType($givenlookup) { 
  $collectionTypeDropMenu = "&nbsp;";
  if (trim($givenlookup) !== "") { 
      $pdta = array(); 
      $pdta['whichdropdown'] = 'PRCCollectionType';
      $pdta['whichmenu'] = 'COLLECTIONT';
      $pdta['lookupvalue'] = trim($givenlookup);
      $passdata = json_encode($pdta);
      $submenudta = json_decode(callrestapi("POST", dataTree . "/data-doers/generate-sub-menu",serverIdent, serverpw, $passdata), true);
      if ((int)$submenudta['ITEMSFOUND'] > 0 ) { 
        //<tr><td align=right onclick=\"fillField('fldPRCCollectionType','','');\" class=ddMenuClearOption>[clear]</td></tr>  
        $ctTbl = "<table border=0 class=menuDropTbl>";  
        $collectionDefaultValue = ""; 
        $collectionDefaultDsp = "";  
        foreach($submenudta['DATA'] as $ctkey => $ctval) {
              if ((int)$ctval['useasdefault'] === 1) { 
                $collectionDefaultValue = $ctval['menuvalue'];
                $collectionDefaultDsp = $ctval['dspvalue'];
              } 
              $ctTbl .= "<tr><td onclick=\"fillField('fldPRCCollectionType','{$ctval['menuvalue']}','{$ctval['dspvalue']}');\" class=ddMenuItem>{$ctval['dspvalue']}</td></tr>";
        }
        $ctTbl .= "</table>";
        $collectionTypeDropMenu = $ctTbl;
      }  
  }
  $collectionType = "<div class=menuHolderDiv><input type=hidden id=fldPRCCollectionTypeValue value=\"{$collectionDefaultValue}\">"
  . "<div class=inputiconcontainer>"
  . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div>"
  . "<input type=text id=fldPRCCollectionType READONLY class=\"inputFld\" value=\"{$collectionDefaultDsp}\">"
  . "</div>"
  . "<div class=valueDropDown id=ddPRCCollectionType>{$collectionTypeDropMenu}</div>"
  . "</div>";

  return array('menuObj' => $collectionType,'defaultDspValue' => $collectionDefaultDsp, 'defaultLookupValue' => $collectionDefaultValue);
} 

function dropmenuMetsMalignant() { 
  $pdta = array();  
  $pdta['specimencategory'] = 'MALIGNANT';
  $passdata = json_encode($pdta);
  $menudtaarr = json_decode(callrestapi("POST",dataTree."/data-doers/sites-by-specimen-category",serverIdent,serverpw,$passdata), true);
   $metsm = "<table border=0 class=menuDropTbl>";
   $metsDefaultValue = "";
   $metsDefaultDsp = "";
   foreach ( $menudtaarr['DATA'] as $metsval) { 
      $metsm .= "<tr><td onclick=\"fillField('fldPRCMETSSite','{$metsval['siteid']}','{$metsval['site']}');\" class=ddMenuItem>{$metsval['site']}</td></tr>";
   } 
   $metsm .= "</table>";

   $metssite = "<div class=menuHolderDiv><input type=hidden id=fldPRCMETSSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCMETSSite READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCMETSSite> {$metsm} </div></div>";

  return array('menuObj' => $metssite,'defaultDspValue' => $metsDefaultDsp, 'defaultLookupValue' => $metsDefaultValue);
}

function dropmenuUninvolvedIndicator() { 

   $si = serverIdent;
   $sp = serverpw;
   $unknmtarr = json_decode(callrestapi("GET", dataTree . "/global-menu/uninvolved-indicator-options",$si,$sp),true);
   $uninv = "<table border=0 class=menuDropTbl>";
   $uninvDefaultValue = "";
   $uninvDefaultDsp = "";
   foreach ($unknmtarr['DATA'] as $uninvval) {
      if ( (int)$uninvval['useasdefault'] === 1 ) {
        $uninvDefaultValue = $uninvval['codevalue']; 
        $uninvDefaultDsp = $uninvval['menuvalue'];
      }
    $uninv .= "<tr><td onclick=\"fillField('fldPRCUnInvolved','{$uninvval['codevalue']}','{$uninvval['menuvalue']}');\" class=ddMenuItem>{$uninvval['menuvalue']}</td></tr>";
   }
   $uninv .= "</table>";
   $uninvmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCUnInvolvedValue value=\"{$uninvDefaultValue}\">"
   . "<div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCUnInvolved READONLY class=\"inputFld\" value=\"{$uninvDefaultDsp}\"></div><div class=valueDropDown id=ddPRCUnInvolved>{$uninv}</div></div>";

  return array('menuObj' => $uninvmenu,'defaultDspValue' => $uninvDefaultDsp, 'defaultLookupValue' => $uninvDefaultValue);

}

function dropmenuInitialMetric( $passedvalue = "" ) { 
  $si = serverIdent;
  $sp = serverpw;
  $metricuomarr = json_decode(callrestapi("GET", dataTree . "/global-menu/metric-uoms-long",$si,$sp),true);


   $muom = "<table border=0 class=menuDropTbl>";
   $muomDefaultValue = "";
   $muomDefaultDsp = "";
   $lock = "";
   foreach ($metricuomarr['DATA'] as $uomval) {
 
     if ( $passedvalue === "" ) {  
       if ( (int)$uomval['useasdefault'] === 1 ) {
         $muomDefaultValue = $uomval['lookupvalue']; 
         $muomDefaultDsp = $uomval['menuvalue'];
       }
     } else { 
       if ( (int)$uomval['lookupvalue'] === (int)$passedvalue ) { 
         $lock = " lockfield";      
         $muomDefaultValue = $uomval['lookupvalue']; 
         $muomDefaultDsp = $uomval['menuvalue'];
       }
     }
     
    $muom .= "<tr><td onclick=\"fillField('fldPRCMetricUOM','{$uomval['lookupvalue']}','{$uomval['menuvalue']}');\" class=ddMenuItem>{$uomval['menuvalue']}</td></tr>";
   }
   $muom .= "</table>";
   $muommenu = "<div class=menuHolderDiv>"
           . "<input type=hidden id=fldPRCMetricUOMValue value=\"{$muomDefaultValue}\">"
           . "<div class=inputiconcontainer>"
         . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div>"
           . "<input type=text id=fldPRCMetricUOM READONLY class=\"inputFld {$lock}\" value=\"{$muomDefaultDsp}\">"
           . "</div>"
           . "<div class=valueDropDown id=ddPRCMetricUOM>{$muom}</div></div>";
  return array('menuObj' => $muommenu,'defaultDspValue' => $muomDefaultDsp, 'defaultLookupValue' => $muomDefaultValue);
}

function dropmenuInitialSpecCat( $passedvalue = "" ) {   
  $si = serverIdent;
  $sp = serverpw;
  $speccatarr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-specimen-category",$si,$sp),true);

  $speccat = "<table border=0 class=menuDropTbl>";
  foreach ($speccatarr['DATA'] as $spcval) {

   $speccat .= "<tr><td onclick=\"fillField('fldPRCSpecCat','{$spcval['lookupvalue']}','{$spcval['menuvalue']}');\" class=ddMenuItem>{$spcval['menuvalue']}</td></tr>";

  }
  $speccat .= "</table>";
  $spcmenu = "<div class=menuHolderDiv>"
          . "<div class=inputiconcontainer>"
  . "<div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=hidden id=fldPRCSpecCatValue value=\"\">"
          . "</div>"
          . "<input type=text id=fldPRCSpecCat READONLY class=\"inputFld\" value=\"{$passedvalue}\"><div class=valueDropDown id=ddPRCSpecCat>{$speccat}</div></div>";

  return array('menuObj' => $spcmenu, 'defaultDspValue' => '', 'defaultLookupValue' => '');
}

function dropmenuPathRptAllowables() { 
 
   $si = serverIdent;
   $sp = serverpw;
   $prptarr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-menu-prc-pathology-report",$si,$sp),true);
   $prpt = "<table border=0 class=menuDropTbl>";
   $prptDefaultValue = "";
   $prptDefaultDsp = "";
   foreach ($prptarr['DATA'] as $prptval) {
      if ( (int)$prptval['useasdefault'] === 1 ) {
        $prptDefaultValue = $prptval['lookupvalue']; 
        $prptDefaultDsp = $prptval['menuvalue'];
      }
    $prpt .= "<tr><td onclick=\"fillField('fldPRCPathRpt','{$prptval['lookupvalue']}','{$prptval['menuvalue']}');\" class=ddMenuItem>{$prptval['menuvalue']}</td></tr>";
   }
   $prpt .= "</table>";
   $prptmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCPathRptValue value=\"{$prptDefaultValue}\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCPathRpt READONLY class=\"inputFld\" value=\"{$prptDefaultDsp}\"></div><div class=valueDropDown id=ddPRCPathRpt>{$prpt}</div></div>";

  return array('menuObj' => $prptmenu, 'defaultDspValue' => $prptDefaultDsp, 'defaultLookupValue' => $prptDefaultValue);

}

function bldBiosampleProcurementEdit($usr, $selector) { 
// {"statusCode":200,"loggedsession":"4tlt57qhpjfkugau1seif6glo0","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":58,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"menuvalue":"Procure Biosample","pagesource":"procure-biosample","additionalcode":""}]],["433","DATA COORDINATOR","",[{"menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""},{"menuvalue":"Consult Library","pagesource":"val-consult-library","additionalcode":""},{"menuvalue":"Slide Image Library","pagesource":"image-library","additionalcode":""},{"menuvalue":"QMS Actions","pagesource":"qms-actions","additionalcode":""}]],["472","REPORTS","",[{"menuvalue":"All Reports","pagesource":"reports","additionalcode":""},{"menuvalue":"Barcode Run","pagesource":"reports\/inventory\/barcode-run","additionalcode":""},{"menuvalue":"Daily Procurement Sheet","pagesource":"reports\/procurement\/daily-procurement-sheet","additionalcode":""}]],["473","UTILITIES","",[{"menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474","HELP","scienceserver-help",[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]],"lastlogin":{"lastlogdate":"Mon Dec 17th, 2018 at 14:59","fromip":"170.212.0.91"},"accessnbr":"43"} 
    if ((int)$usr->allowprocure <> 1) { 
      //USER NOT ALLOWED TO PROCURE
      $holdingTbl = "<h1>USER NOT ALLOWED TO PROCURE BIOSAMPLES";
    } else { 
      $today = new DateTime('now');
      $tdydte = $today->format('m/d/Y');
      $tdydtev = $today->format('Y-m-d');
      $orscheddater = bldSidePanelORSched( $usr->presentinstitution, $tdydte, $tdydtev );
      //TODO:REMOVE THIS LINE TO DEFAULT TO TODAY'S DATE
      //$tdydtev = '20180507';
      //$orlistTbl = bldORScheduleTbl(  json_decode(callrestapi("GET", dataTree . "/simple-or-schedule/{$usr->presentinstitution}/{$tdydtev}",serverIdent, serverpw), true) );
      
      $procGrid = bldProcurementGridEdit($usr, $selector); //THIS IS THE PROCUREMENT GRID ELEMENTS

      //openAppCard('appcard_procphilisting');
//<div id=appcard_procphilisting class=appcard>
//<table>
//<tr><td class=sidePanel valign=top style="height: 39vh;">
 // <table border=0 width=100% cellspacing=0 cellpadding=0>
//    <tr><td>{$orscheddater}</td></tr>
//    <tr><td colspan=1>{$orlistTbl}</td></tr>
//  </table>
//</td></tr>
//</table> 
//</div>
      $holdingTbl = <<<HOLDINGTBL
            <div id=initialBiogroupInfo>
            <table border=0 id=procurementAddHoldingTbl>
                   <tr>
                      <td valign=top id=procbtnsidebar><center><div class=ttholder onclick="alert('You can\'t change the donor on a saved biogroup (for now).  Void the biogroup and recreate it!');"><i class="material-icons">how_to_reg</i><div class=tt>Donor Information/Operative Schedule</div></div></td>
                      <td valign=top id=procGridHolderCell style="min-width: 80vw;"> {$procGrid['grid']}</td>
                      <td valign=top id=procGridBGDsp>{$procGrid['sidebar']}</td>
                   </tr>
            </table> 
            </div>
HOLDINGTBL;
    }
    return $holdingTbl;
}



function bldBiosampleProcurement($usr) { 
    if ((int)$usr->allowprocure <> 1) { 
      //USER NOT ALLOWED TO PROCURE
      $holdingTbl = "<h1>USER NOT ALLOWED TO PROCURE BIOSAMPLES";
    } else { 
      $today = new DateTime('now');
      $tdydte = $today->format('m/d/Y');
      $tdydtev = $today->format('Y-m-d');
      $orscheddater = bldSidePanelORSched( $usr->presentinstitution, $tdydte, $tdydtev );
      //TODO:REMOVE THIS LINE TO DEFAULT TO TODAY'S DATE
      //$tdydtev = '20180507';
      $orlistTbl = bldORScheduleTbl(  json_decode(callrestapi("GET", dataTree . "/simple-or-schedule/{$usr->presentinstitution}/{$tdydtev}",serverIdent, serverpw), true) );
      $procGrid = bldProcurementGrid($usr); //THIS IS THE PROCUREMENT GRID ELEMENTS

      $holdingTbl = <<<HOLDINGTBL
            <div id=initialBiogroupInfo>
            <table border=0 id=procurementAddHoldingTbl>
                   <tr>
                      <td valign=top id=procbtnsidebar><center><div class=ttholder onclick="openAppCard('appcard_procphilisting');"><i class="material-icons">how_to_reg</i><div class=tt>Donor Information/Operative Schedule</div></div></td>
                      <td valign=top id=procGridHolderCell> {$procGrid}</td>
                      <td valign=top id=procGridBGDspNotDsp>&nbsp;</td>
                   </tr>
            </table> 
            </div>

<div id=appcard_procphilisting class=appcard>
<table>
<tr><td class=sidePanel valign=top style="height: 39vh;">
  <table border=0 width=100% cellspacing=0 cellpadding=0>
    <tr><td>{$orscheddater}</td></tr>
    <tr><td colspan=1>{$orlistTbl}</td></tr>
  </table>
</td></tr>
</table> 
</div>

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

function getBiogroupDefitionDisplay($biogroup, $bgency) { 
  $pdta = array();  
  $pdta['bgency'] = $bgency;
  $passdata = json_encode($pdta);
  //ITEMSFOUND":1,"DATA":{"bgnbr":"82812","readlabel":"82812T","pristineselector":"","voidind":0,"procureinstitution":"Reading Hospital ","technician":"ablatt","proceduredate":"01\/17\/2018","associativeid":"264712306LLW20180117","collecttype":"SURGERY","specimencategory":"MALIGNANT","collectedsite":"OVARY","diagnosis":"CARCINOMA","mets":"","siteposition":"","systemicdx":"","pxiid":"ZH6631191017122XSYIO968264712306LLW","phiage":"67 YEARS","phirace":"WHITE","phisex":"FEMALE","cxind":"NO","rxind":"NO","icind":"NO","prind":"YES","subjectnbr":"","protocolnbr":"","hprind":0,"hprmarkbyon":"","qcind":0,"qcmarkbyon":"","qcvalue":"","qcprocstatus":"S","qmsstatusby":"","qmsstatuson":"","hprstatus":"","hprresult":0,"hprslidereviewed":"","hprby":"","hpron":"","biosamplecomment":"SSV5 -----------","questionhpr":"SSV5 ----------"}} 
  $bgarr = json_decode(callrestapi("POST",dataTree."/data-doers/master-bg-record",serverIdent,serverpw,$passdata), true);
  if ( (int)$bgarr['ITEMSFOUND'] === 1 ) { 
      $bg = $bgarr['DATA'];          
      $rtnThis = "<table border=1><tr><td>Biogroup: {$bg['readlabel']}</td></tr></table>";
      


  } else { 
      $rtnThis = "<h3>NO BIOGROUP FOUND.  ERROR - SEE A CHTNEASTERN INFORMATICS STAFF MEMBER";
  }

return $rtnThis;

}
