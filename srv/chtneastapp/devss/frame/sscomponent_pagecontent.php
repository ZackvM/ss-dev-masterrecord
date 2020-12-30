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
      case 'moremetrics':
        $pdta = json_decode($passedData, true);
        switch ( $pdta['objid'] ) { 
          case 'procurement':    
            $titleBar = "More Procurement Metric Information"; 
            break;
          case 'qms':    
            $titleBar = "More Quality Management System Metric Information"; 
            break;
          case 'shipping':    
            $titleBar = "More Shipping Metric Information"; 
            break;
          case 'data-inventory':    
            $titleBar = "More Data &amp; Inventory Metric Information"; 
            break;
          case 'environment':    
            $titleBar = "More Environmental Monitor Metric Information"; 
            break;

          default: 
            $titleBar = "More Metric Information"; 
        }
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldMoreMetricInfScreen ( $passedData );
        break;  
      case 'bldHotList':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Edit the Hot-List";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = "ZACK WAS HERE";        
        break;  
      case 'rptExtraManSeg':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Report Extra Segments on Manifest";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldQuickBuildManifest ( $passedData );
        break;  
      case 'shipdocspcsrvfee':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Add Speacial Service Fee";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldSpcSrvSDFee( $passedData );
        break; 
      case 'dialogListManifests':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Manifest Listing";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldInventoryManifestListing ( $passedData );
      break;      
      case 'donorvault':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Donor Vault Access";
        $standardSysDialog = 0;
        $closer = "closeThisMasterDialog('{$pdta['dialogid']}');";         
        $innerDialog = "<p>" . $passedData . "<div id=porthole></div>";
      break;                           
      case 'faSendTicket':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Email Ticket";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldFAEmailer( $passedData );
        break;                   
      case 'rqstLocationBarcode':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Request Location Barcode Placard";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldLocationPlacardRequest ( $pdta['dialogid'], $passedData);
        break;                   
      case 'rqstSampleBarcode':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Request New Biosample Barcode Tag";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldInventoryBarcodeTag ( $pdta['dialogid'], $passedData );        
        break;                   
      case 'chartbldr':
        $pdta = json_decode($passedData, true);         
        $titleBar = "Biogroup Donor Chart Review Builder";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldChartReviewBuilder ( $pdta['dialogid'], $passedData);
      break;                    
      case 'furtheractionperformer':  
        $pdta = json_decode($passedData, true);         
        $titleBar = "Further Action Performed";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldFurtherActionAction( $pdta['dialogid'], $passedData );
      break;        
      case 'qmsInvestigatorEmailer':  
        $pdta = json_decode($passedData, true);         
        $rqstnbr =  cryptservice( $pdta['objid'], 'd');
        $titleBar = "QMS Investigator Emailer";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldQMSInvestigatorEmailer ( $pdta['dialogid'] , $passedData );     
      break;        
      case 'qmsManageMoleTst':
        $pdta = json_decode($passedData, true);         
        $rqstnbr =  cryptservice( $pdta['objid'], 'd');
        $titleBar = "Manage Immuno/Molecular Test Values";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog =  bldImmunoMoleTestValues ( $pdta['dialogid'], $passedData );
      break;
      case 'qmsRestatusSegments':
        $pdta = json_decode($passedData, true);         
        $rqstnbr =  cryptservice( $pdta['objid'], 'd');
        $titleBar = "Status Segments (QMS Mass Update)";
        $standardSysDialog = 0;
        $closer = "closeThisDialog('{$pdta['dialogid']}');";         
        $innerDialog = bldQMSGlobalSegmentUpdate ( $pdta['dialogid'] , $passedData );     
    break;
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
        $titleBar = "HPR Slide Tray Return";
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
      case 'bgRqstFA':
         $pdta = json_decode($passedData, true);   
         if ( trim($pdta['objid']) !== 'xxx-xxxx' ) { 
           $bg = cryptservice( $pdta['objid'] , 'd'); 
           $faArr['requestingmodule'] = "FA-MODULE";
           $faArr['biohpr'] = "";
           $faArr['slidebgs'] = "";
           $faArr['bgreadlabel'] = "";
           $faArr['pbiosample'] = $bg; 
         } else {
           $faArr['requestingmodule'] = "FA-MODULE-GEN";
           $faArr['biohpr'] = "";
           $faArr['slidebgs'] = "";
           $faArr['bgreadlabel'] = "";
           $faArr['pbiosample'] = 'GEN-' . strtoupper(generateRandomString(8));              
         }
         $titleBar = "Request Further Action (Master-Record)";
         $standardSysDialog = 0;
         $closer = "closeThisDialog('{$pdta['dialogid']}');";                 
         $innerDialog = bldFurtherActionDialog ( $pdta['dialogid'] , json_encode( $faArr) );
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
      case 'dialogHelpDocEdit': 
        $titleBar = "ScienceServer Edit SOP/Help Document";
        //$footerBar = "DONOR RECORD";
        $innerDialog = bldHelpDocumentEditDialogBox($passedData);
        break;
      case 'dialogHelpDocNew': 
        $titleBar = "ScienceServer New SOP/Help Document";
        //$footerBar = "DONOR RECORD";
        $innerDialog = bldHelpDocumentNewDialogBox($passedData);
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
        //HPR TRAY   
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
</td><td>
<table class=tblBtn id=btnDialogBank style="width: 6vw;" onclick="sendSegmentAssignment('penddestroy');"><tr><td><center>P-Destroy</td></tr></table>
</td>
<td>
  <table class=tblBtn id=btnDialogBank style="width: 6vw;" onclick="sendSegmentAssignment('permcollect');"><tr><td><center>P-Collection</td></tr></table>
</td>
</tr></table>

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

function useradministration ( $rqststr, $usr ) { 
  require(genAppFiles . "/dataconn/sspdo.zck");     
  $topBtnBar = generatePageTopBtnBar('useradministration', $usr, "" );
    
  $userSideSQL = "SELECT userid, username, emailaddress,  allowind, failedlogins, date_format(passwordexpiredate,'%m/%d/%Y' ) as pwordexpire, primaryinstcode FROM four.sys_userbase where primaryfunction <> 'INVESTIGATOR' order by allowind desc, primaryInstCode, lastname";
  $userSideRS = $conn->prepare( $userSideSQL); 
  $userSideRS->execute();
  $instWriter = "";
  $userSideTbl = "<table border=0 cellpadding=0 cellspacing=0 id=userDisplayTbl><thead><tr><th>User</th><th>Access Allowed</th><th>User Lock-out</th><th>P-word Reset</th><th>P-word Expire</th></tr></thead><tbody>";
  while ( $u = $userSideRS->fetch(PDO::FETCH_ASSOC)) { 
      $uency = cryptservice( $u['emailaddress'], 'e' );
      $preset = "<i class=\"material-icons\" onclick=\"sendResetPassword('{$uency}');\">touch_app</i>";      
      $lck = ( (int)$u['failedlogins'] < 6 ) ? "<i class=\"material-icons\">lock_open</i>" : "<i class=\"material-icons\" onclick=\"sendUnlock('{$uency}');\">lock</i>";      
      $chkd = ( (int)$u['allowind'] === 1 ) ? "CHECKED" : "";
      $allowedInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" class=\"checkboxThreeInput\" id=\"checkbox{$u['userid']}Input\"  {$chkd}  onchange=\"toggleAllow('{$uency} ', this.checked);\"  /><label for=\"checkbox{$u['userid']}Input\"></label></div>";
      if ( $instWriter !== $u['primaryinstcode'] ) {
          $inact = ( $u['allowind'] === 0 ) ? " / INACTIVE" : "";
          $userSideTbl .= "<tr><td colspan=5 class=instWriterDsp><center>{$u['primaryinstcode']}{$inact}</td></tr>";
          $instWriter = $u['primaryinstcode'];
      }     
      $userSideTbl .= "<tr><td onclick=\"getUserSpecifics('{$uency}');\"><div class=uname>{$u['username']}</div><div class=uemail>({$u['emailaddress']})</div></td><td><center>{$allowedInd}</td><td><center>{$lck}</td><td><center>{$preset}</td><td>{$u['pwordexpire']}</td></tr>";      
  }
  $userSideTbl .= "</tbody></table>";

$rtnthis = <<<PGCONTENT
{$topBtnBar}
<div id=pageContentHolder>
   <div id=defineUserSide class=holderDivs>  </div>   
   <div id=userListSide class=holderDivs>{$userSideTbl}</div>
</div>
PGCONTENT;
return $rtnthis;    
}

function astrequestlisting ( $rqststr, $usr ) { 
  $url = explode("/",$_SERVER['REQUEST_URI']);   
  if ( trim($url[2]) !== "" ) { 
      //GET QUERY RESULTS
      //TODO: MAKE THIS A WEBSERVICE!
      require(genAppFiles . "/dataconn/sspdo.zck"); 
      $sql = "SELECT objid, bywho, date_format(onwhen,'%m/%d/%Y %H:%i') as onwhen, srchterm, doctype FROM four.objsrchdocument where objid = :objid and doctype = 'ASTREQ'";
      $rs = $conn->prepare($sql); 
      $rs->execute(array(':objid' => $url[2]));
      if ( $rs->rowCount() === 1 ) { 
          $r = $rs->fetch(PDO::FETCH_ASSOC);
          $s = json_decode($r['srchterm'],true);

          //TODO: MAKE THIS A WEBSERVICE! 
          $tqreqSQL = "
              SELECT rq.req_status reqstatus
              , rq.requestid
              , rq.req_number reqnumber
              , ifnull(rq.req_status,'') as reqstatus
              , rq.req_networked as networked
              , ifnull(rq.req_tissuetype,'') as tissuetype
              , ifnull(rq.req_anasitetype,'') as anasitetype
              , ifnull(rq.req_subsite,'') as subsite
              , ifnull(rq.req_subtype,'') as subtype
              , ifnull(rq.req_histologictype,'') as histologictype
              , ifnull(rq.req_diseasename,'') as diseasename
              , ifnull(rq.req_diseaseclass,'') as diseaseclass
              , ifnull(rq.req_diseasequalifier,'') as diseasequalifier
              , ifnull(rq.req_patienthx,'') as patienthx
              , ifnull(rq.req_hasmet,'') as hasmet
              , ifnull(rq.req_hasbf,'') as hasbf
              , ifnull(rq.req_hasnat,'') as hasnat
              , ifnull(rq.req_hasother,'') as hasother
              , ifnull(rq.req_hassolid,'') as hassolid
              , ifnull(rq.req_normalfromcapt,'') as normalfromcapt
              , ifnull(rq.req_normalfromdzpt,'') as normalfromdzpt
              , ifnull(rq.req_normalfromhtpt,'') as normalfromhtpt
 
              , ifnull(rq.req_surgery,'') as rqsurgery
              , ifnull(rq.req_postexcisiontime,'') as rqsurgerytime
              , ifnull(rq.req_surgeryunit,'') as rqsurgerytimeunit

              , ifnull(rq.req_autopsy,'') as rqautopsy
              , ifnull(rq.req_postmortemtime,'') as rqautopsytime
              , ifnull(rq.req_autopsyunit,'') as rqautopsyunit

              , ifnull(rq.req_transplant,'') as rqtansplant
              , ifnull(rq.req_posttransplanttime,'') as rqtranstime
              , ifnull(rq.req_transplantunit,'') as rqtransunit

              , ifnull(rq.req_phlebotomy,'') as rqphlm
              , ifnull(rq.req_postphltime,'') as rqphlmtime
              , ifnull(rq.req_phlebotomyunit,'') as rqphlmunit

              , ifnull(rq.req_trauma,'') as trauma
              , ifnull(rq.req_posttraumatime,'') as traumatime
              , ifnull(rq.req_traumaunit,'') as traumaunit

              , ifnull(rq.req_race,'') as ptrace
              , ifnull(rq.req_gender,'') as ptsex
              , ifnull(rq.req_agemin1,'') as ptagemin1
              , ifnull(rq.req_agemax1,'') as ptagemax1
              , ifnull(rq.req_ageunit1,'') as ptageunit1
              , ifnull(rq.req_agemin2,'') as ptagemin2
              , ifnull(rq.req_agemax2,'') as ptagemax2
              , ifnull(rq.req_ageunit2,'') as ptageunit2

              , ifnull(rq.req_chemoyn,'') as chemoind
              , ifnull(rq.req_radyn,'') as radind

              , ifnull(rq.req_tissuecomment,'') as tissuecomment  
              , ifnull(td.tissueid,'') as tissueid
              , ifnull(td.tis_required,'') as tdrequired
              , ifnull(td.tis_anyall,'') as anyall
              , ifnull(td.tis_histotype,'') histotype
              , ifnull(td.tis_tissuetype,'') as speccat
              , ifnull(td.tis_anasitetype,'') as asite
              , ifnull(td.tis_subsite,'') as tdsubsite
              , ifnull(td.tis_subsitequalifier,'') as tdsubsitequalifier
              , ifnull(td.tis_subtype,'') as tdsubtype
              , ifnull(td.tis_hasuninvolved,'') as hasuninvolved
              , ifnull(tp.prep_preptype,'') as prepdettype
              , ifnull(tp.prep_grouptype,'') as grptype
              , ifnull(tp.prep_required,'') as preprequired

              , ifnull(tp.prep_amount,'') as prepamount
              , ifnull(tp.prep_amountunit,'') as prepamountunit
              , ifnull(tp.prep_sizeh,'') as prepsizeh
              , ifnull(tp.prep_sizel,'') as prepsizel
              , ifnull(tp.prep_sizew,'') as prepsizew
              , ifnull(tp.prep_sizeunit,'') as prepsizeunit

              , pr.projid
              , i.investid
              , concat(ifnull(i.invest_fname,''),' ',ifnull(i.invest_lname,'')) as investigator
              , ifnull(i.invest_homeinstitute,'') as institution
              , ifnull(i.invest_division,'') as idivision
              , ifnull(i.invest_institutiontype,'') as insttype
              , ifnull(i.invest_networked,'') as inetworked
              , ifnull(pr.proj_title,'') as projtitle 
              FROM vandyinvest.investtissreq rq 
                left join vandyinvest.eastern_tissuedetail td on rq.requestid = td.requestid 
                left join vandyinvest.eastern_tissueprep tp on td.tissueid = tp.tissueid 
                left join vandyinvest.investproj pr on rq.projid = pr.projid 
                left join vandyinvest.invest i on pr.investid = i.investid 
              where 1= 1  ";
          
          
          if ( trim($s['RQStatus']) === 'All Requests' )  { 
          } else { 
              $tqreqSQL .= " and rq.req_status = :reqstatus ";
              $qryArr[':reqstatus'] = 'Active';
          }

          if ( trim($s['SPCTerm']) === 'Any' || trim($s['SPCTerm']) === "" ) { 
          } else { 
              $tqreqSQL .= " and rq.req_tissuetype = :reqspc ";
              $qryArr[':reqspc'] = trim($s['SPCTerm']);
          }

          if ( trim($s['investid']) !== "" ) { 
              $tqreqSQL .= " and i.investid = :icode ";
              $qryArr[':icode'] = trim($s['investid']);
          }
          
          if ( trim($s['SearchTerm']) === '') { 
          } else { 
              $tqreqSQL .= " and ( ( ifnull(rq.req_anasitetype,'') like :sta) or (ifnull(rq.req_subsite,'') like :stb) or (ifnull(rq.req_subtype,'') like :stc) or (ifnull(rq.req_histologictype,'') like :std) or (ifnull(rq.req_diseasename,'') like :ste) or (ifnull(rq.req_diseaseclass,'') like :stf) or (ifnull(rq.req_diseasequalifier,'') like :stg) or (ifnull(rq.req_tissuecomment,'') like :sth) or (ifnull(td.tis_anasitetype,'') like :sti) or (ifnull(td.tis_subsite,'') like :stj) or (ifnull(td.tis_subsitequalifier,'') like :stk) or (ifnull(td.tis_subtype,'') like :stl)) ";
              $qryArr[':sta'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stb'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stc'] = trim($s['SearchTerm']) . '%';
              $qryArr[':std'] = trim($s['SearchTerm']) . '%';
              $qryArr[':ste'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stf'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stg'] = trim($s['SearchTerm']) . '%';
              $qryArr[':sth'] = '%' . trim($s['SearchTerm']) . '%';
              $qryArr[':sti'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stj'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stk'] = trim($s['SearchTerm']) . '%';
              $qryArr[':stl'] = trim($s['SearchTerm']) . '%';
          }

          if ( trim($s['preparation']) === '' ) { 
          } else { 
            $tqreqSQL .= " and tp.prep_grouptype = :prpgrptype ";
            $qryArr[':prpgrptype'] = $s['preparation'];
          }

          $tqreqSQL .= " order by rq.requestid, rq.req_number, td.tissueid";          
          $tqreqRS = $conn->prepare($tqreqSQL); 
          $tqreqRS->execute($qryArr);
          if ( $tqreqRS->rowCount() < 1 ) { 
            $pcontent = "NO TissueQuest Requests Have Been Found! ";
          } else {

            $rq = $tqreqRS->fetchall(PDO::FETCH_ASSOC); 
            $rqnbr = "";
            $requestCntr = 0;
            $pcontent .= "";     
            $subtblind = 0;
            $tissueid = "";
            $tsubtblind = 0; 
            $lastSQL = "SELECT date_format(shippedDate,'%m/%d/%Y') as dspshippeddate, substr(concat('000000',ifnull(shipDocRefID,'')),-6) as shipdocrefid FROM masterrecord.ut_procure_segment where (shippeddate between concat(date_format(now(),'%Y'),'-01-01') and now()) and assignedreq = :requestid order by shippeddate desc limit 1";
            $lastRS = $conn->prepare($lastSQL); 
            foreach ( $rq as $key => $value ) {
              //CHECK LAST SHIPPED SQL
              //
              if ( $rqnbr !== $value['requestid'] ) { 
                if ( $tsubtblind === 1) { 
                  $pcontent .= "</table>";  
                }
                if ( $subtblind === 1 ) {   
                  $pcontent .= "</table></td></tr></table></div>";  
                  $tsubtblind = 0; 
                }

                //$pcontent .= "<td valign=top><div class=label>Project Title</div><div class=data>{$value['projtitle']}</div></td>";
                $spc = ( trim($value['tissuetype']) !== "" ) ? strtoupper($value['tissuetype']) : "";
                $siteline = ( trim($value['anasitetype']) !== "" ) ? strtoupper(trim($value['anasitetype'])) : "";
                $siteline .= ( trim($value['subsite']) !== "" ) ?  ( $siteline !== "" ) ? strtoupper( " [{$value['subsite']}]") : strtoupper("[{$value['subsite']}]") : "";
                $siteline .= ( trim($value['subtype']) !== "" ) ? ( $siteline !== "" ) ? strtoupper(" :: {$value['subtype']}") : strtoupper("{$value['subtype']}") : "";
                $dx = ( trim($value['diseasename']) !== "" ) ? $value['diseasename'] : "";
                $dx .= ( trim($value['diseaseclass']) !== "" ) ? ( $dx === "" ) ? $value['diseaseclass'] : " / " . $value['diseaseclass'] : "";
                $dx .= ( trim($value['diseasequalifier']) !== "" ) ? ( dx === "" ) ? $value['diseasequalifier'] : " [{$value['diseasequalifier']}]" : "";
                $tcmts = ( trim($value['tissuecomment']) !== "" ) ? $value['tissuecomment'] : "";
                $sts = ( trim($value['reqstatus']) !== "" ) ? $value['reqstatus'] : "";
                $srgy  = ( trim($value['rqsurgery']) == 'Yes' ) ? ( trim($value['rqsurgerytime']) !== "" ) ? "{$value['rqsurgerytime']} {$value['rqsurgerytimeunit']}" : "Yes" : "No";
                $autop = ( trim($value['rqautopsy']) == 'Yes' ) ? ( trim($value['rqautopsytime']) !== "" ) ? "{$value['rqautopsytime']} {$value['rqautopsyunit']}" : "Yes" : "No";
                $transp = ( trim($value['rqtransplant']) == 'Yes' ) ? ( trim($value['rqtranstime']) !== "" ) ? "{$value['rqtranstime']} {$value['rqtransunit']}" : "Yes" : "No";
                $phlm = ( trim($value['rqphlm']) == 'Yes' ) ? ( trim($value['rqphlmtime']) !== "" ) ? "{$value['rqphlmtime']} {$value['rqphlmunit']}" : "Yes" : "No";
                $trma = ( trim($value['trauma']) == 'Yes' ) ? ( trim($value['rqtraumatime']) !== "" ) ? "{$value['rqtraumatime']} {$value['rqtraumaunit']}" : "Yes" : "No";
                $agedsp = ( trim($value['ptagemin1']) !== "" ) ? $value['ptagemin1'] : "";
                $agedsp .= ( trim($value['ptagemax1']) !== "" ) ? ( trim($agedsp) !== "") ? "-{$value['ptagemax1']}" : $value['ptagemax1'] : "";
                $agedsp .= ( trim($value['ptageunit1']) !== "" ) ? ( trim($agedsp) !== "") ? " {$value['ptageunit1']}" : "" : "";
                $agedsp .= ( trim($value['ptagemin2']) !== "" ) ? ( trim($agedsp) !== "") ? " / {$value['ptagemin2']}" : $value['ptagemin1'] : "";
                $agedsp .= ( trim($value['ptagemax2']) !== "" ) ? ( trim($agedsp) !== "") ? "-{$value['ptagemax2']}" : $value['ptagemax2'] : "";
                $agedsp .= ( trim($value['ptageunit2']) !== "" ) ? ( trim($value['ptagemin2']) !== "") ? " {$value['ptageunit2']}" : "" : "";
                $cxrx = ( trim($value['chemoind']) !== "" ) ? "{$value['chemoind']}" : "-";
                $cxrx .= ( trim($value['radind']) !== "" ) ? " / {$value['radind']}" : " / -";


                $lastRS->execute(array(':requestid' => $value['requestid']));
                $lastdsp = "[NOT SERVED THIS YEAR]";
                if ( $lastRS->rowCount() > 0 ) { 
                  $last = $lastRS->fetch(PDO::FETCH_ASSOC);
                  $lastdsp = $last['dspshippeddate'];
                  $lastdsp .= ( trim($last['shipdocrefid']) !== "" ) ? ( trim($lastdsp) !== "" ) ? " ({$last['shipdocrefid']})" : $last['shipdocrefid'] : "";
                }

                $pcontent .= "<div class=requestholdrdiv><table border=0 class=requestholdr><tr><td class=rqstdsp valign=top colspan=6>Request {$value['requestid']}</td></tr><tr>"; 
                $pcontent .= "<td class=rqststat valign=top><div class=label>Request Status</div><div class=data>{$sts}&nbsp;</div></td>";
                $pcontent .= "<td class=rqststat valign=top><div class=label>Last Served This Year</div><div class=data>{$lastdsp}&nbsp;</div></td>";
                $pcontent .= "<td class=otherfld valign=top><div class=label>Specimen-Category</div><div class=data>{$spc}&nbsp;</div></td>";
                $pcontent .= "<td class=otherfld valign=top><div class=label>Site [Subsite] :: Sub-Type</div><div class=data>{$siteline}&nbsp;</div></td>";
                $pcontent .= "<td class=otherfld valign=top><div class=label>Histologic Type</div><div class=data>{$value['histologictype']}&nbsp;</div></td>";
                $pcontent .= "<td class=otherfld valign=top><div class=label>Disease Diagnosis</div><div class=data>{$dx}&nbsp;</div></td>";
                $pcontent .= "</tr></table>";

                $pcontent .= "<table border=0 class=requestholdr><tr>";  
                $pcontent .= "<td valign=top class=rqststat><div class=label>Invest</div><div class=data>{$value['investid']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqststat><div class=label>Project</div><div class=data>{$value['projid']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=otherfld><div class=label>Investigator</div><div class=data>{$value['investigator']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=otherfld><div class=label>Division / Networked</div><div class=data>{$value['idivision']} / {$value['inetworked']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqstcmts><div class=label>Institution (Type)</div><div class=data>{$value['institution']} ({$value['insttype']})&nbsp;</div></td>";
                $pcontent .= "<td class=rqstcmts valign=top><div class=label>Comments</div><div class=data>{$tcmts}&nbsp;</div></td>";  

                $pcontent .= "</tr></table>";

                $pcontent .= "<table class=requestholdr><tr>"; 
                $pcontent .= "<td valign=top class=rqststat><div class=label>Surgery</div><div class=data><center>{$srgy}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqststat><div class=label>Autopsy</div><div class=data><center>{$autop}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqststat><div class=label>Transplant</div><div class=data><center>{$transp}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqststat><div class=label>Phlembotomy</div><div class=data><center>{$phlm}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=rqststat><div class=label>Trauma</div><div class=data><center>{$trma}&nbsp;</div></td>"; 
                $pcontent .= "<td valign=top class=otherfld><div class=label>Age</div><div class=data>{$agedsp}&nbsp;</div></td>";
                $pcontent .= "<td valign=top><div class=label>Race</div><div class=data>{$value['ptrace']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=otherfld><div class=label>Sex</div><div class=data>{$value['ptsex']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top><div class=label>Cx/Rx</div><div class=data><center>{$cxrx}&nbsp;</div></td>";
                $pcontent .= "</tr></table>";



                $rqnbr = $value['requestid'];
                $requestCntr++;
                $subtblind = 1;
         
                $pcontent .= "<tr><td colspan=7>  <table border=0 class=tisholdrtbl>";
                $pthxtxt = "";
                $pthxdspind = 0; 
              }

              if ( $tissueid !== $value['tissueid'] ) {

     
                $colorArray = getColor( mt_rand(100000,999999)  );
                $pbSqrBgColor = " style=\"background: rgba({$colorArray[0]}, {$colorArray[1]}, {$colorArray[2]},1); \" ";

                if ($pthxtxt !== "<div class=label>Patient History</div><div class=data>{$value['patienthx']}&nbsp;</div>" ) { 
                    $pthxtxt =   "<div class=label>Patient History</div><div class=data>{$value['patienthx']}&nbsp;</div>"; 
                    $pthxdspind = 1;
                } else { 
                    $pthxdspind = 0;
                }

                $pthxdsp = ( $pthxdspind === 1 ) ? $pthxtxt : "";

                $tddesignation = trim($value['speccat']);
                $tddesignation .= ( trim($value['asite']) !== "" ) ? ($tddesignation !== "") ? " / {$value['asite']}" : "{$value['asite']}" : "";
                  $ss = ( trim($value['tdsubsite']) !== "" ) ? $value['tdsubsite'] : ""; 
                  $ssq = ( trim($value['tdsubsitequalifier']) !== "" ) ? $value['tdsubsitequalifier'] : "";
                  $ssq .= ( trim($value['tdsubtype']) !== "" ) ? ( trim($ssq) !== "" ) ? " / {$value['tdsubtype']}" : "{$value['tdsubtype']}" : "";
                  $ssq = ( trim($ssq) !== "" ) ? " [{$ssq}]" : "";
                  $tddesignation .= ( trim($ss) !== "" ) ? " ({$ss}){$ssq}" : $ss;
                $tddesignation = strtoupper($tddesignation);

                $pcontent .= ( $tsubtblind === 1 ) ? "</table></td></tr>" : "";
                $pcontent .= "<tr><td rowspan=2 class=linedenoter {$pbSqrBgColor}></td>";
                $pcontent .= "<td valign=top class=nxtlength><div class=label>Sample Required?</div><div class=data>{$value['tdrequired']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top class=semishortfld><div class=label>Biosample Requested </div><div class=data>{$tddesignation}&nbsp;</div></td>";
                //$pcontent .= "<td valign=top class=shortfld><div class=label>Has MET</div><div class=data><center>{$value['hasmet']}</div></td>";
                //$pcontent .= "<td valign=top class=shortfld><div class=label>Has NAT</div><div class=data><center>{$value['hasnat']}</div></td>";
                //$pcontent .= "<td valign=top class=shortfld><div class=label>Has Solid</div><div class=data><center>{$value['hassolid']}</div></td>";
                //$pcontent .= "<td valign=top class=shortfld><div class=label>Has BF</div><div class=data><center>{$value['hasbf']}</div></td>";
                //$pcontent .= "<td valign=top class=shortfld><div class=label>Has Other</div><div class=data><center>{$value['hasother']}</div></td>";
                $pcontent .= "<td valign=top class=shortfld><div class=label>From CA-PT</div><div class=data><center>{$value['normalfromcapt']}&nbsp;</div></td>";
                $pcontent .= "<td valign=top rowspan=2>{$pthxdsp}&nbsp;</td>";


                $pcontent .= "</tr>";
                $pcontent .= "<tr><td colspan=20 style=\"padding: .5vh 0 1vh 1vw;\"><table border=0><tr><td class=medium><div class=label>Required?</div></td><td class=medium><div class=label>Preparation / Method</div></td><td class=medium><div class=label>Amount Requested</div></td></tr>";
                $tissueid = $value['tissueid'];   
                $tsubtblind = 1; 
              }

              if ( trim($value['grptype']) !== "" ) {

                $prepamount = "";  
                $prepamount = trim("{$value['prepamount']} {$value['prepamountunit']}");
                $prepamount .= ( trim($value['prepsizew']) !== "" ) ? ( $prepamount !== "" ) ? " / {$value['prepsizew']}" : "{$value['prepsizew']}" : "";
                $prepamount .= ( trim($value['prepsizeh']) !== "" ) ? ( $prepamount !== "" ) ? "x{$value['prepsizew']}" : "{$value['prepsizew']}" : "";
                $prepamount .= ( trim($value['prepsizel']) !== "" ) ? ( $prepamount !== "" ) ? "x{$value['prepsizel']}" : "{$value['prepsizel']}" : "";
                $prepamount .= ( trim($value['prepsizeunit']) !== "" ) ? ( $prepamount !== "" ) ? " {$value['prepsizeunit']}" : "{$value['prepsizeunit']}" : "";

                $pcontent .= "<tr><td valign=top><div class=data>{$value['preprequired']}&nbsp;</div></td><td valign=top><div class=data>{$value['prepdettype']} ({$value['grptype']})&nbsp;</div></td><td valign=top><div class=data>{$prepamount}&nbsp;</div></td></tr>";
              }
                 
            }
          }

$mainscreen = <<<PGCONTENT
<table id=rqpTbl border=0>
  <tr><td colspan=10 style="font-size: 1.8vh; font-weight: bold; text-align: center;">Search Parameters</td></tr>
  <tr><td valign=top id=rqpSrchA><div class=label>Search By</div><div class=data>{$r['bywho']} {$r['onwhen']}&nbsp;</div></td>
      <td valign=top id=rqpSrchB><div class=label id=rqpStatus>Request Status</div><div class=data>{$s['RQStatus']}&nbsp;</div></td>
      <td valign=top id=rqpSrchC><div class=label>Search Term</div><div class=data>{$s['SearchTerm']}&nbsp;</div></td>
      <td valign=top id=rqpSrchD><div class=label>Specimen Category</div><div class=data>{$s['SPCTerm']}&nbsp;</div></td>
      <td valign=top id=rqpSrchE><div class=label>Investigator</div><div class=data>{$s['investid']}&nbsp;</div></td>
      <td valign=top id=rqpSrchF><div class=label>Preparation</div><div class=data>{$s['preparation']}&nbsp;</div></td></tr>
</table>
<div id=foundline>Request(s) Found: {$requestCntr}</div>

{$pcontent}
PGCONTENT;
      } else { 
        $mainscreen = <<<PGCONTENT
THE QUERY OBJECT IDENTIFIER ({$url[2]}) WAS NOT FOUND.  SEE A CHTNEASTERN INFORMATICS PERSON.
PGCONTENT;
      }
  } else { 
      //BUILD QUERY SCREEN 
      $mainscreen = bldASTLookup();
  }  
$rtnthis = <<<PGCONTENT
{$mainscreen}
PGCONTENT;
return $rtnthis;
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

function inventorymanifest ( $rqststr, $usr ) { 

     
    if ((int)$usr->allowprocure !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE PROCUREMENT SCREEN";        
    } else {
        
       if ( trim($rqststr[2]) !== "" ) { 
           //RUN QUERY - MAKE SURE INSTITUTIONS MATCH 
           require(serverkeys . "/sspdo.zck");
           $rqstSQL = "SELECT bywho, onwhen, srchterm FROM four.objsrchdocument where objid = :objid and doctype = :doctype";
           $rqstRS = $conn->prepare( $rqstSQL ); 
           $rqstRS->execute( array( ':objid' => trim($rqststr[2]), ':doctype' => 'INTERNAL-MANIFEST-REQUEST-QUERY'));
     
           if ( $rqstRS->rowCount() <> 1 ) { 
//             $nowDateS = date('m/d/Y');
//             $nowDateE = date('m/d/Y');
             $nowDateS = "01/01/2012";
             $nowDateE = date('m/d/Y', strtotime( date('m/d/Y') . ' + 1 day'));
             $innerWorkBench = "ERROR:  QUERY OBJECT NOT FOUND (BAD REQUEST STRING)";  
           } else { 
             $rqst = $rqstRS->fetch(PDO::FETCH_ASSOC);
             $srchterm = json_decode( $rqst['srchterm'], true); 
             $nowDateS = $srchterm['startdate'];
             $nowDateE = $srchterm['enddate'];

             $instSQL = "SELECT dspvalue FROM four.sys_master_menus where menu = 'INSTITUTION' and menuValue = :instcode"; 
             $instRS = $conn->prepare( $instSQL );
             $instRS->execute( array( ':instcode' => $srchterm['institution'] ));
             $inst = $instRS->fetch(PDO::FETCH_ASSOC);

             $insmnu = "<input type=hidden id=presentInstValue value=\"{$srchterm['institution']}\">  "
                              . "<input type=text id=presentInst READONLY class=\"inputFld\" value=\"{$inst['dspvalue']}\" style=\"font-size: 1.3vh;\">";

             $sdte = explode('/',$nowDateS);
             $edte = explode('/',$nowDateE);

             $onOfferSQL = "SELECT replace(sg.bgs,'_','') as bgs, concat(ifnull(sg.prepmethod,''),' / ',ifnull(sg.preparation,'')) as preparation, if(ifnull(sg.metric,'') = '','',concat(ifnull(sg.metric,''), uom.dspvalue)) as metric, ifnull(sg.shipDocRefID,'') as shipdocrefid, ifnull(date_format(sg.shippedDate,'%m/%d/%Y'),'') as shipdate, ifnull(sg.manifestnbr,'') as manifestnbr, sg.qty, sg.hourspost, if( (ifnull(sg.assignedto,'') = 'BANK' OR ifnull(sg.assignedto,'') = 'QC'), ifnull(sg.assignedto,''), concat( ifnull(sg.assignedto,''), ' / ', ifnull(sg.assignedreq,''))) as assignment, date_format(sg.procurementdate,'%m/%d/%Y') as sgProcDate, concat( pxiAge, '/',  substr(ifnull(bs.pxiRace,''),1,1) , '/', substr(bs.pxiGender,1,1) ) as ars, trim(concat(ifnull(bs.anatomicsite,''),' ', ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' (',ifnull(bs.subdiagnos,''),')')), if(ifnull(bs.tisstype,'')='','',concat(' [',ifnull(bs.tisstype,''),']')))) as dxdesignation FROM masterrecord.ut_procure_segment sg LEFT JOIN masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample LEFT JOIN (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'metric') as uom on sg.metricuom = uom.menuvalue where sg.segStatus = :sgsts and sg.voidind <> 1 and bs.voidind <> 1 and sg.procuredAt = :institution and sg.procurementDate between :startdate and :enddate order by sg.bgs";
             $onOfferRS = $conn->prepare( $onOfferSQL );
             $onOfferRS->execute( array( ':institution' => $srchterm['institution'] , ':startdate' => "{$sdte[2]}-{$sdte[0]}-{$sdte[1]}", ':enddate' => "{$edte[2]}-{$edte[0]}-{$edte[1]}", ':sgsts' => 'ONOFFER'  ));
             $oo = $onOfferRS->fetchAll(PDO::FETCH_ASSOC);

             $ooCount = count( $oo );
             
             $qryParaLine = <<<QRYPARALINE
<div id=queryParaLine>

  <div class=dataElementHolder>
    <div class=dataElementLabel>Query By/On</div>
    <div class=dataElementDsp>{$rqst['bywho']} ({$rqst['onwhen']})</div>
  </div>

  <div class=dataElementHolder>
    <div class=dataElementLabel>Institution</div>
    <div class=dataElementDsp>{$inst['dspvalue']}</div>
  </div>

  <div class=dataElementHolder>
    <div class=dataElementLabel>Date Range</div>
    <div class=dataElementDsp>{$nowDateS}-{$nowDateE}</div>
  </div>

  <div class=dataElementHolder>
    <div class=dataElementLabel align=right>On Offer Total</div>
    <div class=dataElementDsp align=right>{$ooCount}</div>
  </div>



</div>
QRYPARALINE;

             //[{"shipdate":"","qty":1,"hourspost":0.5,"assignment":"INV4956 \/ REQ25434"}
             $offers = "<div id=offerHeader> <div class=oHeaderDsp>CHTN #</div> <div class=oHeaderDsp>Preparation</div> <div class=oHeaderDsp>Metric</div> <div class=oHeaderDsp>A/R/S</div> <div class=oHeaderDsp>Designation</div> <div class=oHeaderDsp>Proc Date</div> <div class=oHeaderDsp>SD #</div> <div class=oHeaderDsp>M #</div>  </div><div id=offerElements>";
             foreach ( $oo as $ky => $vl ) {

               $sddsp = ( trim($vl['shipdocrefid']) === "" ) ? "-" : substr('000000' . $vl['shipdocrefid'], -6);  
               $mdsp = ( trim($vl['manifestnbr']) === "" ) ? "-" : substr( '000000' . $vl['manifestnbr'] , -6);  
                 
               $offers .= <<<RECORD
                <div class=offerRecord id="OFR{$vl['bgs']}" onclick="selectOfferRecord('OFR{$vl['bgs']}');" data-selected="0" data-sglabel="{$vl['bgs']}" >
 
                  <div class=dataElement>{$vl['bgs']}</div>                 
                  <div class=dataElement>{$vl['preparation']}</div>                 
                  <div class=dataElement>{$vl['metric']}</div> 
                  <div class=dataElement>{$vl['ars']}</div> 
                  <div class=dataElement>{$vl['dxdesignation']}</div> 
                  <div class=dataElement>{$vl['sgProcDate']}</div> 
                  <div class=dataElement>{$sddsp}</div> 
                  <div class=dataElement>{$mdsp}</div> 

                </div> 
RECORD;
             }
             $offers .= "</div>";

             $maniDiv = <<<MANIFESTCREATE
<div id=manifestBuildHead>

   <div class=holder><button onclick="getNewManifest();">New</button></div>
   <div class=holder><button onclick="generateDialog('dialogListManifests','xxxx-xxxx');">Get</button></div>
   <div class=holder><button onclick="selectAllRecords();">Toggle Select</button></div>
   <div class=holder><button onclick="addRecordToManifest();">Add</button></div>
   <div class=holder><button onclick="markManifestSend();">Send</button></div>
   <div class=holder><button onclick="printThisManifest();">Print</button></div>

   <div id=mFldDsp>
     <div class=holderlable>Manifest #</div>
     <div class=element><input type=text id=fldManifestNbrDsp READONLY></div>
   </div>


   <div id=manifestMetrics> </div>
  
   <div id=manifestDetailHolder> </div>

</div>

MANIFESTCREATE;

             $innerWorkBench = <<<WRKBENCH
             <div id=onOfferGrid>
               {$qryParaLine}
               {$offers}
             </div>
             
             <div id=manifestDspSide>   
              {$maniDiv}
             </div> 
WRKBENCH;

           }


        } else { 
          //$nowDateS = date('m/d/Y');
          //$nowDateE = date('m/d/Y');
          $nowDateS = "01/01/2012";
          $nowDateE = date('m/d/Y', strtotime( date('m/d/Y') . ' + 1 day'));
          $insmnu = bldUsrAllowInstDrop($usr);
        }

        $fCalendar = buildcalendar('biosampleQueryFrom'); 
        $bsqFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=text READONLY id=bsqueryFromDate class="inputFld" style="width: 18vw;" value="{$nowDateS}"></div>
  <div class=valueDropDown style="width: 18vw;"><div id=bsqCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;

        $tCalendar = buildcalendar('biosampleQueryTo'); 
        $bsqToCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=text READONLY id=bsqueryToDate class="inputFld" style="width: 18vw;" value="{$nowDateE}"></div>
  <div class=valueDropDown style="width: 18vw;"><div id=bsqtCalendar>{$tCalendar}</div></div>
</div>
CALENDAR;

        $topBtnBar = generatePageTopBtnBar('inventorymanifest');

        $rtnthis = <<<PAGEHERE
{$topBtnBar}
<div id=qryDivLineHolder>
  <div class=dataElementHold><div>Present Location</div><div>{$insmnu}</div></div>
  <div class=dataElementHold><div>Start Date</div><div>{$bsqFromCalendar}</div></div>
  <div class=dataElementHold><div>End Date</div><div>{$bsqToCalendar}</div></div>
  <div class=dataElementHold><div>&nbsp;</div><div><table class=tblBtn id=btnRefresh style="width: 6vw;"><tr><td style="font-size: 1.3vh; padding: 1.2vh .5vw;"><center>Refresh</td></tr></table></div></div>
</div>

<div id=dspWorkArea> 
  {$innerWorkBench}
</div>




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
     $tt = treeTop;
     $rq = $rqststr[2];
     //TODO MAKE THIS DYNAMIC
     $selectorcheckin = "";
     $selectormove = "";
     $selectorpull = "";
     $selectorship = "";
     $selectorcount = "";
     $selectordestroy = "";
     $selectorpdestroy = "";
     $selectorinvmedia = "";
     $selectoriman = "";
     $pageTitle = "Inventory Module";
     $pageDetail = "";
     $topBtnBar = generatePageTopBtnBar('inventory');
     switch ( $rq ) { 
       case 'processinventory':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorcheckin = " data-selected='true' ";
         $pageTitle = "Process Biosample Inventory (Check-In/Move)";
         $pageDetail = self::bldInventoryProcessInventory();
         break;
       case 'processhprtray':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorhpr = " data-selected='true' ";
         $pageTitle = "Process HPR Tray";
         $pageDetail = self::bldInventoryProcessHPRTray();
         break;
       case 'processshipment':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorpull = " data-selected='true' ";
         $pageTitle = "Process Biosamples For Shipment (Pull/Ship)";
         $pageDetail = self::bldInventoryProcessShipment();
         break;
       case 'processimanifest':
         $topBtnBar = generatePageTopBtnBar('imanifest');
         $selectoriman = " data-selected='true' ";
         $pageTitle = "Process Intra-CHTNEast Shipment";
         $pageDetail = self::bldInventoryProcessIntraManifest();
         break;
       case 'icount':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorcount = " data-selected='true' ";
         $pageTitle = "Basic Inventory Biosample Count";
         $pageDetail = self::bldInventoryCount();
         break;
       case 'destroybiosamples':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectordestroy = " data-selected='true' ";
         $pageTitle = "Biosamples Being Destroyed";
         $pageDetail = self::bldInventoryDestroy();
         break;
       case 'pendingdestroybiosamples':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorpdestroy = " data-selected='true' ";
         $pageTitle = "Biosamples Mark Pending Destroyed";
         $pageDetail = self::bldInventoryPendingDestroy();
         break;     
       case 'investigatorsupplies':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorinvmedia = " data-selected='true' ";
         $pageTitle = "Scan Investigator Supplies (Media/Kits)";
         $pageDetail = self::bldInventoryIMedia();
         break;
       case 'localpickup':
         $topBtnBar = generatePageTopBtnBar('inventory');
         $selectorlocal = " data-selected='true' ";
         $pageTitle = "Shipment Local Pick-up";
         $pageDetail = "&nbsp;";
         //$pageDetail = self::bldInventoryIMedia();
         break;
     }

     $pageDetail = ( trim($pageDetail) === "" ) ?  self::bldInventoryMaster() : $pageDetail;
/*
 *  PROCESS INVENTORY = MOVE INVENTORY / CHECK-IN AND CHANGE STATUS / PROCESS HPR SLIDE
 *  PROCESS SHIPMENT = GIVE A PULL OR SHIP BUTTON
 */


  $at = genAppFiles;
  $waiticon = base64file("{$at}/publicobj/graphics/zwait2.gif", "waiticongif", "gif", true, "");         

     $rtnthis = <<<RTNTHIS
{$topBtnBar} 
<div id=inventoryMasterHoldr>
<div id=inventoryTitle>{$pageTitle}</div>
<div id=inventoryBtnBar>
   <div class=iControlBtn {$selectorcheckin}><a href="{$tt}/inventory/process-inventory">Process Inventory</a></div>
   <div class=iControlBtn {$selectorhpr}><a href="{$tt}/inventory/process-hpr-tray">Process HPR Tray</a></div>
   <div class=iControlBtn {$selectorpull}><a href="{$tt}/inventory/process-shipment">Process Shipment</a></div>
   <div class=iControlBtn {$selectorlocal}><a href="{$tt}/inventory/local-pickup">Local Pick-up</a></div>    
   <div class=iControlBtn {$selectoriman}><a href="{$tt}/inventory/process-imanifest">Process Intra-Manifest</a></div>
   <div class=iControlBtn {$selectorcount}><a href="{$tt}/inventory/icount">Inventory Count</a></div>
   <div class=iControlBtn {$selectorpdestroy}><a href="{$tt}/inventory/pending-destroy-biosamples">P-Destroy Biosamples</a></div>
   <div class=iControlBtn {$selectordestroy}><a href="{$tt}/inventory/destroy-biosamples">Destroy Biosamples</a></div>
   
 </div>
<div id=inventoryControlPage>{$pageDetail}</div>
</div>

<div id=dspIWait>
<div id=waitMsgTitle> Title </div> 
<div id=waitMsg>WAIT ... </div>
<div id=waitIcon><center>{$waiticon}</div>
</div>

RTNTHIS;
     //<div class=iControlBtn {$selectormove}><a href="{$tt}/inventory/move-biosample">Place Inventory</a></div>
     //<div class=iControlBtn {$selectorship}><a href="{$tt}/inventory/ship-ship">Ship Shipment</a></div>
  } else { 
   $rtnthis = "<h2>USER NOT ALLOWED ACCESS TO INVENTORY";
  }
  return $rtnthis; 
}

function bldInventoryMaster() {

  $at = genAppFiles;
  $crsuffix1900 = base64file("{$at}/publicobj/graphics/heneywell1900CRSuffix.png", "crsuffix1900", "png", true, "");         
  $pageContent = <<<PAGECONTENT
This is the Inventory Module for CHTN-ED's ScienceServer.  Start by choosing an activity on the left. 
<p>
If you are using a Honeywell 1900 barcode scanner, then CR Suffix mode must be turned on.  You can activate this mode by scanning this barcode with your Honeywell 1900 Barcode scanner.  <p>
<center>
{$crsuffix1900} 
PAGECONTENT;
return $pageContent;
}


function bldInventoryProcessHPRTray() {
/*
 * MAKE THIS SCREEN FUNCTION FOR HPR Tray PROCESSING 
 */
    $pageContent = <<<PAGECONTENT
<div id=inventoryCheckinElementHoldr>
  <div id=locationscan>
  <div class=scanfieldlabel>1) Scan HPR Slide-tray location label</div>
  <div id=locscandsp_hprt> 

        <div class=telemhold><div class=tDataLabel>HPR Tray</div><div class=tData id=tlocdisplay>&nbsp;</div></div>
        <div class=telemhold><div class=tDataLabel>Tray Status</div><div class=tData id=tstatus>&nbsp;</div></div>
        <div class=telemhold><div class=tDataLabel>Location</div><div class=tData id=tlocation>&nbsp;</div></div>
        <div class=telemhold><div class=tDataLabel>Review Note (if applicable)</div><div class=tData id=treview>&nbsp;</div></div>

        <div id=telemholda>   </div>

        <div id=putawaylocation><div id=scanAnnounce>2) Scan location where placing slides ...</div><div id=locationplace>    </div></div>

  </div>
  </div>
  <div id=itemCountDsp>&nbsp;</div>
  <div id=labelscan class=scanfieldlabel>3) Scan each slide 'out' of the tray: 
    <div id=hprlabelscanholderdiv>  </div>
  </div>
  <div id=ctlButtons><center> 
   <table><tr><td> <div class=iControlBtn id=ctlBtnHPRTrayCommit style="display: none;"><center>Submit</div> </td><td>  <div class=iControlBtn id=ctlBtnCheckCancel><center>Clear</div> </td></tr></table>
   </div>
