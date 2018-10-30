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
define("dataPath","https://data.chtneast.org");
define("applicationTree","/srv/chtneastapp/devss/frame");
define("genAppFiles","/srv/chtneastapp/devss");
define("serverkeys","/srv/chtneastapp/devss/dataconn");
//MODULUS HAS BEEN CHANGED TO DEV.CHTNEAST.ORG
define("eModulus","C06C78F42AF5353164FBFA0AEF97891F5736E00B2D50E9693002654FF151644A834234316B4019056DCDCAF105BAE69D34EEB1F575ADD0B50C9EE6E880BAC2B7C4008A231D732D09B7C4FB0B7BEB981AD06B26DB7AFA66B708BEE30052767779633B8178ED1569A1223FCABBB60904AC90058DA09290B198E09CF4953F8EAC25");
define("eExponent","10001");

//Include functions file
require(genAppFiles . "/appsupport/generalfunctions.php");
require(genAppFiles . "/extlibs/detectmobilelibrary.php");
require(serverkeys . "/serverid.zck");

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
              
            } 
            break;



           case 'GET':
               echo "DATA SERVICES - {$method}";
              $responseCode = 200;               
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
        echo "PRINT AN OBJECT {$method}";         
        } else { 
          echo "ONLY GET REQUESTS ARE ALLOWED AT THIS END POINT";
        }


        //DISPLAY PRINTED OBJECT HERE 


        break;



    default: 
        //ALLOW GET ONLY - PAGE DISPLAYS

       if ($method === "GET") {
        session_start();        
        //CHECK USER 
        if ($_SESSION['loggedin'] && $_SESSION['loggedin'] === 1)  { 
         require(applicationTree . "/bldscienceserveruser.php"); 
         $ssUser = new bldssuser();
         if ((int)$ssUser->statusCode <> 200) { 
           session_regenerate_id(true);
           session_unset(); 
           session_destroy(); 
           $obj = "login";
         } else { 
           $obj = trim($request[1]); 
           $obj = (trim($obj) === "") ?  "root" : trim($obj);            
         }  
        } else { 
            $obj = "login";
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
<body><h1>Requested Page ({$reqPage}) @ CHTN Eastern Division - Not Found!</h1>
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
<title>PAGE NOT FOUND</title>
</head>
<body><h1>Requested method no allowed ({$method}) @ CHTN Eastern Division!</h1>
</body></html>
RTNTHIS;
       }
        echo $rt;
        exit();

}




