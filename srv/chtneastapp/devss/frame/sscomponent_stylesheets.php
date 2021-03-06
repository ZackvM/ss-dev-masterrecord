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
  public $color_zackgrey_highlight = "87, 103, 128"; //#576780
  public $color_zackcomp = "235,242,255"; //#ebf2ff
  public $color_deeppurple = "107, 18, 102";
  public $color_aqua = "203, 232, 240";

  //Z-INDEX:  0-30 - Base - Level // 40-49  - Floating level // 100 Black wait screen // 100+ dialogs above wait screen 
  
function globalstyles($mobileInd) {

$at = genAppFiles;
$floorMap = base64file("{$at}/publicobj/graphics/sysgraphics/baseimages/floorMap.png", "background", "bgurl", true );   


$rtnThis = <<<STYLESHEET

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Bungee);
html {margin: 0; height: 100%; width: 100%; font-family: Roboto; font-size: 1vh; color: rgba({$this->color_black},1);}
 
.appcard { border-left: 1px solid rgba({$this->color_zackgrey},1); height: 100vh; width: 50vw; position: fixed; top: 0; left: 101vw; z-index: 48; padding: 11vh 0 0 0; background: rgba({$this->color_lgrey},1); transition: 1s; 
-webkit-box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);
-moz-box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);
box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);
 }
#standardModalBacker { position: fixed; top: 0; left: 0;  z-index: 100; background: rgba({$this->color_zackgrey},.7); height: 100vh; width: 100vw; display: none; }

#apptrayUserProfilePicture { width: 18vh;  }
.circular--portrait {
  position: relative;
  height: 17vh;
  width: 9vw;
  overflow: hidden;
  border-radius: 50%;
}

.hyperlink { color: rgba({$this->color_darkgreen},1); text-decoration: underline;  }
.hyperlink:hover { cursor: pointer; color: rgba({$this->color_deeppurple},1); }

#standardModalDialog {display: none; background: rgba({$this->color_lgrey},1); position: fixed; margin-left: -20vw; left: 50%; margin-top: -10vh; top: 50%; z-index: 101; border: 2px solid rgba({$this->color_white},1); }

#systemDialogClose { width: .5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 2vh;text-align: right; padding: .3vh .1vw; }
#systemDialogClose:hover { cursor: pointer; color: rgba({$this->color_bred},1); }

#systemDialogTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 1.3vh; padding: .3vh .3vw; width:100%; }

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
.menuDrpItems table .ico { padding: .6vh 1vw .6vh .8vw; width: .5vw; }
.menuDrpItems table tr:hover:not(.trseparator) { cursor: pointer; background: rgba({$this->color_lblue},1); }
.menuDrpItems table .trseparator .menuSeparator { border-top: 1px solid rgba({$this->color_darkgrey},.4); height: 1px; }
.menuDrpItems .menuicons { font-size: 1.8vh; } 

.bigspacer { width: 30%;}
.universeBtns { color: rgba({$this->color_white},1);  font-size: 1.8vh;  padding: 0 0 0 1vw;  white-space: nowrap; }
.universeBtns:hover {color: rgba({$this->color_neongreen},1); cursor: pointer; }
.universeFreeze { color: rgba({$this->color_cornflowerblue},1); }

#hlpHolderDiv { width: 48vw; padding: 0 1vw 0 1vw; height: 92vh; overflow: auto;}
#directoryDisplay { padding: 0 1vw 0 1vw; height: 92vh; overflow: auto;}
#clsBtnHold #closeBtn { font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; width: 2vw;  }
#clsBtnHold #closeBtn:hover { color: rgba({$this->color_bred},1); cursor: pointer; }
#hlpTitle { width: 100%; font-family: Roboto; font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpSubTitle { width: 100%; font-family: Roboto; font-size: 1.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#hlpByLine { width: 100%; font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_darkgreen},1); text-align: right; }
#hlpText { width: 100%; font-family: Roboto; font-size: 1.5vh; line-height: 1.6em; text-align: justify; padding: 1vh 0 0 0; }

#vocsrchHolderDiv { width: 48vw; padding: 0 1vw 0 1vw; height: 92vh; overflow: auto;}
#vsclsBtnHold #closeBtn { font-family: Roboto; font-size: 4vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; width: 2vw;  }
#vsclsBtnHold #closeBtn:hover { color: rgba({$this->color_bred},1); cursor: pointer; }
#vocsrchTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
.vocsrchFldLabel {font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_zackgrey},1); padding-top: 1vh; white-space: nowrap; }
#vocabSrchTermFld {width: 47vw; box-sizing: border-box; }
#srchVocRsltDisplay { width: 47vw; height: 70vh; overflow: auto; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); box-sizing: border-box; margin-top: 1vh; }

#vocabularyDisplayTable { width: 100%;   }
#vocabularyDisplayTable tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
#vocabularyDisplayTable .headercell { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); font-size: 1.3vh; font-weight: bold; padding: .5vh .5vw;    }
#vocabularyDisplayTable .datacell { font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .5vw; border-bottom: 1px solid rgba({$this->color_darkgrey},.5); border-left: 1px solid rgba({$this->color_darkgrey},.5); }
#vocabularyDisplayTable .vocDspSpeccat { width: 8vw; }
#vocabularyDisplayTable .vocDspSite { width: 10vw; }
#vocabularyDisplayTable .vocDspSSite { width: 8vw; }

/* APP TRAY ENVIRONMENTAL */
#titleholder { display: grid; grid-template-columns: 47vw 2vw; width: 49vw; box-sizing: border-box; }
#titleholder #apptrayEnvironTitle {  font-family: Roboto; font-size: 1.8vh; font-weight: bold; padding: .2vh 0 0 .3vw; font-family: Roboto;    } 
#titleholder #envCloseBtn { font-family: Roboto; font-size: 2.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; box-sizing: border-box; padding: 0 0 0 0; }
#titleholder #envCloseBtn:hover { color: rgba({$this->color_bred},1); cursor: pointer; }
#environmentalTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; }
#environHolderDiv {  height: 55vh; width: 49vw; box-sizing: border-box; background-repeat: no-repeat; background-attachment: fixed; background: {$floorMap} no-repeat top left; background-size: 94vh; position: relative; }
#environHolderDiv .tempBox { border: 1px solid rgba({$this->color_dblue},1); width: 5vw; background: rgba({$this->color_white},.7); position: absolute; padding: 2px;  }
#environHolderDiv .tempBox .tempBoxTitle { background: rgba({$this->color_dblue},.6); color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1vh; text-align: center; font-weight: bold; overflow: hidden; white-space: nowrap; } 
#environHolderDiv .tempBox .tempBoxValue { border: 1px solid rgba({$this->color_dblue},1); padding: 2px; text-align: center; font-size: 1.3vh; font-weight: bold;     }  

#enviroDetails { border: 1px solid #000; width: 49vw; box-sizing: border-box; height: 28vh; overflow: auto;   } 

#environmentalReadingsHolder { } 

#sensorDspHolder { width: 48vw; border: 1px solid rgba({$this->color_darkgrey},.5);}
#sensorDspHolder #sensorNbr { font-size: 1.3vh; font-weight: bold; text-align: right; color: rgba({$this->color_darkgrey},.5); padding: .8vh .3vw;  }
#sensorDspHolder .holdercell { box-sizing: border-box; width: 23vw; padding: .8vh .3vw .8vh .3vw; }

.sensorMetricTbl {border-collapse: collapse; width: 100%; border: 1px solid rgba({$this->color_zackgrey},1);  background: rgba({$this->color_white},1); }
.sensorMetricTbl .sensorDspName { background: rgba({$this->color_cornflowerblue},1); font-size: 1.7vh; padding: .6vh .4vw; color: rgba({$this->color_white},1);  }
.sensorMetricTbl .lastread { font-size: 1.1vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: right; padding: 0 .4vw .6vh .4vw; }
.sensorMetricTbl .rowColor:nth-child(odd) { background: rgba({$this->color_lightgrey},1); }
.sensorMetricTbl .sensorTime {font-size: 1.7vh; padding: .3vh .4vw; width: 2vw;  }
.sensorMetricTbl .sensorValue {font-size: 1.7vh; padding: .3vh .4vw .3vh 1vw; width: 3.4vw;  }
.sensorMetricTbl .utcValue { font-size: 1.1vh; text-align: right; }
.sensorMetricTbl .trendIconDsp { width: 1vw; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_lamber},1); }
.uparrow { font-size: 1.7vh; color:  rgba({$this->color_darkgreen},1); font-weight: bold;}

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

.profTrayFieldLabel { font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); padding: 8px 0 0 0; }
.dataDisplay { font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); padding: .2vh 1vw 2vh .2vw; }
.profTraySideFieldLabel { font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 8px 0 0 0; }
.dataDisplay2 { font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); padding: 8px 0 0 5px; }  

#profTrayUserName { font-size: 2.8vh; font-weight: bold; color: rgba({$this->color_dblue},1); padding: 0 1vw; }
#profTrayUserEmail {font-size: 1.8vh; color: rgba({$this->color_dblue},1); padding: 0 1vw; }
#profTrayPictureHold {padding: 0 0 0 3vw; }

#profTrayControlDivHoldTbl { width: 46vw; border-collapse: collapse; }
#profTrayControlDivHolder { border-top: 1px solid rgba({$this->color_zackgrey},1); padding: 2.5vh 1.5vw; height: 55vh; overflow: auto;  }

#profTrayControlAbtMe { padding: 10px; display: block; }
#profTrayControlAccess { padding: 10px; display: none;  }
#profTrayControlManage { padding: 10px; display: none; }

#profTrayDisplayAltYN { width: 6vw; font-size: 1.3vh;  }
#ddprofTrayDisplayAltYN {min-width: 6vw; }

#profTrayAMEmail { font-size: 1.3vh; width: 15vw; }
#profTrayAMDBID { font-size: 1.3vh; width: 5vw; }
#profTrayOfficePhn { font-size: 1.3vh; width: 11vw; } 
#profTrayAltPhone { font-size: 1.3vh; width: 11vw; }

#profTrayAltEmail { font-size: 1.3vh; width: 17vw; }
#profTrayAccessLvl { font-size: 1.3vh; width: 12vw; }
#profTrayProfilePicture {font-size: 1.3vh; width: 19vw; }
#profTrayCC {font-size: 1.3vh; width: 14vw; }
#ddprofTrayCC { min-width: 14vw; }

#profTrayPresentInst { font-size: 1.3vh; width: 18vw; }
#profTrayDLExp { font-size: 1.3vh; width: 10vw;}

#profTrayResetCode { font-size: 1.3vh; }
#profTrayCurrentPW { font-size: 1.3vh; }
#profTrayNewPW {font-size: 1.3vh; }
#profTrayConfirmPW {font-size: 1.3vh; }
#passwordexpireNotice { font-size: 1.5vh; color: rgba({$this->color_darkgreen},1); padding: 1vh 0 0 0; font-weight: bold; }
#passwordexpireNoticeRed { font-size: 1.5vh; color: rgba({$this->color_bred},1); padding: 1vh 0 0 0; font-weight: bold; }

.expireDayNoticeRed { color: rgba({$this->color_bred},1); font-weight: bold;text-decoration: underline overline;  }
.expireDayNoticeGreen { color: rgba({$this->color_neongreen},1); }

#changePWInstr { font-size: 1.3vh; }
#profTrayAltUnlockCode { width: 17vw; font-size: 1.3vh; }


/* GENERAL CONTROLS */
.fldLabel {font-family: Roboto; font-size: 1.4vh;  color: rgba({$this->color_zackgrey},1); padding-top: 1vh; }
input {width: 25vw; box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1);  }
input:focus, input:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }
textarea { box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1); resize: none; }
.pageTitle {font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);}

input[type=checkbox] { width: 2vw; }
input[type=checkbox] + label { color: rgba({$this->color_lblue},1); }
input[type=checkbox]:checked + label { color: rgba({$this->color_bred},1); font-weight: bold; }

.tblBtn tr td { font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1);  }
.tblBtn tr td:hover { background: rgba({$this->color_mblue},1); color: rgba({$this->color_white},1); cursor: pointer; }
.tblBtn tr td.active { background: rgba({$this->color_darkgreen},1); }
.tblBtn[data-aselected='1'] tr td { background: rgba({$this->color_darkgreen},1); color: rgba({$this->color_white},1); cursor: pointer; }
.tblBtn[data-hselected='true'] tr td { background: rgba({$this->color_bred},1); color: rgba({$this->color_white},1); }

/* DROP DOWN TABLES */
.menuHolderDiv { position: relative; }
.menuHolderDiv:hover .valueDropDown { display: block; cursor: pointer; }
.valueDropDown {background: rgba({$this->color_white},1);position: absolute; border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; margin-top: .07vh; min-height: 15vh; max-height: 33vh; overflow: auto; min-width: 10vw; width: 20vw;  display: none; z-index: 25; }
.menuDropTbl { font-size: 1.8vh; padding: .6vh .1vw .6vh .1vw; white-space: nowrap; background: rgba({$this->color_white},1); width: 100%; }

.inputiconcontainer { position: relative; }
.inputmenuiconholder { position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: right; padding: 9px 6px; box-sizing: border-box; }
.menuDropIndicator { font-size: 2vh; color: rgba({$this->color_grey},1); }
.menuHolderDiv:hover .menuDropIndicator { color: rgba({$this->color_cornflowerblue},1); }

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
.masterFloatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 

#appCardHelpFileHolder { height: 82vh; overflow: auto; padding: 0 1vw 1vh 1vw; box-sizing: border-box; } 
#appCardHelpFileHolder #hDocType {font-family: Roboto; font-size: 1vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: left; margin: 0 0 .8vh 0; }
#appCardHelpFileHolder #hDocTitle { font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: left; }
#appCardHelpFileHolder #hDocSTitle { font-family: Roboto; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); text-align: left; border-bottom: 2px solid rgba({$this->color_zackgrey},1); padding-bottom: .4vh; margin-bottom: 1vh; }
#appCardHelpFileHolder #hDocText { font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); line-height: 1.5em; text-align: justify; } 
.hlpSectionDspNbr { font-size: 1.5vh; font-weight: bold; margin: 2vh 0 1vh 0; border-bottom: 1px solid rgba({$this->color_zackgrey},.3);      }   

#hDocMetricsHolder { margin: 2vh 0 3vh 0; clear: both; }
#hDocMetricsHolder #metricBox { border: 1px solid rgba({$this->color_zackgrey},1); padding: .3vh .2vw; text-align: left;  } 
#hDocMetricsHolder #metricBox #metricTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.3vh; font-weight: bold; padding: .4vh .3vw; text-align: left; grid-column: span 6;  }
#hDocMetricsHolder #metricBox .mElemHld { box-sizing: border-box; display: grid; grid-template-columns: 1fr 2fr 2fr 3fr; grid-gap: .2vw; }
#hDocMetricsHolder #metricBox .mElemHld .hdiv { border: 1px solid rgba({$this->color_zackgrey},.4); margin-top: .2vh; }
#hDocMetricsHolder #metricBox .mElemHldMod { border: 1px solid rgba({$this->color_zackgrey},.4); box-sizing: border-box; font-family: Roboto; font-size: 1.5vh; padding: .2vh .3vw;  }
#hDocMetricsHolder #metricBox .mElemHldMod .cGreen { color: rgba({$this->color_darkgreen},1); font-weight: bold; }
#hDocMetricsHolder #metricBox .mElemHld .mElemLbl { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .3vh .2vw 0 .2vw; } 
#hDocMetricsHolder #metricBox .mElemHld .mElemDta { font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_zackgrey},1); padding: 0 .2vw .3vh .2vw; } 

STYLESHEET;

//FOR CHECKBOXES AND RADIO BUTTONS https://www.w3schools.com/howto/howto_css_custom_checkbox.asp
 return $rtnThis;
  }

  function chartreviewbuilder ( $mobileind ) { 

$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 10.5vh; margin-left: .2vw; margin-right: .2vw;   box-sizing: border-box; }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  }       
      

STYLESHEET;
return $rtnThis;

  }

  function scienceserverhelpdeskticketmanagement () { 
$rtnThis = <<<STYLESHEET

#mainPageHolder { margin: 8vh 1vw; } 
#mainPageHolder #workbench { display: grid; grid-template-columns: 30vw auto; grid-gap: .2vw; } 
#mainPageHolder #workbench #headr { grid-column: span 2; text-align: center; border-bottom: 2px solid rgba({$this->color_zackgrey},1); font-size: 1.8vh; padding: .5vh 0 0 0; font-variant: small-caps;   }

#openticketlisting { border: 1px solid rgba({$this->color_zackgrey},1); height: 50vh; overflow: auto;   }
#detailWorkbench { grid-row: span 2; border: 1px solid rgba({$this->color_zackgrey},1); padding: 0 .5vw; }

#dspticketnumber { font-size: 1.5vh; padding: 1vh 0 .4vh 0;   } 



#closeticketlisting { border: 1px solid rgba({$this->color_zackgrey},1); height: 25vh; overflow: auto;   }

.openticketticket { display: block; text-decoration: none; margin: 0 0 0 0; padding: .2vh .3vw .8vh .3vw; border-top: 1px solid rgba({$this->color_zackgrey},.6); background: rgba({$this->color_white},1); font-family: Roboto; color: rgba({$this->color_zackgrey},1); }

.opnTicketLine { font-size: 1.8vh;  }
.opnTicketReason { font-size: 1.5vh; }
.opnTicketMod { font-size: 1.5vh; }
.opnTicketBy {font-size: 1.2vh; } 
.ticketSqrHead { font-family: Roboto; font-size: 2vh; padding: 1vh 0 0 0; color:  rgba({$this->color_bred},1);    } 

.openticketticket:nth-child( even ) { background: rgba({$this->color_darkgreen},.1); }  
.openticketticket:hover { background: rgba({$this->color_cornflowerblue},.1); cursor: pointer; } 

#dspmetricline { display: grid; grid-template-columns: repeat( 5, 1fr); grid-gap: .2vw; padding: 0 .2vw; }
#dspmetricline .metricsqr { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .3vh .2vw; font-size: 1.4vh;  } 
#dspmetricline .metricsqr .label { font-weight: bold; }

#dspmetricline .issuetext { grid-column: span 5; }   
#dspmetricline .issuetext .label { font-weight: bold; font-size: 1.4vh; }
#dspmetricline .issuetext #itext { border: 1px solid rgba({$this->color_zackgrey},.4); height: 10vh; overflow: auto; padding: .2vh .2vw; text-align: justify; line-height: 1.5em; font-size: 1.4vh;   }

#fldtsts { width: 20vw; } 
#dspmetriclineA { display: grid; grid-template-columns: repeat( 3, 1fr); grid-gap: .2vw; padding: 0 .2vw; margin: .8vh 0 0 0; }
#dspmetriclineA .metricsqr { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .3vh .2vw; font-size: 1.4vh;  } 
#dspmetriclineA .metricsqr .label { font-weight: bold; }
#fldGitHub { width: 18vw; } 

#dspmetriclineB { display: grid; grid-template-columns: repeat( 1, 1fr); grid-gap: .2vw; padding: 0 .2vw; margin: .8vh 0 0 0; }
#dspmetriclineB .metricsqr { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .3vh .2vw; font-size: 1.4vh;  } 
#dspmetriclineB .metricsqr .label { font-weight: bold; }
#fldSolutionTitle { width: 100%;   } 

#btnBar { text-align: right; grid-column: span 5; margin: 1vh 0 0 0; }
#btnBar button { font-size: 1.4vh;     }

STYLESHEET;
return $rtnThis;
  }

  function continuousprocessimprovementtracker ( ) { 
$rtnThis = <<<STYLESHEET

#mainPageHolder { margin: 8vh 1vw; } 

STYLESHEET;
return $rtnThis;
  }

  function reportbuilder ( $mobileind ) { 
$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 10.5vh; margin-left: .8vw; margin-right: .8vw;   box-sizing: border-box; }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 

#pageContentHolder { min-height: 80vh; box-sizing: border-box; }
#wizardButtons { display: grid; grid-template-columns: repeat( 12, 1fr ); grid-gap: .2vw; } 
#wizardButtons .btnwiz { border: 1px solid rgba({$this->color_zackgrey},1); text-align: center; font-size: 1.3vh; line-height: 3vh; transition: all .5s ease;      } 
#wizardButtons .btnwiz:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.3); }
#wizardButtons .btnwiz[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }
#wizardButtons .nbrcirc { background: rgba({$this->color_dblue},1); padding: .1vh .3vw; border-radius: 30px; color: rgba({$this->color_white},1); font-weight: bold; } 

