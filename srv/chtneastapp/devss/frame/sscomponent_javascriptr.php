<?php

class javascriptr {

function root( $rqststr ) { 
    
    $rtnThis = <<<RTNTHIS

var lastRequestCalendarDiv = "";
function getCalendar(whichcalendar, whichdiv, monthyear, modalCtl = 0) {        
  var obj = new Object(); 
  obj['whichcalendar'] = whichcalendar; 
  obj['monthyear'] = monthyear;  
  var passeddata = JSON.stringify(obj);
  var mlURL = "/data-doers/front-sscalendar";            
  lastRequestCalendarDiv = whichdiv;      
  universalAJAX("POST",mlURL,passeddata,answerGetCalendar,modalCtl);
}

function answerGetCalendar(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    byId(lastRequestCalendarDiv).innerHTML = rcd['DATA']; 
  } else { 
    alert("ERROR");  
  }
}             
            
RTNTHIS;
    return $rtnThis;
}    

function paymenttracker ( $rqststr) { 

  session_start(); 
  $tt = treeTop;
  $ott = ownerTree;
  $si = serverIdent;
  $sp = serverpw;

  $rtnThis = <<<JAVASCR

function displayPaymentDetail ( whichdetail ) { 
  if (byId('detailDiv'+whichdetail)) { 
    if (byId('detailDiv'+whichdetail).style.display === 'none' || byId('detailDiv'+whichdetail).style.display.trim() === '' ) { 
      byId('detailDiv'+whichdetail).style.display = 'block';
    } else { 
      byId('detailDiv'+whichdetail).style.display = 'none';
    }
  }
}



JAVASCR;
return $rtnThis;

}

function biogroupdefinition ( $rqststr ) { 

  session_start(); 
  $tt = treeTop;
  $ott = ownerTree;
  $si = serverIdent;
  $sp = serverpw;


$rtnThis = <<<JAVASCR

function rowselector(whichrow) { 
  if (byId(whichrow)) { 
    if (byId(whichrow).dataset.selected === "false") { 
          byId(whichrow).dataset.selected = "true";
        } else { 
          byId(whichrow).dataset.selected = "false";
        }
  }
}

JAVASCR;
return $rtnThis;
}
    
function globalscripts ( $keypaircode, $usrid ) {  

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

function universalAJAXStreamTwo(methd, url, passedDataJSON, callbackfunc, dspBacker) { 
  if (dspBacker === 1) { 
    byId('standardModalBacker').style.display = 'block';
  }
  var rtn = new Object();
  var grandurl = dataPath+url;
  httpageone.open(methd, grandurl, true); 
  httpageone.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
  httpageone.onreadystatechange = function() { 
    if (httpageone.readyState === 4) { 
      rtn['responseCode'] = httpageone.status;
      rtn['responseText'] = httpageone.responseText;
      if (parseInt(dspBacker) < 2) { 
        byId('standardModalBacker').style.display = 'none';
      }
      callbackfunc(rtn);
    }
  };
  httpageone.send(passedDataJSON);
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

var key;  
function bodyLoad() {
  var appcards = document.getElementsByClassName('appcard');
  for (var i = 0; i < appcards.length; i++) { 
    //MOVE OTHER CARDS BACK
    byId(appcards[i].id).style.left  = "101vw";
  }
   setMaxDigits(262);
   key = new RSAKeyPair("{$eExpo}","{$eExpo}","{$eMod}", 2048);
}

document.addEventListener('mousemove', function(e) { 
  mousex = e.pageX;
  mousey = e.pageY;
}, false);

document.addEventListener('DOMContentLoaded', function() {  
  bodyLoad();
  byId('standardModalBacker').style.display = 'none';

  if ( byId('btnAltUnlockCode') ) { 
    byId('btnAltUnlockCode').addEventListener('click', function() { 
      var mlURL = "/data-doers/get-alternate-unlock-code";
      universalAJAX("POST",mlURL,"",voidfunction,1);
      alert('If you have a valid ScienceServer Account, you will receive an email with a change code.  You will need this code to change security information.  The code will expire in 5 hours.');
    }, false);
  }
 
  if (byId('btnPWChangeCodeRequest')) { 
    byId('btnPWChangeCodeRequest').addEventListener('click', function() { 
      var mlURL = "/data-doers/get-pw-change-code";
      universalAJAX("POST",mlURL,"",voidfunction,1);
      alert('If you have a valid ScienceServer Account, you will receive an email with a change code.  You will need this code to change security information.  The code will expire in 5 hours.');
    }, false);
  }
  
  if (byId('profTrayProfilePicture')) { 
     byId('profTrayProfilePicture').addEventListener('change', function() {        
      var reader = new FileReader();
      reader.readAsDataURL(byId('profTrayProfilePicture').files[0]);
      reader.onload = function () {
        byId('profTrayBase64Pic').value = reader.result;
        byId('profTrayProfilePicRemove').checked = false;
      };
      reader.onerror = function (error) {
         console.log('Error: ', error);
      };             
      }, false);
  }

  if (byId('btnChangeMyPassword')) { 
    byId('btnChangeMyPassword').addEventListener('click', function() { 
      var crd = new Object(); 
      crd['currentpassword'] = byId('profTrayCurrentPW').value.trim(); 
      crd['newpassword'] = byId('profTrayNewPW').value.trim(); 
      crd['confirmpassword'] = byId('profTrayConfirmPW').value.trim(); 
      crd['changecode'] = byId('profTrayResetCode').value.trim(); 
      var cpass = JSON.stringify(crd);
      var ciphertext = window.btoa( encryptedString(key, cpass, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) ); 
      var dta = new Object(); 
      dta['ency'] = ciphertext;
      var passdata = JSON.stringify(dta);
      //console.log(passdata);
      var mlURL = "/data-doers/update-my-password";
      universalAJAX("POST",mlURL,passdata,answerUpdateMyPassword,1);   
    }, false);
  }
  
  if (byId('btnSaveAbtMe')) { 
    byId('btnSaveAbtMe').addEventListener('click', function() { 
      var dta = new Object(); 
      dta['directorydisplay'] = byId('profTrayDisplayAltYNValue').value; 
      dta['officephone'] = byId('profTrayOfficePhn').value; 
      dta['alternatephone'] = byId('profTrayAltPhone').value;    
      dta['cellcarrier'] = byId('profTrayCCValue').value;             
      dta['alternateemail'] = byId('profTrayAltEmail').value;   
      dta['unlockcode'] = byId('profTrayAltUnlockCode').value;   
      dta['base64picture'] = byId('profTrayBase64Pic').value;  
      dta['removeprofilepic'] = byId('profTrayProfilePicRemove').checked; 
      var passdata = JSON.stringify(dta);
      //console.log(passdata);
      var mlURL = "/data-doers/update-about-me";
      universalAJAX("POST",mlURL,passdata,answerUpdateAboutMe,1);   
   }, false); 
  }

  if (byId('btnSetPresentInst')) { 
    byId('btnSetPresentInst').addEventListener('click', function() { 
      var dta = new Object();
      dta['requestedInstitution'] = byId('profTrayPresentInstValue').value.trim();
      var passdata = JSON.stringify(dta); 
      var mlURL = "/data-doers/update-my-present-institution";
      universalAJAX("POST",mlURL,passdata,answerUpdateMyPresentInstitution,1);   
    }, false);
  }

  if (byId('btnUpdateDL')) { 
    byId('btnSetPresentInst').addEventListener('click', function() { 
      alert('set Drivers License Expire');
    }, false);
  }

  if (byId('btnClearPicFile')) {
    byId('btnClearPicFile').addEventListener('click', function() { 
        byId('profTrayBase64Pic').value = '';
        byId('profTrayProfilePicture').value = null;
    }, false);
  }

  if (byId('profTrayProfilePicRemove')) { 
    byId('profTrayProfilePicRemove').addEventListener('change', function() { 
      if ( byId('profTrayProfilePicRemove').checked === true) { 
        byId('profTrayBase64Pic').value = '';
        byId('profTrayProfilePicture').value = null;
      } 
     }, false);
  }

  if (byId('vocabSrchTermFld')) { 
    byId('vocabSrchTermFld').addEventListener('keyup', function() {
      if (byId('vocabSrchTermFld').value.trim().length > 2) { 
        var obj = new Object(); 
        obj['srchterm'] = byId('vocabSrchTermFld').value.trim();
        var passdta = JSON.stringify(obj); 
        var mlURL = "/data-doers/search-vocabulary-terms";
        universalAJAX("POST",mlURL,passdta,answerSearchVocabulary,0);
      } else { 
        byId('srchVocRsltDisplay').innerHTML = "";
      }
    }, false);
  }
  
}, false);


function answerSearchVocabulary(rtnData) { 
  if (byId('srchVocRsltDisplay')) {
    if ( parseInt(rtnData['responseCode']) === 200 ) {
      var tbl = JSON.parse( rtnData['responseText'] ); 
      byId('srchVocRsltDisplay').innerHTML = tbl['DATA'];
    }
  }
}

function answerUpdateMyPresentInstitution(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("PRESENT INSTITUTION UPDATE ERROR:\\n"+dspMsg);
   } else {
      alert("Your present institution location has been changed."); 
      openAppCard('appcard_useraccount');
      byId('standardModalBacker').style.display = 'block';
      location.reload(true);
   }        
}

function answerUpdateMyPassword(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("PASSWORD UPDATE ERROR:\\n"+dspMsg);
   } else {
      alert("Your password has been reset.  You will be logged off - and you can log in with the new password"); 
      byId('profTrayCurrentPW').value = "";
      byId('profTrayNewPW').value = "";
      byId('profTrayConfirmPW').value = "";
      byId('profTrayResetCode').value = "";
      openAppCard('appcard_useraccount');
      byId('standardModalBacker').style.display = 'block';
      location.reload(true);
   }        
} 

function answerUpdateAboutMe(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("ABOUT ME UPDATE ERROR:\\n"+dspMsg);
   } else { 
     openAppCard('appcard_useraccount');
     byId('standardModalBacker').style.display = 'block';
     location.reload(true);
   }        
}
   
function voidfunction(rtnData) {
//  console.log(rtnData); 
  return null;
}

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

     //Specialized Controls
     if (whichcard === 'appcard_vocabsearch') {
       if (byId('vocabSrchTermFld')) { 
         byId('vocabSrchTermFld').focus();
       }
     }

  } else { 
     byId(whichcard).style.left =  "101vw";
  }
  
}

function closeSystemDialog() { 
  byId('standardModalBacker').style.display = 'none';
  byId('standardModalDialog').style.display = 'none';
  byId('standardModalDialog').innerHTML = '';
}

function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    var CSV = '';    
    //Set Report title in first row or line
    CSV += ReportTitle + '\\n\\n\\n';
    //This condition will generate the Label/Header
    if (ShowLabel) {
        var row = "";
        //This loop will extract the label from 1st index of on array
        for (var index in arrData[0]) {
            //Now convert each value to string and comma-seprated
            row += index + ',';
        }
        row = row.slice(0, -1);
        //append Label row with line break
        CSV += row + '\\n\\n';
    }
    //1st loop is to extract each row
    for (var i = 0; i < arrData.length; i++) {
        var row = "";
        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData[i]) {
            row += '"' + arrData[i][index] + '",';
        }
        row.slice(0, row.length - 1);
        //add a line break after each row
        CSV += row + '\\n\\n';
    }
    if (CSV == '') {        
        alert("Invalid data");
        return;
    }   
    //Generate a file name
    var fileName = "dataFor_";
    //this will remove the blank-spaces from the title and replace it with an underscore
    fileName += ReportTitle.replace(/ /g,"_");   
    //Initialize file format you want csv or xls
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
    // Now the little tricky part.
    // you can use either>> window.open(uri);
    // but this will not work in some browsers
    // or you will not get the correct file extension    
    //this trick will generate a temp <a /> tag
    var link = document.createElement("a");    
    link.href = uri;
    //set the visibility hidden so it will not effect on your web-layout
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";
    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
   
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
} 

function changeProfControlDiv(whichDiv) { 
  if (byId('profTrayControlAbtMe')) { byId('profTrayControlAbtMe').style.display = 'none'; }
  if (byId('profTrayControlAccess')) { byId('profTrayControlAccess').style.display = 'none'; }
  if (byId('profTrayControlManage')) { byId('profTrayControlManage').style.display = 'none'; }
  if (byId('profTrayControl'+whichDiv)) { byId('profTrayControl'+whichDiv).style.display = 'block'; }
} 

