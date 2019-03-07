<?php

class defaultpageelements {

function appcarddisplay($whichpage, $whichUsr, $rqststr) { 
$thisAcct = "";
$hlpfile = buildHelpFiles($whichpage, $rqststr);

if ($whichpage !== "login") { 
  $tt = buildEnvironTemps();    
  $usrProfile = buildUserProfileTray( $whichUsr ); 
  $directory = buildUserDirectory();
  $vocab = buildVocabSearch();

  $thisAcct = <<<THISMENU

<div id=appcard_useraccount class=appcard> 
{$usrProfile}
</div>   

<div id=appcard_environmentals class=appcard>
{$tt}       
</div>   
 
 <div id=appcard_help class=appcard> 
 {$hlpfile}
</div>   
 
 <div id=appcard_chtndirectory class=appcard>
 {$directory} 
 </div>

<div id=appcard_vocabsearch class=appcard>
{$vocab}
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
            if ( trim($univval['googleiconcode']) === 'ac_unit') { 
                $controlListUniverse .= "<td valign=bottom class=\"universeBtns universeFreeze\" align=right {$univval['explainerline']}><i class=\"material-icons universalbtns\">{$univval['googleiconcode']}</i></td>";
            } else {     
                $controlListUniverse .= "<td valign=bottom class=universeBtns align=right {$univval['explainerline']}><i class=\"material-icons universalbtns\">{$univval['googleiconcode']}</i></td>";
            }    
        }
    }
    $controlListUniverse .= "</table>";

    foreach ($whichUsr->allowedinstitutions as $inskey => $insval) {
      if ( trim($whichUsr->presentinstitution) === $insval[0]) {
        $igivendspvalue = "{$insval[1]} ({$insval[0]})";
      }
    }

    $expDay = ((int)$whichUsr->daysuntilpasswordexp === 1) ? "{$whichUsr->daysuntilpasswordexp} day" : "{$whichUsr->daysuntilpasswordexp} days";
    $expDayNotice = ( (int)$whichUsr->daysuntilpasswordexp < 10 ) ? "<span class=expireDayNoticeRed>{$expDay}</span>" : "<span class=expireDayNoticeGreen>{$expDay}</span>";

    //$lastlog = ( trim($whichUsr->lastlogin['lastlogdate']) !== "" ) ?  " <b>| Last Access</b>: {$whichUsr->lastlogin['lastlogdate']} from {$whichUsr->lastlogin['fromip']}" : " <b>| Last Access</b>: - ";
    $lastlog = ( trim($whichUsr->lastlogin['lastlogdate']) !== "" ) ?  " <b>| Last Access</b>: {$whichUsr->lastlogin['lastlogdate']} " : " <b>| Last Access</b>: - ";
    $dspUser = "<b>User</b>: {$whichUsr->userid} ({$whichUsr->username}) <b>| Email</b>: {$whichUsr->useremail} <b>| Password Expires</b>: {$expDayNotice}{$lastlog} <b>| Present Institution</b>: {$igivendspvalue}";
     
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
    case 'paymenttracker':
        $thisTab = "Payments for Processing @ CHTNEastern";
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
  if (trim($whichUsr->profilepicturefile) === 'avatar_male' || trim($whichUsr->profilepicturefile) === 'avatar_female' ) { 
     $profpic = base64file("{$at}/publicobj/graphics/usrprofile/{$whichUsr->profilepicturefile}.png", "apptrayUserProfilePicture", "png", true);   
  } else { 
    $profpic = base64file("{$at}/publicobj/graphics/usrprofile/{$whichUsr->profilepicturefile}", "apptrayUserProfilePicture", "png", true);  
  }
  $usernbr = substr("000000{$whichUsr->dbuserid}",-6); 

  $inscnt = 0;
  $insm = "<table border=0 class=menuDropTbl>";
  $igivendspvalue = "";
  $igivendspcode = "";
  foreach ($whichUsr->allowedinstitutions as $inskey => $insval) {
    $instList .= ( $inscnt > 0 ) ? " <br> {$insval[1]} ({$insval[0]}) " : " {$insval[1]} ({$insval[0]}) ";
    $inscnt++;
    if ( trim($whichUsr->presentinstitution) === $insval[0]) {
      $primeinstdsp = $insval[1];
      $igivendspvalue = "{$insval[1]} ({$insval[0]})";
      $igivendspcode = $insval[0];
    }
    $insm .= "<tr><td onclick=\"fillProfTrayField('profTrayPresentInst','{$insval[0]}','{$insval[1]} ({$insval[0]})');\" class=ddMenuItem>{$insval[1]} ({$insval[0]})</td></tr>";
  }
  $insm .= "</table>";
  $insmnu = "<div class=menuHolderDiv><input type=hidden id=profTrayPresentInstValue value=\"{$igivendspcode}\"><input type=text id=profTrayPresentInst READONLY class=\"inputFld\" value=\"{$igivendspvalue}\"><div class=valueDropDown id=ddprofTrayPresentInst>{$insm}</div></div>";

