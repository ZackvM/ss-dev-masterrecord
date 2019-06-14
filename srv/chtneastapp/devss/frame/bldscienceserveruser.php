<?php

class bldssuser { 
    
    public $statusCode = 404;
    public $loggedsession = "";
    public $dbuserid = 0;
    public $userid = "";
    public $friendlyname = "";
    public $username = "";
    public $useremail = "";
    public $chngpwordind = 0;
    public $allowweeklyupdate = 0;
    public $allowpxi = 0;
    public $allowprocure = 0; 
    public $allowcoord = 0; 
    public $allowhpr = 0;
    public $allowhprreview = 0; 
    public $allowinventory = 0;
    public $presentinstitution = "";
    public $primaryinstitution = "";
    public $daysuntilpasswordexp = 0;
    public $accesslevel = "";
    public $profilepicturefile = "";
    public $officephone = ""; 
    public $displayalternate = 0;
    public $alternateindirectory = 0;
    public $alternateemail = ""; 
    public $alternatephone = "";
    public $alternatephntype = "";
    public $cellcarrierco = "";
    public $textingphone = ""; 
    public $drvlicexp = "";
    public $allowedmodules = "";
    public $allowedinstitutions = "";
    public $lastlogin = "";
    
    function __construct() {
        //$args = func_get_args(); 
        $userelements = self::getUserInformation();
        if (trim($userelements['sssession']) !== "") { 
          $this->statusCode = 200;  
          $this->loggedsession = $userelements['sssession'];
          $this->dbuserid = $userelements['ssuserid'];
          $this->userid = $userelements['ssusername'];
          $this->friendlyname = $userelements['ssfriendlyname'];
          $this->username = $userelements['ssdisplayname'];
          $this->useremail = $userelements['ssemail'];
          $this->chngpwordind = $userelements['sschangepassword'];
          $this->allowweeklyupdate = $userelements['allowweeklyupdate']; 
          $this->allowpxi = $userelements['allowpxi'];
          $this->allowprocure = $userelements['allowprocurement'];
          $this->allowcoord = $userelements['allowcoordination'];
          $this->allowhpr = $userelements['allowhpr'];
          $this->allowhprreview = $userelements['allowhprreview'];
          $this->allowinventory = $userelements['allowinventory'];
          $this->presentinstitution = $userelements['presentinstitution'] ;
          $this->primaryinstitution = $userelements['primaryinstcode'];
          $this->daysuntilpasswordexp = $userelements['daystopasswordexpire'];
          $this->accesslevel = $userelements['accesslevel'];
          $this->accessnbr = $userelements['accessnbr'];
          $this->profilepicturefile = $userelements['profilepicturefile']; 
          $this->officephone = $userelements['officephone'];
          $this->displayalternate = $userelements['displayindirectory'];
          $this->alternateindirectory = $userelements['alternateindirectory'];
          $this->alternateemail = $userelements['alternateemail'];
          $this->alternatephone = $userelements['alternatephone'];
          $this->alternatephntype = $userelements['altphonetype']; 
          $this->textingphone = $userelements['textingphonenumber'];
          $this->cellcarrierco = $userelements['cellcarrierco'];
          $this->drvlicexp = $userelements['dlexpiration'];
          $this->allowedmodules = $userelements['modules'];
          $this->allowedinstitutions = $userelements['institutions'];
          $this->lastlogin = $userelements['lastlogin'];
        }    
    }
    