function fillProfTrayField(whichfield, whichvalue, whichdisplay) { 
  if (byId(whichfield)) { 
     byId(whichfield).value = whichdisplay; 
     if (byId(whichfield+'Value')) { 
        byId(whichfield+'Value').value = whichvalue;    
     }
  }
}

function genSystemReport(whichreport) { 
  var obj = new Object(); 
  obj['rptRequested'] = whichreport; 
  var passdta = JSON.stringify(obj);
  var mlURL = "/data-doers/generate-system-report-request";
  universalAJAX("POST",mlURL,passdta,answerGenSystemReport,1);
}

function answerGenSystemReport(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("SEARCH ERROR:\\n"+dspMsg);
   } else { 
     //SUCCESS
     var prts = JSON.parse(rtnData['responseText']);
     openOutSidePage("{$tt}/print-obj/system-reports/"+prts['DATA']['reportobjectency']);
   }        
}

      
JAVASCR;
return $rtnThis;    
}

function scienceserverhelp($rqststr) { 

  session_start(); 
  $tt = treeTop;
  $ott = ownerTree;
  $si = serverIdent;
  $sp = serverpw;

$rtnThis = <<<JAVASCR

document.addEventListener('DOMContentLoaded', function() {  

  if (byId('btnSearchHelp')) { 
    byId('btnSearchHelp').addEventListener('click', function() {
      submitSearch();
    }, false);
  }

  if (byId('btnHelpTicket')) { 
    byId('btnHelpTicket').addEventListener('click',function() { 
      openHelpTicket();
    }, false);
  }

}, false);        

function openHelpTicket() { 
  var mlURL = "/get-help-ticket-dialog";
  universalAJAX("GET",mlURL,"",answerOpenHelpTicket, 1);
}

function answerOpenHelpTicket(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("HELP TICKET ERROR:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
        if (byId('waitIcon')) {             
          byId('waitIcon').style.display = 'none';  
        }
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = "-25vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "15vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

function submitSearch() {
  var dta = new Object(); 
  dta['searchterm'] = byId('fldHlpSrch').value.trim();
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/submit-help-search";
  universalAJAX("POST",mlURL,passdta,answerSubmitSearch,1);
}

function answerSubmitSearch(rtnData) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("SEARCH ERROR:\\n"+dspMsg);
   } else { 
     //SUCCESS
     var rtn = JSON.parse(rtnData['responseText']);
     navigateSite('scienceserver-help/query-search/'+rtn['DATA']['searchid']);
   }        

}

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    if (byId(whichfield+'Value')) {
      byId(whichfield+'Value').value = whatvalue;
    }    
    byId(whichfield).value = whatplaintext; 
  }
}

function submitHelpTicket() { 
  var dta = new Object(); 
  dta['submitter'] = byId('htSubmitter').value.trim();
  dta['submitteremail'] = byId('htSubmitterEmail').value.trim();
  dta['reason'] = byId('fldHTR').value.trim(); 
  dta['recreateind'] = byId('fldRepYN').value.trim();
  dta['ssmodule'] = byId('fldMod').value.trim();
  dta['description'] = byId('htDescription').value.trim();
  var passdata = JSON.stringify(dta); 
  var mlURL = "/data-doers/submit-help-ticket";
  universalAJAX("POST",mlURL,passdata,answerSubmitHelpTicket,2);
}

function answerSubmitHelpTicket(rtnData) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("HELP TICKET SUBMISSION ERROR:\\n"+dspMsg);
   } else {

      var msgs = JSON.parse(rtnData['responseText']);
      alert('TICKET NUMBER: '+msgs['DATA']['ticketnbr']+'\\n\\nPlease keep this number for reference');
      byId('standardModalDialog').innerHTML = "";
      byId('standardModalBacker').style.display = 'none';
      byId('standardModalDialog').style.display = 'none';
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

  if (byId('btnLoginCtl')) {
    byId('btnLoginCtl').addEventListener('click', function() {
      doLogin();
    }, false);
  }

  document.addEventListener('keypress', function(event) { 
    if (event.which === 13) { 
      doLogin();
    }
  }, false);

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

function collectiongrid($rqststr) { 
session_start(); 
    
  $tt = treeTop;
  $regUsr = session_id();  
    
$rtnThis = <<<JAVASCR

//TODO: WRITE TIMER FUNCTION TO REFRESH GRID

//   
        
document.addEventListener('DOMContentLoaded', function() {  
  if (byId('btnRefresh')) { 
    byId('btnRefresh').addEventListener('click', function() { 
        updateCollectGrid();
     }, false);        
  }        
        
}, false);     
        

function updateCollectGrid() { 

  if ( byId('presentInstValue') && byId('cGridDateValue')   ) {      
   
    if (byId('cResults')) { byId('cResults').innerHTML = ""; }
    if (byId('waitForMe')) { byId('waitForMe').style.display = "block"; }
    var obj = new Object();
    obj['presentinstitution'] = byId('presentInstValue').value;
    obj['requesteddate'] = byId('cGridDateValue').value;
    obj['usrsession'] = "{$regUsr}";   
    var passdata = JSON.stringify(obj);
    var mlURL = "/data-doers/collection-grid-results-tbl";
    universalAJAX("POST",mlURL,passdata,answerUpdateCollectGrid,1);
  }
        
}

function answerUpdateCollectGrid(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    if (byId('waitForMe')) { byId('waitForMe').style.display = "none"; }
    alert("UPDATE COLLECTION GRID ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY PHI EDIT
     if (byId('cResults')) {
        var dta = JSON.parse(rtnData['responseText']); 
        if (byId('waitForMe')) { byId('waitForMe').style.display = "none"; }
        byId('cResults').innerHTML = dta['DATA'];
     }  
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
function getCalendar(whichcalendar, whichdiv, monthyear, modalCtl = 0) {        
  var mlURL = "/sscalendar/"+whichcalendar+"/"+monthyear;
  lastRequestCalendarDiv = whichdiv;      
  universalAJAX("GET",mlURL,"",answerGetCalendar,modalCtl);
}

function answerGetCalendar(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    byId(lastRequestCalendarDiv).innerHTML = rcd['DATA']; 
  } else { 
    alert("ERROR");  
  }
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
  }

}

JAVASCR;
return $rtnThis;    
}


function procurebiosample($rqstrstr) { 

    $sp = serverpw; 
    $tt = treeTop;
    $linuxServer = phiserver;
 
if (trim($rqstrstr[2]) === "") {


    $rtnthis = <<<JAVASCR

document.addEventListener('DOMContentLoaded', function() {  

    if (byId('btnPBClearGrid')) { 
      byId('btnPBClearGrid').addEventListener('click', function() { clearGrid(); }, false);          
    }
            
    if (byId('btnPBAddPHI')) { 
     byId('btnPBAddPHI').addEventListener('click', function() { 
       //GENERATE DUAL-AUTH CODE (AFTER CHECKING USER), DISPLAY CODE WITH OPEN WINDOW BUTTON, PASS BACK SINGLE USE CODE     
       //openOutSidePage('{$linuxServer}'); 
     }, false);
   }
            
    if (byId('btnPBORSched')) {
      byId('btnPBORSched').addEventListener('click', function() { 
        alert(byId('fldPRCProcedureDateValue').value); 
      }, false);
    }

    if (byId('btnPBAddPHI')) { 

    }

    if (byId('btnProcureSaveBiosample')) { 
      byId('btnProcureSaveBiosample').addEventListener('click', function() { 
        masterSaveBiogroup();
      }, false);
    }

    if (byId('btnPBAddDelink')) { 
      byId('btnPBAddDelink').addEventListener('click', function() { getAddPHIDialog(); }, false);          
    } 
       
    if (byId('btnPRCSave')) { 
      byId('btnPRCSave').addEventListener('click', function() { createInitialBG(); }, false);   
    }

    if (byId('btnPBCVocabSrch')) { 
      byId('btnPBCVocabSrch').addEventListener('click', function() { openAppCard('appcard_vocabsearch'); } , false);   
    }

    if (byId('fldPRCDXOverride')) {
      byId('fldPRCDXOverride').addEventListener('change', function() { 
        if (byId('fldPRCDXOverride').checked) {
          //LIST THE WHOLE DX HERE
          byId('ddPRCDXMod').innerHTML = ""; 
          byId('fldPRCDXMod').value = "";
          byId('fldPRCDXModValue').value = "";            
          allDiagnosisMenu();
        } else {
          //IF THE SPECCAT AND SITE FILLED IN MAKE THAT - IF NOT BLANK THE MENU
          if ( byId('fldPRCSpecCat').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "") { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             updateDiagnosisMenu();  
          } else { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             byId('ddPRCDXMod').innerHTML = "";  
          }
        }
      }, false);
    }
            
}, false);        

function clearGrid() { 
   //TODO:  RESET DEFAULT FIELD VALUES
   location.reload(true);   
}

function createInitialBG() { 
    if (byId('initialBiogroupInfo')) { 
      //COLLECT ELEMENTS
      var dta = new Object();
      if (byId('initialBiogroupInfo')) {
//      byId('initialBiogroupInfo').querySelectorAll('*').forEach(function(node) {
      document.querySelectorAll('*').forEach(function(node) {
        if (node.type === 'text' || node.type === 'hidden' || node.type === 'checkbox' || node.type === 'textarea') {
          if (node.type === 'checkbox') { 
            dta[node.id.substr(3)] = node.checked;
          } else { 
          dta[node.id.substr(3)] = node.value.trim();
          }
        }
      });
      }
      var passdata = JSON.stringify(dta);
      var mlURL = "/data-doers/initial-bgroup-save";
      byId('standardModalDialog').innerHTML = "";
      byId('standardModalBacker').style.display = 'block';
      byId('standardModalDialog').style.display = 'none';  
      universalAJAX("POST",mlURL,passdata,answerInitialBGroupSave,2);
      //console.log(passdata);       
    } else { 
      alert('ERROR-SEE A CHTNEASTERN INFORMATICS STAFF');
      return null;  
    }
}       
       
function answerInitialBGroupSave(rtnData) { 
  //console.log(rtnData);
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Biogroup Save Error:\\n"+dspMsg);
    byId('standardModalDialog').innerHTML = "";
    byId('standardModalBacker').style.display = 'none';
    byId('standardModalDialog').style.display = 'none';         
   } else { 
    byId('standardModalDialog').innerHTML = "";
    byId('standardModalBacker').style.display = 'none';
    byId('standardModalDialog').style.display = 'none'; 
    var ency = JSON.parse(rtnData['responseText']); 
    navigateSite('procure-biosample/'+ency['DATA']);            

   }        
}

function fillPXIInformation( pxiid, pxiinitials, pxiage, pxiageuom, pxirace, pxisex, pxiinformed, pxilastfour, sbjtNbr, protocolNbr, cx, rx, sogi ) { 
   //TODO:  CHECK FIELD EXISTANCE

   byId('fldPRCPXIId').value = "";
   byId('fldPRCPXIInitials').value = "";            
   byId('fldPRCPXIAge').value = "";
   byId('fldPRCPXIRace').value = "";            
   byId('fldPRCPXISex').value = "";                        
   byId('fldPRCPXILastFour').value = "";
   byId('fldPRCPXIInfCon').value = "";
   byId('fldPRCPXIDspCX').value = "";       
   byId('fldPRCPXIDspRX').value = "";
   byId('fldPRCPXISubjectNbr').value = "";       
   byId('fldPRCPXIProtocolNbr').value = "";
   byId('fldPRCPXISOGI').value = "";
       
            
   byId('fldPRCPXIId').value = pxiid;
   byId('fldPRCPXIInitials').value = pxiinitials.toUpperCase().trim();         
   byId('fldPRCPXIAge').value = pxiage.toUpperCase().trim();
   byId('fldPRCPXIAgeMetric').value = pxiageuom.toLowerCase().trim();
   byId('fldPRCPXIRace').value = pxirace.toUpperCase().trim();                        
   byId('fldPRCPXISex').value = pxisex.toUpperCase().trim();  
   byId('fldPRCPXILastFour').value = pxilastfour;  
   byId('fldPRCPXIInfCon').value = ( pxiinformed == 1) ? 'NO' : 'PENDING';        
   byId('fldPRCPXIDspCX').value = cx.toUpperCase().trim();       
   byId('fldPRCPXIDspRX').value = rx.toUpperCase().trim();
   byId('fldPRCPXISubjectNbr').value = sbjtNbr;       
   byId('fldPRCPXIProtocolNbr').value = protocolNbr;
   byId('fldPRCPXISOGI').value = sogi.toUpperCase().trim();
       
}

function saveDelink() { 
  var dta = new Object();  
  //TODO: CHECK FIELDS EXIST
  dta['proceduredate'] = byId('fldPRCProcedureDateValue').value;
  dta['delinkedind'] = 1;
  dta['initials'] = byId('fldADDPXIInitials').value.trim();
  dta['age'] = byId('fldADDPXIAge').value.trim();
  dta['ageuom'] = byId('fldADDAgeUOMValue').value.trim();
  dta['race'] = byId('fldADDRaceValue').value.trim();
  dta['sex'] = byId('fldADDSexValue').value.trim();
  dta['lastfour'] = byId('fldDNRLastFour').value.trim();
  dta['interface'] = 'SS';
  var pc = []; 
  pc.push(dta);
  var hld = new Object();
  hld['phicontainer'] = pc;
  var passdta = JSON.stringify(hld);
  var mlURL = "/data-doers/save-delinked-phi";
  if (byId('addDelinkSwirly')) {             
     byId('addDelinkSwirly').style.display = 'block';  
  }
  if (byId('divAddDelink')) { 
      byId('divAddDelink').style.display = 'none';
  }
  universalAJAX("POST",mlURL,passdta,answerSaveDelink,2);   
}

function answerSaveDelink(rtnData) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    if (byId('addDelinkSwirly')) { byId('addDelinkSwirly').style.display = 'none'; }
    if (byId('divAddDelink')) { byId('divAddDelink').style.display = 'block'; }
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ADD DELINKED DONOR ERROR:\\n"+dspMsg);
   } else { 
       byId('standardModalDialog').innerHTML = "";
       byId('standardModalBacker').style.display = 'none';
       byId('standardModalDialog').style.display = 'none';            
       updateORSched(); 
   }        
}

