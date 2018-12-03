<?php

$doTester = "DO FILE HERE";

class ssdataobject { 

  public $objectname = "NO OBJECT SPECIFIED";
  public $objectid = "0";
  public $foundindicator = 0; 
  public $object = array(); 

  function __construct() { 
    $args = func_get_args();
    //TODO:CHECK USER HAS RIGHTS TO VIEW/EDIT THIS OBJECT
    //require(applicationTree . "/bldscienceserveruser.php"); 
    //$ssUser = new bldssuser();
    if (trim($args[0]) === "") { 
    } else {  
      $func = $args[0];
      if (method_exists(databaseobjects, $func)) { 

          $this->objectname = $args[0];
      }
    }

  }
    

}

class databaseobjects { 

 function segments($id) { 

 }

}
