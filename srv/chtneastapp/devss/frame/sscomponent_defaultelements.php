<?php

class defaultpageelements {

function appcarddisplay($whichpage, $whichUsr, $rqststr) { 
//USER: {"statusCode":200,"loggedsession":"fhv3lfj32bcp5qbdd1egiqqjb6","dbuserid":1,"userid":"proczack","":"Zack von Menchhofen","useremail":null,"chngpwordind":0,"allowpxi":1,"allowprocure":1,"allowcoord":1,"allowhpr":1,"allowinventory":1,"presentinstitution":"HUP","primaryinstitution":"HUP","daysuntilpasswordexp":21,"accesslevel":"ADMINISTRATOR","profilepicturefile":"l7AbAkYj.jpeg","officephone":"215-662-4570 x10","alternateemail":"zackvm@zacheryv.com","alternatephone":"215-990-3771","alternatephntype":"CELL","textingphone":"2159903771@vtext.com","drvlicexp":"2020-11-24","allowedmodules":[["432","PROCUREMENT","",[{"googleiconcode":"airline_seat_flat","menuvalue":"Operative Schedule","pagesource":"op-sched","additionalcode":""},{"googleiconcode":"favorite","menuvalue":"Procurement Grid","pagesource":"procurement-grid","additionalcode":""},{"googleiconcode":"play_for_work","menuvalue":"Add Biogroup","pagesource":"collection","additionalcode":""}]],["433","DATA COORDINATOR","",[{"googleiconcode":"search","menuvalue":"Data Query (Coordinators Screen)","pagesource":"data-coordinator","additionalcode":""},{"googleiconcode":"account_balance","menuvalue":"Document Library","pagesource":"document-library","additionalcode":""}]],["434","HPR-QMS","",[]],["472","REPORTS","",[]],["473","UTILITIES","",[]],["474","HELP","scienceserver-help",[]]],"allowedinstitutions":[["HUP","Hospital of The University of Pennsylvania"],["PENNSY","Pennsylvania Hospital "],["READ","Reading Hospital "],["LANC","Lancaster Hospital "],["ORTHO","Orthopaedic Collections"],["PRESBY","Presbyterian Hospital"],["OEYE","Oregon Eye Bank"]]}$thi
$thisAcct = "";
$hlpfile = buildHelpFiles($whichpage, $rqststr);

if ($whichpage !== "login") { 
  $tt = treeTop;    
  

  $usrProfile = buildUserProfileTray( $whichUsr ); 
  $directory = buildUserDirectory();

  $thisAcct = <<<THISMENU

<div id=appcard_useraccount class=appcard> 
{$usrProfile}
</div>   
 
<div id=appcard_environmentals class=appcard> 
 ENVIRONMENTAL MONITORS
 <p>{$tt}       
</div>   
 
 <div id=appcard_help class=appcard> 
 {$hlpfile}
</div>   
 
 <div id=appcard_chtndirectory class=appcard>
 {$directory} 
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
          $controlListUniverse .= "<td valign=bottom class=universeBtns align=right {$univval['explainerline']}><i class=\"material-icons universalbtns\">{$univval['googleiconcode']}</i></td>";
        }
    }
    $controlListUniverse .= "</table>";

    foreach ($whichUsr->allowedinstitutions as $inskey => $insval) {
      if ( trim($whichUsr->primaryinstitution) === $insval[0]) {
        $igivendspvalue = "{$insval[1]} ({$insval[0]})";
      }
    }

    $expDay = ((int)$whichUsr->dayuntilpasswordexp < 1) ? "{$whichUsr->daysuntilpasswordexp} days" : "{$whichUsr->daysuntilpasswordexp} day";
     