#rptworkarea { position: relative; }
.rptwork {  position: absolute; top: .3vh; left: 0; width: 100%; background: rgba({$this->color_white},1); min-height: 80vh; }

#rptWrk0 { display: block;  }
#rptWrk0 #titleLine { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; margin: 3vh 10vw .5vh 10vw;  }  
#rptWrk0 #instructions { font-size: 1.8vh; margin: 0 10vw; text-align: justify; } 

#rptWrk1 { display: none;  }
#rptWrk1 #titleLine { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_zackgrey},1); padding: .8vh .3vw; margin: .2vh 0 0 0; }  
#rptWrk1 #instrLine { font-size: 1.8vh; padding: 1.8vh; padding: .3vh 0; } 
#rptWrk1 #workbench { height: 65vh; overflow: auto; }
#rptWrk1 #workbench .schemaHolder { border: 1px solid rgba({$this->color_zackgrey},1); margin: .5vh 0 0 0; }
#rptWrk1 #workbench .schemaHolder:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.2); } 
#rptWrk1 #workbench .schemaHolder[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }
#rptWrk1 #workbench .schemaHolder .schemaname { font-size: 1.6vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .4vw 0 .4vw;   }
#rptWrk1 #workbench .schemaHolder .schemadesc { font-size: 1.4vh; color: rgba({$this->color_zackgrey},1); padding: 0 .4vw .5vw .4vw;   }  

#rptWrk2 { display: none;  } 
#rptWrk2 #titleLine { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_zackgrey},1); padding: .8vh .3vw; margin: .2vh 0 0 0; }  
#rptWrk2 #instrLine { font-size: 1.8vh; padding: 1.8vh; padding: .3vh 0; } 
#rptWrk2 #cworkbench { height: 65vh; overflow: auto; display: grid; grid-template-columns: repeat( 6, 1fr ); grid-gap: .2vw;   }
#rptWrk2 #cworkbench .dcontainer { font-size: 1.6vh; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); padding: .5vh 0; text-align: center; height: 6vh; transition: all .5s ease; display: grid; grid-template-rows: 2fr 1fr; }
#rptWrk2 #cworkbench .dcontainer:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.3); } 
#rptWrk2 #cworkbench .dcontainer[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }
#rptWrk2 #cworkbench .dcontainer .dcdesc { font-size: 1.1vh; } 

#rptWrk3 { display: none;  } 
#rptWrk3 #titleLine { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_zackgrey},1); padding: .8vh .3vw; margin: .2vh 0 0 0; }  
#rptWrk3 #instrLine { font-size: 1.8vh; padding: 1.8vh; padding: .3vh 0; }
#rptWrk3 #fworkbench { display: grid; grid-template-columns: 1fr;  }  
#rptWrk3 #fworkbench .topLabels { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-weight: bold; font-size: 1.3vh; padding: .3vh 0 .3vh .3vw;    } 
#rptWrk3 #fworkbench #availdataelementlist { border: 1px solid rgba({$this->color_zackgrey},1); height: 70vh; overflow: auto; }
#rptWrk3 #fworkbench #availdataelementlist .felement { margin: .3vh .2vw 0 .2vw; border: 1px solid rgba({$this->color_zackgrey},1); display: grid; grid-template-columns: 30fr 1fr; grid-gap: .2vw; }
#rptWrk3 #fworkbench #availdataelementlist .felement:hover  { cursor: pointer; background: rgba({$this->color_cornflowerblue},.3); }
#rptWrk3 #fworkbench #availdataelementlist .felement[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }
#rptWrk3 #fworkbench #availdataelementlist .felement .felemname { font-size: 1.7vh; padding: .5vh .3vw 0 .3vw;   } 
#rptWrk3 #fworkbench #availdataelementlist .felement .felemdesc { font-size: 1.5vh; padding: 0 .3vw .5vh .3vw; } 
#rptWrk3 #fworkbench #availdataelementlist .seqFld { grid-row: span 2; }
#rptWrk3 #fworkbench #availdataelementlist .seqFld .fldOrd { width: 2vw; text-align: center; font-size: 1.5vh; background: rgba({$this->color_white},1); margin: .5vh 0 .5vh 0; display: none; }    

#rptWrk4 { display: none;  }
#rptWrk4 #titleLine { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_zackgrey},1); padding: .8vh .3vw; margin: .2vh 0 0 0; }  
#rptWrk4 #instrLine { font-size: 1.8vh; padding: 1.8vh; padding: .3vh 0; }
#rptWrk4 #qworkbench { height: 70vh; overflow: auto; border: 1px solid rgba({$this->color_zackgrey},1); } 
#rptWrk4 #qworkbench .qLine { display: grid; grid-template-columns: 2vw 15vw 5vw 11vw auto; margin: 0 0 0 0; padding: .3vh 0 .3vh 0; border-bottom: 1px solid rgba({$this->color_zackgrey},1);   }
#rptWrk4 #qworkbench .qLine .qelement { font-size: 1.5vh; }
#rptWrk4 #qworkbench .qLine .defaultvalue { font-size: 1.3vh; width: 10vw; }    


#rptWrk5 { grid-template-columns: repeat( 3, 1fr ); grid-gap: .2vw; grid-template-rows: 1fr 15fr 2vh 7fr; display: none;}
#rptWrk5 #bAndSTitle { grid-column: span 3; padding: .8vh 0 .4vh 0 ; font-size: 1.8vh; border-bottom: 3px solid rgba({$this->color_zackgrey},1); display: grid; grid-template-columns: 5vw auto;    } 
#rptWrk5 #rptDef {  font-size: 1.8vh; border-bottom: 3px solid rgba({$this->color_zackgrey},1); height: 2vh; grid-column: span 3;  }
#rptWrk5 #defgrid { grid-column: span 3; display: grid; grid-template-columns: repeat( 5, 1fr ); grid-gap: .2vw; } 

#paraDataContainer { width: 100%; border: 1px solid rgba({$this->color_zackgrey},1); height: 50vh; font-size: 1.2vh; padding: .2vh .2vw; } 
#selectorBox { width: 100%; border: 1px solid rgba({$this->color_zackgrey},1); height: 50vh; font-size: 1.2vh;  padding: .4vh .2vw; }
#paraJSONList { width: 100%; border: 1px solid rgba({$this->color_zackgrey},1); height: 50vh; font-size: 1.2vh;  padding: .4vh .2vw; }
.rptSecHead { font-size: 2vh; padding: 2vh 0 0 0;      }


#menuOptionDspTitleBar { width: 20vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 1.4vh; font-weight: bold; padding: .6vh .4vw;  } 
#rptMenuOptionList { height: 50vh; overflow: auto; border: 1px solid rgba({$this->color_zackgrey},1); margin: .3vh .1vw; } 
.optoption { padding: .3vh .2vw; font-size: 1.4vh; }
.optoption:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.2); }
.optoption[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }

STYLESHEET;
return $rtnThis;
  }

   function useradministration ( $mobileind ) { 
$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 10.5vh; margin-left: .2vw; margin-right: .2vw;   box-sizing: border-box; }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 

#pageContentHolder { display: grid; grid-template-columns: 5fr 3fr; width: 100%; height: 90vh; grid-gap: .2vw;  box-sizing: border-box; }
#pageContentHolder .holderDivs { border: 1px solid rgba({$this->color_zackgrey},1); height: 87vh;  } 

#userListSide { overflow: auto; }
.checkboxThree { width: 5vw; height: 3vh; background: rgba( {$this->color_lamber}, 1 ); margin: 0; border-radius: 2px; position: relative; border: 1px solid rgba({$this->color_zackgrey},1); }
.checkboxThree:before { content: 'On '; position: absolute; top: .7vh; left: .5vw; height: .1vh; color: rgba({$this->color_darkgreen},1 ); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree:after  { content: 'Off'; position: absolute; top: .7vh; left: 3.2vw; height: .1vh; color: rgba({$this->color_bred},1); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree label  { display: block; width: 2vw; height: 1.7vh; border-radius: 50px; transition: all .5s ease; cursor: pointer; position: absolute; top: .7vh; z-index: 1; left: .5vw; background: rgba( {$this->color_zackgrey}, 1  ); }
.checkboxThree input[type=checkbox]:checked + label { left: 2.3vw; background: rgba( {$this->color_darkgreen}, 1 ); }
.checkboxThree .checkboxThreeInput { visibility: hidden; }

#userDisplayTbl { font-family: 'Roboto'; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); width: 100%; } 
#userDisplayTbl thead tr th { background: rgba({$this->color_zackgrey},1); font-size: 1.2vh; color: rgba({$this->color_white},1); padding: .5vh .3vw; }
#userDisplayTbl tbody tr:nth-child(even) { background: rgba({$this->color_darkgrey},.2); }
#userDisplayTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},.8); }

#userDisplayTbl tbody tr td { padding: .2vh .3vw; border-bottom: 1px solid rgba({$this->color_darkgrey},1); border-right: 1px solid rgba({$this->color_darkgrey},1);  } 
.uemail { font-size: 1.1vh; } 
.instWriterDsp { background: rgba({$this->color_aqua},1); padding: 1vh .3vw; } 


#defineUserSide #userSideHeader { background: rgba({$this->color_zackgrey},1); width: 100%; color: rgba({$this->color_white},1); font-family: Roboto; font-size: 2vh; padding: 1vh 1vw; box-sizing: border-box;  }

#mainPageHolder { display: grid; grid-template-columns: 20vw auto; box-sizing: border-box; }
#mainPageHolder #picDspBar { background: rgba({$this->color_lamber},.4);   } 
#profilePictHold { } 
.sidebarprofilepicture {width: 18vw; height: auto;  -webkit-box-shadow: 5px 5px 20px 7px rgba(0,0,0,0.1);box-shadow: 5px 5px 20px 7px rgba(0,0,0,0.1); border: 5px solid rgba({$this->color_white},1); border-radius: 10px; margin: .5vh 1vw .6vh 1vw;   }  


.dataElementHoldSide { margin: 0 0 0 1vw; } 
.dataElementHoldSide .dataElementLabelSml { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .3vh .2vw 0 .2vw; margin-top: 1vh; }
.dataElementHoldSide .dataElementDataFld { font-size: 1.5vh; border-bottom: 1px solid rgba({$this->color_zackgrey},1); padding: .3vh .2vw 0 .2vw; }  

#mainPageHolder #dataDspBar {  } 
#dataLineOne { display: grid; grid-template-columns: repeat( 5, 1fr ); grid-gap: .2vw; padding: 1vh 0 0 0; }
#dataLineTwo { display: grid; grid-template-columns: repeat( 3, 1fr ); grid-gap: .2vw; padding: 1vh 0 0 0; }
#dataLineThree { display: grid; grid-template-columns: repeat( 2, 1fr ); grid-gap: .2vw; padding: 1vh 0 0 0; }
#dataLineFour { display: grid; grid-template-columns: repeat( 5, 1fr ); grid-gap: .2vw; padding: 1vh 0 0 0; }
#dataLineFive { display: grid; grid-template-columns:  1fr 1fr; grid-gap: .2vw; padding: 1vh 0 0 0; }
#dataLineAllows { display: grid; grid-template-columns: repeat( 4, 1fr); grid-gap: .2vw; padding: 2vh .2vw 0 .2vw; }
#dataLineModsAndInsts { display: grid; grid-template-columns: repeat( 2, 1fr); grid-gap: .2vw; padding: 1vh .2vw 0 .2vw; } 
#instListHolder { height: 28vh; overflow: auto;     }
#modListHolder{ height: 28vh; overflow: auto;     }
.dataElementHold .dataElementLabel { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);  }
.dataElementHold .dataElementData { }
.dataElementHold .dataElementData input { font-size: 1.5vh; padding: .4vh .2vw;  }  

#updFldUserGender { width: 8vw; }
#dropDownUserGender { width: 12vw; } 

#updFldFriendly { width: 8vw; } 
#updFldLastName { width: 8vw; }
#updFldOriginalAcct { width: 8vw; }
#updFldDisplayName { width: 8vw; } 
#updFldUsersName { width: 8vw; } 
#updFldJobTitle { width: 16vw; } 
#updFldAccessLvl { width: 16vw; }
#dropDownAccLvl { width: 16vw; } 
#updFldOverridePIN { width: 6vw; } 
#updFldPrimaryInst { width: 21vw; }
#dropDownInst { width: 21vw; } 
#updFldPrimaryFunction { width: 19vw; } 
#updFldProfilePhone { width: 10vw; } 
#updFldSMSPhone { width: 8vw; } 
#updFldCellCarrier { width: 11vw; }
#dropDownCC { width: 11vw; }
#updFldDLExpire { width: 9vw; }
#updFldAltEmail { width: 25vw; } 


#allowHeader { grid-column: 1 / 5; margin: .5vh 0 0 0; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); text-align: center; font-size: 1.4vh; padding: .2vh 0; box-sizing: border-box;   }
.allowDspDiv { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_grey},1); display: grid; grid-template-columns: 1fr 1fr; } 
.allowLabelDsp {  font-size: 1.3vh; padding: .2vh .3vw; box-sizing: border-box;  } 

.modinst { border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; padding: .2vh .1vw;  } 
.allowHeaderInst { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); text-align: center; font-size: 1.4vh; padding: .2vh 0; box-sizing: border-box; }

.instListDiv { display: grid; grid-template-columns: 3fr 1fr; border-bottom: 1px solid rgba({$this->color_zackgrey},.5);  }
.iAllowHold { padding: .1vh 0 .1vh 0; }  
.iAllowName { font-size: 1.3vh; padding: .3vh 0 .3vh 0;     } 

.modListDiv { display: grid; grid-template-columns: 3fr 1fr; border-bottom: 1px solid rgba({$this->color_zackgrey},.5);  }
.mAllowHold { padding: .1vh 0 .1vh 0; }  
.mAllowName { font-size: 1.3vh; padding: .3vh 0 .3vh 0;     } 

#ufile { width: 18vw; font-size: 1.3vh; padding: 0;   } 






STYLESHEET;
return $rtnThis;

   }
  
  function furtheractionrequests ( $mobileind ) { 
$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 10.5vh; margin-left: .2vw; margin-right: .2vw;   box-sizing: border-box; }

.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 


.ttholder { position: relative; }
.ttholder .tt { position: absolute; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; z-index: 40; font-weight: normal; }
.ttholder:hover .tt { display: block; }
.displaytypetext { font-size: 1.8vh; white-space: nowrap; } 
.btnBarDropMenuItem:hover { color: rgba({$this->color_neongreen},1); cursor: pointer; } 

#headTitle { margin-left: .5vw; margin-right: .5vw; box-sizing: border-box; font-family: Roboto; font-size: 2vh; color: rgba({$this->color_zackgrey},1); font-weight: bold;  }


#displayHolder { display: grid; grid-template-columns: 6fr 1fr; } 

#furtherActionsQueHolder { border: 1px solid #000; margin-left: .5vw; margin-right: .5vw; box-sizing: border-box;  } 
#furtherActionsQueHolder #faTable { width: 100%; font-family: Roboto; font-size: 1.3vh; }
#furtherActionsQueHolder #faTable thead tr { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }
#furtherActionsQueHolder #faTable thead tr td { text-align: center; padding: 8px 5px; font-weight: bold; height: 3vh; } 
#furtherActionsQueHolder #faTable tbody tr:nth-child(even) { background: rgba({$this->color_darkgrey}, .4); }
#furtherActionsQueHolder #faTable tbody tr:hover { background: rgba({$this->color_lamber},.8); cursor: pointer; }
#furtherActionsQueHolder #faTable tbody tr td { height: 3vh; padding: 8px 5px; }   
#furtherActionsQueHolder #faTable tbody .agentdisplay { background: rgba({$this->color_white},1); cursor: default; } 
#furtherActionsQueHolder #faTable tbody .agentdisplay table { border-top: 2px solid rgba({$this->color_zackgrey},1); border-bottom: 2px solid rgba({$this->color_zackgrey},1); width: 100%; margin-top: 0; background: rgba({$this->color_cornflowerblue},.2); }
#furtherActionsQueHolder #faTable tbody .agentdisplay table tr td { padding: 0; text-align: center; font-size: 1.8vh; font-weight: bold; }  
#furtherActionsQueHolder #faTable tbody .completeline { background: rgba({$this->color_darkgreen},.3); cursor: default; } 
#furtherActionsQueHolder #faTable tbody .completeline table { width: 100%; }
#furtherActionsQueHolder #faTable tbody .completeline table tr td { padding: 0; text-align: center; font-size: 1.8vh; font-weight: bold; }  
#furtherActionsQueHolder #faTable tbody .completeline table tr td:hover { background: rgba({$this->color_darkgreen},.3); cursor: default; }  


#headInstructions { box-sizing: border-box; font-family: Roboto; font-size: 1.7vh; color: rgba({$this->color_zackgrey},1); position: fixed; right: 1vw; } 
#legendTbl { border: 1px solid rgba({$this->color_zackgrey},1); width: 13vw; }
#legendTbl tr td .material-icons {font-size: 2vh; } 
#legendTbl #legendHead { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 8px 0 8px 5px;  }

.nowork { color: rgba({$this->color_bred},1); } 
.somework { color: rgba({$this->color_cornflowerblue},1); } 
.donework { color: rgba({$this->color_darkgreen},1); } 



/* TICKET DISPLAY */

#ticketHolder { border: 1px solid rgba({$this->color_zackgrey},1); margin: 0 1vw; display: grid; grid-template-columns: repeat( 21, 1fr); box-sizing: border-box; padding: 0 0 1vh 0;   }
#ticketHolder #ticketHeadAnnounce { grid-column: 1 / 25; grid-row: 1; font-family: Roboto; font-size: 1.8vh; text-align: center; padding: .5vh 0; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_darkgrey},.5); } 

#ticketHolder .tDataDsp { border-bottom: 1px solid rgba({$this->color_darkgrey},.5); border-right: 1px solid rgba({$this->color_darkgrey},.5); } 
#ticketHolder .tDataDsp .tLabel { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh .5vw 0 .5vw; } 

.tData  { font-family: Roboto; font-size: 1.8vh;  color: rgba({$this->color_zackgrey},1); padding: 1.5vh .5vw .5vh .6vw; }
.tDataFld  { padding: .1vh .5vw .5vh .6vw; }
.tDataAct { font-family: Roboto; font-size: 2vh;  color: rgba({$this->color_darkgreen},1); font-weight: bold; padding: .1vh 0 .5vh .7vw; } 

#ticketHolder .tDataDspWide { border-bottom: 1px solid rgba({$this->color_darkgrey},.5); border-right: 1px solid rgba({$this->color_darkgrey},.5); grid-column: span 2;} 
#ticketHolder .tDataDspWide .tLabel { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh .5vw 0 .5vw; } 

#ticketHolder .tDataDspSuperWide { border-bottom: 1px solid rgba({$this->color_darkgrey},.5); border-right: 1px solid rgba({$this->color_darkgrey},.5); grid-column: span 3;} 
#ticketHolder .tDataDspSuperWide .tLabel { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh .5vw 0 .5vw; } 

#ticketHolder #wholeLineTwo { grid-column: 1 / 22; grid-row: 3; margin-top: 2vh; } 
#ticketHolder #wholeLineTwo .tLabel { font-family: Roboto; font-size: 1.4vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh .5vw 0 .5vw; } 

#ticketHolder #wholeLineThree { grid-column: 1 / 22; grid-row: 4; padding-left: .6vw; padding-bottom: .5vh;  } 
#ticketHolder #wholeLineThree textarea { font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 10px 5px 0; width: 95vw; height: 8vh; } 

#ticketHolder #divDivOne { grid-column: 1 / 22; grid-row: 6; font-family: Roboto; font-size: 1.8vh; text-align: center; padding: .5vh 0; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_darkgrey},.5);}  


#btnHolder { grid-column: 1 / 22; grid-row: 5; margin-top: 2vh; }
#btnHolder #innerbtnholder { width: 8vw; display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 5px; } 
#btnHolder #innerbtnholder .actionBtns { border: 1px solid rgba({$this->color_zackgrey},1); width: 4vw; font-family: Roboto; font-size: 1.5vh; text-align: center; padding: .8vh 0; color: rgba({$this->color_zackgrey},1); margin-bottom: 1vh; background: rgba({$this->color_cornflowerblue},.2); transition: .5s; }  
#btnHolder #innerbtnholder .actionBtns:hover { cursor: pointer; color: rgba({$this->color_white},1); background: rgba({$this->color_cornflowerblue},1); } 

