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

function universalAJAX(methd, url, passedDataJSON, callbackfunc, dspBacker) { 
  if (dspBacker === 1) { 
    byId('standardModalBacker').style.display = 'block';
  }
  var rtn = new Object();
  var grandurl = dataPath+url;
  httpage.open(methd, grandurl, true); 
  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
  httpage.onreadystatechange = function() { 
    if (httpage.readyState === 4) { 
      rtn['responseCode'] = httpage.status;
      rtn['responseText'] = httpage.responseText; 
      byId('standardModalBacker').style.display = 'none';
      callbackfunc(rtn);
    }
  };
  httpage.send(passedDataJSON);
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

  if (byId('btnSndAuthCode')) { 
    byId('btnSndAuthCode').addEventListener('click', function() {
      rqstDualCode();
    }, false);
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

function rqstDualCode() { 
  var dta = new Object(); 
  dta['rqstuser'] = byId('ssUser').value;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$tt}/data-services/system-posts/request-dualcode";
  httpage.open("POST",mlURL,true);
  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) { 
          alert('If you are a registered ScienceServer user, you will receive a dual-authentication access code in your email or by text message.  This code is valid for 30 days');
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

  if (byId('btnSearchDocuments')) { 
    byId('btnSearchDocuments').addEventListener('click', function() {
      searchDocuments();
    }, false);
  }

}, false);        
        
function searchDocuments() { 
  var dta = new Object(); 
  dta['srchterm'] = byId('fSrchTerm').value;
  dta['doctype'] = byId('fDocTypeValue').value;
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/doc-search";
  universalAJAX("POST",mlURL,passdata,answerSearchDocuments,1);
}

function answerSearchDocuments(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    navigateSite('document-library/doc-srch/'+rcd['MESSAGE']);
  } else { 
    var rcd = JSON.parse(rtnData['responseText']);
    alert(rcd['MESSAGE']);  
  }
}

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    byId(whichfield+'Value').value = whatvalue;    
    byId(whichfield).value = whatplaintext; 
  }
}

function getDocumentText(selector) { 
  var dta = new Object(); 
  dta['documentid'] = selector;
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/document-text";
  universalAJAX("POST",mlURL,passdata,answerGetDocumentText,1);
}
  
function answerGetDocumentText(rtnData) { 
  var dspThis = "";
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse(rtnData['responseText']);
    switch (dta['DATA'][0]['doctype']) { 
      case 'PATHOLOGYRPT':
        dspThis = "<table border=0 width=100% cellspacing=0 cellpadding=0><tr><td id=docPRHeader>PATHOLOGY REPORT FOR BIOGROUP "+dta['DATA'][0]['dnpr_nbr']+"</td><td align=right id=iconHold><table onclick=\"alert('NOT FUNCTIONAL AS OF THIS RELEASE');\"><tr><td><i class=\"material-icons\">edit</i></td></tr></table></td></tr>";
        dspThis += "<tr><td colspan=2 id=documentDisplay>"+dta['DATA'][0]['pathreport']+"</td></tr>";
        dspThis += "</table>";
      break; 
      case 'CHARTRVW':
        dspThis = "<table border=0 width=100% cellspacing=0 cellpadding=0><tr><td id=docPRHeader>CHART REVIEW: "+('000000'+dta['DATA'][0]['chartid']).substr(-6)+"</td><td align=right id=iconHold><table onclick=\"alert('NOT FUNCTIONAL AS OF THIS RELEASE');\"><tr><td><i class=\"material-icons\">edit</i></td></tr></table></td></tr>";
        dspThis += "<tr><td colspan=2 id=documentDisplay>"+dta['DATA'][0]['chart']+"</td></tr>";
        dspThis += "<tr><td colspan=2 align=right id=pxiBottomLine>pxi-id: "+dta['DATA'][0]['pxi']+"</td></tr>";
        dspThis += "</table>";
      break;
    }
    if (byId('displayDocText')) { 
     byId('displayDocText').innerHTML = dspThis; 
    }
  } else { 
    //alert(rtnData['responseText']['MESSAGE']);
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
  $pw = serverpw;
    
$rtnthis = <<<JAVASCR
        

document.addEventListener('DOMContentLoaded', function() {  

  if (byId('qryBG')) { 
    byId('qryBG').focus();
  }

  if (byId('btnGenBioSearchSubmit')) { 
    byId('btnGenBioSearchSubmit').addEventListener('click', function() {
      submitqueryrequest('bioqry');
    }, false);
  }

  if (byId('qryInvestigator')) { 
    byId('qryInvestigator').addEventListener('keyup', function() {
      if (byId('qryInvestigator').value.trim().length > 3) { 
          getSuggestions('qryInvestigator'); 
      } else { 
        byId('investSuggestion').innerHTML = "&nbsp;";
        byId('investSuggestion').style.display = 'none';
      }
    }, false);
//    byId('qryInvestigator').addEventListener('blur', function() {  
//        byId('investSuggestion').innerHTML = "&nbsp;";
//        byId('investSuggestion').style.display = 'none';
//    }, false );
  }

  var fieldinputs = document.querySelectorAll('.inputFld'), i;
  for (i = 0; i < fieldinputs.length; i++) { 
      byId(fieldinputs[i].id).addEventListener('focus', function() { 
         closeAllSuggestions();
      });
  }
  
}, false);

function getSuggestions(whichfield) { 
switch (whichfield) { 
  case 'qryInvestigator':
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-invest'; 
    given['given'] = byId(whichfield).value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerInvestSuggestions,0);
  break;
}
}

function closeAllSuggestions() { 
    byId('investSuggestion').innerHTML = "&nbsp;";
    byId('investSuggestion').style.display = 'none';
}

