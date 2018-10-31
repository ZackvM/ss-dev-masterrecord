<?php


class javascriptr {

function globalscripts( $keypaircode, $usrid ) {  

  session_start();
  $sid = session_id();
  $tt = treeTop;
  $ott = ownerTree;
  $dtaTree = dataTree;
  $eMod = eModulus;
  $eExpo = eExponent;
  $si = serverIdent;
  $pw = serverpw;
  
  //LOCAL USER CREDENTIALS BUILT HERE
  $regUsr = session_id();  
  $regCode = registerServerIdent($regUsr);  

$rtnThis = <<<JAVASCR

var byId = function( id ) { return document.getElementById( id ); };
var treeTop = "{$tt}";
var dataPath = "{$dtaTree}";
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

function universalAJAX(methd, url, passedDataJSON, callbackfunc) { 
//  byId('standardModalBacker').style.display = 'block';
    var rtn = new Object();
    var grandurl = dataPath+"/"+url;
    console.log(grandurl);
//  httpage.open(methd, grandurl, true); 
//  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
//  httpage.onreadystatechange = function() { 
//    if (httpage.readyState === 4) { 
//      rtn['responseCode'] = httpage.status;
//      rtn['responseText'] = httpage.responseText; 
//      //byId('standardModalBacker').style.display = 'none';
//      callbackFunc(JSON.stringify(rtn));
//    }
//  };
//  httpage.send(passedDataJSON);
}

function bodyLoad() {
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
  $si = serverIdent;
  $sp = serverpw;

  //LOCAL USER CREDENTIALS BUILT HERE
  $regUsr = session_id();  
  $regCode = registerServerIdent($regUsr);  

  $eMod = eModulus; 
  $eExpo = eExponent;  

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
   key = new RSAKeyPair("{$eExpo}","{$eExpo}","{$eMod}", 2048);
}

document.addEventListener('DOMContentLoaded', function() { 
  bodyLoad(); 
  if (byId('standardModalBacker')) { 
    byId('standardModalBacker').style.display = 'none';
  }
  if (byId('ssUser')) { 
    byId('ssUser').value = "";
    byId('ssPswd').value = "";
    byId('ssUser').focus();
  }
}, false);

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
  var ciphertext = window.btoa( encryptedString(key, cpass, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) ); 
  var dta = new Object(); 
  dta['ency'] = ciphertext;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$tt}/data-services/system-posts/session-login";
  httpage.open("POST",mlURL,true);
  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
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

document.addEventListener('DOMContentLoaded', function() {  

  if (byId('btnBankSearchSubmit')) { 
    byId('btnBankSearchSubmit').addEventListener('click', function() {
      submitqueryrequest('bankqry');
    }, false);
  }





}, false);

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
  var mlURL = "/buildcoordquery";

  universalAJAX("POST",mlURL,passdta,rspquerysubmital);
//          var rcd = JSON.parse(httpage.responseText);
//          navigateSite( rcd['DATA']['qryurl'] );
}

function rspquerysubmital(jsonDta) { 
  console.log("CALL BACK COMPLETE");
}

JAVASCR;
    
    return $rtnthis;
}

}