function getAddPHIDialog() { 
  var mlURL = "/preprocess-dialog-add-phi";
  universalAJAX("GET",mlURL,"",answerGetAddPHIDialog, 1);
}

function answerGetAddPHIDialog(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Add PHI ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY PHI EDIT
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
        if (byId('waitIcon')) {             
          byId('waitIcon').style.display = 'none';  
        }
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = "-25vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "15vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

function saveDonorSpecifics() { 
  var dta = new Object();  
  //TODO: CHECK FIELDS EXIST
  dta['encyeid'] = byId('fldDNReid').value.trim(); 
  dta['sessid'] = byId('fldDNRSess').value.trim();
  dta['institution'] = byId('fldDNRPresInst').value.trim();
  dta['targetind'] = byId('fldDNRTarget').value.trim();
  dta['targetindvalue'] = byId('fldDNRTargetValue').value.trim();
  dta['informedind'] = byId('fldDNRInformedConsent').value.trim();
  dta['informedindvalue'] = byId('fldDNRInformedConsentValue').value.trim();
  dta['age'] = byId('fldDNRAge').value.trim();
  dta['ageuom'] = byId('fldDNRAgeUOM').value.trim();
  dta['ageuomvalue'] = byId('fldDNRAgeUOMValue').value.trim();
  dta['race'] = byId('fldDNRRace').value.trim();
  dta['racevalue'] = byId('fldDNRRaceValue').value.trim();
  dta['sex'] = byId('fldDNRSex').value.trim();
  dta['sexvalue'] = byId('fldDNRSexValue').value.trim();
  dta['lastfour'] = byId('fldDNRLastFour').value.trim();
  dta['notrcvdnote'] = byId('fldDNRNotReceivedNote').value.trim();
  dta['subjectnbr'] = byId('fldADDSubjectNbr').value.trim(); 
  dta['protocolnbr'] = byId('fldADDProtocolNbr').value.trim();
  dta['sogi'] = byId('fldPRCUpennSOGIValue').value.trim();
  dta['cx'] = byId('fldPRCPXICXValue').value.trim(); 
  dta['rx'] = byId('fldPRCPXIRXValue').value.trim();         
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/save-encounter-donor";
  //TODO:  Add a wait swirly   
  if (byId('waitIcon')) {             
     byId('waitIcon').style.display = 'block';  
  }
  if (byId('displayEncounterDiv')) { 
      byId('displayEncounterDiv').style.display = 'none';
  }
  if (byId('waitinstruction')) { 
    byId('waitinstruction').innerHTML = "Please Wait ... ScienceServer is fulfilling your request";    
  }
  universalAJAX("POST",mlURL,passdta,answerSaveDonorSpecifics,2);   
}

function answerSaveDonorSpecifics(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("ENCOUNTER DONOR SAVE ERROR:\\n"+dspMsg);
        if (byId('waitIcon')) {             
          byId('waitIcon').style.display = 'none';  
        }
        if (byId('displayEncounterDiv')) { 
          byId('displayEncounterDiv').style.display = 'block';
       }               
   } else { 
       alert('Encounter has been saved!');
       byId('standardModalBacker').style.display = 'none';
       byId('standardModalDialog').innerHTML = '';
       byId('standardModalDialog').style.display = 'none';
       updateORSched(); 
   }        
}

function saveEncounterNote() { 
  //SAVE ENCOUNTER NOTE
  var dta = new Object(); 
  dta['encyeid'] = byId('fldDNReid').value.trim(); 
  dta['sessid'] = byId('fldDNRSess').value.trim();
  dta['institution'] = byId('fldDNRPresInst').value.trim();
  dta['ecnote'] = byId('fldDNREncounterNote').value.trim();
  dta['notetype'] = byId('fldDNREncNotesType').value.trim();
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/save-encounter-note";
  universalAJAX("POST",mlURL,passdta,answerSaveEncounterNote,2);   
}

function answerSaveEncounterNote(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("ENCOUNTER NOTE SAVE ERROR:\\n"+dspMsg);
   } else { 
       alert('Encounter Note has been saved!');
       byId('standardModalBacker').style.display = 'none';
       byId('standardModalDialog').innerHTML = '';
       byId('standardModalDialog').style.display = 'none';
   }        
}

var lastRequestCalendarDiv = "";
function getCalendar(whichcalendar, whichdiv, monthyear, modalCtl = 0) {
  var mlURL = "/sscalendar/"+whichcalendar+"/"+monthyear;
  lastRequestCalendarDiv = whichdiv;
  universalAJAX("GET",mlURL,"",answerGetCalendar,modalCtl);
}

function answerGetCalendar(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    byId(lastRequestCalendarDiv).innerHTML = rcd['DATA']; 
  } else { 
    alert("ERROR");  
  }
}   

function editPHIRecord(e, phiid) { 
  e.stopPropagation();     
  if (phiid.trim() !== "") { 
    //POP OPEN MODAL EDITOR
    var dta = new Object();
    dta['phicode'] = phiid; 
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/preprocess-phi-edit";          
    universalAJAX("POST",mlURL,passdta,answerPreprocessPHIEdit,1);   
  }
}

function answerPreprocessPHIEdit(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("PHI EDIT ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY PHI EDIT
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
        if (byId('waitIcon')) {             
          byId('waitIcon').style.display = 'none';  
        }
        if (byId('displayEncounterDiv')) { 
          byId('displayEncounterDiv').style.display = 'block';
       }            
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = "-25vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "7vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

function fillField(whichfield, whichvalue, whichdisplay) { 
  if (byId(whichfield)) { 
     byId(whichfield).value = whichdisplay; 
     if (byId(whichfield+'Value')) { 
        byId(whichfield+'Value').value = whichvalue;    
     }
  }
  switch (whichfield) {
    case 'fldPRCProcedureDate':
      updateORSched(); 
    break;
    case 'fldPRCProcedureType':
      if (byId('fldPRCCollectionTypeValue')) { 
        byId('fldPRCCollectionTypeValue').value = "";
      }
      if (byId('fldPRCCollectionType')) { 
        byId('fldPRCCollectionType').value = "";
      }
      if (byId('ddPRCCollectionType')) { 
        byId('ddPRCCollectionType').innerHTML = "&nbsp;";
      } 
      updateSubMenu('PRCCollectionType','COLLECTIONT',whichvalue);
    break;
    case 'fldPRCSpecCat':
       //GET SITES
       byId('fldPRCDXOverride').checked = false;     
       fillField('fldPRCSSite','','');
       fillField('fldPRCSite','','');
       fillField('fldPRCDXMod','','');
       var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Site)</div>";     
       byId('ddPRCDXMod').innerHTML = menuTbl        
       byId('ddPRCSSite').innerHTML = menuTbl;        
       updateSiteMenu();
       if ( byId('fldPRCSpecCatValue').value === 'MALIGNANT') {
         //TURN ON METS
         if (byId('metsFromDsp')) { 
           byId('metsFromDsp').style.display = 'block';
         } 
       } else { 
         //TURN OFF METS
         if (byId('metsFromDsp')) { 
           byId('metsFromDsp').style.display = 'none';
         } 
       }  
    break;
    case 'fldPRCMETSSite':
     if ( byId('fldPRCMETSSiteValue').value.trim() !== ""  ) { 
       updateMETSDiagnosisMenu();
     }
    break;
    case 'fldPRCSite':
       byId('fldPRCDXOverride').checked = false;          
       byId('fldPRCDXMod').value = "";
       byId('fldPRCDXModValue').value = "";
       fillField('fldPRCSSite','',''); 
       updateSubSiteMenu();          
       updateDiagnosisMenu();                       
     break;
    case 'fldDNRTarget':
    //TODO:  Make DYNAMIC for value check
    if ( byId('fldDNRTarget').value === 'NOT RECEIVED') { 
      byId('fldDNRNotReceivedNote').value = "";
      byId('notRcvdNoteDsp').style.display = 'block';
    } else { 
      byId('fldDNRNotReceivedNote').value = "";
      byId('notRcvdNoteDsp').style.display = 'none';
    }
    break;
    case 'fldPRCPresentInst':
      alert('CHANGE Operative Schedule');
    break;
  }        
}            

function updateSubSiteMenu() { 
   if ( byId('fldPRCSpecCatValue') && byId('fldPRCSite') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "" ) {     
       var mlURL = "/data-doers/subsites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       dta['site'] = byId('fldPRCSite').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSSiteMenu,0);               
     } else { 
       //NOTHING SELECTED
     }
   }
}

function answerUpdateSSiteMenu(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA']; 
      var menuTbl = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCSSite','','');\" class=ddMenuClearOption>[clear]</td></tr>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCSSite','"+element['ssiteid']+"','"+element['subsite']+"');\" class=ddMenuItem>"+element['subsite']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">No Subsite Listed</div>";     
    }
  } else {      
    //ERROR - DISPLAY ERROR
     var menuTbl =  "<center><div style=\"font-size: 1.4vh\">No Subsite Listed</div>";     
    //console.log(rtnData);    
  }
  byId('ddPRCSSite').innerHTML = menuTbl;        
}           

function updateMETSDiagnosisMenu() { 
  var mlURL = "/data-doers/diagnosis-mets-downstream"; 
  var dta = new Object();
  dta['siteid'] = byId('fldPRCMETSSiteValue').value.trim();            
  var passdta = JSON.stringify(dta);
  universalAJAXStreamTwo("POST",mlURL,passdta,answerUpdateMETSDiagnosisMenu,0);                 
}

function answerUpdateMETSDiagnosisMenu(rtnData) { 

  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      var menuTbl = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCMETSDX','','');\" class=ddMenuClearOption>[clear]</td></tr>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCMETSDX','"+element['dxid']+"','"+element['diagnosis']+"');\" class=ddMenuItem>"+element['diagnosis']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCMETSDX').innerHTML = menuTbl        
  } else {      
    //ERROR - DISPLAY ERROR
    //console.log(rtnData);    
  }            


}

function allDiagnosisMenu() { 
       var mlURL = "/data-doers/all-downstream-diagnosis"; 
       universalAJAXStreamTwo("POST",mlURL,"",answerUpdateDiagnosisMenu,0);                 
}            
                       
function updateDiagnosisMenu() { 
       var mlURL = "/data-doers/diagnosis-downstream"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       dta['site'] = byId('fldPRCSiteValue').value.trim();            
       var passdta = JSON.stringify(dta);
       universalAJAXStreamTwo("POST",mlURL,passdta,answerUpdateDiagnosisMenu,0);                 
}

function answerUpdateDiagnosisMenu(rtnData) { 
  //console.log(rtnData);
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      var menuTbl = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('fldPRCDXMod','','');\" class=ddMenuClearOption>[clear]</td></tr>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCDXMod','"+element['dxid']+"','"+element['diagnosis']+"');\" class=ddMenuItem>"+element['diagnosis']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCDXMod').innerHTML = menuTbl        
  } else {      
    //ERROR - DISPLAY ERROR
    //console.log(rtnData);    
  }            
}
            
