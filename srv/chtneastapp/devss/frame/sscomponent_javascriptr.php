<?php

class javascriptr {

  private $teststring = "";
  private $sessid = "";
  private $regcode = "";

  function __construct() {
    session_start();    
    $this->teststring = "ZACK WAS HERE IN THE TEST STRING";
    $this->sessid = session_id();
    $this->regcode = registerServerIdent(session_id()); 
  }

//dta['preparation'] = byId('astRequestPrep').value.trim();               

  function useradministration ( $rqststr ) { 
    $tt = treetop; 
     $rtnThis = <<<RTNTHIS

function getUserSpecifics ( uency ) { 
  byId('defineUserSide').innerHTML = "";
  var given = new Object();  
  given['uency'] = uency; 
  var passeddata = JSON.stringify(given);
  var mlURL = "/data-doers/user-get-specifics-dsp";
  universalAJAX("POST",mlURL,passeddata,answerGetUserSpecifics,1);
}

function answerGetUserSpecifics ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("ERROR:\\n"+dspMsg);
   } else {
     var rsp = JSON.parse(rtnData['responseText']); 
     byId('defineUserSide').innerHTML = rsp['DATA'];
   }                  
}

function sendUnlock ( uency ) { 
  var given = new Object();  
  given['uency'] = uency; 
  var passeddata = JSON.stringify(given);
  var mlURL = "/data-doers/user-send-unlock";
  universalAJAX("POST",mlURL,passeddata,answerSendUnlock,1);
}

function answerSendUnlock ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Lock out has been removed.  Refresh your screen');
   }                  
}

function sendResetPassword( uency ) { 
  var given = new Object();  
  given['uency'] = uency; 
  var passeddata = JSON.stringify(given);
  var mlURL = "/data-doers/user-send-reset-password";
  universalAJAX("POST",mlURL,passeddata,answerSendResetPassword,1);              
}
             
function answerSendResetPassword ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Password Reset Email has been sent.\\n\\nUser Account has been reactivated and expiration has been set for two days from now\\n\\nTo see these changes refresh you screen');
   }                  
}
                         
function toggleAllow(uency, toggleind ) { 
  var given = new Object();  
  given['uency'] = uency; 
  given['toggleind'] = toggleind;
  var passeddata = JSON.stringify(given);
  var mlURL = "/data-doers/user-toggle-allow";
  universalAJAX("POST",mlURL,passeddata,answerUserToggleAllow,1);             
}

function answerUserToggleAllow ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("ERROR:\\n"+dspMsg);
   } else {
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
             
function sendChangeModHeaderRequest(whichmodheader, whatvalue) { 
  var given = new Object();  
  given['uency'] = byId('updFldIdent').value; 
  given['toggleind'] = whichmodheader;
  given['togglevalue'] = whatvalue;             
  var passeddata = JSON.stringify(given);
  var mlURL = "/data-doers/user-toggle-mod-header";
  universalAJAX("POST",mlURL,passeddata,answerUserToggleModHeader,1);            
}
             
function answerUserToggleModHeader ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("ERROR:\\n"+dspMsg);
   } else {
   }     
}
             
RTNTHIS;
     return $rtnThis;
  }
 
  function astrequestlisting ( $rqststr ) {
     $tt = treetop; 
     $rtnThis = <<<RTNTHIS

document.addEventListener('DOMContentLoaded', function() { 

if ( byId('btnLookup') ) { 
   byId('btnLookup').addEventListener('click', sendQryRequest);
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


}, false);


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
 
function sendQryRequest() { 
  var dta = new Object();
  dta['qryType'] = 'ASTREQ';
  dta['RQStatus'] = byId('astRequestStatus').value.trim(); 
  dta['SearchTerm'] = byId('astSearchTerm').value.trim(); 
  dta['SPCTerm'] = byId('astRequestSPC').value.trim();               
  dta['investid'] = byId('qryInvestigator').value.trim();               
  var passdta = JSON.stringify(dta);    
  var mlURL = "/data-doers/make-query-request";
  universalAJAX("POST",mlURL,passdta,answerQueryRequest,1);           
}

function answerQueryRequest(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var rsp = JSON.parse(rtnData['responseText']); 
    alert("* * * * ERROR * * * * \\n\\n"+rsp['MESSAGE']);
  } else { 
    //Redirect to results
    var rsp = JSON.parse(rtnData['responseText']); 
    navigateSite("ast-request-listing/"+rsp['DATA']['astsearchid']);
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


RTNTHIS;
     return $rtnThis;

  }

function shipmentdocument ( $rqststr ) { 

    $tt = treeTop;
    
    $rtnThis = <<<RTNTHIS

document.addEventListener('DOMContentLoaded', function() {
    
  if (byId('btnSDLookup')) { 
    byId('btnSDLookup').addEventListener('click', function() { 
      navigateSite('shipment-document');
    }, false);
  }
      
 if (byId('btnCreateNewSD')) { 
    byId('btnCreateNewSD').addEventListener('click', function() { 
      navigateSite('shipment-document/set-up-new-ship-doc');
    }, false);
  } 

 if (byId('btnSaveSD')) { 
    byId('btnSaveSD').addEventListener('click', function() { 
      saveShipDoc();
    }, false);
  }

 if ( byId('btnPrintSD') ) {
   byId('btnPrintSD').addEventListener('click', function() { 
       if ( byId('sdnbrency') ) {       
         openOutSidePage("{$tt}/print-obj/shipment-manifest/"+byId('sdnbrency').value);
      }
    }, false);     
 }
            
 if (byId('btnAddSegment')) { 
    byId('btnAddSegment').addEventListener('click', function() { 
     var obj = new Object();
     obj['sdency'] = byId('sdency').value.trim();
     var passdata = JSON.stringify(obj);
     generateDialog( 'shipdocaddsegment', passdata );
    }, false);
  }

  if ( byId('btnAddSpcSrvcFee')) { 
    byId('btnAddSpcSrvcFee').addEventListener('click', function() { 
     var obj = new Object();
     obj['sdency'] = byId('sdency').value.trim();
     var passdata = JSON.stringify(obj);
     generateDialog( 'shipdocspcsrvfee', passdata );
    }, false);
  }

 if (byId('btnShipOverride')) { 
    byId('btnShipOverride').addEventListener('click', function() { 
     var obj = new Object();
     obj['sdency'] = byId('sdency').value.trim();
     var passdata = JSON.stringify(obj);
     generateDialog( 'shipdocshipoverride', passdata );
    }, false);
  }

 if (byId('btnVoidSD')) { 
    byId('btnVoidSD').addEventListener('click', function() { 
      alert('THIS FUNCTION IS NOT YET OPERATIONAL.  TRY AGAIN LATER');
    }, false);
  }   
         
  if (byId('btnLookup')) { 
    byId('btnLookup').addEventListener('click', function() { 
      performLookup();
    }, false);      
  }           

  if (byId('btnAddSO')) { 
    byId('btnAddSO').addEventListener('click', function() { 
     var obj = new Object();
     obj['sdency'] = byId('sdency').value.trim();
     var passdata = JSON.stringify(obj);
     generateDialog( 'shipdocaddso', passdata );
    }, false);      
  } 

  if ( byId('qryShipDoc')) {            
    byId('qryShipDoc').focus();
  }   
                                 
}, false);

 function saveSDService() { 
   var obj = new Object();
   obj['sdsrvcency'] = byId('sdsrvcency').value.trim();
   obj['spcsrvcvalue'] = byId('spcSrvcValue').value.trim();
   obj['spcsrvcrate'] = byId('spcRate').value.trim();
   obj['spcsrvcqty'] = byId('spcQty').value.trim();
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/shipdoc-special-service-add";
   universalAJAX("POST",mlURL,passdata,answerSaveSDService,2);         
 }

 function answerSaveSDService ( rtnData ) {
   //console.log( rtnData );
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     byId('spcSrvcValue').value = '';
     byId('spcSrvc').value = '';
     byId('spcRate').value = '';
     byId('spcQty').value = '';
     alert('Special Service Fee has been saved to this Ship-Doc.\\n\\nEither Continue adding Service Fees or refresh your screen to see changes');
   }         
 }

 function saveSOOverride() { 
   var obj = new Object();
   obj['sdency'] = byId('soSDEncy').value.trim();
   obj['dialogid'] = byId('soDLGId').value.trim(); 
   obj['sonbr'] = byId('soSONbr').value;  
   obj['soamt'] = byId('soSOAmt').value;  
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/shipdoc-override-salesorder";
   universalAJAX("POST",mlURL,passdata,answerOverrideSalesOrder,2);         
 }
         
function answerOverrideSalesOrder (rtnData) { 
   //console.log( rtnData );
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']); 
     closeThisDialog ( dta['DATA']['dialogid'] );
     alert('SALES ORDER SAVED');
     location.reload(true);  
   }         
}
         
function sendOverrideShip() { 
   var obj = new Object();
   obj['sdency'] = byId('sdency').value.trim();
   obj['sdshipdte'] = byId('sdcActualShipDateValue').value;
   obj['couriertrck'] = byId('sdcCourierTrack').value;
   obj['deviationreason'] = byId('sdcActDeviationReason').value;
   obj['usrpin'] = window.btoa( encryptedString(key, byId('fldUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
   obj['dialogid'] = byId('pdDialogId').value.trim();
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/shipdoc-override-shipdate";
   universalAJAX("POST",mlURL,passdata,answerOverrideShipdate,2);
}

function answerOverrideShipdate( rtnData ) { 
   console.log( rtnData );
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']); 
     closeThisDialog ( dta['DATA']['dialogid'] );
     alert('OVERRIDE SAVED');
     location.reload(true);  
   }
}

function BGSLookupRqst(sdency) { 
   var obj = new Object();
   obj['bgs'] = byId('qryBGS').value;
   obj['sdency'] = sdency;   
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/bgs-look-up-request";
   universalAJAX("POST",mlURL,passdata,answerDisplayBGSLookup,2);
}

function answerDisplayBGSLookup( rtnData ) { 
// console.log( rtnData );
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']); 
     byId('segBGSLookupRslts').innerHTML = dta['DATA']['return']; 
   }
}

function addSegmentToShipDoc( sdency, sidency, whichsegdsp ) { 
   var obj = new Object();
   obj['sdency'] = sdency;
   obj['segid'] = sidency;
   obj['dspnbr'] = whichsegdsp;
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/shipdoc-add-segment";
   universalAJAX("POST",mlURL,passdata,answerAddBGSToSD,2);
} 

function answerAddBGSToSD ( rtnData ) { 
 // console.log( rtnData );
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     alert('SEGMENT HAS BEEN ADDED TO THE SHIP DOC. CONTINUE ADDING SEGMENTS OR RE-FRESH YOUR SCREEN TO REVIEW CHANGES.');
     var dta = JSON.parse(rtnData['responseText']); 
     var elem = byId("sgAssList"+dta['DATA']['dspid']);
     elem.parentElement.removeChild(elem); 
   }
}

function sendRemovalCmd() { 
  if ( !byId('pdSegId') || !byId('pdDspCell') || !byId('pdSDEncy') || !byId('pdRestockStatusValue') || !byId('pdRestockReasonValue') || !byId('pdRestockNote') ) { 
    alert('MISSING ELEMENT.  SEE CHTNEASTERN INFORMATICS STAFF');
  } else { 
   var obj = new Object();
   obj['sdency'] = byId('pdSDEncy').value.trim();
   obj['segid'] = byId('pdSegId').value.trim();
   obj['dspcell'] = byId('pdDspCell').value.trim();
   obj['segstatus'] = byId('pdRestockStatusValue').value.trim();
   obj['rreason'] = byId('pdRestockReasonValue').value.trim();
   obj['rnote'] = byId('pdRestockNote').value.trim();
   obj['dialogid'] = byId('pdDialogId').value.trim();
   var passdata = JSON.stringify(obj);
   var mlURL = "/data-doers/shipdoc-remove-segment";
   universalAJAX("POST",mlURL,passdata,answerRemoveBGSFromSD,2);
  }
}

function answerRemoveBGSFromSD(rtnData) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']); 
     var elem = document.getElementById("cell"+dta['DATA']['dspcellid']);
     elem.parentElement.removeChild(elem); 
     closeThisDialog ( dta['DATA']['dialogid'] );
   }
}        

function removeSpcSrv( ssfid, cellid ) {
   var obj = new Object();
   obj['sdency'] = byId('sdency').value.trim();
   obj['ssfid'] = ssfid;
   obj['dspcell'] = cellid; 
   var passdata = JSON.stringify(obj);
   console.log( passdata );
   var mlURL = "/data-doers/shipdoc-remove-spc-srv-fee";
   universalAJAX("POST",mlURL,passdata,answerRemoveSSFFromSD,2);
}

function answerRemoveSSFFromSD ( rtnData ) {
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     //ERROR MESSAGE HERE
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']); 
     var elem = document.getElementById("cell"+dta['DATA']['dspcellid']);
     elem.parentElement.removeChild(elem); 
   }
}

function removeBGSfromSD(segid, cellid) {
  if (!byId('sdency') || byId('sdency').value.trim() === "") { 
  } else { 
   var obj = new Object();
   obj['sdency'] = byId('sdency').value.trim();
   obj['segid'] = segid;
   obj['dspcell'] = cellid; 
   var passdata = JSON.stringify(obj);
   generateDialog( 'preprocremovesdsegment', passdata );
  }
}

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function performLookup() { 
   var obj = new Object();
   var allfieldsfound = 1;
   ( byId('qryShipDoc') ) ? obj['qryshipdoc'] = byId('qryShipDoc').value : allfieldsfound = 0;     
   if ( allfieldsfound === 1 ) { 
     var passdata = JSON.stringify(obj);
     var mlURL = "/data-doers/lookup-ship-doc-qry";
     universalAJAX("POST",mlURL,passdata,answerLookupShipDocQry,1);
     console.log(passdata);
  } else { 
     alert('ERROR WITH PAYLOAD PACKAGE.  SEE A CHTNEASTERN INFORMATICS MEMBER');
  }    
}
    
function answerLookupShipDocQry ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('displayLookedupData').innerHTML = "";
    byId('standardModalBacker').style.display = 'none';    
   } else {
     //SUCCESS
     var dta = JSON.parse( rtnData['responseText'] );
     byId('displayLookedupData').innerHTML = dta['DATA'];
   }

}
         
function saveShipDoc() { 
  var elements = byId("frmSDHeadSection").elements;
  var dta = new Object();
  for (var i = 0; i < elements.length; i++) { 
    if ( parseInt(elements[i].dataset.frminclude) === 1 ) {
      dta[elements[i].id] = elements[i].value.trim();      
    }
  }
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/shipdoc-screen-save";
  universalAJAX("POST",mlURL,passdata,answerShipDocScreenSave,1);
}

function answerShipDocScreenSave( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("SHIPMENT DOCUMENT ERROR:\\n"+dspMsg);
   } else { 
    alert('SHIP-DOC HEADER SUCCESSFULLY SAVED.  YOUR SCREEN WILL NOW REFRESH');
    location.reload(true); 
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
  //console.log(whichdiv);          
  var mlURL = "/sscalendar/"+whichcalendar+"/"+monthyear;
  lastRequestCalendarDiv = whichdiv;
  universalAJAX("GET",mlURL,"",answerGetCalendar,modalCtl);
}

function answerGetCalendar(rtnData) {
  //console.log( rtnData );
  if (parseInt(rtnData['responseCode']) === 200) {     
    var rcd = JSON.parse(rtnData['responseText']);
    if (byId(lastRequestCalendarDiv)) { 
      byId(lastRequestCalendarDiv).innerHTML = rcd['DATA']; 
    } else { 
      console.log('NO DIV');
    }
  } else { 
    alert("ERROR");  
  }
}

RTNTHIS;

  return $rtnThis;

}

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

function makeEventDialog(eventDate) { 
  generateDialog('eventCalendarEventAdd',eventDate);
}

function enlargeDashboardGraphic(whichgraphic) { 
  generateDialog('enlargeDashboardGraphic',whichgraphic);
}
            
function rootCalendarDeleteEvent(whichEventId) { 
  var dta = new Object(); 
  dta['calEventId'] = whichEventId; 
  dta['calEventDialogId'] = byId('dialogidhld').value;
  var passdta = JSON.stringify(dta);
  //console.log( passdta);          
  var mlURL = "/data-doers/root-calendar-event-delete";
  universalAJAX("POST",mlURL,passdta,answerCalendarDeleteEvent,2);      
}            

function rootCalendarDeleteEvent(whichEventId) { 
  var dta = new Object(); 
  dta['calEventId'] = whichEventId; 
  dta['calEventDialogId'] = byId('dialogidhld').value;
  var passdta = JSON.stringify(dta);
  //console.log( passdta);          
  var mlURL = "/data-doers/root-calendar-event-delete";
  universalAJAX("POST",mlURL,passdta,answerCalendarDeleteEvent,2);      
}            

function answerCalendarDeleteEvent( rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else { 
     var dta = JSON.parse(rtnData['responseText']);
     alert('Event has been deleted');
     closeThisDialog ( dta['DATA']['dialogid'] );
     //RELOAD CALENDAR
     var mmnth = byId('calendarMasterMonthId').value;
     var myear = byId('calendarMasterYearId').value;
     getCalendar('mainroot','mainRootCalendar',mmnth,myear,1);
            
   }            
}
               
function saveRootEvent() { 
  var dta = new Object(); 
  dta['calEventDte'] = byId('rootEventDateValue').value; 
  dta['calEventStartTime'] = byId('rootEventStartValue').value; 
  dta['calEventEndTime'] = byId('rootEventEndValue').value; 
  dta['calEventAllDayInd'] = byId('rootAllDayInd').checked;
  dta['calEventType'] = byId('rootEventtypeValue').value; 
  dta['calEventPHIIni'] = byId('rootICInitials').value;
  dta['calEventSurgeon'] = byId('rootMDInitials').value;
  dta['calEventTitle'] = byId('rootEventTitle').value;
  dta['calEventDesc'] = byId('rootEventDesc').value;
  dta['calEventForWho'] = byId('rootEventInstitutionValue').value;
  dta['calEventDialogId'] = byId('dialogidhld').value;
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/root-calendar-event-save";
  universalAJAX("POST",mlURL,passdta,answerCalendarEventSave,1);
}

function answerCalendarEventSave(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else { 
     var dta = JSON.parse(rtnData['responseText']);
     alert('Event has been saved');
     closeThisDialog ( dta['DATA']['dialogid'] );
     //RELOAD CALENDAR
     var mmnth = byId('calendarMasterMonthId').value;
     var myear = byId('calendarMasterYearId').value;
     getCalendar('mainroot','mainRootCalendar',mmnth,myear,1);
   }
}

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement'];
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    if (byId(whichfield+'Value')) {
      byId(whichfield+'Value').value = whatvalue;
    }    
    byId(whichfield).value = whatplaintext; 
    switch ( whichfield ) { 
      case 'rootEventStart':
         byId('rootAllDayInd').checked = false; 
      break;
      case 'rootEventEnd':
         byId('rootAllDayInd').checked = false; 
      break;
      case 'rootEventtype':
        if ( whatvalue === 'INFCEVT') { 
         byId('icmdheader').style.display = 'block';
         byId('rootICInitials').style.display = 'block';
         byId('rootMDInitials').style.display = 'block';
         byId('rootEventTitle').value = '!NO HIPAA INFORMATION!'; 
         byId('rootEventDesc').value = '!NO HIPAA INFORMATION!';
         byId('rootICInitials').focus();
        } else { 
         byId('icmdheader').style.display = 'none';
         byId('rootICInitials').style.display = 'none';
         byId('rootMDInitials').style.display = 'none';
         byId('rootICInitials').value = '';
         byId('rootMDInitials').value = '';
         byId('rootEventTitle').value = ''; 
         byId('rootEventDesc').value = ''; 
        } 
      break;
    }
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

function inventory ( $rqststr ) { 

  session_start(); 
  $tt = treeTop;
  $ott = ownerTree;
  $si = serverIdent;
  $sp = serverpw;
  
  //LOCAL USER CREDENTIALS BUILT HERE
  $regUsr = $this->sessid;  
  $regCode = $this->regcode; 
  
  
//JAVASCRIPT AS APPLIES TO ALL INVENTORY SCREENS  
$rtnThis = <<<JAVASCR
        
var pressed = false; 
var chars = []; 

document.addEventListener('DOMContentLoaded', function() {  

   //THIS IS BARCODE SCANNER CODE
   document.addEventListener('keypress', function( event ) {
   if ( event.which >= 33 && event.which <= 126 ) { 
       chars.push(String.fromCharCode(event.which)); 
   }
   
   const regex = /_/gi;
   if ( event.which == 13 && chars.length > 4 ) { 
     var barcode = chars.join("");          
     doSomethingWithScan( barcode.replace( regex, '' ) ); 
     chars = [];
   }
        
   if ( pressed == false ) { 
     pressed = true; 
     t = setTimeout( function() { 
     chars = [];
     pressed = false;
   }, 400);
   }

   }, false); 

   if ( byId('btnPrintLocationCard') ) { 
     byId('btnPrintLocationCard').addEventListener('click', rqstLocationCode );
   }

   if ( byId('btnPrintBCCard') ) {
     byId('btnPrintBCCard').addEventListener('click', rqstNewBarCode );
   }

   if ( byId('ctlBtnCheckCommit') ) { 
     byId('ctlBtnCheckCommit').addEventListener('click', actionCheckCheck );
   }
   
   if ( byId('ctlBtnCheckCancel') ) { 
     byId('ctlBtnCheckCancel').addEventListener('click', actionCancel );
   }

   if ( byId('ctlBtnCountCancel') ) { 
     byId('ctlBtnCountCancel').addEventListener('click', actionCancel );
   }

}, false);

function checkBCCodeValue() {       
   if ( byId('bccodevalue') ) { 
       var regex = /[^A-Za-z0-9\@]/gi;
       byId('bccodevalue').value = byId('bccodevalue').value.replace(regex,'').toUpperCase();    
   }     
}
        
function clickedlabel(e) {
  var ele = e.target; 
  var eleId = ele.id;
  if ( eleId.substr(0,9) === "childwarn" || eleId.substr(0,9) === "dspsegsts" || eleId.substr(0,9) === "dspsegprp" || eleId.substr(0,9) === "dspsegdxd" ) {
    var pNode = byId(eleId).parentNode;
    byId( pNode.parentNode.id ).remove();   
  } else {
    byId('scannedLabel'+parseInt(ele.id.replace( /^\D+/g, ''))).remove();
  }
  var elemcnt = document.getElementsByClassName("labelDspDiv");
  byId('itemCountDsp').innerHTML = "SCAN COUNT: " + elemcnt.length; 
}

function rqstLocationCode() {
  generateDialog('rqstLocationBarcode','xxx-xxx');
}

function rqstNewBarCode() { 
  generateDialog('rqstSampleBarcode','xxx-xxx');
}
       
function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement'];
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    if (byId(whichfield+'Value')) {
      byId(whichfield+'Value').value = whatvalue;
    }    
    byId(whichfield).value = whatplaintext; 
  }
}

function makeBCCodeValue( whatvalue ) {
  if ( whatvalue.trim() !== "" && whatvalue.trim() !== '-' ) { 
    if ( byId('bccodevalue') ) { 
      byId('bccodevalue').value = byId('bccodevalue').value.trim() + whatvalue;
    }
  } else {  
    if ( byId('bccodevalue').value.length > 0 ) {
       byId('bccodevalue').value = byId('bccodevalue').value.trim().substr(0, byId('bccodevalue').value.length - 1);
    }
  }
  checkBCCodeValue();     
}

