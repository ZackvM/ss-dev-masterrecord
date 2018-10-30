<?php


class javascriptr {

function globalscripts( $keypaircode, $usrid ) {  

  session_start();
  $sid = session_id();
  $tt = treeTop;
  $ott = ownerTree;
  $dtaTree = dataPath;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = apikey;
  
$rtnThis = <<<JAVASCR

var byId = function( id ) { return document.getElementById( id ); };
var keypair = "{$keypaircode}";
var usrid = "{$usrid}";
var treeTop = "{$tt}";
var mousex;
var mousey;

var httpage = getXMLHTTPRequest();
var httpageone = getXMLHTTPRequest();
function getXMLHTTPRequest() {
try {
req = new XMLHttpRequest();
} catch(err1) {
        try {
	req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(err2) {
                try {
                req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch(err3) {
                  req = false;
                }
        }
}
return req;
}

var key;
function bodyLoad() {
  setMaxDigits(262);
  key = new RSAKeyPair("{$eExpo}","{$eExpo}","{$eMod}",2048);
  var appcards = document.getElementsByClassName('appcard');
  for (var i = 0; i < appcards.length; i++) { 
    //MOVE OTHER CARDS BACK
    byId(appcards[i].id).style.left  = "101vw";
  }
  
}

document.addEventListener('mousemove', function(e) { 
  mousex = e.pageX;
  mousey = e.pageY;
}, false);

document.addEventListener('DOMContentLoaded', function() {  
  bodyLoad();
  byId('standardModalBacker').style.display = 'none';
}, false);

function openOutSidePage(whatAddress) {
    var myRand = parseInt(Math.random()*9999999999999);
    window.open(whatAddress,myRand);
}

function openPageInTab(whichURL) { 
  if (whichURL !== "") {
    window.location.href = whichURL;
  }
}

function navigateSite(whichURL) {
    byId('standardModalBacker').style.display = 'block';
    if (whichURL) {
      window.location.href = treeTop+'/'+whichURL;
    } else {     
      window.location.href = treeTop;
    }
}

function openAppCard(whichcard) { 
  var appcards = document.getElementsByClassName('appcard');
  for (var i = 0; i < appcards.length; i++) { 
     if ( appcards[i].id !== whichcard) { 
        //MOVE OTHER CARDS BACK
        byId(appcards[i].id).style.left  = "101vw";
     }
  }
  
  if ( parseInt(byId(whichcard).style.left) > 100   ) { 
     byId(whichcard).style.left =  "50vw";
  } else { 
     byId(whichcard).style.left =  "101vw";
  }
  
}
 
JAVASCR;
return $rtnThis;    
}

function login($rqstrstr) { 

  session_start();
  $tt = treeTop;
  $ott = ownerTree;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = apikey;

  if(!isset($_COOKIE['ssv7_dualcode'])) {
    $authcode = "";
  } else { 
    $authcode = $_COOKIE['ssv7_dualcode'];      
  }
 
$rtnThis = <<<JAVASCR

var byId = function( id ) { return document.getElementById( id ); };
var treeTop = "{$tt}";
var codeauth = "{$authcode}";

var httpage = getXMLHTTPRequest();
var httpageone = getXMLHTTPRequest();
function getXMLHTTPRequest() {
try {
req = new XMLHttpRequest();
} catch(err1) {
        try {
	req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(err2) {
                try {
                req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch(err3) {
                  req = false;
                }
        }
}
return req;
}

var key;
function bodyLoad() {
  setMaxDigits(262);
  key = new RSAKeyPair("{$eExpo}","{$eExpo}","{$eMod}",2048);
}

document.addEventListener('DOMContentLoaded', function() {  
  bodyLoad();
  byId('standardModalBacker').style.display = 'none';
  if (byId('ssUser')) { 
    byId('ssUser').value = "";
    byId('ssPswd').value = "";
    byId('ssUser').focus();
  }
}, false);

function clearForm() { 
 byId('ssUser').value = "";
 byId('ssPswd').value = "";
 byId('ssUser').focus();
}

function gotoCHTN() {  
  window.location.href = "{$ott}";
}
  
function openPageInTab(whichURL) { 
  if (whichURL !== "") {
    window.location.href = whichURL;
  }
}

function doLogin() { 

  var crd = new Object(); 
  crd['user'] = byId('ssUser').value; 
  crd['pword'] = byId('ssPswd').value; 
  if (byId('ssDualCode')) { 
     crd['dauth'] = byId('ssDualCode').value; 
  } else { 
     //GET COOKIE - DUAL AUTHEN
     //crd['dauth'] = codeauth;
  }
  var cpass = JSON.stringify(crd);
  var ciphertext = window.btoa( encryptedString(key, cpass, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding)); 
     
  var dta = new Object(); 
  dta['ency'] = ciphertext;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$tt}/systemposts/sessionlogin";
  httpage.open("POST",mlURL,true);
  httpage.setRequestHeader("api-token-user","{$si}");
  httpage.setRequestHeader("api-token-key","{$pw}");
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) { 
         location.reload(true);  //TRUE - RELOAD FROM SERVER
      } else { 
         var rcd = JSON.parse(httpage.responseText);
         alert(rcd['MESSAGE']);
      }
  }
  };
  httpage.send(passdata);

}
 
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}  
    
function rqstDualCode() { 
  var dta = new Object(); 
  dta['rqstuser'] = byId('ssUser').value;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$tt}/systemposts/requestdualcode";
  httpage.open("POST",mlURL,true);
  httpage.setRequestHeader("api-token-user","{$si}");
  httpage.setRequestHeader("api-token-key","{$pw}");
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) { 
          alert('If you are a registered ScienceServer user, you will be receiving a dual-authentication access code in your email or by text message.  This code is valid for 30 days');
      } else { 
        var rcd = JSON.parse(httpage.responseText);
        alert(rcd['MESSAGE']);
      }
  }
  };
  httpage.send(passdata);
}
    