</div>
PAGECONTENT;
return $pageContent;
}


function bldInventoryProcessInventory() {
/*
 * MAKE THIS SCREEN FUNCTION FOR CHECK-INs, MOVES PROCESSING 
 */
    $pageContent = <<<PAGECONTENT
<div id=inventoryCheckinElementHoldr> 
  <div id=locationscan> 
  <div class=scanfieldlabel>1) Scan location where placing biosample</div>
  <input type=hidden id=locscancode>
  <div id=locscandsp></div>
  </div>
  <div id=itemCountDsp>SCAN COUNT: 0</div>
  <div id=labelscan>
    <div class=scanfieldlabel>2) Scan Biosample Segment(s). Click segment label to 'delete' from scan list</div>
    <div id=labelscanholderdiv></div>
  </div>
  <div id=ctlButtons><center> 
   <table><tr><td> <div class=iControlBtn id=ctlBtnCheckCommit><center>Submit</div> </td><td>  <div class=iControlBtn id=ctlBtnCheckCancel><center>Cancel</div> </td></tr></table>
   </div>
</div>
PAGECONTENT;
return $pageContent;
}

function bldInventoryProcessIntraManifest() { 

$pageContent = <<<PAGECONTENT
<div id=instrOne class=instructionLabel>1) Scan an intra-CHTN Shipment Manifest</div>
<div id=intraMan>

   <div class=elementholder>
     <div class=elementlabel>Manifest # <input type=hidden id=fldManifestNbr> </div>
     <div class=dataelement id=dspManifestNbr>&nbsp;</div>
   </div>

   <div class=elementholder>
     <div class=elementlabel>Sending Institution</div>
     <div class=dataelement id=dspInstitution>&nbsp;</div>
   </div>

   <div class=elementholder>
     <div class=elementlabel>Created By/Sent By</div>
     <div class=dataelement id=dspagent>&nbsp;</div>
   </div>

   <div class=elementholder>
     <div class=elementlabel>Sent</div>
     <div class=dataelement id=dspsentdate>&nbsp;</div>
   </div>

   <div class=elementholder>
     <div class=elementlabel>Manifest Status</div>
     <div class=dataelement id=dspmanifeststatus>&nbsp;</div>
   </div>

   <div class=elementholder>
     <div class=elementlabel>Segments</div>
     <div class=dataelement id=dspmanifestsegmentcount>&nbsp;</div>
   </div>

</div> 

<div id=instrTwo class=instructionLabel>2) Scan Location </div>

<div id=intraManLoc>

   <div class=elementholder>
     <div class=elementlabel>Check-In Location <input type=hidden id=locscancode></div>
     <div class=dataelement id=locscandsp>&nbsp;</div>
   </div>

</div>

<div id=instrThree class=instructionLabel>3) Scan CHTN #s from Intra-CHTN shipment.  Scanning will mark as received and change CHTN #'s Status </div>

<div id=intraManSegs>

   <div class=elementholder>
     <div class=elementlabel>Manifest's Segment Listing</div>
     <div class=dataelement id=dspSegmentListing>&nbsp;</div>
   </div>

</div>


<div id=instrFour class=instructionLabel>4) If applicable, report manifest errors  </div>
<div id=intraManErrs>
  <button id=btnSegExtraRpt>Extra Biosamples Deviation</button> <button id=btnSegMissingRpt>Report Biosample Segments Missing</button> 
</div>


PAGECONTENT;
return $pageContent;

}



function bldInventoryProcessShipment() {
  

     //{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"details":[{"pulledind":"0","pulledby":"","pulledon":"","segstatusdsp":"On Shipdoc","scanneddate":"02\/11\/2020","scannedStatus":"CHECK IN TO INVENTORY"}

    $pageContent = <<<PAGECONTENT
<div id=workbenchholder>
  <div id=workbench>

    <div id=instrOne class=instructionLabel>1) Scan a CHTN Shipment Document: </div>
    <div id=intraMan>

       <div class=elementholder>
         <div class=elementlabel>Ship-Doc #</div>
         <div class=dataelement id=dspSDShipdoc>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Shipdoc Status</div>
         <div class=dataelement id=dspSDStatus>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Requested Pull Date</div>
         <div class=dataelement id=dspSDPullDate>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Requested Ship Date</div>
         <div class=dataelement id=dspSDShipDate>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Setup Date</div>
         <div class=dataelement id=dspSDSetupDate>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Setup By</div>
         <div class=dataelement id=dspSDSetupBy>&nbsp;</div>
       </div>

    </div>

    <div id=instrTwo class=instructionLabel>2) Pulling or Shipping? </div>
    <div id=intraManOne>
      <div class="checkboxThree"><input type="checkbox" class="checkboxThreeInput" id="checkboxPSInput" onchange="toggleShip(this.checked);" /><label for="checkboxPSInput"></label></div>
      
      <div class=elementholder id=scanlocdspholder>
        <div class=elementlabel>Holding Location <input type=hidden id=locscancode></div>
        <div class=dataelement id=locscandsp>&nbsp;</div>
      </div>

    </div>

    <div id=instrTwo class=instructionLabel>3) Scan CHTN Labels</div>
    <div id=chtnlbllist>&nbsp;</div>


<div id=bttnHoldr><center><button id=submitShipment class=basicButton onclick="submitShipmentRqst();">Submit</button></div>
  </div>
  <div id=sdsidepanel>

       <div class=elementholder>
         <div class=elementlabel>Investigator</div>
         <div class=dataelement id=dspSDInvestigator>&nbsp;</div>
       </div>
       
       <div class=elementholder>
         <div class=elementlabel>Ship Address </div>
         <div class=dataelement id=dspSDShipAddress>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Phone Number </div>
         <div class=dataelement id=dspSDPhone>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>P.O. Nbr</div>
         <div class=dataelement id=dspSDPONbr>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Sales Order</div>
         <div class=dataelement id=dspSDSONbr>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Courier</div>
         <div class=dataelement id=dspSDCourier>&nbsp;</div>
       </div>

       <div class=elementholder>
         <div class=elementlabel>Shipment Comments</div>
         <div class=dataelement id=dspSDShipComments>&nbsp;</div>
       </div>
  </div>

</div>


PAGECONTENT;
return $pageContent;
}

function bldInventoryCount() { 
    $pageContent = <<<PAGECONTENT
<div id=inventoryCheckinElementHoldr>
  <div id=locationscan>
<div id=instructionBlock>Instructions: This screen is for inventory counting.  Simply scan a container box (inventoy location).  This will create a temporary inventory listing in the database which can be reviewed by CHTNEastern Management and Informatics to perform updates to the masterrecord inventory locations. This merely counts what is in the location container box without regard to the status or other locations.  </div>
  <input type=hidden id=locscancode>
  <div class=scanfieldlabel>1) Scanned location that is being counted</div>
  <div id=locscandsp></div>
  </div>
  <div id=itemCountDsp>SCAN COUNT: 0</div>
  <div id=labelscan>
    <div class=scanfieldlabel>2) Scanned biosample segment items. Click segment item to 'Delete' from scan list. Click 'Submit' when finished.</div>
    <div id=labelscanholderdiv></div>
  </div>
  <div id=ctlButtons><center> 
   <table><tr><td> <div class=iControlBtn id=ctlBtnCommitCount><center>Submit</div> </td><td>  <div class=iControlBtn id=ctlBtnCountCancel><center>Cancel</div> </td></tr></table>
   </div>
</div>
PAGECONTENT;
return $pageContent;
}

function bldInventoryDestroy() {
$pageContent = <<<PAGECONTENT
<div id=inventoryCheckinElementHoldr>
  <div id=labelscan>
    <div class=scanfieldlabel>1) scanned biosample labels that are being destroyed. Click biosample label to 'Delete' from scan list</div>
    <div id=labelscanholderdiv></div>
  </div>
  <div id=itemCountDsp>SCAN COUNT: 0</div>
  <div id=ctlButtons><center> 
   <table><tr><td> <div class=iControlBtn id=ctlBtnCommitCount><center>Submit</div> </td><td>  <div class=iControlBtn id=ctlBtnCountCancel><center>Cancel</div> </td></tr></table>
   </div>
</div>
PAGECONTENT;
return $pageContent;
}

function bldInventoryPendingDestroy() {
$pageContent = <<<PAGECONTENT
<div id=inventoryCheckinElementHoldr>
  <div id=labelscan>
    <div class=scanfieldlabel>1) Scanned biosample labels that are being marked 'Pending Destroy'. Click biosample label to 'Delete' from scan list</div>
    <div id=labelscanholderdiv></div>
  </div>
  <div id=itemCountDsp>SCAN COUNT: 0</div>
        
   <div id=IKeyOverride>
        <div id=usrPinOverrideHold>
          <div class=dataLabel>Inventory Pin</div>
          <div><input type=text id=fldUsrInventoryPin READONLY></div>    
   </div>
   <div id=ctlPad>
      <div class=ctlPadBtn onclick="pinme('0');">0</div>
      <div class=ctlPadBtn onclick="pinme('1');">1</div>        
      <div class=ctlPadBtn onclick="pinme('2');">2</div>
      <div class=ctlPadBtn onclick="pinme('3');">3</div>
      <div class=ctlPadBtn onclick="pinme('4');">4</div>
      <div class=ctlPadBtn onclick="pinme('5');">5</div>
      <div class=ctlPadBtn onclick="pinme('6');">6</div>
      <div class=ctlPadBtn onclick="pinme('7');">7</div>
      <div class=ctlPadBtn onclick="pinme('8');">8</div>
      <div class=ctlPadBtn onclick="pinme('9');">9</div>
      <div class=ctlPadBtn onclick="pinme('B');"><i class=material-icons>arrow_back_ios</i></div>          
   </div>     
   </div>    
   
  <div id=ctlButtons><center> 
   <table><tr><td> <div class=iControlBtn id=ctlBtnCommitCount><center>Submit</div> </td><td>  <div class=iControlBtn id=ctlBtnCountCancel><center>Cancel</div> </td></tr></table>
   </div>
</div>
PAGECONTENT;
return $pageContent;
}