function clearThis() { 
  byId('bccodevalue').value = "";
}

function selectPrinter( whichprinter ) { 
  var x = document.getElementsByClassName("labelingdsp");
   for ( var i = 0; i < x.length; i++ ) {
     x[i].dataset.selected = 'false';
   }
  byId( whichprinter ).dataset.selected = 'true';
}

function printRqstBarcodeLabel() { 
  var dta = new Object(); 
  dta['labeltext'] = byId('bccodevalue').value.trim();
  var x = document.getElementsByClassName("labelingdsp");
  for ( var i = 0; i < x.length; i++ ) {
    if( x[i].dataset.selected == 'true' ) {
      dta['printer'] = x[i].dataset.prnname;
      dta['printformat'] = x[i].dataset.labelformat;
    }
  }

  var passdta = JSON.stringify(dta);
  if ( dta['printformat'] === 'PRINTCARD' ) {
      var mlURL = "/data-doers/rqst-inventory-label-encrypt";
      universalAJAX("POST",mlURL,passdta,answerEncryptRqstBarcodeLabel,2);
  } else {
    var mlURL = "/data-doers/print-this-inventory-label";
    universalAJAX("POST",mlURL,passdta,answerPrintRqstBarcodeLabel,2);
  }
}

function answerPrintRqstBarcodeLabel( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('bccodevalue').focus();
   } else {
    alert('Label Printed'); 
    byId('bccodevalue').value = ""; 
  }
}

function answerEncryptRqstBarcodeLabel ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
    var dta = JSON.parse(rtnData['responseText']);        
    byId('bccodevalue').value = "";
    openOutSidePage('{$tt}/print-obj/inventory-item-tag/'+dta['DATA']['dataencryption'] ); 
  }
}

JAVASCR;

switch ( $rqststr[2] ) { 

  case 'processimanifest': 
      $rtnThis .= <<<PROCINVT

document.addEventListener('DOMContentLoaded', function() {

  if (  byId('btnPrintSlides') ) { 
    byId('btnPrintSlides').addEventListener( 'click' , function() { sendPrintRequest('SLD'); }, false );    
  }

  if (  byId('btnPrintFrozens') ) { 
    byId('btnPrintFrozens').addEventListener( 'click' , function() { sendPrintRequest('FRZ'); }, false );    
  }
  
  if (  byId('btnPrintPB') ) { 
    byId('btnPrintPB').addEventListener( 'click' , function() { sendPrintRequest('FIX'); }, false );    
  }
  
  if (  byId('btnPrintICard') ) { 
    byId('btnPrintICard').addEventListener( 'click' , function() { sendPrintRequest('ICRD'); }, false );    
  }
 
}, false);          

function sendPrintRequest ( typeofprint ) { 
  
  var obj = new Object(); 
  obj['typeofprint'] = typeofprint;
  obj['manifestnbr'] = byId('fldManifestNbr').value;
  var passdta = JSON.stringify(obj);
  var mlURL = "/data-doers/invtry-print-imanifest-objects";
  universalAJAX("POST",mlURL,passdta,answerPrintIntraManifestObjects,2);

}

function answerPrintIntraManifestObjects ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
  } else {
    var dta = JSON.parse(rtnData['responseText']); 
    var d = dta['DATA']; 
    if ( d.length > 0 ) {
      openOutSidePage("{$tt}/print-obj/manifest-barcode-run/"+dta['DATA']);  
    } else {       
      alert('Label(s) have been printed '); 
    }
  }

}

function doSomethingWithScan ( scanvalue ) {

  //TODO:  MAKE THIS DYNAMIC - SPECIAL OLD BARCODE ENCODING CHARACTERS
  scanvalue = scanvalue.replace(/%V$/mg,"@");
  scanvalue = scanvalue.replace(/%O/g,"");
  scanvalue = scanvalue.replace(/^(ED)/,"");
  //////////////////

  var scanmanifest = new RegExp(/^(IMN)-[A-Za-z]{2}-[0-9]{6}$/); 
  var scanloc   = new RegExp(/^FRZ[A-Za-z]{1}\d+$/); 
  var scanloca  = new RegExp(/^SSC[A-Za-z]{1}\d+$/); 
  var bglabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?(@)?$/);
  var zbglabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  


  var scanworked = 0;

  if ( scanmanifest.test ( scanvalue ) ) {
    scanworked = 1;
    getIntraManifest ( scanvalue );
  }

  if ( scanloc.test( scanvalue ) || scanloca.test( scanvalue ) ) {
    scanworked = 1;
    if ( byId('locscandsp') ) { 
      byId('standardModalBacker').style.display = 'block';    
      byId('locscancode').value = scanvalue;
      byId('locscandsp').innerHTML = scanvalue;
      
      //MAKE PROMISE TO LOOKUP DATA
      fillInLocationDisplay ( scanvalue ).then ( function (fulfilled) { 
        byId('locscandsp').innerHTML = fulfilled;
      })
      .catch( function (error) { 
        byId('locscancode').value = "";
        byId('locscandsp').innerHTML = error;
      });
    }
  }

  if ( bglabel.test ( scanvalue ) || zbglabel.test ( scanvalue ) ) { 
    scanworked = 1;
    byId('standardModalBacker').style.display = 'block';    

    bgprocessor ( scanvalue ).then ( function ( fulfilled ) {
      alert( fulfilled );
      byId('standardModalBacker').style.display = 'none';    
    })
    .catch ( function ( error ) { 
      var msgs = JSON.parse( error );
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
        dspMsg += "\\n - "+element;
      });
      alert("ERROR:\\n"+dspMsg);
      byId('standardModalBacker').style.display = 'none';    
    });

  } 

  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

}

var bgprocessor = function ( scancode ) { 
  return new Promise( function ( resolve, reject ) {
  
  var obj = new Object(); 
  obj['manifestnbr'] = byId('fldManifestNbr').value;
  obj['locscancode'] = byId('locscancode').value;
  obj['bglabel'] = scancode;
  var passdta = JSON.stringify(obj);         
  httpage.open("POST",dataPath+"/data-doers/invtry-imanifest-bg-processes", true)    
  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
  httpage.onreadystatechange = function() { 
  if (httpage.readyState === 4) {
    if ( parseInt(httpage.status) === 200 ) {
      resolve( passdta );  
    } else { 
      reject( httpage.responseText );
    }
  }
  };
  httpage.send ( passdta );
  });
}


var fillInLocationDisplay = function ( scancode ) { 
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-location-heirach", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           resolve( dta['DATA']['pathdsp'] ) ;
           byId('standardModalBacker').style.display = 'none';    
        } else { 
          reject("NO LOCATION FOUND WITH THE SCANNED CODE: "+scancode);
          byId('standardModalBacker').style.display = 'block';    
        }
      }
    };
    httpage.send ( passdta );
  });
}

function blankManifestCheckin ()  { 
  byId('dspManifestNbr').innerHTML = "&nbsp;";
  byId('dspInstitution').innerHTML = "&nbsp;";
  byId('dspagent').innerHTML = "&nbsp;";
  byId('dspsentdate').innerHTML = "&nbsp;";
  byId('dspmanifeststatus').innerHTML = "&nbsp;";
  byId('fldManifestNbr').value = "";
  byId('dspmanifestsegmentcount').innerHTML = "&nbsp;";
  byId('dspSegmentListing').innerHTML = "&nbsp;";
  byId('locscandsp').innerHTML = "&nbsp;"
  byId('locscancode').value = "";
}

function getIntraManifest ( whichmanifest ) {
  byId('standardModalBacker').style.display = 'block';    
  blankManifestCheckin(); 
  var obj = new Object(); 
  obj['manifestscan'] = whichmanifest;
  var passdta = JSON.stringify(obj);         
  var mlURL = "/data-doers/invtry-get-imanifest";
  universalAJAX("POST",mlURL,passdta,answerGetIntraManifest,2);
}

function answerGetIntraManifest ( rtnData ) { 

  var tt = '{$tt}';
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     if ( byId('dspManifestNbr') ) { 
       byId('dspManifestNbr').innerHTML = dta['DATA']['manifesthead']['manifestnbrdsp'];
       byId('dspInstitution').innerHTML = dta['DATA']['manifesthead']['dspinstitution'];
       byId('dspagent').innerHTML = dta['DATA']['manifesthead']['createdBy'] + " / " + dta['DATA']['manifesthead']['sentby'];
       byId('dspsentdate').innerHTML = dta['DATA']['manifesthead']['senddate'];
       byId('dspmanifeststatus').innerHTML = dta['DATA']['manifesthead']['mstatus'];
       byId('fldManifestNbr').value = dta['DATA']['manifesthead']['manifestnbr'];
       byId('dspmanifestsegmentcount').innerHTML = dta['DATA']['manifesthead']['segOnMani'];
       byId('dspSegmentListing').innerHTML = buildManifestSegmentDisplay ( dta['DATA']['manifestsegments'] )
     }
   }       
   byId('standardModalBacker').style.display = 'none';    

}

function buildManifestSegmentDisplay( segdata ) { 
   var segDspTbl = " <div id=segTblHeaderHold> <div class=sHead>CHTN #</div><div class=sHead>Seg Status</div> <div class=sHead>Preparation</div> <div class=sHead>Metric</div> <div class=sHead>Abbreviated Designation</div>      </div>";
   segdata.forEach((element) => { 
     segDspTbl += "<div id='SEG"+element['segmentid']+"' class=segRecHold>  <div class=segdataelement>"+element['bgs']+"</div> <div class=segdataelement>"+element['segstatusdsp']+"</div> <div class=segdataelement>"+element['prep']+"</div> <div class=segdataelement>"+element['metric']+"</div> <div class=segdataelement>"+element['shortdesig']+"</div>  </div>";
   });
   segDspTbl += "</div>";
   return segDspTbl;
}


PROCINVT;
    break;
  case 'processinventory':
      $rtnThis .= <<<PROCINVT

var fillInLocationDisplay = function ( scancode ) { 
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-location-heirach", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           resolve( "<b>Scanning to</b>: "+ dta['DATA']['pathdsp'] ) ;
        } else { 
          reject("NO LOCATION FOUND WITH THE SCANNED CODE: "+scancode);
        }
      }
    };
    httpage.send ( passdta );
  });
}

var fillInDesigLabelCode = function ( scancode  ) {
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-label-dxdesignation", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText ); 
           if ( dta['DATA']['invmoveind'] !== 'NOMOVE' ) { 
             //resolve( dta['DATA']['desig']+" / "+ dta['DATA']['prp'] + " ("+dta['DATA']['segstatus']+")"  );
             var thisid = Math.random().toString(36).slice(2); 
             var dspLine = "<div class=lblsegstatusdsp id=\'dspsegsts'+thisid+'\'>"+dta['DATA']['segstatus']+"</div>";
             dspLine += "<div <div class=lblsegprpdsp id=\'dspsegprp'+thisid+'\'>"+dta['DATA']['prp']+"</div>";
             dspLine += "<div class=lblsegdxdsp id=\'dspsegdxd'+thisid+'\'>"+dta['DATA']['desig']+"</div>";
             resolve( dspLine ); 
           } else {
             var thisid = Math.random().toString(36).slice(2); 
             resolve( '<div class=scanwarning id=\'childwarn'+thisid+'\'>REMOVE SEGMENT! STATUS IS \''+dta['DATA']['segstatus'].toUpperCase()+'\'</div>'); 
           }
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}   
    
function actionCheckCheck() { 
  var obj = new Object(); 
  var scanlist = [];
  obj['location'] = byId('locscancode').value.trim();
  var lbls = document.getElementsByClassName("labelDspDiv");
  var lblsl = lbls.length;
  for ( var i = 0; i < lbls.length; i++ ) { 
    scanlist.push( byId(lbls[i].id).dataset.label );
  } 
  obj['scanlist'] = scanlist;   
  var pdta = JSON.stringify(obj);
  byId('standardModalBacker').style.display = 'block';    
  byId('waitMsgTitle').innerHTML = 'Processing Inventory'; 
  byId('waitMsg').innerHTML = 'Please wait as we send your inventory process request to the server.  Depending on how many segments you have scanned this could take some time.  Please wait ... '; 
  byId('dspIWait').style.display = 'block';
  var mlURL = "/data-doers/invtry-segment-statuser";
  universalAJAX("POST",mlURL,pdta,answerInventorySegmentStatuser,2);
}

function answerInventorySegmentStatuser ( rtnData ) {
  byId('dspIWait').style.display = 'none'; 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     alert('Biosample Segment(s) have been updated'); 
     actionCancel();
     byId('standardModalBacker').style.display = 'none';    
     byId('dspIWait').style.display = 'none';
     byId('waitMsgTitle').innerHTML = ''; 
     byId('waitMsg').innerHTML = ''; 
  }
}

function actionCancel() {
  if ( byId('locscancode') ) {
    byId('locscancode').value = '';
  }
  if ( byId('locscandsp') ) {
    byId('locscandsp').innerHTML = '';
  }
  if ( byId('itemCountDsp') ) {
    byId('itemCountDsp').innerHTML = 'SCAN COUNT: 0';
  }
  if ( byId('labelscanholderdiv') ) {
    var myNode = byId('labelscanholderdiv');
    while ( myNode.firstChild ) { 
      myNode.removeChild(myNode.firstChild);
    }  
  }
}

function doSomethingWithScan ( scanvalue ) {
    
  //TODO:  MAKE THIS DYNAMIC - SPECIAL OLD BARCODE ENCODING CHARACTERS
  scanvalue = scanvalue.replace(/%V$/mg,"@");
  scanvalue = scanvalue.replace(/%O/g,"");
  scanvalue = scanvalue.replace(/^(ED)/,"");
  //////////////////

  var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?(@)?$/);
  var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  
  var scanloc   = new RegExp(/^FRZ[A-Za-z]{1}\d+$/); 
  var scanloca  = new RegExp(/^SSC[A-Za-z]{1}\d+$/); 

  var scanworked = 0;

  if ( scanlabel.test( scanvalue ) || zlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    if ( byId('labelscan') ) {
      //CHECK LABEL NOT ALREADY SCANNED
      var lbls = document.getElementsByClassName("labelDspDiv");
      var lblsl = lbls.length;
      for ( var i = 0; i < lbls.length; i++ ) { 
        if ( byId(lbls[i].id).dataset.label == scanvalue ) { return null; }
      } 

      var nxtItemNbr = 0;
      if ( lblsl > 0 ) {
        nxtItemNbr =  parseInt(lbls[ (lblsl - 1) ].id.replace( /^\D+/g, '')) + 1; 
      } 

      var lblDiv = document.createElement('div');
      lblDiv.id = "scannedLabel"+nxtItemNbr;
      lblDiv.className = "labelDspDiv";
      lblDiv.dataset.label = scanvalue;
      //lblDiv.innerHTML = scanvalue; 
      byId('labelscanholderdiv').appendChild ( lblDiv );
      lblDiv.addEventListener("click", clickedlabel );
        
      var scnDsp = document.createElement('div');
      scnDsp.id = "scanDisplay"+nxtItemNbr;
      scnDsp.className = "scanDisplay";
      scnDsp.innerHTML = scanvalue;
      byId("scannedLabel"+nxtItemNbr).appendChild( scnDsp );

      var desDsp = document.createElement('div');
      desDsp.id = "desigDisplay"+nxtItemNbr;
      desDsp.className = "desigDisplay";
      desDsp.innerHTML = "-";
      byId("scannedLabel"+nxtItemNbr).appendChild( desDsp );        

      var elemcnt = document.getElementsByClassName("labelDspDiv");
      byId('itemCountDsp').innerHTML = "SCAN COUNT: " + elemcnt.length; 

      //MAKE PROMISE TO LOOKUP DATA
      fillInDesigLabelCode( scanvalue ).then (function (fulfilled) {         
            byId('desigDisplay'+nxtItemNbr).innerHTML = fulfilled;
        })
        .catch(function (error) {
            byId('desigDisplay'+nxtItemNbr).innerHTML = '<span class=errordspmsg>Scanned Label Not Found in Database. See Informatics Staff Memeber Immediately.</span>';
            console.log(error.message);
        });
    } else { 
      alert('The Scan Control doesn\'t exist');
    }
  }
                
  if ( scanloc.test( scanvalue ) || scanloca.test( scanvalue ) ) {
    scanworked = 1;
    if ( byId('locationscan') ) { 
      byId('locscancode').value = scanvalue;
      byId('locscandsp').innerHTML = scanvalue;
      
      //MAKE PROMISE TO LOOKUP DATA
      fillInLocationDisplay ( scanvalue ).then ( function (fulfilled) { 
        byId('locscandsp').innerHTML = fulfilled;
      })
      .catch( function (error) { 
        byId('locscancode').value = "";
        byId('locscandsp').innerHTML = error;
      });
    }
  }

  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

} 

          
PROCINVT;
      break;
  case 'destroybiosamples':
      $rtnThis .= <<<PROCINVT


document.addEventListener('DOMContentLoaded', function() {

  if (  byId('ctlBtnCommitCount') ) { 
    byId('ctlBtnCommitCount').addEventListener( 'click' , function() { sendDRequest(); }, false );    
  }
           
}, false);          

function sendDRequest () { 
  var obj = new Object();
  var bgslist = new Object(); 
  var lbls = document.getElementsByClassName("labelDspDiv");
  var lblsl = lbls.length;
  for ( var i = 0; i < lbls.length; i++ ) { 
    bgslist[i] = byId(lbls[i].id).dataset.label;
  } 
  obj['bgslist'] = bgslist;
  var passdta = JSON.stringify(obj);         
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/inventory-action-destroy";
  universalAJAX("POST",mlURL,passdta,answerSendDRequest,2);
}

function answerSendDRequest ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     alert('Biosample Segment(s) have been updated to \'Destroyed\''); 
     actionCancel();
     byId('standardModalBacker').style.display = 'none';    
   }

}

var fillInDesigLabelCode = function ( scancode  ) {
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-label-dxdesignation", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           //{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"segstatus":"Assigned","prp":"OCT [OCT]","desig":"NORMAL :: LUNG ::"}}
           //resolve( dta['DATA']['desig']+" / "+ dta['DATA']['prp'] + " " +dta['DATA']['segstatus']  );
             var thisid = Math.random().toString(36).slice(2); 
             var dspLine = "<div class=lblsegstatusdsp id=\'dspsegsts'+thisid+'\'>"+dta['DATA']['segstatus']+"</div>";
             dspLine += "<div <div class=lblsegprpdsp id=\'dspsegprp'+thisid+'\'>"+dta['DATA']['prp']+"</div>";
             dspLine += "<div class=lblsegdxdsp id=\'dspsegdxd'+thisid+'\'>"+dta['DATA']['desig']+"</div>";
             resolve( dspLine ); 
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}   

function actionCancel() {
  if ( byId('locscancode') ) {
    byId('locscancode').value = '';
  }
  if ( byId('locscandsp') ) {
    byId('locscandsp').innerHTML = '';
  }
  if ( byId('itemCountDsp') ) {
    byId('itemCountDsp').innerHTML = 'SCAN COUNT: 0';
  }
  if ( byId('labelscanholderdiv') ) {
    var myNode = byId('labelscanholderdiv');
    while ( myNode.firstChild ) { 
      myNode.removeChild(myNode.firstChild);
    }  
  }
}

function doSomethingWithScan ( scanvalue ) {

  //TODO:  MAKE THIS DYNAMIC - SPECIAL OLD BARCODE ENCODING CHARACTERS
  scanvalue = scanvalue.replace(/%V$/mg,"@");
  scanvalue = scanvalue.replace(/%O/g,"");
  //////////////////

  var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?(@)?$/);
  var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  

  var scanworked = 0;
  if ( scanlabel.test( scanvalue ) || zlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    if ( byId('labelscan') ) {
      //CHECK LABEL NOT ALREADY SCANNED
      var lbls = document.getElementsByClassName("labelDspDiv");
      var lblsl = lbls.length;
      for ( var i = 0; i < lbls.length; i++ ) { 
        if ( byId(lbls[i].id).dataset.label == scanvalue ) { return null; }
      } 

      var nxtItemNbr = 0;
      if ( lblsl > 0 ) {
        nxtItemNbr =  parseInt(lbls[ (lblsl - 1) ].id.replace( /^\D+/g, '')) + 1; 
      } 

      var lblDiv = document.createElement('div');
      lblDiv.id = "scannedLabel"+nxtItemNbr;
      lblDiv.className = "labelDspDiv";
      lblDiv.dataset.label = scanvalue;
      //lblDiv.innerHTML = scanvalue; 
      byId('labelscanholderdiv').appendChild ( lblDiv );
      lblDiv.addEventListener("click", clickedlabel );
        
      var scnDsp = document.createElement('div');
      scnDsp.id = "scanDisplay"+nxtItemNbr;
      scnDsp.className = "scanDisplay";
      scnDsp.innerHTML = scanvalue;
      byId("scannedLabel"+nxtItemNbr).appendChild( scnDsp );

      var desDsp = document.createElement('div');
      desDsp.id = "desigDisplay"+nxtItemNbr;
      desDsp.className = "desigDisplay";
      desDsp.innerHTML = "-";
      byId("scannedLabel"+nxtItemNbr).appendChild( desDsp );        

      var elemcnt = document.getElementsByClassName("labelDspDiv");
      byId('itemCountDsp').innerHTML = "SCAN COUNT: " + elemcnt.length; 

      //MAKE PROMISE TO LOOKUP DATA
      fillInDesigLabelCode( scanvalue ).then (function (fulfilled) {         
            byId('desigDisplay'+nxtItemNbr).innerHTML = fulfilled;
        })
        .catch(function (error) {
            byId('desigDisplay'+nxtItemNbr).innerHTML = '<div class=errordspmsg>Scanned Label Not Found in Database. See Informatics Staff Memeber Immediately.</div>';
            console.log(error.message);
        });
    } else { 
      alert('The Scan Control doesn\'t exist');
    }
  }
                
  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

}
  
PROCINVT;
      break;
  case 'pendingdestroybiosamples':
      $rtnThis .= <<<PROCINVT

document.addEventListener('DOMContentLoaded', function() {

  if (  byId('ctlBtnCommitCount') ) { 
    byId('ctlBtnCommitCount').addEventListener( 'click' , function() { sendPDRequest(); }, false );    
  }
           
}, false);          
          
function sendPDRequest() { 
  var obj = new Object();
  var bgslist = new Object(); 
  var lbls = document.getElementsByClassName("labelDspDiv");
  var lblsl = lbls.length;
  for ( var i = 0; i < lbls.length; i++ ) { 
    bgslist[i] = byId(lbls[i].id).dataset.label;
  } 
  obj['bgslist'] = bgslist;
  obj['ipin'] = window.btoa( encryptedString(key, byId('fldUsrInventoryPin').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
  var passdta = JSON.stringify(obj);         
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/inventory-action-pdestroy";
  universalAJAX("POST",mlURL,passdta,answerSendPDRequest,2);
}          

function answerSendPDRequest ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     alert('Biosample Segment(s) have been updated to \'Pending Destroy\''); 
     byId('fldUsrInventoryPin').value = "";
     actionCancel();
     byId('standardModalBacker').style.display = 'none';    
   }
}
          
