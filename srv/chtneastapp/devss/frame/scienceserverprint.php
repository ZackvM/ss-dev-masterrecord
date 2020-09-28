<?php

class printobject { 
     
    public $httpresponse = 500;
    public $objectname = "";
    public $htmlpdfind = 0; //0=PDF 1=html
    public $pagetitle = "";
    public $pagetitleicon = "";
    public $headr = ""; 
    public $style = "";
    public $bodycontent = "";

    private $registeredPages = array('pathologyreport','shipmentmanifest','reports','chartreviewreport', 'helpfile', 'systemreports','systemobjectrequests','furtheractionticket','inventoryitemtag','inventorymanifest','manifestbarcoderun','autoreportprinter'); //chartreview when that is built 
    //pxchart = Patient Chart
    
    function __construct() { 		  
      $args = func_get_args();   
       if (trim($args[0]) === "") {	  		
       } else {
         $rq = explode("/", str_replace("-","",$args[0]));
         if (in_array($rq[2], $this->registeredPages)) {
             //ALLOWED PAGE
             $elements = self::getPrintObject($rq, $args[0]);     
             $this->object = $elements['object'];
             $this->htmlpdfind = $elements['htmlpdfind'];
             $this->pagetitle = $elements['pagetitle'];
             $this->pagetitleicon = $elements['tabicon'];
             $this->headr = $elements['headr'];             
             $this->style = $elements['style'];
             $this->bodycontent = $elements['bodycontent'];
             $this->httpresponse = 200;
         } else { 
             $this->httpresponse = 404;
             $this->object = "";
             $this->htmlpdfind = null;
             $this->pagetitle = "";
             $this->pagetitleicon = "";
             $this->headr = "";
             $this->bodycontent = "<h1>OBJECT TYPE NOT FOUND IN SCIENCESERVER {$rq[2]}</h1>";             
         }         
       }
    }
    
    function getPrintObject($rqArr, $originalURI) {
        $elArr = array();
        $docType = $rqArr[2];
        $docId = $rqArr[3];        
        $conDoc = new documentconstructor();
    
        $elArr['object'] =  (method_exists($conDoc,'unencryptedDocID') ? $conDoc->unencryptedDocID($docType, $docId) : "");
        $elArr['pagetitle'] = (method_exists($conDoc,'pagetabs') ? $conDoc->pagetabs($elArr['object'])   : "");        
        $elArr['headr'] = (method_exists($conDoc,'generateheader') ? $conDoc->generateheader() : ""); 
        $elArr['tabicon'] = (method_exists($conDoc,'faviconBldr') ? $conDoc->faviconBldr($elArr['object']) : "");
        $elArr['style'] = (method_exists($conDoc,'globalstyles') ? $conDoc->globalstyles() : "");        
        $elArr['bodycontent'] = $elArr['object']['documentid'];
        $bdy = (method_exists($conDoc,'documenttext') ? $conDoc->documenttext( $elArr['object'], $originalURI) : "");
        if ($bdy['format'] === "pdf") { 
         $elArr['bodycontent'] = $bdy['pathtodoc'];   
         $elArr['htmlpdfind'] = 0;
        } else { 
         $elArr['bodycontent'] = $bdy['text'];               
         $elArr['htmlpdfind'] = 1;   
        }        
        
        return $elArr;
    }
    
}

class documentconstructor { 
    
function unencryptedDocID( $docType, $encryptedDocId ) {    
    $unencry = cryptservice( $encryptedDocId , 'd');
    $dt = "";
    $docid = "";
    $bgnbr = "";
    $donor = "";

    require(serverkeys . "/sspdo.zck");  
    switch ($docType) { 
        case 'pathologyreport':
            $dt = "PATHOLOGY REPORT";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            //TODO:  TURN INTO WEBSERVICE
            $prSQL = "select dnpr_nbr bgnbr from masterrecord.qcpathreports where prid = :prid"; 
            $prR = $conn->prepare($prSQL);
            $prR->execute(array(':prid' => $docid));
            $pr = $prR->fetch(PDO::FETCH_ASSOC);
            $bgnbr = $pr['bgnbr'];
            break;
        case 'chartreviewreport':     
            $dt = "CHART REVIEW";
            $docid = $unencry;
            break;        
        case 'autoreportprinter':     
            $dt = "PRINT AUTO REPORT";
            $docid = $unencry;
            break;
        case 'shipmentmanifest':
            $dt = "SHIPMENT MANIFEST";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            $prSQL = "select shipdocrefid FROM masterrecord.ut_shipdoc sd where shipdocrefid = :prid"; 
            $prR = $conn->prepare($prSQL);
            $prR->execute(array(':prid' => $docid));
            $pr = $prR->fetch(PDO::FETCH_ASSOC);
            $bgnbr = '';                  
            break;
        case 'furtheractionticket':
            $dt = "FATICKET";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            $bgnbr = '';                  
            break;
        case 'inventoryitemtag':
            $dt = "INVITEMTAG";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            $bgnbr = '';                  
            break;
        case 'reports': 
            $dt = "SCIENCESERVER PRINTABLE REPORTS";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            $bgnbr = '';                  
            break;
        case 'systemreports': 
            $dt = "SCIENCESERVER SYSTEM REPORTS";
            $docIdElem = explode("-", $unencry);
            $docid = $unencry;
            $bgnbr = '';                  
            break;
        case 'helpfile':
            $dt = "SCIENCESERVER HELP DOCUMENT";
            $docIdElem = $unencry;
            $docid = $docIdElem;
            $bgnbr = '';                  
            break;
        case 'systemobjectrequests':
            $dt = "SCIENCESERVER SYSTEM OBJECT";
            $docIdElem = $unencry;
            $docid = $unencry;
            $bgnbr = '';                  
            break;
        case 'inventorymanifest':
            $dt = "SCIENCESERVER INVENTORY MANIFEST";
            $docIdElem = $unencry;
            $docid = $unencry;
            $bgnbr = '';                  
            break; 
        case 'manifestbarcoderun':
            $dt = "SCIENCESERVER MANIFEST BARCODE RUN";
            $docIdElem = $unencry;
            $docid = $unencry;
            $bgnbr = '';                  
            break;
        default: 
            //RETURN ERROR
    }    
    return array('document' => $dt, 'documentid' => $docid, 'bgnbr' => $bgnbr, 'donor' => $donor );
}    
    
function generateheader() {
  $tt = treeTop;      
  $at = genAppFiles;
  $rtnThis = <<<STANDARDHEAD
       
<!-- <META http-equiv="refresh" content="0;URL={$tt}"> //-->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="refresh" content="28800">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
STANDARDHEAD;
  return $rtnThis;
} 

function faviconBldr($whichpage) { 
  $at = genAppFiles;
  $favi = base64file("{$at}/publicobj/graphics/icons/chtnblue.ico", "favicon", "favicon", true);
  return $favi;
}

public $color_white = "255,255,255";
public $color_black = "0,0,0";
public $color_dark = "0,33,113";
public $color_med = "127, 174, 249";
public $color_darkgrey = "145,145,145";
public $color_dangerred = "255, 28, 12";
public $color_darkgreen = "0, 112, 13";
public $color_lightgrey = "239, 239, 239";
public $color_zackgrey = "48,57,71";  //#303947
public $color_zackcomp = "235,242,255"; //#ebf2ff


function globalstyles() {
$rtnthis = <<<RTNTHIS
@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; box-sizing: border-box;}
body { margin: 0;  height: 100%; width: 100%; box-sizing: border-box; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }

RTNTHIS;
return $rtnthis;    
}            
    
function pagetabs($docobject) {
  
  switch($docobject['document']) { 
    case 'PATHOLOGY REPORT':
      $thisTab = "{$docobject['bgnbr']} (Pathology Report)";
      break;
    case 'SCIENCESERVER PRINTABLE REPORTS':
      $thisTab = "ScienceServer Printable Reports";
      break; 
    case 'SCIENCESERVER SYSTEM REPORTS':
      $thisTab = "ScienceServer System Reports";
      break;  
    case 'CHART REVIEW':
      $dspdoc = 'CR-' . substr(('000000' . $docobject['documentid']),-6);
      $thisTab = "Chart Review {$dspdoc}";
      break;
    case 'SCIENCESERVER HELP DOCUMENT':
      $thisTab = "ScienceServer Help Document";
      break;
    case 'SHIPMENT MANIFEST':
      $thisTab = substr(('000000' . $docobject['documentid']),-6) . " Shipment Document"; 
      break;
    case 'SCIENCESERVER SYSTEM OBJECT':
      $thisTab = "ScienceServer Object";
      break;
    case 'SCIENCESERVER INVENTORY MANIFEST': 
      $mNbr = (  substr($docobject['documentid'],0,5) !== 'DtaC:' ) ?  $docobject['documentid']: substr($docobject['documentid'],5);
      $thisTab = "{$mNbr} ScienceServer Inventory Manifest";
      break;
    case 'SCIENCESERVER MANIFEST BARCODE RUN':
      $thisTab = "ScienceServer Manifest Barcode Run";
      break;
    case 'PRINT AUTO REPORT':
      $thisTab = "ScienceServer Printable Auto Email";
      break;  
    default: 
      $thisTab =  "SCIENCESERVER PRINTABLE DOCUMENTS"; 
    break; 
  }
  return $thisTab;
}

function documenttext($docobject, $orginalURI) { 
    $doctext = "";
    switch($docobject['document']) { 
    case 'PATHOLOGY REPORT': 
        $doctext = getPathReportText($docobject['documentid'], $orginalURI);
        break;
    case 'SHIPMENT MANIFEST':
        $doctext = getShipmentDocument($docobject['documentid'], $orginalURI);
        break;
    case 'CHART REVIEW': 
        $doctext = getChartReview($docobject['documentid'], $orginalURI);
        break;
    case 'INVITEMTAG': 
        $doctext = getInvItemTag ($docobject['documentid'], $orginalURI);
        break;        
    case 'FATICKET': 
        $doctext = getFATicket ($docobject['documentid'], $orginalURI);
        break;        
    case 'SCIENCESERVER PRINTABLE REPORTS':
        $doctext = getPrintableReport($docobject['documentid'],$orginalURI);
        break;
    case 'SCIENCESERVER SYSTEM REPORTS':
        $doctext = getSystemPrintReport ( $docobject['documentid'],$originalURI); 
        break;
    case 'SCIENCESERVER HELP DOCUMENT':
        $doctext = getSystemHelpDocument($docobject['documentid'],$orginalURI);
        break;    
    case 'SCIENCESERVER SYSTEM OBJECT':
        $doctext =  getSystemObject($docobject['documentid'], $originalURI) ;
        break;
    case 'SCIENCESERVER INVENTORY MANIFEST':
        $doctext = getInventoryManifest ( $docobject['documentid'] , $originalURI );
        break;
    case 'SCIENCESERVER MANIFEST BARCODE RUN':
        $doctext = getInventoryManifestBarcodeRun ( $docobject['documentid'] , $originalURI );
        break;
    case 'PRINT AUTO REPORT':    
        $doctext = getAutoEmailRpt ( $docobject['documentid'] , $originalURI ) ;
        break;
    }
    return $doctext;
}
 
}

function getSystemPrintReport( $docid, $originalURL ) {
    
  $rptarr = json_decode(callrestapi("GET", dataTree . "/report-parts/{$docid}",serverIdent, serverpw), true);
  //{"MESSAGE":"","ITEMSFOUND":1,"DATA":{"bywho":"proczack","onwhen":"03\/09\/2019","reportmodule":"system-reports","reportname":"dailypristinebarcoderun","requestjson":"{\"rptRequested\":\"dailypristinebarcoderun\",\"user\":[{\"originalaccountname\":\"proczack\",\"emailaddress\":\"zacheryv@mail.med.upenn.edu\",\"allowind\":1,\"allowproc\":1,\"allowcoord\":1,\"allowhpr\":1,\"allowinvtry\":1,\"allowfinancials\":1,\"presentinstitution\":\"HUP\",\"daystilexpire\":139,\"accesslevel\":\"ADMINISTRATOR\",\"accessnbr\":\"43\"}],\"request\":{\"rptsql\":{\"selectclause\":\"sg.bgs, sg.prp, sg.prpmet, bs.speccat, bs.primarysite\",\"fromclause\":\"four.ut_procure_segment sg left join four.ref_procureBiosample_designation bs on sg.pbiosample = bs.pbiosample\",\"whereclause\":\"sg.voidind <> 1 and sg.activeind = 1 and sg.procuredAt = :thisInstitution and date_format(inputon,'%Y-%m-%d') = :thisDate\",\"summaryfield\":\"\",\"groupbyclause\":\"\",\"orderby\":\"sg.bgs\",\"accesslevel\":3,\"allowpdf\":1}}}","typeofreportrequested":"PDF","dspreportname":"Daily Pristine Barcode Run","dspreportdescription":"A sheet of printable barcodes for the institution at which you are currently ","rptcreator":"Zack","rptcreatedon":"March 08, 2019","rqaccesslvl":3,"groupingname":"PROCUREMENT REPORTS"}} 
  $reportnme = $rptarr['DATA']['reportname'];
  $responseCode = 404;
  $format = 'HTML';
  $printrptsfromdata = new sysreportprintables(); 
  if (  method_exists( $printrptsfromdata,   $reportnme )) { 
    $sDocFile = $printrptsfromdata->$reportnme($rptarr);
    $docText = $sDocFile;
    $cmds = new whtmltopdfcommands(); 
    if ( method_exists( $cmds, $reportnme )) { 
      $linuxcmd = $cmds->$reportnme($rptarr);
    } else { 
      //default
      $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED PRINTABLE REPORT MODULE\" ";
    }    
    $responseCode = 200;
  } else { 
    $sDocFile = "THIS PRINTABLE DOES NOT EXISTS: {$docid} ... ";
    $docText = $sDocFile;
  } 

  //IF RESPONSECODE IS 200 CONVERT TO PDF
  if ($responseCode === 200) { 
    $filehandle = generateRandomString();                
    $sdDocFile = genAppFiles . "/tmp/sysrpt{$filehandle}.html";
    $sdDhandle = fopen($sdDocFile, 'w');
    fwrite($sdDhandle, $docText);
    fclose;
    $sdPDF = genAppFiles . "/publicobj/documents/shipdoc/shipdoc{$filehandle}.pdf";
    $linuxCmd = "wkhtmltopdf --load-error-handling ignore {$linuxcmd} {$sdDocFile} {$sdPDF}";
    $output = shell_exec($linuxCmd);
    $format = "pdf";    
  }
  return array('status' => $responseCode, 'text' => $docText, 'pathtodoc' => $sdPDF, 'format' => $format);
}

class sysreportprintables { 

    function dailypristinebarcoderun($rptdef) { 
      $at = genAppFiles;
      $tt = treeTop;
      require("{$at}/extlibs/bcodeLib/qrlib.php");
      $tempDir = "{$at}/tmp/";
      $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" "); 
      $rqst = json_decode($rptdef['DATA']['requestjson'], true);            
      $presInst = $rqst['user'][0]['presentinstitution'];
      $r = "Run By: {$rqst['user'][0]['emailaddress']} at " . date('H:i');
      $tday = date('Y-m-d');
      //$tday = '2019-03-13';  
      $tdaydsp = date('m/d/Y');
      //$tdaydsp = '03/13/2019'; 
      $dta['presentinstitution'] = $presInst;
      $dta['requesteddate'] = $tday;
      $dta['usrsession'] = session_id();
      $pdta = json_encode($dta);
      $rslts = json_decode(callrestapi("POST", dataTree . "/data-doers/pristine-barcode-run", serverIdent, serverpw, $pdta), true); 
      
    foreach ( $rslts['DATA'] as $records) { 
       if ($cellCntr === 2) { 
         $rowTbl .= "</tr><tr>";
         $cellCntr = 0;
       }
       
       //****************CREATE BARCODE
        $codeContents = "{$records['bgs']}";
        $fileName = 'bc' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "", "png", true," style=\"height: 1in;\" ");
        //********************END BARCODE CREATION
       
    $lblTbl = <<<LBLLBL
<table border=0 cellpadding=0 cellspacing=0 style="width: 4in; height: 5.21in; border: 1px solid #000000; box-sizing: border-box;">
<tr><td style="padding: 0 0 0 4px;">{$favi}</td><td align=right valign=bottom> 

