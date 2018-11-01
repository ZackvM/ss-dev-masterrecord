<?php
define("genAppFiles","/srv/chtneastapp");
require(genAppFiles . "/appsupport/generalfunctions.php");
require(genAppFiles . "/dataconn/serverid.zck");
define("serverIdent",$serverid);
define("serverPW", $serverpw);
define("apikey",chtnencrypt(serverPW));
//$ipinfo = json_decode(getipinformation($requesterIP),true);
session_start();
//PASSPAYLOAD WITH SESSION INFO AND IPINFO
$rtn = callrestapi("POST","https://data.chtneast.org/systemposts/logout",serverIdent,apikey);
session_regenerate_id(true);
session_unset();
session_destroy();
header("location: https://scienceserver.chtneast.org");