function pinme( keypressed ) {
  var upinval = byId('fldUsrInventoryPin').value.trim();
  if ( keypressed === 'B' ) { 
     var nwupinval = upinval.substring(0, upinval.length - 1);          
  } else { 
    var nwupinval = upinval+keypressed;
  }
  byId('fldUsrInventoryPin').value = nwupinval;        
}          
          
          
var fillInDesigLabelCode = function ( scancode  ) {
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-label-dxdesignation", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           //resolve( dta['DATA']['desig']+" / "+ dta['DATA']['prp'] + " " +dta['DATA']['segstatus']  );
             var thisid = Math.random().toString(36).slice(2); 
             var dspLine = "<div class=lblsegstatusdsp id=\'dspsegsts'+thisid+'\'>"+dta['DATA']['segstatus']+"</div>";
             dspLine += "<div <div class=lblsegprpdsp id=\'dspsegprp'+thisid+'\'>"+dta['DATA']['prp']+"</div>";
             dspLine += "<div class=lblsegdxdsp id=\'dspsegdxd'+thisid+'\'>"+dta['DATA']['desig']+"</div>";
             resolve( dspLine ); 
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}   

function actionCancel() {
  if ( byId('locscancode') ) {
    byId('locscancode').value = '';
  }
  if ( byId('locscandsp') ) {
    byId('locscandsp').innerHTML = '';
  }
  if ( byId('itemCountDsp') ) {
    byId('itemCountDsp').innerHTML = 'SCAN COUNT: 0';
  }
  if ( byId('labelscanholderdiv') ) {
    var myNode = byId('labelscanholderdiv');
    while ( myNode.firstChild ) { 
      myNode.removeChild(myNode.firstChild);
    }  
  }
}

function doSomethingWithScan ( scanvalue ) {

  //TODO:  MAKE THIS DYNAMIC - SPECIAL OLD BARCODE ENCODING CHARACTERS
  scanvalue = scanvalue.replace(/%V$/mg,"@");
  scanvalue = scanvalue.replace(/%O/g,"");
  //////////////////

  var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?(@)?$/);
  var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  

  var scanworked = 0;
  if ( scanlabel.test( scanvalue ) || zlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    if ( byId('labelscan') ) {
      //CHECK LABEL NOT ALREADY SCANNED
      var lbls = document.getElementsByClassName("labelDspDiv");
      var lblsl = lbls.length;
      for ( var i = 0; i < lbls.length; i++ ) { 
        if ( byId(lbls[i].id).dataset.label == scanvalue ) { return null; }
      } 

      var nxtItemNbr = 0;
      if ( lblsl > 0 ) {
        nxtItemNbr =  parseInt(lbls[ (lblsl - 1) ].id.replace( /^\D+/g, '')) + 1; 
      } 

      var lblDiv = document.createElement('div');
      lblDiv.id = "scannedLabel"+nxtItemNbr;
      lblDiv.className = "labelDspDiv";
      lblDiv.dataset.label = scanvalue;
      //lblDiv.innerHTML = scanvalue; 
      byId('labelscanholderdiv').appendChild ( lblDiv );
      lblDiv.addEventListener("click", clickedlabel );
        
      var scnDsp = document.createElement('div');
      scnDsp.id = "scanDisplay"+nxtItemNbr;
      scnDsp.className = "scanDisplay";
      scnDsp.innerHTML = scanvalue;
      byId("scannedLabel"+nxtItemNbr).appendChild( scnDsp );

      var desDsp = document.createElement('div');
      desDsp.id = "desigDisplay"+nxtItemNbr;
      desDsp.className = "desigDisplay";
      desDsp.innerHTML = "-";
      byId("scannedLabel"+nxtItemNbr).appendChild( desDsp );        

      var elemcnt = document.getElementsByClassName("labelDspDiv");
      byId('itemCountDsp').innerHTML = "SCAN COUNT: " + elemcnt.length; 

      //MAKE PROMISE TO LOOKUP DATA
      fillInDesigLabelCode( scanvalue ).then (function (fulfilled) {         
            byId('desigDisplay'+nxtItemNbr).innerHTML = fulfilled;
        })
        .catch(function (error) {
            byId('desigDisplay'+nxtItemNbr).innerHTML = '<div class=errordspmsg>Scanned Label Not Found in Database. See Informatics Staff Memeber Immediately.</div>';
            console.log(error.message);
        });
    } else { 
      alert('The Scan Control doesn\'t exist');
    }
  }
                
  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

} 
  
PROCINVT;
      break;
  case 'icount': 
      $rtnThis .= <<<PROCINVT

document.addEventListener('DOMContentLoaded', function() {

  if (  byId('ctlBtnCommitCount') ) { 
    byId('ctlBtnCommitCount').addEventListener( 'click' , function() { sendICountRequest(); }, false );    
  }
           
}, false);          
          
function sendICountRequest() { 
  var obj = new Object();
  var bgslist = new Object(); 
  var lbls = document.getElementsByClassName("labelDspDiv");
  var lblsl = lbls.length;
  for ( var i = 0; i < lbls.length; i++ ) { 
    bgslist[i] = byId(lbls[i].id).dataset.label;
  } 
  obj['bgslist'] = bgslist;
  obj['scanloccode'] = byId('locscancode').value;
  var passdta = JSON.stringify(obj);
  //{"bgslist":{"0":"46925A7PBQ"},"scanloccode":"FRZB381"} 
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/inventory-action-icount";
  universalAJAX("POST",mlURL,passdta,answerSendICountRequest,2);
}          

function answerSendICountRequest ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     alert('Inventory Location has been counted'); 
     actionCancel();
     byId('standardModalBacker').style.display = 'none';    
   }
}

var fillInLocationDisplay = function ( scancode ) { 
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-location-heirach", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           //resolve( "<b>CHECKING INTO LOCATION</b>: "+ dta['DATA']['pathdsp'] + " :: <b>" +dta['DATA']['thislocation']+"</b> //<i>Scan Code</i>: "+dta['DATA']['scancode']);
           resolve( "<b>Location being counted</b>: "+ dta['DATA']['pathdsp'] ) ;
        } else { 
          reject("NO LOCATION FOUND WITH THE SCANNED CODE: "+scancode);
        }
      }
    };
    httpage.send ( passdta );
  });
}

var fillInDesigLabelCode = function ( scancode  ) {
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-label-dxdesignation", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           //{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"segstatus":"Assigned","prp":"OCT [OCT]","desig":"NORMAL :: LUNG ::"}}
           //resolve( dta['DATA']['desig']+" / "+ dta['DATA']['prp'] + " " +dta['DATA']['segstatus']  );
             var thisid = Math.random().toString(36).slice(2); 
             var dspLine = "<div class=lblsegstatusdsp id=\'dspsegsts'+thisid+'\'>"+dta['DATA']['segstatus']+"</div>";
             dspLine += "<div <div class=lblsegprpdsp id=\'dspsegprp'+thisid+'\'>"+dta['DATA']['prp']+"</div>";
             dspLine += "<div class=lblsegdxdsp id=\'dspsegdxd'+thisid+'\'>"+dta['DATA']['desig']+"</div>";
             resolve( dspLine ); 
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}   
    
function actionCancel() {
  if ( byId('locscancode') ) {
    byId('locscancode').value = '';
  }
  if ( byId('locscandsp') ) {
    byId('locscandsp').innerHTML = '';
  }
  if ( byId('itemCountDsp') ) {
    byId('itemCountDsp').innerHTML = 'SCAN COUNT: 0';
  }
  if ( byId('labelscanholderdiv') ) {
    var myNode = byId('labelscanholderdiv');
    while ( myNode.firstChild ) { 
      myNode.removeChild(myNode.firstChild);
    }  
  }
}

function doSomethingWithScan ( scanvalue ) {


  //TODO:  MAKE THIS DYNAMIC - SPECIAL OLD BARCODE ENCODING CHARACTERS
  scanvalue = scanvalue.replace(/%V$/mg,"@");
  scanvalue = scanvalue.replace(/%O/g,"");
  //////////////////

  var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?(@)?$/);
  var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  
  var scanloc   = new RegExp(/^FRZ[A-Za-z]{1}\d+$/); 

  var scanworked = 0;

  if ( scanlabel.test( scanvalue ) || zlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    if ( byId('labelscan') ) {
      //CHECK LABEL NOT ALREADY SCANNED
      var lbls = document.getElementsByClassName("labelDspDiv");
      var lblsl = lbls.length;
      for ( var i = 0; i < lbls.length; i++ ) { 
        if ( byId(lbls[i].id).dataset.label == scanvalue ) { return null; }
      } 

      var nxtItemNbr = 0;
      if ( lblsl > 0 ) {
        nxtItemNbr =  parseInt(lbls[ (lblsl - 1) ].id.replace( /^\D+/g, '')) + 1; 
      } 

      var lblDiv = document.createElement('div');
      lblDiv.id = "scannedLabel"+nxtItemNbr;
      lblDiv.className = "labelDspDiv";
      lblDiv.dataset.label = scanvalue;
      //lblDiv.innerHTML = scanvalue; 
      byId('labelscanholderdiv').appendChild ( lblDiv );
      lblDiv.addEventListener("click", clickedlabel );
        
      var scnDsp = document.createElement('div');
      scnDsp.id = "scanDisplay"+nxtItemNbr;
      scnDsp.className = "scanDisplay";
      scnDsp.innerHTML = scanvalue;
      byId("scannedLabel"+nxtItemNbr).appendChild( scnDsp );

      var desDsp = document.createElement('div');
      desDsp.id = "desigDisplay"+nxtItemNbr;
      desDsp.className = "desigDisplay";
      desDsp.innerHTML = "-";
      byId("scannedLabel"+nxtItemNbr).appendChild( desDsp );        

      var elemcnt = document.getElementsByClassName("labelDspDiv");
      byId('itemCountDsp').innerHTML = "SCAN COUNT: " + elemcnt.length; 

      //MAKE PROMISE TO LOOKUP DATA
      fillInDesigLabelCode( scanvalue ).then (function (fulfilled) {         
            byId('desigDisplay'+nxtItemNbr).innerHTML = fulfilled;
        })
        .catch(function (error) {
            byId('desigDisplay'+nxtItemNbr).innerHTML = '<div class=errordspmsg>Scanned Label Not Found in Database. See Informatics Staff Memeber Immediately.</div>';
            console.log(error.message);
        });
    } else { 
      alert('The Scan Control doesn\'t exist');
    }
  }
                
  if ( scanloc.test( scanvalue ) ) {
    scanworked = 1;
    //locationscan,locscancode,locscandsp
    if ( byId('locationscan') ) { 
      byId('locscancode').value = scanvalue;
      byId('locscandsp').innerHTML = scanvalue;
      
      //MAKE PROMISE TO LOOKUP DATA
      fillInLocationDisplay ( scanvalue ).then ( function (fulfilled) { 
        byId('locscandsp').innerHTML = fulfilled;
      })
      .catch( function (error) { 
        byId('locscancode').value = "";
        byId('locscandsp').innerHTML = error;
      });
    }
  }

  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

} 

PROCINVT;
      break;
  case 'processhprtray':
      $rtnThis .= <<<PROCINVT

function doSomethingWithScan ( scanvalue ) {

  //TODO:  SPECIAL BARCODE ENCODING

  var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?$(@)?/);
  var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  
  var scanhpr   = new RegExp(/^HPRT\d+$/); 
  var sldloc = new RegExp(/^SSC[A-Z]{1}\d+$/); 
  var scanloc   = new RegExp(/^FRZ[A-Za-z]{1}\d+$/); 


  var scanworked = 0;

  if ( scanlabel.test( scanvalue ) || zlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    switch ( trayassignable ) {
      case 0: 
        //DO NOTHING
        ( workingtrayid.trim() === "" ) ? alert('You must scan an HPR tray before scanning slides') : alert('This HPR tray, '+byId('tlocdisplay').innerHTML+', is not in a state to process.  It must be released from review');         
        break;
      case 1:
        //LOAD TRAY 

        break;
      case 2: 
        //UNLOAD TRAY
        scanvalue =  scanvalue.replace(/^(ED|ed)/,"") ;
        if ( slidertnloc === "" ) {
          alert('You have not scanned a location where you are placing this slide.');
        } else {
          //alert( scanvalue );
          //make promise 
          if ( byId('labelscan') ) {
            var lbls = document.getElementsByClassName("slidedspdiv");
            var lblsl = lbls.length;
            var slidefound = 0;
            for ( var i = 0; i < lbls.length; i++ ) { 
              if ( byId(lbls[i].id).dataset.label == scanvalue ) { slidefound = 1; }
            } 

            if ( slidefound === 1 ) { 
              slideRemoveFromTray ( scanvalue , slidertnloc,  workingtrayid).then (function (fulfilled) { 
                var dta = JSON.parse( fulfilled );  
                console.log ( dta['DATA'] + ' /// ' + dta['ITEMSFOUND'] );
                byId('hprlabelscanholderdiv').removeChild(byId('sld'+dta['DATA']));
                if ( parseInt( dta['ITEMSFOUND'] ) === 0 ) { 
                  alert('HPR TRAY HAS BEEN STATUSED FOR RE-USE.  YOU MAY RETURN THE HPR TRAY TO SERVICE');
                  location.reload(true);
                } else { 
                  byId('itemCountDsp').innerHTML = "SLIDES IN TRAY: "+parseInt( dta['ITEMSFOUND'] );
                }
              })
              .catch(function (error) {
                console.log(error.message);
              });
            } else { 
              alert( 'The scanned slide ('+scanvalue+') was not found in this HPR Tray.  Please see a CHTNEastern Informatics staff member' );

            }
          } else { 
            alert('The Scan Control doesn\'t exist');
          }
        }
        break;
    }
  }
  
  if ( scanhpr.test( scanvalue ) ) {
    scanworked = 1;
    var dta = new Object(); 
    dta['scancode'] = scanvalue;
    var passdta = JSON.stringify(dta);
    byId('standardModalBacker').style.display = 'block';
    actionCancel();
    var mlURL = "/data-doers/invtry-hprtray-scan-preprocess";
    universalAJAX("POST",mlURL,passdta,answerHPRTrayScanPreprocess,2);
  }

  if ( sldloc.test ( scanvalue ) || scanloc.test ( scanvalue ) ) {
    scanworked = 1;
    if ( workingtrayid.trim() === "" ) { 
      alert('You must scan an HPR Tray before specifying where slides will be stored.  ');
    } else {   
      switch ( trayassignable ) {
        case 0: 
          //DO NOTHING
          alert('This HPR tray, '+byId('tlocdisplay').innerHTML+', is not in a state to process.  It must be released from review');         
          break;
        case 1:
          //LOAD TRAY 
          alert('This HPR tray, '+byId('tlocdisplay').innerHTML+', is not in a state to unload.');         
          break;
        case 2: 
          //UNLOAD TRAY
          var dta = new Object(); 
          dta['scancode'] = scanvalue;
          var passdta = JSON.stringify(dta);
          byId('standardModalBacker').style.display = 'block';
          slidertnloc = "";
          byId('locationplace').innerHTML = "";
          byId('scanAnnounce').innerHTML = "Scan location where placing slides ...";
          var mlURL = "/data-doers/invtry-hprtray-scan-slide-location";
          universalAJAX("POST",mlURL,passdta,answerHPRTrayScanSlideLocation,2);
          break;
      }
    }
  }

  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified as an HPR-Slide-Tray by ScienceServer.  Please create a new label for this component to trigger an action');
  }
}           


var slidertnloc = "";
function answerHPRTrayScanSlideLocation ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    slidertnloc = "";
    byId('locationplace').innerHTML = "";
    byId('scanAnnounce').innerHTML = "2) Scan location where placing slides ...";
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     var dta = JSON.parse(rtnData['responseText']);         
     byId('locationplace').innerHTML = dta['DATA'][0]['pathdsp'];
     slidertnloc = dta['DATA'][0]['scancode'];    
     byId('scanAnnounce').innerHTML = "Slides will be filed in: ";
     byId('standardModalBacker').style.display = 'none';    
   }
}

var slidecount = 0; 
var trayassignable = 0; 
var workingtrayid = "";

function answerHPRTrayScanPreprocess( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
     var dta = JSON.parse(rtnData['responseText']);         
     var trayname = dta['DATA']['locationDisplay'] + ""; 
     workingtrayid = dta['DATA']['locscancode'];
     byId('tlocdisplay').innerHTML = trayname;
     var traystatus = dta['DATA']['traystatus']+" ["+dta['DATA']['traystatuson']+" :: "+dta['DATA']['traystatusby']+"]";
     byId('tstatus').innerHTML = traystatus;
     var trayloc = dta['DATA']['heldwithin'];
     trayloc += ( dta['DATA']['heldnote'].trim() !== "" ) ? " ("+dta['DATA']['heldnote']+")" : "";
     byId('tlocation').innerHTML = trayloc;
     var rvnote = dta['DATA']['reasonnotcomplete'];
     rvnote += ( dta['DATA']['notcompletenote'].trim() !== "" ) ? ": "+dta['DATA']['notcompletenote'] : ""; 
     byId('treview').innerHTML = rvnote;
     var assignable = dta['DATA']['assignablestatus'];
     var slidelisting = "";
     dta['DATA']['slides'].forEach( function(element) {
       var p = element['prepmethod'];
       p += ( element['preparation'].trim() !== "" ) ? " ("+element['preparation']+")" : "";
       var s = element['dspstatus'];
       var d = element['specimencategory'];
       d += ( element['site'].trim() !== "" ) ? " :: " + element['site'] : "";
       d += ( element['dx'].trim() !== "" ) ? " :: " + element['dx'] : "";
       d += ( element['sdx'].trim() !== "" ) ? " [" + element['sdx'] + "]" : "";
       slidelisting += "<div class=slidedspdiv id='sld"+element['bgs']+"' data-label='"+element['bgs']+"' ><div class=sBGS>"+element['bgs']+"</div><div class=sdxd>"+d+"</div><div class=sstatus>"+s+"</div><div class=sprep>"+p+"</div></div>";
     });
     byId('hprlabelscanholderdiv').innerHTML = slidelisting; 

     switch ( parseInt(assignable) ) {
       case 0: 
           //WITH REVIEWER
           byId('ctlBtnHPRTrayCommit').style.display = 'none';
           trayassignable = 0;
           byId('telemholda').innerHTML = "HPR TRAY HELD WITH REVIEWER"; 
         break;
       case 1:
           //LOAD TRAY
           trayassignable = 1;
           byId('telemholda').innerHTML = "LOADING/SENDING HPR TRAY"; 

         break;
       case 2:
           //UNLOAD TRAY
           trayassignable = 2; 
           //WHERE ARE SLIDES GOING
           byId('telemholda').innerHTML = "UNLOADING/CHECKING-IN HPR TRAY"; 
           byId('putawaylocation').style.display = 'block';
         break;
     }

     if ( parseInt(dta['ITEMSFOUND']) > 0 ) { 
       slidecount = parseInt(dta['ITEMSFOUND']);
     } 
     byId('itemCountDsp').innerHTML = "SLIDES IN TRAY: "+slidecount;
     byId('standardModalBacker').style.display = 'none';    
   }
}          

var slideRemoveFromTray = function ( scancode, newloccode, hprtrayid ) { 
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scancode'] = scancode.trim();
    obj['newloccode'] = newloccode.trim();
    obj['hprtrayid'] = hprtrayid.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-remove-slide-hprtray", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) {      
          var dta = JSON.parse( httpage.responseText );  
          resolve( httpage.responseText   ); 
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}



var fillInDesigLabelCode = function ( scancode  ) {
  return new Promise(function(resolve, reject) {
    var obj = new Object(); 
    obj['scanlabel'] = scancode.trim();
    var passdta = JSON.stringify(obj);         
    httpage.open("POST",dataPath+"/data-doers/invtry-label-dxdesignation", true)    
    httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
    httpage.onreadystatechange = function() { 
      if (httpage.readyState === 4) {
         if ( parseInt(httpage.status) === 200 ) { 
           var dta = JSON.parse( httpage.responseText );  
           //{"MESSAGE":[],"ITEMSFOUND":0,"DATA":{"segstatus":"Assigned","prp":"OCT [OCT]","desig":"NORMAL :: LUNG ::"}}
           //resolve( dta['DATA']['desig']+" / "+ dta['DATA']['prp'] + " " +dta['DATA']['segstatus']  );
             var thisid = Math.random().toString(36).slice(2); 
             var dspLine = "<div class=lblsegstatusdsp id=\'dspsegsts'+thisid+'\'>"+dta['DATA']['segstatus']+"</div>";
             dspLine += "<div <div class=lblsegprpdsp id=\'dspsegprp'+thisid+'\'>"+dta['DATA']['prp']+"</div>";
             dspLine += "<div class=lblsegdxdsp id=\'dspsegdxd'+thisid+'\'>"+dta['DATA']['desig']+"</div>";
             resolve( dspLine ); 
        } else { 
          reject(Error("It broke! "+httpage.responseText ));
        }
      }
    };
    httpage.send ( passdta );
  });
}      

function actionCancel() {
  if ( byId('locscancode') ) {
    byId('locscancode').value = '';
  }
  if ( byId('locscandsp') ) {
    byId('locscandsp').innerHTML = '';
  }
  if ( byId('itemCountDsp') ) {
    slidecount = 0;
    byId('itemCountDsp').innerHTML = '&nbsp;';
  }
  trayassignable = 0; 
  if ( byId('ctlBtnHPRTrayCommit') ) {
    //byId('ctlBtnHPRTrayCommit').style.display = 'block';
  }
  workingtrayid = "";
  byId('tlocdisplay').innerHTML = "&nbsp;";
  byId('tstatus').innerHTML = "&nbsp;";
  byId('tlocation').innerHTML = "&nbsp;";
  byId('treview').innerHTML = "&nbsp;";
  byId('telemholda').innerHTML = ""; 
  slidertnloc = "";
  byId('locationplace').innerHTML = "";
  byId('scanAnnounce').innerHTML = "2) Scan location where placing slides ...";
  byId('putawaylocation').style.display = 'none';
  if ( byId('hprlabelscanholderdiv') ) {
    byId('hprlabelscanholderdiv').innerHTML = "&nbsp;"
  }
}


PROCINVT;
      
      break;
  case 'processshipment': 
     $rtnThis .= <<<PROCINVT

function doSomethingWithScan ( scanvalue ) {

  //TODO:  SPECIAL BARCODE ENCODING
  //var scanlabel = new RegExp(/^(ED)?\d{5}[A-Za-z]{1}\d{1,3}([A-Za-z]{1,3})?$(@)?/);
  //var zlabel = new RegExp(/^(Z)?\d{4}[A-Za-z]{1}\d{1,}([A-Za-z]{1,3})?$/);  
  //var scanhpr   = new RegExp(/^HPRT\d+$/); 
  var sdlabel = new RegExp(/^SD\[\d{6}\]$/);



  var scanworked = 0;

  if ( sdlabel.test( scanvalue ) ) { 
    scanworked = 1;
    //BIOSAMPLE LABEL SCANNED
    alert( 'SHIP DOC: ' + scanvalue );
  }
  
  if ( scanworked === 0 ) { 
    alert('This scan ('+scanvalue+') is formatted INCORRECTLY and cannot be identified by ScienceServer.  Please create a new label for this component to trigger an action');
  }

} 
PROCINVT;
      break;
}    
     
  return $rtnThis;
}