   <table>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 14pt; color: #000084; font-weight: bold; text-align: right;">CHTNEastern Biosample</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 10pt; color: #000084; font-weight: bold; text-align: right;">3400 Spruce Street, DULLES 565<br>Philadelphia, PA 19104</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">(215) 662-4570</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">https://www.chtneast.org</td>
     </tr></table>

</td></tr>
<tr><td colspan=2><center>{$qrcode}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Biosample Number</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['bgs']}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Diagnosis Designation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['speccat']} :: {$records['primarysite']} :: {$records['primarysubsite']} :: {$records['diagnosis']} :: {$records['diagnosismodifier']}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Preparation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['prpmet']} ({$records['prp']} / {$records['metric']} {$records['longvalue']})<br>{$records['pxiage']} {$records['pxiAgeUOM']} / {$records['pxirace']} / {$records['pxigender']}</td></tr>
<tr><td colspan=2 style="max-height: 3in;">&nbsp;</td></tr>
</table>
LBLLBL;
    $rowTbl .= "<td>{$lblTbl}</td>";
    $cellCntr++;
    } 
 $resultTbl .= "<table border=0 style=\"width: 8in;\"><tr>{$rowTbl}</tr></table>"; 
      return $resultTbl;    
    } 

    function histologysheet($rptdef) { 
      $at = genAppFiles;
      $tt = treeTop;
      $favi = base64file("{$at}/publicobj/graphics/psom_logo_blue.png", "upennicon", "png", true, " style=\"height: .3in;  \" ");
      $r = $rptdef;
      $rptspec = json_decode($r['DATA']['requestjson'],true);

      $usr = $rptspec['user'][0]['originalaccountname'];
      $usremail = $rptspec['user'][0]['emailaddress'];
      $timestamp = strtotime( $r['DATA']['onwhen'] );
      $dspdate = date('Ymd',$timestamp);
      $presentinstitution = $rptspec['user'][0]['presentinstitution'];

    //****************CREATE BARCODE 
      $vbcode = "{$at}/tmp/hist" . generateRandomString() . ".png";
      ////code128/code128a/code128b/code39/code25/
      prntbarcode("{$vbcode}", "CHTN" . $dspdate, "30", "horizontal", "code39", false, 1); 
      $sidebcode = base64file("{$vbcode}", "sidebarcode", "png", true, "  ");
    //********************END BARCODE CREATION

      
    require(genAppFiles . "/dataconn/sspdo.zck"); 
    $dspdata = array(); 
    //TURN THIS INTO A WEBSERVICE 
    //TOOK OUT WHERE nbrOSlides IS NOT NULL 
    $dataSQL = "SELECT pb.pbiosample, pb.seglabel, des.speccat, des.primarysite, pb.prp, pb.prpmet, qty as nbrofblock, ifnull(nbrOSlides,'') as nbrOSlides, ifnull(sld.prpmet,'') as sldprpmet FROM four.ut_procure_segment pb left join ( select count(1) as nbrOSlides, prpMet, cutfromblockid from four.ut_procure_segment where procuredat = :instone and date_format(inputon,'%Y%m%d') = :dteone and prp = 'SLIDE' group by prpmet, cutfromblockid) as sld on pb.bgs = sld.cutfromblockid left join (select * from four.ref_procureBiosample_designation where activeind = 1) des on pb.pbiosample = des.pbiosample where pb.procuredat = :insttwo and date_format(pb.inputon,'%Y%m%d') = :dtetwo and pb.prp = 'PB' UNION SELECT casebg, casesegment, label, specat, 'PB', 'FFPE', nbrofblocks, nbrofslides, slidetype FROM four.ut_tmp_histoadd where dspind = 1 and date_format(addedon, '%Y-%m-%d') = date_format(now(),'%Y-%m-%d') order by 1";
    $dataRS = $conn->prepare($dataSQL); 
    $dataRS->execute(array( ':instone' => $presentinstitution, ':insttwo' => $presentinstitution, ':dteone' => $dspdate, ':dtetwo' => $dspdate)); 
    while ( $rd = $dataRS->fetch(PDO::FETCH_ASSOC)) { 
      $dspdata[] = $rd;
    }
    
    //four.tmp_histoSheetAdds
    

    //[{"pbiosample":87905,"seglabel":"001","specCat":"NORMAL","primarySite":"KIDNEY","prp":"PB","prpmet":"H&ESLIDE","nbrofblock":1,"nbrOSlides":1},
    $dsptbl = "<table border=0 cellspacing=0 cellpadding=0 width=100% id=rqstTbl><thead><th rowspan=2>#</th><th rowspan=2>Case<br>Number</th><th rowspan=2>Container<br>Number</th><th rowspan=2>Label</th><th rowspan=2>Tissue</th><th rowspan=2>Prep</th><th rowspan=2>Added</th><th rowspan=2># of<br>Block</th><th colspan=2 style=\"border-bottom: none; border-right: 2px solid rgba(48,57,71,1);\">Slide Requests</th><th rowspan=2>Cass.<br>Sent</th><th rowspan=2>Cass.<br>Recvd</th><th rowspan=2>SLIDE<br>Sent</th><th rowspan=2>SLIDE<br>Rcvd</th></tr><tr><th style=\"border-right: none;\">#</th><th style=\"border-right: 2px solid rgba(48,57,71,1);\">Type</th></thead><tbody>";

    $cntr = 1;
    $items = 0;
    $slidesrq = 0;
    foreach ( $dspdata as $k => $v ) {
      $nob = (int)$v['nbrofblock'];  
      $dsptbl .= "<tr><td class=linecounter>{$cntr}</td><td class=casedsp>{$v['pbiosample']}</td><td>{$v['seglabel']}</td><td>{$v['primarySite']}</td><td>{$v['specCat']}</td><td>{$v['prp']}/{$v['prpmet']}</td><td>&nbsp;</td><td valign=right>{$nob}</td><td>{$v['nbrOSlides']}</td><td style=\"border-right: 2px solid rgba(48,57,71,1);\">{$v['sldprpmet']}</td><td class=writables>&nbsp;</td><td class=writables>&nbsp;</td><td class=writables>&nbsp;</td><td class=writables>&nbsp;</td></tr>";
      $slidesrq += (int)$v['nbrOSlides'];
      $cntr++;
      $items++;
    }

    $dttmdsp = date('M dS, Y H:i'); 
    $footTbl = "<table border=0><tr><td align=right>Total Requests: {$items}</td></tr>   <tr><td align=right><b>Slides Requested</b>: <b>{$slidesrq}</b></td></tr>  <tr><td align=right><b>Requesting Technician</b>: {$usr}</td></tr>   <tr><td align=right><b>Printed</b>: {$dttmdsp}</td></tr>      </table>";

    $dsptbl .= "</tbody><tfoot><tr><td colspan=10 align=right>{$footTbl}</td><td colspan=4 valign=bottom align=right style=\"padding: 0 2px 4px 0;\">{$sidebcode}</td></tr><tr><td colspan=14 style=\"font-size: 9pt; fotn-weight: bold; text-align: center; padding: 8px 4px 4px 4px;\">FOR QUESTIONS CALL (215) 662-4570 OR EMAIL CHTNMAIL@UPHS.UPENN.EDU</td></tr></tfoot></table>";

//<tr><td> {$usr} {$usremail} {$presentinstitution} </td></tr>
    $resultTbl = <<<RTNTHIS

<style> 
@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; box-sizing: border-box;}
body { margin: 0;  height: 100%; width: 100%; box-sizing: border-box; font-family: Roboto; color: rgba(48,57,71,1); }
#rqstTbl { font-size: 10pt; margin-top: 10px; border: 2px solid rgba(48,57,71,1); }
#rqstTbl thead th { background: #eee; border-bottom: 2px solid rgba(48,57,71,1); border-right: 1px solid rgba(48,57,71,1); font-size: 8pt; font-weight: bold; padding: 4px 0 4px 0;  } 
#rqstTbl thead th:nth-last-child(1) { border-right: none; } 

#rqstTbl tbody td { font-size: 9pt; padding: 3px; border-bottom: 1px solid rgba(48,57,71,1); border-right: 1px solid rgba(48,57,71,1); }
#rqstTbl tbody td:nth-last-child(1) { border-right: none; }

.linecounter { width: 20px; text-align: center; }
.casedsp { 30px; }  
.writables { width: 30px; }  

#rqstTbl tfoot tr td table tr td { border-right: none; border-bottom: none; padding: 0; } 

</style>

<table border=0 style="width: 8in;">
<tr><td>
  <table width=100% cellspacing=0 cellpadding=0 border=0><tr><td width=25%>{$favi}</td>
                                                             <td style="font-size: 8pt;text-align:center;" width=50%><b>Hospital of the University of Pennsylvania</b><br>Department of Pathology and Laboratory Medicine<br>Histology Tissue Section Log</td>
                                                             <td align=right>{$sidebcode}</td></tr>
  </table>
  </td></tr>
<tr><td> <table width=100% cellspacing=0 cellpadding=0 border=0>
                   <tr><td valign=top width=25% style="font-size: 9pt;"><b>PA</b>: {$presentinstitution} <br>CHTN/VAL</td>
                       <td width=50% style="text-align: center;"><span style="font-size: 22pt; font-weight: bold;">NO EOSIN</span><br><span style="font-size: 16pt;font-style: italic;">HISTOLOGY</span></td>
                       <td valign=top align=right style="font-size: 9pt;">Date: [{$r['DATA']['onwhen']}] <br>Tops Written: {&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>Slides Written: {&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;} </td></tr></table>              </td></tr>

<tr><td> {$dsptbl} </td></tr>

</table>
RTNTHIS;




      return $resultTbl;    
    }

    function histologyembed($rptdef) { 
    //  $at = genAppFiles;
    //  $tt = treeTop;
    //  $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
    //  $r = json_encode($rptdef);
//
    //  $resultTbl .= "<table border=0 style=\"width: 8in;\"><tr>{$r}</tr></table>"; 
        //  return $resultTbl;    
        //  NOT USED !!!!!!!!!!
    }

    function dailypristineprocurementsheet($rptdef) { 
      $at = genAppFiles;
      $tt = treeTop;
      $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
      
       $rqst = json_decode($rptdef['DATA']['requestjson'], true);            
      //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = json_encode(array("institution" => "{$rqst['user'][0]['presentinstitution']}", "date" => date('Y-m-d'), "rpttitle" => "PRISTINE PROC SHEET"));
        $fileName = 'procsht' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .6in;\"   ");
        
        //********************END BARCODE CREATION
      
      $tday = date('Y-m-d');
      //$tday = '2019-03-13';  
      $tdaydsp = date('m/d/Y');
      //$tdaydsp = '03/13/2019';

      $presInst = $rqst['user'][0]['presentinstitution'];
      $r = "Run By: {$rqst['user'][0]['emailaddress']} at " . date('H:i');

        $dta['presentinstitution'] = $presInst;
        $dta['requesteddate'] = $tday;
        $dta['usrsession'] = session_id();
        $pdta = json_encode($dta);
        $rslts = json_decode(callrestapi("POST", dataTree . "/data-doers/collection-grid-results",serverIdent, serverpw, $pdta),true);  
        $rsltdta = json_decode($rslts['DATA'], true);

        $bgheader = "background: #000000; color: #ffffff; font-size: 7pt;padding: 4px;";
        $rsltDsp = "<table border=0 cellspacing=0 cellpadding=0 style=\"font-size: 8pt; color: #303947; width: 100%; margin-top: 15px;border: 1px solid #000000; \">";
        $thisBG = "";
        $countThis = 0;

        foreach ($rsltdta as $key => $val) {

            if ( strtoupper( $presInst ) === strtoupper( $val['institution'] ) ) {  
                $dspInst = $val['dspinstitution'];
                if ( $thisBG !== $val['pbiosample']) { 
                    //ADD BG
                  if ( $countThis === 0 ) {
                  } else { 
                    $rsltDsp .= "</table></td></tr>";
                  }
                  $dCnt = $countThis + 1;
                  $voidThis = ( (int)$val['voidind'] === 1 ) ? "text-decoration: line-through; " : "";
                  $styleThis = "padding: 6px;border-top: 1px solid #A0A0A0; border-bottom: 1px solid #A0A0A0; border-right: 1px solid #A0A0A0; ";

                  $bsC = ( trim($val['bscomment']) !== "" ) ? "<tr><td colspan=30 style=\"background: #f5f5f5; color: #303947;padding: 3px 0 3px 3px;font-size: 7pt; font-weight: bold; \">Biosample Comment</th></tr><tr><td colspan=30 style=\"{$voidThis}{$styleThis}border-right: none;\">{$val['bscomment']}</td></tr>" : "";
                  $hprC = ( trim($val['hprcomment']) !== "" ) ? "<tr><td colspan=30 style=\"background: #f5f5f5; color: #303947;padding: 3px 0 3px 3px;font-size: 7pt; font-weight: bold; \">HPR Question</th></tr><tr><td colspan=30 style=\"{$voidThis}{$styleThis}border-right: none;\">{$val['hprcomment']}</td></tr>" : "";

                  $sbpr = ( trim( $val['subjectnumber'] ) !== "") ? trim($val['subjectnumber']) : "";
                  $sbpr .= ( trim( $val['protocolnumber'] ) !== "") ? ( $sbpr === "") ? trim($val['protocolnumber']) : " / {$val['protocolnumber']}" : "";      


        $rsltDsp .= "<tr><th style=\"{$bgheader}\">BG#</th><th style=\"{$bgheader}\">Category</th><th style=\"{$bgheader}\">Site</th><th style=\"{$bgheader}\">Diagnosis</th><th style=\"{$bgheader}\">Mets Diagnosis</th><th style=\"{$bgheader}\">Procedure</th><th style=\"{$bgheader}\">Initial<br>Metric</th><th style=\"{$bgheader}\">Inf<br>Con</th><th style=\"{$bgheader}\">A/R/S</th><th style=\"{$bgheader}\">Subject/Protocol</th><th style=\"{$bgheader}\">Time / Technician</th></tr>";
                  $rsltDsp .= <<<BIOGROUPLINE
<tr>
  <td style="{$voidThis}{$styleThis}">{$val['pbiosample']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['specimencategory']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['site']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['diagnosis']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['metsdx']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['proctype']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['metuom']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['informedconsent']}</td>
  <td style="{$voidThis}{$styleThis}">{$val['pxiage']} / {$val['pxirace']} / {$val['pxisex']}</td>
  <td style="{$voidThis}{$styleThis}">{$sbpr}</td>
  <td style="{$voidThis}{$styleThis} border-right: none;">{$val['timeprocured']} / {$val['technician']}</td>
</tr>
                  {$bsC}
<tr>
  <td colspan=30 style="padding: 0 20px 0 20px;">
    <table border=0 cellspacing=0 cellpadding=0 style="font-size: 8pt; color: #303947; {$voidThis} margin-top: 8px; margin-bottom: 8px;width: 100%; border: 1px solid #F5F5F5;">
    <tr>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Segment</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Preparation</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Metric</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">HR-Post</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Cut From</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Assignment</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">HPR</th>
      <th style="background: #f5f5f5; padding: 4px; color: #303947; text-decoration: none;font-size: 7pt;">Segment Comment</th>
    </tr>
BIOGROUPLINE;
                  $thisBG = $val['pbiosample'];
                }

                foreach ($val['segmentlist'] as $sky => $sval) {
                  $hpri = ( (int)$sval['hprind'] === 1 ) ? "Y" : "";  
                  $ass = ( strtoupper(substr($sval['assigninvestid'],0,3)) === 'INV' ) ? "{$sval['assigndspname']} ({$sval['assigninvestid']}) [{$sval['assignrequestid']}]" : "{$sval['assigninvestid']}";
                  $rsltDsp .= <<<SEGLINE
<tr>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none;" valign=top>{$sval['pbiosample']}T{$sval['segdsplbl']}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none;" valign=top>{$sval['prp']} / {$sval['prpmet']}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none; text-align: right;" valign=top>{$sval['metric']}{$sval['shortuom']}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none; text-align: right;" valign=top>{$sval['hrpost']}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none;" valign=top>{$sval['cutfromblockid']}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none;" valign=top>{$ass}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none; text-align: center;" valign=top>{$hpri}</td>
<td style="padding: 4px; border: 1px solid #f5f5f5; border-left: none; border-top: none; border-right: none;" valign=top>{$sval['sgcomments']}</td>
</tr>
SEGLINE;
                }
            $countThis++;    
            }
        }

        $rsltDsp .= "</table></td></tr></table> ";

        if ( $countThis === 0 ) { 
          $rsltDsp = "<h3>No Procurement Data Found for {$tdaydsp} at {$presInst}"; 
        }

        $rpttitle = <<<RPTTITLE
              <table border=0 cellspacing=0 cellpadding=0 style="width: 100%;">
                  <tr>
                      <td style="width: 10px; padding: 0 5px 0 0;" rowspan=3>{$favi}</td>
                      <td style="font-size: 15pt; font-weight: bold; text-align: center; height: 50px;" valign=bottom>{$rptdef['DATA']['dspreportname']}</td>
                      <td style="width: 10px;" rowspan=2>{$qrcode}</td>
                  </tr>
                  <tr>
                     <td valign=top style="font-size: 10pt; text-align: center; font-style: italic;">{$dspInst} ({$presInst}) ON {$tdaydsp} </td>    
                  </tr>  
                  <tr><td colspan=2 style="font-size: 7pt; font-weight: bold; font-style: italic; text-align: right;">{$r}</td></tr> 
               </table>