#ticketHolder #actionGrid { grid-column: 1 / 22; grid-row: 7; box-sizing: border-box; padding: 1vh 1vw; font-family: Roboto; font-size: 1.3vh; color: rgba({$this->color_zackgrey},1);}
#ticketHolder #actionGrid table { width: 100%; font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1);   }
#ticketHolder #actionGrid table thead tr td { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 8px; font-weight: bold; }
#ticketHolder #actionGrid table .col1 { width: 30%; }  
#ticketHolder #actionGrid table .col3 { width: 20%; }
#ticketHolder #actionGrid table .col4 { width: 50%; }
#ticketHolder #actionGrid table tbody tr:nth-child(even) { background: rgba({$this->color_darkgrey}, .2); }
#ticketHolder #actionGrid table tbody tr:hover { background: rgba({$this->color_lamber},.8); cursor: pointer;  }
#ticketHolder #actionGrid table tbody tr td { padding: 4px 8px;  }  

#faFldRefBG { width: 8vw; }
#faFldRefSD { width: 8vw; }
#faFldRefHPR { width: 8vw; }
#faFldAssAgent { width: 13vw; } 
#bsqueryFromDate { width: 8vw; }
#ddHTRz { width: 15vw; } 

STYLESHEET;
//border-bottom: 1px solid rgba({$this->color_darkgrey},.7); border-right: 1px solid rgba({$this->color_darkgrey},.7);
return $rtnThis;      
  }
  
function qmsactions ( $mobileind ) { 
$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 10.5vh; margin-left: .2vw; margin-right: .2vw;   box-sizing: border-box; }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 


#dspQueTbl tbody tr td  { padding: 10px 0;  }
#dspQueTbl tbody tr:nth-child(even) { background: rgba({$this->color_darkgrey},.2); }
#dspQueTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},.8); }

.qmsQueItemTbl  { display: table;  }
.tblDspRow { display: table-row; }
.queDataLabel {display: table-cell; color: rgba({$this->color_zackgrey},1); font-weight: bold; font-size: 1.2vh;  width: 6vw; box-sizing: border-box; padding-right: 4px; }
.queDataDsp  { display: table-cell; color: rgba({$this->color_zackgrey},1); font-size: 1.5vh;  width: 70vw;  }

.queCellHolder { border: 1px solid rgba({$this->color_zackgrey},.3); padding: 0; }

.sideiconholder { width: 5vw; text-align: center; }
.hprdecisionicon { font-size: 4vh; }
.decision_DENIED { color: rgba({$this->color_bred},1); }
.decision_CONFIRM { color: rgba({$this->color_darkgreen},1); }
.decision_INCONCLUSIVE { color: rgba({$this->color_deeppurple},1); }
.decision_UNUSABLE { color: rgba({$this->color_ddrwgrey},1); }
.decision_ADDITIONAL { color: rgba({$this->color_cornflowerblue},1); }

#legendDsp { position: fixed; right: 1.5vw; width: 10vw; }
#legendTbl { width: 10vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.nbrDsp { text-align: right; padding: 0 3px 0 0;  }
.legendTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 8px 0; text-align: center;   } 
.dspgreen { color: rgba({$this->color_darkgreen},1); }  
.dspblue { color: rgba({$this->color_cornflowerblue},1); }  
.dspred { color: rgba({$this->color_bred},1); }  
.dspbrown { color: rgba({$this->color_ddrwgrey},1); }  
.dsppurple { color: rgba({$this->color_deeppurple},1); }  

#mainDspTbl { border: 1px solid rgba({$this->color_zackgrey},1); margin-left: 4vw;  } 
.tblTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 8px 0; text-align: center; font-size: 2vh;  }


.ttholder { position: relative; }
.ttholder .tt { position: absolute; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; z-index: 40; font-weight: normal; }
.ttholder:hover .tt { display: block; }
.displaytypetext { font-size: 1.8vh; white-space: nowrap; } 
.btnBarDropMenuItem:hover { color: rgba({$this->color_neongreen},1); cursor: pointer; } 


/* WORKBENCH */

#workbenchwrapper { display: grid; grid-gap: 5px; grid-template-rows: 42vh 42vh; }

#wbrowtwo { height: 42vh; box-sizing: border-box; display: grid; grid-template-columns: 28vw 28vw 43vw; grid-gap: 5px;  }
#wbrowtwo .dataHolderDiv:last-child { border-bottom: none; } 
   #wbrowtwo #wbpristine { height: 42vh; box-sizing: border-box; border: 1px solid rgba({$this->color_cornflowerblue},1); border-right: none; border-bottom: none; overflow: auto; } 
   #wbrowtwo #wbpristine #dataRowTwo { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }


  #wbrowtwo #wbrevieweddata { height: 42vh; box-sizing: border-box; border: 1px solid rgba({$this->color_cornflowerblue},1); border-right: none; border-bottom: none; overflow: auto;  }
    #wbrowtwo #wbrevieweddata #dataRowOne { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }

  #wbrowtwo #wbsupprtdata { height: 42vh; box-sizing: border-box; border: 1px solid rgba({$this->color_cornflowerblue},1); border-right: none; border-bottom: none; }
    #wbrowtwo #wbsupprtdata #dataRowThree {  background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }  
    
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold { display: grid; grid-template-columns: 1fr 2fr; }
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #tmrCompSide { grid-column: 1 / 2; } 
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #tmrCompSide #dataPercentHold { display: grid; grid-template-columns: 1fr 1fr; } 
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #tmrCompSide #dataPercentHold div { border: none; }

    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide { grid-column: 2 / 4; border-left: 1px solid rgba({$this->color_cornflowerblue},1); padding-left: 3px; margin-left: 2px; }
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide div {  }
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide #inputLine { display: grid; grid-template-columns: 1fr 1fr 1fr; border: none; }
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide #inputbuttondiv { padding: 6px 0 0 0; }
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide #inputbuttondiv button { border: 1px solid rgba({$this->color_cornflowerblue},1); background: rgba({$this->color_white},1); padding: .5vh 1vw; font-size: 1.5vh; margin-bottom: 1vh; color: rgba({$this->color_zackgrey},1);  }  
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide #inputbuttondiv button:active { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1);   }  
    #wbrowtwo #wbsupprtdata #tmrCompMoleHold #moleTstSide #inputbuttondiv button:hover  { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); cursor: pointer;   }  
    
    .dspDefinedMoleTests { border: 1px solid rgba({$this->color_zackgrey},.5); height: 20vh; overflow: auto; width: 25vw; } 

#wbrowthree { box-sizing: border-box; height: 45vh; border: 1px solid #000; border: 1px solid rgba({$this->color_cornflowerblue},1); border-right: none; border-bottom: none;  }
  #wbrowthree #dataRowFour { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }
  #wbrowthree #associativeTblDsp { font-size: 1.6vh; height: 42vh; overflow: auto; }
  #wbrowthree #associativeTblDsp .headerCell { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 5px; font-size: 1.3vh; }
  #wbrowthree #associativeTblDsp .cellTwo { width: 4vw; } 
  #wbrowthree #associativeTblDsp .cellThree { width: 60vw; } 
  #wbrowthree #associativeTblDsp .cellFour { width: 18vw; } 
  #wbrowthree #associativeTblDsp .cellFive { width: 18vw; } 
  #wbrowthree #associativeTblDsp .cellSix { width: 5vw; } 
  #wbrowthree #associativeTblDsp .cellSeven { width: 10vw; }
  #wbrowthree #associativeTblDsp .cellEight { width: 20vw; }
  #wbrowthree #associativeTblDsp .cellNine { width: 20vw; } 
  #wbrowthree #associativeTblDsp .dspDataCell { border-top: 2px solid rgba({$this->color_zackgrey},1); padding: 8px 4px; border-bottom: 1px solid rgba({$this->color_darkgrey},.6); border-right: 1px solid rgba({$this->color_darkgrey},.6); font-size: 1.5vh; }
  #wbrowthree #associativeTblDsp .dspDataCellA { padding: 8px 4px; border-bottom: 1px solid rgba({$this->color_darkgrey},.6); border-right: 1px solid rgba({$this->color_darkgrey},.6); font-size: 1.5vh; font-size: 1.3vh; }
  #wbrowthree #associativeTblDsp .mintbck { background: rgba({$this->color_darkgreen},.2); color: rgba({$this->color_zackgrey},1); }
  #wbrowthree #associativeTblDsp .standardbck { color: rgba({$this->color_zackgrey},1); }
  #wbrowthree #associativeTblDsp .inassdsp { padding: .5vh 3vw 3vh 3vw; display: none;  }
  #wbrowthree #associativeTblDsp .inassdspthisgroup { padding: .5vh 3vw 3vh 3vw; display: block;  }


#fldPRCUnInvolved { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 15vw; }
#fldTumorGrade { font-size: 1.5vh;  color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 2.7vw; text-align: right; margin-right: .2vw; }
#fldTumorGradeScale { font-size: 1.5vh;  color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 12vw; }
#fldMoleTest { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 11vw; }
#hprFldMoleResult { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 8vw; }
#hprFldMoleScale { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw; width: 6vw; }

.prcDisplayValue { font-size: 1.5vh;  color: rgba({$this->color_dblue},1); padding: .5vh .3vw; width: 7vw; text-align: right; }
.smlFont { font-size: 1vh; color: rgba({$this->color_bred},1); }

.blueheader { box-sizing: border-box; background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.5vh; font-weight: bold; }
.iconind { } 
.iconind:hover { cursor: pointer; color: rgba({$this->color_neongreen},1); } 
.cmtdsp { text-align: justify; padding-right: 4px; } 

.makeBtn { border: 1px solid rgba({$this->color_cornflowerblue},1); background: rgba({$this->color_white},1); color: rgba({$this->color_cornflowerblue},1); padding: .5vh .5vw; transition: .5s; }
.makeBtn:hover { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); cursor: pointer; }


#pathologyrptdisplay { z-index: 48; position: fixed; top: 10vh; left: -50vw; background: rgba({$this->color_white},1); border: 4px solid rgba({$this->color_zackgrey},1); width: 45vw; height: 80vh; padding: 0; display: grid; grid-template-rows: 3vh 74vh 3vh; transition: 1s; }  
  #pathologyrptdisplay #pathologyreporttextdisplay {  height: 74vh; box-sizing: border-box; padding: 8px 16px; overflow: auto; font-size: 1.8vh; line-height: 1.8em; text-align: justify;     } 
  #pathologyrptdisplay #uploadline { text-align: right; box-sizing: border-box; padding: 8px; background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.3vh;     }

.actionbtnicon { color: rgba({$this->color_zackgrey},1); font-size: 1.8vh; transition: .5s; }
.actionbtnicon:hover { cursor: pointer; color: rgba({$this->color_cornflowerblue},1);  }

.dataHolderDiv { box-sizing: border-box; padding: 0 0 0 4px; border-bottom: 1px solid rgba({$this->color_cornflowerblue},.5); } 
.dataHolderDiv .datalabel { font-size: 1.2vh; font-weight: bold; padding: 8px 0 0 0; box-sizing: border-box; color: rgba({$this->color_darkgrey},.8);  }
.dataHolderDiv .datadisplay { font-size: 1.5vh; box-sizing: border-box; color: rgba({$this->color_zackgrey},1); padding: 5px 0 4px 10px; } 
.dataHolderDiv .datadisplayA { font-size: 1.5vh; box-sizing: border-box; color: rgba({$this->color_zackgrey},1); padding: 2px 0 0 0; } 

.divLineHolder { display: grid; grid-template-columns: repeat( 2, 1fr);  }
.assignNamedsp { grid-column: 1 / 2; white-space: nowrap; }

.alignerRight { display: flex;  }

.divLineHolderSD { display: table; width: 100%; }
.divLineHolderSD .divLineHolderSDRow { display: table-row; }
.divLineHolderSD .divLineHolderSDRow div { display: table-cell;  }

.bgheadsee { display: table; }
.bgheadsee div { display: table-cell; }


#thisworkingtable { }
#thisworkingtable tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#thisworkingtable tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#thisworkingtable tbody tr[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }


.suggestionHolder { position: relative; }
.suggestionDisplay {min-width: 25vw; position: absolute; left: 0; max-height: 30vh; min-height: 15vh; overflow: auto; z-index: 25; box-sizing: border-box; display: none;background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionTable { max-width: 24vw; box-sizing: border-box; }

.attentionGetter {font-size: 1.6vh; text-align: center; padding-top: 10px; padding-bottom: 6px; background: rgba({$this->color_bred},1);  font-weight: bold; color: rgba({$this->color_white},1);  }


/* INCONCLUSIVE WORKBENCH */ 
#inconWorkbenchWrapper { display: grid; grid-template-columns: 1fr 3fr; grid-gap: 5px; }
#inconWorkbenchWrapper #inconSideBar #dataRowOne { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }


#associativemess { grid-row: 2; grid-column: 1 / 4; box-sizing: border-box;  border: 1px solid rgba({$this->color_zackgrey},1);   }
  #associativemess #dataRowFour { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; text-align: center; padding: 3px 0; }
  #associativemess #associativeTblDsp { font-size: 1.6vh;  }
  #associativemess #associativeTblDsp .headerCell { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 5px; font-size: 1.3vh; }
  #associativemess #associativeTblDsp .cellTwo { width: 4vw; } 
  #associativemess #associativeTblDsp .cellThree { width: 60vw; } 
  #associativemess #associativeTblDsp .cellFour { width: 18vw; } 
  #associativemess #associativeTblDsp .cellFive { width: 18vw; } 
  #associativemess #associativeTblDsp .cellSix { width: 5vw; } 
  #associativemess #associativeTblDsp .cellSeven { width: 10vw; }
  #associativemess #associativeTblDsp .cellEight { width: 20vw; }
  #associativemess #associativeTblDsp .cellNine { width: 20vw; } 
  #associativemess #associativeTblDsp .dspDataCell { border-top: 2px solid rgba({$this->color_zackgrey},1); padding: 8px 4px; border-bottom: 1px solid rgba({$this->color_darkgrey},.6); border-right: 1px solid rgba({$this->color_darkgrey},.6); font-size: 1.5vh; }
  #associativemess #associativeTblDsp .dspDataCellA { padding: 8px 4px; border-bottom: 1px solid rgba({$this->color_darkgrey},.6); border-right: 1px solid rgba({$this->color_darkgrey},.6); font-size: 1.5vh; font-size: 1.3vh; }
  #associativemess #associativeTblDsp .mintbck { background: rgba({$this->color_darkgreen},.2); color: rgba({$this->color_zackgrey},1); }
  #associativemess #associativeTblDsp .standardbck { color: rgba({$this->color_zackgrey},1); }
  #associativemess #associativeTblDsp .inassdsp { padding: .5vh 3vw 3vh 3vw; display: none;  }
  #associativemess #associativeTblDsp .inassdspthisgroup { padding: .5vh 3vw 3vh 3vw; display: block;  }

#qmsErrorDone { width: 40vw; margin: 15vh 30vw; border: 4px solid rgba({$this->color_mgrey},.3); padding: .2vh .1vw; border-radius: 8px; }
#qmsErrorDone #qmsErrorHead { font-size: 2vh; background: rgba({$this->color_cornflowerblue},.8); color: rgba({$this->color_white},1); padding: .5vh .3vw; border-top-left-radius: 5px; border-top-right-radius: 5px; }
#qmsErrorDone #qmsAdvisor { font-size: 1.7vh; line-height: 2.5vh; text-align: justify; padding: 1vh .5vw;  }
#qmsErrorDone #qmsActionBtns { padding: 1vh 0; } 
#qmsErrorDone #qmsActionBtns button { font-size: 1.7vh; width: 15vw;     } 





STYLESHEET;
return $rtnThis;
}

function astrequestlisting ( $mobileind ) { 

$rtnThis = <<<STYLESHEET
body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 .2vw 0 .2vw; font-size: 1.4vh; } 
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; } 

#astRequestStatus { width: 10vw; }
#astSearchTerm { width: 20vw; }
#astRequestSPC { width: 10vw; } 
#astRequestPrep { width: 7vw; }

.suggestionHolder { position: relative; }
.suggestionDisplay {min-width: 25vw; position: absolute; left: 0; max-height: 30vh; min-height: 15vh; overflow: auto; z-index: 25; box-sizing: border-box; display: none;background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionTable { max-width: 24vw; box-sizing: border-box; }

.label { font-size: 1vh; font-weight: bold; white-space: nowrap; color: rgba({$this->color_zackgrey},.7); border-bottom: 1px solid rgba({$this->color_zackgrey},.7); padding-top: 5px; margin-bottom: 4px; }
.data { font-size: 1.4vh; padding: 8px 0px 8px 4px; margin-bottom: 6px; border-bottom: 1px solid rgba({$this->color_darkgrey},.3); border-right: 1px solid rgba({$this->color_darkgrey},.3);}   


#ASTHeadline { margin-left: 8vw; margin-right: 8vw; margin-bottom: 2vh; font-family: Roboto; font-size: 2.5vh; font-weight: bold; text-align: center; border-top: 3px double rgba({$this->color_zackgrey},1); border-bottom: 3px double rgba({$this->color_zackgrey},1); padding-bottom: .5vh; padding-top: .5vh; } 
#ASTInstruct { margin-left: 8vw; margin-right: 8vw; margin-bottom: 2vh; font-family: Roboto; font-size: 1.4vh; text-align: justify; line-height: 1.8em;  }
#paraHolder { margin-left: 8vw; margin-right: 8vw; } 

#foundline { font-size: 1.1vh; padding-top: 4vh; font-weight: bold;  padding-left: 1.2vw; color: rgba({$this->color_dblue},1); } 

#rqpTbl { margin-left: 1vw; border: 1px solid rgba({$this->color_zackgrey},1); padding: 8px; } 
#rqpSrchA { width: 15vw; padding: 4px 8px 0 0; } 
#rqpSrchB { width: 7vw; padding: 4px 8px 0 0; } 
#rqpSrchC { width: 20vw; padding: 4px 8px 0 0; } 
#rqpSrchD { width: 10vw; padding: 4px 8px 0 0; } 
#rqpSrchE { width: 7vw; padding: 4px 8px 0 0; } 
#rqpSrchF { width: 15vw; padding: 4px 8px 0 0; } 


.requestholdrdiv { margin-left: 1vw; margin-top: 1.5vh; margin-bottom: 1.5vh; border: 1px solid rgba({$this->color_zackgrey},.7); padding: .1vh 0 .2vh 0; }
.requestholdrdiv:nth-child(even) { background: rgba({$this->color_lightgrey},1);  } 

.requestholdr { width: 98vw; }
.requestholdr .rqstdsp { text-align: center; background: rgba({$this->color_cornflowerblue}, 1); color: rgba({$this->color_white},1); padding: 8px 0 8px 0; font-size: 1.8vh; font-weight: bold;}
.requestholdr .rqststat { width: 4vw;  }
.requestholdr .otherfld { width: 10vw; }  
.requestholdr .rqstcmts { width: 45vw; }


.tisholdrtbl { width: 96vw; margin-left: 1vw; margin-top: .6vh; border: none;  }
.tisholdrtbl .linedenoter { width: .2vw; } 
.tisholdrtbl .shortfld { width: 3vw; }
.tisholdrtbl .semishortfld { width: 30vw; }
.tisholdrtbl .nxtlength  { width: 10vw; }   

.short { width: 5vw; }
.medium { width: 12vw; }  

STYLESHEET;
return $rtnThis;    
}


function shipmentdocument($mobileind) { 
$rtnThis = <<<STYLESHEET
body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 .2vw 0 .2vw; font-size: 1.4vh; } 

#mainShipDocHoldTable { width: 98vw; box-sizing: border-box; margin-left: 1vw; margin-right: 1vw; border: 1px solid rgba({$this->color_dblue},1); }
#mainShipDocHoldTable .sdnew { background: rgba({$this->color_darkgreen},1); color: rgba({$this->color_white},1); }
#mainShipDocHoldTable .sdopen { background: rgba({$this->color_darkgreen},1); color: rgba({$this->color_white},1); }
#mainShipDocHoldTable .sdlocked { background: rgba({$this->color_mamber},1); color: rgba({$this->color_zackgrey},1); }
#mainShipDocHoldTable .sdclosed { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_dblue},1); }

.sdFieldLabel { font-size: 1.2vh; color: rgba({$this->color_zackgrey},1); border-bottom: 1px solid rgba({$this->color_mgrey},1);  }
.sdinput { padding: .7vh .5vw; font-size: 1.3vh; }

