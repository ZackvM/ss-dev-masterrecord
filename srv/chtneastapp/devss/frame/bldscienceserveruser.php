<?php

$userTestString = "USER TEST STRING";


class bldssuser { 
    
    public $statusCode = 404;
    public $loggedsession = "";
    public $dbuserid = 0;
    public $userid = "";
    public $username = "";
    public $useremail = "";
    public $chngpwordind = 0;
    public $allowpxi = 0;
    public $allowprocure = 0; 
    public $allowcoord = 0; 
    public $allowhpr = 0; 
    public $allowinventory = 0;
    public $presentinstitution = "";
    public $primaryinstitution = "";
    public $daysuntilpasswordexp = 0;
    public $accesslevel = "";
    public $profilepicturefile = "";
    public $officephone = ""; 
    public $alternateemail = ""; 
    public $alternatephone = "";
    public $alternatephntype = "";
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
          $this->username = $userelements['ssdisplayname'];
          $this->useremail = $userelements['ssemail'];
          $this->chngpwordind = $userelements['sschangepassword'];
          $this->allowpxi = $userelements['allowpxi'];
          $this->allowprocure = $userelements['allowprocurement'];
          $this->allowcoord = $userelements['allowcoordination'];
          $this->allowhpr = $userelements['allowhpr'];
          $this->allowinventory = $userelements['allowinventory'];
          $this->presentinstitution = $userelements['presentinstitution'] ;
          $this->primaryinstitution = $userelements['primaryinstcode'];
          $this->daysuntilpasswordexp = $userelements['daystopasswordexpire'];
          $this->accesslevel = $userelements['accesslevel'];
          $this->accessnbr = $userelements['accessnbr'];
          $this->profilepicturefile = $userelements['profilepicturefile']; 
          $this->officephone = $userelements['officephone'];
          $this->alternateemail = $userelements['alternateemail'];
          $this->alternatephone = $userelements['alternatephone'];
          $this->alternatephntype = $userelements['altphonetype']; 
          $this->textingphone = $userelements['textingphonenumber'];
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
        $userTopSQL = "SELECT userid, ifnull(originalaccountname,'') as ssusername, lcase(ifnull(emailaddress,'')) as email , ifnull(changePWordInd,1) as changepwordind"
                . ", ifnull(allowind,0) as allowind, ifnull(allowlinux,0) as allowlinux"
                . ", ifnull(allowproc,0) as allowprocurement, ifnull(allowcoord,0) as allowcoordination, ifnull(allowhpr,0) as allowhpr, ifnull(allowinvtry,0) as allowinventory"
                . ", ifnull(presentinstitution,'') as presentinstitution, timestampdiff(MINUTE, now(), sessionexpire) minleftinsession, ifnull(username,'') as displayname, ifnull(primaryinstcode,'') as primaryinstcode, timestampdiff(DAY, now(), passwordExpireDate) daystopasswordexpire"
                . ", ifnull(accesslevel,'') as accesslevel, ifnull(accessnbr,3) as accessnbr, ifnull(logcardid,'') as logcardid , ifnull(inventorypinkey,'') as inventorypinkey, timestampdiff(DAY, now(), logcardexpdte) daystoinventorycardexpire"
                . ", ifnull(profilepicurl,'') as profilepicturefile, ifnull(profilephone,'') as profilephone, ifnull(profilealtemail,'') as profilealternateemail, ifnull(dlexpiredate,'') as dlexpiration, ifnull(altphone,'') as profilealternatephone, ifnull(altphonetype,'') as altphonetype, ifnull(altphonecellcarrier, '') as textingphonenbr "
                . " FROM four.sys_userbase "
                . " where sessionid = :sessionkey and allowind = 1 and timestampdiff(DAY, now(), passwordExpireDate) > -1 and  timestampdiff(minute, now(), sessionExpire) > 0";
        $userTopR = $conn->prepare($userTopSQL);
        $userTopR->execute(array(':sessionkey' => session_id()));
        if ($userTopR->rowCount() === 1) {
            
           $ur = $userTopR->fetch(PDO::FETCH_ASSOC); 
           $uid = $ur['userid'];
           $elArr['ssuserid'] = $ur['userid'];
           $elArr['sssession'] = session_id();             
           $elArr['ssusername'] = $ur['ssusername']; 
           $elArr['ssdisplayname'] = $ur['displayname'];
           $elArr['ssemail'] = $ur['email'];
           $elArr['sschangepassword'] = $ur['changepwordind'];
           $elArr['allowpxi'] = $ur['allowlinux'];
           $elArr['allowprocurement'] = $ur['allowprocurement'];
           $elArr['allowcoordination'] = $ur['allowcoordination'];
           $elArr['allowhpr'] = $ur['allowhpr'];
           $elArr['allowinventory'] = $ur['allowinventory'];
           $elArr['presentinstitution'] = $ur['presentinstitution'];
           $elArr['primaryinstcode'] = $ur['primaryinstcode'];
           $elArr['daystopasswordexpire'] = $ur['daystopasswordexpire'];
           $elArr['accesslevel'] = $ur['accesslevel'];
           $elArr['accessnbr'] = $ur['accessnbr'];
           $elArr['profilepicturefile'] = $ur['profilepicturefile']; 
           $elArr['officephone'] = $ur['profilephone'];
           $elArr['alternateemail'] = $ur['profilealternateemail'];
           $elArr['alternatephone'] = $ur['profilealternatephone'];
           $elArr['altphonetype'] = $ur['altphonetype']; 
           $elArr['textingphonenumber'] = $ur['textingphonenbr'];
           $elArr['dlexpiration'] = $ur['dlexpiration'];           
                     
           //GET ALLOWED MODULES
           $modSQL = "SELECT mods.moduleid, mm.module, mm.pagesource FROM four.sys_userbase_modules mods left join (SELECT menuid as modid, ucase(menuvalue) as module, ifnull(pagesource,'') as pagesource FROM four.sys_master_menus where menu = 'SS5MODULES' and dspInd = 1 order by dsporder) mm on mods.moduleid = mm.modid where userid = :userid and mods.onoffind = 1";
           $modR = $conn->prepare($modSQL);
           $modR->execute(array(':userid' => $uid));
           $mods = array();
           while ($mod = $modR->fetch(PDO::FETCH_ASSOC)) {
              $subModMenuSQL = "SELECT menuvalue, pagesource, ifnull(explainerline,'') as additionalcode  FROM four.sys_master_menus where menu = 'MODULEPAGES' and trim(ifnull(pagesource,'')) <> '' and dspind = 1 and ( additionalinformation <= :accesslevel) and parentid = :parentmodid order by dspOrder";
              $subModMenuR = $conn->prepare($subModMenuSQL); 
              $subModMenuR->execute(array(':parentmodid' => $mod['moduleid'], ':accesslevel' => (int)$ur['accessnbr']));
              $sbmod = array();
              while ($subModMenu = $subModMenuR->fetch(PDO::FETCH_ASSOC)) { 
                $sbmod[] = $subModMenu;
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
           $elArr['ssemail'] = "";
           $elArr['sschangepassword'] = "";
           $elArr['allowpxi'] = 0;
           $elArr['allowprocurement'] = 0;
           $elArr['allowcoordination'] = 0;
           $elArr['allowhpr'] = 0;
           $elArr['allowinventory'] = 0;
           $elArr['presentinstitution'] = "";
           $elArr['primaryinstcode'] = "";
           $elArr['daystopasswordexpire'] = 0;
           $elArr['accesslevel'] = "";
           $elArr['profilepicturefile'] = ""; 
           $elArr['officephone'] = "";
           $elArr['alternateemail'] = "";
           $elArr['alternatephone'] = "";
           $elArr['altphonetype'] = ""; 
           $elArr['textingphonenumber'] = "";
           $elArr['dlexpiration'] = "";
           $elArr['modules'] = "";
           $elArr['institutions'] = "";
           $elArr['lastlogin'] = array('lastlogdate' => "", 'fromip' => "");
        }     
        return $elArr;
    }
        
}
