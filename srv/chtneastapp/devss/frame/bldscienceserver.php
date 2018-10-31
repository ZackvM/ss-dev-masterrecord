<?php

class pagebuilder { 

  public $statusCode = 404;		
  public $pagetitle = "";
  public $pagetitleicon = "";
  public $headr = ""; 
  public $stylr = "";
  public $scriptrs = "";
  public $bodycontent = "";
  public $pagecontrols = "";
  public $acctdisplay = "";
  public $menucontent = "";
  public $modalrs = "";
  public $modalrdialogs = "";
  //PAGE NAME MUST BE REGISTERED IN THIS ARRAY - COULD DO A METHOD SEARCH - BUT I LIKE THE CONTROL OF NOT ALLOWING A PAGE THAT IS NOT READY FOR DISPL
  private $registeredPages = array('login','root');  
  //THE SECURITY EXCPETIONS ARE THOSE PAGES THAT DON'T REQUIRE USER RIGHTS TO ACCESS
  private $securityExceptions = array('login', 'root');

function __construct() { 		  
  $args = func_get_args();   
   if (trim($args[0]) === "") {	  		
   } else {
     session_start();
     if ($_SESSION['loggedin'] !== "true" && $args[0] !== "login") {
         $this->statusCode = 403;
     } else {           
       $mobileInd = $args[2];  
       $usrmetrics = $args[3];  //$usrmetric->username - Class from the index file defining the user      
       if (in_array($args[0], $this->registeredPages)) {
         $pageElements = self::getPageElements($args[0],$args[1], $mobileInd, $usrmetrics);	  
         $this->statusCode = $pageElements['statuscode'];
         $this->pagetitle = $pageElements['tabtitle'];
         $this->pagetitleicon = $pageElements['tabicon'];
         $this->headr = $pageElements['headr'];
//         $this->stylr = $pageElements['styleline'];
         $this->scriptrs = $pageElements['scripts'];
         $this->bodycontent = $pageElements['bodycontent'];
         $this->pagecontrols = $pageElements['controlbars'];
         $this->acctdisplay = $pageElements['acctdsp'];
         $this->menucontent = $pageElements['menu'];
         $this->modalrs = $pageElements['modalscreen'];
         $this->modalrdialogs = $pageElements['moddialog']; 
       } else { 
         $this->statusCode = 404;
       }     
     }     
   }   
}

function getPageElements($whichpage, $rqststr, $mobileInd, $usrmetrics) { 
  session_start();  
  $ss = new stylesheets(); 
  $js = new javascriptr();
  $oe = new defaultpageelements();
  $pc = new pagecontent();
  $elArr = array();
  
  //HEADER - TAB - ICON ---------------------------------------------
  $elArr['tabtitle']     =   (method_exists($oe,'pagetabs') ? $oe->pagetabs($whichpage) : "");
  $elArr['tabicon']      =   (method_exists($oe,'faviconBldr') ? $oe->faviconBldr($whichpage) : "");
  $elArr['headr']        =   (method_exists($pc,'generateHeader') ? $pc->generateHeader($mobileInd, $whichpage) : "");
  //STYLESHEETS ---------------------------------------------------
  if ($whichpage !== "login") {
    $elArr['styleline']    =   (method_exists($ss,'globalstyles') ? $ss->globalstyles($mobileInd) : "");
  }
  $elArr['styleline']   .=   (method_exists($ss,$whichpage) ? $ss->$whichpage($rqststr) : "");
  //JAVASCRIPT COMPONENTS -------------------------------------------
  if ($whichpage !== "login") {  
    //$ky = json_decode(self::buildsessionkeypair($usr),true);
    $elArr['scripts']      =   (method_exists($js,'globalscripts') ? $js->globalscripts( "", "") : "");
  }
  $elArr['scripts']     .=   (method_exists($js,$whichpage) ? $js->$whichpage($rqststr) : "");

  //CONTROL BARS GET BUILT HERE --------------------------------------------------------------   
  $elArr['acctdsp'] =   (method_exists($oe,'appcarddisplay') ? $oe->appcarddisplay($whichpage, $usrmetrics ) : "");
  $elArr['controlbars']  =   (method_exists($oe,'menubuilder') ? $oe->menubuilder($whichpage, $usrmetrics ) : "");     
  $elArr['modalscreen']  =   (method_exists($oe,'modalbackbuilder') ? $oe->modalbackbuilder($whichpage) : "");
  $elArr['moddialog']    =   (method_exists($oe,'modaldialogbuilder') ? $oe->modaldialogbuilder($whichpage) : "");

  //PAGE CONTENT ELEMENTS  ------------------------------------
  //MAKE SURE USER IS ALLOWED ACCESS TO THE PAGE 
  $allowPage = 0;
  if (in_array($whichpage, $this->securityExceptions)) {
    $allowPage = 1;
  } else {      
      foreach ($usrmetrics->allowedmodules as $modval) { 
          $allowPage =  ($whichpage === str_replace("-","",$modval[2])) ? 1 : $allowPage; 
          foreach ($modval[3] as $allowPageLst) {
              $allowPage = ($whichpage === str_replace("-","",$allowPageLst['pagesource'])) ? 1 : $allowPage; 
          }
      }      
  } 
  if ($allowPage === 1) { 
    $elArr['bodycontent'] =   (method_exists($pc,$whichpage) ? $pc->$whichpage($rqststr, $usrmetrics) : "");   
 } else { 
   $elArr['bodycontent'] =  "<h1>USER NOT ALLOWED ACCESS TO THIS MODULE PAGE ({$whichpage})";
 }
 //END PAGE ELEMENTS ---------------------------


//RETURN STATUS - GOOD ---------------------------------------------------------------
  $elArr['statuscode'] = 200;
  return $elArr;
}

function buildsessionkeypair($usrfile) {
    //FOR SECURITY THIS SHOULD BE THE ONLY DIRECT DATABASE CONNECTION IN THIS FILE
    // THIS WILL BUILD THE USER JAVASCRIPT KEYPAIR
  require(genAppFiles .  "/dataconn/sspdo.zck");  

  $delSQL = "delete from four.sys_userbase_keypairs where activeind = 0";
  $delR = $conn->prepare($delSQL); 
  $delR->execute();

  $randomBytes = bin2hex(random_bytes(8));
  $updSQL = "update four.sys_userbase_keypairs set activeind = 0 where userid = :userid";
  $updR = $conn->prepare($updSQL); 
  $updR->execute(array(':userid' => $usrfile->userid));
  session_start();
  
  $insSQL = "insert into four.sys_userbase_keypairs (userid, sessionid, keypaircode, activeind, inputon) values(:userid, :sessionid, :keypaircode, 1, now())";
  $insR = $conn->prepare($insSQL); 
  $insR->execute(array(':userid' => $usrfile->userid, ':sessionid' => session_id(), ':keypaircode' => $randomBytes));
  $payload = json_encode(array('enckeycode' => chtnencrypt($usrfile->userid."::".session_id()."::".$randomBytes)));
  return $payload;
}

}

include 'sscomponent_pagecontent.php';
include 'sscomponent_javascriptr.php';
include 'sscomponent_stylesheets.php';
include 'sscomponent_defaultelements.php';



