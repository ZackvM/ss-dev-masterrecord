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
      if (parseInt(dspBacker) < 2) { 
        byId('standardModalBacker').style.display = 'none';
      }
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

function closeSystemDialog() { 
  byId('standardModalBacker').style.display = 'none';
  byId('standardModalDialog').style.display = 'none';
  byId('standardModalDialog').innerHTML = '';
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

function datacoordinator($rqststr) { 
    
  session_start(); 
    
  $tt = treeTop;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = serverpw;
    
$rtnthis = <<<JAVASCR

function openRightClickMenu(whichmenu, whichelementclicked) { 
  if (byId('resultTblContextMenu')) { 
    if (byId('resultTblContextMenu').style.display === 'none' || byId('resultTblContextMenu').style.display === '') {
      byId('clickedElementId').value = whichelementclicked;
      var bg = byId(whichelementclicked).dataset.biogroup;
      var sg = byId(whichelementclicked).dataset.bgslabel;
      byId('EDITBGDSP').innerHTML = "Edit Biogroup "+bg; 
      byId('EDITSEGDSP').innerHTML = "Edit Segment "+sg; 
      byId('resultTblContextMenu').style.left = (mousex - 10) + "px";
      byId('resultTblContextMenu').style.top = (mousey - 10) + "px";
      byId('resultTblContextMenu').style.display = "block";
    } else {
      byId('clickedElementId').value = ""; 
      byId('EDITBGDSP').innerHTML = "Edit Biogroup"; 
      byId('EDITSEGDSP').innerHTML = "Edit Segment"; 
      byId('resultTblContextMenu').style.left = "-999px";
      byId('resultTblContextMenu').style.top = "-999px";
      byId('resultTblContextMenu').style.display = "none";
    }
  }
}    

document.addEventListener('DOMContentLoaded', function() {  

  if (byId('qryBG')) { 
    byId('qryBG').focus();
  }

  if (byId('coordinatorResultTbl')) {   
   document.addEventListener('contextmenu', function(e) { 
      e.preventDefault();
   }, false);
    var rsltRows = document.getElementsByClassName('resultTblLine'); 
    Array.from(rsltRows).forEach(function(element) {
      byId(element.id).addEventListener('contextmenu', function(e) { 
         e.preventDefault();
         openRightClickMenu('resultstable',element.id);
      }, false)   ;       
    });
  }

  if (byId('btnGenBioSearchSubmit')) { 
    byId('btnGenBioSearchSubmit').addEventListener('click', function() {
      //TODO: CHECK FIELDS EXIST
      var dta = new Object();
      var criteriagiven = 0;
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
      dta['requestNbr'] = byId('qryREQ').value.trim(); 
      dta['shipdocnbr'] = byId('qryShpDocNbr').value.trim(); 
      dta['shipdocstatus'] = byId('qryShpStatusValue').value.trim();
      dta['site'] = byId('qryDXDSite').value.trim();
      dta['specimencategory'] = byId('qryDXDSpecimen').value.trim(); 
      dta['phiage'] = byId('phiAge').value.trim(); 
      dta['phirace'] = byId('phiRaceValue').value.trim(); 
      dta['phisex'] = byId('phiSexValue').value.trim(); 
      dta['procType'] = byId('qryProcTypeValue').value.trim(); 
      dta['PrepMethod'] = byId('qryPreparationMethodValue').value.trim(); 
      dta['preparation'] = byId('qryPreparationValue').value.trim(); 
      criteriagiven = 1;
      if (criteriagiven === 1) { 
        var passdta = JSON.stringify(dta);    
        var mlURL = "/data-doers/make-query-request";
        universalAJAX("POST",mlURL,passdta,answerQueryRequest,1);           
      }
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
  }

  if (byId('btnBarAssignSample')) { 
    byId('btnBarAssignSample').addEventListener('click',function() { 
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        var mlURL = "/data-doers/assign-biogroup";
        universalAJAX("POST",mlURL,passdta,answerAssignBG,1);   
      } else { 
        alert(selection['message']);
      }
    }, false );
  }

  if (byId('btnBarSubmitHPR')) { 
    byId('btnBarSubmitHPR').addEventListener('click', function() { 
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        var mlURL = "/data-doers/hpr-status-by-biogroup";
//        universalAJAX("POST",mlURL,passdta,answerSendHPRSubmitOverride,1);   
      } else { 
        alert(selection['message']);
      }
    }, false);
  }

  var fieldinputs = document.querySelectorAll('.inputFld'), i;
  for (i = 0; i < fieldinputs.length; i++) { 
      byId(fieldinputs[i].id).addEventListener('focus', function() { 
         closeAllSuggestions();
      });
  }  
}, false);

function selectorInvestigator() { 

  if (byId('selectorAssignInv').value.trim().length > 3) { 
    getSuggestions('selectorAssignInv',byId('selectorAssignInv').value.trim()); 
  } else { 
    byId('assignInvestSuggestion').innerHTML = "&nbsp;";
    byId('assignInvestSuggestion').style.display = 'none';
  }

}

function gatherSelection() { 
  var responseCode = 400;
  var msg = "";
  var sglist = new Object();
  var returnObj = new Object(); 
  var itemsSelected = 0;
  var cntr = 0;
  if (byId('coordinatorResultTbl')) { 
    for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) {  
      if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected === 'true') { 
        sglist[cntr] = {biogroup:byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.biogroup,bgslabel:byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.bgslabel,segmentid:byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.segmentid};     
        cntr++;
        //if (!inArray(byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.biogroup, bg)) { 
        //bg[bg.length] = byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.biogroup;    
        //}
      }
    }
    itemsSelected = cntr;
    if (itemsSelected > 0) { 
      responseCode = 200;
    } else { 
      msg = 'You haven\'t selected any samples';
    }
  } else { 
      msg = 'The \'Result\' table doesn\'t have any listed samples.  Run a valid query before performing actions.';    
  }
  returnObj['responseCode'] = responseCode;
  returnObj['message'] = msg;
  returnObj['itemsSelect'] = itemsSelected;
  returnObj['selectionListing'] = sglist;
  return returnObj; 
}

