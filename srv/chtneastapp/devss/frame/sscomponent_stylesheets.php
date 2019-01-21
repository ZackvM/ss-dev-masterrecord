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
  public $color_cornflowerblue = "100,149,237";
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

#apptrayUserProfilePicture { width: 18vh;  }
.circular--portrait {
  position: relative;
  height: 17vh;
  width: 9vw;
  overflow: hidden;
  border-radius: 50%;
}


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
#directoryDisplay { width: 48vw; padding: 0 1vw 0 1vw; height: 92vh; overflow: auto;}
#clsBtnHold #closeBtn { font-family: Roboto; font-size: 4vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; width: 2vw;  }
#clsBtnHold #closeBtn:hover { color: rgba({$this->color_bred},1); cursor: pointer; }
#hlpTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpSubTitle { width: 100%; font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpByLine { width: 100%; font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_darkgreen},1); text-align: right; }
#hlpText { width: 100%; font-family: Roboto; font-size: 1.8vh; line-height: 1.6em; text-align: justify; padding: 1vh 0 0 0; }


.sidebarprofilepicture { height: 15.5vh; }
.circularOverlay { position: relative; height: 15vh; width: 8vw; overflow: hidden; border-radius: 50%; }

#directoryTbl { width: 46vw;   }
#directoryTbl .institutionHeader { font-size: 1.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .8vh 0; border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
#directoryTbl .directorySpacer { height: 1.2vh; }

.profPicCell { width: 9vw; }
.profileDetailDisplay { width: 23vw;  }
.personCell {border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); padding: 10px 10px; } 

.personLbl { font-size: 1.1vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.personData { font-size: 1.1vh;  color: rgba({$this->color_zackgrey},1); }

#directoryHeaderTbl { width: 46vw; border-collapse: collapse;   }
#directoryHeaderTblTitle { font-size: 2vh; color: rgba({$this->color_dblue},1); font-weight: bold;     }
#closeBtnDirectory { font-family: Roboto; font-size: 4vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; width: 1vw;  }
#closeBtnDirectory:hover { color: rgba({$this->color_bred},1); cursor: pointer; }

#profTrayUserName { font-size: 2.8vh; font-weight: bold; color: rgba({$this->color_dblue},1); padding: 0 1vw; }
#profTrayUserEmail {font-size: 1.8vh; color: rgba({$this->color_dblue},1); padding: 0 1vw; }
#profTrayPictureHold {padding: 0 0 0 3vw; }

#profTrayControlDivHoldTbl { width: 46vw; border-collapse: collapse; }
#profTrayControlDivHolder {  }

#profTrayControlAbtMe { padding: 10px; display: block; }
#profTrayControlAccess { padding: 10px; display: none;  }
#profTrayControlManage { padding: 10px; display: none; }

#profTrayDisplayAltYN { width: 3vw; font-size: 1.3vh;  }
#ddprofTrayDisplayAltYN {min-width: 3vw; }

#profTrayAMEmail { font-size: 1.3vh; width: 15vw; }
#profTrayAMDBID { font-size: 1.3vh; width: 5vw; }
#profTrayOfficePhn { font-size: 1.3vh; width: 11vw; } 
#profTrayAltPhone { font-size: 1.3vh; width: 11vw; }

#profTrayAltEmail { font-size: 1.3vh; width: 11vw; }
#profTrayAccessLvl { font-size: 1.3vh; width: 12vw; }
#profTrayProfilePicture {font-size: 1.3vh; width: 15vw; }
#profTrayCC {font-size: 1.3vh; width: 12vw; }
#ddprofTrayCC { min-width: 12vw; }





/* GENERAL CONTROLS */
.fldLabel {font-family: Roboto; font-size: 1.4vh;  color: rgba({$this->color_zackgrey},1); padding-top: 1vh; }
input {width: 25vw; box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1);  }
input:focus, input:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }
textarea { box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1); resize: none; }
.pageTitle {font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);}

input[type=checkbox] { width: 2vw; }
input[type=checkbox] + label { color: rgba({$this->color_lblue},1); }
input[type=checkbox]:checked + label { color: rgba({$this->color_bred},1); font-weight: bold; }

.tblBtn tr td { font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); }
.tblBtn tr td:hover { background: rgba({$this->color_mblue},1); color: rgba({$this->color_white},1); cursor: pointer; }
.tblBtn tr td.active { background: rgba({$this->color_darkgreen},1); }

/* DROP DOWN TABLES */
.menuHolderDiv { position: relative; }
.menuHolderDiv:hover .valueDropDown { display: block; cursor: pointer; }
.valueDropDown {background: rgba({$this->color_white},1);position: absolute; border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; margin-top: .1vh; min-height: 15vh; max-height: 33vh; overflow: auto; display: none; z-index: 25; }
.menuDropTbl { font-size: 1.8vh; padding: .6vh .1vw .6vh .1vw; white-space: nowrap; background: rgba({$this->color_white},1); width: 100%; }

.valueDisplayHolder { position: relative; } 
.valueDisplayDiv { background: rgba({$this->color_white},1);position: absolute; border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; margin-top: .1vh; min-height: 15vh; max-height: 33vh; overflow: auto; display: none; z-index: 25; }

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

