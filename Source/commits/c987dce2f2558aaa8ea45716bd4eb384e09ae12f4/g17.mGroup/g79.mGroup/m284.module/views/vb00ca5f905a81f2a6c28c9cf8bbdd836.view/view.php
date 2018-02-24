<?php
//#section#[header]
// Module Declaration
$moduleID = 284;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Resources");
//#section_end#
//#section#[code]
use \API\Comm\ftp; 
use \API\Model\modules\module;
use \DEV\Resources\paths;
use \DEV\Websites\wsServer;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	// Packs / Uploads / and Installs the website
	// Returns a summary indicating the success or failure of the Install Proccess
	
	// Create Module Page
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Build the module content
	$pageContent->build("", "uc");
	
	// Get Parameters from request
	$websiteId = 26;
	$serverId = 10;
	
	// Get the server settings
	$server = new wsServer($websiteId, $serverId);
	$info = $server->info();
	
	// TODO
	// Switch between different connection types

	// To load the suitable connector / client
	$ftp = new ftp();
	

	// Establish Connection with the remote server
	//$status = $ftp->connect('ftp.skyworks.gr', 'n37408naps', 'yH*29@dW');
	$status = $ftp->connect($info['address'],Â $info['username'],Â $info['password']);
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	// Get connection handler / socket
	$conn = $ftp->getConnection();
	
	// Navigate to the correct http root folder
	// Usually : 'httpdocs'
	ftp_chdir($conn, $info['httpdocs']);
	
	// Get the installation script folder
	// TODO
	// - Temporary installation scripts are located to Developer's Dynamic Resource Path
	$coreDir = systemRoot.paths::getSysDynRsrcPath()."wsdkTest";
	
	// Upload the prepare.php file
	$oFile = $coreDir.'/prepare.php';
	$dPath = 'prepare.php';
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	// if no error
	// Initiate extraction proccess
	$response = doRequest($info['siteUrl']."/prepare.php", $error); //"www.skyworks.gr/test/prepare.php"
	
	if(is_null($response))
		$msg = DOM::create("p", "FCK ".$error);
	else
		$msg = DOM::create("p", $response);
	$pageContent->append($msg);
	
	if(is_null($response))
		$pageContent->getReport();
	
	// If everything ok -> continue
	
	// Upload setup file
	$oFile = $coreDir.'/setup.php';
	$dPath = 'setup.php';
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	
	// Upload core zip file to .temp folder	
	// Get the forlder that Core is placed
	// TODO
	// - wsdk.zip file version needs to be defined and attached to the name
	// - The path '/System/Library/WebDistros/' is adhoc need to be taken from somewhere
	$oFile = systemRoot."/System/Library/WebDistros/"."wsdk.zip";
	$dPath = '.temp/wsdk.zip';
	
	// TODO
	// - Check whenever we need to change the current ftp dir, in most cases of upload file
	// it does not makes a difference (ftp_chdir($conn, 'httpdocs/test/.temp');)
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	$ftp->close();
	
	// if no error
	// Initiate setup proccess
	$response = doRequest($info['siteUrl']."/setup.php", $error); //"www.skyworks.gr/test/setup.php"
	
	if(is_null($response))
		$msg = DOM::create("p", "FCK ".$error);
	else
		$msg = DOM::create("p", $response);
	$pageContent->append($msg);
	
	
	
	// Return output
	return $pageContent->getReport('#summary');
}

// Assuming Module will be loaded using load:view


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "uc");

// Add a hello world dynamic content
$hw = DOM::create("p", "Website packing / uploading / and installing");
$pageContent->append($hw);

$hw = DOM::create("p", "Project Id : ".$websiteID);
$pageContent->append($hw);

// Build Form
$form = new simpleForm();
$form->build('', FALSE)->engageModule($moduleID, "websiteUploader");
$formHolder = HTML::select('.section.formHolder')->item(0);
//DOM::append($formHolder, $form->get());
$pageContent->append($form->get());

// Return output
return $pageContent->getReport();


function doRequest($url, &$error)
{
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

	$curl = curl_init($url);
        if(is_bool($curl) && !$curl)
        {
            $error = 'Error Initializing cURL';
            return NULL;
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 ); // On curl_exec success return the fetch page (string)
        curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to use curl_getinfo() with option CURLINFO_HEADER_OUT in order to debug your cURL request

        //curl_setopt($curl, CURLOPT_HEADER, true);    // we want headers
        //curl_setopt($curl, CURLOPT_NOBODY, true);    // we don't need body

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 16 );

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($curl, CURLINFO_HEADER_OUT);

	$error = '';
        if(is_bool($response) && !$response)
        {
            //echo 'curl_exec error';
            $error .= 'curl_exec error'."\n \r" ;
            $response = NULL;
            // Check for errors and display the error message
            if($errno = curl_errno($curl))
            {
                $error_message = curl_strerror($errno);
                $error .= 'cURL error ({$errno}): {$error_message}'."\n \r" ;
                $error .= 'Curl error: ' . curl_error($curl)."\n \r" ;
            }
            $error .= 'Curl Info'."\n \r" ;
            $error .= print_r($curlInfo, true)."\n \r" ;
        }

        if( $http_status != 200)
        {
            $error .= 'The server responded: ' . $http_status."\n \r" ;
            $response = NULL;
        }

        curl_close($curl);

	return $response;
}
//#section_end#
?>