#sdnbrdsp {font-size: 2vh; font-weight: bold; text-align: center; padding-top: 8px; padding-bottom: 8px; width: 5vw; }
#sdcRqstShipDate {width: 7vw; }
#sdcRqstToLabDate {width: 7vw; }
#sdsetupdsp { width: 10vw; }
#sdtrack { width: 19vw; background: rgba({$this->color_lgrey},1); }
#sdShipDocSalesSetup { width: 10vw; }
#sdcPurchaseOrder { width: 10vw; }
#sdcShipDocSalesOrder { width: 5vw; text-align: right; background: rgba({$this->color_lgrey},1); } 
#sdcShipDocSalesOrderAmt { width: 5vw; text-align: right; background: rgba({$this->color_lgrey},1);}

#sdshpcal {min-width: 17vw; }
#tolabcal { min-width: 17vw; }
#sdcShipDocNbr { width: 6vw }
#sdcShipDocStsDte { width: 8vw; }
#sdcAcceptedBy { width: 10vw; }
#sdcAcceptorsEmail { width: 17vw; }
#sdcShipDocSetupOn { width: 12vw; }

#sdinvcodedsp {font-size: 2vh; font-weight: bold; text-align: center; padding-top: 8px; padding-bottom: 8px; width: 5vw; }
#sdcIName { width: 16vw; background: rgba({$this->color_lgrey},1);}
#sdcIEmail {width: 20vw; background: rgba({$this->color_lgrey},1);}
#sdcTQStatus { width: 5.5vw; background: rgba({$this->color_lgrey},1); }
#sdcInstitution { width: 25vw; background: rgba({$this->color_lgrey},1); }
#sdcInstitutioniType { width: 12vw; background: rgba({$this->color_lgrey},1); }
#sdcIDivision { width: 12vw; background: rgba({$this->color_lgrey},1); }

#sdcInvestShippingAddress { width: 30vw; height: 12vh; }
#sdcInvestBillingAddress { width: 30vw; height: 12vh; }
#sdcShippingPhone {width: 30vw; }
#sdcCourierInfo { width: 30vw; }
#sdcBillPhone { width: 30vw; }
#sdcPublicComments { width: 35vw; height: 22vh; }

#segDemarkation { padding: 1vh 0; text-align: center; font-size: 2vh; color: rgba({$this->color_zackgrey},1); border-top: 4px double rgba({$this->color_mgrey},1); border-bottom: 4px double rgba({$this->color_mgrey},1); background: rgba({$this->color_lgrey},1); }

.segmentInfoHolder { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); height: 17vh; }
.segmentInfoHolder:hover { background: rgba({$this->color_lamber},1); }

.dualHoldTable { width: 24vw; height: 5vw; }
.action-icon { color: rgba({$this->color_zackgrey},1); } 
.action-icon:hover { color: rgba({$this->color_bred},1); cursor: pointer; }

.delbtnholder { width: 2vw; text-align: center; padding-top: 8px; } 
.pulledyes { font-size: 1.1vh; text-align: right; border-top: 1px solid rgba({$this->color_mgrey},1); height: 2vh; }
.pulledno { font-size: 1.1vh; height: 2vh;}
.infoHolderSideTbl {width: 22vw; }
.segbgsdsp { font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); height: 2vh; padding-top: 8px; }
.segdxdesig { font-size: 1.3vh; color: rgba({$this->color_cornflowerblue},1); height: 4vh; }
.segprpdsp { font-size: 1.2vh; color: rgba({$this->color_mgrey},1); height: 2vh; }
.seginvdsp { font-size: 1.2vh; color: rgba({$this->color_mgrey},1); height: 4vh; }

.rTableCell { float: left; margin-right: 3px; margin-bottom: 3px;  }

.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; } 

#qryShipDoc { font-size: 1.2vh; }

#resultTblHolderCell { padding: 1vh .5vw; }
#shipDocQryRsltTbl { border: 1px solid rgba({$this->color_zackgrey},1); }
#shipDocQryRsltTbl #rsltCount { padding: 1vh 1vw; border-bottom: 2px solid rgba({$this->color_zackgrey},1); }
#shipDocQryRsltTbl th { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 8px; }
#shipDocQryRsltTbl .dspCell { padding: 8px; border-bottom: 1px solid rgba({$this->color_mgrey},.6); border-right: 1px solid rgba({$this->color_mgrey},.6);  }
#shipDocQryRsltTbl .rowDsp:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }

.checkboxThree { width: 5vw; height: 3vh; background: rgba( {$this->color_lamber}, 1 ); margin: 0; border-radius: 2px; position: relative; border: 1px solid rgba({$this->color_zackgrey},1); }
.checkboxThree:before { content: 'Yes'; position: absolute; top: .7vh; left: .5vw; height: .1vh; color: rgba({$this->color_darkgreen},1 ); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree:after  { content: 'No '; position: absolute; top: .7vh; left: 3.2vw; height: .1vh; color: rgba({$this->color_bred},1); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree label  { display: block; width: 2vw; height: 1.7vh; border-radius: 50px; transition: all .5s ease; cursor: pointer; position: absolute; top: .7vh; z-index: 1; left: .5vw; background: rgba( {$this->color_zackgrey}, 1  ); }
.checkboxThree input[type=checkbox]:checked + label { left: 2.3vw; background: rgba( {$this->color_darkgreen}, 1 ); }
.checkboxThree .checkboxThreeInput { visibility: hidden; }

STYLESHEET;
//.rTable    { display: table; }
//.rTableRow       { display: table-row; }
//.rTableCell, .rTableHead  { display: table-cell; }
//.rTableHeading    { display: table-header-group; }
//.rTableBody    { display: table-row-group; }
//.rTableFoot    { display: table-footer-group; }
return $rtnThis;    
}  
  
function inventorymanifest ( $mobileind ) { 
$rtnThis = <<<STYLESHEET
  body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 .2vw 0 .2vw; font-size: 1.5vh; }  
  #qryDivLineHolder { display: grid; grid-template-columns: 18vw 18vw 18vw 3vw 10vw 10vw; grid-gap: .2vw; }
  .dataElementHold {  }  
  #bsqueryToDate { font-size: 1.3vh; }  
  #bsqueryFromDate { font-size: 1.3vh;     } 
  #presentInst { width: 18vw; }
  #fldManifestNbr { width: 10vw; font-size: 1.3vh; } 

  #dspWorkArea { margin-top: 1vh; display: grid; grid-template-columns: 2fr 1fr; grid-gap: .2vw; }

  #dspWorkArea #onOfferGrid { border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; height: 78vh; overflow: hidden; }
  #dspWorkArea #onOfferGrid #queryParaLine { display: grid; grid-template-columns: 30% 30% 22% 8%; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-size: 1.3vh; padding: .8vh .2vw .8vh .2vw; }
  #dspWorkArea #onOfferGrid #queryParaLine .dataElementHolder .dataElementLabel { font-size: 1vh; font-weight: bold;  }

  #dspWorkArea #onOfferGrid #offerHeader { display: grid; grid-template-columns: 5vw 15vw 4vw 5vw 25vw 5vw 3vw 3vw; } 
  #dspWorkArea #onOfferGrid #offerHeader .oHeaderDsp { font-size: 1.4vh; font-weight: bold;border-bottom: 1px solid rgba({$this->color_zackgrey},1); border-right: 1px solid rgba({$this->color_zackgrey},1); color: rgba({$this->color_zackgrey},1); padding: 1vh 0 .2vh .2vw; }  

  #dspWorkArea #onOfferGrid #offerElements { height: 70vh; overflow: auto; font-size: 1.5vh;  } 
  #dspWorkArea #onOfferGrid #offerElements .offerRecord {  display: grid; grid-template-columns: 5vw 15vw 4vw 5vw 25vw 5vw 3vw 3vw;  } 
  #dspWorkArea #onOfferGrid #offerElements .offerRecord:nth-child(even)  { background: rgba({$this->color_cornflowerblue},.2);    }
  #dspWorkArea #onOfferGrid #offerElements .offerRecord:hover { cursor: pointer; background: rgba({$this->color_lamber},.4); }
  #dspWorkArea #onOfferGrid #offerElements .offerRecord[data-selected='1'] { background: rgba({$this->color_darkgreen},.2); }

  #dspWorkArea #onOfferGrid #offerElements .offerRecord .dataElement { font-size: 1.5vh; border-bottom: 1px solid rgba({$this->color_zackgrey},.4); border-right: 1px solid rgba({$this->color_zackgrey},.4); color: rgba({$this->color_black},1); padding: .8vh 0 .8vh .2vw;  }  


  #dspWorkArea #manifestDspSide { border: 1px solid rgba({$this->color_zackgrey},1); box-sizing: border-box; height: 78vh; overflow: hidden; padding: .1vh .3vw .1vh .3vw; }
  #dspWorkArea #manifestDspSide #manifestBuildHead { display: grid; grid-template-columns: 5vw 5vw 7vw 5vw 5vw 5vw;     }
  #dspWorkArea #manifestDspSide #manifestBuildHead .holder button { font-size: 1.5vh; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white},1); width: 99%; height: 5vh; transition: .4s; }
  #dspWorkArea #manifestDspSide #manifestBuildHead .holder button:hover { background: rgba({$this->color_cornflowerblue},.5); cursor: pointer;   }

  #dspWorkArea #manifestDspSide #manifestBuildHead #mFldDsp { margin-top: 1vh; }  
  #dspWorkArea #manifestDspSide #manifestBuildHead #mFldDsp .holderlable { font-size: 1.1vh; font-weight: bold; }
  #dspWorkArea #manifestDspSide #manifestBuildHead #mFldDsp .element input[type=text] { border: none; padding: 0; } 

  #dspWorkArea #manifestDspSide #manifestMetrics { grid-column: span 6; font-size: 1.2vh; text-align: right; padding: 0 .5vw .3vh 0;   }

  #dspWorkArea #manifestDspSide #manifestDetailHolder { grid-column: span 6; height: 60vh; overflow: auto; box-sizing: border-box; border: 1px solid rgba({$this->color_zackgrey},1);    }
  #dspWorkArea #manifestDspSide #manifestDetailHolder #detailHeader { display: grid; grid-template-columns: 1vw 5vw 10vw auto; font-size: 1.5vh;   }
  #dspWorkArea #manifestDspSide #manifestDetailHolder #detailHeader .dHead { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .3vh 0;     }  
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord { display: grid; grid-template-columns: 1vw 5vw 10vw auto; font-size: 1.5vh; border-top: 1px solid rgba({$this->color_zackgrey},.4);  }
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord:nth-child(even) {  background: rgba({$this->color_cornflowerblue},.2);  }

  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord .delIco { font-size: 2.5vh; text-align: center; }
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord .delIco:hover { cursor: pointer; color: rgba({$this->color_bred},1); }
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord .manDetBGS { padding: .5vh .1vw; } 
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord .manDetPrep { padding: .5vh .1vw; }
  #dspWorkArea #manifestDspSide #manifestDetailHolder .manDtlRecord .manDetDesig { padding: .5vh .1vw; }

  #fldManifestNbrDsp { width: 6vw; } 
  .hoverIco:hover { cursor: pointer; color: rgba({$this->color_cornflowerblue},1);  } 

  .floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  }       

STYLESHEET;
return $rtnThis;    
}

function biogroupdefinition($mobileind) { 
$rtnThis = <<<STYLESHEET
body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 .2vw 0 .2vw; font-size: 1.5vh; } 

#mainHolderTbl { }
.lineTbl { border-collapse: collapse; }
#lineBiogroupAnnounce { border-bottom: 1px solid rgba({$this->color_dblue},1); width: 100%; font-size: 1.9vh; font-weight: bold; color: rgba({$this->color_dblue},1); }
#assLineBiogroupAnnounce { border-bottom: 1px solid rgba({$this->color_dblue},1); width: 100%; font-size: 1.9vh; font-weight: bold; color: rgba({$this->color_dblue},1); }
.smlInfoLine { font-size: 1vh; color: rgba({$this->color_dblue},1); font-weight: bold; text-align: right; }
#innerAssDspTbl { width: 100%; font-size: 1.3vh; border: 1px solid rgba({$this->color_darkgrey},1);   }
#innerAssDspTbl thead tr th { font-size: 1.1vh; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); border-right: 1px solid rgba({$this->color_white},1); padding: 5px 0 5px 0; }  
#innerAssDspTbl tbody tr td { border-right: 1px solid rgba({$this->color_zackgrey},1); padding: 8px 3px 8px 3px; } 
#innerAssDspTbl tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
#innerAssDspTbl tbody tr:hover { background: rgba({$this->color_lamber},1); }
.hpricon { color: rgba({$this->color_darkgreen},1); }
.hpricon:hover { cursor: pointer; color: rgba({$this->color_cornflowerblue},1);  } 


.dataElementTbl { border-collapse: collapse; border: 1px solid rgba({$this->color_dblue},1); }
.elementLabel { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: .5vh .3vw; font-size: 1.1vh; font-weight: bold; } 
.dataElement { padding: .9vh .3vw; background: rgba({$this->color_white},1); box-sizing: border-box; }
.dataElementc { background: rgba({$this->color_white},1); }

.sideControlBtn { color: rgba({$this->color_darkgrey},.8); background: rgba({$this->color_white},1); border-collapse: collapse; box-sizing: border-box; }
.sideControlBtn tr td { padding: .3vh .5vw; }
.sideControlBtn:hover { color: rgba({$this->color_dblue},1); cursor: pointer; background: rgba({$this->color_cornflowerblue},.6); } 

.noteHolder { position: relative; }
.noteExplainerDropDown { position: absolute; top: 28px; right: -5vw; width: 10vw; background: rgba({$this->color_cornflowerblue},1); padding: 4px; box-sizing: border-box; display: none; font-size: 1.5vh; font-weight: normal; } 
.noteExplainerDropDown:before {content:'';position:absolute; border: 15px solid transparent;border-bottom: 15px solid rgba({$this->color_cornflowerblue},1); top: -25px; left: 0;}
.noteHolder:hover .noteExplainerDropDown { display: block; }

.btnExplainerHolder { position: relative; }
.btnExplainer { position: absolute; top: 5vh; right: 0; white-space: nowrap; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 4px; box-sizing: border-box; display: none; z-index: 55; } 
.btnExplainerHolder:hover .btnExplainer { display: block; }

.sideindicatoricon { font-size: 1.5vw;color: rgba({$this->color_darkgrey},.8); }
.sideindicatoricon:hover { color: rgba({$this->color_cornflowerblue},1); cursor: pointer; }

#elemSpecCat { width: 21vw; }
#elemSite { width: 37vw; }
#elemDX { width: 37vw; }

#elemMets { width: 37vw; }
#elemSystemic { width: 37vw; }
#elemPosition { width: 21vw; }

#elemProceDate { width: 6vw; }
#elemProcedureCollect { width: 18vw; }
#elemARS { width: 30vw; }
#elemCXRX { width: 12vw; }

#elemPR { width: 8vw; }
   #elemPR .dataElement { padding: 0; }
   #elemPR table { width: 8vw; }
   .prAnswer { padding: .9vh .3vw; min-width: 4.5vw; } 

.prExplainer { position: relative; }
.prExplainer .qlSmallIcon { font-size: 1.8vh; color: rgba({$this->color_darkgrey},.8);  } 
.prExplainer .prExplainerText { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 8px; top: 30px; border: 1px solid rgba({$this->color_cornflowerblue},1); display: none; font-size: 1.2vh; white-space: nowrap; }
.prExplainer:hover .prExplainerText { display: block; }
.prExplainer:hover .qlSmallIcon { color: rgba({$this->color_cornflowerblue},1); }
.prExplainer:hover { cursor: pointer; }

#elemSbj { width: 13vw; }
#elemIC { width: 7vw; }

#elemBGCmnt { width: 48vw; }
#elemHPRQ { width: 47.5vw; }

.commentHolder { position: relative; }
.commentHolder:hover {cursor: pointer; }

.commentdsp { overflow: auto;  padding: .9vh .3vw; box-sizing: border-box; }
.cmtEditIcon { position: absolute; right: 12px; top: .7vh;  }
.cmtEditIconCls { font-size: 2.5vh; color: rgba({$this->color_darkgrey},.5); }
.commentHolder:hover .cmtEditIconCls { color: rgba({$this->color_bred}, .6); }


.basicEditIcon {   position: absolute; right: 3px; top: -7px; font-size: 1vh; }

.segstatusdspinfo { position: relative; }
.segstatusinfo { position: absolute; top: 30px; left: 30px; white-space: nowrap; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 4px; box-sizing: border-box; display: none; z-index: 55; }
.segstatusdspinfo:hover .segstatusinfo { display: block; }
.segstatusdspinfo:hover { cursor: pointer; }

.scnstatusdspinfo { position: relative; }
.scnstatusinfo { position: absolute; top: 30px; right: 0; white-space: nowrap; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 4px; box-sizing: border-box; display: none; z-index: 55; }
.scnstatusdspinfo:hover .scnstatusinfo { display: block; }
.scnstatusdspinfo:hover { cursor: pointer; }

.qmsdspholder { position: relative; } 
.qmsdspinfo {  position: absolute; top: 35px; left: 15px; white-space: nowrap; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: 4px; box-sizing: border-box; display: none; z-index: 55;  }
.qmsdspholder:hover .qmsdspinfo { display: block; }
.qmsdspholder:hover { cursor: pointer; }


#segmentListTbl { width: 96vw; table-layout: fixed; font-size: 1.3vh; margin-top: 2vh; border: 1px solid rgba({$this->color_dblue},1); }
#segmentListTbl thead tr { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); font-size: 1.1vh; font-weight: bold; }
#segmentListTbl thead tr td { padding: .5vh .3vw; }

#segmentListTbl tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); }
#segmentListTbl tbody tr:hover { background: rgba({$this->color_lamber},1); }
#segmentListTbl tbody tr[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); }


#segmentListTbl tbody tr td { padding: .2vh .3vw; border-bottom: 1px solid rgba({$this->color_darkgrey},.5); border-right: 1px solid rgba({$this->color_darkgrey},.5); height: 6vh; }

#segmentListTbl .endCell { border-right: none; }
#segmentListTbl .seg-lbl { width: 1.5vw; text-align: center; }
#segmentListTbl .seg-hrp { width: 2.5vw; }
#segmentListTbl .seg-metr { width: 3vw; }
#segmentListTbl .seg-cuttech { width: 5vw; }
#segmentListTbl .seg-qty { width: 1vw; text-align: center; }
#segmentListTbl .seg-procdte { width: 5vw; }
#segmentListTbl .seg-rqst { width: 4vw; }
#segmentListTbl .seg-shpdte {width: 5vw; }
#segmentListTbl .seg-shpdoc { width: 4vw; }

#segmentListTbl tbody tr td .hovertbl table tr:hover { background: rgba({$this->color_zackgrey},1); } 
#segmentListTbl tbody tr td .hovertbl table tr:nth-child(even) { background: rgba({$this->color_zackgrey},1); }
#segmentListTbl tbody tr td .hovertbl table tr td { border: none; height: 1vh; }

#qmsstatind { width: 1.8vw; height: 3.8vh; border: 1px solid rgba({$this->color_zackgrey},1); text-align: center; color: rgba({$this->color_white},1); }

.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; } 

#PRUPHoldTbl { width: 84vw;  }
#PRUPHoldTbl #fldDialogPRUPPathRptTxt {width: 90vw; height: 40vh; font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); line-height: 1.6em; text-align: justify; }
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

.prcFldLbl { font-size: 1.3vh; color: rgba({$this->color_dblue},1);  padding: .5vh 0 0 0; font-weight: bold; }
input {font-size: 1.3vh; padding: 1vh .5vw; }
textarea { font-size: 1.3vh; }
#preparationAdditions { display: none;  }
.addTblHeader {font-size: 1.3vh; color: rgba({$this->color_dblue},1);  padding: 0 0 0 0; font-weight: bold;  border-bottom: 1px solid rgba({$this->color_dblue},1); } 
.addHolder { padding: 1vh 0; border-bottom: 1px solid rgba({$this->color_dblue},1);   }
#assignTbl { margin-top: 1vh; }
#assignTbl #noteBlock { font-size: 1vh; font-weight: bold; padding: 0 0 1vh 0; color: rgba({$this->color_darkgrey},.7);   }
#fldSEGAddMetric {width: 5vw; text-align: right; }