#pageTopButtonBar {width: 100%; box-sizing: border-box; background: rgba({$this->color_zackgrey},1); position: fixed; top: 0; left: 0; z-index: 49;}
#pageTopButtonBar .topBtnHolderCell {border-right: 1px solid rgba({$this->color_white},1); padding: 0 .5vw 0 .5vw; } 
#topBtnBarVerticalSpacer {height: 6.5vh; }
#topBtnBarHorizontalSpacer {width: .5vw; }
#topBtnBarTbl { }
#topBtnBarTbl .topBtnDisplayer {color: rgba({$this->color_white},1); font-size: 1.8vh; } 
#topBtnBarTbl .topBtnDisplayer:hover {cursor: pointer; color: rgba({$this->color_neongreen}); }
#topBtnBarTbl .topBtnDisplayer td {white-space: nowrap; }

.usrAccountTitle { font-size: 2vh; font-weight: bold; color: rgba({$this->color_dblue},1); }

.helppicturecaption { font-size: 1.1vh; color: rgba({$this->color_darkgrey},1); font-weight: bold; font-style: italics; }
STYLESHEET;

//FOR CHECKBOXES AND RADIO BUTTONS https://www.w3schools.com/howto/howto_css_custom_checkbox.asp
 return $rtnThis;
  }

function procurebiosample($mobileind) { 

$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }

#btnPBClearGrid { width: 2vw; }
 
#procurementAddHoldingTbl { height: 86vh; box-sizing: border-box; }
#procurementAddHoldingTbl .sidePanel { width: 30vw; box-sizing: border-box;  }
#procurementAddHoldingTbl #procGridHolderCell { width: 67vw; border-right: 1px solid rgba({$this->color_ddrwgrey},1); box-sizing: border-box;   }

.topBtnHolderCell .ttholder { position: relative; }
.topBtnHolderCell .ttholder:hover .tt { display: block; text-align: left; }
.topBtnHolderCell .tt { position: absolute; left: -.5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }
.topBtnHolderCell .tt .btnBarDropMenuItems { font-size: 1.5vh; }
.topBtnHolderCell .tt .btnBarDropMenuItems .btnBarDropMenuItem:hover { background: rgba({$this->color_lblue},1);  color: rgba({$this->color_white},1); cursor: pointer; } 

.procGridHoldingTable {width: 66vw; box-sizing: border-box; }
.procGridHoldingTitle {font-size: 1.7vh; color: rgba({$this->color_cornflowerblue},1); font-weight: bold; border-bottom: 2px solid rgba({$this->color_ddrwgrey},1); padding: .5vh 0 0 0; } 
.procGridHoldingDecorationLineBACKUP {border-left: 2px solid rgba({$this->color_ddrwgrey},1);border-top: 2px solid rgba({$this->color_ddrwgrey},1);}

.prcFldLbl { font-size: 1.3vh; color: rgba({$this->color_dblue},1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); padding: 0 0 0 0; font-weight: bold; }
input {font-size: 1.3vh; padding: 1vh .5vw; }
textarea { font-size: 1.3vh; }

#fldPRCProcedureDate { width: 8vw; font-size: 1.2vh; }
#fldPRCProcedureInstitutionValue { width: 8vw; font-size: 1.2vh; }

#BSLock { width: 4vw; text-align: center; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_lgrey},1); }
#BSLock .ttholder { position: relative; }
#BSLock .ttholder:hover .tt { display: block; text-align: left; }
#BSLock .tt { position: absolute; left: .5vw; font-size: 1.3vh; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }
.bslockdsp { font-size: 4vh; color: rgba({$this->color_darkgreen},1); }

#fldPRCBGNbr { width: 7vw; }
#fldPRCProcDate { width: 10vw; }
#fldPRCProcedureType { width: 10vw; }
#ddPRCProcedureType { min-width: 10vw; }
#fldPRCCollectionType { width: 10vw; }
#ddPRCCollectionType { min-width: 10vw; }
#fldPRCTechInstitute { width: 15.5vw; }
#fldPRCInitialMetric { width: 3vw; text-align: right; }
#fldPRCMetricUOM {width: 6vw; }
#ddPRCMetricUOM {min-width: 5vw; }
#fldSubjectNbr {width: 8vw; }
#fldProtocolNbr {width: 8vw;}