  $daydsp = ( (int)$whichUsr->daysuntilpasswordexp === 1 ) ? " day" : " days";
  $expirenotice = ( (int)$whichUsr->daysuntilpasswordexp < 15 ) ? "<div id=passwordexpireNoticeRed>Password Expires in {$whichUsr->daysuntilpasswordexp}{$daydsp}.</div>" : "<div id=passwordexpireNotice>Password Expires in {$whichUsr->daysuntilpasswordexp}{$daydsp}.</div>";


  $manageMyAccount = <<<MYACCESS
<table border=0>
<tr><td colspan=2 class=usrAccountTitle>Manage My Account</td></tr>
<tr><!-- <td class=profTrayFieldLabel colspan=2>Driver License Expiration</td> //--><td class=profTrayFieldLabel>Present Institution</td></tr>
<tr>
  <!-- <td><input type=text id=profTrayDLExp value="{$whichUsr->drvlicexp}"></td> //-->
  <!-- <td> <table class=tblBtn id=btnUpdateDL style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Save</td></tr></table> </td> //-->
  <td>{$insmnu}</td><td><table class=tblBtn id=btnSetPresentInst style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Set</td></tr></table></td>
</tr>
</table>

<div>
{$expirenotice}<p>
<div id=changePWInstr><b>Instructions</b>: To change your password, click the "Request Code" button.  The server will send you a &laquo;password change code&raquo;.  Use this &laquo;password change code&raquo; along with your CURRENT password, new password and confirm new password fields to change your password for ScienceServer.</div>
<table>
<tr><td class=profTrayFieldLabel>Password Change Code</td></tr>
<tr><td><input type=text id=profTrayResetCode></td><td><table class=tblBtn id=btnPWChangeCodeRequest style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Request Code</td></tr></table></td></tr>
<tr><td class=profTrayFieldLabel>Current Password</td></tr>
<tr><td><input type=password id=profTrayCurrentPW></td></tr>
<tr><td class=profTrayFieldLabel>New Password</td></tr>
<tr><td><input type=password id=profTrayNewPW></td></tr>
<tr><td class=profTrayFieldLabel>Confirm Password</td></tr>
<tr><td><input type=password id=profTrayConfirmPW></td></tr>
<tr><td align=right> <table class=tblBtn id=btnChangeMyPassword style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Change Password</td></tr></table></td></tr>

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
  $allowedModList .= ( $modcnt > 0) ? "<br>{$modval[1]}" : "{$modval[1]}";
  $modcnt++;
}