    $lastlog = ( trim($whichUsr->lastlogin['lastlogdate']) !== "" ) ?  " <b>| Last Access</b>: {$whichUsr->lastlogin['lastlogdate']} from {$whichUsr->lastlogin['fromip']}" : " <b>| Last Access</b>: - ";
    $dspUser = "<b>User</b>: {$whichUsr->userid} ({$whichUsr->username}) <b>| Email</b>: {$whichUsr->useremail} <b>| Password Expires</b>: {$expDay}{$lastlog} <b>| Present Institution</b>: {$igivendspvalue}";
     
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
    case 'hprreview':
        $thisTab = "HPR Review @ CHTNEastern";
        break;
    case 'procurebiosample':
        $thisTab = "Biosample Sample Procurement (Pristine)";
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
   $thisModDialog = ""; 
   switch ($whichpage) { 
       case 'procurebiosample': 
           $thisModDialog = "<div id=standardModalDialog></div>";
       break;
       case 'datacoordinator': 
           $thisModDialog = "<div id=standardModalDialog></div>";
       break;
       case 'scienceserverhelp':
           $thisModDialog = "<div id=standardModalDialog></div>";
       break;
   }
   return $thisModDialog;
}    

}

function buildUserProfileTray( $whichUsr) { 

  $at = genAppFiles;
  //TODO:  ONLY ALLOW PNGs as profile pictures
  $profpic = base64file("{$at}/publicobj/graphics/usrprofile/{$whichUsr->profilepicturefile}", "apptrayUserProfilePicture", "png", true);  
  $usernbr = substr("000000{$whichUsr->dbuserid}",-6); 

  $inscnt = 0;
  $insm = "<table border=0 class=menuDropTbl>";
  $igivendspvalue = "";
  $igivendspcode = "";
  foreach ($whichUsr->allowedinstitutions as $inskey => $insval) {
    $instList .= ( $inscnt > 0 ) ? " <br> {$insval[1]} ({$insval[0]}) " : " {$insval[1]} ({$insval[0]}) ";
    $inscnt++;
    if ( trim($whichUsr->primaryinstitution) === $insval[0]) {
      $primeinstdsp = $insval[1];
      $igivendspvalue = "{$insval[1]} ({$insval[0]})";
      $igivendspcode = $insval[0];
    }
    $insm .= "<tr><td onclick=\"fillProfTrayField('profTrayPresentInst','{$insval[0]}','{$insval[1]} ({$insval[0]})');\" class=ddMenuItem>{$insval[1]} ({$insval[0]})</td></tr>";
  }
  $insm .= "</table>";
  $insmnu = "<div class=menuHolderDiv><input type=hidden id=profTrayPresentInstValue value=\"{$igivendspcode}\"><input type=text id=profTrayPresentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\"><div class=valueDropDown id=ddprofTrayPresentInst>{$insm}</div></div>";

  $manageMyAccount = <<<MYACCESS
<table border=0>
<tr><td colspan=2 class=usrAccountTitle>Manage My Account</td></tr>
<tr><td>Driver License Expiration</td><td><input type=text id=profTrayDLExp value="{$whichUsr->drvlicexp}"></td><td><table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Save</td></tr></table></td></tr>
<tr><td>Present Institution</td><td>{$insmnu}</td><td><table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Set</td></tr></table></td></tr>
</table>
<hr>
<div>
Password Expires in {$whichUsr->daysuntilpasswordexp} days. <p>
To change your password, click the "Request Code" button.  The server will send you a &laquo;password change code&raquo;.  Use this &laquo;password change code&raquo; along with your CURRENT password, new password and confirm new password fields to change your password for ScienceServer.
<table>
<tr><td colspan=2>Password Change Code</td></tr>
<tr><td><input type=text id=profTrayResetCode></td><td><table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Request Code</td></tr></table></td></tr>
<tr><td colspan=2>Current Password</td></tr>
<tr><td><input type=password id=profTrayCurrentPW></td></tr>
<tr><td colspan=2>New Password</td></tr>
<tr><td><input type=password id=profTrayNewPW></td></tr>
<tr><td colspan=2>Confirm Password</td></tr>
<tr><td><input type=password id=profTrayConfirmPW></td></tr>
<tr><td align=right> <table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Change Password</td></tr></table></td></tr>

</table>
</div>
MYACCESS;

$allowphi       = ( (int)$whichUsr->allowpxi === 1 ) ? "Yes" : "No";
$allowprocure   = ( (int)$whichUsr->allowprocure === 1 ) ? "Yes" : "No";  
$allowdata      = ( (int)$whichUsr->allowcoord === 1 ) ? "Yes" : "No";  
$allowhpr       = ( (int)$whichUsr->allowhpr === 1 ) ? "Yes" : "No";  
$allowinventory = ( (int)$whichUsr->allowinventory === 1 ) ? "Yes" : "No";  

$modcnt = 0;
foreach ($whichUsr->allowedmodules as $modkey => $modval) {
  $allowedModList .= ( $modcnt > 0) ? "; {$modval[1]}" : "{$modval[1]}";
  $modcnt++;
}


$myAccessTbl = <<<MYACCESS
<table border=0>
<tr><td colspan=2 class=usrAccountTitle>My Access Allowances</td></tr>
<tr><td colspan=2>
<table>
  <tr><td>Procurement</td><td>Data Coordination</td><td>UPHS PHI</td><td>HPR/QMS</td><td>Inventory</td><tr>
  <tr><td>{$allowprocure}</td><td>{$allowdata}</td><td>{$allowphi}</td><td>{$allowhpr}</td><td>{$allowinventory}</td></tr>
</table>
</td></tr>
<tr><td>Access </td><td>{$whichUsr->accesslevel}</td></tr>
<tr><td>Access Nbr</td><td>{$whichUsr->accessnbr}</td></tr>
<tr><td>Last Access</td><td>{$whichUsr->lastlogin['lastlogdate']}</td></tr>
<tr><td>Last Access from</td><td>{$whichUsr->lastlogin['fromip']}</td></tr>
<tr><td>SS Module List</td><td> {$allowedModList} </td></tr>
<tr><td valign=top>Allowed Institutions</td><td> {$instList} </td></tr>
</table>
MYACCESS;

   $profDetails = <<<PROFDET
 <table>
   <tr><td id=profTrayUserName>{$whichUsr->username} ({$usernbr})</td></tr>
   <tr><td id=profTrayUserEmail>{$whichUsr->useremail}</td></tr>
</table>
PROFDET;

    $cellcarriers = json_decode(callrestapi("GET", dataTree . "/global-menu/cell-carriers",serverIdent, serverpw), true); 
    $ccm = "<table border=0 class=menuDropTbl>";
    $ccgivendspvalue = "";
    $ccgivendspcode = "";
    foreach ($cellcarriers['DATA'] as $ccval) {
        $ccm .= "<tr><td onclick=\"fillProfTrayField('profTrayCC','{$ccval['codevalue']}','{$ccval['menuvalue']}');\" class=ddMenuItem>{$ccval['menuvalue']}</td></tr>";
    }
    $ccm .= "</table>";
    $ccmnu = "<div class=menuHolderDiv><input type=hidden id=profTrayCCValue value=\"{$givendspcode}\"><input type=text id=profTrayCC READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddprofTrayCC>{$ccm}</div></div>";

    $ynarr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-yes-no",serverIdent, serverpw), true);
    $ynm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($ynarr['DATA'] as $ynval) {
        if ((int)$ynval['codevalue'] === (int)$whichUsr->displayalternate) { 
          $givendspvalue = $ynval['menuvalue']; 
          $givendspcode = $ynval['codevalue']; 
        }
        $ynm .= "<tr><td onclick=\"fillProfTrayField('profTrayDisplayAltYN','{$ynval['codevalue']}','{$ynval['menuvalue']}');\" class=ddMenuItem>{$ynval['menuvalue']}</td></tr>";
    }
    $ynm .= "</table>";
    $ynmnu = "<div class=menuHolderDiv><input type=hidden id=profTrayDisplayAltYNValue value=\"{$givendspcode}\"><input type=text id=profTrayDisplayAltYN READONLY class=\"inputFld\" value=\"{$givendspvalue}\"><div class=valueDropDown id=ddprofTrayDisplayAltYN>{$ynm}</div></div>";

$abtMeTbl = <<<ABTME

<table border=0>
<tr><td colspan=3 class=usrAccountTitle>About {$whichUsr->username} ...</td></tr>
<tr><td>Login ID</td><td>DBID</td><td>Access Level</td><td>Primary Institution</td></tr>
<tr>
   <td>{$whichUsr->useremail}</td>
   <td>{$usernbr}</td>
   <td>{$whichUsr->accesslevel} / {$whichUsr->accessnbr}</td>
   <td>{$primeinstdsp} ({$whichUsr->primaryinstitution})</td>
</tr>
</table>
<hr>
<table>
<tr><td>Display in Directory: </td><td>{$ynmnu}</td></tr>
</table>
<table><tr><td>Office Phone</td><td>Alternate Phone (Cell)</td><td>Cell Carrier</td></tr>
<tr><td><input type=text id=profTrayOfficePhn value="{$whichUsr->officephone}"></td><td><input type=text id=profTrayAltPhone value={$whichUsr->alternatephone}></td><td>{$ccmnu}</tr>
</table>
<table>
<tr><td>Alternate Email: </td><td><input type=text id=profTrayAltEmail value={$whichUsr->alternateemail}></td></tr>
<tr><td>Profile Picture: </td><td><input type=file id=profTrayProfilePicture accept=".png"></td></tr>
<tr><td colspan=2 align=right>
  <table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Save</td></tr></table>
</td></tr>
</table>

ABTME;

$profTray = <<<PROFTRAY
  <div id=clsBtnHold>
   <table width=99% border=0>
      <tr><td></td><td></td><td id=closeBtn onclick="openAppCard('appcard_useraccount');" style="width: 1vw;">&times;</td></tr></table>
  </div>   

    <div id=usrAccountDspDiv>
      <table border=0>
        <tr><td id=profTrayPictureHold><div class="circular--portrait">{$profpic}</div></td>
        <td> {$profDetails}  </td></tr> 
      </table>  
<p>
     <table border=0 id=profTrayControlDivHoldTbl>
       <tr><td style="width: 6vw;">
           <table class=tblBtn id=btnProfileTrayAbtMe style="width: 7vw;" onclick="changeProfControlDiv('AbtMe');"><tr><td style="font-size: 1.3vh; text-align: center;">About Me</td></tr></table>
           </td>
           <td style="width: 6vw;">
           <table class=tblBtn id=btnProfileTrayAccess style="width: 7vw;" onclick="changeProfControlDiv('Access');"><tr><td style="font-size: 1.3vh; text-align: center;">My Access</td></tr></table>
           </td>
           <td style="width: 6vw;"> 
           <table class=tblBtn id=btnProfileTrayManagament style="width: 7vw;" onclick="changeProfControlDiv('Manage');"><tr><td style="font-size: 1.3vh; text-align: center;">Manage Account</td></tr></table>
           </td><td></td></tr>
       <tr><td colspan=4>
    <div id=profTrayControlDivHolder>
      <div id=profTrayControlAbtMe>{$abtMeTbl}</div>
      <div id=profTrayControlAccess>{$myAccessTbl}</div>
      <div id=profTrayControlManage>{$manageMyAccount} </div>
    <div>
     </td></tr> 
     </table>
    </div>


PROFTRAY;

return $profTray;

}

