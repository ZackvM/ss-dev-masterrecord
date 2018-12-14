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
     //https://dev.chtneast.org/print-obj/shipment-manifest/NXJYK0VMWDRzUHphcjc0aVIrczFxZz09
     //https://dev.chtneast.org/reports/inventory/barcode-run
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
function openRightClickMenu(whichmenu, whichelementclicked) { 
  if (byId('resultTblContextMenu')) { 
    if (byId('resultTblContextMenu').style.display === 'none' || byId('resultTblContextMenu').style.display === '') {
      rowidclick = whichelementclicked;
      var bg = byId(whichelementclicked).dataset.biogroup;
      var sg = byId(whichelementclicked).dataset.bgslabel;
      var sh = byId(whichelementclicked).dataset.shipdoc;
      console.log(sh);  
//      byId('EDITBGDSP').innerHTML = "Edit Biogroup "+bg; 
//      byId('EDITSEGDSP').innerHTML = "Edit Segment "+sg;  
      if (parseInt(sh) === 0) { 
//        byId('EDITSHPDOC').innerHTML = "Segment is not on a Ship-Doc";
        byId('PRINTSD').innerHTML = "No Ship-Doc to Print"; 
      } else {
        var shpnbr = ("000000"+sh).substr(-6);
//        byId('EDITSHPDOC').innerHTML = "Edit Ship-Doc "+shpnbr; 
        byId('PRINTSD').innerHTML = "Print Ship-doc "+shpnbr; 
      }
      byId('resultTblContextMenu').style.left = (mousex - 10) + "px";
      byId('resultTblContextMenu').style.top = (mousey - 10) + "px";
      byId('resultTblContextMenu').style.display = "block";
    } else { 
      rowidclick = "";
//      byId('EDITBGDSP').innerHTML = "Edit Biogroup"; 
//      byId('EDITSEGDSP').innerHTML = "Edit Segment"; 
//      byId('EDITSHPDOC').innerHTML = "Edit Ship-Doc"; 
      byId('PRINTSD').innerHTML = "View/Print Ship-Doc"; 
      byId('resultTblContextMenu').style.left = "-999px";
      byId('resultTblContextMenu').style.top = "-999px";
      byId('resultTblContextMenu').style.display = "none";
    }
  }
}    

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
      }, false)   ;       
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
      rsltTbl += "<tr><td onclick=\"fillField('qryPreparation','"+element['menuValue']+"','"+element['longValue']+"');\" class=ddMenuItem>"+element['longValue']+"</td></tr>";
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
     console.log(byId('urlrequestid').value);
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
        
JAVASCR;
    
return $rtnthis;
}

}