#fldPRCSpecCat {width: 13vw; }
#ddPRCSpecCat {min-width: 13vw; }
#fldPRCSite {width: 12vw; }
#ddPRCSite { min-width: 15vw; }
#fldPRCSSite {width: 11.2vw; }
#ddPRCSSite { min-width: 11.2vw; }
#fldPRCDXMod {width: 19.3vw; }
#ddPRCDXMod { min-width: 19.3vw; }
#fldPRCBSCmts {width: 33.6vw; height: 10vh; }
#fldPRCHPRQ {width: 33.6vw; height: 10vh; }
#fldPRCUnknownMet {width: 6vw; }
#ddPRCUnknownMet {min-width: 6vw; }
#fldPRCUnInvolved { width: 6vw; }
#ddPRCUnInvolved { min-width: 4vw; }
#fldPRCSitePositions { width: 15vw; }
#ddPRCSitePositions { width: 15vw; }
#fldPRCDiagnosisDesignation { width: 25vw; }
#fldPRCMETSDesignation {width: 32vw; }
#fldPRCSystemicDX {width: 32vw; }
#fldPRCPXISex {width: 3vw; text-align: center; }
#fldPRCPXIId { display: none; }
#fldPRCPXIInitials { width: 3vw; }
#fldADDPXIInitials { width: 5vw; }
#fldPRCPXIAge {width: 3vw; text-align: right; }
#fldADDPXIAge {width: 3vw; text-align: right; }
#fldPRCPXIAgeMetric {width: 4vw;}
#fldADDAgeUOM {width: 5vw;}
#ddADDAgeUOM {min-width: 5vw; }
#fldPRCPXIRace {width: 11.8vw; }
#fldPRCPXIInfCon {width: 4vw; }
#fldPRCPXILastFour {width: 4vw; }
#fldPRCPXICX {width: 5vw; }
#ddPRCPXICX {min-width: 6vw;}
#fldPRCPXIRX {width: 5vw; }
#ddPRCPXIRX {min-width: 6vw;}
#fldPRCUpennSOGI { width: 6.5vw; }
#ddPRCUpennSOGI { width: 9vw; }
#metsFromDsp { display: none; }
#fldPRCMETSSite { width: 15.5vw; }
#ddPRCMETSSite { min-width: 15.5vw; }
#fldPRCMETSDX { width: 18vw; }
#ddPRCMETSDX { min-width: 18vw; }
#fldPRCSitePosition { width: 16.5vw; }
#ddPRCSitePosition { min-width: 16.5vw; }
#fldPRCSystemList {width: 16.5vw; }
#ddPRCSystemList {min-width: 16.5vw; }
#displayVocabulary { min-width: 25vw; overflow-x: hidden;}
#displayMETSVocabulary { min-width: 25vw; overflow-x: hidden; }
.vocabMenuItem:hover { cursor: pointer; background-color: rgba({$this->color_lblue},1); color: rgba({$this->color_white},1); }
#vocabCloseBtn {font-size: 1.3vh; width: .5vw; text-align: right; padding: 0 .3vw 0 0; }
#vocabCloseBtn:hover { cursor: pointer; color: rgba({$this->color_bred},1); }
#vocabVersionNbr { font-size: 1.4vh; text-align: right; }
#vocabRecordsFound { font-size: 1.4vh; font-weight: bold; }
#vocabMETSCloseBtn {font-size: 1.3vh; width: .5vw; text-align: right; padding: 0 .3vw 0 0; }
#vocabMETSCloseBtn:hover { cursor: pointer; color: rgba({$this->color_bred},1); }
#vocabMETSVersionNbr { font-size: 1.4vh; text-align: right; }
#vocabMETSRecordsFound { font-size: 1.4vh; font-weight: bold; }
#fldPRCPathRpt { width: 5vw; }
#ddPRCPathRpt { width: 5vw; }


#BSDspMainHeader { font-size: 2vh; text-align: center; font-variant: small-caps; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_cornflowerblue},1); padding: 1vh 1vw; border: 2px solid rgba({$this->color_dblue},1); }
.BSDspSectionHeader { padding-top: 1vh; padding-bottom: 1vh; padding-left: .5vw; font-size: 1.4vh; background: rgba({$this->color_lgrey},1); color: rgba({$this->color_cornflowerblue},1); font-weight: bold; border: 1px solid rgba({$this->color_grey},1);  }
.BSDspSpacer { height: 1.3vh;  }
.procGridHoldingLine {padding-top: .5vh; }
.BSDspSmallSpacer { height: .2vh; font-size: .3vh; }

#divORHolder { margin-left: .1vw; margin-top: .2vh; position: relative; font-size: 1.3vh; border: 1px solid rgba({$this->color_zackgrey},1); }

#divORHolder #headerTbl {  background: rgba({$this->color_cornflowerblue},1);  }
#divORHolder #headerTbl th { padding: 4px 4px 4px 4px; } 


#PXIDspTblHD {border-collapse: collapse;}
#dataPart {  height: 78vh; overflow: auto; } 

.procedureSpellOutTbl { border-collapse: collapse;  }
.procTxtDsp { line-height: 1.4em; text-align: justify; padding: 0 .8vw 0 0; }

.dspORTarget { width: 2vw; text-align: center; box-sizing: border-box; border-right: 1px solid  rgba({$this->color_grey},1); }
.dspORInformed {width: 2vw; text-align: center;box-sizing: border-box; border-right: 1px solid  rgba({$this->color_grey},1); }
.dspORAdded {width: 2vw; text-align: center; box-sizing: border-box; border-right: 1px solid  rgba({$this->color_grey},1);}
.dspORInitials {width: 2vw; text-align: left;box-sizing: border-box; border-right: 1px solid  rgba({$this->color_grey},1); padding: 4px; }
.dspProcCell { padding: 0 0 0 4px; }