function biogroupdefinition ( $rqststr ) { 

  session_start(); 
  $tt = treeTop;
  $ott = ownerTree;
  $si = serverIdent;
  $sp = serverpw;

         
$rtnThis = <<<JAVASCR
        
document.addEventListener('DOMContentLoaded', function() {  
     
  if (byId('btnAddSegment')) { 
    byId('btnAddSegment').addEventListener('click', function() { 
      generateDialog('masterAddSegment',byId('masterBGEncy').value);
    }, false);
  }

  if ( byId('btnRqstFA')) { 
     byId('btnRqstFA').addEventListener('click', function() { 
       generateDialog('bgRqstFA', byId('masterBGEncy').value );
     }, false);
  }
        
  if (byId('btnEditSeg')) { 
    byId('btnEditSeg').addEventListener('click', function() { alert('Edit Segment is not yet operational.  Try later.'); }, false);
  }   

  if (byId('btnQMSActions')) { 
    byId('btnQMSActions').addEventListener('click', function() { 
        generateDialog('masterQMSAction',byId('masterBGEncy').value);
    }, false);
  }      

  if (byId('btnAssocGrp')) { 
    byId('btnAssocGrp').addEventListener('click', function() { alert('Associative Grouping display is not yet operational. Try later.'); }, false);
  }    

  if (byId('btnPHIRecord')) { 
    byId('btnPHIRecord').addEventListener('click', function() { alert('Encounter Record display is not yet operational. Try later.'); }, false);
  } 

  if (byId('btnPristine')) { 
    byId('btnPristine').addEventListener('click', function() { alert('Viewing the Pristine Record is not yet operational.  Try later.'); }, false);
  }    
         
  if (byId('btnHPRRecord')) { 
    byId('btnHPRRecord').addEventListener('click', function() { alert('Viewing the HPR Record is not yet operational. Try later.'); }, false);
  }
            
}, false); 

function genSegContext( e, segmentid ) {  
  e.preventDefault();        
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

function fillField(whichfield, whatvalue, whatplaintext, whatmenudiv) { 
  if (byId(whichfield)) { 
    if (byId(whichfield+'Value')) {
      byId(whichfield+'Value').value = whatvalue;
    }    
    byId(whichfield).value = whatplaintext; 
  }
}
         
function printPRpt(e, pathrptencyption) { 
  if (pathrptencyption == '0') { 
  } else { 
    openOutSidePage('{$tt}/print-obj/pathology-report/'+pathrptencyption);  
  }
}

function displayShipDoc(e, shipdocencryption) {
  e.stopPropagation(); 
  openOutSidePage("{$tt}/print-obj/shipment-manifest/"+shipdocencryption);  
}

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement'];
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function dlgSaveBGComments() {
   if ( byId('fldDspBGComment') && byId('fldID') ) { 
     var obj = new Object();
     byId('waitDsp').style.display = 'block';
     byId('dspLineOne').style.display = 'none';
     byId('dspLineTwo').style.display = 'none';
     byId('dspLineThree').style.display = 'none';
     obj['comment'] = byId('fldDspBGComment').value;
     obj['key'] = byId('fldID').value;
     var passdta = JSON.stringify(obj);
     var mlURL = "/data-doers/dialog-action-save-comments";
     universalAJAX("POST",mlURL,passdta,answerDLGSaveBGComments,2);
   }
}

function answerDLGSaveBGComments( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('waitDsp').style.display = 'none';
    byId('dspLineOne').style.display = 'block';
    byId('dspLineTwo').style.display = 'block';
    byId('dspLineThree').style.display = 'block';
   } else { 
      location.reload(true);
   }
} 

function editVocab() { 
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 0 ) { 
    byId('btnVocEditEnable').dataset.vocabunlock = 1;
    byId('buttnText').innerHTML = "Save";
    blankVocabForm();
  } else { 
    packageDiagnosisSave();
  }
}

function blankVocabForm( blankIndicatorLevel = 0 ) {

if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
switch (blankIndicatorLevel) { 
  case 0:
    byId('fldPRCSpecCat').value = "";
    byId('fldPRCSpecCatValue').value = "";
  case 1:
    byId('fldPRCSite').value = "";
    byId('fldPRCSiteValue').value = "";
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category)</div>";     
    byId('ddPRCSite').innerHTML = menuTbl;            
  case 2:
    byId('fldPRCSSite').value = "";
    byId('fldPRCSSiteValue').value = "";
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Site)</div>";     
    byId('ddPRCSSite').innerHTML = menuTbl;            
  case 3:
    byId('fldPRCDXMod').value = "";
    byId('fldPRCDXModValue').value = "";
    byId('fldPRCDXOverride').checked = false;    
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category and Site)</div>";     
    byId('ddPRCDXMod').innerHTML = menuTbl;            
  case 4:
    byId('fldPRCMETSSite').value = "";
    byId('fldPRCMETSSiteValue').value = "";
  case 5:
    byId('fldPRCSitePosition').value = "";
    byId('fldPRCSitePositionValue').value = "";
    byId('fldPRCSystemList').value = "";
    byId('fldPRCSystemListValue').value = "";
}
}

}

function overridedxmenu() { 
if (parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 0) {
   alert('VOCABULARY IS NOT UNLOCKED');
} else {
    if (byId('fldPRCDXOverride').checked) {
          //LIST THE WHOLE DX HERE
          byId('ddPRCDXMod').innerHTML = ""; 
          byId('fldPRCDXMod').value = "";
          byId('fldPRCDXModValue').value = "";            
          allDiagnosisMenu();
        } else {
          if ( byId('fldPRCSpecCat').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "") { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             updateDiagnosisMenu();  
          } else { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             byId('ddPRCDXMod').innerHTML = "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category and Site)</div>";  
          }
        }
}
}

function updateDiagnosisMenu() { 
       var mlURL = "/data-doers/diagnosis-downstream"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       dta['site'] = byId('fldPRCSiteValue').value.trim();            
       var passdta = JSON.stringify(dta);
       universalAJAXStreamTwo("POST",mlURL,passdta,answerUpdateDiagnosisMenu,2);                 
}        
        
function allDiagnosisMenu() { 
       var mlURL = "/data-doers/all-downstream-diagnosis"; 
       universalAJAXStreamTwo("POST",mlURL,"",answerUpdateDiagnosisMenu,2);                 
}            
                       
function packageEncounterSave( eid ) {
  var dta = new Object();
  var allfieldsfound = 1;
  byId('dlgFldPHIAge'+eid) ? dta['agemetric'] = byId('dlgFldPHIAge'+eid).value : allfieldsfound = 0;
  byId('dlgFldAgeUOM'+eid) ? dta['agemetricuom'] = byId('dlgFldAgeUOM'+eid).value : allfieldsfound = 0;
  byId('dlgFldRace'+eid) ? dta['race'] = byId('dlgFldRace'+eid).value : allfieldsfound = 0;
  byId('dlgFldSex'+eid) ? dta['sex'] = byId('dlgFldSex'+eid).value : allfieldsfound = 0;
  byId('dlgFldCX'+eid) ? dta['cxind'] = byId('dlgFldCX'+eid).value : allfieldsfound = 0;
  byId('dlgFldRX'+eid) ? dta['rxind'] = byId('dlgFldRX'+eid).value : allfieldsfound = 0;
  byId('dlgFldSbjt'+eid) ? dta['subjectnbr'] = byId('dlgFldSbjt'+eid).value : allfieldsfound = 0;
  byId('dlgFldProtocol'+eid) ? dta['protocolnbr'] = byId('dlgFldProtocol'+eid).value : allfieldsfound = 0;
  byId('dlgpbiosample'+eid) ? dta['pbiosample'] = byId('dlgpbiosample'+eid).value : allfieldsfound = 0;
  byId('dlgpxiid'+eid) ? dta['pxiid'] = byId('dlgpxiid'+eid).value : allfieldsfound = 0;
  if ( allfieldsfound === 1 ) { 
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/dialog-action-bg-definition-encounter-save";
    universalAJAX("POST",mlURL,passdta,answerBGDefinitionEncounterSave,2);
  } else { 
    alert('ERROR WITH PAYLOAD PACKAGE.  SEE A CHTNEASTERN INFORMATICS MEMBER');
  } 
}

function packageDiagnosisSave() { 
  var dta = new Object();
  var allfieldsfound = 1;
  byId('fldHoldBioGroup') ? dta['refbg'] = byId('fldHoldBioGroup').value.trim() : allfieldsfound = 0;
  byId('fldPRCSpecCat') ? dta['speccat'] = byId('fldPRCSpecCat').value.trim() : allfieldsfound = 0;
  byId('fldPRCSite') ? dta['collectedsite'] = byId('fldPRCSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCSSite') ? dta['collectedsubsite'] = byId('fldPRCSSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCDXMod') ? dta['diagnosismodifier'] = byId('fldPRCDXMod').value.trim() : allfieldsfound = 0;
  byId('fldPRCMETSSite') ? dta['metsfromsite'] = byId('fldPRCMETSSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCSitePosition') ? dta['siteposition'] = byId('fldPRCSitePosition').value.trim() : allfieldsfound = 0;
  byId('fldPRCSystemList') ? dta['systemicdx'] = byId('fldPRCSystemList').value.trim() : allfieldsfound = 0;
  byId('fldPRCDXOverride') ? dta['dxoverride'] = byId('fldPRCDXOverride').checked : allfieldsfound = 0;        
  if ( allfieldsfound === 1 ) { 
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/dialog-action-bg-definition-designation-save";
    universalAJAX("POST",mlURL,passdta,answerBGDefinitionDesignationSave,2);
  } else { 
    alert('ERROR WITH PAYLOAD PACKAGE.  SEE A CHTNEASTERN INFORMATICS MEMBER');
  } 
}

function answerBGDefinitionEncounterSave(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Encounter Saved - Refresh the screen to see the changes');
   }
}

function answerBGDefinitionDesignationSave(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Diagnosis Designation Saved - Your Page will now refresh ... ');
     location.reload(true);
   }

}

function updateSiteMenu() {
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
   if ( byId('fldPRCSpecCatValue') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "") {     
       var mlURL = "/data-doers/sites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSiteMenu,2);               
     } else { 
       //NOTHING SELECTED
     }
   }
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
          menuTbl += "<tr><td onclick=\"fillField('fldPRCSite','"+element['siteid']+"','"+element['site']+"');blankVocabForm(2);updateSubSiteMenu();updateDiagnosisMenu();\" class=ddMenuItem>"+element['site']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCSite').innerHTML = menuTbl        
  } else {      
  }
}        

function updateSubSiteMenu() { 
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
   if ( byId('fldPRCSpecCatValue') && byId('fldPRCSite') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "" ) {     
       var mlURL = "/data-doers/subsites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       dta['site'] = byId('fldPRCSite').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSSiteMenu,2);               
     } else { 
       //NOTHING SELECTED
     }
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
     var menuTbl =  "<center><div style=\"font-size: 1.4vh\">No Subsite Listed</div>";     
  }
  byId('ddPRCSSite').innerHTML = menuTbl;        
}           

function updateDiagnosisMenu() { 
  var mlURL = "/data-doers/diagnosis-downstream"; 
  var dta = new Object();
  dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
  dta['site'] = byId('fldPRCSiteValue').value.trim();            
  var passdta = JSON.stringify(dta);
  universalAJAXStreamTwo("POST",mlURL,passdta,answerUpdateDiagnosisMenu,2);                 
}

function answerUpdateDiagnosisMenu(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      var menuTbl = "<table border=0 class=menuDropTbl style=\"max-width: 25vw;\"><tr><td align=right onclick=\"fillField('fldPRCDXMod','','');\" class=ddMenuClearOption>[clear]</td></tr>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCDXMod','"+element['dxid']+"','"+element['diagnosis']+"');\" class=ddMenuItem style=\"word-wrap: break-word;\">"+element['diagnosis']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCDXMod').innerHTML = menuTbl        
  } else {      
    //ERROR - DISPLAY ERROR
  }            
}
    
function editPathologyReportText(whichdialog) {
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
  dta['dialogid'] = whichdialog;
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
    alert('PATHOLOGY REPORT WAS SUCCESSFULLY SAVED.  YOUR SCREEN WILL NOW REFRESH');
    var rtn = JSON.parse(rtnData['responseText']);    
    closeThisDialog(rtn['DATA']['dialogid']);
    location.reload(true); 
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
    if (parseInt(byId('requestsasked').value) === 0) {        
      var given = new Object(); 
      given['rqstsuggestion'] = 'vandyinvest-requests'; 
      given['given'] = byId('fldSEGselectorAssignInv').value.trim();
      var passeddata = JSON.stringify(given);
      var mlURL = "/data-doers/suggest-something";
      universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
    }
  } 
}

function answerRequestDrop(rtnData) {
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=0 class=\"menuDropTbl\">";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('fldSEGselectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); \">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
    byId('requestsasked').value = 1;
  }
}

function markAsBank() { 
  if (byId('fldSEGselectorAssignInv') && byId('fldSEGselectorAssignReq') ) { 
    byId('fldSEGselectorAssignInv').value = "BANK"; 
    byId('fldSEGselectorAssignReq').value = "";

  } else { 
    alert('ERROR: SEE A CHTNEASTERN INFORMATICS STAFF MEMBER');
  } 
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
    
function addDefinedSegment(printInd) { 
  
    var dta = new Object();
    var noErrorInd = 1;
    
    ( byId('fldSEGBGNum') ) ? dta['bgNum'] = byId('fldSEGBGNum').value : noErrorInd = 0;
    ( byId('fldParentSegment') ) ? dta['parentSegment'] = byId('fldParentSegment').value : noErrorInd = 0;
    ( byId('fldNoParentIndicator') ) ? dta['noParentInd'] = byId('fldNoParentIndicator').checked : noErrorInd = 0;
    ( byId('fldSEGPreparationMethodValue') ) ? dta['preparationMethodValue'] = byId('fldSEGPreparationMethodValue').value : noErrorInd = 0;
    ( byId('fldSEGPreparationMethod') ) ? dta['preparationMethod'] = byId('fldSEGPreparationMethod').value : noErrorInd = 0;
    ( byId('fldSEGPreparation') ) ? dta['preparation'] = byId('fldSEGPreparation').value : noErrorInd = 0;
    ( byId('fldSEGPreparationValue') ) ? dta['preparationValue'] = byId('fldSEGPreparationValue').value : noErrorInd = 0;
    ( byId('fldSEGAddMetric') ) ? dta['addMetric'] = byId('fldSEGAddMetric').value : noErrorInd = 0;
    ( byId('fldSEGAddMetricUOMValue') ) ? dta['addMetricUOMValue'] = byId('fldSEGAddMetricUOMValue').value : noErrorInd = 0;
    ( byId('fldSEGAddMetricUOM') ) ? dta['addMetricUOM'] = byId('fldSEGAddMetricUOM').value : noErrorInd = 0;
    ( byId('fldSEGPreparationContainerValue') ) ? dta['preparationContainerValue'] = byId('fldSEGPreparationContainerValue').value : noErrorInd = 0;
    ( byId('fldSEGPreparationContainer') ) ? dta['preparationContainer'] = byId('fldSEGPreparationContainer').value : noErrorInd = 0;
    ( byId('fldSEGselectorAssignInv') ) ? dta['assignInv'] = byId('fldSEGselectorAssignInv').value : noErrorInd = 0;
    ( byId('fldSEGselectorAssignReq') ) ? dta['assignReq'] = byId('fldSEGselectorAssignReq').value : noErrorInd = 0;
    ( byId('fldSEGSGComments') ) ? dta['segComments'] = byId('fldSEGSGComments').value : noErrorInd = 0;
    ( byId('fldSEGDefinitionRepeater') ) ? dta['definitionRepeater'] = byId('fldSEGDefinitionRepeater').value : noErrorInd = 0;
    ( byId('fldSEGParentExhaustInd') ) ? dta['parentExhaustedInd'] = byId('fldSEGParentExhaustInd').checked : noErrorInd = 0;  
    ( byId('fldSEGAddToHisto') ) ? dta['addToHisto'] = byId('fldSEGAddToHisto').checked : noErrorInd = 0;      
    dta['printSlideInd'] = printInd;
    if ( noErrorInd === 1 ) {
      byId('btnSaveSeg').style.display = 'none';
      byId('btnSaveSegPrnt').style.display = 'none';
      var passdata = JSON.stringify(dta); 
      //TODO MAKE A 'PLEASE WAIT' INDICATION - AS THIS PROCESS CAN TAKE UP TO 10+ SECONDS 
      var mlURL = "/data-doers/coordinator-add-segment";
      universalAJAX("POST",mlURL,passdata, answerCoordinatorAddSegments,2);              
    } else { 
      alert('NOT ALL ELEMENTS EXIST IN THE SCREEN.  SEE A CHTNEASTERN INFORMATICS PERSON');
    }
}

function answerCoordinatorAddSegments(rtnData) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('btnSaveSeg').style.display = 'block';
    byId('btnSaveSegPrnt').style.display = 'block';
   } else {
      alert('SEGMENTS HAVE BEEN ADDED\\n\\nREFRESH YOUR SCREEN TO SEE CHANGES OR CONTINUE ADDING OTHER SEGMENTS');
      byId('fldSEGBGNum').value = "";
      byId('fldParentSegment').value = "";
      byId('fldNoParentIndicator').checked = false;
      byId('fldSEGPreparationMethodValue').value = "";
      byId('fldSEGPreparationMethod').value = "";
      byId('fldSEGPreparation').value = "";
      byId('fldSEGPreparationValue').value = "";
      byId('fldSEGAddMetric').value = "";
      byId('fldSEGAddMetricUOMValue').value = "";
      byId('fldSEGAddMetricUOM').value = "";
      byId('fldSEGPreparationContainerValue').value = "";
      byId('fldSEGPreparationContainer').value = "";
      byId('fldSEGselectorAssignInv').value = "";
      byId('fldSEGselectorAssignReq').value = "";
      byId('fldSEGSGComments').value = "";
      byId('fldSEGDefinitionRepeater').value = 1;
      byId('fldSEGParentExhaustInd').checked = false;
      byId('fldSEGAddToHisto').checked = false;
   }    
}

function revealFurtherQMSActions( whatAction, whatToReveal ) { 
  byId('labdiv'+whatToReveal).style.display = 'none';
  byId('tumdiv'+whatToReveal).style.display = 'none';
//  byId('hprdiv'+whatToReveal).style.display = 'none';
  switch ( whatAction ) { 
    case 'L':
      byId('labdiv'+whatToReveal).style.display = 'block';
    break;
    case 'Q':
      byId('tumdiv'+whatToReveal).style.display = 'block';
    break;
//    case 'H':
//      byId('hprdiv'+whatToReveal).style.display = 'block';
//    break;
  }
}

