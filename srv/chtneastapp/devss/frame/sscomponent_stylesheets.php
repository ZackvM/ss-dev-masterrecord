<?php

class stylesheets {

  public $color_white = "255,255,255";
  public $color_black = "0,0,0";
  public $color_grey = "224,224,224";
  public $color_lgrey = "245,245,245";
  public $color_brwgrey = "239,235,233";
  public $color_ddrwgrey = "189,185,183";
  public $color_lamber = "255,248,225";
  public $color_dullyellow = "226,226,125";
  public $color_mamber = "204,197,175";
  public $color_mgrey = "160,160,160";
  public $color_dblue = "0,32,113";
  public $color_mblue = "13,70,160";
  public $color_lblue = "84,113,210";
  public $color_lightblue = "209, 219, 255";
  public $color_zgrey = "48,57,71";
  public $color_neongreen = "57,255,20";
  public $color_bred = "237, 35, 0";
  public $color_darkgreen = "0, 112, 13";
  public $color_lightgrey = "239, 239, 239";
  public $color_darkgrey = "145,145,145";
  public $color_zackgrey = "48,57,71";  //#303947 
  public $color_zackcomp = "235,242,255"; //#ebf2ff
  public $color_deeppurple = "107, 18, 102";
  public $color_aqua = "203, 232, 240";

  //Z-INDEX:  0-30 - Base - Level // 40-49  - Floating level // 100 Black wait screen // 100+ dialogs above wait screen 
  
function globalstyles($mobileInd) {

 
 $rtnThis = <<<STYLESHEET

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; font-family: Roboto; font-size: 1vh; color: rgba({$this->color_black},1);}
 
.appcard { border-left: 1px solid rgba({$this->color_zackgrey},1); height: 100vh; width: 50vw; position: fixed; top: 0; left: 101vw; z-index: 49; padding: 11vh 0 0 0; background: rgba({$this->color_lgrey},1); transition: 1s; }
#standardModalBacker { position: fixed; top: 0; left: 0;  z-index: 100; background: rgba({$this->color_zackgrey},.7); height: 100vh; width: 100vw; display: none; }
#standardModalDialog {display: none; background: rgba({$this->color_lgrey},1); position: fixed; margin-left: -20vw; left: 50%; margin-top: -10vh; top: 50%; z-index: 101; border: 2px solid rgba({$this->color_white},1); }

#systemDialogClose { width: .5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 2vh;text-align: right; padding: .3vh .1vw; }
#systemDialogClose:hover {cursor: pointer; color: rgba({$this->color_bred},1); }

#systemDialogTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 1.3vh; padding: .3vh .3vw; }


.material-icons {font-size: 2.3vh; }
#topMenuHolder {position: fixed; top: 0; left: 0; width: 100vw; z-index: 50; border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
#globalTopBar { background: rgba({$this->color_zackgrey},1); padding: .7vh 2vw .5vh 2vw; }
#barchtnlogo {height: 4.5vh; }

#topBarMenuTable { width: 100%; }
#topBarMenuTable .spacer {width: 4vw; white-space: nowrap; }
.hdrOnlyItem {color: rgba({$this->color_white},1);  font-size: 1.8vh;  padding: 0 2vw 0 0;  white-space: nowrap;}
.hdrOnlyItem:hover { color: rgba({$this->color_neongreen},1); cursor: pointer; }

#rightControlPanel { width: 4vw; }
#topbarUserDisplay {color: rgba({$this->color_neongreen},1); font-size: 1.2vh; padding: 0 .2vw 1vh 0; }
.universalbtns {font-size: 2.1vh; }

.hdrItem {color: rgba({$this->color_white},1); padding: 0 2vw 0 0; font-size: 1.8vh;  white-space: nowrap; }
.mnuHldr {position: relative; }
.mnuHldr:hover {cursor: pointer; }
.mnuHldr:hover .hdrItem { color: rgba({$this->color_neongreen},1);  white-space: nowrap; }
.menuDrpItems {position: absolute; left: 0; top: 2.1vh;  background: rgba({$this->color_zackgrey},1); min-width: 10vw; display: none;  white-space: nowrap;}
.mnuHldr:hover .menuDrpItems {display: block;}
.menuHolderSqr { white-space: nowrap; } 

.menuDrpItems table {width: 100%; }
.menuDrpItems table td {color: rgba({$this->color_white},1);font-size: 1.8vh; padding: .6vh 1vw .6vh .8vw; white-space: nowrap; }
.menuDrpItems table td:hover { cursor: pointer; background: rgba({$this->color_lblue},1); }

.bigspacer { width: 30%;}
.universeBtns { color: rgba({$this->color_white},1);  font-size: 1.8vh;  padding: 0 0 0 1vw;  white-space: nowrap; }
.universeBtns:hover {color: rgba({$this->color_neongreen},1); cursor: pointer; }

#hlpHolderDiv { width: 48vw; padding: 0 1vw 0 1vw; height: 92vh; overflow: auto;}
#clsBtnHold #closeBtn { font-family: Roboto; font-size: 4vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; width: 2vw;  }
#clsBtnHold #closeBtn:hover { color: rgba({$this->color_bred},1); cursor: pointer; }
#hlpTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpSubTitle { width: 100%; font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpByLine { width: 100%; font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_darkgreen},1); text-align: right; }
#hlpText { width: 100%; font-family: Roboto; font-size: 1.8vh; line-height: 1.6em; text-align: justify; padding: 1vh 0 0 0; }

/* GENERAL CONTROLS */
.fldLabel {font-family: Roboto; font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding-top: 1vh; }
input {width: 25vw; box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1);  }
input:focus, input:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }
textarea { box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1); resize: none; }
.pageTitle {font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);}