.targeticon {font-size: 1.8vh; color: rgba({$this->color_white},1); }
.targetwatch { background: rgba({$this->color_cornflowerblue},1); }
.targetrcvd { background: rgba({$this->color_darkgreen},1); }
.targetnot { background: rgba({$this->color_bred},1); }
.addicon { font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); }

.btnEditPHIRecord { font-size: 1.4vh; color: rgba({$this->color_dblue},1); }
.btnEditPHIRecord:hover {cursor: pointer; color: rgba({$this->color_bred},1); text-decoration: underline; }

.smallORTblLabel { white-space: nowrap; font-weight: bold; }

#procDataDsp {border-collapse: collapse;}
.displayRows {background: rgba({$this->color_white},1); }
.displayRows:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
.displayRows:hover {cursor: pointer; background: rgba({$this->color_lamber},1); }

#vocabularyDspTable { width: 100%; border-collapse: collapse; } 
#vocabularyDspTable th { font-size: 1.4vh; font-weight: bold; white-space: nowrap; text-align: left; }
#vocabularyDspTable td { font-size: 1.4vh; white-space: nowrap;  padding: 3px 8px 3px 5px;  }




#fldDNRTarget { width: 6vw; }
#ddDNRTargetValue {min-width: 6vw; }
#fldADDTarget { width: 6vw; }
#ddADDTargetValue {min-width: 6vw; }

#fldDNRInformedConsent { width: 6vw; }
#ddDNRInformedConsent { min-width: 6vw; }
#fldADDInformedConsent { width: 6vw; }
#ddADDInformedConsent { min-width: 6vw; }

#fldDNRAge { width: 3vw; text-align: right; }
#fldDNRAgeUOM {width: 6vw; }
#ddDNRAgeUOM { min-width: 6vw; }
#fldDNRRace { width: 10vw; }
#fldADDRace {width: 10vw; }
#ddDNRRace { min-width: 10vw; }
#ddADDRace {min-width: 10vw; }

#fldDNRSex { width: 5vw; }
#ddDNRSex { min-width: 5vw; }
#fldADDSex { width: 5vw; }
#ddADDSex { min-width: 5vw; }

#fldDNRLastFour {width: 5vw; }
#fldDNREncNotesType { width: 10vw; }
#fldDNREncounterNote { width: 24vw; }
#fldDNRNotReceivedNote {width: 41vw; }
#notRcvdNoteDsp {display: none; }

.ENCHEAD { width: 78vh; }
.ENCHEAD tr td { text-align: center; font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);  }

.DNRLbl { color: rgba({$this->color_zackgrey},1); font-weight: bold; font-size: 1.2vh; padding: .2vh .2vw; }
.DNRLbl2 { color: rgba({$this->color_zackgrey},1); font-weight: bold; font-size: 1.1vh; padding: .2vh .2vw 0 0; border-bottom: 1px solid rgba({$this->color_darkgrey},1); }
.DNRDta { color: rgba({$this->color_zackgrey},1); font-size: 1.2vh; padding: .2vh .2vw; border-bottom: 1px solid rgba({$this->color_zackgrey},1); min-width: 10vw;}
.procedureTextDsp { color: rgba({$this->color_zackgrey},1); font-size: 1.2vh; padding: 0 .3vw .4vh .3vw; width: 41vw; text-align: justify; line-height: 1.4em; }

#displayPreviousCaseNotes { border: 1px solid rgba({$this->color_zackgrey},1); width: 41vw; height: 15vh; overflow: auto; background: rgba({$this->color_white},1);  }
.noteTextLineTbl { width: 40vw; }
.noteTextLineText { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw 0 .3vw; text-align: justify;   }
.noteTextLineEntry { font-size: 1vh; color: rgba({$this->color_darkgrey},1); text-align: right; padding: 0 .3vw 0 .3vw; border-bottom: 1px solid rgba({$this->color_darkgrey},1); }
.noteTypeLine { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_darkgrey},1); padding: .3vh .0 .1vh .2vw;  }

#waitgif { width: 2vw; padding: 2vh 0;}
#waitIcon { display: none; width: 42.5vw; }
#waitinstruction { font-size: 1.8vh; color: rgba({$this->color_dblue},1); font-weight: bold; }
#displayEncounterDiv { display: block; }
#waitgifADD { width: 1.6vw; padding: 2vh 0; }
STYLESHEET;
return $rtnThis;
}

function scienceserverhelp($mobileind) { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 9vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }

#sshHoldingTable { border-collapse: collapse; width: 97vw; height: 87vh; }
#sshHoldingTable #head { height: 2vh; }
#sshHoldingTable #topicDivHolder { width: 25vw; }
#sshHoldingTable #divDspTopicList { height: 80vh; border-right: 1px solid rgba({$this->color_darkgrey},1); overflow: auto; }


#ssHelpFilesHeaderDsp { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.ssHlpModDiv { position: relative; }

#ticketCHolder { width: 1vw; }
#mainHelpFileHolder { position: relative; width: 25vw; }

.hlpModuleTbl { font-size: 1.7vh; color: rgba({$this->color_zackgrey},1); }

.iconholdercell {width: 1vw; }