function manageMoleTest(addIndicator, referencenumber, fldsuffix) { 
 
  if (byId('molecularTestJsonHolderConfirm'+fldsuffix)) {  
   if (byId('molecularTestJsonHolderConfirm'+fldsuffix).value === "") { 
     if (addIndicator === 1) { 
        var hldVal = [];
        hldVal.push(  [ byId('hprFldMoleTest'+fldsuffix+'Value').value,  byId('hprFldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('molecularTestJsonHolderConfirm'+fldsuffix).value = JSON.stringify(hldVal);
      }
    } else { 
      if (addIndicator === 1) { 
        var hldVal = JSON.parse(byId('molecularTestJsonHolderConfirm'+fldsuffix).value);
        hldVal.push(  [ byId('hprFldMoleTest'+fldsuffix+'Value').value,  byId('hprFldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('molecularTestJsonHolderConfirm'+fldsuffix).value = JSON.stringify(hldVal);
      }
      if (addIndicator === 0) { 
         var hldVal = JSON.parse(byId('molecularTestJsonHolderConfirm'+fldsuffix).value);             
         var newVal = [];
         var key = 0;   
         hldVal.forEach(function(ele) { 
            if (key !== referencenumber) {
              newVal.push(ele);    
            }
            key++;
         });
         hldVal = newVal;
         byId('molecularTestJsonHolderConfirm'+fldsuffix).value = JSON.stringify(hldVal);   
      }      
    }
    byId('hprFldMoleTest'+fldsuffix+'Value').value = "";
    byId('hprFldMoleTest'+fldsuffix).value = "";
    byId('hprFldMoleResult'+fldsuffix+'Value').value = "";
    byId('hprFldMoleResult'+fldsuffix).value = "";
    byId('hprFldMoleScale'+fldsuffix).value = "";            
    var moleTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
    var cntr = 0;         
    hldVal.forEach(function(element) {         
      moleTestTbl += "<tr onclick=\"manageMoleTest(0,"+cntr+",'"+fldsuffix+"');\" class=ddMenuItem><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 0;\">cancel</i><td style=\"width: 15vw; padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1);\">"+element[1]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\">"+element[3]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\">"+element[4]+"</td></tr>";
      cntr++;
     });
     moleTestTbl += "</table>";
     byId('dspDefinedMolecularTestsConfirm'+fldsuffix).innerHTML = moleTestTbl;               
  }

}

var masteridsuffix = "";
function triggerMolecularFill(menuid, menuval, valuedsp, idsuffix) { 
   byId('hprFldMoleResult'+idsuffix).value = "";
   byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   byId('hprFldMoleScale'+idsuffix).value = "";
   masteridsuffix = idsuffix;
   if (menuid === 0) { 
     //CLEAR FIELD
       byId('hprFldMoleTest'+idsuffix+'Value').value = "";
       byId('hprFldMoleTest'+idsuffix).value = "";     
       byId('hprFldMoleResult'+idsuffix).value = "";
       byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   } else { 
       byId('hprFldMoleTest'+idsuffix+'Value').value = menuval;
       byId('hprFldMoleTest'+idsuffix).value = valuedsp;    
       var mlURL = "/immuno-mole-result-list/" + menuid;
       universalAJAX("GET",mlURL,'',answerHPRTriggerMolecularFill,2);            
   }

}
            
function answerHPRTriggerMolecularFill(rtnData) {
   var resultTbl = "";         
   if (parseInt(rtnData['responseCode']) !== 200) {             
   } else {    
     var dta = JSON.parse(rtnData['responseText']);
     var resultTbl = "<table border=0 style=\"min-width: 12.5vw;\">";
     resultTbl += "<tr><td onclick=\"fillField('hprFldMoleResult','','');\" align=right class=ddMenuClearOption>[clear]</td></tr>";            
     dta['DATA'].forEach(function(element) { 
       //element['menuvalue']      
       resultTbl += "<tr><td class=ddMenuItem onclick=\"fillField('hprFldMoleResult"+masteridsuffix+"','"+element['menuvalue']+"','"+element['dspvalue']+"');\">"+element['dspvalue']+"</td></tr>";
     });  
     resultTbl += "</table>";    
     masteridsuffix = "";
   }
   if (byId('moleResultDropDown')) { 
     byId('moleResultDropDown').innerHTML = resultTbl;         
   }
}

function saveQMSAction( suffixid, biogrouplabel ) { 
  var dta = new Object(); 
  dta['bglabel'] = biogrouplabel;
  dta['qmsaction'] = byId('fldQMSStat'+suffixid+'Value').value;
  dta['furtheraction'] = byId('fldQMSLA'+suffixid+'Value').value.trim();
  dta['furtheractionnote'] = byId('fldLabActNote'+suffixid).value.trim();
  dta['prctumor'] = byId('fldTmrTumor'+suffixid).value;
  dta['prccell'] =  byId('fldTmrCell'+suffixid).value;
  dta['prcnecro'] =  byId('fldTmrNecros'+suffixid).value;
  dta['prcacellmucin'] = byId('fldTmrACell'+suffixid).value;
  dta['prcneoplaststrom'] = byId('fldTmrNeoPlas'+suffixid).value;
  dta['prcnonneoplast'] = byId('fldTmrNonNeo'+suffixid).value;
  dta['prcepipth'] = byId('fldTmrEpip'+suffixid).value;
  dta['prcinflame'] = byId('fldTmrInFlam'+suffixid).value; 
  dta['moleculartests'] = byId('molecularTestJsonHolderConfirm'+suffixid).value; 
  dta['qmsnote'] = byId('qmsNotes'+suffixid).value; 
  var passdata = JSON.stringify(dta);
  console.log(passdata);
  var mlURL = "/data-doers/mark-QMS";
  universalAJAX("POST",mlURL,passdata, answerSaveQMSAction,2);              
}

function answerSaveQMSAction(rtnData) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    //byId('btnSaveSeg').style.display = 'block';
    //byId('btnSaveSegPrnt').style.display = 'block';
   } else {
    alert("DATA HAS BEEN SAVED! \\nThe screen will now reload");
    location.reload(true);
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
  $regUsr = $this->sessid;  
  $regCode = $this->regcode; 
  $vault = phiserver;


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

function popDV( idstr ) {
  var dta = new Object();
  dta['idstr'] = idstr;
  var passdata = JSON.stringify(dta); 
  var mlURL = "/data-doers/vault-user-login-check";
  universalAJAX("POST",mlURL,passdata,answerPopDV,1);
}

function answerPopDV ( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("DONOR VAULT ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse(rtnData['responseText']);
     openOutSidePage('{$vault}/'+dta['DATA'] );
   }        
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
    if ( byId('standardModalBacker') ) { 
      byId('standardModalBacker').style.display = 'block';
    }
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
    CSV += ReportTitle + '\\n';
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
        CSV += row + '\\n';
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
        CSV += row + '\\n';
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
     
function makeFurtherActionRequest () {
  var obj = new Object(); 
  obj['rqstPayload'] = byId('faFldRequestJSON').value.trim(); 
  obj['bioReference'] = byId('faFldReference').value.trim();
  obj['actionsValue'] = byId('faFldActionsValue').value.trim(); 
  obj['actionNote'] = byId('faFldNotes').value.trim(); 
  obj['agent'] = byId('faFldAssAgentValue').value.trim();
  obj['priority'] = byId('faFldPriorityValue').value.trim(); 
  obj['duedate'] = byId('faFldByDate').value.trim();
  obj['notifycomplete'] = byId('faFldNotifyComplete').checked;
  var passdta = JSON.stringify(obj);
  var mlURL = "/data-doers/save-further-action";      
  universalAJAX("POST",mlURL,passdta,answerFurtherActionRequests,2);
}

function answerFurtherActionRequests( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
     byId('faFldActionsValue').value = "";
     byId('faFldActions').value = "";
     byId('faFldNotes').value = ""; 
     byId('faFldAssAgentValue').value = "";
     byId('faFldAssAgent').value = "";
     byId('faFldPriorityValue').value = "";
     byId('faFldPriority').value = "";
     byId('faFldByDate').value = "";
     byId('faFldNotifyComplete').checked = false;
     alert('Saved'); 
     //BUILD GRID
     var dta = JSON.parse(rtnData['responseText']);
     bldFurtherActionGrid( dta['DATA']['pbiosample'] );
   }        
}        

function bldFurtherActionGrid( pbiosample ) { 
   var obj = new Object(); 
   obj['bgref'] = pbiosample;  
   var passdta = JSON.stringify(obj); 
   var mlURL = "/data-doers/display-pbfatbl";
   universalAJAX("POST",mlURL,passdta,answerBldFurtherActionGrid,2);
}
   
function answerBldFurtherActionGrid ( rtnData ) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
      if ( byId('dspOtherFurtherActions') ) { 
        var dta = JSON.parse(rtnData['responseText']);
        byId('dspOtherFurtherActions').innerHTML = "";
        byId('dspOtherFurtherActions').innerHTML = dta['DATA'];
      }
   }        
}

function deactivateFA( whichfaid ) { 
   var obj = new Object(); 
   obj['faid'] = parseInt(whichfaid);  
   var passdta = JSON.stringify(obj); 
   var mlURL = "/data-doers/deactivate-pbfa";
   universalAJAX("POST",mlURL,passdta,answerDeactivateFA,2);
 }
   
 function answerDeactivateFA ( rtnData ) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
       var pb = JSON.parse(rtnData['responseText']);
       bldFurtherActionGrid( pb['DATA'] );
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

function openEditDocDialog( whichdocumentid ) { 
  if ( whichdocumentid.trim() !== "" ) { 
    var mlURL = "/setup-help-doc-edit-dialog/" + whichdocumentid;
    universalAJAX("GET",mlURL,"",answerEditDocDialog, 1);
  }
}

function answerEditDocDialog ( rtnData ) { 
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
       byId('standardModalDialog').style.marginLeft = "-40vw";
       byId('standardModalDialog').style.left = "50%";
       byId('standardModalDialog').style.marginTop = 0;
       byId('standardModalDialog').style.top = "2vh";
       byId('standardModalBacker').style.display = 'block';
       byId('standardModalDialog').style.display = 'block';
     }  
   }        
}

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

function displayIndex() { 
  if ( byId('indexMenuSlide') ) {
    if ( parseInt(byId('indexMenuSlide').style.right) === -155 || isNaN(parseInt(byId('indexMenuSlide').style.right))  ) { 
      byId('indexMenuSlide').style.right = '0vw';
      if ( byId('fldHlpSrch') ) { 
        byId('fldHlpSrch').focus();
      }
    } else {
      byId('indexMenuSlide').style.right = '-155vw';
    }
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

  if ( byId('btnForgotEmail') ) { 
    byId('btnForgotEmail').addEventListener('click', function() {
      rqstPwdReset();
    }, false);
  }

  if (byId('btnLoginCtl')) {
    byId('btnLoginCtl').addEventListener('click', function() {
      doLogin();
    }, false);
  }

  if ( byId('forgetLink')) { 
    byId('forgetLink').addEventListener('click', function () { sendPWordReset(); }, false);     
  }

  document.addEventListener('keypress', function(event) { 
    if (event.which === 13) { 
      doLogin();
    }
  }, false);

}, false);

function sendPWordReset() { 

alert('SEND RESET NOT WORKING YET');

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

function rqstPwdReset() { 
  var dta = new Object(); 
  dta['rqstuser'] = byId('ssUser').value;
  var passdata = JSON.stringify(dta);
  var mlURL = "{$tt}/data-services/system-posts/request-password-reset";
  httpage.open("POST",mlURL,true);
  httpage.setRequestHeader("Authorization","Basic " + btoa("{$regUsr}:{$regCode}"));
  httpage.onreadystatechange = function () { 
    if (httpage.readyState === 4) {
      if (httpage.status === 200) { 
          alert('If you are a registered ScienceServer user, you will receive a password reset email.');
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
          alert('If you are a registered ScienceServer user, you will receive a dual-authentication access code in your email or by text message.  This code is valid for 12 hours');
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

function furtheractionrequests ( $rqststr ) { 
    
$tt = treeTop; 
$rtnThis = <<<RTNTHIS

   
function generateDialog( whichdialog, whatobject, e ) {   
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function sendActionUpdate( ) { 
  var dta = new Object(); 
  var taskcom = ( byId('thistaskcomplete') ) ? (byId('thistaskcompleteind').checked) ? 1 : 0 : 1;
  dta['ticket'] = byId('fldTicketNbr').value;
  dta['dateperformed'] = byId('fldDatePerformed').value;
  dta['action'] = byId('fldActionCode').value;
  dta['dialog'] = byId('fldDialogId').value;
  dta['notes'] = byId('fldComments').value;
  dta['taskcomplete'] = taskcom;        
  dta['complete'] = byId('fldCompleteInd').value;
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/further-action-action-note";
  universalAJAX("POST",mlURL,passdta, answerSendActionUpdate,2);  
}

function answerSendActionUpdate ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
      alert('SAVED');  
      var dta = JSON.parse(rtnData['responseText']);        
      closeThisDialog(dta['DATA']);
      location.reload(true); 
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

function saveFATicketEdit() {
  var dta = new Object(); 
  dta['ticket'] = byId('faFldTicketEncy').value.trim();
  dta['duedate'] = byId('bsqueryFromDateValue').value.trim();
  dta['agent'] = byId('faFldAssAgentValue').value.trim();
  dta['ticketnote'] = byId('faFldActionNote').value.trim();
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/further-action-edit-ticket";
  universalAJAX("POST",mlURL,passdta,answerSendActionTwoUpdate,2);
}

function answerSendActionTwoUpdate(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('SAVED');
   }        
}

function printThisTicket() { 
  if ( byId('faFldTicketEncy') ) {
    openOutSidePage("{$tt}/print-obj/further-action-ticket/"+byId('faFldTicketEncy').value.trim());  
  }
}

function sendThisTicket() { 
  generateDialog ('faSendTicket',byId('faFldTicketEncy').value.trim() );
}

function emailThisHereTicket() { 
  var dta = new Object(); 
  dta['ticket'] = byId('encyFASendTicket').value.trim();
  dta['recip'] = byId('faFldEmlAgentValue').value.trim();
  dta['emailtext'] = byId('faSendEmailText').value.trim();
  dta['dialogid'] = byId('faDialogId').value.trim();
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/further-action-email-send-ticket";
  universalAJAX("POST",mlURL,passdta,answerSendActionEmail,2);
}

function answerSendActionEmail ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('SAVED');
     var dta = JSON.parse(rtnData['responseText']);        
     closeThisDialog(dta['DATA']['dialogid']);
   }        
}

RTNTHIS;
return $rtnThis;    
}


function qmsactions ( $rqststr ) { 

$tt = treeTop; 
$rtnThis = <<<RTNTHIS

document.addEventListener('DOMContentLoaded', function() {  
  
  if ( byId('btnRqstFA')) { 
     byId('btnRqstFA').addEventListener('click', function() { 
       generateDialog('bgRqstFA', byId('fldFAGetter').value );
     }, false);
  }        
        
   if (byId('btnReloadGrid')) { 
    byId('btnReloadGrid').addEventListener('click', function() { 
        location.reload(true);
     }, false);        
   }
       
}, false);  
        

function changeSupportingTab(whichtab) { 
  var divs = document.getElementsByClassName('HPRReviewDocument'); 
  for ( var i = 0; i < divs.length; i++ ) {
    byId('dspTabContent'+i).style.display = 'none';
  }
  byId('dspTabContent'+whichtab).style.display = 'block';
} 

function generateDialog( whichdialog, whatobject, e ) {   
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}


function recipSelector(whichrecip) {
  if ( byId(whichrecip).dataset.selected === 'false' ) { 
    byId(whichrecip).dataset.selected = 'true';
  } else { 
    byId(whichrecip).dataset.selected = 'false';
  }
}  

function sendHPREmail() {
  var reciplist = new Array();
  var cntr = 0; 
  var dta = new Object(); 
  var x = document.getElementsByClassName("recipitemlisting");
   for ( var i = 0; i < x.length; i++ ) {
     if ( x[i].dataset.selected === 'true' ) {
       reciplist[cntr] = x[i].id;
       cntr++;
     }
   }
   dta['recipientlist'] = JSON.stringify ( reciplist );
   dta['messagetext'] = byId('hprEmlMsg').value.trim();
   dta['dialogid'] = byId('identDialogid').value;
   var passdta = JSON.stringify(dta);
   var mlURL = "/data-doers/hpr-send-email";
   universalAJAX("POST",mlURL,passdta,answerHPRSendEmail,2);
}

function answerHPRSendEmail( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Message Sent'); 
     var diddta = JSON.parse( rtnData['responseText'] ); 
     closeThisDialog( diddta['DATA'] );
   }
}

function displayShipDoc(e, shipdocencryption) {
  e.stopPropagation(); 
  openOutSidePage("{$tt}/print-obj/shipment-manifest/"+shipdocencryption);  
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

var masteridsuffix = "";
function triggerMolecularFill(menuid, menuval, valuedsp, idsuffix) { 
   byId('hprFldMoleResult'+idsuffix).value = "";
   byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   byId('hprFldMoleScale'+idsuffix).value = "";
   masteridsuffix = idsuffix;
   if (menuid === 0) { 
     //CLEAR FIELD
       byId('fldMoleTest'+idsuffix+'Value').value = "";
       byId('fldMoleTest'+idsuffix).value = "";     
       byId('hprFldMoleResult'+idsuffix).value = "";
       byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   } else { 
       byId('fldMoleTest'+idsuffix+'Value').value = menuval;
       byId('fldMoleTest'+idsuffix).value = valuedsp;    
       var mlURL = "/immuno-mole-result-list/" + menuid;
       universalAJAX("GET",mlURL,'',answerHPRTriggerMolecularFill,2);            
   }
}
            
function answerHPRTriggerMolecularFill(rtnData) {
   var resultTbl = "";         
   if (parseInt(rtnData['responseCode']) !== 200) {             
   } else {    
     var dta = JSON.parse(rtnData['responseText']);
     var resultTbl = "<table border=0 style=\"min-width: 12.5vw;\">";
     resultTbl += "<tr><td onclick=\"fillField('hprFldMoleResult','','');\" align=right class=ddMenuClearOption>[clear]</td></tr>";            
     dta['DATA'].forEach(function(element) { 
       //element['menuvalue']      
       resultTbl += "<tr><td class=ddMenuItem onclick=\"fillField('hprFldMoleResult"+masteridsuffix+"','"+element['menuvalue']+"','"+element['dspvalue']+"');\">"+element['dspvalue']+"</td></tr>";
     });  
     resultTbl += "</table>";    
     masteridsuffix = "";
   }
   if (byId('moleResultDropDown')) { 
     byId('moleResultDropDown').innerHTML = resultTbl;         
   }
}

function manageMoleTest(addIndicator, referencenumber, fldsuffix) { 
  if (byId('hprMolecularTestJsonHolderConfirm')) { 
   if (byId('hprMolecularTestJsonHolderConfirm').value === "") { 
     if (addIndicator === 1) { 
        if ( byId('fldMoleTest'+fldsuffix+'Value').value.trim() === "" ) { 
          alert('YOU HAVE NOT SELECTED A MOLECULAR TEST.');
          return null;
        }
        var hldVal = [];
        hldVal.push(  [ byId('fldMoleTest'+fldsuffix+'Value').value,  byId('fldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);
      }
    } else { 
      if (addIndicator === 1) {
        if ( byId('fldMoleTest'+fldsuffix+'Value').value.trim() === "" ) { 
          alert('YOU HAVE NOT SELECTED A MOLECULAR TEST.');
          return null;
        }
        var hldVal = JSON.parse(byId('hprMolecularTestJsonHolderConfirm').value);
        hldVal.push(  [ byId('fldMoleTest'+fldsuffix+'Value').value,  byId('fldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);
      }
      if (addIndicator === 0) { 
         var hldVal = JSON.parse(byId('hprMolecularTestJsonHolderConfirm').value);             
         var newVal = [];
         var key = 0;   
         hldVal.forEach(function(ele) { 
            if (key !== referencenumber) {
              newVal.push(ele);    
            }
            key++;
         });
         hldVal = newVal;
         byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);   
      }      
    }
    byId('fldMoleTest'+fldsuffix+'Value').value = "";
    byId('fldMoleTest'+fldsuffix).value = "";
    byId('hprFldMoleResult'+fldsuffix+'Value').value = "";
    byId('hprFldMoleResult'+fldsuffix).value = "";
    byId('hprFldMoleScale'+fldsuffix).value = "";            
    var moleTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
    var cntr = 0;         
    hldVal.forEach(function(element) {         
      moleTestTbl += "<tr onclick=\"manageMoleTest(0,"+cntr+",'"+fldsuffix+"');\" class=ddMenuItem><td style=\"border-bottom: 1px solid rgba(160,160,160,1); width: 1vw;\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 0;\">cancel</i><td style=\" padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[1]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[3]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[4]+"</td></tr>";
      cntr++;
     });
     moleTestTbl += "</table>";
     byId('dspDefinedMolecularTestsConfirm'+fldsuffix).innerHTML = moleTestTbl;               
  }
}

function revealPR() { 
  if (byId('pathologyrptdisplay')) { 
    if (parseInt(byId('pathologyrptdisplay').style.left) < 0 || byId('pathologyrptdisplay').style.left == "" ) {
      byId('pathologyrptdisplay').style.left = "5vw";
    } else { 
      byId('pathologyrptdisplay').style.left = "-50vw";
    }
  }
}

function displaySegmentList ( whichsegmentlist, thisgroupind ) { 
  if ( byId('segment'+whichsegmentlist) ) { 
    if ( byId('segment'+whichsegmentlist).style.display == 'block' || ( thisgroupind === 1 &&  byId('segment'+whichsegmentlist).style.display.trim() == ""  )) { 
      byId('segment'+whichsegmentlist).style.display = 'none';
    } else { 
      byId('segment'+whichsegmentlist).style.display = 'block';
    }
  }
}

function dlgSaveBGComments() {
   if ( byId('fldDspBGComment') && byId('fldID') ) { 
     var obj = new Object();
     byId('waitDsp').style.display = 'block';
     byId('dspLineOne').style.display = 'none';
     byId('dspLineTwo').style.display = 'none';
     byId('dspLineThree').style.display = 'none';
     obj['comment'] = byId('fldDspBGComment').value;
     obj['key'] = byId('fldID').value;
     var passdta = JSON.stringify(obj);
     var mlURL = "/data-doers/dialog-action-save-comments";
     universalAJAX("POST",mlURL,passdta,answerDLGSaveBGComments,2);
   }
}

function answerDLGSaveBGComments( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('waitDsp').style.display = 'none';
    byId('dspLineOne').style.display = 'block';
    byId('dspLineTwo').style.display = 'block';
    byId('dspLineThree').style.display = 'block';
   } else { 
      location.reload(true);
   }
} 

function packageEncounterSave( eid ) {
  var dta = new Object();
  var allfieldsfound = 1;
  byId('dlgFldPHIAge'+eid) ? dta['agemetric'] = byId('dlgFldPHIAge'+eid).value : allfieldsfound = 0;
  byId('dlgFldAgeUOM'+eid) ? dta['agemetricuom'] = byId('dlgFldAgeUOM'+eid).value : allfieldsfound = 0;
  byId('dlgFldRace'+eid) ? dta['race'] = byId('dlgFldRace'+eid).value : allfieldsfound = 0;
  byId('dlgFldSex'+eid) ? dta['sex'] = byId('dlgFldSex'+eid).value : allfieldsfound = 0;
  byId('dlgFldCX'+eid) ? dta['cxind'] = byId('dlgFldCX'+eid).value : allfieldsfound = 0;
  byId('dlgFldRX'+eid) ? dta['rxind'] = byId('dlgFldRX'+eid).value : allfieldsfound = 0;
  byId('dlgFldSbjt'+eid) ? dta['subjectnbr'] = byId('dlgFldSbjt'+eid).value : allfieldsfound = 0;
  byId('dlgFldProtocol'+eid) ? dta['protocolnbr'] = byId('dlgFldProtocol'+eid).value : allfieldsfound = 0;
  byId('dlgpbiosample'+eid) ? dta['pbiosample'] = byId('dlgpbiosample'+eid).value : allfieldsfound = 0;
  byId('dlgpxiid'+eid) ? dta['pxiid'] = byId('dlgpxiid'+eid).value : allfieldsfound = 0;
  if ( allfieldsfound === 1 ) { 
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/dialog-action-bg-definition-encounter-save";
    universalAJAX("POST",mlURL,passdta,answerBGDefinitionEncounterSave,2);
  } else { 
    alert('ERROR WITH PAYLOAD PACKAGE.  SEE A CHTNEASTERN INFORMATICS MEMBER');
  } 
}

function answerBGDefinitionEncounterSave(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Encounter Saved - Refresh the screen to see the changes');
   }
}

function editVocab() { 
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 0 ) { 
    byId('btnVocEditEnable').dataset.vocabunlock = 1;
    byId('buttnText').innerHTML = "Save";
    blankVocabForm();
  } else { 
    packageDiagnosisSave();
  }
}

function blankVocabForm( blankIndicatorLevel = 0 ) {

if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
switch (blankIndicatorLevel) { 
  case 0:
    byId('fldPRCSpecCat').value = "";
    byId('fldPRCSpecCatValue').value = "";
  case 1:
    byId('fldPRCSite').value = "";
    byId('fldPRCSiteValue').value = "";
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category)</div>";     
    byId('ddPRCSite').innerHTML = menuTbl;            
  case 2:
    byId('fldPRCSSite').value = "";
    byId('fldPRCSSiteValue').value = "";
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Site)</div>";     
    byId('ddPRCSSite').innerHTML = menuTbl;            
  case 3:
    byId('fldPRCDXMod').value = "";
    byId('fldPRCDXModValue').value = "";
    byId('fldPRCDXOverride').checked = false;    
    var menuTbl =  "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category and Site)</div>";     
    byId('ddPRCDXMod').innerHTML = menuTbl;            
  case 4:
    byId('fldPRCMETSSite').value = "";
    byId('fldPRCMETSSiteValue').value = "";
  case 5:
    byId('fldPRCSitePosition').value = "";
    byId('fldPRCSitePositionValue').value = "";
    byId('fldPRCSystemList').value = "";
    byId('fldPRCSystemListValue').value = "";
}
}

}

function overridedxmenu() { 
if (parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 0) {
   alert('VOCABULARY IS NOT UNLOCKED');
} else {
    if (byId('fldPRCDXOverride').checked) {
          //LIST THE WHOLE DX HERE
          byId('ddPRCDXMod').innerHTML = ""; 
          byId('fldPRCDXMod').value = "";
          byId('fldPRCDXModValue').value = "";            
          allDiagnosisMenu();
        } else {
          if ( byId('fldPRCSpecCat').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "") { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             updateDiagnosisMenu();  
          } else { 
             byId('fldPRCDXMod').value = "";
             byId('fldPRCDXModValue').value = "";
             byId('ddPRCDXMod').innerHTML = "<center><div style='font-size: 1.4vh'>(Choose a Specimen Category and Site)</div>";  
          }
        }
}
}

function updateSiteMenu() {
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
   if ( byId('fldPRCSpecCatValue') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "") {     
       var mlURL = "/data-doers/sites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSiteMenu,2);               
     } else { 
       //NOTHING SELECTED
     }
   }
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
          menuTbl += "<tr><td onclick=\"fillField('fldPRCSite','"+element['siteid']+"','"+element['site']+"');blankVocabForm(2);updateSubSiteMenu();updateDiagnosisMenu();\" class=ddMenuItem>"+element['site']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCSite').innerHTML = menuTbl        
  } else {      
  }
}        

function updateSubSiteMenu() { 
  if ( parseInt(byId('btnVocEditEnable').dataset.vocabunlock) === 1 ) { 
   if ( byId('fldPRCSpecCatValue') && byId('fldPRCSite') ) {
     if ( byId('fldPRCSpecCatValue').value.trim() !== "" && byId('fldPRCSite').value.trim() !== "" ) {     
       var mlURL = "/data-doers/subsites-by-specimen-category"; 
       var dta = new Object();
       dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
       dta['site'] = byId('fldPRCSite').value.trim();
       var passdta = JSON.stringify(dta);
       universalAJAX("POST",mlURL,passdta,answerUpdateSSiteMenu,2);               
     } else { 
       //NOTHING SELECTED
     }
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
     var menuTbl =  "<center><div style=\"font-size: 1.4vh\">No Subsite Listed</div>";     
  }
  byId('ddPRCSSite').innerHTML = menuTbl;        
}           

function updateDiagnosisMenu() { 
  var mlURL = "/data-doers/diagnosis-downstream"; 
  var dta = new Object();
  dta['specimencategory'] = byId('fldPRCSpecCatValue').value.trim();
  dta['site'] = byId('fldPRCSiteValue').value.trim();            
  var passdta = JSON.stringify(dta);
  universalAJAXStreamTwo("POST",mlURL,passdta,answerUpdateDiagnosisMenu,2);                 
}

function answerUpdateDiagnosisMenu(rtnData) { 
  if (parseInt(rtnData['responseCode']) === 200) {
    var dta = JSON.parse( rtnData['responseText'] );
    var rquestFld = dta['MESSAGE'];
    if (parseInt(dta['ITEMSFOUND']) > 0) {
      var dspList = dta['DATA'];
      var menuTbl = "<table border=0 class=menuDropTbl style=\"max-width: 25vw;\"><tr><td align=right onclick=\"fillField('fldPRCDXMod','','');\" class=ddMenuClearOption>[clear]</td></tr>";      
      dspList.forEach( function(element) {          
          menuTbl += "<tr><td onclick=\"fillField('fldPRCDXMod','"+element['dxid']+"','"+element['diagnosis']+"');\" class=ddMenuItem style=\"word-wrap: break-word;\">"+element['diagnosis']+"</td></tr>";            
      });
      menuTbl += "</table>";      
   } else {
      var menuTbl =  "<center><div style=\"font-size: 1.4vh\">(Choose a Specimen Category)</div>";     
    }
    byId('ddPRCDXMod').innerHTML = menuTbl        
  } else {      
    //ERROR - DISPLAY ERROR
  }            
}

function allDiagnosisMenu() { 
       var mlURL = "/data-doers/all-downstream-diagnosis"; 
       universalAJAXStreamTwo("POST",mlURL,"",answerUpdateDiagnosisMenu,2);                 
}            
                    
function packageDiagnosisSave() { 
  var dta = new Object();
  var allfieldsfound = 1;
  byId('fldHoldBioGroup') ? dta['refbg'] = byId('fldHoldBioGroup').value.trim() : allfieldsfound = 0;
  byId('fldPRCSpecCat') ? dta['speccat'] = byId('fldPRCSpecCat').value.trim() : allfieldsfound = 0;
  byId('fldPRCSite') ? dta['collectedsite'] = byId('fldPRCSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCSSite') ? dta['collectedsubsite'] = byId('fldPRCSSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCDXMod') ? dta['diagnosismodifier'] = byId('fldPRCDXMod').value.trim() : allfieldsfound = 0;
  byId('fldPRCMETSSite') ? dta['metsfromsite'] = byId('fldPRCMETSSite').value.trim() : allfieldsfound = 0;
  byId('fldPRCSitePosition') ? dta['siteposition'] = byId('fldPRCSitePosition').value.trim() : allfieldsfound = 0;
  byId('fldPRCSystemList') ? dta['systemicdx'] = byId('fldPRCSystemList').value.trim() : allfieldsfound = 0;
  byId('fldPRCDXOverride') ? dta['dxoverride'] = byId('fldPRCDXOverride').checked : allfieldsfound = 0;        
  if ( allfieldsfound === 1 ) { 
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/dialog-action-bg-definition-designation-save";
    universalAJAX("POST",mlURL,passdta,answerBGDefinitionDesignationSave,2);
  } else { 
    alert('ERROR WITH PAYLOAD PACKAGE.  SEE A CHTNEASTERN INFORMATICS MEMBER');
  } 
}

function answerBGDefinitionDesignationSave(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Diagnosis Designation Saved - Your Page will now refresh ... ');
     location.reload(true);
   }
}

function copyHPRToBS( encyhpr ) { 
  var dta = new Object();
  dta['biohpr'] = encyhpr;
  byId('standardModalBacker').style.display = 'block';
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/copy-hpr-to-bs";
  universalAJAX("POST",mlURL,passdta,answerCopyHPRToBS,2);
} 

function answerCopyHPRToBS ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    byId('standardModalBacker').style.display = 'none';
    alert("ERROR:\\n"+dspMsg);
   } else {
     location.reload(true);
   }
}

function saveMolePrc() { 
  var dta = new Object();
  dta['encyhpr'] = byId('dataRowOne').dataset.encyreviewid; 
  dta['encybg'] = byId('dataRowOne').dataset.encybg;
  dta['uninvolvedvalue'] = byId('fldPRCUnInvolvedValue').value.trim();
  dta['tumorgrade'] = byId('fldTumorGrade').value.trim();
  dta['tumorscale'] = byId('fldTumorGradeScaleValue').value.trim();
  
  //TODO:  FIGURE OUT HOW TO NOT HARD CODE IN THIS PHP/JAVASCRIPT CREATION
  dta['percentagetumor'] = byId('PERCENTAGETUMOR').value.trim();
  dta['percentagecellularity'] = byId('PERCENTAGECELLULARITY').value.trim();
  dta['percentagenecrosis'] = byId('TUMORNECROSIS').value.trim();
  dta['percentageneoplasticstroma'] = byId('NEOPLASTICSTROMA').value.trim();
  dta['percentagenonneoplasticstroma'] = byId('NONNEOPLASTICSTROMA').value.trim();
  dta['percentageacellularmucin'] = byId('ACELLULARMUCIN').value.trim();
  //END TODO
  
  dta['moleteststring'] = byId('hprMolecularTestJsonHolderConfirm').value.trim();
  byId('standardModalBacker').style.display = 'block';
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/qa-mole-test-final";
  universalAJAX("POST",mlURL,passdta,answerSaveMolePrc,2);
}
  
function answerSaveMolePrc(rtnData) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';
   } else {
     alert('Molecular/Percentage Square has been saved ... ');    
     byId('standardModalBacker').style.display = 'none';
   }
}  