RPTTITLE;


        $resultTbl = <<<RSLTTBL
              <!-- HEADER TABLE STARTS HERE //-->
              <table border=0 style="width: 10.5in; box-sizing: border-box; color: #303947;border-bottom: 1px solid #303947;">
                  <tr><td>{$rpttitle}</td></tr>
              </table>
              <!-- HEADER TABLE ENDS HERE //-->
{$rsltDsp}
    
RSLTTBL;
      return $resultTbl;    
    }

}

function getPrintableReport($sdid, $originalURL) { 
  $rptarr = json_decode(callrestapi("GET", dataTree . "/report-parts/{$sdid}",serverIdent, serverpw), true);
  $reportnme = $rptarr['DATA']['reportname'];
  $responseCode = 404;
  $format = 'HTML';

  $printrptsfromdata = new reportprintables(); 
  if (  method_exists( $printrptsfromdata,   $reportnme )) { 
    $sDocFile = $printrptsfromdata->$reportnme($rptarr);
    $docText = $sDocFile;
    
    $cmds = new whtmltopdfcommands(); 
    if ( method_exists( $cmds, $reportnme )) { 
      $linuxcmd = $cmds->$reportnme();
    } else { 
      //default
      $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED PRINTABLE REPORT MODULE\" ";
    }    
    $responseCode = 200;
  } else { 
    $sDocFile = "THIS PRINTABLE DOES NOT EXISTS: {$reportnme} ";
    $docText = $sDocFile;
  } 

  //IF RESPONSECODE IS 200 CONVERT TO PDF
  if ($responseCode === 200) { 
    $filehandle = generateRandomString();                
    $sdDocFile = genAppFiles . "/tmp/sdz{$filehandle}.html";
    $sdDhandle = fopen($sdDocFile, 'w');
    fwrite($sdDhandle, $docText);
    fclose;
    $sdPDF = genAppFiles . "/publicobj/documents/shipdoc/shipdoc{$filehandle}.pdf";

    $linuxCmd = "wkhtmltopdf --load-error-handling ignore {$linuxcmd} {$sdDocFile} {$sdPDF}";
    $output = shell_exec($linuxCmd);
    $format = "pdf";    
  }

  return array('status' => $responseCode, 'text' => $docText, 'pathtodoc' => $sdPDF, 'format' => $format);
}

function getSystemHelpDocument($docid, $originalURL) { 

    $at = genAppFiles;
    $tt = treeTop;    
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .5in;  \" ");
    
    //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = "{$tt}{$orginalURL}";
        $fileName = 'hlpbr' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .5in;\"   ");
        
        //********************END BARCODE CREATION

    require(genAppFiles . "/dataconn/sspdo.zck"); 
    $hlpSQL = "SELECT ifnull(helpurl,'') as helpurl, ifnull(title,'') as hlpTitle, ifnull(subtitle,'') as hlpSubTitle, ifnull(bywhomemail,'') as byemail, ifnull(date_format(initialdate,'%M %d, %Y'),'') as initialdte, ifnull(lasteditbyemail,'') as lstemail, ifnull(date_format(lastedit,'%M %d, %Y'),'') as lstdte, 'THIS IS A PLACE HOLDER' as htmltxt FROM four.base_ss7_help where (replace(helpurl,'-','') = :pgename or replace(screenreference,'-','') = :pgenamea ) and helptype = :hlptype ";
    $hlpR = $conn->prepare($hlpSQL); 
    $hlpR->execute(array(':pgename' => trim($docid), ':pgenamea' => trim($docid), ':hlptype' => 'SCREEN'));
    if ($hlpR->rowCount() < 1) { 
        //NO HELP FILE
        $rthis = <<<RTNTHIS
   <div id=hlpHolderDiv>
   <div id=hlpTitle>ScienceServer Help Files</div> 
   <div id=hlpSubTitle></div>            
   <div id=hlpText>
       There is no help file for this ScienceServer screen. You can search the main help files by click the 'HELP' Menu on the main menu bar. <p> ({$docid})  
   </div>
   </div>                
RTNTHIS;
    } else { 
      $hlp = $hlpR->fetch(PDO::FETCH_ASSOC);
      $rsltdta = json_decode(callrestapi("GET", dataTree . "/help-document-text/{$hlp['helpurl']}", serverIdent, serverpw),true);
      if ( (int)$rsltdta['ITEMSFOUND'] > 0 ) {
        $sctionnbr = ""; 
        $sectionnbrdsp = 0;  
        $subsection = 0;
        $hlpTxt = "<table border=0 cellspacing=0 cellpadding=0>";
        foreach ( $rsltdta['DATA']['doctxt'] as $v  ) { 
          if ( $sctionnbr !== (int)$v['ordernbr'] ) { 
            $sectionnbrdsp += 1;
            $subsection = 1;
            $sctionnbr = (int)$v['ordernbr'];
          }
          $minor = (int)$v['versionnbr']; 
          $hlpTxt .= "<tr><td style=\"font-size: 10pt; font-weight: bold; border-bottom: 1px solid rgba(0,0,0,1); padding: 10px 0 0 0; \">Section: {$sectionnbrdsp}.{$subsection} {$v['sectionhead']} </td></tr><tr><td style=\"padding: 8px 0 0 0; font-size: 10pt; text-align: justify; line-height: 1.5em; \">" . putPicturesInHelpText( $v['sectiontext'] ) . "</td></tr>";
          $subsection++;
        }
        $hlpTxt .= "</table>";
        $modules = "";
        foreach ( $rsltdta['DATA']['modules'] as $v  ) { 
          $modules .= ( trim($modules) === "" ) ? "&#8227; {$v['module']}" : " &#8227; {$v['module']}";  
        }
        $lstby = ( trim($rsltdta['DATA']['docobj']['lstemail']) === "" ) ? "&nbsp;" : "{$rsltdta['DATA']['docobj']['lstemail']}";
        $lstdte = ( trim($rsltdta['DATA']['docobj']['lstdte']) === "" ) ? "&nbsp;" : "({$rsltdta['DATA']['docobj']['lstdte']})";
        $versioning = substr("0000{$rsltdta['DATA']['docobj']['versionmajor']}",-2) . "." . substr("0000{$rsltdta['DATA']['docobj']['versionminor']}", -2) . "." . substr("00000{$minor}",-4);
     }
          $rthis = <<<RTNTHIS
   <table border=0 style="font-family: Roboto; " cellspacing=0 cellpadding=0>
       <tr><td style="font-size: 8pt; font-weight: bold;">{$rsltdta['DATA']['docobj']['hlpType']}</td></tr>
       <tr><td style="font-size: 12pt; font-weight: bold; padding: 8px 0 0 0;">{$rsltdta['DATA']['docobj']['hlpTitle']}</td></tr>
       <tr><td style="font-size: 10pt; padding: 0 0 5px 0;">{$rsltdta['DATA']['docobj']['hlpSubTitle']}</td></tr> 
       <tr><td>{$hlpTxt}</td></tr>
   </table>
   <p>
  <center>
    <table border=0 style="margin: 55px; font-family: 10pt; border: 1px solid #000; width: 90%;">
      <tr><td colspan=4 style="font-size: 7pt; background: #000; color: #fff; font-weight: bold; padding: 5px 0 5px 3px;">Document Metrics</td></tr> 

      <tr>

        <td valign=top style="border: 1px solid #000; width: 20%">
          <table width=100%><tr><td style="font-size: 7pt; background: #000; color: #fff; font-weight: bold; padding: 3px 0 3px 3px;">Document<br>Version</td></tr>
          <tr><td style="font-size: 7pt;padding: 5px 2px;">{$versioning}</td></tr></table>
        </td>

        <td valign=top style="border: 1px solid #000;">
          <table width=100%><tr><td style="font-size: 7pt; background: #000; color: #fff; font-weight: bold; padding: 3px 0 3px 3px;">Creating<br>Author</td></tr>
          <tr><td style="font-size: 7pt;padding: 5px 2px;">{$rsltdta['DATA']['docobj']['byemail']}<br>
          ({$rsltdta['DATA']['docobj']['initialdte']})</td></tr></table>
        </td>

        <td valign=top style="border: 1px solid #000;">
          <table width=100%><tr><td style="font-size: 7pt; background: #000; color: #fff; font-weight: bold; padding: 3px 0 3px 3px;">Last<br>Edited By</td></tr>
          <tr><td style="font-size: 7pt;padding: 5px 2px;">{$lstby}<br>
          {$lstdte}</td></tr></table>
        </td>

        <td valign=top style="border: 1px solid #000;">
          <table width=100%><tr><td style="font-size: 7pt; background: #000; color: #fff; font-weight: bold; padding: 3px 0 3px 3px;">Documentation<br>Modules</td></tr>
          <tr><td style="font-size: 7pt;padding: 5px 2px;">{$modules}</td></tr></table>
        </td>
      
      </tr>
         
      </table> 

   </center>
RTNTHIS;
    }

$dte = date('l jS, M Y H:i');        
$docText = <<<RTNTHIS
<html>
<head>
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto);
         
</style>
</head>
<body>

<table border=0 cellspacing=0 cellpadding=0 width=100% style="border-bottom: 1px solid rgba(0,0,0,1);"><tr><td valign=top width=10%>{$favi}</td><td valign=top style="padding: 0 0 10px 0;"><table border=0 cellspacing=0 cellpadding=0 style="font-family: Roboto; font-size: 9pt;"><tr><td><b>CHTN Eastern Division</b><br>3400 Spruce Street, 565 DULLES<br>Philadelphia, Pennsylvania 19104<br>(215) 662-4570 | chtnmail@uphs.upenn.edu</td></tr></table></td><td valign=top align=right>{$qrcode}</td></tr><tr><td colspan=3 style="font-size: 8pt; font-weight: bold; background: rgba(0,0,0,1); color: rgba(255,255,255,1); padding: 5px 0; text-align: center;">SCIENCESERVER SOP/HELP DOCUMENTATION</td></tr></table>

<table width=100%><tr><td align=right valign=top style="font-size: 7pt; padding: 5px 0 0 0;">Print Date: {$dte}</td></tr></table>
{$rthis}



RTNTHIS;

    $filehandle = generateRandomString();                
    $sdDocFile = genAppFiles . "/tmp/hlpz{$filehandle}.html";
    $sdDhandle = fopen($sdDocFile, 'w');
    fwrite($sdDhandle,  $docText );
    fclose;
    $sdPDF = genAppFiles . "/publicobj/documents/hlpdocprints/hlpdoc{$filehandle}.pdf";
    $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED Help Document\"     {$sdDocFile} {$sdPDF}";
    //$linuxCmd = "wkhtmltopdf --load-error-handling ignore {$linuxcmd} {$sdDocFile} {$sdPDF}";
    $output = shell_exec($linuxCmd);
    $format = "pdf";    
    return array('status' => $responseCode, 'text' => $docText, 'pathtodoc' => $sdPDF, 'format' => $format);
}