.topicicon {font-size: 1.6vh; padding: .3vh .3vw .3vh 1vw; }
.hlpTopicDiv table tr td { padding: .5vh 0 .5vh 0; }
.hlpTopicTbl { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); width: 100%; }
.hlpTopicTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }

.hlpFunctionDiv table tr td {padding: .5vh 0 .5vh 0; }
.hlpFuncTbl { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); width: 100%; }
.hlpFuncTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }

.funcIcon {font-size: 1.8vh; padding: 0 0 0 2vw; }
#instructionDiv { font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; padding: 10vh 0 3vh 0; }

.helpticket { font-size: 1.5vh; }

#hlpMainHolderDiv {padding: 0 1vw 0 1vw; height: 75vh; overflow: auto; } 
#hlpMainTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; padding: .5vh 0 .8vh 0; }
#hlpMainSubTitle { width: 100%; font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; padding: .5vh 0 .8vh 0; }
#hlpMainByLine { width: 100%; font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_darkgreen},1); text-align: right; padding: .8vh 0 .8vh 0; }
#hlpMainText { width: 100%; font-family: Roboto; font-size: 1.8vh; line-height: 1.8em; text-align: justify; padding: 1vh 0 0 0; }

#resultsSearchTbl { width: 100%; }
#resultsSearchTbl #title { font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh 0; text-align: center; border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
#resultsSearchTbl #itemsfound { font-size: 1.6vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh .8vw; } 
#resultsSearchTbl #bywhowhen { font-size: 1.1vh; font-style: italic; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh .8vw; }

#resultsSearchTbl .zoogleTbl { width: 100%;  }
#resultsSearchTbl .zoogleTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#resultsSearchTbl .zoogleTbl .zoogleTitle { font-size: 1.5vh; color: rgba({$this->color_cornflowerblue},1); font-weight: bold; padding: 1vh 3vw 0 5vw; }
#resultsSearchTbl .zoogleTbl .zoogleURL { font-size: 1.1vh; color: rgba({$this->color_darkgreen},1); padding: 0 3vw .3vh 5vw; }
#resultsSearchTbl .zoogleTbl .zoogleAbstract { font-size: 1.2vh; color: rgba({$this->color_zackgrey},1); padding: .3vh 25vw 1vh 5vw;text-align: justify; line-height: 1.8em; } 

.screenShotDspDiv { position: relative; clear: left; }

STYLESHEET;
return $rtnThis;
}

function reports($mobileind) { 
      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }

#reportDefinitionTbl { width: 98vw; height: 85vh; box-sizing: border-box; border: 1px solid rgba({$this->color_zackgrey},1);  }
#reportIdentification { background: rgba({$this->color_grey},1); height: .2vh; }
#defHead { font-size: 1.8vh;  }
#reportFooterBar { background: rgba({$this->color_grey},1); height: .2vh; }
#recordsDisplay { width: 98vw; box-sizing: border-box; height: 74vh; overflow: auto; }

.rptGroupBtn { width:  12vw; height: 10vh; box-sizing: border-box; border: 1px solid rgba({$this->color_zackgrey},1);   }
.rptGroupBtn:hover {background: rgba({$this->color_cornflowerblue},1); cursor: pointer; }
.rptGroupBtn:hover .rptGrpTitle  { color: rgba({$this->color_white},1); }
.rptGroupBtn:hover .rptGrpDesc  { color: rgba({$this->color_white},1); }
.rptGrpTitleBox {width: 12vw; }
.rptGrpTitle {font-size: 1.5vh; text-align: center; rgba({$this->color_dblue},1); }
.rptGrpDesc { font-size: 1.1vh; rgba({$this->color_dblue},1); text-align: center; }

#reportListBox {width: 98vw; border-collapse: collapse; }
#reportListBox #bigTitle {font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_ddrwgrey},1); }
#reportListBox #bigDesc {font-size: 1.5vh; color: rgba({$this->color_ddrwgrey},1); border-bottom: 2px solid rgba({$this->color_ddrwgrey},1); }
#reportListBox #bigFound {font-size: 1.2vh; color: rgba({$this->color_ddrwgrey},1); text-align: right;  }

.reportListBtn { width:  10vw; height: 9vh; box-sizing: border-box; border: 1px solid rgba({$this->color_zackgrey},1);   }
.reportListBtn:hover {background: rgba({$this->color_cornflowerblue},1); cursor: pointer; }
.reportDefInnerTbl {border-collapse: collapse; }
.reportDefInnerTbl .rptTitle  {font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_dblue},1); text-align: center;}
.reportDefInnerTbl .rptDescription {font-size: 1.1vh; color: rgba({$this->color_dblue},1); text-align: center; padding: 0 .5vw 0 .5vw;}

.reportListBtn:hover .reportDefInnerTbl .rptTitle {color: rgba({$this->color_white},1); }
.reportListBtn:hover .reportDefInnerTbl .rptDescription { color: rgba({$this->color_white},1); }