function selectActiveSegmentRecord ( whichrecord ) { 
 if ( byId(whichrecord).dataset.selected === 'false' ) { 
     byId(whichrecord).dataset.selected = 'true';
  } else { 
     byId(whichrecord).dataset.selected = 'false';
  }
}
  
function toggleActiveSegmentRecords ( ) {
  if (byId('thisworkingtable')) { 
    for (var c = 0; c < byId('thisworkingtable').tBodies[0].rows.length; c++) {  
      if (byId('thisworkingtable').tBodies[0].rows[c].dataset.selected === 'true') { 
        byId('thisworkingtable').tBodies[0].rows[c].dataset.selected = 'false';
      } else { 
        byId('thisworkingtable').tBodies[0].rows[c].dataset.selected = 'true';
      }
    }
  }    
}

function restatusSelectedSegments() { 

  if (byId('thisworkingtable')) {
    var cntr = 0;
    var bgslist = []; 
    for (var c = 0; c < byId('thisworkingtable').tBodies[0].rows.length; c++) {  
      if (byId('thisworkingtable').tBodies[0].rows[c].dataset.selected === 'true') { 
        cntr++;  
        bgslist.push(byId('thisworkingtable').tBodies[0].rows[c].dataset.bgs);

      }
    }
    if ( cntr < 1 ) { 
      alert('You have not selected any segments to work with'); 
    } else {
      byId('standardModalBacker').style.display = 'block';
      var passdta = JSON.stringify(bgslist); 
      generateDialog('qmsRestatusSegments', passdta );
    }
  }
}


function selectorInvestigator() { 
  if (byId('qmsGlobalSelectorAssignInv').value.trim().length > 3) { 
    getSuggestions('qmsGlobalSelectorAssignInv',byId('qmsGlobalSelectorAssignInv').value.trim()); 
  } else { 
    byId('assignInvestSuggestion').innerHTML = "&nbsp;";
    byId('assignInvestSuggestion').style.display = 'none';
  }
}

function getSuggestions(whichfield, passedValue) { 
switch (whichfield) { 
  case 'qmsGlobalSelectorAssignInv':
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
       rsltTbl += "<tr class=ddMenuItem onclick=\"fillField('qmsGlobalSelectorAssignInv','"+element['investvalue']+"','"+element['investvalue']+"'); byId('assignInvestSuggestion').innerHTML = '&nbsp;'; byId('assignInvestSuggestion').style.display = 'none';\"><td valign=top>"+element['investvalue']+"</td><td valign=top>"+element['dspinvest']+"</td></tr>";
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
  if (byId('qmsGlobalSelectorAssignInv').value.trim() !== "" ) {   
    if (parseInt(byId('requestsasked').value) === 0) {        
      var given = new Object(); 
      given['rqstsuggestion'] = 'vandyinvest-requests'; 
      given['given'] = byId('qmsGlobalSelectorAssignInv').value.trim();
      var passeddata = JSON.stringify(given);
      var mlURL = "/data-doers/suggest-something";
      universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
    }
  } 
}

function answerRequestDrop(rtnData) {
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=0 class=\"menuDropTbl\">";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('qmsGlobalSelectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); \">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
    byId('requestsasked').value = 1;
  }
}

function markIQMSComplete() { 
    var dta = new Object(); 
    dta['encyreviewid'] =  byId('dataRowOne').dataset.encyreviewid;
    dta['encybg'] = byId('dataRowOne').dataset.encybg;
    var passeddata = JSON.stringify(dta);
    var mlURL = "/data-doers/mark-qa-incon-complete";
    universalAJAX("POST",mlURL,passeddata,answerMarkQAFinalComplete,2);
}  
  
function markQMSComplete() { 
    var dta = new Object(); 
    dta['encyreviewid'] =  byId('dataRowOne').dataset.encyreviewid;
    dta['encybg'] = byId('dataRowOne').dataset.encybg;
    var passeddata = JSON.stringify(dta);
    var mlURL = "/data-doers/mark-qa-final-complete";
    universalAJAX("POST",mlURL,passeddata,answerMarkQAFinalComplete,2);
}

function answerMarkQAFinalComplete ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';
   } else {
     alert('Biosample has been marked \'QA/QMS Complete\' ... Either go back to the Queue or continue work on the associated biogroups');    
     byId('standardModalBacker').style.display = 'none'; 
   }
}

function setQASegStatus( whichsegstatus ) { 
   byId('requestsasked').value = 0;
   byId('requestDropDown').innerHTML = "";
   byId('qmsGlobalSelectorAssignInv').value = '';
   byId('qmsGlobalSelectorAssignReq').value = '';
   byId('qmsGlobalSelectorAssignInv').value = whichsegstatus; 
}

function saveQMSSegReassign( whichdialogid ) { 
   var dta = new Object();
   dta['seglist'] = byId('fldGlobalSegArrString').value.trim(); 
   dta['dialogid'] = whichdialogid; 
   dta['assignInvCode'] = byId('qmsGlobalSelectorAssignInv').value.trim();
   dta['assignReqCode'] = byId('qmsGlobalSelectorAssignReq').value.trim();
   dta['segComments'] = byId('qmsGloablSTSComments').value.trim(); 
   var passeddata = JSON.stringify( dta );
   var mlURL = "/data-doers/qms-mass-reassign-segments";
   universalAJAX("POST",mlURL,passeddata,answerSaveQMSSegReassign,2);
}
  
function answerSaveQMSSegReassign( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Segment(s) have been updated ... This screen will now refresh');
     location.reload(true);           
   }  
}

function emailer_emailselect(whichid) { 

   if ( byId(whichid).dataset.selected === 'true' ) { 
     byId(whichid).dataset.selected = 'false';
   } else { 
     byId(whichid).dataset.selected = 'true';
   } 

}

function insertAtCursor(myField, myValue) {
        //MOZILLA and others
        if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            myField.value = myField.value.substring(0, startPos)
                + myValue
                + myField.value.substring(endPos, myField.value.length);
        } else {
            myField.value += myValue;
        }
}

function getQMSLetter( whichletter ) {

   var dta = new Object();
   dta['qmsletter'] = whichletter;
   dta['bgs'] = byId('insertElementals').dataset.bgs;
   dta['shipdocrefid'] = byId('insertElementals').dataset.shipdocrefid;
   dta['shippeddate'] = byId('insertElementals').dataset.shippeddate;
   dta['prepmethod'] = byId('insertElementals').dataset.prepmethod;
   dta['preparation'] = byId('insertElementals').dataset.preparation;
   dta['dxspecimencategory'] = byId('insertElementals').dataset.dxspecimencategory;
   dta['dxsite'] = byId('insertElementals').dataset.dxsite;
   dta['dxssite'] = byId('insertElementals').dataset.dxssite;
   dta['dxdx'] = byId('insertElementals').dataset.dxdx;
   dta['dxmod'] = byId('insertElementals').dataset.dxmod;
   dta['dxmetsfrom'] = byId('insertElementals').dataset.dxsmetsfrom;
   dta['designation'] = byId('insertElementals').dataset.designation;
   dta['courier'] = byId('insertElementals').dataset.courier;
   dta['tracknbr'] = byId('insertElementals').dataset.tracknbr;
   dta['salesorder'] = byId('insertElementals').dataset.salesorder;
   var passeddata = JSON.stringify( dta );
   var mlURL = "/data-doers/qms-get-email-letter";
   universalAJAX("POST",mlURL,passeddata,answerGetQMSLetter,2);
}

function answerGetQMSLetter ( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     //SUCCESS 
     var dta = JSON.parse(rtnData['responseText']);
     insertAtCursor( byId('emailTextGoeshere') , dta['DATA'] );
   }  
}

function sendQMSEmail( whichdialog ) { 

  var dta = new Object(); 
  var emaillist = []; 
  var x = document.getElementsByClassName("sideemaildspitem"); 
  for ( var i = 0; i < x.length; i++ ) { 
    if ( x[i].dataset.selected === 'true' ) {
      if ( !emaillist.includes ( x[i].dataset.email ) ) {  
        emaillist.push ( x[i].dataset.email );
      }
    }
  }
  dta['emaillist'] = emaillist;
  dta['includeme'] =  byId('incME').checked;
  dta['includechtn'] =  byId('incCHTN').checked;
  dta['includepr'] = byId('incPR').checked;
  dta['emailtext'] = byId('emailTextGoeshere').value.trim();
  dta['bgs'] = byId('insertElementals').dataset.bgs;
  dta['shipdocrefid'] = byId('insertElementals').dataset.shipdocrefid;
  dta['shippeddate'] = byId('insertElementals').dataset.shippeddate;
  dta['prepmethod'] = byId('insertElementals').dataset.prepmethod;
  dta['preparation'] = byId('insertElementals').dataset.preparation;
  dta['dxspecimencategory'] = byId('insertElementals').dataset.dxspecimencategory;
  dta['dxsite'] = byId('insertElementals').dataset.dxsite;
  dta['dxssite'] = byId('insertElementals').dataset.dxssite;
  dta['dxdx'] = byId('insertElementals').dataset.dxdx;
  dta['dxmod'] = byId('insertElementals').dataset.dxmod;
  dta['dxmetsfrom'] = byId('insertElementals').dataset.dxsmetsfrom;
  dta['designation'] = byId('insertElementals').dataset.designation;
  dta['courier'] = byId('insertElementals').dataset.courier;
  dta['tracknbr'] = byId('insertElementals').dataset.tracknbr;
  dta['salesorder'] = byId('insertElementals').dataset.salesorder;
  dta['dialogid'] = whichdialog;
  var passeddata = JSON.stringify( dta );
  var mlURL = "/data-doers/qms-send-email-letter";
  universalAJAX("POST",mlURL,passeddata,answerSendQMSLetter,2);

}

function answerSendQMSLetter ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     //SUCCESS 
     var dta = JSON.parse(rtnData['responseText']);
     alert('Email Sent');
     closeThisDialog(dta['DATA']['dialogid']);
   }  

}
  
function makeFurtherActionRequest () {
  var obj = new Object(); 
  obj['rqstPayload'] = byId('faFldRequestJSON').value.trim(); 
  obj['bioReference'] = byId('faFldReference').value.trim();
  obj['actionsValue'] = byId('faFldActionsValue').value.trim(); 
  obj['actionNote'] = byId('faFldNotes').value.trim(); 
  obj['agent'] = byId('faFldAssAgentValue').value.trim();
  obj['priority'] = byId('faFldPriorityValue').value.trim(); 
  obj['duedate'] = byId('faFldByDate').value.trim();
  obj['notifycomplete'] = byId('faFldNotifyComplete').checked;
  var passdta = JSON.stringify(obj);
  var mlURL = "/data-doers/save-further-action";
  universalAJAX("POST",mlURL,passdta,answerFurtherActionRequests,2);
}

function answerFurtherActionRequests( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
     byId('faFldActionsValue').value = "";
     byId('faFldActions').value = "";
     byId('faFldNotes').value = ""; 
     byId('faFldAssAgentValue').value = "";
     byId('faFldAssAgent').value = "";
     byId('faFldPriorityValue').value = "";
     byId('faFldPriority').value = "";
     byId('faFldByDate').value = "";
     byId('faFldNotifyComplete').checked = false;
     alert('Saved'); 
     //BUILD GRID

     var dta = JSON.parse(rtnData['responseText']);
     bldFurtherActionGrid( dta['DATA']['pbiosample'] );
   }        
}

function bldFurtherActionGrid( pbiosample ) { 
   var obj = new Object(); 
   obj['bgref'] = pbiosample;  
   var passdta = JSON.stringify(obj); 
   var mlURL = "/data-doers/display-pbfatbl";
   universalAJAX("POST",mlURL,passdta,answerBldFurtherActionGrid,2);
}
   
function answerBldFurtherActionGrid ( rtnData ) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
      if ( byId('dspOtherFurtherActions') ) { 
        var dta = JSON.parse(rtnData['responseText']);
        byId('dspOtherFurtherActions').innerHTML = "";
        byId('dspOtherFurtherActions').innerHTML = dta['DATA'];
      }
   }        
}
   
 function deactivateFA( whichfaid ) { 
   var obj = new Object(); 
   obj['faid'] = parseInt(whichfaid);  
   var passdta = JSON.stringify(obj); 
   var mlURL = "/data-doers/deactivate-pbfa";
   universalAJAX("POST",mlURL,passdta,answerDeactivateFA,2);
 }
   
 function answerDeactivateFA ( rtnData ) { 
 if (parseInt(rtnData['responseCode']) !== 200) { 
      var msgs = JSON.parse(rtnData['responseText']);
      var dspMsg = ""; 
      msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
      });
      alert("FURTHER ACTION ERROR:\\n"+dspMsg);
   } else {
       var pb = JSON.parse(rtnData['responseText']);
       bldFurtherActionGrid( pb['DATA'] );
   }    
}
  
RTNTHIS;
return $rtnThis;
}

function inventorymanifest ( $rqststr ) { 

  $tt = treeTop;
  $regUsr = session_id();  
    
$rtnThis = <<<JAVASCR

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


document.addEventListener('DOMContentLoaded', function() {   
  if ( byId('btnRefresh') ) {
    byId('btnRefresh').addEventListener('click', function() { 
      requestOnOfferListing();
    }, false);
  }
}, false);    


function requestOnOfferListing() { 
  byId('standardModalBacker').style.display = 'block'; 
  var obj = new Object(); 
  obj['institution'] = byId('presentInstValue').value; 
  obj['startdate'] = byId('bsqueryFromDate').value; 
  obj['enddate'] = byId('bsqueryToDate').value; 
  var passdata = JSON.stringify ( obj ); 
  var mlURL = "/data-doers/build-manifest-request-list";
  universalAJAX("POST",mlURL,passdata,answerBuildManifestRequestList,1);
}

function answerBuildManifestRequestList ( rtnData ) { 
  var tt = '{$tt}';
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse( rtnData['responseText'] ); 
     location.href = tt+'/inventory-manifest/'+dta['DATA']['searchid'];       

   }       
}

function selectOfferRecord ( whichOfferId ) {  
  if (parseInt(byId(whichOfferId).dataset.selected) === 1) {
    byId(whichOfferId).dataset.selected = '0';
  } else { 
    byId(whichOfferId).dataset.selected = '1';
  }     
}

function getNewManifest() { 
  byId('fldManifestNbrDsp').value = ""; 
  byId('manifestMetrics').innerHTML = "";
  byId('manifestDetailHolder').innerHTML = "";
  byId('standardModalBacker').style.display = 'block'; 
  var obj = new Object(); 
  obj['zxsdc'] = "ZZ"; 
  var passdata = JSON.stringify ( obj ); 
  var mlURL = "/data-doers/build-manifest-new";
  universalAJAX("POST",mlURL,passdata,answerBuildManifestNew,2);
}

function answerBuildManifestNew ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none'; 
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     //BLANK ANY DIVS SHOWING SEGMENTS
     byId('fldManifestNbrDsp').value = dta['DATA']['manifest']; 
     byId('manifestMetrics').innerHTML = dta['DATA']['user'] + " (" + dta['DATA']['when'] + ")"; 
     byId('standardModalBacker').style.display = 'none'; 
   }
}

function selectAllRecords() { 
  var x = document.getElementsByClassName("offerRecord");
  for ( var i = 0; i < x.length ; i++ ) { 
    selectOfferRecord ( x[i].id );  
  }
}

function addRecordToManifest() { 
  byId('standardModalBacker').style.display = 'block'; 
  var itm = [];
  var x = document.getElementsByClassName("offerRecord");
  for ( var i = 0; i < x.length ; i++ ) { 
    if ( parseInt(byId( x[i].id ).dataset.selected) === 1 )  { 
      itm.push ( byId( x[i].id ).dataset.sglabel );
    } 
  } 
  var obj = new Object(); 
  obj['itmlst'] =  itm;
  obj['manifest'] = byId('fldManifestNbrDsp').value;
  var passdata = JSON.stringify ( obj ); 
  var mlURL = "/data-doers/add-to-manifest";
  universalAJAX("POST",mlURL,passdata,answerAddToManifest,2);
}

function answerAddToManifest ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none'; 
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     //BLANK ANY DIVS SHOWING SEGMENTS
     alert('Saved!');
     //Update Manifest Segment Display
     fillManifestSide();
     byId('standardModalBacker').style.display = 'none'; 
   }
}

function fillManifestSide() { 
  if ( byId('fldManifestNbrDsp') ) {   
    byId('standardModalBacker').style.display = 'block'; 
    byId('manifestDetailHolder').innerHTML = "";
    var obj = new Object(); 
    obj['manifest'] = byId('fldManifestNbrDsp').value;
    var passdata = JSON.stringify ( obj ); 
    var mlURL = "/data-doers/display-manifest-details";
    universalAJAX("POST",mlURL,passdata,answerFillManifestSide,2);
  }
}

function answerFillManifestSide ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none'; 
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     var displayDetails = "<div id=detailHeader><div class=dHead>&nbsp;</div><div class=dHead>CHTN #</div><div class=dHead>Preparation</div><div class=dHead>Designation</div></div>";
     for ( var i = 0; i < dta['DATA'].length; i++ ) { 
       displayDetails += "<div id=\"dtl"+dta['DATA'][i]['bgs']+"\" class=manDtlRecord><div class=delIco onclick=\"removeSegFromManifest('"+dta['DATA'][i]['bgs']+"','"+dta['DATA'][i]['manifestnbr']+"');\">&times;</div><div class=manDetBGS>"+dta['DATA'][i]['bgs']+"</div><div class=manDetPrep>"+dta['DATA'][i]['preparation']+"</div><div class=manDetDesig>"+dta['DATA'][i]['dxdesignation']+"</div>   </div>";
     }
     byId('manifestDetailHolder').innerHTML = displayDetails;
     refreshOOGrid();
     byId('standardModalBacker').style.display = 'none'; 
   }
}

function removeSegFromManifest ( bgs, manifest ) { 
 byId('standardModalBacker').style.display = 'block'; 
 var obj = new Object(); 
 obj['manifest'] = manifest;
 obj['bgs'] = bgs;
 var passdata = JSON.stringify ( obj ); 
 var mlURL = "/data-doers/remove-manifest-details";
 universalAJAX("POST",mlURL,passdata,answerRemoveManifestDetails,2);
}