$myAccessTbl = <<<MYACCESS
<table border=0>
<tr><td colspan=2 class=usrAccountTitle>My Access Allowances</td></tr>
<tr><td colspan=2>
<tr><td class=profTraySideFieldLabel>Procurement</td><td class=dataDisplay2>{$allowprocure}</td></tr>
<tr><td class=profTraySideFieldLabel>Data Coordination</td><td class=dataDisplay2>{$allowdata}</td></tr>
<tr><td class=profTraySideFieldLabel>UPHS PHI</td><td class=dataDisplay2>{$allowphi}</td></tr>
<tr><td class=profTraySideFieldLabel>HPR/QMS</td><td class=dataDisplay2>{$allowhpr}</td></tr>
<tr><td class=profTraySideFieldLabel>Inventory</td><td class=dataDisplay2>{$allowinventory}</td></tr>
<tr><td class=profTraySideFieldLabel>Access </td><td class=dataDisplay2>{$whichUsr->accesslevel}</td></tr>
<tr><td class=profTraySideFieldLabel>Access Nbr</td><td class=dataDisplay2>{$whichUsr->accessnbr}</td></tr>
<tr><td class=profTraySideFieldLabel>Last Access</td><td class=dataDisplay2>{$whichUsr->lastlogin['lastlogdate']}</td></tr>
<tr><td class=profTraySideFieldLabel>Last Access from</td><td class=dataDisplay2>{$whichUsr->lastlogin['fromip']}</td></tr>
<tr><td class=profTraySideFieldLabel valign=top>SS Module List</td><td class=dataDisplay2> {$allowedModList} </td></tr>
<tr><td class=profTraySideFieldLabel valign=top>Allowed Institutions</td><td class=dataDisplay2> {$instList} </td></tr>
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
        if ($ccval['menuvalue'] === $whichUsr->cellcarrierco ) {
          $ccgivendspvalue = $ccval['menuvalue'];
          $ccgivendspcode = $ccval['codevalue'];            
        }
        $ccm .= "<tr><td onclick=\"fillProfTrayField('profTrayCC','{$ccval['codevalue']}','{$ccval['menuvalue']}');\" class=ddMenuItem>{$ccval['menuvalue']}</td></tr>";
    }
    $ccm .= "</table>";
    $ccmnu = "<div class=menuHolderDiv><input type=hidden id=profTrayCCValue value=\"{$ccgivendspcode}\"><input type=text id=profTrayCC READONLY class=\"inputFld\" value=\"{$ccgivendspvalue}\"><div class=valueDropDown id=ddprofTrayCC>{$ccm}</div></div>";

    $ynarr = json_decode(callrestapi("GET", dataTree . "/global-menu/four-yes-no",serverIdent, serverpw), true);
    $ynm = "<table border=0 class=menuDropTbl>";
    $givendspvalue = "";
    $givendspcode = "";
    foreach ($ynarr['DATA'] as $ynval) {
        if ((int)$ynval['codevalue'] === (int)$whichUsr->alternateindirectory) { 
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
</table>

<table border=0>
<tr><td class=profTrayFieldLabel>DBID</td><td class=profTrayFieldLabel>Login ID</td><td class=profTrayFieldLabel>Access Level</td><td class=profTrayFieldLabel>Primary Institution</td></tr>
<tr><td class=dataDisplay valign=top>{$usernbr}</td><td class=dataDisplay valign=top>{$whichUsr->useremail}</td><td class=dataDisplay valign=top>{$whichUsr->accesslevel} / {$whichUsr->accessnbr}</td><td class=dataDisplay valign=top>{$primeinstdsp} ({$whichUsr->primaryinstitution})</td></tr>
<tr><td class=profTrayFieldLabel>Directory Display</td><td class=profTrayFieldLabel>Office Phone</td><td class=profTrayFieldLabel>Dual Authentication Cell</td><td class=profTrayFieldLabel>Cell Carrier</td></tr>
<tr><td style="padding-bottom: 2vh;">{$ynmnu}</td><td valign=top><input type=text id=profTrayOfficePhn value="{$whichUsr->officephone}"></td><td valign=top><input type=text id=profTrayAltPhone value={$whichUsr->alternatephone}></td><td valign=top>{$ccmnu}</tr>
<tr><td class=profTrayFieldLabel colspan=2>Alternate Email</td><td class=profTrayFieldLabel colspan=2>Profile Picture</td></tr>
<tr><td colspan=2><input type=text id=profTrayAltEmail value="{$whichUsr->alternateemail}"></td><td colspan=2><table><tr><td><input type=file id=profTrayProfilePicture accept=".png"></td><td> <table class=tblBtn id=btnClearPicFile style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Clear File</td></tr></table> </td></tr></table><input type=hidden id=profTrayBase64Pic></td></tr>
<tr><td class=profTrayFieldLabel colspan=2>Unlock Code</td><td colspan=2><input type=checkbox id=profTrayProfilePicRemove><label for=profTrayProfilePicRemove>Remove my Profile Picture</label></td></tr>
<tr><td colspan=2><input type=text id=profTrayAltUnlockCode></td><td> <table class=tblBtn id=btnAltUnlockCode style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Get Code</td></tr></table> </td></tr>
<tr><td colspan=2></td><td>
  <table class=tblBtn id=btnSaveAbtMe style="width: 6vw;"><tr><td style="font-size: 1.3vh; text-align: center;">Save</td></tr></table>
</td></tr>
</table>
ABTME;

$profTray = <<<PROFTRAY
  <div id=clsBtnHold>
   <table width=95% border=0>
      <tr><td> 
      <table border=0>
        <tr><td id=profTrayPictureHold><div class="circular--portrait">{$profpic}</div></td>
        <td> {$profDetails}  </td></tr> 
      </table>  
      </td><td valign=top id=closeBtn onclick="openAppCard('appcard_useraccount');" style="width: 1vw;">&times;</td></tr></table>
  </div>   

    <div id=usrAccountDspDiv>
<p>
     <table border=0 id=profTrayControlDivHoldTbl>
       <tr><td style="width: 6vw;">
           <table class=tblBtn id=btnProfileTrayAbtMe style="width: 7vw;" onclick="changeProfControlDiv('AbtMe');"><tr><td style="font-size: 1.3vh; text-align: center;">About Me</td></tr></table>
           </td>
           <td style="width: 6vw;">
           <table class=tblBtn id=btnProfileTrayAccess style="width: 7vw;" onclick="changeProfControlDiv('Access');"><tr><td style="font-size: 1.3vh; text-align: center;">My Access</td></tr></table>
           </td>
           <td style="width: 6vw;"> 
           <table class=tblBtn id=btnProfileTrayManagament style="width: 7vw;white-space: nowrap;" onclick="changeProfControlDiv('Manage');"><tr><td style="font-size: 1.3vh; text-align: center;">Manage Account</td></tr></table>
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

function buildVocabSearch() { 

$rtnthis = <<<VOCABSRCH
<div id=vocsrchHolderDiv>
  <div id=vsclsBtnHold><table width=100%><tr><td></td><td id=closeBtn onclick="openAppCard('appcard_vocabsearch');">&times;</td></tr></table></div>   
  <div id=vocsrchTitle>CHTN Diagnosis-Vocabulary Search</div> 
<table width=100% cellspacing=0 cellpadding=0>
<tr><td class=vocsrchFldLabel>Search Term</td></tr>
<tr><td><input type=text id=vocabSrchTermFld></td></tr>
</table>
<div id=srchVocRsltDisplay>
</div>
</div>
VOCABSRCH;
  return $rtnthis;
}

function buildEnvironTemps() {

$sensorReadings = json_decode(callrestapi("GET", dataTree .  "/chtn-eastern-environmental-metrics", serverIdent, serverpw),true);  
//{"MESSAGE":"","ITEMSFOUND":10,"DATA":{"9633":{"sensorname":"Room 566 Ambient (9633)","readings":[{"utctimestamp":"1551181160","sensorid":"9633","namelabel":"S9633.566.AmbientTemp","readinginc":"22.7","gathertime":"06:39","gatherdate":"02\/26\/2019","dtegathered":"Feb 26th, 2019 :: 06:39 AM"}

$readingsTbl = "<table border=0 cellpadding=0 cellspacing=0 id=sensorDspHolder><tr><td colspan=2 id=sensorNbr>Sensors Read: {$sensorReadings['ITEMSFOUND']}</td></tr><tr>";
$cellCntr = 0;

foreach ( $sensorReadings['DATA'] as $ky => $value) { 
    $lastRead = ""; 
    $innerRow = ""; 
    foreach ( $value['readings'] as $k => $v ) { 

       if ( $lastRead === "" ) { $lastRead = $v['dtegathered']; }  

       $fvalue = sprintf("%1\$.1f&#176; C",$v['readinginc']);
       $ths = floatval( $v['readinginc'] );

       if ( trim($value['readings'][$k + 1]['readinginc']) !== "") { 
         $nxt =   floatval(  $value['readings'][$k + 1]['readinginc'] );
       } else { 
         $nxt = "";
       }
       
       $trendInd = "&nbsp;";
       if ($nxt !== "" ) { 
         if ($ths > $nxt) { $trendInd = "<i class=\"material-icons uparrow\">arrow_upward</i>"; }
         if ($ths < $nxt) { $trendInd = "<i class=\"material-icons uparrow\">arrow_downward</i>"; }
         if ($ths === $nxt) { $trendInd = "<i class=\"material-icons uparrow\">subdirectory_arrow_right</i>"; }
       } 
       
       $innerRow .= "<tr class=rowColor><td class=sensorValue>{$fvalue}  </td><td class=trendIconDsp>{$trendInd}</td><td class=sensorTime>{$v['gathertime']}</td><td class=utcValue> ({$v['utctimestamp']})</td></tr>";    

       
       
       } 

    if ( $cellCntr === 2 ) { $readingsTbl .= "</tr><tr>"; $cellCntr = 0; } 
    $readingsTbl .= "<td valign=top class=holdercell><table border=0 class=sensorMetricTbl><tr><td colspan=4 class=sensorDspName>{$value['sensorname']} </td></tr><tr><td colspan=4 class=lastread>Last Read: {$lastRead}</td></tr>{$innerRow}</table></td>";
    $cellCntr++;
}
$readingsTbl .= "</tr></table>";

$rtnthis = <<<VOCABSRCH
<div id=environHolderDiv>
  <div id=environBtnHold><table width=100%><tr><td></td><td id=envCloseBtn onclick="openAppCard('appcard_environmentals');">&times;</td></tr></table></div>   
  <div id=environmentalTitle>Environmental Monitor Data</div> 
  <div id=environmentalReadingsHolder>
{$readingsTbl}

  </div>
</div>
VOCABSRCH;
  return $rtnthis;    
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