function updateSiteMenu() { 
   if ( byId('fldPRCSpecCatValue') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "") {     
       var mlURL = "/data-doers/sites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSiteMenu,0);               
     } else { 
       //NOTHING SELECTED
     }
   }
}

function setPathologyRptRequired(prMenuValue) { 
  //TODO:  MAKE THIS DYNAMIC - TAKE AWAY HARD CODE VALUE CHECK FOR PATH RPT REQUIRED
  var prMenuVal = prMenuValue;
  var prMenuDsp = ""; 
  switch (prMenuValue) { 
    case 0:
      prMenuDsp = "No";
      prMenuVal = 0;
    break;
    default:
      prMenuDsp = "Pending";
      prMenuVal = 2;
  }
  if ( byId('fldPRCPathRptValue') ) { 
    byId('fldPRCPathRptValue').value = prMenuVal;
  } 
  if ( byId('fldPRCPathRpt') ) { 
    byId('fldPRCPathRpt').value = prMenuDsp;
  }
}
            
function answerUpdateSiteMenu(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      var menuTbl = "<table border=0 class=menuDropTbl>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCSite','"+element['siteid']+"','"+element['site']+"');setPathologyRptRequired("+element['pathrptrequiredvalue']+");\" class=ddMenuItem>"+element['site']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCSite').innerHTML = menuTbl        
  } else {      
    //ERROR - DISPLAY ERROR
    //console.log(rtnData);    
  }
}           
            
function updateSubMenu(whichdropdown, whichmenu, lookupvalue) {  
  var mlURL = "/data-doers/generate-sub-menu"; 
  var dta = new Object();
  dta['whichdropdown'] = whichdropdown;
  dta['whichmenu'] = whichmenu;
  dta['lookupvalue'] = lookupvalue;
  var passdta = JSON.stringify(dta);
  universalAJAX("POST",mlURL,passdta,answerUpdateSubMenu,0);            
}

function answerUpdateSubMenu(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      //<tr><td align=right onclick=\"fillField('fld"+rquestFld+"','','');\" class=ddMenuClearOption>[clear]</td></tr>
      var menuTbl = "<table border=0 class=menuDropTbl>"; 
      var defaultValue = "";
      var defaultDsp = "";
      dspList.forEach( function(element) { 
        if (parseInt(element['useasdefault']) === 1) { 
          defaultValue = element['menuvalue'];
          defaultDsp = element['dspvalue'];
        }
        menuTbl += "<tr><td onclick=\"fillField('fld"+rquestFld+"','"+element['menuvalue']+"','"+element['dspvalue']+"');\" class=ddMenuItem>"+element['dspvalue']+"</td></tr>";
      });
      menuTbl += "</table>";
      byId('dd'+rquestFld).innerHTML = menuTbl;
      byId('fld'+rquestFld).value = defaultDsp;
      byId('fld'+rquestFld+"Value").value = defaultValue;
    } else {
      //DO NOTHING
      //console.log(rquestFld);
    }
  } else {      
    //ERROR - DISPLAY ERROR
    //console.log(rtnData);    
  }
}

function updateORSched() {
  if (byId('fldPRCProcedureDateValue')) { 
    var mlURL = "/simple-or-schedule-wrapper/" + byId('fldPRCProcedureDateValue').value;
    universalAJAX("GET",mlURL,"",answerUpdateORSched,1);            
  }
}

function answerUpdateORSched(rtnData) {
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);

    if (parseInt(rcd['ITEMSFOUND']) > 0) {  

     var innerRows = "";
     var ageuom = "yrs";

     rcd['DATA']['orlisting'].forEach(function(element) { 
       var target = element['targetind'];
       switch (target) { 
         case 'T':
          target = "<i class=\"material-icons targeticon\">check_box_outline_blank</i>";
          targetBck = "targetwatch";
          break;
         case 'R':    
          target = "<i class=\"material-icons targeticon\">check_box</i>";
          targetBck = "targetrcvd";
          break;
         case 'N':    
          target = "<i class=\"material-icons targeticon\">indeterminate_check_box</i>";
          targetBck = "targetnot";
          break;
         default:
          target = "-";
          targetBck = "";
       }
       var informed = element['informedconsentindicator'];
       switch (parseInt(informed)) { 
         case 1: //NO
          icicon = "N";
          break;
         case 2: //YES
          icicon = "Y";
          break;
         case 3:  //PENDING
          icicon = "P";
          break;
         default:
          icicon = "";
       } 
       var addeddonor = element['linkage'];
       if (addeddonor === "X") {
         addeddonor = "<i class=\"material-icons addicon\">input</i>";
       } else { 
         addeddonor = "&nbsp;";
       }
       var lastfour = element['lastfourmrn'];
       var ordsp = "";
       if ( element['room'].trim() !== "" ) { 
         ordsp = " in OR "+element['room'];
       }
       
       var studyNbrLineDsp = ( element['studysubjectnbr'].trim() !== "" || element['studyprotocolnbr'].trim() !== "") ? "<tr><td valign=top class=smallORTblLabel>Subject/Protocol </td><td valign=top>"+element['studysubjectnbr']+" :: "+element['studyprotocolnbr']+"</td></tr>" : "";
       var cxrxDsp = ( element['cx'].trim() !== "" || element['rx'].trim() !== "") ? "<tr><td valign=top class=smallORTblLabel>CX/RX </td><td valign=top>"+element['cx'].toUpperCase().substring(0,1)+"::"+element['rx'].toUpperCase().substring(0,1)+"</td></tr>" : "";
       
       
       var proc = "<table class=procedureSpellOutTbl border=0><tr><td valign=top class=smallORTblLabel>A-R-S::Callback</td><td valign=top>"+element['ars']+"::"+lastfour+"</td></tr>"+studyNbrLineDsp+cxrxDsp+"<tr></tr><tr><td valign=top class=smallORTblLabel>Procedure </td><td valign=top>"+element['proceduretext']+"</td></tr><tr><td valign=top class=smallORTblLabel>Surgeon </td><td valign=top>"+element['surgeon']+"</td></tr><tr><td valign=top class=smallORTblLabel>Start Time </td><td valign=top>"+element['starttime']+" "+ordsp+"</td></tr><tr><td colspan=2> <div class=btnEditPHIRecord onclick=\"editPHIRecord(event,'"+element['pxicode']+"');\">Edit Record</div></td></tr></table>";
       var prace = (element['pxirace'].trim() == "-") ? "" : element['pxirace'].trim();
       innerRows += "<tr oncontextmenu=\"return false;\" onclick=\"fillPXIInformation('"+element['pxicode']+"','"+element['pxiinitials']+"','"+element['pxiage']+"','"+ageuom+"','"+prace+"','"+element['pxisex']+"','"+informed+"','"+lastfour+"','"+element['studysubjectnbr']+"','"+element['studyprotocolnbr']+"','"+element['cx']+"','"+element['rx']+"','"+element['sogi']+"');\" class=displayRows><td valign=top class=dspORInitials>"+element['pxiinitials']+"</td><td valign=top class=\"dspORTarget "+targetBck+"\">"+target+"</td><td valign=top class=dspORInformed>"+icicon+"</td><td valign=top class=dspORAdded>"+addeddonor+"</td><td valign=top class=procedureTxt>"+proc+"</td></tr>";
     });

     if (byId('PXIDspBody')) { 
       byId('PXIDspBody').innerHTML = innerRows;
     }

    } else { 
      //NO OR SCHED ITEMS FOUND
      byId('PXIDspBody').innerHTML = "&nbsp;";
    }

  } else { 
    var rcd = JSON.parse(rtnData['responseText']);
    alert(rcd['MESSAGE']);  
  }
}

JAVASCR;
} else { 
    //BIOGROUP SPECIFIED

    $rtnthis = <<<BGJAVASCRPT

document.addEventListener('DOMContentLoaded', function() {  

    if (byId('btnPBClearGrid')) { 
      byId('btnPBClearGrid').addEventListener('click', function() { clearGrid(); }, false);          
    }

    if (byId('btnPRCSaveEdit')) { 
      byId('btnPRCSaveEdit').addEventListener('click', function() { alert('This will save Edits to this biogroup (but not yet)'); }, false);          
    }

    if (byId('btnPBCVocabSrch')) { 
      byId('btnPBCVocabSrch').addEventListener('click', function() { alert('This will help you search the official CHTN vocabulary (but not yet)'); }, false);          
    }

    if (byId('btnPBCLock')) { 
      byId('btnPBCLock').addEventListener('click', function() { markMigration(); }, false);         
    }

    if (byId('btnPBCVoid')) { 
      byId('btnPBCVoid').addEventListener('click', function() { sendVoidPreprocess(); }, false); 
    }

    if (byId('btnPBCSegment')) { 
      byId('btnPBCSegment').addEventListener('click', function() { addSegments(); }, false); 
    }
    
}, false); 

function doBGVoid() { 
  if (byId('fldBGVency')) { 
    var obj = new Object();
    obj['bgency'] = byId('fldBGVency').value.trim(); 
    obj['reasoncode'] = byId('fldBGVReasonValue').value.trim();
    obj['reason'] = byId('fldBGVReason').value.trim();
    obj['reasonnotes'] = byId('fldBGVText').value.trim();  
    var passeddata = JSON.stringify(obj);
    console.log(passeddata);
    var mlURL = "/data-doers/void-bg";
    universalAJAX("POST",mlURL,passeddata,answerBGVoid,2);
  }
}

function answerBGVoid( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Void BG ERROR:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       byId('standardModalDialog').innerHTML = "";
       //byId('standardModalBacker').style.display = 'none';
       byId('standardModalDialog').style.display = 'none';
       location.reload(true);     
     }  
   }       
}

function markMigration() { 
  if (byId('bgSelectorCode')) { 
    var obj = new Object(); 
    obj['bgselector'] = byId('bgSelectorCode').value;
    var passeddata = JSON.stringify(obj);
            if (confirm("This Biogroup ("+byId('fldPRCBGNbr').value.trim()+") has been saved to the procurement module.\\r\\nThis will move the biogroup to the master-record and lock the biogroup from edits in procurement.\\r\\n\\r\\nConfirm that you are now releasing it to the master-record.") ) { 
              var mlURL = "/data-doers/mark-bg-migration";
              universalAJAX("POST",mlURL,passeddata,answerMarkMigration,2);            
            }
  }
}
            
function answerMarkMigration(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("BIOGROUP RELEASE ERROR:\\n"+dspMsg);
   } else { 
      alert('BIOGROUP marked for migration');
   }  
}
            
function sendVoidPreprocess() {
  if (byId('bgSelectorCode')) { 
    var obj = new Object(); 
    obj['bgselector'] = byId('bgSelectorCode').value;
    var passeddata = JSON.stringify(obj);
    var mlURL = "/data-doers/preprocess-void-bg";
    universalAJAX("POST",mlURL,passeddata,answerPreprocessVoid,2);
  }
}

function answerPreprocessVoid(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Void BG ERROR:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = "-25vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "15vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }       

}

function clearGrid() { 
   //TODO:  RESET DEFAULT FIELD VALUES
   navigateSite('procure-biosample'); 
}

function markAsBank() { 
  if (byId('fldSEGselectorAssignInv') && byId('fldSEGselectorAssignReq') ) { 
    byId('fldSEGselectorAssignInv').value = "BANK"; 
    byId('fldSEGselectorAssignReq').value = "";

  } else { 
    alert('ERROR: SEE A CHTNEASTERN INFORMATICS STAFF MEMBER');
  } 
}

function addQMSSegments() { 
  if (byId('fldSEGSegmentBGSelectorId')) { 
    var given = new Object(); 
    given['bgency'] = byId('fldSEGSegmentBGSelectorId').value; 
    given['hrpost'] = byId('fldSEGAddHP').value; 
    given['metric'] = byId('fldSEGAddMetric').value; 
    given['metuom'] = byId('fldSEGAddMetricUOMValue').value; 
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/segment-create-qms-pieces";
    universalAJAX("POST",mlURL,passeddata,answerAddQMSSegments,2);
  } else { 
    alert('ERROR:  SEE CHTNEASTERN INFORMATICS');
  }
}

function answerAddQMSSegments(rtnData) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Add Segment ERROR:\\n"+dspMsg);
   } else { 
     //UPDATE SEGMENT DISPLAY
     updateBSSegmentDisplay();
     resetSegmentAddDialog();
   }        
}

function updateBSSegmentDisplay() { 
  if ( byId('procBSSegmentDsp') ) {
    var given = new Object(); 
    given['selector'] = byId('bgSelectorCode').value; 
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/get-procurement-segment-list-table";
    universalAJAX("POST",mlURL,passeddata,answerUpdateBSSegmentDisplay,2);
  }
}