function getShipmentDocument($sdid, $originalURL) { 
    $at = genAppFiles;
    $tt = treeTop;
    require(serverkeys . "/sspdo.zck");  
    $dspsd = substr("000000{$sdid}",-6);
    session_start();
    $sess = session_id(); 

    $usrChkSQL = "SELECT originalaccountname FROM four.sys_userbase where sessionid = :sessionid and allowind = 1 and sessionexpire > now() and passwordExpireDate > now()";
    $usrChkRS = $conn->prepare($usrChkSQL); 
    $usrChkRS->execute(array(':sessionid' => $sess));
    if ( $usrChkRS->rowCount() < 1 ) {
      $docText = "USER NOT ALLOWED";  
      $filehandle = generateRandomString();                
      $sdDocFile = genAppFiles . "/tmp/sdz{$filehandle}.html";
      $sdDhandle = fopen($sdDocFile, 'w');
      fwrite($sdDhandle, $docText);
      fclose;
      $sdPDF = genAppFiles . "/publicobj/documents/shipdoc/shipdoc{$filehandle}.pdf";
      $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED Shipment Doc {$dspsd}\"     {$sdDocFile} {$sdPDF}";
      $output = shell_exec($linuxCmd);
    } else {

        $usr = $usrChkRS->fetch(PDO::FETCH_ASSOC); 


    //****************CREATE BARCODE
       require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        //$codeContents = "{$tt}{$orginalURL}";
        $codeContents = "SDM-{$dspsd}";
        $fileName = 'pr' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .5in;\"   ");
        
        $vbcode = "{$at}/tmp/sd" . generateRandomString() . ".png";
//        //code128/code128a/code128b/code39/code25/
        prntbarcode("{$vbcode}", $dspsd, "30", "vertical", "code39", false, 1); 
        $sidebcode = base64file("{$vbcode}", "sidebarcode", "png", true, "  ");
        //********************END BARCODE CREATION
    

        //TODO:  TURN THIS INTO A WEBSERVICE
        $topSQL = "SELECT ifnull(sdstatus,'NEW') as sdstatus, sdte.shippeddate as actualshipmentdate, date_format(statusdate, '%m/%d/%Y') as statusdate, ifnull(shipmenttrackingnbr,'') as trackingnbr, ifnull(date_format(rqstshipdate,'%m/%d/%Y'),'') as shipdate, ifnull(investcode,'') as invcode, ifnull(investname,'') as investname, ifnull(shipaddy,'') as shipaddress, ifnull(billaddy,'') as billaddress, ifnull(investemail,'') as invemail, ifnull(ponbr,'') as ponbr, ifnull(salesorder,'') as salesorder, ifnull(date_format(rqstpulldate,'%m/%d/%Y'),'') as tolab, ifnull(acceptedby,'') as acceptedby, ifnull(acceptedbyemail,'') as acceptedbyemail, ifnull(comments,'') as comments, ifnull(date_format(setupon,'%m/%d/%Y'),'') as setupon, ifnull(setupby,'') as setupby, ifnull(courier,'') as courier, ifnull(couriernbr,'') as couriernbr FROM masterrecord.ut_shipdoc sh left join (SELECT distinct shipDocRefID, ifnull(date_format(shippeddate,'%m/%d/%Y'),'') as shippeddate FROM masterrecord.ut_procure_segment where shipdocrefid = :matchsd limit 1) as sdte on sh.shipdocrefid = sdte.shipdocrefid where sh.shipdocrefid = :sdnbr";
        
        $topR = $conn->prepare($topSQL);
        $topR->execute(array(':sdnbr' => $sdid, ':matchsd' => $sdid ));
       if ($topR->rowCount() < 1) { 
           //NO SD FOUND
       } else { 
        
            $sd = $topR->fetch(PDO::FETCH_ASSOC);

            //ACTUAL SHIPMENT DATE GOES HERE
            $sts = $sd['sdstatus'];
            $actshpdte = ( $sd['actualshipmentdate'] !== "" ) ? "({$sd['actualshipmentdate']})" : "";             
            $stsdte = $sd['statusdate'];
            $trcknbr = (trim($sd['trackingnbr']) === "") ? "" : "{$sd['trackingnbr']}";
            $shpdte = $sd['shipdate'];
            $icode = $sd['invcode'];
            $shpadd = nl2br($sd['shipaddress']);
            $billadd = nl2br($sd['billaddress']);
            $invemail = (trim($sd['invemail']) === "") ? "" : "<br>{$sd['invemail']}";
            $ponbr = $sd['ponbr'];
            $salesorder = (trim($sd['salesorder']) === "") ? "" : " / " . substr('000000' . $sd['salesorder'], -6);
            $tolab = $sd['tolab']; 
            $acceptedby = $sd['acceptedby'];
            $acceptedby .= (trim($sd['acceptedbyemail']) !== "") ? "<br>({$sd['acceptedbyemail']})" : "";
            $courier = ( trim($sd['courier']) === "") ? "" : trim($sd['courier']);
            $couriernbr = ( trim($sd['couriernbr']) === "") ? "" : trim($sd['couriernbr']);
            $cmt = $sd['comments']; 
            $setupon = $sd['setupon'];
            $setupby = $sd['setupby'];

            if (trim($sd['investname']) === "" ) {
              $iSQL = "SELECT concat(ifnull(invest_lname,''),', ', ifnull(invest_fname,'')) as iname FROM vandyinvest.invest where investid = :icode";
              $inv = $conn->prepare($iSQL);
              $inv->execute(array(':icode' => $icode));
              if ($inv->rowCount() > 0) { 
                $iinv = $inv->fetch(PDO::FETCH_ASSOC);
                $iname = "[{$iinv['iname']}]";
              } else { 
                $iname = "";
              }
            } else { 
              $iname = "[{$sd['investname']}]";
            }
            
            //TODO: Turn this into a webservice
            $dtlSQL = "SELECT ifnull(sg.qty,0) as qty, ifnull(sg.bgs,'') as bgs, ifnull(bs.pbiosample,'') as pbiosample, ucase(ifnull(bs.proctype,'')) as proctype, ucase(substr(ifnull(prpt.dspvalue,''),1,1)) as prptdsp, ifnull(hprind,0) as hprind, ifnull(qcind,0) as qcind, ifnull(bs.pxiage,'') as pxiage, ifnull(bs.pxirace,'') as pxirace, ifnull(bs.pxigender,'') as pxigender, ifnull(bs.anatomicsite,'') as site, ifnull(bs.subsite,'') as subsite, ifnull(bs.diagnosis,'') as dx, ifnull(bs.subdiagnos,'') as subdx, ifnull(bs.tisstype,'') as specimencategory, ifnull(sg.hourspost,0) as hrpst, ifnull(sg.prepmethod,'') as prepmet , ifnull(sg.preparation,'') as preparation, ifnull(sg.metric,0) as metric, ifnull(mt.longvalue,'') as metricuom, ifnull(cx.dspvalue,'') as chemo, ifnull(rx.dspvalue,'') as rad, date_format(procurementdate,'%Y-%m-%d') as procurementdate FROM masterrecord.ut_shipdocdetails sdd left join masterrecord.ut_procure_segment sg on sdd.segid = sg.segmentid left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') mt on sg.metricUOM = mt.menuvalue left join (SELECT  menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') cx on bs.chemoind = cx.menuvalue  left join (SELECT  menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') rx on bs.radind = rx.menuvalue left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') prpt on bs.pathReport = prpt.menuvalue where sdd.shipdocrefid = :sdnbr union SELECT '', srvcfeecode,'SPCSRVCFEE','','','','','','','', concat(ifnull(srvfeedsp,''),' (', qtymetric, ')'     ) , '' ,'','','','','','', concat('$',format(ifnull(totalfee,'0'),2)),'' ,'','','' FROM masterrecord.ut_shipdoc_spcsrvfee where shipdocrefid = :sdnbra and dspind = 1 order by 2 asc"; 
            //concat('$',format(ifnull(totalfee,'0'),2))
            $dtlR = $conn->prepare($dtlSQL); 
            $dtlR->execute(array(':sdnbr' => $sdid, ':sdnbra' => $sdid)); 

            $nLines = 0;
            $tQty = 0;
            $rower = 0;
            $mias = 0;
            $miaa = 0; 
            while ($dtl = $dtlR->fetch(PDO::FETCH_ASSOC)) { 
              $bd = "";
              $prp = "";
              $weightMet = "";
              $ars = "";
              $cxrx = "";


                $bd = (trim($dtl['site']) !== "") ? trim($dtl['site']) : "";
                if ( trim($dtl['subsite']) !== "") { 
                    if ( trim($bd) !== "" ) { 
                       $bd .= " [" . $dtl['subsite'] . "]";
                    } else { 
                        $bd .= $dtl['subsite'];
                    }
                }                
                if (trim($dtl['dx']) !== "") { 
                    if (trim($bd) !== "") { 
                       $bd .= " / " . $dtl['dx'];
                    } else { 
                        $bd .= $dtl['dx'];
                    }
                }
                if (trim($dtl['subdx']) !== "") { 
                    if (trim($bd) !== "") { 
                        $bd .= " [" . trim($dtl['subdx']) . "]";
                    } else { 
                        $bd .= trim($dtl['subdx']);
                    }
               }
                
                if (trim($dtl['specimencategory']) !== "") { 
                    if (trim($bd) !== "") { 
                        $bd .= " (" . trim($dtl['specimencategory']) . ")";
                    } else { 
                        $bd .= trim($dtl['specimencategory']);
                    }
                }   

     
              if ( trim($dtl['pbiosample']) !== 'SPCSRVCFEE' ) { 
                if (trim($dtl['prepmet']) !== "") { 
                    $prp = trim($dtl['prepmet']);
                }                
                
                if (trim($dtl['preparation']) !== "") { 
                    if (trim($prp) === "") { 
                        $prp = $dtl['preparation']; 
                    } else { 
                        $prp .= " / " . $dtl['preparation'];
                    }
                }
                
                $weightMet = trim( trim($dtl['metric']) . " " . trim($dtl['metricuom']));
                $ars = trim( trim($dtl['pxiage'])  . '/' . substr(trim($dtl['pxirace']), 0, 1) . '/' . substr(trim($dtl['pxigender']),0,1) );    
                $cxrx = substr(trim($dtl['chemo']),0,1) . "/" . substr(trim($dtl['rad']), 0,1);
              }


                if ($rower === 0) { 
                    $bgc = " background: rgba(240,240,240,1); "; 
                    $rower = 1;
                } else { 
                    $bgc = " background: rgba(255,255,255,1); "; 
                    $rower = 0;                    
                }
                
//                //CHECK FOR DIRECT SHIPMENT MIA
                if ( strtoupper($dtl['prptdsp']) === 'P' && (int)$dtl['hprind'] === 0 ) { 
//                   //TODO: MAKE THIS A WEBSERVICE 
                   $chkSQL = "SELECT * FROM masterrecord.ut_master_furtherlabactions where frommodule = :module and objpbiosample = :pbio and actioncode = :actioncode and objshipdoc = :objshipdoc and activeind = 1"; 
                   $chkRS = $conn->prepare($chkSQL);
                   $chkRS->execute(array(':module' => 'SHIPPING', ':pbio' => (int)$dtl['pbiosample'], ':actioncode' => 'MIA-S', ':objshipdoc' => $sdid));                 
                   if ( $chkRS->rowCount() === 0 ) {
                    //ADD MIA
                    //$insSQL = "INSERT INTO masterrecord.ut_master_furtherlabactions (frommodule, activeind,objshipdoc,objpbiosample,actioncode,actiondesc,actionrequestedby,actionrequestedon) VALUES ('SHIPPING', :activeind,:objshipdoc,:biosampleref,:miatype,'PRE-QMS DIRECT SHIPMENT',:rqstby,now())";
                    // $insRS = $conn->prepare($insSQL); 
                    // $insRS->execute(array(':objshipdoc' => $sdid, ':activeind' => 1,   ':biosampleref' => (int)$dtl['pbiosample'], ':rqstby' => $usr['originalaccountname'], ':miatype' => "MIA-{$dtl['proctype']}"));
                    //TODO:  DO NOT HARD CODE THESE VALUES - Assigned Agent Value
                    $insSQL = "INSERT INTO masterrecord.ut_master_furtherlabactions (frommodule, activeind,objshipdoc,objpbiosample,actioncode,actiondesc,actionrequestedby,actionrequestedon, assignedagent) VALUES ('SHIPPING', :activeind,:objshipdoc,:biosampleref,:miatype,'PRE-QMS DIRECT SHIPMENT',:rqstby,now(), 'Xavier')";
                    $insRS = $conn->prepare($insSQL); 
                    $insRS->execute(array(':objshipdoc' => $sdid, ':activeind' => 1,   ':biosampleref' => (int)$dtl['pbiosample'], ':rqstby' => $usr['originalaccountname'], ':miatype' => "MIA-{$dtl['proctype']}"));                     
                   }
                }
//                //CHECK MASTER FURTHER ACTIONS FOR MIA REFERENCE
                $miaSQL = "SELECT actioncode, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'') as actioncompletedon, ifnull(actioncompletedby,'') as actioncompletedby "
                                  . "FROM masterrecord.ut_master_furtherlabactions "
                                  . "where frommodule = :module and objpbiosample = :pbio and actioncode = :actioncode and objshipdoc = :objshipdoc and activeind = 1";
                $miaRS = $conn->prepare($miaSQL);
                $miaRS->execute(array(':module' => 'SHIPPING', ':pbio' => (int)$dtl['pbiosample'], ':actioncode' => 'MIA-' . $dtl['proctype'], ':objshipdoc' => $sdid));
                if ( $miaRS->rowCount() < 1) { 
                } else { 
                  $mia = $miaRS->fetch(PDO::FETCH_ASSOC); 
                  $footnoteind = "";
                  if ( trim($mia['actioncompleteon']) === "" ) { 
                      //DISPLAY MIA
                      $footnoteind = "*";
                      if ( $dtl['proctype'] === 'S' ) { $mias = 1; }
                      if ( $dtl['proctype'] === 'A' ) { $miaa = 1; }
                  }
                }

                 $innerTblLine .=  "<tr style=\"{$bgc} height: 20pt;\">"
                                             . "<td style=\"text-align: right; padding: 1px 3px 1px 1px; border: 1px solid rgba(203,203,203,1); border-left: none;border-top: none;  \">{$dtl['qty']}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;\">{$dtl['bgs']} {$footnoteind}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;\">{$bd}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;\">{$prp}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none; white-space: nonwrap; \">{$weightMet}</td>"                                             
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;\">{$ars}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none; border-right: none; text-align: right;\">{$cxrx}</td></tr>";
               $nLines += 1;

    
               $tQty += ( trim($dtl['pbiosample']) === 'SPCSRVCFEE' ) ? 0 : (int)$dtl['qty'];
            }

            $miaText = "";
            $miaText =  ( $miaa === 1 && $mias === 1) ? "<tr><td class=MIAText>* We strive to provide you with all required specimen data available at the time of tissue shipment, but there are some exceptions.  Same-day direct tissue shipments (Fresh) are provided with a provisional diagnosis only, as pathology reports are not immediately available.  Tissue samples that are not accessioned by surgical pathology will never produce a report (e.g., normal placenta, normal foreskin, etc.).  Autopsy reports may not be available for one to two months.  The missing reports will be emailed to you as soon as they become available. Please feel free to contact our coordinators at (215) 662-4570 if you have any questions.  Thank you.</td></tr>" : "";
            $miaText =  ( $miaa === 1 && $mias === 0 ) ? "<tr><td class=MIAText>* We strive to provide you with all required specimen data available at the time of tissue shipment, but there are some exceptions.  Same-day direct tissue shipments (Fresh) are provided with a provisional diagnosis only, as pathology reports are not immediately available.  Autopsy reports may not be available for one to two months.  The missing reports will be emailed to you as soon as they become available. Please feel free to contact our coordinators at (215) 662-4570 if you have any questions.  Thank you. </td></tr>" : "";
            $miaText =  ( $miaa === 0 && $mias === 1 ) ? "<tr><td class=MIAText> </td></tr>" : "";

    //<td valign=top align=right style="width: 3vw; padding: 10px 1px 0 2px; ">{$sidebcode}</td>    
    $docText = <<<RTNTHIS
<html>
<head>
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0;}
body { margin: 0; font-family: Roboto; font-size: 9pt; color: rgba(48,57,71,1); }
.MIAText { font-size: 7pt; padding: 25px 30px; text-align: justify;   }
</style>
</head>
<body>
<table border=0 cellspacing=0 cellpadding=0 width=100% style="border: 1px solid rgba(0,0,0,1);">
   <tr><td> <!--ROW 1 //-->
   <table border=0 cellspacing=0 cellpadding=0 width=100%>
            <tr><td valign=top rowspan=2 style="width: 10px;">{$qrcode}</td><td style="font-size: 20pt; font-weight: bold;">Shipping Document</td></tr>
            <tr><td valign=top style="font-size: 11pt; font-weight: bold;">Shipment Document Number: {$dspsd} </td></tr>
    </table>
                        
 </td></tr>
            
<tr><td> <!--ROW 2 //-->
 
<table border=0 cellspacing=0 cellpadding=0 width=100%>
    <tr><td valign=top style="width: 50%; font-size: 8pt; padding:  15pt 10pt 10pt 10pt; ">
        <b>Cooperative Human Tissue Network - Eastern</b><br>
            University of Pennsylvania Perelman School of Medicine<br>
            3400 Spruce Street, 565 Dulles Building <br>
            Philadelphia, Pennsylvania 19104-4283 USA<br>
            Telephone: (215) 662-4570</td>
            <td valign=top align=right style="padding:  15pt 2px 10pt 10pt;">
            
            <table  cellspacing=0 cellpadding=0 border=0>
                <tr><td style="font-size: 10pt; border: 1px solid rgba(0,0,0,1); border-right: none; width: 100px;padding: 4px; font-weight: bold;" align=right>STATUS:</td><td colspan=2 style="font-size: 10pt; border: 1px solid rgba(0,0,0,1); border-left: none; padding: 4px;">{$sts}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Date To Pull:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$tolab}</td>
                   <td style="width: 150px; font-size: 8pt; font-weight: bold; padding: 2px 4px 2px 4px; border: 1px solid rgba(0,0,0,1); border-bottom: none; border-top: none;">Comments</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Requested Ship Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$shpdte}</td>
                   <td rowspan=6 valign=top style="padding: 0 4px 1px 0; border: 1px solid rgba(0,0,0,1); border-top: none;font-size: 8pt; padding: 0 4px 0 4px;">{$cmt}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Actual Ship Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$actshpdte}</td>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Setup Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$setupon}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Setup By:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$setupby}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Status Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$stsdte}</td></tr>
             </table>
            
            </td>
            <td valign=top align=right style="width: 3vw; padding: 10px 1px 0 2px; ">{$sidebcodeZ}&nbsp;</td>    
            </tr>
            </table>
            
 </td></tr>

<tr><td> <!--ROW 3 //-->
 
<table border=0 cellspacing=3 cellpadding=0 width=100% style="font-size: 8pt;">
    <tr><td valign=top style="font-size: 8pt; padding: 0 6px 0 6px; width: 50%;border: 1px solid rgba(0,0,0,1);"><b>SHIP TO</b>:<br>{$shpadd}</td><td valign=top style="padding: 0 6px 0 6px;">{$billadd}</td></tr>
</table>            
                        
 </td></tr>

<tr><td> <!--ROW 4 //-->

    <table width=100% cellpadding=0 cellspacing=0 border=0  style="font-size: 7pt;">
    <tr><td style="height: 8px;" colspan=5></td></tr>
    <tr> 
        <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;" valign=top>Investigator</td>
        <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;" valign=top>Ship Courier</td>
        <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;" valign=top>Courier #/Tracking #</td>
        <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;" valign=top>Purchase Order / Sales Order #</td>
        <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; border-right: none;" valign=top>Accepted By</td>
    </tr>
    <tr>
      <td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);" valign=top>{$icode} {$iname}{$invemail}</td>
      <td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);" valign=top>{$courier}</td>
      <td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);" valign=top>{$couriernbr}<br>{$trcknbr}</td>
      <td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);" valign=top>{$ponbr} {$salesorder}</td>
      <td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);">{$acceptedby}</td>
    </tr>
    <tr><td style="height: 10px;" colspan=5></td></tr>
    </table>
    
    
 </td></tr>
            
<tr><td> <!--ROW 5 //-->

    <table width=100% cellpadding=0 cellspacing=0 border=0 style="font-size: 7pt;">
    <tr>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">QTY</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">CHTN-ED #</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">Biosample Designation</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">Preparation</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">Metric</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center;">A/R/S</td>
            <td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; text-align: center; border-right:none;">C/R</td>
    </tr>
    {$innerTblLine}
    </table>
    
 </td></tr>

{$miaText}