#ddSEGPreparationDropDown { min-width: 20vw; }
#fldSEGselectorAssignInv { width: 16vw; }
#fldSEGselectorAssignReq { width: 12vw; }

.suggestionHolder { position: relative; }
.suggestionDisplay {min-width: 25vw; position: absolute; left: 0; max-height: 30vh; min-height: 15vh; overflow: auto; z-index: 25; box-sizing: border-box; display: none;background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionTable { max-width: 24vw; box-sizing: border-box; }
#fldSEGSGComments { width: 34vw;  height: 7vh; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw 0 .3vw; text-align: justify;  }
#segBGDXD { font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); font-weight: bold; padding: 1vh 0 0 0; width: 35vw;  overflow: hidden; border-bottom: 1px solid rgba({$this->color_cornflowerblue},1); }

#fldParentSegment { width: 10vw; }
#divSegmentDisplayLister { width: 10vw; box-sizing: border-box; border: 1px solid rgba({$this->color_zackgrey},1); height: 43vh; overflow: auto;     }
#fldSEGDefinitionRepeater { width: 8vw; text-align: right; }
.reqInd {font-size: 1.3vh; color: rgba({$this->color_bred},1); font-weight: bold; }

#tblSegmentLister { width: 100%; font-size: 1.2vh; }
#tblSegmentLister thead tr td { text-align: left; font-weight: bold; color: rgba({$this->color_zackgrey},.8); background: rgba({$this->color_white},1); padding: 5px; }
#tblSegmentLister tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1);  }
#tblSegmentLister tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},.8); }
#tblSegmentLister tbody tr td { padding: 5px; }

.prcFld {width: 5vw; text-align: right; }

.topHeadCell { padding: 5px; background: rgba({$this->color_dblue},1); font-size: 1.2vh;  color: rgba({$this->color_white},1); border-right: 1px solid rgba({$this->color_white},1); }
.moletestdatadsp { font-size: 1.5vh; padding: 4px; border-bottom: 1px solid rgba({$this->color_dblue},1); border-right: 1px solid rgba({$this->color_dblue},1);  }
.smlTxt { font-size: .9vh; text-align: right;  }

.compDspTbl { width: 10vw; }

.hprindication { color: rgba({$this->color_darkgreen},1); text-decoration: underline;  }

.sdttholder { position: relative; }
.sdttholder .tt { position: absolute; background: rgba({$this->color_zackgrey},1); width: 10vw;  color: rgba({$this->color_white},1); padding: 7px 5px; display: none;  }
.sdttholder:hover .tt { display: block; z-index: 49; }
.sdttholder .qlSmallIcon {font-size: 1.2vh; }
.sdttholder .smlFont { font-size: 1vh; }

.quickLink { margin-top: .6vh; margin-bottom: .6vh; }
.quickLink:hover { color: rgba({$this->color_neongreen},1); }

.assttholder { position: relative; }
.assttholder .tt { position: absolute; background: rgba({$this->color_zackgrey},1); width: 10vw;  color: rgba({$this->color_white},1); padding: 7px 5px; display: none;  }
.assttholder:hover .tt { display: block; z-index: 49; }
.assttholder .qlSmallIcon {font-size: 1.2vh; }
.assttholder .smlFont { font-size: 1vh; }

STYLESHEET;
return $rtnThis;
}

function collectiongrid($mobileind) {     
$rtnThis = <<<STYLESHEET
body { margin: 0; margin-top: 9vh; box-sizing: border-box; padding: 0 1vw 0 1vw; font-size: 1.5vh; } 
    
#presentInst { width: 24vw; } 
#ddpresentInst { min-width: 24vw; }    

.displayRows:nth-child(even) td { background: rgba({$this->color_lightgrey},1); }        

.rowColorA  { background: rgba({$this->color_white},1); } 
.rowColorB  { background: rgba({$this->color_lightgrey},1); } 

#waitForMe {display: none; }    
        
.strthru { position: relative; }
.strthru::before {content: '';border-bottom: 3px solid rgba({$this->color_bred},1);width: 100%;position: absolute;right: 0;top: 50%;}

.lockdsp { width: 1vw; text-align: center;  }
.cgelem_bgnbr {  width: 4vw; color: rgba({$this->color_zackgrey},1); font-size: 2.5vh; padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); text-align: center;}
.cgelem_instTmeTech {  width: 15vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_proccoltype { width: 5vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_metric { width: 5vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_prpt { width: 4vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_infc { width: 4vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_sbjt { width: 6vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_prcl { width: 6vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_age { width: 6vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_race { width: 10vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_sex { width: 4vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_spcat { width: 12vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_site { width: 20vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_dx { width: 20vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_unk { width: 8vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.cgelem_metsf { width: 20vw; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 3px; border-left: 1px solid rgba({$this->color_dblue},.5); border-bottom: 1px solid rgba({$this->color_dblue},.5); }
.topper { border-top: 1px solid rgba({$this->color_dblue},.5); }

.datalbl { font-size: 1.3vh; font-weight: bold; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 3px; } 
.endcell {border-right: 1px solid rgba({$this->color_dblue},.5); }

.segmentHeader { font-size: 1.5vh; font-weight: bold; color: rgba({$this->color_darkgrey},.5); }
.segLbl { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); padding: 4px; }

.cgsgelem_label { width: 7vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); }
.cgsgelem_hpr  { width: 1vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); text-align: center; }
.cgsgelem_qty  { width: 1vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); text-align: right; }
.cgsgelem_prp { width: 25vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); }
.cgsgelem_con { width: 10vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); }
.cgsgelem_hp { width: 4vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5);  text-align: right; }
.cgsgelem_met { width: 2vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5);  text-align: right; }
.cgsgelem_from { width: 5vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5);  text-align: right; }
.cgsgelem_ass { width: 25vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); }
.cgsgelem_tme { width: 8vw; padding: 4px;  font-size: 1.5vh; border-left: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: 1px solid rgba({$this->color_darkgrey},.5); }
.cgsgendcap { border-right: 1px solid rgba({$this->color_darkgrey},.5); }

.segmentHolderTbl { margin-bottom: 4vh; }

STYLESHEET;
return $rtnThis; 
}  
  
function procurebiosample($mobileind) { 

$rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }
#btnPBClearGrid { width: 2vw; } 
#procurementAddHoldingTbl { height: 86vh; box-sizing: border-box; }
#procurementAddHoldingTbl .sidePanel { width: 30vw; box-sizing: border-box;  }
#procurementAddHoldingTbl #procGridHolderCell { padding: 0 0 0 1.5vw;  }
.topBtnHolderCell .ttholder { position: relative; }
.topBtnHolderCell .ttholder:hover .tt { display: block; text-align: left; }
.topBtnHolderCell .tt { position: absolute; left: -.5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }
.topBtnHolderCell .tt .btnBarDropMenuItems { font-size: 1.5vh; }
.topBtnHolderCell .tt .btnBarDropMenuItems .btnBarDropMenuItem:hover { background: rgba({$this->color_lblue},1);  color: rgba({$this->color_white},1); cursor: pointer; } 
.procGridHoldingTable {width: 85vw; box-sizing: border-box; }
.procGridHoldingTitle {font-size: 1.7vh; color: rgba({$this->color_cornflowerblue},1); font-weight: bold; border-bottom: 2px solid rgba({$this->color_ddrwgrey},1); padding: .5vh 0 0 0; } 
.procGridHoldingDecorationLineBACKUP {border-left: 2px solid rgba({$this->color_ddrwgrey},1);border-top: 2px solid rgba({$this->color_ddrwgrey},1);}
.prcFldLbl { font-size: 1.3vh; color: rgba({$this->color_dblue},1);  padding: 0 0 0 0; font-weight: bold; }
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
#fldPRCProcDate { width: 8vw; }
#fldPRCPresentInst {width: 20vw; }
#ddfldPRCPresentInst {min-width: 17vw; }
#fldPRCProcedureType { width: 11vw; }
#ddPRCProcedureType { min-width: 11vw; }
#fldPRCCollectionType { width: 11vw; }
#ddPRCCollectionType { min-width: 11vw; }
#fldPRCTechnician{ width: 13.5vw; }
#fldPRCInitialMetric { width: 3vw; text-align: right; }
#fldPRCMetricUOM {width: 8vw; }
#ddPRCMetricUOM {min-width: 8vw; }
#fldSubjectNbr {width: 7vw; }
#fldADDSubjectNbr { width: 12vw; }
#fldProtocolNbr {width: 7vw;}
#fldADDProtocolNbr {width: 9vw; }
#fldPRCSpecCat {width: 13vw; }
#ddPRCSpecCat {min-width: 13vw; }
#fldPRCSite {width: 12vw; }
#ddPRCSite { min-width: 15vw; }
#fldPRCSSite {width: 11.2vw; }
#ddPRCSSite { min-width: 11.2vw; }
#fldPRCDXMod {width: 23vw; }
#ddPRCDXMod { min-width: 23vw; }
#fldPRCBSCmts {width: 38.5vw; height: 10vh; }
#fldPRCHPRQ {width: 38.5vw; height: 10vh; }
#fldPRCUnknownMet {width: 6vw; }
#ddPRCUnknownMet {min-width: 6vw; }
#fldPRCUnInvolved { width: 10vw; }
#ddPRCUnInvolved { min-width: 10vw; }
#fldPRCSitePositions { width: 15vw; }
#ddPRCSitePositions { width: 15vw; }
#fldPRCDiagnosisDesignation { width: 25vw; }
#fldPRCMETSDesignation {width: 32vw; }
#fldPRCSystemicDX {width: 32vw; }
#fldPRCPXISex {width: 3vw;  }
#fldPRCPXIId { display: none; }
#fldPRCPXIInitials { width: 4vw;  }
#fldADDPXIInitials { width: 4vw; }
#fldPRCPXIAge {width: 2.8vw; text-align: right;  }
#fldADDPXIAge {width: 3vw; text-align: right; }
#fldPRCPXIAgeMetric {width: 2.8vw; }
#fldADDAgeUOM {width: 5vw;}
#ddADDAgeUOM {min-width: 6vw; }
#fldPRCPXIRace {width: 8vw;  }
#fldPRCPXIEth { width: 8vw; } 
#fldPRCPXIInfCon {width: 5vw;  }
#fldPRCPXILastFour {width: 4vw;  }
#fldPRCPXIDspCX {width: 6vw;  }
#fldPRCPXICX {width: 5vw; }
#ddPRCPXICX {min-width: 6vw;}
#fldPRCPXIDspRX {width: 6vw;  }
#fldPRCPXIRX {width: 5vw;  }
#ddPRCPXIRX {min-width: 6vw;}
#fldPRCPXISubjectNbr { width: 9vw;  }
#fldPRCPXIProtocolNbr { width: 9vw; }
#fldPRCPXISOGI {width: 5vw;  }
#fldPRCUpennSOGI { width: 10vw; }
#ddPRCUpennSOGI { width: 10vw; }
#metsFromDsp { display: none; }
#fldPRCMETSSite { width: 14vw; }
#ddPRCMETSSite { min-width: 15.5vw; }
#fldPRCMETSDX { width: 25vw; }
#ddPRCMETSDX { min-width: 25vw; }
#fldPRCSitePosition { width: 16.5vw; }
#ddPRCSitePosition { min-width: 16.5vw; }
#fldPRCSystemList {width: 19vw; }
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
.reqInd {font-size: 1.3vh; color: rgba({$this->color_bred},1); font-weight: bold; }
#BSDspMainHeader { font-size: 2vh; text-align: center; font-variant: small-caps; font-weight: bold; color: rgba({$this->color_white},1); background: rgba({$this->color_cornflowerblue},1); padding: 1vh 1vw; border: 2px solid rgba({$this->color_dblue},1); }
.BSDspSectionHeader { padding-top: .6vh; padding-bottom: .6vh; padding-left: 0vw; font-size: 1.4vh; background: rgba({$this->color_white},1); color: rgba({$this->color_darkgrey},1); border-bottom: 1px solid rgba({$this->color_darkgrey},1); font-weight: bold; }
.BSDspSpacer { height: 1.5vh;  }
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
#fldDNRRace { width: 5vw; }
#fldDNREthno {width: 5vw; } 
#fldADDRace {width: 10vw; }
#ddDNRRace { min-width: 10vw; }
#ddADDRace {min-width: 10vw; }
#fldDNRSex { width: 4vw; }
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
#procbtnsidebar { width: 2.5vw; border-right: 1px solid rgba({$this->color_ddrwgrey}, 1); }
#procbtnsidebar .ttholder { position: relative; color: rgba({$this->color_ddrwgrey},1);  }
#procbtnsidebar .ttholder:hover { cursor: pointer; color: rgba({$this->color_zackgrey},1); }
#procbtnsidebar .ttholder:hover .tt { display: block; text-align: left; }
#procbtnsidebar .tt { position: absolute; left: -.5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }

#procGridBGDsp { padding: 0 0 0 1vw; border-left: 1px solid rgba({$this->color_ddrwgrey},1); }
#bgProcSideBarDspTbl {    }
#dspBGN { font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1);  }
.lockfield { background: rgba({$this->color_grey},.5);   }
.lockfield:focus, .lockfield:active { background: rgba({$this->color_grey},.5);    }

#segBGDXD { font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); font-weight: bold; padding: 1vh 0;  }
#fldSEGAddHP {width: 5vw; text-align: right; }
#fldSEGAddMetric {width: 5vw; text-align: right; }

#ddSEGPreparationDropDown { min-width: 20vw; }
#fldSEGselectorAssignInv { width: 15vw; }
#fldSEGselectorAssignReq { width: 10vw; }
#fldSEGCutFrom {width: 10vw;}

.suggestionHolder { position: relative; }
.suggestionDisplay {min-width: 25vw; position: absolute; left: 0; max-height: 30vh; min-height: 15vh; overflow: auto; z-index: 25; box-sizing: border-box; display: none;background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); }
.suggestionTable { max-width: 24vw; box-sizing: border-box; }

#preparationAdditions { display: none;  }
.addTblHeader {font-size: 1.3vh; color: rgba({$this->color_dblue},1);  padding: 0 0 0 0; font-weight: bold;  border-bottom: 1px solid rgba({$this->color_dblue},1); } 
.addHolder { padding: 1vh 0; border-bottom: 1px solid rgba({$this->color_dblue},1);   }
#assignTbl { margin-top: 1vh; }
#assignTbl #noteBlock { font-size: 1vh; font-weight: bold; padding: 0 0 1vh 0; color: rgba({$this->color_darkgrey},.7);   }

#addPBQtySlide { width: 5vw; text-align: right; }
#addSlideDspBox {background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); width: 27vw; height: 8vh; overflow: auto;  }
#fldSEGSlideQtyNbr { width: 5vw; text-align: right; }
#fldSEGSlideCutFromBlock { width: 10vw; }

.openDivIndicator { color: rgba({$this->color_darkgreen},1);  }
.openDivIndicator:hover { cursor: pointer; color: rgba({$this->color_bred},1); }

#procurementScreenSegmentList { width: 100%; }
#procurementScreenSegmentList thead th { font-size: 1.3vh; color: rgba({$this->color_dblue},1);  padding: 0 0 0 3px; text-align: left; font-weight: bold; border-bottom: 1px solid rgba({$this->color_darkgrey},1); }
#procurementScreenSegmentList tbody td { font-size: 1.3vh; color: rgba({$this->color_zackgrey},1); padding: .4vh 0 .4vh 3px; }
#procurementScreenSegmentList tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); }

#procurementScreenSegmentList tbody .ptSegBGS { width: 8vw; } 
#procurementScreenSegmentList tbody .ptSegHPR { width: 1.5vw; text-align: center; } 
#procurementScreenSegmentList tbody .ptSegHRP { width: 1.5vw; text-align: center; } 
#procurementScreenSegmentList tbody .ptSegPRP { width: 16vw; } 
#procurementScreenSegmentList tbody .ptSegMET { width: 5vw; } 
#procurementScreenSegmentList tbody .ptSegCUT { width: 5vw; } 
#procurementScreenSegmentList tbody .ptSegASS { width: 25vw; }

#segAddslideItmTbl { width: 100%; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); border-collapse: collapse; }
#segAddslideItmTbl th { font-size: 1.3vh; color: rgba({$this->color_dblue},1); text-align: left; }
#segAddslideItmTbl tbody tr:nth-child(even) { background: rgba({$this->color_lightgrey},1); padding: .3vh .5vw;  }

#fldSEGSGComments { width: 100%; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: .5vh .3vw 0 .3vw; text-align: justify;  }

#bgvoiderrortbl { width: 40vw; } 
#bgvoiderrortbl tr td { text-align: center; font-size: 1.8vh; color: rgba({$this->color_bred},1); font-weight: bold; padding: 3vh 3vw; }

STYLESHEET;
return $rtnThis;
}

function scienceserverchangelog () { 

$rtnThis = <<<STYLESHEET
body { margin: 0; box-sizing: border-box;  }

#mainPageHolder { margin: 8vh 15vw; }
#mainPageHolder #head { font-size: 2.2vh; font-weight: bold; } 
#mainPageHolder #instructions { font-size: 1.8vh; line-height: 2.5vh; text-align: justify; margin: 1vh 0 2vh 0; }
#chngLogTbl { width: 70vw; font-family: Roboto; font-size: 1.5vh; border: 4px solid rgba({$this->color_zackgrey},1); }
#chngLogTbl tr th { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .5vh 0;    } 
#chngLogTbl tbody tr:nth-child(even) { background: rgba({$this->color_neongreen},.2); }
#chngLogTbl tbody tr td { padding: .5vh .3vw; border: 1px solid rgba({$this->color_zackgrey},.4); border-top: none; border-left: none; }
#chngLogTbl tbody tr .dtedsp { width: 8vw; } 
#chngLogTbl tbody tr .ssversion { width: 7vw; }  
#chngLogTbl tbody tr .gittag { width: 7vw; }  



STYLESHEET;
return $rtnThis;
}


function scienceserverhelp ( ) {

      $rtnThis = <<<STYLESHEET

body { margin: 0; box-sizing: border-box;  }


#indexMenuSlide { position: fixed; background: rgba({$this->color_zackgrey},1); top: 0; right: -155vw; width: 55vw; height: 100vh; display: grid; grid-template-columns: 55vw; transition: all 1s ease; z-index: 20; } 
#indexMenuSlide #indexHolder { padding: 11vh 1vw 0 1vw; display: grid; grid-template-rows: 5vh auto; grid-gap: .2vh; } 
#indexMenuSlide #indexHolder #srchBoxHolder { height: 100%; box-sizing: border-box; padding: .4vh 0 0 0; display: grid; grid-template-columns: 40vw 6vw ; grid-gap: .2vw;   } 
#indexMenuSlide #mainHelpFileHolder { border: 1px solid #fff; box-sizing: border-box; width: 53vw; height: 83vh; overflow: auto; margin: 0 1vw;   }  


#fldHlpSrch { font-size: 1.7vh; padding: .5vh .2vw; background: rgba({$this->color_zackgrey},1); border: 1px solid rgba({$this->color_white},1); color: rgba({$this->color_white},1); width: 40vw;  } 

.ssHlpModDiv {  } 
.hldTopicDocList { display: block; transition: all .4s ease;  } 
.hlpModuleTbl { display: grid; grid-template-columns: 1vw auto; text-decoration: none; color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.5vh; transition: all .2s ease; padding: .3vh .3vw; outline: none; }
.hlpModuleTbl:hover { color: rgba({$this->color_neongreen},1);   } 
.ssHlpModDiv:hover .hldTopicDocList { display: block; }    

.hlpTopicDiv { display: grid; grid-template-columns: 1.5vw auto; text-decoration: none; color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.5vh; outline: none; padding: .6vh 2vw .8vh 2vw; }
.hlpTopicDiv:hover { background: rgba({$this->color_zackgrey_highlight},1); color: rgba({$this->color_white},1); } 
.topicicon { font-size: 1.5vh; }  

.sideindicon { font-size: 1.5vh; }  



#documentDisplay { margin: 12vh 5vw 1vh 5vw; }
#documentIcons { position: fixed; top: 8vh; left: 1vw; height: 87vh; width: 2.5vw; border-right: 1px solid rgba({$this->color_mgrey}, .6); }

.ttholder { position: relative; color: rgba({$this->color_ddrwgrey},1); padding: 0 .6vw; margin-bottom: 1vh; }
.ttholder:hover { cursor: pointer; color: rgba({$this->color_zackgrey},1); }
.ttholder:hover .tt { display: block; text-align: left; }
.tt { position: absolute; left: -.5vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); box-sizing: border-box; padding: 4px 3px; display: none; white-space: nowrap; z-index: 40; }



#resultsSearchTbl { width: 100%; }
#resultsSearchTbl #title { font-size: 3.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh 0; text-align: center; border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
#resultsSearchTbl #itemsfound { font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh .8vw; } 
#resultsSearchTbl #bywhowhen { font-size: 1.6vh; font-style: italic; color: rgba({$this->color_zackgrey},1); padding: 1vh 0 1vh .8vw; }

#resultsSearchTbl .zoogleTbl { width: 100%;  }
#resultsSearchTbl .zoogleTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#resultsSearchTbl .zoogleTbl .zoogleTitle { font-size: 2vh; color: rgba({$this->color_cornflowerblue},1); font-weight: bold; padding: 1vh 3vw 0 5vw; }
#resultsSearchTbl .zoogleTbl .zoogleURL { font-size: 1.6vh; color: rgba({$this->color_darkgreen},1); padding: 0 3vw .3vh 5vw; }
#resultsSearchTbl .zoogleTbl .zoogleAbstract { font-size: 1.6vh; color: rgba({$this->color_zackgrey},1); padding: .3vh 25vw 1vh 5vw;text-align: justify; line-height: 1.8em; }



#help_welcometitle { font-size: 2vh; color: rgba({$this->color_zackgrey},1); text-align: center; font-weight: bold; padding: 5vh 0 0 0; }
#help_welcomeinstructions { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); padding: 2vh 8vw; text-align: justify; line-height: 1.8em; }
   .ssdsp { font-weight: bold; color: rgba({$this->color_cornflowerblue},1);   }

#moduletitleline { border-bottom: 2px solid rgba({$this->color_zackgrey},1); } 
#moduletitleline #mtitle { font-family: Roboto; font-size: 3vh; color: rgba({$this->color_zackgrey},1); font-weight: bold; }  
#moduletitleline #mtitle .smllr { font-size: 1.8vh; } 
#moduletitleline #mDesc { font-family: Roboto; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); }  

#mDocumentList { display: grid; grid-template-columns: repeat( 4, 1fr); grid-gap: .3vw; padding: 3vh .5vw; }
#mDocumentList a { text-decoration: none; display: grid; grid-template-columns: 3vw auto; color: rgba({$this->color_zackgrey},1); border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_lgrey},1); height: 7vh; overflow: hidden; }
#mDocumentList a:hover { background: rgba({$this->color_lamber},.6); } 
#mDocumentList a .mDocDspIcon { grid-row: span 2; padding: 1.5vh .5vw; border-right: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_dblue},1); }
#mDocumentList a .mDocDspIcon .material-icons { font-size: 3.5vh; color: rgba({$this->color_white},1); } 
#mDocumentList a .mDocTitle { font-family: Roboto; font-size: 1.7vh; padding: .3vh .2vw; }
#mDocumentList a .mDocSTitle { font-family: Roboto; font-size: 1.4vh; padding: 0 .2vw 3vh .2vw; text-align: justify; line-height: 1.4em; }      

#hDocObjHoldr { margin: 1vh 3vw; border-left: 1px solid rgba({$this->color_zackgrey},.4); border-right: 1px solid rgba({$this->color_zackgrey},.4);padding: 0 1vw 0 1vw; } 
#hDocType {font-family: Roboto; font-size: 1vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: left; margin: 0 0 .8vh 0; }
#hDocTitle { font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: left; }
#hDocSTitle { font-family: Roboto; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); text-align: left; border-bottom: 2px solid rgba({$this->color_zackgrey},1); padding-bottom: .4vh; margin-bottom: 1vh; }
#hDocText { font-family: Roboto; font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); line-height: 1.5em; text-align: justify; } 
#hDocText .hlpSectionDspNbr { font-size: 1.2vh; font-weight: bold; margin: 2vh 0 1vh 0; border-bottom: 1px solid rgba({$this->color_zackgrey},.3);      }   

#hDocMetricsHolder { margin: 2vh 0 3vh 0; clear: both; }
#hDocMetricsHolder #metricBox { border: 1px solid rgba({$this->color_zackgrey},1); padding: .3vh .2vw; text-align: left; width: 60vw; } 
#hDocMetricsHolder #metricBox #metricTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.3vh; font-weight: bold; padding: .4vh .3vw; text-align: left; grid-column: span 6;  }
#hDocMetricsHolder #metricBox .mElemHld { box-sizing: border-box; display: grid; grid-template-columns: 1fr 2fr 2fr 3fr; grid-gap: .2vw; }
#hDocMetricsHolder #metricBox .mElemHld .hdiv { border: 1px solid rgba({$this->color_zackgrey},.4); margin-top: .2vh; }
#hDocMetricsHolder #metricBox .mElemHldMod { border: 1px solid rgba({$this->color_zackgrey},.4); box-sizing: border-box; font-family: Roboto; font-size: 1.5vh; padding: .2vh .3vw;  }
#hDocMetricsHolder #metricBox .mElemHldMod .cGreen { color: rgba({$this->color_darkgreen},1); font-weight: bold; }
#hDocMetricsHolder #metricBox .mElemHld .mElemLbl { font-family: Roboto; font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .3vh .2vw 0 .2vw; } 
#hDocMetricsHolder #metricBox .mElemHld .mElemDta { font-family: Roboto; font-size: 1.4vh; color: rgba({$this->color_zackgrey},1); padding: 0 .2vw .3vh .2vw; } 
 
#docHead { display: grid; grid-template-columns: 5vw 12vw 25vw 25vw 15vw; grid-gap: .2vw; margin: 1vh 0 0 .2vw; width: 84vw;   }
#docHead .dataelementhold { padding: .3vh 0 .3vh 0; }  
#docHead .dataelementlabel { font-size: 1.3vh; font-weight: bold; }  

#fldDocId { width: 4.9vw; } 
#fldDocType { width: 11.9vw; } 
#fldDocTitle { width: 24.9vw; } 
#fldDocSubTitle { width: 24.9vw; } 
#fldScreenRef { width: 14.9vw;   } 
#fldSectionList { width: 20vw; margin: .3vh .2vw; } 

#docCtlLine { display: grid; grid-template-columns: 4fr 1fr; border-bottom: 1px solid rgba({$this->color_cornflowerblue},1); margin: 0 0 .8vh 0; } 

#modsList { display: grid; grid-template-columns: repeat( 4, 1fr); grid-gap: .2vw; width: 70vw; margin: 1vh 0 1vh .2vw;  }
#modsList #modsListHead { grid-column: span 4; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .2vh 0 .2vh .2vw; font-size: 1.4vh; }  
.modDspDiv { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_grey},.5); display: grid; grid-template-columns: 2fr 1fr; } 
.allowLabelDsp {  font-size: 1.3vh; padding: .2vh .3vw; box-sizing: border-box;  } 
.allowIndicator { padding: .2vh .1vw .2vh 0; } 