function resetSegmentAddDialog() { 
  byId('fldSEGAddHP') ? byId('fldSEGAddHP').value = "" : "";
  byId('fldSEGAddMetric') ? byId('fldSEGAddMetric').value = "" : "";
  byId('fldSEGAddMetricUOMValue') ? byId('fldSEGAddMetricUOMValue').value = "" : "";
  byId('fldSEGAddMetricUOM') ? byId('fldSEGAddMetricUOM').value = "" : "";
  byId('fldSEGPreparationMethodValue') ? byId('fldSEGPreparationMethodValue').value = "" : "";
  byId('fldSEGPreparationMethod') ? byId('fldSEGPreparationMethod').value = "" : "";
  byId('fldSEGPreparationValue') ? byId('fldSEGPreparationValue').value = "" : "";
  byId('fldSEGPreparation') ? byId('fldSEGPreparation').value = "" : "";
  byId('ddSEGPreparationDropDown') ? byId('ddSEGPreparationDropDown').innerHTML = "<center>(Select a Preparation Method)" : "";
  byId('fldSEGPreparationContainerValue') ? byId('fldSEGPreparationContainerValue').value = "" : "";
  byId('fldSEGPreparationContainer') ? byId('fldSEGPreparationContainer').value = "" : "";
  byId('preparationAdditions') ? byId('preparationAdditions').innerHTML = "" : "";
  byId('fldSEGselectorAssignInv') ? byId('fldSEGselectorAssignInv').value = "" : "";
  byId('fldSEGselectorAssignReq') ? byId('fldSEGselectorAssignReq').value = "" : "";
  byId('requestDropDown') ? byId('requestDropDown').innerHTML = "" : "";
  byId('assignInvestSuggestion') ? byId('assignInvestSuggestion').innerHTML = "" : "";
  byId('fldSEGSGComments') ? byId('fldSEGSGComments').value = "" : "";            
}

function answerUpdateBSSegmentDisplay(rtnData) { 
  var tblhld = JSON.parse(rtnData['responseText']);
  byId('procBSSegmentDsp').innerHTML = tblhld['DATA'];       
}

function hideProcGridLine(whichGridLine, whichControl) { 
  if ( byId(whichGridLine) ) {
    if ( byId(whichGridLine).style.display === '' ||  byId(whichGridLine).style.display === 'block' ) { 
      byId(whichGridLine).style.display = 'none';
      if ( byId(whichControl) ) { 
        byId(whichControl).innerHTML = "&#9870;";
      }
    } else { 
      byId(whichGridLine).style.display = 'block';
      if ( byId(whichControl) ) { 
        byId(whichControl).innerHTML = "&#9871;";
      }
    }
  }
}

function addDefinedSegment() {
   if ( byId('divSegmentAddHolder') ) { 
     var elem = byId('divSegmentAddHolder').getElementsByTagName('*');
     var segmentdef = new Object();
     for (var i = 0; i < elem.length; i++) {
       if (elem[i].id.trim() !== "" ) {      
         if ( elem[i].id.substr(0,3) === 'fld' ) {            
           segmentdef[  elem[i].id.replace(/^fldSEG/,'') ] = elem[i].value.trim();
         }
       } 
     } 
     var passeddata = JSON.stringify(segmentdef);
     var mlURL = "/data-doers/segment-create-defined-pieces";
     universalAJAX("POST",mlURL,passeddata,answerSaveDefinedSegment,2);
   }
}

function answerSaveDefinedSegment(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ADD SEGMENT ERROR:\\n"+dspMsg);
   } else {     
     updateBSSegmentDisplay();
     resetSegmentAddDialog();            
   }        
}

function selectorInvestigator() { 
  if (byId('fldSEGselectorAssignInv').value.trim().length > 3) { 
    getSuggestions('fldSEGselectorAssignInv',byId('fldSEGselectorAssignInv').value.trim()); 
  } else { 
    byId('assignInvestSuggestion').innerHTML = "&nbsp;";
    byId('assignInvestSuggestion').style.display = 'none';
  }
}

function getSuggestions(whichfield, passedValue) { 
switch (whichfield) { 
  case 'fldSEGselectorAssignInv':
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-invest';  
    given['given'] = byId(whichfield).value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerAssignInvestSuggestions,2);
  break;
}
}

function answerAssignInvestSuggestions(rtnData) { 
var rsltTbl = "";
if (parseInt(rtnData['responseCode']) === 200 ) { 
  var dta = JSON.parse(rtnData['responseText']);
  if (parseInt( dta['ITEMSFOUND'] ) > 0 ) { 
    var rsltTbl = "<table border=0 class=\"menuDropTbl\"><tr><td colspan=2 style=\"font-size: 1.2vh; padding: 8px;\">Below are suggestions for the investigator field. Use the investigator's ID.  These are live values from CHTN's TissueQuest. Found "+dta['ITEMSFOUND']+" matches.</td></tr>";
    dta['DATA'].forEach(function(element) { 
       rsltTbl += "<tr class=ddMenuItem onclick=\"fillField('fldSEGselectorAssignInv','"+element['investvalue']+"','"+element['investvalue']+"'); byId('assignInvestSuggestion').innerHTML = '&nbsp;'; byId('assignInvestSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
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

function setAssignsRequests() {
  if (byId('fldSEGselectorAssignInv').value.trim() !== "" ) { 
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-requests'; 
    given['given'] = byId('fldSEGselectorAssignInv').value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
  } 
}

function answerRequestDrop(rtnData) {
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=0 class=\"menuDropTbl\">";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('fldSEGselectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); byId('requestDropDown').innerHTML = '&nbsp;';\">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
  }
}

function updatePrepAddDisplay( whichPrep ) { 
  if (byId('preparationAdditions')) { 
   byId('preparationAdditions').innerHTML = ""; 
   byId('preparationAdditions').style.display = "none";
   //GET THE ADDS
   var dta = new Object(); 
   dta['additionsforprep'] = whichPrep;
   var passdta = JSON.stringify(dta);
   var mlURL = "/data-doers/preprocess-preparation-additions";
   universalAJAXStreamTwo("POST",mlURL,passdta,answerPreparationAdditions,2);   
  } 
}

function answerPreparationAdditions(rtnData) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Add Segment ERROR:\\n"+dspMsg);
   } else { 
     var rsp = JSON.parse(rtnData['responseText']);
     if (rsp['DATA']['pagecontent'].trim() !== "") { 
       byId('preparationAdditions').innerHTML = rsp['DATA']['pagecontent']; 
       byId('preparationAdditions').style.display = "block";
     }
   }        
}

function selectThisAdditive(additiveId) {
   if (byId(additiveId)) { 
     if (parseInt(byId(additiveId).dataset.aselected) === 1) {
       byId(additiveId).dataset.aselected = '0';
     } else { 
       byId(additiveId).dataset.aselected = '1';
     }     
     var addbtn = byId('freshadditivebtns').getElementsByTagName('*');
     var additives = [];
     var cnt = 0;
     for (var i = 0; i < addbtn.length; i++) {
        if (addbtn[i].id.substr(0,4) === "add-") {  
          if ( addbtn[i].dataset.aselected === "1" ) { 
            additives.push( addbtn[i].id.substr(4) );
            cnt++;
          }
        }
     }
     if ( additives.length > 0 ) { 
      if (byId('fldSEGAdditiveDivValue') ) { 
        byId('fldSEGAdditiveDivValue').value = JSON.stringify(additives);
      }
     } else { 
      if (byId('fldSEGAdditiveDivValue') ) { 
        byId('fldSEGAdditiveDivValue').value = "";
      }
     }
   }
}

function requestAdditionalSlides() { 
  if ( byId('additionalPreparation') && byId('addPBQtySlide') && byId('fldSEGAdditiveDivValue') ) { 
    if ( byId('additionalPreparation').value.trim() === "") { alert('You must select a \'Type of Slide\''); return null; }
    if ( byId('addPBQtySlide').value.trim() === "") { alert('You must specify a value for quantity of slide(s)'); return null; }
    if ( byId('addPBQtySlide').value.trim().match(/^\d+$/) === null ) { alert('You may only use numbers in the quantity field'); byId('addPBQtySlide').value = ""; byId('addPBQtySlide').focus(); return null; }

    if ( byId('fldSEGAdditiveDivValue').value.trim() === "") { 
      var arr = new Object(); 
      var itm = new Object();
      itm['typeofslide'] = byId('additionalPreparation').value.trim(); 
      itm['qty'] = byId('addPBQtySlide').value.trim();
      arr[0] = itm;
    } else { 
      //NEW ARRAY
      var itm = new Object();
      itm['typeofslide'] = byId('additionalPreparation').value.trim(); 
      itm['qty'] = byId('addPBQtySlide').value.trim();
      var arr = JSON.parse( byId('fldSEGAdditiveDivValue').value );
      arr[ Object.keys(arr).length ] = itm;
    }
    byId('fldSEGAdditiveDivValue').value = JSON.stringify(arr);
    byId('additionalPreparation').value = "";
    byId('addPBQtySlide').value = "";
    buildPBSlideAddTbl();
  } 
}

function buildPBSlideAddTbl() { 
  if ( byId('addSlideDspBox') && byId('fldSEGAdditiveDivValue')) { 
    byId('addSlideDspBox').innerHTML = "";
    if (byId('fldSEGAdditiveDivValue').value !== "") { 
      var arr = JSON.parse( byId('fldSEGAdditiveDivValue').value.trim() );

      var slideTbl = "<table border=0 id=segAddslideItmTbl><tr><th style=\"width: 1vw;\"></th><th style=\"width: 8vw;\">Type of Slide</th><th style=\"width: 3vw;\">Qty</th></tr><tbody>";  
      for (var i = 0; i < Object.keys(arr).length; i++ ) { 
        //var slide = Object.getOwnPropertyNames(arr[i]);
        slideTbl += "<tr onclick=\"removeSlideItm("+i+");\"><td>&nbsp;</td><td>"+  arr[i]['typeofslide'] +"</td><td>"+arr[i]['qty']+"</td></tr>";
      }
      slideTbl += "</tbody></table>";
      byId('addSlideDspBox').innerHTML = slideTbl;
    }
  }
}

function removeSlideItm(whichkey) { 
  if ( byId('fldSEGAdditiveDivValue').value.trim() !== "") { 
    var arr = JSON.parse( byId('fldSEGAdditiveDivValue').value );
    var itm = new Object();
    var cnt = 0;
    for (var i = 0; i < Object.keys(arr).length; i++) { 
      if ( i === whichkey ) {
      } else {
        var slditm = new Object();
        slditm['typeofslide'] = arr[i]['typeofslide'];
        slditm['qty'] = arr[i]['qty'];
        itm[cnt] = slditm;
        cnt++;        
      }
    }
    byId('fldSEGAdditiveDivValue').value = "";
    var slides = Object.keys(itm).length;
    if ( slides > 0) { 
      byId('fldSEGAdditiveDivValue').value = JSON.stringify(itm);
    } else { 
    }
    buildPBSlideAddTbl();
  }
}

function addSegments() { 
  if (!byId('bgSelectorCode')) { 
  } else { 
    if (byId('bgSelectorCode').value.trim() !== "") {
      var dta = new Object(); 
      dta['bgrecordselector'] = byId('bgSelectorCode').value;
      var passdta = JSON.stringify(dta);
      //console.log(passdta);
      byId('standardModalDialog').innerHTML = "";
      byId('standardModalBacker').style.display = 'block';
      byId('standardModalDialog').style.display = 'none';
      var mlURL = "/data-doers/preprocess-add-bg-segments";
      universalAJAX("POST",mlURL,passdta,answerAddSegmentsDialog,2);   
    }
  }
}

function answerAddSegmentsDialog(rtnData) { 
  
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("Add Segment ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY PHI EDIT
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
        if (byId('waitIcon')) {             
          byId('waitIcon').style.display = 'none';  
        }
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = "-25vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "15vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
       if (byId('fldSEGAddHP')) { 
         byId('fldSEGAddHP').focus();
       }
     }  
   }        

}

function fillPXIInformation( pxiid, pxiinitials, pxiage, pxiageuom, pxirace, pxisex, pxiinformed, pxilastfour, sbjtNbr, protocolNbr, cx, rx, sogi ) { 

alert('To change the donor, void the biogroup and re-create it (functionality will be added soon.)');

}

function updatePrepmenu(whatvalue) { 
            
  byId('fldSEGPreparationValue').value = '';
  byId('fldSEGPreparation').value = '';
  byId('ddSEGPreparationDropDown').innerHTML = "&nbsp;"; 
  if (whatvalue.trim() === "") { 
  } else { 
     var mlURL = "/sub-preparation-menu/"+whatvalue;
     universalAJAX("GET",mlURL,"",answerUpdatePrepmenu,2);
  }
}

function answerUpdatePrepmenu(rtnData) { 
  byId('fldSEGPreparationValue').value = '';
  byId('fldSEGPreparation').value = '';
  byId('ddSEGPreparationDropDown').innerHTML = "&nbsp;"; 
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    if (parseInt( dta['ITEMSFOUND'] ) > 0 ) {
      rsltTbl = "<table border=0 class=menuDropTbl>";
      dta['DATA'].forEach(function(element) { 
        rsltTbl += "<tr><td onclick=\"fillField('fldSEGPreparation','"+element['menuvalue']+"','"+element['longvalue']+"');\" class=ddMenuItem>"+element['longvalue']+"</td></tr>";
      }); 
      rsltTbl += "</table>";  
      byId('ddSEGPreparationDropDown').innerHTML = rsltTbl; 
    } else { 
      rsltTbl = "<table border=0 class=menuDropTbl>";
      dta['DATA'].forEach(function(element) { 
        rsltTbl += "<tr><td onclick=\"fillField('fldSEGPreparation','NOVAL','NO VALUE');\" class=ddMenuItem>NO VALUE</td></tr>";
      }); 
      rsltTbl += "</table>";  
      byId('ddSEGPreparationDropDown').innerHTML = rsltTbl; 
    }
  }
}

function fillField(whichfield, whichvalue, whichdisplay) { 
  if (byId(whichfield)) { 
     byId(whichfield).value = whichdisplay; 
     if (byId(whichfield+'Value')) { 
        byId(whichfield+'Value').value = whichvalue;    
     }
  }
  switch (whichfield) {
    case 'fldPRCProcedureDate':
      updateORSched(); 
    break;
    case 'fldPRCProcedureType':
      if (byId('fldPRCCollectionTypeValue')) { 
        byId('fldPRCCollectionTypeValue').value = "";
      }
      if (byId('fldPRCCollectionType')) { 
        byId('fldPRCCollectionType').value = "";
      }
      if (byId('ddPRCCollectionType')) { 
        byId('ddPRCCollectionType').innerHTML = "&nbsp;";
      } 
      updateSubMenu('PRCCollectionType','COLLECTIONT',whichvalue);
    break;
    case 'fldPRCSpecCat':
       //GET SITES
       byId('fldPRCDXOverride').checked = false;     
       fillField('fldPRCSSite','','');
       fillField('fldPRCSite','','');
       fillField('fldPRCDXMod','','');
       var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Site)</div>";     
       byId('ddPRCDXMod').innerHTML = menuTbl        
       byId('ddPRCSSite').innerHTML = menuTbl;        
       updateSiteMenu();
       if ( byId('fldPRCSpecCatValue').value === 'MALIGNANT') {
         //TURN ON METS
         if (byId('metsFromDsp')) { 
           byId('metsFromDsp').style.display = 'block';
         } 
       } else { 
         //TURN OFF METS
         if (byId('metsFromDsp')) { 
           byId('metsFromDsp').style.display = 'none';
         } 
       }  
    break;
    case 'fldPRCMETSSite':
     if ( byId('fldPRCMETSSiteValue').value.trim() !== ""  ) { 
       updateMETSDiagnosisMenu();
     }
    break;
    case 'fldPRCSite':
       byId('fldPRCDXOverride').checked = false;          
       byId('fldPRCDXMod').value = "";
       byId('fldPRCDXModValue').value = "";
       fillField('fldPRCSSite','',''); 
       updateSubSiteMenu();          
       updateDiagnosisMenu();                       
     break;
    case 'fldDNRTarget':
    //TODO:  Make DYNAMIC for value check
    if ( byId('fldDNRTarget').value === 'NOT RECEIVED') { 
      byId('fldDNRNotReceivedNote').value = "";
      byId('notRcvdNoteDsp').style.display = 'block';
    } else { 
      byId('fldDNRNotReceivedNote').value = "";
      byId('notRcvdNoteDsp').style.display = 'none';
    }
    break;
    case 'fldPRCPresentInst':
      alert('CHANGE Operative Schedule');
    break;
  }        
}            


