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

    private $registeredPages = array('pathrpt','shipdoc'); 
    //pxchart = Patient Chart
    
    function __construct() { 		  
      $args = func_get_args();   
       if (trim($args[0]) === "") {	  		
       } else {
           $rq = explode("/", $args[0]);
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
             $this->bodycontent = "<h1>OBJECT TYPE NOT FOUND IN SCIENCESERVER</h1>";             
         }         
       }
    }
    
    function getPrintObject($rqArr, $originalURI) {
        $elArr = array();
        $docType = $rqArr[2];
        $docId = $rqArr[3];        
        $conDoc = new documentconstructor();
    
        $elArr['object'] =  (method_exists($conDoc,'unencryptedDocID') ? $conDoc->unencryptedDocID($docType, $docId) : "");
        $elArr['pagetitle'] = (method_exists($conDoc,'pagetabs') ? $conDoc->pagetabs($elArr['object']) : "");        
        $elArr['headr'] = (method_exists($conDoc,'generateheader') ? $conDoc->generateheader() : ""); 
        $elArr['tabicon'] =   (method_exists($conDoc,'faviconBldr') ? $conDoc->faviconBldr($elArr['object']) : "");
        $elArr['style'] = (method_exists($conDoc,'globalstyles') ? $conDoc->globalstyles() : "");        
        
        
        $bdy = (method_exists($conDoc,'documenttext') ? $conDoc->documenttext($elArr['object'], $originalURI) : "");
        
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
    $unencry = simplecrypt( $encryptedDocId , 'd');
    $dt = "";
    $docid = "";
    $bgnbr = "";
    $donor = "";
   
    switch ($docType) { 
        case 'pathrpt':
            $dt = "PATHOLOGY REPORT";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            require_once(genAppFiles . "/dataconn/sspdo.zck");  
            $prSQL = "select dnpr_nbr bgnbr from masterrecord.qcpathreports where prid = :prid"; 
            $prR = $conn->prepare($prSQL);
            $prR->execute(array(':prid' => $docid));
            $pr = $prR->fetch(PDO::FETCH_ASSOC);
            $bgnbr = $pr['bgnbr'];
            break;
        case 'pxchart':
            
            ///PATIENT CHART
            break;
        case 'shipdoc':
            $dt = "SHIPMENT DOCUMENT";
            $docIdElem = explode("-", $unencry);
            $docid = $docIdElem[0];
            require_once(genAppFiles . "/dataconn/sspdo.zck");  
            $prSQL = "select shipdocrefid FROM masterrecord.ut_shipdoc sd where shipdocrefid = :prid"; 
            $prR = $conn->prepare($prSQL);
            $prR->execute(array(':prid' => $docid));
            $pr = $prR->fetch(PDO::FETCH_ASSOC);
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
body { margin: 0;  height: 100%; width: 100%; box-sizing: border-box; font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); }
RTNTHIS;
return $rtnthis;    
}            
    
function pagetabs($docobject) {
  
  switch($docobject['document']) { 
    case 'PATHOLOGY REPORT':
      $thisTab = "{$docobject['bgnbr']} (Pathology Report)";
    break;
    default: 
      $thisTab = "CHTN Eastern Division"; 
    break; 
  }
  return $thisTab;
}

function documenttext($docobject, $orginalURI) { 
    $doctext = "";
    //{"document":"PATHOLOGY REPORT","documentid":"41468","bgnbr":"82200T","donor":""} 
    switch($docobject['document']) { 
    case 'PATHOLOGY REPORT': 
        $doctext = getPathReportText($docobject['documentid'], $orginalURI);
        break;
    case 'SHIPMENT DOCUMENT':
        $doctext = getShipmentDocument($docobject['documentid'], $orginalURI);
        break;
    }
    return $doctext;
}

    
}