input[type=checkbox] { width: 2vw; }
input[type=checkbox] + label { color: rgba({$this->color_lblue},1); }
input[type=checkbox]:checked + label { color: rgba({$this->color_bred},1); font-weight: bold; }

.tblBtn tr td { font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.tblBtn tr td:hover { background: rgba({$this->color_mblue},1); color: rgba({$this->color_white},1); cursor: pointer; }
.tblBtn tr td.active { background: rgba({$this->color_darkgreen},1); }

/* DROP DOWN TABLES */
.menuHolderDiv { position: relative; }
.menuHolderDiv:hover .valueDropDown { display: block; cursor: pointer; }
.valueDropDown {background: rgba({$this->color_white},1);position: absolute; border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; margin-top: .1vh; min-height: 15vh; max-height: 33vh; overflow: auto; display: none; z-index: 25; }
.menuDropTbl { font-size: 1.8vh; padding: .6vh .1vw .6vh .1vw; white-space: nowrap; background: rgba({$this->color_white},1); width: 100%; }
.ddMenuItem {padding: .3vh .2vw;}
.ddMenuItem:hover { cursor: pointer;  background: rgba({$this->color_lblue},1); color: rgba({$this->color_white},1);  }
.ddMenuClearOption { font-size: 1.1vh; }
.ddMenuClearOption:hover {color: rgba({$this->color_bred},1); }

/* DROP DOWN CALENDARS */
.ddMenuCalendar { width: 100%; }
.ddMenuCalTopRow { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }
.smallCtlBtn { font-size: 1.5vh;padding: 3px 0; }
.smallCtlBtn:hover { color: rgba({$this->color_neongreen},1); cursor: pointer; }
.ddMenuCalTitle { font-size: 1.3vh; text-align: center; padding: 3px 0; }
.ddCalHeadDay { background: rgba({$this->color_mgrey},1); padding: 8px 5px; width: 14%; box-sizing: border-box;  }
.topSpacer { background: rgba({$this->color_grey},1); border-left: 1px solid rgba({$this->color_zackgrey}, 1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
.btmSpacer { background: rgba({$this->color_grey},1); border-left: 1px solid rgba({$this->color_zackgrey}, 1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
.mnuDaySquare { height: 3vh; text-align: left; padding: 4px 0 0 4px; border-left: 1px solid rgba({$this->color_zackgrey}, 1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
.mnuDaySquare:hover {background: rgba({$this->color_lblue},1); color: rgba({$this->color_white},1); }
.calBtmLineClear { font-size: 1vh; padding: 8px 0; }
.calBtmLineClear:hover { color: rgba({$this->color_bred},1); }

#pageTopButtonBar {width: 100%; box-sizing: border-box; background: rgba({$this->color_zackgrey},1); position: fixed; top: 0; z-index: 49;}
#pageTopButtonBar .topBtnHolderCell {border-right: 1px solid rgba({$this->color_white},1); padding: 0 .5vw 0 .5vw; } 
#topBtnBarVerticalSpacer {height: 6.5vh; }
#topBtnBarHorizontalSpacer {width: .5vw; }
#topBtnBarTbl { }
#topBtnBarTbl .topBtnDisplayer {color: rgba({$this->color_white},1); font-size: 1.8vh; } 
#topBtnBarTbl .topBtnDisplayer:hover {cursor: pointer; color: rgba({$this->color_neongreen}); }
#topBtnBarTbl .topBtnDisplayer td {white-space: nowrap; }

#usrAccountTitle { font-size: 2vh; font-weight: bold; color: rgba({$this->color_lblue},1); }


STYLESHEET;

//FOR CHECKBOXES AND RADIO BUTTONS https://www.w3schools.com/howto/howto_css_custom_checkbox.asp
 return $rtnThis;
  }

function root($mobileind) { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 7vh; box-sizing: border-box; padding: 0 2vw 0 2vw;  }
              
#rootTable { font-family: Roboto; font-size: 2vh; }

STYLESHEET;
return $rtnThis;
  }    

  function segment($mobileind) { 
      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 12vh; box-sizing: border-box;  }
      
STYLESHEET;
      return $rtnThis;
  }
  
function datacoordinator($mobileind) { 
      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 12vh; box-sizing: border-box;  }

#mainQGridHoldTbl {width: 96%;  box-sizing: border-box; margin-left: 2vw; margin-right: 2vw; }
#gridholdingdiv {width: 100%; height: 80vh; position: relative;}
.gridDiv { position: absolute; top: 0; left: 0; width: 100%; height: 100%;}

#biogroupdiv {background: rgba({$this->color_white},1); display: block; }

#bigQryRsltTbl {margin-left: .5vw; margin-right: .5vw; width: 99vw; } 
#bankDataHolder { height: 80vh; width: 98vw; overflow: auto; border: 1px solid #000; }

.suggestionHolder { position: relative; }
.suggestionDisplay {min-width: 25vw; position: absolute; left: 0; max-height: 30vh; min-height: 15vh; overflow: auto; z-index: 25; box-sizing: border-box; display: none;background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionTable { max-width: 24vw; box-sizing: border-box;   }

.suggestionCloseBtn:hover {color: rgba({$this->color_bred},1); cursor: pointer; }
.suggestionDspLine:nth-child(even) { background: rgba({$this->color_grey},1); }
.suggestionDspLine td { padding: 2px; border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionDspLine:hover { background: rgba({$this->color_lamber},1); cursor: pointer; }


#coordinatorResultTbl { width: 99vw; border-collapse: collapse;   }
#coordinatorResultTbl thead th { white-space: nowrap; padding: 8px 4px; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 1.3vh; text-align: left; } 
#coordinatorResultTbl thead .cnttxt {text-align: center; }
#coordinatorResultTbl .colorline {width: .1vw !important; }

#coordinatorResultTbl tbody td {font-size: 1.4vh; padding: 8px 6px; }
#coordinatorResultTbl tbody .tinyText {font-size: 1vh; }
#coordinatorResultTbl tbody .bgsLabel { font-weight: bold; }
#coordinatorResultTbl tbody .informationalicon { color: rgba({$this->color_mgrey},1); text-align: center; }
#coordinatorResultTbl tbody .informationalicon:hover {cursor: pointer; color: rgba({$this->color_bred},1); }

#coordinatorResultTbl tbody tr {border-bottom: 1px solid rgba({$this->color_mgrey},1); }
#coordinatorResultTbl tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#coordinatorResultTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#coordinatorResultTbl tbody tr[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }

#coordinatorResultTbl .strikeout { text-decoration: line-through; }
#coordinatorResultTbl tbody .qmsiconholder { text-align: center; font-size: 1vh; } 
.qms .material-icons {font-size: 2.1vh; }
#coordinatorResultTbl tbody .qmsiconholders { text-align: center; background: rgba({$this->color_bred}, 1); color: rgba({$this->color_white},1);  }
#coordinatorResultTbl tbody .qmsiconholderl { text-align: center; background: rgba({$this->color_deeppurple}, 1); color: rgba({$this->color_white},1); } 
#coordinatorResultTbl tbody .qmsiconholderr { text-align: center; background: rgba({$this->color_dullyellow}, 1); color: rgba({$this->color_dblue},1); } 
#coordinatorResultTbl tbody .qmsiconholderh { text-align: center; background: rgba({$this->color_lblue}, 1); color: rgba({$this->color_white},1); } 
#coordinatorResultTbl tbody .qmsiconholderq { text-align: center; background: rgba({$this->color_darkgreen}, 1); color: rgba({$this->color_white},1);} 
#coordinatorResultTbl tbody .qmsiconholdern { text-align: center; background: rgba({$this->color_aqua}, 1); color: rgba({$this->color_zackgrey},1);} 

#coordinatorResultTbl tbody .ttholder { position: relative; }
#coordinatorResultTbl tbody .ttholder:hover .tt { display: block; }
#coordinatorResultTbl tbody .ttholder .infoIconDiv {float: left; }
#coordinatorResultTbl tbody .ttholder .infoTxtDspDiv {position: absolute; background: rgba({$this->color_aqua},1); min-width: 20vw;  max-width: 35vw; z-index: 40; left: 2vw; display: none; padding: 8px; box-sizing: border-box; font-size: 1.8vh; }
#coordinatorResultTbl tbody .ttholder .infoTxtDspDiv:after{content:'';position:absolute;border:15px solid transparent;border-top:15px solid rgba({$this->color_aqua},1); top:0px;left:-10px;}
#coordinatorResultTbl tbody .ttholder:hover .infoTxtDspDiv { display: block; }
#coordinatorResultTbl tbody .tt { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; white-space: nowrap; z-index: 40; }
#coordinatorResultTbl tbody .cntr { text-align: center; }
#coordinatorResultTbl tbody .groupingstart {border-left: 3px solid rgba({$this->color_zackgrey},1); }
#coordinatorResultTbl thead .groupingstart {border-left: 3px solid rgba({$this->color_white},1); }

#selectorAssignInv {width: 10vw; }

#assigSegHolder {border-left: rgba({$this->color_dblue},1) 1px solid; padding: 8px 4px 0 4px; }
#segmentAssignListing { height: 20vh; overflow: auto;  }

#assigningSegTbl { font-size: 1.5vh; }
#assigningSegTbl #saTitle { text-align: center;  border-bottom: 1px solid rgba({$this->color_dblue},1); }
#assigningSegTbl .segmentBGSNbr { border-left: 1px solid rgba({$this->color_dblue},1);border-bottom: 1px solid rgba({$this->color_dblue},1); padding: 2px 2px 0 2px; font-size: 1.2vh; }

#resultTblContextMenu { position: fixed; z-index: 51; background: rgba({$this->color_white}, 1); top: -999px; left: -999px; border: 1px solid rgba({$this->color_dblue},1); display: none; }

#contentMenuTbl { }
#contentMenuTbl .contextOptionHolder { border-bottom: 1px solid rgba({$this->color_white},1); background: rgba({$this->color_white},1); }
#contentMenuTbl .contextOptionHolder:hover { cursor: pointer; background: rgba({$this->color_lamber},1); border-bottom: 1px solid rgba({$this->color_mgrey},1);  }

#contentMenuTbl .contextOptionHolder .cmOptionIcon { font-size: 2.5vh; color: rgba({$this->color_mgrey}, 1) }
#contentMenuTbl .contextOptionHolder .cmOptionText { font-size: 1.2vh; color: rgba({$this->color_mgrey},1); }

#contentMenuTbl .contextOptionHolder:hover .cmOptionIcon {color: rgba({$this->color_dblue},1); }
#contentMenuTbl .contextOptionHolder:hover .cmOptionText {color: rgba({$this->color_dblue},1); }

#resultHoldingDiv { position: relative; } 
#resultHoldingDiv #recordResultDiv {  display: block;}
#resultHoldingDiv #dspParameterGrid { display: none;}

#qParameterHolder { padding: 0 25vw 0 25vw; }
#qParameterHolder #qryParameterDspTbl { width: 50vw; border: 1px solid rgba({$this->color_zackgrey},1); }
#qParameterHolder #qryParameterDspTbl #title { text-align: center; font-size: 2.8vh; color: rgba({$this->color_lblue},1); font-weight: bold;    }
#qParameterHolder #qryParameterDspTbl .columnQParamName { width: 5vw; padding: 8px 5px 8px 5px; font-weight: bold; font-size: 1.4vh; white-space: nowrap; border-bottom: 1px solid rgba({$this->color_grey},1); border-right: 1px solid rgba({$this->color_grey},1); }
#qParameterHolder #qryParameterDspTbl .ColumnDataObj { font-size: 1.4vh; padding: 8px 5px;border-bottom: 1px solid rgba({$this->color_grey},1); } 
#qParameterHolder #qryParameterDspTbl #srchTrmParaTitle { text-align: center; font-size: 2vh; color: rgba({$this->color_lblue},1); padding: 3vh 0 0 0; }


/* shipdocCreator */
#sdcMainHolderTbl { width: 80vw;}
#sdcRqstShipDate {width: 9vw; }
#sdshpcal {min-width: 17vw; }
#sdcRqstToLabDate {width: 9vw; }
#tolabcal { min-width: 17vw; }

#sdcShipDocNbr { width: 5vw }
#sdcAcceptedBy { width: 9vw; }
#sdcAcceptorsEmail { width: 16vw; }
#sdcPurchaseOrder { width: 11vw; }
#sdcPublicComments {width: 62vw; height: 10vh; }

#TQAnnouncement { font-family: Roboto; font-size:  2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); border-top: 1px solid rgba({$this->color_zackgrey},1);  }

#sdcInvestCode { width: 6vw; }
#sdcInvestName { width: 18vw; }
#sdcInvestEmail { width: 26vw; }
#sdcInvestPrimeDiv { width: 10vw; }
#sdcInvestInstitution { width: 35.2vw; }
#sdcInvestTQStatus {width: 10vw; }
#sdcInvestTQInstType {width: 15vw; }

#sdcInvestShippingAddress {width: 29.7vw; height: 18vh; resize:none; }
#sdcShippingPhone {width: 29.7vw; }
#sdcShippingEmail {width: 14.8vw; }
#sdcInvestBillingAddress {width: 29.7vw; height: 18vh; resize:none; }
#sdcBillPhone { width: 29.7vw;  }
#sdcBillEmail { width: 14.8vw; }


#segmentListHolder { width: 20vw; border-left: 1px solid rgba({$this->color_zackgrey},1); }

STYLESHEET;
return $rtnThis;
}  
  
function documentlibrary($mobileind) { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 7vh; box-sizing: border-box; padding: 0 2vw 0 2vw;  }
        
#doclibabstbl { font-family: Roboto; font-size: 1.8vh; }
#doclibabstbl #headerwarning {font-size: 1.5vh; font-weight: bold; }
#doclibabstbl #byline {font-size: 1.2vh; }   
#doclibabstbl .fldLabel { font-size: 1.8vh; border-bottom: 1px solid rgba({$this->color_zackgrey},1); white-space: nowrap;}
#doclibabstbl .datalines td {border-left: 1px solid rgba({$this->color_lightgrey},1); border-bottom: 1px solid rgba({$this->color_lightgrey},1); }
#doclibabstbl .datalines:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
#doclibabstbl .datalines:hover {cursor: pointer; background: rgba({$this->color_lightblue},1); }
#doclibabstbl .datalines .bgnbr { text-align: center; padding: 1vh 0; } 
#doclibabstbl .datalines .abstracttext { padding: 1vh 1vw; }

#docVertList {width: 20vw; font-size: 1.3vh; box-sizing: border-box; }
#vertHold {width: 20vw; box-sizing: border-box; }
#vertdivhold {width: 21vw; height: 74vh; border: 1px solid rgba({$this->color_zackgrey},1); overflow: auto;box-sizing: border-box;}

.prntIcon {width: 1.4vw; }
.prntIcon:hover {cursor: pointer; color: rgba({$this->color_neongreen},1); }

#displayDocText {border: 1px solid rgba({$this->color_zackgrey},1);height: 74vh;overflow: auto; padding: 1vh 1vw; box-sizing: border-box; } 
#docPRHeader {border-bottom: 1px solid rgba({$this->color_grey},1); color: rgba({$this->color_mgrey},1); font-weight: bold;   }
#iconHold {border-bottom: 1px solid rgba({$this->color_grey},1); color: rgba({$this->color_mgrey},1); }
#iconHold:hover {cursor: pointer; color: rgba({$this->color_bred},1); }

#documentDisplay { text-align: justify; line-height: 1.8em; font-size: 1.4vh; padding: 1vh .3vw; } 
#pxiBottomLine {border-top: 1px solid rgba({$this->color_grey},1); font-size: 1vh; font-weight: bold; }
STYLESHEET;
return $rtnThis;
}      
   
function login($mobileInd) {
$at = genAppFiles; 
  $bgPic = base64file("{$at}/publicobj/graphics/bg.png","background","bgurl",true);
   $rtnThis = <<<STYLESHTS

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; font-family: Roboto; font-size: 1vh; color: rgba({$this->color_black},1);}
body {margin: 0; height: 100%; width: 100%; background: rgba({$this->color_white},1); background: {$bgPic} repeat; }
    
#topLocBar {background: rgba({$this->color_zackgrey},1); height: 4vh; position: fixed; left: 0; top: 0; z-index: 51; width: 100%; box-sizing: border-box; padding: 0 0 0 12vw; }
#chtnTopTab {height: 4vh; width: 9vw; background: rgba({$this->color_zackcomp},1); padding: .9vh 0 .9vh 0; box-sizing: border-box; font-family: 'Fira Sans', tahoma; font-size: 1.7vh; color: rgba({$this->color_dark},1); text-align: center; }
#chtnTopTab:hover {cursor: pointer; }
.otherTabs { height: 4vh; width: 10vw;  padding: .9vh 0 .9vh 0; box-sizing: border-box; font-family: 'Fira Sans', tahoma; font-size: 1.7vh; color: rgba({$this->color_darkgrey},1); text-align: center;  white-space: nowrap; }
.otherTabs:hover {cursor: pointer; color: rgba({$this->color_lightgrey},1); }
#makeGreen { color: rgba({$this->color_darkgreen},1); }           

#loginHolder { position: absolute; width: 40vw; border: 1px solid rgba({$this->color_dblue},1); margin-top: -30vh; top: 50%; margin-left: -20vw; left: 50%; -webkit-box-shadow: 0px 14px 74px -15px rgba(194,194,194,1);-moz-box-shadow: 0px 14px 74px -15px rgba(194,194,194,1); box-shadow: 0px 14px 74px -15px rgba(194,194,194,1); background: rgba({$this->color_white},1); box-sizing: border-box; }

#loginDialogHead { font-family: Roboto; font-size: 2.8vh; color: rgba({$this->color_white},1); background: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh .8vw; }

#loginGrid {padding: 1vh 2vw 3vh 2vw; }
#loginGrid .label {font-family: Roboto; font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_mblue},1); padding-top: 2vh; }
#loginGrid input {width: 100%; box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_zackcomp},1);  }
#loginGrid input:focus, #loginGrid input:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }

#loginFooter {font-family: Roboto; font-size: 1.2vh; line-height: 1.8em; padding: 2vh .8vw 2vh .8vw; text-align: justify; border-top: 1px solid rgba({$this->color_dblue},1); background: rgba({$this->color_lightgrey},1); }

.adminBtn {font-family: Roboto; font-size: 1.8vh; border: 1px solid rgba({$this->color_mblue},1); color: rgba({$this->color_mblue}, 1); padding: 8px 4px 8px 4px; }
.adminBtn:hover {cursor: pointer; background: rgba({$this->color_lgrey},1); }

.pseudoLink { font-size: 1vh; font-weight: normal; }
.pseudoLink:hover {cursor: pointer; color: rgba({$this->color_darkgreen},1); }
STYLESHTS;
return $rtnThis;           
}
  
}


/* BACKUP 
 *
  function procurementgrid($mobileind) { 
      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 8vh; box-sizing: border-box; padding: 0 2vw 0 2vw;  }

#pqcTbl {width: 15vw; height: 15vh; font-family: Roboto; font-size: 1.5vh; }
#pqcLeft {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1);}
#pqcLeft:hover {color: rgba({$this->color_neongreen},1); }
#pqcTitle {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); text-align: center; } 
#pqcRight {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); text-align: right;}
#pqcRight:hover {color: rgba({$this->color_neongreen},1); }
.pqcDayHead {background: rgba({$this->color_lightgrey},1); font-size: 1.2vh; font-weight: bold; text-align: center; padding: 4px; }
.pqcDaySqr { font-size: 1.2vh; padding: 2px; }
.pqcDaySqr table {border: 1px solid rgba({$this->color_zackgrey},1);width: 100%; height: 100%; }
.pqcDaySqr table:hover {background: rgba({$this->color_neongreen},1); }
#pqcTopSpacer {background: rgba({$this->color_lightgrey},1); }
#pqcBtmSpacer {background: rgba({$this->color_lightgrey},1); }


STYLESHEET;
return $rtnThis;
  }
 */
