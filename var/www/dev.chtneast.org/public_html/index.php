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

//Include functions file
require(genAppFiles . "/appsupport/generalfunctions.php");
require(genAppFiles . "/extlibs/detectmobilelibrary.php");
require(serverkeys . "/serverid.zck");
define("serverIdent",$serverid);
define("serverPW", $serverpw);
define("apikey",chtnencrypt(serverPW));

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

/*****USER PROCESSING ******
//$authuser = $_SERVER['PHP_AUTH_USER']; 
//$authpw = cryptservice( $_SERVER['PHP_AUTH_PW'] , 'd');
 *******************************/

switch ($request[1]) { 
    case 'dataservices': 
        //BOTH GET AND POSTS GO HERE
        $responseCode = 400;
        switch ($method) { 
           case 'POST':
              echo "DATA SERVICES - {$method}";
              //$postedData = file_get_contents('php://input')
              $responseCode = 200;
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
        session_start();        
        //CHECK USER 
        if ($_SESSION['loggedon'] === 1)  { 
         $obj = trim($request[1]); 
         $obj = (trim($obj) === "") ?  "root" : trim($obj);            
        } else { 
            $obj = "login";
        }


        //GET PAGE - and Display



        echo $obj;
}