function answerRemoveManifestDetails ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none'; 
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     //Update Manifest Segment Display
     fillManifestSide();
     byId('standardModalBacker').style.display = 'none'; 
   }
}

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}

function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function refreshOOGrid () { 
  var p = window.location.pathname.split('/'); 
  if ( p[2].trim() !== "" && byId('offerElements') ) { 
    byId('standardModalBacker').style.display = 'block'; 
    byId('offerElements').innerHTML = "&nbsp;";
    var obj = new Object(); 
    obj['manifestqry'] = p[2];
    var passdata = JSON.stringify ( obj ); 
    var mlURL = "/data-doers/manifest-offer-details-refresh";
    universalAJAX("POST",mlURL,passdata,answerManifestOfferDetails,2);
  }
}

function answerManifestOfferDetails ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none'; 
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     byId('offerElements').innerHTML = dta['DATA'];
     byId('standardModalBacker').style.display = 'none'; 
   }

}

function sendToGetThisManifest ( ) { 
  getThisManifest( byId('fldQryManifestNbr').value ); 
}

function getThisManifest ( thismanifest ) { 
  var dta = new Object(); 
  dta['qrymanifest'] = thismanifest;
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/qry-this-manifest";
  universalAJAX("POST",mlURL,passdta,answerThisQryManifest,2);
}

function answerThisQryManifest ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     byId('fldManifestNbrDsp').value = dta['DATA'][0]['manifestnbr'];    
     byId('manifestMetrics').innerHTML = dta['DATA'][0]['whoby'] + " ("+dta['DATA'][0]['manifestdate'] + ")"; 
     if ( byId('manifestdialogid') ) { 
       fillManifestSide();
       closeThisDialog( byId('manifestdialogid').value );
     }
   }

}

function markManifestSend() { 

  if ( byId('fldManifestNbrDsp').value.trim() !== "" ) { 
    var dta = new Object(); 
    dta['qrymanifest'] = byId('fldManifestNbrDsp').value.trim();
    var passdta = JSON.stringify(dta);
    var mlURL = "/data-doers/send-this-manifest";
    universalAJAX("POST",mlURL,passdta,answerSendManifest,2);
  } else { 
    alert('No Manifest Specified');
  }

}

function answerSendManifest ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     alert('Manifest has been sent!'); 
   }

}

function printThisManifest( thismanifest = "" ) { 
  var dta = new Object(); 
  if ( thismanifest === "" ) {
    if ( byId('fldManifestNbrDsp').value.trim() !== "" ) { 
      dta['qrymanifest'] = byId('fldManifestNbrDsp').value.trim();
    } else { 
      alert('No Manifest Specified');
      return null;
    }
  } else { 
    dta['qrymanifest'] = thismanifest;
  }
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/encrypt-this-manifest";
  universalAJAX("POST",mlURL,passdta,answerEncryptManifest,2);
}

function answerEncryptManifest ( rtnData ) { 

  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var dta = JSON.parse( rtnData['responseText'] );
     openOutSidePage("{$tt}/print-obj/inventory-manifest/"+dta['DATA']);
   }

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
  dta['procedureDate'] = byId('fldProcedureDate').value.trim();       
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
    if (parseInt(byId('requestsasked').value) === 0) {        
      var given = new Object(); 
      given['rqstsuggestion'] = 'vandyinvest-requests'; 
      given['given'] = byId('fldSEGselectorAssignInv').value.trim();
      var passeddata = JSON.stringify(given);
      var mlURL = "/data-doers/suggest-something";
      universalAJAX("POST",mlURL,passeddata,answerRequestDrop,2);
    }
  } 
}

function answerRequestDrop(rtnData) {
  var rsltTbl = "";
  if (parseInt(rtnData['responseCode']) === 200 ) { 
    var dta = JSON.parse(rtnData['responseText']);
    var menuTbl = "<table border=0 class=\"menuDropTbl\">";
    dta['DATA'].forEach(function(element) { 
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('fldSEGselectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); \">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
    byId('requestsasked').value = 1;
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

function reportListDisplay( id ) {
  if ( byId('RL'+id).style.display === 'none' ) { 
    byId('RL'+id).style.display = 'block';
  } else { 
    byId('RL'+id).style.display = 'none';
  } 
}

function markFavorite( reporturl ) { 
  var dta = new Object(); 
  dta['reporturl'] = reporturl;
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/mark-report-favorite";
  universalAJAX("POST",mlURL,passdata,answerMarkReportFavorite,1);
}

function answerMarkReportFavorite( rtnData ) { 
   if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("Favorite Object Error:\\n"+dspMsg);  //DSIPLAY ERROR MESSAGE
   } else {  
     var prts = JSON.parse(rtnData['responseText']); 
     alert('Report marked to your favorites'); 
     location.reload(true);
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

  if ( byId('dspScanType') ) {
    byId('dspScanType').focus();
  }

  document.addEventListener('keypress', function(event) { 
    if (event.which === 13) { 
      sendHPRReviewRequest();
    }  
  }, false);

}, false);

function sendHPRReviewRequest() { 
  if (byId('dspScanType')) { 
      var dta = new Object();
      dta['doctype'] = 'HPRWorkBenchRequest';
      dta['srchterm'] = byId('dspScanType').value.trim();    
      var passdata = JSON.stringify(dta);
      var mlURL = "/data-doers/doc-search";
      universalAJAX("POST",mlURL,passdata,answerSendHPRReviewRequest,1);
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

  if (byId('btnHPRReviewSave')) { 
    byId('btnHPRReviewSave').addEventListener('click', function() {
      gatherAndSaveReview();
    }, false);
  }

  if (byId('btnHPRReviewNotFit')) { 
    byId('btnHPRReviewNotFit').addEventListener('click', function() {
      gatherAndUnuseReview();
    }, false);
  }

}, false);

function returnTrayAction ( dialogid ) { 

if ( byId('fldRtnLocationValue') ) { 
  var dta = new Object();
  dta['rtnTrayId'] = byId('rtnTryId').value;
  dta['dialogid'] = dialogid;
  dta['locationscancode'] = byId('fldRtnLocationValue').value;
  dta['returnlocationnote'] = byId('rtnlocationnote').value;
 
  if ( byId('fldRtnNonFinishReasonValue') ) { 
    dta['notfinishedreason'] = byId('fldRtnNonFinishReasonValue').value; 
    dta['notfinishednote'] = byId('rtnnonfinishednote').value;
  }

  var passdta = JSON.stringify(dta);
  closeThisDialog( dialogid ); 
  byId('standardModalBacker').style.display = 'block';    
  var mlURL = "/data-doers/hpr-return-slide-tray";
  universalAJAX("POST",mlURL,passdta,answerHPRReturnTray,2);
}

}

function answerHPRReturnTray( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
    navigateSite('hpr-review');
   }
}
           
function sendSaveUnusable( dialogid ) {
  var dta = JSON.parse( byId('valSavedData').value );
  dta['unusabletxt'] = byId('ususableReasonTxt').value;
  var passdta = JSON.stringify(dta);
  //console.log ( passdta );
  closeThisDialog( dialogid ); 
  byId('standardModalBacker').style.display = 'block';    
  var mlURL = "/data-doers/hpr-save-review";
  universalAJAX("POST",mlURL,passdta,answerHPRSaveReview,2);
}

function gatherAndInconReview( dialogid ) {
  var dta = new Object();
  if (byId('fldOnBehalfValue')) { 
    dta['onbehalf'] = ( byId('fldOnBehalfValue').value.trim() === "" ) ? 0 : byId('fldOnBehalfValue').value;  
  } else {
    dta['onbehalf'] = 0;  
  } 
  dta['segid'] =  byId('inconSegId').value;
  dta['inconreason'] = byId('reasonInconclusiveTxt').value;
  dta['inconfurtheractions'] = byId('hprDGFAJsonHolder').value;
  dta['dialogid'] = dialogid; 
  closeThisDialog( dialogid ); 
  byId('standardModalBacker').style.display = 'block';    
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/hpr-save-incon-review";
  universalAJAX("POST",mlURL,passdta,answerHPRSaveReview,2);
}


function gatherAndUnuseReview() { 
  var dta = new Object();
  if (byId('fldOnBehalfValue')) { 
    dta['onbehalf'] = ( byId('fldOnBehalfValue').value.trim() === "" ) ? 0 : byId('fldOnBehalfValue').value;  
  } else {
    dta['onbehalf'] = 0;  
  } 
  dta['segid'] =  byId('HPRWBTbl').dataset.segid;
  dta['decision'] = byId('decisionSqr').dataset.hprdecision;
  dta['specimencategory'] = byId('HPRWBTbl').dataset.specimencategory;
  dta['site'] = byId('HPRWBTbl').dataset.site;
  dta['ssite'] = byId('HPRWBTbl').dataset.ssite;
  dta['diagnosis'] = byId('HPRWBTbl').dataset.dx;
  dta['diagnosismodifier'] = byId('HPRWBTbl').dataset.dxm;
  dta['metsfrom'] = byId('HPRWBTbl').dataset.mets;
  dta['systemic'] = byId('HPRWBTbl').dataset.sysm;
  dta['uninvolved'] = byId('fldPRCUnInvolvedValue').value;
  dta['tumorgrade'] = byId('fldTumorGrade').value;
  dta['tumorgradescale'] = byId('fldTumorGradeScaleValue').value;
  dta['techaccuracy'] = byId('fldTechAccValue').value;
  var prcflds = document.getElementsByClassName("prcFld");
  var prcdta = new Object();
  for ( var i = 0; i < prcflds.length; i++ ) {
   prcdta['prc_'+prcflds[i].id.toLowerCase()] = prcflds[i].value; 
  }
  dta['complexion'] = prcdta;
  //dta['hprfurtheraction'] = byId('hprFAJsonHolder').value;
  dta['hprmoleculartests'] = byId('hprMolecularTestJsonHolderConfirm').value;
  dta['rarereason'] = byId('fldRareReasonTxt').value;
  dta['generalcomments'] = byId('fldGeneralCmtsTxt').value;
  dta['specialinstructions'] = byId('fldSpecialInstructions').value;
  dta['reviewassignind'] = byId('btnHPRReviewAssign').dataset.hselected;          
  var passdta = JSON.stringify(dta);
  generateDialog('hprUnusuableDialog', passdta );
}

function gatherAndSaveReview() { 
  var dta = new Object();
  if (byId('fldOnBehalfValue')) { 
    dta['onbehalf'] = ( byId('fldOnBehalfValue').value.trim() === "" ) ? 0 : byId('fldOnBehalfValue').value;  
  } else {
    dta['onbehalf'] = 0;  
  } 
  dta['segid'] =  byId('HPRWBTbl').dataset.segid;
  dta['decision'] = byId('decisionSqr').dataset.hprdecision;
  dta['specimencategory'] = byId('HPRWBTbl').dataset.specimencategory;
  dta['site'] = byId('HPRWBTbl').dataset.site;
  dta['ssite'] = byId('HPRWBTbl').dataset.ssite;
  dta['diagnosis'] = byId('HPRWBTbl').dataset.dx;
  dta['diagnosismodifier'] = byId('HPRWBTbl').dataset.dxm;
  dta['metsfrom'] = byId('HPRWBTbl').dataset.mets;
  dta['systemic'] = byId('HPRWBTbl').dataset.sysm;
  dta['uninvolved'] = byId('fldPRCUnInvolvedValue').value;
  dta['tumorgrade'] = byId('fldTumorGrade').value;
  dta['tumorgradescale'] = byId('fldTumorGradeScaleValue').value;
  dta['techaccuracy'] = byId('fldTechAccValue').value;          
  var prcflds = document.getElementsByClassName("prcFld");
  var prcdta = new Object();
  for ( var i = 0; i < prcflds.length; i++ ) {
   prcdta['prc_'+prcflds[i].id.toLowerCase()] = prcflds[i].value; 
  }
  dta['complexion'] = prcdta;
  //dta['hprfurtheraction'] = byId('hprFAJsonHolder').value;
  dta['hprmoleculartests'] = byId('hprMolecularTestJsonHolderConfirm').value;
  dta['rarereason'] = byId('fldRareReasonTxt').value;
  dta['generalcomments'] = byId('fldGeneralCmtsTxt').value;
  dta['specialinstructions'] = byId('fldSpecialInstructions').value;
  dta['reviewassignind'] = byId('btnHPRReviewAssign').dataset.hselected;          
            
  byId('standardModalBacker').style.display = 'block';    
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/hpr-save-review";
  universalAJAX("POST",mlURL,passdta,answerHPRSaveReview,2);
}

function answerHPRSaveReview( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
    navigateSite('hpr-review/'+byId('backToTrayURL').value);
   }
}

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
            
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}        

function recipSelector(whichrecip) {
  if ( byId(whichrecip).dataset.selected === 'false' ) { 
    byId(whichrecip).dataset.selected = 'true';
  } else { 
    byId(whichrecip).dataset.selected = 'false';
  }
}   

function sendHPREmail() {
  var reciplist = new Array();
  var cntr = 0; 
  var dta = new Object(); 
  var x = document.getElementsByClassName("recipitemlisting");
   for ( var i = 0; i < x.length; i++ ) {
     if ( x[i].dataset.selected === 'true' ) {
       reciplist[cntr] = x[i].id;
       cntr++;
     }
   }
   dta['recipientlist'] = JSON.stringify ( reciplist );
   dta['messagetext'] = byId('hprEmlMsg').value.trim();
   dta['dialogid'] = byId('identDialogid').value;
   var passdta = JSON.stringify(dta);
   var mlURL = "/data-doers/hpr-send-email";
   universalAJAX("POST",mlURL,passdta,answerHPRSendEmail,2);
}

function answerHPRSendEmail( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     alert('Message Sent'); 
     var diddta = JSON.parse( rtnData['responseText'] ); 
     closeThisDialog( diddta['DATA'] );
   }
}

function changeSupportingTab(whichtab) { 
  var divs = document.getElementsByClassName('HPRReviewDocument'); 
  for ( var i = 0; i < divs.length; i++ ) { 
    byId('dspTabContent'+i).style.display = 'none';
  }
  byId('dspTabContent'+whichtab).style.display = 'block';
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

var masteridsuffix = "";
function triggerMolecularFill(menuid, menuval, valuedsp, idsuffix) { 
   byId('hprFldMoleResult'+idsuffix).value = "";
   byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   byId('hprFldMoleScale'+idsuffix).value = "";
   masteridsuffix = idsuffix;
   if (menuid === 0) { 
     //CLEAR FIELD
       byId('hprFldMoleTest'+idsuffix+'Value').value = "";
       byId('hprFldMoleTest'+idsuffix).value = "";     
       byId('hprFldMoleResult'+idsuffix).value = "";
       byId('hprFldMoleResult'+idsuffix+'Value').value = "";    
   } else { 
       byId('hprFldMoleTest'+idsuffix+'Value').value = menuval;
       byId('hprFldMoleTest'+idsuffix).value = valuedsp;    
       var mlURL = "/immuno-mole-result-list/" + menuid;
       universalAJAX("GET",mlURL,'',answerHPRTriggerMolecularFill,2);            
   }
}
            
function answerHPRTriggerMolecularFill(rtnData) {
   var resultTbl = "";         
   if (parseInt(rtnData['responseCode']) !== 200) {             
   } else {    
     var dta = JSON.parse(rtnData['responseText']);
     var resultTbl = "<table border=0 class=\"menuDropTbl hprNewDropDownFont\">";
     resultTbl += "<tr><td onclick=\"fillField('hprFldMoleResult','','');\" align=right class=ddMenuClearOption>[clear]</td></tr>";            
     dta['DATA'].forEach(function(element) { 
       //element['menuvalue']      
       resultTbl += "<tr><td class=ddMenuItem onclick=\"fillField('hprFldMoleResult"+masteridsuffix+"','"+element['menuvalue']+"','"+element['dspvalue']+"');\">"+element['dspvalue']+"</td></tr>";
     });  
     resultTbl += "</table>";    
     masteridsuffix = "";
   }
   if (byId('moleResultDropDown')) { 
     byId('moleResultDropDown').innerHTML = resultTbl;         
   }
}

function manageDGFurtherActions( addIndicator, referencenumber ) { 
if ( byId('hprDGFAJsonHolder') ) {
   if ( addIndicator === 1 ) { 
     //ADD FURTHER ACTION 
     if ( byId('fldDGFurtherAction').value.trim() === "" ) { 
       alert('You haven\'t choosen a \'further action\'');       
     } else { 
       if ( byId('hprDGFAJsonHolder').value === "" ) { 
         var hldVal = [];    
       } else { 
         var hldVal = JSON.parse( byId('hprDGFAJsonHolder').value  );      
      }
      hldVal.push(  [ byId('fldDGFurtherActionValue').value,  byId('fldDGFurtherAction').value, byId('fldDGFANote').value.trim()   ] );             
      byId('hprDGFAJsonHolder').value = JSON.stringify(hldVal);      
      byId('fldDGFurtherActionValue').value = "";
      byId('fldDGFurtherAction').value = "";
      byId('fldDGFANote').value = "";    
     }       
   }
   if ( addIndicator === 0 ) { 
     //DELETE FURTHER ACTION 
     var hldVal = JSON.parse(byId('hprDGFAJsonHolder').value); 
     var newVal = [];
     var key = 0;   
     hldVal.forEach(function(ele) { 
       if (key !== referencenumber) {
         newVal.push(ele);    
       }
       key++;
    });
    hldVal = newVal;
    byId('hprDGFAJsonHolder').value = JSON.stringify(hldVal);              
   }
    var faTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
    var cntr = 0;         
    hldVal.forEach(function(element) {   
      faTestTbl += "<tr class=ddMenuItem onclick=\"manageDGFurtherActions(0,"+cntr+");\" ><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 0;\">cancel</i><td style=\"width: 15vw; padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1);\">"+element[1]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\">"+element[2]+"</td></tr>";
      cntr++;
    });   
    faTestTbl += "</table>"; 
   byId('dgfurtheractiondsplisting').innerHTML = faTestTbl;
}
}            
                       
function manageFurtherActions( addIndicator, referencenumber ) { 
if ( byId('hprFAJsonHolder') ) {
   if ( addIndicator === 1 ) { 
     //ADD FURTHER ACTION 
     if ( byId('fldFurtherAction').value.trim() === "" ) { 
       alert('You haven\'t choosen a \'further action\'');       
     } else { 
       if ( byId('hprFAJsonHolder').value === "" ) { 
         var hldVal = [];    
       } else { 
         var hldVal = JSON.parse( byId('hprFAJsonHolder').value  );      
      }
      hldVal.push(  [ byId('fldFurtherActionValue').value,  byId('fldFurtherAction').value, byId('fldFANote').value.trim()   ] );             
      byId('hprFAJsonHolder').value = JSON.stringify(hldVal);      
      byId('fldFurtherActionValue').value = "";
      byId('fldFurtherAction').value = "";
      byId('fldFANote').value = "";    
     }       
   }
   if ( addIndicator === 0 ) { 
     //DELETE FURTHER ACTION 
     var hldVal = JSON.parse(byId('hprFAJsonHolder').value); 
     var newVal = [];
     var key = 0;   
     hldVal.forEach(function(ele) { 
       if (key !== referencenumber) {
         newVal.push(ele);    
       }
       key++;
    });
    hldVal = newVal;
    byId('hprFAJsonHolder').value = JSON.stringify(hldVal);              
   }
    var faTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
    var cntr = 0;         
    hldVal.forEach(function(element) {   
      faTestTbl += "<tr class=ddMenuItem onclick=\"manageFurtherActions(0,"+cntr+");\" ><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 0;\">cancel</i><td style=\"width: 15vw; padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1);\">"+element[1]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1);\">"+element[2]+"</td></tr>";
      cntr++;
    });   
    faTestTbl += "</table>"; 
   byId('furtheractiondsplisting').innerHTML = faTestTbl;
}
}
            
function manageMoleTest(addIndicator, referencenumber, fldsuffix) { 
  if (byId('hprMolecularTestJsonHolderConfirm')) { 
   if (byId('hprMolecularTestJsonHolderConfirm').value === "") { 
     if (addIndicator === 1) { 

        if ( byId('hprFldMoleTest'+fldsuffix+'Value').value.trim() === "" ) { 
          alert('YOU HAVE NOT SELECTED A MOLECULAR TEST.');
          return null;
        }

        var hldVal = [];
        hldVal.push(  [ byId('hprFldMoleTest'+fldsuffix+'Value').value,  byId('hprFldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);
      }
    } else { 
      if (addIndicator === 1) {

        if ( byId('hprFldMoleTest'+fldsuffix+'Value').value.trim() === "" ) { 
          alert('YOU HAVE NOT SELECTED A MOLECULAR TEST.');
          return null;
        }

        var hldVal = JSON.parse(byId('hprMolecularTestJsonHolderConfirm').value);
        hldVal.push(  [ byId('hprFldMoleTest'+fldsuffix+'Value').value,  byId('hprFldMoleTest'+fldsuffix).value, byId('hprFldMoleResult'+fldsuffix+'Value').value, byId('hprFldMoleResult'+fldsuffix).value, byId('hprFldMoleScale'+fldsuffix).value.trim()      ] );    
        byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);

      }
      if (addIndicator === 0) { 
         var hldVal = JSON.parse(byId('hprMolecularTestJsonHolderConfirm').value);             
         var newVal = [];
         var key = 0;   
         hldVal.forEach(function(ele) { 
            if (key !== referencenumber) {
              newVal.push(ele);    
            }
            key++;
         });
         hldVal = newVal;
         byId('hprMolecularTestJsonHolderConfirm').value = JSON.stringify(hldVal);   
      }      
    }
    byId('hprFldMoleTest'+fldsuffix+'Value').value = "";
    byId('hprFldMoleTest'+fldsuffix).value = "";
    byId('hprFldMoleResult'+fldsuffix+'Value').value = "";
    byId('hprFldMoleResult'+fldsuffix).value = "";
    byId('hprFldMoleScale'+fldsuffix).value = "";            
    var moleTestTbl = "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
    var cntr = 0;         
    hldVal.forEach(function(element) {         
      moleTestTbl += "<tr onclick=\"manageMoleTest(0,"+cntr+",'"+fldsuffix+"');\" class=ddMenuItem><td style=\"border-bottom: 1px solid rgba(160,160,160,1); width: 1vw;\"><i class=\"material-icons\" style=\"font-size: 1.8vh; color:rgba(237, 35, 0,1); width: .3vw; padding: 8px 0 8px 0;\">cancel</i><td style=\" padding: 8px 0 8px 8px;border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[1]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[3]+"</td><td style=\"border-bottom: 1px solid rgba(160,160,160,1); font-size: 1.5vh;\">"+element[4]+"</td></tr>";
      cntr++;
     });
     moleTestTbl += "</table>";
     byId('dspDefinedMolecularTestsConfirm'+fldsuffix).innerHTML = moleTestTbl;               
  }
}

function loadDXOverride() { 
   generateDialog( 'hprDXOverride', 'xx' );
}

function loadDesignation() {
   generateDialog( 'hprDesignationSpecifier', 'xx' );
}
            
function loadInconclusive(objid) { 
    generateDialog('hprInconclusiveDialog',objid);
}

function loadMETSBrowser() { 
  if ( byId('HPRWBTbl').dataset.specimencategory.toUpperCase().trim() !== "MALIGNANT") { 
    alert('Metastatic sites can only be specified for malignant biosamples');
  } else { 
    generateDialog( 'hprMetastaticSiteBrowser', 'xx');
  }
}

function loadSystemicBrowser() {
   generateDialog( 'hprSystemicListBrowser', 'xx' );
}

function browseHPRDxOverride ( srchvalue, dialogid ) { 
  if ( srchvalue.length > 3 ) { 
    var obj = new Object();
    obj['srchterm'] = srchvalue;
    obj['dialogid'] = dialogid;
    var passdta = JSON.stringify(obj);
    var mlURL = "/data-doers/hpr-vocab-browser-dx-override";
    universalAJAX("POST",mlURL,passdta,answerHPRVocabBrowserDXOverride,2);
  } 
  if ( srchvalue.length < 4 ) { 
    byId('vocabBrowserDsp').innerHTML = "";
  }
}

function answerHPRVocabBrowserDXOverride( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var voctbl = JSON.parse( rtnData['responseText'] );
     byId('vocabBrowserDsp').innerHTML = voctbl['DATA'];  
   }
}

function browseHPRVocabulary ( srchvalue, includesubind, dialogid ) {
  if ( srchvalue.length > 3 ) { 
    var obj = new Object();
    obj['srchterm'] = srchvalue;
    obj['includess'] = includesubind;
    obj['dialogid'] = dialogid;
    var passdta = JSON.stringify(obj);
    var mlURL = "/data-doers/hpr-vocab-browser";
    universalAJAX("POST",mlURL,passdta,answerHPRVocabBrowser,2);
  } 
  if ( srchvalue.length < 4 ) { 
    byId('vocabBrowserDsp').innerHTML = "";
  }
} 

function answerHPRVocabBrowser ( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     var voctbl = JSON.parse( rtnData['responseText'] );
     byId('vocabBrowserDsp').innerHTML = voctbl['DATA'];  
   }
}

