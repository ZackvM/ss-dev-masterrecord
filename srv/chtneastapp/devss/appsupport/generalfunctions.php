<?php

function testerfunction() { 
return "RETURN THIS VALUE";
}
 
function getipinformation($ip) { 
    //$ws = file_get_contents("http://ipinfo.io/{$ip}/json?token=6043850d53cbb6");
   $ws = file_get_contents("http://ip-api.com/json/{$ip}");
   return $ws;
}
 
function checkPostingUser($usrname, $passwrd) { 
    $responseCode = 401;  //UNAUTHORIZED 
    session_start();
    if ($usrname === serverIdent) { 
      //CHECK SERVER CREDENTIALS
      if ( cryptservice( $passwrd , 'd' ) === servertrupw ) { 
          $responseCode = 200;
      }
    } else { 
      //CHECK CODE IN DATABASE   
      require(serverkeys . "/sspdo.zck");  
      $chkSQL = "SELECT sessid, accesscode FROM serverControls.ss_srvIdents where sessid = :usrsess and datediff(now(), onwhen) < 1";  
      $rs = $conn->prepare($chkSQL); 
      $rs->execute(array(':usrsess' => $usrname));
      if ((int)$rs->rowCount() > 0) { 
          $r = $rs->fetch(PDO::FETCH_ASSOC);
      }
      if (cryptservice($passwrd,'d',true, $usrname) === $usrname) { 
         $responseCode = 200;
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
  $publicCert = openssl_pkey_get_public("file:///" . genAppFiles . "/accessfiles/publickey.pem");
  openssl_public_encrypt($pdata, $crypted, $publicCert);
  $crypted = base64_encode($crypted);  
  return "{$crypted}";
}

function chtndecrypt($pdata) {   
  $encMsg = base64_decode($pdata); 
  $privateKey = openssl_pkey_get_private("file:///" . genAppFiles . "/accessfiles/privatekey.pem");      
  openssl_private_decrypt($encMsg, $decrypted, $privateKey); 
  return "{$decrypted}"; 
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

    $headers = array("api-token-user:{$user}","api-token-key:{$apikeyencrypt}");  //ADD AUTHORIZATION HEADERS HERE
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
        case "favicon": 
          $object = "<link href=\"data:image/x-icon;base64,{$filecontent}\" rel=\"icon\" type=\"image/x-icon\" {$additionals}>";
        break;
        case "js":
          $object = "<script type=\"text/javascript\" src=\"data:text/javascript;base64,{$filecontent}\" {$additionals}></script>";
          break;
        case "bgurl":
          $object = " url('data:{$mime};base64,{$filecontent}') ";
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

function barcode( $filepath="", $text="0", $size="20", $orientation="horizontal", $code_type="code128", $print=false, $SizeFactor=1 ) {
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

function buildcalendar($whichcalendar, $month, $pyear) { 

//******FORMATTING
    switch ($whichcalendar) {
      case 'procquery':
        $calTblId = "pqcTbl";
        $leftBtnId = "pqcLeft";
        $leftBtnAction = " onclick=\"alert('LEFT');\" ";
        $calTitle = "pqcTitle";
        $rightBtnId = "pqcRight";
        $rightBtnAction = " onclick=\"alert('RIGHT');\" ";
        $dayHeadClass = "pqcDayHead";
        $topSpacer = "pqcTopSpacer";
        $daySquare = "pqcDaySqr";
        $btmSpacer = "pqcBtmSpacer";
        $daySquare = "pqcDaySqr";
        $btmLine = "pqcBtmLine";
        break;
      case 'biosampleQueryFrom':
        $calTblId = "bsqTbl";
        $leftBtnId = "bsqLeft";
        $calTitle = "bsqTitle";
        $rightBtnId = "bsqRight";
        $dayHeadClass = "bsqDayHead";
        $topSpacer = "bsqTopSpacer";
        $daySquare = "bsqDaySqr";
        $btmSpacer = "bsqBtmSpacer";
        $daySquare = "bsqDaySqr";
        $btmLine = "bsqBtmLine";
        break;   
      case 'biosampleQueryTo':
        $calTblId = "bstqTbl";
        $leftBtnId = "bstqLeft";
        $calTitle = "bstqTitle";
        $rightBtnId = "bstqRight";
        $dayHeadClass = "bstqDayHead";
        $topSpacer = "bstqTopSpacer";
        $daySquare = "bstqDaySqr";
        $btmSpacer = "bstqBtmSpacer";
        $daySquare = "bstqDaySqr";
        $btmLine = "bstqBtmLine";          
         break;
      case 'shipBSQFrom':
        $calTblId = "shpFromTbl";
        $leftBtnId = "shpFromLeft";
        $calTitle = "shpFromTitle";
        $rightBtnId = "shpFromRight";
        $dayHeadClass = "shpFromDayHead";
        $topSpacer = "shpFromTopSpacer";
        $daySquare = "shpFromDaySqr";
        $btmSpacer = "shpFromBtmSpacer";
        $daySquare = "shpFromDaySqr";
        $btmLine = "shpFromBtmLine";
        break;
        case 'shipBSQTo':
        $calTblId = "shpToTbl";
        $leftBtnId = "shpToLeft";
        $calTitle = "shpToTitle";
        $rightBtnId = "shpToRight";
        $dayHeadClass = "shpToDayHead";
        $topSpacer = "shpToTopSpacer";
        $daySquare = "shpToDaySqr";
        $btmSpacer = "shpToBtmSpacer";
        $daySquare = "shpToDaySqr";
        $btmLine = "shpToBtmLine";          
          break;
   default: 
      $calTblId = "GENERALCALENDAR";
      $leftBtnId = "GENERALLEFTBTN";
      $leftBtnAction = " onclick=\"alert('LEFT');\" ";
      $calTitle = "GENERALTITLE";
      $rightBtnId = "GENERALRIGHTBTN";
      $rightBtnAction = " onclick=\"alert('RIGHT');\" ";
      $dayHeadClass = "GENERALDAYHEADCLASS";
      $topSpacer = "GENERALTOPSPACER";
      $daySquare = "GENERALDAYQUARE";
      $btmSpacer = "GENERALBTMSPACER";
      $daySquare = "GENERALDAYSQAURE";
      $btmLine = "GENERALBTMLINE";
    }


//************************** BUILD THE CALENDAR *******************
if (trim($month) === "") { 
  $month = date('m');
}
if (trim($pyear) === "") { 
  $pyear = date('Y');
}

$daysofweek = array('S','M','T','W','Th','F','Sa'); 
$firstDayOfMonth = mktime(0,0,0,$month,1,$year);
$numberOfDays = date('t',$firstDayOfMonth);
$dateComponents = getdate($firstDayOfMonth);
$monthName = $dateComponents['month'];
$monthNbr = str_pad($dateComponents['mon'],2,"0", STR_PAD_LEFT);
$dayOfWeek = $dateComponents['wday'];
$lastMonth = substr(('00'.((int)$month - 1)), -2);
$lastYear = $pyear; 
if ((int)$lastMonth === 0) { 
  $lastMonth = 12; 
  $lastYear = ($lastYear - 1);
}
$nextMonth = substr(('00'.((int)$month + 1)), -2);
$nextYear = $pyear; 
if ((int)$nextMonth === 13) { 
  $nextMonth = '01';
  $nextYear = ((int)$nextYear + 1);
}

switch ($whichcalendar) {
  case 'procquery':
    $leftBtnAction  = " onclick=\"getcalendar('procquery','{$lastMonth}','{$lastYear}');\" ";
    $rightBtnAction = " onclick=\"getcalendar('procquery','{$lastMonth}','{$lastYear}');\" "; 
    break;
    case 'biosampleQueryFrom':
    $leftBtnAction  = " onclick=\"getCalendar('biosampleQueryFrom','bsqCalendar', '{$lastMonth}/{$lastYear}');\" ";
    $rightBtnAction = " onclick=\"getCalendar('biosampleQueryFrom','bsqCalendar' ,'{$nextMonth}/{$nextYear}');\" ";         
    break;
  case 'biosampleQueryTo':
    $leftBtnAction  = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar', '{$lastMonth}/{$lastYear}');\" ";
    $rightBtnAction = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar' ,'{$nextMonth}/{$nextYear}');\" ";       
    break;
   case 'shipBSQFrom':
    //$leftBtnAction  = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar', '{$lastMonth}/{$lastYear}');\" ";
    //$rightBtnAction = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar' ,'{$nextMonth}/{$nextYear}');\" ";  
    break;
    case 'shipBSQTo':
    //$leftBtnAction  = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar', '{$lastMonth}/{$lastYear}');\" ";
    //$rightBtnAction = " onclick=\"getCalendar('biosampleQueryTo','bsqtCalendar' ,'{$nextMonth}/{$nextYear}');\" ";  
    break;        
  default: 
    $leftBtnAction  = "  ";
    $rightBtnAction = "  "; 
}

$rtnthiscalendar = "<table border=0 cellspacing=0 cellpadding=0 id=\"{$calTblId}\"><tr><td id=\"{$leftBtnId}\"><i class=\"material-icons\" {$leftBtnAction}>keyboard_arrow_left</i></td><td colspan=5 id=\"{$calTitle}\">{$monthName} {$pyear}</td><td id=\"{$rightBtnId}\"><i class=\"material-icons\" {$rightBtnAction}>keyboard_arrow_right</i></td></tr>";
$rtnthiscalendar .= "<tr>";
foreach ($daysofweek as $day) { 
  $rtnthiscalendar .= "<th class=\"{$dayHeadClass}\">{$day}</th>";
}
$rtnthiscalendar .= "</tr>";
$rtnthiscalendar .= "<tr>";
if ($dayOfWeek > 0) { 
    $rtnthiscalendar .= "<td colspan='{$dayOfWeek}' id=\"{$topSpacer}\">&nbsp;</td>";
}
$currentDay = 1; 
while ($currentDay <= $numberOfDays) { 
   if ($dayOfWeek === 7) { 
     $rtnthiscalendar .= "</tr><tr>";
     $dayOfWeek = 0;
   }
   $currentDayDsp = str_pad($currentDay,2,"00",STR_PAD_LEFT);
//INDIVIDUAL BUTTON ACTION HERE
    switch ($whichcalendar) {
       case 'procquery':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\"fillTopDate('{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = "<table><tr><td>{$currentDayDsp}</td></tr></table>";
         $btmLineDsp = "";
         break;
         case 'biosampleQueryFrom':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('bsqueryFromDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "&nbsp;";
         break;
         case 'biosampleQueryTo':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('bsqueryToDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "&nbsp;";
         break;     
         case 'shipBSQFrom':
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('shpQryFromDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "&nbsp;";            
         break;
         case 'shipBSQTo':     
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\" fillField('shpQryToDate','{$pyear}-{$monthNbr}-{$currentDayDsp}','{$monthNbr}/{$currentDayDsp}/{$pyear}'); \" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "&nbsp;";     
         break;
       default: 
         $sqrID = "daySqr{$currentDayDsp}";
         $action = " onclick=\"alert('{$monthNbr}/{$currentDayDsp}/{$pyear}');\" ";
         $dayDsp = $currentDayDsp;
         $btmLineDsp = "&nbsp;";
    }
//INDIVIDUAL BUTTON ACTION END
   $rtnthiscalendar .= "<td class=\"{$daySquare}\" id=\"{$sqrID}\" {$action} valign=top>{$dayDsp}</td>";
   $currentDay++;
   $dayOfWeek++;
}
if ($dayOfWeek !== 7) { 
  $remainingDays = (7 - $dayOfWeek); 
  $rtnthiscalendar .= "<td colspan={$remainingDays} id=\"{$btmSpacer}\">&nbsp;</td>";
}
$rtnthiscalendar .= "</tr>";
$rtnthiscalendar .= "<tr><td colspan=7 id=\"{$btmLine}\">{$btmLineDsp}</td></tr>";
$rtnthiscalendar .= "</table>";
//****************************** END BUILD CALENDAR *********************
return $rtnthiscalendar;    
}

/*
 * BACKUP FUNCTIONS
 *
function calltidal($method, $url, $data = false) { 
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

    $headers = array("tidal-user:Y2EvMkFPaGxNbTkyRDJuWVVhUGFsdz09");  //ADD AUTHORIZATION HEADERS HERE
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

*
*
 */