function buildUserDirectory() { 
    //{"MESSAGE":"","ITEMSFOUND":0,"DATA":[{"institution":"Hospital of The University of Pennsylvania (HUP)","userid":"000111","emailaddress":"linus@pennmedicine.upenn.edu","originalaccountname":"VLiVolsi","username":"Dr. Viriginia Livolsi","profilephone":"","profilepicurl":"avatar_female","dspalternateindir":0,"profilealtemail":"","altphone":"484-238-5982","altphonetype":"CELL","dsptitle":"Principal Investigator"} 

  $at = genAppFiles;
  $boypic = base64file("{$at}/publicobj/graphics/usrprofile/avatar_male.png", "", "png", true, " class=\"sidebarprofilepicture\" " );   
  $girlpic =  base64file("{$at}/publicobj/graphics/usrprofile/avatar_female.png", "", "png", true, " class=\"sidebarprofilepicture\" "); 


  $directoryRS = json_decode(callrestapi("GET", dataTree .  "/chtn-staff-directory-listing", serverIdent, serverpw), true);  
  $directory = "<div id=directoryDisplay>";
  $directory .= "<table border=0 cellspacing=4 id=directoryTbl><tr><td colspan=2><table border=0 id=directoryHeaderTbl><tr><td id=directoryHeaderTblTitle>CHTNEastern Directory Listing</td><td id=closeBtnDirectory onclick=\"openAppCard('appcard_chtndirectory');\">&times;</td></tr><tr><td colspan=2 style=\"font-size: 1.2vh; font-style: italic; text-align: center;\">(Alphabetical by Primary Institution/Last Name)</table></td></tr>";
  $inst = "";
  $cellCntr = 1;  
  foreach ($directoryRS['DATA'] as $dicKey => $dicVal) { 
    if ( trim($dicVal['institution']) !== $inst) {
        $directory .= "<tr><td colspan=2 class=directorySpacer>&nbsp;</td></tr><tr><td colspan=2 class=institutionHeader>{$dicVal['institution']}</td></tr><tr><tr><td colspan=2 class=directorySpacer>&nbsp;</td></tr>";
        $inst = trim($dicVal['institution']);
        $cellCntr = 1;
    } 
    if ($cellCntr === 3) { 
      $directory .= "</tr><tr>";
      $cellCntr = 1;
    }
 
    $phntype = ( trim($dicVal['altphonetype']) !== "") ? " ({$dicVal['altphonetype']})" : "";

    switch ($dicVal['profilepicurl']) { 
      case 'avatar_male':
         $profPicture = "<div class=circularOverlay>{$boypic}</div>";
         break;
      case 'avatar_female':
         $profPicture = "<div class=circularOverlay>{$girlpic}</div>";
         break;
      default:
         $profpic = base64file("{$at}/publicobj/graphics/usrprofile/{$dicVal['profilepicurl']}", "", "png", true, " class=\"sidebarprofilepicture\" " );   
         $profPicture = "<div class=circularOverlay>{$profpic}</div>";    
    }

    $profileDetTbl = <<<DETAILTBL
<table><tr><td class=personLbl>Name:&nbsp;</td><td class=personData>{$dicVal['username']} ({$dicVal['originalaccountname']})</td></tr>
       <tr><td class=personLbl>Position:&nbsp;</td><td class=personData>{$dicVal['dsptitle']}</td></tr>
       <tr><td class=personLbl>Email:&nbsp;</td><td class=personData>{$dicVal['emailaddress']}</td></tr>
       <tr><td class=personLbl>Phone:&nbsp;</td><td class=personData>{$dicVal['profilephone']}</td></tr>
       <tr><td class=personLbl>Alt Phone:&nbsp;</td><td class=personData>{$dicVal['altphone']}{$phntype}</td></tr>
       <tr><td class=personLbl>Alt Email:&nbsp;</td><td class=personData>{$dicVal['profilealtemail']}</td></tr>
       <tr><td class=personLbl>User Nbr:&nbsp;</td><td class=personData>{$dicVal['userid']}</td></tr>
</table>
DETAILTBL;

$personTbl = <<<PERSONTBL
  <table border=0 class=profileDetailDisplay><tr><td class=profPicCell>{$profPicture}</td><td>{$profileDetTbl}</td></tr></table>
PERSONTBL;

    $directory .= "<td valign=top class=personCell>{$personTbl}</td>";
    $cellCntr++;
  }
  $directory .= "</table>";
  $directory .= "<p>&nbsp;<p>&nbsp;<p></div>";

  return $directory;

}