<tr><td style="background: rgba(0,0,0,1);"> <!--ROW 6 //-->

    <table style="font-size: 7pt; color: rgba(255,255,255,1);">       
        <tr><td>Total # biosamples: </td><td>{$tQty}</td></tr>
         <tr><td># of Lines: </td><td>{$nLines}</td></tr>
        </table>
    
 </td></tr>            
            
</table>
</body> 
</html>                     
RTNTHIS;
        
$filehandle = generateRandomString();                
$sdDocFile = genAppFiles . "/tmp/sdz{$filehandle}.html";
$sdDhandle = fopen($sdDocFile, 'w');
fwrite($sdDhandle, $docText);
fclose;
$sdPDF = genAppFiles . "/publicobj/documents/shipdoc/shipdoc{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED Shipment Doc {$dspsd} ({$usr['originalaccountname']})\"     {$sdDocFile} {$sdPDF}";
$output = shell_exec($linuxCmd);
        
        }
    }
    return array('status' => 200, 'text' => $docText, 'pathtodoc' => $sdPDF, 'format' => 'pdf');
}

function getInvItemTag ( $docid, $originalURI ) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .5in;  \" ");
    $tempDir = "{$at}/tmp/";
    require("{$at}/extlibs/bcodeLib/qrlib.php");
    //$presInst = $rqst['user'][0]['presentinstitution'];
    //  $r = "Run By: {$rqst['user'][0]['emailaddress']} at " . date('H:i');
    $tday = date('Y-m-d');
    require(serverkeys . "/sspdo.zck");  
    session_start();

       //****************CREATE BARCODE
        $codeContents = "{$docid}";
        $fileName = 'ittbc' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "", "png", true," style=\"height: 1in;\" ");
        //********************END BARCODE CREATION

        $detSQL = "SELECT replace(sg.bgs,'_','') as bgs, ifnull(sg.prepmethod,'') as prpmet, ifnull(sg.preparation,'') as prp, ifnull(bs.tissType,'') as speccat, ifnull(bs.anatomicSite,'') as primarysite, ifnull(bs.subSite,'') as primarysubsite, ifnull(bs.diagnosis,'') as diagnosis, ifnull(bs.subdiagnos,'') as diagnosismodifier, ifnull(bs.metsSite,'') as metsite, ifnull(sg.metric,'') as metric, ifnull(mt.longvalue,'') as metricuom, ifnull(bs.pxiage,'') as pxiage, ifnull(ag.longvalue,'') as pxiAgeUOM, ifnull(bs.pxirace,'') as pxirace, ifnull(bs.pxigender,'') as pxigender  FROM masterrecord.ut_procure_segment sg left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pbiosample left join (SELECT menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') as mt on sg.metricuom = mt.menuvalue LEFT JOIN (SELECT menu, menuValue, longvalue FROM four.sys_master_menus where menu = 'AGEUOM') as ag on bs.pxiageuom = ag.menuValue where replace(sg.bgs,'_','') = :labelnbr and sg.voidind <> 1";
        $detRS = $conn->prepare($detSQL); 
        $detRS->execute(array(':labelnbr' => $docid));
        if ( $detRS->rowCount() > 0 ) { 
            $records = $detRS->fetch(PDO::FETCH_ASSOC); 
        } else { 
            $records = array();
        }

    $lblTbl = <<<LBLLBL
<table border=0 cellpadding=0 cellspacing=0 style="width: 4in; height: 5.21in; border: 1px solid #000000; box-sizing: border-box;">
<tr><td style="padding: 0 0 0 4px;">{$favi}</td><td align=right valign=bottom> 

   <table>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 14pt; color: #000084; font-weight: bold; text-align: right;">CHTNEastern Biosample</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 10pt; color: #000084; font-weight: bold; text-align: right;">3400 Spruce Street, DULLES 565<br>Philadelphia, PA 19104</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">(215) 662-4570</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">https://www.chtneast.org</td>
     </tr></table>

</td></tr>
<tr><td colspan=2><center>{$qrcode}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Biosample Number</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$docid}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Diagnosis Designation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['speccat']} :: {$records['primarysite']} :: {$records['primarysubsite']} :: {$records['diagnosis']} :: {$records['diagnosismodifier']}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Preparation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['prpmet']} ({$records['prp']} / {$records['metric']} {$records['metricuom']})<br>{$records['pxiage']} {$records['pxiAgeUOM']} / {$records['pxirace']} / {$records['pxigender']}</td></tr>
<tr><td colspan=2 style="max-height: 3in;">&nbsp;</td></tr>
</table>
LBLLBL;
    
    $rowTbl .= "<td>{$lblTbl}</td>";

           $docText = <<<PRTEXT
             <html>
                <head>
                <style>
                   @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons);
                   html {margin: 0;}
                   body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
                </style>
                </head>
                <body>
<table border=0 style="width: 8in;"><tr>{$rowTbl}</tr></table>
PRTEXT;

$filehandle = generateRandomString();                
$prDocFile = genAppFiles . "/tmp/IIT{$filehandle}.html";
$prDhandle = fopen($prDocFile, 'w');
fwrite($prDhandle, $docText);
fclose;
$prPDF = genAppFiles . "/publicobj/documents/fatickets/iit{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED  {$crnbr}\"     {$prDocFile} {$prPDF}";
$output = shell_exec($linuxCmd);
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/fatickets/iit{$filehandle}.pdf", 'format' => 'pdf');    
}

function getAutoEmailRpt ( $docid, $originalURI ) { 
  require(serverkeys . "/sspdo.zck");      
  $rs = $conn->prepare( "SELECT textofemail FROM four.sys_auto_emails_history where selectorid = :rptid ");
  $rs->execute(array(':rptid' => $docid));
  if ( $rs->rowCount() <> 1 ) { 
    $docText = "ERROR:  NO AUTO REPORT FOUND WITH ID: " . $docid;
  } else { 
    $d = $rs->fetch(PDO::FETCH_ASSOC);  
    $docText = $d['textofemail'];  
  }
  $filehandle = generateRandomString();                
  $prDocFile = genAppFiles . "/tmp/AUTOE{$filehandle}.html";
  $prDhandle = fopen($prDocFile, 'w');
  fwrite($prDhandle, $docText);
  fclose;
  $prPDF = genAppFiles . "/publicobj/documents/autorpts/{$filehandle}.pdf";
  $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED\" {$prDocFile} {$prPDF}";
  $output = shell_exec($linuxCmd);
  return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/autorpts/{$filehandle}.pdf", 'format' => 'pdf');    
}

function getFATicket( $docid, $originalURI ) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .5in;  \" ");
    require(serverkeys . "/sspdo.zck");  
    session_start();

    $uripart = explode("/", $originalURI);
    $fatnbr = cryptservice($uripart[3],'d');

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
SELECT substr(concat('000000',idlabactions),-6) as ticketnbr
, actionstartedind
, ifnull(date_format(startedondate,'%m/%d/%Y'),'') as startedondate
, ifnull(startedby,'') as startedby
, ifnull(frommodule,'Unknown Module') frommodule
, ifnull(objshipdoc,'') as objshipdoc
, ifnull(objhprid,'') as objhprid
, ifnull(objpbiosample,'') as objpbiosample
, ifnull(objbgs,'') as objbgs
, ifnull(assignedagent,'') as assignedagent
, ifnull(actioncode,'UNKNOWN') as actioncode
, ifnull(actiondesc,'') as actiondesc
, ifnull(actionnote,'') as actionnote
, ifnull(notifyoncomplete,0) as notifyoncomplete
, ifnull(date_format(duedate,'%Y-%m-%d'),'') as duedateval
, ifnull(date_format(duedate,'%m/%d/%Y'),'') as duedate
, ifnull(actionrequestedby,'UNKNOWN') as actionrequestedby
, ifnull(date_format(actionrequestedon,'%m/%d/%Y'),'') as actionrequestedon
, ifnull(date_format(actioncompletedon,'%m/%d/%Y'),'') as actioncompleteon
, ifnull(actioncompletedby,'') as actioncompletedby
, faaction.assignablestatus as actiongridtype
, prioritymarkcode
, faprio.dspvalue as prioritydsp
, lastagent
, ifnull(agentlist.dspagent,'') as lastagentdsp
FROM masterrecord.ut_master_furtherlabactions fa
LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAACTIONLIST') faaction on fa.actioncode = faaction.menuvalue
LEFT JOIN (SELECT menuvalue, dspvalue, assignablestatus FROM four.sys_master_menus where menu = 'FAPRIORITYSCALE') faprio on fa.prioritymarkcode = faprio.menuvalue
left join (SELECT originalaccountname, concat(ifnull(friendlyName,''),' (', ifnull(dspjobtitle,''),')') as dspagent FROM four.sys_userbase where allowInvtry = 1 and primaryInstCode = 'HUP') as agentlist on fa.lastagent = agentlist.originalaccountname
where idlabactions = :ticket and activeind = 1
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
             <html>
                <head>
                <style>
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
        } else {  
            $docText = "TICKET {$docid} NOT FOUND";
        }

$filehandle = generateRandomString();                
$prDocFile = genAppFiles . "/tmp/FAT{$filehandle}.html";
$prDhandle = fopen($prDocFile, 'w');
fwrite($prDhandle, $docText);
fclose;
$prPDF = genAppFiles . "/publicobj/documents/fatickets/fat{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED  {$crnbr}\"     {$prDocFile} {$prPDF}";
$output = shell_exec($linuxCmd);
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/fatickets/fat{$filehandle}.pdf", 'format' => 'pdf');    
}

function getChartReview($chartreviewid, $originalURI) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
    require(serverkeys . "/sspdo.zck");  
    session_start();
    //TODO:  TURN THIS INTO A WEBSERVICE

    $sql = "SELECT distinct substr(concat('000000',cr.chartreviewid),-6) as crid, cr.crtxt, cr.bywhom, date_format(cr.onwhen,'%m/%d/%Y') as onwhen, trim(concat(ifnull(bs.pxiAge,''),' ',ifnull(auom.longvalue,''))) as phiage, ifnull(suom.longvalue,'') as phisex, ifnull(ruom.dspvalue,'') as phirace, ifnull(bs.assocID,'') as procedureid FROM masterrecord.cr_txt_v1 cr left join masterrecord.ut_procure_biosample bs on cr.associd = bs.assocID left join (SELECT  menuvalue, longvalue FROM four.sys_master_menus where menu = 'ageuom') auom on bs.pxiAgeUOM = auom.menuvalue left join (SELECT  menuvalue, longvalue FROM four.sys_master_menus where menu = 'pxsex') suom on bs.pxiGender = suom.menuvalue left join (SELECT  menuvalue, dspvalue FROM four.sys_master_menus where menu = 'pxrace') ruom on bs.pxiRace = ruom.menuvalue where cr.chartreviewid = :crid";
    $prR = $conn->prepare($sql);
    $prR->execute(array(':crid' => $chartreviewid));
    $sts = 404;
    if ($prR->rowCount() === 1) {
        $cr = $prR->fetch(PDO::FETCH_ASSOC);
        $crnbr = 'CR-' . substr(('000000'.$cr['crid']),-6);
        //$maincrtext = preg_replace('/\r\n/','<p>',preg_replace('/\n\n/','<p>',$cr['crtxt']));
        $maincrtext = preg_replace('/\n/','<br>',   preg_replace('/\r\n/','<p>', preg_replace('/\n\n/','<p>',$cr['crtxt'])))   ;
        //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = "{$tt}{$orginalURI}";
        $fileName = 'cr' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .7in;\"   ");
        //********************END BARCODE CREATION
        

        $bgListSQL = "select replace(read_Label,'_','') as biosamples from masterrecord.ut_procure_biosample where associd = :associd"; 
        $bgListRS = $conn->prepare($bgListSQL);





        $metTbl = <<<BSTBL
<table border=0 width=50%>
<tr><td class=demHead>Age</td><td class=demHead>Race</td><td class=demHead>Sex</td></tr>
<tr>
  <td>{$cr['phiage']}</td>
  <td>{$cr['phirace']}</td>
  <td>{$cr['phisex']}</td>
</tr>
</table>
BSTBL;
    
        
        $printDte = date('m/d/Y H:i');
        $docText = <<<PRTEXT
             <html>
                <head>
                <style>
                   @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
                   html {margin: 0;}
                   body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
                   .line {border-bottom: 1px solid rgba(0,0,0,1); height: 2pt; }
                   .docheader { background: #000; color: #fff; padding: 5px 3px; border: 1px solid #000; border-bottom: none;   }
                   .demHead { font-size: 10pt; font-style: italic; font-weight: bold; border-bottom: 2px solid #000; padding: 5px 2px;  }  
                   .tagger { font-size: 8pt; } 
                </style>
                </head>
                <body>
                <table border=0 width=100% cellpadding=0 cellspacing=0>
                <tr><td rowspan=2 valign=top style="width: 1in;">{$favi}</td><td style="font-size: 14pt; font-weight: bold; padding: 0 0 0 0; ">CHART REVIEW</td><td rowspan=2 align=right valign=top>{$qrcode}</td></tr>
                <tr><td valign=top style=" font-size: 8pt;"><b>CHTN Eastern Division</b><br>Unversity of Pennsylvania Perelman School of Medicine<br>3400 Spruce Street, Dulles 565, Philadelphia, Pennsylvania 19104 <br>(215) 662 4570 | https://www.chtneast.org | email: chtnmail@uphs.upenn.edu</td></tr>
                <tr><td colspan=3 class=line></td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan=3 class=docheader>Chart Review Findings</td></tr>
                <tr><td colspan=3 style="font-size: 10pt; text-align: justify; line-height: 1.4em; padding: 5px 5px; border: 1px solid #000; border-top: none;">{$maincrtext}</td></tr>
                
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan=3 class=docheader>Donor Demographic Information</td></tr>
                <tr><td colspan=3 style="font-size: 10pt; text-align: justify; line-height: 1.4em; padding: 5px 5px; border: 1px solid #000; border-top: none;">{$metTbl}</td></tr>                    
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan=3 class=line></td></tr> 
                <tr><td colspan=3 align=right style="font-size: 8pt; font-weight: bold; ">Print Date: {$printDte} / Procecure Id: {$cr['procedureid']}</td></tr>
                </table>
                </body> 
                </html>
PRTEXT;

$filehandle = generateRandomString();                
$prDocFile = genAppFiles . "/tmp/prz{$filehandle}.html";
$prDhandle = fopen($prDocFile, 'w');
fwrite($prDhandle, $docText);
fclose;
$prPDF = genAppFiles . "/publicobj/documents/pathrpts/pxchart{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED  {$crnbr}\"     {$prDocFile} {$prPDF}";
$output = shell_exec($linuxCmd);
                
    } else { 
        //Chart Review NOT FOUND
    }
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/pathrpts/pxchart{$filehandle}.pdf", 'format' => 'pdf');
}

