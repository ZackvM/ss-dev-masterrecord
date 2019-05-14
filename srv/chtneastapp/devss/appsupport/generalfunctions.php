<?php

function testerfunction() { 
return "RETURN THIS VALUE";
}
 
function getBrowserZack($u_agent) {

    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 

function get_browser_name($user_agent) {
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    return 'Other';
}

function getipinformation($ip) { 
    //$ws = file_get_contents("http://ipinfo.io/{$ip}/json?token=6043850d53cbb6");
   $ws = file_get_contents("http://ip-api.com/json/{$ip}");
   return $ws;
}
 
function checkPostingUser($usrname, $passwrd) { 
    $responseCode = 401;  //UNAUTHORIZED 
    if ($usrname === serverIdent) { 
      //CHECK SERVER CREDENTIALS
      if ( cryptservice( $passwrd , 'd' ) === servertrupw ) { 
          $responseCode = 200;
      }
    } else { 
      

      //CHECK CODE IN DATABASE   
      require(serverkeys . "/sspdo.zck"); 
      //TODO: MAKE THIS AWEBSERVICE
      $chkSQL = "SELECT sessid, accesscode FROM serverControls.ss_srvIdents where sessid = :usrsess and datediff(now(), onwhen) < 1";  
      $rs = $conn->prepare($chkSQL); 
      $rs->execute(array(':usrsess' => $usrname));
      if ((int)$rs->rowCount() > 0) { 
          $r = $rs->fetch(PDO::FETCH_ASSOC);
          if ( cryptservice( $passwrd, 'd', true, $usrname ) === $usrname ) { 
            $responseCode = 200;
          }
      }
      
      
    }
    return $responseCode;
}

function registerServerIdent($sesscode) {     
   $idcode = generateRandomString(20); 
   require(serverkeys . "/sspdo.zck");  
   $delSQL = "delete FROM serverControls.ss_srvIdents where sessid = :sessioncode";
   $dr = $conn->prepare($delSQL); 
   $dr->execute(array(':sessioncode' => $sesscode));
   $insSQL = "insert into serverControls.ss_srvIdents (sessid, accesscode, onwhen) values(:sesscode , :accesscode , now())";
   $iR = $conn->prepare($insSQL); 
   $iR->execute(array(':sesscode' => $sesscode, ':accesscode' => $idcode)); 
   return cryptservice($sesscode,'e',true,$sesscode);  
}

function cryptservice( $string, $action = 'e', $usedbkey = false, $passedsid = "") {
    $output = false;
    require( serverkeys . "/serverid.zck");
    if ($usedbkey) {
        session_start(); 
        $sid = (trim($passedsid) === "") ? session_id() : $passedsid;
        require(serverkeys . "/sspdo.zck");
        $sql = "select accesscode from serverControls.ss_srvIdents where sessid = :sid";
        $rs = $conn->prepare($sql); 
        $rs->execute(array(':sid' => $sid));
        if ($rs->rowCount() < 1) { 
            exit(); 
        } else { 
          $r = $rs->fetch(PDO::FETCH_ASSOC);
        }
        $localsecretkey = $r['accesscode']; 
        $secret_key = $localsecretkey;
        $secret_iv = $localsecretkey;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $localsecret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if ( $action == 'e' ) {
          $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        if( $action == 'd' ) {
          $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
    } else { 
      $secret_key = $secretkey;
      $secret_iv = $siv;
      $encrypt_method = "AES-256-CBC";
      $key = hash( 'sha256', $secret_key );
      $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
      if ( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
      }
      if( $action == 'd' ) {
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
      }
    }
    return $output;
}

function chtnencrypt($pdata) {  
  $publicCert = openssl_pkey_get_public("file:///" . serverkeys . "/pubkey.pem");
  openssl_public_encrypt($pdata, $crypted, $publicCert);
  $crypted = base64_encode($crypted);  
  return "{$crypted}";
}

function chtndecrypt($pdata) {   
  $encMsg = base64_decode($pdata); 
  $privateKey = openssl_pkey_get_private("file:///" . serverkeys . "/privatekey.pem");     
  //openssl_pkey_export($privateKey, $pkeyout);  TO TEST AND OUTPUT PRIVATE KEY - TESTING AND REFERENCE ONLY
  openssl_private_decrypt($encMsg, $decrypted, $privateKey); 
  return $decrypted; 
}

function callrestapi($method, $url, $user = "", $apikeyencrypt = "", $data = false) { 
  try {
    $ch = curl_init(); 
    if (FALSE === $ch) { return Exception('failed to initialize'); } 
    switch ($method) { 
      case "POST": 
        curl_setopt($ch, CURLOPT_POST, 1); 
        if ($data) { 
          curl_setopt($ch,CURLOPT_POSTFIELDS, $data); 
        }
      break; 
      case "GET": 
        curl_setopt($ch, CURLOPT_GET, 1); 
      break;
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $headers = array(
        'Content-Type:application/json'
       ,'Authorization: Basic '. base64_encode("{$user}:{$apikeyencrypt}")
    );  //ADD AUTHORIZATION HEADERS HERE
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $content = curl_exec($ch);
    if (FALSE === $content) { 
      return Exception(curl_error($ch),curl_errno($ch));
    } else {
      return $content;
    }
  } catch(Exception $e) { 
    return sprintf('CURL failed with error #%d: %s', $e->getCode(), $e->getMessage()); 
  } 
}

function base64file($path, $identifier, $expectedObject, $createObject = true, $additionals = "") { 
  $object = NULL;    
  if (!file_exists($path) || !is_file($path)) {
  } else { 
    ob_start(); 
    readfile($path);
    $filecontent = base64_encode(ob_get_clean()); 
    if ($createObject) { 
      $mime = mime_content_type($path);
      switch ($expectedObject) { 
        case "image": 
          $object = "<img id=\"{$identifier}\" src=\"data:{$mime};base64,{$filecontent}\" {$additionals}>";
        break;
        case "png":
          $object = "<img id=\"{$identifier}\" src=\"data:image/png;base64,{$filecontent}\" {$additionals}>";
        break;
        case "pdf":
          //NOT YET DONE
            $object = "<object style=\"width: 100%; height: 100%;\" data=\"data:application/pdf;base64,{$filecontent}\" type=\"application/pdf\" class=\"internal\" {$additionals} >  <embed  style=\"width: 100%; height: 100%;\" src=\"data:application/pdf;base64,{$filecontent}\"  type=\"application/pdf\" {$additionals} >";
        break;
        case "pdfhlp":
          //NOT YET DONE
            $object = "<object style=\"width: 100%; height: 75vh;\" data=\"data:application/pdf;base64,{$filecontent}\" type=\"application/pdf\" class=\"internal\" {$additionals} >  <embed  style=\"width: 100%; height: 100%;\" src=\"data:application/pdf;base64,{$filecontent}\"  type=\"application/pdf\" {$additionals} >";
        break;        
        case "favicon": 
          $object = "<link href=\"data:image/x-icon;base64,{$filecontent}\" rel=\"icon\" type=\"image/x-icon\" {$additionals}>";
        break;
        case "js":
          $object = "<script type=\"text/javascript\" src=\"data:text/javascript;base64,{$filecontent}\" {$additionals}></script>";
          break;
        case "bgurl":
          $object = " url('data:{$mime};base64,{$filecontent}') ";
          break; 
        case 'gif':
            $object = "<img id=\"{$identifier}\" src=\"data:{$mime};base64,{$filecontent}\" {$additionals}>";            
            break;
        default:
          $object = "<img id=\"{$identifier}\" src=\"data:{$mime};base64,{$filecontent}\" {$additionals}>";
      } 
    } else { 
      $object = $filecontent;
    }
  }    
  return $object;
}

function checkCURL() { 
   if  (in_array  ('curl', get_loaded_extensions())) {
        return "CURL is available on your web server";
    }  else {
        return "CURL is not available on your web server";
    }
}

function newzackdropdownelement($definitionArray) { 
    //Array = array("dropDivId" => "mnuDspDivInstitution", "fieldId" => "prcInstitution", "fieldName" => "prcInstitution", "fieldClassName" => "zackmenuinput", "fieldAddStyle" => "width: 15vw;", "readOnlyInd" => 1, "fieldPlaceHolder" => "Procuring Institution", "divClassName" => "zackdspmenu fllDivDsp", "divValueTbl" => $mnuTbl, "defaultValue" => "")
 
   $addStyle = (trim($definitionArray['fieldAddStyle']) !== "" ) ? " style=\"{$definitionArray['fieldAddStyle']}\" " : "";
   $RDOnly = ((int)$definitionArray['readOnlyInd'] === 1) ? " READONLY " : "";
   $plcHld = (trim($definitionArray['fieldPlaceHolder']) !== "") ? " placeholder = '{$definitionArray['fieldPlaceHolder']}' " : "";
   $divValues = (trim($definitionArray['divValueTbl']) !== "") ? $definitionArray['divValueTbl'] : "";
   $dftVal = (trim($definitionArray['defaultValue']) !== "") ? " value=\"{$definitionArray['defaultValue']}\" " : "";
   $clickAction = (trim($definitionArray['clickAction']) !== "") ? $definitionArray['clickAction'] : " dspDropMenu('{$definitionArray['dropDivId']}'); ";

   $elementRtn = "<div class=zackdropmenuholder><div class=zackdspvalue><table><tr><td><span class=inputwrapper onclick=\"byId('{$definitionArray['fieldId']}').value = '';{$clickAction}\"><input type=text name='{$definitionArray['fieldName']}' id='{$definitionArray['fieldId']}' class='{$definitionArray['fieldClassName']}' {$addStyle} {$RDOnly} {$plcHld} {$dftVal}></span></td></tr></table></div><div class=\"{$definitionArray['divClassName']}\" id=\"{$definitionArray['dropDivId']}\">{$divValues}</div></div>";
   return $elementRtn;
} 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function guidv4() {
    if (function_exists('com_create_guid') === true) { return trim(com_create_guid(), '{}');  }
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function getColor($num) {
    $hash = md5('color' . $num); // modify 'color' to get a different palette
    return array(hexdec(substr($hash, 0, 2)), hexdec(substr($hash, 2, 2)), hexdec(substr($hash, 4, 2))); 
}

function buildcalendar($whichcalendar, $pmonth, $pyear ) { 

if (trim($pmonth) === "") { $pmonth = date('m'); }
if (trim($pyear) === "") { $pyear = date('Y'); }
$daysofweek = array('Su','M','T','W','Th','F','Sa'); 
$firstDayOfMonth = mktime(0,0,0,$pmonth,1,$pyear);
$numberOfDays = date('t',$firstDayOfMonth);
$dateComponents = getdate($firstDayOfMonth);
$monthName = $dateComponents['month'];
$monthNbr = str_pad($dateComponents['mon'],2,"0", STR_PAD_LEFT);
$dayOfWeek = $dateComponents['wday'];
$dyr = $dateComponents['wday'];
$lastMonth = substr(('00'.((int)$pmonth - 1)), -2);
$lastYear = $pyear; 
if ((int)$lastMonth === 0) { $lastMonth = 12; $lastYear = ($lastYear - 1); }
$nextMonth = substr(('00'.((int)$pmonth + 1)), -2);
$nextYear = $pyear; 
if ((int)$nextMonth === 13) { $nextMonth = '01'; $nextYear = ((int)$nextYear + 1); }

//******FORMATTING
    switch (strtolower($whichcalendar)) {
     case 'mainroot':
        $daysofweek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'); 
        $calTblId = "mainRootTbl";
        $leftBtnId = "mainRootLeftCtl";
        $calTitle = "mainRootCalTitle";
        $rightBtnId = "mainRootRightCtl";
        $dayHeadClass = "mainRootCalHeadDay";
        $topSpacer = "mainRootTopSpacer";
        $btmSpacer = "mainRootBtmSpacer";
        $daySquare = "mnuMainRootDaySquare";
        $btmLine = "mainRootBtmLine";
        $calendarClass = "ddMainRootMenuCalendar";
        $calTitleClass = "ddMainRootMenuCalTitle";
        $topBarClass = "ddMainRootMenuCalTopRow";
        $topCtlBtnClass= "smallMainRootCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('mainroot','mainRootCalendar','{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('mainroot','mainRootCalendar','{$nextMonth}/{$nextYear}');\" ";         

        
        $chkToday = date('m/d/Y');
        //TODO:  CHECK THAT THE OVERLOAD ELEMENTS CONTAIN VALUES
        $fn = func_get_arg(3);
        $em = func_get_arg(4);  
        $ls = func_get_arg(5); 
        //SELECT * FROM four.sys_master_menus where menu = 'EVENTTYPE' and dspind = 1
        //[{"date":{"day":1,"month":1,"year":2019,"dayOfWeek":2},"name":[{"lang":"en","text":"New Year's Day"}],"holidayType":"public_holiday"}]
        $pbholidayws = json_decode(callrestapi("GET","https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForMonth&month={$pmonth}&year={$pyear}&country=us&region=pa&holidayType=public_holiday"),true);   ///GET ALL HOLIDAYS FOR A MONTH IN US/PA
    
        
        $pubholiday = array();
        foreach ( $pbholidayws as $pbk => $pbv ) { 
            $pubholiday[$pbv['date']['month']][(int)$pbv['date']['day'] ] = array("type" => "PUBLICHOLIDAY", "name" => $pbv['name'][0]['text']);
            $tdyEventList .= ( (int)date('d') == (int)$pbv['date']['day'] ) ? "&raquo; {$pbv['name'][0]['text']}" : "";
        }
        $dspChkToday =  "<div id=saluations>Hi {$fn}<br>Today is " . date('l, jS \of F, Y') . ". Here's what's happening today:</div>";
        
        break;
     case 'procedureprocurequery':
        $calTblId = "pqcTbl";
        $leftBtnId = "pqcLeft";
        $calTitle = "pqcTitle";
        $rightBtnId = "pqcRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $btmSpacer = "btmSpacer";
        $daySquare = "mnuDaySquare";
        $btmLine = "pqcBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('procedureprocurequery','procureProcedureCalendar','{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('procedureprocurequery','procureProcedureCalendar','{$nextMonth}/{$nextYear}');\" "; 
        break;        
     case 'procquery':
        $calTblId = "pqcTbl";
        $leftBtnId = "pqcLeft";
        $leftBtnAction = " onclick=\"alert('LEFT');\" ";
        $calTitle = "pqcTitle";
        $rightBtnId = "pqcRight";
        $rightBtnAction = " onclick=\"alert('RIGHT');\" ";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $btmSpacer = "btmSpacer";
        $daySquare = "mnuDaySquare";
        $btmLine = "pqcBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getcalendar('procquery','{$lastMonth}','{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getcalendar('procquery','{$nextMonth}','{$nextYear}');\" "; 
        break;
     case 'cgriddatecontrol':
        $calTblId = "bsqTbl";
        $leftBtnId = "bsqLeft";
        $calTitle = "bsqTitle";
        $rightBtnId = "bsqRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "bsqBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        //TODO - FIX THE GETTER HERE
        $leftBtnAction  = " onclick=\"getCalendar('cGridDateControl','cGridCalendar', '{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('cGridDateControl','cGridCalendar' ,'{$nextMonth}/{$nextYear}');\" ";         
        break;           
     case 'biosamplequeryfrom':
        $calTblId = "bsqTbl";
        $leftBtnId = "bsqLeft";
        $calTitle = "bsqTitle";
        $rightBtnId = "bsqRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "bsqBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('biosampleQueryFrom','bsqCalendar', '{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('biosampleQueryFrom','bsqCalendar' ,'{$nextMonth}/{$nextYear}');\" ";         
        break;   
     case 'biosamplequeryto':
        $calTblId = "bstqTbl";
        $leftBtnId = "bstqLeft";
        $calTitle = "bstqTitle";
        $rightBtnId = "bstqRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "bstqBtmLine";          
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar', '{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar' ,'{$nextMonth}/{$nextYear}');\" ";       
        break;
     case 'shipactual':
        $calTblId = "shpSDCTbl";
        $leftBtnId = "shpFromLeft";
        $calTitle = "shpFromTitle";
        $rightBtnId = "shpFromRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "shpFromBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('shipactual','sdShpCalendar', '{$lastMonth}/{$lastYear}',2);\" ";
        $rightBtnAction = " onclick=\"getCalendar('shipactual','sdShpCalendar' ,'{$nextMonth}/{$nextYear}',2);\" ";  
        break;
     case 'shipsdcfrom':
        $calTblId = "shpSDCTbl";
        $leftBtnId = "shpFromLeft";
        $calTitle = "shpFromTitle";
        $rightBtnId = "shpFromRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "shpFromBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('shipSDCFrom','rShpCalendar', '{$lastMonth}/{$lastYear}',2);\" ";
        $rightBtnAction = " onclick=\"getCalendar('shipSDCFrom','rShpCalendar' ,'{$nextMonth}/{$nextYear}',2);\" ";  
        break;
     case 'shipsdctolab':
        $calTblId = "shpSDCTbl";
        $leftBtnId = "shpFromLeft";
        $calTitle = "shpFromTitle";
        $rightBtnId = "shpFromRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "shpFromBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('shipSDCToLab','rToLabCalendar', '{$lastMonth}/{$lastYear}',2);\" ";
        $rightBtnAction = " onclick=\"getCalendar('shipSDCToLab','rToLabCalendar' ,'{$nextMonth}/{$nextYear}',2);\" ";  
        break;
     case 'shipbsqfrom':
        $calTblId = "shpFromTbl";
        $leftBtnId = "shpFromLeft";
        $calTitle = "shpFromTitle";
        $rightBtnId = "shpFromRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "shpFromBtmLine";
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('shipBSQFrom','shpfCalendar', '{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('shipBSQFrom','shpfCalendar' ,'{$nextMonth}/{$nextYear}');\" ";  
        break;
     case 'shipbsqto':
        $calTblId = "shpToTbl";
        $leftBtnId = "shpToLeft";
        $calTitle = "shpToTitle";
        $rightBtnId = "shpToRight";
        $dayHeadClass = "ddCalHeadDay";
        $topSpacer = "topSpacer";
        $daySquare = "mnuDaySquare";
        $btmSpacer = "btmSpacer";
        $btmLine = "shpToBtmLine";          
        $calendarClass = "ddMenuCalendar";
        $calTitleClass = "ddMenuCalTitle";
        $topBarClass = "ddMenuCalTopRow";
        $topCtlBtnClass= "smallCtlBtn";
        $leftBtnAction  = " onclick=\"getCalendar('shipBSQTo','shptCalendar', '{$lastMonth}/{$lastYear}');\" ";
        $rightBtnAction = " onclick=\"getCalendar('shipBSQTo','shptCalendar' ,'{$nextMonth}/{$nextYear}');\" ";  
        break;
   default: 
      $calTblId = "GENERALCALENDAR";
      $leftBtnId = "GENERALLEFTBTN";
      $leftBtnAction = " onclick=\"alert('LEFT');\" ";
      $calTitle = "GENERALTITLE";
      $rightBtnId = "GENERALRIGHTBTN";
      $rightBtnAction = " onclick=\"alert('RIGHT');\" ";
      $dayHeadClass = "ddCalHeadDay";
      $topSpacer = "topSpacer";
      $daySquare = "mnuDaySquare";
      $btmSpacer = "btmSpacer";
      $btmLine = "GENERALBTMLINE";
      $calendarClass = "ddMenuCalendar";
      $calTitleClass = "ddMenuCalTitle";
      $topBarClass = "ddMenuCalTopRow";
      $topCtlBtnClass= "smallCtlBtn";
      $leftBtnAction  = "  ";
      $rightBtnAction = "  "; 
    }


//************************** BUILD THE CALENDAR *******************

$rtnthiscalendar = <<<CALSTRT
   <table border=0 cellspacing=0 cellpadding=0 id="{$calTblId}" class="{$calendarClass}">
     <tr class="{$topBarClass}">
       <td id="{$leftBtnId}"{$leftBtnAction}><i class="material-icons {$topCtlBtnClass}">keyboard_arrow_left</i></td>
       <td colspan=5 id="{$calTitle}" class="{$calTitleClass}">{$monthName} {$pyear}</td>
       <td id="{$rightBtnId}" align=right><i class="material-icons {$topCtlBtnClass}" {$rightBtnAction}>keyboard_arrow_right</i></td>
    </tr>
CALSTRT;

$rtnthiscalendar .= "<tr>";
$dcnt = 0;
foreach ($daysofweek as $day) { 
  $endcell = "";
  if ( $dcnt === 0 ) { $endcell = " starterHeadCell"; }
  if ( $dcnt === 6 ) { $endcell = " endHeadCell"; }


  $rtnthiscalendar .= "<th class=\"{$dayHeadClass}{$endcell}\">{$day}</th>";
  $dcnt++;
}
$rtnthiscalendar .= "</tr>";
$rtnthiscalendar .= "<tr>";

if ($dayOfWeek > 0) { 
    $rtnthiscalendar .= "<td colspan='{$dayOfWeek}' class=\"{$topSpacer}\">&nbsp;</td>";
}
$currentDay = 1; 
while ($currentDay <= $numberOfDays) {

   if ($dayOfWeek === 7) { 
     $rtnthiscalendar .= "</tr><tr>";
     $dayOfWeek = 0;
   }

   $currentDayDsp = str_pad($currentDay,2,"00",STR_PAD_LEFT);
   
    //INDIVIDUAL BUTTON ACTION HERE
    switch (strtolower($whichcalendar)) {
         case 'procedureprocurequery':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('fldPRCProcedureDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = $currentDayDsp;
         //         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('fldPRCProcedureDate','','');\" ><center>[clear]</td></tr>";         
         $btmLineDsp = "<tr><td colspan=7></td></tr>";          
         break;
         case 'procquery':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\"fillTopDate('{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = "<table><tr><td>{$currentDayDsp}</td></tr></table>";
         $btmLineDsp = "";
         break;
         case 'biosamplequeryfrom':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('bsqueryFromDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('bsqueryFromDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'cgriddatecontrol':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('cGridDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         //$btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('bsqueryToDate','','');\" ><center>[clear]</td></tr>";
         $btmLineDsp = "";
         break;
         case 'biosamplequeryto':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('bsqueryToDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('bsqueryToDate','','');\" ><center>[clear]</td></tr>";
         break;    
         case 'shipactual':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('sdcActualShipDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('sdcActualShipDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'shipsdcfrom':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('sdcRqstShipDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('sdcRqstShipDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'shipsdctolab':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('sdcRqstToLabDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('sdcRqstToLabDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'shipbsqfrom':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('shpQryFromDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('shpQryFromDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'shipbsqto':     
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('shpQryToDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "<tr><td colspan=7 class=calBtmLineClear onclick=\" fillField('shpQryToDate','','');\" ><center>[clear]</td></tr>";
         break;
         case 'mainroot':

         $caldayeventlistdsp = "";
         if ( array_key_exists( (int)$currentDayDsp , $pubholiday[(int)$monthNbr]) ) { 
           $caldayeventlistdsp = $pubholiday[(int)$monthNbr][(int)$currentDayDsp]['name'];
         }  
             
          if ( "{$monthNbr}/{$currentDayDsp}/{$pyear}" === $chkToday ) {

            //THIS IS TODAY  
            $daySquare = ( $dayOfWeek === 0 ) ? "mnuMainRootDaySquare calendarEndDay todayDsp" : "mnuMainRootDaySquare todayDsp";
            $dayDsp = <<<DAYDSP
<div class=caldayeventholder>
  <div class="caldayday caldaytoday">{$currentDayDsp}</div>
  {$caldayeventlistdsp}
</div>
DAYDSP;
          } else { 
            //THIS IS NOT TODAY  
            $daySquare = ( $dayOfWeek === 0 ) ? "mnuMainRootDaySquare calendarEndDay" : "mnuMainRootDaySquare";
            $dayDsp = <<<DAYDSP
<div class=caldayeventholder>
  <div class=caldayday>{$currentDayDsp}</div>
  {$caldayeventlistdsp}
</div>
DAYDSP;

          }

          $sqrID = "daySqr{$currentDayDsp}";
          $action = " onclick=\"alert('{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";

          //TODO:  THIS IS WHERE THE CURRENT DAY SQUARE DISPLAY GOES
          
          
          
          $btmLineDsp = "<tr><td colspan=7 id={$btmLine}><div id=mainRootTodayActivityDsp>{$dspChkToday}</div></td></tr>";
         break;
       default: 
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\"alert('{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "";
    }
//INDIVIDUAL BUTTON ACTION END
   $rtnthiscalendar .= "<td class=\"{$daySquare}\" id=\"{$sqrID}\" {$action} valign=top>{$dayDsp}</td>";
   $currentDay++;
   $dayOfWeek++;
}
if ($dayOfWeek !== 7) { 
  $remainingDays = (7 - $dayOfWeek); 
  $rtnthiscalendar .= "<td colspan={$remainingDays} class=\"{$btmSpacer}\">&nbsp;</td>";
}
$rtnthiscalendar .= "</tr>";
$rtnthiscalendar .= "{$btmLineDsp}";
$rtnthiscalendar .= "</table>";
//****************************** END BUILD CALENDAR *********************
return $rtnthiscalendar;    
}

function putPicturesInHelpText( $hlpTxt ) { 
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
    $at = genAppFiles;
    //$pattern = '/\bPICTURE:[^*?"<>|:.]{0,}\.[A-Za-z]{3}\b/'; ex.: PICTURE:pathname.threechar-ext
    $pattern = '/\bPICTURE:(\{.{1,}\})/';
    preg_match_all($pattern,$hlpTxt,$outlist,PREG_PATTERN_ORDER); 
    //$o = $outlist[0][0];
    if ( count($outlist[0]) > 0 ) { 
      foreach ($outlist[0] as $pictDef) {
        $pictureDef = preg_replace('/\bPICTURE:/','',$pictDef);
        $pd = json_decode( $pictureDef , true); 
        //BUILD THE PICTURE
        //{"picturefile": "help/elproDiagram.png","type":"png","useid":"pictElproDiagram","width":"5vw","height":"5vh"}
        $picfile = ( array_key_exists('picturefile',$pd) ) ? "{$pd['picturefile']}" : "";
        $pictype = ( array_key_exists('type', $pd) ) ? $pd['type'] : "";
        $useid = ( array_key_exists('useid', $pd) ) ? $pd['useid'] : "";
        $additionals  = " style = \""; 
        $additionals .= ( array_key_exists('width', $pd) ) ? "width: {$pd['width']};" : "";
        $additionals .= ( array_key_exists('height', $pd) ) ?  "height: {$pd['height']};" : ""; 
        $additionals .= "\"";
        $picturecode = base64file("{$at}/publicobj/{$picfile}", $useid, $pictype, true, $additionals);
        $divstyle = ( array_key_exists('holdingdivstyle',$pd) ) ? " style = \"position: relative; display: inline-block; {$pd['holdingdivstyle']}\"" : " style = \"position: relative; display: inline-block; \"";   
        $caption = ( array_key_exists('caption', $pd) ) ? $pd['caption'] : "";
        $picturedsp = "<div class=helpPictureDisplay {$divstyle}>{$picturecode}<div class=helppicturecaption>{$caption}</div></div>";
        $hlpTxt = str_replace($pictDef,$picturedsp,$hlpTxt);
      }
    }
    return $hlpTxt;
}

function putPicturesInPrintHelpText( $hlpTxt ) { 
/*
 * NOTETOZACK: TO ADD PICTURES TO THE HELP FILE EMBED A JSON STRING INTO THE DATABASE FILE AS BELOW:
 * PICTURE:{"picturefile": "help/elproDiagram.png","type":"png","useid":"pictElproDiagram","width":"10vw", "caption":"elpro monitor diagram", "holdingdivstyle":"float: left; margin-right: 10px; margin-bottom: 10px;" , "printwidth":"150px" , "printheight" : "100px;"}  
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
    $at = genAppFiles;
    //$pattern = '/\bPICTURE:[^*?"<>|:.]{0,}\.[A-Za-z]{3}\b/'; ex.: PICTURE:pathname.threechar-ext
    $pattern = '/\bPICTURE:(\{.{1,}\})/';
    preg_match_all($pattern,$hlpTxt,$outlist,PREG_PATTERN_ORDER); 
    //$o = $outlist[0][0];
    if ( count($outlist[0]) > 0 ) { 
      foreach ($outlist[0] as $pictDef) {
        $pictureDef = preg_replace('/\bPICTURE:/','',$pictDef);
        $pd = json_decode( $pictureDef , true); 
        //BUILD THE PICTURE
        //{"picturefile": "help/elproDiagram.png","type":"png","useid":"pictElproDiagram","width":"5vw","height":"5vh"}
        $picfile = ( array_key_exists('picturefile',$pd) ) ? "{$pd['picturefile']}" : "";
        $pictype = ( array_key_exists('type', $pd) ) ? $pd['type'] : "";
        $useid = ( array_key_exists('useid', $pd) ) ? $pd['useid'] : "";
        $additionals  = " style = \""; 
        $additionals .= ( array_key_exists('printwidth', $pd) ) ? "width: {$pd['printwidth']};" : "";
        $additionals .= ( array_key_exists('printheight', $pd) ) ?  "height: {$pd['printheight']};" : ""; 
        $additionals .= "\"";
        $picturecode = base64file("{$at}/publicobj/{$picfile}", $useid, $pictype, true, $additionals);
        $divstyle = ( array_key_exists('holdingdivstyle',$pd) ) ? " style = \"position: relative; display: inline-block; {$pd['holdingdivstyle']}\"" : " style = \"position: relative; display: inline-block; \"";   
        $caption = ( array_key_exists('caption', $pd) ) ? $pd['caption'] : "";
        $picturedsp = "<div class=helpPictureDisplay {$divstyle}>{$picturecode}<div class=helppicturecaption>{$caption}</div></div>";
        $hlpTxt = str_replace($pictDef,$picturedsp,$hlpTxt);
      }
    }
    return $hlpTxt;
}
