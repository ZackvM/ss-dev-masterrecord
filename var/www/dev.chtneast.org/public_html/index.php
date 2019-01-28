<?php

/* DEV-SCIENCESERVER MAIN INDEX */

/*
 * REQUIRED SERVER DIRECTORY STRUCTURE
 * /srv/chtneastapp/devss = applicationTree - Can be changed if application files are moved
 *    +----accessfiles (Public/private key hold)
 *    +----appsupport (files/functions that do things to support all application frames) 
 *    +----frame (build/application files)
 *    +----dataconn (directory for data connection strings - Only to be used by PHP files under the applicationTree)
 *    +----tmp (application generated temporary files)
 *    +----publicobj (physical objects to pull 
 * 
 */

//START SESSSION FOR ALL TRACKING 
session_start(); 
//DEFINE APPLICATION PATH PARAMETERS
define("uriPath","dev.chtneast.org");
define("ownerTree","https://www.chtneast.org");
define("treeTop","https://dev.chtneast.org");
define("dataTree","https://dev.chtneast.org/data-services");
define("applicationTree","/srv/chtneastapp/devss/frame");
define("genAppFiles","/srv/chtneastapp/devss");
define("serverkeys","/srv/chtneastapp/devss/dataconn");
define("phiserver","https://chtn2017.uphs.upenn.edu");
//MODULUS HAS BEEN CHANGED TO DEV.CHTNEAST.ORG
define("eModulus","C7D2CD63A61A810F7A220477B584415CABCF740E4FA567D0B606488D3D5C30BAE359CA3EAA45348A4DC28E8CA6E5BCEC3C37A429AB3145D70100EE3BB494B60DA522CA4762FC2519EEF6FFEE30484FB0EC537C3A88A8B2E8571AA2FC35ABBB701BA82B3CD0B2942010DECF20083A420395EF4D40E964FA447C9D5BED0E91FC35F12748BB0715572B74C01C791675AF024E961548CE4AA7F7D15610D4468C9AC961E7D6D88A6B0A61D2AD183A9DFE2E542A50C1C5E593B40EC62F8C16970017C68D2044004F608E101CD30B69310A5EE550681AB411802806409D04F2BBB3C49B1483C9B9E977FCEBA6F4C8A3CB5F53AE734FC293871DCE95F40AD7B9774F4DD3");
define("eExponent","10001");

//Include functions file
require(genAppFiles . "/appsupport/generalfunctions.php");
require(genAppFiles . "/extlibs/detectmobilelibrary.php");
require(serverkeys . "/serverid.zck");

//STOP INTERNET EXPLORER USE
//$browser = getBrowserZack($_SERVER['HTTP_USER_AGENT']) ;
//Mozilla Firefox, Google Chrome, Apple Safari
//if ( array_key_exists('name', $browser)  ) { 
//    if ( 
//            (!strtolower(trim($browser['name'])) === "mozilla firefox") 
//      && (!strtolower(trim($browser['name'])) === "google chrome") 
//      && (!strtolower(trim($browser['name'])) === "apple safari" )  
//    ) { 
//        //TODO:  REMOVE THIS RESTRICTION FOR THE INVESTIGATOR GATEWAY
//        echo "<h1>You must use either Firefox, Chrome or Safari to access this data application";
//    }
//} else { 
//    exit(1);
//}


define("serverIdent",$serverid);
define("servertrupw", $serverpw);
define("serverpw", cryptservice($serverpw) );