function getSystemObject( $docid, $originalURI ) { 

    $thisobj = explode("::",$docid);
    require(serverkeys . "/sspdo.zck"); 

    switch ( $thisobj[0] ) {
      case "iloccard":
        $at = genAppFiles;
        $tt = treeTop;
        $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .5in;  \" ");
        $sql = "SELECT lvl3.typeolocation lvl3type, lvl3.locationdsp lvl3locdsp, lvl3.locationnote lvl3locnote, lvl2.typeolocation lvl2type, lvl2.locationdsp lvl2locdsp, lvl2.locationnote lvl2locnote, lvl1.typeolocation lvl1type, lvl1.locationdsp lvl1locdsp, lvl1.locationnote lvl1locnote, i.scancode, i.locationnote, i.locationdsp, i.typeolocation, i.hierarchyBottomInd FROM four.sys_inventoryLocations i left join ( SELECT locationid, typeOLocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1 ) lvl1 on i.parentid = lvl1.locationid left join ( SELECT locationid, typeOLocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1 ) lvl2 on lvl1.parentid = lvl2.locationid left join ( SELECT locationid, typeOLocation, locationdsp, locationnote, parentid FROM four.sys_inventoryLocations where activelocation = 1 ) lvl3 on lvl2.parentid = lvl3.locationid where i.scancode = :scancode and i.activelocation = 1 and i.physicalLocationInd = 1";
       $rs = $conn->prepare($sql);
       $rs->execute(array(':scancode' => $thisobj[1])); 
       if ( $rs->rowCount() === 1 ) { 
           //GOOD
           //****************CREATE BARCODE
           require ("{$at}/extlibs/bcodeLib/qrlib.php");
           $tempDir = "{$at}/tmp/";
           $codeContents = $thisobj[1];
           $fileName = 'obj' . generateRandomString() . '.png';
           $pngAbsoluteFilePath = $tempDir.$fileName;
           if (!file_exists($pngAbsoluteFilePath)) {
            QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
           } 
           $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .7in;\"   ");
           //********************END BARCODE CREATION
           $r = $rs->fetchall(PDO::FETCH_ASSOC);
           $dte = date('m/d/Y');
           //[{"lvl3type":"ROOM","lvl3locdsp":"Room 566","lvl3locnote":"Room566","lvl2type":"STORAGEFREEZER","lvl2locdsp":"PNS.165","lvl2locnote":"Panasonic-165","lvl1type":"SHELF","lvl1locdsp":"PNS.165:001","lvl1locnote":"Shelf#001","scancode":"FRZB380","locationnote":"Box #001(SC0019)","locationdsp":"SC-0019","typeolocation":"STORAGECONTAINER","hierarchyBottomInd":1}]
           $inner = <<<SCNCARDTBL
<table border=0 width=100%><tr><td id=typedenote><center>{$r[0]['typeolocation']}</td></tr></table>
<table border=0 width=100%><tr><td><center>{$qrcode}</td></tr><tr><td><center>{$r[0]['locationnote']} ({$r[0]['locationdsp']})</td></tr></table>
<table border=0 width=100%><tr><td align=right id=ipath>[ {$r[0]['lvl3locnote']} ({$r[0]['lvl3type']}) :: {$r[0]['lvl2locnote']} ({$r[0]['lvl2type']}) :: {$r[0]['lvl1locnote']} ({$r[0]['lvl1type']}) :: {$r[0]['locationnote']} ({$r[0]['typeolocation']}) ]</td></tr></table>
<table border=0 width=100%><tr><td align=right id=prntdte>Print Date: {$dte}</td></tr></table>
SCNCARDTBL;
       } else { 
          //BAD
          $inner = "SCAN CODE NOT FOUND ... SEE A CHTNEASTERN INFORMATICS STAFF MEMBER";
       }

       $docstr = <<<DOCLAYOUT
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons);
html {margin: 0;}
#loccardholder { border: 2px solid #000; }
#topblock { font-size: 11pt; }
#typedenote { background: #000; color: #fff; padding: 5px 0; font-size: 9pt; font-weight: bold; } 
.smlr { font-size: 8pt; }
#ipath { font-size: 7pt; }  
#prntdte { font-size: 6pt; font-weight: bold; }  
</style>
<body>
<table border=0 width=100%>
<tr>
<td width=60%>
    <div id=loccardholder>
    <table border=0 width=100%>
      <tr><td>{$favi}</td><td align=right id=topblock><b><span class=smlr>CHTN Eastern</span><br>Inventory Location Card</td></tr>
    </table>
    {$inner} 
   </div>
</td>
<td width=40%>&nbsp;</td></tr>
</table>
</body>
DOCLAYOUT;

        $docText = $docstr;
        break;
      case "ilocmap":
          $sql = "SELECT i.scancode, i.locationnote, i.locationdsp, lvl1.scancode lvl1scancode, lvl1.locationnote lvl1locationnote, lvl1.locationdsp lvl1locationdsp, lvl1.hierarchybottomind lvl1bottomind, lvl2.scancode lvl2scancode, lvl2.locationnote lvl2locationnote, lvl2.locationdsp lvl2locationdsp, lvl2.hierarchybottomind lvl2bottomind FROM four.sys_inventoryLocations i left join ( select * from four.sys_inventoryLocations where activelocation = 1 ) lvl1 on i.locationid = lvl1.parentid left join ( select * from four.sys_inventoryLocations where activelocation = 1 ) lvl2 on lvl1.locationid = lvl2.parentid where i.scancode = :locid and i.mastercontainerind = 1 and i.activelocation = 1";
        $rs = $conn->prepare($sql);  
        $rs->execute(array('locid' => $thisobj[1]));
        if ( $rs->rowCount() > 0 ) { 
          $r = $rs->fetchall(PDO::FETCH_ASSOC);
          $docText = $thisobj[1] . "<p>" . json_encode($r);
        } else { 
          $docText = "NO CONTAINER CABINET FOUND WITH SCANCODE >>> " . $thisobj[1];
        }
        break;
    }

$filehandle = generateRandomString();                
$prDocFile = genAppFiles . "/tmp/prz{$filehandle}.html";
$prDhandle = fopen($prDocFile, 'w');
fwrite($prDhandle, $docText);
fclose;
$prPDF = genAppFiles . "/publicobj/documents/pathrpts/pxchart{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 4  --margin-right 4  --margin-top 4  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED  {$crnbr}\"     {$prDocFile} {$prPDF}";
$output = shell_exec($linuxCmd);

    return array('status' => $sts, 'text' =>  'THIS IS A SYSTEM OBJECT', 'pathtodoc' =>  genAppFiles . "/publicobj/documents/pathrpts/pxchart{$filehandle}.pdf", 'format' => 'pdf');
}

function getInventoryManifestBarcodeRun ( $manifestNbr, $originalURI ) { 

    $at = genAppFiles;
    $tt = treeTop;
    require("{$at}/extlibs/bcodeLib/qrlib.php");
    $tempDir = "{$at}/tmp/";
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
    $errorInd = 0;
    require(serverkeys . "/sspdo.zck");  
    session_start();

    $who = session_id();
    $usrSQL = "SELECT emailaddress as usrid, username, originalaccountname as usr, presentinstitution  FROM four.sys_userbase where sessionid = :sessid and allowInd = 1 and allowInvtry = 1 and TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";
    $usrRS = $conn->prepare( $usrSQL ); 
    $usrRS->execute( array( ':sessid' => $who  ));

    ( $usrRS->rowCount() <> 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "USER ACCOUNT NOT FOUND") : "";

    if ( $errorInd === 0 ) { 

      $u = $usrRS->fetch(PDO::FETCH_ASSOC); 
      $m = (int)preg_replace('/[^0-9]/','',$manifestNbr); 
 
      $mSQL = "SELECT date_format(sentOn,'%m/%d/%Y') as sentondsp, inst.dspinstitution FROM masterrecord.ut_ship_manifest_head mhd left join (SELECT menuvalue, dspValue as dspinstitution FROM four.sys_master_menus where menu = 'INSTITUTION') inst on mhd.institutionCode = inst.menuvalue where manifestnbr = :m";
      $mRS = $conn->prepare( $mSQL );
      $mRS->execute(array(':m' => $m  ));

      ( $mRS->rowCount() <> 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "MANIFEST ({$manifestNbr}) NOT FOUND" ) : "";

      
      if ( $errorInd === 0 ) {

          $detSQL = "SELECT replace(sg.bgs,'_','') as bgs, concat(ifnull(sg.prepmethod,''), if(ifnull(sg.preparation,'')='','',concat(' [',ifnull(sg.preparation,''),']'))) as prep , concat(ifnull(sg.metric,''), ifnull(muom.dspshortmetric,'')) as metric, concat(ifnull(bs.pxiAge,0),'/', ifnull(bs.pxiRace,'U'),'/', ifnull(bs.pxiGender,'U')) as ars, concat(if(ifnull(bs.tissType,'')='','',ifnull(bs.tissType,'')) , if( concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'')='','',concat(' (',ifnull(bs.subSite,''),')'))) = '','',concat(' :: ',concat(ifnull(bs.anatomicSite,''), if(ifnull(bs.subSite,'')='','',concat(' (',ifnull(bs.subSite,''),')'))))), if( concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' (',ifnull(bs.subdiagnos,''),')')))='','',concat(' :: ',concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','',concat(' (',ifnull(bs.subdiagnos,''),')'))))) , if(ifnull(bs.metsSite,'')='','',concat(' :: ',ifnull(bs.metsSite,'')))) as desig FROM masterrecord.ut_ship_manifest_segment msg left join masterrecord.ut_ship_manifest_head mhd on msg.manifestnbr = mhd.manifestnbr left join masterrecord.ut_procure_segment sg on msg.segmentid = sg.segmentId left join (SELECT menu, menuvalue, dspValue as dspshortmetric FROM four.sys_master_menus where menu = 'metric') muom on sg.metricuom = muom.menuvalue left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample where msg.includeind = 1 and msg.manifestnbr = :m and sg.prepmethod <> 'SLIDE'";
          $detRS = $conn->prepare( $detSQL );
          $detRS->execute( array( ':m' => $m ));
          if ( $detRS->rowCount() > 0 ) { 
            $det = $detRS->fetchAll(PDO::FETCH_ASSOC);
            //[{"bgs":"89889T001","prep":"PB [FFPE]","metric":"0.10g","ars":"28\/BLACK\/F","desig":"DISEASE ::KIDNEY"},


            $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: 1in;  \" "); 
            $cellCntr = 0; 
            $cellCntrA = 0;
            $rowTbl = "<table><tr>";
            foreach ( $det as $v ) {
              if ($cellCntr === 2) { 
                $rowTbl .= "</tr></table><p><table><tr>";
                $cellCntr = 0;
              }
              if ( $cellCntrA === 4 ) { 
                $rowTbl .= "<div style = \"display:block; clear:both; page-break-after:always;\"></div>";
                $cellCntrA = 0;
              }

       //****************CREATE BARCODE
        $codeContents = "{$v['bgs']}";
        $fileName = 'bc' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "", "png", true," class=dspBarcode ");
        //********************END BARCODE CREATION

    $lblTbl = <<<LBLLBL
<table border=0 cellpadding=0 cellspacing=0 class=labelHoldTbl>
<tr><td style="padding: 4px 0 0 4px;">{$favi}</td><td align=right valign=bottom> 

   <table cellpadding=0 cellspacing=0>
      <tr><td class=CHTNTitle>CHTNEastern Biosample</td></tr>
      <tr><td class=CHTNAddress>3400 Spruce Street, DULLES 565<br>Philadelphia, PA 19104</td></tr>
      <tr><td class=CHTNAddress>(215) 662-4570</td></tr>
      <tr><td class=CHTNAddress>https://www.chtneast.org</td>
     </tr></table>

</td></tr>
<tr><td colspan=2><center>{$qrcode}</td></tr>
<tr><td colspan=2 class=labelDataLabel>Biosample Number</td></tr>
<tr><td colspan=2 class=CHTNNbr>{$v['bgs']}</td></tr>
<tr><td colspan=2 class=labelDataLabel>Diagnosis Designation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$v['desig']}</td></tr>
<tr><td colspan=2 class=labelDataLabel>Data Metrics</td></tr>
<tr><td colspan=2><center>
                  <table class=DMHold>
                    <tr><td class=DELbl>Preparation</td><td class=DELbl>Metric</td><td class=DELbl>Donor Age/Race/Sex</td></tr>
                    <tr><td class=DE>{$v['prep']}</td><td class=DE>{$v['metric']}</td><td>{$v['ars']}</td></tr>
                  </table></td></tr>
<tr><td colspan=2 style="max-height: 3in;">&nbsp;</td></tr>
</table>
LBLLBL;


              $rowTbl .= "<td>{$lblTbl}</td>";
              $cellCntr++;
              $cellCntrA++;
            }
//            $detTbl .= "<table border=1 style=\"width: 8in;\"><tr>{$rowTbl}</tr></table>"; 
            $detTbl .= $rowTbl;

          } else { 
             $detTbl = "<table><tr><td>NO SEGMENTS FOUND FOR THIS MANIFEST (NON-SLIDE SEGMENTS)</td></tr></table>";
          }