function getShipmentDocument($sdid, $originalURL) { 
    $at = genAppFiles;
    $tt = treeTop;
    include("{$at}/dataconn/sspdo.zck");  
    $dspsd = substr("000000{$sdid}",-6);
    session_start();
    
    //****************CREATE BARCODE
        require ("{$at}/appsupport/bcodeLib/qrlib.php");
        $tempDir = "{$at}/tmp/";
        $codeContents = "{$tt}{$orginalURI}";
        $fileName = 'pr' . generateRandomString() . '.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;
        if (!file_exists($pngAbsoluteFilePath)) {
          QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 2);
        } 
        $qrcode = base64file("{$pngAbsoluteFilePath}", "topqrcode", "png", true, " style=\"height: .5in;\"   ");
        
        $vbcode = "{$at}/tmp/sd" . generateRandomString() . ".png";
        //code128/code128a/code128b/code39/code25/
        barcode("{$vbcode}", $dspsd, "30", "vertical", "code39", false, 1);
        
        $sidebcode = base64file("{$vbcode}", "sidebarcode", "png", true, "  ");
        //********************END BARCODE CREATION
    
        
        $topSQL = "SELECT ifnull(status,'NEW') as sdstatus, date_format(statusDate, '%m/%d/%Y') as statusdate, ifnull(shipmenttrackingnbr,'') as trackingnbr, ifnull(date_format(shipdate,'%m/%d/%Y'),'') as shipdate, ifnull(invcode,'') as invcode, ifnull(shipaddy,'') as shipaddress, ifnull(billaddy,'') as billaddress, ifnull(invemail,'') as invemail, ifnull(ponbr,'') as ponbr, ifnull(salesorder,'') as salesorder, ifnull(date_format(tolab,'%m/%d/%Y'),'') as tolab, ifnull(acceptedby,'') as acceptedby, ifnull(comments,'') as comments, ifnull(date_format(setupon,'%m/%d/%Y'),'') as setupon, ifnull(setupby,'') as setupby FROM masterrecord.ut_shipdoc where shipdocrefid = :sdnbr";
        
        $topR = $conn->prepare($topSQL);
        $topR->execute(array(':sdnbr' => $sdid));
        if ($topR->rowCount() < 1) { 
           //NO SD FOUND
        } else { 
        
            $sd = $topR->fetch(PDO::FETCH_ASSOC);
            $sts = $sd['sdstatus'];
            $stsdte = $sd['statusdate'];
            $trcknbr = (trim($sd['trackingnbr']) === "") ? "" : " / {$sd['trackingnbr']}";
            $shpdte = $sd['shipdate'];
            $icode = $sd['invcode'];
            $shpadd = $sd['shipaddress'];
            $billadd = $sd['billaddress'];
            $invemail = (trim($sd['invemail']) === "") ? "" : "<br>{$sd['invemail']}";
            $ponbr = $sd['ponbr'];
            $salesorder = (trim($sd['salesorder']) === "") ? "" : " / " . substr('000000' . $sd['salesorder'], -6);
            $tolab = $sd['tolab']; 
            $acceptedby = $sd['acceptedby'];
            $cmt = $sd['comments']; 
            $setupon = $sd['setupon'];
            $setupby = $sd['setupby'];
            
            $iSQL = "SELECT concat(ifnull(invest_lname,''),', ', ifnull(invest_fname,'')) as iname FROM vandyinvest.invest where investid = :icode";
            $inv = $conn->prepare($iSQL);
            $inv->execute(array(':icode' => $icode));
            if ($inv->rowCount() > 0) { 
                $iinv = $inv->fetch(PDO::FETCH_ASSOC);
                $iname = "[{$iinv['iname']}]";
            } else { 
                $iname = "";
            }
            
            
            $dtlSQL = "SELECT ifnull(sg.qty,0) as qty, ifnull(sg.bgs,'') as bgs, ifnull(bs.pxiage,'') as pxiage, ifnull(bs.pxirace,'') as pxirace, ifnull(bs.pxigender,'') as pxigender, ifnull(bs.anatomicsite,'') as site, ifnull(bs.subsite,'') as subsite, ifnull(bs.diagnosis,'') as dx, ifnull(bs.subdiagnos,'') as subdx, ifnull(bs.tisstype,'') as specimencategory, ifnull(sg.hourspost,0) as hrpst, ifnull(sg.prepmethod,'') as prepmet , ifnull(sg.preparation,'') as preparation, ifnull(sg.metric,0) as metric, ifnull(mt.longvalue,'') as metricuom, ifnull(cx.dspvalue,'') as chemo, ifnull(rx.dspvalue,'') as rad FROM masterrecord.ut_shipdocdetails sdd left join masterrecord.ut_procure_segment sg on sdd.segid = sg.segmentid left join masterrecord.ut_procure_biosample bs on sg.biosamplelabel = bs.pbiosample left join (SELECT  menuvalue, longvalue FROM four.sys_master_menus where menu = 'METRIC') mt on sg.metricUOM = mt.menuvalue left join (SELECT  menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') cx on bs.chemoind = cx.menuvalue  left join (SELECT  menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') rx on bs.radind = rx.menuvalue  where sdd.shipdocrefid = :sdnbr order by sg.bgs";
            $dtlR = $conn->prepare($dtlSQL); 
            $dtlR->execute(array(':sdnbr' => $sdid)); 

            $nLines = 0;
            $tQty = 0;
            $rower = 0;
            while ($dtl = $dtlR->fetch(PDO::FETCH_ASSOC)) { 
                
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
                
                if ($rower === 0) { 
                    $bgc = " background: rgba(240,240,240,1); "; 
                    $rower = 1;
                } else { 
                    $bgc = " background: rgba(255,255,255,1); "; 
                    $rower = 0;                    
                }
                
                
                $innerTblLine .=  "<tr style=\"{$bgc} height: 20pt;\">"
                                             . "<td style=\"text-align: right; padding: 1px 3px 1px 1px; border: 1px solid rgba(203,203,203,1); border-left: none;border-top: none;  \">{$dtl['qty']}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;  \">{$dtl['bgs']}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;  \">{$bd}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;  \">{$prp}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none; white-space: nonwrap; \">{$weightMet}</td>"                                             
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none;  \">{$ars}</td>"
                                             . "<td style=\"padding: 2px; border: 1px solid rgba(203,203,203,1); border-left: none; border-top: none; border-right: none; text-align: right;  \">{$cxrx}</td></tr>";
               $nLines += 1;
               $tQty += (int)$dtl['qty'];
            }
            
            
    $docText = <<<RTNTHIS
<html>
<head>
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0;}
body { margin: 0; font-family: Roboto; font-size: 1.5vh; color: rgba(48,57,71,1); }
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
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Shipment Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$shpdte}</td><td style="width: 150px; font-size: 8pt; font-weight: bold; padding: 2px 4px 2px 4px; border: 1px solid rgba(0,0,0,1); border-bottom: none; border-top: none;">Comments</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">To Lab:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$tolab}</td><td rowspan=5 valign=top style="padding: 0 4px 1px 0; border: 1px solid rgba(0,0,0,1); border-top: none;">{$cmt}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Setup Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$setupon}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Setup By:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$setupby}</td></tr>
                <tr><td style="font-size: 8pt; white-space: nowrap; font-weight: bold; text-align: right; padding: 2px 4px 2px 0;">Status Date:</td><td style="white-space: nowrap; padding: 2px 4px 2px 2px; font-size: 8pt;">{$stsdte}</td></tr>
             </table>
            
            </td>
            <td valign=top align=right style="width: 3vw; padding: 10px 1px 0 2px; ">{$sidebcode}</td>    
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
    <tr><td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;">Investigator</td><td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;">Ship Via</td><td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;">Courier/Tracking #</td><td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold;">Purchase Order / Sales Order #</td><td style="border: 1px solid rgba(0,0,0,1); border-left: none; padding: 2px; font-weight: bold; border-right: none;">Accepted By</td></tr>
    <tr><td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);">{$icode} {$iname}{$invemail}</td><td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);">&nbsp;</td><td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);">&nbsp; {$trcknbr}</td><td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);border-right: 1px solid rgba(0,0,0,1);">{$ponbr} {$salesorder}</td><td style="padding: 2px 3px 2px 3px;border-bottom: 1px solid rgba(0,0,0,1);">{$acceptedby}</td></tr>
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
$linuxCmd = "wkhtmltopdf --load-error-handling ignore --page-size Letter  --margin-bottom 15 --margin-left 8  --margin-right 8  --margin-top 8  --footer-spacing 5 --footer-font-size 8 --footer-line --footer-right  \"page [page]/[topage]\" --footer-center \"https://www.chtneast.org\" --footer-left \"CHTNED Shipment Doc {$dspsd}\"     {$sdDocFile} {$sdPDF}";
$output = shell_exec($linuxCmd);
        
        }
    return array('status' => 200, 'text' => $docText, 'pathtodoc' => $sdPDF, 'format' => 'pdf');
    //return array('status' => $sts, 'text' =>  '', 'pathtodoc' => genAppFiles . "/publicobj/documents/pathrpts/pathologyrpt{$filehandle}.pdf", 'format' => 'pdf');
}

function getPathReportText($pathrptid, $orginalURI) { 
    $at = genAppFiles;
    $tt = treeTop;
    $favi = base64file("{$at}/publicobj/graphics/chtn_trans.png", "mastericon", "png", true, " style=\"height: .8in;  \" ");
    include("{$at}/dataconn/sspdo.zck");  
    session_start();
    
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
        require ("{$at}/appsupport/bcodeLib/qrlib.php");
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
<tr><td style="font-size: 9pt; font-weight: bold; border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); padding: 0 0 0 2;  " colspan=6>Collected Sample Designation</td></tr>
<tr><td style="font-size: 9pt; padding: 5pt 2pt 5pt 2pt;border-bottom: 1px solid rgba(206,206,206,1);border-left: 1px solid rgba(206,206,206,1); " colspan=6>{$dxdgnos}</td></tr>
</table>
BSTBL;
        }
        
        //GET HPR PERCENTAGES
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