function makeHPRDXOverride ( dialogid, diagnosis, dmodifier ) { 
  var decision = 'CONFIRM';
  decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() === "" && byId('HPRWBTbl').dataset.specimencategory.trim() !== "" ) ? "ADDITIONAL" : decision; 
  decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && byId('HPRWBTbl').dataset.specimencategory.trim() === "" ) ? "DENIED" : decision; 
  decision = ( (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && byId('HPRWBTbl').dataset.specimencategory.trim() !== "") && (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== byId('HPRWBTbl').dataset.specimencategory.trim())) ? "DENIED" : decision;
  if ( decision !== "DENIED") { 
    //CHECK SITE  
    decision = ( byId('HPRWBTbl').dataset.osite.trim() === "" && byId('HPRWBTbl').dataset.site.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.osite.trim() !== "" && byId('HPRWBTbl').dataset.site.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.osite.trim() !== "" && byId('HPRWBTbl').dataset.site.trim() !== "") && (byId('HPRWBTbl').dataset.osite.trim() !== byId('HPRWBTbl').dataset.site.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK SUB-SITE  
    decision = ( byId('HPRWBTbl').dataset.ossite.trim() === "" && byId('HPRWBTbl').dataset.ssite.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.ossite.trim() !== "" && byId('HPRWBTbl').dataset.ssite.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.ossite.trim() !== "" && byId('HPRWBTbl').dataset.ssite.trim() !== "") && (byId('HPRWBTbl').dataset.ossite.trim() !== byId('HPRWBTbl').dataset.ssite.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK DX 
    decision = ( byId('HPRWBTbl').dataset.odx.trim() === "" && diagnosis.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() !== "") && (byId('HPRWBTbl').dataset.odx.trim() !== diagnosis.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK DXM 
    decision = ( byId('HPRWBTbl').dataset.odxm.trim() === "" && dmodifier.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() !== "") && (byId('HPRWBTbl').dataset.odxm.trim() !== dmodifier.trim())) ? "DENIED" : decision; 
  }
  byId('decisionSqr').dataset.hprdecision = decision;

  switch ( decision ) { 
    case 'CONFIRM':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionconfirm\">thumb_up</i>";
     break;
     case 'ADDITIONAL':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionadd\">note_add</i>";
     break;
     case 'DENIED':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisiondenied\">thumb_down</i>";
     break;
  }

  byId('HPRWBTbl').dataset.dx = diagnosis.trim(); 
  byId('HPRWBTbl').dataset.dxm = dmodifier.trim(); 
  var dspDesig = byId('HPRWBTbl').dataset.specimencategory.trim();
  dspDesig += " " + byId('HPRWBTbl').dataset.site.trim(); 
  dspDesig += ( byId('HPRWBTbl').dataset.ssite.trim() !== "" ) ? " (" + byId('HPRWBTbl').dataset.ssite.trim() + ")" : "";
  dspDesig += ( diagnosis.trim() !== "" ) ? " / " + diagnosis.trim() : "";
  dspDesig += ( dmodifier.trim() !== "" ) ? " [" + dmodifier.trim() + "]" : "";
  byId('dspHPRDesignation').innerHTML = dspDesig.trim();
  closeThisDialog ( dialogid );
}

function makeHPRDesignation( dialogid, spcat, site, subsite, diagnosis, dmodifier ) { 
  var decision = 'CONFIRM';
  decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() === "" && spcat.trim() !== "" ) ? "ADDITIONAL" : decision; 
  decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && spcat.trim() === "" ) ? "DENIED" : decision; 
  decision = ( (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && spcat.trim() !== "") && (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== spcat.trim())) ? "DENIED" : decision; 
  if ( spcat.toUpperCase().trim() !== "MALIGNANT" ) {
    //MAKE SURE METASTATIC IS NOT FILLED IN
    if ( byId('HPRWBTbl').dataset.omets.trim() === "" ) {
      byId('HPRWBTbl').dataset.mets = "";
      byId('dspHPRMetsFrom').innerHTML = ""; 
    } else { 
      byId('HPRWBTbl').dataset.mets = "";
      byId('dspHPRMetsFrom').innerHTML = ""; 
      decision = "DENIED";
    }
  }
  if ( decision !== "DENIED") { 
    //CHECK SITE  
    decision = ( byId('HPRWBTbl').dataset.osite.trim() === "" && site.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.osite.trim() !== "" && site.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.osite.trim() !== "" && site.trim() !== "") && (byId('HPRWBTbl').dataset.osite.trim() !== site.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK SUB-SITE  
    decision = ( byId('HPRWBTbl').dataset.ossite.trim() === "" && subsite.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.ossite.trim() !== "" && subsite.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.ossite.trim() !== "" && subsite.trim() !== "") && (byId('HPRWBTbl').dataset.ossite.trim() !== subsite.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK DX 
    decision = ( byId('HPRWBTbl').dataset.odx.trim() === "" && diagnosis.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() !== "") && (byId('HPRWBTbl').dataset.odx.trim() !== diagnosis.trim())) ? "DENIED" : decision; 
  }
  if ( decision !== "DENIED") { 
    //CHECK DXM 
    decision = ( byId('HPRWBTbl').dataset.odxm.trim() === "" && dmodifier.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() !== "") && (byId('HPRWBTbl').dataset.odxm.trim() !== dmodifier.trim())) ? "DENIED" : decision; 
  }
  byId('decisionSqr').dataset.hprdecision = decision;

  switch ( decision ) { 
    case 'CONFIRM':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionconfirm\">thumb_up</i>";
     break;
     case 'ADDITIONAL':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionadd\">note_add</i>";
     break;
     case 'DENIED':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisiondenied\">thumb_down</i>";
     break;
  }

  byId('HPRWBTbl').dataset.specimencategory = spcat.trim(); 
  byId('HPRWBTbl').dataset.site = site.trim(); 
  byId('HPRWBTbl').dataset.ssite = subsite.trim(); 
  byId('HPRWBTbl').dataset.dx = diagnosis.trim(); 
  byId('HPRWBTbl').dataset.dxm = dmodifier.trim(); 
  var dspDesig = spcat.trim();
  dspDesig += " " + site.trim(); 
  dspDesig += ( subsite.trim() !== "" ) ? " (" + subsite.trim() + ")" : "";
  dspDesig += ( diagnosis.trim() !== "" ) ? " / " + diagnosis.trim() : "";
  dspDesig += ( dmodifier.trim() !== "" ) ? " [" + dmodifier.trim() + "]" : "";
  byId('dspHPRDesignation').innerHTML = dspDesig.trim();
  closeThisDialog ( dialogid );
}

function makeHPRMetsFrom( dialogid, metssite, metsssite ) { 
  var decision = byId('decisionSqr').dataset.hprdecision;
  if ( byId('decisionSqr').dataset.hprdecision === 'DENIED' ) {
  } else { 
    decision = ( byId('HPRWBTbl').dataset.omets.trim() === "" && metssite.trim() !== "" ) ? "ADDITIONAL" : decision; 
    decision = ( byId('HPRWBTbl').dataset.omets.trim() !== "" && metssite.trim() === "" ) ? "DENIED" : decision; 
    decision = ((byId('HPRWBTbl').dataset.omets.trim() !== "" && metssite.trim() !== "") && (byId('HPRWBTbl').dataset.omets.trim() !== metssite.trim())) ? "DENIED" : decision;  
  }
  byId('decisionSqr').dataset.hprdecision = decision;

  switch ( decision ) { 
    case 'CONFIRM':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionconfirm\">thumb_up</i>";
     break;
     case 'ADDITIONAL':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionadd\">note_add</i>";
     break;
     case 'DENIED':
       byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisiondenied\">thumb_down</i>";
     break;
  }
  byId('HPRWBTbl').dataset.mets = metssite.trim(); 
  byId('dspHPRMetsFrom').innerHTML = metssite.trim();
  closeThisDialog ( dialogid );
}

function makeHPRSystemic ( dialogid, systemdesig ) { 
  byId('HPRWBTbl').dataset.sysm = systemdesig;
  byId('dspHPRSystemic').innerHTML = systemdesig.trim();
  closeThisDialog ( dialogid );
}

function resetDesig() {
  byId('HPRWBTbl').dataset.specimencategory = byId('HPRWBTbl').dataset.ospecimencategory; 
  byId('HPRWBTbl').dataset.site = byId('HPRWBTbl').dataset.osite; 
  byId('HPRWBTbl').dataset.ssite = byId('HPRWBTbl').dataset.ossite; 
  byId('HPRWBTbl').dataset.dx = byId('HPRWBTbl').dataset.odx; 
  byId('HPRWBTbl').dataset.dxm = byId('HPRWBTbl').dataset.odxm; 
  var dspDesig = byId('HPRWBTbl').dataset.ospecimencategory;
  dspDesig += " " + byId('HPRWBTbl').dataset.osite; 
  dspDesig += ( byId('HPRWBTbl').dataset.ossite.trim() !== "" ) ? " (" + byId('HPRWBTbl').dataset.ossite.trim() + ")" : "";
  dspDesig += ( byId('HPRWBTbl').dataset.odx.trim() !== "" ) ? " / " + byId('HPRWBTbl').dataset.odx.trim() : "";
  dspDesig += ( byId('HPRWBTbl').dataset.odxm.trim() !== "" ) ? " [" + byId('HPRWBTbl').dataset.odxm.trim() + "]" : "";
  byId('dspHPRDesignation').innerHTML = dspDesig.trim(); 
  byId('HPRWBTbl').dataset.mets = byId('HPRWBTbl').dataset.omets;
  byId('dspHPRMetsFrom').innerHTML = byId('HPRWBTbl').dataset.mets;
  byId('HPRWBTbl').dataset.sysm = byId('HPRWBTbl').dataset.osysm;
  byId('dspHPRSystemic').innerHTML = byId('HPRWBTbl').dataset.sysm;
  byId('decisionSqr').dataset.hprdecision = "CONFIRM";
  byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionconfirm\">thumb_up</i>";
}

function blankDesig( whichdesig ) { 
  switch ( whichdesig ) { 
    case 'dspHPRMetsFrom':
      byId('HPRWBTbl').dataset.mets = "";
      byId('dspHPRMetsFrom').innerHTML = byId('HPRWBTbl').dataset.mets;
      var decision = 'CONFIRM';
      decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() === "" && byId('HPRWBTbl').dataset.specimencategory.trim() !== "" ) ? "ADDITIONAL" : decision; 
      decision = ( byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && byId('HPRWBTbl').dataset.specimencategory.trim() === "" ) ? "DENIED" : decision; 
      decision = ( (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== "" && byId('HPRWBTbl').dataset.specimencategory.trim() !== "") && (byId('HPRWBTbl').dataset.ospecimencategory.trim() !== byId('HPRWBTbl').dataset.specimencategory.trim())) ? "DENIED" : decision;
      if ( decision !== "DENIED") { 
        //CHECK SITE  
        decision = ( byId('HPRWBTbl').dataset.osite.trim() === "" && byId('HPRWBTbl').dataset.site.trim() !== "" ) ? "ADDITIONAL" : decision; 
        decision = ( byId('HPRWBTbl').dataset.osite.trim() !== "" && byId('HPRWBTbl').dataset.site.trim() === "" ) ? "DENIED" : decision; 
        decision = ((byId('HPRWBTbl').dataset.osite.trim() !== "" && byId('HPRWBTbl').dataset.site.trim() !== "") && (byId('HPRWBTbl').dataset.osite.trim() !== byId('HPRWBTbl').dataset.site.trim())) ? "DENIED" : decision; 
      }
      if ( decision !== "DENIED") { 
        //CHECK SUB-SITE  
        decision = ( byId('HPRWBTbl').dataset.ossite.trim() === "" && byId('HPRWBTbl').dataset.ssite.trim() !== "" ) ? "ADDITIONAL" : decision; 
        decision = ( byId('HPRWBTbl').dataset.ossite.trim() !== "" && byId('HPRWBTbl').dataset.ssite.trim() === "" ) ? "DENIED" : decision; 
        decision = ((byId('HPRWBTbl').dataset.ossite.trim() !== "" && byId('HPRWBTbl').dataset.ssite.trim() !== "") && (byId('HPRWBTbl').dataset.ossite.trim() !== byId('HPRWBTbl').dataset.ssite.trim())) ? "DENIED" : decision; 
      }
      if ( decision !== "DENIED") { 
        //CHECK DX 
        decision = ( byId('HPRWBTbl').dataset.odx.trim() === "" && diagnosis.trim() !== "" ) ? "ADDITIONAL" : decision; 
        decision = ( byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() === "" ) ? "DENIED" : decision; 
        decision = ((byId('HPRWBTbl').dataset.odx.trim() !== "" && diagnosis.trim() !== "") && (byId('HPRWBTbl').dataset.odx.trim() !== diagnosis.trim())) ? "DENIED" : decision; 
      }
      if ( decision !== "DENIED") { 
        //CHECK DXM 
        decision = ( byId('HPRWBTbl').dataset.odxm.trim() === "" && dmodifier.trim() !== "" ) ? "ADDITIONAL" : decision; 
        decision = ( byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() === "" ) ? "DENIED" : decision; 
        decision = ((byId('HPRWBTbl').dataset.odxm.trim() !== "" && dmodifier.trim() !== "") && (byId('HPRWBTbl').dataset.odxm.trim() !== dmodifier.trim())) ? "DENIED" : decision; 
      }
      byId('decisionSqr').dataset.hprdecision = decision;
      switch ( decision ) { 
        case 'CONFIRM':
          byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionconfirm\">thumb_up</i>";
          break;
        case 'ADDITIONAL':
          byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisionadd\">note_add</i>";
          break;
        case 'DENIED':
          byId('decisionSqr').innerHTML = "<i class=\"material-icons hprdecisionicon hprdecisiondenied\">thumb_down</i>";
          break;
      }
      break;
    case 'dspHPRSystemic': 
      byId('HPRWBTbl').dataset.sysm = "";
      byId('dspHPRSystemic').innerHTML = byId('HPRWBTbl').dataset.sysm;
      break;
  }
}
            
function selectRR() { 
   if ( byId('btnHPRReviewAssign').dataset.hselected === 'false' ) {            
      byId('btnHPRReviewAssign').dataset.hselected = 'true';     
   } else { 
      byId('btnHPRReviewAssign').dataset.hselected = 'false';                 
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

  if (byId('btnBarRsltRequestLink')) { 
    byId('btnBarRsltRequestLink').addEventListener('click',function() { 
      alert('Create Linkage Here');

    },false );
  }

  if (byId('btnBarRsltCheckInTray')) { 
    byId('btnBarRsltCheckInTray').addEventListener('click', function() { 
      var error = 0;  
      if (byId('coordinatorResultTbl')) { 

        var boxid = "";
        for (var c = 0; c < byId('coordinatorResultTbl').tBodies[0].rows.length; c++) { 
          if ( boxid === "" ) { 
            boxid = byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.hprboxnbr;
          } else { 
            if ( boxid === byId('coordinatorResultTbl').tBodies[0].rows[c].dataset.hprboxnbr ) { 
            } else { 
              error = 1; 
              msg = 'All Segments must be HPR Segments and must be assigned to the same HPR Slide Tray';
            }
          }   
        }
      
        if ( boxid.trim() === "" ) { 
              error = 1; 
              msg = 'No Segments are marked with an HPR Tray';
        }

      } else { 
        error = 1;
        msg = 'The \'Result\' table doesn\'t have any listed samples.  Run a valid query before performing actions.';    
      }

      if ( error === 1 ) {
        alert(msg);
      } else {
        var obj = new Object();
        obj['boxid'] = boxid;
        var passdata = JSON.stringify(obj);
        generateDialog( 'hprreturnslidetray', passdata );
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
      dta['hprTrayInvLoc'] = byId('qryHPRInvLocValue').value.trim();
      dta['procDateFrom'] = byId('bsqueryFromDateValue').value.trim();
      dta['procDateTo'] = byId('bsqueryToDateValue').value.trim();
      dta['shipDateFrom'] = byId('shpQryFromDateValue').value.trim();  
      dta['shipDateTo'] = byId('shpQryToDateValue').value.trim();  
      dta['iManifest'] = byId('qryIManifestNbr').value.trim();  
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
      menuTbl += "<tr><td class=ddMenuItem onclick=\"fillField('selectorAssignReq','"+element['requestid']+"','"+element['requestid']+"'); \">"+element['requestid']+" ["+element['rqstatus']+"]</td></tr>";
    });  
    menuTbl += "</table>";
    byId('requestDropDown').innerHTML = menuTbl; 
  }
}
//byId('requestDropDown').innerHTML = '&nbsp;';
  
  
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
   case 'penddestroy':
      dta['investigatorid'] = 'PENDDESTROY';
      dta['requestnbr'] = '';
      break;
   case 'permcollect':
      dta['investigatorid'] = 'PERMCOLLECT';
      dta['requestnbr'] = '';
      break;
  }
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/assign-segments";
  universalAJAX("POST",mlURL,passdata,answerSendSegmentAssignment,2);
}      

function answerSendSegmentAssignment(rtnData) {  
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     alert("* * * * ERROR * * * *\\n"+dspMsg);
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

function updateRtnLocations() { 

  var thisRegex = new RegExp('sRTNInvLoc[0-9]+Value');
  if ( byId('frmRtnTraySpecifics') ) { 
    var dta = new Object();
    var sld = new Object();
    var e = byId('frmRtnTraySpecifics').elements;
    var cntr = 0;
    for ( var i = 0; i < e.length; i++ ) {
      if ( thisRegex.test ( e[i].id ) ) {
        sld[e[i].dataset.bgs] = e[i].value;   
      }
    }
  }
  dta['slides'] = sld;
  dta['hprboxid'] = byId('rtnHPRTrayScanCode').value;
  dta['userid'] = window.btoa( encryptedString(key, byId('fldTRtnUsrPIN').value, RSAAPP.PKCS1Padding, RSAAPP.RawEncoding) );
  dta['devreason'] = byId('fldTRtnDeviationReason').value;
  byId('waiterIndicator').style.display = 'block';
  byId('rtnBtnDsp').style.display = 'none';
  //console.log(JSON.stringify(dta));
  var passdata = JSON.stringify(dta);
  var mlURL = "/data-doers/return-hpr-tray-inventory";
  universalAJAX("POST",mlURL,passdata,answerReturnHPRTrayInventory,2);
}

function answerReturnHPRTrayInventory( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
     var msgs = JSON.parse(rtnData['responseText']);
     var dspMsg = ""; 
     msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
     });
     byId('waiterIndicator').style.display = 'none';
     byId('rtnBtnDsp').style.display = 'block';
     alert("RETURN HPR TRAY INVENTORY ERROR:\\n"+dspMsg);
  } else { 
    //Good results
    location.reload(); 
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

function generateDialog( whichdialog, whatobject ) { 
  var dta = new Object(); 
  dta['whichdialog'] = whichdialog;
  dta['objid'] = whatobject;   
  var passdta = JSON.stringify(dta);
  byId('standardModalBacker').style.display = 'block';
  var mlURL = "/data-doers/preprocess-generate-dialog";
  universalAJAX("POST",mlURL,passdta,answerPreprocessGenerateDialog,2);
}
            
function answerPreprocessGenerateDialog( rtnData ) {
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
    byId('standardModalBacker').style.display = 'none';    
   } else {
        var dta = JSON.parse(rtnData['responseText']);         
        //TODO: MAKE SURE ALL ELEMENTS EXIST BEFORE CREATION
        var d = document.createElement('div');
        d.setAttribute("id", dta['DATA']['dialogID']); 
        d.setAttribute("class","floatingDiv");
        d.style.left = dta['DATA']['left'];
        d.style.top = dta['DATA']['top'];
        d.innerHTML = dta['DATA']['pageElement']; 
        document.body.appendChild(d);
        byId(dta['DATA']['dialogID']).style.display = 'block';
        if ( dta['DATA']['primeFocus'].trim() !== "" ) { 
          byId(dta['DATA']['primeFocus'].trim()).focus();
        }
        byId('standardModalBacker').style.display = 'block';
  }
}
        
function closeThisDialog(dlog) { 
   byId(dlog).parentNode.removeChild(byId(dlog));
   byId('standardModalBacker').style.display = 'none';        
}

function savechartreviewdocument () { 
  var dta = new Object(); 
  dta['crid'] = byId('fldCRId').value;
  dta['bgref'] = byId('fldCRBGRefd').value;   
  dta['bgassoc'] = byId('fldCRAssoc').value;   
  dta['doctxt'] = byId('crtexteditor').value;   
  var passdta = JSON.stringify(dta);
  var mlURL = "/data-doers/save-chart-review-document";
  universalAJAX("POST",mlURL,passdta,answerSaveChartReviewDocument,2);
}

function answerSaveChartReviewDocument ( rtnData ) { 
  if (parseInt(rtnData['responseCode']) !== 200) { 
    var msgs = JSON.parse(rtnData['responseText']);
    var dspMsg = ""; 
    msgs['MESSAGE'].forEach(function(element) { 
       dspMsg += "\\n - "+element;
    });
    alert("ERROR:\\n"+dspMsg);
   } else {
     //update crid with new id - if applicable 
     var dta = JSON.parse(rtnData['responseText']);         
     byId('fldCRId').value = parseInt( dta['DATA']['chartreviewid'] );
     byId('CRIdDsp').innerHTML = ( '000000' + parseInt( dta['DATA']['chartreviewid'] )).substr(-6);
     alert('Chart Review '+( '000000' + parseInt( dta['DATA']['chartreviewid'] )).substr(-6)+' has been saved!');     
   }
}

function printChartReview ( e, chartid ) { 
  e.stopPropagation(); 
  if (parseInt(chartid) === 0 ) { 
    alert( 'You must specify a chart review id (only saved charts are printable)');
  } else { 
    openOutSidePage('{$tt}/print-obj/chart-review-report/'+chartid);  
  }
}


JAVASCR;
    
return $rtnthis;
}

}