$docText = <<<PRTEXT
   <html><head>
   <style>
     @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
     html {margin: 0;}
     body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
     .line {border-bottom: 1px solid rgba(0,0,0,1); height: 2pt; }
  
     .dataLabel { font-size: 8pt; font-weight: bold; } 
     .smlDataDsp { font-size: 8pt; } 
     .pageTitle { font-size: 14pt; font-weight: bold; }

     .labelHoldTbl {  width: 3.8in; height: 4.5in;    border: 1px solid #000000; box-sizing: border-box;  }
     .CHTNTitle { font-family: Roboto; font-size: 16pt; color: #000084; font-weight: bold; text-align: right; padding: 0 .03in .05in 0;  } 
     .CHTNAddress { font-family: tahoma, verdana; font-size: 10pt; color: #000084; font-weight: bold; text-align: right; padding-right: .03in;  } 

     .labelDataLabel {  font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;  }
     .CHTNNbr { font-size: 16pt; text-align: center; font-weight: bold;  } 

     .DMHold { font-size: 9pt;  } 
     .DELbl { font-weight: bold; font-size: 8pt; border-bottom: 1px solid #d3d3d3; }
     .DE { font-size: 9pt; }  


     .dspBarcode { height: 1.2in; } 
   </style>
   </head>
   <body>
     <table border=0 width=100%> 
       <tr><td class=pageTitle>MANIFEST BARCODE RUN</td><td align=right><div><div class=dataLabel>Manifest #</div><div class=smlDataDsp>{$manifestNbr}</div></div></td></tr>
       <tr><td colspan=2 class=line></td></tr>
     </table>
       {$detTbl}
   </body>
   </html>
PRTEXT;
      } else { 
        $docText = json_encode( $msgArr );
      }

    } else { 
        $docText = json_encode( $msgArr );
    }

    $filehandle = generateRandomString();                
    $manDocFile = genAppFiles . "/tmp/mnbcr{$filehandle}.html";
    $manDhandle = fopen($manDocFile, 'w');
    fwrite($manDhandle, $docText);
    fclose;
    $manPDF = genAppFiles . "/publicobj/documents/manifestbcr{$filehandle}.pdf";
    $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED MANIFEST BARCODE\" {$manDocFile} {$manPDF}";
    $output = shell_exec($linuxCmd);
    
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/manifestbcr{$filehandle}.pdf", 'format' => 'pdf');
}

function getInventoryManifest ( $manifestNbr, $originalURI ) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in; \" ");
    $errorInd = 0;
    require(serverkeys . "/sspdo.zck");  
    session_start();

    $who = session_id();
    $usrSQL = "SELECT emailaddress as usrid, username, originalaccountname as usr, presentinstitution FROM four.sys_userbase where sessionid = :sessid and allowInd = 1 and allowProc = 1 and TIMESTAMPDIFF(DAY, now(), passwordExpireDate) > 0";
    $usrRS = $conn->prepare( $usrSQL ); 
    $usrRS->execute( array( ':sessid' => $who ));

    ( $usrRS->rowCount() <> 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "USER ACCOUNT NOT FOUND") : "";

    if ( $errorInd === 0 ) { 

      $u = $usrRS->fetch(PDO::FETCH_ASSOC);

      if ( substr($manifestNbr,0,5) !== 'DtaC:' ) {
        $manHeadSQL = "SELECT mhd.manifestnbr, concat(ifnull(prefix,''),'-', substr(concat('000000',ifnull(mhd.manifestnbr,'')),-6)) as manifestnbrdsp,  concat(ifnull(prefix,''),'-', substr(concat('000000', ifnull(mhd.parentmanifest,'')), -6)) as parentdsp, ifnull(mhd.parentmanifest,'') as parentmanifest , mstatus, date_format(manifestdate,'%m/%d/%Y %H:%i') as manifestdatedsp, createdBy, ifnull(mcnt.segOnMani,0) as segOnMani, institutionCode, inst.dspvalue, inst.address, ifnull(date_format(senton,'%m/%d/%Y'),'') as senddate, ifnull(sentby,'') as sentby  FROM masterrecord.ut_ship_manifest_head mhd left join (SELECT manifestnbr, count(1) segOnMani FROM masterrecord.ut_ship_manifest_segment where includeind = 1 group by manifestnbr) as mcnt on mhd.manifestnbr = mcnt.manifestnbr LEFT JOIN (SELECT menuvalue, dspvalue, ifnull(additionalInformation,'[NO ADDRESS LISTED]') as address FROM four.sys_master_menus where menu = 'INSTITUTION') inst on mhd.institutionCode = inst.menuvalue where mhd.institutioncode = :instcode and concat(ifnull(prefix,''),'-', substr(concat('000000',ifnull(mhd.manifestnbr,'')),-6)) = :mannbr";
        $manHeadRS = $conn->prepare( $manHeadSQL );
        $manHeadRS->execute( array( ':mannbr' => $manifestNbr, ':instcode' => $u['presentinstitution'] ));
      } else { 
        $manHeadSQL = "SELECT mhd.manifestnbr, concat(ifnull(prefix,''),'-', substr(concat('000000',ifnull(mhd.manifestnbr,'')),-6)) as manifestnbrdsp,  concat(ifnull(prefix,''),'-', substr(concat('000000', ifnull(mhd.parentmanifest,'')), -6)) as parentdsp, ifnull(mhd.parentmanifest,'') as parentmanifest , mstatus, date_format(manifestdate,'%m/%d/%Y %H:%i') as manifestdatedsp, createdBy, ifnull(mcnt.segOnMani,0) as segOnMani, institutionCode, inst.dspvalue, inst.address, ifnull(date_format(senton,'%m/%d/%Y'),'') as senddate, ifnull(sentby,'') as sentby  FROM masterrecord.ut_ship_manifest_head mhd left join (SELECT manifestnbr, count(1) segOnMani FROM masterrecord.ut_ship_manifest_segment where includeind = 1 group by manifestnbr) as mcnt on mhd.manifestnbr = mcnt.manifestnbr LEFT JOIN (SELECT menuvalue, dspvalue, ifnull(additionalInformation,'[NO ADDRESS LISTED]') as address FROM four.sys_master_menus where menu = 'INSTITUTION') inst on mhd.institutionCode = inst.menuvalue where concat(ifnull(prefix,''),'-', substr(concat('000000',ifnull(mhd.manifestnbr,'')),-6)) = :mannbr";
        $manHeadRS = $conn->prepare( $manHeadSQL );
        $manHeadRS->execute( array( ':mannbr' => substr( $manifestNbr,5) ));
      }

      ( $manHeadRS->rowCount() <> 1 ) ? list( $errorInd, $msgArr[] ) = array( 1, "MANIFEST ({$manifestNbr}) / " . substr($manifestNbr,0,4) . " NOT FOUND. EITHER DOESN'T EXIST OR USER NOT ALLOWED") : "";

      if ( $errorInd === 0 ) { 
        $manHead = $manHeadRS->fetch(PDO::FETCH_ASSOC);

        //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";

        $codeContents = "IMN-" . preg_replace('/DtaC:/','',$manifestNbr);
        $fileName = 'man' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .7in;\"   ");
        //********************END BARCODE CREATION

        $fromAdd = preg_replace('/\n/','<br>', $manHead['address']);
        $printDate = date('m/d/Y');
  
        $sendDate = ( trim($manHead['senddate']) === "" ) ? "<span class=bigOleError>ERROR: MANIFEST HAS NOT BEEN MARKED AS SENT</span>" : $manHead['senddate']; 
        $sendBy = ( trim($manHead['sentby']) === "" ) ? "" : " ({$manHead['sentby']})";

        $manint = (int)$manHead['manifestnbr'];
        $detSQL = "SELECT replace(sg.bgs,'_','') as bgs, concat(ifnull(sg.prepmethod,''), concat(' [',ifnull(sg.preparation,''),']')) as prep, if( ifnull(sg.metric,'') = '' ,'', concat( ifnull(sg.metric,''),' ', ifnull(uom.dspvalue,''))) as metric, trim(concat(ifnull(bs.anatomicSite,''),' ', trim(concat(ifnull(bs.diagnosis,''), if(ifnull(bs.subdiagnos,'')='','', concat(' (', ifnull(bs.subdiagnos,''),')')))), concat(' [',ifnull(bs.tissType,''),']'))) as shortdesig, concat(ifnull(bs.pxiAge,'UNK'),'/',substr(ifnull(bs.pxiRace,'UNK'),1,3),'/',ifnull(bs.pxiGender,'UNK')) as ars, date_format(msg.addedon,'%m/%d/%Y') as segaddedon, msg.addedby segaddedby, msg.scanned, date_format(msg.scannedon,'%m/%d/%Y') as scannedon FROM masterrecord.ut_ship_manifest_segment msg LEFT JOIN masterrecord.ut_procure_segment sg on msg.segmentid = sg.segmentid left join (SELECT menuvalue, dspValue FROM four.sys_master_menus where menu = 'metric') uom on sg.metricUOM = uom.menuvalue left join masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample where sg.manifestnbr = :manifestint and msg.includeind = 1 order by replace(sg.bgs,'_','')";
        $detRS = $conn->prepare( $detSQL ); 
        $detRS->execute(array(':manifestint' => $manint ));

        if ( $detRS->rowCOunt() > 0 ) { 
          $segTbl = "<table width=100% border=0 id=detailTbl><thead><tr><th><!--Line Nbr //--></th><th>CHTN #</th><th>Preparation</th><th>Metric</th><th>Short Designation</th><th>A/R/S</th><th>Added</th><th>Scanned To<br>Inventory</th></tr></thead><tbody>";

          $rcd = 0; 
          while ( $r = $detRS->fetch(PDO::FETCH_ASSOC) ) { 
            $rcd += 1; 
            $rcvdico = ( (int)$r['scanned'] === 1 ) ?  "<center>RCVD<br>{$r['scannedon']}" : "<center>&nbsp;";
            $segTbl .= "<tr>"
                 . "<td valign=top>{$rcd}</td>"
                 . "<td valign=top>{$r['bgs']}</td>"
                 . "<td valign=top>{$r['prep']}</td>"
                 . "<td valign=top style=\"white-space: nowrap;\">{$r['metric']}</td>"
                 . "<td valign=top>{$r['shortdesig']}</td>"
                 . "<td valign=top>{$r['ars']}</td>"
                 . "<td valign=top>{$r['segaddedon']}<br>({$r['segaddedby']})</td>"
                 . "<td valign=top>{$rcvdico}</td>"
                 . "</tr>";
          }
          $segTbl .= "</tbody></table>";

        } else { 
          $segTbl = "<table width=100%><tr><td><center><span class=bigOleError>NO SEGMENTS HAVE BEEN ADDED TO THIS MANIFEST</span></td></tr></table>";
        }
 
        $mNbr = (  substr($manifestNbr,0,5) !== 'DtaC:' ) ? $manifestNbr : substr($manifestNbr,5);
        $mp = ( $manHead['parentmanifest'] === '' ) ?  "" : " / {$manHead['parentdsp']} (parent)";
 
        
        $docText = <<<PRTEXT
             <html>
                <head>
                <style>
                   @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
                   html {margin: 0;}
                   body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
                   .line {border-bottom: 1px solid rgba(0,0,0,1); height: 2pt; }
                   .bigOleError { font-size: 10pt; color: rgba(204, 0, 0,1); font-weight: bold; }
                   #maniHeadTbl { font-size: 10pt; margin-top: 5px; }
                   #maniHeadTbl #headr { font-weight: bold; text-align: center; border: 1px solid rgba(0,0,0,1); padding: 10px 0; background: rgba(227, 227, 227, 1);  }
                   #verifWarn {  font-weight: bold; text-align: center; border: 1px solid rgba(0,0,0,1); padding: 10px 0; background: rgba(227, 227, 227, 1); font-size: 10pt; } 
                   #maniHeadTbl .datalabel { background: rgba(0,0,0,1); color: rgba(255,255,255,1); font-size: 8pt; font-weight: bold; padding: 5px 3px; width: 15px;   }  
                   #maniHeadTbl .datadsp { border-bottom: 1px solid rgba(0,0,0,1); padding: 0 4px;  }
                   #shipTbl { font-size: 10pt; }
                   #shipTbl .shipHead { background: rgba(0,0,0,1); color: rgba(255,255,255,1); font-size: 8pt; font-weight: bold; padding: 5px 3px;     }  
                   #shipTbl .shipcube { border: 1px solid rgba(0,0,0,1); line-height: 1.3em; padding: 4px;   }
                   #detailTbl { font-size: 9pt; border: 1px solid rgba(0,0,0,1); }
                   #detailTbl thead th { background: rgba(0,0,0,1); color: rgba(255,255,255,1); font-weight: bold; }
                   #detailTbl tbody tr td { border-bottom: 1px solid rgba(150,150,150,1); padding: 3px 2px; border-right: 1px solid rgba(150,150,150,1); } 
                   #detailTbl tbody tr:nth-child(even) {  background: rgba(227, 227, 227, .6); }
                </style>
                </head>
                <body>
                <table border=0 width=100%>
                <tr><td rowspan=2 valign=top style="width: 1in;">{$favi}</td><td style="font-size: 14pt; font-weight: bold; padding: 0 0 0 0; ">INVENTORY MANIFEST: {$mNbr} </td><td rowspan=2 align=right valign=top>{$qrcode}</td></tr>
                <tr><td valign=top style=" font-size: 8pt;"><b>CHTN Eastern Division</b><br>Unversity of Pennsylvania Perelman School of Medicine<br>3400 Spruce Street, Dulles 565, Philadelphia, Pennsylvania 19104 <br>(215) 662 4570 | https://www.chtneast.org | email: chtnmail@uphs.upenn.edu</td></tr>
                <tr><td colspan=3 class=line></td></tr>

                <tr><td colspan=3>  
                   <table border=0 width=100% id=maniHeadTbl>
                     <tr><td colspan=2 id=headr>CHTNEAST INTRA-SHIPPING MANIFEST</td></tr>
                     <tr><td class=datalabel>Manifest #: </td><td class=datadsp>{$manHead['manifestnbrdsp']}{$mp}</td></tr>
                     <tr><td class=datalabel>Status: </td><td class=datadsp>{$manHead['mstatus']}</td></tr>
                     <tr><td class=datalabel>Segment(s): </td><td class=datadsp>{$manHead['segOnMani']}</td></tr>
                     <tr><td class=datalabel>Institution: </td><td class=datadsp>{$manHead['dspvalue']} ({$manHead['institutionCode']})</td></tr>
                     <tr><td class=datalabel>Initiated: </td><td class=datadsp>{$manHead['manifestdatedsp']} ({$manHead['createdBy']})</td></tr>
                     <tr><td class=datalabel>Sent: </td><td class=datadsp>{$sendDate}{$sendBy}</td></tr>
                     <tr><td class=datalabel>Printed: </td><td class=datadsp>{$printDate} ({$u['username']})</td></tr>
                   </table>    
                </td></tr>

                <tr><td colspan=3>
                  <table width=100% border=0 id=shipTbl>
                    <tr><td width=50% class=shipHead>Ship From</td><td width=50% class=shipHead>Ship To</td></tr>
                    <tr><td valign=top class=shipcube>{$fromAdd}</td><td valign=top class=shipcube><b>CHTN Eastern Division</b><br>Unversity of Pennsylvania Perelman School of Medicine<br>3400 Spruce Street, Dulles 565<br>Philadelphia, Pennsylvania 19104</td></tr>

                  </table>
                </td></tr>

                <tr><td colspan=3 class=line></td></tr>
 
                <tr><td colspan=3 id=verifWarn>Please ensure that all CHTN segment numbers listed below are contained in the package being sent to the CHTN Eastern distribution facility. Thank you! </td></tr> 
          
                <tr><td colspan=3>
  
                  {$segTbl}

                </td></tr>
                </table>
                </body>
                </html>
PRTEXT;

      } else { 
        $docText = json_encode( $msgArr );
      }
    } else {
      $docText = json_encode( $msgArr );
    }
    $filehandle = generateRandomString();                
    $manDocFile = genAppFiles . "/tmp/prz{$filehandle}.html";
    $manDhandle = fopen($manDocFile, 'w');
    fwrite($manDhandle, $docText);
    fclose;
    $manPDF = genAppFiles . "/publicobj/documents/manifest{$filehandle}.pdf";
    $linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED INVENTORY MANIFEST\"     {$manDocFile} {$manPDF}";
    $output = shell_exec($linuxCmd);
    
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/manifest{$filehandle}.pdf", 'format' => 'pdf');
}

function getPathReportText($pathrptid, $orginalURI) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
    require(serverkeys . "/sspdo.zck");  
    session_start();

    //TODO:  TURN THIS INTO A WEBSERVICE
    $sql = "select pathreport, pxiid, dnpr_nbr as bgnbr from masterrecord.qcpathreports pr where prid = :prid";
    $prR = $conn->prepare($sql);
    $prR->execute(array(':prid' => $pathrptid));
    $sts = 404;
    if ($prR->rowCount() === 1) { 
        //FOUND
        
        $pr = $prR->fetch(PDO::FETCH_ASSOC);
        $bg = $pr['bgnbr'];
        $maindocText = $pr['pathreport'];           
        //****************CREATE BARCODE
        require ("{$at}/extlibs/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = "{$tt}{$orginalURI}";
        $fileName = 'pr' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .7in;\"   ");
        //********************END BARCODE CREATION
        
        $metSQL = <<<SQLSTMT
select bs.pxiage, bs.pxirace, bs.pxigender, sx.dspvalue as donorsex, pt.proctypedsp, cx.cxindicator, rx.rxindicator 
, bs.anatomicsite, bs.tisstype, bs.subsite, bs.siteposition, bs.diagnosis, bs.subdiagnos, bs.metssite 
from masterrecord.ut_procure_biosample bs 
left join (select menuvalue, dspvalue from four.sys_master_menus where menu= 'PXSEX') sx on bs.pxigender = sx.menuvalue
left join (SELECT menuvalue, dspvalue as proctypedsp FROM four.sys_master_menus where menu = 'proctype') pt on bs.proctype = pt.menuvalue
left join (SELECT menuvalue, dspvalue as cxindicator FROM four.sys_master_menus where menu = 'CX') cx on bs.chemoind = cx.menuvalue  
left join (SELECT menuvalue, dspvalue as rxindicator FROM four.sys_master_menus where menu = 'RX') rx on bs.chemoind = rx.menuvalue  
where read_label like :bgnbr
SQLSTMT;
        $metR = $conn->prepare($metSQL); 
        $metR->execute(array(':bgnbr' => "{$bg}%"));
        if ($metR->rowCount() > 0 ) { 
            $met = $metR->fetch(PDO::FETCH_ASSOC); 

            $dxdgnos = strtoupper(trim($met['anatomicsite']));
            $dxdgnos .= (trim($met['subsite']) !== "") ? "-" . strtoupper(trim($met['subsite'])) : "";
            $dxdgnos .= (trim($met['siteposition']) !== "") ? " (" . strtoupper(trim($met['siteposition'])) : "";
            $dxlim = strtoupper(trim($met['diagnosis'])); 
            $dxlim .= (trim($met['subdiagnos']) !== "") ? "-" . strtoupper(trim($met['subdiagnos'])) : "";
            $dxdgnos .= (trim($dxlim) !== "") ? " / " . $dxlim : "";
            $dxdgnos .= (trim($met['tisstype']) !== "") ? " [" . strtoupper($met['tisstype']) . "]" : "";
            $dxdgnos .= (trim($met['metsite']) !== "") ? " / MET: " . strtoupper(trim($met['metssite'])) : "";

            $metTbl = <<<BSTBL
<table>
<tr><td colspan=6 style="font-size: 12pt; font-weight: bold; padding: 10pt 0 10pt 0; ">Biosample Donor/Collection</td></tr>
<tr>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Donor Age</td>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Donor Sex</td>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Donor Race</td>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Procedure</td>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Chemotherapy</td>
    <td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  ">Radiation</td></tr>
<tr>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['pxiage']}</td>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['donorsex']}</td>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['pxirace']}</td>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['proctypedsp']}</td>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['cxindicator']}</td>
    <td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); ">{$met['rxindicator']}</td></tr>