BGJAVASCRPT;

}

return $rtnthis;

}

function reports($rqststr) { 

    $sp = serverpw; 
    $tt = treeTop;

    $rtnthis = <<<JAVASCR

document.addEventListener('DOMContentLoaded', function() {  

  if (byId('btnClearRptGrid')) { 
    byId('btnClearRptGrid').addEventListener('click', function() {
      clearRptParameterGrid();
    }, false);
  }

  if (byId('btnGenRptData')) { 
    byId('btnGenRptData').addEventListener('click', function() {
      makeReportDataRequest();
    }, false);
  }

  if (byId('btnGenRptPDF')) { 
    byId('btnGenRptPDF').addEventListener('click', function() {
      makeReportPDFRequest();
    }, false);
  }
            
  if (byId('btnRRExport')) { 
    byId('btnRRExport').addEventListener('click', function() {
      if (byId('jsonToExport')) {         
        var jsonToExport = byId('jsonToExport').innerHTML;
         JSONToCSVConvertor(jsonToExport, 'ScienceServer Report Results', false);       
      }
    }, false);
  }

}, false);        

function makeRequestArray() {
  var valuearr = new Object();
  var wherearr = new Object();
  var returnarr = new Object(); 
  if (byId('reportParameterGrid')) {
    var elearr = byId('reportParameterGrid').elements;
    var foundcount = 0;
    for (var i = 0; i < elearr.length; i++) {
      if (elearr[i].type == 'checkbox' && elearr[i].checked == true) {
        var fldnbr = elearr[i].id.replace('fldParaChkBx','');
        for (var j = 0; j < elearr.length; j++ ) {
          if (fldnbr == elearr[j].dataset.paracount) { 
            valuearr[elearr[j].dataset.criterianame] = elearr[j].value;           
          }
        }
        wherearr[foundcount] = elearr[i].dataset.sqlwhere;
        foundcount++;
      }
    }
  }
  returnarr['valuelist'] = valuearr; 
  returnarr['wherelist'] = wherearr;
  return returnarr;
}

function makeReportDataRequest() { 
  var requestArr = makeRequestArray();
  requestArr['typeofrequest'] = 'TABULAR';
  requestArr['requestedreporturl'] = byId('reporturlname').value;
  sendMakeReportReq(requestArr);
}

function makeReportPDFRequest() { 
  var requestArr = makeRequestArray();
  requestArr['typeofrequest'] = 'PDF';
  requestArr['requestedreporturl'] = byId('reporturlname').value;
  sendMakeReportReq(requestArr);
}

function sendMakeReportReq(requestArray) { 
  var dta = new Object(); 
  dta['request'] = requestArray;
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/create-report-obj";
  universalAJAX("POST",mlURL,passdata,answerSendMakeReportReq,1);
}

function answerSendMakeReportReq(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("REPORT OBJECT CREATION ERRORS:\\n"+dspMsg);  //DSIPLAY ERROR MESSAGE
   } else {  
     var prts = JSON.parse(rtnData['responseText']);
     switch (prts['DATA']['typerequested']) { 
       case 'TABULAR':
         navigateSite("reports/report-results/"+prts['DATA']['reportobject']);
       break;
       case 'PDF':
         openOutSidePage("{$tt}/print-obj/reports/"+prts['DATA']['reportobjectency']);
       break;  
     }
   }
}

