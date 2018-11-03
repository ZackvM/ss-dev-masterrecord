<?php

class defaultpageelements {

function appcarddisplay($whichpage, $whichUsr) { 
//USER: {"statusCode":200,"loggedsession":"fhv3lfj32bcp5qbdd1egiqqjb6","dbuserid":1,"userid":"proczack","":"Zack von Menchhofen","useremail":null,"chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":21,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""}]],["434","HPR-QMS","",[]],["472","REPORTS","",[]],["473","UTILITIES","",[]],["474","HELP","scienceserver-help",[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]}$thi
$thisAcct = "";
$allAcct = json_encode($whichUsr);
$hlpfile = buildHelpFiles($whichpage);



if ($whichpage !== "login") { 
  $tt = treeTop;    
  $thisAcct = <<<THISMENU

<div id=appcard_useraccount class=appcard> 
 Account Display
 <p>{$tt}
 <br>{$whichUsr->useremail}   
 <hr>
 {$allAcct}
</div>   
 
<div id=appcard_environmentals class=appcard> 
 ENVIRONMENTAL MONITORS
 <p>{$tt}       
</div>   
 
 <div id=appcard_help class=appcard> 
 {$hlpfile}
</div>   
 
THISMENU;
}
return $thisAcct;    
}    
    
function menubuilder($whichpage, $whichUsr) {

$thisMenu = "";    
if ($whichpage !== "login") {
    $tt = treeTop;    
    $at = genAppFiles;
    $chtn = base64file("{$at}/publicobj/graphics/smlchtnlogo.png", "barchtnlogo", "png", true);   
    
    $controlList = "<table border=0 cellpadding=0 cellspacing=0>";
    foreach ($whichUsr->allowedmodules as $modval) {
        if (trim($modval[2]) !== "") { 
            //HEADERONLY
            $controlList .= "<td valign=bottom class=menuHolderSqr>"
                                         . "<div class=mnuHldrOnly>"
                                         . "<div class=hdrOnlyItem onclick=\"navigateSite('{$modval[2]}');\">{$modval[1]}"
                                         . "</div>"
                                         . "</div>"
                                  . "</td>";
        } else { 
           //HEADER AND ITEMS
           $controlList .= "<td valign=bottom class=menuHolderSqr><div class=mnuHldr><div class=hdrItem>{$modval[1]}</div>"; 
              //GET ITEMS
           $controlList .= "<div class=menuDrpItems><table>";
           foreach ($modval[3] as $lstItem) {
              $controlList .= "<tr><td onclick=\"navigateSite('{$lstItem['pagesource']}');\">" . $lstItem['menuvalue'] . "</td></tr>";               
          }
          $controlList .= "</table></div>";
          $controlList .= "</div></td>";           
        }
    }
    $controlList .= "</table>";

    $uBtnsj = json_decode(callrestapi("GET", dataTree . "/ssuniversalcontrols", serverIdent, serverpw),true);
    $controlListUniverse = "<table border=0 cellspacing=0 cellpadding=0>";
    foreach ($uBtnsj['DATA'] as $unbr => $univval) { 
        if ($unbr !== "statusCode") {
          $controlListUniverse .= "<td valign=bottom class=universeBtns align=right {$univval['explainerline']}><i class=\"material-icons\">{$univval['googleiconcode']}</i></td>";
        }
    }
    $controlListUniverse .= "</table>";
    
    $dspUser = "<b>User</b>: {$whichUsr->userid} ({$whichUsr->username})";
    
    
    $topBar = <<<TBAR
          <div id=topMenuHolder>
            <div id=globalTopBar>
                <table border=0 cellpadding=0 cellspacing=0 id=topBarMenuTable>
                    <tr>
                        <td onclick="navigateSite('');" rowspan=2>{$chtn}</td><td colspan=20 align=right id=topbarUserDisplay>{$dspUser}</td></tr>
                        <tr><td class=spacer>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td align=right>
                         {$controlList}
                         </td>
                         <td align=right id=rightControlPanel>
                          {$controlListUniverse} 
                         </td>
                    </tr>
                </table>
            </div>
        </div>
TBAR;
  
  

//$thisMenu = <<<THISMENU
//<div id=topMenuHolder>{$topBar}</div>             
//THISMENU;
} 
  return $topBar;
}

function faviconBldr($whichpage) {  
  $at = genAppFiles;
  $favi = base64file("{$at}/publicobj/graphics/icons/chtnblue.ico", "favicon", "favicon", true);
  return $favi;
}

function pagetabs($whichpage) { 
  $thisTab = "CHTN Eastern Division";
  switch($whichpage) { 
    case 'home':
      $thisTab = "CHTN Eastern Division";
      break;
    case 'datacoordinator':
      $thisTab = "Data Coordinator @ CHTNEastern";
      break;  
    case 'unlockshipdoc':
      $thisTab = "UNLOCK SHIPDOC (ADMINISTRATION ONLY)";
      break;
    case 'scienceserverhelp':
      $thisTab = "ScienceServer Help @ CHTNEastern";
      break;
    default: 
      $thisTab = "CHTN Eastern Division"; 
    break; 
  }
  return $thisTab;
}
    
function modalbackbuilder($whichpage) {
  $thisModBack = "";
  switch ($whichpage) {     
    default: 
      $thisModBack = "<div id=standardModalBacker></div>";    
  }                
  return $thisModBack;
}

function modaldialogbuilder($whichpage) {
  
  $thisModDialog = "<div id=standardModalDialog></div>";

    return $thisModDialog;
}    

}


