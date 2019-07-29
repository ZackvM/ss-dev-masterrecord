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
 
    $standardSysDialog = 1;
    switch($whichdialog) {
    case 'irequestdisplay':
        $pdta = json_decode($passedData, true);         
        $rqstnbr =  cryptservice( $pdta['objid'], 'd');
        $titleBar = "Investigator Request Information ({$rqstnbr})";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldInvestigatorRequestDialog ( $pdta['dialogid'] , $passedData );     
    break;
    case 'hprreturnslidetray':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Slide Tray Return (Override)";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRSlideTrayReturnOverride ( $pdta['dialogid'], $passedData );
        break;
    case 'datacoordhprdisplay': 
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Review";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRReviewDisplay ( $pdta['dialogid'], $passedData );
        break;
    case 'trayreturndialog': 
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Unusuable Biosample";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRReturnTray ( $pdta['dialogid'], $passedData );
        break;
      case 'hprUnusuableDialog': 
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Unusuable Biosample";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRUsuableSave ( $pdta['dialogid'], $passedData );
        //$innerDialog = $passedData;
        break;         
      case 'hprInconclusiveDialog':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Inconclusive Biosample";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRInconclusiveDesignation ( $pdta['dialogid'], $pdta['objid'] );
        break;         
      case 'hprSystemicListBrowser':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Vocabulary Browser - Systemic/Co-Mobid List";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRVocabSystemic ( $pdta['dialogid'], $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;        
      case 'hprMetastaticSiteBrowser':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Vocabulary Browser - METS FROM Site List";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRVocabMETSSite ( $pdta['dialogid'], $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;        
      case 'hprDXOverride':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Vocabulary Browser - Diagnosis Override";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        //$innerDialog = $passedData;
        $innerDialog = bldHPRVocabDXOverride ( $pdta['dialogid'], $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;        
      case 'hprDesignationSpecifier':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Vocabulary Browser - Search Term";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRVocabBrowser ( $pdta['dialogid'], $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;        
      case 'hprprviewer':  
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Pathology Report Viewer";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPRPRBigViewer ( $pdta['dialogid'], $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;        
        case 'hprAssistEmailer':
        $pdta = json_decode($passedData, true);          
        $titleBar = "HPR Direct Emailer";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";              
        $innerDialog = bldHPREmailerDialog( $pdta['dialogid']   );
        //$footerBar = "SEGMENT ADD";       
        break;        
      case 'enlargeDashboardGraphic':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Dashboard Metric";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData . " " . $pdta['objid']; 
        $innerDialog = bldEnlargeDashboardGraphic ( $pdta['objid'] );
        //$footerBar = "SEGMENT ADD";       
        break;  
      case 'eventCalendarEventAdd':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Calendar Event Editor";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData; 
        $innerDialog = bldDialogCalendarAddEvent ( $passedData );
        //$footerBar = "SEGMENT ADD";       
        break;  
      case 'shipdocaddso':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Add Sales Order to Ship-Doc";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData; 
        $innerDialog = bldDialogShipDocAddSalesOrder ( $passedData );
        //$footerBar = "SEGMENT ADD";       
        break;
      case 'shipdocshipoverride':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Ship &amp; Close Shipment Document";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData; 
        $innerDialog = bldDialogShipDocShipOverride ( $passedData );
        //$footerBar = "SEGMENT ADD";       
        break;
      case 'shipdocaddsegment':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Add Segment";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData; 
        $innerDialog = bldDialogShipDocAddSegment ( $passedData );
        //$footerBar = "SEGMENT ADD";       
        break;
      case 'preprocremovesdsegment':  
        $pdta = json_decode($passedData, true);          
        $titleBar = "Remove Segment from Shipdoc";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";       
        //$innerDialog = $passedData; 
        $innerDialog = bldDialogShipDocPreRemoveSeg( $passedData);
        //$footerBar = "SEGMENT ADD";       
        break;
      case 'masterQMSAction':  
        $pdta = json_decode($passedData, true);          
        $titleBar = "QMS Functions";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogMasterQMSAction( $passedData);
        //$footerBar = "SEGMENT ADD";       
        break;
      case 'masterAddSegment':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Segment Additions (Master-Record)";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogMasterAddSegment( $passedData );
        //$footerBar = "SEGMENT ADD";   
        break;        
      case 'prnew': 
        $pdta = json_decode($passedData, true);          
        $titleBar = "Pathology Report Uploader";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogUploadPathRpt( $passedData );
        //$footerBar = "DONOR RECORD";            
        break; 
      case 'predit': 
        $pdta = json_decode($passedData, true);          
        $titleBar = "Pathology Report Editor";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogEditPathRpt( $passedData );
        //$footerBar = "DONOR RECORD";            
        break;
      case 'dlgEDTDX':  
        $pdta = json_decode($passedData, true);          
        $titleBar = "Diagnosis Designation Editor";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogEditDesigDX( $passedData );
        //$footerBar = "DONOR RECORD";
        break;    
      case 'dlgEDTENC':
        $pdta = json_decode($passedData, true);          
        $titleBar = "Encounter Editor";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogEditEncounter( $passedData );
        //$footerBar = "DONOR RECORD";
        break;
      case 'dlgCMTEDIT':  
        $pdta = json_decode($passedData, true);          
        $titleBar = "Comment Editor";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";        
        $innerDialog = bldDialogCoordEditComments( $passedData );        
        //$footerBar = "DONOR RECORD";
      break;    
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
        $titleBar = "Inventory Check-in Over-Ride Deviation Screen";
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
              if ( trim( $hprv['hprtraystatus'] ) !== '' && strtoupper( $hprv['hprtraystatus'] )  !== 'NOTUSED'  ) { 
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

          $cdrop = "<table border=0 class=menuDropTbl>";
          if ( count($idta['DATA']['courier']) > 0 ) {
            foreach ( $idta['DATA']['courier'] as $cval) { 
              $courierDsp =  $cval['courier'];
              $courierDsp .= ( trim($cval['couriernbr']) !== "" ) ? " :: " . trim($cval['couriernbr']) : "";
              $courierDsp .= ( trim($cval['couriercmt']) !== "" ) ? "  (" . trim($cval['couriercmt']) . ")" : "";
              $mcourierDsp =  $cval['courier'];
              $mcourierDsp .= ( trim($cval['couriernbr']) !== "" ) ? " :: " . trim($cval['couriernbr']) : "";
              $cdrop .= "<tr><td onclick=\"fillField('sdcCourierInfo','{$cval['courierid']}','{$mcourierDsp}');\" class=ddMenuItem>{$courierDsp}</td></tr>";
            }
          } else { 
            $cdrop .= "<tr><td>THERE IS NO COURIER INFORMATION LISTED FOR INVESTIGATOR {$dta['inv']}</td></tr>";
          }
          $cdrop .= "</table>";
          $couriermnu = "<table><tr><td>Courier</td></tr><tr><td><div class=menuHolderDiv><input type=hidden id=sdcCourierInfoValue value=\"\"><input type=text id=sdcCourierInfo value=\"\"><div class=valueDropDown style=\"min-width: 50vw;\">{$cdrop}</div></div></td></tr></table>";
          
        $innerDialog = <<<DIALOGINNER
<form id=frmShipDocCreate>
<table border=0  id=sdcMainHolderTbl>
    <tr><td>Ship Doc</td><td>Shipment Accepted By *</td><td>Acceptor's Email *</td><td>Shipment Purchase Order # *</td><td>Requested Ship Date *</td><td>Date to Pull *</td><td>Segments For Shipment *</td></tr>
    <tr>
            <td><input type=text id=sdcShipDocNbr READONLY value="NEW"></td>
            <td><input type=text id=sdcAcceptedBy value=""></td>
            <td><input type=text id=sdcAcceptorsEmail value=""></td>
            <td><div class=menuHolderDiv><input type=text id=sdcPurchaseOrder value=""><div class=valueDropDown style="width: 20vw;">{$po}</div></div></td>
            <td>{$shpCalendar}</td>
            <td>{$labCalendar}</td>
           <td rowspan=10 valign=top id=segmentListHolder><div id=sdcSegmentListDiv><!-- SEGMENT LISTING //--> {$segmentTbl}</div> </td></tr>
    <tr><td colspan=6> <table><tr><td>Public Comments</td></tr><tr><td><TEXTAREA id=sdcPublicComments></textarea></td></tr></table> </td></tr>         

<tr><td valign=top colspan=6>
<table border=0 width=100%><tr><td id=TQAnnouncement>Investigator Information from CHTN TissueQuest</td></tr><tr><td id=TQWarning>This information is from the central CHTN database (TissueQuest).  If you correct or change any information below you must also update it in TissueQuest!</td></tr></table>
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
<tr><td colspan=6>{$couriermnu}</td></tr>
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

    $closerAction = ( $standardSysDialog === 1 ) ? "closeSystemDialog();" : "{$closer}";

if ( trim($titleBar) === "" && trim($closerAction) === "" ) {  
  //return dialog with no dialog title bar
  $rtnthis = <<<PAGEHERE
<table border=0 cellspacing=0 cellpadding=0>
<tr><td colspan=2>
  {$innerDialog}
</td></tr>
<tr><td colspan=2>{$footerBar}</td></tr>
</table>
PAGEHERE;
} else {
  //return dialog with black title bar header  
  $rtnthis = <<<PAGEHERE
<table border=0 cellspacing=0 cellpadding=0>
<tr><td id=systemDialogTitle>{$titleBar}</td><td onclick="{$closerAction}" id=systemDialogClose>{$this->closeBtn}</td></tr>
<tr><td colspan=2>
  {$innerDialog}
</td></tr>
<tr><td colspan=2>{$footerBar}</td></tr>
</table>
PAGEHERE;
}

return $rtnthis;
}

function shipmentdocument ( $rqststr, $usr ) { 
  $url = explode("/",$_SERVER['REQUEST_URI']);   
  if ( trim($url[2]) !== "" ) {
     if ( trim($url[2]) === 'set-up-new-ship-doc' ) { 
         $sdPage = "NEW SHIPMENT DOCUMENT";
     } else {             
        $topBtnBar = generatePageTopBtnBar('shipdocedit', $usr);
        $sdPage = bldShipDocEditPage( $url[2] );
     }
  } else { 
    $sdPage = bldShipDocLookup();  
  }               
   
   $rtnThis = <<<RTNTHIS
           {$topBtnBar}
           {$sdPage}
RTNTHIS;
  
  

    return $rtnThis;    

}

function biogroupdefinition( $rqststr, $usr ) { 
  $u = json_encode($usr);
  $r = json_encode($rqststr);
  $url = explode("/",$_SERVER['REQUEST_URI']);   
  $bg = cryptservice($url[2],'d',false);
if ( trim($bg) !== '' ) { 
    //TODO:  CHECK THAT THIS IS AN ACTUAL BGNUMBER  

$topBtnBar = generatePageTopBtnBar('biogroupdefinition' , $usr);
$bgdsp = bldBiogroupDefitionDisplay($bg, $url[2]);
$rtnthis = <<<PAGEHERE
{$topBtnBar}
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

function inventory ( $rqststr, $whichusr ) { 

  
  if ( (int)$whichusr->allowinventory === 1 ) {
    switch ( strtolower(trim($rqststr[2]))) {      
      case 'inventorybiosamples':
        $rtnthis = bldInventoryAction_Inventorymaster($whichusr);
        break;
      case 'count':
          $rtnthis = bldInventoryAction_Inventorytally($whichusr);
          break;
      default: 
        $rtnthis = "<h2>{$rqststr[2]} NOT FOUND";
    }

  } else { 
   $rtnthis = "<h2>USER NOT ALLOWED ACCESS TO INVENTORY";
  }
  return $rtnthis; 
}

function reports ( $rqststr, $whichusr ) {


$accesslvl = $whichusr->accessnbr;
$user = $whichusr->userid;
//$rststr[1] = REPORTS // $rqststr[2] = reportname-for-criteria-screen or report-results // $rqststr[3] = report-run-id 


if ( trim($rqststr[2]) === "" ) { 
    //GET FULL REPORT LIST
  
    $pdta = json_encode(array( "user" => cryptservice( "{$user}::{$whichusr->loggedsession}",'e')));
    $rptdta = json_decode(callrestapi("POST", dataTree . "/data-doers/user-report-listing",serverIdent, serverpw, $pdta), true);
    $rdta = $rptdta['DATA'];
    $pg = "";
    $rCntr = 0;
    foreach ( $rdta as $key => $val ) {

      if ( $key === 'myfavoritereports' ) { 
        $sideicon = "<i class=\"material-icons favoritestar\">star</i>";
        $displayThis = "block";
      } else { 
        $sideicon = "<i class=\"material-icons basicrpt\">reorder</i>";
        $displayThis = "none";
      }

      $pg .= "<div class=reportGroupHolder id=RG{$rCntr}><table border=0 width=100% id=RGTBL{$rCntr} onclick=\"reportListDisplay({$rCntr});\"><tr><td class=groupHeaderName>{$val['name']}</td></tr><tr><td class=groupdescription>{$val['description']}</td></tr></table><div class=reportlist style=\"display: {$displayThis};\" id=RL{$rCntr}>";
      $pg .= "<table border=0 style=\"margin-left: 2vw; margin-bottom: 1vh;\"><tr>";
      $cllCntr = 0;
      foreach ( $val['list'] as $k => $v ) { 
        if ( $cllCntr === 5 ) { 
          $pg .= "</tr><tr>";
          $cllCntr = 0;
        }  
        $pg .= "<td valign=top class=rptDspSqr><table class=rptItemTblDsp><tr><td rowspan=2 class=\"primeiconholder hoverer\" onclick=\"markFavorite('{$v['reporturl']}');\">{$sideicon}</td><td onclick=\"navigateSite('reports/{$v['reporturl']}');\" class=rptTitleDsp>{$v['reportname']}</td></tr><tr><td onclick=\"navigateSite('reports/{$v['reporturl']}');\" class=rptDescDsp>{$v['description']}</td></tr></table></td>";
        $cllCntr++;
      }
      $pg .= "</tr></table>";
      $pg .= "</div></div>";
      $rCntr++;
    }

}

if ( $rqststr[2] === "reportresults" &&  trim($rqststr[3]) !== "" ) {
     //TABULAR REPORT RESULTS   
     $topBtnBar = generatePageTopBtnBar('reportresultsscreen');
     $pgContent = bldReportResultsScreen(trim($rqststr[3]), $whichusr);
     $pg = <<<CONTENT
{$pgContent}
CONTENT;
}

if ( trim($rqststr[2]) !== "" && trim($rqststr[2]) !== 'reportresults' ) {
  //GET REPORT PARAMETERS
  $topBtnBar = generatePageTopBtnBar('reportscreen');
  $reportParameters = bldReportParameterScreen($rqststr[2], $whichusr);        
  $pg = <<<CONTENT
{$reportParameters}
CONTENT;
     }


$rtnthis = <<<PAGEHERE
{$topBtnBar} 
{$pg}
PAGEHERE;

return $rtnthis;    
}

function qmsactions ( $rqststr, $whichusr ) { 

    if ( (int)$whichusr->allowqms <> 1 ) { 
        $pg = "<h1>USER ({$whichusr->userid}) NOT ALLOWED ACCESS TO QMS MODULE";
    } else { 
        if ( trim($rqststr[2]) === "" || trim($rqststr[2]) === "display" ) { 
            //GENERATE QUE LIST
            $topBtnBar = generatePageTopBtnBar('qmsaction');
            $pg = bldQMSQueList( $rqststr[3] );
        } else {             
            if ( trim($rqststr[2]) === 'workbench' ) { 
              $r = explode("/",$_SERVER['REQUEST_URI']);
              $pg = bldQAWorkbench ( $r[3] );
            } 
        }
    }
    
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

    $qcstatustxt .= ( trim($val['hprdecision']) !== "" && (int)$val['hprresultid'] !== 0 ) ? "<br>HPR Decision: <a href=\"javascript:void(0);\" class=hprindication onclick=\"generateDialog('datacoordhprdisplay',{$val['hprresultid']});\">{$val['hprdecision']}</a>" : ""; 
    $qcstatustxt .= ( trim($val['hprdecision']) !== "" && (int)$val['hprresultid'] === 0 ) ? "<br>HPR Decision: {$val['hprdecision']}" : ""; 
    $qcstatustxt .= ( trim($val['reviewedon']) !== "" ) ? "<br>HPR Review: {$val['reviewedon']}" : ""; 
    $qcstatustxt .= ( trim($val['hprreviewer']) !== "" ) ? " ({$val['hprreviewer']})" : "";

    $qcstatustxt .= ( trim($val['htrydsp']) !== "" ) ? "<br>In Slide {$val['htrydsp']}" : ""; 
    $qcstatustxt .= ( trim($val['htrystatus']) !== "" ) ? "<br>Slide Tray Status: {$val['htrystatus']}" : "";
    $qcstatustxt .= ( trim($val['hprtraystatuson']) !== "" ) ? " ({$val['hprtraystatuson']})" : "";

    $qcstatustxt .= ( trim($val['heldwithin']) !== "" ) ? "<br>Tray Location: {$val['heldwithin']}" : "";
    $qcstatustxt .= ( trim($val['heldwithinnote']) !== "" ) ? "<br> :: {$val['heldwithinnote']}" : "";

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
            $dspSD .= "<div class=tt>Shipdoc Status: {$val['sdstatus']}<br>Status by: [INFO NOT AVAILABLE]<p><div onclick=\"displayShipDoc(event,'{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">print</i> Print Ship-Doc (" . substr(('000000' . $val['shipdocnbr']),-6) . ")</div><p><div onclick=\"navigateSite('shipment-document/{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">edit</i> Edit Ship-Doc (" . substr(('000000' . $val['shipdocnbr']),-6) . ")</div></div>";
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
 
$rqstvw = (  substr(strtoupper($val['tqrequestnbr']),0,3) === "REQ" ) ? "<div class=ttholder>{$val['tqrequestnbr']}<div class=\"tt righttt\" onclick=\"generateDialog('irequestdisplay','" . cryptservice($val['tqrequestnbr']) . "');\">View Request</div></div>" : "{$val['tqrequestnbr']}";    
    
    
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
     data-hprboxnbr = "{$val['hprboxnbr']}"
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
  <td valign=top>{$rqstvw}</td>
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
<tr><td class=columnQParamName>HPR Slide Tray: </td>        <td class=ColumnDataObj>{$srchtrm['hprTrayInvLoc']}</td></tr>
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
 //TAG RELEASE  

$fsCalendar = buildcalendar('mainroot', date('m'), date('Y'), $whichUsr->friendlyname, $whichUsr->useremail, $whichUsr->loggedsession );

//DISPLAY WEEKLY GOALS
//  if ($whichUsr->primaryinstitution === 'HUP') { 
//      $weekGoal = bldWeeklyGoals($whichUsr);
//  } else { 
//      $weekGoal = "";
//  }

$graphListArr = ["grphfreezer" => "root_freezers.png", "grphrollshipgrid" => "root_yearrollship.png" , "grphinvestigatorinf" => "root_invnbrs.png", "grphsegshiptotal" => "root_totlshipped.png", "grphslidessubmitted" => "root_hprslidessubmitted.png"];
$graphics = array();
$at = genAppFiles;

foreach ( $graphListArr as $key => $grph ) {
    if ( file_exists("{$at}/publicobj/graphics/sysgraphics/{$grph}" ) ) { 
      $graphics[$key] = base64file("{$at}/publicobj/graphics/sysgraphics/{$grph}", "{$key}", "png", true , " onclick = \"enlargeDashboardGraphic('{$grph}');\" class=\"rootScreenGraph dashboardimage\" ");         
    }
}

$grphTbl = <<<METRICGRPHS

<table border=0>
<tr><td rowspan=3 valign=top class=dashBoardGraphic>{$graphics['grphrollshipgrid']}</td><td valign=top class=dashBoardGraphic style="height: 10vh;">{$graphics['grphinvestigatorinf']}</td><td class=dashBoardGraphic rowspan=3 valign=top>{$graphics['grphfreezer']}</td></tr>
<tr><td valign=top class=dashBoardGraphic style="height: 10vh;"> {$graphics['grphsegshiptotal']} </td></tr>
<tr><td valign=top class=dashBoardGraphic style="height: 10vh;"> {$graphics['grphslidessubmitted']} </td></tr>

</table>


METRICGRPHS;

  $rtnthis = <<<PAGEHERE
<table border=0 id=rootTable>
    <tr><td rowspan=2 valign=top>{$grphTbl}</td><td style="width: 42vw;" align=right valign=top>{$weekGoal}</td></tr>
    <tr><td style="width: 42vw;" align=right valign=top><div id="mainRootCalendar">{$fsCalendar}</div></td></tr>    
</table>
  
PAGEHERE;
return $rtnthis;
}

function hprreview($rqststr, $whichusr) { 

  if ((int)$whichusr->allowhpr <>  1) { 
    //USER NOT ALLOWED TO PROCURE
    $pg = "<h1>USER NOT ALLOWED TO ACCESS TO HISTOPATHOLOGIC REVIEW";
  } else { 
    if (trim($rqststr[2]) === "") { 
      //HPR Query Grid and Scanner
      $pg = <<<PAGECONTENT
        <div id=headAnnouncement>Scan or Type :: Tray or Slide Number</div>
        <center>
        <input type=text id=dspScanType>
PAGECONTENT;
    } else { 
      if ( trim($rqststr[3]) === "" ) {
        //GET TRAY LIST 
        $topBtnBar = generatePageTopBtnBar('hprreviewactionstray',$whichusr, $rqststr[2] ); 
        $pgContent = buildHPRTrayDisplay( $rqststr[2], $whichusr );
      } else { 
        //GET WORKBENCH WITH BACK BUTTON TO TRAY
        $topBtnBar = generatePageTopBtnBar('hprreviewactions',$whichusr, $rqststr[2] ); 
        $technicianSide = buildHPRTechnicianSide ( $_SERVER['REQUEST_URI'] );   
        $workBench = buildHPRWorkBenchSide ( $_SERVER['REQUEST_URI'] , $whichusr->allowhprreview    ); 
        $pgContent = <<<REVIEWTBL
          <table border=0 id=masterHPRSlideReviewTbl>
            <tr><td colspan=2 id=masterHPRSlideAnnounceLine>{$technicianSide['topLineAnnouncement']}</td></tr>
            <tr><td id=masterHPRTechnicianSide valign=top>{$technicianSide['techMetrics']}</td><td valign=top rowspan=3 id=masterHPRWorkbenchSide> {$workBench['wrkBnch']} </td></tr>
            <tr><td id=masterHPRDivBtns> 
              <table cellpadding=0 cellspacing=0>
                <tr>
                  <td class=tabBtn onclick="changeSupportingTab(0);">Pathology<br>Report</td>
                  <td class=tabBtn onclick="changeSupportingTab(1);">Constituent<br>Segments</td>
                  <td class=tabBtn onclick="changeSupportingTab(2);">Previous<br>Reviews</td>
                  <td class=tabBtn onclick="changeSupportingTab(5);">Associative<br>Groups</td>
                  <td class=tabBtn onclick="changeSupportingTab(3);">Images &amp;<br>Other Files</td>
                  <td class=tabBtn onclick="changeSupportingTab(4);">Virtual<br>Slide</td>
                </tr>
              </table>
             </td></tr>
            <tr><td  id=masterHPRDocumentSide valign=top>{$technicianSide['documentMetrics']}</td></tr>
            </table>
REVIEWTBL;
      }
      //{$technicianSide['documentMetrics']}
    //$u = json_encode($whichusr);  
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
$authCode = "";
$authExpire = "";
if(!isset($_COOKIE['ssv7auth'])) {
} else {             
    $authCodeAsset = json_decode( $_COOKIE['ssv7auth'] , true );
    //{"dualcode":"dDhJVDB6S0EvdFRwaDBQYUZ5Q253dz09","expiry":1560793836}
    $authCode = cryptservice( $authCodeAsset['dualcode'] , 'd');
    $authExpire = " will expire on " .date( 'h:iA', strtotime($authCodeAsset['expirydate']));
}

$addLine = "<tr><td class=label>Dual-Authentication Code <span id=dspAuthExpiry>{$authExpire}</span>&nbsp;<span class=pseudoLink id=btnSndAuthCode>(Send Authentication Code)</span></td></tr><tr><td><input type=text id=ssDualCode value=\"{$authCode}\"></td></tr>";
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

function buildHPRWorkBenchSide ( $rqsturi, $allowhprreview ) { 
    $tt = treeTop;
    $rurl =  explode( "/", $rqsturi );
    $segbg = explode("::",cryptservice( $rurl[3], 'd', false));
    $segData = json_decode(callrestapi("GET", dataTree. "/do-single-segment/" . $segbg[0],serverIdent, serverpw), true);    
    $sg = $segData['DATA'][0];
    $prcList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-percentages",serverIdent,serverpw), true);
    $reviewList = json_decode( callrestapi("GET", dataTree . "/hpr-reviewer-list",serverIdent,serverpw), true);
    $techAccList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-technician-accuracy",serverIdent,serverpw), true);
    $tmrGradeScaleList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-tumor-grade-scale",serverIdent,serverpw), true);
    $faList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-further-actions",serverIdent,serverpw), true);
    $idsuffix = generateRandomString(8);
    $moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);

    //UNINVOLVED SAMPLE
    //TODO: MAKE ALL MENUS THROUGH THE WHOLE SYSTEM LIKE THIS!!! - I LIKE THE SIDE BUTTON
    $univData = dropmenuUninvolvedIndicator();
    $uninvmenu = $univData['menuObj'];

//DESIGNATION BUILD
    $dtaSpecCat = strtoupper(trim($sg['specimencategory']));
    $spc = strtoupper(trim($sg['specimencategory']));
    $dtaSiteSub = strtoupper(trim($sg['site']));
    $ste = strtoupper(trim($sg['site']));
    $dtaSiteSub .= ( trim($sg['subsite']) !== "" ) ? " [" . strtoupper(trim($sg['subsite'])) . "]" : "";
    $sste = strtoupper(trim($sg['subsite']));
    $dtaSiteSub .= ( trim($sg['siteposition']) !== "" ) ? " (" . strtoupper(trim($sg['siteposition'])) . ")" : "";
    $spos = strtoupper(trim($sg['siteposition']));
    $dtaDXMod = ( trim($sg['dx']) !== "") ? " / " . strtoupper(trim($sg['dx'])) : "";
    $dx = strtoupper(trim($sg['dx']));
    $dtaDXMod .= ( trim($sg['dxmod']) ) ? " [" . strtoupper(trim($sg['dxmod'])) . "]" : "";
    $mdx = strtoupper(trim($sg['dxmod']));
    $dtaMetsFrom = strtoupper(trim($sg['metssite']));
    $mets = strtoupper(trim($sg['metssite']));
//    $dtaMetsFrom .= ( trim($sg['metssitedx']) !== "" ) ? " / " . strtoupper(trim($sg['metssitedx'])) : "";
    $dtaSystemic = strtoupper(trim( $sg['systemicdx'] ));
    $sysm =  strtoupper(trim( $sg['systemicdx'] ));

    $rList = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\">";
    foreach ( $reviewList['DATA'] as $rval) { 
      $rList .= "<tr><td onclick=\"fillField('fldOnBehalf','{$rval['userid']}','{$rval['displayname']}');\" class=ddMenuItem>{$rval['displayname']}</td></tr>";
    }
    $rList .= "</table>";
     
    $reviewersmenu = ((int)$allowhprreview === 1 ) ? "" : "<tr><td colspan=5><table><tr><td class=hprPreLimFldLbl>Proxy For: </td><td> <div class=menuHolderDiv><input type=hidden id=fldOnBehalfValue value=\"\"><input type=text id=fldOnBehalf READONLY class=\"inputFld hprDataField\" style=\"width: 10vw;\" value=\"\"><div class=valueDropDown style=\"min-width: 10vw;\">{$rList}</div></div></td></tr></table></td></tr>";
    
$dxtbl = <<<DXTBL
<table border=0 cellspacing=0 cellpadding=0 width=100%
            id=HPRWBTbl 
            data-segid="{$segbg[0]}" 
            data-ospecimencategory="{$spc}" 
            data-specimencategory="{$spc}" 
            data-osite="{$ste}" 
            data-site="{$ste}" 
            data-ossite="{$sste}" 
            data-ssite="{$sste}" 
            data-ospos="{$spos}" 
            data-spos="{$spos}" 
            data-odx="{$dx}" 
            data-dx="{$dx}" 
            data-odxm="{$mdx}" 
            data-dxm="{$mdx}" 
            data-omets="{$mets}" 
            data-mets="{$mets}" 
            data-osysm="{$sysm}" 
            data-sysm="{$sysm}">
     {$reviewersmenu}
     <tr>
       <td rowspan=4 id=decisionSqr data-hprdecision="CONFIRM"><i class="material-icons hprdecisionicon hprdecisionconfirm">thumb_up</i></td>
       <td colspan=4 class=hprPreLimFldLbl ondblclick="resetDesig();"><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Diagnosis Designation&nbsp;<span class=actionInstruction>(Double-Click Designation to Reset)</span></td><td align=right> <table border=0><tr><td class=sideDesigBtns onclick="loadInconclusive('{$rurl[3]}');">Inconclusive</td></tr></table> </td></tr></table></td></tr>
     <tr>
       <td colspan=4 class=hprPreLimDtaFld valign=top>
         <table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td><div id=dspHPRDesignation ondblclick="resetDesig();">{$dtaSpecCat} {$dtaSiteSub} {$dtaDXMod}</div></td>
       <td align=right><table border=0> <tr><td class=sideDesigBtns onclick="loadDesignation();">Designation</td><td class=sideDesigBtns onclick="loadDXOverride();">DX Override</td></tr></table></td></tr></table></td>
     </tr>
     <tr>
       <td colspan=2 class="hprPreLimFldLbl rightEndCap" width=50% ondblclick="blankDesig('dspHPRMetsFrom');">METS From&nbsp;<span class=actionInstruction>(Double-Click Designation to Blank)</span></td>
       <td colspan=2 class="hprPreLimFldLbl" ondblclick="blankDesig('dspHPRSystemic');">Systemic/Co-Mobid&nbsp;<span class=actionInstruction>(Double-Click Designation to blank)</span></td>
     </tr>
     <tr>
       <td colspan=2 class="hprPreLimDtaFld rightEndCap" valign=top>
         <table cellspacing=0 cellpadding=0 width=100%><tr><td id=dspHPRMetsFrom ondblclick="blankDesig('dspHPRMetsFrom');">{$dtaMetsFrom}</td><td align=right><table border=0 onclick="loadMETSBrowser();"><tr><td class=sideDesigBtns>Metastatic</td></tr></table></td></tr></table></td>
       <td colspan=2 class=hprPreLimDtaFld valign=top>  <table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td id=dspHPRSystemic ondblclick="blankDesig('dspHPRSystemic');">{$dtaSystemic}&nbsp;</td><td align=right> <table border=0><tr><td class=sideDesigBtns onclick="loadSystemicBrowser();">Systemic</td></tr></table> </td></tr></table></td>
     </tr>
</table>
DXTBL;

$desig = <<<DDX
{$dxtbl}
DDX;
//END DESIGNATION BUILD
//BUILD TUMOR GRADE 
$tmrGrd = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"fillField('fldTumorGradeScale','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ( $tmrGradeScaleList['DATA'] as $procval) { 
      $tmrGrd .= "<tr><td onclick=\"fillField('fldTumorGradeScale','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
  }
$tmrGrd .= "</table>";
//END TUMOR GRADE
//PERCENTAGE BUILDER
$prcTbl = "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
$cntr = 0;
foreach ( $prcList['DATA'] as $prcv ) { 
  if ($cntr === 4) {
    $prcTbl .= "</tr><tr>";
    $cntr = 0;
  }
  $pdspTbl = "<table border=0 width=100%><tr><td class=hprPreLimFldLbl>{$prcv['menuvalue']}</td><td align=right><input type=text class=prcFld id='{$prcv['codevalue']}'></td></tr></table>";
  $prcTbl .= "<td class=prcSqrHolder> {$pdspTbl} </td>";
  $cntr++;
}
$prcTbl .= "</tr></table>";
//END PERCENTAGE BUILDER
//TECH ACC BUILDER
$techAcc = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\">";
$techAccDefault = "";
$techAccDefaultCode = "";
  foreach ($techAccList['DATA'] as $procval) { 
      if ( (int)$procval['useasdefault'] === 1 ) { $techAccDefault = $procval['menuvalue'];  $techAccDefaultCode = $procval['lookupvalue']; }  
      $techAcc .= "<tr><td onclick=\"fillField('fldTechAcc','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
  }
$techAcc .= "</table>";
//END TECH ACC BUILDER


//FA LISTING
//$fa = "<table border=0  class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"fillField('fldFurtherAction','','');\" class=ddMenuClearOption>[clear]</td></tr>";
//  foreach ($faList['DATA'] as $procval) { 
//      $fa .= "<tr><td onclick=\"fillField('fldFurtherAction','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
//  }
//$fa .= "</table>";
//
//$faTbl = <<<FATBL
//<table border=1>
//<tr><td class=hprPreLimFldLbl>Further Action</td><td class=hprPreLimFldLbl>Note</td><td rowspan=2 valign=top><table class=tblBtn style="width: 2.3vw;" onclick="manageFurtherActions(1,);"><tr><td><center><i class="material-icons" style="font-size: 1.8vh;">playlist_add</i></td></tr></table> </td></tr>
//<tr><td> <div class=menuHolderDiv><input type=hidden id=hprFAJsonHolder><input type=hidden id=fldFurtherActionValue value=""><input type=text id=fldFurtherAction READONLY class="inputFld hprDataField " style="width: 17vw;" value=""><div class=valueDropDown style="min-width: 20vw;">{$fa}</div></div></td><td><input type=text id=fldFANote class="hprDataField" style="width: 8vw;" ></td></tr>
//<tr><td colspan=3><div id=furtheractiondsplisting style="border: 1px solid rgba(160,160,160,.8); height: 16vh; overflow: auto;"></div></td></tr>
//</table>
//FATBL;
//END FA LISTING



//MOLE TBL 
    //molecular test
    $molemnu = "<table border=0 width=100% class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"triggerMolecularFill(0,'','','{$idsuffix}');\" class=ddMenuClearOption>[clear]</td></tr>";
    foreach ($moletest['DATA'] as $moleval) { 
        $molemnu .= "<tr><td onclick=\"triggerMolecularFill({$moleval['menuid']},'{$moleval['menuvalue']}','{$moleval['dspvalue']}','{$idsuffix}');\" class=ddMenuItem>{$moleval['dspvalue']}</td></tr>";
    }
    $molemnu .= "</table>";
$moleTbl = <<<MOLETBL
      <table border=0>
       <tr><td colspan=2 class=hprPreLimFldLbl>Indicated Immuno/Molecular Test Results</td><td class=hprPreLimFldLbl>Result Index</td><td class=hprPreLimFldLbl colspan=2>Scale Degree</td>
           <td rowspan=2 valign=top>
                     <table class=tblBtn style="width: 2.3vw;" onclick="manageMoleTest(1,'','{$idsuffix}');">
                     <tr><td><i class="material-icons" style="font-size: 2vh;">playlist_add</i></td></tr></table></td></tr>
       <tr><td class=fieldHolder valign=top colspan=2>
                    <div class=menuHolderDiv>
                      <input type=hidden id=hprFldMoleTest{$idsuffix}Value>
                      <input type=text id=hprFldMoleTest{$idsuffix} READONLY style="width: 25vw;" class=hprDataField>
                      <div class=valueDropDown style="min-width: 25vw;">{$molemnu}</div>
                    </div>
            </td>
            <td class=fieldHolder valign=top>
             <div class=menuHolderDiv>
               <input type=hidden id='hprFldMoleResult{$idsuffix}Value'>
               <input type=text id='hprFldMoleResult{$idsuffix}' READONLY class=hprDataField style="width: 12.5vw;">
               <div class=valueDropDown id=moleResultDropDown style="min-width: 12.5vw;"> </div>
             </div>
            </td>
            <td class=fieldHolder valign=top>
              <input type=text id=hprFldMoleScale{$idsuffix} class=hprDataField style="width: 12.5vw;">
            </td>
       </tr>
       <tr><td colspan=6 valign=top>
           <input type=hidden id=hprMolecularTestJsonHolderConfirm>
           <div id=dspDefinedMolecularTestsConfirm{$idsuffix} class=dspDefinedMoleTests>
           </div>
           </td>
       </tr>
      </table>
MOLETBL;
//MOLETBL END
//onchange="readFilesChosen(this);" ///THIS IS FROM bonanzaboys.com
    $workBench = <<<WORKBENCH
<div class=dspWBDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td><center>Work Bench <input type=hidden id=backToTrayURL value='{$rurl[2]}'></td></tr></table></div>
<table border=0>
  <tr>
    <td valign=top>{$desig}</td>
  </tr>
  <tr>
    <td>
      <table border=0 cellspacing=0 cellpadding=0>
        <tr>
          <td class=hprPreLimFldLbl>Uninvolved Sample</td>
          <td class=hprPreLimFldLbl>Tumor Grade (if applicable)</td>
          <td class=hprPreLimFldLbl style="width: 21vw;">Technican Accuracy</td>  
          </tr>
        <tr>
         <td>{$uninvmenu}</td>
         <td><table><tr><td><input type=text id=fldTumorGrade class="hprDataField" style="width: 5vw;"></td><td> <div class=menuHolderDiv><input type=hidden id=fldTumorGradeScaleValue value=""><input type=text id=fldTumorGradeScale READONLY class="inputFld hprDataField" style="width: 10vw;" value=""><div class=valueDropDown style="min-width: 10vw;">{$tmrGrd}</div></div></td></tr></table></td>
          <td><div class=menuHolderDiv><input type=hidden id=fldTechAccValue value="{$techAccDefaultCode}"><input type=text id=fldTechAcc READONLY class="inputFld hprDataField" style="width: 23vw;" value="{$techAccDefault}"><div class=valueDropDown style="min-width: 23vw;">{$techAcc}</div></div></td>
         </tr>
      </table>
    </td>
  </tr>


  <tr><td style="padding: 1vh 0 0 0; text-align: center; padding: 8px; font-size: 1.5vh; color: rgba(255,255,255,1);background:rgba(100,149,237,1); font-weight: bold;">TUMOR COMPLEXION (PERCENTAGES)</td></tr>
  <tr>
    <td> {$prcTbl} </td>
  </tr>
  <tr><td style="padding: 1vh 0 0 0; text-align: center; padding: 8px; font-size: 1.5vh; color: rgba(255,255,255,1);background:rgba(100,149,237,1); font-weight: bold;">MOLECULAR TESTS</td></tr>
  <tr>
    <td><center> <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                     <tr>
                                       <!-- <td valign=top width=50%> {$faTbl} </td> //-->
                                       <td valign=top> {$moleTbl} </td>
                                     </tr>
                                     </table>  
    </td>
  </tr>
  <tr>
    <td style="padding: 1vh 0 0 0;"><table width=100% border=0>
                                      <tr><td class=hprPreLimFldLbl>General Comments</td><td class=hprPreLimFldLbl>Rare Reason</td><td class=hprPreLimFldLbl>Special Instructions to Staff</td></tr>
                                      <tr><td><textarea id=fldGeneralCmtsTxt style="width: 19vw; height: 10vh;"></textarea></td></td><td><textarea id=fldRareReasonTxt style="width: 19vw; height: 10vh;"></textarea></td><td><textarea id=fldSpecialInstructions style="width: 18vw; height: 10vh;"></textarea></td></tr>
                                    </table>
    </td>
  </tr>
  <!-- //TODO ADD LATER TO TOOL BAR <tr><td style="padding: 3vh 0 0 0;"><center><div class="upload-btn-wrapper"><button class="btn">Upload Supporting Files</button><input type="file" name="myfile" name="photos[]" id=zckPicSelector accept="image/jpeg, image/png" multiple  /></div></td></tr> //-->
  <tr>
    <td align=right><table border=0><tr><td> <table class=tblBtn id=btnHPRReviewSave style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table> </td><td> <table class=tblBtn id=btnHPRReviewNotFit style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Save::Unusable</td></tr></table> </td><td> <table class=tblBtn id=btnCancel style="width: 6vw;" onclick="navigateSite('hpr-review/{$rurl[2]}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td></tr></table>    </td>
  </tr>
</table>

WORKBENCH;

    return array( "wrkBnch" => $workBench );
}

function buildHPRTechnicianSide ( $rqsturi ) { 
    $tt = treeTop;
    $rurl =  explode( "/", $rqsturi );
    $segbg = explode("::",cryptservice( $rurl[3], 'd', false));
    $segData = json_decode(callrestapi("GET", dataTree. "/do-single-segment/" . $segbg[0],serverIdent, serverpw), true);    
    $sg = $segData['DATA'][0];

//$sg['biosamplelabel']
    $segConstitList = json_decode( callrestapi("GET", dataTree. "/hpr-get-constit-list/" . $segbg[0],serverIdent, serverpw), true );
    $segAssGroup = json_decode( callrestapi("GET", dataTree . "/get-ass-group-from-seg/{$segbg[0]}",serverIdent, serverpw), true) ;
    $segHPRLines = json_decode( callrestapi("GET", dataTree . "/get-past-hpr-reviews-by-biogroup-singleline/{$sg['biosamplelabel']}",serverIdent, serverpw), true) ;

    $dtaAge = strtolower(trim("{$sg['phiage']} {$sg['phiageuom']}"));
    $dtaRace = ucwords(trim($sg['phirace']));
    $dtaSex = ucwords(trim($sg['phisex']));
    $dtaBGCmts = preg_replace('/[Ss]{2}[Vv]\d\s{0,}\-{1,}/','',trim($sg['biosamplecomment'])) . "&nbsp;";
    $dtaSGCmts = preg_replace('/[Ss]{2}[Vv]\d\s{0,}\-{1,}/','',trim($sg['segmentcomments']))  . "&nbsp;";
    $dtaQstn =   preg_replace('/[Ss]{2}[Vv]\d\s{0,}\-{1,}/','',trim($sg['hpquestion']))       . "&nbsp;";
    $dtaProcedure = $sg['proceduretype'];
    $dtaProcedure .= ( trim($sg['procedureinstitution']) !== "" ) ? " / " . strtoupper(trim( $sg['procedureinstitution'])) : "";
    $dtaProcedure .= "<br><span class=smlrTxt>";
    $dtaProcedure .= $sg['proceduredate'];
    $dtaProcedure .= ( trim($sg['proctechnician']) !== "" ) ? " [" . strtolower(trim($sg['proctechnician'])) . "]" : "";
    $dtaProcedure .= "</span>";
    $dtaUninv = $sg['uninvolvedind'];
    $dtaCXRX = $sg['cx'];
    $dtaCXRX .= "/{$sg['rx']}";
    $dtaPRIC = $sg['pthrpt'];
    $dtaPRIC .= "/{$sg['infc']}";
    $dspSlide =  "<table width=100% border=0><tr><td> Slide: " . strtoupper(preg_replace("/[^[:alnum:]]/iu", '', $sg['bgs']));
    $dspSlide .= ( trim($sg['scannedlocation']) !== "") ? " / {$sg['scannedlocation']}" : "";
    $dspSlide .= "</td><td align=right>";
    $dspSlide .= ( $sg['hprslideread'] === 'N' ) ? "<i class=\"material-icons topreadindicator needread\">error</i>" : "<i class=\"material-icons topreadindicator doneread\">check_circle</i>"   ;
    $dspSlide .= "</td></tr></table>";
    $dtaSpecCat = strtoupper(trim($sg['specimencategory']));
    $dtaSiteSub = strtoupper(trim($sg['site']));
    $dtaSiteSub .= ( trim($sg['subsite']) !== "" ) ? " [" . strtoupper(trim($sg['subsite'])) . "]" : "";
    $dtaSiteSub .= ( trim($sg['siteposition']) !== "" ) ? " (" . strtoupper(trim($sg['siteposition'])) . ")" : "";
    $dtaDXMod = ( trim($sg['dx']) !== "") ? " / " . strtoupper(trim($sg['dx'])) : "";
    $dtaDXMod .= ( trim($sg['dxmod']) ) ? " [" . strtoupper(trim($sg['dxmod'])) . "]" : "";
    $dtaMetsFrom = strtoupper(trim($sg['metssite']));
    $dtaMetsFrom .= ( trim($sg['metssitedx']) !== "" ) ? " / " . strtoupper(trim($sg['metssitedx'])) : "";
    $dtaSystemic = strtoupper(trim( $sg['systemicdx'] ));
    $dtaProcInst = $sg['dspprocureinstitution'];
    $dtaProcInst .= ( trim($sg['proctechnician']) !== "" ) ? " :: {$sg['proctechnician']}" : ""; 
    $dtaProcDte = trim($sg['dspprocurementdate']);
    $tohpr = trim($sg['tohprby']);
    $tohpr .= ( trim($sg['tohpron']) !== "" ) ? " :: {$sg['tohpron']}" : "";
    if ( trim($sg['prprid']) !== "" ) { 
      $selector = cryptservice("PR-" . $sg['prprid'] .  "-" . $sg['prrecordselector'], "e");         
      $oselector = cryptservice( $sg['prprid'], "e");      
      $pBtnTbl = "<table><tr><td class=prntIcon onclick=\"generateDialog('hprprviewer','{$selector}');\"><i class=\"material-icons\">pageview</i></td><td onclick=\"openOutSidePage('{$tt}/print-obj/pathology-report/{$oselector}');\" class=prntIcon><i class=\"material-icons\">print</i></td></tr></table>";
    } else { 
      $pBtnTbl = "<table><tr><td class=prntIcon><i class=\"material-icons\">crop_square</i></td></tr></table>"; 
    }
    $thisBtn = "<table><tr><td class=prntIcon><i class=\"material-icons\">crop_square</i></td></tr></table>";

    //CONSTITUENT TABLE
    $constitAmt = $segConstitList['ITEMSFOUND'];
    $constitTbl = "<table border=0 cellpadding=0 cellspacing=0 id=constitTbl><thead><tr><td colspan=25>Constituent Segments Found: {$constitAmt}</td></tr></thead><tbody>";
    foreach ( $segConstitList['DATA'] as $ckey => $cval ) {
      $bgs = trim(strtoupper( preg_replace( '/_/','',  $cval['bgs'])   ));   
      $segdate = ( trim( $cval['segstatusdate'] ) !== "" ) ? "({$cval['segstatusdate']})" : "";
      $assigned = $cval['investname']; 
        $assigned .= ( trim( $cval['assignedtocode'] ) !== "" ) ? " ({$cval['assignedtocode']})" : "";
        //$assigned .= ( trim( $cval['assignedtoreq'] ) !== "" ) ? " / {$cval['assignedtoreq']}" : "";
      $assigned = strtoupper($assigned);

      $invstPop = "";
      //onclick=\"generateDialog('irequestdisplay','" . cryptservice($assval['assignedreq']) . "');\" "

      if ( $cval['assignedtocode'] !== "BANK" ) { 
 
          $viewlink = ( trim($cval['assignedtoreq']) !== "" ) ? "<span class=rqstclickview onclick=\"generateDialog('irequestdisplay','" . cryptservice($cval['assignedtoreq']) . "');\">(View Request)</span>" : "" ;
          
          $invstPop = <<<THISDOC
                  <div class=popUpInfo>
                     <div class=rqstdsp1>Investigator: {$cval['investfullname']}</div>
                     <div class=rqstdsp2>Request: {$cval['assignedtoreq']} {$viewlink}</div>
                     <div class=rqstdsp3>Institution: {$cval['hinstitute']}</div>
                     <div class=rqstdsp4>Division: {$cval['investdivision']}</div>
                  </div>
                  
              
THISDOC;
      }
      $shipdoc = "";
      $shipPop = "";
      $shipicon = "";
      if ( trim($cval['shipdocrefid']) !== "" ) {
        $shipdoc = substr('000000' . $cval['shipdocrefid'],-6);
        $shipicon = "<i class=\"material-icons constiticon\">local_shipping</i>";
        $shipPop = "<div class=popUpShipInfo><table><tr><td>Ship Date: {$cval['shippeddate']} </td></tr><tr><td>Shipment Doc: {$shipdoc}  </td></tr></table>";
      }     
      $hprs = ( (int)$cval['tohpr'] === 1 ) ? "<i class=\"material-icons constiticon\">brightness_high</i>" : ""; 
      $hprcomp = ( (int)$cval['hprslideread'] === 1 ) ? "<i class=\"material-icons constiticon\">check_circle</i>" : "";
 
      $constitTbl .= <<<TBLROW
<tr>
  <td style="width: 1vw;">{$hprs}</td>
  <td style="width: 1vw;">{$hprcomp}</td>
  <td>{$bgs}</td>
  <td><div class=constitInfoHolder> <div class=primaryInfo>{$cval['segstatus']}</div> <div class=popUpInfo>Status Date: {$segdate}</div> </div></td>
  <td><div class=constitInfoHolder> <div class=primaryInfo>{$cval['prepmethod']}</div> <div class=popUpInfo>{$cval['preparation']}</div> </div> </td>
  <td>{$cval['metricdsp']}</td>
  <td><div class=constitInfoHolder> <div class=primaryInfo>{$assigned} </div>{$invstPop} </div></td>
  <td style="width: 1vw;"><div class=constitInfoHolder> <div class=primaryInfo>{$shipicon} </div>{$shipPop} </div></td>
</tr>
TBLROW;
    } 
    $constitTbl .= "<tbody></table>";
    //END CONSTITUENT TABLE
    //START ASSOCIATIVE GROUP TABLE
    $assAmt = $segAssGroup['ITEMSFOUND'];
    //<th>Uninvolved</th>
    //<td valign=top>{$aval['uninvolvedind']}</td>
    $wholeAssTbl = "<table border=0 cellpadding=0 cellspacing=0 id=wholeAssTbl><thead><tr><td colspan=25>Biogroups Found in Associative Group: {$assAmt}</td></tr><tr><th>Biogroup</th><th>Designation</th><th>A/R/S</th><th>HPR<br>Decision</th><th>Constituent<br>Segments</th></tr></thead><tbody>";
    foreach ( $segAssGroup['DATA'] as $akey => $aval ) {
    $wholeAssTbl .= <<<ASSROW
<tr>
  <td valign=top>{$aval['bsreadlabel']}</td>
  <td valign=top>{$aval['specimencategory']}<br>
                 {$aval['site']}<br>
                 {$aval['dx']}<br>
                 {$aval['metssite']}</td>
  <td valign=top>{$aval['ars']}</td>
  <td valign=top>{$aval['hprdecision']}<br><span class=smlFont>({$aval['hpron']})</span></td>
  <td valign=top><center>{$aval['nbrOfSegments']}</td>
</tr>
ASSROW;
    }    
    $wholeAssTbl .= "</tbody></table>";
    //END ASSOCIATIVE GROUP TABLE
    //START PAST REVIEW TABLE
    //$segHPRLines 
    if ( (int)$segHPRLines['ITEMSFOUND'] > 0 ) {
      $pastAmt = $segHPRLines['ITEMSFOUND'];
      $pastHPRTbl = "<table border=0 cellpadding=0 cellspacing=0 id=pastHPRTbl><thead><tr><td colspan=25>Past Reviews: {$pastAmt} (Click the line to view details)</td></tr><tr> <th>Decision</th> <th>Reviewer<br>Date Performed</th> <th>Slide<br>Read</th> <th>Review Designation</th> </tr></thead><tbody>";
      foreach ( $segHPRLines['DATA'] as $hkey => $hval ) {
       $decision = ( strtoupper(trim($hval['decision'])) === strtoupper(trim($hval['vocabularydecision'])) ) ? strtoupper(trim($hval['decision'])) : strtoupper(trim($hval['decision'])) . "<br><span class=smlFont>(Vocabulary: " . strtoupper(trim($hval['vocabularydecision'])) . ")</span>";

    $pastHPRTbl .= <<<ASSROW
<tr onclick="generateDialog('datacoordhprdisplay','{$hval['biohpr']}');">
  <td valign=top>{$decision}</td>
  <td valign=top>{$hval['reviewer']}<br><span class=smlFont>({$hval['reviewedon']})</td>
  <td valign=top>{$hval['slideread']}</td>
  <td valign=top>{$hval['speccat']}<br>{$hval['site']}<br>{$hval['dx']}</td>
</tr>
ASSROW;
      }
      $pastHPRTbl .= "</tbody></table>";
    } else {
      $pastHPRTbl = "<h2>No Past Reviews for {$sg['biosamplelabel']} Exist</h2>";
    }
    //END PAST REVIEW TABLE

    $docSide = <<<DOCSIDE
<div id=dspTabContent0 class=HPRReviewDocument style="display: block;">
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Pathology Report for {$sg['biosamplelabel']}</td><td align=right>{$pBtnTbl}</td></tr></table></div>
<div id=dspPathologyRptTxt>{$sg['pathologyreporttext']}<p></div>
</div>

<div id=dspTabContent1 class=HPRReviewDocument>
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Constituent Segments of {$sg['biosamplelabel']}</td><td align=right>{$thisBtn}</td></tr></table></div>
<div id=dspConstituentst> {$constitTbl} </div>
</div>


<div id=dspTabContent2 class=HPRReviewDocument>
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Past HPReviews for Biogroup {$sg['biosamplelabel']}</td><td align=right>{$thisBtn}</td></tr></table></div>
<div id=dspPastHPR> {$pastHPRTbl} </div>
</div>


<div id=dspTabContent3 class=HPRReviewDocument>
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Images &amp; Files</td><td align=right>{$thisBtn}</td></tr></table></div>
IMAGES &amp; FILES ARE NOT OPERATIONAL IN THIS RELEASE OF SCIENCESERVER.
</div>


<div id=dspTabContent4 class=HPRReviewDocument>
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Virtual Slide</td><td align=right>{$thisBtn}</td></tr></table></div>
THE VIRTUAL SLIDE IS NON-OPERATIONAL IN THIS RELEASE OF SCIENCESERVER.
</div>


<div id=dspTabContent5 class=HPRReviewDocument>  
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Associative Biogroups to Biogroup {$sg['biosamplelabel']}</td><td align=right>{$thisBtn}</td></tr></table></div>
<div id=dspAssGroups>{$wholeAssTbl}</div>
</div>

DOCSIDE;

$procMetrics = <<<PROCMETRICS
<div class=dspDocTitle><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>Preliminary Diagnosis/Procurement Metrics for Biogroup {$sg['biosamplelabel']}</td><td align=right>{$thisBtn}</td></tr></table></div>
<div id=prelimDX> 
<table border=0 cellspacing=0 cellpadding=0 id=HPRPreLimTbl> 
<tr><td colspan=4 class=hprPreLimFldLbl>Diagnosis Designation</td></tr>
<tr><td colspan=4 class=hprPreLimDtaFld valign=top>{$dtaSpecCat} {$dtaSiteSub} {$dtaDXMod}&nbsp;</td></tr>
<tr><td colspan=2 class="hprPreLimFldLbl rightEndCap">METS From</td><td colspan=2 class="hprPreLimFldLbl">Systemic/Co-Mobid</td></tr>
<tr><td colspan=2 class="hprPreLimDtaFld rightEndCap" valign=top>{$dtaMetsFrom}&nbsp;</td><td colspan=2 class=hprPreLimDtaFld valign=top>{$dtaSystemic}&nbsp;</td></tr>
<tr><td colspan=4 class=hprPreLimFldLbl>Question for Reviewer</td></tr>
<tr><td colspan=4 class=hprPreLimDtaFld style="height: 4vh;" valign=top><div style="height: 4vh; overflow: auto; color: rgba(237, 35, 0,1);">{$dtaQstn}</div></td></tr>
<tr><td class="hprPreLimFldLbl rightEndCap">Donor Age</td><td colspan=2 class="hprPreLimFldLbl rightEndCap">Donor Race</td><td class=hprPreLimFldLbl>Donor Sex</td></tr>
<tr><td class="hprPreLimDtaFld rightEndCap" valign=top>{$dtaAge}</td><td colspan=2 class="hprPreLimDtaFld rightEndCap" valign=top>{$dtaRace}</td><td class=hprPreLimDtaFld valign=top>{$dtaSex}</td></tr>
<tr><td class="hprPreLimFldLbl rightEndCap twentyfive">CX/RX</td><td class="hprPreLimFldLbl rightEndCap twentyfive">Pathology Report/Informed Consent</td><td class="hprPreLimFldLbl rightEndCap twentyfive">Uninvolved Indicator</td><td class=hprPreLimFldLbl>Procedure</td></tr>
<tr><td class="hprPreLimDtaFld rightEndCap twentyfive" valign=top>{$dtaCXRX}</td><td class="hprPreLimDtaFld rightEndCap twentyfive" valign=top>{$dtaPRIC}</td><td class="hprPreLimDtaFld rightEndCap twentyfive" valign=top>{$dtaUninv}</td><td class=hprPreLimDtaFld valign=top>{$dtaProcedure}</td></tr>
<tr><td colspan=2 class="hprPreLimFldLbl rightEndCap">Biosample Comment</td><td colspan=2 class=hprPreLimFldLbl>Segment Comment</td></tr>
<tr><td class="hprPreLimDtaFld rightEndCap" style="height: 6vh;" valign=top colspan=2><div style="height: 6vh; overflow: auto;">{$dtaBGCmts}</div></td><td class=hprPreLimDtaFld style="height: 6vh;" valign=top colspan=2><div style="height: 6vh; overflow: auto;">{$dtaSGCmt}</div></td></tr>
<tr><td colspan=4 align=right style="padding: .5vh .5vw 0 0;"> <table border=0 id=submitTbl><tr><td>Submitted</td><td colspan=2>{$tohpr}</td></tr></table> </td></tr>
</table>
</div>

PROCMETRICS;
    
    return array( "techMetrics" => $procMetrics, "topLineAnnouncement" => "{$dspSlide}", "documentMetrics" => $docSide );
}

function buildHPRTrayDisplay( $rqst, $usr ) { 
  $si = serverIdent;
  $sp = serverpw;
  $dta = json_decode(callrestapi("GET", dataTree . "/hpr-request-code/{$rqst}", $si, $sp), true);
  if ((int)$dta['ITEMSFOUND'] === 1) { 
    //GET WORKBENCH TRAY 
    $pdta = json_encode(array('srchTrm' => $dta['DATA'][0]));
    $sidedta = json_decode(callrestapi("POST", dataTree . "/data-doers/hpr-workbench-side-panel",serverIdent, serverpw, $pdta), true);          
    if ((int)$sidedta['ITEMSFOUND'] < 1) { 
        //NO SLIDES FOUND
        $pg = "<div id=hprwbHeadErrorHolder><H1>{$sidedta['MESSAGE'][0]} - See a CHTNEastern Staff if you feel this is incorrect.</div>";
    } else {
        //SIDE PANEL BUILD
        $cellCntr = 0;
        $sidePanelTblInner = "<tr class=rowBacker>";
        $slideCntr = 0;
        $slideDone = 0;
        foreach ($sidedta['DATA'] as $skey => $sval) {
           $searchtype = $sval['srchtype'];
           $tray = $sval['tray'];
           $freshDsp = ((int)$sval['freshcount'] > 0) ? "[CONTAINS DIRECT SHIPMENT]" : "";
           $cntr = ($skey + 1); 
           $slideidentifier = cryptservice(  "{$sval['segmentid']}::{$sval['pbiosample']}" );
           $clickAction = " onclick=\"navigateSite('hpr-review/{$rqst}/{$slideidentifier}');\" ";
           $readYet = ( $sval['hprslideread'] !== 'N' ) ? "<i class=\"material-icons readyes\">check_circle</i>" : "<i class=\"material-icons readno\">error</i>";
           if ( $sval['hprslideread'] !== 'N' ) { $slideDone++; }
           $pastHPRDsp = "<tr><td>&nbsp;</td></tr>";
           if ( trim($sval['recentdecision']) !== "" ) { 
               $pastHPRDsp = "<tr>"
                   . "<td valign=top class=slidedate colspan=3><b>Recent Decision</b>: " . strtoupper(trim($sval['recentdecision'])) . " "
                   . " / <b>Slide Read</b>: " . preg_replace('/_/','',strtoupper(trim($sval['recentslideread']))) . " "
                   . " on " . strtoupper(trim($sval['recentreviewon'])) . "</td>"
                   . "</tr>";
           } 
           if ( $cellCntr === 2 ) {
             $sidePanelTblInner .= "</tr><tr class=rowBacker>";
             $cellCntr = 0;
           }
           $sidePanelTblInner .= <<<SLIDELINE
<td {$clickAction} class="rowHolder dspSlideTrayCell" valign=top>
    <table border=0 class=hprSlideDsp>
      <tr>
        <td rowspan=4 class=slideicon>{$readYet}</td>
        <td colspan=3 class=bgsslidenbr>{$sval['bgs']}</td>
      </tr>
      <tr><td colspan=3 class=slidedesignation valign=top>{$sval['designation']}</td></tr>
      <tr><td valign=top class=slidedate><b>Procurement</b>: {$sval['procurementdate']}</td><td valign=top class=slidetech><b>Tech</b>: {$sval['procuringtech']}</td></tr>
      {$pastHPRDsp}
      <tr><td valign=top colspan=4 class=slidefreshdsp>{$freshDsp}</td></tr>
    </table>
</td>
SLIDELINE;
          
           $cellCntr++;   
           $slideCntr++;
        } 
        $sidePanelTblInner .= "</tr>";
        
        $rtnTrayBtn = "";
        if ( $searchtype === 'T' ) {      
            //MARK TRAY 'WITHREVIEWER'
            //TODO:  MAKE THIS A WEBSERVICE
            require(serverkeys . "/sspdo.zck");
            $chkSQL = "SELECT hprtraystatus FROM four.sys_inventoryLocations where scancode = :trayscancode";
            $chkRS = $conn->prepare($chkSQL); 
            $chkRS->execute(array(':trayscancode' => $tray));
            $chk = $chkRS->fetch(PDO::FETCH_ASSOC); 
            if ( $chk['hprtraystatus'] !== 'WITHREVIEWER' ) { 
              $traySTSSQL = "update four.sys_inventoryLocations set hprtraystatus = 'WITHREVIEWER', hprtraystatusby = :usr, hprtraystatuson = now() where scancode = :scncode";
              $traySTSRS = $conn->prepare($traySTSSQL);
              $traySTSRS->execute(array(':usr' => $usr->userid, ':scncode' => $tray)); 
              $tryHisSQL = "insert into masterrecord.history_hpr_tray_status (trayscancode, tray, traystatus, historyon, historyby) values(:trayscancode, :tray, :traystatus, now(), :historyby)";
              $tryHisRS = $conn->prepare($tryHisSQL);
              $tryHisRS->execute(array(':trayscancode' => $tray, ':tray' => $tray, ':traystatus' => 'WITHREVIEWER', ':historyby' => $usr->userid));
            }
          //RETURN TRAY BUTTON
          $rtnTrayBtn = "<table class=tblBtn id=btnHPRRtnTray style=\"width: 6vw;\" onclick=\"generateDialog('trayreturndialog','{$tray}');\"><tr><td style=\"font-size: 1.3vh;\"><center>Return Tray</td></tr></table>";
        }
        
        $pbarPrc = (( (int)$slideDone / (int)$slideCntr ) * 42) ;
        $pbarDsp = round((((int)$slideDone / (int)$slideCntr) * 100));

        $sidePanelTbl = "<center><table border=0 cellspacing=0 cellpadding=0 id=sidePanelSlideListTbl>";      
        $sidePanelTbl .= "<tr><td colspan=2><div id=progressBarHolder><div id=progressBarDsp style=\"width: {$pbarPrc}vw;\"></div></div></td></tr>"; 
        $sidePanelTbl .= "<tr><td class=slidesfound colspan=2><b>Tray Progress</b>: {$slideDone} of {$slideCntr} ({$pbarDsp}%)   </td></tr>";  
        $sidePanelTbl .= "<tr><th class=workbenchheader colspan=2>{$sidedta['MESSAGE'][0]}</th></tr>";
        $sidePanelTbl .= "<tr><td colspan=2 align=right>{$rtnTrayBtn}</td></tr>";
        $sidePanelTbl .= $sidePanelTblInner;
        $sidePanelTbl .= "<tr><td colspan=2 align=right>{$rtnTrayBtn}</td></tr>";
        $sidePanelTbl .= "</table>";
        //SIDE PANEL BUILD END               
        $pg = <<<PGCONTNT
    <div id=sidePanel>{$sidePanelTbl}</div>
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
  return $pg;
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

$hprtraysdta = json_decode(callrestapi("GET", dataTree . "/inventory-simple-hpr-tray-list",$si,$sp),true);
$hprtrays = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryHPRInvLoc','','');\" class=ddMenuClearOption>[clear]</td></tr>";
foreach ($hprtraysdta['DATA'] as $hprtval) {
  $hprtrays .= "<tr><td onclick=\"fillField('qryHPRInvLoc','{$hprtval['scancode']}','{$hprtval['hprstatus']}');\" class=ddMenuItem>{$hprtval['hprstatus']}</td></tr>";
}
$hprtrays .= "</table>";


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
<tr><td class=fldLabel>Biogroup Number</td><td class=fldLabel>Procuring Institution</td><td class=fldLabel>Segment Status</td><td class=fldLabel>QMS Status</td><td class=fldLabel>HPR Location</td></tr>
<tr>
  <td><input type=text id=qryBG class="inputFld" style="width: 20vw;"></td>
  <td><div class=menuHolderDiv><input type=hidden id=qryProcInstValue><input type=text id=qryProcInst READONLY class="inputFld" style="width: 20vw;"><div class=valueDropDown style="min-width: 20vw;">{$proc}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qrySegStatusValue><input type=text id=qrySegStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$seg}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qryHPRStatusValue><input type=text id=qryHPRStatus READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$hpr}</div></div></td>
<td><div class=menuHolderDiv><input type=hidden id=qryHPRInvLocValue><input type=text id=qryHPRInvLoc READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$hprtrays}</div></div></td>
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

function generatePageTopBtnBar($whichpage, $whichusr, $additionalinfo = "") { 

//TODO:  DUMP THE BUTTONS INTO A DATABASE AND GRAB WITH A WEBSERVICE    
//TODO:  MOVE ALL JAVASCRIPT TO JAVASCRIPT FILE

switch ($whichpage) { 
case 'shipdocedit':
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnCreateNewSD border=0><tr><td><i class="material-icons">fiber_new</i></td><td>New Ship-Doc</td></tr></table></td>       
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnVoidSD border=0><tr><td><i class="material-icons">block</i></td><td>Void</td></tr></table></td>         
    $innerBar = <<<BTNTBL
<tr>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSDLookup border=0><tr><td><i class="material-icons">layers_clear</i></td><td>Look-Up</td></tr></table></td> 
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSaveSD border=0><tr><td><i class="material-icons">save</i></td><td>Save</td></tr></table></td> 
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintSD border=0><tr><td><i class="material-icons">print</i></td><td>Print</td></tr></table></td>             
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSegment border=0><tr><td><i class="material-icons">add_circle_outline</i></td><td>Add Segment</td></tr></table></td>         
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnShipOverride border=0><tr><td><i class="material-icons">local_shipping</i></td><td>Ship Override</td></tr></table></td>         
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSO border=0><tr><td><i class="material-icons">monetization_on</i></td><td>Add Sales Order</td></tr></table></td>         
</tr>        
BTNTBL;
    break;
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

case 'biogroupdefinition':
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnVoidSeg><tr><td><i class="material-icons">cancel</i></td><td>Void Segment</td></tr></table></td>
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnEditDX><tr><td><i class="material-icons">edit</i></td><td>Edit DX</td></tr></table></td>
    //  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnEditSeg><tr><td><i class="material-icons">edit</i></td><td>Edit Segment</td></tr></table></td>
    // <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnQMSActions><tr><td><i class="material-icons">thumbs_up_down</i></td><td>QMS Actions</td></tr></table></td>
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPHIRecord><tr><td><i class="material-icons">group</i></td><td>Encounter</td></tr></table></td>
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnHPRRecord><tr><td><i class="material-icons">gavel</i></td><td>View HPR</td></tr></table></td>
  //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAssocGrp><tr><td><i class="material-icons">group_work</i></td><td>Associative</td></tr></table></td>
  //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPristine><tr><td><i class="material-icons">change_history</i></td><td>Pristine</td></tr></table></td>
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSegment><tr><td><i class="material-icons">add_circle_outline</i></td><td>Add Segment</td></tr></table></td> 
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

case 'qmsactionwork':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork onclick="window.location.href= '{$additionalinfo}'" ><tr><td><i class="material-icons">layers_clear</i></td><td>Queue List</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnSendEmail><tr><td><i class="material-icons">textsms</i></td><td>Email</td></tr></table></td>
  <td class=topBtnHolderCell onclick="revealPR();"><table class=topBtnDisplayer id=btnRevealPR><tr><td><i class="material-icons">arrow_right_alt</i></td><td>Pathology Report</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork><tr><td><i class="material-icons">done_all</i></td><td>Mark QA Complete</td></tr></table></td>
</tr>
BTNTBL;

  //<td class=topBtnHolderCell>
  //  <div class=ttholder>
  //    <table class=topBtnDisplayer id=btnDisplayByStatus><tr><td><i class="material-icons">view_list</i></td><td>QA Actions</td></tr></table>
  //    <div class=tt>
  //      <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>View HPR Record&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>View Pristine Procurement Record&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem onclick="revealPR();"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>View Pathology Report&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>View Donor Record&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Change Vocabulary&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Reset Vocabulary&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Change METS From&nbsp;&nbsp;&nbsp</td></tr>     
  //        <tr class=btnBarDropMenuItem><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Change Systemic/Co-Mobid&nbsp;&nbsp;&nbsp</td></tr>     
  //      </table>
  //    </div>  
  //  </div>
  //</td>           

    break;


case 'qmsactionworkincon':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork onclick="window.location.href= '{$additionalinfo}'" ><tr><td><i class="material-icons">layers_clear</i></td><td>Queue List</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnSendEmail><tr><td><i class="material-icons">textsms</i></td><td>Email</td></tr></table></td>

</tr>
BTNTBL;
    break;
case 'qmsaction':




//TODO:  Come up with dynamic way of doing this
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGrid><tr><td><i class="material-icons">layers_clear</i></td><td>Refresh</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnSendEmail><tr><td><i class="material-icons">textsms</i></td><td>Email</td></tr></table></td>
  <td class=topBtnHolderCell>
    <div class=ttholder>
      <table class=topBtnDisplayer id=btnDisplayByStatus><tr><td><i class="material-icons">view_list</i></td><td>Display By Status</td></tr></table>
      <div class=tt>
        <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
          <tr class=btnBarDropMenuItem id=btnDisplay_ALL onclick="navigateSite('qms-actions');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: ALL&nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnDisplay_CONFIRM onclick="navigateSite('qms-actions/display/confirm');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: Confirms &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnDisplay_ADD onclick="navigateSite('qms-actions/display/additional');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: Confirm Additionals &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnDisplay_denied onclick="navigateSite('qms-actions/display/denied');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: Denied Cases &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnDisplay_denied onclick="navigateSite('qms-actions/display/unusable');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: Cases marked Unusable &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnDisplay_denied onclick="navigateSite('qms-actions/display/inconclusive');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: Inconclusive &nbsp;&nbsp;&nbsp</td></tr>     
        </table>
      </div>  
    </div>
  </td>           

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

case 'hprreviewactionstray':

$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell onclick="navigateSite('hpr-review');"><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">layers_clear</i></td><td>New Review</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">textsms</i></td><td>Assistance</td></tr></table></td>
 <!--TODO: THIS BUTTON WILL RETURN THE TRAY FOR PROCESSING ...  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">textsms</i></td><td>{$additionalinfo}</td></tr></table></td> //-->
</tr>
BTNTBL;


break; 

case 'hprreviewactions': 
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell onclick="navigateSite('hpr-review/{$additionalinfo}');"><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">arrow_back_ios</i></td><td>Back To Tray</td></tr></table></td>
  <td class=topBtnHolderCell onclick="navigateSite('hpr-review');"><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">layers_clear</i></td><td>New Review</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">textsms</i></td><td>Assistance</td></tr></table></td>
  <!-- TODO: ADD BACK IN TO A LATER RELEASE :  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnNewHPRReview><tr><td><i class="material-icons">change_history</i></td><td>Request Vocabulary Change</td></tr></table></td> //-->
</tr>
BTNTBL;
break; 
case 'coordinatorResultGrid':
  //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltAssignSample><tr><td><i class="material-icons">person_add</i></td><td>Assign</td></tr></table></td>
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltNew><tr><td><i class="material-icons">fiber_new</i></td><td>New Search</td></tr></table></td>

  <td class=topBtnHolderCell>
    <div class=ttholder><table class=topBtnDisplayer id=btnBarRsltPrintAction><tr><td><i class="material-icons">print</i></td><td>Print</td></tr></table>
    <div class=tt>
      <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
        <tr class=btnBarDropMenuItem id=btnPrintAllPathologyRpts><td><i class="material-icons">arrow_right</i></td><td>Print Selected Pathology Reports&nbsp;&nbsp;&nbsp</td></tr>     
        <tr class=btnBarDropMenuItem id=btnPrintAllShipDocs><td><i class="material-icons">arrow_right</i></td><td>Print Selected Ship-Docs&nbsp;&nbsp;&nbsp</td></tr>     
        <tr class=btnBarDropMenuItem id=btnPrintAllLabels><td><i class="material-icons">arrow_right</i></td><td>Print Selected Labels&nbsp;&nbsp;&nbsp</td></tr>     
      </table>
    </div>
    </div>
  </td>

  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltExport><tr><td><i class="material-icons">import_export</i></td><td>Export Results</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltToggle><tr><td><i class="material-icons">get_app</i></td><td>Toggle Select</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltParams><tr><td><i class="material-icons">settings</i></td><td>View Parameters</td></tr></table></td>


  <td class=topBtnHolderCell>
    <div class=ttholder>
      <table class=topBtnDisplayer id=btnAssignGrouping><tr><td><i class="material-icons">insert_link</i></td><td>Assignment &amp; Linkage</td></tr></table>
      <div class=tt>
        <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
          <tr class=btnBarDropMenuItem id=btnBarRsltAssignSample><td><i class="material-icons">arrow_right</i></td><td>Assign Segments to Requests &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnBarRsltRequestLink><td><i class="material-icons">arrow_right</i></td><td>Create Request Linkage &nbsp;&nbsp;&nbsp</td></tr>     
        </table>
      </div>  
    </div>
  </td>           


  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltMakeSD><tr><td><i class="material-icons">local_shipping</i></td><td>Create Shipdoc</td></tr></table></td>  


  <td class=topBtnHolderCell>
    <div class=ttholder>
      <table class=topBtnDisplayer id=btnHPRProcess><tr><td><i class="material-icons">assignment</i></td><td>HPR Process Override</td></tr></table>
      <div class=tt>
        <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
          <tr class=btnBarDropMenuItem id=btnBarRsltSubmitHPR><td><i class="material-icons">arrow_right</i></td><td>Submit Slide Tray to HPR (Override)&nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnBarRsltCheckInTray><td><i class="material-icons">arrow_right</i></td><td>Check-In Slide Tray (Override)&nbsp;&nbsp;&nbsp</td></tr>     
        </table>
      </div>  
    </div>
  </td>           

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

function bldInvestigatorRequestDialog( $dialogid, $passedData ) { 

  require(serverkeys . "/sspdo.zck");    
  //{"whichdialog":"irequestdisplay","objid":"WENLK0VpZlZtSjhON3RnU2NVV3NNUT09","dialogid":"8luIRRyc5qU4QHp"} 
  $pdta = json_decode($passedData,true); 
  $rqstnbr = cryptservice( $pdta['objid'] , 'd' );

  //TODO: Turn ALL THIS INTO Webservice
  $reqSQL = <<<REQSQL
SELECT 
i.investid, ifnull(i.invest_fname,'') as investfname, ifnull(i.invest_lname,'') as investlname, ifnull(i.invest_salutation,'') as investsaluation, ifnull(i.invest_title,'') as investtitle, ifnull(i.invest_degree,'') as investdegree
, ifnull(i.invest_homeinstitute,'') as investinstitution, ifnull(i.invest_institutiontype,'') as investinstitutiontype, ifnull(i.invest_division,'') as investdivision, ifnull(i.invest_chtn,'') as investchtn
, ifnull(i.invest_subgroup,'') as investsubgroup, ifnull(i.invest_status,'') as investstatus, ifnull(date_format(i.invest_statusdate,'%m/%d/%Y'),'') as investstatusdate, ifnull(i.invest_vip,'') as investvip
, ifnull(i.invest_subgroupcomm,'') as investsubgroupcomm, ifnull(date_format(i.invest_lastshipdate,'%m/%d/%Y'),'') as investlastshipdate, ifnull(date_format(i.invest_appreceivedate,'%m/%d/%Y'),'') as investappreceivedate
, ifnull(date_format(i.invest_appapprovedate,'%m/%d/%Y'),'') as investappapprovedate, ifnull(date_format(i.invest_appcompletedate,'%m/%d/%Y'),'') as investappcompletedate
, ifnull(date_format(i.invest_guidelinedate,'%m/%d/%Y'),'') as investguidelinedate, ifnull(date_format(i.invest_duareceivedate,'%m/%d/%Y'),'') as investduareceivedate, ifnull(i.invest_firstenterby,'') as investfirstenterby
, ifnull(date_format(i.invest_firstenterdate,'%m/%d/%Y'),'') as investfirstenterdate, ifnull(i.invest_networked,'') as investnetworked, ifnull(i.invest_nonnetworkreason,'') as investnonnetworkreason
, ifnull(date_format(i.invest_networkbydate,'%m/%d/%Y'),'') as investnetworkbydate, ifnull(i.invest_lastupdateby,'') as investlastupdateby
, pr.projid, ifnull(pr.proj_number,'') as projnumber, ifnull(pr.proj_title,'') as projtitle, ifnull(pr.proj_priority,'') as projpriority, ifnull(pr.proj_status,'') as projstatus
, ifnull(date_format(pr.proj_statusdate,'%m/%d/%Y'),'') as projstatusdate, ifnull(date_format(pr.proj_lastshipdate,'%m/%d/%Y'),'') as projlastshipdate, ifnull(pr.proj_firstenterby,'') as projfirstenterby
, ifnull(date_format(pr.proj_firstenterdate,'%m/%d/%Y'),'') as projfirstenterdate, ifnull(pr.proj_lastupdateby,'') as projlastupdateby, ifnull(date_format(pr.proj_lastupdatedate,'%m/%d/%Y'),'') as projlastupdatedate
, ifnull(pr.proj_irbtype,'') as projirbtype, ifnull(pr.proj_irbnumber,'') as projirbnumber, ifnull(date_format(pr.proj_irbreceivedate,'%m/%d/%Y'),'') as projirbreceivedate
, ifnull(date_format(pr.proj_irbexpiredate,'%m/%d/%Y'),'') as projirbexpiredate, ifnull(pr.proj_studynum,'') as projstudynum, ifnull(pr.proj_networked,'') as projnetworked
, ifnull(date_format(pr.proj_networkdate,'%m/%d/%Y'),'') as projnetworkdate, ifnull(pr.proj_comment,'') as projcomment
, rq.requestid, ifnull(rq.req_number,0) reqnumber, ifnull(rq.req_networked,'No') as reqnetworked, ifnull(date_format(rq.req_networkdate,'%m/%d/%Y'),'') as reqnetworkdate, ifnull(rq.req_networkcomment,'') as reqnetworkcomments
, ifnull(rq.req_status,'') as reqstatus, ifnull(date_format(rq.req_statusdate,'%m/%d/%Y'),'') as reqstatusdate, ifnull(rq.req_statuscomment,'') as reqstatuscomment, ifnull(rq.req_firstenterby,'') as reqfirstenteredby
, ifnull(date_format(rq.req_firstenterdate,'%m/%d/%Y'),'') as reqfirstenterdate, ifnull(date_format(rq.req_lastupdatedate,'%m/%d/%Y'), '') as reqlastupdatedate, ifnull(rq.req_lastupdateby,'') as reqlastupdateby
, ifnull(date_format(rq.req_lastshipdate,'%m/%d/%Y'),'') as reqlastshipdate, ifnull(rq.req_surgery,'No') as reqsurgery, ifnull(rq.req_surgeryunit,'') as reqsurgeryunit, ifnull(rq.req_postexcisiontime,'') as reqpostexcisiontime
, ifnull(rq.req_transplant,'No') as reqtransplant, ifnull(rq.req_transplantunit,'') as reqtransplantunit, ifnull(rq.req_autopsy,'No') as reqautopsy, ifnull(rq.req_autopsyunit,'') as reqautopsyunit
, ifnull(rq.req_postmortemtime,'') as reqpostmortemtime, ifnull(rq.req_trauma,'') as reqtrauma, ifnull(rq.req_posttraumatime,'') as reqposttraumatime, ifnull(rq.req_phlebotomy,'') as reqphlebotomy
, ifnull(rq.req_phlebotomyunit,'') as reqphlebotomyunit, ifnull(rq.req_postphltime,'') as reqpostphltime, ifnull(rq.req_agemin1,'') as reqagemin1, ifnull(rq.req_agemax1,'') as reqagemax1, ifnull(rq.req_ageunit1,'') as reqageunit1
, ifnull(rq.req_agemin2,'') as reqagemin2, ifnull(rq.req_agemax2,'') as reqagemax2, ifnull(rq.req_ageunit2,'') as reqageunit2, ifnull(rq.req_agemin3,'') as reqagemin3, ifnull(rq.req_agemax3,'') as reqagemax3
, ifnull(rq.req_ageunit3,'') as reqageunit3, ifnull(rq.req_race,'') as reqrace, ifnull(rq.req_gender,'') as reqsex

, ifnull(rq.req_diseasequalifier,'') as reqdiseasequalifier, ifnull(rq.req_diseaseclass,'') as diseaseclass
, ifnull(rq.req_histologictype,'') as reqhistologictype, ifnull(rq.req_subtype,'') as reqsubstype, ifnull(rq.req_subsite,'') as reqsubsite, ifnull(rq.req_anasitetype,'') as reqanasitetype
, ifnull(rq.req_tissuetype,'') as reqtissuetype, ifnull(rq.req_diseasename,'') as reqdiseasename

, ifnull(rq.req_anything,'') as reqanything
, ifnull(rq.req_normalfromcapt,'') as reqnormalfromcapt, ifnull(rq.req_hasmet,'No') as reqhasmets, ifnull(rq.req_patienthx,'') as reqpatienthx, ifnull(rq.req_chemodiffdz,'') as reqchemodiffdx
, ifnull(rq.req_chemocurrdz,'') as reqchemocurrdz, ifnull(rq.req_raddiffdz,'') as reqraddiffdz, ifnull(rq.req_radcurrdz,'') as reqradcurrdz, ifnull(rq.req_moletherapy,'') as reqmoletherapy
, ifnull(rq.req_biotherapy,'') as reqbiotherapy, ifnull(rq.req_radpriordz,'') as reqradpriordz, ifnull(rq.req_hasbf,'') as reqhasbf, ifnull(rq.req_hassolid,'') as reqhassolid, ifnull(rq.req_hasnat,'') as reqhasnat
, ifnull(rq.req_chartreview,'') as reqchartreview, ifnull(rq.req_vacctherapy,'') as reqvacctherapy, ifnull(rq.req_chemoyn,'') as reqchemoyn, ifnull(rq.req_radyn,'') as reqradyn, ifnull(rq.req_normalfromhtpt,'') as reqnormalfromhtpt
, ifnull(rq.req_hasother,'') as reqhasother, ifnull(rq.req_normalfromdzpt,'') as reqnormalfromdzpt, ifnull(rq.req_glscoreanyall,'') as reqglscoreanyall, ifnull(rq.req_glscore4,'') as reqglscore4
, ifnull(rq.req_glscore5,'') as reqglscore5, ifnull(rq.req_glscore6,'') as reqglscore6, ifnull(rq.req_glscore7,'') as reqglscore7, ifnull(rq.req_glscore8,'') as reqglscore8, ifnull(rq.req_glscore9,'') as reqglscore9
, ifnull(rq.req_ajccstageanyall,'') as reqajccstageanyall, ifnull(rq.req_ajccgrade1,'') as reqajccgrade1, ifnull(rq.req_ajccgrade2,'') as reqajccgrade2, ifnull(rq.req_ajccgrade3,'') as reqajccgrade3
, ifnull(rq.req_ajccgrade4,'') as reqajccgrade4, ifnull(rq.req_tissuecomment,'') as reqtissuecomment
FROM vandyinvest.investtissreq rq 
left join vandyinvest.investproj pr on rq.projid = pr.projid
left join vandyinvest.invest i on pr.investid = i.investid
where requestid = :requestnbr
REQSQL;

  $tissSQL = <<<TISSSQL
SELECT 
ifnull(tis_subtype,'') as tissubsite
, ifnull(tis_anasitetype,'') as tisanasitetype
, ifnull(tis_required,'') as tisrequired
, ifnull(tis_subsite,'') as tissubsite
, ifnull(tis_subtype,'') as tissubtype
, ifnull(tis_anyall,'') as tisanyall
, ifnull(tis_subsitequalifier,'') as tissubsitequalifier
, ifnull(tis_tissuetype,'') as tistissuetype
, ifnull(tis_dztissueid,'') as tisdztissueid
, ifnull(tis_hasuninvolved,'') as tishasuninvolved
, ifnull(tis_histotype,'') as tishistotype
, tissueid
FROM vandyinvest.eastern_tissuedetail where requestid = :requestid
TISSSQL;

  //TODO: DO DATA CHECKS TO MAKE SURE PROPER QUERY OBJECT
  //TODO: MAKE THIS A WEBSERVICE 
  $reqRS = $conn->prepare($reqSQL); 
  $reqRS->execute(array(':requestnbr' => $rqstnbr));
  //{"investchtn":"CHTN","investsubgroup":"""investvip":"","investsubgroupcomm":"""investnonnetworkreason":"","investnetworkbydate":"","investlastupdateby":"cellinin"
  //"projfirstenterby":"cellinin","projfirstenterdate":"11\/16\/2017","projlastupdateby":"cellinin","projlastupdatedate":"07\/02\/2019"
  //"reqnetworkcomments":"","reqstatuscomment":"","reqfirstenteredby":"cellinin","reqfirstenterdate":"11\/16\/2017","reqlastupdatedate":"07\/02\/2019","reqlastupdateby":"cellinin" 
  $req = $reqRS->fetch(PDO::FETCH_ASSOC);

  $tisRS = $conn->prepare($tissSQL); 
  $tisRS->execute(array('requestid' => $rqstnbr));
  $tis = array();

  $tisReqSQL = <<<PREPSQL
SELECT  
prepid
, ifnull(prp.prep_required,'') as prequired
, ifnull(prp.prep_frequency,'') as pfreq
, ifnull(prp.prep_frequencyunit,'') as pfrequnit

, ifnull(prp.prep_sizeh,'') as psizeh
, ifnull(prp.prep_sizew,'') as psizew
, ifnull(prp.prep_sizel,'') as psizel
, ifnull(prp.prep_sizeunit,'') as psizeunit
, ifnull(prp.prep_amount,'') as pamt
, ifnull(prp.prep_amountunit,'') as pamtunit
, ifnull(prp.prep_prepconc,'') as pconc

, ifnull(prp.prep_grouptype,'') as pgrptype
, ifnull(prp.prep_preptype,'') as ppretype

, ifnull(prp.prep_minshipcount,'') as pminship 
, ifnull(prp.prep_mincount,'') as pmincnt
, ifnull(prp.prep_maxamount,'') as pmaxcnt
, ifnull(prp.prep_prefercount,'') as pprefercnt
, ifnull(prp.prep_satdelivery,'') as psatdel

, ifnull(prp.prep_comment,'') as ppcmt
, ifnull(prp.prep_freshcomment,'') as pfrshcmt
, ifnull(prp.prep_shipinstr,'') as pshpinst
, ifnull(prp.prep_shipsameday,'') as pshipsameday

FROM vandyinvest.eastern_tissueprep prp where tissueid = :tissueid
PREPSQL;
  $tisReqRS = $conn->prepare($tisReqSQL);
  while ( $t = $tisRS->fetch(PDO::FETCH_ASSOC)) {
    $tisReqRS->execute(array(':tissueid' => $t['tissueid']));
    $tisPrep = array(); 
      while ( $p = $tisReqRS->fetch(PDO::FETCH_ASSOC)) { 
        $tisPrep[] = $p;
      }  
      $tis[] = array_merge($t,array('prep' => $tisPrep));  
  }
///END WEBSERVICE DATA
  
  $tprepTbl = "<table border=0 cellpadding=0 cellspacing=0 class=RQHolderTbl><tr><td class=RQHeaders>PREPARATION REQUIREMENTS (Scroll)</td></tr><tr><td> <div id=preprequirementdiv><table border=0 width=100%><tr><td class=headerInfoCell width=10px>#</td><td class=headerInfoCell>Required</td><td class=headerInfoCell>Any-All</td><td class=headerInfoCell>Tissue Type</td><td class=headerInfoCell>Site::Sub-Site</td><td class=headerInfoCell>Site Qualifier</td><td class=headerInfoCell>Histotype::Modifier</td><td class=headerInfoCell>Has<br>Uninvolved</td>   </tr>"; 
  $prepCntr = 1; 
  foreach ( $tis as $tkey => $tval ) { 

      $tss = ( trim($tval['tissubsite']) !== "" ) ? " :: {$tval['tissubsite']}" : "";
      $tmod = ( trim($tval['tissubtype']) !== "" ) ? " :: {$tval['tissubtype']}" : "";

      $innerprep = "";
      foreach ( $tval['prep'] as $pkey => $pval ) {
              $freq = ( trim($pval['pfreq']) !== "" ) ? "{$pval['pfreq']}" : "";
              $freq .= ( trim($pval['pfrequnit']) !== "" ) ? " {$pval['pfrequnit']}" : "";

              $amt = ( trim($pval['psizeh']) !== "" ) ? "{$pval['psizeh']}" : "";
              $amt .= ( trim($pval['psizew']) !== "" ) ? ( trim($amt) !== "" ) ? "x{$pval['psizew']}" : "{$pval['psizew']}" : ""; 
              $amt .= ( trim($pval['psizel']) !== "" ) ? ( trim($amt) !== "" ) ? "x{$pval['psizel']}" : "{$pval['psizel']}" : ""; 
              $amt .= ( trim($pval['psizeunit']) !== "" ) ? ( trim($amt) !== "" ) ? " {$pval['psizeunit']}" : "{$pval['psizeunit']}" : ""; 
   
              $amta = ( trim($pval['pamt']) !== "" ) ? "{$pval['pamt']}" : "";
              $amta .= ( trim($pval['pamtunit']) !== "" ) ? ( trim($amta) !== "" ) ? " {$pval['pamtunit']}" : "{$pval['pamtunit']}" : "";
              $amta = ( trim($amta) !== "" && trim($amt) !== "" ) ? " :: " . $amta : $amta;

              $minmax = ( trim($pval['pmincnt']) !== "" ) ? "{$pval['pmincnt']}" : "";
              $minmax .= ( trim($pval['pmaxcnt']) !== "" ) ? "::{$pval['pmaxcnt']}" : "";
 
              $ship = ( trim($pval['pminship']) !== "" ) ? "{$pval['pminship']}" : "";
              $ship .= ( trim($pval['pprefercnt']) !== "" ) ? "::{$pval['pprefercnt']}" : "";

              $innerprep .= "<tr><td class=dataDisplay>{$pval['prequired']}</td><td class=dataDisplay>{$pval['pgrptype']}</td><td class=dataDisplay>{$pval['ppretype']}</td><td class=dataDisplay>{$freq}</td><td class=dataDisplay>{$amt}{$amta}</td><td class=dataDisplay>{$pval['pconc']}</td><td class=dataDisplay>{$minmax}</td><td class=dataDisplay>{$ship}</td><td class=dataDisplay>{$pval['psatdel']}</td><td class=dataDisplay>{$pval['ppcmt']}</td><td class=dataDisplay>{$pval['pfrshcmt']}</td><td class=dataDisplay>{$pval['pshipinst']}</td><td class=dataDisplay>{$pval['pshipsameday']}</td></tr>";
      } 
      $prepTbl = ( trim($innerprep) !== "" ) ? "<table border=0 width=100%><tr><td class=smlHeadCell>Required</td><td class=smlHeadCell>Group</td><td class=smlHeadCell>Type</td><td class=smlHeadCell>Freq</td><td class=smlHeadCell>Amount</td><td class=smlHeadCell>Conc.</td><td class=smlHeadCell>Min-Max</td><td class=smlHeadCell>Ship</td><td class=smlHeadCell>Saturday?</td><td class=smlHeadCell>Prep Comments</td><td class=smlHeadCell>Fresh Comments</td><td class=smlHeadCell>Ship Instructions</td><td class=smlHeadCell>Same Day Instructions</td></tr>{$innerprep}</table>" : "";

      
      $tprepTbl .= "<tr><td class=dataDisplay>{$prepCntr}</td><td class=dataDisplay>{$tval['tisrequired']}</td><td class=dataDisplay>{$tval['tisanyall']}</td><td class=dataDisplay>{$tval['tistissuetype']}</td><td class=dataDisplay>{$tval['tisanasitetype']}{$tss}</td><td class=dataDisplay>{$tval['tissubsitequalifier']}</td><td class=dataDisplay>{$tval['tishistotype']}{$tmod}</td><td class=dataDisplay>{$tval['tishasuninvolved']}</td></tr><tr><td colspan=8 style=\"padding: 0 0 0 1vw;\">{$prepTbl}</td></tr>";
      $prepCntr++;
  }
  $tprepTbl .= "</table></div></td></tr></table>"; 
  $tisdsp = $tprepTbl;

  $investname = ( trim($req['investsaluation']) !== "" ) ? "{$req['investsaluation']} " : "";
  $investname .= ( trim($req['investfname']) !== "" ) ? "{$req['investfname']}" : "";
  $investname .= ( trim($req['investlname']) !== "" ) ? " {$req['investlname']}" : "";
  $investname .= ( trim($req['investdegree']) !== "" ) ? " , {$req['investdegree']}" : "";
 // $investname .= ( trim($req['investtitle']) !== "" ) ? " ({$req['investtitle']})" : "";

  $istatDate = ( trim($req['investstatusdate']) !== "" ) ? "<br>({$req['investstatusdate']})" : "";
  $projnbr = ( trim($req['projnumber']) !== "" ) ? substr(("000" . $req['projnumber']),-3) : "";
  $projStsDate = ( trim($req['projstatusdate']) !== "" ) ? "<br>({$req['projstatusdate']})" : "";
  $projNetDate = ( trim($req['projnetworkdate']) !== "" ) ? "<br>({$req['projnetworkdate']})" : "";
  $irbstuff = ( trim($req['projirbreceivedate']) !== "" ) ? "{$req['projirbreceivedate']}" : "";
  $irbstuff .= ( trim($req['projirbexpiredate']) !== "" ) ? "&nbsp::&nbsp;{$req['projirbexpiredate']}" : "";
 
  $reqnbr = substr(('000' . $req['reqnumber']),-3);
  $reqstsDate = ( trim($req['reqstatusdate']) !== "" ) ? "<br>{$req['reqstatusdate']}" : "";
  $reqNetDate = ( trim($req['reqnetworkdate']) !== "" ) ? "<br>{$req['reqnetworkdate']}" : "";

  $agedsp = ( trim($req['reqagemin1']) !== "" ) ? "{$req['reqagemin1']}" : "";
  $agedsp .= ( trim($req['reqagemax1']) !== "" ) ? "-{$req['reqagemax1']}" : "";
  $agedsp .= ( trim($req['reqageunit1']) !== "" ) ? " {$req['reqageunit1']}" : ""; 

  $age2dsp = ( trim($req['reqagemin2']) !== "" ) ? "{$req['reqagemin2']}" : "";
  $age2dsp .= ( trim($req['reqagemax2']) !== "" ) ? "-{$req['reqagemax2']}" : "";
  $age2dsp .= ( trim($req['reqageunit2']) !== "" ) ? " {$req['reqageunit2']}" : ""; 

  $age3dsp = ( trim($req['reqagemin3']) !== "" ) ? "{$req['reqagemin3']}" : "";
  $age3dsp .= ( trim($req['reqagemax3']) !== "" ) ? "-{$req['reqagemax3']}" : "";
  $age3dsp .= ( trim($req['reqageunit3']) !== "" ) ? " {$req['reqageunit3']}" : ""; 

  $gls = ( trim($req['reqglscore4']) !== "" ) ? "{$req['reqglscore4']}" : "";
  $gls .= ( trim($req['reqglscore5']) !== "" ) ? ( trim($gls) !== "" ) ? ", {$req['reqglscore5']}" :  "{$req['reqglscore5']}" : "";
  $gls .= ( trim($req['reqglscore6']) !== "" ) ? ( trim($gls) !== "" ) ? ", {$req['reqglscore6']}" :  "{$req['reqglscore6']}" : "";
  $gls .= ( trim($req['reqglscore7']) !== "" ) ? ( trim($gls) !== "" ) ? ", {$req['reqglscore7']}" :  "{$req['reqglscore7']}" : "";
  $gls .= ( trim($req['reqglscore8']) !== "" ) ? ( trim($gls) !== "" ) ? ", {$req['reqglscore8']}" :  "{$req['reqglscore8']}" : "";
  $gls .= ( trim($req['reqglscore9']) !== "" ) ? ( trim($gls) !== "" ) ? ", {$req['reqglscore9']}" :  "{$req['reqglscore9']}" : "";

  $ajcc = ( trim($req['reqajccgrade1']) !== "" ) ? "{$req['reqajccgrade1']}" : "";
  $ajcc .= ( trim($req['reqajccgrade2']) !== "" ) ? ( trim($ajcc) !== "" ) ? ", {$req['reqajccgrade2']}" :  "{$req['reqajccgrade2']}" : "";
  $ajcc .= ( trim($req['reqajccgrade3']) !== "" ) ? ( trim($ajcc) !== "" ) ? ", {$req['reqajccgrade3']}" :  "{$req['reqajccgrade3']}" : "";
  $ajcc .= ( trim($req['reqajccgrade4']) !== "" ) ? ( trim($ajcc) !== "" ) ? ", {$req['reqajccgrade4']}" :  "{$req['reqajccgrade4']}" : "";


$investtbl = <<<INVESTTBL
<table border=0 cellpadding=0 cellspacing=0><tr><td class=RQHeaders>INVESTIGATOR INFORMATION</td></tr></table>

<table border=0 width=100%>
<tr><td class=headerInfoCell>Invest #</td><td class=headerInfoCell>Investigator Status<br>Status Date</td><td class=headerInfoCell>Networked</td><td class=headerInfoCell>Investigator Name<br>Title</td><td class=headerInfoCell>Institution<br>Type</td><td class=headerInfoCell>CHTN Division</td><td class=headerInfoCell>App. Received</td><td class=headerInfoCell>App. Complete</td><td class=headerInfoCell>App. Approved</td><td class=headerInfoCell>Guidelines Sent</td><td class=headerInfoCell>DUA Sent</td><td class=headerInfoCell>Entered By<br>Entered Date</td>  </tr>
<tr>
  <td valign=top class=dataDisplay>{$req['investid']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investstatus']}{$istatDate}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investnetworked']}&nbsp;</td> 
  <td valign=top class=dataDisplay style="white-space: nowrap;">{$investname}<br>{$req['investtitle']}&nbsp;</td>
  <td valign=top class=dataDisplay style="white-space: nowrap;">{$req['investinstitution']}<br>{$req['investinstitutiontype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investdivision']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investappreceivedate']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investappcompletedate']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investappapproveddate']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investguidelinedate']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investduareceivedate']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['investfirstenterby']}<br>{$req['investfirstenterdate']}&nbsp;</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=0><tr><td class=RQHeaders>PROJECT INFORMATION</td></tr><tr><td>

<table border=0 width=100%>
<tr><td class=headerInfoCell>Project Id</td><td class=headerInfoCell>Proj #</td><td class=headerInfoCell>Status</td><td class=headerInfoCell>Networked</td><td class=headerInfoCell>Project title</td><td class=headerInfoCell>IRB Type</td><td class=headerInfoCell>IRB #<br>Received::Expiration</td><td class=headerInfoCell>Priority</td><td class=headerInfoCell>Study #</td><td class=headerInfoCell>Project<br>Comments</td></tr>
<tr> 
  <td valign=top class=dataDisplay>{$req['projid']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$projnbr}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['projstatus']}{$projStsDate}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['projnetworked']}{$projNetDate}&nbsp;</td>
  <td valign=top class=dataDisplay style="white-space: nowrap;">{$req['projtitle']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['projirbtype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['projirbnumber']}&nbsp;<br>{$irbstuff}</td>
  <td valign=top class=dataDisplay>{$req['projpriority']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['projstudynum']}&nbsp;</td>
  <td class=dataDisplay>{$req['projcomment']}&nbsp;</td>
</tr>
</table>
</td></tr></table>


<table border=0 cellspacing=0 cellpadding=0><tr><td class=RQHeaders>REQUEST {$req['requestid']} INFORMATION</td></tr><tr><td>

<table border=0 width=100%>
<tr>
<td class=headerInfoCell># in<br>Project</td><td class=headerInfoCell>Status</td><td class=headerInfoCell>Networked</td>
<td class=headerInfoCell>Specimen Category</td><td class=headerInfoCell>Site</td><td class=headerInfoCell>Sub-Site</td><td class=headerInfoCell>Histologic Type</td><td class=headerInfoCell>Sub Type</td><td class=headerInfoCell>Disease Class</td><td class=headerInfoCell>Disease Qualifier</td>
<td class=headerInfoCell>Last Shipped</td><td class=headerInfoCell>Tissue Comments</td>
</tr>
<tr>
<td valign=top class=dataDisplay>{$reqnbr} </td>
<td valign=top class=dataDisplay>{$req['reqstatus']}{$reqstsDate}</td>
<td valign=top class=dataDisplay>{$req['reqnetworked']}{$reqNetDate}</td>
  <td valign=top class=dataDisplay>{$req['reqtissuetype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['reqanasitetype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['reqsubsite']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['reqhistologictype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['reqsubstype']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['diseaseclass']}&nbsp;</td>
  <td valign=top class=dataDisplay>{$req['reqdiseasequalifier']}&nbsp;</td>
<td valign=top class=dataDisplay>{$req['reqlastshipdate']}</td>
<td valign=top class=dataDisplay>{$req['reqtissuecomments']}</td>
</tr>
</table>
</td></tr></table>


<table border=0 cellspacing=0 cellpadding=0><tr><td class=RQHeaders>BIOSAMPLE REQUEST PARAMETERS</td></tr></table>

<table border=0>
<tr><td valign=top>
  <table border=0>
    <tr><td colspan=2 class=headerInfoCellVert>Surgery</td><td colspan=2 class=headerInfoCellVert>Autopsy</td><td colspan=2 class=headerInfoCellVert>Trauma</td></tr>
    <tr>
      <td class=dataCellLft>{$req['reqsurgery']}&nbsp;</td><td class=dataCellRgt>{$req['reqsurgeryunit']} {$req['reqpostexcisiontime']}&nbsp;</td>
      <td class=dataCellLft> {$req['reqautopsy']} </td><td class=dataCellRgt> {$req['reqautospyunit']} {$req['reqpostmortemtime']}</td><td class=dataCellLft>{$req['reqtrauma']}&nbsp;</td><td class=dataCellRgt>{$req['reqposttraumatime']}&nbsp;</td>
    </tr>
    <tr><td colspan=2 class=headerInfoCellVert>Transplant</td><td colspan=2 class=headerInfoCellVert>Phlebotomy</td></tr>
    <tr>
      <td class=dataCellLft>{$req['reqtransplant']}&nbsp;</td><td class=dataCellRgt>{$req['reqtransplantunit']}&nbsp;</td>
      <td class=dataCellLft>{$req['reqphlebotomy']}&nbsp;</td><td class=dataCellRgt>{$req['reqpostphltime']}&nbsp;</td>
    </tr>
  </table>
</td><td valign=top>

<table border=0>
<tr><td valign=top class=headerInfoCellVert>Age</td><td valign=top style="white-space: nowrap;" class=dataDisplayVert>{$agedsp} {$age2dsp} {$age3dsp}&nbsp;</td></tr>
<tr><td valign=top class=headerInfoCellVert>Race</td><td valign=top class=dataDisplayVert>{$req['reqrace']}&nbsp;</td></tr>
<tr><td valign=top class=headerInfoCellVert>Sex</td><td valign=top class=dataDisplayVert>{$req['reqsex']}&nbsp;</td></tr>
</table>

</td>
<td valign=top>

<table border=0>
  <tr><td class=headerInfoCellVert>Anything</td><td class=dataDisplayVert>{$req['reqanything']}</td><td class=headerInfoCellVert>Normal From CA-PT</td><td class=dataDisplayVert>{$req['reqnormalfromcapt']}</td><td class=headerInfoCellVert>Has METS</td><td class=dataDisplayVert>{$req['reqhasmets']}</td><td class=headerInfoCellVert>PT HX</td><td class=dataDisplayVert>{$req['reqpatienthx']}</td></tr>
  <tr><td class=headerInfoCellVert>Chemo Diff DX</td><td class=dataDisplayVert>{$req['reqchemodiffdx']}</td><td class=headerInfoCellVert>Chemo Curr DX</td><td class=dataDisplayVert>{$req['reqchemocurrdz']}</td><td class=headerInfoCellVert>Rad Diff DX</td><td class=dataDisplayVert>{$req['reqraddiffdz']}</td><td class=headerInfoCellVert>Rad Curr DX</td><td class=dataDisplayVert>{$req['reqradcurrdz']}</td></tr>
  <tr><td class=headerInfoCellVert>Mole Therapy</td><td class=dataDisplayVert>{$req['reqmoletherapy']}</td><td class=headerInfoCellVert>Bio Therapy</td><td class=dataDisplayVert>{$req['reqbiotherapy']}</td><td class=headerInfoCellVert>Rad Prior DX</td><td class=dataDisplayVert>{$req['reqradpriordz']}</td><td class=headerInfoCellVert>Has BF</td><td class=dataDisplayVert>{$req['reqhasbf']}</td></tr>
  <tr><td class=headerInfoCellVert>Has Solid</td><td class=dataDisplayVert>{$req['reqhassolid']}</td><td class=headerInfoCellVert>Has NAT</td><td class=dataDisplayVert>{$req['reqhasnat']}</td><td class=headerInfoCellVert>Chart review</td><td class=dataDisplayVert>{$req['reqchartreview']}</td><td class=headerInfoCellVert>Vacc Therapy</td><td class=dataDisplayVert>{$req['reqvacctherapy']}</td></tr>
</table>

</td>
<td valign=top>

<table border=0>
<tr><td class=headerInfoCellVert>Chemo</td><td class=dataDisplayVert>{$req['reqchemoyn']}</td><td class=headerInfoCellVert>Radiation</td><td class=dataDisplayVert>{$req['reqradyn']}</td></tr>
<tr><td class=headerInfoCellVert>Norm from HT-PT</td><td class=dataDisplayVert>{$req['reqnormalfromhtpt']}</td><td class=headerInfoCellVert>Has Other</td><td class=dataDisplayVert>{$req['reqhasother']}</td></tr>
<tr><td class=headerInfoCellVert>GL Score Any</td><td class=dataDisplayVert>{$req['reqglscoreanyall']}</td><td class=headerInfoCellVert>GL Score</td><td class=dataDisplayVert>{$gls}</td></tr>
<tr><td class=headerInfoCellVert>AJCC Stage Any</td><td class=dataDisplayVert>{$req['reqajccstageanyall']}</td><td class=headerInfoCellVert>AJCC Stage</td><td class=dataDisplayVert>{$ajcc}</td></tr>
</table>

</td>
</tr>
</table>


{$tisdsp}

INVESTTBL;


$rtnThis = <<<PGCONTENT
<style>

.RQHolderTbl { } 
.RQHeaders { width: 90vw; background: rgba(100,149,237,1); color: rgba(255,255,255,1); font-size: 1.5vh; font-weight: bold; padding: 8px; box-sizing: border-box; }

.headerInfoCell { font-size: 1.1vh; background: rgba(145,145,145,.2); color: rgba(48,57,71,1); font-weight: bold; padding: 3px 5px; }
.dataDisplay { color: rgba(48,57,71,1); font-size: 1.3vh; padding: 8px 6px; border-bottom: 1px solid rgba(145,145,145,.5);border-right: 1px solid rgba(145,145,145,.5); min-width: 5vw; } 

.headerInfoCellVert { font-size: 1.1vh; background: rgba(145,145,145,.2); color: rgba(48,57,71,1); font-weight: bold; padding: 8px 6px; }
.dataDisplayVert { color: rgba(48,57,71,1); font-size: 1.1vh; padding: 8px 6px; border-bottom: 1px solid rgba(145,145,145,.5);border-right: 1px solid rgba(145,145,145,.5); min-width: 4vw; max-width: 6vw; } 
.dataCellLft { border-left: 1px solid rgba(145,145,145,.5); border-bottom: 1px solid rgba(145,145,145,.5); color: rgba(48,57,71,1); font-size: 1.1vh; padding: 8px 6px; }
.dataCellRgt { border-right: 1px solid rgba(145,145,145,.5); border-bottom: 1px solid rgba(145,145,145,.5); color: rgba(48,57,71,1); font-size: 1.1vh; padding: 8px 6px; }

.smlHeadCell { font-size: 1vh; color: rgba(48,57,71,1); border-bottom: 1px solid rgba(145,145,145,1); font-weight: bold; } 

#preprequirementdiv { height: 20vh; overflow: auto; }

</style>

{$investtbl}

PGCONTENT;
return $rtnThis;
} 

function bldHPRSlideTrayReturnOverride ( $dialogid, $passedData ) { 
  $at = genAppFiles;
  $waitpic = base64file("{$at}/publicobj/graphics/zwait2.gif", "waitgifADD", "gif", true);         
  require(serverkeys . "/sspdo.zck");    
  $dta = json_decode( $passedData, true);
  $obj = json_decode($dta['objid'],true);
  $invListWS = json_decode(callrestapi("GET", dataTree . "/global-menu/inventory-location-storagecontainers",serverIdent, serverpw), true);

  //TODO: MAKE THIS A WEBSERVICE
  //TRAY METRICS
  $traySQL = "SELECT ifnull(iloc.scanCode,'') as hprtrayscancode, ifnull(iloc.typeOLocation,'') as hprtraytypeoflocation, ifnull(iloc.locationdsp,'') as hprtrayname, ifnull(iloc.hprtraystatus,'') as hprtraystatuscode, ifnull(ists.longvalue,'') as hprtraystatusdsp, ifnull(iloc.hprtrayheldwithin,'') as hprtrayheldwithincode, ifnull(rloc.typeolocation,'') as rtnheldwithintypeoflocation, ifnull(rloc.locationdsp,'') as rtnheldwithinlocationdsp, ifnull(iloc.hprtrayheldwithinnote,'') as rtnheldwithinnote, ifnull(iloc.hprtrayreasonnotcomplete,'') as reasontraynotcomplete, ifnull(notcom.dspvalue,'') as reasontraynotcompletedsp, ifnull(iloc.hprtrayreasonnotcompletenote,'') reasonnotcompletenote, ifnull(iloc.hprtraystatusby,'') as hprtraystatusby, ifnull(date_format(iloc.hprtraystatuson,'%m/%d/%Y'),'') as hprtraystatuson FROM four.sys_inventoryLocations iloc left join (select * from four.sys_inventoryLocations) rloc on iloc.hprtrayheldwithin = rloc.scancode left join (SELECT dspvalue as menuvalue, longvalue FROM four.sys_master_menus where menu = 'HPRTrayStatus') as ists on iloc.hprtraystatus = ists.menuvalue left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu ='HPRRTNINCOMPLETEREASON') as notcom on iloc.hprtrayreasonnotcomplete = notcom.menuvalue where iloc.scancode = :boxid";
  $trayRS = $conn->prepare($traySQL); 
  $trayRS->execute(array(':boxid' => $obj['boxid']));
  $tray = $trayRS->fetch(PDO::FETCH_ASSOC);

  $tlocNote = ( trim($tray['rtnheldwithinnote']) !== "" ) ? "<br><span class=smlrFont>({$tray['rtnheldwithinnote']})</span>" : "";

  $partialNote = ( trim($tray['reasonnotcompletenote']) !== "" ) ? "<br><span class=smlrFont>({$tray['reasonnotcompletenote']})</span>" : "";
  $partialLine = ( trim($tray['reasontraynotcompletedsp']) !== "" ) ? "<tr><td valign=top class=datalabel >Review Not Complete</td><td valign=top class=datadisplay>{$tray['reasontraynotcompletedsp']}{$partialNote}</td></tr>" : "";

  $whoDte = ( trim($tray['hprtraystatuson']) !== "" ) ? " :: {$tray['hprtraystatuson']}" : "";
  $bywho = ( trim($tray['hprtraystatusby']) !== "" ) ? " ({$tray['hprtraystatusby']}{$whoDte})" : ""; 

  $trayTbl = <<<TRAYTBL
<table border=0 >
<tr><td valign=top class=datalabel>HPR Tray: </td><td valign=top class=datadisplay>{$tray['hprtrayname']} ({$tray['hprtraytypeoflocation']}) </td></tr>
<tr><td valign=top class=datalabel>Present Status: </td><td valign=top class=datadisplay>{$tray['hprtraystatusdsp']}{$bywho}</td></tr>
<tr><td valign=top class=datalabel>Present Location: </td><td valign=top class=datadisplay>{$tray['rtnheldwithinlocationdsp']}{$tlocNote}</td></tr>
{$partialLine}
</table>
TRAYTBL;

  //TODO: MAKE AS A WEBSERVICE
  //SLIDE LISTING
  $slideSQL = <<<SLIDESQL
SELECT replace(sg.bgs,'_','') as bgs, sg.segstatus, ifnull(sts.dspvalue,'') as segstatusdsp, ifnull(sg.prepmethod,'') as prepmethod, ifnull(sg.preparation,'') as preparation, ifnull(sg.assignedto,'') as assignedto, ifnull(sg.scannedlocation,'') as scannedlocation, ifnull(sg.scanloccode,'') as scanloccode
, ifnull(sg.hprslideread,'') as hprslideread, ifnull(sg.HPRBoxNbr,'') as hprboxnbr, ifnull(bs.HPRDecision,'') as hprdecision, date_format(bs.HPROn,'%m/%d/%Y') as hpron, ifnull(bs.HPRBy,'') as hprby 
from masterrecord.ut_procure_segment sg left join (SELECT menuvalue, dspvalue, longvalue FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') as sts on sg.segStatus = sts.menuvalue left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample where HPRBoxNbr = :boxid
SLIDESQL;
  $slideRS = $conn->prepare($slideSQL); 
  $slideRS->execute(array(':boxid' => $obj['boxid']));

  $slideList = "<table border=0 id=choosyTbl><thead><tr><td colspan=25>{$slideRS->rowCount()} slides found</td></tr><tr><th>Slide #</th><th>Present Location</th><th>HPR Decision</th><th>HPR By</th><th>File Location</th></tr></thead><tbody>";
  $sldcntr = 0;
  while ($s = $slideRS->fetch(PDO::FETCH_ASSOC)) {

        $invmenu = "<table border=0 class=menuDropTbl>";
        foreach ($invListWS['DATA'] as $pky => $pval) { 
           $invmenu .= "<tr><td onclick=\"fillField('sRTNInvLoc{$sldcntr}','{$pval['codevalue']}','{$pval['menuvalue']}');\" class=ddMenuItem>{$pval['menuvalue']}</td></tr>";  
        }
        $invmenu .= "</table>";  
        $dspInvMenu = "<div class=menuHolderDiv><input type=hidden id=\"sRTNInvLoc{$sldcntr}Value\" data-bgs=\"{$s['bgs']}\"><input type=text id=\"sRTNInvLoc{$sldcntr}\" READONLY class=inventoryLocDsp><div class=valueDropDown>{$invmenu}</div></div>";

        $rvwDate = ( trim($s['hpron']) !== "" ) ? " ({$s['hpron']})" : "";

      $slideList .= <<<SINNER
<tr class=displayrow>
  <td class="displaythis">{$s['bgs']}</td><td class="displaythis">{$s['scannedlocation']}</td><td class="displaythis">{$s['hprdecision']}</td><td class="displaythis">{$s['hprby']}{$rvwDate}</td><td>{$dspInvMenu}</td>
</tr>
SINNER;
    $sldcntr++;      
  }
  $slideList .= "</tbody></table>";


  $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-menu-hpr-inventory-override",serverIdent, serverpw), true);
  $devm = "<table border=0 class=menuDropTbl>";
  foreach ($devarr['DATA'] as $devval) { 
    $devm .= "<tr><td onclick=\"fillField('fldTRtnDeviationReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
  }
  $devm .= "</table>";

  $devTbl = <<<DEVTBL
<table border=0>
  <tr><td rowspan=2 valign=top style="font-size: 1.1vh; width: 15vw; text-align: justify; padding: 0 .5vw;"><b>CHTNEASTERN SOP DEVIATION NOTIFICATION</b>: This is NOT a standard inventory screen and should only be used in extenuating operating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures. Please enter a reason for the deviation below.</td><td class=datalabel>Inventory User Pin</td><td class=datalabel>Deviation Reason</td></tr>
  <tr><td><input type=password id=fldTRtnUsrPIN style="width: 9vw;"></td><td><div class=menuHolderDiv><input type=text id=fldTRtnDeviationReason style="width: 25vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
</table>
DEVTBL;

$rtnThis = <<<PGCONTENT
<style>
 .inventoryLocDsp {font-size: 1.4vh; padding: .5vh .3vw;  } 
 .ddMenuItem {font-size: 1.1vh; }
 .valueDropDown { width: 30vw; }

 .menuDropTbl {  }
 .ddMenuItem { font-size: 1.5vh; }


 .datalabel { font-size: 1.5vh; font-weight: bold; color: rgba(48,57,71,1); } 
 .datadisplay { font-size: 1.5vh; color: rgba(48,57,71,1); } 
 .squarethis { border: 1px solid rgba(48,57,71,1); }
 .smlrFont { font-size: 1.1vh; } 

 #choosyTbl { font-size: 1.4vh; border-collapse: collapse; width: 100%; }
 #choosyTbl thead th { background: rgba(48,57,71,1); color: rgba(255,255,255,1); padding: 8px 5px; border-right: 1px solid rgba(255,255,255,1);   } 
 #choosyTbl tbody .displaythis { font-size: 1.5vh; color: rgba(48,57,71,1); padding: 8px 4px; border-right: 1px solid rgba(48,57,71,1); }
 #choosyTbl tbody .displayrow:nth-child(even) { background: rgba(160,160,160, .3); }  
</style>

<form id=frmRtnTraySpecifics>

<input type=hidden id=rtnHPRTrayScanCode value={$obj['boxid']}>

<table border=0 width=100%>
<tr><td>  <table><tr><td valign=top class=squarethis>{$trayTbl}</td><td valign=top class=squarethis>{$devTbl}</td></tr></table> </td></tr>
<tr><td> <table width=100%><tr><td valign=top class=squarethis>{$slideList}</td></tr></table> </td></tr>
<tr><td align=right>
<div id=waiterIndicator style="font-size: 1.5vh;"><center>{$waitpic}<br>Please wait ...</div>
<div id=rtnBtnDsp><table><tr><td><table class=tblBtn id=btnRtnSave style="width: 6vw;" onclick="updateRtnLocations();"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table></td><td><table class=tblBtn id=btnRtnClose style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Close</td></tr></table></td></tr></table></div>

</td></tr> 
</table>
</form>
PGCONTENT;
return $rtnThis;
}

function bldHPRReviewDisplay ( $dialogid, $passedData ) { 
    require(serverkeys . "/sspdo.zck");    
    $dta = json_decode( $passedData, true);
    $obj = $dta['objid'];

//TODO: TURN INTO WEBSERVICE
    $headSQL = <<<HPRSQL
SELECT 
hpr.biohpr
, replace(ifnull(hpr.bgs,''),'_','') as slideread
, ifnull(hpr.bgreference,'') as bgreference
, ifnull(hpr.reviewer,'') as reviewer
, ifnull(date_format(hpr.reviewedOn,'%m/%d/%Y'),'') as reviewdate
, ifnull(hpr.inputby,'') as inputby
, ucase(ifnull(hpr.decision,'')) as decision
, ucase(ifnull(hpr.vocabularydecision,'')) as vocdecision
, ucase(ifnull(hpr.speccat,'')) as specimencategory
, ucase(ifnull(hpr.site,'')) as site
, ucase(ifnull(hpr.subsite,'')) as subsite 
, ucase(ifnull(hpr.dx,'')) as diagnosis
, ucase(ifnull(hpr.subdiagnosis,'')) as diagnosismodifier
, ucase(ifnull(hpr.mets,'')) as mets
, ucase(ifnull(hpr.systemiccomobid,'')) as systemicdx
, ucase(ifnull(hpr.tumorgrade,'')) as tumorgrade
, ifnull(tscale.dspvalue,'') as tumorscale
, ifnull(uni.longvalue,'') as uninvolvedsample
, ifnull(hpr.rareReason,'') as rarereason
, ifnull(hpr.generalcomments,'') as generalcomments
, ucase(ifnull(hpr.specialInstructions,'')) as specialinstructions
, ucase(ifnull(hpr.inconclusivetxt,'')) as inconclusivetxt
, ucase(ifnull(hpr.unusabletxt,'')) as unusabletxt 
FROM masterrecord.ut_hpr_biosample hpr 
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HPRTUMORSCALE') tscale on hpr.tumorScale = tscale.menuvalue
left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'UNINVOLVEDIND') uni on hpr.uninvolvedSample = uni.menuvalue
where biohpr = :hprid 
HPRSQL;
    $headRS = $conn->prepare($headSQL); 
    $headRS->execute(array(':hprid' => $obj));
    $headD = $headRS->fetch(PDO::FETCH_ASSOC); 
    $reviewNbr = substr('000000' . $headD['biohpr'],-6);
    $decision = ( $headD['decision'] === $headD['vocdecision'] ) ? $headD['decision'] : ( trim($headD['vocdecision']) !== "" ) ? "{$headD['decision']}<br><span class=topUnderFont>(DESIGNATION: {$headD['vocdecision']})</span>" : "{$headD['decision']}" ;
    $decisiondenote = substr($headD['decision'],0,1);
    $designationdsp = trim($headD['specimencategory']);
    $ssite = ( $headD['subsite'] !== "" ) ? " ({$headD['subsite']})" : "";
    $designationdsp .= ( $headD['site'] !== "" ) ? ( $designationdsp === "" ) ? "{$headD['site']}{$ssite}" : "<br>{$headD['site']}{$ssite}" : "";
    $modd = ( $headD['diagnosismodifier'] !== "" ) ? " [{$headD['diagnosismodifier']}]" : "";
    $designationdsp .= ( $headD['diagnosis'] !== "" ) ?  ( $designationdsp === "" ) ? "{$headD['diagnosis']}{$modd}" : "<br>{$headD['diagnosis']}{$modd}" : "";
    $designationdsp .= ( $headD['mets'] !== "" ) ?  ( $designationdsp === "" ) ? "(<b>METS From</b>: {$headD['mets']})" : "<br>(<b>METS From</b>: {$headD['mets']})" : "";
    $designationdsp .= ( $headD['systemicdx'] !== "" ) ?  ( $designationdsp === "" ) ? "(<b>Systemic/Co-Mobid</b>: {$headD['systemicdx']})" : "<br>(<b>Systemic/Co-Mobid</b>: {$headD['systemicdx']})" : "";
    $tgrade = ( $headD['tumorgrade'] !== "" ) ? $headD['tumorgrade'] : "";
    $tgrade .= ( $headD['tumorscale'] !== "" ) ? ( $tgrade === "" ) ? "{$headD['tumorscale']}" : " ({$headD['tumorscale']})" : "";
    $cinner = ( trim($headD['generalcomments']) !== "" ) ? "<tr><td class=clabel>General Comments: </td><td class=ccmts>{$headD['generalcomments']}</td></tr>" : "";
    $cinner .= ( trim($headD['rarereason']) !== "" ) ? "<tr><td class=clabel>Rare Reason: </td><td class=ccmts>{$headD['rarereason']}</td></tr>" : "";
    $cinner .= ( trim($headD['specialinstructions']) !== "" ) ? "<tr><td class=clabel>Special Instructions: </td><td class=ccmts>{$headD['specialinstructions']}</td></tr>" : "";
    $cinner .= ( trim($headD['inconclusivetxt']) !== "" ) ? "<tr><td class=clabel>Inconclusive Text: </td><td class=ccmts>{$headD['inconclusivetxt']}</td></tr>" : "";
    $cinner .= ( trim($headD['unusabletxt']) !== "" ) ? "<tr><td class=clabel>Unusable Text: </td><td class=ccmts>{$headD['unusable']}</td></tr>" : "";
    $cmttbl = ( $cinner === "" ) ? "&nbsp;" : "<table border=0 cellspacing=0 cellpadding=0 id=commenttable>{$cinner}</table>";
        
//TODO: TURN INTO WEBSERVICE
$prcSQL = <<<PRCSQL
SELECT biohpr, if ( ifnull(prc.longvalue,'') = '',hprp.prcType,ifnull(prc.longvalue,'')) as prctype, hprp.prcvalue
FROM masterrecord.ut_hpr_percentages hprp
left join ( SELECT menuvalue, longValue FROM four.sys_master_menus where menu = 'HPRPERCENTAGE' ) prc on hprp.prctypevalue = prc.menuvalue 
where (biohpr = :biohpr) and ifnull(prcvalue,0) <> 0
PRCSQL;

$prcRS = $conn->prepare($prcSQL); 
$prcRS->execute(array(':biohpr' => $headD['biohpr']));

if ($prcRS->rowCount() > 0 ) {
    $percentageTbl = "<table width=100%>";
    while ($r = $prcRS->fetch(PDO::FETCH_ASSOC)) {
      $ptype =  ucwords( strtolower( $r['prctype'])); 
      $percentageTbl .= "<tr><td>{$ptype}</td><td style=\"width: 2vw; white-space: nowrap; text-align: right;\">{$r['prcvalue']}%</td></tr>";
    }
    $percentageTbl .= "</table>";
} else { 
    $percentageTbl = "No Compositional Definition Defined";
}
//TODO: TURN INTO WEBSERVICE
$tstSQL = <<<PRCSQL
SELECT ifnull(moletest,'') as testname, ifnull(resultindex,'') as resultindex, ifnull(resultdegree,'') as resultdegree FROM masterrecord.ut_hpr_moleculartests where biohpr = :biohpr
PRCSQL;
$tstRS = $conn->prepare($tstSQL); 
$tstRS->execute(array(':biohpr' => $headD['biohpr']));

if ($tstRS->rowCount() > 0 ) {
    $tstTbl = "<table width=100%>";
    while ($t = $tstRS->fetch(PDO::FETCH_ASSOC)) {
      $ttype =  $t['testname']; 
      $tstTbl .= "<tr><td>{$ttype}</td><td>{$t['resultindex']} {$t['resultdegree']}</td></tr>";
    }
    $tstTbl .= "</table>";
} else { 
    $tstTbl = "No Molecular/Immuno-Histogy Test Results Defined";
}

//TODO: TURN INTO WEBSERVICE
$faSQL = <<<PRCSQL
SELECT ifnull(actiontype,'') as actiontype, ifnull(actionnote,'') as actionnote, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as actionrequestedon, ifnull(actioncomplete,0) as actioncomplete, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'') actioncompletedon, ifnull(actioncompletedby,'') as actioncompletedby FROM masterrecord.ut_hpr_factions where biohpr = :biohpr
PRCSQL;
$faRS = $conn->prepare($faSQL); 
$faRS->execute(array(':biohpr' => $headD['biohpr']));

if ($faRS->rowCount() > 0 ) {
    $faTbl = "<table width=100%>";
    while ($f = $faRS->fetch(PDO::FETCH_ASSOC)) {
      $ftype =  $f['actiontype'];
      $ftype .= ( trim($f['actionnote']) !== "" ) ? "<br><span class=smlFont>[{$f['actionnote']}]</span>" : "";
      $comp = ( (int)$f['actioncomplete'] === 1 ) ? "COMPLETE" : "NOT COMPLETE";
      $faTbl .= "<tr><td>{$ftype}</td><td>{$f['actionrequestedon']}</td><td>{$comp}</td></tr>";
    }
    $faTbl .= "</table>";
} else { 
    $faTbl = "No Further Actions Required from this Review";
}

$rtnThis = <<<PGCONTENT
<style>

.topUnderFont { font-size: 1.2vh; }
#hprAnncLine { width: 70vw; box-sizing: border-box; font-size: 1vh; }
#hprAnncLine tr td { padding: .3vh .3vw 0 0; }

#decisiontbl { width: 70vw; box-sizing: border-box; font-size: 1.6vh;   }
#decisiontbl tr td { text-align: center; border: 1px solid rgba(100,149,237,1); padding: 10px; height: 4vh; font-weight: bold; }
#decisiontbl .hprdecD { background: rgba(84,113,210, 1); color: rgba(48,57,71,1);     }
#decisiontbl .hprdecU { background: rgba(237, 35, 0, 1); color: rgba(255,255,255,1);     }
#decisiontbl .hprdecC { background: rgba(0, 112, 13, 1); color: rgba(255,255,255,1);     }
#decisiontbl .hprdecA { background: rgba(226,226,125, 1); color: rgba(48,57,71,1);     }
#decisiontbl .hprdecI { background: rgba(107, 18, 102, 1); color: rgba(255,255,255,1);     }

#hprdatatblone { width: 100%; box-sizing: border-box; font-size: 1.2vh; }
#hprdatatblone tbody tr:nth-child(even) {background: rgba(239, 239, 239,1); }
#hprdatatblone tbody tr:hover { cursor: pointer; background: rgba(255,248,225,1); }
#hprdatatblone tr td { padding: 5px 0 5px 3px; border-bottom: 1px solid rgba(0,32,113,1); border-right: 1px solid rgba(0,32,113,1);  }
#hprdatatblone tr th { background: rgba(0,32,113,1); color: rgba(255,255,255,1);   }  

#commenttable {  width: 100%; }
#commenttable tr {   }
#commenttable .clabel { font-size: 1vh; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,1); padding: 0 3px 0 0; width: 1vw; white-space: nowrap; } 
#commenttable .ccmts { border-bottom: 1px solid rgba(255, 255, 255,1); border-right: 1px solid rgba(255, 255, 255,1);}

#multipleThingsTbl {width: 70vw; box-sizing: border-box; font-size: 1.2vh; }
#multipleThingsTbl tr th { background: rgba(0,32,113,1); color: rgba(255,255,255,1);   }  
#multipleThingsTbl tr td { padding: 5px 0 5px 3px; border-bottom: 1px solid rgba(0,32,113,1); border-right: 1px solid rgba(0,32,113,1);  }

</style>

<table id=decisiontbl><tr><td class="hprdec{$decisiondenote}">{$decision} </td></tr></table>
<table id=hprAnncLine><tr><td align=right><b>Review Number</b>: {$reviewNbr} </td></tr></table>

<table id=hprdatatblone>
  <thead>
    <tr><th>Slide<br>Read</th><th>Review<br>Performed</th><th>Entered By</th><th>Designation</th><th>Tumor Grade/Scale</th><th>Uninvolved</th><th>Comments</th></tr></thead>
  <tbody>
    <tr>
      <td valign=top>{$headD['slideread']}&nbsp;</td>
      <td valign=top>{$headD['reviewer']}<br>({$headD['reviewdate']})&nbsp;</td>
      <td valign=top>{$headD['inputby']}&nbsp;</td>
      <td valign=top>{$designationdsp}&nbsp;</td>
      <td valign=top>{$tgrade}&nbsp;</td>
      <td valign=top>{$headD['uninvolvedsample']}&nbsp;</td>
      <td valign=top>{$cmttbl}</td>
    </tr>
  </tbody>
</table>
<table border=0 id=multipleThingsTbl>
<thead><tr><th width=33% valign=top>Compositional<br>Definition</td><th width=33% valign=top>Further-Actions<br>Necessary</td><th width=33% valign=top>Molecular/Immuno<br>Test Results</td></tr></thead><tbody>
<tr><td valign=top>{$percentageTbl}</td><td valign=top>{$faTbl}</td><td valign=top>{$tstTbl}</td></tr>
</tbody>
</table>
<table width=100%><tr><td align=right>
<table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Close</td></tr></table>
</td></tr></table>
PGCONTENT;
return $rtnThis;
}

function bldHPRReturnTray ( $dialogid, $passedData ) { 
   
    require(serverkeys . "/sspdo.zck");    
    $dta = json_decode( $passedData, true);
    $obj = $dta['objid'];
    //TODO: TURN INTO WEBSERVICE
    $chkSQL = "SELECT * FROM masterrecord.ut_procure_segment where HPRBoxNbr = :trayscancode and ifnull(hprslideread,0) = 0";
    $chkRS = $conn->prepare($chkSQL);
    $chkRS->execute(array(':trayscancode' => $obj));
    if ( $chkRS->rowCount() < 1 ) { 
    } else { 

        $nonReasonDta = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-return-non-finished-reasons",serverIdent,serverpw),true);
        $rsnlist = "<table border=0  class=\"menuDropTbl hprNewDropDownFont\">";
        foreach ($nonReasonDta['DATA'] as $rval) { 
          $rsnlist .= "<tr><td onclick=\"fillField('fldRtnNonFinishReason','{$rval['lookupvalue']}','{$rval['menuvalue']}');\" class=ddMenuItem>{$rval['menuvalue']}</td></tr>";
        }
        $rsnlist .= "</table>";
        $rsnTbl = <<<FATBL
<div class=menuHolderDiv><input type=hidden id=fldRtnNonFinishReasonValue value=""><input type=text id=fldRtnNonFinishReason READONLY class="inputFld hprDataField" style="width: 20vw;" value=""><div class=valueDropDown style="min-width: 20vw;">{$rsnlist}</div></div>
FATBL;
        $reasonNotFinished = "<tr><td class=rtninstructionline style=\"padding-top: 15px; color: rgba(237, 35, 0, 1);\">Not all the slides in this tray have been marked as 'Read'.</td></tr><tr><td class=rtninstructionline>Reason Not Finished</td></tr><tr><td>{$rsnTbl}</td></tr><tr><td class=rtninstructionline>Additional 'Non-Finished' Comments</td></tr><tr><td><textarea id=rtnnonfinishednote></textarea></td></tr>";

    }

    $locListDta = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-return-locations",serverIdent,serverpw),true);
    $loclist = "<table border=0  class=\"menuDropTbl hprNewDropDownFont\">";
    foreach ($locListDta['DATA'] as $lval) { 
      $loclist .= "<tr><td onclick=\"fillField('fldRtnLocation','{$lval['lookupvalue']}','{$lval['menuvalue']}');\" class=ddMenuItem>{$lval['menuvalue']}</td></tr>";
    }
    $loclist .= "<tr><td onclick=\"fillField('fldRtnLocation','TMPLOCOTHRNONLOC','OTHER LOCATION - SEE NOTE');\" class=ddMenuItem>OTHER LOCATION - SEE NOTE</td></tr>";
    $loclist .= "</table>";
$locTbl = <<<FATBL
<div class=menuHolderDiv><input type=hidden id=fldRtnLocationValue value=""><input type=text id=fldRtnLocation READONLY class="inputFld hprDataField" style="width: 20vw;" value=""><div class=valueDropDown style="min-width: 20vw;">{$loclist}</div></div>
FATBL;
 
    $rtnThis = <<<PGCONTENT
<style>
    #rtntitleline { font-size: 1.9vh; font-weight: bold; padding: 10px; text-align: center; }
    .rtninstructionline { font-size: 1.5vh; padding: 4px; }       
    #rtnlocationnote { width: 20vw; height: 10vh; box-sizing: border-box; padding: 5px; font-size: 1.5vh; border: 1px solid #000;   } 
    #rtnnonfinishednote { width: 20vw; height: 10vh; box-sizing: border-box; padding: 5px; font-size: 1.5vh; border: 1px solid #000;   } 
</style>

<table border=0>
    <tr><td id=rtntitleline>Thank You for the review! <input type=hidden id=rtnTryId value="{$obj}"></td></tr>
    <tr><td class=rtninstructionline>Slide Tray Pick-up Location </td></tr>
    <tr><td>{$locTbl}</td></tr>       
    <tr><td class=rtninstructionline>Location Note</td></tr>
    <tr><td><textarea id=rtnlocationnote></textarea></td></tr> 
    {$reasonNotFinished}
    <tr><td align=right> 

                <table>
                  <tr>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="returnTrayAction('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Return</td></tr></table>  </td>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td>
                  </tr>
                </table>   

        </td></tr>
    </table>
PGCONTENT;
return $rtnThis;
  
}

function bldHPRUsuableSave ( $dialogid , $passedData ) { 
   
    $dta = json_decode( $passedData, true);
    $obj = $dta['objid'];

    $rtnThis = <<<PGCONTENT
<style>

.iTitleLine { font-size: 1.8vh; color: rgba(48,57,71,1); padding: .5vh .3vw; }
.iFieldLbl { font-size: 1.3vh; color: rgba(48,57,71,1); font-weight: bold; white-space: nowrap; width: 2vw; padding: 4px;  }
.iFieldData { font-size: 1.3vh; color: rgba(48,57,71,1); white-space: nowrap; padding: 4px;}
.iTxtDsp { font-size: 1.4vh; border: 1px solid rgba(48,57,71,1); width: 27vw; height: 12vh;   }

</style>
<textarea id=valSavedData width=100% style="display: none;">{$obj}</textarea>
<table border=0>
<tr><td class=iFieldLbl>Reason Unusable/Not-Fit-For-Purpose</td></tr>
<tr><td><textarea class=iTxtDsp id=ususableReasonTxt></textarea></td></tr>
<tr><td align=right> 

                <table>
                  <tr>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="sendSaveUnusable('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table>  </td>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td>
                  </tr>
                </table>   

 </td></tr>
</table>

PGCONTENT;
return $rtnThis;
    
}

function bldHPRInconclusiveDesignation ( $dialogid, $objectid ) { 
    
    $faList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-further-actions",serverIdent,serverpw), true);
    //FA LISTING
    $fa = "<table border=0  class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"fillField('fldFurtherAction','','');\" class=ddMenuClearOption>[clear]</td></tr>";
    foreach ($faList['DATA'] as $procval) { 
      $fa .= "<tr><td onclick=\"fillField('fldDGFurtherAction','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
    }
    $fa .= "</table>";

$faTbl = <<<FATBL
<table border=0>
<tr><td class=hprPreLimFldLbl>Further Action</td><td class=hprPreLimFldLbl>Note</td><td rowspan=2 valign=top><table class=tblBtn style="width: 2.3vw;" onclick="manageDGFurtherActions(1,);"><tr><td><center><i class="material-icons" style="font-size: 1.8vh;">playlist_add</i></td></tr></table> </td></tr>
<tr><td> <div class=menuHolderDiv><input type=hidden id=hprDGFAJsonHolder><input type=hidden id=fldDGFurtherActionValue value=""><input type=text id=fldDGFurtherAction READONLY class="inputFld hprDataField " style="width: 17vw;" value=""><div class=valueDropDown style="min-width: 20vw;">{$fa}</div></div></td><td><input type=text id=fldDGFANote class="hprDataField" style="width: 8vw;" ></td></tr>
<tr><td colspan=3><div id=dgfurtheractiondsplisting style="border: 1px solid rgba(160,160,160,.8); height: 16vh; overflow: auto;"></div></td></tr>
</table>
FATBL;
//END FA LISTING
    $obj = explode("::",cryptservice($objectid, 'd')); // 0 = segmentid / 1 = biosampleid
    //TODO:  Turn Into a webservice
    require(serverkeys . "/sspdo.zck");    
    $lookupSQL = "SELECT ucase(replace(bs.read_label,'_','')) as readlabel,  replace(sg.bgs,'_','') as bgs, bs.procureinstitution, bs.createdby, ucase(concat(ifnull(bs.tisstype,''), ' ', ifnull(bs.anatomicsite,''), if(ifnull(bs.subsite,'')='','', concat(' (',ifnull(bs.subsite,''),')')) , ' ' , ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' (',ifnull(bs.subdiagnos,''),')')), if(ifnull(bs.metssite,'')='','',concat(' [',ifnull(bs.metssite,''),']')))) as desig, ucase(concat(if(ifnull(bs.pxiage,'')='','',ifnull(bs.pxiage,'')), if(ifnull(bs.pxirace,'')='','',concat('/',ifnull(bs.pxirace,''))), if(ifnull(bs.pxigender,'')='','',concat('/',ifnull(bs.pxigender,''))))) as ars  FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample where segmentid = :segid";
    $segRS = $conn->prepare($lookupSQL); 
    $segRS->execute(array(':segid' => $obj[0])); 
    $seg = $segRS->fetch(PDO::FETCH_ASSOC); 
    
$pg = <<<PAGECONTENT

<style>

.iTitleLine { font-size: 1.8vh; color: rgba(48,57,71,1); padding: .5vh .3vw; }
.iFieldLbl { font-size: 1.3vh; color: rgba(48,57,71,1); font-weight: bold; white-space: nowrap; width: 2vw; padding: 4px;  }
.iFieldData { font-size: 1.3vh; color: rgba(48,57,71,1); white-space: nowrap; padding: 4px;}
.iTxtDsp { font-size: 1.4vh; border: 1px solid rgba(48,57,71,1); width: 27vw; height: 8vh;  }

</style>
<input type=hidden id=inconSegId value={$obj[0]}>
        <table border=0><tr><td colspan=2 class=iTitleLine>INCONCLUSIVE BIOSAMPLE ({$seg['readlabel']})</td></tr>
        <tr><td class=iFieldLbl>Slide Read: </td><td class=iFieldData>{$seg['bgs']}</td></tr>
        <tr><td class=iFieldLbl>Sample From: </td><td class=iFieldData>{$seg['procureinstitution']} ({$seg['createdby']})</td></tr>
        <tr><td class=iFieldLbl>Designation: </td><td class=iFieldData>{$seg['desig']}</td></tr>
        <tr><td class=iFieldLbl>A/R/S: </td><td class=iFieldData>{$seg['ars']}</td></tr>
        <tr><td class=iFieldLbl colspan=2>Reason Inconclusive</td></tr>
        <tr><td colspan=2><textarea width=100% class=iTxtDsp id=reasonInconclusiveTxt></textarea></td></tr>
        <tr><td colspan=2>{$faTbl}</td></tr>
        <tr><td colspan=2 align=right> 
                <table>
                  <tr>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="gatherAndInconReview('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table>  </td>
                    <td> <table class=tblBtn id=btnIHPRReviewSave style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td>
                  </tr>
                </table>   
            </td></tr>
        </table>        
PAGECONTENT;
return $pg;       
}

function bldQAWorkbench ( $encryreviewid ) { 
  $pdta = array();
  $pdta['reviewid'] = $encryreviewid;
  $payload = json_encode($pdta);

  $reviewdta = json_decode(callrestapi("POST", dataTree . "/data-doers/qa-review-workbench-data",serverIdent, serverpw, $payload), true);
  $moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);
  $tmrGradeScaleList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-tumor-grade-scale",serverIdent,serverpw), true);

  $headdta = $reviewdta['DATA']['hprhead'];
  $ass = $reviewdta['DATA']['associativelisting'];
  $prc = $reviewdta['DATA']['percentvalues'];
  $mol = $reviewdta['DATA']['moleculartests'];

  //TODO:  Figure out a way not to hard code this 
    switch ( $headdta['hprdecisionvalue'] ) { 
      case 'DENIED':
        $decIcon = "cancel";        
        break;
      case 'CONFIRM':
        $decIcon = "check_circle";
        break;
      case 'ADDITIONAL':
        $decIcon = "add_circle";
        break;
      case 'INCONCLUSIVE':
        $decIcon = "help";
        break;
      case 'UNUSABLE':
        $decIcon = "block";
        break;
    }

  if ( $headdta['hprdecisionvalue'] === 'INCONCLUSIVE' ) {
  

    //INCONCLUSIVE WORKBENCH GOES HERE    
    $workbench = <<<WORKBENCH
INCONCLUSIVE DISPLAY GOES HERE     
WORKBENCH;
    $topBtnBar = generatePageTopBtnBar('qmsactionworkincon', "", $_SERVER['HTTP_REFERER'] );


  } else { 


$prnbr = substr(("000000" . $headdta['pathreportid']),-6);
$uploadline = ( trim($headdta['pruploadedby']) !== "" ) ? "<b>Uploaded</b>: {$headdta['pruploadedby']} :: {$headdta['uploadedon']} (<b>Pathology Report</b>: {$prnbr})" : "";

$hprDecDataDisplay = bldQAWorkbench_hprData( $headdta );



      $workbench = <<<WORKBENCHPARTS
<div id=workbenchwrapper>
<div id=wbrowtwo>
  <div id=wbrevieweddata>{$hprDecDataDisplay}</div>
  <div id=wbpristine>Master-Record Data</div>
  <div id=wbsupprtdata>Tumor Comp &amp; Tests</div>
</div>
<div id=wbrowthree>Assoc Segments</div>
</div>

<div id=pathologyrptdisplay>
<div class=blueheader><table width=100% border=0 cellpadding=0 cellspacing=0><tr><td style="padding: 2px 0 0 8px;">Pathology Report </td><td align=right style="padding: 2px 8px 0 0;"><i class="material-icons iconind" onclick="revealPR();">arrow_back</i></td></tr></table></div>
<div id=pathologyreporttextdisplay>{$headdta['pathreport']}</div>
<div id=uploadline>{$uploadline}</div>
</div>

WORKBENCHPARTS;

    $topBtnBar = generatePageTopBtnBar('qmsactionwork', "", $_SERVER['HTTP_REFERER'] );
  }
  $pg = <<<PGCONTENT
{$topBtnBar}
{$workbench}
PGCONTENT;
  return $pg;
}

function bldQAWorkbench_hprData ( $headdta ) { 
//{"hprtumorgrade":"","hprtumorscalevalue":"","hprtumorscaledsp":"","hpruninvolvedvalue":"0","hpruninvolveddsp":"NA (Not Applicable)","hprrarereason":"","hprgeneralcomments":"","hprspecialinstructions":"","hprinconclusivetext":"","hprunusabletext":"","bsspeccat":"MALIGNANT","bsanatomicsite":"KIDNEY","bssubsite":"","bsdx":"CARCINOMA","bsdxmod":"RENAL CELL","bsmets":"","bscomo":"","associd":"YcWMAztqGtR3mNIfKFiw","bschemoindvalue":"2","bschemoinddsp":"Unknown","bsradindvalue":"2","bsradinddsp":"Unknown","bsproctypevalue":"S","bsproctypedsp":"Surgery","bsprocureinstitution":"HUP","bsprocureinstitutiondsp":"Hospital of The University of Pennsylvania","procurementdate":"06\/20\/2019","bspxiage":"61","bspxiageuom":"Years","bspxiageuomvalue":"1","bspxisex":"F","bspxisexdsp":"Female","bspxirace":"WHITE","pathreportid":42621,"pathreport":"Clinical Information:"

$rbyon = $headdta['reviewer'];
$rbyon .= ( trim($headdta['reviewer']) !== trim($headdta['inputby']) ) ?  ( trim($headdta['inputby']) !== "" ) ? " ({$headdta['inputby']})" : "" : "";
$rbyon .= ( trim($headdta['reviewedon']) !== "" ) ? " :: {$headdta['reviewedon']}" : "";
$asite = ( trim($headdta['hprsite']) !== "" ) ? trim($headdta['hprsite']) : "";
$asite .= ( trim($headdta['hprsubsite']) !== "" ) ? ( trim($asite) !== "" ) ? " :: {$headdta['hprsubsite']}" : " -- :: {$headdta['hprsubsite']}" : "";
$dx = ( trim($headdta['hprdx']) !== "" ) ? trim($headdta['hprdx']) : "";
$dx .= ( trim($headdta['hprdxmod']) !== "" ) ? ( trim($dx) !== "" ) ? " :: {$headdta['hprdxmod']}" : " -- :: {$headdta['hprdxmod']}" : "";
$rtnThis = <<<RTNTHIS
<div id=dataRowOne data-hprdecision="{$headdta['hprdecisionvalue']}"  data-hprreviewid="{$headdta['biohpr']}">HPR Decision</div>
<div class=dataHolderDiv id=hprDataReview><div class=datalabel>Review</div><div class=datadisplay>{$headdta['biohpr']}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataDecision><div class=datalabel>Decision</div><div class=datadisplay>{$headdta['hprdecision']}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataSpecCat><div class=datalabel>Specimen Category</div><div class=datadisplay>{$headdta['hprspeccat']}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataSite><div class=datalabel>Site :: Sub-Site</div><div class=datadisplay>{$asite}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataDX><div class=datalabel>Diagnosis :: Modifier</div><div class=datadisplay>{$dx}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataMETS><div class=datalabel>Metastatic FROM</div><div class=datadisplay>{$headdta['hprmets']}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataCoMo><div class=datalabel>Systemic Diagnosis and/or Co-Mobidity</div><div class=datadisplay>{$headdta['hprcomobid']}&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataReviewedOnBy><div class=datalabel>Reviewed By :: On</div><div class=datadisplay>{$rbyon}&nbsp;</div></div>
RTNTHIS;
return $rtnThis;
}

function bldQAWorkbenchbu ( $encryreviewid ) { 

$pdta = array();
$pdta['reviewid'] = $encryreviewid;
$payload = json_encode($pdta);

$reviewdta = json_decode(callrestapi("POST", dataTree . "/data-doers/qa-review-workbench-data",serverIdent, serverpw, $payload), true);
$moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);
$tmrGradeScaleList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-tumor-grade-scale",serverIdent,serverpw), true);

$headdta = $reviewdta['DATA']['hprhead'];
$ass = $reviewdta['DATA']['associativelisting'];
$prc = $reviewdta['DATA']['percentvalues'];
$mol = $reviewdta['DATA']['moleculartests'];

$reviewer = $headdta['reviewer'];
$reviewer .= ( trim($headdta['inputby']) !== "" && trim($headdta['reviewer']) !== trim($headdta['inputby']) ) ? " ({$headdta['inputby']})" : ""; 

//$ss = ( trim($headdta['hprsubsite']) !== "" ) ? " ({$headdta['hprsubsite']})" : "";
//$hprspc = ( trim($headdta['hprspeccat']) !== "" ) ? " [{$headdta['hprspeccat']}]" : "";
//$hprsite = ( trim($headdta['hprsite']) !== "" || trim($ss) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Site [Specimen Category]</td><td class=qmsDataDsp valign=top>{$headdta['hprsite']}{$ss}{$hprspc}</td></tr>" : "";
//$hprmod = ( trim($headdta['hprdxmod']) !== "" ) ? " ({$headdta['hprdxmod']})" : "";
//$hprdx = ( trim($headdta['hprdx']) !== "" || trim($hprmod) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Diagnosis (Modifier)</td><td  class=qmsDataDsp valign=top>{$headdta['hprdx']}{$hprmod}</td></tr>" : "" ;
//$hprmet = ( trim($headdta['hprmets']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Metastatic FROM</td><td  class=qmsDataDsp valign=top>{$headdta['hprmets']}</td></tr>" : "";
//$hprcomo = ( trim($headdta['hprcomobid']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>System or Co-Mobid</td><td  class=qmsDataDsp valign=top>{$headdta['hprcomobid']}</td></tr>" : "";
//$involv = ( trim($headdta['hpruninvolveddsp']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Uninvolved Sample</td><td class=qmsDataDsp valign=top>{$headdta['hpruninvolveddsp']}</td></tr>" : "";

//THESE ARE THE HPR COMMENTS
//$hprgencmt = ( trim( $headdta['hprgeneralcomments']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Comment:</td><td class=qmsDataDsp valign=top>{$headdta['hprgeneralcomments']}</td></tr>" : "";
//$hprrarecmt = ( trim( $headdta['hprrarereason']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Rare Reason:</td><td class=qmsDataDsp valign=top>{$headdta['hprrarereason']}</td></tr>" : "";
//$hprspecicmt = ( trim( $headdta['hprspecialinstructions']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Special Instructions to Staff:</td><td class=qmsDataDsp valign=top>{$headdta['hprspecialinstructions']}</td></tr>" : "";
//$hprunusecmt = ( trim( $headdta['hprunusabletext']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Reason Unusable:</td><td class=qmsDataDsp valign=top>{$headdta['hprunusabletext']}</td></tr>" : "";
//$hprinconcmt = ( trim( $headdta['hprinconclusivetext']) !== "" ) ? "<tr><td valign=top class=qmsDataLabel>Inconclusive:</td><td class=qmsDataDsp valign=top>{$headdta['hprinconclusivetext']}</td></tr>" : "";

//$pxTbl = "<tr><td valign=top class=qmsDataLabel>A/R/S</td><td class=qmsDataDsp valign=top>{$headdta['bspxiage']} {$headdta['bspxiageuom']} / {$headdta['bspxirace']} / {$headdta['bspxisexdsp']} </td></tr><tr><td class=qmsDataLabel>Chemo/Radiation</td><td class=qmsDataDsp valign=top>{$headdta['bschemoinddsp']} / {$headdta['bsradinddsp']}</td></tr><tr><td class=qmsDataLabel valign=top>Procedure:</td><td class=qmsDataDsp valign=top>{$headdta['bsproctypedsp']} ({$headdta['procurementdate']})</td> </tr><tr><td class=qmsDataLabel valign=top>Institution:</td><td class=qmsDataDsp valign=top colspan=4>{$headdta['bsprocureinstitutiondsp']}</td></tr>  ";

//$tscale = ( trim($headdta['hprtumorscaledsp']) !== "" ) ? " ({$headdta['hprtumorscaledsp']})" : "";
//$tumordsp = ( trim($headdta['hprtumorgrade']) !== "" ) ? "<tr><td class=qmsDataLabel>Tumor Grade/Scale</td><td class=qmsDataDsp>{$headdta['hprtumorgrade']}{$tscale}</td></tr>" : "";

//<table border=0 cellspacing=0 cellpadding=0 width=100% style="margin-top: 1vh;">
//<tr><td style="text-align: center;" colspan=2 class=headerTitleCell>Review Metrics</td></tr>
//{$hprsite}
//{$hprdx}
//{$hprmet}
//{$hprcomo}
//{$involv}
//{$tumordsp}
//{$pxTbl}
//{$hprgencmt}
//{$hprrarecmt}
//{$hprspecicmt}
//{$hprunusecmt}
//{$hprinconcmt}
//</table>

//$hprdatatbl = <<<DATASTUFF
//<table border=0 cellspacing=0 cellpadding=0 width=100%>
//<tr><td class=qmsDataLabel width=15%>Review #: </td><td class=qmsDataDsp>{$headdta['biohpr']}</td></tr><tr><td class=qmsDataLabel>Reviewed: </td><td class=qmsDataDsp>{$headdta['reviewedon']}</td></tr><tr><td class=qmsDataLabel>Reviewer: </td><td class=qmsDataDsp>{$reviewer}</td></tr>
//</table>
//
//DATASTUFF;

//$prnbr = substr(("000000" . $headdta['pathreportid']),-6);
//$uploadline = ( trim($headdta['pruploadedby']) !== "" ) ? "<b>Uploaded</b>: {$headdta['pruploadedby']} :: {$headdta['uploadedon']} (<b>Pathology Report</b>: {$prnbr})" : "";

//{"shippeddate":"","shipdocrefid":"""assignedto":"BANK","investlname":"","investfname":"","investinstitution":"","assignedreq":"""hprresult":18705 }


//ALL ASSOCIATIVE SEGMENTS
//$assbg = "";
//$assTbl = "<table border=0 width=100% id=assBGDspTbl>";
//$bggroups = 0;
//$segrowcnt = 0;
//$innerAss = "<table width=100% border=0 class=iAssTbl>";
//foreach ( $ass as $asskey => $assval ) { 
//    if ( $assbg !== $assval['readlabel'] ) { 
//        //add TBLROW FOR BG
//        $innerAss .= "</table>";
//        $assTbl .= "<tr><td colspan=6 valign=top class=inassdsp>{$innerAss}</td></tr>";
//        $innerAss = "<table border=0 width=100% class=iAssTbl>";
//        $mintgreen = ( substr( $assval['readlabel'] ,0, 6) === substr( $headdta['slidebgs'],0,6) ) ? "mintbck" : "standardbck"; 
//
//        $ss = ( trim($assval['subsite']) !== "" ) ? " ({$assval['subsite']})" : "";
//        $dx = ( trim($assval['dx']) !== "" ) ? " / {$assval['dx']}" : "";
//        $mdx = ( trim($assval['subdx']) !== "" ) ? " ({$assval['subdx']})" : "";
//        $met = ( trim($assval['metsite']) !== "") ? " [{$assval['metsite']}]" : "";
//        $hprcomp = ( (int)$assval['hprind'] === 1 ) ? "<i class=\"material-icons constiticon\">check_circle</i>" : "";
//        $qacomp = ( (int)$assval['qcind'] === 1 ) ? "<i class=\"material-icons constiticon\">check_circle</i>" : "";
//
//        $assTbl .= <<<ROWLINE
//<tr class="{$mintgreen} bgrowline" >
//  <td valign=top>{$assval['readlabel']}</td>
//  <td valign=top>{$assval['specimencategory']} {$assval['site']}{$ss}{$dx}{$mdx}{$met}</td>
//  <td valign=top>{$assval['hprdecdsp']}<br><span class=smlFont>[{$assval['hpron']}]</span></td>
//  <td valign=top>{$assval['qmsstatus']}</td>
//</tr>
//ROWLINE;
//        $assbg = $assval['readlabel']; 
//        $bggroups++;
//    }  
//
//    $prep = ( trim($assval['preparation']) !== "" ) ? " / {$assval['preparation']}" : "";
//    $ifname = ( trim($assval['investfname']) !== "" ) ? ", {$assval['investfname']} " : "";
//    $iname = (trim($assval['investlname']) !== "" ) ? "{$assval['investlname']}{$ifname}" : ""; 
//    $reqNbr = ( trim($assval['assignedreq']) !== "" ) ? "/{$assval['assignedreq']}" : "";
//    $reqency = ( trim($assval['assignedreq']) !== "" ) ? " onclick=\"generateDialog('irequestdisplay','" . cryptservice($assval['assignedreq']) . "');\" " : ""; 
//    $reqPopStrt = ( trim($assval['assignedreq']) !== "" ) ? "<div class=assttholder>" : "";
//    $reqPopEnd = ( trim($assval['assignedreq']) !== "" ) ? "<div class=\"tt quickLink\" {$reqency}>Click to view Request {$assval['assignedreq']}</div></div>" : "";
//
//    $assign = ( trim($assval['assignedto']) !== "" && ( trim($assval['assignedto']) !== "BANK" && trim($assval['assignedto']) !== "QC") ) ? "{$reqPopStrt}{$iname}<br><span class=smlFont>({$assval['assignedto']}{$reqNbr})</span>{$reqPopEnd}" : "-{$assval['assignedto']}-";
//
//    $sdencry = ( trim($assval['shipdocrefid']) !== "" ) ? cryptservice($assval['shipdocrefid']) : "";
//    $ship = ( trim($assval['shipdocrefid']) !== "" ) ? "<div class=sdttholder>" . substr(('000000' . $assval['shipdocrefid']),-6) . "<div class=tt>Shipdoc Status: {$val['sdstatus']}<br>Status by: [INFO NOT AVAILABLE]<p><div onclick=\"displayShipDoc(event,'{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">print</i> Print Ship-Doc (" . substr(('000000' . $assval['shipdocrefid']),-6) . ")</div><div onclick=\"navigateSite('shipment-document/{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">edit</i> Edit Ship-Doc (" . substr(('000000' . $assval['shipdocrefid']),-6) . ")</div></div>" : "";
//    $ship .= ( trim($assval['shippeddate']) !== "" ) ? "<br><span class=smlFont>[{$assval['shippeddate']}]</font>" : "";
//
//    $innerAss .= <<<INASSTBL
//  <tr>
//    <td valign=top>{$assval['bgs']}</td>
//    <td valign=top>{$assval['prepmethod']}{$prep}</td>
//    <td valign=top>{$assign}</td>
//    <td valign=top>{$ship}</td>
//  </tr>
//INASSTBL;
//    $segrowcnt++; 
//}
//$innerAss .= "</table>";
//$assTbl .= "<tr><td colspan=6 valign=top class=inassdsp>{$innerAss}</td></tr>";
//$assTbl .= "</table>";

//TODO:  Figure out a way not to hard code this 
    switch ( $headdta['hprdecisionvalue'] ) { 
      case 'DENIED':
        $decIcon = "cancel";        
        break;
      case 'CONFIRM':
        $decIcon = "check_circle";
        break;
      case 'ADDITIONAL':
        $decIcon = "add_circle";
        break;
      case 'INCONCLUSIVE':
        $decIcon = "help";
        break;
      case 'UNUSABLE':
        $decIcon = "block";
        break;
    }

//<table border=0 cellspacing=0 cellpadding=0><tr><td class=headerTitleCell>Pathology Report</td></tr><tr><td valign=top><div id=qmsPRSideDsp><table><tr><td>{$headdta['pathreport']}</td></tr><tr><td align=right>{$uploadline}</td></tr></table></div></td></tr></table> 

//THIS IS THE SEGMENT TABLE ALL ASSOCIATIVE GROUPS
//<table border=0 width=100%><tr><td id=assDspMetrics>Biogroups: {$bggroups} / Segment Records: {$segrowcnt}</td></tr><tr><td> {$assTbl} </td></tr></table> 

//$hprside = <<<HPRTBL
//<table border=0 cellspacing=0 cellpadding=0>
//<tr><td class=headerTitleCell><table><tr><td><i class=material-icons>{$decIcon}</i></td><td>REVIEW {$headdta['slidebgs']} / {$headdta['hprdecision']} </td></tr></table></td></tr>
//<tr><td valign=top style="height: 3vh; ">
//  <table border=0 style="background: rgba(160,160,160,.5);">
//    <tr>
//        <td class=hprSideTopBtns onclick="changeSupportingTab(0);">Review<br>Metrics</td>
//        <td class=hprSideTopBtns onclick="changeSupportingTab(1);">Pathology<br>Report</td>
//        <td class=hprSideTopBtns onclick="changeSupportingTab(2);">Associate &amp; Constitutent<br>Segments</td>
//    </tr>
//  </table></td></tr>
//<tr><td valign=top>
//
//<div id=masterHold>    
//<div id=dspTabContent0 class=HPRReviewDocument style="display: block; padding: 2vh 0;"> 
//      <table border=0  cellspacing=0 cellpadding=0 width=100%><tr><td valign=top> {$hprdatatbl} </td></tr></table></div>
//
//<div id=dspTabContent1 class=HPRReviewDocument style="display: none; margin-top: 1vh;">
//</div>
//
//<div id=dspTabContent2 class=HPRReviewDocument style="display: none;">
//</div>
//
//</div>
//</td></tr>
//</table>
//HPRTBL;

if ( $headdta['hprdecisionvalue'] === 'INCONCLUSIVE' ) {

//INCONCLUSIVE WORKBENCH GOES HERE    
$workbench = <<<WORKBENCH
INCONCLUSIVE DISPLAY GOES HERE     
WORKBENCH;

  $topBtnBar = generatePageTopBtnBar('qmsactionworkincon', "", $_SERVER['HTTP_REFERER'] );

} else { 

//REGULAR WORK BENCH STARTS HERE 
//if ( trim($headdta['hprspeccat']) == trim($headdta['bsspeccat'])) {
//    if ( trim($headdta['hprspeccat']) !== "") { 
//        $dxdsspc = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprspeccat'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprspeccat']) !== "") {
//        $dxdsspc = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprspeccat'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsspeccat']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsspc = "<span class=bshprnonmatch><div class=ttholder>[HPR SPECIMEN CATEGORY REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsspeccat']))  . "]</div></div></span>"; 
//    }
//}
// 
//if ( trim($headdta['hprsite']) == trim($headdta['bsanatomicsite'])) {
//    if ( trim($headdta['hprsite']) !== "") { 
//        $dxdsst = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprsite'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprsite']) !== "") {
//        $dxdsst = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprsite'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsanatomicsite']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsst = "<span class=bshprnonmatch><div class=ttholder>[HPR SITE REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsanatomicsite']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprsubsite']) == trim($headdta['bssubsite'])) {
//    if ( trim($headdta['hprsubsite']) !== "") { 
//        $dxdsst = "<span class=bshprmatch>(" . strtoupper(trim($headdta['hprsubsite'])) . ")</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprsubsite']) !== "") {
//        $dxdssst = "<span class=bshprnonmatch><div class=ttholder>(" . strtoupper(trim($headdta['hprsubsite'])) . ")<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bssubsite']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdssst = "<span class=bshprnonmatch><div class=ttholder>[HPR SUB-SITE REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bssubsite']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprdx']) == trim($headdta['bsdx'])) {
//    if ( trim($headdta['hprdx']) !== "") { 
//        $dxdsdx = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprdx'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprdx']) !== "") {
//        $dxdsdx = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprdx'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsdx']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsdx = "<span class=bshprnonmatch><div class=ttholder>[HPR DIAGNOSIS REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsdx']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprdxmod']) == trim($headdta['bsdxmod'])) {
//    if ( trim($headdta['hprdxmod']) !== "") { 
//        $dxdsdxm = "<span class=bshprmatch>(" . strtoupper(trim($headdta['hprdxmod'])) . ")</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprdxmod']) !== "") {
//        $dxdsdxm = "<span class=bshprnonmatch><div class=ttholder>(" . strtoupper(trim($headdta['hprdxmod'])) . ")<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsdxmod']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsdxm = "<span class=bshprnonmatch><div class=ttholder>[HPR DIAGNOSIS MODIFIER REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsdxmod']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprmets']) == trim($headdta['bsmets'])) {
//    if ( trim($headdta['hprmets']) !== "") { 
//        $dxdsmets = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprmets'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprmets']) !== "") {
//        $dxdsmets = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprmets'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsmets']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsmets = "<span class=bshprnonmatch><div class=ttholder>[HPR DIAGNOSIS MODIFIER REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsmets']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprmets']) == trim($headdta['bsmets'])) {
//    if ( trim($headdta['hprmets']) !== "") { 
//        $dxdsmets = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprmets'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprmets']) !== "") {
//        $dxdsmets = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprmets'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsmets']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdsmets = "<span class=bshprnonmatch><div class=ttholder>[HPR DIAGNOSIS MODIFIER REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bsmets']))  . "]</div></div></span>"; 
//    }
//}
//if ( trim($headdta['hprcomobid']) == trim($headdta['bscomo'])) {
//    if ( trim($headdta['hprcomobid']) !== "") { 
//        $dxdscomo = "<span class=bshprmatch>" . strtoupper(trim($headdta['hprcomobid'])) . "</span>";
//    } else { 
//      //BOTH HPR AND BS BLANK
//    }
//} else { 
//    if ( trim($headdta['hprcomobid']) !== "") {
//        $dxdscomo = "<span class=bshprnonmatch><div class=ttholder>" . strtoupper(trim($headdta['hprcomobid'])) . "<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bscomo']))  . "]</div></div></span>"; 
//    } else { 
//        $dxdscomo = "<span class=bshprnonmatch><div class=ttholder>[HPR DIAGNOSIS MODIFIER REMOVED]<div class=tt style=\"white-space: nowrap;\">Procured as: [" .  strtoupper(trim($headdta['bscomo']))  . "]</div></div></span>"; 
//    }
//}

//PERCENTAGE TABLE
//$percentTbl = "<table border=0><tr><td colspan=4 class=qmsDataLabel>Biosample Composition Percentages</td></tr><tr>";
//$cntr = 0;
//foreach ( $prc as $pkey => $pvalue ) { 
//   if ($cntr === 4) { 
//     $percentTbl .= "</tr><tr>";
//     $cntr = 0;
//   }
//   $percentTbl .= "<td><table><tr><td>{$pvalue['ptypedsp']}</td><td><input type=text id={$pvalue['ptypeval']} class=prcDisplayValue value={$pvalue['prcvalue']}></td></tr></table></td>";
//   $cntr++;
//}
//$percentTbl .= "</tr></table>";

//UNINVOLVED MENU
//$univData = dropmenuUninvolvedIndicator( $headdta['hpruninvolvedvalue']   );
//$uninvmenu = $univData['menuObj'];

//BUILD TUMOR GRADE 
//$tmrGrd = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"fillField('fldTumorGradeScale','','');\" class=ddMenuClearOption>[clear]</td></tr>";
//$thistumorscalevalue = "";
//$thistumorscaledsp = "";
//  foreach ( $tmrGradeScaleList['DATA'] as $procval) { 
//      if ( $procval['lookupvalue'] === $headdta['hprtumorscalevalue'] ) { 
//         $thistumorscalevalue = $procval['lookupvalue'];
//         $thistumorscaledsp = $procval['menuvalue'];
//      }
//      $tmrGrd .= "<tr><td onclick=\"fillField('fldTumorGradeScale','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
//  }
//$tmrGrd .= "</table>";
//END TUMOR GRADE

//molecular test
//$molemnu = "<table border=0 width=100% class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"triggerMolecularFill(0,'','','{$idsuffix}');\" class=ddMenuClearOption>[clear]</td></tr>";
//foreach ($moletest['DATA'] as $moleval) { 
//  $molemnu .= "<tr><td onclick=\"triggerMolecularFill({$moleval['menuid']},'{$moleval['menuvalue']}','{$moleval['dspvalue']}','{$idsuffix}');\" class=ddMenuItem>{$moleval['dspvalue']}</td></tr>";
//}
//$molemnu .= "</table>";

//if ( count($mol) > 0 ) {
//      $moleTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
//      $cntr = 0;         
//      
//      foreach ( $mol as $molkey => $molval ) {
//        $fld1 = $molval['moletestvalue'];
//        $fld2 = addslashes($molval['moletest']);
//        $fld3 = $molval['resultindexvalue'];
//        $fld4 = addslashes($molval['resultindex']);  
//        $fld5 = addslashes($molval['resultdegree']);  
//        $moleArrA[] = array( $fld1, $fld2, $fld3, $fld4, $fld5) ;    
//
//        $moleTestTbl .= "<tr onclick=\"manageMoleTest(0,{$cntr},'" . $fldsuffix . "');\" class=ddMenuItem><td style=\"border-bottom: 1px solid rgba(160,160,160,1); width: 1vw;\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 3px;\">cancel</i><td style=\" padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">" . $fld2 . "</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">" . $fld4 . "</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">" . $fld5 . "</td></tr>";
//        $cntr++;
//
//      }
//    }
//    $moleTestTbl .= "</table>";
//    $moleArr = json_encode($moleArrA);  
//[["ALK","ALK (Anaplastic Lymphoma Kinase)","ALK-NEGATIVE","Negative (-)",""]]
//$mol = [{"moletestvalue":"ER","moletest":"ER (Estrogen Receptor)","resultindexvalue":"ER-POSITIVE","resultindex":"Positive (+)","resultdegree":"95%"},{"moletestvalue":"PR","moletest":"PR (Progesterone Receptor)","resultindexvalue":"PR-POSITIVE","resultindex":"Positive (+)","resultdegree":"95%"},{"moletestvalue":"HER2","moletest":"HER2 (Human Epidermal Growth Factor Receptor 2)","resultindexvalue":"HER-POSITIVE","resultindex":"Positive (+)","resultdegree":"3+"}]
//$moleTbl = <<<MOLETBL
//      <table border=0 class=maindxdstbl>
//       <tr><td colspan=2 class=hprPreLimFldLbl>Indicated Immuno/Molecular Test Results</td><td class=hprPreLimFldLbl>Result Index</td><td class=hprPreLimFldLbl colspan=2>Scale Degree</td>
//           <td rowspan=2 valign=top class=btnBar style="padding-top: 2vh; "> <button onclick="manageMoleTest(1,'','{$idsuffix}');">Add Test</button> </td></tr>
//       <tr><td class=fieldHolder valign=top colspan=2>
//                    <div class=menuHolderDiv>
//                      <input type=hidden id=hprFldMoleTest{$idsuffix}Value>
//                      <input type=text id=hprFldMoleTest{$idsuffix} READONLY style="width: 10vw;" class=hprDataField>
//                      <div class=valueDropDown style="min-width: 25vw;">{$molemnu}</div>
//                    </div>
//            </td>
//            <td class=fieldHolder valign=top>
//             <div class=menuHolderDiv>
//               <input type=hidden id='hprFldMoleResult{$idsuffix}Value'>
//               <input type=text id='hprFldMoleResult{$idsuffix}' READONLY class=hprDataField style="width: 10vw;">
//               <div class=valueDropDown id=moleResultDropDown style="min-width: 12.5vw;"> </div>
//             </div>
//            </td>
//            <td class=fieldHolder valign=top>
//              <input type=text id=hprFldMoleScale{$idsuffix} class=hprDataField style="width: 10vw;">
//            </td>
//       </tr>
//       <tr><td colspan=6 valign=top>
//           <input type=hidden id=hprMolecularTestJsonHolderConfirm value='{$moleArr}'>
//           <div id=dspDefinedMolecularTestsConfirm{$idsuffix} class=dspDefinedMoleTests>
//           {$moleTestTbl}
//           </div>
//           </td>
//       </tr>
//      </table>
//MOLETBL;
//MOLETBL END

//$workbench = <<<WORKBENCH
//<div id=qaHeader>
//<table><tr><td><i class=material-icons>{$decIcon}</i></td><td>Slide {$headdta['slidebgs']} / {$headdta['hprdecision']} </td></tr></table>
//</div>
//<div id=qaVocabChanger>
//    <table border=1 
//        id="wb{$headdta['biohpr']}"
//        class=maindxdstbl
//        data-speccat="{$headdta['hprspeccat']}"
//        data-site="{$headdta['hprsite']}"
//        data-ssite="{$headdta['hprsubsite']}"
//        data-dx="{$headdta['hprdx']}"
//        data-moddx="{$headdta['hprdxmod']}"
//        data-mets="{$headdta['hprmets']}"
//        data-como="{$headdta['hprcomobid']}"
//        data-ohspeccat="{$headdta['hprspeccat']}"
//        data-ohsite="{$headdta['hprsite']}"
//        data-ohssite="{$headdta['hprsubsite']}"
//        data-ohdx="{$headdta['hprdx']}"
//        data-ohmoddx="{$headdta['hprdxmod']}"
//        data-ohmets="{$headdta['hprmets']}"
//        data-ohcomo="{$headdta['hprcomobid']}"        
//        >
//        <tr><td colspan=2 class=qmsDataLabel>Diagnosis Designation</td><td rowspan=5 valign=top class=btnBar><table><tr><td><button onclick="alert('{$headdta['biohpr']}');">Change Vocab</button></td></tr><tr><td><button onclick="alert('{$headdta['biohpr']}');">Reset Vocab</button></td></tr><tr><td><button onclick="alert('{$headdta['biohpr']}');">METS FROM</button></td></tr><tr><td><button onclick="alert('{$headdta['biohpr']}');">Sys/Co-Mo</button></td></tr>   </table></td></tr>
//        <tr><td colspan=2><table><tr><td>{$dxdsspc}</td><td>{$dxdsst}</td><td>{$dxdssst}</td><td>{$dxdsdx}</td><td>{$dxdsdxm}</td></tr></table></td></tr>
//        <tr><td class=qmsDataLabel>MET From</td><td class=qmsDataLabel>Systemic/CoMobid</td></tr>    
//        <tr><td>{$dxdsmets}&nbsp;</td><td>{$dxdscomo}&nbsp;</td></tr>           
//        <tr><td>Uninvolved Biosample</td><td>Tumor Grade/Scale</td></tr>
//                <tr><td>{$uninvmenu}</td><td><table><tr><td><input type=text id=fldTumorGrade style="width: 5vw;" value="{$headdta['hprtumorgrade']}"></td><td> <div class=menuHolderDiv><input type=hidden id=fldTumorGradeScaleValue value="{$thistumorscalevalue}"><input type=text id=fldTumorGradeScale READONLY class="inputFld hprDataField" style="width: 10vw;" value="{$thistumorscaledsp}"><div class=valueDropDown style="min-width: 10vw;">{$tmrGrd}</div></div></td></tr></table></td></tr>      
//    </table> 
//</div>    
//
//<div id=qaPrcMole><table><tr><td valign=top>{$percentTbl}</td><td valign=top>{$moleTbl}</td></tr></table></div>
//
//<div id=qaBGComments><table><tr><td>Biogroup Comments</td><td>QMS Question</td></tr><tr><td><TEXTAREA id=qaBGComments>  </TEXTAREA></td><td><TEXTAREA id=qaBGQSTN></textarea></td></tr></table></div>
//
//<div id=qaControlLine><table id=qacontrollinetbl><tr><td><button onclick="alert('{$headdta['biohpr']}');">Accept Biogroup</button></td></tr></table></div>

//WORKBENCH;
  $workbench = (int)$headdta['biohpr'];
  $topBtnBar = generatePageTopBtnBar('qmsactionwork', "", $_SERVER['HTTP_REFERER'] );
}


$pg = <<<PGCONTENT
{$topBtnBar}
{$workbench}
PGCONTENT;
return $pg;
}

function bldQMSQueList ( $decisiondisplay ) {     
    
    
$pdta['decisiondisplay'] = (trim($decisiondisplay) === "") ? "all" : $decisiondisplay ;
$payload = json_encode($pdta);
$qmsquedta = json_decode(callrestapi("POST", dataTree . "/data-doers/qms-que-list",serverIdent, serverpw, $payload), true);    

$cellCntr = 1;
$cntConfirm = 0;
$cntAdd = 0;
$cntDenied = 0;
$cntUnuse = 0;
$cntIncon = 0;
foreach ($qmsquedta['DATA'] as $qkey => $qval ) {  
    $procdesig =  ( trim($qval['procspeccat']) !== "" ) ? "[{$qval['procspeccat']}]" : "";
    $procdesig .= ( trim($qval['procsite']) !== "") ? " " . strtoupper($qval['procsite']) : "";
    $procdesig .= ( trim($qval['procsubsite']) !== "" ) ?  (trim($procdesig) !== "") ? " (" . strtoupper($qval['procsubsite']) . ")" :  " " . strtoupper($qval['procsubsite']) : "";
    $procdesig .= ( trim($qval['procdiagnosis']) !== "" ) ?  (trim($procdesig) !== "") ? " / " . strtoupper($qval['procdiagnosis']) :  " " . $qval['procdiagnosis'] : "";
    $procdesig .= ( trim($qval['procsubdiagnosis']) !== "" ) ?  " (" . strtoupper($qval['procsubdiagnosis']) . ")" :  "";
    $procdesig = trim($procdesig);
    $hprdesig = ( trim($qval['hprspeccat']) !== "" ) ? "[{$qval['hprspeccat']}]" : "";
    $hprdesig .= ( trim($qval['hprsite']) !== "") ? " " . strtoupper($qval['hprsite']) : "";
    $hprdesig .= ( trim($qval['hprsubsite']) !== "" ) ?  (trim($hprdesig) !== "") ? " (" . strtoupper($qval['hprsubsite']) . ")" :  " " .  strtoupper($qval['hprsubsite']) : "";
    $hprdesig .= ( trim($qval['hprdiagnosis']) !== "" ) ?  (trim($hprdesig) !== "") ? " / " . strtoupper($qval['hprdiagnosis']) :  " " . $qval['hprdiagnosis'] : "";
    $hprdesig .= ( trim($qval['hprsubdiagnosis']) !== "" ) ?  " (" . strtoupper($qval['hprsubdiagnosis']) . ")" :  "";
    $hprdesig = trim($hprdesig);
    $reviewid = cryptservice($qval['hprresultid'], 'e');

    //TODO:  Figure out a way not to hard code this 
    switch ( $qval['hprdecisionvalue'] ) { 
      case 'DENIED':
        $decIcon = "cancel";
        $cntDenied++;
        break;
      case 'CONFIRM':
        $decIcon = "check_circle";
        $cntConfirm++; 
        break;
      case 'ADDITIONAL':
        $decIcon = "add_circle";
        $cntAdd++;
        break;
      case 'INCONCLUSIVE':
        $decIcon = "help";
        $cntIncon++;
        break;
      case 'UNUSABLE':
        $decIcon = "block";
        $cntUnuse++;
        break;
    }
    $dataTbl = "<div class=qmsQueItemTbl><div class=tblDspRow><div class=queDataLabel valign=top>Review: </div><div class=queDataDsp valign=top>{$qval['hprslidereviewed']} ({$qval['hprby']} :: {$qval['hpron']})</div></div>"
                                   . "<div class=tblDspRow><div class=queDataLabel valign=top>HPR Decision: </div><div class=queDataDsp valign=top>{$qval['hprdecisiondsp']}</div></div>"
                                   . "<div class=tblDspRow><div class=queDataLabel valign=top>Proc-Designation: </div><div class=\"queDataDsp\" valign=top>{$procdesig}</div></div>"                              
                                   . "<div class=tblDspRow><div class=queDataLabel valign=top>HPR-Designation: </div><div class=\"queDataDsp\" valign=top>{$hprdesig}</div></div>"
                      . "</div>";
    $iQueTbl .= "<tr onclick=\"navigateSite('qms-actions/work-bench/" . $reviewid . "');\"><td class=\"queCellHolder sideiconholder\"><i class=\"material-icons hprdecisionicon decision_{$qval['hprdecisionvalue']} \">{$decIcon}</i></td><td valign=top class=queCellHolder>{$dataTbl}</td></tr>";
    $cellCntr++;
}

//TODO:  MAke This Dynamic!
$legendConfirm = ( ( $pdta['decisiondisplay'] == 'all' || $pdta['decisiondisplay'] == 'confirm') || (int)$cntConfirm > 0 ) ? "<tr><td><i class=\"material-icons dspgreen\">check_circle</i></td><td>Confirmed Cases </td><td class=nbrDsp>{$cntConfirm}</td></tr>" : ""; 
$legendConfirmAdd = ( ( $pdta['decisiondisplay'] == 'all' || $pdta['decisiondisplay'] == 'add') || (int)$cntAdd > 0 ) ? "<tr><td><i class=\"material-icons dspblue\">add_circle</i></td><td>With Additions </td><td class=nbrDsp>{$cntAdd} </td></tr>" : ""; 
$legendDenied = ( ( $pdta['decisiondisplay'] == 'all' || $pdta['decisiondisplay'] == 'denied') || (int)$cntDenied > 0 ) ? "<tr><td><i class=\"material-icons dspred\">cancel</i></td><td>Denied Cases</td><td class=nbrDsp>{$cntDenied}</td></tr>" : ""; 
$legendUnusable = ( ( $pdta['decisiondisplay'] == 'all' || $pdta['decisiondisplay'] == 'unusable') || (int)$cntUnuse > 0 ) ? "<tr><td><i class=\"material-icons dspbrown\">block</i></td><td>Unusable Cases</td><td class=nbrDsp>{$cntUnuse}</td></tr>" : ""; 
$legendInconclusive = ( ( $pdta['decisiondisplay'] == 'all' || $pdta['decisiondisplay'] == 'inconclusive') || (int)$cntIncon > 0 ) ? "<tr><td><i class=\"material-icons dsppurple\">help</i></td><td>Inconclusive</td><td class=nbrDsp>{$cntIncon}</td></tr>" : ""; 

$pg = <<<BLDTBL
<div id=legendDsp>

       <table border=0 id=legendTbl>
            <tr><td colspan=3 class=legendTitle>Legend &amp; Count</td></tr>
            {$legendConfirm} 
            {$legendConfirmAdd}                       
            {$legendDenied} 
            {$legendUnusable} 
            {$legendInconclusive}                       
            <tr><td></td><td><b>Total Queued </td><td class=nbrDsp><b>{$qmsquedta['ITEMSFOUND']}</td></tr>
       </table>

</div>

<table border=0 id=mainDspTbl><tr><td class=tblTitle >QA Case Review Queue</td></tr>
<tr><td>
<table border=0 id=dspQueTbl>
<tbody> 
{$iQueTbl}
</table>
</td></tr>
</table>
BLDTBL;
return $pg;
}

function bldHPRVocabSystemic ( $dialogid, $objectid ) { 
//TODO: Turn into a webservice    
require(serverkeys . "/sspdo.zck");    
$allDXSQL = "SELECT diagnosis FROM four.sys_master_menu_vocabulary where ifnull(systemicIndicator,0) = 1 order by diagnosis";
$allDXRS = $conn->prepare($allDXSQL); 
$allDXRS->execute(); 

$allDXTbl = "<table border=0 id=hprVocabResultTbl style=\"width: 100%;\" cellspacing=0 cellpadding=0><thead><tr><th>Systemic Diagnosis</th></tr><tbody>";
while ( $r = $allDXRS->fetch(PDO::FETCH_ASSOC) ) { 
  $allDXTbl .= "<tr ondblclick=\"makeHPRSystemic( '{$dialogid}','{$r['diagnosis']}');\" ><td>{$r['diagnosis']}</td></tr>";
}
$allDXTbl .= "</tbody></table>";

$pg = <<<PAGECONTENT
<style>
#vocabBrowserDsp { height: 30vh; overflow: auto; border: 1px solid #000;  }
#instructionblock {  box-sizing: border-box; font-size: 1.2vh; line-height: 1.5em; text-align:justify; }
</style>
<table border=0>
<tr><td id=instructionblock style="width: 50vh;">INSTRUCTIONS: This is the list of possible co-mobidities/systemic diagnosis. To choose one, double-click that entry.  This list is designated by CHTN Eastern.  It has no bearing on the diagnosis designation but should be a condition which applies to the donor's complete system.  </td></tr>
<tr><td colspan=2><div id=vocabBrowserDsp>{$allDXTbl}</div></td></tr>
</table>
PAGECONTENT;
return $pg;
}

function bldHPRVocabMETSSite ( $dialogid, $objectid ) { 
//TODO: Turn into a webservice    
require(serverkeys . "/sspdo.zck");    
$allDXSQL = "select * from (SELECT distinct site, '' as subsite FROM four.sys_master_menu_vocabulary where trim(ifnull(site,'')) <> '' ) uniontbl order by site, subsite";
$allDXRS = $conn->prepare($allDXSQL); 
$allDXRS->execute(); 

$allDXTbl = "<table border=0 id=hprVocabResultTbl style=\"width: 100%;\" cellspacing=0 cellpadding=0><thead><tr><th>Site</th></tr></thead><tbody>";
while ( $r = $allDXRS->fetch(PDO::FETCH_ASSOC) ) { 
  $allDXTbl .= "<tr ondblclick=\"makeHPRMetsFrom( '{$dialogid}','{$r['site']}','{$r['subsite']}');\" ><td>{$r['site']}</td></tr>";
}
$allDXTbl .= "</tbody></table>";

$pg = <<<PAGECONTENT
<style>
#vocabBrowserDsp { height: 30vh; overflow: auto; border: 1px solid #000;  }
#instructionblock {  box-sizing: border-box; font-size: 1.2vh; line-height: 1.5em; text-align:justify; }
</style>
<table border=0>
<tr><td id=instructionblock style="width: 50vh;" >INSTRUCTIONS: This is the 'metastatic from' site list.  This list is all sites from the CHTN Network Vocabulary without regard to diagnosis.  To choose a site to specify as 'metastatic from', double-click that choice on the list. </td></tr>
<tr><td style="border: 1px solid rgba(160,160,160,1);"><div id=vocabBrowserDsp>{$allDXTbl}</div></td></tr>
</table>
PAGECONTENT;
return $pg;
}

function bldHPRVocabDXOverride ( $dialogid, $objectid ) {

$pg = <<<PAGECONTENT
<style>
#vocabBrowserDsp { height: 30vh; overflow: auto; border: 1px solid #000;  }
#instructionblock { width: 51vw; box-sizing: border-box; font-size: 1.2vh; line-height: 1.5em; text-align:justify; }
#voclbl { font-size: 1.5vh; font-weight: bold; color: rgba(0,32,113,1); padding: .5vh 0 0 0; }
#srchHPRVocab {width: 51vw; }
</style>
<table border=0>
<tr><td colspan=2 id=instructionblock>INSTRUCTIONS: Enter a diagnosis in the 'Diagnosis Term' Field.  These are 'like' value matches meaning that whole terms do not need to be entered.  For instance, if searching for Follicular-variant, the term entered could be: 'follicular' or more simply 'folli'.  ScienceServer will match terms as they are entered.  To select a term for use in the review, double-click the term in the result listing. <b>THIS IS A DIAGNOSIS OVERRIDE.  WITH THIS DIALOG ASSIGNMENT OF DIAGNOSIS IS PERFORMED WITHOUT REGARD TO SITE-SPECIMEN CATEGORY. THEREFORE LEAVING THE CONFINES OF THE CHTN NETWORK APPROVED VOCABULARY. </td></tr>
<tr><td id=voclbl>Diagnosis Term</td><td align=right></td></tr>
<tr><td colspan=2><input type=text id=srchHPRVocab onkeyup="browseHPRDxOverride(this.value, '{$dialogid}'  );"></td></tr>
<tr><td colspan=2 style="border: 1px solid rgba(160,160,160,1);"><div id=vocabBrowserDsp>{$allDXTbl}</div></td></tr>
</table>
PAGECONTENT;
return $pg;
}

function bldHPRVocabBrowser ( $dialogid, $objectid ) { 
$pg = <<<PAGECONTENT
<style>
#vocabBrowserDsp { height: 30vh; overflow: auto; border: 1px solid #000;  }
#instructionblock { width: 51vw; box-sizing: border-box; font-size: 1.2vh; line-height: 1.5em; text-align:justify; }
#voclbl { font-size: 1.5vh; font-weight: bold; color: rgba(0,32,113,1); padding: .5vh 0 0 0; }
#srchHPRVocab {width: 51vw; }
</style>
<table border=0>
<tr><td colspan=2 id=instructionblock>INSTRUCTIONS: Enter a diagnosis in the 'Vocabulary Search Term' field in the following form: <b>specimen category site diagnosis modifier</b>.  These are 'like' value matches meaning that whole terms do not need to be entered.  For instance, if searching for Follicular-variant carcinoma of thyroid, a malignant condition, the term entered could be: 'malignant thyroid carcinoma follicular' or more simply 'thyro car foll'.  ScienceServer will match terms as they are entered.  To include Sub-site listings in the results, check the 'Include Sub-Site' check-box in the right corner before entering a term. To select a term for use in the review, double-click the term in the result listing.</td></tr>
<tr><td id=voclbl>Vocabulary Term</td><td align=right><input type=checkbox id=srchIncludeSub><label for=srchIncludeSub>Include Sub-Sites</label></td></tr>
<tr><td colspan=2><input type=text id=srchHPRVocab onkeyup="browseHPRVocabulary(this.value, byId('srchIncludeSub').checked, '{$dialogid}'  );"></td></tr>
<tr><td colspan=2 style="border: 1px solid rgba(160,160,160,1);"><div id=vocabBrowserDsp></div></td></tr>
</table>

PAGECONTENT;
return $pg;
}

function bldHPRPRBigViewer( $dialogid, $objectid ) { 
    //TODO:  Turn into a webservice
    //TODO:  Search Document for $conn and turn all into webservices  
  require(serverkeys . "/sspdo.zck");    
  $obj = explode( '-' , cryptservice( $objectid, 'd' ));    
  $prTxtSQL = "SELECT  biospecimen, pathreport FROM masterrecord.qcpathreports where prid = :prid and selector = :selector";
  $prTxtRS = $conn->prepare($prTxtSQL);    
  $prTxtRS->execute(array(':prid' => $obj[1] , ':selector' => $obj[2]));
if ( $prTxtRS->rowCount() < 1) { 
    $prTxt = "NO PATHOLOGY REPORT FOUND!";
    $prBG = "";
} else { 
    $pr = $prTxtRS->fetch(PDO::FETCH_ASSOC);
    $prTxt = $pr['pathreport'];
    $prBG = "Pathology Report for {$pr['biospecimen']}";
}
  
  
$pg = <<<PAGECONTENT
<style>
#HPRDialogPRText { width: 80vw; }
#HPRDialogPRTxtDsp { width: 80vw; box-sizing: border-box; height: 60vh; }
#HPRDialogPRHoldDiv {  width: 80vw; box-sizing: border-box; height: 60vh; overflow: auto; font-size: 1.6vh; padding: 8px; color:  rgba(48,57,71,1); line-height: 1.8em; text-align: justify; }   
#HPRDialogAnncLine { background: rgba(100,149,237,1);  font-size: 2vh; font-weight: bold; padding: 8px; color: rgba(255,255,255,1); }        
</style>
 <table id=HPRDialogPRText>
     <tr><td id=HPRDialogAnncLine>{$prBG}</td></tr>
     <tr><td id=HPRDialogPRTxtDsp><div id=HPRDialogPRHoldDiv>{$prTxt}</div></td></tr>
     <tr><td align=right>   
   <table>
<tr>
<td><table class=tblBtn id=btnEventCanel style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Close</td></tr></table></td>
</tr>
</table>
     
     </td></tr>
 </table>
   
PAGECONTENT;
 return $pg;
}

function bldHPREmailerDialog( $dialogid ) { 
require(serverkeys . "/sspdo.zck");
$at = genAppFiles;
$boypic = base64file("{$at}/publicobj/graphics/usrprofile/avatar_male.png", "", "png", true, " class=\"hpremailprofilepicture\" " );   
$girlpic =  base64file("{$at}/publicobj/graphics/usrprofile/avatar_female.png", "", "png", true, " class=\"hpremailprofilepicture\" "); 
$recipListSQL = "SELECT userid, username, displayname, dspjobtitle, profilepicurl FROM four.sys_userbase where allowind = 1 and trim(ifnull(emailaddress,'')) <> '' and allowHPRInquirer = 1 order by lastname";
$recipListRS = $conn->prepare($recipListSQL); 
   $recipListRS->execute(); 
   $recipList = "<div id=recipList><table border=0 id=recipListTbl>";
   //BUILD RECIP LIST
   while ( $rl = $recipListRS->fetch(PDO::FETCH_ASSOC)) { 
       
       switch ($rl['profilepicurl']) { 
      case 'avatar_male':
         $profPicture = "{$boypic}";
         break;
      case 'avatar_female':
         $profPicture = "{$girlpic}";
         break;
      default:
         $profpic = base64file("{$at}/publicobj/graphics/usrprofile/{$rl['profilepicurl']}", "", "png", true, " class=\"hpremailprofilepicture\" " );   
         $profPicture = "{$profpic}";    
    }
       $recipItem = <<<RTBL
<div class=itemHolderDiv><div class=emlProfPic>{$profPicture}</div><div class=emlName>{$rl['username']} ({$rl['displayname']})</div>
 <div class=emlTitle>{$rl['dspjobtitle']}</div></div>               
RTBL;
       $recipList .= "<tr data-selected=\"false\" class=recipitemlisting id=\"recip{$rl['userid']}\" onclick=\"recipSelector(this.id);\"><td> {$recipItem}</td></tr>";
   }
   $recipList .= "</table></div>";

$pg = <<<PAGECONTENT
<style>
        #HPREmailHoldTbl { width: 50vw; height: 50vh; }
        #HPREmailHoldTbl #instruct { text-align: justify; line-height: 1.8em; font-size: 1.2vh;  height: 5vh; }
        #recipList { border: 1px solid #000; height: 47vh; width: 17.5vw; overflow: auto; }
        #recipListTbl { width: 17vw; border-collapse: collapse; }
        #recipListTbl tr:hover { background: rgba(255,248,225,1); cursor: pointer; }
        #recipListTbl tr[data-selected=true] { background: rgba(0, 112, 13,.3); }

        .hpremailprofilepicture { height: 8vh; display: block; margin: 0 auto;}
        .emlProfPic { width: 4.5vw; border-right: 1px solid rgba( 48,57,71, .5); float: left; margin-right: .3vw;  } 
        .emlName { font-size: 1.5vh; font-weight: bold; vertical-align: bottom; padding-top: 2.5vh; }
        .emlTitle { font-size: 1.5vh; font-style: italic; vertical-align: top; } 

        .fieldLabel { font-size: 1.2vh; font-weight: bold; border-bottom: 1px solid rgba(48,57,71,1); }
        #hprEmlMsg { font-size: 1.5vh; line-height: 1.8em; padding: 8px; border: 1px solid rgba(48,57,71,1); width: 32vw; height: 41vh; box-sizing: border-box; } 
</style>
<input type=hidden id=identDialogid value="{$dialogid}">        
<table id=HPREmailHoldTbl border=0 cellspacing=0 cellpadding=0>
<tr><td valign=top rowspan=2><table border=0><tr><td class=fieldLabel style="padding: .4vh 0 0 .2vw;">Select Recipient(s)</td></tr><tr><td>{$recipList}</td></tr></table></td><td valign=top>

   <table><tr><td class=fieldLabel style="padding: .4vh 0 0 .2vw;">Message (Don't forget to mention slides, biogroups or trays that may be relevant)</td></tr>
   <tr><td><TEXTAREA id=hprEmlMsg></TEXTAREA></td></tr>
   </table>


</td></tr>
<tr><td align=right valign=bottom> 

<table>
<tr>
<td><table class=tblBtn id=btnHPREmailSend style="width: 6vw;" onclick="sendHPREmail();"><tr><td style="font-size: 1.3vh;"><center>Send</td></tr></table></td>
<td><table class=tblBtn id=btnEventCanel style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td>
</tr>
</table>

</td></tr>           
</table>        
        
PAGECONTENT;
return $pg;
}

function bldEnlargeDashboardGraphic ( $whichgraphic ) { 
  $at = genAppFiles;
  if ( file_exists("{$at}/publicobj/graphics/sysgraphics/{$whichgraphic}" ) ) { 
    $id = generateRandomString();  
    $graphics = base64file("{$at}/publicobj/graphics/sysgraphics/{$whichgraphic}", "{$id}", "png", true);         
  } else { 
    $graphics = "NO GRAPHIC FOUND ({$whichgraphic})";
  }

  list($iwidth, $iheight, $itype, $iattr) = getimagesize("{$at}/publicobj/graphics/sysgraphics/{$whichgraphic}");
  
  $pgGraphics = <<<ENLRGDGRPH
<table border=0>
<tr><td>{$iwidth} / {$iheight} / {$itype} / {$iattr} </td></tr>
<tr><td style="padding: 2vh 2vw;">{$graphics}</td></tr>
</table>
ENLRGDGRPH;
return $pgGraphics;
}

function bldDialogCalendarAddEvent ( $passeddata ) { 
  require(serverkeys . "/sspdo.zck");
  session_start(); 
  $sess = session_id();
  $pdta = json_decode ( $passeddata, true );

  //TODO:  TURN INTO A WEBSERVICE 
  $usrSQL = "SELECT ifnull(originalAccountName,'') as acctname , presentinstitution, friendlyName, inme.menuvalue as institutioncode, inme.dspvalue institutionname FROM four.sys_userbase usr left join four.sys_userbase_allowinstitution ins on usr.userid = ins.userid left join (SELECT menuid, menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') inme on ins.institutionmenuid = inme.menuid where sessionid = :sess and allowInd = 1 and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0 and ins.onoffind = 1";
  $usrRS = $conn->prepare($usrSQL); 
  $usrRS->execute(array(':sess' => $sess)); 

  if ( $usrRS->rowCount() === 0 ) { 
$inner = <<<INNNNER
  <h2> User account not found - or session has expired </h2>
INNNNER;
  } else {     

  $usr = "";
  $friendly = "";
  $presentInstitution = "";

  $instMnu = "<table border=0 class=menuDropTbl>";
  $instMnu .= "<tr><td onclick=\"fillField('rootEventInstitution','ALLINST','All CHTN Eastern Locations');\" class=ddMenuItem>All CHTN Eastern Institutions</td></tr>";
  while ($u = $usrRS->fetch(PDO::FETCH_ASSOC)) { 
    $usr = $u['acctname']; 
    $friendly = $u['friendlyName']; 
    $presentInstitution = $u['presentinstitution']; 
    $instMnu .= "<tr><td onclick=\"fillField('rootEventInstitution','{$u['institutioncode']}','{$u['institutionname']}');\" class=ddMenuItem>{$u['institutionname']}</td></tr>";
  } 
  $instMnu .= "<tr><td onclick=\"fillField('rootEventInstitution','{$usr}','Just for {$friendly}');\" class=ddMenuItem>[Private Event] Just for {$friendly}</td></tr>";
  $instMnu .= "</table>";

  $rqDate = DateTime::createFromFormat('Ymd', date('Ymd'));
  $py = $rqDate->format('Y');
  $pm = $rqDate->format('m');
  $pd = $rqDate->format('d'); 
  $dteDefault = "";
  $dteDefaultVal = ""; 

  if ( trim($pdta['objid']) !== "" ) { 
    //default date passed 
      if ( verifyDate( trim($pdta['objid']), 'Ymd',true) ) { 
          //GOOD DATE
          $rqDate = DateTime::createFromFormat('Ymd', $pdta['objid']);
          $py = $rqDate->format('Y');
          $pm = $rqDate->format('m');
          $pd = $rqDate->format('d'); 
          $dteDefault = "{$pm}/{$pd}/{$py}";
          $dteDefaultVal = "{$py}-{$pm}-{$pd}"; 
      }     
  }

  $timesval = json_decode(callrestapi("GET", dataTree . "/globalmenu/time-value-list",serverIdent,serverpw),true);
  $etypeval = json_decode(callrestapi("GET", dataTree . "/globalmenu/calendar-event-types",serverIdent,serverpw),true);
  $sTime = "<table border=0 class=menuDropTbl>";
  foreach ($timesval['DATA'] as $tval) {
    $sTime .= "<tr><td onclick=\"fillField('rootEventStart','{$tval['lookupvalue']}','{$tval['menuvalue']}');\" class=ddMenuItem>{$tval['menuvalue']}</td></tr>";
  }
  $sTime .= "</table>";
  $eTime = "<table border=0 class=menuDropTbl>";
  foreach ($timesval['DATA'] as $tval) {
    $eTime .= "<tr><td onclick=\"fillField('rootEventEnd','{$tval['lookupvalue']}','{$tval['menuvalue']}');\" class=ddMenuItem>{$tval['menuvalue']}</td></tr>";
  }
  $eTime .= "</table>";
  $eType = "<table border=0 class=menuDropTbl>";
  foreach ($etypeval['DATA'] as $etval) {
    $eType .= "<tr><td onclick=\"fillField('rootEventtype','{$etval['lookupvalue']}','{$etval['menuvalue']}');\" class=ddMenuItem>{$etval['menuvalue']}</td></tr>";
  }
  $eType .= "</table>";
  $r = buildcalendar('rootevent', $pm, $py ); 
  $rootEventCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=rootEventDateValue value="{$dteDefaultVal}"><input type=text READONLY id=rootEventDate class=rEventFld value="{$dteDefault}"></div>
  <div class=valueDropDown><div id=rootEventDropCal>{$r}</div></div>
</div>
CALENDAR;

  
  //TODO: TURN INTO WEBSERVICE
  session_start();
  $sessid = session_id();
  $chkUsrSQL = "SELECT originalaccountname as usr, presentinstitution as presentinstitution FROM four.sys_userbase where 1=1 and sessionid = :sessid and ( allowInd = 1 ) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
  $rs = $conn->prepare($chkUsrSQL); 
  $rs->execute(array(':sessid' => $sessid));
  if ($rs->rowCount() === 1) { 
    $u = $rs->fetch(PDO::FETCH_ASSOC); 
  }
  
  
  $curSQL = "SELECT eventid, inputby, inputon, date_format(eventdate,'%m/%d/%Y') as eventdate, loc.dspvalue as location, if (alldayind = 1,'AllDay', concat( ifnull(cal.eventstarttime,''), if(ifnull(cal.eventendtime,'')='','',concat('&#45;',ifnull(cal.eventendtime,''))))) as eventtime, evt.dspvalue dspEventType, if(ifnull(cal.eventtitle,'')='',concat(ifnull(cal.icdonorinitials,''), if(ifnull(cal.icsurgeon,'')='','',concat('::', ifnull(cal.icsurgeon,'')))),ifnull(cal.eventtitle,'')) as eventtitle, ifnull(cal.eventdesc,'') as eventdesc FROM four.sys_master_calendar cal left join (SELECT menuvalue, dspvalue, googleiconcode as dspcolor FROM four.sys_master_menus where menu = 'EVENTTYPE' and dspind = 1) as evt on cal.eventtype = evt.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION' union select 'ALLINST','All CHTN Eastern Locations' union SELECT originalaccountname, concat('Display for Me (', ifnull(displayname,''),')') usrAsLoc FROM four.sys_userbase) as loc on cal.dspForWhom = loc.menuvalue where dspind = :dsp and eyear = :yr and emonth = :mn and eday = :dy and ( dspForWhom = 'ALLINST' OR dspForWhom = :presinst OR lcase(dspForWhom) = :usr) order by eventstarttime";
  $curRS = $conn->prepare($curSQL);
  $curRS->execute(array(':dsp' => 1, ':yr' => $py, ':mn' => $pm, ':dy' => $pd, ':presinst' => $u['presentinstitution'], ':usr' => $u['usr']));
  if ( $curRS->rowCount() === 0 ) {
      $currentEvntList = "";
  } else { 
      //BUILD DELETE TABLE
      $curEvTbl = "<table><tr><td><span id=annc>Today's Events</span> (click 'trash' icon to delete)</td></tr></table><table border=0 cellspacing=0 cellpadding=0 id=tblTodaysEventList>";
      while ($e = $curRS->fetch(PDO::FETCH_ASSOC)) {           
         $evDate = DateTime::createFromFormat('m/d/Y', $e['eventdate']);
         $nwDate = DateTime::createFromFormat('m/d/Y', date('m/d/Y'));         
         $delAction = "";
         $evIcon = "<i class=\"material-icons indIcon\">check_box</i>";
          if ( strtolower($e['inputby']) === strtolower($u['usr']) ) {
            if ( $evDate < $nwDate ) {
            } else {
              $evIcon = "<i class=\"material-icons indIcon delIcon\">delete_forever</i>";
              $delAction = " onclick=\"rootCalendarDeleteEvent('{$e['eventid']}');\" ";
              
            }
          }
          $curEvTbl .= "<tr><td {$delAction}>{$evIcon}</td><td>{$e['eventtime']}</td><td>{$e['eventdesc']}</td><td>{$e['dspEventType']}</td><td>{$e['location']}</td></tr>";          
      }
      $curEvTbl .= "</table>";
      
      $currentEvntList = "<div id=divTodaysEventList>{$curEvTbl}</div>";
  }
  
  $inner = <<<INNNER
<input type=hidden id=dialogidhld value="{$pdta['dialogid']}">
<table border=0>
<tr><td rowspan = 7 valign=top> {$currentEvntList} </td><th>Event Date</th><th>Start Time</th><th>End Time</th><th>Event Type</th><th colspan=3 id=icmdheader>IC/MD Initials</th></tr>
<tr>
    <td>{$rootEventCalendar}</td>
    <td><div class=menuHolderDiv><input type=hidden id=rootEventStartValue><input type=text id=rootEventStart READONLY class=rEventFld><div class=valueDropDown style="width: 6vw;">{$sTime}</div></div></td>
    <td><div class=menuHolderDiv><input type=hidden id=rootEventEndValue><input type=text id=rootEventEnd READONLY class=rEventFld><div class=valueDropDown style="width: 6vw;">{$eTime}</div></div></td>
    <td><div class=menuHolderDiv><input type=hidden id=rootEventtypeValue><input type=text id=rootEventtype READONLY class=rEventFld><div class=valueDropDown style="width: 12vw;">{$eType}</div></div></td>
    <td><input type=text id=rootICInitials class=rEventFld value="" maxlength=2></td>
    <td><input type=text id=rootMDInitials class=rEventFld value="" maxlength=2></td>
</tr>
<tr><td></td><td colspan=2><center><input type=checkbox id=rootAllDayInd onchange="byId('rootEventStartValue').value='';byId('rootEventStart').value='';byId('rootEventEndValue').value='';byId('rootEventEnd').value='';"><label for=rootAllDayInd>All-Day Event</label></td></tr>
<tr><th>Event Title</th><th colspan=5>Event Description</th></tr>
<tr>
  <td><input type=text id=rootEventTitle class=rEventFld value="" maxlength=10></td>
  <td colspan=5><input type=text id=rootEventDesc class=rEventFld value=""></td>  
</tr>
<tr><th colspan=3>Display at</th><td rowspan=2 colspan=3 align=right valign=bottom> 

<table>
<tr>
<td><table class=tblBtn id=btnEventSave style="width: 6vw;" onclick="saveRootEvent();"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table></td>
<td><table class=tblBtn id=btnEventCanel style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td>
</tr>
</table>

</td></tr>
<tr><td colspan=3><div class=menuHolderDiv><input type=hidden id=rootEventInstitutionValue value="ALLINST"><input type=text id=rootEventInstitution READONLY class=rEventFld value="All CHTN Eastern Institutions"><div class=valueDropDown style="width: 21vw;">{$instMnu}</div></div></td></tr> 
</table>

INNNER;
  }

$rtnThis = <<<RTNTHIS
<style>
#divTodaysEventList { width: 25vw; height: 21vh; border: 1px solid rgba(48,57,71,1); overflow: auto; }
#tblTodaysEventList { width: 25vw; }
#tblTodaysEventList tr:nth-child(even) { background: rgba(160,160,160,.5); }      
#tblTodaysEventList tr:hover {cursor: pointer; background: rgba(255,248,225,.8); }
#tblTodaysEventList tr td { padding: 4px; }        
        
#annc { font-size: 1.6vh; color: rgba(48,57,71,1); }         
        
.indIcon { font-size: 2vh;  }         
.delIcon:hover  { color:  rgba(237, 35, 0, 1); }         
        
</style>      
{$inner}
RTNTHIS;

return $rtnThis;    
}

function bldDialogShipDocAddSalesOrder ( $passeddata ) { 
  require(serverkeys . "/sspdo.zck");
  $pdta = json_decode ( $passeddata, true );
  $obj = json_decode ( $pdta['objid'], true );     
  $sd = cryptservice($obj['sdency'], 'd' );
  $dspsd = substr('000000' . $sd, -6);
  $lock = 0;
  
  $getSOSQL = "SELECT shipdocrefid, ifnull(salesorder,'') salesorder, ifnull(salesorderamount,'') as soamount, ifnull(soby,'') as soby, ifnull(date_format(soon,'%m/%d/%Y'),'') as soon FROM masterrecord.ut_shipdoc where shipdocrefid = :sd"; 
  $getSORS = $conn->prepare($getSOSQL); 
  $getSORS->execute(array(':sd' => $sd));
  if ($getSORS->rowCount() <> 1 ) { 
     $lock = 1;
     $inner = "ERROR:  SHIP DOC REFERENCE NOT FOUND.   SEE CHTN INFORMATICS PERSONNEL"; 
  } else { 
      $so = $getSORS->fetch(pdo::FETCH_ASSOC);
      if ( trim($so['salesorder']) === "" ) { 
          //ALLOW EDIT 
          $inner = <<<INNERTBL
                <input type=hidden id=soSDEncy value='{$obj['sdency']}'>
                <input type=hidden id=soDLGId value='{$pdta['dialogid']}'>  
                <table border=0><tr><td id=editinstructions colspan=2>Enter the Sales Order Document for Ship-Doc {$dspsd} below:</td></tr>
                <tr><th>Sales Order #</th><th>Amount</th></tr>
                <tr><td><input type=text id=soSONbr></td><td><input type=text id=soSOAmt></td></tr>
                <tr><td align=right colspan=2><table><tr><td><table class=tblBtn id=btnSDCCancel style="width: 6vw;" onclick="saveSOOverride();"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table></td><td><table class=tblBtn id=btnSDCCancel style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td></tr></table></td></tr>
                </table>
INNERTBL;
          
      } else { 
          //DISPLAY SALES ORDER INFORMATION
          $dspso = substr('000000' . $so['salesorder'],-6);
          $dspsoamt = ((int)$so['soamount'] === 0 ) ? "" : sprintf("$%01.2f", $so['soamount']); //salesorderamount
          $inner = "<table border=0><tr><td colspan=4 id=lockinstr>This Ship-Doc already has a referenced Sales Order.  If this is incorrect, see a CHTN-Informatics Staff Member. Information is outlined below.</td></tr><tr><td class=lockhead>Ship Doc</td><td class=lockhead>Sales Order</td><td class=lockhead>Amount</td><td class=lockhead>Entered by</td><td class=lockhead>Entered On</td></tr>";
          $inner .= "<tr><td class=lockdata>{$dspsd}</td><td class=lockdata>{$dspso}</td><td class=lockdata>{$dspsoamt}</td><td class=lockdata>{$so['soby']}</td><td class=lockdata>{$so['soon']}</td></tr></table>";
      }

  }
  
$rtnThis = <<<RTNTHIS
<style>
 #lockinstr { width: 25vw; text-align: justify; line-height: 1.3em; padding: .8vh 0; }
 .lockhead { font-weight: bold; border-bottom: 1px solid rgba(145,145,145,1); }        
 .lockdata { padding: 0 0 .8vh 0; }      
 #soSONbr { width: 11vw; text-align: right; }
 #soSOAmt { width: 10vw; text-align: right; }
 #editinstructions { width: 21vw; font-size: 1.4vh; padding: 1vh 0; }     
</style>      
{$inner}
RTNTHIS;

return $rtnThis;    
}

function bldDialogShipDocShipOverride ( $passeddata ) { 
  //{"whichdialog":"shipdocshipoverride","objid":"{\"sdency\":\"aEU0c21vMFU5eUtYazJ5aldxUGNBQT09\"}","dialogid":"VDFJp1o0nDfa6r7"}
  require(serverkeys . "/sspdo.zck");
  $pdta = json_decode ( $passeddata, true );
  $obj = json_decode ( $pdta['objid'], true );     
  $sd = cryptservice($obj['sdency'], 'd' );
  $dspsd = substr('000000' . $sd, -6);

$shCalendar = buildcalendar('shipactual'); 
$shpCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=sdcActualShipDateValue value=""><input type=text READONLY id=sdcActualShipDate class="sdinput"></div>
  <div class=valueDropDown id=sdshpcal><div id=sdShpCalendar>{$shCalendar}</div></div>
</div>
CALENDAR;

  $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-menu-ship-doc-actual-ship",serverIdent, serverpw), true);
  $devm = "<table border=0 class=menuDropTbl style=\"width: 15vw;\">";
  foreach ($devarr['DATA'] as $devval) {
    $devm .= "<tr><td onclick=\"fillField('sdcActDeviationReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
  }
  $devm .= "</table>";

  $inner = <<<BLDDIALOG
<input type=hidden id=pdDialogId value="{$pdta['dialogid']}">
<table border=0>
<tr><td colspan=5 style="width: 12vw; padding: 1vh .5vw; text-align: justify; line-height: 1.3em;">This dialog will status ship-doc {$dspsd} as shipped, mark all segments on the ship-doc as being shipped to investigator and close the ship doc and all segments.  This will effectively make the Ship-Doc un-editable!  This is a Standard Operating Procedure (SOP) override.  This action should be performed within the inventory module.</td></tr>  
<tr><td>Actual Ship Date *</td><td>Courier Tracking #</td><td> <div class="ttholder headhead">Deviation Reason * (?)<div class=tt style="width: 25vw;">This is NOT a standard operational screen and should only be used in extenuating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures. Please enter a reason for the deviation in the field provided.</div></div></td><td>User Pin</td></tr>
<tr><td>{$shpCalendar}</td><td><input type=text id=sdcCourierTrack></td><td> <div class=menuHolderDiv><input type=text id=sdcActDeviationReason READONLY style="width: 15vw;"><div class=valueDropDown>{$devm}</div></div> </td><td><input type=password id=fldUsrPIN style="width: 7vw;">  </td></tr>
<tr><td align=right colspan=4>
    <table>
     <tr>
       <td><table class=tblBtn id=btnSDCShip style="width: 6vw;" onclick="sendOverrideShip();"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table></td>
       <td><table class=tblBtn id=btnSDCCancel style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td>
     </tr>
   </table>
</td></tr>
</table>
BLDDIALOG;

$rtnThis = <<<RTNTHIS
<style>
 #sdcActualShipDate { font-size: 1.7vh; width: 6vw; padding: 1.3vh .5vw;} 
 #sdcCourierTrack { font-size: 1.7vh; width: 20vw; }
 .ttholder { position: relative; }
 .ttholder:hover .tt { display: block; }
 .tt { position: absolute; background: rgba(240,240,240,1); color: rgba(0,0,0,,1); padding: 7px 5px; display: none; z-index: 40; text-align: justify; line-height: 1.3em; }
</style>      
{$inner}
RTNTHIS;

return $rtnThis;
}

function bldDialogShipDocAddSegment ( $passeddata ) { 
  require(serverkeys . "/sspdo.zck");
  $pdta = json_decode ( $passeddata, true );
  $obj = json_decode ( $pdta['objid'], true );     
  $sd = cryptservice($obj['sdency'], 'd' );
  $dspsd = substr('000000' . $sd, -6);

  $chkSQL = "SELECT sdstatus, ifnull(investcode,'') as investcode, ifnull(investname,'') as investname  FROM masterrecord.ut_shipdoc where shipdocrefid = :sd";
  $chkRS = $conn->prepare($chkSQL);
  $chkRS->execute(array( ':sd' => $sd ));

  if ( $chkRS->rowCount() <> 1 ) {
    $inner = "<table id=errorDsp><tr><td>ERROR: NO SHIP DOC FOUND ({$dspsd}). SEE CHTNEASTERN INFORMATICS STAFF</td></tr></table>";
  } else { 
    $sddata = $chkRS->fetch(PDO::FETCH_ASSOC);
    if ( strtoupper(trim($sddata['sdstatus'])) === 'CLOSED' ) { 
      $inner = "<table id=errorDsp><tr><td>SHIPMENT DOCUMENT {$dspsd} IS CLOSED/SHIPPED.  YOU MAY NOT MODIFY OR ADD SEGMENTS TO IT.</td></tr></table>";
    } else { 
 
        $iname = trim($sddata['investname']); 
        if ( trim($iname) === "" ) { 
          //GET INVESTIGATOR'S NAME
        }

        //TODO:  Turn into a webservice
        $sgAssListSQL = "SELECT sg.segmentid, replace(sg.bgs,'_','') as bgs, ucase(concat(ifnull(sg.prepmethod,''), if(ifnull(sg.preparation,'')='','',concat(' / ',ifnull(sg.preparation,''))))) as prepdsp, ucase(concat(ifnull(bs.tisstype,''), if(ifnull(bs.anatomicsite,'')='','',concat(' :: ',ifnull(bs.anatomicsite,''))), if(ifnull(bs.diagnosis,'') =  '','',concat(' :: ',ifnull(bs.diagnosis,''))), if(ifnull(bs.subdiagnos,'') = '','',concat(' (',ifnull(bs.subdiagnos,''),')')))) as dx FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample where segstatus = :segstatus and assignedTo = :investcode order by bgs asc";
        $sgAssListRS = $conn->prepare($sgAssListSQL); 
        $sgAssListRS->execute(array(':segstatus' => 'ASSIGNED', ':investcode' => $sddata['investcode'] ));
        if ( $sgAssListRS->rowCount() === 0 ) { 
          //NO SEGMENTS
          $bgsTbl = "<table><tr><td>NO SEGMENTS ARE PRESENTLY ASSIGNED TO INVESTIGATOR {$sddata['investcode']}</td></tr></table>";
        } else { 

            $bgsCnt = 0; 
            $bgsMasterCnt = 0;
            $bgsTbl = "<table border=0><tr>"; 
            while ( $sg = $sgAssListRS->fetch(PDO::FETCH_ASSOC) ) { 
                if ($bgsCnt === 3) { 
                  $bgsTbl .= "</tr><tr>";
                  $bgsCnt = 0;
                }

                   $bgsDsp = "<table border=0><tr><td class=clsBGS>{$sg['bgs']}</td><td class=clsPrp>&nbsp;[{$sg['prepdsp']}]</td></tr><td colspan=2 class=clsDX>{$sg['dx']}</td></tr></table>";
                   $sdency = cryptservice($sd,'e');
                   $sid = cryptservice($sg['segmentid'],'e'); 
                $bgsTbl .= "<td onclick=\"addSegmentToShipDoc('{$sdency}','{$sid}',{$bgsMasterCnt});\" ><div id='sgAssList{$bgsMasterCnt}' class=\"offeredSegCell\">{$bgsDsp}</div></td>";
                $bgsCnt++;
                $bgsMasterCnt++;
            }
            $bgsTbl .= "</tr></table>";
        }
        $inner = <<<INNERDLOG
<table border=0>
<tr><td id=topInstr>Enter a CHTN label # or select from the assigned segments below</td></tr>
<tr><td style="padding: 0 0 0 .9vw;"><table><tr><td><input type=text id=qryBGS></td><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="BGSLookupRqst('{$sdency}');"><tr><td style="font-size: 1.3vh;"><center>Look-up</td></tr></table></td></tr></table></td></tr>
<tr><td><div id=segBGSLookupRslts></div></td></tr>
<tr><td id=segInstr>The segments below are currently assigned to investigator {$sddata['investcode']} ({$iname}).  Click to add to Ship-Doc {$dspsd}. </td></tr>
<tr><td><div id=BGSSegAssignedList>{$bgsTbl}</div></td></tr>
<tr><td align=right><table><tr><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');location.reload(true);"><tr><td style="font-size: 1.3vh;"><center>Refresh Screen</td></tr></table><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td></tr></table></td></tr>
</table>
INNERDLOG;
    } 
  }

$rtnThis = <<<RTNTHIS
<style>
  #errorDsp { font-size: 1.8vh; color: rgba(0,0,0,1);  }
  #errorDsp tr td { text-align: center; width: 25vw; box-sizing: border-box; line-height: 1.8em; padding: 1vh 1vw; }
  #BGSSegAssignedList { max-height: 20vh; overflow: auto; }
  .offeredSegCell { border: 1px solid rgba(145,145,145,1); background: rgba(255,255,255,1); height: 7vh; padding: 7px; box-sizing: border-box; }
  .offeredSegCell:hover { background: rgba(255,248,225,1); cursor: pointer; }
  .clsBGS { font-size: 1.5vh; font-weight: bold; width: 3vw; }
  .clsPrp { }
  .clsDX { font-size: 1.1vh; font-style: italic; }

  #segBGSLookupRslts { height: 8vh; }

  #segInstr { font-size: 1.4vh; padding: 1vh 1vw 0 1vw; text-align: center; width: 38vw; border-top: 1px solid rgba(145,145,145,1);  } 
  #topInstr { font-size: 1.4vh; padding: 2vh 1vw 0 1vw;  width: 38vw;  }
  #qryBGS { font-size: 1.2vh; width: 15vw; }
  .statAss { font-size: 1.1vh; text-align: right;  }
</style>      
{$inner}
RTNTHIS;

return $rtnThis;
}

function bldDialogShipDocPreRemoveSeg ( $passeddata ) { 
    require(serverkeys . "/sspdo.zck");

    //$passeddata = {"whichdialog":"preprocremovesdsegment","objid":"{\"sdency\":\"ZkFBTkJsZUM5SXhVcEtvOURscGUwQT09\",\"segid\":\"449032\",\"dspcell\":\"BJZsPcjs\"}","dialogid":"ByPQjoMeGia4U2u"}
$pdta = json_decode ( $passeddata, true );
$obj = json_decode ( $pdta['objid'], true );     

$sd = cryptservice($obj['sdency'], 'd' );
$dspsd = substr('000000' . $sd, -6);
$segid = $obj['segid']; 
$dspcellid = $obj['dspcell'];

$bgsSQL = "select ucase(replace(ifnull(bgs,''),'_','')) as bgs from masterrecord.ut_procure_segment where segmentid = :sid";
$bgsRS = $conn->prepare($bgsSQL); 
$bgsRS->execute(array(':sid' => $segid));
if ( $bgsRS->rowCount() <> 1 ) { 
   $rtnThis = "ERROR:  SEGMENT NOT FOUND BY ID.  SEE CHTNEASTERN INFORMATICS PERSON";
} else { 
  $bgs = $bgsRS->fetch(PDO::FETCH_ASSOC); 
  //New Segment Statuses Allowed - Assign, Bank, Permanent Collection, X_NFIPI   
  $poarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ship-doc-restock-segment-values",serverIdent,serverpw),true);
  $po = "<table border=0 id=rsdropdown class=menuDropTbl>";
  foreach ($poarr['DATA'] as $poval) { 
    $po .= "<tr><td onclick=\"fillField('pdRestockStatus','{$poval['lookupvalue']}','{$poval['menuvalue']}');\" class=ddMenuItem>{$poval['menuvalue']}</td></tr>";
  }
  $po .= "</table>";
 
  $rarr = json_decode(callrestapi("GET", dataTree . "/global-menu/shipdoc-restock-reasons",serverIdent,serverpw),true);
  $rr = "<table border=0 id=rrdropdown class=menuDropTbl>";
  foreach ($rarr['DATA'] as $rval) { 
    $rr .= "<tr><td onclick=\"fillField('pdRestockReason','{$rval['lookupvalue']}','{$rval['menuvalue']}');\" class=ddMenuItem>{$rval['menuvalue']}</td></tr>";
  }
  $rr .= "</table>";


$reasonTbl = <<<REASONS

<table border=0>
<tr><td>Segment Status *</td><td>Removal Reason *</td></tr>
<tr>
  <td><div class=menuHolderDiv><input type=hidden id=pdRestockStatusValue><input type=text id=pdRestockStatus><div class=valueDropDown>{$po}</div></div></td>
  <td><div class=menuHolderDiv><input type=hidden id=pdRestockReasonValue><input type=text id=pdRestockReason><div class=valueDropDown>{$rr}</div></div></td>
</tr>
<tr><td colspan=2 style="padding: .8vh 0; ">Note</td></tr>
<tr>
  <td colspan=2><input type=text id=pdRestockNote></td>
</tr>
</table>
REASONS;

$btnTbl = <<<BTTNS
<table><tr><td><table class=tblBtn id=btnConfirmRemoval style="width: 6vw;" onclick="sendRemovalCmd();"><tr><td style="font-size: 1.3vh;"><center>Confirm</td></tr></table></td><td><table class=tblBtn id=btnCancelRemoval style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td></tr></table>
BTTNS;

$rtnThis = <<<RTNTHIS
<style>

#delHolderTbl { width: 20vw; }
#instructionCell { font-size: 1.3vh; padding: 1vh .5vw; width: 20vw; box-sizing: border-box; line-height: 1.8em; text-align: justify; }
#pdRestockStatus { width: 15vw; font-size: 1.2vh; }
#rsdropdown { min-width: 15vw; }
#pdRestockReason { width: 18vw; font-size: 1.2vh; }
#rrdropdown { min-width: 18vw; }
#pdRestockNote { width: 34vw; font-size: 1.2vh; box-sizing: border-box; }

</style>      
<table border=0 cellspacing=0 cellpadding=0 id=delHolderTbl>
    <tr><td id=instructionCell>This action will remove segment <b>&laquo;{$bgs['bgs']}&raquo;</b> from <b>&laquo;{$dspsd}&raquo;</b>.  You must re-status the segment and state a reason for removal from the shipment document.<input type=hidden id=pdSegId value={$segid}><input type=hidden id=pdDspCell value="{$dspcellid}"><input type=hidden id=pdSDEncy value="{$obj['sdency']}"><input type=hidden id=pdDialogId value="{$pdta['dialogid']}"></td></tr>
    <tr><td style="padding: 0 .5vw;">{$reasonTbl}</td></tr>
    <tr><td align=right>{$btnTbl}</td></tr>
</table>
        
RTNTHIS;
}

return $rtnThis;
}

function bldDialogMasterQMSAction( $passeddata ) { 

    $pdta = json_decode( $passeddata, true);    
    $bg = cryptservice( $pdta['objid'] , 'd', false ); 
    $errorInd = 0;
    session_start(); 
    $sess = session_id();
    require(serverkeys . "/sspdo.zck");
    $rtnThis = "ERROR!";
    session_start();      
    $sessid = session_id();
    $chkUsrSQL = "SELECT originalaccountname, presentinstitution, inst.dspvalue as institutionname FROM four.sys_userbase usr left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') inst on usr.presentinstitution = inst.menuvalue where 1=1 and sessionid = :sessid and (allowInd = 1 and allowCoord = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
    $rs = $conn->prepare($chkUsrSQL); 
    $rs->execute(array(':sessid' => $sessid));
    if ($rs->rowCount() === 1) { 
      $usrrecord = $rs->fetch(PDO::FETCH_ASSOC);
    } else { 
      (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
    }

    if ($errorInd !== 0 ) { 
      $rtnThis .= json_encode($msgArr);
    } else { 
    $pdta['bglookup'] = $bg;
    $payload = json_encode($pdta);
    $bgqmsdta = json_decode(callrestapi("POST", dataTree . "/data-doers/get-bg-qmsstat",serverIdent, serverpw, $payload), true);
    
    if ((int)$bgqmsdta['ITEMSFOUND'] === 0 ) { 
    } else {
        $qmsstatus = json_decode(callrestapi("GET", dataTree . "/globalmenu/qms-assignable-status",serverIdent,serverpw), true);
        foreach ($bgqmsdta['DATA'] as $key => $value) { 
            if ( strtoupper(trim($value['qcprocstatus'])) === 'Q' ) { 
                //QMS COMPLETE
                $inner = "<table border=0>";
                $inner .= " <tr><td style=\"padding: 1vh 1vw 1vh 1vw; \">{$value['readlabel']} HAS A STATUS OF QMS COMPLETE - NO FURTHER ACTION MAY BE TAKEN.</td><td style=\"padding: 1vh 1vw 1vh 1vw; \">Statused On/By: {$value['qcmarkbyon']}</td></tr>";
            } else { 
                //DROP DOWNS
                $idsuffix = generateRandomString(8);
                //qms actions
                $met = "<table border=0 class=menuDropTbl>";
                foreach ($qmsstatus['DATA'] as $metval) {
                  $met .= "<tr><td onclick=\"fillField('fldQMSStat{$idsuffix}','{$metval['lookupvalue']}','{$metval['menuvalue']}');revealFurtherQMSActions('{$metval['lookupvalue']}','{$idsuffix}');\" class=ddMenuItem>{$metval['menuvalue']}</td></tr>";
                }
                $met .= "</table>";                

$readlabel = preg_replace('/_/','',$value['readlabel']);
$labAction = bldDialogMasterQMSActionLabAction();                
$tumorAction = bldDialogMasterQMSActionQMS();
//$hprAction = bldDialogMasterQMSActionHPR($bg, $readlabel );

$inner = <<<TBLONE
<table border=0 cellspacing=0 cellpadding=0>
   <tr>
     <th class=topTHCell>BG #</th>
     <th class=topTHCell>Present QMS Status</th>
     <th class=topTHCell>HPR Decision</th>
     <th class=topTHCell>QMS Process Status</th>
     <th colspan=2 class=topTHCell>Further Information</th>
</tr>
TBLONE;
$inner .= <<<TBLTWO
 <tr>
   <td valign=top class=topDataCell style="width: 3vw;">{$readlabel}&nbsp;</td>
   <td valign=top class=topDataCell style="width: 12vw;">{$value['qcprocstatusdsp']}&nbsp;</td>
   <td valign=top class=topDataCell style="width: 8vw;">{$value['hprdecision']}&nbsp;</td>
   <td valign=top class=topDataCell>
     <div class=menuHolderDiv>
       <input type=hidden id=fldQMSStat{$idsuffix}Value>
       <input type=text id=fldQMSStat{$idsuffix} READONLY class="inputFld qmsDivCls" style="width: 8vw;">
         <div class=valueDropDown style="min-width: 8vw;">{$met}</div></div>
   </td>
   <td valign=top style="width: 40vw;">
     <div id="labdiv{$idsuffix}" style="display: none;">{$labAction}</div>
     <div id="tumdiv{$idsuffix}" style="display: none;">{$tumorAction}</div>
     <div id="hprdiv{$idsuffix}" style="display: none;">{$hprAction}</div>
   </td>
   <td valign=top><table class=tblBtn  onclick="saveQMSAction('{$idsuffix}','{$readlabel}');"><tr><td><i class="material-icons">add_circle_outline</i></td></tr></table></td>
 </tr>
TBLTWO;
            }
        }
        $inner .= "</table>";
        
        
$rtnThis = <<<RTNTHIS
<style>

.topTHCell { background: rgba(160,160,160,1); text-align: left; padding: 8px; font-size: 1.3vh; border-right: 1px solid #fff; }
.topDataCell { border-bottom: 1px solid rgba(160,160,160,1);border-right: 1px solid rgba(160,160,160,1); font-size: 1.3vh; padding: 8px; }
.faHead { text-align: left; font-size: 1.1vh; border-bottom: 1px solid rgba(160,160,160,1); padding: 0 8px 0 4px; }

#labdiv { display: none; } 
#tumdiv { display: none; }       

.dspDefinedMoleTests { border: 1px solid rgba(160,160,160,1); width: 25.2vw; height: 10.4vh; overflow: auto;   padding: 4px; box-sizing: border-box; }
.qmsNotes { width: 36vw; height: 8vh; box-sizing: border-box; }

</style>
        
<table border=0 cellspacing=0 cellpadding=0>
    <tr><td>{$inner}</td></tr>
</table>
        
RTNTHIS;
    }
}

return $rtnThis;
}

function bldShipDocLookup() {  
    
    $thisPage = <<<THISPAGE
            <table border=0>
            <tr><td colspan=2 style="padding: 1vh .5vw 0 .5vw;">Shipment Document #</td></tr>
            <tr><td style="padding: 0 .5vw;"><input type=text id=qryShipDoc></td><td><table class=tblBtn id=btnLookup style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Lookup</td></tr></table></td><td></td></tr>
            <tr><td colspan=3 id=resultTblHolderCell><div id=displayLookedupData></div></td></tr>
            </table>
            
THISPAGE;
    return $thisPage;
}

function bldShipDocEditPage( $whichsdency ) { 

 $pdta = array(); 
 $pdta['sdency'] = $whichsdency; 
 $payload = json_encode($pdta);
 $sd = json_decode(callrestapi("POST", dataTree . "/data-doers/shipment-document-data",serverIdent, serverpw, $payload), true); 
 if ( (int)$sd['ITEMSFOUND'] > 0 ) {
   $sdnbrency = cryptservice($sd['DATA']['sdhead'][0]['shipdocrefid'], 'e');  
   $sdnbr = substr('000000' . $sd['DATA']['sdhead'][0]['shipdocrefid'],-6);
   $sdstatus =  $sd['DATA']['sdhead'][0]['sdstatus']; // NEW // OPEN // LOCKED-PULLED // CLOSED-SHIPPED
   //TODO: MAKE THIS DYNAMIC
   switch ( $sdstatus ) { 
     case 'NEW':
         $topcellclass = "sdnew";
         break;
     case 'OPEN':
         $topcellclass = "sdopen";
         break;
     case 'LOCKED':
         $topcellclass = "sdlocked";
         break;
     case 'CLOSED':
         $topcellclass = "sdclosed";
         break;
   }
   $sdstatusdate = $sd['DATA']['sdhead'][0]['statusdate'];
   $sdacceptedby = $sd['DATA']['sdhead'][0]['acceptedby'];
   $sdacceptedbyemail = $sd['DATA']['sdhead'][0]['acceptedbyemail'];
   $sdponbr = $sd['DATA']['sdhead'][0]['ponbr'];
   $rqstshipdate = $sd['DATA']['sdhead'][0]['rqstshipdate'];
   $rqstshipdateval = $sd['DATA']['sdhead'][0]['rqstshipdateval'];
   $rqstpulldate = $sd['DATA']['sdhead'][0]['rqstpulldate'];
   $rqstpulldateval = $sd['DATA']['sdhead'][0]['rqstpulldateval'];
   $sdsetupon = $sd['DATA']['sdhead'][0]['setupon'];
   $sdsetupby = $sd['DATA']['sdhead'][0]['setupby'];
   $sdsonbr = ( (int)$sd['DATA']['sdhead'][0]['salesorder'] === 0 ) ? "" : substr('000000' .  (int)$sd['DATA']['sdhead'][0]['salesorder'], -6);
   $sdsoamt = ( $sd['DATA']['sdhead'][0]['salesorderamount'] === 0 ) ? "" : sprintf("$%01.2f", $sd['DATA']['sdhead'][0]['salesorderamount']); //salesorderamount
   $sdsoon =  $sd['DATA']['sdhead'][0]['salesorderon'];
   $sdsoby =  $sd['DATA']['sdhead'][0]['salesorderby'];
   $sdshiptrck = $sd['DATA']['sdhead'][0]['shipmenttrackingnbr'];
   $sdcomments = $sd['DATA']['sdhead'][0]['comments'];
   $sdinvcode =  $sd['DATA']['sdhead'][0]['investcode'];
   $sdiname =  $sd['DATA']['sdhead'][0]['investname']; //IF BLANK - HISTORIC DATA
   $sdiemail =  $sd['DATA']['sdhead'][0]['investemail']; //IF BLANK - HISTORIC DATA
   $sdidiv =  $sd['DATA']['sdhead'][0]['investdivision']; //IF BLANK - HISTORIC DATA
   $sdiinstitution =  $sd['DATA']['sdhead'][0]['investinstitution']; //IF BLANK - HISTORIC DATA
   $sdiinstittype =  $sd['DATA']['sdhead'][0]['institutiontype']; //IF BLANK - HISTORIC DATA
   $sditqstatus =  $sd['DATA']['sdhead'][0]['tqstatusoncreation']; //IF BLANK - HISTORIC DATA
   $sdshpadd =  $sd['DATA']['sdhead'][0]['shipmentaddress'];
   $sdshpphn =  $sd['DATA']['sdhead'][0]['shipmentphone'];
   $sdbladd =  $sd['DATA']['sdhead'][0]['billaddress'];
   $sdblphn =  $sd['DATA']['sdhead'][0]['billphone'];

   $sdcourier =  $sd['DATA']['sdhead'][0]['courier'];
   $sdcouriernbr =  $sd['DATA']['sdhead'][0]['couriernbr'];
   $valcourier = ( trim($sdcourier) !== "" ) ? trim($sdcourier) : "";
   $valcourier .= ( trim($sdcouriernbr) !== "" ) ? ( trim($valcourier) !== "" ) ? " :: {$sdcouriernbr}" : "{$sdcouriernbr}" : "";
   $tqcourierid =  ( (int)$sd['DATA']['sdhead'][0]['tqcourierid'] <> 0 ) ? (int)$sd['DATA']['sdhead'][0]['tqcourierid'] : "";

   $idta = json_decode(callrestapi("GET", dataTree . "/investigator-head/{$sdinvcode}", serverIdent, serverpw),true);
   $cdrop = "<table border=0 class=menuDropTbl>";
   if ( count($idta['DATA']['courier']) > 0 ) {
     foreach ( $idta['DATA']['courier'] as $cval) { 
       $courierDsp =  $cval['courier'];
       $courierDsp .= ( trim($cval['couriernbr']) !== "" ) ? " :: " . trim($cval['couriernbr']) : "";
       $courierDsp .= ( trim($cval['couriercmt']) !== "" ) ? "  (" . trim($cval['couriercmt']) . ")" : "";
       $mcourierDsp =  $cval['courier'];
       $mcourierDsp .= ( trim($cval['couriernbr']) !== "" ) ? " :: " . trim($cval['couriernbr']) : "";
       $cdrop .= "<tr><td onclick=\"fillField('sdcCourierInfo','{$cval['courierid']}','{$mcourierDsp}');\" class=ddMenuItem>{$courierDsp}</td></tr>";
     }
   } else { 
     $cdrop .= "<tr><td>THERE IS NO COURIER INFORMATION LISTED FOR INVESTIGATOR {$sdinvcode}</td></tr>";
   }
   $cdrop .= "</table>";
   $couriermnu = "<table cellpadding=0 cellspacing=0 border=0><tr><td><div class=menuHolderDiv><input type=hidden id=sdcCourierInfoValue value=\"{$tqcourierid}\" data-frminclude=1><input type=text id=sdcCourierInfo class=sdinput value=\"{$valcourier}\" READONLY><div class=valueDropDown style=\"min-width: 50vw;\">{$cdrop}</div></div></td></tr></table>";

   $shpTbl = <<<SHTBL
<table><tr><td class=sdFieldLabel>Shipping Address *</td></tr><tr><td><TEXTAREA class=sdinput id=sdcInvestShippingAddress data-frminclude=1>{$sdshpadd}</TEXTAREA></td></tr><tr><td class=sdFieldLabel>Shipping Phone * (format: '(123) 456-7890 x0000' / x is optional)</td></tr><tr><td><input type=text class=sdinput id=sdcShippingPhone value="{$sdshpphn}" data-frminclude=1></td><tr><td class=sdFieldLabel>Courier</td></tr><tr><td>{$couriermnu}</td></tr></table>
SHTBL;

   $bilTbl = <<<SHTBL
<table><tr><td class=sdFieldLabel>Billing Address *</td></tr><tr><td><TEXTAREA class=sdinput id=sdcInvestBillingAddress data-frminclude=1>{$sdbladd}</TEXTAREA></td></tr><tr><td class=sdFieldLabel>Billing Phone * (format: '(123) 456-7890 x0000' / x is optional)</td></tr><tr><td><input type=text class=sdinput id=sdcBillPhone value="{$sdblphn}" data-frminclude=1></td></tr></table>
SHTBL;

$shCalendar = buildcalendar('shipSDCFrom'); 
$shpCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=sdcRqstShipDateValue value="{$rqstshipdateval}" data-frminclude=1><input type=text READONLY id=sdcRqstShipDate class="sdinput" value="{$rqstshipdate}"></div>
  <div class=valueDropDown id=sdshpcal><div id=rShpCalendar>{$shCalendar}</div></div>
</div>
CALENDAR;

$lbCalendar = buildcalendar('shipSDCToLab'); 
$labCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=sdcRqstToLabDateValue value="{$rqstpulldateval}" data-frminclude=1><input type=text READONLY id=sdcRqstToLabDate class="sdinput" value="{$rqstpulldate}"></div>
  <div class=valueDropDown id=tolabcal><div id=rToLabCalendar>{$lbCalendar}</div></div>
</div>
CALENDAR;

//GET PO ALLOW VALUES
          $poarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ship-doc-po-values",serverIdent,serverpw),true);
          $po = "<table border=0 class=menuDropTbl>";
          foreach ($poarr['DATA'] as $poval) { 
            $po .= "<tr><td onclick=\"fillField('sdcPurchaseOrder','{$poval['lookupvalue']}','{$poval['menuvalue']}');\" class=ddMenuItem>{$poval['menuvalue']}</td></tr>";
          }
          $po .= "</table>";

$sdsetup = ( trim($sdsetupon) !== "" || trim($sdsetupby) !== "") ? ( trim($sdsetupby) !== "" ) ?  "{$sdsetupon} :: {$sdsetupby}" : "{$sdsetupon}" : "";

//   $sdheadtbl = json_encode($sd['DATA']['sdhead'][0]);
   $sdheadtbl .= <<<SDHDR
 <form id=frmSDHeadSection><input type=hidden id=sdency value="{$whichsdency}" data-frminclude=1><input type=hidden id=sdnbrency value="{$sdnbrency}">                    
<table border=0>
    <tr><td class=sdFieldLabel>Shipdoc #</td>
        <td class=sdFieldLabel>Requested Ship Date *</td>
        <td class=sdFieldLabel>Date to Pull *</td>
        <td class=sdFieldLabel>Accepted By *</td>
        <td class=sdFieldLabel>Acceptor's Email *</td>
        <td class=sdFieldLabel>Setup On :: By</td>
        <td class=sdFieldLabel>Ship Tracking #</td>
        <td class=sdFieldLabel>Purchase Order # *</td>
        <td class=sdFieldLabel>Sales Order # </td>
        <td class=sdFieldLabel>Amount </td>
    <tr>
            <td id=sdnbrdsp>{$sdnbr}</td>
            <td>{$shpCalendar}</td>
            <td>{$labCalendar}</td>
            <td><input type=text id=sdcAcceptedBy class=sdinput value="{$sdacceptedby}" data-frminclude=1></td>
            <td><input type=text id=sdcAcceptorsEmail class=sdinput value="{$sdacceptedbyemail}" data-frminclude=1></td>
            <td><input type=text id=sdsetupdsp class=sdinput value="{$sdsetup}" READONLY></td>
            <td><input type=text id=sdtrack class=sdinput value="{$sdshiptrck}" READONLY></td>
            <td><div class=menuHolderDiv><input type=text id=sdcPurchaseOrder class=sdinput value="{$sdponbr}" data-frminclude=1><div class=valueDropDown style="min-width: 20vw;">{$po}</div></div></td>
            <td><input type=text id=sdcShipDocSalesOrder READONLY class=sdinput value="{$sdsonbr}" data-frminclude=0></td> 
            <td><input type=text id=sdcShipDocSalesOrderAmt READONLY class=sdinput value="{$sdsoamt}" data-frminclude=0></td> 
    </tr>
</table>

<table>
<tr><td style="height: 2vh;"></td></tr>
<tr>
  <td class=sdFieldLabel>Invest #</td>
  <td class=sdFieldLabel>Invest Name</td>
  <td class=sdFieldLabel>Invest Email</td>
  <td class=sdFieldLabel>Institution</td>
  <td class=sdFieldLabel>Institution Type</td>
  <td class=sdFieldLabel>Primary Division</td>
  <td class=sdFieldLabel>TQ Status at Setup</td>
</tr>
<tr>
  <td id=sdinvcodedsp>{$sdinvcode}</td>
  <td><input type=text id=sdcIName class=sdinput value="{$sdiname}" READONLY></td>
  <td><input type=text id=sdcIEmail class=sdinput value="{$sdiemail}" READONLY></td>
  <td><input type=text id=sdcInstitution class=sdinput value="{$sdiinstitution}" READONLY></td>
  <td><input type=text id=sdcInstitutioniType class=sdinput value="{$sdiinstittype}" READONLY></td>
  <td><input type=text id=sdcIDivision class=sdinput value="{$sdidiv}" READONLY></td>
  <td><input type=text id=sdcTQStatus class=sdinput value="{$sditqstatus}" READONLY></td>
</tr>
</table>
<table>
<tr><td style="height: 2vh;"></td></tr>
  <tr><td valign=top>
   {$shpTbl}
  </td><td valign=top> 
   {$bilTbl}
  </td> 
  <td valign=top>
    <table><tr><td class=sdFieldLabel>Public Comments</td></tr><tr><td><textarea class=sdinput id=sdcPublicComments data-frminclude=1>{$sdcomments}</TEXTAREA></td></tr></table>
  </td>
</tr>
</table></form>
SDHDR;

 //{"DATA":{"sdhead":[]
 //,"sddetail":[{"shipdocDetId":64068,"pulledon":"","pulledby":inventorylocation":"Walk-In Cooler :: FFPE 02 (CB 793) :: SHELF 1 (SH-823) :: FFPE BIN (BN-829) [STORAGE CONTAINER]"},{"shipdocDetId":64069,"shipdocrefId":5435,"segmentid":449032,"addtosdon":"04\/22\/2019","addtosdby":"proczack","pulledon":"","pulledby":"","bgs":"87106T004","dxdesig":"MALIGNANT :: THYROID :: CARCINOMA (FOLLICULAR)","metric":"","preparation":"SLIDE \/ H&E Slide","qty":1,"inventorylocation":"OVERRIDE CHECKIN PROCESS"},{"shipdocDetId":64070,"shipdocrefId":5435,"segmentid":449033,"addtosdon":"04\/22\/2019","addtosdby":"proczack","pulledon":"","pulledby":"","bgs":"87106T005","dxdesig":"MALIGNANT :: THYROID :: CARCINOMA (FOLLICULAR)","metric":"","preparation":"SLIDE \/ H&E Slide","qty":1,"inventorylocation":"OVERRIDE CHECKIN PROCESS"},{"shipdocDetId":64071,"shipdocrefId":5435,"segmentid":449034,"addtosdon":"04\/22\/2019","addtosdby":"proczack","pulledon":"","pulledby":"","bgs":"87106T006","dxdesig":"MALIGNANT :: THYROID :: CARCINOMA (FOLLICULAR)","metric":"","preparation":"SLIDE \/ H&E Slide","qty":1,"inventorylocation":"OVERRIDE CHECKIN PROCESS"}]}} 

//TURN THIS INTO A DIV TABLE
$detailSegmentTbl = "<div class=rtTable><div class=rTableRow>";
$cellRowCntr = 0;
foreach ( $sd['DATA']['sddetail'] as $dkey => $dval ) { 
  if ( $cellRowCntr === 4 ) { 
    $cellRowCntr = 0; 
    $detailSegmentTbl .= "</div><div class=rTableRow>";
  }

  $cellident = generateRandomString(8);
  $prpdsp = ( trim($dval['qty']) !== "" ) ? "[Qty: {$dval['qty']}]" : "" ;
  $prpdsp .= ( trim($dval['metric']) !== "" ) ? " :: {$dval['metric']}" : "";
  $prpdsp .= ( trim($dval['preparation']) !== "" ) ? ( trim($prpdsp) !== "" ) ? " :: {$dval['preparation']}" : "{$dval['preparation']}" : "";
  $prpdsp = trim($prpdsp);
  $atloc = ( trim($dval['inventorylocation']) !== "" ) ? "{$dval['inventorylocation']}" : ""; 
  $plld = ( (int)$dval['pulledind'] === 1) ? "<tr><td class=pulledyes>Pulled: {$dval['pulledon']}</td></tr>": "<tr><td class=pulledno>&nbsp;</td></tr>"; 
  
  $infoTbl = <<<INFOTBL
<table border=0 class=infoHolderSideTbl>
<tr><td colspan=2 class=segbgsdsp>{$dval['bgs']}</td></tr>
<tr><td colspan=2 class=segdxdesig valign=top>{$dval['dxdesig']}</td></tr>
<tr><td class=segprpdsp>{$prpdsp}</td></tr>
<tr><td class=seginvdsp>{$atloc}</td></tr>
{$plld}
</table>
INFOTBL;

switch ( $sdstatus ) {
     case 'NEW':
         //ALLOW DELBTN
         $delbtn = "<i class=\"material-icons action-icon\" onclick=\"removeBGSfromSD('{$dval['segmentid']}','{$cellident}');\">remove_circle_outline</i>";
         break;
     case 'OPEN':
         //ALLOW DELBTN
         $delbtn = "<i class=\"material-icons action-icon\" onclick=\"removeBGSfromSD('{$dval['segmentid']}','{$cellident}');\">remove_circle_outline</i>";
         break;
     case 'LOCKED':
         //ALLOW ONLY IF NOT PULLED       
         $delbtn =  ( (int)$dval['pulledind'] === 0 )  ?  "<i class=\"material-icons action-icon\" onclick=\"removeBGSfromSD('{$dval['segmentid']}','{$cellident}');\">remove_circle_outline</i>" : "<i class=\"material-icons\">vpn_lock</i>";
         break;
     case 'CLOSED':
         //DON'T ALLOW
         $delbtn = "<i class=\"material-icons\">vpn_lock</i>";
         break;    
}

//INNER DISPLAY TABLE 
$innerDet = <<<INNDETTBL
<table border=0 class=dualHoldTable>
    <tr><td class=delbtnholder valign=top>{$delbtn}</td><td>{$infoTbl}</td></tr>
</table>
INNDETTBL;

  $detailSegmentTbl .= "<div id=\"cell{$cellident}\" class=\"segmentInfoHolder rTableCell\">{$innerDet}</div>";
  $cellRowCntr++;
}
$detailSegmentTbl .= "</div></div>";




$sdPage = <<<SDPAGE
<table border=0 id=mainShipDocHoldTable>
  <tr><td id=banner class={$topcellclass} colspan=2><table border=0 width=100%><tr><td id=sdnbrdsp>{$sdstatus}</td></tr></table></td></tr>
  <tr><td style="height: 1vh;"></td></tr>
  <tr><td><div id=shipdocheaderdiv>{$sdheadtbl}</div></td></tr>
  <tr><td style="height: 2vh;"></td></tr>
  <tr><td id=segDemarkation>SEGMENT DETAILS</td></tr>
  <tr><td style="height: 2vh;"></td></tr>
  <tr><td> {$detailSegmentTbl} </td></tr> 
</table>
SDPAGE;




 } else { 
     foreach ($sd['MESSAGE'] as $value) { 
       $errorList .= "<br><h3>{$value}</h3>";
     }
$sdPage = <<<SDPAGE
<table border=1 id=mainShipDocHoldTable>
  <tr><td>{$errorList}</td></tr>
</table>
SDPAGE;

 }



    return $sdPage;
}

function bldDialogMasterQMSActionHPR ($biogroup, $readlabel) { 
    
    //<td class=faHead>Technician Accuracy</td> - No Longer Relevant on this screen
    
    $hprAction = <<<HPRACTION

   <table border=0>
        <tr><td colspan=2>Histo-Pathologic Review</td></tr>
        <tr><td class=faHead>Decision</td></tr>
        <tr><td>{$biogroup} / {$readlabel}</td></tr>   
    </table>
        
HPRACTION;
    return $hprAction;
    
}

function bldDialogMasterQMSActionQMS () {
    $idsuffix = generateRandomString(8);
    $moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);
    
    //molecular test
    $molemnu = "<table border=0 width=100%><tr><td align=right onclick=\"triggerMolecularFill(0,'','','{$idsuffix}');\" class=ddMenuClearOption>[clear]</td></tr>";
    foreach ($moletest['DATA'] as $moleval) { 
        $molemnu .= "<tr><td onclick=\"triggerMolecularFill({$moleval['menuid']},'{$moleval['menuvalue']}','{$moleval['dspvalue']}','{$idsuffix}');\" class=ddMenuItem>{$moleval['dspvalue']}</td></tr>";
    }
    $molemnu .= "</table>";
    
    $tumorAction = <<<TUMACTTBL
<table border=0>
  <tr><th colspan=2>Percentages</th><th>Indicated Immuno/Molecular Test Results</th></tr>
  <tr>
    <td class=faHead>Tumor</td>
    <td class=faHead>Cellularity</td>
    <td rowspan=8 valign=top>
      <table border=0 width=100%>
       <tr><td class=faHead colspan=2>Indicated Immuno/Molecular Test Results</td><td rowspan=4 valign=top><table class=tblBtn onclick="manageMoleTest(1,'','{$idsuffix}');"><tr><td><i class="material-icons">playlist_add</i></td></tr></table></td></tr>
       <tr><td class=fieldHolder valign=top colspan=2>
                    <div class=menuHolderDiv>
                      <input type=hidden id=hprFldMoleTest{$idsuffix}Value>
                      <input type=text id=hprFldMoleTest{$idsuffix} READONLY style="width: 25vw;">
                      <div class=valueDropDown style="min-width: 25vw;">{$molemnu}</div>
                    </div>
            </td>
       </tr>
       <tr><td class=faHead>Result Index</td><td class=faHead>Scale Degree</td></tr>
       <tr><td class=fieldHolder valign=top>
             <div class=menuHolderDiv>
               <input type=hidden id='hprFldMoleResult{$idsuffix}Value'>
               <input type=text id='hprFldMoleResult{$idsuffix}' READONLY style="width: 12.5vw;">
               <div class=valueDropDown id=moleResultDropDown style="min-width: 12.5vw;"> </div>
             </div>
            </td>
            <td class=fieldHolder valign=top>
              <input type=text id=hprFldMoleScale{$idsuffix} style="width: 12.5vw;">
            </td>
       </tr>
       <tr><td colspan=3 valign=top>
           <input type=hidden id=hprMolecularTestJsonHolderConfirm{$idsuffix}>
           <div id=dspDefinedMolecularTestsConfirm{$idsuffix} class=dspDefinedMoleTests>
           </div>
           </td>
       </tr>
      </table>
    </td></tr>
  <tr>
    <td><input type=text id=fldTmrTumor{$idsuffix} class=prcFld></td>
    <td><input type=text id=fldTmrCell{$idsuffix} class=prcFld></td>
</tr>
<tr>
    <td class=faHead>Necrosis</td>
    <td class=faHead>Acell Mucin</td>
</tr>
<tr>
    <td><input type=text id=fldTmrNecros{$idsuffix} class=prcFld></td>
    <td><input type=text id=fldTmrACell{$idsuffix} class=prcFld></td>
</tr>
<tr>
    <td class=faHead>Neo-Plastic Stroma</td>
    <td class=faHead>Non-Neo Stroma</td>
</tr>
<tr>
    <td><input type=text id=fldTmrNeoPlas{$idsuffix} class=prcFld></td>
    <td><input type=text id=fldTmrNonNeo{$idsuffix} class=prcFld></td>
</tr>
<tr>
    <td class=faHead>Epipthelial</td>
    <td class=faHead>Inflammation</td>
</tr>
<tr>
    <td><input type=text id=fldTmrEpip{$idsuffix} class=prcFld></td>
    <td><input type=text id=fldTmrInFlam{$idsuffix} class=prcFld></td>
</tr>
<tr><td colspan=3 class=faHead>Notes</td></tr>
<tr><td colspan=3><TEXTAREA class=qmsNotes id='qmsNotes{$idsuffix}'></TEXTAREA></td></tr> 
</table>
TUMACTTBL;

return $tumorAction;
    
}

function bldDialogMasterQMSActionLabAction ( ) { 
    $idsuffix = generateRandomString(8);
    $qmsstatusla = json_decode(callrestapi("GET", dataTree . "/globalmenu/qms-lab-actions",serverIdent,serverpw), true);
    //lab Action Menu 
    $la = "<table border=0 class=menuDropTbl>";
    foreach ($qmsstatusla['DATA'] as $laval) {
        $la .= "<tr><td onclick=\"fillField('fldQMSLA{$idsuffix}','{$laval['lookupvalue']}','{$laval['menuvalue']}');\" class=ddMenuItem>{$laval['menuvalue']}</td></tr>";
    }
    $la .= "</table>"; 
$labAction = <<<LATBL
<table border=0>
  <tr>
    <th class=faHead>Lab Action to Perform</th>
    <th class=faHead>Note</th>
  </tr>
  <tr>
    <td>
      <div class=menuHolderDiv>
        <input type=hidden id=fldQMSLA{$idsuffix}Value>
        <input type=text id=fldQMSLA{$idsuffix} READONLY class="inputFld qmsDivCls" style="width: 15vw;">
        <div class=valueDropDown style="min-width: 10vw;">{$la}</div>
      </div>
    </td>
    <td>
      <input type=text id="fldLabActNote{$idsuffix}" style="width: 23vw;">
    </td>
  </tr>
</table>
LATBL;
      return $labAction;
    
}

function bldDialogMasterAddSegment ( $passeddata ) { 

    $pdta = json_decode( $passeddata, true);
    $bg = cryptservice( $pdta['objid'] , 'd', false );    
    $errorInd = 0;
    session_start(); 
    $sess = session_id();
    require(serverkeys . "/sspdo.zck");
    $rtnThis = "ERROR!";
    session_start();      
    $sessid = session_id();
    $chkUsrSQL = "SELECT originalaccountname, presentinstitution, inst.dspvalue as institutionname FROM four.sys_userbase usr left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION') inst on usr.presentinstitution = inst.menuvalue where 1=1 and sessionid = :sessid and (allowInd = 1 and allowCoord = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
    $rs = $conn->prepare($chkUsrSQL); 
    $rs->execute(array(':sessid' => $sessid));
    if ($rs->rowCount() === 1) { 
      $usrrecord = $rs->fetch(PDO::FETCH_ASSOC);
    } else { 
      (list( $errorInd, $msgArr[] ) = array(1 , "SPECIFIED USER INVALID.  LOGOUT AND BACK INTO SCIENCESERVER AND TRY AGAIN OR SEE A CHTNEASTERN INFORMATICS STAFF MEMEBER."));
    }

    //TODO: TURN INTO A WEBSERVICE
    $dxdSQL = "select concat(ucase(ifnull(anatomicSite,'')), if(ifnull(tisstype,'') ='','',concat(' :: ',ifnull(tisstype,''),'')), concat(' ',ifnull(diagnosis,''), if(ifnull(subdiagnos,'')='','',concat(' / ',ifnull(subdiagnos,'')))), if(ifnull(metssite,'')='','', concat(' [',ifnull(metssite,''),']'))) as dxd from masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
    $dxdRS = $conn->prepare($dxdSQL); 
    $dxdRS->execute(array(':pbiosample' => $bg));
    $dxdDesig = $dxdRS->fetch(PDO::FETCH_ASSOC);
    $dxd = "[{$bg}] " . $dxdDesig['dxd'];
 
    $segListSQL = "SELECT substr(bgs,1,6) as pbiosamplelabel, ifnull(prepmethod,'') as prepmethod, ifnull(preparation,'') as preparation, ifnull(slidegroupid,'') as slidegroup, ifnull(assignedto,'') as assignedto, if(min(SegmentLabel) = max(SegmentLabel), min(SegmentLabel), concat(min(SegmentLabel),'-', max(SegmentLabel))) as segrange FROM masterrecord.ut_procure_segment where biosamplelabel = :bg group by substr(bgs,1,6), prepmethod, preparation, slidegroupid, assignedto order by segrange";
    $segListRS = $conn->prepare($segListSQL); 
    $segListRS->execute(array(':bg' => $bg));
    $segTbl = "<table><tr><td class=prcFldLbl>Cut From <span class=reqInd>*</span></td></tr><tr><td><input type=text id=fldParentSegment READONLY></td></tr><tr><td align=right><input type=checkbox id=fldNoParentIndicator onchange=\"byId('fldParentSegment').value = '';\"><label for=fldNoParentIndicator>No Parent</label></td></tr><tr><td><div id=divSegmentDisplayLister><table id=tblSegmentLister cellspacing=0 cellpadding=0><thead><tr><th>Segment #</th><th>Preparation</th></tr></thead></tbody>";
    while ($r = $segListRS->fetch(PDO::FETCH_ASSOC)) {
        if ( strtoupper(trim($r['prepmethod'])) !== 'SLIDE' ) {
            //CUT ALLOWED
            $clicker = "fillField('fldParentSegment','','{$r['pbiosamplelabel']}{$r['segrange']}');byId('fldNoParentIndicator').checked = false; ";
        } else { 
            $clicker = "alert('SLIDES ARE NOT ALLOWED AS PARENT SEGMENTS');";
        }
        $segTbl .= "<tr onclick=\"{$clicker}\"><td>{$r['segrange']}</td><td>{$r['prepmethod']}</td></tr>";
    }
    $segTbl .= "</tbody></table></div></td></tr></table>";

  //END TODO


    if ( $errorInd === 0 ) {
    $si = serverIdent;
    $sp = serverpw;
    $preparr = json_decode(callrestapi("GET", dataTree . "/globalmenu/all-preparation-methods",$si,$sp),true);
    $prp = "<table border=0 class=menuDropTbl>";
    //<tr><td align=right onclick=\"fillField('qryPreparationMethod','','');updatePrepmenu('');\" class=ddMenuClearOption>[clear]</td></tr>
    foreach ($preparr['DATA'] as $prpval) {
      $prp .= "<tr><td onclick=\"fillField('fldSEGPreparationMethod','{$prpval['lookupvalue']}','{$prpval['menuvalue']}');updatePrepmenu('{$prpval['lookupvalue']}');\" class=ddMenuItem>{$prpval['menuvalue']}</td></tr>";
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

<table border=0><tr><td valign=top>{$segTbl}</td><td>
<table border=0 cellspacing=0 cellpadding=0 id=dxdTbl>
<tr><td id=segBGDXD>{$dxd}<input type=hidden id=fldSEGBGNum value='{$bg}'></td></tr>
</table> 

<table border=0>
<tr>
  <td class=prcFldLbl>Preparation Method <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Preparation <span class=reqInd>*</span></td>
<tr>
   <td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationMethodValue><input type=text id=fldSEGPreparationMethod READONLY class="inputFld" style="width: 15vw;"><div class=valueDropDown style="min-width: 15vw;">{$prp}</div></div></td>
   <td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationValue><input type=text id=fldSEGPreparation READONLY class="inputFld" style="width: 19vw;"><div class=valueDropDown style="min-width: 25vw;" id=ddSEGPreparationDropDown><center>(Select a Preparation Method)</div></div></td></tr>
</table>      


<table>
<tr>
  <td class=prcFldLbl>Metric <span class=reqInd>*</span></td>
  <td class=prcFldLbl>Container</td></tr>
</tr>
<tr>
   <td><table><tr><td><input type=text id=fldSEGAddMetric></td><td><div class=menuHolderDiv><input type=hidden id=fldSEGAddMetricUOMValue><input type=text id=fldSEGAddMetricUOM READONLY class="inputFld" style="width: 10vw;"><div class=valueDropDown style="min-width: 10vw;">{$met}</div></div></td></tr></table>  </td>
<td><div class=menuHolderDiv><input type=hidden id=fldSEGPreparationContainerValue><input type=text id=fldSEGPreparationContainer READONLY class="inputFld" style="width: 18vw;"><div class=valueDropDown style="min-width: 15vw;">{$prpcon}</div></div></td>
</tr>
</table>

<table border=0 id=assignTbl>
  <tr>
   <td class=prcFldLbl>Assignment <span class=reqInd>*</span></td>
   <td class=prcFldLbl>Request #</td>
   <td rowspan=2 valign=bottom style="padding: 0;"><table class=tblBtn id=btnBankBank onclick="markAsBank();" style="width: 6vw;"><tr><td style=" font-size: 1.1vh;"><center>Bank</td></tr></table></td>
  </tr>
  <tr>
    <td valign=top><input type=hidden id=requestsasked value=0>
         <div class=suggestionHolder>
          <input type=text id=fldSEGselectorAssignInv class="inputFld" onkeyup="selectorInvestigator(); byId('fldSEGselectorAssignReq').value = '';byId('requestDropDown').innerHTML = '';byId('requestsasked').value = 0;  ">
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
</table>

<table border=0 width=80%>
<tr><td class=prcFldLbl>Segment Comments</td></tr>   
<tr><td><TEXTAREA id=fldSEGSGComments></TEXTAREA></td></tr>   
</table>
   
   
<table width=100% border=0>
<tr><td class=prcFldLbl>Repeat <span class=reqInd>*</span></td><td align=right rowspan=3 valign=bottom>

  <table cellspacing=0 cellpadding=0 border=0><tr>
    <td><table class=tblBtn id=btnSaveSeg style="width: 6vw;" onclick="addDefinedSegment(0);"><tr><td style=" font-size: 1.1vh;"><center>Save Segment</td></tr></table></td>
    <td><table class=tblBtn id=btnSaveSegPrnt style="width: 6vw;" onclick="addDefinedSegment(1);"><tr><td style=" font-size: 1.1vh;"><center>Save &amp; Print</td></tr></table></td>
  </tr></table>

</td></tr>
<tr><td valign=top><input type=text id=fldSEGDefinitionRepeater value=1></td></tr>
<tr><td><input type=checkbox id=fldSEGParentExhaustInd><label for=fldSEGParentExhaustInd>Parent Component has been Exhausted</label></td></tr>
<tr><td colspan=2 style="text-align: right; font-size: 1vh; color: rgba(237, 35, 0, 1); font-weight: bold;">{$usrrecord['originalaccountname']} / {$usrrecord['institutionname']} </td></tr>
</table>

</td></tr>
</table>
</div>

RTNTHIS;
    } else { 
        $rtnThis[] = $msgArr;
    }
  

   return $rtnThis;
}

function bldDialogUploadPathRpt ( $passeddata ) { 

//{"whichdialog":"prnew","objid":"aEMrL0VTWmdCZXRHcU52K1p6K3N1Zz09","dialogid":"7JG7PQ8Qw6BXxXV"}      
    $pdta = json_decode( $passeddata, true);
    $bg = cryptservice( $pdta['objid'] , 'd', false );    
    $errorInd = 0;
    session_start(); 
    $sess = session_id();
    require(serverkeys . "/sspdo.zck");
    $rtnThis = "ERROR!";
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
       $bsnbr = cryptservice( $pdta['objid'], 'd');
       $pathrptSQL = "select bs.read_Label as bgs, bs.pbiosample, bs.pathReport, ifnull(bs.pathreportid,0) as pathreportid, concat(trim(ifnull(bs.anatomicSite,'')), if(trim(ifnull(bs.tissType,''))='','', concat(' (',trim(ifnull(bs.tissType,'')),')'))) site, concat(trim(ifnull(bs.diagnosis, '')), if(trim(ifnull(bs.subdiagnos,''))='','', concat( ' :: ' , trim(ifnull(bs.subdiagnos,''))))) as diagnosis, ifnull(bs.procureInstitution,'') as procinstitution, ifnull(date_format(bs.procedureDate,'%m/%d/%Y'),'') as proceduredate, concat( if(trim(ifnull(bs.pxiAge,'')) = '', '-',trim(ifnull(bs.pxiAge,''))),'::',if(trim(ifnull(bs.pxiRace,''))='','-',ucase(trim(ifnull(bs.pxiRace,'')))),'::',if(trim(ifnull(bs.pxiGender,'')) = '','-',ucase(trim(ifnull(bs.pxiGender,''))))) ars, ifnull(bs.pxiid,'NOPXI') as pxiid from  masterrecord.ut_procure_biosample bs where bs.pbiosample = :bsnbr";

$pathrptRS = $conn->prepare($pathrptSQL); 
       $pathrptRS->execute(array(':bsnbr' => $bsnbr));
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
       }
    
    
  $devarr = json_decode(callrestapi("GET", dataTree . "/global-menu/dev-edit-pathrpt-reasons",serverIdent, serverpw), true);
  $devm = "<table border=0 class=menuDropTbl>";
  foreach ($devarr['DATA'] as $devval) {
    $devm .= "<tr><td onclick=\"fillField('fldDialogPRUPEditReason','','{$devval['menuvalue']}');\" class=ddMenuItem>{$devval['menuvalue']}</td></tr>";
  }
  $devm .= "</table>";
$prTxt = preg_replace('/<p\s?\/?>/i',"\n\n",preg_replace('/<br\s?\/?>/i', "\n", $pdta['pathologyrpt']));

$rtnThis = <<<RTNTHIS
<input type=hidden id=fldDialogPRUPLabelNbr value='{$pdta['labelnbr']}'>
<input type=hidden id=fldDialogPRUPBG value='{$pdta['pbiosample']}'>
<input type=hidden id=fldDialogPRUPUser value='{$pdta['user']}'>
<input type=hidden id=fldDialogPRUPSess value='{$pdta['sessionid']}'>
<input type=hidden id=fldDialogPRUPPXI value='{$pdta['pxiid']}'>
<input type=hidden id=fldDialogPRUPPRID value='NEWPRPT'>
<table border=0 id=PRUPHoldTbl cellspacing=0 cellpadding=0>
<tr><td colspan=7 id=VERIFHEAD>PATHOLOGY REPORT UPLOAD</td></tr>
<tr><td class=lblThis style="width: 8vw;">Biogroup</td>
        <td class=lblThis style="width: 15vw;">Site</td>
        <td class=lblThis style="width: 15vw;">Diagnosis</td>
        <td class=lblThis style="width: 8vw;">Institution</td>
        <td class=lblThis style="width: 10vw;">Procedure Date</td>
        <td class=lblThis style="width: 10vw;">A/R/S</td>
        <td></td></tr>
<tr><td class=dspVerif>{$bg}</td><td class=dspVerif>{$pdta['site']}</td><td class=dspVerif>{$pdta['diagnosis']}</td><td class=dspVerif>{$pdta['procinstitution']}</td><td class=dspVerif>{$pdta['proceduredate']}</td><td class=dspVerif>{$pdta['ars']}</td><td></td></tr>

<tr><td colspan=7 class=headhead>Pathology Report Text</td></tr>
<tr><td colspan=7 style="padding: 0 0 0 .4vw;"><TEXTAREA id=fldDialogPRUPPathRptTxt>{$prTxt}</TEXTAREA></td></tr>
<tr><td colspan=7 style="padding: .4vh .8vw .4vh .8vw;"><input type=checkbox id=HIPAACertify><label for=HIPAACertify id=HIPAABoxLabel>By clicking this box, I ({$pdta['user']}) certify that this Pathology Report Text DOES NOT contain any HIPAA patient identifiers.  This includes:  names, birthdays, addresses, phone numbers, email addresses, physician names, pathology assistance names, technician names, institution names, dates, etc.  I have made myself familiar with the HIPAA identifiers and certify that ALL HIPAA idenitifers have been removed as per CHTNEastern Standard Operating Procedures.  This pathology report is redacted so that the donor/patient cannot be identified.</td></tr>

<tr><td colspan=7><center>
<table><tr><td class=headhead valign=bottom>Override PIN (Inventory PIN)</td><td valign=bottom class=headhead> Reason for Edit </td></tr>
<tr><td style="padding: 0 0 0 .5vw;"><input type=password id=fldUsrPIN style="width: 11vw; font-size: 1.3vh;"></td><td style="padding: 0 0 0 .5vw;"><div class=menuHolderDiv><input type=text id=fldDialogPRUPEditReason style="font-size: 1.3vh; width: 13vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
</table>
</td></tr>

<tr><td colspan=7 align=right style="padding: 0 20vw 0 0;"><table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="editPathologyReportText('{$pdta['dialogid']}');"><tr><td style="white-space: nowrap;"><center>Save</td></tr></table></td></tr>

</table>
RTNTHIS;

    }
return $rtnThis;
}

function bldDialogEditPathRpt( $passeddata ) { 
  $pdta = json_decode($passeddata, true); 
  $errorInd = 0;
  session_start(); 
  $sess = session_id();
  require(serverkeys . "/sspdo.zck");

  $rtnThis = "ERROR!";
  
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
       $prid = cryptservice( $pdta['objid'], 'd');
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
            //$dta = array('pagecontent' => bldDialogGetter('dataCoordEditPR', $pdta) ); 
            
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
<tr><td colspan=7 id=VERIFHEAD>PATHOLOGY REPORT EDIT</td></tr>
<tr><td class=lblThis style="width: 8vw;">Biogroup</td>
        <td class=lblThis style="width: 15vw;">Site</td>
        <td class=lblThis style="width: 15vw;">Diagnosis</td>
        <td class=lblThis style="width: 8vw;">Institution</td>
        <td class=lblThis style="width: 10vw;">Procedure Date</td>
        <td class=lblThis style="width: 10vw;">A/R/S</td>
        <td></td></tr>
<tr><td class=dspVerif>{$bg}</td><td class=dspVerif>{$pdta['site']}</td><td class=dspVerif>{$pdta['diagnosis']}</td><td class=dspVerif>{$pdta['procinstitution']}</td><td class=dspVerif>{$pdta['proceduredate']}</td><td class=dspVerif>{$pdta['ars']}</td><td></td></tr>

<tr><td colspan=7 class=headhead>Pathology Report Text</td></tr>
<tr><td colspan=7 style="padding: 0 0 0 .4vw;"><TEXTAREA id=fldDialogPRUPPathRptTxt>{$prTxt}</TEXTAREA></td></tr>
<tr><td colspan=7 style="padding: .4vh .8vw .4vh .8vw;"><input type=checkbox id=HIPAACertify><label for=HIPAACertify id=HIPAABoxLabel>By clicking this box, I ({$pdta['user']}) certify that this Pathology Report Text DOES NOT contain any HIPAA patient identifiers.  This includes:  names, birthdays, addresses, phone numbers, email addresses, physician names, pathology assistance names, technician names, institution names, dates, etc.  I have made myself familiar with the HIPAA identifiers and certify that ALL HIPAA idenitifers have been removed as per CHTNEastern Standard Operating Procedures.  This pathology report is redacted so that the donor/patient cannot be identified.</td></tr>

<tr><td colspan=7><center>
<table><tr><td class=headhead valign=bottom>Override PIN (Inventory PIN)</td><td valign=bottom class=headhead> Reason for Edit </td></tr>
<tr><td style="padding: 0 0 0 .5vw;"><input type=password id=fldUsrPIN style="width: 11vw; font-size: 1.3vh;"></td><td style="padding: 0 0 0 .5vw;"><div class=menuHolderDiv><input type=text id=fldDialogPRUPEditReason style="font-size: 1.3vh; width: 13vw;"><div class=valueDropDown>{$devm}</div></div></td></tr>
</table>
</td></tr>

<tr><td colspan=7 align=right style="padding: 0 20vw 0 0;"><table class=tblBtn id=btnUploadPR style="width: 6vw;" onclick="editPathologyReportText('{$pdta['dialogid']}');"><tr><td style="white-space: nowrap;"><center>Save</td></tr></table></td></tr>

</table>
RTNTHIS;

       }

   }
  return $rtnThis;
}

function bldDialogEditDesigDX( $passeddata ) { 
  $pdta = json_decode($passeddata, true); 
  $errorInd = 0;
  session_start(); 
  $sess = session_id();
  require(serverkeys . "/sspdo.zck");

  //TODO:  MAKE WEBSERVICE
  $siteSQL = "SELECT upper(ifnull(tisstype,'')) as speccat, upper(ifnull(anatomicsite,'')) as psite, upper(ifnull(subSite,'')) as subsite, concat( upper(ifnull(diagnosis,'')),  if (ifnull(subdiagnos,'') = '','',concat(' :: ', upper(ifnull(subdiagnos,''))))) as dx, upper(ifnull(metsSite,'')) as metssite, upper(ifnull(sitePosition,'')) as siteposition, upper(ifnull(pdxSystemic,'')) as pdxsystemic  FROM masterrecord.ut_procure_biosample where pBioSample = :pbiosample";
  $siteR = $conn->prepare($siteSQL);
  $siteR->execute(array(':pbiosample' => $pdta['objid']));

  $speccat = "";
  $primesite = "";
  $subsite = "";
  $sitePos = "";
  $dx = "";
  $met = "";
  $sysdx = "";
  $goodMainVocab = 0;
  $goodSysVocab = 0;
  $goodMetsVocab = 0;
  if ($siteR->rowCount() === 1) { 
    $defdx = $siteR->fetch(PDO::FETCH_ASSOC);
    $payload = json_encode($defdx);
    //{"speccat":"MALIGNANT","psite":"BLADDER","subsite":"SEROSA","dx":"CARCINOMA :: UROTHELIAL (TRANSITIONAL CELL)","metssite":"KIDNEY","siteposition":"LEFT","pdxsystemic":"ZACKITIS"}
    $speccat = $defdx['speccat'];
    $primesite = $defdx['psite'];
    $subsite = $defdx['subsite'];
    $sitePos = $defdx['siteposition'];
    $dx = $defdx['dx'];
    $met = $defdx['metssite'];
    $sysdx = $defdx['pdxsystemic'];
    $voccheck = json_decode(callrestapi("POST", dataTree . "/data-doers/validate-chtn-vocabulary",serverIdent, serverpw, $payload), true);
    //{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"mainvocchk":1,"systemicvocchk":0,"metsvocchk":1}}
    $goodMainVocab = $voccheck['DATA']['mainvocchk'];
    $goodSysVocab =  $voccheck['DATA']['systemicvocchk'];
    $goodMetsVocab = $voccheck['DATA']['metsvocchk'];
  }

  $vocMain = ( (int)$goodMainVocab <> 0 ) ? "Vocabulary Match Found" : "<span class=badvocabind>No Vocabulary Match Found</span>";
  $vocSyst = ( (int)$goodSysVocab <> 0 ) ? "Vocabulary Match Found" : "<span class=badvocabind>No Vocabulary Match Found</span>";
  $vocMets = ( (int)$goodMetsVocab <> 0 ) ? "Vocabulary Match Found" : "<span class=badvocabind>No Vocabulary Match Found</span>";

  $preambleTxt = "This dialog allow you to edit the diagnosis designation parameters of a biosample.  You must be aware that the CHTNEastern over time has used multiple vocabluaries.  At the end of this paragraph is a denotation as to whether the vocabulary is correct to the present CHTN Network's vocabulary.  If you change any values to get the 'Save' button, you must unlock the form which will blank all values.<center> <table><tr><td><b>Main Designation</b>:</td><td>{$vocMain}</td></tr><tr><td><b>Systemic</b>:</td><td>{$vocSyst}</td></tr><tr><td><b>Metastatic</b>:</td><td>{$vocMets}</td></tr></table>"; 


  //SPECIMEN CATEGORY
    $spcData = dropmenuInitialSpecCat( $speccat, 'bgvocabedit' );
    $spcmenu = $spcData['menuObj'];
  //SITE POSITIONS
    $asiteposData = dropmenuVocASitePositions( $sitePos, 'bgvocabedit' ); 
    $aspmenu = $asiteposData['menuObj'];
  //BASE SITE-SUBSITE MENU
    $sitesubsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSite READONLY class=\"inputFld\" value=\"{$primesite}\"></div><div class=\"valueDropDown vocEditTbl\" id=ddPRCSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>"; 

   $subsite = "<div class=menuHolderDiv><input type=hidden id=fldPRCSSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSSite READONLY class=\"inputFld\" value=\"{$subsite}\"></div><div class=\"valueDropDown vocEditTbl\" id=ddPRCSSite><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div></div></div>";

   //BASE DX-MOD Menu
     $dxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCDXModValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCDXMod READONLY class=\"inputFld\" value=\"{$dx}\"></div><div class=\"valueDropDown vocEditTbl\" id=ddPRCDXMod><center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category & Site)</div></div></div>";
 
 //METASTATIC SITE MENU DROPDOWN
     $metsData = dropmenuMetsMalignant( $met );
     $metssite = $metsData['menuObj'];
     $metsdxmod = "<div class=menuHolderDiv><input type=hidden id=fldPRCMETSDXValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCMETSDX READONLY class=\"inputFld\" value=\"\"></div><div class=valueDropDown id=ddPRCMETSDX ><center><div style=\"font-size: 1.2vh; max-width: 25vw; overflow: auto;\">(Choose a Metastatic site)</div></div></div>";

  //SYSTEMIC LIST 
    $sysData = dropmenuSystemicDXListing( $sysdx ); 
    $sysdxmenu = $sysData['menuObj'];


  $rtnThis = <<<RTNTHIS
<style>
  #vocHoldTbl { width: 35vw; font-size: 1.3vh; } 
  #preambleTxt { text-align: justify; line-height: 1.8em; width: 32vw; box-sizing: border-box; padding: 8px; font-size: 1.3vh; }
  #fldPRCSpecCat { font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCSite { font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCSSite {  font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCDXMod {  font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCMETSSite  {  font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCSitePosition { font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  #fldPRCSystemList { font-size: 1.3vh; padding: 1vh .5vw 1vh .5vw; width: 25vw; }
  .vocEditTbl { min-width: 25vw; }
  .badvocabind { font-weight: bold; color: rgba(237, 35, 0, 1); }
</style>
<table>
<tr><td colspan=2 id=preambleTxt> {$preambleTxt}</td></tr>
</table>
<table border=0 id=vocHoldTbl>
<tr><td>Specimen Category:&nbsp; </td><td>{$spcmenu} <input type=hidden id=fldHoldBioGroup value="{$pdta['objid']}"></td></tr>
<tr><td>Site:&nbsp;</td><td>{$sitesubsite}</td></tr>
<tr><td>Sub-Site:&nbsp;</td><td>{$subsite}</td></tr>
<tr><td>Site Position:&nbsp; </td><td>{$aspmenu}</td></tr>
<tr><td>Diagnosis :: Modifier: &nbsp;</td><td>{$dxmod}</td></tr>
<tr><td></td><td><input type=checkbox id=fldPRCDXOverride onclick="overridedxmenu();"><label for=dxoverride>Diagnosis Override</label></td></tr>
<tr><td>METS From: &nbsp;</td><td>{$metssite}</td></tr>
<tr><td>Systemic Diagnosis: &nbsp; </td><td>{$sysdxmenu}</td></tr>
<tr><td colspan=2 align=right> <table class=tblBtn id=btnVocEditEnable data-vocabunlock=0 style="width: 6vw;" onclick="editVocab();"><tr><td style=" font-size: 1.1vh;"><center><span id=buttnText>Unlock</span></td></tr></table> </td></tr>
</table>



RTNTHIS;
  return $rtnThis;

}

function bldDialogEditEncounter ( $passeddata ) { 
  //{"whichdialog":"dlgEDTENC","objid":"569c4a4a-8bca-4a02-9c7b-28d89aac7573","dialogid":"h0gFsujDaQ1SOtC"}
  $pdta = json_decode($passeddata, true); 
  $errorInd = 0;
  session_start(); 
  $sess = session_id();
  require(serverkeys . "/sspdo.zck");
  $objref = explode("::",$pdta['objid']);
  
  if ( trim($objref[0]) === "" || trim($objref[1]) === "" ) { 
      //ERROR
      $rtnThis = "<table><tr><td><h3>ERROR:  SEE CHTNEastern Informatics personnel</h3></td></tr></table>";
  } else { 

      //TODO:  MAKE THIS A WEBSERVICE
      $allPXISQL = "SELECT pbiosample, pxiid, replace(read_label,'_','') as readlabel, upper(concat(ifnull(tisstype,''), if(ifnull(anatomicSite,'')='','',concat(' :: ', ifnull(anatomicSite,''))), if(ifnull(diagnosis,'')='','',concat(' :: ',ifnull(diagnosis,''))))) as dxdesig, upper(ifnull(pxirace,'')) as pxirace, upper(ifnull(pxiGender,'')) as pxisex, ifnull(pxiage,0) as pxiage, ifnull(pxiageuom,1) as pxiageuom, ifnull(chemoind, 2) as chemoind, ifnull(radind,2) as radind, ifnull(subjectnbr,'') as subjectnbr, ifnull(protocolnbr,'') as protocolnbr FROM masterrecord.ut_procure_biosample where pxiid = :pxiid";
      $allPXIRS = $conn->prepare($allPXISQL); 
      $allPXIRS->execute(array(':pxiid' => $objref[0])); 
      $allPXIRef = $allPXIRS->rowCount();
      $rcCnt = 1;

      $agarr = json_decode(callrestapi("GET", dataTree . "/global-menu/age-uoms",serverIdent, serverpw), true);
      $rarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-race",serverIdent, serverpw), true);
      $sxarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-sex",serverIdent, serverpw), true);
      $cxarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-cx",serverIdent, serverpw), true);
      $rxarr = json_decode(callrestapi("GET", dataTree . "/global-menu/pxi-rx",serverIdent, serverpw), true);


      $bgList = "<table border=0 cellspacing=8 cellpadding=0 id=matrixTbl><tr><td colspan=30 class=announcer>[ENCOUNTER ID FOUND ON {$allPXIRef} BIOSAMPLES]</td></tr><tr><td class=toplabel>Designation [Biogroup]</td><td class=toplabel>Age (at procedure)</td><td class=toplabel>Race</td><td class=toplabel>Sex</td><td class=toplabel>Chemo</td><td class=toplabel>Radiation</td><td class=toplabel>Subject #</td><td class=toplabel>Protocol #</td></tr>";
      while ($r = $allPXIRS->fetch(PDO::FETCH_ASSOC)) { 
        $idsuffix = generateRandomString(8);

        $agm = "<table border=0 class=\"menuDropTbl ageUOMOptTbl\">";
        $givendspvalue = "";
        $givendspcode = "";
        foreach ($agarr['DATA'] as $agval) {
          if ( (int)$r['pxiageuom'] === (int)$agval['lookupvalue'] ) { 
            $givendspcode = $agval['lookupvalue'];
            $givendspvalue = $agval['menuvalue'];
          } 
          $agm .= "<tr><td onclick=\"fillField('dlgFldAgeUOM{$idsuffix}','{$agval['lookupvalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
        }
        $agm .= "</table>";
        $ageuommnu = "<div class=menuHolderDiv><input type=hidden id=dlgFldAgeUOM{$idsuffix}Value value=\"{$givendspcode}\"><input type=text class=dlgFldAgeUOM id=dlgFldAgeUOM{$idsuffix} READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddAGEUOM>{$agm}</div></div>";

      $rm = "<table border=0 class=\"menuDropTbl raceOptTbl\">";
      $rgivendspvalue = "";
      $rgivendspcode = "";
      foreach ($rarr['DATA'] as $rval) {
        if ( strtoupper($rval['lookupvalue']) === strtoupper($r['pxirace']) ) { 
            $rgivendspcode = $rval['codevalue'];
            $rgivendspvalue = $rval['menuvalue'];
          }
          $rm .= "<tr><td onclick=\"fillField('dlgFldRace{$idsuffix}','{$rval['lookupvalue']}','{$rval['menuvalue']}');\" class=ddMenuItem>{$rval['menuvalue']}</td></tr>";
        }
        $rm .= "</table>";
        $racemnu="<div class=menuHolderDiv><input type=hidden id=dlgFldRace{$idsuffix}Value value=\"{$rgivendspcode}\"><input type=text id=dlgFldRace{$idsuffix} READONLY class=\"inputFld dlgFldRace\" value=\"{$rgivendspvalue}\"><div class=valueDropDown id=ddADDRace>{$rm}</div></div>";

      $sxm = "<table border=0 class=\"menuDropTbl sexOptTbl\">";
      $sxgivendspvalue = "";
      $sxgivendspcode = "";
      foreach ($sxarr['DATA'] as $sxval) {
        if ($sxval['lookupvalue'] === $r['pxisex'] ) { 
          $sxgivendspvalue = $sxval['menuvalue'];
          $sxgivendspcode = $sxval['lookupcode'];
        } 
        $sxm .= "<tr><td onclick=\"fillField('dlgFldSex{$idsuffix}','{$sxval['lookupvalue']}','{$sxval['menuvalue']}');\" class=ddMenuItem>{$sxval['menuvalue']}</td></tr>";
      }
      $sxm .= "</table>";
      $sexmnu = "<div class=menuHolderDiv><input type=hidden id=dlgFldSex{$idsuffix}Value value=\"{$sxgivendspcode}\"><input type=text id=dlgFldSex{$idsuffix} READONLY class=\"inputFld dlgFldSex\" value=\"{$sxgivendspvalue}\"><div class=valueDropDown id=ddDNRSex>{$sxm}</div></div>";

      $cxm = "<table border=0 class=\"menuDropTbl cxOptTbl\">";
      $cxgivendspvalue = "";
      $cxgivendspcode = "";
      foreach ($cxarr['DATA'] as $cxval) {
        if ( (int)$cxval['lookupvalue'] === (int)$r['chemoind'] ) { 
          $cxgivendspvalue = $cxval['menuvalue'];
          $cxgivendspcode = $cxval['lookupcode'];
        } 
        $cxm .= "<tr><td onclick=\"fillField('dlgFldCX{$idsuffix}','{$cxval['lookupvalue']}','{$cxval['menuvalue']}');\" class=ddMenuItem>{$cxval['menuvalue']}</td></tr>";
      }
      $cxm .= "</table>";
      $cxmnu = "<div class=menuHolderDiv><input type=hidden id=dlgFldCX{$idsuffix}Value value=\"{$cxgivendspcode}\"><input type=text id=dlgFldCX{$idsuffix} READONLY class=\"inputFld dlgFldCX\" value=\"{$cxgivendspvalue}\"><div class=valueDropDown id=ddDNRSex>{$cxm}</div></div>";

      $rxm = "<table border=0 class=\"menuDropTbl cxOptTbl\">";
      $rxgivendspvalue = "";
      $rxgivendspcode = "";
      foreach ($rxarr['DATA'] as $rxval) {
        if ( (int)$rxval['lookupvalue'] === (int)$r['radind'] ) { 
          $rxgivendspvalue = $rxval['menuvalue'];
          $rxgivendspcode = $rxval['lookupcode'];
        } 
        $rxm .= "<tr><td onclick=\"fillField('dlgFldRX{$idsuffix}','{$rxval['lookupvalue']}','{$rxval['menuvalue']}');\" class=ddMenuItem>{$rxval['menuvalue']}</td></tr>";
      }
      $rxm .= "</table>";
      $rxmnu = "<div class=menuHolderDiv><input type=hidden id=dlgFldRX{$idsuffix}Value value=\"{$rxgivendspcode}\"><input type=text id=dlgFldRX{$idsuffix} READONLY class=\"inputFld dlgFldRX\" value=\"{$rxgivendspvalue}\"><div class=valueDropDown id=ddDNRSex>{$rxm}</div></div>";

      $ageEditTbl = "<table border=0 cellpadding=0 cellspacing=0><tr><td><input type=text class=dlgFldPHIAge id=\"dlgFldPHIAge{$idsuffix}\" value=\"{$r['pxiage']}\" maxlength=2></td><td style=\"padding: 0 0 0 4px;\">{$ageuommnu}</td></tr></table>";
      $sbj = "<input type=text class=dlgFldSbjt id=\"dlgFldSbjt{$idsuffix}\" value=\"{$r['subjectnbr']}\">";
      $prt = "<input type=text class=dlgFldProtocol id=\"dlgFldProtocol{$idsuffix}\" value=\"{$r['protocolnbr']}\">";
      $sveBtn = "<table class=tblBtn id=btnSaveSeg style=\"width: 6vw;\" onclick=\"packageEncounterSave('{$idsuffix}');\"><tr><td style=\"font-size: 1.1vh;\"><center>Update</td></tr></table>";
      $bgList .= "<tr><td class=bgiddsp>{$r['dxdesig']} [{$r['readlabel']}] <input type=hidden id='dlgpbiosample{$idsuffix}' value=\"{$r['pbiosample']}\">  <input type=hidden id='dlgpxiid{$idsuffix}' value=\"{$r['pxiid']}\">  </td><td>{$ageEditTbl}</td><td>{$racemnu}</td><td>{$sexmnu}</td><td>{$cxmnu}</td><td>{$rxmnu}</td><td>{$sbj}</td><td>{$prt}</td><td>{$sveBtn}</td></tr>";
      $rcCnt++;
      }
      $bgList .= "</table>";


  $rtnThis = <<<RTNTHIS
<style>
  #holdingTbl { width: 90vw; }
  #vwarning { font-weight: bold; color: rgba(237, 35, 0,1); } 
  #instructionsText { text-align: justify; line-height: 1.8em; padding: 8px 8px 20px 8px; }
  #matrixTbl { font-size: 1.4vh; }

  .numberer { width: 2vw; text-align: center; font-size: 1.1vh; border: 1px solid rgba(48,57,71,1); color: rgba(48,57,71,1); background: rgba(255,255,255,1); }
  .bgiddsp { font-size : 1.1vh; width: 18vw; max-width: 17vw; box-sizing: border-box; border: 1px solid rgba(48,57,71,.5); border-top: none; border-right: none; padding: 0 0 0 4px; color: rgba(48,57,71,1); background: rgba(239,239,239,.8); }

  .dlgFldAgeUOM { width: 6vw; font-size: 1.1vh; }
  .ageUOMOptTbl { min-width: 7vw; }
  .dlgFldPHIAge { width: 2.5vw; text-align: right;  font-size: 1.1vh;}
  .dlgFldRace { width: 12vw;  font-size: 1.1vh;}
  .raceOptTbl { min-width: 13vw; }
  .dlgFldSex { width: 6vw;  font-size: 1.1vh;}
  .sexOptTbl { width: 7vw; }
  .dlgFldCX { width: 6vw;  font-size: 1.1vh;}
  .cxOptTbl { min-width: 7vw; }
  .dlgFldRX { width: 6vw;   font-size: 1.1vh;}
  .rxOptTbl { min-width: 7vw; }
  .dlgFldSbjt { width: 12vw; font-size: 1.1vh;}
  .dlgFldProtocol { width: 12vw; font-size: 1.1vh;}
  .toplabel { font-size: 1.1vh; font-weight: bold; color: rgba(48,57,71,1); border-bottom: 1px solid rgba(48,57,71,.8); padding: 0 4px 0 4px;  }
  .announcer { font-size: 1.1vh; text-align: center;   }

</style>

<table border=0 id=holdingTbl>
<tr><td id=instructionsText>
<b>Instructions</b>: Below is a list of all donor encounters for this donor's id.  When editing the encounter record, make sure that all donor encouters are correct for each biogroup's donor encounter (i.e subject/protocol numbers should only be referenced for those biogroups under which that encounter has happened). If you are unsure how to manage data on this screen <span id=vwarning>DO NOT GUESS</span>, ask either a CHTNEastern Informatics person or a CHTNEastern manager.   

</td></tr>
<tr><td valign=top>{$bgList}</td></tr>
</table>
RTNTHIS;
  }
return $rtnThis;
}

function bldDialogCoordEditComments ( $passeddata ) { 
  $pdta = json_decode($passeddata, true); 
  $rqstobj = explode(":",$pdta['objid']); 
  $errorInd = 0;
  session_start(); 
  $sess = session_id();

  //TODO:  MAKE THIS A WEBSERVICE
  switch ( $rqstobj[0] ) { 
    case 'BGC':
        $mainLabel = "Biogroup Comments";
        $cSQL = "SELECT ifnull(biosamplecomment,'') as dspcomment FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
        $cType = "BIOSAMPLE";
        break;
    case 'HPQ':
        $mainLabel = "Question for HPR/QMS Reviewer";
        $cSQL = "SELECT ifnull(questionHPR,'') as dspcomment FROM masterrecord.ut_procure_biosample where pbiosample = :pbiosample";
        $cType = "HPRQ";
        break;    
    default:   
      $errorInd = 1;    
  }
  //{"whichdialog":"dlgCMTEDIT","objid":"HPQ:82454","dialogid":"uXJek9Zu8MXVTqh"}
  $rtnThis = "";
  if ( $errorInd === 1 ) { 
    //DISPLAY ERROR
  } else {
    require(serverkeys . "/sspdo.zck");
    $at = genAppFiles;
    $serverAccess = generateRandomString(20);

    $accSQL = "insert into serverControls.ss_srvIdents (sessid, accesscode, onwhen) values (:sess,:acc ,now())";
    $accR = $conn->prepare($accSQL);
    $accR->execute(array(':sess' => $sess, ':acc' => $serverAccess));

    $idarr['commenttype'] = $cType;
    $idarr['record'] = cryptservice($rqstobj[1],'e');
    $idarr['access'] = cryptservice($serverAccess,'e');
    $IDTHIS = json_encode($idarr);

    $rs = $conn->prepare($cSQL);
    $rs->execute(array(':pbiosample' => $rqstobj[1]));
    $r = $rs->fetch(PDO::FETCH_ASSOC);

    $waitpic = base64file("{$at}/publicobj/graphics/zwait2.gif", "waitgifcmt", "gif", true);         


    $rtnThis = <<<RTNTHIS
<style>
#fldDspBGComment { width: 50vw; height: 10vh; font-size: 1.5vh; padding: 5px; box-sizing: border-box; background: rgba(255,255,255,1); } 
#waitgifcmt { width: 1vw; }
#waitDsp { display: none; width: 50vw; }
</style>
<form id=frmBGCommentUpdater>
<table border=0>
<tr><td><center><div id=waitDsp>{$waitpic}<br>Please wait ...</div></td></tr>
<tr><td class=fldLabel id=dspLineOne>{$mainLabel}</td></tr>
<tr><td id=dspLineTwo><TEXTAREA id=fldDspBGComment>{$r['dspcomment']}</TEXTAREA></td></tr>
<tr><td align=right id=dspLineThree><table class=tblBtn id=btnSaveBGComment style="width: 6vw;" onclick="dlgSaveBGComments();"><tr><td><center>Save</td></tr></table></td></tr>
</table>
<input type=hidden id=fldID value={$IDTHIS}>
</form>
RTNTHIS;

  }
  return $rtnThis;
}

function bldWeeklyGoals( $whichUsr ) { 
    //$whichUsr THIS IS THE USER ARRAY {"statusCode":200,"loggedsession":"i46shslvmj1p672lskqs7anmu1","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":20,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"googleiconcode":"lock_open","menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"googleiconcode":"account_balance","menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""}]],["472","REPORTS","",[{"googleiconcode":"account_balance","menuvalue":"All Reports","pagesource":"all-reports","additionalcode":""}]],["473","UTILITIES","",[{"googleiconcode":"account_balance","menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474",null,null,[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]} 
  
    
    return "Our Weekly Goals " . $whichUsr->username . " ... " . $whichUsr->allowweeklyupdate;
}

function bldInventoryAction_Inventorytally ( $whichusr ) { 
    
       return "<h1>INVENTORY TALLY!";
}

function bldInventoryAction_Inventorymaster ( $whichusr ) { 
    
    $rtnThis = <<<RTNTHIS
            
<table>     
    <tr><td class=fldLabel>Scan ... </td></tr>
    <tr><td><input type=hidden READONLY id=fldLocationScanCode><input type=text READONLY id=fldDspScanToLocation></td></tr>
    <tr><td><div id=errorDsp></div></td></tr>        
    <tr><td><div id=dspScanList></div></td></tr>       
</table>
            
RTNTHIS;
    
    

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
    <td valign=top><input type=hidden id=requestsasked value=0>
         <div class=suggestionHolder>
          <input type=text id=fldSEGselectorAssignInv class="inputFld" onkeyup="selectorInvestigator(); byId('fldSEGselectorAssignReq').value = '';byId('requestDropDown').innerHTML = '';byId('requestsasked').value = 0;  ">
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

function dropmenuSystemicDXListing( $defaultVal ) { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-systemic-dx-list",$si,$sp),true);
  $asp = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCSystemList','','');\" class=ddMenuClearOption>[clear]</td></tr>";

  if ($defaultVal === "" ) {
    $aspDefaultValue = "";
    $aspDefaultDsp = "";
  } else { 
    $aspDefaultValue = "";
    $aspDefaultDsp = $defaultVal;
  }


  foreach ($asparr['DATA'] as $aspval) {
    $asp .= "<tr><td onclick=\"fillField('fldPRCSystemList','{$aspval['codevalue']}','{$aspval['menuvalue']}');\" class=ddMenuItem>{$aspval['menuvalue']}</td></tr>";
  }
  $asp .= "</table>";
  $aspmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCSystemListValue value=\"\">"
          . "<div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCSystemList READONLY class=\"inputFld\" value=\"{$aspDefaultDsp}\"></div><div class=\"valueDropDown vocEditTbl\" id=ddPRCSystemList>{$asp}</div></div>";
  return array('menuObj' => $aspmenu,'defaultDspValue' => $aspDefaultDsp, 'defaultLookupValue' => $aspDefaultValue);
}

function dropmenuVocASitePositions( $defaultDsp = "", $screenref = "" ) { 
  $si = serverIdent;
  $sp = serverpw;
  $asparr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-site-positions",$si,$sp),true);
  $asp = "<table border=0 class=\"menuDropTbl vocEditTbl\"><tr><td align=right onclick=\"fillField('fldPRCSitePosition','','');\" class=ddMenuClearOption>[clear]</td></tr>";
  if ( $defaultDsp === "" ) {
    $aspDefaultValue = "";
    $aspDefaultDsp = "";
  } else { 
    $aspDefaultValue = "";
    $aspDefaultDsp = $defaultDsp;
  }
  $actionStuff = "";
  foreach ($asparr['DATA'] as $aspval) {
    $actionStuff = ( $screenref === 'bgvocabedit' ) ? "" : "";
      if ( (int)$aspval['useasdefault'] === 1 ) {
        $aspDefaultValue = $aspval['lookupvalue']; 
        $aspDefaultDsp = $aspval['menuvalue'];
      }
    $asp .= "<tr><td onclick=\"fillField('fldPRCSitePosition','{$aspval['lookupvalue']}','{$aspval['menuvalue']}');{$actionStuff}\" class=ddMenuItem>{$aspval['menuvalue']}{$screenRef}  </td></tr>";
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

function dropmenuMetsMalignant( $defaultVal ) { 
  $pdta = array();  
  $pdta['specimencategory'] = 'MALIGNANT';
  $passdata = json_encode($pdta);
  $menudtaarr = json_decode(callrestapi("POST",dataTree."/data-doers/sites-by-specimen-category",serverIdent,serverpw,$passdata), true);
  $metsm = "<table border=0 class=menuDropTbl>";

  if ( $defaultVal === "" ) { 
   $metsDefaultValue = "";
   $metsDefaultDsp = "";
  } else { 
   $metsDefaultValue = "";
   $metsDefaultDsp = $defaultVal;
  } 
  
  
   foreach ( $menudtaarr['DATA'] as $metsval) { 
      $metsm .= "<tr><td onclick=\"fillField('fldPRCMETSSite','{$metsval['siteid']}','{$metsval['site']}');\" class=ddMenuItem>{$metsval['site']}</td></tr>";
   } 
   $metsm .= "</table>";

   $metssite = "<div class=menuHolderDiv><input type=hidden id=fldPRCMETSSiteValue value=\"\"><div class=inputiconcontainer><div class=inputmenuiconholder><i class=\"material-icons menuDropIndicator\">menu</i></div><input type=text id=fldPRCMETSSite READONLY class=\"inputFld\" value=\"{$metsDefaultDsp}\"></div><div class=\"valueDropDown vocEditTbl\" id=ddPRCMETSSite> {$metsm} </div></div>";

  return array('menuObj' => $metssite,'defaultDspValue' => $metsDefaultDsp, 'defaultLookupValue' => $metsDefaultValue);
}

function dropmenuUninvolvedIndicator( $defaultvalue = "") { 

   $si = serverIdent;
   $sp = serverpw;
   $unknmtarr = json_decode(callrestapi("GET", dataTree . "/global-menu/uninvolved-indicator-options",$si,$sp),true);
   $uninv = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\">";
   $uninvDefaultValue = "";
   $uninvDefaultDsp = "";
   foreach ($unknmtarr['DATA'] as $uninvval) {

      if ( $defaultvalue === "" ) {  
        if ( (int)$uninvval['useasdefault'] === 1 ) {
          $uninvDefaultValue = $uninvval['codevalue']; 
          $uninvDefaultDsp = $uninvval['menuvalue'];
        }
      } else { 
        if ( $defaultvalue === $uninvval['codevalue'] ) { 
          $uninvDefaultValue = $uninvval['codevalue']; 
          $uninvDefaultDsp = $uninvval['menuvalue'];
        }
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

function dropmenuInitialSpecCat( $passedvalue = "", $screenref = "" ) {   
  $si = serverIdent;
  $sp = serverpw;
  $speccatarr = json_decode(callrestapi("GET", dataTree . "/global-menu/vocabulary-specimen-category",$si,$sp),true);

  $speccat = "<table border=0 class=\"menuDropTbl vocEditTbl\">";
  foreach ($speccatarr['DATA'] as $spcval) {
   $actionStuff = ( $screenref === 'bgvocabedit' ) ?  "updateSiteMenu();blankVocabForm(1);" : "";
   $speccat .= "<tr><td onclick=\"fillField('fldPRCSpecCat','{$spcval['lookupvalue']}','{$spcval['menuvalue']}');{$actionStuff}\" class=ddMenuItem>{$spcval['menuvalue']}</td></tr>";

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

function bldBiogroupDefitionDisplay($biogroup, $bgency) { 
  $pdta = array();  
  $pdta['bgency'] = $bgency;
  $passdata = json_encode($pdta);
  $bgarr = json_decode(callrestapi("POST",dataTree."/data-doers/master-bg-record",serverIdent,serverpw,$passdata), true);
  
  if ( (int)$bgarr['ITEMSFOUND'] === 1 ) { 
      $bg = $bgarr['DATA'];          


//ITEMSFOUND":1,"DATA":{"voidind":0,
 // "hprind":0,"hprmarkbyon":""
  //,"qcind":0,"qcmarkbyon":"","qcvalue":"","biosamplecomment":"SSV5 -----------","questionhpr":"SSV5 ----------"}}                   
      //TODO:  MAKE THIS DYNAMIC
      switch ( $bg['qcprocstatus'] ) {
      case 'S': //SUBMITTED
        $qmsicon = "<i class=\"material-icons\">offline_bolt</i>";
        $clssuffix = " 237, 35, 0, 1";
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>Submitted</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr></table>";
      break;
      case 'L': //LAB ACTION  
        $qmsicon = "<i class=\"material-icons\">schedule</i>";  
        $clssuffix = " 107, 18, 102, 1";
        $hprnavi = ((int)$bg['hprresult'] !== 0) ? "<a href=\"javascript:void(0);\" class=hprindication onclick=\"generateDialog('datacoordhprdisplay',{$bg['hprresult']});\">View HPR</a>" : ""; 
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>In Lab Action</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr><tr><td>HPR Decision: </td><td>{$bg['hprstatus']}{$hprnavi} </td></tr><tr><td>Slide Seen: </td><td>{$bg['hprslidereviewed']}</td></tr></table>";
      break;
      case 'R': //RESUBMITTED 
        $qmsicon = "<i class=\"material-icons\">history</i>";
        $clssuffix = "226,226,125,1";
        $hprnavi = ((int)$bg['hprresult'] !== 0) ? "<a href=\"javascript:void(0);\" class=hprindication onclick=\"generateDialog('datacoordhprdisplay',{$bg['hprresult']});\">View HPR</a>" : ""; 
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>Re-Submitted</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr><tr><td>HPR Decision: </td><td>{$bg['hprstatus']}{$hprnavi}</td></tr><tr><td>Slide Seen: </td><td>{$bg['hprslidereviewed']}</td></tr></table>";
      break;
      case 'H':
        $qmsicon = "<i class=\"material-icons\">offline_pin</i>";
        $clssuffix = "84,113,210,1";
        $hprnavi = ((int)$bg['hprresult'] !== 0) ? "<a href=\"javascript:void(0);\" class=hprindication onclick=\"generateDialog('datacoordhprdisplay',{$bg['hprresult']});\">View HPR</a>" : ""; 
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>HPR Review Complete</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr><tr><td>HPR Decision: </td><td>{$bg['hprstatus']} {$hprnavi}</td></tr><tr><td>Slide Seen: </td><td>{$bg['hprslidereviewed']}</td></tr></table>";
      break;
      case 'Q':
        $qmsicon = "<i class=\"material-icons\">stars</i>";
        $clssuffix = "0, 112, 13, 1";
        $hprnavi = ((int)$bg['hprresult'] !== 0) ? "<a href=\"javascript:void(0);\" class=hprindication onclick=\"generateDialog('datacoordhprdisplay',{$bg['hprresult']});\">View HPR</a>" : ""; 
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>QMS PROCESS Complete</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr><tr><td>HPR Decision: </td><td>{$bg['hprstatus']} {$hprnavi}</td></tr><tr><td>Slide Seen: </td><td>{$bg['hprslidereviewed']}</td></tr></table>";
      break;
      case 'N':
        $qmsicon = "<i class=\"material-icons\">play_circle_outline</i>";
        $clssuffix = "203, 232, 240, 1";
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>Not Applicable to biosample</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr></table>";
      break;
      default:  //NO VALUE MATCHING
        $qmsicon = "<i class=\"material-icons\">help_outline</i>";
        $clssuffix = "145,145,145,1";
        $qcstatustxt = "<table><tr><td>QMS Process: </td><td>Not Yet Submitted</td></tr><tr><td>Statused By: </td><td>{$bg['qmsstatusby']}</td></tr><tr><td>Statused On: </td><td>{$bg['qmsstatuson']}</td></tr></table>";
      }

      
      $rtnThis = "<table id=mainHolderTbl border=0 data-bgnbr='{$bgency}'>";
      $rtnThis .= <<<TOPLINE
              <tr><td rowspan=10 valign=top><div class=qmsdspholder><table border=0 cellspacing=0 cellpadding=0><tr><td id=qmsstatind style="background: rgba({$clssuffix});">{$qmsicon}</td></tr></table><div class=qmsdspinfo>{$qcstatustxt}</div></div></td><td>
                  <table border=0 class=lineTbl id=lineBiogroupAnnounce>
                        <tr>
                            <td>Biogroup {$bg['readlabel']} <input type=hidden id=masterBG value={$bg['bgnbr']}><input type=hidden id=masterBGEncy value="{$bgency}"></td>
                            <td align=right>{$bg['technician']}@{$bg['procureinstitution']}</td>
                       </tr></table>
              </td></tr>
TOPLINE;
      //DESIGNATION
                            
if ( strtoupper($bg['qcprocstatus']) === 'L' ) {
    $qdta = array();  
    $qdta['bgency'] = cryptservice($bg['readlabel'],'e');                
    $qpassdata = json_encode($qdta);                
    $bglarr = json_decode(callrestapi("POST",dataTree."/data-doers/master-bg-lab-action-notes",serverIdent,serverpw,$qpassdata), true);
    $rtnThis .= "<tr><td valign=top>";                                  
    $rtnThis .= "<table border=0  style=\"border: 1px solid rgba(107, 18, 102, 1); width: 96.5vw;\"><tr><td colspan=2 style=\"background: rgba(107, 18, 102, 1); color: rgba(255,255,255,1); padding: 8px 5px 8px 5px;\">LAB ACTIONS REQUIRED</td></tr><tr><td style=\"width: 15vw; padding: 8px;\">" . $bglarr['DATA'][0]['labactionactiondsp'] .  "</td><td style=\"padding: 8px;\">" . $bglarr['DATA'][0]['labactionnote'] . "</td></tr></table>";
    $rtnThis .= "</td></tr>";        
}                             
                                                     
      $rtnThis .= "<tr><td valign=top>";

      $rtnThis .= <<<LINEONE
<table border=0 width=100%>
  <tr>
      <td><table class=dataElementTbl id=elemSpecCat><tr><td class=elementLabel>Specimen Category</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['specimencategory']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
      <td><table class=dataElementTbl id=elemSite><tr><td class=elementLabel>Collected Site (Site :: Subsite)</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['collectedsite']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
      <td><table class=dataElementTbl id=elemDX><tr><td class=elementLabel>Diagnosis :: Modifier</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['diagnosis']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
  </tr>
</table>

<table border=0 width=100%>
  <tr>
      <td><table class=dataElementTbl id=elemMets><tr><td class=elementLabel><div class=noteHolder style="width: 6vw;">Metastatic From *<div class=noteExplainerDropDown>Since CHTNEast has been collecting for over 20 years, this designation has changed from TO/FROM. Read the Pathology Report to verify.</div></div></td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['mets']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
      <td><table class=dataElementTbl id=elemSystemic><tr><td class=elementLabel>Systemic Diagnosis</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['systemicdx']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
      <td><table class=dataElementTbl id=elemPosition><tr><td class=elementLabel>Site Position</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTDX','{$bg['bgnbr']}');">{$bg['siteposition']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table></td>
  </tr>
</table>

LINEONE;
 
      $rtnThis .= <<<ANOTHERLINE
              </td></tr>
ANOTHERLINE;
      //END DESIGNATION


    $prDocId = "";
    switch (trim($bg['prind'])) { 
      case 'YES':
        if ((int)$bg['pathologyrptdocid'] !== 0) {  
          $prDocId = ((int)$bg['pathologyrptdocid'] > 0) ? cryptservice( (int)$bg['pathologyrptdocid'], "e" ) : 0;
          $dspBG = $bg['bgnbr'];
          $pRptDsp = <<<PRPTNOTATION
            <table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td class=prAnswer>{$bg['prind']}</td>
                 <td onclick="printPRpt(event,'{$prDocId}');"><div class=prExplainer><i class="material-icons qlSmallIcon">print</i><div class=prExplainerText>Click to print pathology report for {$bg['bgnbr']}</div></div></td>
                 <td onclick="generateDialog('predit','{$prDocId}');"><div class=prExplainer><i class="material-icons qlSmallIcon">file_copy</i><div class=prExplainerText>Click to edit pathology report for {$bg['bgnbr']}</div></div></td>
               </tr>
            </table>
PRPTNOTATION;
        } else { 
          $pRptDsp = <<<PRPTNOTATION
                  <table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td class=prAnswer>{$bg['prind']}</td>
                 <td><div class=prExplainer><i class="material-icons qlSmallIcon">error</i><div class=prExplainerText>Biogroup ({$bg['bgnbr']}) has multiple pathology Report References.  See a CHTNEastern Informatics Staff Member! </div></div></td>
                 </tr>
            </table>                
PRPTNOTATION;
        }
      break;
      case 'PENDING':
        $dspBG = $bg['bgnbr'];
$pRptDsp = <<<PRPTNOTATION
            <table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td class=prAnswer>{$bg['prind']}</td>
                 <td onclick="generateDialog('prnew','{$bgency}');"><div class=prExplainer><i class="material-icons qlSmallIcon">file_copy</i><div class=prExplainerText>Click to edit pathology report for {$bg['bgnbr']}</div></div></td>
               </tr>
            </table>
PRPTNOTATION;
      break; 
      default: 
        $dspBG = $bg['bgnbr'];
          //<div class=quickLink onclick="getUploadNewPathRpt(event,'{$sglabel}');"><i class="material-icons qlSmallIcon">file_copy</i> Upload Pathology Report (Biogroup: {$dspBG})</div>
          //<td onclick="generateDialog('prnew','{$dspBG}');"><div class=prExplainer><i class="material-icons qlSmallIcon">file_copy</i><div class=prExplainerText>Click to upload pathology report for {$bg['bgnbr']}</div></div></td>
        $pRptDsp = <<<PRPTNOTATION
        <table border=0 cellspacing=0 cellpadding=0>
               <tr>
                 <td class=prAnswer>{$bg['prind']}</td>                
               </tr>
            </table>     
PRPTNOTATION;
    }

    //PHI INFORMATION
    //<div class=basicEditIcon><i class="material-icons cmtEditIconCls">menu</i></div>
$rtnThis .= <<<NEXTLINE
<tr><td>
    
<table border=0 width=100%>
  <tr>

      <td>  
      <table class=dataElementTbl id=elemProceDate><tr><td class=elementLabel>Procedure Date</td></tr><tr><td class=dataElement>{$bg['proceduredate']}&nbsp;</td></tr></table> 
      </td>

      <td>  
      <table class=dataElementTbl id=elemProcedureCollect><tr><td class=elementLabel>Procedure :: Collection Type</td></tr><tr><td class=dataElement>{$bg['collecttype']}&nbsp;</td></tr></table> 
      </td>      
   
      <td>  
      <table class=dataElementTbl id=elemARS><tr><td class=elementLabel>Age :: Race :: Sex</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTENC','{$bg['pxiid']}::{$bg['bgnbr']}');">{$bg['phiage']} :: {$bg['phirace']} :: {$bg['phisex']}&nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table> 
      </td>      

      <td>  
      <table class=dataElementTbl id=elemCXRX><tr><td class=elementLabel>Chemo :: Radiation Indicator</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTENC','{$bg['pxiid']}::{$bg['bgnbr']}');">{$bg['cxind']} :: {$bg['rxind']} &nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table> 
      </td>

      <td> 

      <table class=dataElementTbl id=elemPR>
               <tr>
                 <td class=elementLabel>Pathology Report</td></tr>
               <tr>
                 <td class="dataElement">
                   {$pRptDsp}

                 </td>
               </tr>
      </table> 

      </td>

      <td>  
      <table class=dataElementTbl id=elemSbj><tr><td class=elementLabel>Subject :: Protocol Numbers</td></tr><tr><td class=dataElement><div class=commentHolder onclick="generateDialog('dlgEDTENC','{$bg['pxiid']}::{$bg['bgnbr']}');">{$bg['subjectnbr']} :: {$bg['protocolnbr']} &nbsp;<div class=basicEditIcon><i class="material-icons cmtEditIconCls">edit</i></div></div></td></tr></table> 
      </td>   

      <td align=right>  
      <table class=dataElementTbl id=elemIC><tr><td class=elementLabel>Consent</td></tr><tr><td class=dataElement>{$bg['icind']}&nbsp;</td></tr></table> 
      </td>      
   
 </tr></table>
         
</td></tr>            
NEXTLINE;
//END PHI LINE

//BG METRICS 
$rtnThis .= <<<NEXTLINETWO
<tr><td>
  

<table border=0 width=100%>
  <tr>

      <td>  
      <table class=dataElementTbl id=elemBGCmnt><tr><td class=elementLabel>Biosample Comments</td></tr><tr>
          <td class=dataElementc>
          <div class=commentHolder onclick="generateDialog('dlgCMTEDIT', 'BGC:{$bg['bgnbr']}');">
             <div class=commentdsp style="width: 100%; height: 8vh;">
                {$bg['biosamplecomment']}                
             </div>
             <div class=cmtEditIcon><i class="material-icons cmtEditIconCls">edit</i></div>   
          </div>
          </td></tr></table> 
      </td>

      <td align=right>  
      <table class=dataElementTbl id=elemBGCmnt><tr><td class=elementLabel>Question for HPR/QMS Reviewer</td></tr><tr>
          <td class=dataElementc>
          <div class=commentHolder onclick="generateDialog('dlgCMTEDIT', 'HPQ:{$bg['bgnbr']}');">
             <div class=commentdsp style="width: 100%; height: 8vh;">
                {$bg['questionhpr']}                
             </div>
             <div class=cmtEditIcon><i class="material-icons cmtEditIconCls">edit</i></div>   
          </div>
          </td></tr></table> 
      </td>                
                    
 </tr></table>
</td></tr>            
NEXTLINETWO;

if ( strtoupper($bg['qcprocstatus']) === 'Q' ) {                 
  $qdta = array();  
  $qdta['bgency'] = cryptservice($bg['readlabel'],'e');                
  $qpassdata = json_encode($qdta);                
  $bgqarr = json_decode(callrestapi("POST",dataTree."/data-doers/master-bg-qms",serverIdent,serverpw,$qpassdata), true);  
  if ( count($bgqarr['DATA']['molecular']) > 0 || count($bgqarr['DATA']['percents']) > 0 ) {     
    if ( count($bgqarr['DATA']['molecular']) > 0 ) { 
        //BUILD MOLECULAR TESTS
        $moleTbl = "<td valign=top><table border=0 cellspacing=0 cellpading=0 style=\"border: 1px solid rgba(0,32,113,1);\"><tr><td colspan=4 class=elementLabel>Molecular Tests &amp; Other Test Results</td></tr><tr><td><table><tr><td class=topHeadCell>Specified Test</td><td class=topHeadCell>Test Results</td><td class=topHeadCell>Scale Degree/Note</td><td class=topHeadCell style=\"border-right: none;\">Input On/By</td></tr>";
        foreach ($bgqarr['DATA']['molecular'] as $mval) { 
            $moleTbl .= "<tr><td data-moleid={$mval['molecularid']} class=moletestdatadsp>{$mval['moletest']}</td><td class=moletestdatadsp>{$mval['testreslt']}</td><td class=moletestdatadsp>{$mval['molenote']}</td><td class=moletestdatadsp style=\"border-right: none;\">{$mval['inputdate']} {$mval['onby']}</td></tr>";
        }
        $moleTbl .= "</table></td></tr></table></td>";
    } 
    if ( count($bgqarr['DATA']['percents']) > 0 ) { 
        //BUILD PERCENTS        
        $percentTbl = "<td valign=top><table border=0  cellspacing=0 cellpading=0 style=\"border: 1px solid rgba(0,32,113,1);\"><tr><td class=elementLabel colspan=10>Biosample Composition</td></tr>";
        $trcellcntr = 0;
        foreach ( $bgqarr['DATA']['percents'] as $pval ) { 
            if ( $trcellcntr === 4) { 
              $percentTbl .= "</tr><tr>";
              $trcellcntr = 0;
            }
            $innerCompTbl = "<table border=0 data-compid={$pval['prcid']} class=compDspTbl><tr><td class=topHeadCell style=\"border-right: 1px solid rgba(0,32,113,1);\">{$pval['prctype']}</td></tr><tr><td  class=moletestdatadsp style=\"text-align: right;\">{$pval['prcvalue']} %</td></tr></table>";
            $percentTbl .= "<td valign=top>{$innerCompTbl}</td>";                     
            $trcellcntr++;
        }
        $percentTbl .= "</table></td>";
    }
    $rtnThis .= "<tr><td valign=top>"; 
    
    $qmnote = ( trim($bgqarr['DATA']['qmsnote'][0]['qmsnote']) !== "") ? trim($bgqarr['DATA']['qmsnote'][0]['qmsnote']) : ""; 
    $qmnoteby = ( trim($bgqarr['DATA']['qmsnote'][0]['qmsstatusby']) !== "" ) ? "<p>(" . trim($bgqarr['DATA']['qmsnote'][0]['qmsstatusby']) : "";
    $qmnoteby .= ( trim($bgqarr['DATA']['qmsnote'][0]['qmsstatuson']) !== "" ) ?  ( trim($qmnoteby) === "" ) ?  "<p>(" . trim($bgqarr['DATA']['qmsnote'][0]['qmsstatuson'])   :  " :: "  . trim($bgqarr['DATA']['qmsnote'][0]['qmsstatuson'])  : "";
    $qmnoteby .= ( trim($qmnoteby) === "") ? "" : ")";  
    
    $rtnThis .= "<table border=0 ><tr>{$percentTbl}{$moleTbl}</tr><tr><td colspan=2><table  cellspacing=0 cellpading=0 style=\"border: 1px solid rgba(0,32,113,1);\" width=100%><tr><td  class=elementLabel>QMS Note</td></tr><tr><td colspan=2 style=\"padding: 8px;\">{$qmnote}&nbsp;<div style=\"text-align: right; font-size: 1.1vh; font-style: italic;\">{$qmnoteby}</div></td></tr></table></table>" ;
    $rtnThis .= "</td></tr>";
  }
}

//END BG METRICS
      
      //"reconcilind":0
      //"hprblockind":0,"slidegroupid":""}]
      $segTbl = "<table border=0 id=segmentListTbl><thead><tr><td class=seg-lbl>#</td><td>Segment<br>Status</td><td>Preparation</td><td class=seg-hrp><center>Hours<br>Post</td><td class=seg-metr>Metric</td><td class=seg-procdte>Procurement<br>Date</td><td>Processed<br>Institution</td><td class=seg-cuttech>Processing<br>Technician</td><td class=seg-qty>Qty</td><td>Assignment</td><td class=seg-rqst>Request</td><td class=seg-shpdoc>Ship Doc</td><td class=seg-shpdte>Shipped</td><td>Comments</td><td>Inventory<br>Location</td></tr></thead><tbody>";
      foreach ($bg['segments'] as $sky => $svl) { 
          
          $prp = ( trim($svl['prepmethod']) !== "" ) ? "{$svl['prepmethod']}" : "";
          $prp .= ( trim($svl['preparation']) !== "" ) ?  ( trim($prp) !== "" ) ? " :: {$svl['preparation']}" : "{$svl['preparation']}"  :  "";
          $metric = ( (int)$svl['metric'] <> 0 ) ? "{$svl['metric']}{$svl['muom']}" : "";
          $segststbl = "<div class=hovertbl><table border=0><tr><td>Status Date: </td><td>{$svl['statusdate']}&nbsp;</td></tr><tr><td>Statused By: </td><td>{$svl['statusby']}&nbsp;</td></tr></table></div>";

          $assignment = ( trim($svl['investid']) !== "" ) ? trim($svl['investid']) : "";
          $assignment .= ( trim($svl['iname']) !== "," ) ? ( trim($assignment) !== "" ) ? " ({$svl['iname']})" : "{$svl['iname']}" : "";
          $innerAss = ( trim($svl['assigneddate']) !== "" ) ? "<div class=segstatusinfo><div class=hovertbl><table><tr><td>Assigned Date: </td><td>{$svl['assigneddate']}</td></tr><tr><td>Assigned By: </td><td>{$svl['assignedby']}</td></tr></table></div></div>" : ""; 
          $assdiv = "<div class=segstatusdspinfo>{$assignment}{$innerAss}</div>";


          $sdencry = ( trim($svl['shipdocref']) !== "" ) ? cryptservice($svl['shipdocref']) : "";
          $ship = ( trim($svl['shipdocref']) !== "000000" ) ? "<div class=sdttholder>" . substr(('000000' . $svl['shipdocref']),-6) . "<div class=tt><div onclick=\"displayShipDoc(event,'{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">print</i> Print Ship-Doc (" . substr(('000000' . $svl['shipdocref']),-6) . ")</div><div onclick=\"navigateSite('shipment-document/{$sdencry}');\" class=quickLink><i class=\"material-icons qlSmallIcon\">edit</i> Edit Ship-Doc (" . substr(('000000' . $svl['shipdocref']),-6) . ")</div></div>" : "";
          $ship .= ( trim($svl['shippeddate']) !== "" ) ? "<br><span class=smlFont>[{$svl['shippeddate']}]</font>" : "";
          //$shipdoc = ( trim($svl['shipdocref']) !== "000000" ) ? trim($svl['shipdocref']) : "";

          $reqency = ( trim($svl['assignedrequest']) !== "" ) ? "<div class=assttholder>{$svl['assignedrequest']}<div class=tt><div onclick=\"generateDialog('irequestdisplay','" . cryptservice($svl['assignedrequest']) . "');\" class=quickLink>View Request {$svl['assignedrequest']}</div></div></div>" : ""; 


          $scnDsp = ( trim($svl['scannedlocation']) !== "" ) ? "<div class=scnstatusdspinfo>{$svl['scannedlocation']}<div class=scnstatusinfo><div class=hovertbl><table><tr><td>Scanned: </td><td>{$svl['scannedby']}</td></tr><tr><td>Status: </td><td>{$svl['scannedstatus']}</td></tr><tr><td>Date: </td><td>{$svl['scanneddate']}</td></tr></table></div></div></div>" : "&nbsp;";

          $sgcmt = preg_replace('/[Ss][Ss][Vv]\d(SEGMENT|segment)\s(COMMENTS|comments)-{1,}/','',$svl['segmentcomments']);
     
          $segTbl .= <<<SEGMENTLINES
                  <tr
                    id = "sg{$svl['segmentid']}"
                    data-bgs = "{$svl['bgs']}"
                    data-shipdoc = "{$shipdoc}"
                    data-segmentid = {$svl['segmentid']}
                    data-selected = "false"
                    onclick="rowselector('sg{$svl['segmentid']}');" 
                  >
                      <td class=seg-lbl>{$svl['segmentlabel']}&nbsp;</td>
                      <td><div class=segstatusdspinfo>{$svl['segstatus']}<div class=segstatusinfo>{$segststbl}</div></div></td>
                      <td>{$prp}&nbsp;</td>                      
                      <td class=seg-hrp align=right>{$svl['hourspost']}&nbsp;</td>
                      <td class=seg-metr align=right>{$metric}&nbsp;</td>
                      <td class=seg-procdte>{$svl['procurementdate']}&nbsp;</td>
                      <td class=seg-cuttech>{$svl['dspinstitution']}&nbsp;</td>     
                      <td>{$svl['cuttech']}&nbsp;</td>                          
                      <td class=seg-qty>{$svl['qty']}&nbsp;</td>
                      <td>{$assdiv}</td>
                      <td class=seg-rqst>{$reqency}&nbsp;</td>
                      <td class=seg-shpdoc>{$ship}&nbsp;</td>
                      <td class=seg-shpdte>{$svl['shippeddate']}&nbsp;</td>
                      <td>{$sgcmt}&nbsp;</td>
                      <td class="endCell ">{$scnDsp}</td>
                  </tr>
SEGMENTLINES;
      }
      $segTbl .= "</tbody></table>";

      $assCount = count($bg['associativegroup']);
      $outerAss = "";
      if ( (int)$assCount > 0 ) { 
          //BUILD THE ASS TABLE
         if ( (int)$assCount < 2 ) { 
            $headline = $assCount . " Other biogroup in Associative Group";
         } else { 
            $headline = $assCount . " Other biogroups in Associative Group";
         }
         $innerAss = "<table border=0 cellpadding=0 cellspacing=0 id=innerAssDspTbl><thead><tr><th colspan=2></th><th>Biosample<br>Label</th><th>Specimen<br>Category</th><th>Site</th><th>Diagnosis</th><th>Mets<br>From Site</th><th>Systemic</th><th>QMS<br>Status</th><th>HPR<br>Decision</th></thead><tbody>";
         foreach ( $bg['associativegroup'] as $aky => $avl ) { 
           $hprcompicon = ( (int)$avl['hprind'] === 1 ) ? "<i class=\"material-icons hpricon\" onclick=\"generateDialog('datacoordhprdisplay',{$avl['hprresult']});\">offline_pin</i>" : "&nbsp;";
           $qmscompicon = ( (int)$avl['qcind'] === 1 ) ? "<i class=\"material-icons hpricon\" onclick=\"alert('datacoordQCdisplay');\">stars</i>" : "&nbsp;";
   
           $newBGEncy = cryptservice( $avl['pbiosample'], 'e') ;
  
             $innerAss .= <<<ASSROW
<tr ondblclick="navigateSite('biogroup-definition/{$newBGEncy}');">
  <td style="width: 1vw; text-align:center;">{$hprcompicon}</td>
  <td style="width: 1vw; text-align:center;">{$qmscompicon}</td>
  <td>{$avl['readlabel']}&nbsp;</td>
  <td>{$avl['specimencategory']}&nbsp;</td>
  <td>{$avl['site']}&nbsp;</td>
  <td>{$avl['diagnosis']}&nbsp;</td>
  <td>{$avl['metssite']}&nbsp;</td>
  <td>{$avl['systemic']}&nbsp;</td>
  <td>{$avl['qmsstatus']}&nbsp;</td>
  <td>{$avl['hprdecision']}&nbsp;</td>
</tr>
ASSROW;

         }
         $innerAss .= "</tbody></table>";

         $outerAss = "<table width=100%><tr><td id=assLineBiogroupAnnounce>ASSOCIATIVE GROUP</td></tr><tr><td class=smlInfoLine>{$headline} </td></tr><tr><td>{$innerAss}</td></tr></table>";
      }

      $rtnThis .= "<tr><td>{$outerAss}</td></tr>";
      $rtnThis .= "<tr><td>{$segTbl}</td></tr>";                   
                         
      $rtnThis .= "</table>";
  } else { 
      $rtnThis = "<h3>NO BIOGROUP FOUND.  ERROR - SEE A CHTNEASTERN INFORMATICS STAFF MEMBER";
  }

return $rtnThis;

}