function clearRptParameterGrid() { 
  if (byId('reportParameterGrid')) {
    var elearr = byId('reportParameterGrid').elements;
    for (var i = 0; i < elearr.length; i++) {
      if (elearr[i].type == 'checkbox' && elearr[i].disabled == false) { 
       elearr[i].checked = false;
      }
      if (elearr[i].type == 'text' || elearr[i].type == 'hidden') { 
        elearr[i].value = "";
      }
    }
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


JAVASCR;
return $rtnthis;
}

function hprreview($rqststr) { 

  $sp = serverpw;    
    
  if (trim($rqststr[2]) === "") { 
    //THIS IS THE JAVASCRIPT FOR THE QUERY GRID PAGE
    $rtnthis = <<<JAVASCR
            
        
            
document.addEventListener('DOMContentLoaded', function() {  

  if (byId('fldHPRScan')) { 
    byId('fldHPRScan').focus();
  }

  if (byId('btnHPRScanSearch')) { 
    byId('btnHPRScanSearch').addEventListener('click', function() { 
      sendHPRReviewRequest();
    } , false);
  }

  document.addEventListener('keypress', function(event) { 
    if (event.which === 13) { 
      sendHPRReviewRequest();
    }
  }, false);

}, false);

function sendHPRReviewRequest() { 
  if (byId('fldHPRScan')) { 
    if (byId('fldHPRScan').value.trim() !== "") { 
      var dta = new Object();
      dta['doctype'] = 'HPRWorkBenchRequest';
      dta['srchterm'] = byId('fldHPRScan').value.trim();    
      var passdata = JSON.stringify(dta);
      var mlURL = "/data-doers/doc-search";
      universalAJAX("POST",mlURL,passdata,answerSendHPRReviewRequest,1);
    } else { 
      alert('You haven\'t scanned/entered a tray #, biogroup, or slide');
    }
  }
}

function answerSendHPRReviewRequest(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    navigateSite('hpr-review/'+rcd['MESSAGE']);
  } else { 
    var rcd = JSON.parse(rtnData['responseText']);
    alert(rcd['MESSAGE']);  
  }
}


JAVASCR;
  
  } else { 
    //THIS IS THE JAVASCRIPT FOR THE RESULTS-WORK PAGE
    $rtnthis = <<<JAVASCR
                        
document.addEventListener('DOMContentLoaded', function() {  


}, false);

function changeReviewDisplay(whichdisplay) {
  if (byId('reviewersWorkBenchConfirm')) { byId('reviewersWorkBenchConfirm').style.display = 'none'; }
  if (byId('reviewersWorkBenchAdd')) { byId('reviewersWorkBenchAdd').style.display = 'none'; }
  if (byId('reviewersWorkBenchDeny')) { byId('reviewersWorkBenchDeny').style.display = 'none'; }
  if (byId('reviewersWorkBenchIncon')) { byId('reviewersWorkBenchIncon').style.display = 'none'; }
  if (byId('reviewersWorkBenchUnuse')) { byId('reviewersWorkBenchUnuse').style.display = 'none'; } 
  if (byId('divWorkBenchPathRptDsp')) { byId('divWorkBenchPathRptDsp').style.display = 'none'; }
  if (byId(whichdisplay)) { byId(whichdisplay).style.display = 'block'; }
}

function requestSegmentInfo(whichsegment, whichpbiosample) { 
  if (whichsegment.trim() !== "") {  
      var dta = new Object();
      dta['segmentid'] = whichsegment;
      dta['pbiosample'] = whichpbiosample;
      var passdata = JSON.stringify(dta);
      var mlURL = "/data-doers/hpr-workbench-builder";
      universalAJAX("POST",mlURL,passdata,answerHPRWorkBenchSegmentLookup,1);
  }
}

function answerHPRWorkBenchSegmentLookup(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //alert("HPR WORKBENCH ERROR:\\n"+dspMsg);  //DSIPLAY ERROR MESSAGE
   } else {  
            var prts = JSON.parse(rtnData['responseText']);
            if (byId('workBench')) { 
              byId('workBench').innerHTML = prts['DATA']['workbenchpage'];
            }
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
            
function triggerMolecularFill(menuid, menuval, valuedsp) { 

   if (menuid === 0) { 
     //CLEAR FIELD
       byId('hprFldMoleTestValue').value = "";
       byId('hprFldMoleTest').value = "";     
       byId('hprFldMoleResult').value = "";
       byId('hprFldMoleResultValue').value = "";    
   } else { 
       byId('hprFldMoleTestValue').value = menuval;
       byId('hprFldMoleTest').value = valuedsp;    
       var mlURL = "/immuno-mole-result-list/" + menuid;
      universalAJAX("GET",mlURL,'',answerHPRTriggerMolecularFill,1);            
   }
            
}
            
function answerHPRTriggerMolecularFill(rtnData) {
   var resultTbl = "";         
   if (parseInt(rtnData['responseCode']) !== 200) {             
   } else { 
   
     var dta = JSON.parse(rtnData['responseText']);
     var resultTbl = "<table border=0 class=\"menuDropTbl\">";
     resultTbl += "<tr><td onclick=\"fillField('hprFldMoleResult','','');\" align=right class=ddMenuClearOption>[clear]</td></tr>";            
     dta['DATA'].forEach(function(element) { 
       //element['menuvalue']      
       resultTbl += "<tr><td class=ddMenuItem onclick=\"fillField('hprFldMoleResult','"+element['menuvalue']+"','"+element['dspvalue']+"');\">"+element['dspvalue']+"</td></tr>";
     });  
     resultTbl += "</table>";    
        
   }
   if (byId('moleResultDropDown')) { 
     byId('moleResultDropDown').innerHTML = resultTbl;         
   }
}
            
            
function manageMoleTest(addIndicator, referencenumber) { 
 
  if (byId('molecularTestJsonHolderConfirm')) { 
   
   if (byId('molecularTestJsonHolderConfirm').value === "") { 
     if (addIndicator === 1) { 
        var hldVal = [];
        hldVal.push(  [ byId('hprFldMoleTestValue').value,  byId('hprFldMoleTest').value, byId('hprFldMoleResultValue').value, byId('hprFldMoleResult').value, byId('hprFldMoleScale').value.trim()      ] );    
        byId('molecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);
      }
    } else { 
      if (addIndicator === 1) { 
        var hldVal = JSON.parse(byId('molecularTestJsonHolderConfirm').value);
        hldVal.push(  [ byId('hprFldMoleTestValue').value,  byId('hprFldMoleTest').value, byId('hprFldMoleResultValue').value, byId('hprFldMoleResult').value, byId('hprFldMoleScale').value.trim()      ] );    
        byId('molecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);
      }
      if (addIndicator === 0) { 
         var hldVal = JSON.parse(byId('molecularTestJsonHolderConfirm').value);             
         var newVal = [];
         var key = 0;   
         hldVal.forEach(function(ele) { 
            if (key !== referencenumber) {
              newVal.push(ele);    
            }
            key++;
         });
         hldVal = newVal;
         byId('molecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);   
      }      
    }
    
         byId('hprFldMoleTestValue').value = "";
         byId('hprFldMoleTest').value = "";
         byId('hprFldMoleResultValue').value = "";
         byId('hprFldMoleResult').value = "";
         byId('hprFldMoleScale').value = "";            
    var moleTestTbl = "<table cellspacing=0 cellpadding=0>";
    var cntr = 0;         
    hldVal.forEach(function(element) {
            
            moleTestTbl += "<tr onclick=\"manageMoleTest(0,"+cntr+");\"><td><td>"+element[1]+"</td><td>"+element[3]+"</td><td>"+element[4]+"</td></tr>";
            cntr++;
     });
     moleTestTbl += "</table>";
     byId('dspDefinedMolecularTestsConfirm').innerHTML = moleTestTbl;       
            
  }

}
           
JAVASCR;

  }
    
return $rtnthis;
}

function datacoordinator($rqststr) { 
    
  session_start(); 
    
  $tt = treeTop;
  $eMod = encryptModulus;
  $eExpo = encryptExponent;
  $si = serverIdent;
  $pw = serverpw;
    
$rtnthis = <<<JAVASCR

var rowidclick = "";

var key;         
document.addEventListener('DOMContentLoaded', function() {  
     
  if (byId('cntxEditBG')) { 
    byId('cntxEditBG').addEventListener('click', function() { 
      if (rowidclick !== "") {
        //alert(byId(rowidclick).dataset.ebiogroup);  
        openRightClickMenu('',''); //CLOSE MENU
      }
    }, false);
  }
 
  if (byId('cntxPrntSD')) { 
    byId('cntxPrntSD').addEventListener('click', function() { 
      if (rowidclick !== "") {
        openOutSidePage("{$tt}/print-obj/shipment-manifest/"+byId(rowidclick).dataset.eshipdoc);  
        openRightClickMenu('',''); //CLOSE MENU
      }
    }, false);
  }

  if (byId('qryBG')) { 
    byId('qryBG').focus();
  }

  if (byId('btnBarRsltNew')) { 
    byId('btnBarRsltNew').addEventListener('click', function() { 
      navigateSite('data-coordinator');
    }, false);
  }

  if (byId('btnBarRsltInventoryOverride')) { 
    byId('btnBarRsltInventoryOverride').addEventListener('click', function() {       
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        //console.log(passdta);
        var mlURL = "/data-doers/preprocess-inventory-override";
        universalAJAX("POST",mlURL,passdta,answerPreprocessInventoryOverride,1);   
      } else { 
        alert(selection['message']);
      }
    }, false);
  }

  if (byId('btnBarRsltExport')) { 
    byId('btnBarRsltExport').addEventListener('click', function() { 
      exportResults();
    }, false);
  }

  if (byId('btnBarRsltToggle')) { 
    byId('btnBarRsltToggle').addEventListener('click', function() { 
      toggleSelect();
    }, false);
  }

  if (byId('btnBarRsltParams')) { 
    byId('btnBarRsltParams').addEventListener('click', function() { 
      displayParameters();
    }, false);
  }

  if (byId('btnBarRsltMakeSD')) { 
    byId('btnBarRsltMakeSD').addEventListener('click', function() { 
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        var mlURL = "/data-doers/preprocess-shipdoc";
        universalAJAX("POST",mlURL,passdta,answerPreprocessShipDoc,1);   
      } else { 
        alert(selection['message']);
      }
    }, false);
  }

  if (byId('btnBarRsltAssignSample')) { 
    byId('btnBarRsltAssignSample').addEventListener('click',function() { 
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        var mlURL = "/data-doers/preprocess-assign-segments";
        universalAJAX("POST",mlURL,passdta,answerAssignBG,1);   
      } else { 
        alert(selection['message']);
      }
    }, false );
  }

  if (byId('btnBarRsltSubmitHPR')) { 
    byId('btnBarRsltSubmitHPR').addEventListener('click', function() { 
      //SUBMIT TO HPR
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        var mlURL = "/data-doers/preprocess-override-hpr";
        universalAJAX("POST",mlURL,passdta,answerPreprocessOverrideHPR,1);   
      } else { 
        alert(selection['message']);
      }
    }, false);
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
      }, false);       
    });
  }

  if (byId('resultTblContextMenu')) { 
     byId('resultTblContextMenu').addEventListener('mouseout', function() { 
         //openRightClickMenu('resultstable','');
     }, false);
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
      dta['diagnosis'] = byId('qryDXDDiagnosis').value.trim();  
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

  if (byId('btnPrintAllPathologyRpts')) { 
    byId('btnPrintAllPathologyRpts').addEventListener('click', function() { 
      var prlist = [];        
      if (byId('coordinatorResultTbl')) { 
        for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) {  
          if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected === 'true') { 
            if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printprid.trim() !== "" && !inArray( byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printprid.trim(),prlist)) {
              openOutSidePage('{$tt}/print-obj/pathology-report/'+byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printprid.trim());  
              prlist.push(byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printprid.trim());
            }
          } 
        }
      }           
    }, false);
  }

  if (byId('btnPrintAllShipDocs')) { 
    byId('btnPrintAllShipDocs').addEventListener('click', function() { 
      var sdlist = [];        
      if (byId('coordinatorResultTbl')) { 
        for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) {  
          if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected === 'true') { 
            if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printsdid.trim() !== "" && !inArray( byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printsdid.trim(),sdlist)) {
              openOutSidePage('{$tt}/print-obj/shipment-manifest/'+byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printsdid.trim()); 
              sdlist.push(byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.printsdid.trim()); 
            }
          } 
        }
      }          
    }, false);
  }

  if (byId('btnPrintAllLabels')) { 
    byId('btnPrintAllLabels').addEventListener('click', function() { 
      var selection = gatherSelection();
      if (parseInt(selection['responseCode']) === 200) { 
        var passdta = JSON.stringify(selection['selectionListing']);
        //console.log(passdta);
        var mlURL = "/data-doers/preprocess-label-print";
        universalAJAX("POST",mlURL,passdta,answerPreprocessLabelPrint,1);   
      } else { 
        alert(selection['message']);
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
  
function editPathRpt(e, docid) { 
  e.stopPropagation();
  if (docid.toString().trim() !== "") { 
    var dta = new Object(); 
    dta['docid'] = docid;
    var passdata = JSON.stringify(dta);
    var mlURL = "/data-doers/preprocess-pathology-rpt-edit";
    universalAJAX("POST",mlURL,passdata,answerEditPathRpt, 1);
  }
}

function answerPreprocessInventoryOverride(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Inventory Over-ride Error:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "8vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "3vh";
       //byId('systemDialogTitle').style.width = "82vw";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}
              
function answerPreprocessLabelPrint(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Print Thermal Labels Error:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "15vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "5vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }    
}

function sendLblPrintRequest() { 
   if (byId('fldDialogLabelPrnterValue') && byId('fldDialogLabelQTY') && byId('segmentListingPayLoad') ) { 
     var obj = new Object();    
     obj['payload'] = byId('segmentListingPayLoad').value; 
     obj['formatname'] = byId('fldDialogLabelPrnterValue').value;          
     obj['qty'] = byId('fldDialogLabelQTY').value;         
     var passdata = JSON.stringify(obj);
     var mlURL = "/data-doers/label-print-request";
     universalAJAX("POST",mlURL,passdata, answerLabelPrintRequest,2);                  
  }
}

function answerLabelPrintRequest(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Label Print Error:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       byId('standardModalDialog').innerHTML = "";
       byId('standardModalBacker').style.display = 'none';
       byId('standardModalDialog').style.display = 'none';
     }  
   }              
}
                 
function answerEditPathRpt(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Pathology Report Edit Error:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "8vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "3vh";
       byId('systemDialogTitle').style.width = "82vw";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

function getUploadNewPathRpt(e, labelNbr) { 
  e.stopPropagation(); 
  if (labelNbr.toString().trim() !== "") { 
    var mlURL = "/preprocess-pathology-rpt-upload/"+labelNbr.toString();
    universalAJAX("GET",mlURL,"",answerGetUploadNewPathRpt, 1);
  }
}

function answerGetUploadNewPathRpt(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Pathology Report Upload Error:\\n"+dspMsg);
   } else { 
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];
       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "8vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "3vh";
       byId('systemDialogTitle').style.width = "82vw";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

function editPathologyReportText() { 
  var dta = new Object(); 
  dta['labelNbr'] = byId('fldDialogPRUPLabelNbr').value.trim();
  dta['bg'] = byId('fldDialogPRUPBG').value.trim();
  dta['user'] = byId('fldDialogPRUPUser').value.trim();
  dta['pxiid'] = byId('fldDialogPRUPPXI').value.trim();
  dta['sess'] = byId('fldDialogPRUPSess').value.trim();
  dta['prtxt'] = byId('fldDialogPRUPPathRptTxt').value.trim();
  dta['prid'] = byId('fldDialogPRUPPRID').value.trim();
  dta['hipaacert'] = byId('HIPAACertify').checked;
  dta['usrpin'] = window.btoa( encryptedString(key, byId('fldUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
  dta['editreason'] = byId('fldDialogPRUPEditReason').value.trim();
  var passdata = JSON.stringify(dta); 
  //TODO MAKE A 'PLEASE WAIT' INDICATION - AS THIS PROCESS CAN TAKE UP TO 10+ SECONDS 
  var mlURL = "/data-doers/pathology-report-coordinator-edit";
  universalAJAX("POST",mlURL,passdata, answerEditPathologyReportText,2);          
}

function answerEditPathologyReportText(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("PATHOLOGY REPORT EDIT ERROR:\\n"+dspMsg);
   } else { 
    alert('PATHOLOGY REPORT WAS SUCCESSFULLY SAVED');
    byId('fldDialogPRUPLabelNbr').value = "";
    byId('fldDialogPRUPBG').value = "";
    byId('fldDialogPRUPUser').value = "";
    byId('fldDialogPRUPSess').value = "";
    byId('fldDialogPRUPPathRptTxt').value = "";
    byId('fldDialogPRUPPRID').value = "";
    byId('HIPAACertify').checked = false;
    byId('fldUsrPIN').value = "";
    byId('fldDialogPRUPEditReason').value = ""; 
    byId('standardModalDialog').style.display = 'none';
    location.reload();
  }
}

function uploadPathologyReportText() { 
  var dta = new Object(); 
  dta['labelNbr'] = byId('fldDialogPRUPLabelNbr').value.trim();
  dta['bg'] = byId('fldDialogPRUPBG').value.trim();
  dta['user'] = byId('fldDialogPRUPUser').value.trim();
  dta['pxiid'] = byId('fldDialogPRUPPXI').value.trim();
  dta['sess'] = byId('fldDialogPRUPSess').value.trim();
  dta['prtxt'] = byId('fldDialogPRUPPathRptTxt').value.trim();
  dta['hipaacert'] = byId('HIPAACertify').checked;
  dta['usrpin'] = window.btoa( encryptedString(key, byId('fldUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
  dta['deviation'] = byId('fldDialogPRUPDeviationReason').value.trim();
  var passdata = JSON.stringify(dta); 
  //TODO MAKE A 'PLEASE WAIT' INDICATION - AS THIS PROCESS CAN TAKE UP TO 10+ SECONDS 
  var mlURL = "/data-doers/pathology-report-upload-override";
  universalAJAX("POST",mlURL,passdata, answerUploadPathologyReportText,2);          
}

function answerUploadPathologyReportText(rtnData) {   
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("PATHOLOGY REPORT UPLOAD ERROR:\\n"+dspMsg);
   } else { 
    //PATH REPORT HAS BEEN SAVED
    alert('PATHOLOGY REPORT WAS SUCCESSFULLY UPLOADED');
    byId('fldDialogPRUPLabelNbr').value = "";
    byId('fldDialogPRUPBG').value = "";
    byId('fldDialogPRUPUser').value = "";
    byId('fldDialogPRUPSess').value = "";
    byId('fldDialogPRUPPathRptTxt').value = "";
    byId('HIPAACertify').checked = false;
    byId('fldUsrPIN').value = "";
    byId('fldDialogPRUPDeviationReason').value = ""; 
    byId('standardModalDialog').style.display = 'none';
    location.reload();
  }
}

function printPRpt(e, pathrptencyption) { 
  e.stopPropagation();
  if (pathrptencyption == '0') { 
  } else { 
  openOutSidePage('{$tt}/print-obj/pathology-report/'+pathrptencyption);  
  }
}

function displayShipDoc(e, shipdocencryption) {
  e.stopPropagation(); 
  openOutSidePage("{$tt}/print-obj/shipment-manifest/"+shipdocencryption);  
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
        sglist[cntr] = {biogroup : byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.biogroup , bgslabel : byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.bgslabel , segmentid:byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.segmentid };     
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
    var given = new Object(); 
    given['rqstsuggestion'] = 'vandyinvest-requests'; 
    given['given'] = byId('selectorAssignInv').value.trim();
    var passeddata = JSON.stringify(given);
    var mlURL = "/data-doers/suggest-something";
    universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
  } 
}

function answerRequestDrop(rtnData) { 
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=0 class=\"menuDropTbl\">";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('selectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); byId('requestDropDown').innerHTML = '&nbsp;';\">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
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
    var rsltTbl = "<table border=0 class=\"menuDropTbl\"><tr><td colspan=2 style=\"font-size: 1.2vh; padding: 8px;\">Below are suggestions for the investigator field. Use the investigator's ID.  These are live values from CHTN's TissueQuest. Found "+dta['ITEMSFOUND']+" matches.</td></tr>";
    dta['DATA'].forEach(function(element) { 
       rsltTbl += "<tr class=ddMenuItem onclick=\"fillField('selectorAssignInv','"+element['investvalue']+"','"+element['investvalue']+"'); byId('assignInvestSuggestion').innerHTML = '&nbsp;'; byId('assignInvestSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
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
      var rsltTbl = "<table border=0 class=\"menuDropTbl\"><tr><td colspan=2 style=\"font-size: 1.2vh; padding: 8px;\">Below are suggestions for the investigator field. Use the investigator's ID.  These are live values from CHTN's TissueQuest. Found "+dta['ITEMSFOUND']+" matches.</td></tr>";
      dta['DATA'].forEach(function(element) { 
      rsltTbl += "<tr class=ddMenuItem onclick=\"fillField('qryInvestigator','"+element['investvalue']+"','"+element['investvalue']+"'); byId('investSuggestion').innerHTML = '&nbsp;'; byId('investSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
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
 
  switch (whichfield) { 
     case 'qryDXDSite':
     //alert('HERE');
     break;
  }
   
}

var lastRequestCalendarDiv = "";
function getCalendar(whichcalendar, whichdiv, monthyear, modalCtl = 0) {
  var mlURL = "/sscalendar/"+whichcalendar+"/"+monthyear;
  lastRequestCalendarDiv = whichdiv;
  universalAJAX("GET",mlURL,"",answerGetCalendar,modalCtl);
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

function answerPreprocessOverrideHPR(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("QMS/HPR SUBMITTAL ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY OVERRIDE SCREEN
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];

       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "10vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "3vh";
       byId('standardModalDialog').style.width = "80vw";
       byId('systemDialogTitle').style.width = "80vw";

       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  

   }
}

function answerPreprocessShipDoc(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else { 
     //DISPLAY SHIPDOC CREATOR
     if (byId('standardModalDialog')) {
       var dta = JSON.parse(rtnData['responseText']); 
       byId('standardModalDialog').innerHTML = dta['DATA']['pagecontent'];

       byId('standardModalDialog').style.marginLeft = 0;
       byId('standardModalDialog').style.left = "10vw";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "3vh";
       byId('standardModalDialog').style.width = "80vw";
       byId('systemDialogTitle').style.width = "80vw";

       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
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

       byId('standardModalDialog').style.marginLeft = "-20vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = "-10vh";
       byId('standardModalDialog').style.top = "50%";
       byId('standardModalDialog').style.width = "40vw";
       byId('systemDialogTitle').style.width = "40vw";

       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
       byId('selectorAssignInv').focus();
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
    rsltTbl = "<table border=0 class=menuDropTbl><tr><td align=right onclick=\"fillField('qryPreparation','','');\" class=ddMenuClearOption>[clear]</td></tr>";
    dta['DATA'].forEach(function(element) { 
      rsltTbl += "<tr><td onclick=\"fillField('qryPreparation','"+element['menuvalue']+"','"+element['longvalue']+"');\" class=ddMenuItem>"+element['longvalue']+"</td></tr>";
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
          byId(whichrow).dataset.selected = "true";
        } else { 
          byId(whichrow).dataset.selected = "false";
        }
  }
}
        
function answerSendHPRSubmitOverride(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var rsp = JSON.parse(rtnData['responseText']);
    var dspMsg = "";
    rsp['MESSAGE'].forEach(function(element) { 
      dspMsg += "\\n - "+element;
    }); 
    alert("* * * * ERROR * * * * \\n\\n"+dspMsg);
  } else { 
    //Redirect to results
    alert('HPR TRAY OVERRIDE COMPLETE');
    location.reload();
  }        
}

function sendHPRTray() {
  var dta = new Object();
  var sldlst = new Object();       
  var cntr = 0;        
  var e = byId('frmQMSSubmitter').elements;
   for ( var i = 0; i < e.length; i++ ) {
      if (e[i].id.substr(0,6) === 'chkBox') {   
        if (e[i].checked) {      
          sldlst[cntr] = [byId('tr'+parseInt(e[i].id.substr(6))).dataset.bg , byId('tr'+parseInt(e[i].id.substr(6))).dataset.newqms, byId('fldSld'+parseInt(e[i].id.substr(6))).value, byId('fldSld'+parseInt(e[i].id.substr(6))+'Value').value];
          cntr++;
        }
      }
   }
   dta['slidelist'] = sldlst;
   dta['deviationreason'] = byId('fldDeviationReason').value;
   dta['hprtray'] = byId('fldHPRTray').value; 
   dta['invscancode'] = byId('fldHPRTrayValue').value;    
   dta['usrPIN'] = window.btoa( encryptedString(key, byId('fldUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );   
   var passdata = JSON.stringify(dta);        
   //TODO MAKE A 'PLEASE WAIT' INDICATION - AS THIS PROCESS CAN TAKE UP TO 10+ SECONDS 
   var mlURL = "/data-doers/inventory-hprtray-override";
   universalAJAX("POST",mlURL,passdata,answerSendHPRSubmitOverride,2);          
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
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var rsp = JSON.parse(rtnData['responseText']); 
    alert("* * * * ERROR * * * * \\n\\n"+rsp['MESSAGE']);
  } else { 
    //Redirect to results
    location.reload(); 
  }        
}

function toggleSelect() { 
  if (byId('coordinatorResultTbl')) { 
    for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) {  
      if (byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected === 'true') { 
        byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected = 'false';
      } else { 
        byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.selected = 'true';
      }
    }
  }
}

function exportResults() { 
  if (byId('urlrequestid')) {
   if (byId('urlrequestid').value.trim() !== "") {  
     //console.log(byId('urlrequestid').value);
     var mlURL = "/biogroup-search/"+byId('urlrequestid').value.trim();
     universalAJAX("GET",mlURL,"",answerExportResults,1);
   }
  }
}

function answerExportResults(rtnData) {
   var rcd = JSON.parse(rtnData['responseText']); 
   //TODO:  TAKE OUT EXTRA LINES
   JSONToCSVConvertor(JSON.stringify(rcd['DATA']['searchresults'][0]['data']), "BiogroupSearch", false);
}

function displayParameters() { 
  if (byId('recordResultDiv')) { 
    if (byId('recordResultDiv').style.display === 'block' || byId('recordResultDiv').style.display === '') { 
      byId('recordResultDiv').style.display = 'none';
      byId('dspParameterGrid').style.display = 'block';
    } else { 
      byId('recordResultDiv').style.display = 'block';
      byId('dspParameterGrid').style.display = 'none';
    }  
  }
}

function packCreateShipdoc() {
  var elements = document.getElementById("frmShipDocCreate").elements;
  var dta = new Object();
  var segments = [];  
  for (var i = 0; i < elements.length; i++) { 
    if ( elements[i].type === 'checkbox' ) {
      if (elements[i].id.substr(0,10) === 'sdcBGSList' ) {
        if (elements[i].checked) {  
          segments[segments.length] = { segmentid : byId(elements[i].id).dataset.segment, bgs : byId(elements[i].id).dataset.bgs  }; 
        }
      } 
    }
    dta[elements[i].id] = elements[i].value.trim();      
  }
  dta['listedSegments'] = JSON.stringify(segments);
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/shipdoc-quick-creator";
  universalAJAX("POST",mlURL,passdata,answerPackCreateShipdoc,2);
}

function answerPackCreateShipdoc(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("SHIPMENT DOCUMENT CREATION ERROR:\\n"+dspMsg);
  } else { 
    //Good results
    var answerBack = JSON.parse(rtnData['responseText']);
    var sdn = ('000000'+answerBack['DATA']['shipdocrefid']).substr(-6);
    alert("SHIPMENT DOCUMENT CREATED ("+  sdn   +")"); 
    location.reload(); 
  }        
}

function checkBoxIndicators(whichbox) { 
   var submittingCnt = 0;
   var e = byId('frmQMSSubmitter').elements;
   for ( var i = 0; i < e.length; i++ ) {
      if (e[i].id.substr(0,6) === 'chkBox') {   
        if (e[i].checked) {      
          submittingCnt++;
        }
      }
   }
   if (byId('nbrQMSSubmittal')) { 
       byId('nbrQMSSubmittal').innerHTML = submittingCnt;
   }
}
  
function updateStatuses() { 

  if ( byId('frmCheckInOverride')) { 
    var e = byId('frmCheckInOverride').elements;
    var lobj = new Object();
    var cntr = 0;
    for ( var i = 0; i < e.length; i++ ) {
       if ( e[i].id.substr(0,3) === 'fld') { 
         var idparts = e[i].id.replace(/^fld/,'').split(".");
         lobj[idparts[1].replace(/[Vv]alue$/,'')] = "";
       }
    }
   Object.keys(lobj).forEach(function(key) {
       lobj[key] = { bgs: byId('fldSegId.'+key).value, statusupdate: byId('fldNewStatus.'+key).value, loccode: byId('fldInvLoc.'+key+'Value').value, locdesc: byId('fldInvLoc.'+key).value  };
   });
   lobj['userid'] = window.btoa( encryptedString(key, byId('fldUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
   lobj['devReason'] = byId('fldDeviationReason').value;
   byId('waiterIndicator').style.display = 'block';
   //console.log(JSON.stringify(lobj));
   var passdata = JSON.stringify(lobj);
   var mlURL = "/data-doers/quick-segment-status-update";
   universalAJAX("POST",mlURL,passdata,answerUpdateStatuses,2);
  }
}
  
function answerUpdateStatuses(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     byId('waiterIndicator').style.display = 'none';
     alert("STATUS UPDATE ERROR:\\n"+dspMsg);
  } else { 
    //Good results
    location.reload(); 
  }   
}
        
JAVASCR;
    
return $rtnthis;
}

}