//DEFINE THE REQUEST PARAMETERS
$requesterIP = $_SERVER['REMOTE_ADDR']; 
$method = $_SERVER['REQUEST_METHOD'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$host = $_SERVER['HTTP_HOST'];
$https = ($_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
$originalRequest = str_replace("-","", strtolower($_SERVER['REQUEST_URI']));
$request = explode("/",str_replace("-","", strtolower($_SERVER['REQUEST_URI']))); 

///DETECT PLATFORM ON WHICH THE ACCESSOR IS VIEWING SITE
$detect = new Mobile_Detect(); 
$mobilePlatform = false; 
$mobilePrefix = "w";
if ($detect->isMobile()) { 
    $mobilePlatform = true; 
    $mobilePrefix = "m";
    echo "<h1>SCIENCESERVER IS NOT PRESENTLY FORMATTED FOR MOBILE DEVICES.  USE DESKTOP BROWSERS ONLY";
    exit();    
}
/*
 * OTHER MOBILE DETECTION METHODS
 * $detect->isTablet()
 * $detect->isIOS()
 * $detect->isAndroidOS()
 */


switch ($request[1]) {

    case 'chtneasternmicroscope': 
      
        $pg = <<<PAGECONTENT

<html>  
  <head>
    <title>CHTN Eastern Microscope</title>
    <link rel="stylesheet" href="https://dev.chtneast.org/slides/leaf.css">
    <script src="https://dev.chtneast.org/slides/leaf.js"></script>
    <style>
      #map {
        height: 80vh;
        width: 40vw;
        float: left;
      }
      #console { 
        height: 80vh;
        width: 40vw;
        border: 1px solid #000;
        float: left;
            overflow: auto;
            }
    </style>
  </head>
  <body>
    
    <div id="map"></div>
   
   
     <script>
      var map = L.map('map').setView([51.505, -0.09], 3);
      
      L.tileLayer('https://dev.chtneast.org/slides/slidetwo/{z}/{y}/{x}.jpg', {
        maxZoom: 9
      }).addTo(map);
                        
    </script>
  </body>
</html>


PAGECONTENT;
echo $pg;
        exit();
        break; 
    case 'dataservices': 
        //BOTH GET AND POSTS GO HERE
        $responseCode = 400;
        $data = "";
        switch ($method) { 
          case 'POST':
            $authuser = $_SERVER['PHP_AUTH_USER']; 
            $authpw = $_SERVER['PHP_AUTH_PW']; 
            if ((int)checkPostingUser($authuser, $authpw) === 200) { 
              //CONTINUE WITH POST REQUEST
              require(genAppFiles . "/dataservices/posters/scienceserverposter.php"); 
              $postedData = file_get_contents('php://input');
              $passedPayLoad = "";
              if (trim($postedData) !== "") { 
                $passedPayLoad = trim($postedData);
              } 
              $doer = new dataposters($originalRequest, $passedPayLoad);
              $responseCode = $doer->responseCode; 
              $data = $doer->rtnData;                
            } else { 
                $responseCode = 401;
                $data = "USER NOT FOUND";
            } 
            break;

          case 'GET':
            $responseCode = 400;
            $data = "";
            $authuser = $_SERVER['PHP_AUTH_USER']; 
            $authpw = $_SERVER['PHP_AUTH_PW']; 
            if ((int)checkPostingUser($authuser, $authpw) === 200) {   
              require(genAppFiles . "/dataservices/getter/scienceservergetter.php");  
              $obj = new objgetter($originalRequest);
              $responseCode = $obj->responseCode;
              $data = $obj->rtnData;  
            } else {
                $responseCode = 401;
            }
            break;
           default: 
               echo "ONLY GET/POST are allowed under this end point!"; 
               $responseCode = 405;
               header('HTTP/1.0 401 Unauthorized');
        }

        header('Content-type: application/json; charset=utf8');
        header('Access-Control-Allow-Origin: *'); 
        header('Access-Control-Allow-Header: Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Max-Age: 3628800'); 
        header('Access-Control-Allow-Methods: GET, POST');
        http_response_code($responseCode);
        echo $data;
        break;        

    case 'printobj': 
      //PRINT OBJECT - GET ONLY  
      if ($method === "GET") { 
        require(applicationTree . "/scienceserverprint.php");       

        $prnt = new  printobject($_SERVER['REQUEST_URI']);     
        http_response_code($prnt->httpresponse);
                
        if ($prnt->httpresponse === 200) { 
          $pgTitle = (trim($prnt->pagetitle) !== "") ? "<title>" . $prnt->pagetitle . "</title>" : "<title>CHTN Eastern</title>";    
          $pgHead = (trim($prnt->headr) !== "") ? $prnt->headr : "";         
          $pgIcon = (trim($prnt->pagetitleicon) !== "") ? $prnt->pagetitleicon : "";
          $pgStyle = (trim($prnt->style) !== "") ? "<style>" . $prnt->style . "\n</style>" :  "";
          if ($prnt->htmlpdfind === 0) { 
           //OBJECT IS PDF         
           $pgBody =  base64file($prnt->bodycontent,'pathologyrptdsp','pdf',true,''); 
          }
          if ($prnt->htmlpdfind === 1) { 
           //OBJECT IS HTML         
           //echo $prnt->bodycontent;                    
           $pgBody = $prnt->bodycontent;
         }
$rt = <<<RTNTHIS
<!DOCTYPE html>
<html>   
<head>
{$pgHead}           
{$pgIcon}            
{$pgTitle}
{$pgStyle}
{$pgScriptr}
</head>
<body>
{$pgBody}
</body>
</html>
RTNTHIS;
  echo $rt;
  exit();
        } else { 
          echo $prnt->bodycontent . " (" . $prnt->httpresponse . ")";
        }
        exit();   
      } else { 
        echo "ONLY GET REQUESTS ARE ALLOWED AT THIS END POINT";
      }
    break;
    default: 
        //ALLOW GET ONLY - PAGE DISPLAYS

       if ($method === "GET") {
        session_start();        
        //CHECK USER
        if (!$_SESSION['loggedin'] || $_SESSION['loggedin'] !== "true")  { 
            $obj = "login";
        } else {
         require(applicationTree . "/bldscienceserveruser.php"); 
         $ssUser = new bldssuser();
         if ((int)$ssUser->statusCode <> 200) { 
           session_regenerate_id(true);
           session_unset(); 
           session_destroy(); 
           $obj = "login";
         } else { 
             $obj = (trim($request[1]) === "") ?  "root" : trim($request[1]);   
         }   
        }

        require(applicationTree . "/bldscienceserver.php"); 
        $pageBld = new pagebuilder($obj, $request, $mobileprefix, $ssUser);
        if ((int)$pageBld->statusCode <> 200) { 
            //PAGE NOT FOUND            
            http_response_code($pageBld->statusCode);
            
    $rt = <<<RTNTHIS
<!DOCTYPE html>
<html>
<head>
<title>PAGE NOT FOUND</title>
</head>
<body><h1>Requested Page ({$obj}) @ CHTN Eastern Division - Not Found!</h1>
</body></html>
RTNTHIS;
//PAGE NOT FOUND END
        } else { 
//PAGE FOUND AND DISPLAY HERE
    $pgIcon = (trim($pageBld->pagetitleicon) !== "") ? $pageBld->pagetitleicon : "";
    $pgHead = (trim($pageBld->headr) !== "") ? $pageBld->headr : "";
    $pgTitle = (trim($pageBld->pagetitle) !== "") ? "<title>" . $pageBld->pagetitle . "</title>" : "<title>CHTN Eastern</title>";
    $pgStyle = (trim($pageBld->stylr) !== "") ? "<style>" . $pageBld->stylr . "\n</style>" :  "";
    $pgScriptr = (trim($pageBld->scriptrs) !== "") ? "<script lang=javascript>" . $pageBld->scriptrs . "</script>" : "";
    $pgControls = $pageBld->pagecontrols;
    $pgBody = $pageBld->bodycontent;
    $userAccount = $pageBld->acctdisplay;
    $pgMenu = $pageBld->menucontent;
    $pgModal = $pageBld->modalrs;
    $pgDialogs = $pageBld->modalrdialogs;
    $rt = <<<RTNTHIS
<!DOCTYPE html>
<html>
<head>
{$pgIcon}            
{$pgHead}
{$pgTitle}
{$pgStyle}
{$pgScriptr}
</head>
<body>
{$pgControls}
{$userAccount}
{$pgBody}
{$pgMenu}
{$pgModal}
{$pgDialogs}    
</body>
</html>
RTNTHIS;
//PAGE FOUND AND DISPLAY HERE END
        }

    } else { 
   
        header('Content-type: application/json; charset=utf8');
        header('Access-Control-Allow-Origin: *'); 
        header('Access-Control-Allow-Header: Origin, X-Requested-With, Content-Type, Accept');
        header('Access-Control-Max-Age: 3628800'); 
        header('Access-Control-Allow-Methods: GET');
        http_response_code(405);
    $rt = <<<RTNTHIS
<!DOCTYPE html>
<html>
<head>
<title>METHOD NOT ALLOWED</title>
</head>
<body><h1>Requested method no allowed ({$method}) @ CHTN Eastern Division!</h1>
</body></html>
RTNTHIS;
       }
        echo $rt;
        exit();

}