.checkboxThree { width: 5vw; height: 3vh; background: rgba( {$this->color_lamber}, 1 ); margin: 0; border-radius: 2px; position: relative; border: 1px solid rgba({$this->color_zackgrey},1); }
.checkboxThree:before { content: 'On '; position: absolute; top: .7vh; left: .5vw; height: .1vh; color: rgba({$this->color_darkgreen},1 ); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree:after  { content: 'Off'; position: absolute; top: .7vh; left: 3.2vw; height: .1vh; color: rgba({$this->color_bred},1); font-family: 'Roboto'; font-size: 1.4vh; }
.checkboxThree label  { display: block; width: 2vw; height: 1.7vh; border-radius: 50px; transition: all .5s ease; cursor: pointer; position: absolute; top: .7vh; z-index: 1; left: .5vw; background: rgba( {$this->color_zackgrey}, 1  ); }
.checkboxThree input[type=checkbox]:checked + label { left: 2.3vw; background: rgba( {$this->color_darkgreen}, 1 ); }
.checkboxThree .checkboxThreeInput { visibility: hidden; }

#ctlBox {    } 
#saveDocButton { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_grey},1); margin: 1vh 2vw 0 0; font-size: 1.5vh; font-weight: bold; padding: .5vh .8vw; } 


#sectiondisplay { height: 65vh; display: grid; grid-template-rows: 6vh 55vh 3vh; grid-gap: .2vh; }    
#sectiondisplay #sectionselectordiv { display: grid; grid-template-columns: 20.5vw 4vw auto; } 
#btnAddSection { margin: .3vh 0; border: 1px solid rgba({$this->color_zackgrey},1); height: 4.8vh; width: 3vw; text-align: center; font-size: 3.8vh; background: rgba({$this->color_white},1); color:rgba({$this->color_zackgrey},1); transition: .5s; }
#btnAddSection:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.6); }
#sectiondisplay #workbenchdiv { overflow: auto; }

.sectionHolderDiv { margin: .8vh .2vw; padding: .8vh .2vw; border: 1px solid rgba({$this->color_zackgrey},1); } 
.sectiondelimitline { display: grid; grid-template-columns: 82vw 1vw; }
.sectiondelimitline .sectiondelimit { font-size: 1.4vh; }  
.sectiondelimitline .setiondeleter { border: 1px solid rgba({$this->color_zackgrey},1);  text-align: center; font-size: 2vh; background: rgba({$this->color_white},1); color:rgba({$this->color_bred},1); transition: .5s;   } 
.sectiondelimitline .setiondeleter:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); } 
.sectiontxtbox textarea { font-size: 1.3vh; width: 83vw; height: 15vh; } 
.sectionmetric { font-size: 1.1vh; text-align: right; font-weight: bold; width: 83vw; }  

#docInstructions { font-size: 1.2vh; line-height: 1.4em; text-align: justify; padding: 0 .5vw 0 0; } 

#sectiondisplay #sectionworkbuttons { padding: .5vh .5vw 0 0;  } 


STYLESHEET;
return $rtnThis;
//#hDocMetricsHolder #metricBox #metricModTitle { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.3vh; font-weight: bold; padding: .4vh .3vw; text-align: left; grid-column: span 6;    }
}


function scienceserverhelp_bu20200324($mobileind) { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 9vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }

#sshHoldingTable { border-collapse: collapse; width: 97vw; height: 87vh; }
#sshHoldingTable #head { height: 2vh; }
#sshHoldingTable #topicDivHolder { width: 25vw; }
#sshHoldingTable #divDspTopicList { width: 27vw;  }

#ssHelpFilesHeaderDsp { font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.ssHlpModDiv { position: relative; }

#ticketCHolder { width: 1vw; }

.hlpModuleTbl { font-size: 1.7vh; color: rgba({$this->color_zackgrey},1); }

.iconholdercell {width: 1vw; }

.topicicon {font-size: 1.6vh; padding: .3vh .3vw .3vh 1.5vw; }
.hlpTopicDiv table tr td { padding: .5vh 0 .5vh 0; }
.hlpTopicTbl { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); width: 100%; }
.hlpTopicTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }

.hlpFunctionDiv table tr td {padding: .5vh 0 .5vh 0; }
.hlpFuncTbl { font-size: 1.5vh; color: rgba({$this->color_zackgrey},1); width: 100%; }
.hlpFuncTbl:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }

.funcIcon {font-size: 1.8vh; padding: 0 0 0 2vw; }
#instructionDiv { font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; padding: 10vh 0 3vh 0; }

.helpticket { font-size: 1.5vh; }

#hlpMainHolderDiv {padding: 2vh 2vw 0 1vw;  min-height: 75vh; } 
#hlpMainTitle { width: 100%; font-family: Roboto; font-size: 3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; padding: 0 0 .8vh 0; }
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

#helperdspholder { display: grid; grid-template-columns: 22vw 76vw; grid-gap: .2vw; grid-template-rows: 5.5vh auto; width: 98vw; height: 88vh; box-sizing: border-box;  } 
#helperdspholder #helpdsptitleline { display: grid; grid-template-columns: repeat( 2, 1fr); border-bottom: 2px solid rgba({$this->color_zackgrey},1); height: 5.5vh;   }
#helperdspholder #helpdsptitleline #SSTitle { height: 5.5vh; box-sizing: border-box; font-family: Roboto; font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: 1.5vh .3vw .3vh .3vw; font-variant: small-caps;  }
#helperdspholder #helpdsptitleline #srchHolder { height: 5.5vh;}  

#helpindexholder { border-right: 1px solid rgba({$this->color_zackgrey},1); height: 87vh; width: 22vw; overflow: auto; position: fixed; top: 9vh; }  


#mainHelpFileHolder { position: relative; width: 21vw;  }

STYLESHEET;
return $rtnThis;
}

function inventory ( $mobileind ) { 

//<!-- #inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv:nth-child(even) { background: rgba({$this->color_mgrey},.3); } //-->

      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 11vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; } 

#crsuffix1900 { width: 10vw; } 

#inventoryMasterHoldr { display: grid; grid-template-columns: 1fr 8fr;}
#inventoryMasterHoldr #inventoryTitle { grid-row: 1; grid-column: span 2; font-family: Roboto; font-size: 3vh; font-weight: bold; text-align: right; border-bottom: 2px solid rgba({$this->color_zackgrey},.5); padding: 0 2vw 0 0;  }
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn { border: 1px solid rgba({$this->color_zackgrey}, 1); margin-top: 10px; text-align: center; text-decoration: none; color: rgba({$this->color_zackgrey},1); overflow: hidden; height: 8vh; background: rgba({$this->color_lgrey},1); }
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn:hover { background: rgba({$this->color_lamber}, .5); } 
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn[data-selected='true'] { background: rgba({$this->color_darkgreen},.2); color: rgba({$this->color_white},1); } 
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn:first-child { margin-top: 2px; } 
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn a         { text-decoration: none; color: rgba({$this->color_zackgrey},1); display:inline-block; width: 100%; height: 100%; padding: 3.2vh 5px; font-family: Roboto; font-size: 1.5vh;  } 
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn a:link    { text-decoration: none; }
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn a:visited { text-decoration: none; }
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn a:hover   { text-decoration: none; }
#inventoryMasterHoldr #inventoryBtnBar .iControlBtn a:active  { text-decoration: none; }

#inventoryMasterHoldr #inventoryControlPage { padding: 0 0 0 10px; }

#inventoryCheckinElementHoldr { display: grid; grid-template-rows: repeat( 11, 1fr); height: 85%; } 
#inventoryCheckinElementHoldr #locationscan { grid-row: 1 / 2; } 
#inventoryCheckinElementHoldr #locationscan #locscandsp { font-family: Roboto; font-size: 2vh; color: rgba({$this->color_dblue},1); padding: 1vh 0 1vh 3vw;   }
#inventoryCheckinElementHoldr #locationscan #instructionBlock { font-family: Roboto; font-size: 1.3vh; color: rgba({$this->color_dblue},1); padding: .5vh 5vw .5vh 0;    }

#inventoryCheckinElementHoldr #itemCountDsp { grid-row: 2 / 3;  font-size: 2vh; color: rgba({$this->color_zackgrey},1); text-align: center; } 
#inventoryCheckinElementHoldr #labelscan { grid-row: 3 / 8; height: 35vh; overflow: auto; } 
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv { display: grid; grid-gap: 4px; grid-template-columns: repeat(5, 1fr); } 
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv { border: 1px solid rgba({$this->color_zackgrey},1); padding: 4px; height: 8vh; background-color: rgba({$this->color_white},1); font-family: Roboto; font-size: 1.4vh; overflow: hidden; }
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv:hover { background: rgba({$this->color_lamber},.5); cursor: pointer; }
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv .scanDisplay { font-weight: bold; color: rgba({$this->color_dblue},1); }
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv .lblsegstatusdsp { font-weight: bold; color: rgba({$this->color_cornflowerblue},1); font-size: 1.8vh; text-align: center; }
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv .lblsegprpdsp { font-weight: bold; color: rgba({$this->color_dblue},1); font-size: 1.4vh; text-align: center; }
#inventoryCheckinElementHoldr #labelscan #labelscanholderdiv .labelDspDiv .lblsegdxdsp { color: rgba({$this->color_dblue},1); font-size: 1.2vh; text-align: center; }
 
 
#inventoryCheckinElementHoldr #ctlButtons .iControlBtn { border: 1px solid rgba({$this->color_zackgrey}, 1); text-align: center; text-decoration: none; color: rgba({$this->color_zackgrey},1); overflow: hidden; height: 3vh; width: 6vw; background: rgba({$this->color_lgrey},1); font-size: 2.5vh; padding: 1vh 0; margin-top: .5vh; }
#inventoryCheckinElementHoldr #ctlButtons .iControlBtn:hover { background: rgba({$this->color_lamber}, .5); cursor: pointer; }

.errordspmsg { color: rgba({$this->color_bred},1); font-weight: bold; text-align: center; } 

.scanfieldlabel { font-size: 1.8vh; font-weight: bold; } 

#inventoryCheckinElementHoldr #ctlButtons { grid-row: 9 / 10; border-top: 1px solid rgba({$this->color_zackgrey},1); } 

#IKeyOverride { grid-row: 8 / 9; display: grid;  grid-template-columns: 8vw auto; }
#usrPinOverrideHold { margin: 0 .2vw 0 .2vw; }
.dataLabel { font-size: 1.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
#fldUsrInventoryPin { width: 7vw; text-align: center; }

#ctlPad { display: grid; grid-template-columns: repeat( 11, 3vw); grid-gap: .2vw; padding: 1.3vh 0 0 0; } 
#ctlPad .ctlPadBtn {  border: 1px solid rgba({$this->color_zackgrey},1); height: 6vh; width: 3vw;  box-sizing: border-box;  font-size: 2vh; font-weight: bold; padding: 1.6vh 0 1.6vh 1.1vw; transition: 1s; 
-webkit-box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);
-moz-box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);
box-shadow: -8px 0px 29px -8px rgba({$this->color_zackgrey},0.29);  } 
#ctlPad .ctlPadBtn:hover { cursor: pointer; }  

.scanwarning { font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_bred},1); }  

#dspIWait { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba({$this->color_lamber},1); width: 30vw; height: 20vh; border: 8px solid rgba({$this->color_mgrey},1); border-radius: 8px; box-sizing: border-box; padding: 1vh 1vw; display: none; z-index: 101;   }
#waiticongif { width: auto; height: 2vh;    } 
#waitMsgTitle { font-family: Roboto; font-size: 2.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); border-bottom: 2px solid rgba({$this->color_zackgrey},1);     }
#waitMsg { font-family: Roboto; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); padding: .5vh 0 1vh 0; line-height: 1.5em;   }

#locscandsp_hprt {  display: grid; grid-template-columns: 7vw 23vw 25vw 30vw; grid-gap: .3vw;  } 
#locscandsp_hprt .telemhold { border: 1px solid rgba({$this->color_zackgrey},1); margin-top: .5vh; padding: .2vh .2vw; boz-sizing: border-box; }
#locscandsp_hprt #telemholda { grid-column: span 4; font-size: 1.5vh; padding: .5vh .3vw 0 .3vw; color: rgba({$this->color_darkgreen},1);  }
#locscandsp_hprt #putawaylocation  { grid-column: span 4; font-size: 1.5vh; display: block; padding: 0 .3vw .5vh .3vw; }
#locscandsp_hprt .tDataLabel { font-family: Roboto; color: rgba{$this->color_zackgrey},1); font-size: 1.2vh; font-weight: bold;   }  
#locscandsp_hprt .tData { font-family: Roboto; color: rgba{$this->color_zackgrey},1); font-size: 1.5vh; padding: 0 0 0 .2vw; }  

#hprlabelscanholderdiv { display: grid; grid-template-columns: repeat( 5, 15vw); grid-gap: .3vw;  }
#hprlabelscanholderdiv .slidedspdiv { border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_cornflowerblue},.1); } 
#hprlabelscanholderdiv .slidedspdiv .sBGS { font-size: 1.4vh; padding: .2vh .3vw 0 .3vw; color: rgba({$this->color_zackgrey},1); font-weight: bold;}
#hprlabelscanholderdiv .slidedspdiv .sdxd { font-size: 1.2vh; font-weight: bold; text-align: center; padding: .1vh .3vw 0 .3vw; height: 4vh; overflow: auto; }
#hprlabelscanholderdiv .slidedspdiv .sstatus { font-size: 1.1vh; color: rgba({$this->color_darkgreen},1); padding: .2vh .3vw .5vh .3vw;  text-align: center;  }  
#hprlabelscanholderdiv .slidedspdiv .sprep { background: rgba({$this->color_dblue},1); font-size: 1vh; text-align: right; color: rgba({$this->color_white},1); padding: .1vh .3vw; } 