function answerInvestSuggestions(rtnData) { 
var rsltTbl = "";
if (parseInt(rtnData['responseCode']) === 200 ) { 
  var dta = JSON.parse(rtnData['responseText']);
  if (parseInt( dta['ITEMSFOUND'] ) > 0 ) { 
    var rsltTbl = "<table border=0 class=suggestionTable><tr><td colspan=2>Below are suggestions for the investigator field. Use the investigator's ID.  These are live values from CHTN's TissueQuest. Found "+dta['ITEMSFOUND']+" matches.</td></tr>";
    dta['DATA'].forEach(function(element) { 
       rsltTbl += "<tr class=suggestionDspLine onclick=\"fillField('qryInvestigator','"+element['investvalue']+"','"+element['investvalue']+"'); byId('investSuggestion').innerHTML = '&nbsp;'; byId('investSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
    }); 
    rsltTbl += "</table>";  
    byId('investSuggestion').innerHTML = rsltTbl; 
    byId('investSuggestion').style.display = 'block';
  } else { 
    byId('investSuggestion').innerHTML = "&nbsp;";
    byId('investSuggestion').style.display = 'none';
  }
}
}

function clearCriteriaGrid() { 
  var fieldinputs = document.querySelectorAll('input'), i;
  for (i = 0; i < fieldinputs.length; i++) { 
      //console.log(i+") "+fieldinputs[i].id);
      byId(fieldinputs[i].id).value = "";
  }
}

function fillField(whichfield, whichvalue, whichdisplay) { 
  if (byId(whichfield)) { 
     byId(whichfield).value = whichdisplay; 
     if (byId(whichfield+'Value')) { 
        byId(whichfield+'Value').value = whichvalue;    
     }
  }       
}

var lastRequestCalendarDiv = "";
function getCalendar(whichcalendar, whichdiv, monthyear) {
var mlURL = "/sscalendar/"+whichcalendar+"/"+monthyear;
lastRequestCalendarDiv = whichdiv;
universalAJAX("GET",mlURL,"",answerGetCalendar,0);
}

function answerGetCalendar(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    byId(lastRequestCalendarDiv).innerHTML = rcd['DATA']; 
  } else { 
    alert("ERROR");  
  }
}

function submitqueryrequest(whichquery) { 
  var dta = new Object();
  var criteriagiven = 0;
  switch (whichquery) {
    case 'bioqry':
      dta['qryType'] = 'BIO';
      dta['BG'] = byId('qryBG').value.trim(); 
      dta['procInst'] = byId('qryProcInstValue').value.trim(); 
      dta['segmentStatus'] = byId('qrySegStatusValue').value.trim(); 
      dta['qmsStatus'] = byId('qryHPRStatusValue').value.trim(); 
      dta['procDateFrom'] = byId('bsqueryFromDateValue').value.trim();
      dta['procDateTo'] = byId('bsqueryToDateValue').value.trim();
      dta['shipDateFrom'] = byId('shpQryFromDateValue').value.trim();  
      dta['shipDateTo'] = byId('shpQryToDateValue').value.trim();  
      dta['investigatorCode'] = byId('qryInvestigator').value.trim(); 
      dta['shipdocnbr'] = byId('qryShpDocNbr').value.trim(); 
      dta['shipdocstatus'] = byId('qryShpStatusValue').value.trim();
      dta['site'] = byId('qryDXDSite').value.trim();
      dta['specimencategory'] = byId('qryDXDSpecimen').value.trim(); 
      dta['PrepMethod'] = byId('qryPreparationMethodValue').value.trim(); 
      dta['preparation'] = byId('qryPreparationValue').value.trim(); 
      criteriagiven = 1;
    break; 
    default: 
      return null;
  }
  if (criteriagiven === 1) { 
    var passdta = JSON.stringify(dta);    
    //console.log(passdta);        
    var mlURL = "/data-doers/make-query-request";
    universalAJAX("POST",mlURL,passdta,answerQueryRequest,1);           
  }
}

function answerQueryRequest(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var rsp = JSON.parse(rtnData['responseText']); 
    alert("* * * * ERROR * * * * \\n\\n"+rsp['MESSAGE']);
  } else { 
    //Redirect to results
    var rsp = JSON.parse(rtnData['responseText']); 
    navigateSite("data-coordinator/"+rsp['DATA']['coordsearchid']);
  }        
}
               
function updatePrepmenu(whatvalue) { 
  byId('qryPreparationValue').value = '';
  byId('qryPreparation').value = '';
  byId('preparationDropDown').innerHTML = "&nbsp;"; 
  if (whatvalue.trim() === "") { 
  } else { 
     //WEBSERVICE END POINT: https://dev.chtneast.org/data-services/subpreparationmenu/frozen
     var mlURL = "/sub-preparation-menu/"+whatvalue;
     universalAJAX("GET",mlURL,"",answerUpdatePrepmenu,0);
  }
}

function answerUpdatePrepmenu(rtnData) { 
  byId('qryPreparationValue').value = '';
  byId('qryPreparation').value = '';
  byId('preparationDropDown').innerHTML = "&nbsp;"; 
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    if (parseInt( dta['ITEMSFOUND'] ) > 0 ) { 
    rsltTbl = "<table border=1><tr><td align=right onclick=\"fillField('qryPreparation','','');\">[clear]</td></tr>";
    dta['DATA'].forEach(function(element) { 
      rsltTbl += "<tr><td onclick=\"fillField('qryPreparation','"+element['menuValue']+"','"+element['longValue']+"');\">"+element['longValue']+"</td></tr>";
    }); 
    rsltTbl += "</table>";  
    byId('preparationDropDown').innerHTML = rsltTbl; 
    }
  }
}


JAVASCR;
    
    return $rtnthis;
}

}