JAVASCR;
return $rtnThis;
}

function documentlibrary($rqststr) {     
   
  session_start(); 
    
  $tt = treeTop;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = apikey;
    
$rtnThis = <<<JAVASCR
    
document.addEventListener('DOMContentLoaded', function() {  
if (byId('fSrchTerm')) { 
  byId('fSrchTerm').focus();        
}
}, false);        
        

function searchDocuments() { 
  var dta = new Object(); 
  dta['srchterm'] = byId('fSrchTerm').value;
  dta['doctype'] = byId('fDocTypeValue').value;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$dta}/datadoers/docsearch";
  httpage.open("POST",mlURL,true);        
  httpage.setRequestHeader("api-token-user","{$si}");
  httpage.setRequestHeader("api-token-key","{$pw}"); 
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) {     
        var rcd = JSON.parse(httpage.responseText);
        navigateSite('document-library/docsrch/'+rcd['MESSAGE']);
      } else { 
        var rcd = JSON.parse(httpage.responseText);
        alert(rcd['MESSAGE']);  
      }
      }
    };
    httpage.send(passdata);
}

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    byId(whichfield+'Value').value = whatvalue;    
    byId(whichfield).value = whatplaintext; 
  }
}
  
 
JAVASCR;
return $rtnThis;    
}

function procurementgrid($rqststr) { 

  session_start(); 
    
  $tt = treeTop;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = apikey;
    
$rtnThis = <<<JAVASCR


function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    byId(whichfield+'Value').value = whatvalue;    
    byId(whichfield).value = whatplaintext; 
  }
}

function fillTopDate(whatDate) {
  var dteparts = whatDate.split("/"); 
  byId('fDate').value = whatDate;
  byId('fDateValue').value = dteparts[2]+dteparts[0]+dteparts[1];
}

function getcalendar(whatcalendar,whatmonth,whatyear) { 
  alert('GETTHISCALENDAR '+whatcalendar);


}

JAVASCR;
return $rtnThis;    
}

function datacoordinator($rqststr) { 
    
  session_start(); 
    
  $tt = treeTop;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = apikey;
    
$rtnthis = <<<JAVASCR

function changeSearchGrid(whichgrid) { 
  byId('biogroupdiv').style.display = 'none';
  byId('shipdiv').style.display = 'none';        
  byId('bankdiv').style.display = 'none';        
  byId(whichgrid).style.display = 'block';        
}
        
function fillField(whichfield, whichvalue, whichdisplay) { 
  if (byId(whichfield)) { 
     byId(whichfield).value = whichdisplay; 
     if (byId(whichfield+'Value')) { 
        byId(whichfield+'Value').value = whichvalue;    
     }
  }       
}

function getCalendar(whichcalendar, whichdiv, monthyear) { 
alert(monthyear);        
}

function submitqueryrequest(whichquery) { 
  var dta = new Object();
  switch (whichquery) {
    case 'bankqry':
      dta['qryType'] = 'BANK';
      dta['site'] = byId('bnkSite').value.trim();
      dta['dx'] = byId('bnkDiagnosis').value.trim();
      dta['specimencategory'] = byId('bnkSpecCat').value.trim();
      dta['prepFFPE'] = (byId('bnkPrpFFPE').checked) ? 1 : 0;
      dta['prepFIXED'] = (byId('bnkPrpFixed').checked) ? 1 : 0; 
      dta['prepFROZEN'] = (byId('bnkPrpFrozen').checked) ? 1 : 0;
    break;
    case 'shpqry':
        dta['qryType'] = 'SHIP';
        dta['shipdocnumber'] = byId('shpShipDocNbr').value.trim();
        dta['sdstatus'] = byId('qryShpStatusValue').value.trim();
        dta['sdshipfromdte'] = byId('shpQryFromDateValue').value.trim();
        dta['sdshiptodte'] = byId('shpQryToDateValue').value.trim();
        dta['investigator'] = byId('shpShipInvestigator').value.trim();
    break;    
    default: 
      return null;
  }
  var passdta = JSON.stringify(dta);

  console.log(passdta);
  var mlURL = "{$dta}/datadoers/buildcoordquery";
  httpage.open("POST",mlURL,true);        
  httpage.setRequestHeader("api-token-user","{$si}");
  httpage.setRequestHeader("api-token-key","{$pw}"); 
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) {     
          var rcd = JSON.parse(httpage.responseText);
          navigateSite( rcd['DATA']['qryurl'] );
      } else { 
        var rcd = JSON.parse(httpage.responseText);
        alert(rcd['MESSAGE']);  
      }
      }
    };
    httpage.send(passdta);



}
        
JAVASCR;
    
    return $rtnthis;
}

}