.instructionLabel { font-size: 2vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); padding: .5vh 0 0 .2vw; }
#intraMan { display: grid; grid-template-columns: 7vw 20vw 10vw 8vw 10vw 6vw; grid-gap: .2vw; margin: .5vh .2vw 0 .2vw;  }
#intraMan .elementholder { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; } 
#intraMan .elementholder .elementlabel { font-size: 1.5vh; color: rgba({$this->color_zackgrey},.6); font-weight: bold; }
#intraMan .elementholder .dataelement { font-size: 1.8vh; color: rgba({$this->color_black},1); padding: .2vh .2vw; } 
#dspmanifestsegmentcount { padding: .2vh 2vw;  } 

#instrTwo { margin-top: 2vh; } 
#intraManLoc { display: grid; grid-template-columns: 62vw; grid-gap: .2vw; margin: .5vh .2vw 0 .2vw;  }
#intraManLoc .elementholder { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; } 
#intraManLoc .elementholder .elementlabel { font-size: 1.5vh; color: rgba({$this->color_zackgrey},.6); font-weight: bold; }
#intraManLoc .elementholder .dataelement { font-size: 1.8vh; color: rgba({$this->color_black},1); padding: .2vh .2vw; }


#instrThree { margin-top: 2vh; } 
#intraManSegs { display: grid; grid-template-columns: 62vw; grid-gap: .2vw; margin: .5vh .2vw 0 .2vw;  }
#intraManSegs .elementholder { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; } 
#intraManSegs .elementholder .elementlabel { font-size: 1.5vh; color: rgba({$this->color_zackgrey},.6); font-weight: bold; }
#intraManSegs .elementholder .dataelement { font-size: 1.8vh; color: rgba({$this->color_black},1); padding: .2vh .2vw; }
#dspSegmentListing { height: 40vh; overflow: auto; } 

#segTblHeaderHold { display: grid; grid-template-columns: 2vw 5vw 7vw 12vw 4vw auto; grid-gap: .2vw;     }
#segTblHeaderHold .sHead { background: rgba({$this->color_zackgrey}, 1);  color: rgba({$this->color_white},1); padding: .2vh .2vw; font-size: 1.5vh; font-weight: bold; } 

.segRecHold { display: grid; grid-template-columns: 2vw 5vw 7vw 12vw 4vw auto; grid-gap: .2vw;  }
.segRecHold:nth-child(even) { background: rgba({$this->color_cornflowerblue},.1); }
.segRecHold[data-selected='true'] { background: rgba({$this->color_darkgreen},.1); }

.segdataelement  { font-size: 1.5vh; border-bottom: 1px solid rgba({$this->color_zackgrey},.4); border-right: 1px solid rgba({$this->color_zackgrey},.4); padding: .3vh .2vw   }

#instrFour { margin-top: 2vh; } 
#intraManErrs { display: grid; grid-template-columns: repeat( 2, 1fr); grid-gap: .2vw; width: 62vw;  } 
#intraManErrs button { display: block; border: 1px solid rgba({$this->color_zackgrey},1); background: rgba({$this->color_white}, 1); font-size: 2vh; padding: 1vh 0; color: rgba({$this->color_lblue},1); transition: .5s; }
#intraManErrs button:hover { cursor: pointer; background: rgba({$this->color_darkgrey},1); color: rgba({$this->color_neongreen},1);     }

#workbenchholder { display: grid; grid-template-columns: 2fr 1fr; grid-gap: .2vw; margin-top: .2vh; } 
#workbenchholder #workbench { } 
#workbenchholder #workbench #intraMan { display: grid; grid-template-columns: repeat( 6, 1fr); grid-gap: .2vw; margin: .5vh .2vw 0 .2vw;  }

#workbenchholder #workbench #intraManOne {display: grid; grid-template-columns: 15vw auto; grid-gap: .5vw; }  
#workbenchholder #workbench #intraManOne .checkboxThree { width: 15vw; height: 5vh; background: rgba( {$this->color_lamber}, 1 ); margin: 0; border-radius: 2px; position: relative; border: 1px solid rgba({$this->color_zackgrey},1); }
#workbenchholder #workbench #intraManOne .checkboxThree:before { content: 'SHIP'; position: absolute; top: 1.2vh; left: .5vw; height: .1vh; color: rgba({$this->color_darkgreen},1 ); font-family: 'Roboto'; font-size: 2.3vh; font-weight: bold; }
#workbenchholder #workbench #intraManOne .checkboxThree:after  { content: 'PULL'; position: absolute; top: 1.2vh; left: 11.5vw; height: .1vh; color: rgba({$this->color_mblue},1); font-family: 'Roboto'; font-size: 2.3vh; font-weight: bold; }
#workbenchholder #workbench #intraManOne .checkboxThree label  { display: block; width: 5vw; height: 3.7vh; border-radius: 50px; transition: all .5s ease; cursor: pointer; position: absolute; top: .7vh; z-index: 1; left: .3vw; background: rgba({$this->color_mblue},1); }
#workbenchholder #workbench #intraManOne .checkboxThree input[type=checkbox]:checked + label { left: 9.7vw; background: rgba( {$this->color_darkgreen}, 1 ); }
#workbenchholder #workbench #intraManOne .checkboxThree .checkboxThreeInput { visibility: hidden; }

#workbenchholder #workbench #intraManOne .elementholder { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; }
#workbenchholder #workbench #intraManOne .elementholder .elementlabel { font-size: 1.5vh; color: rgba({$this->color_zackgrey},.6); font-weight: bold; }
#workbenchholder #workbench #intraManOne .elementholder .dataelement { font-size: 1.8vh; color: rgba({$this->color_black},1); padding: .2vh .2vw; }
#workbenchholder #sdsidepanel { display: grid; grid-gap: .2vh; } 
#workbenchholder #sdsidepanel .elementholder { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; }
#workbenchholder #sdsidepanel .elementholder .elementlabel { font-size: 1.5vh; color: rgba({$this->color_zackgrey},.6); font-weight: bold; }
#workbenchholder #sdsidepanel .elementholder .dataelement { font-size: 1.8vh; color: rgba({$this->color_black},1); padding: .2vh .2vw; }


#workbenchholder #workbench #chtnlbllist { border: 1px solid rgba({$this->color_zackgrey},.4); padding: .2vh .2vw; box-sizing: border-box; height: 40vh; overflow: auto; display: grid; grid-template-columns: repeat( 4, 1fr ); grid-gap: .2vw; grid-template-rows: repeat( 200, 10vh); }
#workbenchholder #workbench #chtnlbllist .bgslbldsp { border: 1px solid rgba({$this->color_zackgrey},.4); min-height: 11vh; background: rgba({$this->color_white},1);  }
#workbenchholder #workbench #chtnlbllist .bgslbldsp[data-scanned='true'] { background: rgba({$this->color_darkgreen},.1);  color: rgba({$this->color_zackgrey},1);      } 
#workbenchholder #workbench #chtnlbllist .bgslbldsp .lineone { display: grid; grid-template-columns: 1fr 8fr; }
#workbenchholder #workbench #chtnlbllist .bgslbldsp .lineone .bgsnbr { font-size: 1.8vh; font-weight: bold; }
#workbenchholder #workbench #chtnlbllist .bgslbldsp .desigLine { font-size: 1.3vh; padding: 0 .2vw 0 .2vw; }
#workbenchholder #workbench #chtnlbllist .bgslbldsp .preparation { padding: .2vh .2vw 0 .2vw; font-style: italic;   }
#workbenchholder #workbench #chtnlbllist .bgslbldsp .storage { padding: .3vh .2vw .3vh .2vw;   } 
#bttnHoldr { margin: 1vh 0 0 0;  }
.basicButton { border: 1px solid rgba({$this->color_zackgrey},1); padding: 1vh .5vw; font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_lgrey},1); transition: .4s; }
.basicButton:hover { cursor: pointer; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); }  

.shipperInformationDiv {  width: 70vw; background: rgba({$this->color_white},1); padding: .1vh .1vw .5vh .1vw; } 
.shipperInformationDiv #titleBar { background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); display: grid; grid-template-columns: auto .8vw; font-size: 1.4vh; padding: .2vh .2vw;   }   
.shipperInformationDiv #titleBar #closeBtnHere { font-size: 2vh; } 
.shipperInformationDiv #titleBar #closeBtnHere:hover { cursor: pointer; color: rgba({$this->color_bred},1); font-size: 2vh; } 
.shipperInformationDiv #buttonLine { display: grid; grid-template-columns: repeat( 7, 1fr); grid-gap: .2vw; padding: .3vh 0; }
.shipperInformationDiv #buttonLine .shpIndBtn { border: 1px solid rgba({$this->color_zackgrey},.4); background: rgba({$this->color_white},1); font-size: 2.5vh;  text-align: center; color: rgba({$this->color_zackgrey},.4); padding: 1vh 0;}  
.shipperInformationDiv #buttonLine .shpIndBtn:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.3); }  
.shipperInformationDiv #buttonLine .shpIndBtn[data-selected='true'] { background: rgba({$this->color_darkgreen},.5); color: rgba({$this->color_white},1); } 

.shipperInformationDiv #trckElementHolder #trckElementLbl { font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},.7); padding: 2vh .5vw 0 1vw; }  
.shipperInformationDiv #trckElementHolder #trckNbrGoesHere { width: 69vw; height: 5vh; text-align: center; padding: 1vh .2vw; font-size: 3vh; color: rgba({$this->color_zackgrey},.7); }     

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

.reportGroupHolder { border: 1px solid rgba({$this->color_zackgrey},1); margin-top: .1vh; }
.groupHeaderName { font-size: 1.5vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.groupdescription { font-size: 1.1vh; color: rgba({$this->color_mgrey},1); }
.favoritestar { font-size: 2.8vh; color: rgba(255,255,0,1); -webkit-text-stroke: 1px rgba({$this->color_mgrey},1); } 
.basicrpt { font-size: 2.8vh; color: rgba({$this->color_mgrey},1); -webkit-text-stroke: 1px rgba({$this->color_zackgrey},1); }
.primeiconholder { width: 2vw; padding: .2vh .2vw; box-sizing: border-box; }
.hoverer { cursor: pointer; }

.rptItemTblDsp { width: 18vw; height: 10vh; }
.rptTitleDsp {font-size: 1.8vh; color: rgba({$this->color_zackgrey},1); }
.rptDescDsp {font-size: 1.2vh; color: rgba({$this->color_mgrey},1);  }
.rptDspSqr { border:  1px solid rgba({$this->color_darkgrey},1); }
.rptDspSqr:hover { cursor: pointer; background: rgba({$this->color_lamber},.5); }


STYLESHEET;
return $rtnThis;    
}

function paymenttracker($mobileind) { 
$rtnThis = <<<STYLESHEET

  body { margin: 0; margin-top: 9vh; box-sizing: border-box; padding: 0 1vw 0 1vw;  }
  #mainPayHoldTblTitle { font-size: 1.8vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); border-bottom: 1px solid rgba({$this->color_zackgrey},1); }
  #payListTable { width: 15.3vw; border-collapse: collapse;  }
  .payCounter { width: 5vw; text-align: right; font-style: italic; font-size: 1.4vh;  }
  .payDateDsp { font-size: 1.4vh; }
  .payDetailDiv { display: none; }
  .goodpay { color: rgba({$this->color_darkgreen},1); font-size: 1.6vh; }
  .badpay { color: rgba({$this->color_bred},1); font-size: 1.6vh;}
  .payiconholder { width: 1.3vw; text-align: center; }
  .payinvestname { width: 5vw; } 
  .payinvoices { width: 6vw; }
  .mohknee {width: 3vw; text-align: right; }

  .topbtns { font-size: 1.5vh; }

  .dspFldLabel { font-size: 1.2vh; color: rgba({$this->color_darkgrey},.8); font-weight: bold; border-bottom: 1px solid rgba({$this->color_darkgrey},.8); padding: 1vh 0 0 0; } 
  #dspFldTransUUID { width: 10vw; }
  #dspFldTransType { width: 9vw; }
  #dspFldTransDate { width: 12vw; }
  #dspFldTransStat { width: 10vw; }
  #dspFldAuthCode { width: 10vw; }
  #dspFldAuthRefNo { width: 9vw; }
  #dspFldAuthAuthTime { width: 12vw; }
  #dspFldCard { width: 10vw; }
  #dspFldAuthMsg {width: 42vw; }

  #dspFldTransINVID { width: 10vw; }
  #dspFldTransName { width: 25vw; }
  #dspFldTransEmail { width: 25vw; }
  #dspFldTransPhone { width: 25vw; }
  #dspFldTransCoName { width: 35vw; }
  #dspFldAuthInvoices { width: 32vw; }
  #dspFldAuthAmt { width: 10vw; text-align: right; }
  #dspFldTransAddress { width: 35.5vw; height: 8vh; }
  #dspFldTransAddCity { width: 25vw; }
  #dspFldTransAddState { width: 4vw; }
  #dspFldTransAddZip { width: 6vw; }


STYLESHEET;
return $rtnThis;
}

