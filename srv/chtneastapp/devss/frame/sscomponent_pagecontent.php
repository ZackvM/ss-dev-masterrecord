<?php

class pagecontent {


    //json_encode($rqstStr) THIS IS THE ARRAY HOLDING THE URI COMPONENTS 
    //$whichUsr THIS IS THE USER ARRAY {"statusCode":200,"loggedsession":"i46shslvmj1p672lskqs7anmu1","dbuserid":1,"userid":"proczack","username":"Zack von Menchhofen","useremail":"zacheryv@mail.med.upenn.edu","chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":20,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""},{"googleiconcode":"lock_open","menuvalue":"Unlock Ship-Doc","pagesource":"unlock-shipdoc","additionalcode":""}]],["434","HPR-QMS","",[{"googleiconcode":"account_balance","menuvalue":"Review CHTN case","pagesource":"hpr-review","additionalcode":""}]],["472","REPORTS","",[{"googleiconcode":"account_balance","menuvalue":"All Reports","pagesource":"all-reports","additionalcode":""}]],["473","UTILITIES","",[{"googleiconcode":"account_balance","menuvalue":"Payment Tracker","pagesource":"payment-tracker","additionalcode":""}]],["474",null,null,[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]}     

public $maximizeBtn = "<i class=\"material-icons\">keyboard_arrow_up</i>";
public $minimizeBtn = "<i class=\"material-icons\">keyboard_arrow_down</i>"; 
public $closeBtn = "<i class=\"material-icons\">close</i>";
public $menuBtn = "<i class=\"material-icons\">menu</i>";
public $checkBtn = "<i class=\"material-icons\">check</i>";

function datacoordinator($rqststr, $whichusr) { 
    if ((int)$whichusr->allowcoord !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE COORDINATOR SCREEN";        
    } else {    
        if (trim($rqststr[2]) === "") {

            $BSGrid = buildBSGrid();
            $BSShipGrid = buildShippingQryGrid();
            $BSBankGrid = buildBSBankGrid(); 
            $rtnthis = <<<MAINQGRID
    <table border=0 id=mainQGridHoldTbl cellspacing=0 cellpadding=0>
        <tr>
            <td style="width: 6vw;height: 6vh;" valign=bottom><table class=tblBtn style="width: 6vw;" onclick="changeSearchGrid('biogroupdiv');"><tr><td><center>Biogroup</td></tr></table></td>
            <td style="width: 6vw;height: 6vh;" valign=bottom><table class=tblBtn style="width: 6vw;" onclick="changeSearchGrid('shipdiv');"><tr><td><center>Shipping</td></tr></table></td>
            <td style="width: 6vw;height: 6vh;" valign=bottom><table class=tblBtn style="width: 6vw;" onclick="changeSearchGrid('bankdiv');"><tr><td><center>Bank</td></tr></table></td>
            <td>&nbsp;</td>        
        </tr> 
         <tr><td colspan=4 valign=top>
               <div id=gridholdingdiv>     
                 <div class=gridDiv id=biogroupdiv>{$BSGrid}</div>
                 <div class=gridDiv id=shipdiv>{$BSShipGrid}</div>
                 <div class=gridDiv id=bankdiv>{$BSBankGrid}</div>                                        
               </div>     
         </td></tr>                    
    </table>
MAINQGRID;
            } else { 
                //$rtnthis = "DO SOMETHING HERE";            

                switch ($rqststr[2]) { 
                  case 'bank':
                    $qryid = $rqststr[3];
                    $qdata = getBankQryData($rqststr[3]); 
                
                    if((int)$qdata['status'] === 200) { 
                      $qbywhom = $qdata['qryby'];
                      $qon = $qdata['qryon'];
                      $qcrit = $qdata['criteria'];                      
                      $bdata = json_decode($qdata['bankdata'], true);
                      $nbrFound = $bdata['recordsFound'];
                      if((int)$nbrFound > 0) { 
                         $dataTable = "<table border=1><tr><th>CHTN #</th><th>PR</th><th>A</th><th>Site</th><th>Diagnosis</th><th>Category</th><th>Metastatic</th><th>Procedure</th><th>HP</th><th>Preparation</th><th>Metric</th><th>A/R/S</th><th>CX</th><th>RX</th></tr>";
                         foreach($bdata['returnData'] as $val) { 
                           $arsdsp = $val['age']; 
                           $arsdsp .= "/" . strtoupper(substr($val['race'],0,2));
                           $arsdsp .= "/" . strtoupper(substr($val['sex'],0,1));
                           $cxdsp = strtoupper(substr($val['chemotherapy'],0,1));
                           $rxdsp = strtoupper(substr($val['radiation'],0,1));                    
                           $prdsp = (trim($val['pathologyrpturl']) !== "") ? "<td onclick=\"openOutSidePage('{$val['pathologyrpturl']}');\">P</td>" : "<td>&nbsp;</td>";
                           $adsp = (trim($val['associativeid']) !== "") ? "<td onclick=\"alert('{$val['associativeid']}');\">A</td>" : "<td>&nbsp;</td>";
                           $dataTable .= <<<BGSLINE
<tr>
<td>{$val['divisionallabel']}&nbsp;</td>
{$prdsp}
{$adsp}<td>{$val['site']}&nbsp;</td><td>{$val['diagnosis']}&nbsp;</td><td>{$val['specimencategory']}&nbsp;</td><td>{$val['metastatic']}&nbsp;</td><td>{$val['proceduretype']}&nbsp;</td><td>{$val['hourspost']}&nbsp;</td><td>{$val['preparation']}&nbsp;</td><td>{$val['metric']}&nbsp;</td><td>{$arsdsp}</td><td>{$cxdsp}&nbsp;</td><td>{$rxdsp}&nbsp;</td></tr>
BGSLINE;
                         }
                         $dataTable .= "</table>";
                      } else { 
                          //NO FOUND
                      }
                    } else { 
                    }

                    $rtnthis = <<<RSLTTBL
<table border=0 cellpadding=0 cellspacing=0 id=bigQryRsltTbl >
<tr><td>BUTTON BAR</td></tr>
<tr><td><table><tr><td>Bank Search</td></tr>
<tr><td>{$qbywhom} {$qon} {$qcrit['site']} </td></tr>
</table></td></tr>
<tr><td><div id=bankDataHolder>{$dataTable}</div></td></tr>
<tr><td>Records Found: {$nbrFound}</td></tr>
</table>
RSLTTBL;
                       break;
                  case 'shipdoc': 
                    $qryid = $rqststr[3];
                    $qdata = getShipQryData($rqststr[3]); 

                    if((int)$qdata['status'] === 200) { 
                      $qbywhom = $qdata['qryby'];
                      $qon = $qdata['qryon'];
                      $qcrit = $qdata['criteria'];                      
                  
                       $dataTable = $qdata['shipdata'];
                    } else { 
                    }




                    $rtnthis = <<<RSLTTBL
<table border=0 cellpadding=0 cellspacing=0 id=bigQryRsltTbl >
<tr><td>BUTTON BAR</td></tr>
<tr><td><table><tr><td>Bank Search</td></tr>
<tr><td>{$qbywhom} {$qon}</td></tr>
</table></td></tr>
<tr><td><div id=bankDataHolder>{$dataTable} </div></td></tr>
<tr><td>Records Found: {$nbrFound}</td></tr>
</table>
RSLTTBL;
                      break; 
                  default: 
                      $rtnthis = "NO DATA FOUND";
                }

        }
    }    
    return $rtnthis;
}

function procurementgrid($rqststr, $whichusr) { 
    if ((int)$whichusr->allowcoord !== 1) { 
     $rtnthis = "<h1>USER IS NOT ALLOWED TO USE THE COORDINATOR SCREEN";        
    } else {    

        if (trim($rqststr[2]) === "") { 
            ///QUERY TOP ONLY
            $month = substr('00' . date('m'),-2);
            $day = substr('00' . date('d'),-2);
            $year = date('Y');
        } else { 
            ///GET GRID DATA
        }

        $instvl = $whichusr->presentinstitution;
        foreach ($whichusr->allowedinstitutions as $instlst) {
          if (trim($instlst[0]) === $instvl) { 
            $instdsp = $instlst[1];
          }
        } 

        $dInstitutions = "<table border=0 class=menuDropTbl>";
        foreach ($whichusr->allowedinstitutions as $allinst) { 
           $dInstitutions .= "<tr><td class=ddMenuItem onclick=\"fillField('fInstitution','{$allinst[0]}','{$allinst[1]}','');\">{$allinst[1]}</td></tr>";
        }
        $dInstitutions .= "</table>";


        $institution = <<<INSTITUTION
<div class=menuHolderDiv style="min-width: 20vw;">
  <div class=valueHolder style="min-width: 20vw;"><input type=hidden id=fInstitutionValue value="{$instvl}"><input type=text READONLY id=fInstitution style="width: 20vw;" value="{$instdsp}"></div>
  <div class=valueDropDown style="min-width: 20vw;" id=ddInstitutions>{$dInstitutions}</div>
</div>
INSTITUTION;




$dCalendar = buildcalendar('procquery'); 
        $calendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=fDateValue value="{$year}{$month}{$day}"><input type=text READONLY id=fDate value="{$month}/{$day}/{$year}" style="width: 10vw;"></div>
  <div class=valueDropDown style="min-width: 15vw;" id=ddInstitutions><div id=dspCalendarHere>{$dCalendar}</div></div>
</div>

CALENDAR;


$rtnthis = <<<PAGECONT
<table border=0><tr><td colspan=3>Procurement</td></tr><tr><td>Institution</td><td>Date</td><td></td></tr><tr><td>{$institution}</td><td>{$calendar}</td><td><table class=tblBtn onclick="alert('get grid');"><tr><td>Display</td></tr></table></td></tr></table>
PAGECONT;

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
        switch ($typevl) { 
            case 'PATHOLOGYRPT':
                $typedsp = 'Pathology Report Text Search';
                $docobj = "pathrpt";
                $printObj = "pathology-report";
                break;
            case 'PRBGNBR':
                $typedsp = 'Pathology Report Biogroup Search';
                $docobj = "pathrpt";
                $printObj = "pathology-report";
                break;
            case 'CHARTRVW':
                $typedsp = 'Chart Review';
                $docobj = "pxchart";
                $printObj = "chart-review";
                break; 
            case 'SHIPDOC':
                $typedsp = 'Shipment Document (Ship Doc)';
                $docobj = "shipdoc";
                $printObj = "shipment-manifest";
                break;                 
        } 
        $dtadsp = "<table width=100% border=0 id=doclibabstbl cellspacing=4>";
        $warning = "";
        $qryBy = $dta['DATA']['head']['bywho'];
        $qryOn = $dta['DATA']['head']['onwhendsp'];
        if ((int)$dta['ITEMSFOUND'] > 249) { 
            $warning = "(You have reached your limit of return results.  For a more indepth query, see a CHTNEast IT Staff)";
        }
        $dtadsp .= "<tr><td colspan=2 valign=top align=right id=byline>({$qryBy}: {$qryOn})</td></tr>";    
        $dtadsp .= "<tr><td colspan=2 valign=top id=headerwarning>Documents Found: " . $dta['ITEMSFOUND'] . " {$warning}</td></tr>";
        $dtadsp .= "<tr><td valign=top class=fldLabel>Ref #</td><td valign=top class=fldLabel>Document Abstract</td></tr>";
        foreach($dta['DATA']['records'] as $rs) { 
            $selector = cryptservice($rs['prid'] . "-" . $rs['selector'], "e");
            $dtadsp .= "<tr onclick=\"openOutSidePage('{$tt}/print-obj/{$printObj}/{$selector}');\" class=datalines>"
                                 . "<td valign=top class=bgnbr>{$rs['dspmark']}</td>"
                                 . "<td valign=top class=abstracttext>{$rs['abstract']}...</td>"
                                 . "</tr>";
        }        
        $dtadsp .= "</table>";
    } else { 
        //NO QUERY SPECIFIED
        $dtadsp = " NO QUERY ID ";
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
             {$estring}<p>{$dstring}<p>     
             {$dtadsp}
         </td>
    </tr>    
</table>          
PAGEHERE;
return $rtnthis;  
}
 
function root($rqstStr, $whichUsr) { 
    //json_encode($rqstStr) THIS IS THE ARRAY HOLDING THE URI COMPONENTS 
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

$controlBtn = "<table><tr><td class=adminBtn onclick=\"doLogin();\">Login</td></tr></table>";    
    
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

function buildShippingQryGrid() { 
  $si = serverIdent;
  $sp = serverpw;
    
$fCalendar = buildcalendar('shipBSQFrom'); 
$shpFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=shpQryFromDateValue><input type=text READONLY id=shpQryFromDate></div>
  <div class=valueDropDown style="min-width: 15vw;" id=fcal><div id=bsqtCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;
  
$tCalendar = buildcalendar('shipBSQTo'); 
$shpToCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=shpQryToDateValue><input type=text READONLY id=shpQryToDate></div>
  <div class=valueDropDown style="min-width: 15vw;" id=tcal><div id=bsqtCalendar>{$tCalendar}</div></div>
</div>
CALENDAR;


$segstatusarr = json_decode( callrestapi("GET", dataTree . "/globalmenu/allshipdocstatus",$si,$sp) , true);

$seg = "<table border=1><tr><td align=right onclick=\"fillField('qryShpStatus','','');\">[clear]</td></tr>";
foreach ($segstatusarr['DATA'] as $segval) { 
  $seg .= "<tr><td onclick=\"fillField('qryShpStatus','{$segval['lookupvalue']}','{$segval['menuvalue']}');\">{$segval['menuvalue']}</td></tr>";
}
$seg .= "</table>";
$shpsts = "<div class=menuHolderDiv><input type=hidden id=qryShpStatusValue><input type=text id=qryShpStatus READONLY><div class=valueDropDown>{$seg}</div></div>";

$grid = <<<GRIDLAY
<table border=0 width=100%>
<tr><td>Shipping Query</td></tr>
<tr><td colspan=2>

   <table border=0>    
   <tr><td>Ship-Doc #</td><td>Ship Doc Status</td></tr> 
   <tr><td><input type=text id=shpShipDocNbr></td><td>{$shpsts}</td></tr>
   <tr><td>Shipping Date from</td><td>Shipping Date to</td></tr>
   <tr><td>{$shpFromCalendar}</td><td>{$shpToCalendar}</td></tr>    
   <tr><td colspan=2>Investigator (Inv# or last name)</td></tr>
   <tr><td colspan=2><input type=text id=shpShipInvestigator></td>
   </table>

   </td></tr>
<tr><td align=right style="padding: 3vh 45vw 0 0;">
    <table class=tblBtn style="width: 6vw;" onclick="submitqueryrequest('shpqry');"><tr><td><center>Search</td></tr></table>   
   </td></tr>
</table>            
GRIDLAY;
return $grid;             
}


function buildBSBankGrid() { 
  
  $spcarr = json_decode(callrestapi("GET", dataTree . "/globalmenu/chtnvocabularyspecimencategory", serverIdent, serverpw),true);
  //$spcd = json_decode($spcarr['datareturn'],true);
  $spc = "<table border=1>";
  $spc .= "<tr><td onclick=\"fillField('bnkSpecCat','','');\">[clear]</td></tr>";
  foreach ($spcarr['DATA'] as $spcval) { 
    $spc .= "<tr><td onclick=\"fillField('bnkSpecCat','{$spcval['lookupvalue']}','{$spcval['menuvalue']}');\">{$spcval['menuvalue']}</td></tr>";
  }
  $spc .= "</table>";

    $grid = <<<GRIDLAY
<table border=0 width=100%>
<tr><td colspan=4>Read 'Help' Screen to use this screen more fully.  This is a derivative of the CHTN Transient Inventory App.</td></tr>
<tr><td>Site</td><td>Diagnosis</td><td>Specimen Category</td><td width=20%></td></tr>
<tr>
   <td><input type=text id=bnkSite></td>
   <td><input type=text id=bnkDiagnosis></td>
   <td><div class=menuHolderDiv><input type=text id=bnkSpecCat READONLY><div class=valueDropDown>{$spc}</div></div></td>
   <td></td>
</tr>
<tr><td colspan=3>
<center>
<table border=0>
<tr>
<td><div><input type=checkbox id=bnkPrpFFPE checked><label for=bnkPrpFFPE>FFPE</label></div></td>
<td><div><input type=checkbox id=bnkPrpFixed checked><label for=bnkPrpFixed>FIXED</label></div></td>
<td><div><input type=checkbox id=bnkPrpFrozen checked><label for=bnkPrpFrozen>FROZEN</label></div></td>
</tr>
</table>
</td><td></td></tr>
<tr><td colspan=3 align=right>
<table class=tblBtn style="width: 6vw;" id=btnBankSearchSubmit><tr><td><center>Search</td></tr></table>
</td><td></td></tr>
</table>

GRIDLAY;
return $grid; 
}

function buildBSGrid() { 
  $si = serverIdent;
  $sp = apikey;
  $segstatusarr = json_decode(callrestapi("GET","https://data.chtneast.org/globalmenu/allsegmentstati",$si,$sp),true);
  $segstatusd = json_decode($segstatusarr['datareturn'],true);
  $seg = "<table border=1>";
  foreach ($segstatusd['DATA'] as $segval) { 
    $seg .= "<tr><td onclick=\"fillField('qrySegStatus','{$segval['lookupvalue']}','{$segval['menuvalue']}');\">{$segval['menuvalue']}</td></tr>";
  }
  $seg .= "</table>";
  $preparr = json_decode(callrestapi("GET","https://data.chtneast.org/globalmenu/allpreparationmethods",$si,$sp),true);
  $prepd = json_decode($preparr['datareturn'], true);
  $prp = "<table border=1>";
  foreach ($prepd['DATA'] as $prpval) {
    $prp .= "<tr><td>{$prpval['menuvalue']} - {$prpval['lookupvalue']}</td></tr>";
  }
  $prp .= "</table>";
  $fCalendar = buildcalendar('biosampleQueryFrom'); 
$bsqFromCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=bsqueryFromDateValue><input type=text READONLY id=bsqueryFromDate></div>
  <div class=valueDropDown style="min-width: 15vw;" id=ddInstitutions><div id=bsqCalendar>{$fCalendar}</div></div>
</div>
CALENDAR;
$tCalendar = buildcalendar('biosampleQueryTo'); 
$bsqToCalendar = <<<CALENDAR
<div class=menuHolderDiv>
  <div class=valueHolder><input type=hidden id=bsqueryToDateValue><input type=text READONLY id=bsqueryToDate></div>
  <div class=valueDropDown style="min-width: 15vw;" id=ddInstitutions><div id=bsqtCalendar>{$tCalendar}</div></div>
</div>
CALENDAR;


    $grid = <<<BSGRID
<table border=0 width=100%>
<tr><td>BIOGROUP</td></tr>
<tr><td>

<table border=0>

<tr><td>Biogroup</td><td>Segment Status</td></tr>
<tr>
  <td><input type=text id=qryBG></td>
  <td><div class=menuHolderDiv><input type=hidden id=qrySegStatusValue><input type=text id=qrySegStatus READONLY><div class=valueDropDown>{$seg}</div></div></td>
</tr>

<tr><td colspan=2>Procurement Date</td></td></tr>
<tr>
  <td>{$bsqFromCalendar}</td>
  <td>{$bsqToCalendar}</td>
</tr>

<tr><td>Diagnosis Designation (Site)</td><td>Diagnosis Designation (Diagnosis or Specimen Category)</td></tr>
<tr>
  <td><input type=text id=qryDXDSite></td>
  <td><input type=text id=qryDXDDiagnosis></td>
</tr>

<tr><td colspan=2>Preparation</td></tr>
<tr>
  <td><div class=menuHolderDiv><input type=text id=qryPreparationMethod READONLY><div class=valueDropDown>{$prp}</div></div></td>
  <td><div class=menuHolderDiv><input type=text id=qryPreparation READONLY><div class=valueDropDown>&nbsp;</div></div></td>
</tr>

</table>

</td></tr>
<tr><td align=right style="padding: 3vh 45vw 0 0;">
<table class=tblBtn style="width: 6vw;" onclick="alert('search BG');"><tr><td><center>Search</td></tr></table>
</td></tr>
</table>

BSGRID;
return $grid; 
}

function getBankQryData($qryid) { 
  $si = serverIdent;
  $sp = apikey;
  $status = 500;
  $qryby = "";
  $critarr = json_decode(callrestapi("GET","https://scienceserver.chtneast.org/data-obj-request/bankqrycriteria/{$qryid}",$si,$sp),true);
//"DATA":{"bywhom": "proczack","qrydate": "10\/11\/2018","qrytype": "BANK","jsoncriteria": "{\"qryType\":\"BANK\",\"site\":\"thyroid\",\"dx\":\"\",\"specimencategory\":\"MALIGNANT\",\"prepFFPE\":1,\"prepFIXED\":1,\"prepFROZEN\":1}"}}
  if ((int)$critarr['status'] === 200) { 
      //GET DATA
      $qryby = $critarr['DATA']['bywhom'];    
      $qryon = $critarr['DATA']['qrydate'];
      $qrycrit = json_decode($critarr['DATA']['jsoncriteria'], true);

      $prp = array();
      if ((int)$qrycrit['prepFFPE'] === 1) $prp[] = "FFPE";
      if ((int)$qrycrit['prepFIXED'] === 1) $prp[] = "FIXED";
      if ((int)$qrycrit['prepFROZEN'] === 1) $prp[] = "FROZEN";
      $passdata['requester'] = "";
      $passdata['requestedDataPage'] = 0;
      $passdata['requestedSite'] = $qrycrit['site'];
      $passdata['requestedDiagnosis'] = $qrycrit['dx'];
      $passdata['requestedCategory'] = $qrycrit['specimencategory']; 
      $passdata['requestedPreparation'] = $prp;
      $tidalsearch = calltidal('POST','https://data.chtneast.org/runbank',json_encode($passdata));

      $status = 200;
  } else { 
  } 
  return array('status' => $status, 'qryby' => $qryby, 'qryon' => $qryon, 'criteria' => $qrycrit, 'bankdata' => $tidalsearch);
}

function getShipQryData($qryid) {
  $si = serverIdent;
  $sp = apikey;
  $status = 500;
  $qryby = "";
  $critarr = json_decode(callrestapi("GET","https://scienceserver.chtneast.org/data-obj-request/shipdocqrycriteria/{$qryid}",$si,$sp),true);
  if ((int)$critarr['status'] === 200) { 
      $qryby = $critarr['DATA']['bywhom'];    
      $qryon = $critarr['DATA']['qrydate'];
      $qrycrit = json_decode($critarr['DATA']['jsoncriteria'], true);
      $passdata['requester'] = "";
      $passdata['requestedDataPage'] = 0;
      $passdata['shipdocnumber'] = $qrycrit['shipdocnumber'];
      $passdata['sdstatus'] = $qrycrit['sdstatus'];
      $passdata['sdshipfromdte'] = $qrycrit['sdshipfromdte']; 
      $passdata['sdshiptodte'] = $qrycrit['sdshiptodte'];
      $passdata['investigator'] = $qrycrit['investigator'];
      //$tidalsearch = calltidal('POST','https://data.chtneast.org/runbank',json_encode($passdata));
      $status = 200;
  } else { 
  } 
  return array('status' => $status, 'qryby' => $qryby, 'qryon' => $qryon, 'criteria' => $qrycrit, 'shipdata' => json_encode($passdata));
}