#reportResultDataTbl { font-size: 1.4vh; border-collapse: collapse;    }
#reportResultDataTbl th {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .4vh .4vw;border-bottom: 1px solid rgba({$this->color_white},1); border-right: 1px solid rgba({$this->color_white},1);}
#reportResultDataTbl tbody tr:nth-child(even) {background: rgba({$this->color_lightgrey},1); }
#reportResultDataTbl tbody td {border-bottom: 1px solid rgba({$this->color_grey},1); border-right: 1px solid rgba({$this->color_grey},1); padding: .5vh .4vw; }

#reportresultitemfound { font-size: 1.3vh; color: rgba({$this->color_ddrwgrey},1); }

#jsonToExport {display: none; }


STYLESHEET;
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

function hprreview($mobileind) { 
    
$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 8vh; box-sizing: border-box;  }

input {width: 25vw; box-sizing: border-box; font-family: Roboto; font-size: 1.4vh;color: rgba({$this->color_zackgrey},1); padding: .8vh .5vw .8vh .5vw; border: 1px solid rgba({$this->color_mgrey},1);  }
input:focus, input:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }

#hprInnerScan { position: absolute; width: 25vw; height: 16vh; left: 50%; top: 50%; transform: translate(-50%, -50%);  }
#hprInnerScan table tr th {font-size: 1.5vh; text-align: left; }
#hprwbHeadErrorHolder { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); Text-align: center; } 

#masterWorkBenchTbl { width: 99vw; height: 90vh; }
#masterWorkBenchTbl #sidePanelTD {  }
#masterWorkBenchTbl #sidePanelTD #sidePanel {width: 15vw; height: 90vh; box-sizing: border-box;  overflow: auto;border: 1px solid rgba({$this->color_zackgrey},1); }
#masterWorkBenchTbl #workBenchTD {width: 84vw; height: 85vh; box-sizing: border-box; }
#masterWorkBenchTbl #workBenchTD #workBench {width: 84vw; height: 85vh; box-sizing:  }

#masterWorkBenchTbl #sidePanelTD #sidePanel #sidePanelSlideListTbl {width: 100%; border-collapse: collapse; box-sizing: border-box; }