function root($mobileind) { 

/*
#rootTable { font-family: Roboto; font-size: 1.5vh; width: 100%; }
/// SCREEN CALENDAR FORMATTING
.dashBoardGraphic { padding-left: .5vw; padding-right: .5vw; padding-bottom: .5vh; border-right: 1px solid rgba({$this->color_mgrey},.5); border-bottom: 1px solid rgba({$this->color_mgrey},.5); }
.dashBoardGraphic:hover {  border-right: 1px solid rgba({$this->color_bred},1); border-bottom: 1px solid rgba({$this->color_bred},1); cursor: pointer; }
#grphfreezer { width: 25vw; }
#grphinvestigatorinf { width: 13vw; }
#grphrollshipgrid { height: 37vh;}
#grphsegshiptotal { width: 13vw; }
#grphslidessubmitted { width: 13vw; }
#faQueueDspTbl { border: 1px solid rgba({$this->color_mgrey},.5); border-left: none; border-top: none; padding-bottom: .5vh; padding-right: .5vw; }
#faQueueDspTbl:hover {  border-right: 1px solid rgba({$this->color_bred},1); border-bottom: 1px solid rgba({$this->color_bred},1); } 
#faQueueTitleDsp { font-size: 1.4vh; font-weight: bold;  text-align: center; border-bottom: 2px solid rgba({$this->color_cornflowerblue},1); } 
.faQueueItemTitle { font-size: 1.3vh; border-bottom: 1px solid rgba({$this->color_zackgrey},.5); padding-top: .8vh;  }
.faQueueItemMetric { font-size: 1.3vh; text-align: right; border-bottom: 1px solid rgba({$this->color_zackgrey},.5); padding-top: .8vh; padding-left: .5vw; }
.faQueueDspBtn { font-size: 1.1vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); text-align: center; border: 1px solid rgba({$this->color_mgrey},.5); padding: 8px 8px; background: rgba({$this->color_mgrey},.2); width: 3vw; } 
.faQueueDspBtn:hover { cursor: pointer; background: rgba({$this->color_cornflowerblue},.3); } 
 */

      $rtnThis = <<<STYLESHEET
body { margin: 0; margin-top: 7vh; box-sizing: border-box; padding: 0 0 0 0;  }     
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; }

 #dashboardholder { display: grid; grid-template-columns: repeat( 4, 1fr ); grid-gap: .5vw; padding: 0 .5vw 0 .5vw; }

 #dashboardholder #headr { grid-column: span 4; border-bottom: 1px solid rgba({$this->color_mgrey},1); padding: .5vh .4vw 0 .4vw; display: grid; grid-template-columns: repeat( 4, 1fr);  }

 #dashboardholder #headr #tagger { grid-column: span 1; } 

 #dashboardholder #headr #moreInfoBar { grid-column: span 2; display: grid; grid-template-columns: repeat( 5, 1fr); grid-gap: .2vw; margin: 0 0 .2vh 0;  } 
 #dashboardholder #headr #moreInfoBar a {  display: block; text-decoration: none; outline: none;  color: rgba({$this->color_cornflowerblue},1); font-family: Roboto; font-size: 1.4vh; border: 1px solid rgba({$this->color_cornflowerblue},1); text-align: center; line-height: 3vh; background: rgba({$this->color_mgrey},.1); transition: all 0.5s; height: 3vh;  }
 #dashboardholder #headr #moreInfoBar a:hover { background: rgba({$this->color_darkgreen},1); color: rgba({$this->color_white},1); font-weight: bold; } 




 #dashboardholder #headr #headrTag { font-family: Roboto; font-size: 1.5vh;  color: rgba({$this->color_darkgreen},1); }
 #dashboardholder #headr #headrTag #chtn { font-weight: bold; color: rgba({$this->color_cornflowerblue},1); } 
 #dashboardholder #headr #dash { font-family: Roboto; font-size: 1.3vh;  color: rgba({$this->color_zackgrey},1);   }  

 #dashboardholder #headr #datedsp { grid-column: span 1; font-size: 2vh; text-align: right; padding: 1vh 0 0 0; color: rgba({$this->color_darkgreen},1); }
 #dashboardholder #headr #datedsp #ddMonth {  font-weight: bold; color: rgba({$this->color_cornflowerblue},1);  }

 #dashboardholder #calendarHolder { grid-column: span 2; grid-row: span 2; border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw;  }
 #dashboardholder #calendarHolder #calHead { font-family: Roboto; font-size: 1.5vh;  color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); } 

 #dashboardholder #calendarHolder #theCalendar { border: 1px solid rgba({$this->color_darkgrey},.5);margin: .2vh 0 0 0; }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl {  box-sizing: border-box; width: 100%;   }              
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootLeftCtl { width: 14%; height: 4vh; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_darkgrey},.5); }              
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootLeftCtl:hover { cursor: pointer; color: rgba({$this->color_darkgreen},1); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootRightCtl { width: 14%; height: 4vh; color: rgba({$this->color_zackgrey},1); background: rgba({$this->color_darkgrey},.5); }              
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootRightCtl:hover { cursor: pointer; color: rgba({$this->color_darkgreen},1); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootCalTitle { text-align: center; background: rgba({$this->color_darkgrey},.5); font-weight: bold; font-size: 1.8vh; padding: .8vh 0;  }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .mainRootCalHeadDay { font-size: 1.2vh; width: 5vw; height: 2vh; border: 1px solid rgba({$this->color_darkgrey},.5); border-right: none; background: rgba({$this->color_darkgrey},1); text-align: center; }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .starterHeadCell { border-left: none;   }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .mainRootTopSpacer {background: rgba({$this->color_lightgrey},.7); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .mainRootBtmSpacer {background: rgba({$this->color_lightgrey},.7); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .mnuMainRootDaySquare { border: 1px solid rgba({$this->color_darkgrey},.5); border-bottom: none; border-right: none; width: 6vw; height: 8vh; }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .calendarEndDay { border-left: none; }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl .todayDsp { background: rgba({$this->color_lamber},.5); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #mainRootBtmLine { border-top: 1px solid rgba({$this->color_darkgrey},.5); padding: 1vh 1vw; background: rgba({$this->color_lightgrey},1); }
 #dashboardholder #calendarHolder #theCalendar #mainRootTbl #saluations { font-size: 1.2vh; font-weight: bold; }
.caldayeventholder { font-size: 1vh; position: relative; padding: 0 3px 0 3px; box-sizing: border-box;  }
.caldayday { float: left; margin-top: 1px; margin-right: 1px; padding-left: 1px; font-size: 1.6vh; color: rgba({$this->color_zackgrey},.8); }
.caldaytoday { color: rgba({$this->color_darkgreen},1); font-weight: bold; }
.eventHoverDisplay { position: absolute; top: 30px; left: -30px; background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); padding: 4px; display: none; z-index: 48; width: 15vw;  }
.caldayeventholder:hover .eventHoverDisplay { display: block; }
.eventDspItemTable { border-collapse: collapse; } 
.eventDspItemTable tr td { white-space: nowrap; }
.popEvntTbl { width: 12vw; }
.popTimeCell {  white-space: nowrap; width: 3.5vw; border-top: 1px solid rgba({$this->color_darkgrey},.5);  }
.popEvtType {  font-size: 1vh;  color: rgba({$this->color_darkgrey},.5); text-align: right; border-top: 1px solid rgba({$this->color_darkgrey},.5);  }
.rEventFld { font-size: 1.3vh; }
#rootEventDate { width: 8vw; }
#rootEventDropCal { width: 12vw; }
#rootEventStart { width: 6vw; }
#rootEventEnd { width: 6vw; }
#rootEventtype { width: 12vw; }
#rootEventTitle { width: 8vw; }
#rootEventDesc { width: 100%; } 
#rootICInitials {width: 4vw; display: none; }
#rootMDInitials {width: 4vw; display: none; }
#icmdheader { display: none; }
#rootEventInstitution { width: 100%; }

.displayerDiv { grid-column: span 1; grid-row: span 1; border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw; } 
.displayerDivLong { grid-column: span 5; grid-row: span 1; border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw; } 
.metricHead { font-family: Roboto; font-size: 1.2vh;  color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); }
.dashboardimage { width: 100%; }

#dashboardenlargeimagehold_landscape { width: 39vw; height: 50vh; overflow: hidden; } 
#dashboardenlargeimagehold_portrait { width: 39vw; height: 50vh; overflow: hidden; } 
#dashboardenlargeimagehold_square { width: 39vw; height: 50vh; overflow: hidden; }

.datasqr { display: grid; grid-template-columns: 3fr 1fr;  margin: .2vh 0 .2vh 0; border: 3px solid rgba({$this->color_zackgrey},1); padding: .2vh .2vw; border-radius: 5px; }  
.datasqr .datalabel { font-size: 1.3vh;  }  
.datasqr .datadsp   { font-size: 1.3vh; font-weight: bold; text-align: right; }  

#fadatadsp { display: grid; grid-template-columns: repeat( 2, 1fr); }
#fadatadsp .sidelbl { color: rgba({$this->color_darkgreen},1); font-size: 1.3vh; }  
#fadatadsp .sidedata { text-align: right; color: rgba({$this->color_darkgreen},1); font-size: 1.3vh; } 
#fadatadsp #takerlink { grid-column: span 2; text-align: center; } 

#dashboardholder #multiInfoLine { grid-column: span 2; display: grid; grid-template-columns: repeat( 5, 1fr); grid-gap: .5vw; }
#dashboardholder #multiInfoLine .infoHold {  border: 3px solid rgba({$this->color_mgrey},.3); padding: .1vh .2vw;  }
#dashboardholder #multiInfoLine .infoHold .iLabel { font-family: Roboto; font-size: 1.2vh; color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); } 
#dashboardholder #multiInfoLine .infoHold .iData {  font-family: Roboto; font-size: 3vh; font-weight: bold;  color: rgba({$this->color_zackgrey},1); text-align: center; padding: 1vh 0;  }

#dashboardholder .displayDivSmlr { grid-column: span 2; grid-row: span 2;  display: grid; grid-template-columns: repeat( 2, 1fr); grid-gap: .5vw;  }
#dashboardholder .displayDivSmlr #weekship { border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw;  }
#dashboardholder .displayDivSmlr #weekship #weekshipholder { height: 17vh; overflow: auto; }  



#dashboardholder #multiInfoLine #starrating { border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw; overflow: hidden;  }
#dashboardholder #multiInfoLine #starrating .iLabel { font-family: Roboto; font-size: 1.2vh; color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); }
#dashboardholder #multiInfoLine #starrating .iLabelA { font-family: Roboto; font-size: 1.2vh; color: rgba({$this->color_darkgreen},1);  }
#dashboardholder #multiInfoLine #starrating #starholdercntdsp { display: grid; grid-template-columns: repeat( 5,1fr); padding: .5vh 0;  }

#dashboardholder .displayDivSmlr #anotherMetricDsp { border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw; height: 8vh;   }
#dashboardholder .displayDivSmlr #anotherMetricDspA { border: 3px solid rgba({$this->color_mgrey},.3); padding: .2vh .2vw;  }
#dashboardholder .displayDivSmlr #anotherMetricDspA .iLabel { font-family: Roboto; font-size: 1.2vh; color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_mgrey},1); }

#dashboardholder #multiInfoLineTwo { grid-column: span 2; display: grid; grid-template-columns: repeat( 5, 1fr); grid-gap: .5vw; }

#weekshiptbl { font-family: Roboto; font-size: 1.3vh;  }
#weekshiptbl tr th { text-align: left; font-weight: bold; color: rgba({$this->color_darkgreen},1); border-bottom: 1px solid rgba({$this->color_darkgreen},1); }
#weekshiptbl tbody tr:nth-child(even) { background: rgba({$this->color_neongreen},.3);   }
#weekshiptbl tbody tr td { padding: .4vh .2vw;  } 
#sdtotl { background: rgba({$this->color_black},1); color: rgba({$this->color_white},1); padding: .2vh .2vw; } 





.smlGreyFont { color: rgba({$this->color_cornflowerblue},1); font-size: .9vh; font-weight: bold;  } 

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

body { margin: 0; margin-top: 10.5vh; margin-left: .2vw; box-sizing: border-box; }
.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 
.tabBtn { font-size: 1.3vh; text-align: center; width: 5vw; border-right: 1px solid rgba({$this->color_cornflowerblue},1); background: rgba({$this->color_white},1); color: rgba({$this->color_zackgrey},1); }
.tabBtn:hover { cursor: pointer; background: rgba({$this->color_lightgrey},1); }

#sidePanelSlideListTbl { border: 1px solid rgba({$this->color_zackgrey},1); font-size: 1.3vh; }
#sidePanelSlideListTbl .workbenchheader { font-size: 1.8vh; padding: 1vh 0 0 0; font-weight: bold; }   
#sidePanelSlideListTbl .slidesfound { font-size: 1vh; text-align: right; padding: 0 .5vw 0 0; }

#sidePanelSlideListTbl .hprSlideDsp { border: 1px solid rgba({$this->color_zackgrey},1); margin: 10px 10px; height: 13vh; width: 20vw; } 
#sidePanelSlideListTbl .readyes { font-size: 3vh; color: rgba({$this->color_darkgreen},1); }
#sidePanelSlideListTbl .readno { font-size: 3vh; color: rgba(255, 182, 38, 1);  }
#sidePanelSlideListTbl .bgsslidenbr { font-size: 1.8vh; font-weight: bold; height: 1vh;  }
#sidePanelSlideListTbl .slidedesignation { font-size: 1.4vh;  height: 3vh; }  
#sidePanelSlideListTbl .slidedate { font-size: 1vh; } 
#sidePanelSlideListTbl .slidetech { font-size: 1vh; }
#sidePanelSlideListTbl .slidefreshdsp { font-size: 1.2vh; text-align: center; color: rgba({$this->color_bred},1);  }
#sidePanelSlideListTbl .rowHolder { }
#sidePanelSlideListTbl .rowHolder:hover { cursor: pointer; background: rgba({$this->color_lamber},1);  }

#progressBarHolder { border-bottom: 1px solid rgba({$this->color_cornflowerblue},1); height: .7vh; width: 42vw; box-sizing: border-box; overflow: hidden; position: relative; }
#progressBarDsp { background: rgba({$this->color_darkgreen},1); position: absolute; top: 0; left: 0; height: 1vh; }



#headAnnouncement { font-size: 2vh; color: rgba({$this->color_zackgrey},1); text-align: center; padding: 4vh 0 0 0; }
#dspScanType { font-size: 5vh; text-align: center; } 

#HPRPreLimTbl { width: 40vw; }
.topreadindicator { font-size: 2.3vh; }
.needread { color: rgba({$this->color_bred},1); }
.doneread { color: rgba({$this->color_darkgreen},1); }
.hprPreLimFldLbl { font-size: 1.2vh; font-weight: bold; color: rgba({$this->color_dblue},1); padding: .4vh 0 0 .1vw; }
.hprPreLimDtaFld { font-size: 1.5vh; color: rgba({$this->color_dblue},1); padding: .4vh 0 .2vh .2vw; border-bottom: 1px dashed rgba({$this->color_dblue},.5); }

.prcSqrHolder { border-left: 1px solid rgba({$this->color_mgrey},.7);border-bottom: 1px solid rgba({$this->color_mgrey},1); }
.hprDataField { font-size: 1.5vh; color: rgba({$this->color_dblue},1); padding: .8vh .5vw; }
.hprNewDropDownFont { font-size: 1.5vh; color: rgba({$this->color_dblue},1); } 

.rightEndCap { border-right: 1px solid rgba({$this->color_dblue},.5); }
.twentyfive { width: 10.5vw; }
.smlrTxt { font-size: 1.2vh; font-style: italic; }
#submitTbl { font-size: 1vh; color: rgba({$this->color_mgrey},1); }

#masterHPRSlideReviewTbl { width: 98vw; box-sizing: border-box; }
#masterHPRSlideAnnounceLine { height: 3vh; font-size: 2vh; font-weight: bold; color: rgba({$this->color_dblue},1); box-sizing: border-box; padding: 8px 0 0 2px; border-bottom: 2px solid rgba({$this->color_dblue}, 1); }
#masterHPRTechnicianSide { width: 39vw; height: 40vh; box-sizing: border-box; border: 1px solid rgba({$this->color_cornflowerblue},1); }
#masterHPRControlBtns { width:  2vw; }
#masterHPRDocumentSide { border: 1px solid rgba({$this->color_cornflowerblue},1);} 
#masterHPRDivBtns { height: 4vh; border: 1px solid rgba({$this->color_cornflowerblue},1); }
#masterHPRWorkbenchSide { box-sizing: border-box; border: 1px solid rgba({$this->color_cornflowerblue},1);}

.HPRReviewDocument { display: none; height: 35vh;  }

.dspDocTitle { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; width: 40vw; box-sizing: border-box; padding: 4px; height: 4vh; }

#dspPathologyRptTxt { font-size: 1.5vh;  height: 31vh; overflow: auto; box-sizing: border-box; padding: 8px 15px 8px 12px; line-height: 1.7em; text-align: justify; }

#dspConstituentst { height: 31vh; overflow: auto; box-sizing: border-box;  }
#constitTbl {width: 40vw; box-sizing: border-box; font-size: 1.4vh; }
#constitTbl tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#constitTbl tbody tr:hover { background: rgba({$this->color_lamber},1); }
#constitTbl tr td { padding: 5px 0 5px 3px; border-bottom: 1px solid rgba({$this->color_dblue},1); border-right: 1px solid rgba({$this->color_dblue},1);  } 
.constiticon { font-size: 1.9vh; color: rgba({$this->color_darkgreen},1);  }

#dspAssGroups { height: 31vh; overflow: auto; box-sizing: border-box; }
#wholeAssTbl { width: 100%; box-sizing: border-box; font-size: 1.2vh; } 
#wholeAssTbl tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#wholeAssTbl tbody tr:hover { background: rgba({$this->color_lamber},1); }
#wholeAssTbl tr td { padding: 5px 0 5px 3px; border-bottom: 1px solid rgba({$this->color_dblue},1); border-right: 1px solid rgba({$this->color_dblue},1);  }
#wholeAssTbl tr th { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1);   }  
.smlFont { font-size: 1vh; }

#dspPastHPR { height: 31vh; overflow: auto; box-sizing: border-box; }
#pastHPRTbl { width: 100%; box-sizing: border-box; font-size: 1.2vh; } 
#pastHPRTbl tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#pastHPRTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#pastHPRTbl tr td { padding: 5px 0 5px 3px; border-bottom: 1px solid rgba({$this->color_dblue},1); border-right: 1px solid rgba({$this->color_dblue},1);  }
#pastHPRTbl tr th { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1);   }  


#dspPRTxt { display: block; }

.prntIcon .material-icons { font-size: 2vh;  }
.prntIcon .material-icons:hover { cursor: pointer; color: rgba({$this->color_dblue},1); }

.dspWBDocTitle { background: rgba({$this->color_cornflowerblue},1); color: rgba({$this->color_white},1); font-size: 1.8vh; font-weight: bold; box-sizing: border-box; height: 3.5vh; padding: 5px; }
#HPRWBTbl { width: 56vw; }

.sideDesigBtns { width: 5vw; padding: 3px 0; box-sizing: border-box; border: 1px solid rgba({$this->color_dblue},1); text-align: center; background: rgba({$this->color_white},1); color: rgba({$this->color_zackgrey},1); font-size: 1vh;  font-weight: bold; }
.sideDesigBtns:hover { cursor: pointer; background: rgba({$this->color_lightgrey},1); }

#decisionSqr { width: 5vw; border: 1px solid rgba({$this->color_dblue},1); text-align: center; }
#decisionSqr[data-hprdecision='CONFIRM'] { background: rgba({$this->color_darkgreen},.8); color: rgba({$this->color_white},1); }
#decisionSqr[data-hprdecision='ADDITIONAL'] { background: rgba({$this->color_lamber},.8); color: rgba({$this->color_dblue},1); }
#decisionSqr[data-hprdecision='DENIED'] { background: rgba({$this->color_bred},.8); color: rgba({$this->color_white},1); }
#decisionSqr .hprdecisionicon { font-size: 5vh; }

.primaryInfo { } 
.constitInfoHolder { position: relative; }
.constitInfoHolder:hover .popUpInfo { cursor: pointer; display: block; }
.constitInfoHolder:hover .popUpShipInfo { cursor: pointer; display: block; }

.popUpInfo { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; white-space: nowrap; z-index: 40; grid-template-columns: 1fr; grid-template-rows: repeat( 4, 1fr );  }


/* .popUpInfo { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; white-space: nowrap; z-index: 40; } */
/* .popUpInfo table tr td { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_dblue},1); } */

   
.popUpShipInfo { position: absolute; background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); padding: 7px 5px; display: none; white-space: nowrap; z-index: 40; right: 0; }
.popUpShipInfo table tr td { background: rgba({$this->color_dblue},1); color: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_dblue},1); }

.prcFld { width: 3vw; font-size: 1.5vh; color: rgba({$this->color_dblue},1); padding: .8vh .5vw; text-align: right;}
.dspDefinedMoleTests { border: 1px solid rgba(160,160,160,1); height: 10vh; overflow: auto;   padding: 4px; box-sizing: border-box; }

.upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.btn {
  border: 2px solid gray;
  color: gray;
  background-color: white;
  padding: 8px 20px;
  border-radius: 8px;
  font-size: 20px;
  font-weight: bold;
}

.upload-btn-wrapper input[type=file] {
  font-size: 100px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}

.actionInstruction { color: rgba({$this->color_mgrey},.8); font-style: italic; }

#hprVocabResultTbl { width: 51vw; font-size: 1.5vh; box-sizing: border-box; }
#hprVocabResultTbl thead tr th { padding: .5vh 0 0 4px; border-bottom: 1px solid rgba({$this->color_zackgrey},1); text-align: left; }
#hprVocabResultTbl tbody tr:nth-child(even) {background: rgba({$this->color_lgrey},1); }
#hprVocabResultTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#hprVocabResultTbl tbody tr td { padding: 4px; }

#fldPRCUnInvolved { font-size: 1.5vh; color: rgba({$this->color_dblue},1); padding: .8vh .5vw; width: 18vw; }
#ddPRCUnInvolved { min-width: 18vw; }

STYLESHEET;
return $rtnThis;
}

function datacoordinator($mobileind) { 
      $rtnThis = <<<STYLESHEET

body { margin: 0; margin-top: 12vh; box-sizing: border-box;  }

.floatingDiv {  z-index: 101;  background: rgba({$this->color_white},1); border: 1px solid rgba({$this->color_zackgrey},1); position: fixed; padding: 2px; top: 150px ; left: 150px;  } 

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
#coordinatorResultTbl tbody .righttt { right: 0; }
#coordinatorResultTbl tbody .righttt:hover { color: rgba({$this->color_neongreen},1); }
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
#TQWarning { font-family: Roboto; font-size:  1.3vh; font-weight: bold; color: rgba({$this->color_bred},1); }

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
#sdcCourierInfo { width: 25vw; }

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

.dspInvOverrideSegmentLabel {width: 8vw; }
.inventoryLocDsp { width: 30vw; }
.inventoryNewStatus { width: 13vw; }
.checkInHead {font-size: 1.8vh;  background: rgba(48,57,71,1); color: rgba(255,255,255,1); padding: .5vh .3vw .5vh .3vw; }
#waitgifADD { width: 1.4vw; padding: 1vh 0; }
#waiterIndicator { display: none; }


.hprindication { color: rgba({$this->color_darkgreen},1); text-decoration: underline;  }


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

function softwaremanual () { 

  //#masterhead #crumbhld { text-align: right; padding: 0 1vw 0 0; font-size: 1vh; grid-column: span 3;     }  
  $at = genAppFiles; 
  $bgPic = base64file("{$at}/publicobj/graphics/bg.png","background","bgurl",true);
  $rtnThis = <<<STYLESHTS

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; font-family: Roboto; font-size: 1vh; color: rgba({$this->color_black},1);}
body {margin: 5vh 0 0 0; width: 100%; background: rgba({$this->color_white},1); color: rgba({$this->color_zackgrey},1); }

#masterhead { position: fixed; top: 0; left: 0; background: rgba({$this->color_white},1); width: 100%; display: grid; grid-template-columns: 4.5vw 90vw; grid-gap: .2vw; background: rgba({$this->color_zackgrey},1);  z-index: 99; border-bottom: 1px solid rgba({$this->color_white},1); }
#masterhead #headlogohld { margin: .6vh 0 .3vh .5vw; width: 4vw;  } 
#masterhead #headlogohld #bluStar { height: 3vh; }   
#masterhead #titlehld {  font-size: 1.8vh; font-weight: bold; margin: .3vh 0 .3vh 0; line-height: 3.5vh; padding: .6vh 0 0 0; height: 3vh; overflow: hidden; color: rgba({$this->color_white},1);  }  

#toctray { position: fixed; top: 0; right: -37vw; display: grid; grid-template-columns: 1.6vw auto; z-index: 98; transition:  all 1s ease; }
#toctray #trayctl { display: grid; grid-template-rows:  9vh auto; height: 100vh; }
#toctray .sidehold { border-right: 1px solid rgba({$this->color_zackgrey},1);  }
#toctray #ctlbtn { border: 1px solid rgba({$this->color_zackgrey},1); border-right: none; padding: 5vh 0 0 0; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }
#toctray #ctlbtn:hover { cursor: pointer; color: rgba({$this->color_neongreen},1);   } 
#toctray #ctlbtn .material-icons { font-size: 3vh; }  
#toctray #tocdsp { padding: 5vh 1vw 0 1vw; width: 35vw; background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }  
#toctray #tocdsp #TOCTitle { font-size: 2vh; font-weight: bold; border-bottom: 6px solid rgba({$this->color_white},1); } 
#toctray #tocdsp #sortbtns { display: grid; grid-template-columns: 3fr 1fr 1fr 1fr; grid-gap: .2vw; }
#toctray #tocdsp #sortbtns .sortbtn { border: 1px solid rgba({$this->color_white},1); text-align: center; padding: .3vh 0; margin: .2vh 0 0 0;  }
#toctray #tocdsp #sortbtns .sortbtn:hover { cursor: pointer; color: rgba({$this->color_neongreen},1);   } 


STYLESHTS;
return $rtnThis;           
}

function login($mobileInd) {
$at = genAppFiles; 
  $bgPic = base64file("{$at}/publicobj/graphics/bg.png","background","bgurl",true);
   $rtnThis = <<<STYLESHTS

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Fira+Sans);
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

#loginGrid #elementHolderBTNs { display: grid; grid-template-columns: 5fr 1fr 1fr; grid-gap: .2vw; margin-top: .8vh; } 
#loginGrid .elementHolder { margin-bottom: 1vh; margin-top: 1vh; }
#loginGrid .elementHolder .elementLabel {  font-family: Roboto; font-size: 1.4vh; font-weight: bold; color: rgba({$this->color_mblue},1); padding-top: 1vh; }  

#loginFooter {font-family: Roboto; font-size: 1.2vh; line-height: 1.8em; padding: 2vh .8vw 2vh .8vw; text-align: justify; border-top: 1px solid rgba({$this->color_dblue},1); background: rgba({$this->color_lightgrey},1); }
#loginvnbr { font-family: Roboto; font-size: .9vh; padding: .4vh .4vw .4vh .8vw; text-align: right; background: rgba({$this->color_lightgrey},1); }

#manuallink { font-family: Roboto; font-size: .9vh; padding: .4vh .4vw .4vh .8vw; text-align: right; background: rgba({$this->color_lightgrey},1); }
#manuallink a { color: blue; } 

.adminBtn {font-family: Roboto; font-size: 1.4vh; font-weight: bold;  border: 1px solid rgba({$this->color_mblue},1); color: rgba({$this->color_mblue}, 1); padding: 8px 4px 8px 4px; }
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