function bldInventoryIMedia() { 
    $pageContent = <<<PAGECONTENT
THIS IS THE INVESTIGATOR MEDIA SCREEN HOLDER
PAGECONTENT;
return $pageContent;
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

function furtheractionrequests ( $rqststr, $whichusr ) { 
if ( (int)$whichusr->allowcoord <> 1 ) { 
      $pg = "<h1>USER ({$whichusr->userid}) NOT ALLOWED ACCESS TO COORDINATOR MODULE";    
} else {  
    $r = explode("/", $_SERVER['REQUEST_URI']);  
    if ( trim( $rqststr[2] ) === "" || trim( $rqststr[2] ) === 'sortby'  ) {
        if ( trim( $rqststr[3] ) !== "" ) { 
          $topBtnBar = generatePageTopBtnBar('faTable');
          $rqagent = explode("::", cryptservice ( $r[3], 'd'));
          $pg = bldFurtherActionQueue ( $whichusr, $rqagent );
        } else { 
          $pg = bldFurtherActionQueue ( $whichusr, '' );
          $topBtnBar = generatePageTopBtnBar('faTable');
        }
    } else {
        $topBtnBar = generatePageTopBtnBar('faTableEdit');
        $pg = bldFurtherActionItem($r[2]);

    }
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

function scienceserverhelp ( $rqststr, $whichusr ) {

  $tt = treeTop; 
  $givenSearchTerm = "";  
  $dta = json_decode(callrestapi("GET", dataTree . "/sshlp-topic-list", serverIdent, serverpw),true);
  $t = "<div id=mainHelpFileHolder>";
  foreach ( $dta['DATA'] as $key => $val ) {
    $t .= "<div class=ssHlpModDiv><a href=\"{$tt}/scienceserver-help/{$val['modurlref']}\" class=hlpModuleTbl><div><i class=\"material-icons sideindicon\">keyboard_arrow_right</i></div><div>{$val['module']}</div></a>";
    $t .= "<div class=hldTopicDocList> ";
    if ((int)count($val['topics']) > 0) { 
      foreach ( $val['topics'] as $tky => $tvl ) {
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
        $t .= "<a href=\"{$tt}/scienceserver-help/{$val['modurlref']}/{$tvl['topicurl']}\" class=\"hlpTopicDiv\"><div>{$topicon}</div><div>{$tvl['topictitle']}</div></a>";
        if ((int)count($tvl['functionslist']) > 0 ) { 
          foreach ( $tvl['functionslist'] as $fky => $fvl ) { 
            $t .= "<a class=\"hlpTopicDiv\" href='{$tt}/scienceserver-help/{$val['modurlref']}/{$fvl['helpurl']}');\"><div><i class=\"material-icons topicicon\">arrow_right</i></div><div>{$fvl['title']}</div></a>";
          }
        }
      }
    }

    $t .= "</div></div>";
  }
  $t .= "</div>";

  if ( trim($rqststr[2]) === "" ) {
    //NO TOPIC REQUESTED - DISPLAY GENERAL PAGE
    $topBtnBar = generatePageTopBtnBar('scienceserverhelp', $whichusr);
    $helpFile = <<<RTNTHIS
<div id=help_welcomediv>
<div id=help_welcometitle>ScienceServer v7 Help Files</div>
     <div id=help_welcomeinstructions>
     These are documents to assist you with the use and documentation of CHTN Eastern's Specimen/Laboratory Information Management System (SMS/LIMS) <span class=ssdsp>ScienceServer</span> version 7.  To find 'Help' with a particular screen or function in <span class=ssdsp>ScienceServer</span>, click the 'Display Index' button above.  From the AppCard slideout-tray, you can either scroll the list of documents (categorized into <span class=ssdsp>ScienceServer</span> functions), or use the 'Search Term' box to find a certain key word within the document text. Clicking on a topic in the index will display all documents in that topic.  Clicking on a document title will display the document.  There are three types of documents in the index: 1)  general help documents indicated with a <span><i class="material-icons">library_books</i></span> icon; 2) screen help documents indicated with a <span><i class="material-icons">desktop_windows</i></span> icon.  These documents display on the functional screens within <span class=ssdsp>ScienceServer</span>; and, 3) uploaded pdfs indicated with a <span><i class="material-icons">picture_as_pdf</i></span>icon.  These are documents generated outside of the <span class=ssdsp>ScienceServer</span> environment but have relavency to <span class=ssdsp>ScienceServer</span>.  These documents are opened in a PDF view within the <span class=ssdsp>ScienceServer</span> help window.<p>Finally, if you cannot find a document which will answer your question/concern about <span class=ssdsp>ScienceServer</span>, you can click on the 'Open Help Ticket' above, which will send a ticket to the CHTN Eastern Informatics team.  An informatics team member will get back to you with an answer.  
     </div>
</div>
RTNTHIS;
  } else {
    if ( trim($rqststr[2]) === "querysearch" ) { 
      //MAKE QUERY RESULTS    
      $rsltdta = json_decode(callrestapi("GET", dataTree . "/search-help-results/{$rqststr[3]}", serverIdent, serverpw),true);
      $searchtermarr = json_decode($rsltdta['DATA']['head']['srchterm'], true);
      $givenSearchTerm = $searchtermarr['searchterm'];
      $itemsfound =$rsltdta['ITEMSFOUND']; 
      $objid = $rsltdta['DATA']['head']['objid'];
      $bywho = $rsltdta['DATA']['head']['bywho'];
      $onwhen = $rsltdta['DATA']['head']['onwhendsp'];

      foreach ($rsltdta['DATA']['searchresults'] as $rkey => $rval) { 
        $abs = strip_tags( $rval['abstract'] );
        $inner .= "<tr><td colspan=2>";
          $inner .= "<table class=zoogleTbl onclick=\"navigateSite('scienceserver-help/{$rval['modindexurl']}/{$rval['helpurl']}');\">";
          $inner .= "<tr><td class=zoogleTitle>{$rval['titledsp']}</td></tr>";
          $inner .= "<tr><td class=zoogleURL>{$tt}/scienceserver-help/{$rval['urldsp']}</td></tr>";
          $inner .= "<tr><td class=zoogleAbstract>{$abs}</td></tr>";
        $inner .= "</table>";
        $inner .= "</td></tr>";
      }
      
      $topBtnBar = generatePageTopBtnBar('scienceserverhelp', $whichusr);
      $helpFile = <<<RTNTHIS
<table border=0 cellspacing=0 cellpadding=0 id=resultsSearchTbl>
<tr><td id=title colspan=2>Search Results</td></tr>
<tr><td id=itemsfound>Items found: {$itemsfound}</td><td id=bywhowhen align=right valign=top> Query By: {$bywho} ({$onwhen}) </td></tr>
{$inner}
</table>
RTNTHIS;

    } else {
      if ( trim($rqststr[3]) === "" ) { 
        //DISPLAY TOPIC LIST
        $rsltdta = json_decode(callrestapi("GET", dataTree . "/topic-document-list/{$rqststr[2]}", serverIdent, serverpw),true);
        $mTitle = ( trim($rsltdta['DATA']['module']['module']) !== "" ) ? trim($rsltdta['DATA']['module']['module']) : "Module Not Found";
        $mTitle .= ( trim($rsltdta['DATA']['module']['moduleid']) !== "" ) ? " <span class=smllr>[Module #: " .substr( ('0000' . trim($rsltdta['DATA']['module']['moduleid'])),-6) . "]</span>" : "";
        $mDesc = ( trim($rsltdta['DATA']['module']['moduledescription']) !== "" ) ? trim($rsltdta['DATA']['module']['moduledescription']) : "";
        $mDocs = "";
        foreach ( $rsltdta['DATA']['module']['documentlist'] as $dk => $dv ) { 
            $dTitle = ( trim($dv['doctitle']) !== "" ) ? trim($dv['doctitle']) : "&nbsp;"; 
            $dSTitle = ( trim($dv['docsubtitle']) !== "" ) ? trim($dv['docsubtitle']) : "&nbsp;"; 
            switch ($dv['helptype']) { 
              case 'TOPIC': 
                $topicon = "<i class=\"material-icons\">library_books</i>";
                break;
              case 'PDF':
                $topicon = "<i class=\"material-icons\">picture_as_pdf</i>";
                break;
              case 'SCREEN':
                $topicon = "<i class=\"material-icons\">desktop_windows</i>";    
                break;
              default:
                $topicon = "<i class=\"material-icons\">desktop_windows</i>";
            }
            $mDocs .= "<a href=\"{$tt}/scienceserver-help/{$rsltdta['DATA']['module']['modurlref']}/{$dv['helpurl']}\" class=mDocHolder><div class=mDocDspIcon>{$topicon}</div><div class=mDocTitle>{$dTitle}</div><div class=mDocSTitle>{$dSTitle}</div></a>"; 
        }
        $topBtnBar = generatePageTopBtnBar('scienceserverhelp', $whichusr);
        $helpFile = <<<RTNTHIS
<div id=moduletitleline>
  <div id=mtitle>{$mTitle}</div>
  <div id=mDesc>{$mDesc}</div>
</div>

<div id=mDocumentList>
  {$mDocs}
</div>

RTNTHIS;
      } else { 
        //DISPLAY DOCUMENT
   
    $rsltdta = json_decode(callrestapi("GET", dataTree . "/help-document-text/{$rqststr[3]}", serverIdent, serverpw),true);
    if ( (int)$rsltdta['ITEMSFOUND'] > 0 ) {
  
      $topBtnBar = generatePageTopBtnBar('scienceserverhelp', $whichusr, $rqststr[3] );
      if ( $rsltdta['DATA']['docobj']['hlpType'] === 'PDF' ) { 
        $hlpTxt = base64file( genAppFiles . $rsltdta['DATA']['docobj']['pdfdocurl'], "HELPDSPPDF","pdfhlp",true);
      } else {
          //[{"versionnbr":1,"sectionhead":"General Procedure","ordernbr":0.1,"sectiontext":"Welcome to ScienceServer version 7. Throughout the years, CHTN - Eastern has built a massive database with over 20 years of collection and distribution data. This application will grant you access to this data dependent on your access rights."}] 

         $sctionnbr = ""; 
         $sectionnbrdsp = 0;  
         $subsection = 0;
         foreach ( $rsltdta['DATA']['doctxt'] as $v  ) { 
           if ( $sctionnbr !== (int)$v['ordernbr'] ) { 
             $sectionnbrdsp += 1;
             $subsection = 1;
             $sctionnbr = (int)$v['ordernbr'];
           }
           $minor = (int)$v['versionnbr']; 
           $hlpTxt .= "<div class=hlpSectionDspNbr>Section: {$sectionnbrdsp}.{$subsection} {$v['sectionhead']} </div><div class=hlpSectionTxt>" . putPicturesInHelpText( $v['sectiontext'] ) . "</div>";
           $subsection++;
         }

         $modules = "";
         foreach ( $rsltdta['DATA']['modules'] as $v  ) { 
           $modules .= ( trim($modules) === "" ) ? "&#8227; {$v['module']}" : " &#8227; {$v['module']}";  
         }

      }

      $lstby = ( trim($rsltdta['DATA']['docobj']['lstemail']) === "" ) ? "&nbsp;" : "{$rsltdta['DATA']['docobj']['lstemail']}";
      $lstdte = ( trim($rsltdta['DATA']['docobj']['lstdte']) === "" ) ? "&nbsp;" : "({$rsltdta['DATA']['docobj']['lstdte']})";
      $versioning = substr("0000{$rsltdta['DATA']['docobj']['versionmajor']}",-2) . "." . substr("0000{$rsltdta['DATA']['docobj']['versionminor']}", -2) . "." . substr("00000{$minor}",-4);

      $helpFile = <<<RTNTHIS
<div id=hDocObjHoldr>

  <div id=hDocType>{$rsltdta['DATA']['docobj']['hlpType']}</div>
  <div id=hDocTitle>{$rsltdta['DATA']['docobj']['hlpTitle']}</div>
  <div id=hDocSTitle>{$rsltdta['DATA']['docobj']['hlpSubTitle']}</div>

  <center>
  <div id=hDocMetricsHolder>
    <div id=metricBox>
      <div id=metricTitle>Document Metrics</div> 

      <div class=mElemHld>

        <div class=hdiv>
          <div class=mElemLbl>Document Version</div>
          <div class=mElemDta>{$versioning}</div>
        </div>

        <div class=hdiv>
          <div class=mElemLbl>Creating Author</div>
          <div class=mElemDta>{$rsltdta['DATA']['docobj']['byemail']}</div>
          <div class=mElemDta>({$rsltdta['DATA']['docobj']['initialdte']})</div>
        </div>

        <div class=hdiv>
          <div class=mElemLbl>Last Edited By</div>
          <div class=mElemDta>{$lstby}</div>
          <div class=mElemDta>{$lstdte}</div>
        </div>

        <div class=hdiv>
          <div class=mElemLbl>Documentation Modules</div>
          <div class=mElemDta>{$modules}</div>
        </div>

      </div>
    </div>
   </div> 
   </center>

  <div id=hDocText>{$hlpTxt}</div>


</div>
RTNTHIS;
    } else {
    $helpFile = <<<RTNTHIS
ERROR: NO DOCUMENT FOUND
RTNTHIS;
    }
      }    
    }
  }

$rtnthis = <<<PAGEHERE
{$topBtnBar}
<div id=indexMenuSlide>
  <div id=indexHolder> 
    <div id=srchBoxHolder>
      <div><input type=text id=fldHlpSrch value="{$givenSearchTerm}" placeholder="Search Term"> </div> 
      <div><table class=tblBtn id=btnSearchHelp style="width: 6vw; border-collapse: collapse;"><tr><td style="font-size: 1.7vh; padding: .5vh 0; background: rgba(48,57,71,1); border: 1px solid  rgba(255,255,255,1); color: rgba(255,255,255,1);"><center>Search</td></tr></table></div>  
    </div>
  </div>
  {$t}
</div>

<div id=documentDisplay>
{$helpFile}
</div>

PAGEHERE;

return $rtnthis;    

}


function scienceserverhelp_bu20200324($rqststr, $whichusr) { 

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
<div id=helperdspholder>
  <div> </div>  
  <div id=helpdsptitleline> 
     <div id=SSTitle>ScienceServer Help Documents</div>
     <div id=srchHolder align=right> 
       <table style="border-collapse: collapse;">
         <tr><td> <input type=text id=fldHlpSrch value="{$givenSearchTerm}"> </td> 
             <td><table class=tblBtn id=btnSearchHelp style="width: 6vw;"><tr><td><center>Search</td></tr></table></td> 
             {$printTopicBtn} 
             <td><table class=tblBtn id=btnHelpTicket style="width: 6vw;"><tr><td><center><i class="material-icons helpticket">build</i></td></tr></table></td> 
         </tr></table>  
     </div> 
     </div>
  <div id=helpindexholder>{$t}</div>
  <div> </div> <div id=helpdocumentholder> {$helpFile}  </div>
</div>
PAGEHERE;

return $rtnthis;    
}

function chartreviewbuilder ( $rqststr, $whichusr ) { 
    //DOING IT A DIFFERENT WAY
    if ((int)$whichusr->allowcoord !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE COORDINATOR SCREEN";        
    } else { 
        if ( trim($rqststr[2]) !== "" ) { 
        } else { 
          $rtnthis = <<<CRRQST
                  <table border=1>
                  <tr><td>Biogroup </td></tr>
                  <tr><td><input type=text id=fldBioRqst></td></tr>
                  <tr><td align=right><button>Request</button></td></tr>
                  </table>
CRRQST;
        }
    }    
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
   <th class="cnttxt">Ch</th>     
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
$pbiosampecnt = 0;
foreach ($dta['DATA']['searchresults'][0]['data'] as $fld => $val) { 
    if ($pbident <> $val['pbiosample']) { 
        //GET NEW COLOR
        $colorArray = getColor($val['pbiosample']);
        $pbSqrBgColor = " style=\"background: rgba({$colorArray[0]}, {$colorArray[1]}, {$colorArray[2]},1); \" ";
        $pbident = $val['pbiosample'];
        $pbiosamplecnt++;
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

    if ( trim($val['manifestnbr']) !== "" ) { 
        $eManifestNbr = cryptservice( 'DtaC:' . $val['manifestprefix'] . "-" . substr('000000' . $val['manifestnbr'], -6) );
        $stsDte .= "<br><a href=\"javascript:void(0);\" onclick=\"openOutSidePage('{$tt}/print-obj/inventory-manifest/{$eManifestNbr}');\" style=\"color: rgba(57,255,20,1);\" >Manifest #: " . $val['manifestprefix'] . "-" . substr('000000' . $val['manifestnbr'], -6) . "</a> (Manifest Status: {$val['manifeststatus']})";
    }

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
 
$bgdbl = cryptservice( $val['pbiosample'], 'e');

$chtrvw = substr(( '000000' . (int)$val['charttextdocid'] ),-6);
$echtrvw = ( (int)$val['charttextdocid'] <> 0 ) ? cryptservice ( substr(( '000000' . (int)$val['charttextdocid'] ),-6), 'e') : "";
$chtprntline = ( (int)$val['charttextdocid'] <> 0 ) ? "<div class=quickLink onclick=\"printChartReview( event, '{$echtrvw}');\"   ><i class=\"material-icons qlSmallIcon\">print</i> Print Chart Review (#{$chtrvw})</div>" : "";

$chart = <<<CHRTICON
<div class=ttholder>        
  {$val['chartindicator']}
  <div class=tt>
    <div class=quickLink onclick="generateDialog('chartbldr','{$bgdbl}');"><i class="material-icons qlSmallIcon">file_copy</i> Chart Review Dialog</div>
    {$chtprntline}
  </div>
</div>
CHRTICON;

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
  <td valign=top class="cntr">{$chart}</td>
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
<tr><td class=columnQParamName>Records Found: </td>         <td class=ColumnDataObj>Biosample Groups: {$pbiosamplecnt} / Segment Records: {$itemsfound}</td></tr>
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
<table border=0><tr><td>Biosample Groups Found: {$pbiosamplecnt} / Segment Recordss found: {$itemsfound} <input type=hidden value="{$rqststr[2]}" id=urlrequestid></td><td align=right>(Hover mouse on grid for context menu)</td></tr>
<tr><td colspan=2>{$dataTbl}&nbsp;</td></tr>
</table>
DSPTHIS;

$topBtnBar = generatePageTopBtnBar('coordinatorResultGrid',$whichusr);
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

function scienceserverchangelog ( $rqstStr, $whichUsr ) { 
  require(genAppFiles . "/dataconn/sspdo.zck"); 
  $tt = treeTop;

  $rs = $conn->prepare("SELECT date_format(dategitchange,'%Y-%M') as dategitchangedsp, ssversionnbr, gittag, changenotes FROM four.app_changelog order by dategitchange desc");
  $rs->execute();
  $chngTbl = "<table border=0 id=chngLogTbl><tr><th>Date Implemented</th><th>Version Reference</th><th>GitHub Tag</th><th>Implementation Notes</th></tr><tbody>";
  while ( $r = $rs->fetch(PDO::FETCH_ASSOC) ) { 
    $chngTbl .= "<tr><td valign=top class=dtedsp>{$r['dategitchangedsp']}</td><td valign=top class=ssversion>{$r['ssversionnbr']}</td><td valign=top class=gittag>{$r['gittag']}</td><td valign=top>{$r['changenotes']}</td></tr>";
  }
  $chgnTbl .= "</tbody></table>";



  $rtnthis = <<<PAGEHERE
<div id=mainPageHolder>
<div id=head>ScienceServer Application Change-Log</div>
<div id=instructions>This screen shows the progression of changes to the CHTNEastern Application framework (front-facing website / CHTNEastern Transient Inventory / ScienceServer Development &amp; ScienceServer Production).  The initial records were taken from GITHub records and may be missing some application deployment milestones, as the GitHub Repository did not track all changes (just major release candidates).  However, as of development release v7.0.8 (hlpandstuff) all records are entered directly into this changelog.  Displayed newest to oldest. </div>
<div id=dataholder>{$chngTbl}</div>
</div>
PAGEHERE;
return $rtnthis;
}

function scienceserverhelpdeskticketmanagement ( $rqstStr, $whichUsr ) { 
  require(genAppFiles . "/dataconn/sspdo.zck"); 
  $tt = treeTop;

  $tcktRS = $conn->prepare( "SELECT ticketnumber, bywho, date_format(onwhen,'%m/%d/%Y') as onwhen, reasoncode, affectedSSModule, ticketstatus  FROM four.app_helpTicket ht where ifnull(ticketstatus,'') <> 'CLOSED' order by ticketnumber desc" );
  $tcktRS->execute();
  $openTicketList = "";
  $openTicketCount = 0;
  while ( $t = $tcktRS->fetch(PDO::FETCH_ASSOC) ) {
      
    $ticketnumber = substr(("000000" . $t['ticketnumber']),-6);
    $ticketstatus = ( trim( $t['ticketstatus'] ) === "" ) ? "OPEN" : trim( $t['ticketstatus'] );
    $ticketEncyr = cryptservice( generateRandomString(5) . "::" . ($t['ticketnumber']."::".generateRandomString(5) ),'e');   
    $openTicketList .= <<<TICKET
      <a href="{$tt}/scienceserver-helpdesk-ticket-management/{$ticketEncyr}" id="ticket{$ticketnumber}" class="openticketticket" >
        <div class=opnTicketLine>{$ticketnumber} ({$ticketstatus})</div>
        <div class=opnTicketReason>Reason: <b>{$t['reasoncode']}</b></div>
        <div class=opnTicketMod>Module: <b>{$t['affectedSSModule']}</b></div>
        <div class=opnTicketBy>Requested By: <i>{$t['bywho']} [{$t['onwhen']}]</i></div>
      </a>
TICKET;
    $openTicketCount++;
  } 

  $ctcktRS = $conn->prepare( "SELECT ticketnumber, bywho, date_format(onwhen,'%m/%d/%Y') as onwhen, reasoncode, affectedSSModule, ticketstatus, date_format( statuswhen,'%m/%d/%Y') as statuswhen  FROM four.app_helpTicket ht where ifnull(ticketstatus,'') = 'CLOSED' and statuswhen > date_add( now(), interval -30 day) order by ticketnumber desc" );
  $ctcktRS->execute();
  $closedTicketList = "";
  $closedTicketCount = 0;
  $ticketEncyr = cryptservice( ( generateRandomString(10)."::".$t['ticketnumber']."::".generateRandomString(5) ),'e');   
  while ( $t = $ctcktRS->fetch(PDO::FETCH_ASSOC) ) {
    $closedticketnumber = substr(("000000" . $t['ticketnumber']),-6);
    //$closedticketstatus = ( trim( $t['ticketstatus'] ) === "" ) ? "OPEN" : trim( $t['ticketstatus'] );
    $closedticketstatus = $t['statuswhen'];
    $cticketEncyr = cryptservice( generateRandomString(5) . "::" . ($t['ticketnumber']."::".generateRandomString(5) ),'e');   
    $closedTicketList .= <<<TICKET
      <a  href="{$tt}/scienceserver-helpdesk-ticket-management/{$cticketEncyr}" id="cticket{$closedticketnumber}" class="openticketticket">
        <div class=opnTicketLine>{$closedticketnumber} ({$closedticketstatus})</div>
        <div class=opnTicketReason>Reason: <b>{$t['reasoncode']}</b></div>
        <div class=opnTicketMod>Module: <b>{$t['affectedSSModule']}</b></div>
        <div class=opnTicketBy>Requested By: <i>{$t['bywho']} [{$t['onwhen']}]</i></div>
      </a>
TICKET;
  } 

  $sideWorkbench = "";
  if ( $rqstStr[2] ) { 
    $url = explode("/",$_SERVER['REQUEST_URI']);
    $tickency = cryptservice( $url[2], 'd');
    if ( trim($tickency) === "" ) { 
      $sideWorkbench = "ERROR: TICKET ENCRYPTION FAILED";
    } else {    
      $t = explode("::",$tickency);    
      $ticket = $t[1];
      $thisticketRS = $conn->prepare( "SELECT substr(concat('000000',ht.ticketnumber),-6) as ticketnumber, ifnull(ht.bywho,'') as bywho, ifnull(ht.bywhoemail,'') as bywhoemail, ifnull(date_format(ht.onwhen,'%m/%d/%Y'),'') as onwhen, ifnull(ht.reasoncode,'Other') as reasoncode, ifnull(ht.affectedssmodule,'NO MODULE STATED') as affectedssmodule, ifnull(ht.recreateind,'No') as recreateind, ifnull(ht.description,'-') as descriptionofissue, ifnull(ht.solutioncategory,'') solutioncategory, ifnull(ht.persontakenby,'') persontakenby, ifnull(date_format(ht.startedwhen,'%m/%d/%Y'),'') as takenwhen, ifnull(ht.ticketstatus,'OPEN') as ticketstatus, ifnull(date_format(ht.statuswhen,'%m/%d/%Y'),'') as statuswhen , ifnull(ht.title,'') as title, ifnull(ht.githubid,'') as githubid, ifnull(ht.itnotes,'') as itnotes FROM four.app_helpTicket ht where ticketnumber = :ticketnumber" );
      $thisticketRS->execute(array(':ticketnumber' => $ticket ));
      if ( $thisticketRS->rowCount() <> 1 ) {
        $sideWorkbench = "ERROR: CANNOT FIND TICKET {$ticket}";
      } else {
          $ti = $thisticketRS->fetch(PDO::FETCH_ASSOC);
          //{"assignedtosection":"","persontakenby":"","takenwhen":"","ticketstatus":"OPEN","statuswhen":"","title":"","githubid":"","itnotes":""}
          $itext = preg_replace('/\\n{1}/','<br>',  preg_replace('/\\n{2}/','&nbsp;<p>', $ti['descriptionofissue']));


    $stsRS = $conn->prepare( "SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HELPTICKETSTATUS' and dspind = 1 order by dsporder" );
    $stsRS->execute(); 
    $stsm = "<table border=0 class=menuDropTbl>";
    $givensdspvalue = "";
    $givensdspcode = "";
    while ( $s = $stsRS->fetch(PDO::FETCH_ASSOC) ) { 
      if ( $s['menuvalue'] === $ti['ticketstatus'] ) { 
        $givensdspvalue = $s['dspvalue'];
        $givensdspcode = $s['menuvalue'];
      }
      $stsm .= "<tr><td onclick=\"fillField('fldtsts','{$s['menuvalue']}','{$s['dspvalue']}');\" class=ddMenuItem>{$s['dspvalue']}</td></tr>";
    }
    $stsm .= "</table>";
    $stsmnu = "<div class=menuHolderDiv><input type=hidden id=fldtstsValue value=\"{$givensdspcode}\"><input type=text id=fldtsts READONLY class=\"inputFld\" value=\"{$givensdspvalue}\"><div class=valueDropDown id=ddtsts>{$stsm}</div></div>";

    $solRS = $conn->prepare( "SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'HELPTICKETSOLUTIONCAT' and dspind = 1 order by dsporder" );
    $solRS->execute(); 
    $solm = "<table border=0 class=menuDropTbl>";
    $givensodspvalue = "";
    $givensodspcode = "";
    while ( $so = $solRS->fetch(PDO::FETCH_ASSOC) ) { 
      if ( $so['menuvalue'] === $ti['solutioncategory'] ) { 
        $givensodspvalue = $so['dspvalue'];
        $givensodspcode = $so['menuvalue'];
      }
      $solm .= "<tr><td onclick=\"fillField('fldtsol','{$so['menuvalue']}','{$so['dspvalue']}');\" class=ddMenuItem>{$so['dspvalue']}</td></tr>";
    }

    $solm .= "</table>";
    $solmnu = "<div class=menuHolderDiv><input type=hidden id=fldtsolValue value=\"{$givensodspcode}\"><input type=text id=fldtsol READONLY class=\"inputFld\" value=\"{$givensodspvalue}\"><div class=valueDropDown id=ddtsol>{$solm}</div></div>";

    $htrarr = json_decode(callrestapi("GET", dataTree . "/global-menu/help-ticket-reasons",serverIdent, serverpw), true);
    $agm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($htrarr['DATA'] as $agval) {
        if ( $ti['reasoncode'] === $agval['menuvalue'] ) { 
          $givendspvalue = "{$agval['menuvalue']}";
          $givendspcode = "{$agval['codevalue']}";
        } 
        $agm .= "<tr><td onclick=\"fillField('fldHTR','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
    }
    $agm .= "</table>";
    $htrmnu = "<div class=menuHolderDiv><input type=hidden id=fldHTRValue value=\"{$givendspcode}\"><input type=text id=fldHTR READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddHTR>{$agm}</div></div>";


    $sideWorkbench = <<<TICKETGOESHERE
<div id=dspticketnumber>Ticket Number: {$ti['ticketnumber']} <input type=hidden id=fldWorkTicket value="{$url[2]}"></div>

<div id=dspmetricline>

  <div class=metricsqr>
    <div class=label>Reason</div>
    <div> {$htrmnu} </div>
  </div>

  <div class=metricsqr>
    <div class=label>Affected Module</div>
    <div>{$ti['affectedssmodule']}</div>
  </div>
  
  <div class=metricsqr>
    <div class=label>Recreate Issue</div>
    <div>{$ti['recreateind']}</div>
  </div>

  <div class=metricsqr>
    <div class=label>Ticket By</div>
    <div>{$ti['bywho']}</div>
    <a href="javascript:void(0);">{$ti['bywhoemail']}</a>
  </div>

  <div class=metricsqr>
    <div class=label>Opened On</div>
    <div>{$ti['onwhen']}</div>
  </div>

<div class=issuetext>
  <div class=label>Issue: </div>
  <div id=itext>{$itext}</div>
</div>

</div>


<div id=dspmetriclineA>

  <div class=metricsqr>
    <div class=label>Ticket Status</div>
    <div> {$stsmnu} </div>
  </div>

  <div class=metricsqr>
    <div class=label>Solution</div>
    <div> {$solmnu} </div>
  </div>

  <div class=metricsqr>
    <div class=label>GITHub Ticket #</div>
    <div><input type=text id=fldGitHub value="{$ti['githubid']}"></div>
  </div>

</div>


<div id=dspmetriclineB>

  <div class=metricsqr>
    <div class=label>Title</div>
    <div> <input type=text id=fldSolutionTitle value="{$ti['title']}"></div>
    <div class=label style="margin-top: 1vh;">Informatics Notes</div>
    <div><TEXTAREA style="width: 100%; height: 15vh;" id=fldSolutionText>{$ti['itnotes']}</TEXTAREA></div>
  </div>

</div>


<div id=btnBar><button id=btnSaveTicketWork>Save</button></div>



TICKETGOESHERE;
      }
    }
  }


  //<div align=right><div style="width: 10vw; text-align: left;">Search Ticket Number</div><div style="width: 10vw;"><input type=text id=fldSrchTickets style="width: 10vw;"></div></div>
  $rtnthis = <<<PAGEHERE
<div id=mainPageHolder>
  <div id=workbench>
    <div id=headr>ScienceServer Technical Helpdesk Ticket Management</div>
    <div class=ticketSqrHead>Open Tickets (Open Tickets: {$openTicketCount})<div id=openticketlisting>{$openTicketList}</div></div><div id=detailWorkbench> <div>{$sideWorkbench}</div></div>
    <div class=ticketSqrHead>Close (Last 30 Days)<div id=closeticketlisting>{$closedTicketList}</div></div>
  </div>
</div>
PAGEHERE;
return $rtnthis;
}

function continuousprocessimprovementtracker ( $rqstStr, $whichUsr ) { 

      require(genAppFiles . "/dataconn/sspdo.zck"); 
      $hlpR = $conn->prepare( "SELECT * FROM four.app_cti_helptickets cti left join four.app_helpTicket htck on cti.itticketreference = htck.ticketnumber" ); 
      $hlpR->execute();
      $cti = json_encode( $hlpR->fetchAll(PDO::FETCH_ASSOC) );



  $rtnthis = <<<PAGEHERE
<div id=mainPageHolder>
Continuous Process Improvement Ticket Track
<div>
 {$cti}
</div>
</div>
PAGEHERE;
return $rtnthis;
}

function root($rqstStr, $whichUsr) { 
 //TAG RELEASE  

$fsCalendar = buildcalendar('mainroot', date('m'), date('Y'), $whichUsr->friendlyname, $whichUsr->useremail, $whichUsr->loggedsession );

//$graphListArr = ["grphfreezer" => "root_freezers.png", "grphrollshipgrid" => "root_yearrollship.png" , "grphinvestigatorinf" => "root_invnbrs.png", "grphsegshiptotal" => "root_totlshipped.png", "grphslidessubmitted" => "root_hprslidessubmitted.png", "hprpie" => "hpr_last10weekdecision_pie.png", "grphcollected" => "chtn_segment_collections.png", "grphshipped" => "chtn_segment_distribute.png", "grphinvest" => "inv_acttoinvest_pie" ];
$graphListArr = ["grphrollshipgrid" => "root_yearrollshipa.png", "grphslidessubmitted" => "root_hprslidessubmitted.png","hprpie" => "hpr_last10weekdecision_pie.png","grphcollected" => "chtn_segment_collections.png","grphshipped" => "chtn_segment_distribute.png","grphinvest" => "inv_acttoinvest_pie.png","grphstarrate" => "star_survey_rating.png","grphprisassign" => "pristine_assignment.png" ];
$graphics = array();
$at = genAppFiles;
foreach ( $graphListArr as $key => $grph ) {
  if ( file_exists("{$at}/publicobj/graphics/sysgraphics/{$grph}" ) ) { 
    $graphics[$key] = base64file("{$at}/publicobj/graphics/sysgraphics/{$grph}", "{$key}", "png", true , " onclick = \"enlargeDashboardGraphic('{$grph}');\" class=\"dashboardimage\" "); 
  }
}

$starwhite = base64file("{$at}/publicobj/graphics/starwhite.png", "whiteStar", "png", true , " style=\"width: 90%; height: auto;\" ");
$staryellow = base64file("{$at}/publicobj/graphics/staryellow.png", "yellowStar", "png", true , " style=\"width: 90%; height: auto;\" ");
$starhalf = base64file("{$at}/publicobj/graphics/starhalf.png", "yellowStar", "png", true , " style=\"width: 90%; height: auto;\" ");

//TODO:  MAKE THESE A WEBSERVICE
require(serverkeys . "/sspdo.zck");

$sRS = $conn->prepare("SELECT sum(qty) shipttl FROM masterrecord.ut_procure_segment where shippeddate between date_format(now(),'%Y-01-01') and date_format(now(),'%Y-12-31')"); 
$sRS->execute(); 
$s = $sRS->fetch(PDO::FETCH_ASSOC);

$faCntSQL = "SELECT count(1) as assignedme FROM masterrecord.ut_master_furtherlabactions where activeind = 1 and actioncompletedon is null and assignedagent = :usrid union SELECT ifnull(count(1),0) as assignedme FROM masterrecord.ut_master_furtherlabactions where activeind = 1 and actioncompletedon is null and trim(ifnull(assignedagent,'')) = ''";
$faCntRS = $conn->prepare($faCntSQL); 
$faCntRS->execute(array(':usrid' => $whichUsr->userid));
$faCnt = $faCntRS->fetchAll(PDO::FETCH_ASSOC); 

$actRQRS = $conn->prepare( "select * from (select count(1) reqservedthisyear from (SELECT distinct assignedReq FROM masterrecord.ut_procure_segment where shippeddate between date_format(now(),'%Y-01-01') and now()) distincti) as reqsrv, (SELECT count(1) actyr FROM vandyinvest.activeinvesttissreq where activeyear = date_format(now(),'%Y') ) actreq" );
$actRQRS->execute(); 
if ( $actRQRS->rowCount() <> 1 ) { 
  $rqSrvdLine = "0 / 0";
} else { 
  $act = $actRQRS->fetch(PDO::FETCH_ASSOC);
  $rqSrvdLine = "{$act['reqservedthisyear']}/{$act['actyr']}";
}

$ooRS = $conn->prepare( "Select *  from (select count(1) as onoffer from masterrecord.ut_procure_segment where segstatus = 'ONOFFER'  and procuredat = 'HUP' ) oo, (SELECT count(1) as sentman FROM masterrecord.ut_ship_manifest_head where mstatus = 'SENT' and institutioncode = 'HUP' ) mn" );
$ooRS->execute(); 
if ( $ooRS->rowCount() <> 1 ) { 
  $rqSrvdLine = "0 / 0";
} else { 
  $oo = $ooRS->fetch(PDO::FETCH_ASSOC);
  $ooLine = "{$oo['onoffer']}/{$oo['sentman']}";
}

$shpSQL = "SELECT count(1) as slidecount FROM masterrecord.ut_procure_segment where toHPR = 1 and ifnull(hprslideread,0) = 0 and ifnull(hprboxnbr,'') <> ''";
$shpRS = $conn->prepare($shpSQL);
$shpRS->execute();                  
$shpT = $shpRS->fetch(PDO::FETCH_ASSOC);
$qmssub = $shpT['slidecount'];

$starRS = $conn->prepare( "select round(( strs.ttlvalue / ansr.ttlanswer ),1) averageRating, ansr.ttlanswer from (SELECT if(sum(starvalue)=0,1,sum(starvalue)) as ttlvalue FROM webcapture.srvy_answers where activeind = 1 and answeredOn between date_add( now(), interval -6 month) and now()) strs, (SELECT if( count(1) = 0, 1,count(1))  as ttlanswer FROM webcapture.srvy_answers where activeind = 1 and answeredOn between date_add( now(), interval -6 month) and now()) ansr" );
$starRS->execute();
$star = $starRS->fetch(PDO::FETCH_ASSOC);
$starrating = (double)$star['averageRating'];

$stardelimited = $starrating;
$innerStarsHere = "";
for ( $i = 0; $i < 5; $i++ ) {
//  $innerStarsHere .= " / " . $stardelimited; 
  if ( $stardelimited > 0.9 ) {
    $innerStarsHere .= "<div>{$staryellow}</div>";     
  } else { 
    if ( $stardelimited > 0.1 ) {  
      $innerStarsHere .= "<div>{$starhalf}</div>";     
    } else {     
      $innerStarsHere .= "<div>{$starwhite}</div>";     
    }
  }   
  $stardelimited = ( $stardelimited - 1 );

}


$opsdRS = $conn->prepare( "select replace(sbtbl.invest,'Dr. ','') as invest, sbtbl.shipdoc, sdstatus, rqshipdate, sum( qty ) ttlseg from (SELECT concat('[',ifnull(sd.investcode,'-') , '] ', ifnull(sd.investname,'') ) as invest, substr(concat('000000',sd.shipdocrefid),-6) as shipdoc, sds.longvalue sdstatus, date_format( sd.rqstshipdate, '%m/%d/%Y') as rqshipdate, sg.shipDocRefID, sg.qty FROM masterrecord.ut_shipdoc sd left join masterrecord.ut_procure_segment sg on sd.shipdocrefid = sg.shipDocRefID left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'SDStat') sds on sd.sdstatus = sds.menuvalue where ( sd.sdstatus <> 'CLOSED' and sd.sdstatus <> 'VOID' ) and sg.voidind <> 1 ) sbtbl group by invest, shipdoc, sdstatus, rqshipdate order by rqshipdate" );
$opsdRS->execute();

$weekshpTbl = "<table width=100% id=weekshiptbl><tr><th>Invest</th><th>Ship Doc</th><th>Status</th><th>Rqst On</th><th>Segments</th></tr><tbody>";
$ttlttlseg = 0;
$ttlsd = 0;
while ( $r = $opsdRS->fetch(PDO::FETCH_ASSOC)) {  
  $i = explode( " ", $r['invest'] );  
  $icode = $i[0];
  $iname = $i[(count( $i ) - 1)];
  $weekshpTbl .= "<tr><td>{$icode} {$iname}</td><td>{$r['shipdoc']}</td><td>{$r['sdstatus']}</td><td>{$r['rqshipdate']}</td><td align=right>{$r['ttlseg']}</td></tr>";
  $ttlttlseg += (int)$r['ttlseg'];
  $ttlsd += 1;
}
$weekshpTbl .= "</tbody><tr><td colspan=5 align=right id=sdtotl>Ship-Docs: {$ttlsd} / Segments: {$ttlttlseg}</td></tr>";
$weekshpTbl .= "</table>";





$dd = date("l") . ", " . date("jS") . " of " . date("F") . ", " . date("Y");
//<div class=infoHold><div class=iLabel>Requests Served / Active</div><div class=iData>{$rqSrvdLine}</div></div>
$rtnthis = <<<PAGEHERE
<div id=dashboardholder>

  <div id=headr>
  
   <div id=tagger>
      <div id=headrTag>CHTNEastern ScienceServer</div>
      <div id=dash>Your Daily Dashboard</div>
    </div>
    <div id=moreInfoBar>
      <a href="javascript:void(0)" onclick="generateDialog('moremetrics','procurement');">More Procurement</a>
      <a href="javascript:void(0)" onclick="generateDialog('moremetrics','qms');">More QMS</a>
      <a href="javascript:void(0)" onclick="generateDialog('moremetrics','shipping');">More Shipping</a>
      <a href="javascript:void(0)" onclick="generateDialog('moremetrics','data-inventory');">More Data/Inventory</a>
      <a href="javascript:void(0)" onclick="generateDialog('moremetrics','environment');">More Environment</a>
    </div>
    <div id=datedsp> {$dd} </div>
  </div>

  <div id=calendarHolder>
    <div id=calHead>CHTNCalendar</div>
    <div id=theCalendar>{$fsCalendar}</div>
  </div>
 
  <div class=displayerDiv>
    <div class=metricHead>Shipping Metrics</div>
    <div>{$graphics['grphshipped']}</div>
  </div>

  <div class=displayerDiv>
    <div class=metricHead>Collection Metrics</div>
    <div>{$graphics['grphcollected']}</div>
  </div>

  <div class=displayerDiv>
    <div class=metricHead>Investigator Metrics</div>
    <div>{$graphics['grphinvest']}</div>
  </div>

  <div class=displayerDiv>
    <div class=metricHead>QMS Metrics</div>
    <div>{$graphics['hprpie']}</div>
  </div>

<div id=multiInfoLine>

  <div class=infoHold  onclick="navigateSite('further-action-requests');">
    <div class=iLabel>Your Further Actions</div>
    <div id=fadatadsp> 
      <div class=sidelbl>Your Tickets</div><div class=sidedata>{$faCnt[0]['assignedme']}</div>
      <div class=sidelbl>Non-Assigned</div><div class=sidedata>{$faCnt[1]['assignedme']}</div>
      <div class=sidelbl id=takerlink>Click here to go to Futher Actions </div>
    </div>
  </div>
  
  <div class=infoHold>
    <div class=iLabel>Shipped This Year</div>
    <div class=iData>{$s['shipttl']}</div>
  </div>


  <div class=infoHold>
    <div class=iLabel>QMS Slides Submitted</div>
    <div class=iData>{$qmssub}</div>
  </div>
  
  <div class=infoHold>
    <div class=iLabel>On Offer/Manifests (HUP)</div>
    <div class=iData>{$ooLine}</div>
  </div>
    
   <div id=starrating>
      <div class=iLabel>Survey Star Rating</div>
      <div id=starholdercntdsp>
        {$innerStarsHere}
      </div>
      <div class=iLabelA>Star Rating: {$starrating}</div> 
    </div>

</div>

<div class=displayDivSmlr>

    <div id=anotherMetricDspA><div class=iLabel>Pristine Segment Assignments (Exclude QMS)</div><div>{$graphics['grphprisassign']}</div></div>
    <div id=weekship><div class=metricHead>Week Ship-Listing (Scrollable)</div><div id=weekshipholder>{$weekshpTbl}</div> </div>

</div>


<div id=multiInfoLineTwo>

  <div class=displayerDivLong>
    <div>{$graphics['grphrollshipgrid']}</div>
  </div>


</div>

</div>
PAGEHERE;
//<div onclick="doTheHotlist();" id=hotListHolder><div class=metricHead>Weekly Hot-List - What's in "high" demand ...</div>    </div>
//<div class=datasqr><div class=datalabel>Shipped This Year</div><div class=datadsp>{$s['shipttl']}</div></div>      
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


//$addLine = "<tr><td class=label>Dual-Authentication Code <span id=dspAuthExpiry>{$authExpire}</span>&nbsp;<span class=pseudoLink id=btnSndAuthCode>(Send Authentication Code)</span></td></tr><tr><td><input type=text id=ssDualCode value=\"{$authCode}\"></td></tr>";
$addLine = "<div class=elementHolder><div class=elementLabel>Dual-Authentication Code <span id=dspAuthExpiry>{$authExpire}</span></div><div class=element><input type=text id=ssDualCode value=\"{$authCode}\"></div></div>";
//$controlBtn = "<table><tr><td class=adminBtn id=btnLoginCtl>Login</td></tr></table>";    
$controlBtn = <<<CONTROLBTNS
<div><table id=btnForgotEmail><tr><td class=adminBtn>Forgot Password</td></tr></table></div>
<div align=right><table width=100% id=btnSndAuthCode><tr><td class=adminBtn><center>Auth Code</td></tr></table></div>
<div align=right><table width=100%><tr><td class=adminBtn id=btnLoginCtl><center>Login</td></tr></table></div>
CONTROLBTNS;

$ssversion = scienceserverTagVersion;


$rtnThis = <<<PAGECONTENT

<div id=loginHolder>        
<div id=loginDialogHead>ScienceServer Specimen Management System Login </div>
<div id=loginGrid>
  <div class=elementHolder><div class=elementLabel>User Id (Email Address)</div><div class=element><input type=text id=ssUser></div></div>
  <div class=elementHolder><div class=elementLabel>Password</div><div class=element><input type=password id=ssPswd></div></div>
  {$addLine}
  <div id=elementHolderBTNs >{$controlBtn}</div>
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
    <td align=right>
        <table border=0 width=100%>
            <tr><td> <table class=tblBtn id=btnHPRReviewAssign style="width: 6vw;" data-hselected="false" onclick="selectRR();"><tr><td style="font-size: 1.3vh;"><center>Review Assignment</td></tr></table> </td>
                    <td width=90%>&nbsp;</td>
                    <td> <table class=tblBtn id=btnHPRReviewSave style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Save</td></tr></table> </td>
                    <td> <table class=tblBtn id=btnHPRReviewNotFit style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Save::Unusable</td></tr></table> </td>
                    <td> <table class=tblBtn id=btnCancel style="width: 6vw;" onclick="navigateSite('hpr-review/{$rurl[2]}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td>
            </tr></table>    </td>
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
<tr><td class=fldLabel>Assigned To (Investigator Id)</td><td class=fldLabel>TQ-Request Id</td><td class=fldLabel>Ship Doc Number</td><td class=fldLabel>Ship Doc Status</td><td class=fldLabel>Inventory Manifest #</td></tr>
<tr>
  <td><div class=suggestionHolder><input type=text id=qryInvestigator class="inputFld"><div id=investSuggestion class=suggestionDisplay>&nbsp;</div></div></td>
  <td><input type=text id=qryREQ class="inputFld" style="width: 10vw;"></td>
  <td><input type=text id=qryShpDocNbr class="inputFld" style="width: 20vw;"></td>
  <td>{$shpsts}</td>
  <td><input type=text id=qryIManifestNbr class="inputFld" style="width: 10vw; text-align: right;"></td>
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

$tt = treeTop;

require(serverkeys . "/sspdo.zck");    
switch ($whichpage) {
  case 'scienceserverhelp':
    if ( trim($additionalinfo) !== "" ) {
          
      $hlpurl = cryptservice( $additionalinfo );
      $pBtn = "<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintDoc border=0 onclick=\"openOutSidePage('{$tt}/print-obj/help-file/{$hlpurl}');\"><tr><td><i class=\"material-icons\">print</i></td><td>Print Document</td></tr></table></td>";
      if ( $whichusr->useremail === 'zacheryv@mail.med.upenn.edu' || $whichusr->useremail === 'dfitzsim@pennmedicine.upenn.edu' ) {  
        $eBtn = "<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintDoc border=0 onclick=\"openEditDocDialog('{$hlpurl}');\"><tr><td><i class=\"material-icons\">edit</i></td><td>Edit Document</td></tr></table></td>";
      }
    }

    if ( $whichusr->useremail === 'zacheryv@mail.med.upenn.edu' || $whichusr->useremail === 'dfitzsim@pennmedicine.upenn.edu' ) {
        $nBtn = "<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintDoc border=0 onclick=\"openNewDocDialog();\"><tr><td><i class=\"material-icons\">note_add</i></td><td>New Document</td></tr></table></td>";
    } 

    $innerBar = <<<BTNTBL
<tr>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSDLookup border=0 onclick="displayIndex();"><tr><td><i class="material-icons">list</i></td><td>Display Index</td></tr></table></td>
{$nBtn} 
{$eBtn} 
{$pBtn}
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnHelpTicket border=0 onclick="openHelpTicket();"><tr><td><i class="material-icons">build</i></td><td>Open Help Ticket</td></tr></table></td>
</tr>
BTNTBL;
    break;
  case 'useradministration': 
    $innerBar = <<<BTNTBL
<tr>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSDLookup border=0><tr><td><i class="material-icons">fiber_new</i></td><td>New User</td></tr></table></td> 
 </tr>
BTNTBL;
      break;    
case 'shipdocedit':
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnCreateNewSD border=0><tr><td><i class="material-icons">fiber_new</i></td><td>New Ship-Doc</td></tr></table></td>       
    //<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnVoidSD border=0><tr><td><i class="material-icons">block</i></td><td>Void</td></tr></table></td>         
    $innerBar = <<<BTNTBL
<tr>
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSDLookup border=0><tr><td><i class="material-icons">layers_clear</i></td><td>Look-Up</td></tr></table></td> 
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnSaveSD border=0><tr><td><i class="material-icons">save</i></td><td>Save</td></tr></table></td> 
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintSD border=0><tr><td><i class="material-icons">print</i></td><td>Print</td></tr></table></td>             
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSegment border=0><tr><td><i class="material-icons">add_circle_outline</i></td><td>Add Segment</td></tr></table></td>         
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSpcSrvcFee border=0><tr><td><i class="material-icons">add_circle_outline</i></td><td>Add Special Service</td></tr></table></td>         
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnShipOverride border=0><tr><td><i class="material-icons">local_shipping</i></td><td>Ship Override</td></tr></table></td>         
<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSO border=0><tr><td><i class="material-icons">monetization_on</i></td><td>Add Sales Order</td></tr></table></td>         
</tr>        
BTNTBL;
    break;
case 'inventorymanifest':
//<td class=topBtnHolderCell onclick="generateDialog('dialogListManifests','xxxx-xxxx');"><table class=topBtnDisplayer id=btn border=0><tr><td><!--ICON //--></td><td>List Manifests</td></tr></table></td>       
$innerBar = <<<BTNTBL
<tr>
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
    //  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnConsumeSegment> <tr><td><i class="material-icons">autorenew</i></td><td>Consume Segment</td></tr></table></td>  
$innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnAddSegment><tr><td><i class="material-icons">add_circle_outline</i></td><td>Add Segment</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnRqstFA><tr><td><i class="material-icons">perm_data_setting</i></td><td>Rqst Further Action</td></tr></table></td>
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
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork onclick="window.location.href= '{$tt}/qms-actions'" ><tr><td><i class="material-icons">layers_clear</i></td><td>Queue List</td></tr></table></td>
  <td class=topBtnHolderCell onclick="revealPR();"><table class=topBtnDisplayer id=btnRevealPR><tr><td><i class="material-icons">arrow_right_alt</i></td><td>Pathology Report</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnRqstFA><tr><td><i class="material-icons">perm_data_setting</i></td><td>Rqst Further Action</td></tr></table></td>
  <td class=topBtnHolderCell onclick="markQMSComplete();"><table class=topBtnDisplayer id=btnReloadGridWork><tr><td><i class="material-icons">done_all</i></td><td>Mark QA Complete</td></tr></table></td>
  <td class=topBtnHolderCell style="border-left: 4px double rgba(255,255,255,1);" onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnSendEmail><tr><td><i class="material-icons">textsms</i></td><td>Email</td></tr></table></td>
  
</tr>
BTNTBL;

  //<td class=topBtnHolderCell onclick="generateDialog('qmsManageMoleTst','xxxx-xxxx');"><table class=topBtnDisplayer id=btnReloadGridWork><tr><td><i class="material-icons">list</i></td><td>Manage Immuno/Molecular Values</td></tr></table></td>


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


case 'inventory':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintLocationCard ><tr><td><i class="material-icons">print</i></td><td>Location Card</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintBCCard ><tr><td><i class="material-icons">print</i></td><td>Barcode Tag</td></tr></table></td> 
</tr>
BTNTBL;
    break;
case 'imanifest':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintLocationCard ><tr><td><i class="material-icons">print</i></td><td>Location Card</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintBCCard ><tr><td><i class="material-icons">print</i></td><td>Barcode Tag</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintSlides ><tr><td><i class="material-icons">print</i></td><td>Slides On Manifest</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintFrozens ><tr><td><i class="material-icons">print</i></td><td>Frozens On Manifest</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintPB ><tr><td><i class="material-icons">print</i></td><td>Fixed On Manifest</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnPrintICard ><tr><td><i class="material-icons">print</i></td><td>ICards</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnNxtManifest ><tr><td><i class="material-icons">next_plan</i></td><td>Next Manifest</td></tr></table></td> 
</tr>
BTNTBL;

    break;
case 'faTable':

    $agentListSQL = "SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and allowInd = 1 and primaryInstCode = 'HUP' order by friendlyname";
    $agentListRS = $conn->prepare($agentListSQL); 
    $agentListRS->execute();

    $innerdrop = "";      
    while ( $agnt = $agentListRS->fetch(PDO::FETCH_ASSOC) ) {
      $agentcode = cryptservice ( $agnt['originalaccountname'] . "::" . date('HimdY'), 'e');  
      $innerdrop .= "<tr class=btnBarDropMenuItem id=btnDisplay_ALL onclick=\"navigateSite('further-action-requests/sort-by/{$agentcode}');\"><td><i class=\"material-icons\">arrow_right</i></td><td class=displaytypetext>Display: {$agnt['dspagent']}&nbsp;&nbsp;&nbsp</td></tr>";    
    }

    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnExport onclick="generateDialog('bgRqstFA', 'xxx-xxxx' );" ><tr><td><i class="material-icons">post_add</i></td><td>New Ticket</td></tr></table></td> 
  <td class=topBtnHolderCell>
    <div class=ttholder>
      <table class=topBtnDisplayer id=btnDisplayByStatus><tr><td><i class="material-icons">view_list</i></td><td>Display By Agent</td></tr></table>
      <div class=tt>
        <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
          <tr class=btnBarDropMenuItem id=btnDisplay_ALL onclick="navigateSite('further-action-requests');"><td><i class="material-icons">arrow_right</i></td><td class=displaytypetext>Display: ALL&nbsp;&nbsp;&nbsp</td></tr>    
          {$innerdrop}
        </table>
      </div>  
    </div>
  </td>           
</tr>
BTNTBL;
    break;

case 'faTableEdit':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork onclick="window.location.href= '{$tt}/further-action-requests'" ><tr><td><i class="material-icons">layers_clear</i></td><td>Queue List</td></tr></table></td>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnExport onclick="generateDialog('bgRqstFA', 'xxx-xxxx' );" ><tr><td><i class="material-icons">post_add</i></td><td>New Ticket</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnExport onclick="printThisTicket();"><tr><td><i class="material-icons">print</i></td><td>Print Ticket</td></tr></table></td> 
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnExport onclick="sendThisTicket();"><tr><td><i class="material-icons">mail</i></td><td>Send Ticket</td></tr></table></td> 
</tr>
BTNTBL;
    break;


case 'qmsactionworkincon':
    $innerBar = <<<BTNTBL
<tr>
  <td class=topBtnHolderCell><table class=topBtnDisplayer id=btnReloadGridWork onclick="window.location.href= '{$additionalinfo}'" ><tr><td><i class="material-icons">layers_clear</i></td><td>Queue List</td></tr></table></td>
  <td class=topBtnHolderCell onclick="generateDialog('hprAssistEmailer','xxx-xxx');"><table class=topBtnDisplayer id=btnSendEmail><tr><td><i class="material-icons">textsms</i></td><td>Email</td></tr></table></td>
  
  <td class=topBtnHolderCell  style="border-left: 4px double rgba(255,255,255,1);" onclick="markIQMSComplete();"><table class=topBtnDisplayer id=btnReloadGridWork><tr><td><i class="material-icons">done_all</i></td><td>Mark QA Complete</td></tr></table></td>

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

    $ovrRdBtn = ( $whichusr->accesslevel === 'ADMINISTRATOR' ||  $whichusr->useremail === 'andrea.stone@towerhealth.org' ) ? "<td class=topBtnHolderCell><table class=topBtnDisplayer id=btnBarRsltInventoryOverride><tr><td><i class=\"material-icons\">blur_linear</i></td><td>Check-In Override</td></tr></table></td>" : "" ;
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
      <table class=topBtnDisplayer id=btnAssignGrouping><tr><td><i class="material-icons">insert_link</i></td><td>Status/Assign &amp; Linkage</td></tr></table>
      <div class=tt>
        <table class=btnBarDropMenuItems cellspacing=0 cellpadding=0 border=0>
          <tr class=btnBarDropMenuItem id=btnBarRsltAssignSample><td><i class="material-icons">arrow_right</i></td><td>Assign/Status Segments &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnBarRsltRequestLink><td><i class="material-icons">arrow_right</i></td><td>Create Request Linkage &nbsp;&nbsp;&nbsp</td></tr>     
          <tr class=btnBarDropMenuItem id=btnBarLongTermMarker><td><i class="material-icons">arrow_right</i></td><td>Toggle Long-Term Storage &nbsp;&nbsp;&nbsp</td></tr>     
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

  {$ovrRdBtn}

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

function bldHelpDocumentNewDialogBox () { 
  require(serverkeys . "/sspdo.zck");


//TODO: DON'T HARD CODE THIS
$typem = "<table border=0 class=menuDropTbl>";
$typem .= "<tr><td onclick=\"fillField('fldDocType','SCREEN','SS-Screen Instructions/SOP');\" class=ddMenuItem>SS-Screen Instructions/SOP</td></tr>";
$typem .= "<tr><td onclick=\"fillField('fldDocType','TOPIC','Topic/SOP');\" class=ddMenuItem>Topic/SOP</td></tr>";
//$typem .= "<tr><td onclick=\"fillField('fldDocType','FUNCTIONALITYDESC','Functionality/Technical Documentation');\" class=ddMenuItem>Functionality/Technical Documentation</td></tr>";
$typem .= "</table>";
$doctypemnu = "<div class=menuHolderDiv><input type=hidden id=fldDocTypeValue value=\"\"><input type=text id=fldDocType READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown id=ddDocType>{$typem}</div></div>";

//TODO; MAKE THIS A SERVICE
$modListRS = $conn->prepare( "SELECT moduleid, module FROM four.base_ssv7_help_index_modulelist where dspind = 1 order by dsporder" );
$modListRS->execute();
$modulelisting = "";
$allowCntr = 0;
foreach ( $modListRS as $mkey => $mval ) {
  $modInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" onchange=\"\" class=\"checkboxThreeInput modchkbox\" id=\"m{$mval['moduleid']}\" {$chkd} /><label for=\"m{$mval['moduleid']}\"></label></div>";
  $modulelisting .= "<div class=modDspDiv><div class=allowLabelDsp>" . $mval['module']  . "</div><div class=allowIndicator align=right>{$modInd}</div></div>";
  $allowCntr++;
}

    //TODO:: MAKE A SERVICE
    $sectRS = $conn->prepare( "SELECT menuvalue, dspvalue, dsporder FROM four.sys_master_menus where menu = 'SOPHELPSECTIONS' and dspInd = 1 order by dsporder" ); 
    $sectRS->execute(); 
    $sectm = "<table border=0 class=menuDropTbl>";
    while ( $s = $sectRS->fetch(PDO::FETCH_ASSOC) ) { 
      $sectm .= "<tr><td onclick=\"fillField('fldSectionList','{$s['menuvalue']}','{$s['dspvalue']}');\" class=ddMenuItem>{$s['dspvalue']}</td></tr>";
    }
    $sectm .= "</table>";
    $sectlistmnu = "<div class=menuHolderDiv><input type=hidden id=fldSectionListValue value=\"\"><input type=text id=fldSectionList READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown id=ddDocType>{$sectm}</div></div><div id=btnAddSection onclick=\"makeNewSection();\"> + </div><div id=docInstructions><b>Instructions</b>: Once the document header is saved (This creates a document in the system), the user may select sections off the section menu (left of these instructions).  Click the plus button (+).  This will add the section to the document.  In the final document the sections are always kept in the ScienceServer Document Section Order - not the order that they were added to the screen.  Once you have finished filling in all sections, click the \"Save Sections\" button below.  This will save all sections to the document.  To remove a section, click the delete button ( &times; ) above that sections text. (Basic HTML can be used in these sections).   </div>";


$rtnThis = <<<RTNTHIS
<div id=docHead>

  <div class=dataelementhold>
    <div class=dataelementlabel>Doc-ID</div>
    <div class=dataelement><input type=text READONLY id=fldDocId></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Type</div>
    <div class=dataelement>{$doctypemnu}</div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Document Title</div>
    <div class=dataelement><input type=text id=fldDocTitle></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Document Sub-Title</div>
    <div class=dataelement><input type=text id=fldDocSubTitle></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Screen Ref (IT Staff Only)</div>
    <div class=dataelement><input type=text id=fldScreenRef></div>
  </div>

</div>

<div id=docCtlLine>

  <div id=modsList>
    <div id=modsListHead>ScienceServer Help Modules</div> 
    {$modulelisting}
  </div>
 
  <div id=ctlBox align=right>
    <button id=saveDocButton onclick="saveDocument();">Save Document Head</button>
  </div>

</div>

<div id=sectiondisplay>
  <div id=sectionselectordiv>{$sectlistmnu}</div>
  <div id=workbenchdiv>  </div>
  <div id=sectionworkbuttons align=right><button id=saveSection onclick="saveScreenSections();">Save Sections</button></div>
</div>

RTNTHIS;

  return $rtnThis;
}

function bldHelpDocumentEditDialogBox ( $passedData ) { 

  session_start(); 
  $sid = session_id();
  $pdta = json_decode($passedData,true);
  //{"helpdocid":"reportingmodule"} 

  //TODO:  MAKE THIS A WEBSERVICE
  require(serverkeys . "/sspdo.zck");
  $hlpHeadRS = $conn->prepare( "SELECT hlpid, helptype, title, subtitle, screenreference FROM four.base_ss7_help where replace(helpurl,'-','') = :hlpurl and helpdspind = 1" );
  $hlpHeadRS->execute( array( ':hlpurl' => $pdta['helpdocid'] ));

  if ( $hlpHeadRS->rowCount() <> 1 ) {
    $rtnThis = <<<RTNTHIS
HELP DOCUMENT ({$pdta['helpdocid']}) NOT FOUND! 
RTNTHIS;
  } else { 
    $hlpHead = $hlpHeadRS->fetch(PDO::FETCH_ASSOC);

    //TODO: DON'T HARD CODE THIS 
    $typem = "<table border=0 class=menuDropTbl>";
    $typem .= "<tr><td onclick=\"fillField('fldDocType','SCREEN','SS-Screen Instructions/SOP');\" class=ddMenuItem>SS-Screen Instructions/SOP</td></tr>";
    $typem .= "<tr><td onclick=\"fillField('fldDocType','TOPIC','Topic/SOP');\" class=ddMenuItem>Topic/SOP</td></tr>";
    //$typem .= "<tr><td onclick=\"fillField('fldDocType','FUNCTIONALITYDESC','Functionality/Technical Documentation');\" class=ddMenuItem>Functionality/Technical Documentation</td></tr>";
    $typem .= "</table>";
    $htv = "";
    $htd = "";
    switch ( $hlpHead['helptype'] ) {
      case 'SCREEN':
        $htv = 'SCREEN';
        $htd = 'SS-Screen Instructions/SOP';
        break;
      case 'TOPIC':
        $htv = 'TOPIC';
        $htd = 'Topic/SOP';
        break;
      //case 'FUNCTIONALITYDESC':
      //  $htv = 'FUNCTIONALITYDESC';
      //  $htd = 'Functionality/Technical Documentation';
        break;
    }
    $doctypemnu = "<div class=menuHolderDiv><input type=hidden id=fldDocTypeValue value=\"{$htv}\"><input type=text id=fldDocType READONLY class=\"inputFld\" value=\"{$htd}\"><div class=valueDropDown id=ddDocType>{$typem}</div></div>";

    //TODO:: MAKE A SERVICE
    $sectRS = $conn->prepare( "SELECT menuvalue, dspvalue, dsporder FROM four.sys_master_menus where menu = 'SOPHELPSECTIONS' and dspInd = 1 order by dsporder" ); 
    $sectRS->execute(); 
    $sectm = "<table border=0 class=menuDropTbl>";
    while ( $s = $sectRS->fetch(PDO::FETCH_ASSOC) ) { 
      $sectm .= "<tr><td onclick=\"fillField('fldSectionList','{$s['menuvalue']}','{$s['dspvalue']}');\" class=ddMenuItem>{$s['dspvalue']}</td></tr>";
    }
    $sectm .= "</table>";
    $sectlistmnu = "<div class=menuHolderDiv><input type=hidden id=fldSectionListValue value=\"\"><input type=text id=fldSectionList READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown id=ddDocType>{$sectm}</div></div><div id=btnAddSection onclick=\"makeNewSection();\"> + </div><div id=docInstructions><b>Instructions</b>: Once the document header is saved (This creates a document in the system), the user may select sections off the section menu (left of these instructions).  Click the plus button (+).  This will add the section to the document.  In the final document the sections are always kept in the ScienceServer Document Section Order - not the order that they were added to the screen.  Once you have finished filling in all sections, click the \"Save Sections\" button below.  This will save all sections to the document.  To remove a section, click the delete button ( &times; ) above that sections text. (Basic HTML can be used in these sections).  </div>";


//TODO; MAKE THESE QUERIES A SERVICE
$docModListRS = $conn->prepare( "SELECT md.module FROM four.base_ss7_help_doc_to_idx hdi left join four.base_ssv7_help_index_modulelist md on hdi.modindxid = md.moduleid where hdi.dspind = 1 and md.dspind = 1 and hdi.helpdocid = :hlpid order by md.dsporder" );
$docModListRS->execute( array( ':hlpid' => $hlpHead['hlpid'] ));
$docModList = $docModListRS->fetchAll(PDO::FETCH_ASSOC);
$dm = array();
foreach ( $docModList as $v ) { 
  $dm[] = $v['module'];
}

$modListRS = $conn->prepare( "SELECT moduleid, module FROM four.base_ssv7_help_index_modulelist where dspind = 1 order by dsporder" );
$modListRS->execute();
$modulelisting = "";
$allowCntr = 0;
foreach ( $modListRS as $mkey => $mval ) {
  $chkd = ( in_array( $mval['module'], $dm ) ) ? " CHECKED " : "";   
  $modInd = "<div class=\"checkboxThree\"><input type=\"checkbox\" class=\"checkboxThreeInput modchkbox\" id=\"m{$mval['moduleid']}\" {$chkd} /><label for=\"m{$mval['moduleid']}\"></label></div>";
  $modulelisting .= "<div class=modDspDiv><div class=allowLabelDsp>" . $mval['module']  . "</div><div class=allowIndicator align=right>{$modInd}</div></div>";
  $allowCntr++;
}

$sectionRS = $conn->prepare( "SELECT sechd.menuvalue as sectionid, sechd.dspvalue as sectiondsp, sechd.dsporder as delimit, sec.sectiontext, sec.bywhom, date_format(sec.onwhen,'%m/%d/%Y') as onwhen FROM four.base_ss7_help_doc_sections sec left join ( SELECT menuvalue, dspvalue, dsporder FROM four.sys_master_menus where menu = 'SOPHELPSECTIONS' and dspInd = 1 order by dsporder ) sechd on sec.sectionid = sechd.menuvalue where sec.helpdocid = :hlpdocid and sec.activeind = 1 order by sechd.dsporder" );
$sectionRS->execute(array(':hlpdocid' => $hlpHead['hlpid'] ));
if ( $sectionRS->rowCount() > 0 ) { 
  $sectioncount = 0; 
  $sectionnbrmj = 0;
  $sectionnbrdsp = 1;
  $inseccount = 1;
  while ( $s = $sectionRS->fetch(PDO::FETCH_ASSOC) ) { 
    if ( (int)$s['delimit'] !== $sectionnbrmj ) {
      $sectionnbrdsp++;  
      $sectionnbrmj = (int)$s['delimit'];
      $inseccount = 1;
    }

    $secdspnbr = substr( ('000'.$sectionnbrdsp) , -2) . "." . substr(('0000'.$inseccount),-3);

    $secdsp .= <<<SECTTHIS
<div class=sectionHolderDiv id="sectionbox{$sectioncount}">

  <input type=hidden id='secttype{$sectioncount}' class=sectiondenotesection value='{$s['sectionid']}'>
  <div id='sectiondsp{$sectioncount}' class=sectiondelimitline>
    <div class=sectiondelimit><b>{$secdspnbr}</b> {$s['sectiondsp']}</div>
    <div class=setiondeleter id='secdel{$sectioncount}' onclick="removeSectionFromScreen({$sectioncount});">&times;</div>
  </div>
  <div class=sectiontxtbox><textarea id="sectiontext{$sectioncount}" class=sectxtbox>{$s['sectiontext']}</textarea></div>
  <div class=sectionmetric>{$s['bywhom']} | {$s['onwhen']} </div>

</div>
SECTTHIS;
    $sectioncount++;
    $inseccount++;
  }
}

    $rtnThis = <<<RTNTHIS
<div id=docHead>

  <div class=dataelementhold>
    <div class=dataelementlabel>Doc-ID</div>
    <div class=dataelement><input type=text READONLY id=fldDocId value="{$hlpHead['hlpid']}"></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Type</div>
    <div class=dataelement>{$doctypemnu}</div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Document Title</div>
    <div class=dataelement><input type=text id=fldDocTitle value="{$hlpHead['title']}"></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Document Sub-Title</div>
    <div class=dataelement><input type=text id=fldDocSubTitle value="{$hlpHead['subtitle']}"></div>
  </div>

  <div class=dataelementhold>
    <div class=dataelementlabel>Screen Ref (IT Staff Only)</div>
    <div class=dataelement><input type=text id=fldScreenRef value="{$hlpHead['screenreference']}"></div>
  </div>

</div>

<div id=docCtlLine>

  <div id=modsList>
    <div id=modsListHead>ScienceServer Help Modules</div> 
    {$modulelisting}
  </div>
 
  <div id=ctlBox align=right>
    <button id=saveDocButton onclick="saveDocument();">Save Document Head</button>
  </div>

</div>

<div id=sectiondisplay>
  <div id=sectionselectordiv>{$sectlistmnu}  </div>
  <div id=workbenchdiv>  {$secdsp}  </div>
  <div id=sectionworkbuttons align=right><button id=saveSection onclick="saveScreenSections();">Save Section</button>  </div>
</div>

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

function bldImmunoMoleTestValues ( $dialog, $passedData ) { 

$moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);

foreach ( $moletest['DATA'] as $k => $v ) {
   $tstid = substr(("00000" . $v['menuid']),-5);
   $tstlist .= "<div class=tstListItem><div class=menuidentifier>{$v['menuvalue']} [#{$tstid}]</div><div class=menudspitem>{$v['dspvalue']}</div></div>";
}


$rtnPage = <<<RTNPAGE
<style>

#dialogholder { display: grid; grid-template-columns: 1fr 2fr; grid-gap: 5px; }
#testList { height: 30vh; border: 1px solid #000; overflow: auto; box-sizing: border-box; padding: 5px; }

#headerDiv { font-family: Roboto; font-size: 1.8vh; font-weight: bold; padding-top: .5vh; padding-bottom: .5vh; text-align: center;  }  

.tstListItem { margin-bottom: 10px; border-bottom: 1px solid rgba(145,145,145,.4); padding-bottom: 3px; padding-top: 3px; background: rgba(255,255,255,1);   }
.tstListItem:hover { cursor: pointer; background: rgba(255,248,225,.4); }   
.menuidentifier { font-size: 1vh; font-weight: bold; text-align: right; color: rgba(145,145,145,.3);  } 
.menudspitem { font-family: Roboto; font-size: 1.4vh;   }  

</style>

<div id=headerDiv>Add Molecluar/Immuno Test Values</div>
<div id=dialogholder> 
  <div id=testList>{$tstlist}</div>
  <div id=editor>&nbsp;</div>
</div>
RTNPAGE;
  return $rtnPage;    
}

function bldFurtherActionDialog ( $dialog, $passedData ) { 

$pdta = json_decode( $passedData, true );

require(serverkeys . "/sspdo.zck");
$agentListSQL = "SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP' order by friendlyname";
$agentListRS = $conn->prepare($agentListSQL); 
$agentListRS->execute();
$agnt = "<table border=0 class=menuDropTbl>";
  $agnt .= "<tr><td onclick=\"fillField('faFldAssAgent','','');\" class=ddMenuItem align=right style=\"font-size: 1.1vh;\">[clear]</td></tr>";
while ( $al = $agentListRS->fetch(PDO::FETCH_ASSOC)) { 
  $agnt .= "<tr><td onclick=\"fillField('faFldAssAgent','{$al['originalaccountname']}','{$al['dspagent']}');\" class=ddMenuItem>{$al['dspagent']}</td></tr>";
}
$agnt .= "</table>";
$agntmnu = "<div class=menuHolderDiv><input type=hidden id=faFldAssAgentValue value=\"\"><input type=text id=faFldAssAgent READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown id=ddHTR>{$agnt}</div></div>";

$faaarr = json_decode(callrestapi("GET", dataTree . "/global-menu/further-action-actions",serverIdent, serverpw), true);
$faa = "<table border=0 class=menuDropTbl>";
$givendspvalue = "";
$givendspcode = "";
foreach ($faaarr['DATA'] as $faaval) {
  if ((int)$agval['useasdefault'] === 1 ) { 
    $givendspcode = $agval['lookupvalue'];
    $givendspvalue = $agval['menuvalue'];
  }
  $faa .= "<tr><td onclick=\"fillField('faFldActions','{$faaval['codevalue']}','{$faaval['menuvalue']}');\" class=ddMenuItem>{$faaval['menuvalue']}</td></tr>";
}
$faa .= "</table>";
$faamnu = "<div class=menuHolderDiv><input type=hidden id=faFldActionsValue value=\"{$givendspcode}\"><input type=text id=faFldActions READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddHTR>{$faa}</div></div>";

$htrarr = json_decode(callrestapi("GET", dataTree . "/global-menu/further-action-priorities",serverIdent, serverpw), true);
$agm = "<table border=0 class=menuDropTbl>";
$givendspvalue = "";
$givendspcode = "";
foreach ($htrarr['DATA'] as $agval) {
  if ((int)$agval['useasdefault'] === 1 ) { 
    $givendspcode = $agval['lookupvalue'];
    $givendspvalue = $agval['menuvalue'];
  }
  $agm .= "<tr><td onclick=\"fillField('faFldPriority','{$agval['codevalue']}','{$agval['menuvalue']}');\" class=ddMenuItem>{$agval['menuvalue']}</td></tr>";
}
$agm .= "</table>";
$htrmnu = "<div class=menuHolderDiv><input type=hidden id=faFldPriorityValue value=\"{$givendspcode}\"><input type=text id=faFldPriority READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddHTR>{$agm}</div></div>";

$pastFASQL = "SELECT  substr(concat('000000',idlabactions),-6) as faid , if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind, ifnull(actionstartedind,0) as actionstartedind, ifnull(frommodule,'') as requestingModule , if(ifnull(objbgs,'') = '', ifnull(objpbiosample,'') ,ifnull(objbgs,'')) as biosampleref  , ifnull(assignedagent,'') as assignedagent , ifnull(faact.dspvalue,'') as actiondescription, ifnull(actionnote,'') as actionnote , ifnull(fapri.dspvalue,'-') as dspPriority , if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate , ifnull(actionrequestedby,'') as requestedby , ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon FROM masterrecord.ut_master_furtherlabactions fa left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue where objpbiosample = :pbiosample and activeind = 1 order by idlabactions desc";

$pastFARS = $conn->prepare( $pastFASQL ); 
$pastFARS->execute(array(':pbiosample' => $pdta['pbiosample']));
if ( $pastFARS->rowCount() < 1 ) {
    //TODO: SET PF Default
  $pfaTbl = " - No Further Actions Listed for this reference (Biogroup/Shipdoc) - ";  
} else {
  $pfaTbl = "<table border=0 id=faActionDspTbl><thead><tr><td></td><td>Ticket #</td><td>Completed</td><td>Biosample</td><td>Module</td><td>Action</td><td>Assigned Agent</td><td>Priority<br>Due Date</td><td>Requested By</td></tr></thead><tbody>";   
  while ( $f = $pastFARS->fetch(PDO::FETCH_ASSOC)) { 
     if ( $f['actionstartedind'] === 0) { 
       $rmBtn = "<td onclick=\"deactivateFA('{$f['faid']}');\"><center><i class=\"material-icons rmbtn\">delete_forever</i></td>"; 
     } else {  
       $rmBtn = "<td>&nbsp;</td>";                   
    }      
    $pfaTbl .= "<tr>{$rmBtn}<td>{$f['faid']}</td><td>{$f['completedind']}</td><td>{$f['biosampleref']}</td><td>{$f['requestingModule']}</td><td>{$f['actiondescription']}<br>{$f['actionnote']}</td><td>{$f['assignedagent']}</td><td>{$f['dspPriority']}<br>{$f['duedate']}</td><td>{$f['requestedby']}<br>{$f['requestedon']}</td></tr>";
  }
  $pfaTbl .= "</tbody></table>";
}

$bgdsp = ( trim($pdta['slidebgs']) !== "" ) ? trim($pdta['slidebgs']) : trim($pdta['pbiosample']);

$dspPage = <<<DSPPAGE
<div id=faWrapper>
<table border=0>
  <tr><td id=faHead>Further Actions <input type=hidden id=faFldRequestJSON value='{$passedData}'></td></tr>
  <tr><td>
<table border=0><tr>
  <td><div class=faDivHolder><div class=dspLabel>Biogroup/Shipdoc/Ref# *</div><div class=elemHolder><input type=text id=faFldReference class="inputFld" value="{$bgdsp}"></div></div></td>
  <td><div class=faDivHolder><div class=dspLabel>Action to Take *</div><div class=elemHolder>{$faamnu}</div></div></td>
  <td><div class=faDivHolder><div class=dspLabel>Notes</div><div class=elemHolder><input type=text id=faFldNotes class="inputFld" value=""></div></div></td>
  <td><div class=faDivHolder><div class=dspLabel>Assign Agent</div><div class=elemHolder>{$agntmnu}</div></div></td>
  <td><div class=faDivHolder><div class=dspLabel>Priority *</div><div class=elemHolder>{$htrmnu}</div></div></td>
  <td><div class=faDivHolder><div class=dspLabel>By Date (mm/dd/yyyy)</div><div class=elemHolder><input type=text id=faFldByDate class="inputFld" value=""></div></div></td>
</tr>
<tr><td colspan=6 align=right><input type=checkbox id=faFldNotifyComplete><label for=faFldNotifyComplete>Notify me when complete</label></td></tr>
<tr><td colspan=6 align=right><button type=button onclick="makeFurtherActionRequest();">Save</button></td></tr>
</table>
</td>
</tr>
<tr><td>
<div id=dspOtherFurtherActions>
{$pfaTbl} 

</div>
</td></tr>

</table>
</div>
DSPPAGE;

$rtnPage = <<<RTNPAGE
<style>

#faWrapper { display: grid; margin: 0 5px ; } 
#faHead { grid-row: 1; text-align: center; font-family: Roboto; font-weight: bold; font-size: 1.6vh; color: rgba( 255,255,255,1 ); border-bottom: 1px solid rgba(0,32,113,1); background: rgba(100,149,237,1);  } 
#faRowOne {   }
.dspLabel { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba( 0,32,113,1 ); padding-top: 5px; padding-bottom: 5px; }

input { font-size: 1.2vh; padding: 10px 5px; }

button { background: rgba(255,255,255,1); border: 1px solid rgba(0,32,113,1); padding: 10px 7px; font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba( 0,32,113,1 );  } 
button:hover { background: rgba(255,248,225,.6); cursor: pointer; } 

.ddMenuItem {font-size: 1.4vh; }  

#faFldReference { width: 8vw; }
#faFldPriority { width: 9vw; }
#faFldByDate { width: 8vw; } 
#faFldActions { width: 14vw; } 
#faFldNotes { width: 25vw; }
#faFldAssAgent { width: 10vw; }


#dspOtherFurtherActions {  border: 1px solid rgba( 0,32,113,1 );  height: 15vh; overflow: auto;  } 
#dspOtherFurtherActions #faActionDspTbl { width: 100%; font-size: 1.3vh; color: rgba( 0,32,113,1); }
#dspOtherFurtherActions #faActionDspTbl thead {   }
#dspOtherFurtherActions #faActionDspTbl thead tr td { background: rgba( 0,32, 113, 1); color: rgba( 255,255,255,1); font-weight: bold; } 
#dspOtherFurtherActions #faActionDspTbl tbody tr:nth-child( even ) { background: rgba( 145,145,145,.2 ); }
#dspOtherFurtherActions #faActionDspTbl tbody td { border-bottom: 1px solid rgba( 145,145,145,1); border-right: 1px solid rgba( 145,145,145,1); }  

.rmbtn:hover { color: rgba(237, 35, 0,1); cursor: pointer; }

</style>
{$dspPage}
RTNPAGE;
  return $rtnPage;    
}

function bldInventoryBarcodeTag( $dialog, $passedData ) { 

   $prntarr = json_decode(callrestapi("GET", dataTree . "/thermal-printer-list-dialog",serverIdent, serverpw), true);
   $pcntr = 0;
   foreach ($prntarr['DATA'] as $pky => $pval) { 
     $prnmenu .= "<div id='printer{$pcntr}' class=labelingdsp data-prnname='{$pval['printername']}' data-labelformat='{$pval['formatname']}' data-selected='false' onclick=\"selectPrinter(this.id);\"> <div class=labelDeviceName>{$pval['printernamedsp']}</div><div class=labelDeviceLocation>{$pval['printerlocation']}</div></div>"; 
     $pcntr++;
   }
   $prnmenu .= "<div id='printer{$pcntr}' class=labelingdsp data-prnname='INVCARD' data-labelformat='PRINTCARD'  data-selected='true' onclick=\"selectPrinter(this.id);\"><div class=labelDeviceName>Inventory Barcode Tag</div><div class=labelDeviceLocation>On-Screen Inventory Tag-Card</div></div>";  

   $dspPage = <<<DSPPAGE

    <div id=masterHold>
        <div id=bcTagHold>
                      
              <div id=fldHolder><div class=label>Biosample Label</div><input type=text id=bccodevalue onkeyup="checkBCCodeValue();"></div>

               <div id=keyboardDsp>

                  <div id=keyboardRowHolder1><div class=keyboardBtn onclick="makeBCCodeValue('1');">1</div><div class=keyboardBtn onclick="makeBCCodeValue('2');">2</div><div class=keyboardBtn onclick="makeBCCodeValue('3');">3</div><div class=keyboardBtn onclick="makeBCCodeValue('4');">4</div><div class=keyboardBtn onclick="makeBCCodeValue('5');">5</div><div class=keyboardBtn onclick="makeBCCodeValue('6');">6</div><div class=keyboardBtn onclick="makeBCCodeValue('7');">7</div><div class=keyboardBtn onclick="makeBCCodeValue('8');">8</div><div class=keyboardBtn onclick="makeBCCodeValue('9');">9</div><div class=keyboardBtn onclick="makeBCCodeValue('0');">0</div></div>

                  <div id=keyboardRowHolder2><div class=keyboardBtn onclick="makeBCCodeValue('Q');">Q</div><div class=keyboardBtn onclick="makeBCCodeValue('W');">W</div><div class=keyboardBtn onclick="makeBCCodeValue('E');">E</div><div class=keyboardBtn onclick="makeBCCodeValue('R');">R</div><div class=keyboardBtn onclick="makeBCCodeValue('T');">T</div><div class=keyboardBtn onclick="makeBCCodeValue('Y');">Y</div><div class=keyboardBtn onclick="makeBCCodeValue('U');">U</div><div class=keyboardBtn onclick="makeBCCodeValue('I');">I</div><div class=keyboardBtn onclick="makeBCCodeValue('O');">O</div><div class=keyboardBtn onclick="makeBCCodeValue('P');">P</div></div>
                 
                  <div id=keyboardRowHolder3><div class=keyboardBtn onclick="makeBCCodeValue('A');">A</div><div class=keyboardBtn onclick="makeBCCodeValue('S');">S</div><div class=keyboardBtn onclick="makeBCCodeValue('D');">D</div><div class=keyboardBtn onclick="makeBCCodeValue('F');">F</div><div class=keyboardBtn onclick="makeBCCodeValue('G');">G</div><div class=keyboardBtn onclick="makeBCCodeValue('H');">H</div><div class=keyboardBtn onclick="makeBCCodeValue('J');">J</div><div class=keyboardBtn onclick="makeBCCodeValue('K');">K</div><div class=keyboardBtn onclick="makeBCCodeValue('L');">L</div></div>
                  
                  <div id=keyboardRowHolder4><div class=keyboardBtn onclick="makeBCCodeValue('Z');">Z</div><div class=keyboardBtn onclick="makeBCCodeValue('X');">X</div><div class=keyboardBtn onclick="makeBCCodeValue('C');">C</div><div class=keyboardBtn onclick="makeBCCodeValue('V');">V</div><div class=keyboardBtn onclick="makeBCCodeValue('B');">B</div><div class=keyboardBtn onclick="makeBCCodeValue('N');">N</div><div class=keyboardBtn onclick="makeBCCodeValue('M');">M</div><div class=keyboardBtn onclick="makeBCCodeValue('@');">@</div><div class=keyboardBtn onclick="makeBCCodeValue('-');"><i class="material-icons">backspace</i></div></div>
 
               </div>

              <div id=cmdBtnHolder> <div class=cmdBtn onclick="clearThis();">CLEAR</div><div class=cmdBtn onclick="printRqstBarcodeLabel();">PRINT</div><div class=cmdBtn onclick="closeThisDialog('{$dialog}');">CANCEL</div>    </div>

        </div>        

<div id=printerRealEstate> 
  <div class=label>Choose a Labeling-System</div> 
  <div id=labellerHolder> 
    {$prnmenu}  
  </div> 
</div>

</div>
DSPPAGE;
   
  $rtnPage = <<<RTNPAGE
<style>
  #masterHold { display: grid; grid-template-columns: 4fr 2fr; } 
  #bcTagHold { display: grid; grid-template-rows: 1fr 3fr 1fr; }
  #fldHolder { margin: .6vh .2vw .2vh .2vw; }
  #fldHolder .label {font-size: 1.3vh; font-weight: bold; }
  #fldHolder1 {  margin: .6vh .2vw; }
  #fldHolder1 .label {font-size: 1.3vh; font-weight: bold; }
  #bccodevalue { text-align: center; font-size: 1.9vh; width: 40vw; }         
  #keyboardDsp { display: grid; grid-template-rows: repeat(4, 1fr); margin: .3vh .2vw;  }  
  #keyboardRowHolder1 { display: grid; grid-template-columns: repeat(10, 1fr); grid-gap: 5px; margin-top: .2vh; }        
  #keyboardRowHolder2 { display: grid; grid-template-columns: repeat(10, 1fr); grid-gap: 5px; margin-top: .2vh; }        
  #keyboardRowHolder3 { display: grid; grid-template-columns: repeat(9, 1fr); grid-gap: 5px; margin-top: .2vh; }        
  #keyboardRowHolder4 { display: grid; grid-template-columns: repeat(9, 1fr); grid-gap: 5px; margin-top: .2vh; }        

  .keyboardBtn { border: 1px solid rgba(201,201,201,1); text-align: center; font-size: 6vh; width: 4vw; color: rgba( 180,180,180,1); transition: .9s; }
  #keyboardRowHolder3 .keyboardBtn { width: 4.4vw; } 
  #keyboardRowHolder4 .keyboardBtn { width: 4.4vw; }
  .keyboardBtn:hover { background: rgba(100,149,237,1); color: rgba(255,255,255,1); cursor: pointer;   } 
  .keyboardBtn:focus { background: rgba(100,149,237,1); color: rgba(255,255,255,1); }
  .keyboardBtn .material-icons { font-size: 3vh; } 

  #cmdBtnHolder { display: grid; grid-template-columns: repeat(3, 1fr); grid-gap: 5px; margin: .6vh .2vw; border-top: 2px solid rgba(201,201,201,1); padding-top: .3vh; }
  .cmdBtn { border: 1px solid rgba(201,201,201,1); text-align: center; font-size: 5vh; color: rgba( 190,190,190,1); transition: .9s; padding-top: .6vh; } 
  .cmdBtn:hover { background: rgba(100,149,237,1); color: rgba(255,255,255,1); cursor: pointer;   } 
  .cmdBtn:focus { background: rgba(100,149,237,1); color: rgba(255,255,255,1); }
  .cmdBtn:active { background: rgba(100,149,237,1); color: rgba(255,255,255,1); }
 
  #printerRealEstate { margin: .6vh .2vw .2vh .2vw; display: grid; grid-template-rows: 1.9vh 1fr; } 
  #printerRealEstate .label { font-size: 1.3vh; font-weight: bold; }
  #printerRealEstate #labellerHolder { height: 40vh; overflow: auto;  }
  #printerRealEstate #labellerHolder .labelingdsp { height: 4vh; border: 1px solid rgba(201,201,201,1); margin: .2vh 0; padding: .2vh .2vw; color: rgba(170,170,170,1);}
  #printerRealEstate #labellerHolder .labelingdsp[data-selected='true'] { background: rgba(100,149,237,1); color: rgba(255,255,255,1); } 
  #printerRealEstate #labellerHolder .labelingdsp .labelDeviceName { font-size: 2vh;  }   
  #printerRealEstate #labellerHolder .labelingdsp .labelDeviceLocation { font-size: 1vh; }   

  .labelingdsp {  } 

</style>
   {$dspPage}
RTNPAGE;
  return $rtnPage;    
}

function bldInventoryManifestListing ( $passedData ) { 

  require(serverkeys . "/sspdo.zck");
  session_start();
  $pdta = json_decode( $passedData, true );
  //"{\"whichdialog\":\"dialogListManifests\",\"objid\":\"xxxx-xxxx\",\"dialogid\":\"C1ojvsCrqvKDg3q\"}"
  $objid = json_decode( $pdta['objid'], true );
  
  $p = json_encode( $passedData );
  $who = session_id();
  $usrSQL = "SELECT emailaddress as usrid, originalaccountname as usr, presentinstitution  FROM four.sys_userbase where sessionid = :sessid and allowInd = 1 and allowProc = 1 and TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";
  $usrRS = $conn->prepare( $usrSQL ); 
  $usrRS->execute( array( ':sessid' => $who ));

  ( $usrRS->rowCount() <> 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "USER ACCOUNT NOT FOUND") : "";

  if ( $errorInd === 1 ) { 

      $innerSpace = "<div class=errorMsgHead>ERROR:</div>"; 
      foreach ( $msgArr as $msg ) { 
        $innerSpace .= "<div class=errorMsgLine>{$msg}</div>";
      }

  } else { 

      $u = $usrRS->fetch(PDO::FETCH_ASSOC);
      //{"usrid":"zacheryv@mail.med.upenn.edu","usr":"proczack","presentinstitution":"HUP"}
      $presInstCode = $u['presentinstitution'];
      $mSQL = "SELECT mhd.manifestnbr, concat(ifnull(prefix,''),'-', substr(concat('000000',ifnull(mhd.manifestnbr,'')),-6)) as manifestnbrdsp, mstatus, date_format(manifestdate,'%m/%d/%Y %H:%i') as manifestdatedsp, createdBy, ifnull(mcnt.segOnMani,0) as segOnMani FROM masterrecord.ut_ship_manifest_head mhd left join (SELECT manifestnbr, count(1) segOnMani FROM masterrecord.ut_ship_manifest_segment where includeind = 1 group by manifestnbr) as mcnt on mhd.manifestnbr = mcnt.manifestnbr where mhd.institutioncode = :instCode and mhd.manifestdate between date_add(now(), interval -30 day) and now() order by mhd.manifestdate desc";
      $mRS = $conn->prepare( $mSQL );
      $mRS->execute( array( ':instCode' => $presInstCode  ));

      ( $mRS->rowCount() < 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "NO MANIFESTS FOUND FOR THIS INSTITUTION (Code: {$presInstCode})") : "";

      if ( $errorInd === 1 ) { 

        $innerSpace = "<div class=errorMsgHead>ERROR:</div>"; 
        foreach ( $msgArr as $msg ) { 
          $innerSpace .= "<div class=errorMsgLine>{$msg}</div>";
        }

      } else {  

        $mnTbl = "<div id=manifestHeadHold><div class=mHeader> </div><div class=mHeader> </div><div class=mHeader>Manifest #</div><div class=mHeader>Status</div><div class=mHeader>Segments</div><div class=mHeader>Creation Date / By</div></div>";   
        while ( $m = $mRS->fetch(PDO::FETCH_ASSOC) ) {
          
          $edtIco = ( $m['mstatus'] === 'OPEN' ) ? "<i class=\"material-icons hoverIco\" onclick=\"getThisManifest ('{$m['manifestnbrdsp']}');\">edit</i>" : "&nbsp;" ;

          $mnTbl .= "<div class=manifestRecord><div class=manifestIconOne><i class=\"material-icons hoverIco\" onclick=\"printThisManifest('{$m['manifestnbrdsp']}');\">print</i></div><div class=manifestIconTwo>{$edtIco}</div><div class=manifestNbrDsp>{$m['manifestnbrdsp']}</div><div class=manifestStsDsp>{$m['mstatus']}</div><div class=segonmani>{$m['segOnMani']}</div><div class=manifestMetDsp>{$m['manifestdatedsp']} ({$m['createdBy']})</div></div>";
        }

        $innerSpace = <<<QRYBOX
<input type=hidden id=manifestdialogid value='{$pdta['dialogid']}'>
<div id=mAnnounceHeader>Intra-CHTNEast Inventory Manifests Listing</div>
  <div id=mInstructions>Below is a list of Intra-CHTN Shipment Manifests created in the last 30 days for your present institution (Code: {$presInstCode}). You may select any open manifest to edit.  Any locked manifests may be printed only. If the manifest you are looking for does not appear on this list, you may enter a manifest number in the space at the bottom to retrieve that manifest. </div>
  <div id=mListHolder>{$mnTbl}</div>
  <div id=mQryLine>
    <div class=label>[OR] Enter a manifest #: </div>
    <div><input type=text id=fldQryManifestNbr></div>
    <div><table class=tblBtn id=btnQryGo style="width: 4vw;" onclick="sendToGetThisManifest();"><tr><td style="font-size: 1.5vh; padding: 1.4vh .5vw;"><center>Go!</td></tr></table></div>   
  </div>
  <div align=right><table class=tblBtn id=btnClose style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.5vh; padding: 1.4vh .5vw;"><center>Close</td></tr></table></div>
QRYBOX;
      }
  }


  $rtnPage = <<<RTNPAGE
<style>
  #mAnnounceHeader { font-size: 2vh; font-weight: bold; padding: 1vh .5vw 0 .5vw; width: 60vw; box-sizing: border-box;      }
  #mInstructions { padding: 0 .5vw 1vh .5vw; box-sizing: border-box; width: 60vw; line-height: 1.3em; text-align: justify;  } 
  #mListHolder { margin: 0 .5vw 1vh .5vw; width: 59vw; border: 1px solid #000; height: 20vh; overflow: auto;  } 

  #manifestHeadHold { width: 50vw; display: grid; grid-template-columns: 2vw 2vw 7vw 7vw 5vw auto; grid-gap: .2vw; }
  #manifestHeadHold .mHeader { font-weight: bold; padding: .2vh .2vw; border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3);  }
  
  .manifestRecord { width: 50vw; display: grid; grid-template-columns: 2vw 2vw 7vw 7vw 5vw auto; grid-gap: .2vw;  }

  .manifestRecord .manifestIconOne { padding: .2vh .2vw;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3); text-align: center;}  
  .manifestRecord .manifestIconTwo { padding: .2vh .2vw;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3); text-align: center;}  
  .manifestRecord .manifestNbrDsp { padding: .2vh .2vw;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3);}  
  .manifestRecord .manifestStsDsp { padding: .2vh .2vw;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3);}  
  .manifestRecord .segonmani { padding: .2vh .2vw; text-align: right;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3);} 
  .manifestRecord .manifestMetDsp { padding: .2vh .2vw;  border-bottom: 1px solid rgba(0,0,0,.3); border-right: 1px solid rgba(0,0,0,.3); }  

  #mQryLine { margin: 0 .5vw 1vh .5vw; width: 59vw; border: 1px solid #000; padding: .5vh .2vw;  display: grid; grid-template-columns: 10vw auto; } 
  #mQryLine .label { font-weight: bold; grid-column: span 2;   }
  #fldQryManifestNbr { width: 10vw;    } 

</style>
<div id=dialogPageDsp> 
{$innerSpace} 
</div>
RTNPAGE;
  return $rtnPage;    
}

function bldMoreMetricInfScreen ( $passedData ) { 
  $pdta = json_decode( $passedData, true);
  //{"whichdialog":"moremetrics","objid":"procurement","dialogid":"RgHQE6GfNxkb37S"} 
  $rtnPage = <<<RTNPAGE
<style>
#mmMainDiv { width: 94vw; height: 90vh; overflow: auto;     } 

</style>
<div id=mmMainDiv>

  {$passedData}


</div>
RTNPAGE;
  return $rtnPage;    
}

function bldQuickBuildManifest ( $passedData ) { 
  //{"whichdialog":"rptExtraManSeg","objid":"70","dialogid":"w1JyUMvhRNHZU02"}
  $pdta = json_decode( $passedData, true);

  $rtnPage = <<<RTNPAGE
<style>

#dlgHoldr { width: 40vw; display: grid; grid-template-columns: 18vw auto; grid-gap: .5vw;   } 
#fldDisplayCodeHere { width: 18vw; border: 1px solid #000; margin: .3vh 0 .3vh .2vw; height: 5vh; text-align: center; text-align: center; font-size: 4vh; font-weight: bold; font-family: arial; }
#keyboardHoldr { margin: .3vh 0 .3vh .2vw; display: grid; grid-template-columns: repeat( 3, 1fr); grid-gap: .2vw; width: 18vw; }
.actionKey { border: 1px solid #000; text-align: center; font-size: 4vh; font-weight: bold; font-family: arial; padding: .3vh 0; transition: .5s; }  
.actionKey:hover { cursor: pointer; background: rgba(145,145,145,1); color: rgba(57,255,20,1);  }  
.spannr { grid-column: span 2; } 
.bigSpannr { grid-column: span 3; }

#manListSegSide { overflow: auto; height: 40vh; margin: .3vh .1vw .3vh 0;      } 

#manInstrPara { grid-column: span 2; padding: .5vh .5vw; font-size: 1.5vh; line-height: 1.5em; text-align: justify;   }
#manInstrPara .warning { font-weight: bold; color: rgba(222, 48, 0,1 ); } 
.elmHldr { border: 1px solid #000; margin-bottom: .2vh; display: grid; grid-template-columns: 1vw auto;   }
.elmHldr .elmCHTNLbl { font-size: 1.6vh; font-weight: bold; } 
.elmHldr .elmDataDspr { font-size: 1.4vh; color: rgba(76, 82, 252,1);   } 

.deletr  { grid-row: span 2; text-align: center; font-size: 1.8vh; font-weight: bold; color: rgba(222, 48, 0,1 ); }
.deletr:hover { cursor: pointer; } 

button {  display: block; border: 1px solid rgba( 48,57,71,1); background: rgba(255,255,255, 1); font-size: 2vh; padding: 1vh 0; color: rgba(84,113,210,1); transition: .5s; width: 100%; }
button:hover { cursor: pointer; background: rgba(145,145,145,1); color: rgba(57,255,20,1);   } 
</style>

<input type=hidden id=flddialogid value="{$pdta['dialogid']}">          
<input type=hidden id=fldParentManifest value="{$pdta['objid']}">
<div id=dlgHoldr>
<div id=manInstrPara>
<span class=warning>SOP DEVIATION!</span><br>All manifests should be created at the procurement location from which the segment was sent.  By performing this override, it will be logged in the system as an SOP deviation and will be reported to CHTNEastern. This utility should only be used as a last resort. <i><b>Proper Procedure</b>: Call the issuing institution to create a manifest for the extra segment(s) found in the shipment</i>.   
</div>
  <div id=pullSegSide>
    <input type=text id=fldDisplayCodeHere READONLY>
    <div id=keyboardHoldr>
       <div class=spannr> </div>    
       <div class=actionKey onclick="backr();"> < </div>
 
       <div class=actionKey onclick="bldNwManLabel('7');"> 7 </div>
       <div class=actionKey onclick="bldNwManLabel('8');"> 8 </div>
       <div class=actionKey onclick="bldNwManLabel('9');"> 9 </div>

       <div class=actionKey onclick="bldNwManLabel('4');"> 4 </div>
       <div class=actionKey onclick="bldNwManLabel('5');"> 5 </div>
       <div class=actionKey onclick="bldNwManLabel('6');"> 6 </div>

       <div class=actionKey onclick="bldNwManLabel('1');"> 1 </div>
       <div class=actionKey onclick="bldNwManLabel('2');"> 2 </div>
       <div class=actionKey onclick="bldNwManLabel('3');"> 3 </div>

       <div class="actionKey spannr" onclick="bldNwManLabel('0');"> 0 </div>
       <div class=actionKey onclick="bldNwManLabel('T');"> T </div>

       <div class="actionKey bigSpannr" onclick="addCHTNLabel();"> Add CHTN #</div>

    </div>
  </div>
  <div id=manListSegSide>


  </div>
  <div>&nbsp;</div><div><button onclick="createDeviationManifest();">Create Child Manifest</button></div>
</div>
RTNPAGE;
  return $rtnPage;    

}

function bldSpcSrvSDFee( $passedData ) { 

  require(serverkeys . "/sspdo.zck");
  $pdta = json_decode( $passedData, true );
  $objid = json_decode( $pdta['objid'], true );
  $sd = substr(('000000' . cryptservice($objid['sdency'], 'd' )),-6);

  $sdChkSQL = "SELECT sdstatus, institutiontype, investcode, investname FROM masterrecord.ut_shipdoc where shipdocrefid = :sdnbr and sdstatus <> 'CLOSED' "; 
  $sdChkRS = $conn->prepare( $sdChkSQL );
  $sdChkRS->execute( array( ':sdnbr' => (int)$sd));

  if ( $sdChkRS->rowCount() === 1 ) {
    
    $sdChk = $sdChkRS->fetch(PDO::FETCH_ASSOC);  

    //TODO: Turn this into a webservice
    $feeSQL = "SELECT menuvalue, dspvalue, academValue, commercialValue, additionalInformation FROM four.sys_master_menus where menu = 'SPCSDSRVCFEE' and dspind = 1 order by dspOrder"; 
    $feeRS = $conn->prepare($feeSQL); 
    $feeRS->execute();
    $instMnu = "<table border=0 class=menuDropTbl>";
    while ($f = $feeRS->fetch(PDO::FETCH_ASSOC)) {
        if ( trim($sdChk['institutiontype']) === 'Academic/Non-Profit' ) { 
          $fillRate = "fillField('spcRate','{$f['academValue']}','{$f['academValue']}');";
          if ( $f['additionalInformation'] === 'FL' ) { 
            $fillQty = "fillField('spcQty','1','1');fillField('spcQtyType','FL','FL');";
          } else { 
            $fillQty = "fillField('spcQty','','');fillField('spcQtyType','HR','HR');";
          }
          $actn = "byId('spcQty').focus();";
        } else {
          $fillRate = "fillField('spcRate','{$f['commercialValue']}','{$f['commercialValue']}');";
          if ( $f['additionalInformation'] === 'FL' ) { 
            $fillQty = "fillField('spcQty','1','1');fillField('spcQtyType','FL','FL');";
          } else { 
            $fillQty = "fillField('spcQty','','');fillField('spcQtyType','HR','HR');";
          }
          $actn = "byId('spcQty').focus();";
        }
        $instMnu .= "<tr><td onclick=\"fillField('spcSrvc','{$f['menuvalue']}','{$f['dspvalue']}');{$fillRate}{$fillQty}{$actn}\" class=ddMenuItem>{$f['dspvalue']}</td></tr>";
    } 
    $instMnu .= "</table>";

  
  $rtnPage = <<<RTNPAGE
<style>
#spcRate { width: 4vw; text-align: right; }
#spcQty { width: 4vw; text-align: right; }
#btnAdd { width: 5vw; border: 1px solid #000; text-align: center; padding: 1vh 0; margin-top: 1.8vh; }
#btnAdd:hover { cursor: pointer; background: rgba(100,149,237,1); color: rgba(255,255,255,1); }  
#addLine { display: grid; grid-template-columns: 2fr 4vw 4vw; grid-gap: 10px; margin: .5vh .4vw; } 
</style>
<input type=hidden id=sdsrvcency value='{$objid['sdency']}'>
<input type=hidden id=investType value='{$sdChk['institutiontype']}'>
<div>Add a special service fee to Ship-Doc {$sd} ({$sdChk['investcode']} / {$sdChk['investname']}).</div>
<div id=addLine>
  <div class=menuHolderDiv>
    <div>Special Service</div>
    <input type=hidden id=spcSrvcValue value=""><input type=text id=spcSrvc READONLY value=""><div class=valueDropDown style="width: 21vw;">{$instMnu}</div></div>
  <div>
    <div>Rate</div>
    <input type=text id=spcRate>
  </div>
  <div>
    <div>Qty/Hr</div>
    <input type=text id=spcQty>
    <input type=hidden id=spcQtyType>
  </div>
</div>
<div align=right>
<table><tr><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="saveSDService();"><tr><td style="font-size: 1.3vh;"><center>Add Service</td></tr></table><td><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');location.reload(true);"><tr><td style="font-size: 1.3vh;"><center>Refresh Screen</td></tr></table><td><table class=tblBtn id=btnBGSLookup style="width: 6vw;" onclick="closeThisDialog('{$pdta['dialogid']}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table></td></tr></table>
</div> 
RTNPAGE;
  } else { 
  $rtnPage = <<<RTNPAGE
<style>
</style>
<div>You may not add special service fees to ship-doc {$sd}.  Either the ship-doc doesn't exist or is already 'CLOSED'</div>
RTNPAGE;
  }
  return $rtnPage;    
}

function bldFAEmailer ( $passedData ) { 
    //{"whichdialog":"faSendTicket","objid":"a2JSdkdxZENhb2ZsTkRCMG1PbnFhZz09","dialogid":"lKtuL2sCkOlYgpw"}
    $pdta = json_decode( $passedData, true );
    require(serverkeys . "/sspdo.zck");
    $agentListSQL = "SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP' order by friendlyname";
    $agentListRS = $conn->prepare($agentListSQL); 
    $agentListRS->execute();
    $agnt = "<table border=0 class=menuDropTbl>";
      $agnt .= "<tr><td onclick=\"fillField('faFldEmlAgent','','');\" class=ddMenuItem align=right style=\"font-size: 1.1vh;\">[clear]</td></tr>";
      while ( $al = $agentListRS->fetch(PDO::FETCH_ASSOC)) { 
        $agnt .= "<tr><td onclick=\"fillField('faFldEmlAgent','{$al['originalaccountname']}','{$al['dspagent']}');\" class=ddMenuItem>{$al['dspagent']}</td></tr>";
      }
      $agnt .= "</table>";
    $agntmnu = "<div class=menuHolderDiv><input type=hidden id=faFldEmlAgentValue value=\"\"><input type=text id=faFldEmlAgent READONLY class=\"inputFld\" value=\"\"><div class=valueDropDown style=\"width: 25vw;\">{$agnt}</div></div>";

    $ticketnbr = cryptservice( $pdta['objid'] ,'d');

  $rtnPage = <<<RTNPAGE
<style>
  .ctlBtn { border: 1px solid rgba( 201,201,201,1); font-size: 1.8vh; color: rgba(201,201,201,1); background: rgba(255,255,255,1); transition: .5s; }
  .ctlBtn tr td { padding: .3vh .3vw; }  
  .ctlBtn:hover { background: rgba(100,149,237,1); color: rgba(255,255,255,1); cursor: pointer; }
  #faSendEmailText { width: 25vw; height: 15vh; resize: none; }
  .sflabel { font-size: 1.3vh; font-weight: bold; padding-top: .8vh;  }  
</style>
<input type=hidden id=encyFASendTicket value="{$pdta['objid']}">
<input type=hidden id=faDialogId value="{$pdta['dialogid']}">
<div id=pageholder>
   <div id=row1><div class=sflabel>Team Member to Email</div>{$agntmnu}</div>
   <div id=row2><div class=sflabel>Email Text (A PDF of the Ticket will be attached to Email)</div><textarea id=faSendEmailText></textarea></div>
   <div id=row3 align=right><table><tr><td>  <table onclick="emailThisHereTicket();" class=ctlBtn><tr><td>Send Email</td></tr></table> <td><td> <table onclick="closeThisDialog('{$pdta['dialogid']}');" class=ctlBtn><tr><td>Cancel</td></tr></table> </td></tr></table> </div>
</div>
RTNPAGE;
  return $rtnPage;    
}

function bldLocationPlacardRequest ( $dialog, $passedData ) { 
 
   $tt = treeTop;
   $invListWS = json_decode( callrestapi("GET", dataTree . "/inventory-hierarchy",serverIdent, serverpw), true);
   $iloc = $invListWS['DATA'];
   $inner = "<div class=rootnode><div class=\"controlelements\">&#10148;</div><div class=dataelement>{$iloc['root']['locationdsp']}</div></div><div id=topContainer>";
   foreach ( $iloc['root']['child'] as $ky => $vl ) {
     $scode = cryptservice( "iloccard::" . $vl['scancode'],'e');  
     $ccode = cryptservice( "ilocmap::" . $vl['scancode'],'e');  
     $printer = ( (int)$vl['hierarchybottom'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$scode}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : ""; 
     $conicon =  ( (int)$vl['containerind'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$ccode}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";
     $inner .= "<div class=elementnode><div>&nbsp;</div><div class=\"controlelements\">&#10148;</div><div class=dataelement>{$conicon}&nbsp;{$vl['locationdsp']} [{$vl['locationtype']}]{$printer}</div></div>";
     foreach ( $vl['child'] as $kyc => $vlc ) { 
         $ccodea = cryptservice( "ilocmap::" . $vlc['scancode'],'e');  
         $scodea = cryptservice( "iloccard::" . $vlc['scancode'],'e');  
         $printerc = ( (int)$vlc['hierarchybottom'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$scodea}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";  
         $conicona =  ( (int)$vlc['containerind'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$ccodea}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";
         $inner .= "<div class=elementnodel1> 
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div class=\"controlelements\">&#10148;</div>
                                 <div class=dataelement>{$conicona}&nbsp;{$vlc['locationdsp']} [{$vlc['locationtype']}] {$printerc}</div>
                     </div>";
         foreach ( $vlc['child'] as $kycc => $vlcc) {  
             $ccodeb = cryptservice( "ilocmap::" . $vlcc['scancode'],'e');  
             $scodeb = cryptservice( "iloccard::" . $vlcc['scancode'],'e');  
             $printercc = ( (int)$vlcc['hierarchybottom'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$scodeb}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";  
             $coniconb =  ( (int)$vlcc['containerind'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$ccodeb}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";
             $inner .= "<div class=elementnodel2>
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div class=\"controlelements\">&#10148;</div> 
                                 <div class=dataelement>{$coniconb}&nbsp;{$vlcc['locationdsp']} [{$vlcc['locationtype']}] {$printercc}</div></div>";
             foreach ( $vlcc['child'] as $kyccc => $vlccc ) {
               $ccodec = cryptservice( "ilocmap::" . $vlccc['scancode'],'e');  
               $scodec = cryptservice( "iloccard::" . $vlccc['scancode'],'e');  
               $printerccc = ( (int)$vlccc['hierarchybottom'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$scodec}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";  
               $coniconc =  ( (int)$vlccc['containerind'] === 1 ) ? "<a href=\"{$tt}/print-obj/system-object-requests/{$ccodec}\" target=\"_new" . str_replace('.','',uniqid('',true)) . "\">&#128438;</a>" : "";
               $inner .= "<div class=elementnodel3> 
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div>&nbsp;</div>
                                 <div class=\"controlelements\">&#10148;</div>  
                          <div class=dataelement>{$coniconc}&nbsp;{$vlccc['locationdsp']} [{$vlccc['locationtype']}] {$printerccc}</div></div>";
             }
           $inner .= "";
         }
         $inner .= "";
     }
     $inner .= "";
   }
   $inner .= "</div>";

    $dspPage = <<<THISPAGE
<div id=partholder>
<div id=title>CHTNED Five Level Inventory Hierarchy Map</div>
{$inner}
</div>
THISPAGE;

  $rtnPage = <<<RTNPAGE
<style>
#partholder { border: 1px solid #000; height: 70vh; width: 25vw; overflow: auto; }
#partholder #title { font-size: 1.8vh; font-weight: bold; text-align: center; padding: .5vh 0; } 
#partholder .rootnode { display: grid; grid-template-columns:  20px auto; padding: 0 0 0 5px; } 
#partholder #topContainer .elementnode { display: grid; grid-template-columns: 20px 20px auto; padding: 0 0 0 5px; background: rgba(100,149,237,.2); border-top: 1px solid rgba( 48,57,71,1 ); border-bottom: 1px solid rgba( 48,57,71,1 ); padding: .3vh 0;  }
a, a:visited, a:active { text-decoration: none; font-size: 1.6vh; color: blue; }  
a:hover {  color: green; }
  
#partholder #topContainer .elementnodel1 { display: grid; grid-template-columns: 20px 20px 20px auto; padding: 0 0 0 5px; }
#partholder #topContainer .elementnodel2 { display: grid; grid-template-columns: 20px 20px 20px 20px auto; padding: 0 0 0 5px; }
#partholder #topContainer .elementnodel3 { display: grid; grid-template-columns: 20px 20px 20px 20px 20px auto; padding: 0 0 0 5px; }
.controlelements { font-size: 1.2vh; }
.dataelement { font-size: 1.4vh; }  
</style>
   {$dspPage}
RTNPAGE;
  return $rtnPage;  
}

function bldChartReviewBuilder ( $dialog, $passedData ) { 
    //{"whichdialog":"chartbldr","objid":"ZHZEZ3RRWks4cVpaUEV5QmFLRWZjdz09","dialogid":"7qp9tb4qMUWmZyk"} 
    $pdta = json_decode($passedData, true);
    $pbiosample = cryptservice($pdta['objid'], 'd');
    ///CHECK WHETHER THERE IS A CHART FOR THIS BIOGROUP, OTHER BIOGROUPS IN THIS INT-DOUBLE, BY ASSOC GROUP 

    /*
     * 1) Check BG for CRID
     * 2) Check assoc to make sure that all assoc have same crid
     * 3) v1 iteration - do by pbiosample - but this might need to be changed to the readlabel with a 'like' comparison if not all biogroups come up
     *    select pbiosample, read_label, ifnull(associd,'') as associd, ifnull(chartind,0) as chartind, ifnull(crtxtv1id,0) as crtxtv1id, ifnull(pxirace,'') as pxirace, ifnull(pxigender,'') as pxisex, ifnull(pxiage,0) as pxiage from masterrecord.ut_procure_biosample bs where pbiosample = 87106
     *    - OR -
     *    select pbiosample, read_label, ifnull(associd,'') as associd, ifnull(chartind,0) as chartind, ifnull(crtxtv1id,0) as crtxtv1id, ifnull(pxirace,'') as pxirace, ifnull(pxigender,'') as pxisex, ifnull(pxiage,0) as pxiage from masterrecord.ut_procure_biosample bs where replace(read_label,'_','') like '87106%'
        //(NOT PATIENT!!! AS PER DEE - THINK BMI ACROSS YEARS) 
     *
     */

     require(serverkeys . "/sspdo.zck");    
     $chkSQL = "select pbiosample, read_label, ifnull(associd,'') as associd, ifnull(crtxtv1id,0) as crtxtv1id, ifnull(pxirace,'') as pxirace, ifnull(pxigender,'') as pxisex, ifnull(pxiage,0) as pxiage from masterrecord.ut_procure_biosample bs where pbiosample = :bggrp";
     $chkRS = $conn->prepare($chkSQL);
     $chkRS->execute( array( ':bggrp' => $pbiosample ));
     if ( $chkRS->rowCount() === 1 ) {      
         $mnCR = $chkRS->fetch(PDO::FETCH_ASSOC);
         $mnAss = $mnCR['associd']; 
         $mnCRId = $mnCR['crtxtv1id']; //IF = 0 then New
         $echtrvw = ( (int)$mnCRId <> 0 ) ? cryptservice ( substr(( '000000' . (int)$mnCRId ),-6), 'e') : "000000";
         $mnCRBGRead = $mnCR['read_label']; 
         $mnCRRce = $mnCR['pxirace'];
         $mnCRSex = $mnCR['pxisex'];
         $mnCRAge = $mnCR['pxiage'];

         $assSQL = "select pbiosample, replace(read_label,'_','') as readlabel from masterrecord.ut_procure_biosample bs where associd = :assoc"; 
         $assRS = $conn->prepare($assSQL); 
         $assRS->execute( array( ':assoc' => $mnAss )); 

         $bgAssList = "";
         while ( $a = $assRS->fetch(PDO::FETCH_ASSOC)) { 
           $bgAssList .= ( trim($bgAssList) === "" ) ? $a['readlabel'] : ", {$a['readlabel']}"; 
         } 
         $crIDDsp = ( (int)$mnCRId === 0 ) ? "NEW CHART" : substr("000000{$mnCRId}",-6);

         $crDocTxt = "";
         if ( (int)$mnCRId !== 0 ) { 
             //GET CRDoc
           $docSQL = "SELECT * FROM masterrecord.cr_txt_v1 where chartreviewid = :crdocid"; 
           $docRS = $conn->prepare($docSQL); 
           $docRS->execute(array(':crdocid' => (int)$mnCRId ));
           if ( $docRS->rowCount() < 1 ) { 
             $crDocTxt = "ERROR:  CHART REVIEW IS MISSING!  SEE A CHTNEASTERN STAFF MEMBER";
           } else { 
             $cr = $docRS->fetch(PDO::FETCH_ASSOC); 
             $crDocTxt = $cr['crtxt'];
             //CAN ALSO DISPLAY $cr['bywhom'] and $cr['onwhen'] FOR LAST EDITORS
           }
         }


    $rtnPage = <<<RTNPAGE
<style>

#crinstructions { font-size: 1.4vh; padding: 1vh .5vw; width: 50vw; }    

#crmetrics { display: grid; grid-template-columns: repeat( 5, 1fr); grid-gap: .2vw; margin: 1vh .5vw;   } 
#crmetrics .metrichold { border: 1px solid #000; }
#crmetrics .metrichold .metriclbl { background: #000; color: #fff; font-weight: bold; font-size: 1.3vh; padding: .2vh .2vw; }  
#crmetrics .metrichold .metricdata { padding: .4vh .2vw; font-size: 1.5vh; }

#crtext {  }
#crtext .docLabel { margin: 1vh .5vw 0 .5vw; font-size: 1.3vh; font-weight: bold; }  
#crtext #crtexteditor { width: 50vw; margin: .2vh .5vw 1vh .5vw; height: 40vh; border: 1px solid #000; box-sizing: border-box; font-family: Roboto; font-size: 1.3vh;  padding: .6vh .5vw .6vh .5vw; resize: none;   } 

#bttnBarHolder { margin: 0 .5vw 1vh .5vw; } 
#bttnBarHolder #bttnBar { width: 10vw; display: grid; grid-template-columns: repeat( 3, 1fr); grid-gap: .2vw; } 
.crBtn { font-size: 1.2vh; border: 1px solid #000; text-align: center; padding: .5vh .2vw;  background: #fff; }
.crBtn:hover { cursor: pointer; background: #ddd; }   

</style>

<input type=hidden id=fldCRId value={$mnCRId}>
<input type=hidden id=fldCRBGRefd value='{$pbiosample}'>
<input type=hidden id=fldCRAssoc value='{$mnAss}'>

<div id=crinstructions>This is the chart review document editor. Chart Review Text will be attached to this biogroup ({$pbiosample}) and all associative PHI matches (See the 'Chart Applies' list below).  This should be a culminative document - Edit only as necessary. <p><u>Cut/Paste from a notepad ONLY</u>.  <b>Verify formatting of document after save</b>. </div>
<div id=crmetrics>  

<div class=metrichold><div class=metriclbl>Donor Age</div><div class=metricdata>{$mnCRAge}</div></div>
<div class=metrichold><div class=metriclbl>Donor Race</div><div class=metricdata>{$mnCRRce}</div></div>
<div class=metrichold><div class=metriclbl>Donor Sex</div><div class=metricdata>{$mnCRSex}</div></div>
<div class=metrichold><div class=metriclbl>Chart Applies</div><div class=metricdata>{$bgAssList}</div></div>
<div class=metrichold><div class=metriclbl>Chart ID</div><div class=metricdata><span id=CRIdDsp>{$crIDDsp}</span></div></div>

</div>
<div id=crtext>
<div class=docLabel>Document Editor</div>
<textarea id=crtexteditor>{$crDocTxt}</textarea>
</div>
<div id=bttnBarHolder align=right> <div id=bttnBar> <div class=crBtn onclick="savechartreviewdocument();">Save</div><div class=crBtn onclick="printChartReview(event,'{$echtrvw}');">Print</div><div class=crBtn onclick="closeThisDialog('{$dialog}')";>Cancel</div></div></div>
RTNPAGE;
  } else { 
    $rtnPage = <<<RTNPAGE
<h1>ERROR:  See a CHTNEastern Informatics Person!</h1>
RTNPAGE;
  }
  return $rtnPage;  
}

function bldFurtherActionAction ( $dialog, $passedData ) { 
  $pdta = json_decode($passedData, true);         
  $pArr = json_decode( cryptservice( $pdta['objid'], 'd') , true);

  require(serverkeys . "/sspdo.zck");    
  //TODO: TURN INTO WEBSERVICE
  $actionSQL = "SELECT ifnull(dspvalue,'ACTION ERROR') as actiondsp, ifnull(additionalinformation,0) as completeind, parentid FROM four.sys_master_menus where menu = 'FADETAILACTION' and menuvalue = :actioncode";
  $actionRS = $conn->prepare( $actionSQL ); 
  $actionRS->execute(array(':actioncode' => $pArr['actioncode']));
  if ( $actionRS->rowCount() <> 1 ) { 
      $dspPage = "ERROR FINDING ACTION CODE VALUE ... SEE A CHTNEASTERN INFORMATICS STAFF MEMBER";
  } else {
     $act = $actionRS->fetch(PDO::FETCH_ASSOC);
     $ticket = substr(("000000" . $pArr['ticket']),-6);
     $nowDte = date('m/d/Y');

     $warning = ( (int)$act['completeind'] === 1 ) ? "THIS IS THE COMPLETION ACTION FOR THIS ACTION GROUP.  IF YOU CONTINUE, IT WILL MARK THIS ACTIVITY AS COMPLETE AND LOCK THIS ACTION GROUP." : "";
     $complete = (int)$act['completeind'];
     
     $taskcomplete = ( (int)$complete === 0 ) ? "<input type=checkbox id=thistaskcompleteind CHECKED><label for=\"thistaskcompleteind\">This action is now complete. Clicking this box will lock this further action task.</label>" : "";

     
     
     $dspPage = <<<DSPPAGE
<input type=hidden id=fldDialogId value='{$dialog}'>
<input type=hidden id=fldCompleteInd value={$complete}>
<div id=infoHoldingTbl>
  <div class=itmHoldr>
    <div class=fldLbl>Action Performed</div>
    <div class=fldDta>{$act['actiondsp']}<input type=hidden id=fldActionCode value='{$pArr['actioncode']}'></div>
  </div>
  <div class=itmHoldr>
    <div class=fldLbl>Ticket #</div>
    <div class=fldDta>{$ticket}<input type=hidden id=fldTicketNbr value={$pArr['ticket']}></div>
  </div>
  <div class=itmHoldr>
    <div class=fldLbl>Date Performed</div>
    <div class=fldDta><input type=text id=fldDatePerformed value='{$nowDte}'></div>
    <div class=hint>(mm/dd/YYYY)</div> 
  </div>
  <div class=itmHoldr id=secondline>
    <div class=fldLbl>Notes</div>
    <div class=fldDta><TEXTAREA id=fldComments></TEXTAREA></div>
    <div class=hint>Be Specific - Include Details - Who/What/How</div>
    <div>{$taskcomplete}</div>
  </div>
</div>
<div id=warningline>{$warning}</div>
<div id=btnHoldr align=right><button onclick="sendActionUpdate();">Save</button>&nbsp;<button onclick="closeThisDialog('{$dialog}');">Cancel</button></div>
DSPPAGE;
  }




  $rtnPage = <<<RTNPAGE
<style>
   #infoHoldingTbl { font-family: Roboto; font-size: 1.5vh; width: 36vw; display: grid; grid-template-columns: repeat( 3, 1fr); grid-gap: 5px;   }
   .itmHoldr { border-left: 1px solid rgba(0,32,113,.6); border-bottom: 1px solid rgba(0,32,113,.6);} 
   .fldLbl { font-family: Roboto; font-size: 1.3vh; font-weight: bold; padding: 10px 4px 0 4px; }
   .fldDta { font-family: Roboto; font-size: 1.5vh; padding: 0 4px 0 4px; } 
   .hint { font-family: Roboto; font-size: 1vh; color: rgba( 0,32,113,.6); padding: 0 4px 0 4px; }  
   #fldDatePerformed { width: 8vw; font-size: 1.5vh; }
   #fldComments { width: 35vw; }  
   #secondline { grid-column: span 3; grid-row: 2; }
   #warningline { width: 35vw; padding: 10px; text-align: center; font-family: Roboto; font-size: 1.6vh; color: rgba(237, 35, 0,1); font-weight: bold;  }  
   #btnHoldr { width: 36vw; padding: 10px 6px; } 
   button { border: 1px solid rgba(0,32,113,1); background: rgba(255,255,255,1); padding: 10px 4px; width: 3vw; transition: .5s; }
   button:hover { cursor: pointer; background: rgba(0,32,113,1); color: rgba(255,255,255,1); }    
</style>
   {$dspPage}
RTNPAGE;
  return $rtnPage;
}

function bldQMSInvestigatorEmailer ( $dialog, $passedData ) { 
    
$pdta = json_decode ( $passedData, true);     
 
$dta = array();
$dta['refBGS'] = cryptservice ( $pdta['objid'] , 'e' );
$payload = json_encode($dta);
///{"MESSAGE":["INV4506"],"ITEMSFOUND":0,"DATA":{"head":{"bgs":"87906T003","shipdocrefid":"005536"
//,"shippeddate":"06\/20\/2019","prepmethod":"FRESH"
//,"preparation":"DMEM","specimencategory":"MALIGNANT","asite":"KIDNEY","ssite":"","diagnosis":"CARCINOMA"
//,"modifier":"RENAL CELL - CLEAR CELL -CONVENTIONAL"
//,"metsfrom":"","actualshippeddate":"06\/20\/2019","investcode":"INV4506","investemail":"tanya@verseautx.com"
//,"investname":"Dr. Tanya Novobrantseva","investinstitution":"VERSEAU THERAPEUTICS, INC.","investdivision":"Eastern"
//,"courier":"UPS","trackingnbr":"","salesorder":"006884","setupby":"fcortright","ttlsegments":2}
//,"investemails":[{"add_type":"INVESTIGATOR","add_email":"tanya@verseautx.com","add_attn":"Tanya Novobrantseva"}
//,{"add_type":"SHIPPING","add_email":"tanya@verseautx.com","add_attn":"Mohammad Zafari"}
//,{"add_type":"BILLING","add_email":"tanya@verseautx.com","add_attn":"Tanya Novobrantseva"}]
//,"contactemail":[{"con_email":"mohammad.zafari@verseautx.com","condspname":"Mohammad Zafari, Scientist","concomments":""}
//,{"con_email":"ryan@verseautx.com","condspname":"Ryan Phennicle, Scientist","concomments":""}
//,{"con_email":"edie.triano@verseautx.com","condspname":"Edie Triano, Controller"
//,"concomments":"invoices sent to ap@verseautx.com"}
//,{"con_email":"apo@verseautx.com","condspname":"Apo Rosario, Office Manager","concomments":""}]}} 
$edta = json_decode(callrestapi("POST", dataTree . "/data-doers/qa-investigator-emailer-data",serverIdent, serverpw, $payload), true);

$head = $edta['DATA']['head'];

$iemlst = $edta['DATA']['investemails'];
$iemllstdsp = ( trim($head['acceptoremail']) !== "" ) ? "<div onclick=\"emailer_emailselect(this.id);\" id=\"email1\" class=sideemaildspitem data-email=\"{$head['acceptoremail']}\" data-selected='false'><div class=emailDspName>{$head['acceptedby']}</div><div class=emailDspEmail>({$head['acceptoremail']})</div><div class=emailDspType>SHIPMENT ACCEPTED BY</div></div>" : "";
$iemllstdsp .= ( trim($head['investemail']) !== "" ) ? "<div onclick=\"emailer_emailselect(this.id);\" id=\"email2\" class=sideemaildspitem data-email=\"{$head['investemail']}\" data-selected='true'><div class=emailDspName>{$head['investname']}</div><div class=emailDspEmail>({$head['investemail']})</div><div class=emailDspType>SHIPMENT'S INVESTIGATOR</div></div>" : "";

$cntr = 3;
foreach ( $iemlst as $emlkey => $emlval ) { 
    $iemllstdsp .= ( trim($emlval['add_email']) !== "" ) ? "<div onclick=\"emailer_emailselect(this.id);\" id=\"email{$cntr}\" class=sideemaildspitem data-email=\"{$emlval['add_email']}\" data-selected='false'><div class=emailDspName>{$emlval['add_attn']}</div><div class=emailDspEmail>({$emlval['add_email']})</div><div class=emailDspType>{$emlval['add_type']}</div></div>" : "";
    $cntr++;
}
$cemlst = $edta['DATA']['contactemail'];
foreach ( $cemlst as $emlkey => $emlval ) {
    $iemllstdsp .= ( trim($emlval['con_email']) !== "" ) ? "<div onclick=\"emailer_emailselect(this.id);\" id=\"email{$cntr}\" class=sideemaildspitem data-email=\"{$emlval['con_email']}\" data-selected='false'><div class=emailDspName>{$emlval['condspname']}</div><div class=emailDspEmail>({$emlval['con_email']})</div><div class=emailDspType>{$emlval['concomments']}</div></div>" : "";
    $cntr++;
}

$iname = $head['investname'];

$desig = ( trim($head['specimencategory']) !== "" ) ? "[{$head['specimencategory']}]" : ""; 
$desig .= ( trim($head['asite']) !== "" ) ? " " . trim( $head['asite'] )  : "";
$desig .= ( trim($head['ssite']) !== "" ) ? " / {$head['ssite']}" : "";

$desig .= ( trim($head['diagnosis']) !== "" || trim($head['modifier']) !== "" ) ? " :: " : "";
$desig .= ( trim($head['diagnosis']) !== "" ) ? $head['diagnosis'] : "";
$desig .= ( trim($head['modifier']) !== "" ) ? ( trim($head['diagnosis']) !== "" ) ? " / {$head['modifier']} " : "{$head['modifier']}" : "";

$desig .= ( trim($head['metsfrom']) !== "" ) ? " ({$head['metsfrom']})" : "";


$elementallist = " data-bgs='{$head['bgs']}' ";
$elementallist .= " data-shippeddate='{$head['shippeddate']}' ";
$elementallist .= " data-shipdocrefid='{$head['shipdocrefid']}' ";
$elementallist .= " data-prepmethod='{$head['prepmethod']}' ";
$elementallist .= " data-preparation='{$head['preparation']}' ";
$elementallist .= " data-dxspecimencategory='{$head['specimencategory']}' ";
$elementallist .= " data-dxsite='{$head['asite']}' ";
$elementallist .= " data-dxssite='{$head['ssite']}' ";
$elementallist .= " data-dxdx='{$head['diagnosis']}' ";
$elementallist .= " data-dxmod='{$head['modifier']}' ";
$elementallist .= " data-dxmetsfrom='{$head['metsfrom']}' ";
$elementallist .= " data-courier='{$head['courier']}' ";
$elementallist .= " data-tracknbr='{$head['trackingnbr']}' ";
$elementallist .= " data-salesorder='{$head['salesorder']}' ";
$elementallist .= " data-designation='{$desig}' ";
$elementallist .= " data-iname='{$iname}' ";


foreach ( $edta['DATA']['qmsletters'] as $ltky => $ltvl ) { 
  $elementals .= "<div class=elementalInsert onclick=\"getQMSLetter('{$ltvl['menuvalue']}');\">Letter:  {$ltvl['dspvalue']} </div>";
}
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$head['bgs']}');\">Biosample Label</div>";
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$head['shipdocrefid']}');\">Ship-Doc</div>";
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$head['salesorder']}');\">Sales Order</div>";
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$head['courier']}');\">Courier</div>";
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$head['tracknbr']}');\">Track #</div>";
$elementals .= "<div class=elementalInsert onclick=\"insertAtCursor(byId('emailTextGoeshere'),'{$desig}');\">Designation</div>";

$dspPage = <<<DSPPAGE
<div id=emailBuilderHold>
  <div id=headLine>Send a message to Investigator {$head['investname']} &amp; Team</div>
  <div id=availEmails> {$iemllstdsp} </div>
  <div id=textwriter><div id=textWriterArea><textarea id=emailTextGoeshere>Dear {$iname}:\n\n</textarea></div>
  <div id=sendControls> 
    <div id=includeCmds>
      <div><input type=checkbox id=incME><label for=incME>Include a copy to me</label></div>   
      <div><input type=checkbox id=incCHTN><label for=incCHTN>Include a copy to CHTNEast</label></div>   
      <div><input type=checkbox id=incPR><label for=incPR>Include a copy of Pathology Report</label></div>   
      <!-- <div><input type=checkbox id=incSD><label for=incSD>Include a copy of Ship-Doc</label></div> //-->  
    </div>
    <div id=sendBtn><button type=button onclick="sendQMSEmail('{$dialog}');">Send</button></div></div>
  </div>
  <div id=insertElementals {$elementallist}>{$elementals}   </div>
</div>        
DSPPAGE;

$rtnPage = <<<RTNPAGE
<style>
#emailBuilderHold { display: grid; grid-template-columns: repeat( 7, 1fr);  width: 60vw; grid-gap: 5px; }  

#headLine { grid-column: 1 / 8; grid-row: 1; font-family: Roboto; font-size: 1.8vh; color: rgba(48,57,71,1); padding: 8px 5px; } 

#availEmails { grid-column: 1 / 3; grid-row: 2; border: 1px solid rgba(48,57,71,1); height: 60vh; overflow: auto;  }   
#textwriter { grid-column: 3 / 7; grid-row: 2; display: grid; grid-template-rows: repeat( 15, 4vh );  } 
#insertElementals { grid-column: 7 / 8; grid-row: 2; border: 1px solid rgba(48,57,71,1); height: 60vh; overflow: auto;  }   

.elementalInsert { border-bottom: 1px solid rgba(145,145,145,.5); text-align: center; font-size: 1.4vh; padding: 10px; min-height: 3vh; }
.elementalInsert:hover { background: rgba(255,248,225,.5); cursor: pointer; } 

#textwriter #textWriterArea { grid-row: 1 / 14; }
#textwriter #textWriterArea textarea { width: 100%; height: 50vh; border: 1px solid rgba(48,57,71,1); font-family: Roboto; font-size: 1.3vh; color: rgba(48,57,71,1); line-height: 1.4em;  }

#textwriter #sendControls {grid-row: 14 / 16;   display: grid; grid-template-columns: repeat( 5, 1fr );  }
#textwriter #sendControls #includeCmds { grid-column: 1 / 5;  } 

button {border: 1px solid rgba(100,149,237,1); background: rgba(255,255,255,1); padding: .5vh 1vw; font-size: 1.4vh;  color: rgba(48,57,71,1);}
button:hover { background: rgba( 100,149,237,1); color: rgba(255,255,255,1); cursor: pointer; } 



.sideemaildspitem {  padding: 8px 6px; background: rgba(255,255,255,1); border-top: 1px solid rgba(145,145,145,.6);  }
.sideemaildspitem:nth-child(even)  {  background: rgba(145,145,145,.2);  }
.sideemaildspitem:hover  {  background: rgba(255,248,225,.8); cursor: pointer; }
.sideemaildspitem[data-selected='true'] { background: rgba(0, 112, 13,.2); }

.emailDspName { font-family: Roboto; font-size: 1.6vh; } 
.emailDspEmail { font-family: Roboto; font-size: 1.2vh; } 
.emailDspType { text-align: right; color: rgba(189,185,183,1); font-size: 1vh; font-weight: bold; padding: 5px 0 0 0;  } 

        
</style>
   {$dspPage}
RTNPAGE;
  return $rtnPage;    
    
}

function bldQMSGlobalSegmentUpdate( $dialog, $passedData ) { 

  require(serverkeys . "/sspdo.zck"); 
  $pdta = json_decode( $passedData, true );
  $bglist = json_decode($pdta['objid'] , true); 

  //TODO:  TURN INTO WEBSERVICE  
  $chkSQL = "SELECT ifnull(segmentid,'') as segmentid, ifnull(replace(bgs,'_',''),'') as bgs, ifnull(sg.segstatus,'') as segstatus, ifnull(segsts.dspValue,'') as segstatusdsp, ifnull(segsts.assignablestatus,'') as assignablestatus, ifnull(segsts.qmschangeind,'') as qmschangeind, ifnull(date_format(sg.shippeddate,'%m/%d/%Y'),'') as shippeddate, ifnull(sg.shipdocrefid,'') as shipdocrefid FROM masterrecord.ut_procure_segment sg left join (SELECT menuvalue, dspvalue, assignablestatus, commercialvalue as qmschangeind FROM four.sys_master_menus where menu = 'SEGMENTSTATUS') as segsts on sg.segstatus = segsts.menuvalue where replace(bgs,'_','') = :bgs";
  $chkRS = $conn->prepare($chkSQL);
  $genErrorInd = 0; 
  $bgErrorInd = 0;
  foreach ( $bglist as $segkey => $segval ) {
     $chkRS->execute(array(':bgs' => $segval));
     if ( $chkRS->rowCount() < 1 ) {
         $genErrorInd = 1; 
         $msgArr[] = "<div class=errorMsgDsp><b>{$segval}</b> WAS NOT FOUND IN THE DATABASE.  CRITICAL ERROR!</div>";
     } else { 
        $chkArr = $chkRS->fetch(PDO::FETCH_ASSOC);  
        //87895T001 :: Banked - 1 -- QMSCHANGE ... 87895T002 :: Permanent Collection - 1 -- QMSCHANGE ... 87895T003 :: Shipped - 0 -- ... 2019-06-19 00:00:00  
        if ( $chkArr['qmschangeind'] === "" ) { 
            $bgErrorInd = 1; 
            $msgArr[] = "<div class=errorMsgDsp><b>{$segval}</b> IS NOT STATUSED (<b><i>{$chkArr['segstatusdsp']}</i></b>) TO ALLOW CHANGES IN STATUS</div>";
        } else { 
           if ( trim($chkArr['shippeddate']) !== "" ) { 
            $bgErrorInd = 1; 
            $msgArr[] = "<div class=errorMsgDsp><b>{$segval}</b> HAS BEEN SHIPPED (<b><i>{$chkArr['shippeddate']}</i></b>).  STATUS CHANGES ARE NOT ALLOWED</div>";
           } else {
             if ( trim($chkArr['shipdocrefid']) !== "" ) {
               $bgErrorInd = 1; 
               $sdDsp = substr("000000" . $chkArr['shipdocrefid'], -6);
               $msgArr[] = "<div class=errorMsgDsp><b>{$segval}</b> IS LISTED ON A SHIP-DOC (<b><i>{$sdDsp}</i></b>). IT MUST BE REMOVED FROM THE SHIP-DOC BEFORE IT CAN BE RE-STATUSED.</div>";
             } else {    

                 $bgrtn[] = array ( $segval =>  $chkArr['segmentid']);
             }
           }
        }
     }
  } 

  if ( (int)$genErrorInd === 1 || (int)$bgErrorInd === 1 ) { 
    $dspPage = "<h2>ERROR!</h2>";
    $errorCnt = 0;
    foreach ( $msgArr as $ky => $vl ) { 
      $dspPage .= ( $errorCnt < 1 ) ? "{$vl}" : "<br>{$vl}";
      $errorCnt++;    
    }
  } else { 
      //BUILD PAGE HERE
      $segArr = json_encode( $bgrtn );
      
      $dspPage = <<<DSPPAGE
              
<input type=hidden id=fldGlobalSegArrString value={$segArr}>
<table border=0> 
   <tr><td class=fldLabel>Investigator Id</td><td class=fldLabel>Request #</td></tr>
   <tr>
       <td style="width: 10vw;">
         <div class=suggestionHolder>
          <input type=text id=qmsGlobalSelectorAssignInv class="inputFld" onkeyup="selectorInvestigator(); byId('qmsGlobalSelectorAssignReq').value = ''; byId('requestsasked').value = 0;  byId('requestDropDown').innerHTML = ''; ">
          <div id=assignInvestSuggestion class=suggestionDisplay>&nbsp;</div>
         </div>
       </td>
       <td><input type=hidden id=requestsasked value=0>
         <div class=menuHolderDiv onmouseover="byId('assignInvestSuggestion').style.display = 'none'; setAssignsRequests();">
          <input type=text id=qmsGlobalSelectorAssignReq READONLY class="inputFld" style="width: 8vw;">
          <div class=valueDropDown id=requestDropDown style="min-width: 8vw;"></div>
         </div>
       </td>
   </tr>
   <tr><td style="font-size: 1vh">(Suggestions on name, institution, inv#)</td><td></td></tr>
<tr><td colspan=2 align=center> 
  <table cellspacing=0 cellpadding=0 border=0><tr>
    <td><table class=tblBtn id=btnMarkBank style="width: 8vw;" onclick="setQASegStatus('Bank');"><tr><td style=" font-size: 1.1vh;"><center>Bank</td></tr></table></td>

  <td><table class=tblBtn id=btnMarkPCollection  style="width: 8vw;" onclick="setQASegStatus('Permanent Collection');"><tr><td style=" font-size: 1.1vh;"><center>Permanent Collection</td></tr></table></td>

   <td><table class=tblBtn id=btnMarkPDestroy style="width: 8vw;" onclick="setQASegStatus('Pending Destroy');"><tr><td style=" font-size: 1.1vh;"><center>Pending Destroy</td></tr></table></td>
  
   </tr></table>
</td></tr>
<tr><td colspan=2>
<table border=0>
<tr><td class=fldLabel>Status Comments</td></tr>   
<tr><td><TEXTAREA id=qmsGloablSTSComments></TEXTAREA></td></tr>   
</table>
</td></tr>
<tr><td colspan=2 align=right> 
  <table cellspacing=0 cellpadding=0 border=0><tr>
    <td><table class=tblBtn id=btnMarkBank style="width: 6vw;" onclick="saveQMSSegReassign('{$dialogid}');"><tr><td style=" font-size: 1.1vh;"><center>Save</td></tr></table></td>
  </table>
</td></tr>
</table>
DSPPAGE;
  }

  $rtnPage = <<<RTNPAGE
<style>
.errorMsgDsp { font-size: 1.4vh; padding: 0 1vw 0 1vw;   }
.fldLabel { font-size: 1.4vh; font-weight: bold; border-bottom: 1px solid rgba(48,57,71,.6); }
#qmsGloablSTSComments { width: 33vw; }          
</style>
   {$dspPage}
RTNPAGE;
  return $rtnPage;
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
                    <td> <table class=tblBtn id=btnIHPRReviewCancel style="width: 6vw;" onclick="closeThisDialog('{$dialogid}');"><tr><td style="font-size: 1.3vh;"><center>Cancel</td></tr></table> </td>
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
  $headdta = $reviewdta['DATA']['hprhead'];
  $pristine = $reviewdta['DATA']['pristine'];
  $ass = $reviewdta['DATA']['associativelisting'];
  $prc = $reviewdta['DATA']['percentvalues'];
  $mol = $reviewdta['DATA']['moleculartests'];
  
  if ( (int)$headdta['qcind'] === 0 ) {
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
     $hprDecDataDisplay = bldQAWorkbench_hprData( $headdta );
     $assWork = bldQAWorkBench_assDsp ( $ass, $headdta, 1 ); 
     //TODO:  TURN INTO A WEBSERVICE     
     require(serverkeys . "/sspdo.zck");    
     $faSQL = "SELECT actiontype, actionnote, date_format(actionrequestedon,'%m/%d/%Y') as actionrequestedon  FROM masterrecord.ut_hpr_factions where biohpr = :biohpr";
     $faRS = $conn->prepare($faSQL);
     $faRS->execute(array(':biohpr' => (int)$headdta['biohpr']));
     
     if ( $faRS->rowCount() > 0 ) { 
         foreach ( $faRS as $faKey => $faVal ) {
            $addnote = ( trim($faVal['actionnote']) !== "" ) ? "<br>[{$faVal['actionnote']}]" : ""; 
            $faListInner .= "<div class=faListItem>{$faVal['actiontype']} ({$faVal['actionrequestedon']}) {$addnote}</div>";
         }
         $faList = "<div class=dataHolderDiv id=hprDataDecision><div class=datalabel>Further/Lab Action Requested List</div><div class=datadisplay>{$faListInner}</div></div>";                     
     }
     $faArr['requestingmodule'] = "QMS-QA";
     $faArr['biohpr'] = $headdta['biohpr'];
     $faArr['slidebgs'] = $headdta['slidebgs'];
     $faArr['bgreadlabel'] = $headdta['bsreadlabel'];
     $faArr['pbiosample'] = $headdta['pbiosample'];
     $actiondialog = bldFurtherActionDialog ('xxxx-xxxxx', json_encode( $faArr) );
     $workbench = <<<WORKBENCH
<div id=inconWorkbenchWrapper>            
     <div id=inconSideBar>       
            {$hprDecDataDisplay} 
            {$faList}
     </div>
     <div id=inconWorkBenchArea style="border: 1px solid #000;">
     {$actiondialog}
     {$h} 
     </div>
     <div id=associativemess>
            <div id=dataRowFour>Associated Biogroups and All Segments</div>
            <div id=associativeTblDsp>
              {$assWork}
            </div>
     </div>
</div>
            
WORKBENCH;
    $topBtnBar = generatePageTopBtnBar('qmsactionworkincon', "", $_SERVER['HTTP_REFERER'] );
  
  } else { 
    $prnbr = substr(("000000" . $headdta['pathreportid']),-6);
    $uploadline = ( trim($headdta['pruploadedby']) !== "" ) ? "<b>Uploaded</b>: {$headdta['pruploadedby']} :: {$headdta['uploadedon']} (<b>Pathology Report</b>: {$prnbr})" : "";
    $hprDecDataDisplay = bldQAWorkbench_hprData( $headdta );
    $bioBSDataDisplay = bldQAWorkbench_bsData( $headdta, $pristine );
    $prcMoleDataDisplay = bldQAWorkbench_prcTst( $headdta, $prc, $mol );
    $assWork = bldQAWorkBench_assDsp ( $ass, $headdta ); 

     $faArr['requestingmodule'] = "QMS-QA";
     $faArr['biohpr'] = $headdta['biohpr'];
     $faArr['slidebgs'] = $headdta['slidebgs'];
     $faArr['bgreadlabel'] = $headdta['bsreadlabel'];
     $faArr['pbiosample'] = $headdta['pbiosample'];
     $faArrRead = cryptservice ( $headdta['bsreadlabel'] , 'e');
 
    $workbench = <<<WORKBENCHPARTS
<div id=workbenchwrapper>
            <input type=hidden id=fldFAGetter value={$faArrRead}>
<div id=wbrowtwo>
  <div id=wbrevieweddata>{$hprDecDataDisplay}</div>
  <div id=wbpristine>{$bioBSDataDisplay}</div>
  <div id=wbsupprtdata>{$prcMoleDataDisplay}</div>
</div>
<div id=wbrowthree> 
  <div id=dataRowFour>Associated Biogroups and All Segments</div>
  <div id=associativeTblDsp>
  {$assWork}
  </div>
</div>

</div>
<div id=pathologyrptdisplay>
<div class=blueheader><table width=100% border=0 cellpadding=0 cellspacing=0><tr><td style="padding: 2px 0 0 8px;">Pathology Report </td><td align=right style="padding: 2px 8px 0 0;"><i class="material-icons iconind" onclick="revealPR();">arrow_back</i></td></tr></table></div>
<div id=pathologyreporttextdisplay>{$headdta['pathreport']}</div>
<div id=uploadline>{$uploadline}</div>
</div>
WORKBENCHPARTS;
    $topBtnBar = generatePageTopBtnBar('qmsactionwork', "", $_SERVER['HTTP_REFERER'] );
  }
  } else { 

    $bg = cryptservice(  $encryreviewid . "::" . $headdta['pbiosample'], 'e');
    $workbench = <<<WORKBENCH
<input type=hidden id=fldEncyBGRev value='{$bg}'>

<div id=qmsErrorDone>
  <div id=qmsErrorHead>QMS Already Complete</div>
  <div id=qmsAdvisor>QA Review has already been performed on biogroup {$headdta['bsreadlabel']}. Review that information by querying the biogroup on the 'Data Coordinator' Screen.<p>There are two action options:<br> 1) if you just want to mark QA complete with the last actions, click the 'Just Mark Done' button below and the biogroup will be removed from the QMS-QA Queue Listing, or<br>2) To re-perform QA review on this biogroup, click the 'Reset QMS' button.</div> 
  <div id=qmsActionBtns><center> <button id=btnJustMark>Just Mark Done</button> <button id=btnResetQMS>Reset QMS</button></div>
</div>
WORKBENCH;
  }
  $pg = <<<PGCONTENT
{$topBtnBar}
{$workbench}
PGCONTENT;
  return $pg;
}

function bldQAWorkBench_assDsp ( $ass, $headdta, $inconind = 0 ) { 

//ALL ASSOCIATIVE SEGMENTS
$assbg = "";
$assTbl = "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
$assTbl .= "<tr><td class=\"headerCell cellTwo\">Biogroup</td><td class=\"headerCell cellThree\">Designation</td><td class=\"headerCell cellFour\">Review Decision<br>Date of Review</td><td class=\"headerCell cellFive\">QMS Status</td><td class=\"headerCell cellFive\">&nbsp;</td></tr>";
$bggroups = 0;
$segrowcnt = 0;

foreach ( $ass as $asskey => $assval ) { 
    if ( $assbg !== $assval['readlabel'] ) { 
        $innerAss .= ( $bggroups === 0 ) ? "" : "</tbody></table></div>";
        $assTbl .= ( $bggroups === 0 ) ? "" : "<tr><td colspan=6 valign=top>{$innerAss}</td></tr>";

        $mintgreen = ( substr( $assval['readlabel'] ,0, 6) === substr( $headdta['slidebgs'],0,6) ) ? "mintbck" : "standardbck";
        $hprflipper = ( substr( $assval['readlabel'] ,0, 6) === substr( $headdta['slidebgs'],0,6) ) ? "" : "<td title=\"Flip to the Associative Group\" onclick=\"navigateSite('qms-actions/work-bench/" . cryptservice($assval['hprresult']) . "');\"><i class=\"material-icons actionbtnicon\">pageview</i></td>";
        $thisGroupInd = ( substr( $assval['readlabel'] ,0, 6) === substr( $headdta['slidebgs'],0,6) ) ? 1 : 0; 

        if ( $thisGroupInd === 1 ) {
          $bgencry = cryptservice($headdta['pbiosample']);
          $bgSee = "&nbsp;<i class=\"material-icons actionbtnicon\" title=\"Click the 'Heart' to view the Biogroup\" onclick=\"navigateSite('biogroup-definition/{$bgencry}');\" >favorite</i>&nbsp;";  
          $innerAss = "<div class=inassdspthisgroup id=\"segment{$bggroups}\" ><table border=0 width=100% cellspacing=0 cellpadding=0 id=thisworkingtable><thead><tr><td colspan=6><table><tr><td title=\"Select All Segments\" onclick=\"toggleActiveSegmentRecords();\"><i class=\"material-icons actionbtnicon\">select_all</i></td><td onclick=\"restatusSelectedSegments();\" title=\"Restatus Selected Segments\"><i class=\"material-icons actionbtnicon\">apps</i></td></tr></table></td></tr><tr><td class=\"headerCell cellSix\">Segment</td><td class=\"headerCell cellSeven\">Preparation</td><td class=\"headerCell cellSeven\">Metric</td><td class=\"headerCell cellSeven\">Segment Status</td><td class=\"headerCell cellEight\">Assignment</td><td class=\"headerCell cellNine\">Shipping Information</td></tr></thead><tbody>";
        } else { 
          $bgSee = "";    
          $innerAss = "<div class=inassdsp id=\"segment{$bggroups}\" ><table border=0 width=100% cellspacing=0 cellpadding=0><tr><td class=\"headerCell cellSix\">Segment</td><td class=\"headerCell cellSeven\">Preparation</td><td class=\"headerCell cellSeven\">Metric</td><td class=\"headerCell cellSeven\">Segment Status</td> <td class=\"headerCell cellEight\">Assignment</td><td class=\"headerCell cellNine\">Shipping Information</td></tr>";
        }
        $ss = ( trim($assval['subsite']) !== "" ) ? " ({$assval['subsite']})" : "";
        $dx = ( trim($assval['dx']) !== "" ) ? " / {$assval['dx']}" : "";
        $mdx = ( trim($assval['subdx']) !== "" ) ? " ({$assval['subdx']})" : "";
        $met = ( trim($assval['metsite']) !== "") ? " [{$assval['metsite']}]" : "";
        $bglinedsp = ($bggroups + 1);
        $hprresultdsp = ( trim($assval['hprresult']) !== "" ) ? substr( "000000{$assval['hprresult']}",-6) : "";
$assTbl .= <<<ROWLINE
<tr>
  <td valign=top class="dspDataCell {$mintgreen}"><div class=bgheadsee><div>{$bgSee}</div><div>{$assval['readlabel']}</div></div></td>
  <td valign=top class="dspDataCell {$mintgreen}">{$assval['specimencategory']} {$assval['site']}{$ss}{$dx}{$mdx}{$met}</td>
  <td valign=top class="dspDataCell {$mintgreen}">{$assval['hprdecdsp']}<br><span class=smlFont>[{$assval['hpron']} :: {$hprresultdsp}]</span></td>
  <td valign=top class="dspDataCell {$mintgreen}">{$assval['qmsstatus']}</td>
  <td valign=top class="dspDataCell {$mintgreen}" align=right><table cellspacing=0 cellpadding=0><tr>{$hprflipper}<td onclick="displaySegmentList('{$bggroups}',{$thisGroupInd});" title="Display/Hide Segments in this group"><i class="material-icons actionbtnicon">pie_chart</td></tr></table></td>
</tr>
ROWLINE;
        $assbg = $assval['readlabel']; 
        $bggroups++;
    }  

    if ( $thisGroupInd === 1 ) { 
      $prep = ( trim($assval['preparation']) !== "" ) ? " / {$assval['preparation']}" : "";
      $ifname = ( trim($assval['investfname']) !== "" ) ? ", {$assval['investfname']} " : "";
      $iname = (trim($assval['investlname']) !== "" ) ? "{$assval['investlname']}{$ifname}" : ""; 
      $reqNbr = ( trim($assval['assignedreq']) !== "" ) ? "/{$assval['assignedreq']}" : "";
      
      $metricdsp = ( trim($assval['metric']) !== "") ? "{$assval['metric']}" : "&nbsp;";
      $metricdsp .= ( trim($assval['mmdsp']) !== "" ) ? " {$assval['mmdsp']}" : "";
      
//<div title=\"Email investigator and team\" onclick=\"generateDialog('qmsInvestigatorEmailer','{$assval['bgs']}');\"><i class=\"material-icons actionbtnicon\">email</i></div>
      $reqency = ( trim($assval['assignedreq']) !== "" ) ? " onclick=\"generateDialog('irequestdisplay','" . cryptservice($assval['assignedreq']) . "');\" " : ""; 
      $reqPopEnd = ( trim($assval['assignedreq']) !== "" ) ? "<div {$reqency} title=\"View request {$assval['assignedreq']}\"><i class=\"material-icons actionbtnicon\">pageview</i></div>" : "";
      $assign = ( trim($assval['assignedto']) !== "" && ( trim($assval['assignedto']) !== "BANK" && trim($assval['assignedto']) !== "QC") ) ? "<div class=divLineHolder><div class=assignNamedsp>{$iname} ({$assval['assignedto']}{$reqNbr})</div><div class=alignerRight align=right>{$reqPopEnd}</div></div>" : "<div><div>-BANK-</div></div>";

      $sdencry = ( trim($assval['shipdocrefid']) !== "" ) ? cryptservice($assval['shipdocrefid']) : "";
      $thisemailer = ( $inconind === 1 ) ? "" : "<div title=\"Email investigator and team\" onclick=\"generateDialog('qmsInvestigatorEmailer','{$assval['bgs']}');\"><i class=\"material-icons actionbtnicon\">email</i></div>";
      $ship = ( trim($assval['shipdocrefid']) !== "" ) ? "<div class=divLineHolderSD>
                                                            <div class=divLineHolderSDRow>
                                                              <div>" . substr(('000000' . $assval['shipdocrefid']),-6) . "</div>
                                                              <div>Status: {$assval['sdstatus']}</div>
                                                              <div>Shipped: [{$assval['shippeddate']}]</div>
                                                              <div>Sales Order: {$assval['salesorder']}</div> 
                                                              <div onclick=\"navigateSite('shipment-document/{$sdencry}');\" title=\"View/Edit Shipment Document\"><i class=\"material-icons actionbtnicon\">pageview</i></div>
                                                              {$thisemailer}
                                                            </div>
                                                          </div>" : "";
    } else { 
      $prep = ( trim($assval['preparation']) !== "" ) ? " / {$assval['preparation']}" : "";
      
      $metricdsp = ( trim($assval['metric']) !== "") ? "{$assval['metric']}" : "&nbsp;";
      $metricdsp .= ( trim($assval['mmdsp']) !== "" ) ? " {$assval['mmdsp']}" : "";
      
      $ifname = ( trim($assval['investfname']) !== "" ) ? ", {$assval['investfname']} " : "";
      $iname = (trim($assval['investlname']) !== "" ) ? "{$assval['investlname']}{$ifname}" : ""; 
      $reqNbr = ( trim($assval['assignedreq']) !== "" ) ? "/{$assval['assignedreq']}" : "";
      $assign = ( trim($assval['assignedto']) !== "" && ( trim($assval['assignedto']) !== "BANK" && trim($assval['assignedto']) !== "QC") ) ? "<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td style=\"padding: 0;\" valign=top>{$iname} ({$assval['assignedto']}{$reqNbr})</td><td align=right valign=top> </td></tr></table>" : "<table cellpadding=0 cellspacing=0 border=0 width=100%><tr><td style=\"padding: 0;\" valign=top>-BANK-</td><td align=right> </td></tr></table>";
      $ship = ( trim($assval['shipdocrefid']) !== "" ) ? "<table border=0 width=100% cellpadding=0 cellspacing=0><tr><td valign=top>" . substr(('000000' . $assval['shipdocrefid']),-6) . "</td><td valign=top>Status: {$assval['sdstatus']}</td><td valign=top>Shipped: [{$assval['shippeddate']}]</td><td valign=top>Sales Order: {$assval['salesorder']} <td align=right> </td></tr></table>" : "";
    }

    $innerAss .= <<<INASSTBL
  <tr id="activetbl{$segrowcnt}" data-selected='false' data-bgs='{$assval['bgs']}' onclick="selectActiveSegmentRecord('activetbl{$segrowcnt}');">
    <td valign=top class="dspDataCellA">{$assval['bgs']}</td>
    <td valign=top class="dspDataCellA">{$assval['prepmethod']}{$prep}</td>
    <td valign=top class="dspDataCellA">{$metricdsp}</td>
    <td valign=top class="dspDataCellA">{$assval['segstatusdsp']}</td>
    <td valign=top class="dspDataCellA">{$assign}</td>
    <td valign=top class="dspDataCellA">{$ship}</td>
  </tr>
INASSTBL;
    $segrowcnt++; 
}
$innerAss .= "</tbody></table></div>";
$assTbl .= "<tr><td colspan=6 valign=top>{$innerAss}</td></tr>";
$assTbl .= "</table>";
return $assTbl;
}

function bldQAWorkbench_prcTst ( $hd, $pr, $mt ) { 
//$hd = Head data / $pr = percent data / $mt = Molecular test data
    
//UNINVOLVED MENU
$univData = dropmenuUninvolvedIndicatorA( $hd['hpruninvolvedvalue'] );
$uninvmenu = $univData['menuObj'];
//TUMOR GRADE SCALE MENU
$tmrGradeData = dropmenuTumorGrade( $hd['hprtumorscalevalue'] );
$tmrGradeMenu = $tmrGradeData['menuObj'];
//PERCENTAGE TABLE
foreach ( $pr as $pkey => $pvalue ) { 
   $percentTbl .= "<div class=dataHolderDiv><div class=datalabel>{$pvalue['ptypedsp']}</div><div class=datadisplayA><input type=text id={$pvalue['ptypeval']} class=prcDisplayValue value={$pvalue['prcvalue']}></div></div>";
}
//MOLECULAR TEST
$moleTstData = dropmenuMolecularTests(); 
$moleTstMenu = $moleTstData['menuObj'];
$resultIndexMenu = "<div class=menuHolderDiv><input type=hidden id='hprFldMoleResult{$idsuffix}Value'><input type=text id='hprFldMoleResult{$idsuffix}' READONLY><div class=valueDropDown id=moleResultDropDown style=\"min-width: 12.5vw;\"> </div></div>";
    if ( count($mt) > 0 ) {
      $moleTestTbl = "<table cellspacing=0 cellpadding=0 border=0 style=\"width: 8vw;\">";
      $cntr = 0;         
      foreach ( $mt as $molkey => $molval ) {
        $fld1 = $molval['moletestvalue'];
        $fld2 = addslashes($molval['moletest']);
        $fld3 = $molval['resultindexvalue'];
        $fld4 = addslashes($molval['resultindex']);  
        $fld5 = addslashes($molval['resultdegree']);  
        $moleArrA[] = array( $fld1, $fld2, $fld3, $fld4, $fld5) ;    
        $moleTestTbl .= "<tr onclick=\"manageMoleTest(0,{$cntr},'" . $fldsuffix . "');\" class=ddMenuItem><td style=\"border-bottom: 1px solid rgba(160,160,160,1); width: 1vw;\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 3px;\">cancel</i><td style=\" padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.3vh;\">" . $fld2 . "</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.3vh;\">" . $fld4 . "</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.3vh;\">" . $fld5 . "</td></tr>";
        $cntr++;
      }
    }
    $moleTestTbl .= "</table>";
    if ( !$moleArrA ) {
      $moleArrA = [];       
    }
    $moleArr = json_encode($moleArrA);  
//MOLETBL END


$rtnThis = <<<RTNTHIS
<div id=dataRowThree>Tumor Composition &amp; Molecular Tests</div>
<div id=tmrCompMoleHold>
   <div id=tmrCompSide>
     <div class=dataHolderDiv id=invHldMnu><div class=datalabel>Uninvolved Sample</div><div class="datadisplayA">{$uninvmenu}</div></div>
     <div class=dataHolderDiv id=tmrHldMnu><div class=datalabel>Tumor Grade</div><div class="datadisplayA"><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text id=fldTumorGrade value="{$hd['hprtumorgrade']}"></td><td>{$tmrGradeMenu}</td></tr></table></div></div>
     <div id=dataPercentHold>
     {$percentTbl}
     </div>
   </div>
   <div id=moleTstSide>
     <div id=inputLine> 
     <div class=dataHolderDiv style="border: none;"><div class=datalabel>Immuno/Molecular Test</div><div class="datadisplayA">{$moleTstMenu}</div></div>
     <div class=dataHolderDiv style="border: none;"><div class=datalabel>Result Index</div><div class="datadisplayA">{$resultIndexMenu}</div></div>
     <div class=dataHolderDiv style="border: none;"><div class=datalabel>Degree Scale</div><div class="datadisplayA"><input type=text id=hprFldMoleScale{$idsuffix}></div></div>
     </div>
     <div id=inputbuttondiv><center><table cellspacing=0 cellpadding=0 style="margin-top: 1vh; margin-bottom: 1vh;"><tr><td onclick="manageMoleTest(1,'','{$idsuffix}');" class=makeBtn style="font-size: 1.3vh;">Add Test Result</td></tr></table></div>
     <div ><input type=hidden id=hprMolecularTestJsonHolderConfirm value='{$moleArr}'><div id=dspDefinedMolecularTestsConfirm{$idsuffix} class=dspDefinedMoleTests>{$moleTestTbl}</div></div>
   </div>
</div>

<center><table style="margin-top: 2vh;"><tr><td class=makeBtn style="font-size: 1.6vh;" onclick="saveMolePrc();">Save</td></tr></table>
RTNTHIS;
return $rtnThis;
}

function bldQAWorkbench_bsData ( $headdta, $pristine ) { 
  $bsinst = ( trim($headdta['bsprocureinstitutiondsp']) !== "") ? " (" . trim($headdta['bsprocureinstitutiondsp']) . " - {$headdta['procurementdate']})" : "";     
  $asite = ( trim($headdta['bsanatomicsite']) !== "" ) ? trim($headdta['bsanatomicsite']) : "";
  $asite .= ( trim($headdta['bssubsite']) !== "" ) ? ( trim($asite) !== "" ) ? " :: {$headdta['bssubsite']}" : " -- :: {$headdta['bssubsite']}" : "";    
  $dx = ( trim($headdta['bsdx']) !== "" ) ? " // " . trim($headdta['bsdx']) : "";
  $dx .= ( trim($headdta['bsdxmod']) !== "" ) ? ( trim($dx) !== "" ) ? " :: {$headdta['bsdxmod']}" : " -- :: {$headdta['bsdxmod']}" : "";
  $mets = ( trim( $headdta['bsmets'] ) !== "" ) ? "<div class=dataHolderDiv id=hprDataMETS><div class=datalabel>Metastatic FROM</div><div class=datadisplay>{$headdta['bsmets']}&nbsp;</div></div>"  : "";
  $como = ( trim( $headdta['bscomo']) !== "" ) ? "<div class=dataHolderDiv id=hprDataCoMo><div class=datalabel>Systemic Diagnosis and/or Co-Mobidity</div><div class=datadisplay>{$headdta['bscomo']}&nbsp;</div></div>"  : "";

  $age = ( trim($headdta['bspxiage']) !== "" ) ? "{$headdta['bspxiage']}" : "" ;
    $age .= ( trim($age) !== "" ) ?    ( trim($headdta['bspxiageuom']) !== "") ? " {$headdta['bspxiageuom']}" : "" : "";
    $age .= ( trim($headdta['bspxirace']) !== "" ) ? ( trim($age) !== "") ? " / {$headdta['bspxirace']}"    :  "{$headdta['bspxirace']}" : "";
    $age .= ( trim($headdta['bspxisexdsp']) !== "" ) ? ( trim($age) !== "") ? " / {$headdta['bspxisexdsp']}"    :  "{$headdta['bspxisexdsp']}" : "";
  
  $cxrx = ( trim($headdta['bschemoinddsp']) !== "" ) ?   "{$headdta['bschemoinddsp']}"  : "";
    $cxrx .= ( trim($headdta['bsradinddsp']) !== "" ) ?   ( trim($cxrx) !== "" ) ? " - {$headdta['bsradinddsp']}" : " - {$headdta['bsradinddsp']}"  : " / -";
  
  //$bsc = ( trim($headdta['bscomments']) !== "" ) ? "<div class=dataHolderDiv id=bsDataComments><div class=datalabel><table width=100% cellpadding=0 cellspacing=0><tr><td valign=top>Biosample Comments</td><td valign=top align=right><table cellpadding=0 cellspacing=0 onclick=\"generateDialog('dlgCMTEDIT','BGC:{$headdta['pbiosample']}');\" title=\"Edit Biosample Comment\"><tr><td valign=top><i class=\"material-icons actionbtnicon\">edit</i></td></tr></table></td></tr></table></div><div class=\"datadisplay cmtdsp\">{$headdta['bscomments']}&nbsp;</div></div>" : "";

    $bsc = "<div class=dataHolderDiv id=bsDataComments><div class=datalabel><table width=100% cellpadding=0 cellspacing=0><tr><td valign=top>Biosample Comments</td><td valign=top align=right><table cellpadding=0 cellspacing=0 onclick=\"generateDialog('dlgCMTEDIT','BGC:{$headdta['pbiosample']}');\" title=\"Edit Biosample Comment\"><tr><td valign=top><i class=\"material-icons actionbtnicon\">edit</i></td></tr></table></td></tr></table></div><div class=\"datadisplay cmtdsp\">{$headdta['bscomments']}&nbsp;</div></div>";

  $hprq = ( trim($headdta['bshprqstn']) !== "" ) ? "<div class=dataHolderDiv id=bsDataQuestion><div class=datalabel><table width=100% cellpadding=0 cellspacing=0><tr><td valign=top>Question for Reviewer</td><td valign=top align=right> </td></tr></table></div><div class=\"datadisplay cmtdsp\">{$headdta['bshprqstn']}&nbsp;</div></div>" : "";
  //$hprq = ( trim($headdta['bshprqstn']) !== "" ) ? "<div class=dataHolderDiv id=bsDataQuestion><div class=datalabel><table width=100% cellpadding=0 cellspacing=0><tr><td valign=top>Question for Reviewer</td><td valign=top align=right><table cellpadding=0 cellspacing=0 onclick=\"generateDialog('dlgCMTEDIT','HPQ:{$headdta['pbiosample']}');\"><tr><td valign=top><i class=\"material-icons actionbtnicon\">edit</i></td></tr></table></td></tr></table></div><div class=\"datadisplay cmtdsp\">{$headdta['bshprqstn']}&nbsp;</div></div>" : "";


  $pris = ( trim($pristine[0]['speccat']) !== "" ) ? "{$pristine[0]['speccat']}" : "";
  $pss = ( trim($pristine[0]['primarysubsite']) !== "" ) ? " ({$pristine[0]['primarysubsite']})" : " - ";
  $pris .= ( trim($pristine[0]['primarysite']) !== "" ) ? " :: {$pristine[0]['primarysite']}{$pss}" : " :: - {$pss}";
  $pmdx = ( trim($pristine[0]['dxmod']) !== "" ) ? " ({$pristine[0]['dxmod']})" : " - ";
  $pris .= ( trim($pristine[0]['dx']) !== "" ) ? " // {$pristine[0]['dx']}{$pmdx}" : " // - {$pmdx}";
  $puni = ( trim($pristine[0]['uninvolved']) !== "" ) ? "{$pristine[0]['uninvolved']}" : "-";
  $pris .= " [{$puni}]";
  //$pris .= ( trim($pristine[0]['classification']) !== "" ) ? " [{$pristine[0]['classification']}{$puni}]" : "[-{$puni}]";
  $pris .= "<br><span class=smlFont>({$pristine[0]['refby']} :: {$pristine[0]['refon']})</span>";

  $biohprency = cryptservice($headdta['biohpr']);

$rtnThis = <<<RTNTHIS
<div id=dataRowTwo>Biosample Data for {$headdta['bsreadlabel']}</div>
<div class=dataHolderDiv id=bsDataProcurement><div class=datalabel><span class=smlFont>[Procured As]</span><br>Specimen Category :: Site (Sub-Site) // Diagnosis (Modifier) [Uninvolved]</div><div class=datadisplay>{$pris}&nbsp;</div></div>
<div class=dataHolderDiv id=bsDataSpecCat><div class=datalabel><span class=smlFont>[Present Designation]</span><br><table width=100% cellpadding=0 cellspacing=0><tr><td valign=top>Specimen Category :: Site :: Sub-Site // Diagnosis :: Modifier</td><td align=right valign=top><table cellspacing=0 cellpadding=0><tr><td valign=top title="Copy HPR Designation to this biogroup" onclick="copyHPRToBS('{$biohprency}');"><i class="material-icons actionbtnicon">file_copy</i></td><td valign=top onclick="generateDialog('dlgEDTDX','{$headdta['pbiosample']}');" title="Edit diagnosis designation for this biogroup"><i class="material-icons actionbtnicon">edit</i></td></tr></table></td></tr></table></div><div class="datadisplay cmtdsp">{$headdta['bsspeccat']} :: {$asite} {$dx} &nbsp;</div></div>
{$mets}
{$como}
<div class=dataHolderDiv id=bsDataARS><div class=datalabel><table width=100% cellspacing=0 cellpadding=0><tr><td valign=top>Age/Race/Sex/Chemo-Radiation</td><td align=right valign=top><table cellspacing=0 cellpadding=0 onclick="generateDialog('dlgEDTENC','{$headdta['bspxiid']}::{$headdta['pbiosample']}');" title="Edit Donor Encounter Records!"><tr><td valign=top><i class="material-icons actionbtnicon">edit</i></td></tr></table></td></tr></table></div><div class="datadisplay cmtdsp">{$age} / {$cxrx}&nbsp;</div></div>
{$bsc}
{$hprq}
<div class=dataHolderDiv id=bsDataProcurement><div class=datalabel>Procedure (Institution - Procurement Date)</div><div class=datadisplay>{$headdta['bsproctypedsp']}{$bsinst}&nbsp;</div></div>
RTNTHIS;
return $rtnThis;
}

function bldQAWorkbench_hprData ( $headdta ) { 

$encyhpr = cryptservice( $headdta['biohpr'], 'e');    
$encybg = cryptservice( $headdta['bsreadlabel'], 'e' );
$rbyon = $headdta['reviewer'];
$rbyon .= ( trim($headdta['reviewer']) !== trim($headdta['inputby']) ) ?  ( trim($headdta['inputby']) !== "" ) ? " ({$headdta['inputby']})" : "" : "";
$rbyon .= ( trim($headdta['reviewedon']) !== "" ) ? " :: {$headdta['reviewedon']}" : "";
$asite = ( trim($headdta['hprsite']) !== "" ) ? trim($headdta['hprsite']) : "";
$asite .= ( trim($headdta['hprsubsite']) !== "" ) ? ( trim($asite) !== "" ) ? " :: {$headdta['hprsubsite']}" : " -- :: {$headdta['hprsubsite']}" : "";
$dx = ( trim($headdta['hprdx']) !== "" ) ? " // " . trim($headdta['hprdx']) : "";
$dx .= ( trim($headdta['hprdxmod']) !== "" ) ? ( trim($dx) !== "" ) ? " :: {$headdta['hprdxmod']}" : " -- :: {$headdta['hprdxmod']}" : "";
$mets = ( trim( $headdta['hprmets'] ) !== "" ) ? "<div class=dataHolderDiv id=hprDataMETS><div class=datalabel>Metastatic FROM</div><div class=datadisplay>{$headdta['hprmets']}&nbsp;</div></div>"  : "";
$como = ( trim( $headdta['hprcomobid']) !== "" ) ? "<div class=dataHolderDiv id=hprDataCoMo><div class=datalabel>Systemic Diagnosis and/or Co-Mobidity</div><div class=datadisplay>{$headdta['hprcomobid']}&nbsp;</div></div>"  : "";
$cmtRR = ( trim($headdta['hprrarereason']) !== "" ) ? "<div class=dataHolderDiv id=hprDataRareReason><div class=datalabel>Comments : Rare Reason</div><div class=\"datadisplay  cmtdsp\">{$headdta['hprrarereason']}&nbsp;</div></div>"  : "";
$cmtGC = ( trim($headdta['hprgeneralcomments']) !== "" ) ? "<div class=dataHolderDiv id=hprDataGeneralComments><div class=datalabel>Comments : HPR Comments</div><div class=\"datadisplay  cmtdsp\">{$headdta['hprgeneralcomments']}&nbsp;</div></div>"  : "";
$cmtSI = ( trim($headdta['hprspecialinstructions']) !== "" ) ? "<div class=dataHolderDiv id=hprDataSpecialInstructions><div class=datalabel>Comments : Special Instructions</div><div class=\"datadisplay  cmtdsp\">{$headdta['hprspecialinstructions']}&nbsp;</div></div>"  : "";
$cmtINC = ( trim($headdta['hprinconclusivetext']) !== "" ) ? "<div class=dataHolderDiv id=hprDataInconclusiveText><div class=datalabel>Comments : Inconclusive Text</div><div class=\"datadisplay  cmtdsp\">{$headdta['hprinconclusivetext']}&nbsp;</div></div>"  : "";
$cmtUU =  ( trim($headdta['hprunusabletext']) !== "" ) ? "<div class=dataHolderDiv id=hprDataInconclusiveText><div class=datalabel>Comments : Unusable Text</div><div class=\"datadisplay  cmtdsp\">{$headdta['hprunusabletext']}&nbsp;</div></div>"  : "";

$reviewreassig = ( (int)$headdta['reviewassignind'] === 1 ) ? "<div class=dataHolderDiv><div class=\"attentionGetter\" >REVIEW ASSIGNMENT</div></div>" : "";

$rtnThis = <<<RTNTHIS
<div id=dataRowOne data-hprdecision="{$headdta['hprdecisionvalue']}"  data-encyreviewid="{$encyhpr}" data-encybg="{$encybg}">HPR Review</div>
<div class=dataHolderDiv id=hprDataDecision><div class=datalabel>Decision</div><div class=datadisplay>{$headdta['hprdecision']} (Review #: {$headdta['biohpr']})&nbsp;</div></div>
<div class=dataHolderDiv id=hprDataDecision><div class=datalabel>Slide Read</div><div class=datadisplay>{$headdta['slidebgs']} &nbsp;</div></div>
{$reviewreassig}
<div class=dataHolderDiv id=hprDataSpecCat><div class=datalabel><span class=smlFont>[HPR Decision Designation]</span><br>Specimen Category :: Site :: Sub-Site // Diagnosis :: Modifier</div><div class="datadisplay cmtdsp">{$headdta['hprspeccat']} :: {$asite} {$dx} &nbsp;</div></div>
{$mets}
{$como}
{$cmtGC}
{$cmtSI}
{$cmtUU}
{$cmtRR}
{$cmtINC}
<div class=dataHolderDiv id=hprDataReviewedOnBy><div class=datalabel>Reviewed By :: On</div><div class=datadisplay>{$rbyon}&nbsp;</div></div>
RTNTHIS;
return $rtnThis;
}

function bldFurtherActionItem ( $itmency ) { 

    $ticketNbr = cryptservice( $itmency , 'd');
 
    //TODO:  TURN INTO A WEBSERVICE
    require(serverkeys . "/sspdo.zck");    
    $sql = "SELECT substr(concat('000000',idlabactions),-6) as ticketnbr , actionstartedind , ifnull(date_format(startedondate,'%m/%d/%Y'),'') as startedondate, ifnull(startedby,'') as startedby , ifnull(frommodule,'Unknown Module') frommodule, ifnull(objshipdoc,'') as objshipdoc, ifnull(objhprid,'') as objhprid, ifnull(objpbiosample,'') as objpbiosample, ifnull(objbgs,'') as objbgs, ifnull(assignedagent,'') as assignedagent, ifnull(actioncode,'UNKNOWN') as actioncode, ifnull(actiondesc,'') as actiondesc, ifnull(actionnote,'') as actionnote, ifnull(notifyoncomplete,0) as notifyoncomplete, ifnull(date_format(duedate,'%Y-%m-%d'),'') as duedateval, ifnull(date_format(duedate,'%m/%d/%Y'),'') as duedate, ifnull(actionrequestedby,'UNKNOWN') as actionrequestedby, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as actionrequestedon , ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'') as actioncompleteon, ifnull(actioncompletedby,'') as actioncompletedby, faaction.assignablestatus as actiongridtype, ifnull(faprio.dspvalue,'') priorityvalue, ifnull(agentlist.dspagent,'') as lastagentdsp FROM masterrecord.ut_master_furtherlabactions fa LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAACTIONLIST') faaction on fa.actioncode = faaction.menuvalue LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') faprio on fa.prioritymarkcode = faprio.menuvalue left join (SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP') as agentlist on fa.lastagent = agentlist.originalaccountname where idlabactions = :ticketnumber and activeind = :activeind";
    $rs = $conn->prepare($sql); 
    $rs->execute(array(':ticketnumber' => (int)$ticketNbr, ':activeind' => 1)); 
    if ( $rs->rowCount() <> 1 ) { 
      //NO TICKET FOUND
    $pg = <<<BLDPG
<h2>Ticket Number {$ticketNbr} was not found.  See CHTN Eastern Informatics.</h2>
BLDPG;
    } else { 


    $ticket = $rs->fetch(PDO::FETCH_ASSOC);
    $pbioref = ( trim($ticket['objpbiosample']) !== "" ) ?  (int)$ticket['objpbiosample'] : "";
    $pbioref .= ( trim($ticket['objbgs']) !== "" ) ?  ( trim($pbioref) === "" ) ? $ticket['objbgs'] : " / {$ticket['objbgs']}"  : "-";
    $sd = ( trim($ticket['objshipdoc']) !== "" ) ? substr(('000000'.$ticket['objshipdoc'] ),-6) : "";
    $ddate = ( trim($ticket['duedate']) !== "" && $ticket['duedate'] !== '01/01/1900' ) ? $ticket['duedate'] : "";
    $ddateval = ( trim($ticket['duedateval']) !== "" && $ticket['duedateval'] !== '1900-01-01' ) ? $ticket['duedateval'] : "";
    $notify = ( (int)trim($ticket['notifyoncomplete']) === 1 ) ? "Yes" : "No";
    $workstart = ( trim($ticket['startedondate']) !== "" ) ? trim($ticket['startedondate']) : "";
    $workstart .= ( trim($ticket['startedby']) !== "" ) ? ( $workstart === "" ) ? trim( $ticket['startedby'] ) : " :: {$ticket['startedby']}" : "-";  
    $complete = ( trim($ticket['actioncompleteon']) !== "" ) ? trim($ticket['actioncompleteon']) : "";
    $complete .= ( trim( $ticket['actioncompletedby']) !== "" ) ? ( trim($complete) === "" ) ? $ticket['actioncompletedby'] : " :: {$ticket['actioncompletedby']}" : "-";


    $agentListSQL = "SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP' order by friendlyname";
    $agentListRS = $conn->prepare($agentListSQL); 
    $agentListRS->execute();
    $agnt = "<table border=0 class=menuDropTbl>";
    $agnt .= "<tr><td onclick=\"fillField('faFldAssAgent','','');\" class=ddMenuItem align=right style=\"font-size: 1.1vh;\">[clear]</td></tr>";
    $thisagent = "";
    $thisagentcode = "";
    while ( $al = $agentListRS->fetch(PDO::FETCH_ASSOC)) { 
        if ( trim($ticket['assignedagent']) === trim($al['originalaccountname']) ) { 
          $thisagent = $al['dspagent'];
          $thisagentcode = $al['originalaccountname'];
        }
      $agnt .= "<tr><td onclick=\"fillField('faFldAssAgent','{$al['originalaccountname']}','{$al['dspagent']}');\" class=ddMenuItem>{$al['dspagent']}</td></tr>";
    }
    $agnt .= "</table>";
    $agntmnu = "<div class=menuHolderDiv><input type=hidden id=faFldAssAgentValue value=\"{$thisagentcode}\"><input type=text id=faFldAssAgent READONLY class=\"inputFld\" value=\"{$thisagent}\"><div class=valueDropDown id=ddHTRz>{$agnt}</div></div>";

    $fCalendar = buildcalendar('biosampleQueryFrom'); 
    $bsqFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=bsqueryFromDateValue value="{$ddateval}"><input type=text READONLY id=bsqueryFromDate class="inputFld" value="{$ddate}"></div>
  <div class=valueDropDown style="width: 18vw;"><div id=bsqCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;


    //TODO:   TURN INTO A WEBSERVICE
    $faListSQL = "SELECT actionlist.menuvalue detailactioncode, actionlist.dspvalue detailaction, ifnull(actionlist.additionalInformation,0) as completeactionind, doneaction.whoby, ifnull(date_format(doneaction.whenon,'%m/%d/%Y'),'') as whenon, doneaction.comments, ifnull(doneaction.finishedstepind,0) finishedstep, ifnull(doneaction.finishedby,'') as finishedby, ifnull(date_format(doneaction.finishedon,'%m/%d/%Y %H:%i'),'') as finisheddate  FROM four.sys_master_menus actionlist left join (SELECT fadetailactioncode, whoby, whenon, comments, finishedstepind, finishedby, finishedon FROM masterrecord.ut_master_faction_detail where faticket = :ticketnbr ) doneaction on actionlist.menuvalue = doneaction.fadetailactioncode  where actionlist.parentid = :actioncodeid and actionlist.menu = 'FADETAILACTION' and actionlist.dspind = 1 order by actionlist.dsporder";
    $faListRS = $conn->prepare($faListSQL);
    $faListRS->execute(array(':ticketnbr' => $ticketNbr, ':actioncodeid' => $ticket['actiongridtype']));
   
    $action = "{$ticket['actiongridtype']}    <table border=0 cellspacing=0 cellpadding=0><thead><tr><td class=col5></td><td class=col6>#</td><td class=col1>Action</td><td class=col3>Performed By :: When</td><td class=col4>Comments</td></tr></thead><tbody>";
    $faActionDsp = "";
    $faActionStepCount = 0;
    while ( $r = $faListRS->fetch(PDO::FETCH_ASSOC)) { 
      $onwhen = ( trim($r['whenon']) !== "" ) ? " :: {$r['whenon']}" : "";
      $finishedind = (int)$r['finishedstep'];
      $pArr = array();
      $pArr['ticket'] = (int)$ticketNbr;
      $pArr['actioncode'] = $r['detailactioncode'];
      $pArrJson = cryptservice(json_encode($pArr));
       
      $fad = "";
      $stepCountDsp = "&nbsp;";
      $comcheckdsp = "";
      if ( $faActionDsp !== $r['detailaction'] ) { 
          $fad = $r['detailaction'];
          $faActionDsp = $r['detailaction']; 
          
          if ( trim($ticket['actioncompleteon']) === "" ) { 
            $actionPop = ( $finishedind === 0 ) ? " generateDialog('furtheractionperformer','{$pArrJson}'); " : " alert('The Action ({$faActionDsp}) has already been completed.'); ";  
          } else { 
            $actionPop = " alert('This ticket ({$ticket['ticketnbr']}) has already been completed and closed.'); ";    
          }
          
          $comcheckdsp = ( $finishedind === 1) ? "<i class=\"material-icons\">check_circle_outline</i>" : "";
          $faActionStepCount++;
          $stepCountDsp = "{$faActionStepCount}.";          
      } else { 
          $fad = "&nbsp;";
          $actionPop = "";
      }
      
      $action .= "<tr onclick=\"{$actionPop}\"><td>{$comcheckdsp}</td><td>{$stepCountDsp}</td><td>{$fad}</td><td>{$r['whoby']} {$onwhen}</td><td>{$r['comments']}</td></tr>";
    }
    $action .= "</tbody></table>";

    $hprdsp = ( (int)$ticket['objhprid'] === 0 ) ? "" : $ticket['objhprid'];
    $refer = ( trim($_SERVER['HTTP_REFERER']) !== "" ) ? $_SERVER['HTTP_REFERER'] : treeTop . "/further-action-requests";
    $lastAgnt =  ( trim($ticket['lastagentdsp']) !== "" ) ? " (Last Assigned: {$ticket['lastagentdsp']})" : "";
    
    $pg = <<<BLDPG
<div id=ticketHolder>
<div id=ticketHeadAnnounce>Further Action/Lab Action Request</div>
<div class=tDataDsp><div class=tLabel>Ticket # </div><div class=tData>{$ticket['ticketnbr']}<input type=hidden id=faFldTicketEncy value="{$itmency}"></div></div> 
<div class=tDataDsp><div class=tLabel>Date Requested </div><div class=tData>{$ticket['actionrequestedon']}</div></div> 
<div class=tDataDsp><div class=tLabel>Request By</div><div class=tData>{$ticket['actionrequestedby']}</div></div> 
<div class=tDataDsp><div class=tLabel>Notify </div><div class=tData>{$notify}</div></div> 
<div class=tDataDspWide><div class=tLabel>Due Date </div><div class=tDataFld>{$bsqFromCalendar}</div></div> 
<div class=tDataDspWide><div class=tLabel>Priority</div><div class=tData>{$ticket['priorityvalue']}</div></div> 
<div class=tDataDspWide><div class=tLabel>Biogroup Ref. </div><div class=tData>{$pbioref}</div></div> 
<div class=tDataDspWide><div class=tLabel>Ship-Doc</div><div class=tData>{$sd}</div></div> 
<div class=tDataDspWide><div class=tLabel>HPR Review # </div><div class=tData>{$hprdsp}</div></div> 
<div class=tDataDspSuperWide><div class=tLabel>Agent</div><div class=tDataFld>{$agntmnu}{$lastAgnt}</div></div> 
<div class=tDataDspSuperWide><div class=tLabel>Work Started</div><div class=tData>{$workstart}</div></div> 
<div class=tDataDsp><div class=tLabel>Completed</div><div class=tData>{$complete}&nbsp;</div></div> 

<div id=wholeLineTwo><div class=tLabel>Requested Action </div><div class=tDataAct>{$ticket['actiondesc']}</div></div> 
<div id=wholeLineThree><TEXTAREA id=faFldActionNote>{$ticket['actionnote']}</TEXTAREA></div> 

<div id=btnHolder><center> <div id=innerbtnholder> <div class=actionBtns onclick="saveFATicketEdit();">Save</div> <div class=actionBtns onclick="openPageInTab('{$refer}');">Cancel</div>  </div></div>


<div id=divDivOne>Actions Taken</div> 

<div id=actionGrid>
  Click row to perform/complete the action.
  {$action} 
</div> 

</div>
BLDPG;
    }
return $pg;
}

function bldFurtherActionQueue ( $whichuser, $thisperson ) { 

        //TODO: Turn into a webservice
    require(serverkeys . "/sspdo.zck");   
if ( trim($thisperson) === "" ) {  
        $queSQL = "SELECT substr(concat('000000',idlabactions),-6) as faid
, if ( actionstartedind = 1, 'Yes','No') as actionstartedind
, if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind
, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'')  as actioncompletedon
, ifnull(actioncompletedby,'') as actioncompletedby 
, ifnull(frommodule,'') as requestingModule 
, if(ifnull(objbgs,'') = '', ifnull(objpbiosample,'') ,ifnull(objbgs,'')) as biosampleref
, substr(concat('000000',ifnull(objshipdoc,'')),-6) as shipdocref
, ifnull(assignedagent,'') as assignedagent  
, if(ifnull(assignedagent,'')='','NO AGENT',ifnull(assignedagent,'')) as dspassignedagent 
, ifnull(faact.dspvalue,'') as actiondescription
, ifnull(actionnote,'') as actionnote 
, ifnull(fapri.dspvalue,'') as dspPriority 
, if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate 
, ifnull(actionrequestedby,'') as requestedby 
, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon 
FROM masterrecord.ut_master_furtherlabactions fa 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue 
where 1 = 1 and activeind = 1 and ( if(ifnull(actioncompletedon,'') = '', 'No', 'Yes') = 'No' ) 
union 
SELECT  
substr(concat('000000',idlabactions),-6) as faid
, if ( actionstartedind = 1, 'Yes','No') as actionstartedind
, if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind
, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'')  as actioncompletedon
, ifnull(actioncompletedby,'') as actioncompletedby 
, ifnull(frommodule,'') as requestingModule 
, if(ifnull(objbgs,'') = '', ifnull(objpbiosample,'') ,ifnull(objbgs,'')) as biosampleref
, substr(concat('000000',ifnull(objshipdoc,'')),-6) as shipdocref  
, ifnull(assignedagent,'') as assignedagent 
, if(ifnull(assignedagent,'')='','NO AGENT',ifnull(assignedagent,'')) as dspassignedagent
, ifnull(faact.dspvalue,'') as actiondescription
, ifnull(actionnote,'') as actionnote 
, ifnull(fapri.dspvalue,'') as dspPriority 
, if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate 
, ifnull(actionrequestedby,'') as requestedby 
, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon 
FROM masterrecord.ut_master_furtherlabactions fa 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue 
where 1 = 1 and activeind = 1 and ( datediff(now(),actioncompletedon) < 15 ) 
order by 3, 9, 14, 11";
        $queRS = $conn->prepare($queSQL); 
        $queRS->execute();
        $ticketcount = $queRS->rowCount();
} else { 
        $queSQL = "SELECT substr(concat('000000',idlabactions),-6) as faid
, if ( actionstartedind = 1, 'Yes','No') as actionstartedind
, if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind
, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'')  as actioncompletedon
, ifnull(actioncompletedby,'') as actioncompletedby 
, ifnull(frommodule,'') as requestingModule 
, if(ifnull(objbgs,'') = '', ifnull(objpbiosample,'') ,ifnull(objbgs,'')) as biosampleref
, substr(concat('000000',ifnull(objshipdoc,'')),-6) as shipdocref
, ifnull(assignedagent,'') as assignedagent  
, if(ifnull(assignedagent,'')='','NO AGENT',ifnull(assignedagent,'')) as dspassignedagent 
, ifnull(faact.dspvalue,'') as actiondescription
, ifnull(actionnote,'') as actionnote 
, ifnull(fapri.dspvalue,'') as dspPriority 
, if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate 
, ifnull(actionrequestedby,'') as requestedby 
, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon 
FROM masterrecord.ut_master_furtherlabactions fa 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue 
where 1 = 1 and activeind = 1 and ( if(ifnull(actioncompletedon,'') = '', 'No', 'Yes') = 'No' )
and ( ifnull(assignedagent,'') = '' OR ifnull(assignedagent,'') = :agentuser ) 
union 
SELECT  
substr(concat('000000',idlabactions),-6) as faid
, if ( actionstartedind = 1, 'Yes','No') as actionstartedind
, if (ifnull(actioncompletedon,'') = '', 'No', 'Yes') completedind
, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'')  as actioncompletedon
, ifnull(actioncompletedby,'') as actioncompletedby 
, ifnull(frommodule,'') as requestingModule 
, if(ifnull(objbgs,'') = '', ifnull(objpbiosample,'') ,ifnull(objbgs,'')) as biosampleref
, substr(concat('000000',ifnull(objshipdoc,'')),-6) as shipdocref  
, ifnull(assignedagent,'') as assignedagent 
, if(ifnull(assignedagent,'')='','NO AGENT',ifnull(assignedagent,'')) as dspassignedagent
, ifnull(faact.dspvalue,'') as actiondescription
, ifnull(actionnote,'') as actionnote 
, ifnull(fapri.dspvalue,'') as dspPriority 
, if( ifnull(date_format(duedate,'%m/%d/%Y'),'') = '01/01/1900','',ifnull(date_format(duedate,'%m/%d/%Y'),'')) as duedate 
, ifnull(actionrequestedby,'') as requestedby 
, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as requestedon 
FROM masterrecord.ut_master_furtherlabactions fa 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') fapri on fa.prioritymarkcode = fapri.menuvalue 
left join ( SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'FAACTIONLIST' ) as faact on fa.actioncode = faact.menuvalue 
where 1 = 1 and activeind = 1 and ( datediff(now(),actioncompletedon) < 15 )
and ( ifnull(assignedagent,'') = '' OR ifnull(assignedagent,'') = :agentusera )
order by 3, 9, 14, 11";
        $queRS = $conn->prepare($queSQL); 
        $queRS->execute(array(':agentuser' => $thisperson[0], ':agentusera' => $thisperson[0]));
        $ticketcount = $queRS->rowCount();
}

        $innerTbl = "<table id=faTable border=0><thead><tr><td> </td><td>Ticket #</td><td>Started</td><td>Priority</td><td>Due Date</td><td>Action To Perform</td><td>Bio/Ship-Doc/Ref</td><td>Agent</td><td>Requested On/By</td><td>Completed On/By</td></tr></thead><tbody>";
        $agent = "";
        $completelineind = 0;
        $ticketOpen = 0; 
        $ticketInProgress = 0;
        $ticketClosed = 0; 
        while ( $rs = $queRS->fetch(PDO::FETCH_ASSOC)) { 
          

          if ( $rs['completedind'] === 'Yes' && $completelineind === 0 ) { 
            $innerTbl .= "<tr><td colspan=10 class=completeline> <table border=0><tr><td>COMPLETED TICKETS IN LAST 15 DAYS</td></tr></table></td></tr>";
            $completelineind = 1;
          }
          if ( trim($rs['dspassignedagent']) !== $agent ) {
            $innerTbl .= "<tr><td colspan=10 class=agentdisplay> <table border=0><tr><td>{$rs['dspassignedagent']}</td></tr></table></td></tr>";
            $agent = $rs['dspassignedagent'];
          }
          $ticketency = cryptservice( $rs['faid'] );   
          $dueDte = ( trim($rs['duedate']) !== "" ) ? "{$rs['duedate']}" : "";
          $actDsp = $rs['actiondescription'];
          $actDsp .= ( trim($rs['actionnote']) !== "" ) ? "<br>[<b>Detail Note</b>: {$rs['actionnote']}]" : "";
          $bioRef = $rs['biosampleref'];
          $bioRef .= ( trim($rs['shipdocref']) !== "000000" ) ?  ( trim($bioRef) !== "" ) ? "/SD: {$rs['shipdocref']}" : "" : ""; 
          $comDsp = ( trim($rs['actioncompletedon']) !== "" ) ? "{$rs['actioncompletedon']} ({$rs['actioncompletedby']})" : "";

          $icon = ""; 
          $icon = ( trim($rs['actionstartedind']) === "No" ) ? "<center><i class=\"material-icons nowork\">warning</i>" : $icon; 
          $icon = ( trim($rs['actionstartedind']) === "Yes" ) ? "<center><i class=\"material-icons somework\">work</i>" : $icon; 
          $icon = ( trim($rs['completedind']) === "Yes" ) ? "<center><i class=\"material-icons donework\">done_all</i>" : $icon;
          
          if ( trim($rs['completedind']) === "Yes" ) { 
              $ticketClosed++; 
          } else { 
              if ( trim($rs['actionstartedind']) === "No" ) { 
                  $ticketOpen++;
              } else { 
                  $ticketInProgress++;
              }
          }

          $innerTbl .= "<tr class=faRow onclick=\"navigateSite('further-action-requests/{$ticketency}');\"><td class=faCell>{$icon}</td><td class=faCell>{$rs['faid']}</td><td class=faCell>{$rs['actionstartedind']}</td><td class=faCell>{$rs['dspPriority']}</td><td>{$dueDte}</td><td>{$actDsp}</td><td>{$bioRef}</td><td>{$rs['dspassignedagent']}</td><td>{$rs['requestedon']} ({$rs['requestedby']})</td><td>{$comDsp}</td></tr>";
        } 
        $innerTbl .= "</tbody></table>";

 $dspperson = ( trim($thisperson[0]) === "" ) ? "Queue Listing for All Agents" : "Queue Listing for {$thisperson[0]}"; 

$pg = <<<BLDTBL
    <div id=headTitle>Further Actions Ticket Listing </div>
    <div id=displayHolder>
    <div id=furtherActionsQueHolder>
      {$innerTbl}
    </div>  
    <div id=headInstructions> 
            <table id=legendTbl>
              <tr><td colspan=3 id=legendHead>Legend</td></tr>
              <tr><td colspan=3><center>{$dspperson}</td></tr>
              <tr><td><i class="material-icons nowork">warning</i></td><td>Open Ticket</td><td align=right>{$ticketOpen}&nbsp;</td></tr>
              <tr><td><i class="material-icons somework">work</i></td><td>In Progress Ticket</td><td align=right>{$ticketInProgress}&nbsp;</td></tr>
              <tr><td><i class="material-icons donework">done_all</i></td><td>Completed Ticket</td><td align=right>{$ticketClosed}&nbsp;</td></tr>
              <tr><td></td><td>Total</td><td align=right>{$ticketcount}&nbsp;</td></tr>
            </table>
    </div>
    </div>
&nbsp;<p>
BLDTBL;

    
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
    $graphics = base64file("{$at}/publicobj/graphics/sysgraphics/{$whichgraphic}", "{$id}", "png", true , " style=\"width: 100%; height: auto;\" ");         
  } else { 
    $graphics = "NO GRAPHIC FOUND ({$whichgraphic})";
  }

  list($iwidth, $iheight, $itype, $iattr) = getimagesize("{$at}/publicobj/graphics/sysgraphics/{$whichgraphic}");
  $orientation = ( $iwidth != $iheight ? ( $iwidth > $iheight ? 'landscape' : 'portrait' ) : 'square' );

  $pgGraphics = <<<ENLRGDGRPH
<div id=dashboardenlargeimagehold_{$orientation}>
  {$graphics}

</div>

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

function bldASTLookup() {

          $poarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ast-request-statuses",serverIdent,serverpw),true);
          $dfvl = "";
          $po = "<table border=0 class=menuDropTbl>";
          foreach ($poarr['DATA'] as $poval) {
              if ( (int)$poval['useasdefault'] === 1 ) { 
                $dfvl = $poval['lookupvalue'];
              }    
              $po .= "<tr><td onclick=\"fillField('astRequestStatus','{$poval['lookupvalue']}','{$poval['menuvalue']}');\" class=ddMenuItem>{$poval['menuvalue']}</td></tr>";
          }
          $po .= "</table>";

          $spcarr = json_decode(callrestapi("GET", dataTree . "/global-menu/ast-req-specimen-categories",serverIdent,serverpw),true);
          $spcdfvl = "";
          $spc = "<table border=0 class=menuDropTbl>";
          foreach ($spcarr['DATA'] as $spcval) {
              if ( (int)$spcval['useasdefault'] === 1 ) { 
                $spcdfvl = $spcval['lookupvalue'];
              }    
              $spc .= "<tr><td onclick=\"fillField('astRequestSPC','{$spcval['lookupvalue']}','{$spcval['menuvalue']}');\" class=ddMenuItem>{$spcval['menuvalue']}</td></tr>";
          }
          $spc .= "</table>";

          //astreqpreps
          $prparr = json_decode(callrestapi("GET", dataTree . "/global-menu/ast-req-preps",serverIdent,serverpw),true);
          $prpdfvl = "";
          $prp = "<table border=0 class=menuDropTbl><tr><td onclick=\"fillField('astRequestPrep','','');\" class=ddMenuItem align=right style=\"font-size: 1vh;\">[Clear]</td></tr>";
          foreach ($prparr['DATA'] as $prpval) {
              if ( (int)$prpval['useasdefault'] === 1 ) { 
                $prpdfvl = $prpval['lookupvalue'];
              }    
              $prp .= "<tr><td onclick=\"fillField('astRequestPrep','{$prpval['lookupvalue']}','{$prpval['menuvalue']}');\" class=ddMenuItem>{$prpval['menuvalue']}</td></tr>";
          }
          $prp .= "</table>";

          //<td><div class=menuHolderDiv><input type=text id=astRequestPrep value="{$prpdfvl}" READONLY><div class=valueDropDown style="width: 10vw;">{$prp}</div></div></td>
          //<td>Detail Preparation<sup>*</sup></td>

          $thisPage = <<<THISPAGE
            <div id=ASTHeadline>A.S.T. Search List</div>
            <div id=ASTInstruct>This CHTN Eastern at one time used a printed report called the AST (Autopsy/Surgery/Transplant) Request List to search for requests.  These requests come from the CHTN Central database.  This database is called TissueQuest and is hosted at Vanderbilt University.  The printed report was several hundreds of pages long and CHTN Eastern would print several copies at the beginning of each week.  Now with computerization, this AST List is interactive and searchable - but we've kept the name "AST".  <p>To Search the AST enter the specified search parameters in the fields below.  You can enter investigator information as you do on all other ScienceServer Screens - either with a name, institution or INV#.  If you use the 'Detail Preparation' parameter, the AST will only display preparations within the request that match (be aware that this field many return results unexpectedly).  Entering a term in the 'Search Term' field will search ALL Diagnosis Designation fields within the request (including comment fields) for that term.<p>This search is conducted against a grouping of data that is immense.  Searches can run up to 30 seconds to execute! </div>      

            <div id=paraHolder>
            <table border=0>
            <tr><td>Request Status</td><td>Search Term </td><td>TQ-Specimen Category</td><td>Investigator ID</td></tr>
            <tr>
                <td><div class=menuHolderDiv><input type=text id=astRequestStatus value="{$dfvl}" READONLY><div class=valueDropDown style="width: 10vw;">{$po}</div></div></td>
                <td><input type=text id=astSearchTerm value=""></td>
                <td><div class=menuHolderDiv><input type=text id=astRequestSPC value="{$spcdfvl}" READONLY><div class=valueDropDown style="width: 10vw;">{$spc}</div></div></td>
                <td><div class=suggestionHolder><input type=text id=qryInvestigator class="inputFld"><div id=investSuggestion class=suggestionDisplay>&nbsp;</div></div></td>
            </tr>
            <tr>
                <td colspan=10 align=right>                       
                  <table class=tblBtn id=btnLookup style="width: 6vw;"><tr><td style="font-size: 1.3vh;"><center>Lookup</td></tr></table>
                </td>
            </tr>
            </table> 
            </div>
THISPAGE;
    return $thisPage;

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
   $sdcsrvysent = $sd['DATA']['sdhead'][0]['surveyemailsent'];
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
<table><tr><td class=sdFieldLabel>Billing Address *</td></tr><tr><td><TEXTAREA class=sdinput id=sdcInvestBillingAddress data-frminclude=1>{$sdbladd}</TEXTAREA></td></tr><tr><td class=sdFieldLabel>Billing Phone * (format: '(123) 456-7890 x0000' / x is optional)</td></tr><tr><td><input type=text class=sdinput id=sdcBillPhone value="{$sdblphn}" data-frminclude=1></td></tr><tr><td class=sdFieldLabel>Send Survey When Shipment Complete</td></tr><tr><td><div class="checkboxThree">
   <input type="checkbox" class="checkboxThreeInput" id="sdcSendSurvey" {$sdcsrvysent}  data-frminclude=1 /><label for="sdcSendSurvey"></label></div></td></tr></table>
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

  $delbtn = "";
  if ( (int)$dval['segmentid'] === 0 ) {
    //SPECIAL SERVICES DISPLAY GOES HERE
    switch ( $sdstatus ) {
      case 'NEW':
        //ALLOW DELBTN
        $delbtn = "<i class=\"material-icons action-icon\" onclick=\"removeSpcSrv('{$dval['shipdocDetId']}','{$cellident}');\">remove_circle_outline</i>";
        break;
      case 'OPEN':
        //ALLOW DELBTN
        $delbtn = "<i class=\"material-icons action-icon\" onclick=\"removeSpcSrv('{$dval['shipdocDetId']}','{$cellident}');\">remove_circle_outline</i>";
        break;
      case 'LOCKED':
        //ALLOW ONLY IF NOT PULLED       
        $delbtn = "<i class=\"material-icons action-icon\" onclick=\"removeSpcSrv('{$dval['shipdocDetId']}','{$cellident}');\">remove_circle_outline</i>";
        break;
      case 'CLOSED':
        //DON'T ALLOW
        $delbtn = "<i class=\"material-icons\">vpn_lock</i>";
        break;    
    }
  } else {
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
<tr><td colspan=2><input type=checkbox id=fldSEGAddToHisto><label for=fldSEGParentExhaustInd>Add To Today's Histology Sheet (For Slides, Parent MUST be on Histology Sheet)</label></td></tr>
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
    $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and sessionid = :sessid and (allowInd = 1 and allowCoord = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
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

     $chkUsrSQL = "SELECT originalaccountname FROM four.sys_userbase where 1=1 and sessionid = :sessid and (allowInd = 1 and allowCoord = 1) and TIMESTAMPDIFF(MINUTE,now(),sessionexpire) > 0 and TIMESTAMPDIFF(DAY, now(), passwordexpiredate) > 0"; 
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
      $rtnThis = "<table><tr><td><h3>ERROR:  SEE CHTNEastern Informatics personnel </h3></td></tr></table>";
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
//<td style="padding: 0 0 0 .5vw;"><div class=menuHolderDiv><input type=text id=fldDialogPRUPDeviationReason style="font-size: 1.3vh; width: 13vw;"><div class=valueDropDown>{$devm}</div></div></td>
//<td style="width: 12vw;" valign=bottom> <div class="ttholder headhead">SOP DEVIATION (?)<div class=tt style="width: 25vw;">This is NOT a standard operational screen and should only be used in extenuating circumstances.  The use of this screen will be tracked as a deviation from standard operating procedures.<br>Please enter a reason for the deviation below.</div></div>   </td>

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
<table><tr><td class=headhead valign=bottom>Override PIN (Inventory PIN)</td></tr>
<tr><td style="padding: 0 0 0 .5vw;"><input type=password id=fldUsrPIN style="width: 11vw; font-size: 1.3vh;"></td>


</tr>
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
       <tr><td class=DNRLbl>Procedure Date: </td><td class=DNRDta>{$ORDate} <input type=hidden value='{$ORDate}' id=fldProcedureDate> @ {$starttime}</td><td class=DNRLbl>Surgeon: </td><td class=DNRDta>{$surgeons} </td></tr>
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
ERROR: NO DONOR RECORD FOUND.  SEE A CHTNEAST INFORMATICS STAFF MEMBER {$passeddata['phicode']}
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

function dropmenuMolecularTests ( $defaultvalue = "" ) { 
  $moletest = json_decode(callrestapi("GET", dataTree . "/immuno-mole-testlist",serverIdent,serverpw),true);
  //molecular test
  $molemnu = "<table border=0 width=100% class=\"menuDropTbl hprNewDropDownFont\"><tr><td align=right onclick=\"triggerMolecularFill(0,'','','{$idsuffix}');\" class=ddMenuClearOption>[clear]</td></tr>";
  foreach ($moletest['DATA'] as $moleval) { 
    $molemnu .= "<tr><td onclick=\"triggerMolecularFill({$moleval['menuid']},'{$moleval['menuvalue']}','{$moleval['dspvalue']}','{$idsuffix}');\" class=ddMenuItem>{$moleval['dspvalue']}</td></tr>";
  }
  $molemnu .= "</table>";
  $molemenu = "<div class=menuHolderDiv><input type=hidden id=fldMoleTest{$idsuffix}Value><input type=text id=fldMoleTest{$idsuffix} READONLY><div class=valueDropDown style=\"min-width: 25vw;\">{$molemnu}</div></div>";
  return array('menuObj' => $molemenu,'defaultDspValue' => "", 'defaultLookupValue' => "");
}


function dropmenuTumorGrade ( $defaultvalue =  "" ) { 
   $si = serverIdent;
   $sp = serverpw;
   $tmrGradeScaleList = json_decode(callrestapi("GET", dataTree . "/global-menu/hpr-tumor-grade-scale",$si,$sp), true);
//BUILD TUMOR GRADE 
   $tmrGrd = "<table border=0 class=\"menuDropTbl\"><tr><td align=right onclick=\"fillField('fldTumorGradeScale','','');\" class=ddMenuClearOption>[clear]</td></tr>";
   $thistumorscalevalue = "";
   $thistumorscaledsp = "";
   foreach ( $tmrGradeScaleList['DATA'] as $procval) { 
      if ( $procval['lookupvalue'] === $defaultvalue ) { 
         $thistumorscalevalue = $procval['lookupvalue'];
         $thistumorscaledsp = $procval['menuvalue'];
      }
      $tmrGrd .= "<tr><td onclick=\"fillField('fldTumorGradeScale','{$procval['lookupvalue']}','{$procval['menuvalue']}');\" class=ddMenuItem>{$procval['menuvalue']}</td></tr>";
   }
   $tmrGrd .= "</table>";
//END TUMOR GRADE
   $tmrgrdmenu = "<div class=menuHolderDiv><input type=hidden id=fldTumorGradeScaleValue value=\"{$thistumorscalevalue}\">"
   . "<input type=text id=fldTumorGradeScale READONLY value=\"{$thistumorscaledsp}\"><div class=valueDropDown id=ddPRCUnInvolved>{$tmrGrd}</div></div>";
  return array('menuObj' => $tmrgrdmenu,'defaultDspValue' => $thistumorscaledsp, 'defaultLookupValue' => $thistumorscalevalue);

}

function dropmenuUninvolvedIndicatorA( $defaultvalue = "") { 
   //TODO:   BAD BAD BAD ZACK - COMBINE THE FUNCTION dropmenuUninvolvedIndicator with this one!!!  
   $si = serverIdent;
   $sp = serverpw;
   $unknmtarr = json_decode(callrestapi("GET", dataTree . "/global-menu/uninvolved-indicator-options",$si,$sp),true);
   $uninv = "<table border=0 class=\"menuDropTblA\">";
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
      $uninv .= "<tr><td onclick=\"fillField('fldPRCUnInvolved','{$uninvval['codevalue']}','{$uninvval['menuvalue']}');\" class=ddMenuItemA>{$uninvval['menuvalue']}</td></tr>";
   }
   $uninv .= "</table>";
   $uninvmenu = "<div class=menuHolderDiv><input type=hidden id=fldPRCUnInvolvedValue value=\"{$uninvDefaultValue}\">"
   . "<input type=text id=fldPRCUnInvolved READONLY value=\"{$uninvDefaultDsp}\"><div class=valueDropDown id=ddPRCUnInvolved>{$uninv}</div></div>";
  return array('menuObj' => $uninvmenu,'defaultDspValue' => $uninvDefaultDsp, 'defaultLookupValue' => $uninvDefaultValue);
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
      session_start();
      $ky = generateRandomString();
      $sessid = cryptservice( session_id() . "::" . date('YmdHis')  , 'e', true, session_id());
      $today = new DateTime('now');
      $tdydte = $today->format('m/d/Y');
      $tdydtev = $today->format('Y-m-d');
      $orscheddater = bldSidePanelORSched( $usr->presentinstitution, $tdydte, $tdydtev );
      //TODO:REMOVE THIS LINE TO DEFAULT TO TODAY'S DATE
      //$tdydtev = '20180507';
      $orlistTbl = bldORScheduleTbl(  json_decode(callrestapi("GET", dataTree . "/simple-or-schedule/{$usr->presentinstitution}/{$tdydtev}",serverIdent, serverpw), true) );
      $procGrid = bldProcurementGrid($usr); //THIS IS THE PROCUREMENT GRID ELEMENTS
      //$dvault = phiserver . "/" . $sessid;
      $dvault = $sessid;
      $linkToVault = "";
      if ( (int)$usr->allowpxi === 1 ) {
        $linkToVault = "<div class=ttholder onclick=\"popDV('{$dvault}');\"><i class=\"material-icons\">account_balance</i><div class=tt>Access Donor Vault</div></div>";
      }

      $holdingTbl = <<<HOLDINGTBL
            <div id=initialBiogroupInfo>
            <table border=0 id=procurementAddHoldingTbl>
                   <tr>
                      <td valign=top id=procbtnsidebar>
                          <center>
                          <div class=ttholder onclick="openAppCard('appcard_procphilisting');"><i class="material-icons">how_to_reg</i><div class=tt>Donor Information/Operative Schedule</div></div>
                          {$linkToVault}
                      </td>
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
                    oncontextmenu = "genSegContext(event, {$svl['segmentid']} );"
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
      if ( (int)$assCount > 0 && trim($bg['associativegroup']) !== "" ) { 
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

