<?php

class ssdataobject { 

  public $objectname = "NO OBJECT SPECIFIED";
  public $objectid = "0";
  public $foundindicator = 0; 
  public $session = "";
  public $object = array(); 

  function __construct() { 
    $args = func_get_args();
    //$args[0] MUST EQUAL DATABASEOBJECTS NAME, $ARGS[1] MUST BE OBJECT ID
    //TODO: CHECK USER HAS RIGHTS TO VIEW/EDIT THIS OBJECT
    //require(applicationTree . "/bldscienceserveruser.php"); 
    //$ssUser = new bldssuser();
    if (trim($args[0]) === "" || trim($args[1]) === "" ) { 
    } else {  
      $func = $args[0];
      $dofunclist = new databaseobjects();
      if (method_exists($dofunclist, $func)) { 
          $do = $dofunclist->segment($args[1]); 
          $this->objectname = $do['objectname'];               
          $this->session = $do['sessionid'];
          $this->objectid = $do['objectid'];
          $this->foundindicator = count($do['object']);
          $this->object = $do['object'];
      }
    }
  }
    
}

class databaseobjects { 

 function segment($id) {      
     $defineSQL = <<<OBJECTSQL
SELECT sg.biosamplelabel, sg.segmentid, ifnull(sg.bgs,'XXXXXX') as bgs
, ifnull(sg.segstatus,'') segstatuscode
, ifnull(sg.statusdate,'') as statusdate, ifnull(sg.statusby,'') as statusby
, ifnull(sg.shipdocrefid,'') as shipdocrefid
, ifnull(sg.shippeddate,'') as shippeddate
, ifnull(sg.hourspost,'') as hourspost
, ifnull(sg.metric,'') as metric
, ifnull(sg.metricuom,'') as metricuomcode
, ifnull(sg.prepmethod,'') as prepmethod 
, ifnull(sg.preparation,'') as preparation
, ifnull(sg.prepmodifier,'') as prepmodifier
, ifnull(sg.prepadditives,'') as prepadditive
, ifnull(sg.assignedto,'') as assignedto
, ifnull(sg.assignedproj,'') as assignedproject
, ifnull(sg.assignedreq,'') as assignedrequest
, ifnull(sg.assignmentdate,'') as assigneddate
, ifnull(sg.assignedby,'') as assignedby
, ifnull(sg.procurementdate,'') as procurementdate
, ifnull(sg.enteredon,'') as procurementdbdate
, ifnull(sg.enteredby,'') as procuringtechnician
, ifnull(sg.procuredat,'') as procuringinstitution
, ifnull(sg.hprblockind,0) as hprblockind
, ifnull(sg.slidegroupid,0) as slidegroupid
, ifnull(sg.reqrqrbloodmatch,'') as reqrequestbloodmatch
, ifnull(sg.reqchartreview,'') as reqrequestchartreview
, ifnull(sg.slidefromblockid,'') as slidefromblockid
, ifnull(sg.voidind,0) as voidind 
, ifnull(sg.segmentvoidreason,'') as segmentvoidreason
, ifnull(sg.scannedlocation,'') as scannedlocation
, ifnull(sg.scanloccode,'') as scanloccode
, ifnull(sg.scannedstatus,'') as scannedstatus
, ifnull(sg.scannedby,'') as scannedby
, ifnull(sg.scanneddate,'') as scanneddate
, ifnull(sg.tohpr,0) as tohprind 
, ifnull(sg.hprboxnbr,'') as hprboxnbr
, ifnull(sg.tohprby,'') as tohprby 
, ifnull(sg.tohpron,'') as tohpron
, ifnull(sg.segmentcomments,'') as segmentcomments
, ifnull(sg.qty,1) as qty  
, ifnull(bs.tisstype,'') as specimencategory
, ifnull(bs.anatomicSite,'') site
, ifnull(bs.subSite, '') subsite
, ifnull(bs.diagnosis,'') as dx
, ifnull(bs.subdiagnos,'') as dxmod
, ifnull(bs.metssite,'') as metssite
, ifnull(bs.metssitedx,'') as metssitedx
, ifnull(bs.pdxsystemic,'') as systemicdx
, ifnull(cxv.dspvalue,'') as cx
, ifnull(rxv.dspvalue,'') as rx
, ifnull(bs.hprind,0) as hprind
, ifnull(bs.qcind,0) as qcind
, ifnull(prv.dspvalue,'Pending') as pthrpt
, ifnull(icv.dspvalue,'No') as infc 
FROM masterrecord.ut_procure_segment sg
LEFT JOIN masterrecord.ut_procure_biosample bs on sg.biosampleLabel = bs.pBioSample
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'CX') as cxv on bs.chemoind = cxv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'RX') as rxv on bs.radind = rxv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'PRpt') as prv on bs.pathreport = prv.menuvalue
left join (SELECT menuvalue, dspvalue FROM four.sys_master_menus where menu = 'infc') as icv on bs.pathreport = icv.menuvalue
where segmentid = :objectid             
OBJECTSQL;
     $obj = runObjectSQL($defineSQL, $id);
     return array('sessionid' => '',  'objectname' => 'Segment', 'objectid' => $id, 'object' => $obj);
 }

}

function runObjectSQL($sql, $id) { 
    session_start();
    $obj = array();
    require(serverkeys . "/sspdo.zck");  
    $objRS = $conn->prepare($sql);
    $objRS->execute(array(':objectid' => $id));
    while ($rs = $objRS->fetch(PDO::FETCH_ASSOC)) { 
        $obj[] = $rs; 
    }
    return $obj;
}