<tr><td style="height: 5pt;"></td></tr>
<!--  SECTION TAKEN OUT AS PER DEE'S REQUEST ON 20190130 
<tr><td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 0 0 2;  " colspan=6>Collected Sample Designation</td></tr>
<tr><td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); " colspan=6>{$dxdgnos}</td></tr>
//-->
</table>
BSTBL;
        }

        //TODO:  GET MOLECULAR TESTS


        //GET HPR PERCENTAGES 
        //TODO: TURN THIS INTO A WEBSERVICE
        $hprSQL = "select ifnull(prctype,'') prctype, ifnull(prcvalue,'') prcvalue from  (SELECT hpr.biohpr FROM masterrecord.ut_hpr_biosample hpr where bgs like :bgNbr  order by reviewedon desc limit 1) hpr left join masterrecord.ut_hpr_percentages prc on hpr.biohpr = prc.biohpr"; 
    $hprR = $conn->prepare($hprSQL);
    $hprR->execute(array(':bgNbr' => "{$bg}%"));
    
    $hprInner = "";
    while ($hpr = $hprR->fetch(PDO::FETCH_ASSOC)) { 
        if (trim($hpr['prctype']) !== "") { 
          $prcDsp = (trim(hpr['prcvalue']) === "" ) ? "-" : "{$hpr['prcvalue']}%";   
          $hprInner .= "<tr><td style=\"font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); \">{$hpr['prctype']}</td><td style=\"font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); \">{$prcDsp}</td></tr>";   
        }
    }
    
    if (trim($hprInner) !== "" ) { 
        $hprTbl = "<table><tr><td colspan=3 style=\"font-size: 12pt; font-weight: bold; padding: 10pt 0 10pt 0; \">Quality Control Assessment</td></tr>"
                       . "<tr><td style=\"font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  \">Metric Type</td>"
                       . "<td style=\"font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 2pt 0 2pt;  \">Percentage</td></tr>{$hprInner}</table>";
    } else { 
        $hprTbl = "";        
    }
        $printDte = date('m/d/Y H:i');
        //#mastericon {height: 4vh; }
        $docText = <<<PRTEXT
             <html>
                <head>
                <style>
                   @import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
                   html {margin: 0;}
                   body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
                   .line {border-bottom: 1px solid rgba(0,0,0,1); height: 2pt; }
                </style>
                </head>
                <body>
                <table border=0 width=100%>
                <tr><td rowspan=2 valign=top style="width: 1in;">{$favi}</td><td style="font-size: 14pt; font-weight: bold; padding: 0 0 0 0; ">Pathology Report for Biogroup {$bg} </td><td rowspan=2 align=right valign=top>{$qrcode}</td></tr>
                <tr><td valign=top style=" font-size: 8pt;"><b>CHTN Eastern Division</b><br>Unversity of Pennsylvania Perelman School of Medicine<br>3400 Spruce Street, Dulles 565, Philadelphia, Pennsylvania 19104 <br>(215) 662 4570 | https://www.chtneast.org | email: chtnmail@uphs.upenn.edu</td></tr>
                <tr><td colspan=3 class=line></td></tr>
                <tr><td colspan=3 style="font-size: 10pt; text-align: justify; line-height: 1.4em;">{$maindocText}</td></tr>
                <tr><td colspan=3 class=line></td></tr> 
                <tr><td colspan=3 style="font-size: 14pt; font-weight: bold; ">CHTN Eastern Collection &amp; Review Metrics</td></tr> 
                <tr><td colspan=3>{$metTbl}</td></tr>    
                <tr><td colspan=3>{$hprTbl}</td></tr>
                <tr><td colspan=3 class=line></td></tr> 
                <tr><td colspan=3 align=right style="font-size: 8pt; font-weight: bold; ">Print Date: {$printDte}</td></tr>
                </table>
                </body> 
                </html>
PRTEXT;

$filehandle = generateRandomString();                
$prDocFile = genAppFiles . "/tmp/prz{$filehandle}.html";
$prDhandle = fopen($prDocFile, 'w');
fwrite($prDhandle, $docText);
fclose;
$prPDF = genAppFiles . "/publicobj/documents/pathrpts/pathologyrpt{$filehandle}.pdf";
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED Biosample {$bg}\"     {$prDocFile} {$prPDF}";
$output = shell_exec($linuxCmd);

        
    } else { 
        //PATH REPORT NOT FOUND
        
    }
    return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/pathrpts/pathologyrpt{$filehandle}.pdf", 'format' => 'pdf');
    
}

function prntbarcode( $filepath="", $text="0", $size="20", $orientation="horizontal", $code_type="code128", $print=false, $SizeFactor=1 ) {
    //https://github.com/davidscotttufts/php-barcode/blob/master/barcode.php
	$code_string = "";
	// Translate the $text into barcode the correct $code_type
	if ( in_array(strtolower($code_type), array("code128", "code128b")) ) {
		$chksum = 104;
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
		$code_keys = array_keys($code_array);
		$code_values = array_flip($code_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$code_string .= $code_array[$activeKey];
			$chksum=($chksum + ($code_values[$activeKey] * $X));
		}
		$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
		$code_string = "211214" . $code_string . "2331112";
	} elseif ( strtolower($code_type) == "code128a" ) {
		$chksum = 103;
		$text = strtoupper($text); // Code 128A doesn't support lower case
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","NUL"=>"111422","SOH"=>"121124","STX"=>"121421","ETX"=>"141122","EOT"=>"141221","ENQ"=>"112214","ACK"=>"112412","BEL"=>"122114","BS"=>"122411","HT"=>"142112","LF"=>"142211","VT"=>"241211","FF"=>"221114","CR"=>"413111","SO"=>"241112","SI"=>"134111","DLE"=>"111242","DC1"=>"121142","DC2"=>"121241","DC3"=>"114212","DC4"=>"124112","NAK"=>"124211","SYN"=>"411212","ETB"=>"421112","CAN"=>"421211","EM"=>"212141","SUB"=>"214121","ESC"=>"412121","FS"=>"111143","GS"=>"111341","RS"=>"131141","US"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","CODE B"=>"114131","FNC 4"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
		$code_keys = array_keys($code_array);
		$code_values = array_flip($code_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$code_string .= $code_array[$activeKey];
			$chksum=($chksum + ($code_values[$activeKey] * $X));
		}
		$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];
		$code_string = "211412" . $code_string . "2331112";
	} elseif ( strtolower($code_type) == "code39" ) {
		$code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");
		// Convert to uppercase
		$upper_text = strtoupper($text);
		for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
			$code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
		}
		$code_string = "1211212111" . $code_string . "121121211";
	} elseif ( strtolower($code_type) == "code25" ) {
		$code_array1 = array("1","2","3","4","5","6","7","8","9","0");
		$code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			for ( $Y = 0; $Y < count($code_array1); $Y++ ) {
				if ( substr($text, ($X-1), 1) == $code_array1[$Y] )
					$temp[$X] = $code_array2[$Y];
			}
		}
		for ( $X=1; $X<=strlen($text); $X+=2 ) {
			if ( isset($temp[$X]) && isset($temp[($X + 1)]) ) {
				$temp1 = explode( "-", $temp[$X] );
				$temp2 = explode( "-", $temp[($X + 1)] );
				for ( $Y = 0; $Y < count($temp1); $Y++ )
					$code_string .= $temp1[$Y] . $temp2[$Y];
			}
		}
		$code_string = "1111" . $code_string . "311";
	} elseif ( strtolower($code_type) == "codabar" ) {
		$code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
		$code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");
		// Convert to uppercase
		$upper_text = strtoupper($text);
		for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
			for ( $Y = 0; $Y<count($code_array1); $Y++ ) {
				if ( substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
					$code_string .= $code_array2[$Y] . "1";
			}
		}
		$code_string = "11221211" . $code_string . "1122121";
	}
	// Pad the edges of the barcode
	$code_length = 20;
	if ($print) {
		$text_height = 30;
	} else {
		$text_height = 0;
	}

	for ( $i=1; $i <= strlen($code_string); $i++ ){
		$code_length = $code_length + (integer)(substr($code_string,($i-1),1));
        }
	if ( strtolower($orientation) == "horizontal" ) {
		$img_width = $code_length*$SizeFactor;
		$img_height = $size;
	} else {
		$img_width = $size;
		$img_height = $code_length*$SizeFactor;
	}
	$image = imagecreate($img_width, $img_height + $text_height);
	$black = imagecolorallocate ($image, 0, 0, 0);
	$white = imagecolorallocate ($image, 255, 255, 255);
	imagefill( $image, 0, 0, $white );
	if ( $print ) {
		imagestring($image, 5, 31, $img_height, $text, $black );
	}
	$location = 10;
	for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
		$cur_size = $location + ( substr($code_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" )
			imagefilledrectangle( $image, $location*$SizeFactor, 0, $cur_size*$SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black) );
		else
			imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
	}

	// Draw barcode to the screen or save in a file
	if ( $filepath=="" ) {
		header ('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
	} else {
		imagepng($image,$filepath);
		imagedestroy($image);
	}
}

class whtmltopdfcommands { 

    function masterrecordprocurementlisting() { 
      $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"Masterrecord Procurement Listing\" ";
      return $linuxcmd;
    }

    function barcoderun() { 
        $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"Daily Barcode Run\" ";
        return $linuxcmd;
    }

    function dailypristinebarcoderun() { 
        $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"Daily Barcode Run (Pristine)\" ";
        return $linuxcmd;
    }
   
    function dailypristineprocurementsheet($rptdef) {
        $rqst = json_decode($rptdef['DATA']['requestjson'], true); 
        $inst = $rqst['user'][0]['presentinstitution'];
        $tdaydsp = date('m/d/Y');
        $linuxcmd = " --page-size Letter --orientation Landscape  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"- For {$inst} on {$tdaydsp} -\" --footer-left \"Daily Pristine Procurement Sheet\" ";
        return $linuxcmd;
    }

    function histologysheet() { 
        $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 4  --margin-right 4  --margin-top 4 ";
        return $linuxcmd;
    }

    function histologyembed() { 
        $linuxcmd = " --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8 ";
        return $linuxcmd;
    }




}

class reportprintables {

    function masterrecordprocurementlisting($rptdef) { 
      $at = genAppFiles;
      $tt = treeTop;
      $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
      $pdta = json_encode($rptdef);
      $tbldta = json_decode(callrestapi("POST", dataTree . "/data-doers/grab-report-data",serverIdent, serverpw, $pdta), true);           

      $introTbl = <<<HEADERTBL
<table border=0 cellpadding=0 cellspacing=0 width=100%> 
<tr><td style="font-family: Tahoma, verdana; font-size: 12pt; text-align: center; color: #000084; border-bottom: 2px solid #000084;">Daily Procurement Log Sheet (master-record)</td></tr>
<tr><td style="font-family: tahoma, verdana; font-size: 10pt; color: #000000; text-align: justify; line-height: 1.5em; padding: 8px 4px 5px 4px;">This is the daily procurement log sheet for the data transferred to the primary database of the CHTN-Eastern (master-record).  This is NOT the pristine procurement record (for that data, print the daily procurement sheet marked 'Pristine'.  This report shows all procurement across all CHTNED primary and remote sites.  </td></tr>
</table>
HEADERTBL;

      $rowTbl = "<table border=0 style=\"font-family: tahoma, verdana; font-size: 7pt; border-collapse: collapse; width: 100%; border: 1px solid #000;\"><thead><tr><th style=\"background: #000; color: #fff; padding: 4px;\">Biosample</th><th style=\"background: #000; color: #fff; padding: 4px;\">Specimen Category</th><th style=\"background: #000; color: #fff; padding: 4px;\">Site</th><th style=\"background: #000; color: #fff; padding: 4px;\">Diagnosis</th><th style=\"background: #000; color: #fff; padding: 4px;\">METS</th><th style=\"background: #000; color: #fff; padding: 4px;\">PHI</th></tr></thead><tr><td colspan=6></td></tr><tr><td colspan=6>";
      $bioline = "";
      $collectorTbl = "<table border=0 style=\"font-family: tahoma, verdana; font-size: 7pt; border-collapse: collapse; width: 100%;\">";
      foreach($tbldta['DATA'] as $records) {  
          if ($records['Biosample_Nbr'] !== $bioline) { 
              //close old entry - Start New entry
              $collectorTbl .= "</table>";     
              $topline = "border-top: 1px solid #b5b5b5; padding: 5px 3px 0 3px;";
              $rowTbl .= "{$collectorTbl}</td></tr><tr><td style=\"{$topline}\">{$records['Biosample_Nbr']}</td><td style=\"{$topline}\">{$records['Specimen_Category']}</td><td style=\"{$topline}\">{$records['Site_Designation']}</td><td style=\"{$topline}\">{$records['DX_Designation']}</td><td style=\"{$topline}\">{$records['Mets Designation']}</td><td>{$records['PHI_Age']} / {$records['PHI_Race']} / {$records['PHI_Sex']}</td>      </tr><tr><td colspan=6>";
              //RESETS
              $bioline = $records['Biosample_Nbr']; 
              $segTopper = "background: #b5b5b5; padding: 2px;font-size: 6pt; ";
              $collectorTbl = "<table border=0 style=\"font-family: tahoma, verdana; font-size: 7pt; border-collapse: collapse; width: 100%;\"><thead><tr><th></th><th style=\"{$segTopper}\">Label</th><th style=\"{$segTopper}\">Preparation</th><th style=\"{$segTopper}\">Preparation Modifier</th><th style=\"{$segTopper}\">Metric</th><th style=\"{$segTopper}\">HP</th><th style=\"{$segTopper}\">Slide/Block</th><th style=\"{$segTopper}\">HPR Indicator</th><th style=\"{$segTopper}\">QTY</th><th style=\"{$segTopper}\">Technician/Institution</th></tr></thead><tr><td>&nbsp;</td><td>{$records['Sample_Label']}</td><td>{$records['Preparation']}</td><td>{$records['Preparation_Modifier']}</td><td>{$records['Metric']}</td><td>{$records['Hours_Post_Excision']}</td><td>{$records['Slide_From_Block']}</td><td>{$records['HPR_Block_Indicator']}</td><td>{$records['Segment_Quantity']}</td><td>{$records['Procuring_Technician']}@{$records['Institution_Procured_At']}</td></tr>";
          } else { 
            //ADD TO ENTRY
              $collectorTbl .= "<tr><td>&nbsp;</td><td>{$records['Sample_Label']}</td><td>{$records['Preparation']}</td><td>{$records['Preparation_Modifier']}</td><td>{$records['Metric']}</td><td>{$records['Hours_Post_Excision']}</td><td>{$records['Slide_From_Block']}</td><td>{$records['HPR_Block_Indicator']}</td><td>{$records['Segment_Quantity']}</td><td>{$records['Procuring_Technician']}@{$records['Institution_Procured_At']}</td></tr>";
          }
      }
      $collectorTbl .= "</table>";     
      $rowTbl .= "{$collectorTbl}</td></tr>";
      $rowTbl .= "</table>";

      $resultTbl .= "<table border=0 style=\"width: 8in;\"><tr>{$introTbl} {$rowTbl}</tr></table>"; 
      return $resultTbl;    
    }

    function barcoderun($rptdef) {     
      $at = genAppFiles;
      $tt = treeTop;
      require("{$at}/extlibs/bcodeLib/qrlib.php");
      $tempDir = "{$at}/tmp/";
      $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
      $pdta = json_encode($rptdef);
      $tbldta = json_decode(callrestapi("POST", dataTree . "/data-doers/grab-report-data",serverIdent, serverpw, $pdta), true);           

  $cellCntr = 0;
  foreach ($tbldta['DATA'] as $records) {
    if ($cellCntr === 2) { 
        $rowTbl .= "</tr><tr>";
        $cellCntr = 0;
    }
        //****************CREATE BARCODE
        $codeContents = "{$records['Biosample_Label']}";
        $fileName = 'bc' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "", "png", true," style=\"height: 1in;\" ");
        //********************END BARCODE CREATION

    $lblTbl = <<<LBLLBL
<table border=0 cellpadding=0 cellspacing=0 style="width: 4in; height: 5.21in; border: 1px solid #000000; box-sizing: border-box;">
<tr><td style="padding: 0 0 0 4px;">{$favi}</td><td align=right valign=bottom> 

   <table>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 14pt; color: #000084; font-weight: bold; text-align: right;">CHTNEastern Biosample</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 10pt; color: #000084; font-weight: bold; text-align: right;">3400 Spruce Street, DULLES 565<br>Philadelphia, PA 19104</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">(215) 662-4570</td></tr>
      <tr>
        <td style="font-family: tahoma, verdana; font-size: 9pt; color: #000084; font-weight: bold; text-align: right;">https://www.chtneast.org</td>
     </tr></table>

</td></tr>
<tr><td colspan=2><center>{$qrcode}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Biosample Number</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['Biosample_Label']}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Diagnosis Designation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['Diagnosis_Designation']}</td></tr>
<tr><td colspan=2 style="font-size: 9pt; border-bottom: 1px solid #d3d3d3; font-weight: bold; padding: 4px 0 0 4px;">Preparation</td></tr>
<tr><td colspan=2 style="font-size: 11pt; text-align: center;">{$records['Preparation']}</td></tr>
<tr><td colspan=2 style="max-height: 3in;">&nbsp;</td></tr>
</table>
LBLLBL;
    $rowTbl .= "<td>{$lblTbl}</td>";
    $cellCntr++;
  }  
      $resultTbl .= "<table border=0 style=\"width: 8in;\"><tr>{$rowTbl}</tr></table>"; 
      return $resultTbl;    
      //return json_encode($tbldta);
  }


}