.workbenchheader { font-size: 1.5vh; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.workbenchheaderconfirm { font-size: 1.5vh; background: rgba({$this->color_darkgreen},1); color: rgba({$this->color_white},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.workbenchheaderadd { font-size: 1.5vh; background: rgba({$this->color_dullyellow},1); color: rgba({$this->color_dblue},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.workbenchheaderdeny { font-size: 1.5vh; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.workbenchheaderincon { font-size: 1.5vh; background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }
.workbenchheaderunuse { font-size: 1.5vh; background: rgba({$this->color_bred},1); color: rgba({$this->color_white},1); padding: .8vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); }

#masterWorkBenchTbl #sidePanelTD #sidePanel #sidePanelSlideListTbl .slidesfound { font-size: 1.2vh; text-align: right; background: rgba({$this->color_white},1); color: rgba({$this->color_zackgrey},1); padding: 1vh .3vw 0 0; border-top: 1px solid rgba({$this->color_zackgrey},1);  }
#masterWorkBenchTbl #sidePanelTD #sidePanel #sidePanelSlideListTbl .rowHolder { padding: .3vh 0 ; }
#masterWorkBenchTbl #sidePanelTD #sidePanel #sidePanelSlideListTbl .rowBacker:nth-child(even) { background: rgba({$this->color_grey},1);  }
#masterWorkBenchTbl #sidePanelTD #sidePanel #sidePanelSlideListTbl .rowBacker:hover { background: rgba({$this->color_lamber},1); cursor: pointer; }

.slide { width: 14.8vw; }
.slide .slidecountr { font-size: 2vh; font-weight: bold; text-align: center; width: 2vw; }
.slide .slidenbr { font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.slide .slidedesignation { font-size: 1.3vh; text-align: justify; }
.slide .slidedate { font-size: 1vh; white-space: nowrap;}
.slide .slidetech { font-size: 1vh; text-align: right; white-space:nowrap;}
.slide .slidefreshdsp { font-size: 1.2vh; color: rgba({$this->color_bred},1); font-weight: bold; text-align: center; }

#buttonStripHolder { width: 2vw; }

#workBenchHolding { width: 83.8vw; height: 85vh; box-sizing: border-box;  }
#workBenchPrelimInfoHold { width: 35vw; padding: 0 0 0 0;  box-sizing: border-box; height: 85vh; overflow: hidden; }
#workBenchTD { width: 35vw; box-sizing: border-box; height: 85vh; overflow: hidden; }
#divWorkBenchPrelimInfo {border: 1px solid rgba({$this->color_zackgrey},1); height: 45vh; overflow: auto;  }

#workBenchDisplayHolder {position: relative; box-sizing: border-box;  }
#divWorkBenchPathRptDsp {border: 1px solid rgba({$this->color_zackgrey},1); display: block; width: 50vw; }
#reviewersWorkBenchConfirm { display: none;}
#reviewersWorkBenchAdd { display: none;}
#reviewersWorkBenchDeny { display: none; }
#reviewersWorkBenchIncon { display: none; }
#reviewersWorkBenchUnuse { display: none; }

#hprPathRptTextDsp { height: 86vh; overflow: auto; padding: .8vh .3vw 1vh .3vw; box-sizing: border-box; font-size: 1.3vh; line-height: 1.8em; text-align: justify; }
#allSegmentDsp { margin-top: .8vh; border: 1px solid rgba({$this->color_zackgrey},1); }
#allSegHolder { height: 11vh; overflow: auto; }
#constituentTbl tr:nth-child(odd) { background: rgba({$this->color_grey},1); }
#constituentTbl .conDataCell { font-size: 1.2vh; padding: .5vh .3vw .5vh .3vw; border-bottom: 1px solid rgba({$this->color_mgrey},1); }

#pastHPRDsp { margin-top: .8vh; border: 1px solid rgba({$this->color_zackgrey},1); }
.prntbtn { font-size: 1.6vh; }
.prntbtn:hover { cursor: pointer; color: rgba({$this->color_neongreen},1); } 

.littleFieldLabel { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_lgrey},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); padding: .5vh 0 .5vh .3vw; border-right: 1px solid rgba({$this->color_mgrey},1);} 
.littleFieldLabelEnd { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_lgrey},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); padding: .5vh 0 .5vh .3vw; } 
.littleFieldLabelWork { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh 0 .5vh .4vw; } 
.dataFieldDsp { font-size: 1.2vh; background: rgba({$this->color_white},1); padding: .8vh .3vw; border-bottom: 1px solid rgba({$this->color_mgrey},1); border-right: 1px solid rgba({$this->color_mgrey},1);  }
.dataFieldDspEnd { font-size: 1.2vh; background: rgba({$this->color_white},1); padding: .8vh .3vw; border-bottom: 1px solid rgba({$this->color_mgrey},1);   }


.hoverRow:hover {background: rgba({$this->color_lamber},1); cursor: pointer; }
.fieldHolder {padding: 0 0 .5vh .1vw; }

#divHPRConfirm {border: 1px solid rgba({$this->color_zackgrey},1); width: 50vw; }

.sideActionBtn { position: relative; }
.sideActionBtn .iconGraphic {    }
.sideActionBtn .iconGraphic .material-icons { font-size: 2vh; color: rgba({$this->color_grey},1); }
.sideActionBtn .btnExplainer { display: none; position: absolute; z-index: 40; white-space: nowrap; background: rgba({$this->color_grey},1); padding: .5vh .5vw; font-size: 1.2vh; left: 1vw;  }

.sideActionBtn:hover { cursor: pointer; }
.sideActionBtn:hover .iconGraphic .material-icons { color: rgba({$this->color_darkgreen},1); }
.sideActionBtn:hover .btnExplainer { display: block;  }

#hprFldSiteConfirm { width: 24vw; }
#hprFldSubSiteConfirm {width: 12vw; }
#hprFldSpecCatConfirm { width: 13vw; }

#hprFldDiagnosisConfirm {width: 24.8vw; }
#hprFldDXModifierConfirm {width: 24.7vw; }
#hprFldMetsSiteConfirm {width: 20.8vw; }
#hprFldMetsDXConfirm {width: 28.8vw; }

#hprFldPRCTumorConfirm {width: 7vw; text-align: right; }
#hprFldPRCCellConfirm {width: 7vw; text-align: right; }
#hprFldPRCNecroConfirm {width: 7vw; text-align: right; }
#hprFldPRCNeoPlasticConfirm {width: 7vw; text-align: right; }
#hprFldPRCNonNeoConfirm {width: 7vw; text-align: right; }
#hprFldPRCACellConfirm {width: 7vw; text-align: right; }
#hprFldPRCEpiCellConfirm {width: 7vw; text-align: right; }
#hprFldPRCInflamConfirm {width: 7vw; text-align: right; }

#hprFldTechAcc {width: 16vw; }
#TechAccDropDown {width: 16vw;  font-size: 1.5vh;}
#hprFldBSCommentsConfirm { width: 16vw; height: 9vh; font-size: 1.3vh; }
#hprFldRareCommentsConfirm { width: 16vw; height: 9vh; font-size: 1.3vh; }
#hprFldMoleTest {width: 31vw; }
#moleTestDropDown {width: 31vw; }
#hprFldMoleResult {width: 15.5vw; }
#moleResultDropDown {width: 15vw; }
#hprFldMoleScale {width: 15vw; }

.moleaddbtn:hover {color: rgba({$this->color_darkgreen},1); cursor: pointer; }

#dspDefinedMolecularTestsConfirm { border: 1px solid rgba({$this->color_mgrey},1);  overflow: auto;  width: 31vw; box-sizing: border-box; height: 12vh;  }

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
#coordinatorResultTbl tbody .ttholder:hover .tt { display: block; text-align: left; }
#coordinatorResultTbl tbody .ttholder .infoIconDiv {float: left; }
#coordinatorResultTbl tbody .ttholder .infoTxtDspDiv {position: absolute; background: rgba({$this->color_aqua},1); min-width: 20vw;  max-width: 35vw; z-index: 40; left: 2vw; display: none; padding: 8px; box-sizing: border-box; font-size: 1.8vh; }
#coordinatorResultTbl tbody .ttholder .infoTxtDspDiv:after{content:'';position:absolute;border:15px solid transparent;border-top:15px solid rgba({$this->color_aqua},1); top:0px;left:-10px;}
#coordinatorResultTbl tbody .ttholder:hover .infoTxtDspDiv { display: block; }
#coordinatorResultTbl tbody .tt { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; white-space: nowrap; z-index: 40; }
#coordinatorResultTbl tbody .cntr { text-align: center; }
#coordinatorResultTbl tbody .groupingstart {border-left: 3px solid rgba({$this->color_zackgrey},1); }
#coordinatorResultTbl thead .groupingstart {border-left: 3px solid rgba({$this->color_white},1); }

.topBtnHolderCell .ttholder { position: relative; }
.topBtnHolderCell .ttholder:hover .tt { display: block; text-align: left; }
.topBtnHolderCell .tt { position: absolute; left: -.5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }
.topBtnHolderCell .tt .btnBarDropMenuItems { font-size: 1.5vh; }
.topBtnHolderCell .tt .btnBarDropMenuItems .btnBarDropMenuItem:hover { background: rgba({$this->color_lblue},1);  color: rgba({$this->color_white},1); cursor: pointer; } 

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

.quickLink { margin-top: .6vh; margin-bottom: .6vh; }
.quickLink:hover { color: rgba({$this->color_neongreen},1); }
.quickLink .qlSmallIcon {font-size: 1.2vh; }

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

#sdcSegmentListDiv {border: none; height: 72vh; overflow: auto; }

#segmentListHolder { width: 20vw; border-left: 1px solid rgba({$this->color_zackgrey},1); }

#qmsSldListTbl { font-size: 1.5vh; border-collapse: collapse; }
#qmsSldListTbl th { text-align: left; border-bottom: 1px solid rgba({$this->color_zackgrey},1); font-size: 1.2vh; font-weight: bold; padding: 1vh .5vw 0 .5vw; } 
#qmsSldListTbl tbody tr { border-bottom: 1px solid rgba({$this->color_mgrey},1); height: 7vh; box-sizing: border-box;}
#qmsSldListTbl tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
#qmsSldListTbl tbody tr:hover { background: rgba({$this->color_lamber},1); }
#qmsSldListTbl tbody tr td { border-right: 1px solid rgba({$this->color_mgrey},1); padding: 1.2vh .5vw 1.2vh .5vw; }
#qmsSldListTbl thead tr .fldHolder { height: 3vh; } 

#qmsSldListTbl tbody .sldOptionListTbl {font-size: 1.8vh; width: 9.9vw; }
#qmsSldListTbl tbody .sldOptionListTbl tr { background: rgba({$this->color_white},1); height: 1vh;}
#qmsSldListTbl tbody .sldOptionListTbl tr:hover { background: rgba({$this->color_white},1); cursor: pointer; }
#qmsSldListTbl tbody .sldOptionListTbl tr td { border: none; padding: 0; }
#qmsSldListTbl tbody .sldOptionListTbl .sldOptionClear {text-align: right; font-size: 1vh; font-weight: bold; color: rgba({$this->color_mgrey},1); }
#qmsSldListTbl tbody .sldOptionListTbl .sldOptionClear:hover { color: rgba({$this->color_bred},1); }

.sldLblFld { width: 6vw; font-size: 1.8vh; }
.sldusedfld { width: 1vw; text-align: center; }
.sldassignhpr { width: 1vw; text-align: center; }
.sldassigninv { width: 1vw; text-align: center; }

#PRUPHoldTbl {  }
#PRUPHoldTbl #fldDialogPRUPPathRptTxt {width: 83vw; height: 48vh; font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); line-height: 1.6em; text-align: justify; }
#PRUPHoldTbl #VERIFHEAD { font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_cornflowerblue},1); text-align: center; padding: .5vh 0 .5vh 0; }
#PRUPHoldTbl .lblThis {font-size: 1.3vh; white-space: nowrap; font-weight: bold; color: rgba({$this->color_zackgrey},1);border-right: 1px solid rgba({$this->color_grey},1); padding: 0 0 0 .2vw; }
#PRUPHoldTbl .dspVerif {font-size: 1.3vh; white-space: nowrap; color: rgba({$this->color_zackgrey},1); padding: .8vh 1vw .8vh .2vw;border-right: 1px solid rgba({$this->color_grey},1); border-bottom: 1px solid rgba({$this->color_grey},1); }
#PRUPHoldTbl .headhead { padding: 1vh 0 0 .5vw; font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);  }
#PRUPHoldTbl input[type=checkbox] { width: 1vw; }
#PRUPHoldTbl input[type=checkbox] + label { color: rgba({$this->color_bred},1); font-size: 1.5vh; text-align: justify; line-height: 1.8em; }
#PRUPHoldTbl input[type=checkbox]:checked + label { color: rgba({$this->color_darkgreen},1);font-size: 1.5vh; text-align: justify; line-height: 1.8em; font-weight: normal; }

#PRUPHoldTbl .ttholder { position: relative; }
#PRUPHoldTbl .ttholder:hover .tt { display: block; }
#PRUPHoldTbl .tt { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; z-index: 40; }



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