function buildHelpFiles($whichpage) { 
    //REWRITE THIS TO PULL FROM DATABASE
    
    require(genAppFiles . "/dataconn/sspdo.zck"); 
    $hlpSQL = "SELECT ifnull(title,'') as hlpTitle, ifnull(subtitle,'') as hlpSubTitle, ifnull(bywhomemail,'') as byemail, ifnull(date_format(initialdate,'%M %d, %Y'),'') as initialdte, ifnull(lasteditbyemail,'') as lstemail, ifnull(date_format(lastedit,'%M %d, %Y'),'') as lstdte, ifnull(txt,'') as htmltxt FROM four.base_ss7_help where screenreference = :pgename";
    $hlpR = $conn->prepare($hlpSQL); 
    $hlpR->execute(array(':pgename' => $whichpage));
    if ($hlpR->rowCount() < 1) { 
        $rthis = <<<RTNTHIS

   <div id=hlpHolderDiv>
   <div id=clsBtnHold><table width=100%><tr><td></td><td id=closeBtn onclick="openAppCard('appcard_help');">&times;</td></tr></table></div>   
   <div id=hlpTitle>ScienceServer v7 Help Files</div> 
   <div id=hlpSubTitle>-Sub Title-</div>            
   <div id=hlpByLine>zacheryv@mail.med.upenn.edu / September 25, 2018</div>             
   <div id=hlpText>
       There is no help file for this ScienceServer screen.  
                
   </div>
   </div>                
RTNTHIS;
    } else { 
        
    $hlp = $hlpR->fetch(PDO::FETCH_ASSOC);

        $rthis = <<<RTNTHIS

   <div id=hlpHolderDiv>
   <div id=clsBtnHold><table width=100%><tr><td></td><td id=closeBtn onclick="openAppCard('appcard_help');">&times;</td></tr></table></div>   
   <div id=hlpTitle>{$hlp['hlpTitle']}</div> 
   <div id=hlpSubTitle>{$hlp['hlpSubTitle']}</div>            
   <div id=hlpByLine>{$hlp['byemail']} / {$hlp['initialdte']}</div>             
   <div id=hlpText>
    {$hlp['htmltxt']}
   </div>
   </div>         
RTNTHIS;

    }






    return $rthis;
}