function howManyResultsSelected() { 
    var countr = 0;        
    for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) { 
        if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected === 'true') { 
          countr++; 
        }
    }
   return countr;        
}

function getSuggestions(whichfield, passedValue) { 
switch (whichfield) { 
  case 'qryInvestigator':
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-invest'; 
    given['given'] = byId(whichfield).value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerInvestSuggestions,0);
  break;
  case 'selectorAssignInv':
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-invest';  
    given['given'] = byId(whichfield).value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerAssignInvestSuggestions,2);
  break;
}
}

function closeAllSuggestions() { 
    byId('investSuggestion').innerHTML = "&nbsp;";
    byId('investSuggestion').style.display = 'none';
}

function setAssignsRequests() {
  if (byId('selectorAssignInv').value.trim() !== "" ) { 
    buildRequestDrop(byId('selectorAssignInv').value.trim());
  } 
}

function buildRequestDrop(whichinvestigator) { 
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-requests'; 
    given['given'] = whichinvestigator;
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
}

function answerRequestDrop(rtnData) { 
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    //{"MESSAGE":0,"ITEMSFOUND":10,"DATA":[{"requestid":"REQ15262"},{"requestid":"REQ17321"},{"requestid":"REQ20137"},{"requestid":"REQ19002"},{"requestid":"REQ21130"},{"requestid":"REQ21131"},{"requestid":"REQ22758"},{"requestid":"REQ22757"},{"requestid":"REQ23034"}]}
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=1>";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td onclick=\"fillField('selectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); byId('requestDropDown').innerHTML = '&nbsp;';\">"+element['requestid']+"</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
  }
}

function answerAssignInvestSuggestions(rtnData) { 
var rsltTbl = "";
if (parseInt(rtnData['responseCode']) === 200 ) { 
  var dta = JSON.parse(rtnData['responseText']);
  if (parseInt( dta['ITEMSFOUND'] ) > 0 ) { 
    var rsltTbl = "<table border=0 class=suggestionTable><tr><td colspan=2>Below are suggestions for the investigator field. Use the investigator's ID.  These are live values from CHTN's TissueQuest. Found "+dta['ITEMSFOUND']+" matches.</td></tr>";
    dta['DATA'].forEach(function(element) { 
       rsltTbl += "<tr class=suggestionDspLine onclick=\"fillField('selectorAssignInv','"+element['investvalue']+"','"+element['investvalue']+"'); byId('assignInvestSuggestion').innerHTML = '&nbsp;'; byId('assignInvestSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
    }); 
    rsltTbl += "</table>";  
    byId('assignInvestSuggestion').innerHTML = rsltTbl; 
    byId('assignInvestSuggestion').style.display = 'block';
  } else { 
    byId('assignInvestSuggestion').innerHTML = "&nbsp;";
    byId('assignInvestSuggestion').style.display = 'none';
  }
}
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
        
function answerAssignBG(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("ASSIGNMENT ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY ASSIGNMENT CREATOR
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}
               
function updatePrepmenu(whatvalue) { 
  byId('qryPreparationValue').value = '';
  byId('qryPreparation').value = '';
  byId('preparationDropDown').innerHTML = "&nbsp;"; 
  if (whatvalue.trim() === "") { 
  } else { 
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

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}        
        
function rowselector(whichrow) { 
  if (byId(whichrow)) { 
    if (byId(whichrow).dataset.selected === "false") { 
          //SELECT
          byId(whichrow).dataset.selected = "true";
        } else { 
          //DESELECT
          byId(whichrow).dataset.selected = "false";
        }
  }
}
        
function answerSendHPRSubmitOverride(rtnDta) { 
   console.log(rtnDta);    
}

function sendSegmentAssignment(typeofassign) { 
  var dta = new Object(); 
  dta['segmentlist'] = byId('dialogBGSListing').value;
  switch(typeofassign) { 
    case 'invest':
      dta['investigatorid'] = byId('selectorAssignInv').value;
      dta['requestnbr'] = byId('selectorAssignReq').value;
      break;
    case 'bank':
      dta['investigatorid'] = 'BANK';
      dta['requestnbr'] = '';
      break;
  }

  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/assign-segments";
  universalAJAX("POST",mlURL,passdata,answerSendSegmentAssignment,2);
}      

function answerSendSegmentAssignment(rtnData) { 
  console.log(rtnData);

}

JAVASCR;
    
return $rtnthis;
}

}