function buildHelpFiles($whichpage, $request) { 

    $c = count($request);
    if ((int)$c > 2) { 
        $whichpage .= ":subpage";
    }
    
    //TODO - PULL FROM A WEB SERVICE    
    require(genAppFiles . "/dataconn/sspdo.zck"); 
    $hlpSQL = "SELECT ifnull(title,'') as hlpTitle, ifnull(subtitle,'') as hlpSubTitle, ifnull(bywhomemail,'') as byemail, ifnull(date_format(initialdate,'%M %d, %Y'),'') as initialdte, ifnull(lasteditbyemail,'') as lstemail, ifnull(date_format(lastedit,'%M %d, %Y'),'') as lstdte, ifnull(txt,'') as htmltxt FROM four.base_ss7_help where screenreference = :pgename and helptype = :hlptype ";
    $hlpR = $conn->prepare($hlpSQL); 
    $hlpR->execute(array(':pgename' => $whichpage, ':hlptype' => 'SCREEN'));


    if ($hlpR->rowCount() < 1) { 
        //NO HELP FILE
        $rthis = <<<RTNTHIS
   <div id=hlpHolderDiv>
   <div id=clsBtnHold><table width=100%><tr><td></td><td id=closeBtn onclick="openAppCard('appcard_help');">&times;</td></tr></table></div>   
   <div id=hlpTitle>ScienceServer Help Files</div> 
   <div id=hlpSubTitle>&nbsp;</div>            
   <div id=hlpByLine>&nbsp;</div>             
   <div id=hlpText>
       There is no help file for this ScienceServer screen. You can search the main help files by click the 'HELP' Menu on the main menu bar. <p> ({$whichpage})  
   </div>
   </div>                
RTNTHIS;
    } else { 

/*
 * NOTETOZACK: TO ADD PICTURES TO THE HELP FILE EMBED A JSON STRING INTO THE DATABASE FILE AS BELOW:
 * PICTURE:{"picturefile": "help/elproDiagram.png","type":"png","useid":"pictElproDiagram","width":"10vw", "caption":"elpro monitor diagram", "holdingdivstyle":"float: left; margin-right: 10px; margin-bottom: 10px;"} 
 * OR
 * PICTURE:{"picturefile": "graphics/chtn_trans.png","type":"png","useid":"pictCHTNTrans","height":"10vh","holdingdivstyle":"float: right; border: 1px solid #000084;"}
 *
 * References must be single line json (no carriage returns) and are outlined as 
 * picturefile (required) - under mainapp/publicobj ... directory/file
 * useid (required) is the id that will be written to the HTML output
 * height
 * width 
 * holdingdivstyle  these are in addition to position relative and display inline block
 * caption is text that will be placed in grey under the picture
 *
 */
        $hlp = $hlpR->fetch(PDO::FETCH_ASSOC);
        $ar = json_encode($hlp);
    $hlpTitle = $hlp['hlpTitle'];
    $hlpSubTitle = $hlp['hlpSubTitle'];
    $hlpEmail = $hlp['byemail'];
    $hlpDte = ( trim($hlp['initialdte']) !== "" ) ? " / {$hlp['initialdte']}" : "";
    $hlpTxt = putPicturesInHelpText( $hlp['htmltxt'] );
        $rthis = <<<RTNTHIS
   <div id=hlpHolderDiv>
   <div id=clsBtnHold><table width=100%><tr><td></td><td id=closeBtn onclick="openAppCard('appcard_help');">&times;</td></tr></table></div>   
   <div id=hlpTitle>{$hlpTitle}</div> 
   <div id=hlpSubTitle>{$hlpSubTitle}</div>            
   <div id=hlpByLine>{$hlpEmail} {$hlpDte}</div>             
   <div id=hlpText>
        {$hlpTxt}
        <p>&nbsp;
   </div>
   </div>         
RTNTHIS;
    }
    return $rthis;
}