    function getUserInformation() { 
        $elArr = array();
        session_start(); 
        require_once(serverkeys . "/sspdo.zck"); 
        $userTopSQL = "SELECT ub.userid"
                . ", ifnull(ub.friendlyName,'') as ssfriendlyname"
                . ", ifnull(ub.originalaccountname,'') as ssusername"
                . ", lcase(ifnull(ub.emailaddress,'')) as email "
                . ", ifnull(ub.changePWordInd,1) as changepwordind"
                . ", ifnull(ub.allowind,0) as allowind"
                . ", ifnull(ub.allowweeklyupdate,0) as allowweeklyupdate"
                . ", ifnull(ub.allowlinux,0) as allowlinux"
                . ", ifnull(ub.allowproc,0) as allowprocurement"
                . ", ifnull(ub.allowcoord,0) as allowcoordination"
                . ", ifnull(ub.allowhpr,0) as allowhpr"
                . ", ifnull(ub.allowhprreview,0) as allowhprreview"
                . ", ifnull(ub.allowinvtry,0) as allowinventory"
                . ", ifnull(ub.presentinstitution,'') as presentinstitution"
                . ", timestampdiff(MINUTE, now(), ub.sessionexpire) minleftinsession"
                . ", ifnull(ub.username,'') as displayname"
                . ", ifnull(ub.primaryinstcode,'') as primaryinstcode"
                . ", timestampdiff(DAY, now(), ub.passwordExpireDate) daystopasswordexpire"
                . ", ifnull(ub.accesslevel,'') as accesslevel"
                . ", ifnull(ub.accessnbr,3) as accessnbr"
                . ", ifnull(ub.logcardid,'') as logcardid "
                . ", ifnull(ub.inventorypinkey,'') as inventorypinkey"
                . ", timestampdiff(DAY, now(), ub.logcardexpdte) daystoinventorycardexpire"
                . ", ifnull(ub.profilepicurl,'') as profilepicturefile"
                . ", ifnull(ub.profilephone,'') as profilephone"
                . ", ifnull(ub.profilealtemail,'') as profilealternateemail"
                . ", ifnull(ub.dlexpiredate,'') as dlexpiration"
                . ", ifnull(ub.altphone,'') as profilealternatephone"
                . ", ifnull(ub.altphonetype,'') as altphonetype"
                . ", ifnull(ub.altphonecellcarrier, '') as textingphonenbr"
                . ", ifnull(ub.dspindirectory,0) displayindirectory"
                . ", ifnull(ub.dspAlternateInDir,0)  dspalternateindir"
                . ", ifnull(cc.dspvalue,'') as ccarrier "
                . " FROM four.sys_userbase ub "
                . " left join (SELECT menuvalue, dspvalue  FROM four.sys_master_menus where menu = 'CELLCARRIER' ) cc on  ub.cellcarriercode = cc.menuvalue "
                . " where sessionid = :sessionkey and allowind = 1 and timestampdiff(DAY, now(), passwordExpireDate) > -1 and  timestampdiff(minute, now(), sessionExpire) > 0";
        $userTopR = $conn->prepare($userTopSQL);
        $userTopR->execute(array(':sessionkey' => session_id()));
        if ($userTopR->rowCount() === 1) {
            
           $ur = $userTopR->fetch(PDO::FETCH_ASSOC); 
           $uid = $ur['userid'];
           $elArr['ssuserid'] = $ur['userid'];
           $elArr['ssfriendlyname'] = $ur['ssfriendlyname'];
           $elArr['sssession'] = session_id();             
           $elArr['ssusername'] = $ur['ssusername']; 
           $elArr['ssdisplayname'] = $ur['displayname'];
           $elArr['ssemail'] = $ur['email'];
           $elArr['sschangepassword'] = $ur['changepwordind'];
           $elArr['allowpxi'] = $ur['allowlinux'];
           $elArr['allowweeklyupdate'] = $ur['allowweeklyupdate'];
           $elArr['allowprocurement'] = $ur['allowprocurement'];
           $elArr['allowcoordination'] = $ur['allowcoordination'];
           $elArr['allowhpr'] = $ur['allowhpr'];
           $elArr['allowhprreview'] = $ur['allowhprreview'];
           $elArr['allowinventory'] = $ur['allowinventory'];
           $elArr['presentinstitution'] = $ur['presentinstitution'];
           $elArr['primaryinstcode'] = $ur['primaryinstcode'];
           $elArr['daystopasswordexpire'] = $ur['daystopasswordexpire'];
           $elArr['accesslevel'] = $ur['accesslevel'];
           $elArr['accessnbr'] = $ur['accessnbr'];
           $elArr['profilepicturefile'] = $ur['profilepicturefile']; 
           $elArr['officephone'] = $ur['profilephone'];
           $elArr['displayindirectory'] = $ur['displayindirectory'];
           $elArr['alternateindirectory'] = $ur['dspalternateindir'];
           $elArr['alternateemail'] = $ur['profilealternateemail'];
           $elArr['alternatephone'] = $ur['profilealternatephone'];
           $elArr['altphonetype'] = $ur['altphonetype']; 
           $elArr['textingphonenumber'] = $ur['textingphonenbr'];
           $elArr['dlexpiration'] = $ur['dlexpiration'];           
           $elArr['cellcarrierco'] = $ur['ccarrier'];
                     
           //GET ALLOWED MODULES
           $modSQL = "SELECT mods.moduleid, mm.module, mm.pagesource FROM four.sys_userbase_modules mods left join (SELECT menuid as modid, ucase(menuvalue) as module, ifnull(pagesource,'') as pagesource FROM four.sys_master_menus where menu = 'SS5MODULES' and dspInd = 1 order by dsporder) mm on mods.moduleid = mm.modid where userid = :userid and mods.onoffind = 1";
           $modR = $conn->prepare($modSQL);
           $modR->execute(array(':userid' => $uid));
           $mods = array();
           while ($mod = $modR->fetch(PDO::FETCH_ASSOC)) {

              $subModMenuSQL = "SELECT menuvalue, pagesource, ifnull(explainerline,'') as additionalcode, ifnull(googleiconcode,'') as dspsystemicon, ifnull(queriable,0) as dspinmenu FROM four.sys_master_menus where menu = 'MODULEPAGES' and trim(ifnull(pagesource,'')) <> '' and dspind = 1 and ( additionalinformation <= :accesslevel) and parentid = :parentmodid order by dspOrder";
              $subModMenuR = $conn->prepare($subModMenuSQL); 
              $subModMenuR->execute(array(':parentmodid' => $mod['moduleid'], ':accesslevel' => (int)$ur['accessnbr']));
              $sbmod = array();
              while ($subModMenu = $subModMenuR->fetch(PDO::FETCH_ASSOC)) { 
                $sbmod[] = $subModMenu;
              }

              if ( (int)$mod['moduleid'] === 472 ) {

                $rptListSQL = "SELECT concat(substr(rl.reportname,1,30),'...') as menuvalue, 'x' as pagesource, concat('navigateSite(\'reports/',ifnull(rl.urlpath,''),'\')') as additionalcode,'x' as dspsystemicon, 1 as dspinmenu FROM four.ut_reportgroup_to_reportlist rtr left join four.ut_reportlist rl on rtr.reportid = rl.reportid where userfav = :usr  and rtr.dspind = 1 and rl.dspind = 1 and rl.accesslvl <= :accessnbr order by rtr.dsporder limit 10";
                $rptListRS = $conn->prepare($rptListSQL);
                $rptListRS->execute(array(':accessnbr' => $ur['accessnbr'], ':usr' => $ur['ssusername']));
                while ( $rpt = $rptListRS->fetch(PDO::FETCH_ASSOC) ) {
                  $sbmod[] = $rpt; 
                }
              }

              $mods[]  = array($mod['moduleid'],$mod['module'],$mod['pagesource'],$sbmod);
           }
           
           //GET ALLOWED INSTITUTIONS
           $instSQL = "SELECT instlst.institutioncode, instlst.institutionname FROM four.sys_userbase_allowinstitution ainst left join (SELECT ucase(menuvalue) as institutioncode, dspvalue as institutionname, menuid as joincode FROM four.sys_master_menus where menu = 'INSTITUTION' and dspind = 1) instlst on ainst.institutionmenuid = instlst.joincode where userid = :userid and ainst.onoffind = 1";
           $instR = $conn->prepare($instSQL);
           $instR->execute(array(':userid' => $uid));
           $insts = array();
           $cntr = 0;
           while ($inst = $instR->fetch(PDO::FETCH_ASSOC)) { 
               $insts[$cntr] = array($inst['institutioncode'], $inst['institutionname'] );
               ++$cntr;
           }

           //GET LAST LOGIN 
           $lastLogSQL = "SELECT fromip, date_format(logDateTime, '%a %b %D, %Y at %H:%i') as dspdatetime FROM four.sys_lastLogins where userid = :userid order by logDateTime desc limit 1,1";
           $lastLogR = $conn->prepare($lastLogSQL); 
           $lastLogR->execute(array(':userid' => $uid ));
           if ( $lastLogR->rowCount() === 1 ) {
             $ll = $lastLogR->fetch(PDO::FETCH_ASSOC);  
             $lastlog['lastlogdate'] = $ll['dspdatetime'];
             $lastlog['fromip'] = $ll['fromip']; 
           } else { 
             $lastlog['lastlogdate'] = "";
             $lastlog['fromip'] = ""; 
           }

           $elArr['modules'] = $mods;
           $elArr['institutions'] = $insts;
           $elArr['lastlogin'] = $lastlog;

           
        }   else { 
           $elArr['sssession'] = "";             
           $elArr['ssusername'] = ""; 
           $elArr['ssdisplayname'] = "";
           $elArr['ssfriendlyname'] = "";
           $elArr['ssemail'] = "";
           $elArr['sschangepassword'] = "";
           $elArr['allowpxi'] = 0;
           $elArr['allowweeklyupdate'] = 0;
           $elArr['allowprocurement'] = 0;
           $elArr['allowcoordination'] = 0;
           $elArr['allowhpr'] = 0;
           $elArr['allowhprreview'] = 0;
           $elArr['allowinventory'] = 0;
           $elArr['presentinstitution'] = "";
           $elArr['primaryinstcode'] = "";
           $elArr['daystopasswordexpire'] = 0;
           $elArr['accesslevel'] = "";
           $elArr['profilepicturefile'] = ""; 
           $elArr['officephone'] = "";
           $elArr['displayindirectory'] = 0;
           $elArr['alternateemail'] = "";
           $elArr['alternatephone'] = "";
           $elArr['altphonetype'] = ""; 
           $elArr['alternateindirectory'] = "";
           $elArr['textingphonenumber'] = "";
           $elArr['cellcarrierco'] = "";
           $elArr['dlexpiration'] = "";
           $elArr['modules'] = "";
           $elArr['institutions'] = "";
           $elArr['lastlogin'] = array('lastlogdate' => "", 'fromip' => "");
        }     
        return $elArr;
    }
        
}
